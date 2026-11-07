<?php

/**
 * File Integrity Checker
 *
 * Compares the files under $scanRoot against a known-good baseline using
 * SHA-256 content hashes, and optionally sends an email alert.
 *
 * How it works (two phases):
 *   Phase 1  Walk the tree and stat every file (size + mtime). No file content
 *            is read. In compare mode, files whose size differs from the
 *            baseline are already known to be changed, so they are NOT hashed.
 *   Phase 2  Hash the remaining files in chunks. The progress bar is driven by
 *            bytes hashed / total bytes to hash, so it moves smoothly even
 *            through multi-gigabyte files.
 *
 * Usage:
 *   php integrityCheck.php --generate-baseline
 *   php integrityCheck.php
 *   php integrityCheck.php --hash-only
 *   php integrityCheck.php --verbose
 *   php integrityCheck.php --no-email
 *
 * Exit codes:
 *   0  no differences
 *   1  usage / baseline error
 *   2  differences detected
 *
 * The progress display goes to STDERR, the report goes to STDOUT, so
 * `php integrityCheck.php > report.txt` still shows the progress bar.
 *
 * Requires PHP 7.4+ (CLI).
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit("This script must be run from the command line.\n");
}

// ============================================================
// CONFIGURATION
// ============================================================

$scanRoot = '/var/www';

// Where to store the baseline (keep this outside the web root if possible)
$baselineFile = '/root/file_baseline.json';

// Paths to ignore. Glob syntax: * = anything (including '/'), ? = one character.
// Matched case-insensitively against "/relative/path" (directories end in '/').
$ignorePaths = [
	'*/dbContents/*',
    '*/cache/*',
'*/tmp/*',
'*/temp/*',
'*/logs/*',
'*/log/*',
'*/sessions/*',
'*/var/cache/*',
'*/var/log/*',
'*/storage/framework/cache/*',
'*/storage/framework/sessions/*',
'*/storage/framework/views/*',
'*/storage/logs/*',
'*/node_modules/*',
'*/.git/*',
'*/vendor/*/tests/*',
];

// File extensions to ignore (optional), e.g. ['log', 'tmp']
$ignoreExtensions = [];

// Follow symlinks? Off by default: avoids loops and double-counting.
// (Symlinks are then skipped entirely.)
$followSymlinks = false;

// -------------------- Email settings --------------------
$emailEnabled = true;                                  // set to false to disable
$emailTo      = 'rene.veerman.netherlands@gmail.com';  // <-- change this
$emailFrom    = 'integrity-check@nicer.app';
$emailSubject = '[ALERT] File integrity differences detected on ' . gethostname();
// --------------------------------------------------------

// How many bytes to read per hashing step (also the progress granularity).
const HASH_CHUNK_BYTES = 1048576; // 1 MiB

// ============================================================
// FORMATTING HELPERS
// ============================================================

function formatBytes(float $bytes): string
{
    $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return $i === 0 ? sprintf('%d B', (int) $bytes) : sprintf('%.1f %s', $bytes, $units[$i]);
}

function formatDuration(float $seconds): string
{
    $s = (int) round($seconds);
    $h = intdiv($s, 3600);
    $m = intdiv($s % 3600, 60);
    $sec = $s % 60;
    return $h > 0 ? sprintf('%d:%02d:%02d', $h, $m, $sec) : sprintf('%02d:%02d', $m, $sec);
}

function terminalWidth(): int
{
    $cols = (int) (getenv('COLUMNS') ?: 0);
    if ($cols <= 0 && function_exists('shell_exec')) {
        $cols = (int) trim((string) @shell_exec('tput cols 2>/dev/null'));
    }
    return $cols > 0 ? $cols : 80;
}

// UTF-8 aware length that works with or without the mbstring extension.
function textLength(string $s): int
{
    return function_exists('mb_strlen') ? mb_strlen($s) : (int) preg_match_all('/./su', $s);
}

// ============================================================
// PROGRESS BAR (bytes hashed / total bytes)
// ============================================================

final class ByteProgress
{
    private int $totalBytes;
    private int $totalFiles;
    private bool $tty;
    private int $cols;
    private float $startTime;
    private float $lastDraw = 0.0;
    private int $lastStep = -1;
    private float $lastColsCheck = 0.0;

    public function __construct(int $totalBytes, int $totalFiles)
    {
        $this->totalBytes = $totalBytes;
        $this->totalFiles = $totalFiles;
        $this->tty        = function_exists('stream_isatty') && stream_isatty(STDERR);
        $this->cols       = $this->tty ? terminalWidth() : 80;
        $this->startTime  = microtime(true);
        $this->lastColsCheck = $this->startTime;
    }

    public function update(int $doneBytes, int $doneFiles, string $path, bool $force = false): void
    {
        $now = microtime(true);
        if (!$force && ($now - $this->lastDraw) < 0.1) {
            return;
        }
        $this->lastDraw = $now;

        $doneBytes = min($doneBytes, $this->totalBytes);
        $frac = $this->totalBytes > 0 ? $doneBytes / $this->totalBytes : 1.0;

        // Not a terminal (cron, redirected output): one plain line per 10%.
        if (!$this->tty) {
            $step = (int) floor($frac * 10);
            if ($step !== $this->lastStep) {
                $this->lastStep = $step;
                fwrite(STDERR, sprintf(
                    "  %3d%%  %s / %s  [%d/%d files]\n",
                    (int) round($frac * 100),
                                       formatBytes($doneBytes),
                                           formatBytes($this->totalBytes),
                                               $doneFiles,
                                       $this->totalFiles
                ));
            }
            return;
        }

        $elapsed = $now - $this->startTime;
        $rate    = $elapsed > 0.25 ? $doneBytes / $elapsed : 0.0;
        $eta     = $rate > 0 ? formatDuration(($this->totalBytes - $doneBytes) / $rate) : '--:--';

        $barWidth = 24;
        $filled   = (int) round($frac * $barWidth);
        $bar      = str_repeat('█', $filled) . str_repeat('░', $barWidth - $filled);

        $line = sprintf(
            '%s %5.1f%%  %s / %s  %s/s  ETA %s  [%d/%d] ',
            $bar,
            $frac * 100,
            formatBytes($doneBytes),
                formatBytes($this->totalBytes),
                    formatBytes($rate),
                        $eta,
                        $doneFiles,
                        $this->totalFiles
        );

        // Re-read the terminal width every couple of seconds (handles resizing).
        if ($now - $this->lastColsCheck >= 2.0) {
            $this->lastColsCheck = $now;
            $this->cols = terminalWidth();
        }

        // Make the path safe to display on exactly one line. Wide characters
        // (CJK, emoji) occupy 2 columns and control characters (e.g. newlines in
        // file names) break the line, so every run of non-printable-ASCII bytes
        // is shown as a single '?'. After this, 1 byte == 1 column.
        $path = (string) preg_replace('/[^\x20-\x7E]+/', '?', $path);

        // Truncate the path (keeping its tail). The extra -1 keeps the cursor off
        // the last column, which would otherwise make some terminals wrap.
        $available = $this->cols - textLength($line) - 2;
        if ($available >= 8) {
            if (strlen($path) > $available) {
                $path = '~' . substr($path, -($available - 1));
            }
        } else {
            $path = '';
        }

        fwrite(STDERR, "\r\033[2K" . $line . $path);
    }

    public function finish(int $doneFiles): void
    {
        $this->update($this->totalBytes, $doneFiles, '', true);
        if ($this->tty) {
            fwrite(STDERR, "\n");
        }
    }
}

// ============================================================
// PHASE 1: SIZE SCAN (no file contents are read)
// ============================================================

function globToRegex(string $glob): string
{
    $quoted = preg_quote($glob, '#');
    // preg_quote() escapes * and ? — turn the escaped forms back into wildcards.
    $regex = str_replace(['\*', '\?'], ['.*', '.'], $quoted);
    return '#^' . $regex . '$#is';
}

function compileIgnorePatterns(array $globs): array
{
    return array_map('globToRegex', $globs);
}

function shouldIgnore(string $relativePath, array $ignoreRegexes, array $ignoreExtensions): bool
{
    $path = '/' . ltrim(str_replace('\\', '/', $relativePath), '/');

    foreach ($ignoreRegexes as $regex) {
        if (preg_match($regex, $path)) {
            return true;
        }
    }

    // Extension check applies to files only (directories end in '/').
    if ($ignoreExtensions && substr($path, -1) !== '/') {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext !== '' && in_array($ext, $ignoreExtensions, true)) {
            return true;
        }
    }

    return false;
}

/**
 * Walks $root and returns [relativePath => ['size' => int, 'mtime' => int]].
 * Unreadable directories / entries are added to $unreadable as [relativePath => reason].
 */
