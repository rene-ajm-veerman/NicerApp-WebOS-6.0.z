<?php 
$rootPathNA = realpath(dirname(__FILE__).'/../../..').'/NicerAppWebOS';
require_once ($rootPathNA.'/boot.php');

global $naDebugAll;
global $naIP;
$debug = false;
if ($debug) {
    echo '<pre>info : '.__FILE__.' : $debug = true.<br/>'.PHP_EOL;
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);
/*if (
    $ip !== '::1'
    && $ip !== '127.0.0.1'
    && $ip !== '80.101.238.137'
) {
    header('HTTP/1.0 403 Forbidden');
    echo '403 - Access forbidden.';
    exit();
}*/

$toTry = [
    "landscape" => [ "IPUA delete" => false ],
    "portrait" => [ "IPUA delete" => false ]
];


$fnl = dirname(__FILE__).'/log.txt';
if ($debug) $f = fopen($fnl, 'w'); elseif (file_exists($fnl)) unlink ($fnl);


global $naWebOS;
$db = $naWebOS->dbs->findConnection('couchdb');
$cdb = $db->cdb;
$dbName = $db->dataSetName('themes');
try {
    $cdb->setDatabase($dbName, false);
    //$call = $cdb->getAllDocs();
    //var_dump ($call); exit();
    //$callOK = $call->status === '200';
    $callOK = true;
} catch (Exception $e) {
    echo 'info : database does not yet exist ('.$dbName.').<br/>'.PHP_EOL;
    echo '<pre style="color:red">'.PHP_EOL; var_dump ($e); echo PHP_EOL.'</pre>'.PHP_EOL;
    exit();
}

