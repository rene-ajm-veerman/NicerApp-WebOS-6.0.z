<?php
/**
 * File Integrity Checker
 * Compares current files under /var/www against a known-good baseline.
 * Uses SHA-256 content hashes + optional email alerts.
 *
 * Usage:
 *   php file_integrity_check.php --generate-baseline
 *   php file_integrity_check.php
 *   php file_integrity_check.php --hash-only
 *   php file_integrity_check.php --verbose
 *   php file_integrity_check.php --hash-only --verbose
 */

declare(strict_types=1);

// ============================================================
// CONFIGURATION
// ============================================================

$scanRoot = '/var/www';

// Where to store the baseline (keep this outside the web root if possible)
$baselineFile = __DIR__ . '/../data/file_baseline.json';

// Directories / patterns to ignore
$ignorePaths = [
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

// File extensions to ignore (optional)
$ignoreExtensions = [
    // 'log', 'tmp', 'cache',
];

// -------------------- Email settings --------------------
$emailEnabled   = true;                          // set to false to disable
$emailTo        = 'rene.veerman.netherlands@gmail.com';           // <-- change this
$emailFrom      = 'integrity-check@nicer.app';
$emailSubject   = '[ALERT] File integrity differences detected on ' . gethostname();
// --------------------------------------------------------

// ============================================================
// HELPERS
// ============================================================
function updateProgress(int $current, int $total, string $path): void
{
    // Get terminal width (fallback to 80)
    $cols = (int) trim(shell_exec('tput cols') ?: 80);
    $prefix = sprintf('[%d/%d] [%.2f%%] ', $current, $total, ($current / $total) * 100);
    $available = $cols - mb_strlen($prefix) - 1;

    // Truncate the path so the whole line fits on one line
    if (mb_strlen($path) + mb_strlen($prefix) > $available) {
        $path = '…' . mb_substr($path, -($available - 1));
    }

    // Clear the line and rewrite it
    echo "\033[2K\r" . $prefix . $path;
}

function shouldIgnore(string $relativePath, array $ignorePaths, array $ignoreExtensions): bool
{
    $relativePath = str_replace('\\', '/', $relativePath);

    foreach ($ignorePaths as $pattern) {
        $regex = '#^' . str_replace(
            ['*', '?'],
            ['.*', '.'],
            preg_quote($pattern, '#')
        ) . '$#i';

        if (preg_match($regex, $relativePath) || preg_match($regex, '/' . $relativePath)) {
            return true;
        }
    }

    $ext = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
    if ($ext && in_array($ext, $ignoreExtensions, true)) {
        return true;
    }

    return false;
}

function collectFiles(string $root, array $ignorePaths, array $ignoreExtensions): array
{
    $files = [];
    $root = rtrim($root, '/');

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $idx = 0;
    $count = 0;
    foreach ($iterator as $item) { $count++; };

    foreach ($iterator as $item) {
        /** @var SplFileInfo $item */
        if (!$item->isFile()) {
            continue;
        }
        $idx++;

        $fullPath = $item->getPathname();
        updateProgress($idx, $count, $fullPath);
        $relative = substr($fullPath, strlen($root) + 1);
        $relative = str_replace('\\', '/', $relative);

        if (shouldIgnore($relative, $ignorePaths, $ignoreExtensions)) {
            continue;
        }

        $files[$relative] = [
            'size'  => $item->getSize(),
            'mtime' => $item->getMTime(),
            'hash'  => hash_file('sha256', $fullPath),
        ];
    }

    ksort($files);
    return $files;
}

function loadBaseline(string $file): ?array
{
    if (!is_file($file)) {
        return null;
    }
    $json = file_get_contents($file);
    $data = json_decode($json, true);
    return is_array($data) ? $data : null;
}

function saveBaseline(string $file, array $files): void
{
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0750, true);
    }
    file_put_contents(
        $file,
        json_encode($files, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    );
    chmod($file, 0640);
}

