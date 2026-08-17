<?php
require_once(dirname(__FILE__).'/../../boot.php');
global $naWebOS;
global $naLAN;
global $naIP;
global $naIPforDB;
global $naBrowserInfo;
global $naIsBot;
if ($naLAN) exit ('HTTP ERROR 403 Forbidden - naLAN===true');
if ($naIsBot) exit ('HTTP ERROR 403 Forbidden - naIsBot===true');



if (false) {
    echo '<pre>';
    var_dump ($_SERVER);
    echo PHP_EOL.PHP_EOL;
    var_dump ($_COOKIE);
    echo '</pre>';
    exit();
}

// input sanity checks
$in = $_POST;
if (!is_string($in['msg'])) exit ('HTTPS ERROR 403 Forbidden - msg must be a string.');
//if (strpos($in['msg'],'<')!==false) exit ('HTTPS ERROR 403 Forbidden - msg may not contain \'<\'.'); // expressly allowed these days.
if (!is_string($in['stacktrace'])) exit ('HTTPS ERROR 403 Forbidden - stacktrace must be a string.');
if (strpos($in['stacktrace'],'<')!==false) exit ('HTTPS ERROR 403 Forbidden - stacktrace may not contain \'<\'.');
//if ($naIsBot) exit ('HTTPS ERROR 403 Forbidden - Bots are not allowed to log their presence on this site.');

// find unused _id
$cdbDomain = $naWebOS->domainFolderForDB;
$tokenLength = 2;
$triesPerTokenLength = 10;
$id = randomString ($tokenLength);
$id4db = id4db ($id);

function id4db ($id) {
    global $naIP;
    $filler = ' ';
    $mt = microtime(true);
    $mts = $mt - floor($mt);
    return $naIP.$filler.date('Y-m-d H:i:s').$filler.$mts.$filler.$_POST['dateTZ'];
}

$db = $naWebOS->dbsAdmin->findConnection('couchdb');
$cdb = $db->cdb;
$dataSetName = $cdbDomain.'___analytics';
//echo $dataSetName; exit();
$cdb->setDatabase($dataSetName, true);

$foundGoodID = false;
$triesDone = 0;
while (!$foundGoodID) {
    try {
        $call = $cdb->get ($id4db);
        //echo '<pre>'; var_dump ($call); echo '</pre>';
    } catch (Exception $e) {
        $foundGoodID = true;
    }
    if (!$foundGoodID) {
        $triesDone++;
        if ($triesDone >= $triesPerTokenLength) {
            $tokenLength++;
            $triesDone = 0;
        }
        $id = randomString ($tokenLength);
        $id4db = id4db ($id);
    }
};

// find geoIP data if we got it already
$dataSetName = $cdbDomain.'___ipinfo';
//echo $dataSetName; exit();
$cdb->setDatabase($dataSetName, true);
try {
    $call = $cdb->get($naIPforDB);
    //echo '<pre>'; var_dump ($call); echo '</pre>';
    $ipInfo = json_decode($call->body->ip_info,true);
} catch (Exception $e) {
    // fetch it from a good Web API if we haven't got the geoIP data yet.
    try {
        $key = trim(file_get_contents(dirname(__FILE__).'/ipinfo.io.key.txt'));
        $xec = 'curl -H "Authorization: Bearer '.$key.'" https://ipinfo.io/'.$naIP;
        exec ($xec, $output, $result);
        $ipInfo = json_decode(join('',$output), true);

        // and add this data to our own database
        $rec = [
            '_id' => $naIPforDB,
            'ip' => $naIP,
            'ip_info' => join('',$output)
        ];
        $ipInfo = json_decode(join('',$output),true);
        $cdb->post($rec);
    } catch (Exception $e) {};
}

// add the data
$dataSetName = $cdbDomain.'___analytics';
//echo $dataSetName; exit();
$cdb->setDatabase($dataSetName, true);
$rec = [
    '_id' => $id4db,
    'ip' => $naIP,
    'naStart' => $in['naStart_millisecondsSinceEpoch'],
    'millisecondsSinceEpoch' => $in['millisecondsSinceEpoch'], // milliseconds passed since Epoch (1970s or so) until now.
    'dateTZ' => $in['dateTZ'],
    'msg' => $in['msg'],
    'htmlClasses' => $in['htmlClasses'],
    'referrer' => $in['referrer'],
    'stacktrace' => $in['stacktrace'],
    'ipinfo' => json_encode($ipInfo)
];

try {
    $cdb->post($rec);
} catch (Exception $e) {
    exit ($e->getMessage());
}
exit ('Success.')
?>
