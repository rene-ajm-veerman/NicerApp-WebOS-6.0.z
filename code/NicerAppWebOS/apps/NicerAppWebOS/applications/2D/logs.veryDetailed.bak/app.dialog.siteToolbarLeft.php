    <div style="display:flex;align-items:center;height:100px;max-height:100px;align-items: center;justify-content: space-between;">
<?php
global $naWebOS;
global $naLAN;
if (!$naLAN) die('403 Forbidden.');
//echo '<pre style="color:yellow;background:rgba(0,0,50,0.5);border-radius:10px;margin:10px;">'; var_dump ($naWebOS->view); echo '</pre>';

echo $naWebOS->html_vividButton (
    1, 'order:1',

    'btnRobots', 'vividButton_icon_100x100 grouped', '_100x100', 'grouped',
    '',
    'if (!$(this).is(\'.disabled\')) { naLog.showEvents(event,\'robots\'); }',
    '',
    '',

    2, 'Show robot visitors',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.green2a.png',
    'btnVisitors_robots.png',

    '',
    '',

    null,
    null,
    null
);
echo $naWebOS->html_vividButton (
    3, 'order:2',

    'btnHumans', 'vividButton_icon_100x100 grouped', '_100x100', 'grouped',
    '',
    'if (!$(this).is(\'.disabled\')) { naLog.showEvents(event,\'humans\'); }',
    '',
    '',

    4, 'Show human visitors',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.green2a.png',
    'btnVisitors_humans.png',

    '',
    '',

    null,
    null,
    null
);
echo $naWebOS->html_vividButton (
    5, 'order:3',

    'btnLAN', 'vividButton_icon_100x100 grouped', '_100x100', 'grouped',
    '',
    'if (!$(this).is(\'.disabled\')) { naLog.showEvents(event,\'LAN\'); }',
    '',
    '',

    6, 'Show human visitors',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.green2a.png',
    'btnVisitors_LAN.png',

    '',
    '',

    null,
    null,
    null
);
?>
</div>
<script type="text/javascript">
    setTimeout(function() {
        na.site.settings.buttons['#btnHumans'].select();
    }, 2000);
</script>
<?php



//echo '<img src="/siteMedia/btnRobot.png" style="width:100px;height:100px;"/>';
//echo '<img src="/siteMedia/btnHumans.png" style="width:100px;height:100px;"/>';


foreach ($naWebOS->view as $appID => $appRec) break;
if ($appRec['page']=='index') {
    $db = $naWebOS->dbs->findConnection('couchdb');
    $cdb = $db->cdb;

    $debug = false;
    $dbName = $db->dataSetName('logentries');
    $cdb->setDatabase($dbName);

    // fetch dataRecord
    $findCommand = [
        'selector' => [
            's2' => [ '$exists' => true ],
            's3' => [ '$exists' => true ],
            'isIndex' => true,
            'isBot' => false,
            'isLAN' => false
        ],
        'fields' => ['_id', 's2', 's3', 'isIndex', 'isBot', 'isLAN' ],
        'sort' => [
            [ 's2' => 'desc' ],
            [ 's3' => 'desc' ],
            [ 'isIndex' => 'desc' ],
            [ 'isBot' => 'desc' ],
            [ 'isLAN' => 'desc' ]
        ],
        'use_index' => $naWebOS->globals['cdbDesignDocs']['logentries_frontpage'],
        'limit' => 10 * 1000
    ];


    //echo '<pre style="padding:8px;border-radius:10px;background:rgba(255,255,255,0.5);color:green;">'; var_dump ($findCommand); echo '</pre>';
    try {
        $call = $cdb->find ($findCommand);
    } catch (Exception $e) {
        //echo '<pre>tt33 ';var_dump ($cdb);echo '</pre>';
        //echo '<pre>'; echo json_encode(debug_backtrace(), JSON_PRETTY_PRINT); echo '</pre>';
        $msg = $fncn.' FAILED (siteToolbarLeft) while trying to find in \''.$dbName.'\' : '.$e->getMessage();
        echo $msg;
        die();
    }

    if (false)
    foreach ($call->body->docs as $docID => $doc) {
        $call2 = $cdb->get($doc->_id);
        //echo '<pre>';var_dump ($call2->body);echo '</pre>';
        $docA = json_decode(json_encode($call2->body), true);

        $now = DateTime::createFromFormat('U', $call2->body->s2);
        $now2 = $now->format("Y-m-d H:i:s");

        $class = '';
        if ($call2->body->isBot) $class.='bot ';

        $url = '';
        $tooltip = '';
        if (array_key_exists('request', $docA)) {
            $url = $docA['request']['$_SERVER']['REQUEST_URI'];
            $tooltip = str_replace('\/','/',str_replace('"', "'", str_replace(' ', '&nbsp;', str_replace(PHP_EOL, '<br/>', json_encode($docA['request']['$naWebOS->view'],JSON_PRETTY_PRINT)))));
        }
        if (array_key_exists('httpOpts', $docA))
            if (array_key_exists('ALL cURL fields', $docA['httpOpts']))
                $url = $docA['httpOpts']['ALL cURL fields']['CURLOPT_URL'];
            else
                $url = $docA['httpOpts']['CURLOPT_URL'];


        echo '<h2 class="logEntry '.$class.' tooltip flex" s1="'.$call2->body->s1.'" i="'.$call2->body->i.'" title="'.$tooltip.'" alt="'.$tooltip.'"  onclick="naLog.onclick_logEntry(event);"><span class="datetimeAccurate">'.$now2.'</span> <span class="ip">'.$call2->body->ip.'</span><br/>'.$url.'</h2>';

    }

    echo PHP_EOL;
    echo '<script type="text/javascript">setTimeout(function() { na.desktop.settings.visibleDivs.push(\'#siteToolbarLeft\');na.site.onresize({});},1000);</script>';
}
?>
<script type="text/javascript" src="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/logs/naLog.source.js"></script>