function scanSizes(
    string $root,
    array $ignoreRegexes,
    array $ignoreExtensions,
    bool $followSymlinks,
    array &$unreadable
): array {
    $files    = [];
    $root     = rtrim($root, '/');
    $rootLen  = strlen($root) + 1;
    $stack    = [$root];
    $seenDirs = [];
    $isTty    = function_exists('stream_isatty') && stream_isatty(STDERR);
    $lastDraw = 0.0;

    while ($stack) {
        $dir = array_pop($stack);

        $entries = @scandir($dir, SCANDIR_SORT_NONE);
        if ($entries === false) {
            $unreadable[ltrim(substr($dir, $rootLen - 1), '/') ?: '.'] = 'directory not readable';
            continue;
        }

        foreach ($entries as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }

            $full = $dir . '/' . $name;
            $rel  = substr($full, $rootLen);

            $st = @lstat($full);
            if ($st === false) {
                $unreadable[$rel] = 'cannot stat';
                continue;
            }

            $type = $st['mode'] & 0170000;

            if ($type === 0120000) { // symlink
                if (!$followSymlinks) {
                    continue;
                }
                $st = @stat($full);
                if ($st === false) {
                    continue; // dangling link
                }
                $type = $st['mode'] & 0170000;
            }

            if ($type === 0040000) { // directory
                if (shouldIgnore($rel . '/', $ignoreRegexes, $ignoreExtensions)) {
                    continue;
                }
                if ($followSymlinks) {
                    $real = realpath($full);
                    if ($real === false || isset($seenDirs[$real])) {
                        continue;
                    }
                    $seenDirs[$real] = true;
                }
                $stack[] = $full;
                continue;
            }

            if ($type !== 0100000) { // only regular files (skip sockets, fifos, devices)
                continue;
            }

            if (shouldIgnore($rel, $ignoreRegexes, $ignoreExtensions)) {
                continue;
            }

            $files[$rel] = ['size' => (int) $st['size'], 'mtime' => (int) $st['mtime']];

            // Lightweight "files found" counter while enumerating.
            if ($isTty) {
                $now = microtime(true);
                if ($now - $lastDraw >= 0.1) {
                    $lastDraw = $now;
                    fwrite(STDERR, "\r\033[2K  " . number_format(count($files)) . ' files found...');
                }
            }
        }
    }

    if ($isTty) {
        fwrite(STDERR, "\r\033[2K");
    }

    ksort($files, SORT_STRING);
    return $files;
}

