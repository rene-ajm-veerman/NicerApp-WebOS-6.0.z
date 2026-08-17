<?php
//echo realpath(dirname(__FILE__).'/../../../../../../').'/NicerAppWebOS/boot.php';exit;
require_once (realpath(dirname(__FILE__).'/../../../../../../').'/NicerAppWebOS/boot.php');

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set("display_errors", 0);   // Set to 1 only when debugging

require_once (dirname(__FILE__).'/class.newsApp-3.php');

global $naWebOS;

$debug = false;

// ====================== INPUT HANDLING ======================
$dateBeginStr = urldecode($_REQUEST['dateBegin'] ?? '');
$dateEndStr   = urldecode($_REQUEST['dateEnd']   ?? '');
$section      = $_REQUEST['section'] ?? '';
$loads        = intval($_REQUEST['loads'] ?? 0);

try {
    $dateBegin = new DateTime($dateBeginStr);
    $dateEnd   = new DateTime($dateEndStr);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date format']);
    exit;
}

$dateBegin->setTimezone(new DateTimeZone(date_default_timezone_get()));
$dateEnd->setTimezone(new DateTimeZone(date_default_timezone_get()));

// Security: Limit time window
$dateDiff = $dateEnd->getTimestamp() - $dateBegin->getTimestamp();
if ($dateDiff < 0 || $dateDiff > 3600 * 6) {  // max 6 hours
    $dateEnd = clone $dateBegin;
    $dateEnd->modify('+6 hours');
}

// ====================== COUCHDB QUERY ======================
$newsApp3_factorySettings = json_decode(
    file_get_contents(dirname(__FILE__).'/config.factorySettings.json'),
                                        true
);

$newsApp3 = new newsApp3_class($newsApp3_factorySettings);

$startTs = $dateBegin->format('U');
$endTs   = $dateEnd->format('U');

$searchPubDate = [
    '$gt' => intval($startTs),
    '$lt' => intval($endTs)
];

$bookmark = null;
$done = false;
$arr = [];

$maxDocs = 600;           // safety limit
$totalFetched = 0;

while (!$done && $totalFetched < $maxDocs) {
    $findCommand = [
        'selector' => [
            'pd' => $searchPubDate,
            'p'  => [
                '$regex' => '^/' . str_replace(['__', '_'], ['/', ' '], $section)
            ]
        ],
        'limit'     => 200,
        'use_index' => '_design/f8296ee26307f4441eaf3723ab3c982e996830a1',
        'fields'    => ['_id', '_rev', 't', 'de', 'm', 'am', 'pd', 'pubDate', 'da', 'dd', 'c', 'cc']
    ];
    //echo json_encode ($findCommand,JSON_PRETTY_PRINT);

    if ($bookmark) {
        $findCommand['bookmark'] = $bookmark;
    }

    try {
        $dbName = $naWebOS->dbs->findConnection('couchdb')->dataSetName('app_2D_news__rss_items');
        $naWebOS->dbs->findConnection('couchdb')->cdb->setDatabase($dbName, false);

        $call = $naWebOS->dbs->findConnection('couchdb')->cdb->find($findCommand);

        if (isset($call->body->docs) && is_array($call->body->docs)) {
            foreach ($call->body->docs as $doc) {
                $arr[] = $doc;
                $totalFetched++;
            }

            $bookmark = $call->body->bookmark ?? null;
            if (count($call->body->docs) < 200) {
                $done = true;
            }
        } else {
            $done = true;
        }
    } catch (Exception $e) {
        if ($debug) {
            error_log("CouchDB error: " . $e->getMessage());
        }
        break;
    }
}

// ====================== OUTPUT ======================
header('Content-Type: application/json; charset=utf-8');
echo json_encode($arr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

if ($debug) {
    file_put_contents(
        dirname(__FILE__).'/last_call.log',
                      "Loads: $loads | Section: $section | Items returned: " . count($arr) . "\n"
    );
}
?>
