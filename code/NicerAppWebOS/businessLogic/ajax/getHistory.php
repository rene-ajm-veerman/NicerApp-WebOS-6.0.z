<?php
/**
 * Generic History endpoint for uDB2 dataParts
 *
 * Expects POST/GET:
 *   - id            (string)  Document _id
 *   - limit         (int)     Max revisions (default 50)
 *   - history       (string)  Optional explicit history DB suffix
 *                             (e.g. "cms_comments_history")
 *   - database      (string)  Optional live database / table name
 *                             (used when auto-deriving the history DB)
 *
 * Returns JSON:
 *   { "ok": true, "id": "...", "history": [ ... ] }
 *   or { "error": "..." }
 */

require_once(__DIR__ . '/../../boot.php');   // adjust path if needed
global $naWebOS;

header('Content-Type: application/json; charset=utf-8');

// -----------------------------------------------------------------
// Input
// -----------------------------------------------------------------
$documentID = $_POST['id']      ?? $_GET['id']      ?? null;
$limit      = (int)($_POST['limit']   ?? $_GET['limit']   ?? 50);
$history    = $_POST['history'] ?? $_GET['history'] ?? true;   // true = auto-derive
$database   = $_POST['database']?? $_GET['database']?? null;

if (empty($documentID)) {
    echo json_encode(['error' => 'Missing document id']);
    exit;
}

// -----------------------------------------------------------------
// Resolve a uDB2 instance
// -----------------------------------------------------------------
try {
    // Prefer an already-configured connection if available
    $db = $naWebOS->dbs->findConnection('couchdb');

    // Create / obtain a uDB2 instance
    // (adjust this line to however you normally instantiate uDB2 in v6)
    if (method_exists($db, 'getUDB2')) {
        $uDB = $db->getUDB2();
    } else {
        // Fallback – create from the existing connector
        $uDB = uDB2::createFromConfig([
            'driver'   => 'couchdb',
            'database' => $database ?: ($db->dataSetName('cms_comments') ?? 'default')
        ]);
        // Make sure the underlying connector is set
        $uDB->couchConnector = $db;   // or $db->cdb / whatever your structure uses
    }

    if ($database) {
        $uDB->setTable($database);
    }

} catch (Throwable $e) {
    echo json_encode([
        'error' => 'Could not initialize database layer',
        'detail'=> $e->getMessage()
    ]);
    exit;
}

// -----------------------------------------------------------------
// Fetch history
// -----------------------------------------------------------------
try {
    $historyDocs = $uDB->getHistory($documentID, [
        'limit'   => $limit,
        'history' => $history
    ]);

    echo json_encode([
        'ok'      => true,
        'id'      => $documentID,
        'history' => $historyDocs
    ]);

} catch (Throwable $e) {
    echo json_encode([
        'error'  => 'Failed to retrieve history',
        'detail' => $e->getMessage()
    ]);
}
?>
