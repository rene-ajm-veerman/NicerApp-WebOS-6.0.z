<?php
require_once(dirname(__DIR__, 6).'/NicerAppWebOS/boot.php');
global $naWebOS;
global $naLAN;
$debugMe = false;
$openToPublic = true;

if (!$openToPublic && !$naLAN) {
    echo '<h1>NicerApp WebOS logs</h1>';
    echo 'This data is unavailable outside nicer.app\'s LAN, sorry.';
    exit();
}

global $db;
global $cdb;
$cdbDomain = $naWebOS->domainFolderForDB;//str_replace('.','_',$naWebOS->domainFolder);
$db = $naWebOS->dbsAdmin->findConnection('couchdb');
//echo '<pre>t233:'; var_dump($db->roles); echo '</pre>'; //exit();
$cdb = $db->cdb;
global $dataSetName;
$dataSetName = $cdbDomain.'___analytics';
//echo $dataSetName; exit();
$cdb->setDatabase($dataSetName, false);


foreach ($naWebOS->view as $afn => $as) break;
$naWebOS->view[$afn]['beginDateTime'] = safeHTTPinput ('beginDateTime', (time() * 1000) - (7 * 24 * 3600 * 1000));
$naWebOS->view[$afn]['endDateTime'] = safeHTTPinput ('endDateTime');
//echo '<pre>'; var_dump($naWebOS->view); die();

    function transformResults_findCommand ($call) {
        global $naWebOS;
        global $cdb;
        global $dataSetName;
        $debugMe = false;
        $cdbDomain = $naWebOS->domainFolderForDB;//str_replace('.','_',$naWebOS->domainFolder);
        $dsn = $cdbDomain.'___ipinfo';
        //var_dump($dsn);

        $cdb->setDatabase($dsn);
        $r = [];
        //echo '<pre>'; var_dump($call); echo '</pre>';
        //$b = json_decode(json_encode($call),true);
        foreach ($call->body->docs as $idx => $rec) {
            if ($debugMe) { echo '<pre style="background:rgba(255,0,0,0.555);color:yellow;border-radius:10px;margin:10px;padding:10px;">'; var_dump($rec->_id); echo '</pre>'; };

            try {
                $dat = $cdb->get(urlencode($rec->_id));
                //echo '<pre style="background:rgba(0,50,0,0.555);color:white;border-radius:10px;margin:10px;padding:10px;">'; var_dump($dat); echo '</pre>';

                $findCommand = [
                    'selector' => [ 'ip' => $rec->ip ],
                    'fields' => [ 'ip', 'ip_info' ]
                ];
                $cdb->setDatabase($dsn);
                $call2 = $cdb->find($findCommand);
                //echo '<pre>'; var_dump ($call2); echo '</pre>';



                if (
                    isset($call2)
                    && property_exists($call2,'body')
                    && property_exists($call2->body, 'docs')
                ) $rec->ipinfo = $call2->body->docs;
            } catch (Exception $e) {

            }

            $r[] = $rec;
            //if ($debugMe) { echo '<pre style="background:rgba(100,0,0,0.555);color:white;border-radius:10px;margin:10px;padding:10px;">'; var_dump($rec); echo '</pre>'; };
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
            //if ($debugMe) { echo '<pre style="background:rgba(255,0,0,0.555);color:yellow;border-radius:10px;margin:10px;padding:10px;">'; var_dump($rec->id); echo '</pre>'; };
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

             if (
                 isset($call2)
                 && property_exists($call2,'body')
                 && property_exists($call2->body, 'docs')
             ) $dat->body->ipinfo = $call2->body->docs;
             $r1 = json_decode(json_encode($dat->body));
             $r[] = $r1;
            //if ($debugMe) { echo '<pre style="background:rgba(100,0,0,0.555);color:white;border-radius:10px;margin:10px;padding:10px;">'; var_dump($r1); echo '</pre>'; };
        }

        $cdb->setDatabase($dataSetName);
        return $r;
    }

$in = &$_GET;
$fields = [ '_id', 'ip', 'millisecondsSinceEpoch', 'msg', 'referrer', 'stacktrace', 'ipinfo', 'htmlClasses', 'dateTZ' ];

