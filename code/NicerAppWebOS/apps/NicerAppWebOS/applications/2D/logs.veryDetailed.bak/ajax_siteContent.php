<?php
require_once (dirname(__FILE__).'/../../../../../boot.php');
global $naWebOS;
global $naLAN;
if (!$naLAN) die('403 Forbidden.');
//echo '<pre style="color:yellow;background:rgba(0,0,50,0.5);border-radius:10px;margin:10px;">'; var_dump ($naWebOS->view); echo '</pre>';
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
        'fields' => ['_id'],
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
        $msg = $fncn.' FAILED (ajax_siteContent) while trying to find in \''.$dbName.'\' : '.$e->getMessage();
        //trigger_error ($msg, E_USER_ERROR);
        echo $msg;
        //return false;
        die();
    }



    //echo '<pre style="color:white;background:rgba(0,50,0,0.5);border-radius:10px;margin:10px;">'; var_dump($call); echo '</pre>'; //exit();
    //if (false)
    foreach ($call->body->docs as $docID => $doc) {
        //echo '<pre style="padding:5px;margin:8px;color:white;background:rgba(0,50,0,0.5);">'; var_dump ($doc); echo '</pre>';
        $call2 = $cdb->get($doc->_id);
        //echo $call2->body->entry->request->html;

        //echo '<pre style="color:white;background:rgba(0,50,0,0.5);border-radius:10px;padding:5px;margin:10px;">'; var_dump($doc); echo '</pre>';


        $marginLeft = 10;
        if (!$call2->body->isIndex) $marginLeft = 50;
        $docA = json_decode(json_encode($call2->body), true);

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

        if ($docA['isIndex']) {
            $now = DateTime::createFromFormat('U', $call2->body->s2);
            $now2 = $now->format("Y-m-d H:i:s");

            echo '<div id="'.$doc->_id.'" i="'.$call2->body->i.'" class="tooltip" style="margin:10px;margin-left:'.$marginLeft.'px" onclick="naLog.onclick_logEntry(event);" title="'.$tooltip.'" alt="'.$tooltip.'">';
            echo '<h2><span class="datetimeAccurate">'.$now2.'</span> <span class="ip">'.$call2->body->ip.'</span> <span class="url">'.$url.'</span></h2>';
            //echo hmJSON ($docA['request'], 'Request response');
            echo '</div>';
        }

        /*
        // fetch dataRecord
        $findCommand2 = [
            'selector' => [
                'type' => 'new request',
                'isIndex' => true,
                'isBot' => false,
                'isLAN' => false,
                's1' => (is_null($doc->s1)?1:$doc->s1),
                's2' => [ '$gt' => 0 ]
            ],
            'fields' => ['_id', 'isIndex', 'isBot', 'isLAN', 'type', 'ip', 's1', 's2', 'request', 'httpOpts', 'httpResponse'],
            'sort' => [
                [ 's1' => 'desc' ],
                [ 's2' => 'desc' ],
                [ 'type' => 'desc' ],
                [ 'isIndex' => 'desc' ],
                [ 'isBot' => 'desc' ],
                [ 'isLAN' => 'desc' ]
            ],
            'use_index' => '_design/249f3b14593cc6f19467c3697f2398397bd9aab6'
        ];
        echo '<pre style="padding:8px;border-radius:10px;background:rgba(255,255,255,0.5);color:green;">'; var_dump ($findCommand); echo '</pre>';
        try {
            $call2 = $cdb->find ($findCommand2);
        } catch (Exception $e) {
            $msg = $fncn.' FAILED while trying to find in \''.$dbName.'\' : '.$e->getMessage();
            //trigger_error ($msg, E_USER_ERROR);
            echo $msg;
            //return false;
            die();
        }

        foreach ($call2->body->docs as $docID2 => $doc2) {
            $now3 = DateTime::createFromFormat('U', $doc2->s2);
            $now4 = $now3->format("Y-m-d H:i:s");
            if (!$doc2->isIndex) {
                $marginLeft = 50;
                $docB = json_decode(json_encode($doc2), true);
                $url = '';
                if (array_key_exists('request', $docA))
                    $url = $docA['request']['$_SERVER']['REQUEST_URI'];
                if (array_key_exists('httpOpts', $docA))
                    $url = $docA['httpOpts']['ALL cURL fields']['CURLOPT_URL'];
                echo '<div style="margin-left:'.$marginLeft.'px">';
                echo '<h3><span class="datetimeAccurate">'.$now4.'</span> <span class="ip">'.$doc2->ip.'</span> '.$url.'</h3>';
                //if (array_key_exists('request', $docB)) echo hmJSON ($docB['request'], 'Request response');
                //if (array_key_exists('httpOpts', $docB)) echo hmJSON ($docB['httpOpts'], 'HTTP options');
                ///if (array_key_exists('httpResponse', $docB)) echo hmJSON ($docB['httpResponse'], 'HTTP response');
                //else { echo '<pre>'; var_dump ($docB); echo '</pre>'; };
                echo '</div>';
            }
        }
        */
    }

    $html = '';
    $html .= '<script type="text/javascript">/*na.m.clearAllConditions(); na.hms.quitAllProcessing();*/ na.m.waitForCondition("scope1", function() { return document.getElementById("scope1_data_0") }, function() {na.site.settings.current.running_loadTheme = false; na.site.settings.current.loadingApps = false; na.hms.startProcessing(); na.site.transformLinks(); na.site.startTooltips(event,$("#siteContent")[0]);}, 150); </script>';
    echo $html;



//}
//require_once(dirname(__FILE__).'/../../../../../logs.php');
?>