// ============================================================
// PHASE 2: HASHING WITH BYTE-BASED PROGRESS
// ============================================================

/**
 * Hashes one file in chunks, advancing $doneBytes as it goes.
 * Returns the SHA-256 hex digest, or null if the file could not be read.
 */
function hashFileWithProgress(
    string $fullPath,
    string $relPath,
    int $expectedSize,
    ByteProgress $progress,
    int &$doneBytes,
    int $doneFiles
): ?string {
    $fh = @fopen($fullPath, 'rb');
    if ($fh === false) {
        $doneBytes += $expectedSize; // keep the bar honest: this file's bytes are "handled"
        return null;
    }

    $ctx  = hash_init('sha256');
    $read = 0;

    while (!feof($fh)) {
        $chunk = fread($fh, HASH_CHUNK_BYTES);
        if ($chunk === false) {
            fclose($fh);
            $doneBytes += max(0, $expectedSize - $read);
            return null;
        }
        if ($chunk === '') {
            break;
        }
        hash_update($ctx, $chunk);
        $n = strlen($chunk);
        $read      += $n;
        $doneBytes += $n;
        $progress->update($doneBytes, $doneFiles, $relPath);
    }
    fclose($fh);

    // If the file grew/shrank while we were reading, re-sync with the planned total.
    $doneBytes += $expectedSize - $read;

    return hash_final($ctx);
}

