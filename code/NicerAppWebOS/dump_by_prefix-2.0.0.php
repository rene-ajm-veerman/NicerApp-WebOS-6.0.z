<?php
/**
 * NicerApp WebOS – Database dump by prefix
 * Supports CouchDB + SQL, pagination, and gzip compression.
 *
 * Usage (CLI):
 *   php dump_by_prefix.php --prefix="said_by___" [--type=couchdb|sql|both] [--username=admin] [--out=./dumps] [--page-size=1000] [--compress=1]
 */

require_once __DIR__ . '/boot.php';   // adjust path if needed

/**
 * Main entry
 */
function dumpByPrefix(
    string $prefix,
    string $type       = 'both',      // couchdb | sql | both
    string $username   = 'admin',
    string $outDir     = './dumps',
    int    $pageSize   = 1000,
    bool   $compress   = true
): void {
    if (!is_dir($outDir)) {
        mkdir($outDir, 0755, true);
    }

    echo "=== NicerApp Dump by Prefix ===\n";
    echo "Prefix    : {$prefix}\n";
    echo "Type      : {$type}\n";
    echo "Username  : {$username}\n";
    echo "Output    : {$outDir}\n";
    echo "Page size : {$pageSize}\n";
    echo "Compress  : " . ($compress ? 'yes (gzip)' : 'no') . "\n\n";

    if ($type === 'couchdb' || $type === 'both') {
        dumpCouchDBByPrefix($prefix, $username, $outDir, $pageSize, $compress);
    }

    if ($type === 'sql' || $type === 'both') {
        dumpSQLByPrefix($prefix, $username, $outDir, $pageSize, $compress);
    }

    echo "\n=== Done ===\n";
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
                $params = [
                    'include_docs' => true,
                    'limit'        => $pageSize + 1,   // +1 to detect more pages
                ];

                if ($startKey !== null) {
                    $params['startkey'] = json_encode($startKey);
                    $params['skip']     = 1;            // skip the previous last key
                }

                $response = $conn->cdb->getAllDocs($params);
                $rows     = $response->body->rows ?? [];

                if (empty($rows)) {
                    break;
                }

                $hasMore = count($rows) > $pageSize;
                if ($hasMore) {
                    array_pop($rows);                   // remove the extra one
                }

                foreach ($rows as $row) {
                    if (isset($row->id) && str_starts_with($row->id, '_design/')) {
                        continue;                       // skip design docs
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
 * Uses uDB2 (mysqli path) – adjust connection details if needed
 */
function dumpSQLByPrefix(
    string $prefix,
    string $username,
    string $outDir,
    int    $pageSize,
    bool   $compress
): void {
    echo "\n--- SQL ---\n";

    // Example connection – replace with your real credentials / config loading
    // You can also load from $naWebOS->cfg or domainConfig files
    global $naWebOS;

    $cRec = [
        'driver'   => 'mysqli',
        'host'     => $naWebOS->cfg['sql']['host']     ?? 'localhost',
        'username' => $naWebOS->cfg['sql']['username'] ?? 'root',
        'password' => $naWebOS->cfg['sql']['password'] ?? '',
        'database' => $naWebOS->cfg['sql']['database'] ?? '',   // empty = list tables across all DBs if allowed
    ];

    try {
        $uDB = uDB2::createFromConfig($cRec, $username);
    } catch (Exception $e) {
        echo "Could not connect to SQL: " . $e->getMessage() . "\n";
        return;
    }

    // List tables that match the prefix
    // Note: uDB2 does not expose a native listTables yet, so we use raw mysqli
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

            $rows  = [];
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
        'compress::'
    ]);

    if (empty($opts['prefix'])) {
        fwrite(STDERR, "Usage:\n");
        fwrite(STDERR, "  php dump_by_prefix.php --prefix=NAME [--type=couchdb|sql|both] [--username=admin] [--out=./dumps] [--page-size=1000] [--compress=1]\n");
        exit(1);
    }

    dumpByPrefix(
        $opts['prefix'],
        $opts['type']       ?? 'both',
        $opts['username']   ?? 'admin',
        $opts['out']        ?? './dumps',
        (int)($opts['page-size'] ?? 1000),
        ($opts['compress'] ?? '1') !== '0'
    );
}
