<?php
require_once(realpath(dirname(__FILE__).'/..').'/boot.php');

class class_NicerAppWebOS_log {
    public $cn = 'class_NicerAppWebOS_log';

    public $entries = [];

    public function __construct() {
    }

    public function reset() {
        $this->entries = [];
    }



    public function fetch ($timeBegin, $timeEnd) {
        $debug = false;
        global $naWebOS;
        $db = $naWebOS->dbs->findConnection('couchdb');
        $cdb = $db->cdb;

        $dtBegin = new DateTime($timeBegin);
        $dtEnd = new DateTime($timeEnd);

        $startkey = $dtBegin->format('U');
        $endkey = $dtEnd->format('U');

        $searchPubDate = [
            '$gt' => intval($startkey),
            '$lt' => intval($endkey)
        ];

        // search the specified date-time range ($startkey to $endkey) for all possible combinations of the specified $searchKeys.
        $findCommand = array (
            'selector' => array (
                't' => $searchPubDate,
                'sid' => session_id()
            ),
            'use_index' => 'primaryIndex',
            'fields' => array ('_id', 't', 'to', 'entries' )
        );

        global $naWebOS;
        $arr2 = [];
        $done = false;
        while (!$done) {
            $go = true;
            try {
                $dbName = $db->dataSetName('logentries');
                $cdb->setDatabase ($dbName, 'true');
                $call = $cdb->find ($findCommand);
                if ($debug) { echo '$dbName='.$dbName.PHP_EOL.'$findCommand='.json_encode($findCommand, JSON_PRETTY_PRINT).PHP_EOL.'$call='.json_encode($call,JSON_PRETTY_PRINT).PHP_EOL.PHP_EOL; }
            } catch (Exception $e) {
                echo '$findCommand : Exception->getMessage()='.$e->getMessage().PHP_EOL;
                $go = false;
                $done = true;
            }
            if ($go) {
                foreach ($call->body->docs as $idx => $doc) {
                    $arr2[] = $doc;
                }
            }

            if (!is_null($call) && !is_null($call->body) && count($call->body->docs)===0) $done = true;
            if (!is_null($call) && !is_null($call->body)) $bookmark = $call->body->bookmark; else $bookmark = null;
        }
        echo json_encode ($arr2);
    }




    public function fetchIntoPast ($hours) {
        global $naWebOS;
        $db = $naWebOS->dbs->findConnection('couchdb');
        $cdb = $db->cdb;

        $debug = false;
        $h = (date('h')-$hours);
        $timeBegin = date('Y-m-d ').$h.':'.date('i').':'.date('sa');
        $timeEnd = date('Y-m-d h:i:sa');
        $dbName = $db->dataSetName('logentries');

        /*
        $startkey = strtotime('-1 hour');
        $endkey = strtotime('now');
        $searchPubDate = [
            '$gt' => intval($startkey),
            '$lt' => intval($endkey)
        ];*/

        // search the specified date-time range ($startkey to $endkey) for all possible combinations of the specified $searchKeys.
        $findCommand = array (
            'selector' => array (
                //'t' => $searchPubDate,
                'sid' => session_id()
            ),
            'use_index' => 'primaryIndex',
            'fields' => array ('_id', 't', 'to', 'entries' )
        );

        global $naWebOS;
        $arr2 = [];
        $done = false;
        $bookmark = null;
        while (!$done) {
            $findCommand = array (
                'selector' => array (
                    //'t' => $searchPubDate
                    'sid' => session_id()
                ),
                'use_index' => 'primaryIndex',
                'fields' => array ('_id', 't', 'to', 'entries' )
            );
            if ($debug) { echo '$dbName='.$dbName.PHP_EOL.'$findCommand='.json_encode($findCommand, JSON_PRETTY_PRINT).PHP_EOL; };

            if (!is_null($bookmark)) $findCommand['bookmark'] = $bookmark;

            $go = true;
            try {
                $cdb->setDatabase ($dbName, 'true');
                $call = $cdb->find ($findCommand);
                if ($debug) { echo '$call='.json_encode($call,JSON_PRETTY_PRINT).PHP_EOL.PHP_EOL; exit(); }
            } catch (Exception $e) {
                echo '$findCommand : Exception->getMessage()='.$e->getMessage().PHP_EOL;
                $go = false;
                $done = true;
            }
            if ($go) {
                foreach ($call->body->docs as $idx => $doc) {
                    $arr2[] = $doc;
                }
                if (!is_null($call) && !is_null($call->body) && count($call->body->docs)===0) $done = true;
                if (!is_null($call) && !is_null($call->body)) $bookmark = $call->body->bookmark; else $bookmark = null;

            }

        }
        return $arr2;
    }