// ============================================================
// BASELINE I/O
// ============================================================

function loadBaseline(string $file): ?array
{
    if (!is_file($file)) {
        return null;
    }
    $json = file_get_contents($file);
    if ($json === false) {
        return null;
    }
    $data = json_decode($json, true);
    return is_array($data) ? $data : null;
}

function saveBaseline(string $file, array $files): bool
{
    $dir = dirname($file);
    if (!is_dir($dir) && !mkdir($dir, 0750, true) && !is_dir($dir)) {
        return false;
    }

    $json = json_encode($files, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        fwrite(STDERR, 'ERROR: could not encode baseline as JSON: ' . json_last_error_msg() . "\n");
        return false;
    }

    // Write to a temp file and rename, so a crash never leaves a half-written baseline.
    $tmp = $file . '.tmp.' . getmypid();
    if (file_put_contents($tmp, $json) === false) {
        return false;
    }
    chmod($tmp, 0640);
    return rename($tmp, $file);
}

// ============================================================
// REPORT (shared by console output and email)
// ============================================================

function buildReport(
    array $missing,
    array $extra,
    array $changed,
    array $unreadable,
    int $baselineCount,
    int $currentCount,
    bool $hashOnly,
    bool $verbose
): string {
    $out  = "=== File Integrity Report ===\n";
    $out .= 'Host           : ' . gethostname() . "\n";
    $out .= 'Time           : ' . date('Y-m-d H:i:s T') . "\n";
    $out .= 'Mode           : ' . ($hashOnly ? 'hash-only (size/content changes)' : 'full') . "\n";
    $out .= 'Baseline files : ' . $baselineCount . "\n";
    $out .= 'Current files  : ' . $currentCount . "\n";
    $out .= 'Missing        : ' . count($missing) . "\n";
    $out .= 'Extra          : ' . count($extra) . "\n";
    $out .= 'Changed        : ' . count($changed) . "\n";
    $out .= 'Unreadable     : ' . count($unreadable) . "\n\n";

    if ($missing) {
        $out .= "--- MISSING FILES (were in baseline, now gone) ---\n";
        foreach ($missing as $f) {
            $out .= " - {$f}\n";
        }
        $out .= "\n";
    }

    if ($extra) {
        $out .= "--- EXTRA FILES (present now, not in baseline) ---\n";
        foreach ($extra as $f) {
            $out .= " + {$f}\n";
        }
        $out .= "\n";
    }

    if ($changed) {
        $out .= "--- CHANGED FILES ---\n";
        foreach ($changed as $f => $info) {
            $b = $info['baseline'];
            $c = $info['current'];
            $out .= " ~ {$f} ({$info['reason']})\n";
            if ($verbose) {
                $out .= '     size : ' . ($b['size'] ?? '?') . ' → ' . $c['size'] . "\n";
                $out .= '     mtime: ' . date('Y-m-d H:i:s', (int) ($b['mtime'] ?? 0))
                . ' → ' . date('Y-m-d H:i:s', $c['mtime']) . "\n";
                $out .= '     hash : ' . ($b['hash'] ?? '?') . "\n";
                $out .= '          → ' . ($c['hash'] ?? '(not hashed, size already differs)') . "\n";
            }
        }
        $out .= "\n";
    }

    if ($unreadable) {
        $out .= "--- UNREADABLE (could not be checked) ---\n";
        foreach ($unreadable as $f => $why) {
            $out .= " ? {$f} ({$why})\n";
        }
        $out .= "\n";
    }

    return $out;
}

