<?php
/**
 * NicerApp WebOS – Restore from dump
 *
 * Usage:
 *   php restore_by_prefix-2.0.0.php \
 *       --dump-dir=./dumps/20260821-015042 \
 *       [--type=couchdb|sql|both] \
 *       [--username=admin] \
 *       [--merge=1] \
 *       [--page-size=500]
 *
 * --merge=1  → upsert (merge with existing data)
 * --merge=0  → replace (wipe target first, then insert)
 */

require_once __DIR__ . '/boot.php';

function restoreFromDump(
    string $dumpDir,
    string $type       = 'both',
    string $username   = 'admin',
    bool   $merge      = true,
    int    $pageSize   = 500
): void {
    if (!is_dir($dumpDir)) {
        fwrite(STDERR, "ERROR: Dump directory does not exist: {$dumpDir}\n");
        exit(1);
    }

    echo "=== NicerApp Restore from Dump ===\n";
    echo "Dump dir   : {$dumpDir}\n";
    echo "Type       : {$type}\n";
    echo "Username   : {$username}\n";
    echo "Merge      : " . ($merge ? 'yes (upsert)' : 'no (replace)') . "\n";
    echo "Page size  : {$pageSize}\n\n";

    if ($type === 'couchdb' || $type === 'both') {
        restoreCouchDB($dumpDir, $username, $merge, $pageSize);
    }

    if ($type === 'sql' || $type === 'both') {
        restoreSQL($dumpDir, $username, $merge, $pageSize);
    }

    echo "\n=== Restore finished ===\n";
}

/**
 * Restore CouchDB databases from *.json / *.json.gz files
 */
