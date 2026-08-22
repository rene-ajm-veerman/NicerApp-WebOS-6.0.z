<?php
/**
 * Generic History endpoint
 *
 * Required:
 *   - id            Document _id
 *   - database      Live database / table suffix (e.g. "cms_comments", "cms_pages")
 *
 * Optional:
 *   - limit         Max revisions (default 50)
 *   - history       Explicit history DB suffix (default = database + "_history")
 *   - appID         Used for permission check (default = database)
 */

require_once(__DIR__ . '/../../boot.php');
global $naWebOS, $naUsername;

header('Content-Type: application/json; charset=utf-8');

$documentID = $_POST['id']       ?? $_GET['id']       ?? null;
$database   = $_POST['database'] ?? $_GET['database'] ?? null;
$limit      = (int)($_POST['limit']   ?? $_GET['limit']   ?? 50);
$history    = $_POST['history']  ?? $_GET['history']  ?? null;   // null = auto
$appID      = $_POST['appID']    ?? $_GET['appID']    ?? $database;

if (!$documentID || !$database) {
    echo json_encode(['error' => 'Missing id or database']);
    exit;
}

// Permission check
/*if (!function_exists('naHasPermission') || !naHasPermission($appID, 'viewHistory')) {
    echo json_encode(['error' => 'Permission denied', 'code' => 'viewHistory']);
    exit;
}*/

try {
    $db  = $naWebOS->dbsAdmin->findConnection('couchdb');
    $cdb = $db->cdb;

    // Resolve live + history database names
    $liveDbName = $db->dataSetName($database);
    $histSuffix = $history ?: ($database . '_history');
    $histDbName = $db->dataSetName($histSuffix);

    // Fetch history
    $dbg = 'setDatabase("'.$histDbName.'")';
    $cdb->setDatabase($histDbName);

    // Correct indexes for cms_comments_history

$indexes = [

    // Main index used by getHistory()
    // Supports: selector on documentID + sort by historyDatetime DESC
    [
        'index' => [
            'fields' => [
                ['documentID' => 'desc'],
                ['historyDatetime' => 'desc']
            ]
        ],
        'name' => 'by_documentID_historyDatetime_desc',
        'type' => 'json'
    ],

    // Alternative if you ever want ascending
    [
        'index' => [
            'fields' => [
                ['documentID' => 'asc'],
                ['historyDatetime' => 'asc']
            ]
        ],
        'name' => 'by_documentID_historyDatetime_asc',
        'type' => 'json'
    ],

    // Simple global newest-first index
    [
        'index' => [
            'fields' => [
                ['historyDatetime' => 'desc']
            ]
        ],
        'name' => 'by_historyDatetime_desc',
        'type' => 'json'
    ]
];
    foreach ($indexes as $rec) {
        try {
		$dbg = 'setIndex "'.$rec['name'].'"';
            $cdb->setIndex($rec);
        } catch (Exception $e) {
            // ignore if it already exists
        }
    }

    $mango = [
        'selector' => [
            'documentID' => $documentID
        ],
        'limit' => $limit,
        'sort'  => [['historyDatetime' => 'desc']]
    ];

    $result = $cdb->find($mango);
    $docs   = $result->body->docs ?? [];

    $history = [];
    foreach ($docs as $doc) {
        $history[] = json_decode(json_encode($doc), true);
    }

    echo json_encode([
        'ok'       => true,
        'id'       => $documentID,
        'database' => $database,
        'history'  => $history
    ]);

} catch (Throwable $e) {
    echo json_encode([
        'error'  => 'Failed to retrieve history',
        'detail' => $e->getMessage(),
	'dbg' => $dbg
    ]);
}
?>