if (
    $naWebOS->view[$afn]['beginDateTime']
    && $naWebOS->view[$afn]['endDateTime']
) {
    $findCommand = [
        'selector' => [ 'millisecondsSinceEpoch' => [['$gt'=>$naWebOS->view[$afn]['beginDateTime']-1], ['$lt'=>$naWebOS->view[$afn]['endDateTime']+1]]  ],
        'fields' => &$fields,
        'sort' => [['millisecondsSinceEpoch'=>'desc']],
        'limit' => 200, // hardcoded (in couchdb!) max value
        'use_index' => '_design/dc5ff73364e89735492d44ab68012948531197ad'
    ];
    //echo '<pre>'; var_dump($findCommand); die();
    $bm = 'abc';
    $oldBM = 'def';
    $results = [];
    while ($bm!==$oldBM) {
        if ($bm!=='abc') $findCommand['bookmark'] = $bm;
        $oldBM = $bm;

        try {
            $call = $cdb->find($findCommand);

            if (
                isset($call)
                && property_exists($call,'body')
                && property_exists($call->body, 'bookmark')
                && is_string($call->body->bookmark)
                && $call->body->bookmark !== ''
                && $call->body->bookmark !== 'nil'
            ) {
                $bm = $call->body->bookmark;
            } else {
                $bm = 'abc';
            };

            $results = array_merge_recursive($results, transformResults_findCommand ($call));
        } catch (Exception $e) {}
    }
} else if ($naWebOS->view[$afn]['beginDateTime']) {
    $findCommand = [
        'selector' => [ 'millisecondsSinceEpoch' => ['$gt' => $naWebOS->view[$afn]['beginDateTime'] - 1] ],
        'fields' => [ '_id', 'millisecondsSinceEpoch' ]
    ];
    //echo '<pre>'.json_encode($findCommand,JSON_PRETTY_PRINT); die();
    $bm = 'abc';
    $oldBM = 'def';
    $results = [];
    $i = 0;
    while ($bm!==$oldBM) {
        if ($bm!=='abc') $findCommand['bookmark'] = $bm;
        $oldBM = $bm;
        try {
            //echo '<pre>'.json_encode($findCommand,JSON_PRETTY_PRINT).'</pre>'.PHP_EOL;
            $call = $cdb->find($findCommand);
            if (
                isset($call)
                && property_exists($call,'body')
                && property_exists($call->body, 'bookmark')
                && is_string($call->body->bookmark)
                && $call->body->bookmark !== ''
                && $call->body->bookmark !== 'nil'
            ) {
                $bm = $call->body->bookmark;
                //echo $bm.'<br/>'.PHP_EOL;
            } else {
                $bm = 'abc';
            };

            global $naWebOS;
            global $cdb;
            global $dataSetName;
            $debugMe = false;
            $cdbDomain = $naWebOS->domainFolderForDB;//str_replace('.','_',$naWebOS->domainFolder);
            $dsn = $cdbDomain.'___ipinfo';
            //echo $dsn; exit;
            //var_dump ($call->body->docs); exit;

            try {
                foreach ($call->body->docs as $idx2=>$rec2) {
                    //echo '<pre>t34:'; var_dump ($rec2->_id); echo '</pre>';
                    $call2 = $cdb->get (urlencode($rec2->_id));
                    //echo '<pre>t45:'; var_dump ($call2); echo '</pre>';
                    $add = [ ];
                    foreach ($fields as $idx3=>$field) {
                        if (property_exists($call2->body, $field)) $add[$field] = $call2->body->$field;
                    };
                    //var_dump ($add);

                    try {
                        $cdb->setDatabase($dsn);
                        $dat = $cdb->get(urlencode($call2->body->_id));
                        //echo '<pre style="background:rgba(0,50,0,0.555);color:white;border-radius:10px;margin:10px;padding:10px;">'; var_dump($dat); echo '</pre>';

                        $findCommand = [
                            'selector' => [ 'ip' => $call2->body->ip ],
                            'fields' => [ 'ip', 'ip_info' ]
                        ];
                        $call2 = $cdb->find($findCommand);
                        //echo '<pre>'; var_dump ($call2); echo '</pre>';



                        if (
                            isset($call3)
                            && property_exists($call3,'body')
                            && property_exists($call3->body, 'docs')
                        ) $add['ipinfo'] = json_decode(json_encode($call3->body->docs),true);

                    } catch (Exception $e) {
                        $msg = $e->getMessage();
                        $cdb->setDatabase($dataSetName);
                        //echo PHP_EOL.$msg.'<br/>'.PHP_EOL;

                    }

                    $results = array_merge_recursive($results, [$add]);
                    //if ($debugMe) { echo '<pre style="background:rgba(100,0,0,0.555);color:white;border-radius:10px;margin:10px;padding:10px;">'; var_dump($rec); echo '</pre>'; };

                    $cdb->setDatabase($dataSetName);

                }
            } catch (Exception $e) {
                $msg = $e->getMessage();
                $cdb->setDatabase($dataSetName);
                echo PHP_EOL.$msg.'<br/>'.PHP_EOL;
            }

            /*
             *        var_dump ($oldBM===$bm);
             *        var_dump ($oldBM);
             *        var_dump ($bm); echo '<br/>';PHP_EOL;
             *        $i++;
             *        if ($i>4) die();
             */


        } catch (Exception $e) {
            $msg = $e->getMessage();
            echo $msg.PHP_EOL;
        }


    }
} else if (array_key_exists('end', $in)) {
    $findCommand = [
        'selector' => [ 'millisecondsSinceEpoch' => ['$lt' => $naWebOS->view[$afn]['endDateTime'] + 1] ],
        'fields' => &$fields,
        'sort' => [['millisecondsSinceEpoch'=>'desc']],
        'limit' => 200, // hardcoded (in couchdb!) max value
        'use_index' => '_design/dc5ff73364e89735492d44ab68012948531197ad'
    ];
    $bm = 'abc';
    $oldBM = 'def';
    $results = [];
    while ($bm!==$oldBM) {
        if ($bm!=='abc') $findCommand['bookmark'] = $bm;
        $call = $cdb->find($findCommand);

        $oldBM = $bm;
        if (
            isset($call)
            && property_exists($call,'body')
            && property_exists($call->body, 'bookmark')
            && is_string($call->body->bookmark)
            && $call->body->bookmark !== ''
            && $call->body->bookmark !== 'nil'
        ) {
            $bm = $call->body->bookmark;
        } else {
            $bm = 'abc';
        };

        $results = array_merge_recursive($results, transformResults_findCommand ($call));
    }
} else {
    $findCommand = [];
    $bm = 'abc';
    $oldBM = 'def';
    $results = [];
    $i = 0;
    while ($bm!==$oldBM) {
        if ($bm!=='abc') $findCommand['bookmark'] = $bm;
        $call = $cdb->getAllDocs();

        $oldBM = $bm;
        if (
            isset($call)
            && property_exists($call,'body')
            && property_exists($call->body, 'bookmark')
            && is_string($call->body->bookmark)
            && $call->body->bookmark !== ''
            && $call->body->bookmark !== 'nil'
        ) {
            $bm = $call->body->bookmark;
        } else {
            $bm = 'abc';
        };

        $results = array_merge_recursive($results, transformResults_getAllDocs ($call));
    }
}

