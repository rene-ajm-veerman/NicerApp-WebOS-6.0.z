<?php
/**
 * Dump all documents from CouchDB databases that match a name prefix.
 * Uses NicerApp WebOS uDB2 / class_NicerAppWebOS_database_API.
 *
 * Usage (CLI):
 *   php dump_by_prefix.php --prefix="yourprefix" [--username=admin] [--out=./dumps]
 *
 * Or include it and call:
 *   dumpDatabasesByPrefix('yourprefix', 'admin', './dumps');
 */

// Adjust path if needed so boot.php / the uDB classes are found
require_once __DIR__ . '/boot.php';   // or the correct relative path to boot.php

function dumpDatabasesByPrefix(string $prefix, string $username = 'admin', string $outDir = './dumps'): void
{
    global $naWebOS;

    if (!is_dir($outDir)) {
        mkdir($outDir, 0755, true);
    }

    echo "Connecting as user: {$username}\n";
    $dbs = new class_NicerAppWebOS_database_API($username);

    // Get all databases from every connection
    $all = $dbs->getAllDatabases();   // returns [ ['c' => connectionRec, 'x' => Sag response] , ... ]

    $matched = [];
    foreach ($all as $entry) {
        $conn = $entry['c']['conn'];          // class_NicerAppWebOS_database_API_couchdb_3_2__2_0_0
        $dbNames = $entry['x']->body ?? [];   // array of database names

        foreach ($dbNames as $dbName) {
            if (str_starts_with($dbName, $prefix) || str_starts_with(strtolower($dbName), strtolower($prefix))) {
                $matched[] = [
                    'name' => $dbName,
                    'conn' => $conn
                ];
            }
        }
    }

    if (empty($matched)) {
        echo "No databases match prefix '{$prefix}'\n";
        return;
    }

    echo "Found " . count($matched) . " matching database(s):\n";

    foreach ($matched as $item) {
        $dbName = $item['name'];
        $conn   = $item['conn'];

        echo "  Dumping: {$dbName} ... ";

        try {
            // Switch to this database
            $conn->cdb->setDatabase($dbName);

            // Fetch every document (include_docs=true)
            // Sag's getAllDocs() accepts query parameters
            $response = $conn->cdb->getAllDocs([
                'include_docs' => 'true',
                'limit'        => 100 * 1000 * 1000  
            ]);

            $docs = [];
            if (isset($response->body->rows) && is_array($response->body->rows)) {
                foreach ($response->body->rows as $row) {
                    // Skip design documents if you want only data
                    if (isset($row->id) && str_starts_with($row->id, '_design/')) {
                        continue;
                    }
                    if (isset($row->doc)) {
                        $docs[] = $row->doc;
                    }
                }
            }

            $file = rtrim($outDir, '/') . '/' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $dbName) . '.json';
            file_put_contents(
                $file,
                json_encode($docs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );

            echo count($docs) . " documents → {$file}\n";

        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }

    echo "Done.\n";
}

// ---------- CLI entry point ----------
if (php_sapi_name() === 'cli') {
    $opts = getopt('', ['prefix:', 'username::', 'out::']);

    if (empty($opts['prefix'])) {
        fwrite(STDERR, "Usage: php dump_by_prefix.php --prefix=NAME [--username=admin] [--out=./dumps]\n");
        exit(1);
    }

    $prefix   = $opts['prefix'];
    $username = $opts['username'] ?? 'admin';
    $outDir   = $opts['out'] ?? './dumps';

    dumpDatabasesByPrefix($prefix, $username, $outDir);
}
