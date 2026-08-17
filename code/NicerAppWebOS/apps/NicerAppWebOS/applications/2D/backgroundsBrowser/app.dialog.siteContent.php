<?php
global $naWebOS;
global $naLAN;
if (!$naLAN) die('403 Forbidden.');
$debugMe = false;

global $db;
global $cdb;
$cdbDomain = $naWebOS->domainFolderForDB;//str_replace('.','_',$naWebOS->domainFolder);
$db = $naWebOS->dbsAdmin->findConnection('couchdb');
$cdb = $db->cdb;
global $dataSetName;
$dataSetName = $cdbDomain.'___analytics';
//echo $dataSetName; exit();
$cdb->setDatabase($dataSetName, true);

    function transformResults_findCommand ($call) {
        global $naWebOS;
        global $cdb;
        global $dataSetName;
        $debugMe = false;
        $cdbDomain = $naWebOS->domainFolderForDB;//str_replace('.','_',$naWebOS->domainFolder);
        $dsn = $cdbDomain.'___ipinfo';
        $cdb->setDatabase($dsn);
        $r = [];
        //echo '<pre>'; var_dump($call); echo '</pre>';
        //$b = json_decode(json_encode($call),true);
        foreach ($call->body->docs as $idx => $rec) {
            if ($debugMe) { echo '<pre style="background:rgba(255,0,0,0.555);color:yellow;border-radius:10px;margin:10px;padding:10px;">'; var_dump($rec->_id); echo '</pre>'; };

            //$dat = $cdb->get(urlencode($rec-><_id));
            //echo '<pre styel="background:rgba(0,50,0,0.555);color:white;border-radius:10px;margin:10px;padding:10px;">'; var_dump($dat); echo '</pre>';

            $findCommand = [
                'selector' => [ 'ip' => $rec->ip ],
                'fields' => [ 'ip', 'ip_info' ]
            ];
            $call2 = $cdb->find($findCommand);

            $rec->ipinfo = $call2->body->docs;
            $r[] = $rec;
            if ($debugMe) { echo '<pre style="background:rgba(100,0,0,0.555);color:white;border-radius:10px;margin:10px;padding:10px;">'; var_dump($rec); echo '</pre>'; };
        }

        $cdb->setDatabase($dataSetName);
        return $r;
    }
    function transformResults_getAllDocs ($call) {
        global $naWebOS;
        global $cdb;
        global $dataSetName;
        $debugMe = false;
        $cdbDomain = $naWebOS->domainFolderForDB;//str_replace('.','_',$naWebOS->domainFolder);
        $dsn = $cdbDomain.'___ipinfo';
        $r = [];
        //echo '<pre>'; var_dump($call); echo '</pre>'; return;
        //$b = json_decode(json_encode($call),true);
        foreach ($call->body->rows as $idx => $rec) {
            if ($debugMe) { echo '<pre style="background:rgba(255,0,0,0.555);color:yellow;border-radius:10px;margin:10px;padding:10px;">'; var_dump($rec->id); echo '</pre>'; };
            if (!property_exists($rec, 'id')) continue;
            if (strpos($rec->id,'design/')!==false) continue;

            $cdb->setDatabase($dataSetName);
            $dat = $cdb->get(urlencode($rec->id));
            //echo '<pre style="background:rgba(100,0,0,0.555);color:white;border-radius:10px;margin:10px;padding:10px;">'; var_dump($dat); echo '</pre>';

            $findCommand = [
                'selector' => [ 'ip' => $dat->body->ip ],
                'fields' => [ 'ip', 'ip_info' ]
            ];
            $cdb->setDatabase($dsn);
            $call2 = $cdb->find($findCommand);

            $dat->body->ipinfo = $call2->body->docs;
            $r1 = json_decode(json_encode($dat->body));
            $r[] = $r1;
            if ($debugMe) { echo '<pre style="background:rgba(100,0,0,0.555);color:white;border-radius:10px;margin:10px;padding:10px;">'; var_dump($r1); echo '</pre>'; };
        }

        $cdb->setDatabase($dataSetName);
        return $r;
    }

/*
$in = &$_GET;
$fields = [ '_id', 'ip', 'secondsSinceEpoch', 'msg', 'referrer', 'stacktrace', 'info' ];

if (
    array_key_exists('begin', $in)
    && array_key_exists('end', $in)
) {
    $findCommand = [
        'selector' => [ 'secondsSinceEpoch' => [['$gt']=>$in['begin']-1, ['$lt']=>$in['end']+1]  ],
        'fields' => &$fields
    ];
    $call = $cdb->find($findCommand);
    $results = transformResults_findCommand ($call);
} else if (array_key_exists('begin', $in)) {
    $findCommand = [
        'selector' => [ 'secondsSinceEpoch' => ['$gt' => $in['begin'] - 1] ],
        'fields' => &$fields
    ];
    $call = $cdb->find($findCommand);
    $results = transformResults_findCommand ($call);
} else if (array_key_exists('end', $in)) {
    $findCommand = [
        'selector' => [ 'secondsSinceEpoch' => ['$lt' => $in['end'] + 1] ],
        'fields' => &$fields
    ];
    $call = $cdb->find($findCommand);
    $results = transformResults_findCommand ($call);
} else {
    $findCommand = [];
    $call = $cdb->getAllDocs();
    $results = transformResults_getAllDocs ($call);
}

if ($debugMe) {
    echo '<pre style="background:rgba(0,0,50,0.555);color:lime;border-radius:10px;margin:10px;padding:10px;">';
    var_dump ($results);
    echo '</pre>';
    echo '<pre style="background:rgba(0,255,0,0.555);color:yellow;border-radius:10px;margin:10px;padding:10px;">';
    var_dump ($findCommand);
    echo '</pre>';
    echo '<pre style="background:rgba(255,255,255,0.555);color:navy;border-radius:10px;margin:10px;padding:10px;">';
    var_dump ($call);
    echo '</pre>';
}
*/
?>
<link rel="StyleSheet" href="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/backgroundsBrowser/naBackgroundsBrowser.css?m=<?=filemtime(dirname(__FILE__).'/naBackgroundsBrowser.css')?>"/>
<script type="text/javascript" src="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/backgroundsBrowser/naBackgroundsBrowser.source.js?m=<?=filemtime(dirname(__FILE__).'/naBackgroundsBrowser.source.js')?>'"></script>
<script type="text/javascript">
    naBackgroundsBrowser.view(na.backgrounds.data);
</script>
<div class="saitContent"></div>