function sendAlertEmail(string $to, string $from, string $subject, string $report): bool
{
    $body  = "File Integrity Check Alert\n\n" . $report;
    $body .= "Please investigate immediately.\n";

    $headers = [
        'From: ' . $from,
        'Content-Type: text/plain; charset=UTF-8',
        'X-Mailer: FileIntegrityChecker',
    ];

    return mail($to, $subject, $body, implode("\r\n", $headers));
}

// ============================================================
// MAIN
// ============================================================

$options = getopt('', ['generate-baseline', 'hash-only', 'verbose', 'no-email', 'help']);

if (isset($options['help'])) {
    echo <<<HELP
    File Integrity Checker (SHA-256 + email alerts)

    --generate-baseline   Scan {$scanRoot} and write a new baseline
    --hash-only           Ignore mtime-only changes (size/content changes are still reported)
    --verbose             Show detailed size / mtime / hash info
    --no-email            Don't send the alert email, even if enabled in the config
    --help                This help

    HELP;
    exit(0);
}

$verbose  = isset($options['verbose']);
$hashOnly = isset($options['hash-only']);
$generate = isset($options['generate-baseline']);
if (isset($options['no-email'])) {
    $emailEnabled = false;
}

// In compare mode, fail early if there is no baseline (before spending time hashing).
$baseline = null;
if (!$generate) {
    $baseline = loadBaseline($baselineFile);
    if ($baseline === null) {
        fwrite(STDERR, "ERROR: Baseline not found or unreadable at {$baselineFile}\n");
        fwrite(STDERR, "Run with --generate-baseline first (on a clean system).\n");
        exit(1);
    }
}

$ignoreRegexes = compileIgnorePatterns($ignorePaths);
$unreadable    = [];
$startedAt     = microtime(true);

// ---------- Phase 1: file sizes ----------
fwrite(STDERR, "Phase 1/2: reading file sizes under {$scanRoot} ...\n");
$current = scanSizes($scanRoot, $ignoreRegexes, $ignoreExtensions, $followSymlinks, $unreadable);

$totalSize = 0;
foreach ($current as $f) {
    $totalSize += $f['size'];
}

// Decide what actually needs hashing.
$toHash       = [];
$sizeMismatch = 0;
foreach ($current as $rel => $f) {
    $rel = (string) $rel;
    if ($generate) {
        $toHash[] = $rel;
        continue;
    }
    if (!isset($baseline[$rel])) {
        continue; // extra file: reported without hashing
    }
    if ((int) ($baseline[$rel]['size'] ?? -1) !== $f['size']) {
        $sizeMismatch++; // size differs => content differs; no need to hash
        continue;
    }
    $toHash[] = $rel;
}

$bytesToHash = 0;
foreach ($toHash as $rel) {
    $bytesToHash += $current[$rel]['size'];
}

fwrite(STDERR, sprintf(
    "  %s files, %s total\n",
    number_format(count($current)),
                       formatBytes($totalSize)
));
if (!$generate) {
    fwrite(STDERR, sprintf(
        "  %s file(s) already differ in size from the baseline (will not be hashed)\n",
                           number_format($sizeMismatch)
    ));
}
fwrite(STDERR, sprintf(
    "  %s file(s), %s to hash\n\n",
                       number_format(count($toHash)),
                       formatBytes($bytesToHash)
));

