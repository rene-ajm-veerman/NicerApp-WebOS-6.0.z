<?php
/**
 * NicerApp WebOS – Database dump by prefix
 * Supports CouchDB + SQL, pagination, gzip compression,
 * timestamped output folders and automatic retention.
 *
 * (C) 2026 Rene AJM Veerman and https://grok.com
 * MIT licensed
 *
 * Usage:
 *   php dump_by_prefix-2.0.0.php --prefix="said_by___" \
 *       [--type=couchdb|sql|both] \
 *       [--username=admin] \
 *       [--out=./dumps] \
 *       [--page-size=2000] \
 *       [--compress=1] \
 *       [--retain-days=14]
 */

require_once __DIR__ . '/../boot.php';

function dumpByPrefix(
    string $prefix,
    string $type        = 'both',
    string $username    = 'admin',
    string $baseOutDir  = '../../backups/dbContents',
    int    $pageSize    = 1000,
    bool   $compress    = true,
    int    $retainDays  = 14
): void {
    // Create timestamped directory: ./dumps/20260821-015042
    $timestamp = date('Ymd-His');
    $outDir    = rtrim($baseOutDir, '/') . '/' . $timestamp;

    if (!is_dir($outDir)) {
        mkdir($outDir, 0755, true);
    }

    echo "=== NicerApp Dump by Prefix ===\n";
    echo "Prefix       : {$prefix}\n";
    echo "Type         : {$type}\n";
    echo "Username     : {$username}\n";
    echo "Output       : {$outDir}\n";
    echo "Page size    : {$pageSize}\n";
    echo "Compress     : " . ($compress ? 'yes (gzip)' : 'no') . "\n";
    echo "Retain days  : {$retainDays}\n\n";

    if ($type === 'couchdb' || $type === 'both') {
        dumpCouchDBByPrefix($prefix, $username, $outDir, $pageSize, $compress);
    }

    if ($type === 'sql' || $type === 'both') {
        dumpSQLByPrefix($prefix, $username, $outDir, $pageSize, $compress);
    }

    // Clean up old dump folders
    cleanupOldDumps($baseOutDir, $retainDays);

    echo "\n=== Done ===\n";
    echo "Dump saved to: {$outDir}\n";
}

/**
 * Delete dump folders older than $retainDays
 */
function cleanupOldDumps(string $baseOutDir, int $retainDays): void
{
    if ($retainDays <= 0) {
        return;
    }

    $baseOutDir = rtrim($baseOutDir, '/');
    if (!is_dir($baseOutDir)) {
        return;
    }

    $cutoff = time() - ($retainDays * 86400);
    $deleted = 0;

    foreach (scandir($baseOutDir) as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $baseOutDir . '/' . $item;

        // Only consider directories that look like YYYYMMDD-HHMMSS
        if (!is_dir($path) || !preg_match('/^\d{8}-\d{6}$/', $item)) {
            continue;
        }

        $mtime = filemtime($path);
        if ($mtime !== false && $mtime < $cutoff) {
            // Recursive delete
            $it = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($path);
            $deleted++;
            echo "Deleted old dump: {$item}\n";
        }
    }

    if ($deleted > 0) {
        echo "Cleaned up {$deleted} old dump folder(s) (older than {$retainDays} days)\n";
    }
}

/**
 * CouchDB dump with pagination + optional gzip
 */