    public function add ($entries, $sessionKeyName='naErrors') {
        global $naWebOS;
        //echo '<h1>class.NicerAppWebOS.log.php</h1><pre>';var_dump ($naWebOS->dbs);echo '</pre>';

        $e = new Exception();

        $r = [];
        if (is_object($naWebOS)) {
            try {
                if ( $naWebOS->dbs instanceof class_NicerAppWebOS_database_API ) {
                    $dbsPostedInto = $naWebOS->dbs->addLogEntries ($entries);
                    $r[] = ['databases' => $dbsPostedInto];
                }
            } catch (Throwable $e) {
                echo $this->cn.'->add($entries) : $e->getMessage()='.$e->getMessage();
            } catch (Exception $e) {
                echo $this->cn.'->add($entries) : $e->getMessage()='.$e->getMessage();
            }

            /*
            try {
                if ($this->addTo_phpOutput('NicerApp_WebOS_errors_startup', $entries))
                    $r[] = ['$_SESSION::NicerApp_WebOS_errors_startup' => true];
            } catch (Throwable $e) {
                echo $this->cn.'->add($entries) : $e->getMessage()='.$e->getMessage();
            } catch (Exception $e) {
                echo $this->cn.'->add($entries) : $e->getMessage()='.$e->getMessage();
            }
            */
        }

        global $naLAN;
        /*
        $fncn = '.../NicerAppWebOS/logic.business-5.8.z/class.NicerAppWebOS.log.php::class_NicerAppWebOS_log->add()';
        $html = '<h2>'.$fncn.'</h2>'.PHP_EOL;
        $html .=
            '<pre class="naWebOS_phpTrace">'
            .str_replace("\n",'<br/>'.PHP_EOL, $e->getTraceAsString())
            .'</pre>'.PHP_EOL;

        if (
            session_status() === PHP_SESSION_ACTIVE
            && isset($_SESSION['na_error_log_filepath_html'])
            && !is_null($_SESSION['na_error_log_filepath_html'])
        ) {
            foreach ($entries as $entryIdx => $entry) {
                $it = $entry['ENTRY:add_PHPerror'];
                $html .= '<div class="addLogEntries" style="margin-left:20px;opacity:0.75;"><span class="addLogEntry_idx">'.$entryIdx.'</span><div class="addLogEntry_data">'.$it['html'].'</div></div>'.PHP_EOL;
            }
            file_put_contents ($_SESSION['na_error_log_filepath_html'], $html, FILE_APPEND);

        }         */
/*elseif ($naLAN || (isset($naWebOS) && $naWebOS->showAllErrors)) {

            foreach ($entries as $entryIdx => $entry) {
                $it = $entry['ENTRY:add_PHPerror'];
                $html .= '<div class="addLogEntries" style="margin-left:20px;opacity:0.75;"><span class="addLogEntry_idx">'.$entryIdx.'</span><div class="addLogEntry_data">'.$it['html'].'</div></div>'.PHP_EOL;
                error_log ($it['txt']);
            }

            if (!array_key_exists($sessionKeyName,$_SESSION)) $_SESSION[$sessionKeyName] = [];
            array_push ($_SESSION[$sessionKeyName], $html);
            //exit();
        } else {
            foreach ($entries as $entryIdx => $entry) {
                $it = $entry['ENTRY:add_PHPerror'];
                error_log ($it['txt']);
            }
            if (!array_key_exists($sessionKeyName,$_SESSION)) $_SESSION[$sessionKeyName] = [];
            array_push ($_SESSION[$sessionKeyName], $it['html']);
        }*/

        return $r;
    }

