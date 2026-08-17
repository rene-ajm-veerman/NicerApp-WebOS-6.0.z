<?php
    require_once (realpath(dirname(__FILE__).'/../../../../../../').'/NicerAppWebOS/boot.php');
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once (dirname(__FILE__).'/class.newsApp-3.php');
$debug = false;

global $naWebOS;

    //header ('Content-Type: application/json');
    //ob_start("ob_gzhandler");
    //ini_set ('memory_limit', '1G');
    
    /*
    $_GET['dateBegin'] = 'Tue Mar 20 2018 16:31:58 GMT+0100 (CET)';
    $_GET['dateEnd'] = 'Tue Mar 20 2018 17:31:58 GMT+0100 (CET)';
    echo $_GET['dateBegin'].' to '.$_GET['dateEnd'].'<br/>'.PHP_EOL;
    */
    //echo $_GET['dateBegin'].' to '.$_GET['dateEnd'].'<br/>'.PHP_EOL;exit();
    $dateBeginStr = str_replace('.','0',urldecode($_GET['dateBegin']));
    $dateEndStr = str_replace('.','0',urldecode($_GET['dateEnd']));
    $dateBeginStr = str_replace ('GMT000', 'GMT+0000', $dateBeginStr);
    $dateEndStr = str_replace ('GMT000', 'GMT+0000', $dateEndStr);
    $dateBeginStr = str_replace ('GMT-01000', 'GMT-1000', $dateBeginStr);
    $dateEndStr = str_replace ('GMT-01000', 'GMT-1000', $dateEndStr);
    $dateBegin = new DateTime($dateBeginStr);
    //echo $_GET['dateBegin'].' to '.$_GET['dateEnd'].'<br/>'.PHP_EOL;
    $dateScanning = new DateTime($dateBeginStr);
    $dateEnd = new DateTime($dateEndStr);
    //$dateEnd = $dateEnd->add (new DateInterval('PT2M'));
    
    $dateBegin->setTimeZone(new DateTimeZone(date_default_timezone_get()));
    $dateScanning->setTimeZone(new DateTimeZone(date_default_timezone_get()));
    $dateEnd->setTimeZone(new DateTimeZone(date_default_timezone_get()));
    
    
    //echo '<pre>'; var_dump ($dateBegin); echo '</pre>'.PHP_EOL;   
    //echo '<pre>'; var_dump ($dateEnd); echo '</pre>'.PHP_EOL;
    //exit();
    $dateDiff = $dateEnd->format('U') - $dateBegin->format('U');
    if (
        $dateDiff < 0
        || $dateDiff > 60 * 60 // 1 hour
    ) {
        //exit();
    }
    
    
    $newsApp3_factorySettings_fn = dirname(__FILE__).'/config.factorySettings.json';
    $newsApp3_factorySettings = json_decode(file_get_contents($newsApp3_factorySettings_fn), true);

    $newsApp3 = new newsApp3_class($newsApp3_factorySettings);

    
    $startkey = $dateBegin->format('U');//[ intval(date('Y', $dateBegin)), intval(date('m', $dateBegin)), intval(date('d', $dateBegin)), intval(date('H', $dateBegin)), intval(date('i', $dateBegin)), intval(date('s', $dateBegin)) ];
    $endkey = $dateEnd->format('U');//[ intval(date('Y', $dateEnd)), intval(date('m', $dateEnd)), intval(date('d', $dateEnd)), intval(date('H', $dateEnd)), intval(date('i', $dateEnd)), intval(date('s', $dateEnd)) ];
    
    
    $searchPubDate = [
        '$gt' => intval($startkey),
        '$lt' => intval($endkey)
    ];
    $bookmark = null;
    $done = false;
    
    $arr2 = [];
    while (!$done) {
        $findCommand = array (
            'selector' => array (
                'pd' => $searchPubDate,
                'p' => [
                    '$regex' => '^/'.str_replace('_',' ',str_replace('__','/',$_REQUEST['section'])).'.*'
                ]
            ),
            'use_index' => 'primaryIndex',
            'fields' => array ('_id', '_rev', 't', 'de', 'm', 'am', 'pd', 'pubDate', 'da', 'dd', 'c', 'cc' )
        );
        if (!is_null($bookmark)) $findCommand['bookmark'] = $bookmark;
        $go = true;
        try {
            $dbName = $naWebOS->dbs->findConnection('couchdb')->dataSetName('app_2D_news__rss_items');
            $naWebOS->dbs->findConnection('couchdb')->cdb->setDatabase ($dbName, false);
            $call = $naWebOS->dbs->findConnection('couchdb')->cdb->find ($findCommand);
            //file_put_contents(dirname(__FILE__).'/call.json', $dbName.PHP_EOL.json_encode($findCommand,JSON_PRETTY_PRINT).PHP_EOL.json_encode($call, JSON_PRETTY_PRINT));
            if ($debug && count($call->body->docs)>0) { echo '$findCommand='; var_dump ($findCommand); echo PHP_EOL.'$call='; var_dump ($call); echo PHP_EOL.PHP_EOL; }
        } catch (Exception $e) {
            global $naErr;
            $naErr->addStr('<p>'.$e->getMessage().'</p>'.PHP_EOL, $e->getMessage());
            echo $e->getMessage();
            $go = false;
            $done = true;
        }

        if ($go) {
            if (!is_null($call) && !is_null($call->body) && count($call->body->docs)===0) $done = true;
            if (!$done && !is_null($call) && !is_null($call->body)) $bookmark = $call->body->bookmark; else $bookmark = null;
            if (!$done) foreach ($call->body->docs as $idx => $doc) $arr2[] = $doc;
        }
        
    }
    
    echo json_encode($arr2);
?>
