<?php 
class class_NicerAppWebOS__errorHandler {
    // ANSI color generator provided the color coding for the text-only output of this component.
    // thanks go to https://ansi.gabebanks.net/
    
    
    public function add ($errno, $errstr, $errfile, $errline) {
        //var_dump (error_reporting());
        //var_dump ($errno);
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
            //case E_STRICT : $errtype = 'E_STRICT'; break; // DEPRECATED
            case E_RECOVERABLE_ERROR : $errtype = 'E_RECOVERABLE_ERROR'; break;
            case E_DEPRECATED : $errtype = 'E_DEPRECATED'; break;
            case E_USER_DEPRECATED : $errtype = 'E_USER_DEPRECATED'; break;
            default : $errtype = 'E-UNKNOWN-ERRORTYPE'; break;
        }

        global $phpScript_startupTime;
        global $naIP;
        global $naVersionNumber;
        $time = microtime(true) - $phpScript_startupTime;
        //var_dump (dirname(__FILE__).'/errors.css');
        //date_default_timezone_set('UTC');
        $dtz = new DateTime('now');//new DateTimeZone(date_default_timezone_get());
        $dtz_offset = $dtz->getOffset();
        $unixTimeStamp = time();//date(DATE_ATOM);//date(DATE_RFC2822);//date('Y-m-d H:i:sa');
        $timestamp = date(DATE_RFC2822);
        $target = $_SERVER['SCRIPT_NAME']=='/index.php' ? 'naErrors_startup' : 'naErrors';

        $classErrorType = 'phpErrorType_'.$errtype;
        $errHTML =
            PHP_EOL.
            '<div class="phpError '.$classErrorType.'">'.PHP_EOL
                ."\t".'<div class="phpErrorLabel">PHP Error</div>'.PHP_EOL
                ."\t".'<div class="phpErrorDetails">'.PHP_EOL
                    ."\t\t".'<span class="phpErrorDateTimeLabel">Date & time : </span>'.PHP_EOL
                    ."\t\t".'<span class="phpErrorDateTime">'.$timestamp.'</span><br/>'.PHP_EOL

                    ."\t\t".'<span class="phpErrorTimeLabel">Time since start : </span>'.PHP_EOL
                    ."\t\t".'<span class="phpErrorTimeSinceScriptStart">'.$time.'</span><br/>'.PHP_EOL

                    ."\t\t".'<span class="phpErrorTypeLabel">Type : </span>'.PHP_EOL
                    ."\t\t".'<span class="phpErrorType">'.$errtype.'</span><br/>'.PHP_EOL

                    ."\t\t".'<span class="phpErrorMsgLabel">Message : </span>'.PHP_EOL
                    ."\t\t".'<span class="phpErrorMsg">'.$errstr.'</span>'.PHP_EOL
                ."\t".'</div>'.PHP_EOL
                ."\t".'<div class="phpErrorLocation"><span class="phpErrorLocationLabel">Location : </span>'
                .'<span class="phpError_inFile">'.$errfile.'</span>:<span class="phpError_line">'.$errline.'</span></div>'.PHP_EOL;
            $errHTML .=
                "\t".'<div class="phpDebugBacktrace"><span class="phpDebugBacktraceLabel">Backtrace : </span>'.PHP_EOL."\t\t".$this->backtrace('html').PHP_EOL."\t".'</div>'.PHP_EOL.PHP_EOL;
            //echo '<pre style="color:darkgreen;font-size:small;">'; var_dump ($errcontext); echo '</pre>'; // info overload, usually
        $errHTML .= '</div>'.PHP_EOL.PHP_EOL;

        global $naIP;
        global $naIsBot; global $naIsDesktop; global $naIsMobile; global $naBrowserMarketSharePercentage;
        global $naLAN;
        $date = date('Y-m-d H:i:s');

        $errANSI = PHP_EOL."\033[37;49;1m[ ".$naIP." : ".$date."]".PHP_EOL."\033[31;47;3;1mPHP ".$errtype.' error in "'.$errfile.'":'.$errline." : \033[35;47;3;1m".$errstr.",\033[31;47;3;1m backtrace = \033[37;49m".PHP_EOL.$this->backtrace('ansi')."\033[37;49m".PHP_EOL;

        $errTXT = "[".$naIP." : ".$date."] PHP ".$errtype.' error in "'.$errfile.'":'.$errline." : ".$errstr.", backtrace = ".PHP_EOL.$this->backtrace('txt');

        $headers_list = [];
        if (php_sapi_name() !== 'cli')
        foreach (getallheaders() as $name => $value) {
            array_push($headers_list, array("name" => $name, "value" => $value));
        }