    public function addTo_phpOutput ($sk, $val) {
        $key = date(DATE_ATOM);
        if (php_sapi_name()!=='cli') {
            if (!array_key_exists($sk, $_SESSION)) $_SESSION[$sk] = [];
            if (!is_array($_SESSION[$sk])) $_SESSION[$sk] = [];
            $_SESSION[$sk] = array_merge_recursive ($_SESSION[$sk], [ $key => $val ]);
        } else {
            //
            if (is_array($val))
            foreach ($val as $idx => $rec) {
                foreach ($rec as $key2 => $rec2) {
                    echo $rec2['txt'];
                }
            }

            $debugMe = false;
            if ($debugMe && !is_array($val)) {
                $msg =
                    'class.NicerAppWebOS.log.php::addTo_phpOutput() :'.PHP_EOL
                    .'key='.$key.PHP_EOL
                    .'php_sapi_name()='.php_sapi_name().PHP_EOL
                    .'sk='.$sk.PHP_EOL
                    .'val='.json_encode($val,JSON_PRETTY_PRINT);
                echo $msg;
                error_log ($msg);
            }
        }
    }

    public function add_HTTPcall ($url, $debugInfo, $output) {
        $dtz = new DateTime('now');//new DateTimeZone(date_default_timezone_get());
        $dtz_offset = $dtz->getOffset();
        $unixTimeStamp = time();//date(DATE_ATOM);//date(DATE_RFC2822);//date('Y-m-d H:i:sa');

        return [
            'ENTRY:add_HTTPcall' => [
                't' => $unixTimeStamp,
                'to' => $dtz_offset,
                'html' =>
                    '<span class="naLogURL">'.$url.'</span>'
                    .'<div class="naLogDebugInfo">'
                        .$this->displayHTTPcall_debugInfo('html', $debugInfo)
                    .'</div>'
                    .'<div class="naLogHTTPcallOutput">'
                        .$this->displayHTTPcall_output('html', $output)
                    .'</div>',
                'txt' =>
                    'CALL to '.$url.PHP_EOL
                    .$this->displayHTTPcall_debugInfo('txt', $debugInfo).PHP_EOL
                    .$this->displayHTTPcall_output('txt', $debugInfo).PHP_EOL
                    .PHP_EOL
            ]
        ];


    }

    public function add_PHPcall ($fncn, $params, $output) {
        $dtz = new DateTime('now');//new DateTimeZone(date_default_timezone_get());
        $dtz_offset = $dtz->getOffset();
        $unixTimeStamp = time();//date(DATE_ATOM);//date(DATE_RFC2822);//date('Y-m-d H:i:sa');

        return [
            'ENTRY:add_PHPcall' => [
                't' => $unixTimeStamp,
                'to' => $dtz_offset,
                'html' =>
                    '<span class="naLogFNCN">'
                        .$this->displayFNCN($fncn)
                    .'</span>'
                    .'<span class="naLogClassNameToFunctionName">-&gt;</span>'
                    .'<span class="naLogFunctionName">'.$functionName.'</span>'
                    .'<span class="naLogSpacer"> : </span><br/>'
                    .'<pre class="naLogFunctionParams">'
                        .json_encode($params, JSON_PRETTY_PRINT)
                    .'</pre>'
                    .'<pre class="naLogFunctionOutput">'
                        .json_encode($output, JSON_PRETTY_PRINT)
                    .'</pre>',
                'txt' =>
                    $fncn.' : '.PHP_EOL
                    .'PARAMS = '.json_encode($params, JSON_PRETTY_PRINT).PHP_EOL
                    .'OUTPUT = '.json_encode($output, JSON_PRETTY_PRINT).PHP_EOL
            ]
        ];
    }

    public function add_var ($fncn, $varName, $val) {
        $dtz = new DateTime('now');//new DateTimeZone(date_default_timezone_get());
        $dtz_offset = $dtz->getOffset();
        $unixTimeStamp = time();//date(DATE_ATOM);//date(DATE_RFC2822);//date('Y-m-d H:i:sa');

        return [
            'ENTRY:add_var' => [
                't' => $unixTimeStamp,
                'to' => $dtz_offset,
                'html' =>
                    '<span class="naLogFNCN">'
                        .$this->displayFNCN($fncn)
                    .'</span>'
                    .'<span class="naLogVarName">'.$varName.'</span>'
                    .'<span class="naLogSpacer"> : </span><br/>'
                    .'<pre class="naLogVarVal">'.json_encode($val, JSON_PRETTY_PRINT).'</pre>',
                'txt' => $varName.' : '.json_encode($val, JSON_PRETTY_PRINT)
            ]
        ];
    }
}

$naLog = new class_NicerAppWebOS_log();
global $naLog;

?>