function sendAlertEmail(
    string $to,
    string $from,
    string $subject,
    array $missing,
    array $extra,
    array $changed,
    bool $hashOnly
): bool {
    $body = "File Integrity Check Alert\n";
    $body .= "Host      : " . gethostname() . "\n";
    $body .= "Time      : " . date('Y-m-d H:i:s T') . "\n";
    $body .= "Mode      : " . ($hashOnly ? 'hash-only (content changes)' : 'full') . "\n\n";

    $body .= "Summary\n";
    $body .= "-------\n";
    $body .= "Missing  : " . count($missing) . "\n";
    $body .= "Extra    : " . count($extra) . "\n";
    $body .= "Changed  : " . count($changed) . "\n\n";

    if (!empty($missing)) {
        $body .= "MISSING FILES\n";
        $body .= str_repeat('-', 40) . "\n";
        foreach ($missing as $f) {
            $body .= "  - {$f}\n";
        }
        $body .= "\n";
    }

    if (!empty($extra)) {
        $body .= "EXTRA FILES\n";
        $body .= str_repeat('-', 40) . "\n";
        foreach ($extra as $f) {
            $body .= "  + {$f}\n";
        }
        $body .= "\n";
    }

    if (!empty($changed)) {
        $body .= "CHANGED FILES\n";
        $body .= str_repeat('-', 40) . "\n";
        foreach ($changed as $f => $info) {
            $body .= "  ~ {$f}  ({$info['reason']})\n";
            $body .= "      old hash: {$info['baseline']['hash']}\n";
            $body .= "      new hash: {$info['current']['hash']}\n";
        }
        $body .= "\n";
    }

    $body .= "\nPlease investigate immediately.\n";

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

$options = getopt('', ['generate-baseline', 'hash-only', 'verbose', 'help']);

if (isset($options['help'])) {
    echo <<<HELP
File Integrity Checker (SHA-256 + email alerts)

  --generate-baseline   Scan /var/www and write a new baseline
  --hash-only           Only report real content changes (ignore size/mtime-only)
  --verbose             Show detailed size / mtime / hash info
  --help                This help

HELP;
    exit(0);
}

$verbose  = isset($options['verbose']);
$hashOnly = isset($options['hash-only']);

echo "Scanning {$scanRoot} ...\n";
$current = collectFiles($scanRoot, $ignorePaths, $ignoreExtensions);
echo "Found " . count($current) . " files.\n\n";

if (isset($options['generate-baseline'])) {
    saveBaseline($baselineFile, $current);
    echo "Baseline written to: {$baselineFile}\n";
    echo "Total files recorded: " . count($current) . "\n";
    exit(0);
}

// Compare mode
$baseline = loadBaseline($baselineFile);

if ($baseline === null) {
    fwrite(STDERR, "ERROR: Baseline not found at {$baselineFile}\n");
    fwrite(STDERR, "Run with --generate-baseline first (on a clean system).\n");
    exit(1);
}

$baselineFiles = array_keys($baseline);
$currentFiles  = array_keys($current);

$missing = array_diff($baselineFiles, $currentFiles);
$extra   = array_diff($currentFiles, $baselineFiles);
$common  = array_intersect($baselineFiles, $currentFiles);

$changed = [];
foreach ($common as $rel) {
    $b = $baseline[$rel];
    $c = $current[$rel];

    // Primary check: content hash
    if (($b['hash'] ?? null) !== ($c['hash'] ?? null)) {
        $changed[$rel] = [
            'baseline' => $b,
            'current'  => $c,
            'reason'   => 'content hash mismatch',
        ];
        continue;
    }

    // Secondary: size or mtime only
    if (!$hashOnly && ($b['size'] !== $c['size'] || $b['mtime'] !== $c['mtime'])) {
        $changed[$rel] = [
            'baseline' => $b,
            'current'  => $c,
            'reason'   => 'size/mtime changed (content identical)',
        ];
    }
}

// Report
$hasIssues = !empty($missing) || !empty($extra) || !empty($changed);

echo "=== File Integrity Report ===\n";
echo "Mode           : " . ($hashOnly ? 'hash-only' : 'full') . "\n";
echo "Baseline files : " . count($baseline) . "\n";
echo "Current files  : " . count($current) . "\n";
echo "Missing        : " . count($missing) . "\n";
echo "Extra          : " . count($extra) . "\n";
echo "Changed        : " . count($changed) . "\n\n";

if (!empty($missing)) {
    echo "--- MISSING FILES (were in baseline, now gone) ---\n";
    foreach ($missing as $f) {
        echo "  - {$f}\n";
    }
    echo "\n";
}

if (!empty($extra)) {
    echo "--- EXTRA FILES (present now, not in baseline) ---\n";
    foreach ($extra as $f) {
        echo "  + {$f}\n";
    }
    echo "\n";
}

if (!empty($changed)) {
    echo "--- CHANGED FILES ---\n";
    foreach ($changed as $f => $info) {
        echo "  ~ {$f}  ({$info['reason']})\n";
        if ($verbose) {
            echo "      size : {$info['baseline']['size']} → {$info['current']['size']}\n";
            echo "      mtime: " . date('Y-m-d H:i:s', $info['baseline']['mtime'])
                 . " → " . date('Y-m-d H:i:s', $info['current']['mtime']) . "\n";
            echo "      hash : {$info['baseline']['hash']}\n";
            echo "           → {$info['current']['hash']}\n";
        }
    }
    echo "\n";
}

if (!$hasIssues) {
    echo "✓ No differences found. Filesystem matches baseline.\n";
    exit(0);
}

echo "⚠ Differences detected. Investigate immediately.\n";

// Send email alert
if ($emailEnabled) {
    $sent = sendAlertEmail(
        $emailTo,
        $emailFrom,
        $emailSubject,
        $missing,
        $extra,
        $changed,
        $hashOnly
    );

    if ($sent) {
        echo "Email alert sent to {$emailTo}\n";
    } else {
        echo "WARNING: Failed to send email alert.\n";
    }
}

exit(2);
?>