// ---------- Phase 2: hashing ----------
if ($toHash) {
    fwrite(STDERR, "Phase 2/2: hashing ...\n");

    $progress   = new ByteProgress($bytesToHash, count($toHash));
    $doneBytes  = 0;
    $doneFiles  = 0;
    $rootPrefix = rtrim($scanRoot, '/') . '/';

    foreach ($toHash as $rel) {
        $hash = hashFileWithProgress(
            $rootPrefix . $rel,
            $rel,
            $current[$rel]['size'],
            $progress,
            $doneBytes,
            $doneFiles
        );
        $doneFiles++;

        if ($hash === null) {
            $unreadable[$rel] = 'file not readable';
            unset($current[$rel]['hash']);
        } else {
            $current[$rel]['hash'] = $hash;
        }
        $progress->update($doneBytes, $doneFiles, $rel);
    }

    $progress->finish($doneFiles);
    fwrite(STDERR, sprintf(
        "Done in %s.\n\n",
        formatDuration(microtime(true) - $startedAt)
    ));
}

// ---------- Generate-baseline mode ----------
if ($generate) {
    // Unreadable files have no hash; leave them out of the baseline.
    $toSave = array_filter($current, static function (array $f): bool {
        return isset($f['hash']);
    });

    if (!saveBaseline($baselineFile, $toSave)) {
        fwrite(STDERR, "ERROR: could not write baseline to {$baselineFile}\n");
        exit(1);
    }

    echo "Baseline written to: {$baselineFile}\n";
    echo 'Total files recorded: ' . count($toSave) . "\n";
    if ($unreadable) {
        echo 'WARNING: ' . count($unreadable) . " item(s) could not be read and are NOT in the baseline:\n";
        foreach ($unreadable as $f => $why) {
            echo " ? {$f} ({$why})\n";
        }
    }
    exit(0);
}

// ---------- Compare mode ----------
$missing = [];
$extra   = [];
$changed = [];

foreach ($baseline as $rel => $b) {
    $rel = (string) $rel;

    if (isset($unreadable[$rel])) {
        continue; // reported under "unreadable"
    }
    if (!isset($current[$rel])) {
        $missing[] = $rel;
        continue;
    }

    $c = $current[$rel];

    if ((int) ($b['size'] ?? -1) !== $c['size']) {
        $changed[$rel] = ['baseline' => $b, 'current' => $c, 'reason' => 'size changed'];
        continue;
    }

    if (($b['hash'] ?? null) !== ($c['hash'] ?? null)) {
        $changed[$rel] = ['baseline' => $b, 'current' => $c, 'reason' => 'content hash mismatch'];
        continue;
    }

    if (!$hashOnly && (int) ($b['mtime'] ?? 0) !== $c['mtime']) {
        $changed[$rel] = ['baseline' => $b, 'current' => $c, 'reason' => 'mtime changed (content identical)'];
    }
}

foreach ($current as $rel => $_) {
    $rel = (string) $rel;
    if (!isset($baseline[$rel]) && !isset($unreadable[$rel])) {
        $extra[] = $rel;
    }
}

sort($missing, SORT_STRING);
sort($extra, SORT_STRING);
ksort($changed, SORT_STRING);
ksort($unreadable, SORT_STRING);

$report = buildReport(
    $missing,
    $extra,
    $changed,
    $unreadable,
    count($baseline),
                      count($current),
                      $hashOnly,
                      $verbose
);
echo $report;

$hasIssues = $missing || $extra || $changed || $unreadable;

if (!$hasIssues) {
    echo "✓ No differences found. Filesystem matches baseline.\n";
    exit(0);
}

echo "⚠ Differences detected. Investigate immediately.\n";

if ($emailEnabled) {
    // The email always includes full detail (sizes, mtimes, hashes).
    $emailReport = buildReport(
        $missing,
        $extra,
        $changed,
        $unreadable,
        count($baseline),
                               count($current),
                               $hashOnly,
                               true
    );

    if (sendAlertEmail($emailTo, $emailFrom, $emailSubject, $emailReport)) {
        echo "Email alert sent to {$emailTo}\n";
    } else {
        echo "WARNING: Failed to send email alert.\n";
    }
}

exit(2);
?>
