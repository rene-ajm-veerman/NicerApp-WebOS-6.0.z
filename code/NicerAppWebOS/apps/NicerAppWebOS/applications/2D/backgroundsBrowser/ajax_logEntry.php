<?php
require_once (dirname(__FILE__).'/../../../../../boot.php');
global $naWebOS;
global $naLAN;
if (!$naLAN) exit('403 Forbidden.');

    $db = $naWebOS->dbs->findConnection('couchdb');
    $cdb = $db->cdb;

    $debug = false;
    $dbName = $db->dataSetName('logentries');
    $cdb->setDatabase($dbName);

    // fetch dataRecord
    $findCommand = [
        'selector' => [
            'i' => $_GET['i'],
            's2' => [ '$ne' => null ],
            's3' => [ '$ne' => null ]
        ],
        'fields' => ['_id'],
        'sort' => [
                [ 's2' => 'desc' ],
                [ 's3' => 'desc' ],
                [ 'isIndex' => 'desc' ],
                [ 'isBot' => 'desc' ],
                [ 'isLAN' => 'desc' ]
        ],
        'use_index' => $naWebOS->globals['cdbDesignDocs']['logentries_pageLoad'],
        'limit' => 100
    ];
    //echo '<pre style="padding:8px;border-radius:10px;background:rgba(255,255,255,0.5);color:green;">'; var_dump ($findCommand); echo '</pre>';
    try {
        $call = $cdb->find ($findCommand);
    } catch (Exception $e) {
        $msg = $fncn.' FAILED (ajax_logEntry) while trying to find in \''.$dbName.'\' : '.$e->getMessage();
        //trigger_error ($msg, E_USER_ERROR);
        echo $msg;
        //return false;
        exit();
    }



    //echo '<pre style="color:white;background:rgba(0,50,0,0.5);border-radius:10px;margin:10px;">'; var_dump($call); echo '</pre>'; die();
    foreach ($call->body->docs as $docIdx => $doc) {
        $call2 = $cdb->get($doc->_id);
        //echo $call2->body->entry->request->html;

        //echo '<pre style="color:white;background:rgba(0,50,0,0.5);border-radius:10px;padding:5px;margin:10px;">'; var_dump($call2->body); echo '</pre>';

        $marginLeft = 10;
        if (!$call2->body->isIndex) $marginLeft = 50;
        $docA = json_decode(json_encode($call2->body), true);

        $now = DateTime::createFromFormat('U', $call2->body->s2);
        $now2 = $now->format("Y-m-d H:i:s");

        $url = '';
        $tooltip = '';
        if (array_key_exists('bd',$docA)) $bd = $docA['bd']; else $bd = '';
        if (array_key_exists('ipInfo',$docA)) $ipInfo = $docA['ipInfo']['city'].', '.$docA['ipInfo']['country']; else $ipInfo = '';

        if (array_key_exists('request', $docA)) {
            $url = $docA['request']['$_SERVER']['REQUEST_URI'];
            $tooltip = str_replace('"', "'", str_replace(' ', '&nbsp;', str_replace(PHP_EOL, '<br/>', json_encode($docA['request']['$naWebOS->view'],JSON_PRETTY_PRINT))));
        }
        if (array_key_exists('httpOpts', $docA))
            if (array_key_exists('ALL cURL fields', $docA['httpOpts']))
                $url = $docA['httpOpts']['ALL cURL fields']['CURLOPT_URL'];
            else
                $url = $docA['httpOpts']['CURLOPT_URL'];

        $class = '';
        if (array_key_exists('httpResponse', $docA)) {
            if (array_key_exists('docs', $docA['httpResponse']) && count($docA['httpResponse']['docs']) > 0) $class .= 'withData ';
        }

        if (array_key_exists('classErrorType', $docA)) $class .= $docA['classErrorType'].' ';

        echo '<div style="margin-left:'.$marginLeft.'px">';
        if ($ipInfo!=='') echo '<h2 id="'.$doc->_id.'" class="logEntry '.$class.'" onclick="naLog.onclick_logEntry_details(event);" tooltip="'.$tooltip.'" title="'.$tooltip.'" alt="'.$tooltip.'"><span class="datetimeAccurate">'.$now2.'</span> <span class="ip">'.$call2->body->ip.'</span> <span class="url">'.$url.'</span> <span class="bd">'.$bd.'</span> <span class="ipInfo">'.$ipInfo.'</span></h2>';
        else echo '<h2 id="'.$doc->_id.'" class="logEntry '.$class.'" onclick="naLog.onclick_logEntry_details(event);" tooltip="'.$tooltip.'" title="'.$tooltip.'" alt="'.$tooltip.'"><span class="datetimeAccurate">'.$now2.'</span> <span class="ip">'.$call2->body->ip.'</span> <span class="url">'.$url.'</span> <span class="bd">'.$bd.'</span></h2>';
        if (array_key_exists('classErrorType', $docA)) {
            echo '<h3 style="margin-left:'.$marginLeft.'px" class="logEntry error '.$class.'">';
            echo $docA['errType'].'<br/>';
            echo $docA['errFile'].':'.$docA['errLine'].'<br/>';
            echo $docA['errMsg'];
            echo '</h3>';
        }
        echo '</div>';

        //echo '<pre style="padding:5px;margin:8px;color:white;background:rgba(0,50,0,0.5);">'; var_dump ($doc); echo '</pre>';
/*
        // fetch dataRecord
        $findCommand2 = [
            'selector' => [
                //'type' => 'new request',
                's1' => $doc->s1
            ],
            'fields' => ['_id', 'isIndex', 'ip', 's1', 's2', 'request', 'httpOpts', 'httpResponse'],
            'sort' => [
                [ 's1' => 'asc' ],
                [ 's2' => 'asc' ]
            ],
            'use_index' => '_design/249f3b14593cc6f19467c3697f2398397bd9aab6'
        ];
        //echo '<pre style="padding:8px;border-radius:10px;background:rgba(255,255,255,0.5);color:green;">'; var_dump ($findCommand); echo '</pre>';
        try {
            $call2 = $cdb->find ($findCommand2);
        } catch (Exception $e) {
            $msg = $fncn.' FAILED while trying to find in \''.$dataSetName.'\' : '.$e->getMessage();
            //trigger_error ($msg, E_USER_ERROR);
            echo $msg;
            //return false;
            die();
        }

        foreach ($call2->body->docs as $docID2 => $doc2) {
            $now3 = DateTime::createFromFormat('U.u', $doc2->s2);
            $now4 = $now3->format("Y-m-d H:i:s.u");
            if (!$doc2->isIndex) {
                $marginLeft = 50;
                $docB = json_decode(json_encode($doc2), true);
                echo '<div style="margin-left:'.$marginLeft.'px">';
                echo '<h3><span class="datetimeAccurate">'.$now4.'</span> <span class="ip">'.$doc2->ip.'</span> '.$docB['request']['$_SERVER']['REQUEST_URI'].'</h3>';
                if (array_key_exists('request', $docB)) echo hmJSON ($docB['request'], 'Request response');
                if (array_key_exists('httpOpts', $docB)) echo hmJSON ($docB['httpOpts'], 'HTTP options');
                if (array_key_exists('httpResponse', $docB)) echo hmJSON ($docB['httpResponse'], 'HTTP response');
                //else { echo '<pre>'; var_dump ($docB); echo '</pre>'; };
                echo '</div>';
            }
        }
*/
    }
    $html .= '<script type="text/javascript">clearTimeout (timeout_hms); var timeout_hms = setTimeout (function() {na.site.settings.current.running_loadTheme = false; na.site.settings.current.loadingApps = false; debugger; na.hms.startProcessing()}, 200); na.site.transformLinks()</script>';
    echo $html;
