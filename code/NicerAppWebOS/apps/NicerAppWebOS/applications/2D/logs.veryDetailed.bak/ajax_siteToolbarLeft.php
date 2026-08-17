<div style="display:flex;height:100px;">
<?php
require_once (dirname(__FILE__).'/../../../../../boot.php');
global $naWebOS;
global $naLAN;
$fncn = 'ajax_siteToolbarLeft.php';

if (!$naLAN) die('403 Forbidden.');
//echo '<pre style="color:yellow;background:rgba(0,0,50,0.5);border-radius:10px;margin:10px;">'; var_dump ($naWebOS->view); echo '</pre>';

switch ($_GET['type']) {
    case 'robots' : $btnID = '#btnRobots'; break;
    case 'humans' : $btnID = '#btnHumans'; break;
    case 'LAN' : $btnID = '#btnLAN'; break;
}
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
        $('.vividButton_icon_100x100').each(function(idx,el) {
            na.site.settings.buttons['#'+el.id] = new naVividButton(el);
        });
        na.site.settings.buttons['<?php echo $btnID?>'].select();
    }, 50);
</script>
<?php



//echo '<img src="/siteMedia/btnRobot.png" style="width:100px;height:100px;"/>';
//echo '<img src="/siteMedia/btnHumans.png" style="width:100px;height:100px;"/>';

//if (!is_null($naWebOS->view)) {
    foreach ($naWebOS->view as $appID => $appRec) break;
//if ($appRec['page']=='index') {
    $db = $naWebOS->dbs->findConnection('couchdb');
    $cdb = $db->cdb;

    $debug = false;
    $dbName = $db->dataSetName('logentries');
    $cdb->setDatabase($dbName);

    // fetch dataRecord
    $findCommand = [
        'selector' => [
            's2' => [ '$gt' => 0 ],
            's3' => [ '$exists' => true ],
            'isIndex' => true
        ],
        'fields' => ['_id' ],
        'sort' => [
                [ 's2' => 'desc' ],
                [ 's3' => 'desc' ],
                [ 'isIndex' => 'desc' ],
                [ 'isBot' => 'desc' ],
                [ 'isLAN' => 'desc' ]
        ],
            'use_index' => $naWebOS->globals['cdbDesignDocs']['logentries_frontpage'],
        'limit' => 100
    ];
    if ($_GET['type']=='robots') $findCommand['selector']['isBot'] = true;
    elseif ($_GET['type']=='LAN') $findCommand['selector']['isLAN'] = true;
    else {
        $findCommand['selector']['isBot'] = false;//[ '$or' => [ [ '$eq' => null ], [ '$eq' => false ] ]];
        $findCommand['selector']['isLAN'] = false;//JUST RESULTS IN BOT HITS SHOWING FOR HUMAN VISITORS! [ '$or' => [ [ '$eq' => null ], [ '$eq' => false ] ]];
    }

    //echo '<pre style="padding:8px;border-radius:10px;background:rgba(255,255,255,0.5);color:green;">'; var_dump ($findCommand); echo '</pre>';
    try {
        $call = $cdb->find ($findCommand);
    } catch (Exception $e) {
        $msg = $fncn.' FAILED (ajax_siteToolbarLeft) while trying to find in \''.$dbName.'\' : '.$e->getMessage();
        echo $msg;
        die();
    }

    //if (false)
    foreach ($call->body->docs as $docID => $doc) {
        $call2 = $cdb->get($doc->_id);
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


        echo '<h2 class="logEntry '.$class.' tooltip" i="'.$call2->body->i.'"  onclick="naLog.onclick_logEntry(event);" title="'.$tooltip.'" alt="'.$tooltip.'"><span class="datetimeAccurate">'.$now2.'</span> <span class="ip">'.$call2->body->ip.'</span><br/>'.$url.'</h2>';

    }

    echo PHP_EOL;
    echo '<script type="text/javascript">setTimeout(function() { na.desktop.settings.visibleDivs.push(\'#siteToolbarLeft\');na.desktop.resize();},1000);</script>';

    $html = '';
    $html .= '<script type="text/javascript">setTimeout (function() {na.site.settings.current.running_loadTheme = false; na.site.settings.current.loadingApps = false; na.hms.startProcessing()}, 1500); na.site.transformLinks(); na.site.startTooltips(event,$("#siteToolbarLeft")[0]);</script>';
    echo $html;

//}
?>
<script type="text/javascript" src="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/logs/naLog.source.js"></script>
