<?php 
class class_NicerApp_WCS__errorHandler {
    
    public function fetch ($timeBegin, $timeEnd) {
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
                'pd' => $searchPubDate
            ),
            'fields' => array ('_id', 'date', 'errHTML', 'errTXT' )
        );
            
        global $naWebOS;
        $arr2 = [];
        $done = false;
        while (!$done) {
            $go = true;
            try {
                $dbName = $db->dataSetName('errorHandling');
                $cdb->setDatabase ($dbName, 'true');
                $call = $cdb->find ($findCommand);
                if ($debug) { echo '$findCommand='.json_encode($findCommand, JSON_PRETTY_PRINT).PHP_EOL.'$call='.json_encode($call,JSON_PRETTY_PRINT).PHP_EOL.PHP_EOL; }
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
        $startkey = strtotime('-1 hour');
        $endkey = strtotime('now');


        $searchPubDate = [
            '$gt' => intval($startkey),
            '$lt' => intval($endkey)
        ];
        
        // search the specified date-time range ($startkey to $endkey) for all possible combinations of the specified $searchKeys.
        $findCommand = array (
            'selector' => array (
                'pd' => $searchPubDate
            ),
            'fields' => array ('_id', 'date', 'errHTML', 'errTXT' )
        );

        global $naWebOS;
        $arr2 = [];
        $done = false;
        while (!$done) {
            $go = true;
            try {
                $dbName = $db->dataSetName('errorHandling');
                $cdb->setDatabase ($dbName, 'true');
                $call = $cdb->find ($findCommand);
                if ($debug) { echo '$findCommand='.json_encode($findCommand, JSON_PRETTY_PRINT).PHP_EOL.'$call='.json_encode($call,JSON_PRETTY_PRINT).PHP_EOL.PHP_EOL; }
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
    
    
    
    public function add ($errno, $errstr, $errfile, $errline) {
        /*if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting, so let it fall
            // through to the standard PHP error handler
            echo 'die2()'; exit();
            return false;
        }*/
        
        
        switch ($errno) {
            case E_ERROR : $errtype = 'E_ERROR'; break;
            case E_WARNING : $errtype = 'E_WARNING'; break;
            case E_PARSE : $errtype = 'E_PARSE'; break;
            case E_NOTICE : $errtype = 'E_NOTICE'; break;
            case E_CORE_ERROR : $errtype = 'E_CORE_ERROR'; break;
            case E_CORE_WARNING : $errtype = 'E_CORE_WARNING'; break;
            case E_COMPILE_ERROR : $errtype = 'E_COMPILE_ERROR'; break;
            case E_COMPILE_WARNING : $errtype = 'E_COMPILE_WARNING'; break;
            case E_USER_ERROR : $errtype = 'E_USER_ERROR'; break;
            case E_USER_WARNING : $errtype = 'E_USER_WARNING'; break;
            case E_USER_NOTICE : $errtype = 'E_USER_NOTICE'; break;
            case E_STRICT : $errtype = 'E_STRICT'; break;
            case E_RECOVERABLE_ERROR : $errtype = 'E_RECOVERABLE_ERROR'; break;
            case E_DEPRECATED : $errtype = 'E_DEPRECATED'; break;
            case E_USER_DEPRECATED : $errtype = 'E_USER_DEPRECATED'; break;
            default : $errtype = 'E-UNKNOWN-ERRORTYPE'; break;
        }        
        
        global $phpScript_startupTime;
        $time = microtime(true) - $phpScript_startupTime;
        //var_dump (dirname(__FILE__).'/errors.css');
        $errhtml = 
            PHP_EOL.
            '<link type="text/css" rel="StyleSheet" href="/NicerAppWebOS/errors.css?c='.date('Ymd_His', filemtime(dirname(__FILE__).'/errors.css')).'">'.PHP_EOL
            .'<div class="phpError">'.PHP_EOL
                ."\t".'<div class="phpErrorLabel">PHP Error</div>'.PHP_EOL
                ."\t".'<div class="phpErrorDetails">'.PHP_EOL
                    ."\t\t".'<span class="phpErrorTimeLabel">Time since start : </span>'.PHP_EOL
                    ."\t\t".'<span class="phpErrorTimeSinceScriptStart">'.$time.'</span><br/>'.PHP_EOL
                    ."\t\t".'<span class="phpErrorTypeLabel">Type : </span>'.PHP_EOL
                    ."\t\t".'<span class="phpErrorType">'.$errtype.'</span><br/>'.PHP_EOL
                    ."\t\t".'<span class="phpErrorMsgLabel">Message : </span>'.PHP_EOL
                    ."\t\t".'<span class="phpErrorMsg">'.$errstr.'</span>'.PHP_EOL
                ."\t".'</div>'.PHP_EOL
                ."\t".'<div class="phpErrorLocation"><span class="phpErrorLocationLabel">Location : </span>'
                .'<span class="phpError_inFile">'.$errfile.'</span>:<span class="phpError_line">'.$errline.'</span></div>'.PHP_EOL;
            $errhtml .=
                "\t".'<div class="phpDebugBacktrace"><span class="phpDebugBacktraceLabel">Backtrace : </span>'.PHP_EOL."\t\t".$this->backtrace().PHP_EOL."\t".'</div>'.PHP_EOL.PHP_EOL;
            //echo '<pre style="color:darkgreen;font-size:small;">'; var_dump ($errcontext); echo '</pre>'; // info overload, usually        
        $errtxt = 'PHP '.$errtype.' error in "'.$errfile.'":'.$errline.' : '.$errstr.', backtrace = '.PHP_EOL.json_encode($this->backtrace(), JSON_PRETTY_PRINT).PHP_EOL.PHP_EOL;
            
        $errhtml .= '</div>'.PHP_EOL.PHP_EOL;
        
        global $naWebOS;
        $db = $naWebOS->dbs->findConnection('couchdb');
        $cdb = $db->cdb;

        $d = new DateTime();           
        $rec = [
            'date' => $d->format('U'),
            'errHTML' => $errhtml,
            'errTXT' => $errtxt
        ];
        try { 
            $dbName = $db->dataSetName('errorHandling');
            $cdb->setDatabase($dbName, false);
            $c = $cdb->post($rec);
        } catch (Exception $e) { 
            echo $e->getMessage(); 
            echo '<pre>'.json_encode($rec,JSON_PRETTY_PRINT).'</pre>'; 
            echo '<br/>'; 
            exit();
        };
    
        if (
            $errno===E_ERROR
            || $errno===E_CORE_ERROR
            || $errno===E_COMPILE_ERROR
            || $errno===E_USER_ERROR
        ) exit();
    }
    
    public function addStr ($html, $txt) {
        global $naWebOS;
        $db = $naWebOS->dbs->findConnection('couchdb');
        $cdb = $db->cdb;

        $d = new DateTime();           
        if (!is_string($txt)) $txt = $html;
        $rec = [
            'date' => $d->format('U'),
            'errHTML' => $html,
            'errTXT' => $txt
        ];
        try { 
            $dbName = $db->dataSetName('errorHandling');
            $cdb->setDatabase($dbName, false);
            $c = $cdb->post($rec);
        } catch (Exception $e) { 
            echo $e->getMessage(); 
            echo '<pre>'.json_encode($rec,JSON_PRETTY_PRINT).'</pre>'; 
            echo '<br/>'; 
            exit();
        };
    }

    public function backtrace() {
        $data = debug_backtrace();
        //$data = array_splice ($data, 1, 2);
        //return '<pre>'.json_encode($data, JSON_PRETTY_PRINT).'</pre>';
        $html = '';
        while (count($data) > 0) {
            
            $it = array_shift($data);
            
            if (
                array_key_exists('function', $it) 
                && (
                    // ignore these, as they are part of the custom error handling system itself :
                    $it['function'] == 'backtrace'
                    || $it['function'] == 'mainErrorHandler'
                    || $it['function'] == 'trigger_error'
                )
            ) continue;
            
            $html .= '\t\t\t<div class="backtraceItem">'.PHP_EOL;
            if (array_key_exists('class', $it) && $it['class']!=='') {
                $html .=
                    "\t\t\t\t".'<span class="backtraceCodeLocation">'.PHP_EOL
                    .'<span class="backtraceClassLabel">Class </span>'.PHP_EOL
                    .'<span class="backtraceClass">'.$it['class'].'</span>'.PHP_EOL
                    .'<span class="backtraceType">'.$it['type'].'</span>'.PHP_EOL
                    .'<span class="backtraceFunction">'.$it['function'].'</span>'.PHP_EOL
                    .'</span><br/>'.PHP_EOL.PHP_EOL;
            } else if (array_key_exists('function', $it) && $it['function']!=='') {
                $html .=
                    "\t\t\t\t".'<span class="backtraceCodeLocation">'
                    .'<span class="backtraceFunctionLabel">Function </span>'.PHP_EOL
                    .'<span class="backtraceFunction">'.$it['function'].'</span>'.PHP_EOL
                    .'</span><br/>'.PHP_EOL.PHP_EOL;
            }
            if (array_key_exists('file', $it) && $it['file']!=='') {
                $html .= 
                    "\t\t\t\t".'<span class="backtraceFileLabel">Location : </span>'.PHP_EOL
                    .'<span class="backtraceFile">'.$it['file'].'</span>:'
                    .'<span class="backtraceLineNum">'.$it['line'].'</span>';
            }
            if (array_key_exists('args',$it) && is_array($it['args']) && count($it['args']) > 0) {
                $html .= "\t\t\t\t".'<div class="backtraceFunctionArgs"><span class="backtraceFunctionArgsLabel">Function arguments : </span><br/>';
                foreach ($it['args'] as $idx => $arg) {
                    if (is_array($arg)) {
                        $html .= "\t".'<span class="backtraceFunctionArg"><pre>'.json_encode($arg,JSON_PRETTY_PRINT).'</pre></span>,<br/>'.PHP_EOL;
                    } else {
                        $html .= "\t".'<span class="backtraceFunctionArg"><pre>'.json_encode($arg,JSON_PRETTY_PRINT).'</pre></span><span class="backtraceFunctionArgSeperator">,</span><br/>'.PHP_EOL;
                    }
                }
                $html .= '</div>'.PHP_EOL;
            }
            $html .= '</div><br/>'.PHP_EOL.PHP_EOL.PHP_EOL;    
        }
        return $html;
    }
}
?>