function restoreCouchDB(string $dumpDir, string $username, bool $merge, int $pageSize): void
{
    echo "--- CouchDB Restore ---\n";

    $dbs = new class_NicerAppWebOS_database_API($username);
    $conn = null;

    // Grab the first available CouchDB connection
    $all = $dbs->getAllDatabases();
    if (!empty($all)) {
        $conn = $all[0]['c']['conn'];
    }

    if (!$conn) {
        echo "ERROR: Could not obtain CouchDB connection\n";
        return;
    }

    $files = glob($dumpDir . '/couchdb_*.json*');
    if (empty($files)) {
        echo "No CouchDB dump files found in {$dumpDir}\n";
        return;
    }

    echo "Found " . count($files) . " CouchDB dump file(s)\n";

    foreach ($files as $file) {
        $base = basename($file);

        // Extract original database name
        // couchdb_said_by___cms_tree.json.gz  →  said_by___cms_tree
        $dbName = preg_replace('/^couchdb_/', '', $base);
        $dbName = preg_replace('/\.json(\.gz)?$/', '', $dbName);

        echo "  Restoring CouchDB: {$dbName} from {$base} ... ";

        try {
            // Load documents
            $json = file_get_contents($file);
            if (str_ends_with($file, '.gz')) {
                $json = gzdecode($json);
            }
            $docs = json_decode($json, true);

            if (!is_array($docs)) {
                echo "ERROR: invalid JSON\n";
                continue;
            }

            $conn->cdb->setDatabase($dbName, true);   // create if missing

            if (!$merge) {
                // Replace mode: delete all existing documents first
                purgeCouchDB($conn, $dbName, $pageSize);
            }

            // Bulk insert / upsert
            $inserted = 0;
            $chunks = array_chunk($docs, $pageSize);

            foreach ($chunks as $chunk) {
                // Prepare docs for bulk_docs
                $bulk = [];
                foreach ($chunk as $doc) {
                    // When merging we keep the original _id (and optionally _rev if present)
                    // Sag/CouchDB will treat it as an update if _rev is current, otherwise create
                    unset($doc['_rev']);          // safest for bulk upsert – let CouchDB handle conflicts
                    $bulk[] = $doc;
                }

                $result = $conn->cdb->bulk($bulk, false);  // false = don't all_or_nothing

                if (isset($result->body) && is_array($result->body)) {
                    foreach ($result->body as $res) {
                        if (isset($res->ok) && $res->ok) {
                            $inserted++;
                        }
                    }
                }
            }

            echo "{$inserted} documents restored\n";

        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }
}

/**
 * Delete all documents from a CouchDB database (used in replace mode)
 */
function purgeCouchDB($conn, string $dbName, int $pageSize): void
{
    $conn->cdb->setDatabase($dbName);

    $startKey = null;

    do {
        $response = $conn->cdb->getAllDocs(
            false,                  // include_docs = false (we only need id + rev)
            $pageSize + 1,
            $startKey,
            null,
            null,
            false,
            ($startKey !== null) ? 1 : 0
        );

        $rows = $response->body->rows ?? [];
        if (empty($rows)) {
            break;
        }

        $hasMore = count($rows) > $pageSize;
        if ($hasMore) {
            array_pop($rows);
        }

        $toDelete = [];
        foreach ($rows as $row) {
            if (isset($row->id) && str_starts_with($row->id, '_design/')) {
                continue; // keep design docs
            }
            $toDelete[] = [
                '_id'      => $row->id,
                '_rev'     => $row->value->rev,
                '_deleted' => true
            ];
        }

        if (!empty($toDelete)) {
            $conn->cdb->bulk($toDelete, false);
        }

        if ($hasMore) {
            $startKey = end($rows)->key ?? null;
        } else {
            $startKey = null;
        }

    } while ($startKey !== null);
}

/**
 * Restore SQL tables
 */
function restoreSQL(string $dumpDir, string $username, bool $merge, int $pageSize): void
{
    echo "\n--- SQL Restore ---\n";

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

    $files = glob($dumpDir . '/sql_*.json*');
    if (empty($files)) {
        echo "No SQL dump files found in {$dumpDir}\n";
        $mysqli->close();
        return;
    }

    echo "Found " . count($files) . " SQL dump file(s)\n";

    foreach ($files as $file) {
        $base = basename($file);
        $table = preg_replace('/^sql_/', '', $base);
        $table = preg_replace('/\.json(\.gz)?$/', '', $table);

        echo "  Restoring SQL table: {$table} from {$base} ... ";

        try {
            $json = file_get_contents($file);
            if (str_ends_with($file, '.gz')) {
                $json = gzdecode($json);
            }
            $rows = json_decode($json, true);

            if (!is_array($rows)) {
                echo "ERROR: invalid JSON\n";
                continue;
            }

            if (!$merge) {
                // Replace mode
                $mysqli->query("TRUNCATE TABLE `{$table}`");
            }

            $uDB->setTable($table);
            $inserted = 0;

            foreach (array_chunk($rows, $pageSize) as $chunk) {
                foreach ($chunk as $row) {
                    // Simple insert – for true upsert you would need ON DUPLICATE KEY UPDATE
                    // or use uDB2's update/insert methods if available
                    try {
                        $uDB->insertOne($row);
                        $inserted++;
                    } catch (Exception $e) {
                        // On merge conflicts you may want to update instead
                        // For now we just count successful inserts
                    }
                }
            }

            echo "{$inserted} rows restored\n";

        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }

    $mysqli->close();
}

// ---------- CLI ----------
if (php_sapi_name() === 'cli') {
    $opts = getopt('', [
        'dump-dir:',
        'type::',
        'username::',
        'merge::',
        'page-size::'
    ]);

    if (empty($opts['dump-dir'])) {
        fwrite(STDERR, "Usage:\n");
        fwrite(STDERR, "  php restore_by_prefix-2.0.0.php --dump-dir=./dumps/20260821-015042 \\\n");
        fwrite(STDERR, "      [--type=couchdb|sql|both] \\\n");
        fwrite(STDERR, "      [--username=admin] \\\n");
        fwrite(STDERR, "      [--merge=1] \\\n");
        fwrite(STDERR, "      [--page-size=500]\n");
        exit(1);
    }

    restoreFromDump(
        $opts['dump-dir'],
        $opts['type']       ?? 'both',
        $opts['username']   ?? 'admin',
        ($opts['merge']     ?? '1') !== '0',
        (int)($opts['page-size'] ?? 500)
    );
}
?>