        return [
            'type' => 'PHP error',
            's1' => (
                session_status() === PHP_SESSION_NONE
                ? time()//microtime(true)
                : (
                    array_key_exists('started',$_SESSION)
                    ? $_SESSION['started']
                    : time()//microtime(true)
                )
            ),
            's2' => time(),//microtime(true),
            's2to' => $dtz_offset,
            'year' => date('Y'),
            'month' => date('m'),
            'day' => date('d'),
            'isIndex' => $_SERVER['SCRIPT_NAME']==='/NicerAppWebOS/index.php',
            'ip' => $naIP,
            'sid' => session_id(),
            'nav' => $naVersionNumber,
            'isBot' => $naIsBot,
            'isLAN' => $naLAN,
            'isDesktop' => $naIsDesktop,
            'isMobile' => $naIsMobile,
            'headers' => $headers_list,
            'browserMarketSharePercentage' => $naBrowserMarketSharePercentage,
            'i' => (
                session_status() === PHP_SESSION_NONE
                ? false
                : (
                    array_key_exists('startedID',$_SESSION)
                    ? $_SESSION['startedID']
                    : false
                )
            ),
            'ts' => $timestamp,
            'html' => $errHTML,
            'ansi' => $errANSI,
            'txt' => $errTXT,
            'classErrorType' => $classErrorType,
            'errType' => $errtype,
            'errMsg' => $errstr,
            'errFile' => $errfile,
            'errLine' => $errline
        ];
     }
    
    public function addStr ($html, $txt='') {
        global $naWebOS;
        $db = $naWebOS->dbs->findConnection('couchdb');
        $cdb = $db->cdb;

        $dtz = new DateTime('now');//new DateTimeZone(date_default_timezone_get());
        $dtz_offset = $dtz->getOffset();
        $unixTimeStamp = time();//date(DATE_ATOM);//date(DATE_RFC2822);//date('Y-m-d H:i:sa');

        $rec = [
            't' => $unixTimeStamp,
            'to' => $dtz_offset,
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

    function addStandardResults ($res) {
        //echo '<pre>'; var_dump ($res); exit();

        global $phpScript_startupTime;
        global $naIP;
        global $naVersionNumber;
        $time = microtime(true) - $phpScript_startupTime;
        //var_dump (dirname(__FILE__).'/errors.css');
        //date_default_timezone_set('UTC');
        $dtz = new DateTime('now');//new DateTimeZone(date_default_timezone_get());
        $dtz_offset = $dtz->getOffset();
        $unixTimeStamp = time();//date(DATE_ATOM);//date(DATE_RFC2822);//date('Y-m-d H:i:sa');
        $timestamp = date(DATE_RFC2822);
        $target = $_SERVER['SCRIPT_NAME']=='/index.php' ? 'naErrors_startup' : 'naErrors';

        $classErrorType = 'phpErrorType_E_WARNING';

        $errstr = '';
        //echo '<pre>'; var_dump ($res);
        foreach ($res as $rIdx => $rec) {
            if (is_string($rIdx)) continue;
            $className = array_key_exists('useSSL', $rec['c']['cRec']) && $rec['c']['cRec']['useSSL'] ? 'naUsesSSL' : '';
            $host = '<span class="'.$className.'">'.$rec['c']['ct'].' : '.$rec['c']['cRec']['username'].':*****@'.$rec['c']['cRec']['host'].':'.$rec['c']['cRec']['port'].'</span> : <span class="naResultValue">'.json_encode($rec['resultValue'], JSON_PRETTY_PRINT).(is_array($res) && array_key_exists('origin',$res)?' ('.$res['origin'].')':'').'</span><br/>'.PHP_EOL;
            $errstr .= $host;
        };
        $errHTML =
            PHP_EOL.
            '<div class="phpError '.$classErrorType.'">'.PHP_EOL
                ."\t".'<div class="phpErrorLabel">PHP Error</div>'.PHP_EOL
                ."\t".'<div class="phpErrorDetails">'.PHP_EOL
                    ."\t\t".'<span class="phpErrorDateTimeLabel">Date & time : </span>'.PHP_EOL
                    ."\t\t".'<span class="phpErrorDateTime">'.$timestamp.'</span><br/>'.PHP_EOL

                    ."\t\t".'<span class="phpErrorTimeLabel">Time since start : </span>'.PHP_EOL
                    ."\t\t".'<span class="phpErrorTimeSinceScriptStart">'.$time.'</span><br/>'.PHP_EOL

                    ."\t\t".'<span class="phpErrorTypeLabel">Type : </span>'.PHP_EOL
                    ."\t\t".'<span class="phpErrorType">E_WARNING</span><br/>'.PHP_EOL

                    ."\t\t".'<span class="phpErrorMsgLabel">Message : </span>'.PHP_EOL
                    ."\t\t".'<span class="phpErrorMsg">'.$errstr.'</span>'.PHP_EOL
                ."\t".'</div>'.PHP_EOL;
            $errHTML .=
                "\t".'<div class="phpDebugBacktrace"><span class="phpDebugBacktraceLabel">Backtrace : </span>'.PHP_EOL."\t\t".$this->backtrace('html').PHP_EOL."\t".'</div>'.PHP_EOL.PHP_EOL;
            //echo '<pre style="color:darkgreen;font-size:small;">'; var_dump ($errcontext); echo '</pre>'; // info overload, usually
        $errHTML .= '</div>'.PHP_EOL.PHP_EOL;

        global $naIP;
        $date = date('Y-m-d H:i:s');

        $errstr = '';
        foreach ($res as $rIdx => $rec) {
            if (is_string($rIdx)) continue;
            $cNormal = "\x1b[39;49m";
            $cUsesSSL = array_key_exists('useSSL', $rec['c']['cRec']) && $rec['c']['cRec']['useSSL'] ? "\x1b[32;44m" : "\x1b[36;44m";
            $host = $cUsesSSL.$rec['c']['ct'].' : '.$rec['c']['cRec']['username'].':*****@'.$rec['c']['cRec']['host'].':'.$rec['c']['cRec']['port'].$cNormal.' : '.json_encode($rec['resultValue'],JSON_PRETTY_PRINT).PHP_EOL;
            $errstr .= $host;
        };
        $errANSI = PHP_EOL."\033[37;49;1m[ ".$naIP." : ".$date."]".PHP_EOL."\033[31;47;3;1mPHP E_WARNING :\033[35;47;3;1m".$errstr.",\033[31;47;3;1m backtrace = \033[37;49m".PHP_EOL.$this->backtrace('ansi')."\033[37;49m".PHP_EOL;

        $errstr = '';
        foreach ($res as $rIdx => $rec) {
            if (is_string($rIdx)) continue;
            $host = $rec['c']['ct'].' : '.$rec['c']['cRec']['username'].':*****@'.$rec['c']['cRec']['host'].':'.$rec['c']['cRec']['port'].' : '.json_encode($rec['resultValue'],JSON_PRETTY_PRINT);
            if ($errstr!=='') $errstr.=', ';
            $errstr .= $host;
        };
        $errTXT = "[".$naIP." : ".$date."] PHP E_WARNING : ".$errstr.", backtrace = ".PHP_EOL.$this->backtrace('txt');


        $x = [
            'ENTRY:add_PHPerror' => [
                't' => $unixTimeStamp,
                'to' => $dtz_offset,
                'ts' => $timestamp,
                'ip' => $naIP,
                'sid' => session_id(),
                'nav' => $naVersionNumber,
                'html' => $errHTML,
                'ansi' => $errANSI,
                'txt' => $errTXT
            ]
        ];
        //echo '<pre>'; var_dump ($x); exit();
        return $x;
    }

    public function backtrace($returnType='html') {
        $data = debug_backtrace();
        //$data = array_splice ($data, 1, 2);
        //return '<pre>'.json_encode($data, JSON_PRETTY_PRINT).'</pre>';
        $html = '';
        $txt = '';
        while (count($data) > 0) {
            
            $it = array_shift($data);

            if (
                array_key_exists('function', $it) 
                && (
                    // ignore these, as they are part of the custom error handling system itself :
                    $it['function'] == 'backtrace'
                    || $it['function'] == 'add'
                    || $it['function'] == 'mainErrorHandler'
                    || $it['function'] == 'trigger_error'
                )
            ) continue;

            switch ($returnType) {
                case 'html' :
                    $html .= "\t\t\t<div class=\"backtraceItem\">".PHP_EOL;
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
                    return $html;

                case 'ansi':
                    $ansi = '';
                    if (array_key_exists('class', $it) && $it['class']!=='')
                        $ansi .= "\033[33;49;3mCLASS ".$it['class'].' '.$it['type'].' '.$it['function']."\033[37;49m".PHP_EOL;
                    elseif (array_key_exists('function', $it) && $it['function']!=='')
                        $ansi .= "\033[33;49;3mFUNCTION ".$it['function']."\033[37;49m".PHP_EOL;
                    if (array_key_exists('file', $it) && $it['file']!=='')
                        $ansi .= "\033[32;49;3mLocation : ".$it['file'].', lineNum='.$it['line']."\033[37;49m".PHP_EOL;
                    if (array_key_exists('args',$it) && is_array($it['args']) && count($it['args']) > 0) {
                        $ansi .= "\033[34;49mArguments : ".PHP_EOL;
                        foreach ($it['args'] as $idx => $arg) {
                            $ansi .= json_encode ($arg, JSON_PRETTY_PRINT).','.PHP_EOL;
                        }
                        $ansi .= "\033[37;49m";
                    }
                    return $ansi;

                case 'txt':
                    $txt = '';
                    if (array_key_exists('class', $it) && $it['class']!=='')
                        $txt .= "CLASS ".$it['class'].' '.$it['type'].' '.$it['function'].PHP_EOL;
                    elseif (array_key_exists('function', $it) && $it['function']!=='')
                        $txt .= "FUNCTION ".$it['function'].PHP_EOL;
                    if (array_key_exists('file', $it) && $it['file']!=='')
                        $txt .= "Location : ".$it['file'].', lineNum='.$it['line'].PHP_EOL;
                    if (array_key_exists('args',$it) && is_array($it['args']) && count($it['args']) > 0) {
                        $txt .= "Arguments : ".PHP_EOL;
                        foreach ($it['args'] as $idx => $arg) {
                            $txt .= json_encode ($arg, JSON_PRETTY_PRINT).','.PHP_EOL;
                        }
                        $txt .= "\033[37;49m";
                    }
                    return $txt;
            }
        }
    }
}

$naErr = new class_NicerAppWebOS__errorHandler();
global $naErr;

?>