//var_dump ($cdb->getAllDocs());
if ($callOK) {
    $findCommand = array (
        'selector' => array(
            //'theme' => $_POST['theme']
        ),
        'fields' => array( '_id' ),
        'use_index' => 'primaryIndex'
    );
    if (array_key_exists('backgroundSearchKey',$_POST) && !is_null($_POST['backgroundSearchKey'])) $findCommand['selector']['backgroundSearchKey'] = $_POST['backgroundSearchKey'];
    if (array_key_exists('theme',$_POST) && !is_null($_POST['theme'])) $findCommand['selector']['orientation'] = $_POST['orientation'];
    if (array_key_exists('theme',$_POST) && !is_null($_POST['theme'])) $findCommand['selector']['orientation'] = $_POST['orientation'];
    if (array_key_exists('specificityName',$_POST) && !is_null($_POST['specificityName'])) $findCommand['selector']['specificityName'] = $_POST['specificityName'];
    if (array_key_exists('orientation',$_POST) && !is_null($_POST['orientation'])) $findCommand['selector']['orientation'] = $_POST['orientation'];
    if (array_key_exists('view',$_POST) && !is_null($_POST['view'])) $findCommand['selector']['view'] = $_POST['view'];
    if (array_key_exists('url',$_POST) && !is_null($_POST['url'])) $findCommand['selector']['url'] = $_POST['url'];
    if (array_key_exists('role',$_POST) && !is_null($_POST['role'])) $findCommand['selector']['role'] = $_POST['role'];
    if (array_key_exists('user',$_POST) && !is_null($_POST['user'])) $findCommand['selector']['user'] = $_POST['user'];
    if (array_key_exists('app',$_POST) && !is_null($_POST['app'])) $findCommand['selector']['app'] = $_POST['app'];
    if (
        array_key_exists('specificityName', $_POST)
        && preg_match('/client$/', $_POST['specificityName'])
    ) {
        $findCommand['selector']['ip'] = $naIP;
        //$findCommand['selector']['ua'] = $_SERVER['HTTP_USER_AGENT'];
    }
    
    try { 
        $call = $cdb->find ($findCommand);
    } catch (Exception $e) {
        echo 'Error while accessing $dbName='.$dbName.'<br/><pre>'.PHP_EOL;
        echo $e->getMessage().PHP_EOL;
        echo 'status : Failed';
        exit();
    };
    if ($debug) {
        echo 'info : $findCommand='; var_dump ($findCommand); echo '.<br/>'.PHP_EOL;
        echo 'info : $call='; var_dump ($call); echo '.<br/>'.PHP_EOL;
        //exit();
    }
    
    $hasRecord = false;
    $rets = [];
    if ($call->headers->_HTTP->status==='200') {
        foreach ($call->body->docs as $idx => $d) {
            $hasRecord = true;
            $call2 = $cdb->get($d->_id);
            $rets = array_merge ($rets, [
                /*$idx.'__'.*/$call2->body->theme => json_decode(json_encode($call2->body),true)
            ]);

            //echo json_encode($call2->body, JSON_PRETTY_PRINT);//.'<br/>'.PHP_EOL;
            //exit();
        }

        $triedCount = 0;
        $triedAllOrientations = false;
        if (count($rets)>0) {
            echo json_encode($rets);
            exit();
        } /*else while (!$triedAllOrientations) {
            //if ($triedCount==2) die();
            $check = false;
            foreach ($toTry as $or2 => $tried2) {
                if (is_bool($tried2) && $tried2) $check = true;
            };
            $triedAllOrientations = $check;
            foreach ($toTry as $or => $tried) {
                if (is_array($tried)) {
                    $triedAll = false;
                    while (!$triedAll) {
                        //if ($triedCount>20) die();
                        $check = true;
                        $tried2 = $toTry[$or];
                        if (is_array($tried2)) {
                            foreach ($tried2 as $what2 => $didTry2) if (!$didTry2) $check = false; else $check = true;
                        } else $check = $tried2;

                        if ($debug) {
                            $dbg = [
                                'ia' => is_array($tried2),
                                'check' => $check
                            ];
                            fwrite ($f, json_encode($dbg).PHP_EOL);
                            fclose ($f);
                            $f = fopen ($fnl, 'a');
                        }

                        if ($check) $toTry[$or] = true;
                        $triedAll = $check;

                        $check = true;
                        foreach ($toTry as $ori => $whats) if (is_array($whats)) $check = false;
                        $triedAllOrientations = $check;

                        if ($debug) {
                            fwrite ($f, json_encode($toTry).PHP_EOL);
                            $dbg = [
                                'triedAll' => $triedAll,
                                'triedAllOrientations' => $triedAllOrientations
                            ];
                            fwrite ($f, json_encode($dbg).PHP_EOL);
                            fclose ($f);
                            $f = fopen ($fnl, 'a');
                        }

                        //sleep(1);

                        if (
                            array_key_exists('orientation', $_POST)
                            && $_POST['orientation']!==null
                            && $or === $_POST['orientation']
                        ) {
                            $toTry[$or] = true;
                            continue;
                        }
                        /*
                        fwrite ($f, json_encode($toTry).PHP_EOL);
                        fclose ($f);
                        $f = fopen ($fnl, 'a');* /

                        $findCommand2 = $findCommand;
                        $findCommand2['selector']['orientation'] = $or;

                        if (is_array($tried))
                        foreach ($tried as $what => $didTry) {
                            $triedCount++;
                            switch ($what) {
                                case 'IPUA delete':
                                    if (!$didTry) {
                                        unset ($findCommand2['selector']['ip']);
                                        unset ($findCommand2['selector']['ua']);
                                        if ($debug) {
                                            echo 'info : $findCommand2='; var_dump ($findCommand2); echo '.<br/>'.PHP_EOL;
                                        }
                                        $hasDocument = true;
                                        try {
                                            $call = $cdb->find ($findCommand2);
                                        } catch (Exception $e) {
                                            echo 'Error while accessing $dbName='.$dbName.'<br/><pre>'.PHP_EOL;
                                            echo $e->getMessage().PHP_EOL;
                                            echo 'status : Failed';
                                            $hasDocument = false;
                                        };
                                        if ($debug) {
                                            echo 'info : $call='; var_dump ($call); echo '.<br/>'.PHP_EOL;
                                            //exit();
                                        }
                                        //die();

                                        if ($hasDocument) {
                                            $hasRecord = false;
                                            $rets = [];
                                            if ($call->headers->_HTTP->status==='200') {
                                                foreach ($call->body->docs as $idx => $d) {
                                                    $hasRecord = true;
                                                    $call2 = $cdb->get($d->_id);
                                                    $rets = array_merge ($rets, [
                                                        $call2->body->theme => json_decode(json_encode($call2->body),true)
                                                    ]);

                                                    //echo json_encode($call2->body, JSON_PRETTY_PRINT);//.'<br/>'.PHP_EOL;
                                                    //exit();
                                                }
                                                if (count($rets)>0) {
                                                    echo json_encode($rets);
                                                    exit();
                                                } else {
                                                    if (is_array($toTry[$or])) $toTry[$or][$what] = true;
                                                }
                                            }
                                        } elseif (is_array($toTry[$or])) $toTry[$or][$what] = true;
                                        /*
                        fwrite ($f, json_encode($toTry).PHP_EOL);
                        fclose ($f);
                        $f = fopen ($fnl, 'a');* /
        //die();
                                    } else $toTry[$or][$what] = true;/*else {
                                        if ($debug) {
                                            echo 'info : $findCommand2='; var_dump ($findCommand2); echo '.<br/>'.PHP_EOL;
                                        }
                                        try {
                                            $call = $cdb->find ($findCommand2);
                                        } catch (Exception $e) {
                                            echo 'Error while accessing $dbName='.$dbName.'<br/><pre>'.PHP_EOL;
                                            echo $e->getMessage().PHP_EOL;
                                            echo 'status : Failed';
                                            exit();
                                        };
                                        if ($debug) {
                                            echo 'info : $call='; var_dump ($call); echo '.<br/>'.PHP_EOL;
                                            //exit();
                                        }

                                        $hasRecord = false;
                                        $rets = [];
                                        if ($call->headers->_HTTP->status==='200') {
                                            foreach ($call->body->docs as $idx => $d) {
                                                $hasRecord = true;
                                                $call2 = $cdb->get($d->_id);
                                                $rets = array_merge ($rets, [
                                                    $call2->body->theme => json_decode(json_encode($call2->body),true)
                                                ]);

                                                //echo json_encode($call2->body, JSON_PRETTY_PRINT);//.'<br/>'.PHP_EOL;
                                                //exit();
                                            }
                                            if (count($rets)>0) {
                                                echo json_encode($rets);
                                                exit();
                                            } else {
                                                $toTry[$or] = true;
                                            }
                                        }

                                        fwrite ($f, json_encode($toTry).PHP_EOL);
                                        fclose ($f);
                                        $f = fopen ($fnl, 'a');

                                    }* /
                                    break;

                            }

                        }
                        //if ($triedCount==1) die();
                        $triedCount++;
                    }
                }
            }
        }*/
    }

    /*
    if (!$hasRecord) {
        $rec = array (
            '_id' => cdb_randomString(20),
            'theme' => $_POST['theme'],
            'ip' => $naIP,
            'ua' => $_SERVER['HTTP_USER_AGENT'],
            'url' => $_POST['url'],
            'dialogs' => json_decode($_POST['dialogs'], true)
        );
        if (array_key_exists('specificityName',$_POST) && !is_null($_POST['specificityName'])) $rec['specificityName'] = $_POST['specificityName'];
        if (array_key_exists('view',$_POST) && !is_null($_POST['view'])) $rec['view'] = $_POST['view'];
        if (array_key_exists('url',$_POST) && !is_null($_POST['url'])) $rec['url'] = $_POST['url'];
        if (array_key_exists('role',$_POST) && !is_null($_POST['role'])) $rec['role'] = $_POST['role'];
        if (array_key_exists('user',$_POST) && !is_null($_POST['user'])) $rec['user'] = $_POST['user'];
        $fail = false;
        try {
            $call2 = $cdb->post($rec);
        } catch (Exception $e) {
            if ($debug) {
                echo 'status : Failed : could not add record to database ('.$dbName.').<br/>'.PHP_EOL;
                echo '$rec = <pre style="color:blue">'.PHP_EOL; var_dump ($rec); echo PHP_EOL.'</pre>'.PHP_EOL;
                echo '$call2 = <pre style="color:red">'.PHP_EOL; var_dump ($call2); echo PHP_EOL.'</pre>'.PHP_EOL;
                echo '$e = <pre style="color:red">'.PHP_EOL; var_dump ($e); echo PHP_EOL.'</pre>'.PHP_EOL; 
                exit();
            }
            $fail = true;
            echo 'status : Failed';
        }
        if (!$fail) echo json_encode($rec, JSON_PRETTY_PRINT);
    }
    */

    if ($debug) {
        echo 'ERROR : Failed to get database access.<br/>'.PHP_EOL;
        echo 'info : $call='; var_dump ($call); echo '.<br/>'.PHP_EOL;
    } 
    //echo 'status : Failed.';
}

function triedOrientation ($toTry, $orientation) {

}
?>
