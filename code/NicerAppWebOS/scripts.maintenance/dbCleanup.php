<?php
/**
 * delete_couchdb_dbs_not_matching_prefixes.php
 *
 * Deletes every CouchDB database that does NOT start with any of the given prefixes.
 * Uses your existing uDB2 / class_NicerAppWebOS_database_API_couchdb_3_2__2_0_0 stack.
 *
 * Usage (CLI):
 *   php delete_couchdb_dbs_not_matching_prefixes.php
 *
 * Or include and call:
 *   deleteCouchDBsNotMatchingPrefixes(['myapp_', 'prod_', 'analytics']);
 */

declare(strict_types=1);

// ---------- CONFIG ----------
$prefixes = [
    // Keep databases that start with any of these:
    'said_by___',
    'zoned_at___',
    'nicer_app___'
];

// System / protected databases that should NEVER be deleted
$protected = [
    '_users',
    '_replicator',
    '_global_changes',
    '_metadata',
    '_nodes',
    '_dbs',
];

// Dry-run? (true = only print what would be deleted)
$dryRun = false;

// ---------- BOOTSTRAP (adjust path if needed) ----------
// Make sure these point to your real NicerApp / uDB files
require_once __DIR__ . '/../boot.php';          // or wherever $naWebOS is created
// require_once .../uDB-2.0.0/plugins/class.couchdb-3.2.2_2.0.0.php;  // if not autoloaded

function deleteCouchDBsNotMatchingPrefixes(array $prefixes, array $protected = [], bool $dryRun = true): void
{
    global $naWebOS;

    if (!isset($naWebOS) || !is_object($naWebOS)) {
        throw new RuntimeException('\$naWebOS is not available. Make sure boot.php (or equivalent) was required.');
    }

    // Get the CouchDB connector the same way the rest of the app does
    $dbApi = $naWebOS->dbs->findConnection('couchdb');
    if (!$dbApi || !isset($dbApi->cdb)) {
        throw new RuntimeException('Could not obtain CouchDB connector (cdb).');
    }

    /** @var Sag $cdb */
    $cdb = $dbApi->cdb;

    echo "Fetching all databases...\n";
    $response = $cdb->getAllDatabases();
    $allDbs   = $response->body ?? [];

    if (!is_array($allDbs)) {
        throw new RuntimeException('getAllDatabases() did not return an array.');
    }

    $toDelete = [];
    $kept     = [];

    foreach ($allDbs as $dbName) {
        $dbName = (string)$dbName;

        // Always protect system DBs
        if (in_array($dbName, $protected, true) || str_starts_with($dbName, '_')) {
            $kept[] = $dbName . ' (protected)';
            continue;
        }

        // Keep if it matches ANY prefix
        $matches = false;
        foreach ($prefixes as $prefix) {
            if ($prefix !== '' && str_starts_with($dbName, $prefix)) {
                $matches = true;
                break;
            }
        }

        if ($matches) {
            $kept[] = $dbName;
        } else {
            $toDelete[] = $dbName;
        }
    }

    // Report
    echo "\n=== Databases that will be KEPT ===\n";
    foreach ($kept as $name) {
        echo "  ✓ $name\n";
    }

    echo "\n=== Databases that will be DELETED ===\n";
    if (empty($toDelete)) {
        echo "  (none)\n";
    } else {
        foreach ($toDelete as $name) {
            echo "  ✗ $name\n";
        }
    }

    if ($dryRun) {
        echo "\n*** DRY-RUN mode – nothing was deleted. Set \$dryRun = false to actually delete. ***\n";
        return;
    }

    // Actual deletion
    echo "\nDeleting...\n";
    $deleted = 0;
    $failed  = 0;

    foreach ($toDelete as $dbName) {
        try {
            $cdb->deleteDatabase($dbName);
            echo "  Deleted: $dbName\n";
            $deleted++;
        } catch (Exception $e) {
            echo "  FAILED:  $dbName → " . $e->getMessage() . "\n";
            $failed++;
        }
    }

    echo "\nDone. Deleted: $deleted, Failed: $failed\n";
}

// ---------- RUN ----------
try {
    deleteCouchDBsNotMatchingPrefixes($prefixes, $protected, $dryRun);
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>