if ($debugMe) {
    echo '<pre style="background:rgba(0,255,0,0.555);color:yellow;border-radius:10px;margin:10px;padding:10px;">';
    var_dump ($findCommand);
    echo '</pre>';
    echo '<pre style="background:rgba(255,255,255,0.555);color:navy;border-radius:10px;margin:10px;padding:10px;">';
    //var_dump ($call);
    echo '</pre>';
    echo '<pre style="background:rgba(0,0,50,0.555);color:lime;border-radius:10px;margin:10px;padding:10px;">';
    var_dump ($results);
    echo '</pre>';
    exit;
}
if (
    $naWebOS->view[$afn]['beginDateTime']
    && $naWebOS->view[$afn]['endDateTime']
) {
    $msg = 'From '.date('Y-m-d H:i:s', $naWebOS->view[$afn]['beginDateTime'] / 1000).' to '
        .date('Y-m-d H:i:s', $naWebOS->view[$afn]['endDateTime']);
} else if ($naWebOS->view[$afn]['beginDateTime']) {
    $msg = 'Since '.date('Y-m-d H:i:s', $naWebOS->view[$afn]['beginDateTime'] / 1000);
} else if (array_key_exists('end', $in)) {
    $msg = 'Up to '.date('Y-m-d H:i:s', $naWebOS->view[$afn]['endDateTime'] / 1000);
}
echo json_encode($results);
?>