function dumpCouchDBByPrefix(
    string $prefix,
    string $username,
    string $outDir,
    int    $pageSize,
    bool   $compress
): void {
    echo "--- CouchDB ---\n";

    $dbs = new class_NicerAppWebOS_database_API($username);
    $all = $dbs->getAllDatabases();

    $matched = [];
    foreach ($all as $entry) {
        $conn    = $entry['c']['conn'];
        $dbNames = $entry['x']->body ?? [];

        foreach ($dbNames as $dbName) {
            if (str_starts_with(strtolower($dbName), strtolower($prefix))) {
                $matched[] = ['name' => $dbName, 'conn' => $conn];
            }
        }
    }

    if (empty($matched)) {
        echo "No CouchDB databases match prefix '{$prefix}'\n";
        return;
    }

    echo "Found " . count($matched) . " matching database(s)\n";

    foreach ($matched as $item) {
        $dbName = $item['name'];
        $conn   = $item['conn'];

        echo "  Dumping CouchDB: {$dbName} ... ";

        try {
            $conn->cdb->setDatabase($dbName);

            $docs     = [];
            $startKey = null;
            $total    = 0;

            do {
                // Sag expects positional arguments
                $response = $conn->cdb->getAllDocs(
                    true,                           // include_docs = true (boolean)
                    $pageSize + 1,                  // limit (+1 to detect more pages)
                    $startKey,                      // startkey
                    null,                           // endkey
                    null,                           // keys
                    false,                          // descending
                    ($startKey !== null) ? 1 : 0    // skip previous last key
                );

                $rows = $response->body->rows ?? [];

                if (empty($rows)) {
                    break;
                }

                $hasMore = count($rows) > $pageSize;
                if ($hasMore) {
                    array_pop($rows);
                }

                foreach ($rows as $row) {
                    if (isset($row->id) && str_starts_with($row->id, '_design/')) {
                        continue;
                    }
                    if (isset($row->doc)) {
                        $docs[] = $row->doc;
                        $total++;
                    }
                }

                if ($hasMore) {
                    $startKey = end($rows)->key ?? null;
                } else {
                    $startKey = null;
                }

            } while ($startKey !== null);

            $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $dbName);
            $file     = rtrim($outDir, '/') . "/couchdb_{$safeName}.json";

            $json = json_encode($docs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if ($compress) {
                $file .= '.gz';
                file_put_contents($file, gzencode($json, 9));
            } else {
                file_put_contents($file, $json);
            }

            echo "{$total} documents → {$file}\n";

        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }
}

/**
 * SQL dump with pagination + optional gzip
 */
function dumpSQLByPrefix(
    string $prefix,
    string $username,
    string $outDir,
    int    $pageSize,
    bool   $compress
): void {
    echo "\n--- SQL ---\n";

    global $naWebOS;

    $cRec = [
        'driver'   => 'mysqli',
        'host'     => $naWebOS->cfg['sql']['host']     ?? 'localhost',
        'username' => $naWebOS->cfg['sql']['username'] ?? 'root',
        'password' => $naWebOS->cfg['sql']['password'] ?? '',
        'database' => $naWebOS->cfg['sql']['database'] ?? '',
    ];

    try {
        $uDB = uDB2::createFromConfig($cRec, $username);
    } catch (Exception $e) {
        echo "Could not connect to SQL: " . $e->getMessage() . "\n";
        return;
    }

    $mysqli = new mysqli(
        $cRec['host'],
        $cRec['username'],
        $cRec['password'],
        $cRec['database'] ?: null
    );

    if ($mysqli->connect_error) {
        echo "SQL connection failed: " . $mysqli->connect_error . "\n";
        return;
    }

    $tables = [];
    $result = $mysqli->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        $tableName = $row[0];
        if (str_starts_with(strtolower($tableName), strtolower($prefix))) {
            $tables[] = $tableName;
        }
    }

    if (empty($tables)) {
        echo "No SQL tables match prefix '{$prefix}'\n";
        $mysqli->close();
        return;
    }

    echo "Found " . count($tables) . " matching table(s)\n";

    foreach ($tables as $table) {
        echo "  Dumping SQL table: {$table} ... ";

        try {
            $uDB->setTable($table);

            $rows   = [];
            $offset = 0;
            $total  = 0;

            do {
                $page = $uDB->find([], [
                    'limit' => $pageSize,
                    'skip'  => $offset
                ]);

                if (empty($page)) {
                    break;
                }

                foreach ($page as $row) {
                    $rows[] = $row;
                    $total++;
                }

                $offset += $pageSize;

            } while (count($page) === $pageSize);

            $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $table);
            $file     = rtrim($outDir, '/') . "/sql_{$safeName}.json";

            $json = json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if ($compress) {
                $file .= '.gz';
                file_put_contents($file, gzencode($json, 9));
            } else {
                file_put_contents($file, $json);
            }

            echo "{$total} rows → {$file}\n";

        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }

    $mysqli->close();
}

// ---------- CLI ----------
if (php_sapi_name() === 'cli') {
    $opts = getopt('', [
        'prefix:',
        'type::',
        'username::',
        'out::',
        'page-size::',
        'compress::',
        'retain-days::'
    ]);

    if (empty($opts['prefix'])) {
        fwrite(STDERR, "Usage:\n");
        fwrite(STDERR, "  php dump_by_prefix-3.0.0.php --prefix=NAME \\\n");
        fwrite(STDERR, "      [--type=couchdb|sql|both] \\\n");
        fwrite(STDERR, "      [--username=admin] \\\n");
        fwrite(STDERR, "      [--out=./dumps] \\\n");
        fwrite(STDERR, "      [--page-size=2000] \\\n");
        fwrite(STDERR, "      [--compress=1] \\\n");
        fwrite(STDERR, "      [--retain-days=14]\n");
        exit(1);
    }

    dumpByPrefix(
        $opts['prefix'],
        $opts['type']         ?? 'both',
        $opts['username']     ?? 'admin',
        $opts['out']          ?? './dumps',
        (int)($opts['page-size']   ?? 1000),
        ($opts['compress']    ?? '1') !== '0',
        (int)($opts['retain-days'] ?? 14)
    );
}
?>
