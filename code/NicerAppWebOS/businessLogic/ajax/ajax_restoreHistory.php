<?php
/**
 * Restore a document from a history snapshot
 *
 * POST:
 *   historyId   – _id of the history document
 *   documentId  – live document _id
 *   database    – live database suffix (e.g. cms_comments)
 *   appID       – for permission check
 */

require_once(__DIR__ . '/../../boot.php');
global $naWebOS, $naUsername;

header('Content-Type: application/json; charset=utf-8');

$historyId  = $_POST['historyId']  ?? null;
$documentId = $_POST['documentId'] ?? null;
$database   = $_POST['database']   ?? null;
$appID      = $_POST['appID']      ?? $database;

if (!$historyId || !$documentId || !$database) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

// Permission check
if (!function_exists('naHasPermission') || !naHasPermission($appID, 'restoreHistory')) {
    echo json_encode(['error' => 'Permission denied', 'code' => 'restoreHistory']);
    exit;
}

try {
    $db  = $naWebOS->dbsAdmin->findConnection('couchdb');
    $cdb = $db->cdb;

    $liveDb  = $db->dataSetName($database);
    $histDb  = $db->dataSetName($database . '_history');

    // 1. Load the history snapshot
    $cdb->setDatabase($histDb);
    $histCall = $cdb->get($historyId);
    $histDoc  = $histCall->body;

    if (empty($histDoc->snapshot)) {
        echo json_encode(['error' => 'History entry has no snapshot']);
        exit;
    }

    $snapshot = (array)$histDoc->snapshot;

    // 2. Load current live document (we need its _rev)
    $cdb->setDatabase($liveDb);
    $liveCall = $cdb->get($documentId);
    $liveDoc  = (array)$liveCall->body;

    // 3. Write current live version into history first (so we don’t lose it)
    //    Re-use the same onedit logic you already have, or do it inline:
    $now = time();
    $historyEntry = [
        '_id'               => bin2hex(random_bytes(12)),
        'type'              => 'revision',
        'documentID'        => $documentId,
        'originalRev'       => $liveDoc['_rev'] ?? null,
        'snapshot'          => $liveDoc,
        'historyDatetime'   => $now,
        'historyDatetimeStr'=> function_exists('naDateTimeStr') ? naDateTimeStr($now, 0) : date('c', $now),
        'historyBy'         => $naUsername ?? 'system',
        'historyIP'         => $naIP ?? null,
        'restoredFrom'      => $historyId          // nice to know it was a restore
    ];

    $cdb->setDatabase($histDb);
    $cdb->post($historyEntry);

    // 4. Restore the snapshot onto the live document
    $cdb->setDatabase($liveDb);

    // Keep the current _id and _rev, overwrite the rest
    $restored = $snapshot;
    $restored['_id']  = $liveDoc['_id'];
    $restored['_rev'] = $liveDoc['_rev'];

    // Optional: mark that it was restored
    $restored['restoredAt']     = $now;
    $restored['restoredFrom']   = $historyId;
    $restored['restoredBy']     = $naUsername ?? null;

    $cdb->put($restored['_id'], $restored);

    echo json_encode([
        'ok'         => true,
        'documentId' => $documentId,
        'restoredFrom' => $historyId
    ]);

} catch (Throwable $e) {
    echo json_encode([
        'error'  => 'Restore failed',
        'detail' => $e->getMessage()
    ]);
}
?>
