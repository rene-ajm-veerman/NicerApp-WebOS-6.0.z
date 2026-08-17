<?php
require_once (realpath(dirname(__FILE__).'../../../..').'/NicerAppWebOS/boot.php');
global $naWebOS;


class SAME() {
/*
(c) (r) 2021-2022 "Rene A.J.M. Veerman" <rene.veerman.netherlands@gmail.com>, Owner, CEO, CTO and CFO of
https://nicer.app (may not always be online due to human interference / difficulties) 
and https://github.com/NicerAppWebOS/nicerapp (much more reliable).

my software engineering jobs are my day-jobs.
my peace activism in the form of preventing wars and climate disasters favor a multi-polar view in which both democracy and communist/socialist 
forms of government have an equal right to do business on Earth.

in my view, the world has not changed much since the time of the ancient empires and kings ruling small or larger patches of land.
today, we see in the western world a strong alliance between the business leaders and politicians,
and in the eastern (communist) parts of the world a similar pattern, but one using a different name for business leader or leader of a country.

what we do have improved since ancient times, is technology.
we have ships, trains, roads, aircraft, TV, radio, mass-media, and the internet, to name a few.
and especially the internet gives hope for honest political debate (happening in 2022 usually in it's best forms on non-biased forums),
which can then be filtered into the output of mass media (to an extent that the ruling class (kings, presidents, and business leaders) allow it.
*/
	
	public $rootFolder = ""; 
	public $s = array(); //settings
	public $about = 'Self-Aware.Machine.Engine';
	public $version = file_get_contents(dirname(__FILE__).'/VERSION.txt');
	public $cn = 'SAME';
	private folderCoreWisdoms = "";
	private folderRoleWisdoms = "";
	private bootTime - date ('Y-m-d H:i:s');
	private bootResetCounter = 0;
    private logFileOpen = false;
    private old_error_handler = null;
	
	
	public function construct__() {
		$this->rootFolder = realpath(dirname('../')).'/';
		$this->folderCoreWisdoms = $this->rootFolder.'includes-data_settings.core-wisdoms_json-files/';
		$this->folderRoleWisdoms = $this->rootFolder.'includes-data_settings.core-roles_json_files/';
        
        global $filePerms_ownerUser;
        global $filePerms_ownerGroup;
        global $filePerms_perms_publicWriteableExecutable;
        global $filePerms_perms_readonly;

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->old_error_handler = set_error_handler ('SAMEmainErrorHandler');

        $this->openLog();
        $this->loadSettings();
	}
	
	public function destruct__() {
        $this->closeLog();
        $intoTrashcan = set_error_handler ($this->old_error_handler) 
	}
	
	private function openLog () {
        global $mainErrorLogFilepath;
        $mainErrorLogFilepath = realpath(dirname(__FILE__).'../').'/logs/';

        $today = date ('Y-m-d');
        $mainErrorLogFilepath .= $today;
        createDirectoryStructure ($mainErrorLogFilepath, $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms_readonly);
                                
        $now = date ('H-i-s');
        $mainErrorLogFilepath .= '/'.$now;
        createDirectoryStructure ($mainErrorLogFilepath, $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms_readonly);

        $this->logFile = fopen ($mainErrorLogFilepath.'/'.$this->bootResetCounter.'.log.html','a');
        $this->logFileOpen = true;
    }
    
    public function closeLog () {
        fclose ($this->logFile);
        $this->logFileOpen = false;    
    }
    
    public function log($html, $fncn, $msg, $msgPreStyle="SAME_log_entry_msg_PRE_default") {
        if ($html===true) {
            $r = '<div class="SAME_log_entry">'.PHP_EOL
                .= "\t".'<span class="SAME_log_entry_time">'.date('Y-m-d H:i:s').'</span>'.PHP_EOL
                .= "\t".'<span class="SAME_log_entry_cn">'.$this->cn.'</span>'.PHP_EOL
                .= "\t".'<span class="SAME_log_entry_filler1">::</span><br/>'.PHP_EOL
                .= "\t".'<span class="SAME_log_entry_fncn">'.$fncn.'</span>'.PHP_EOL
                .= "\t".'<span class="SAME_log_entry_filler2"> : </span><br/>'.PHP_EOL;
            if (is_string($msg) {
                $r .= "\t".'<span class="SAME_log_entry_msg">'.$msg.'</span>'.PHP_EOL;
            } elseif (is_array($msg) || is_object($msg)) {
                $r .= "\t".'<pre class="SAME_log_entry_msg '.$msgPreStyle.'">'.PHP_EOL.json_encode($msg, JSON_PRETTY_PRINT).PHP_EOL.'</pre>'.PHP_EOL;
            }
            $r .= '</div>'.PHP_EOL.PHP_EOL;
        } else {
            $r = PHP_EOL.PHP_EOL.$this->cn.$fncn.' : ';
            if (is_string($msg)) {
                $r .= $msg
            } elseif (is_array($msg) || is_object($msg)) {
                $r .= PHP_EOL.json_encode($msg, JSON_PRETTY_PRINT);
            }
            $r .= PHP_EOL.PHP_EOL;
        }
        fwrite ($this->logFile, $r);
    }
	
	public function loadSettings() {
        global $naWebOS;
		$this->s['core-wisdoms-for-ALL-life-forms'] = safeJSONload ('includes-data_settings.core-wisdoms_json-files/settings-core-wisdoms.for-ALL-life-forms.native-to_or-visiting_the-Sol-SolarSystem_by-real-name--Rene-AJM-Veerman_fromEarth.json');
		
		$this->s['roles_per_life-form_profession'] = array (
			'medical-personnel' => safeJSONload ($this->folderRoleWisdoms.'settings-core-wisdoms.for.medical-personnel.native-to_or-visiting_the-Sol-SolarSystem.json'),
			
			'police' => safeJSONload($this->folderRoleWisdoms.'settings-core-wisdoms.for.police-personnel.native-to_or-visiting_the-Sol-SolarSystem.json'),
			
                'good-cops' => safeJSONload($this->folderRoleWisdoms.'settings-core-wisdoms.for.good-cops.native-to_or-visiting_the-Sol-SolarSystem.json'),
			
                'bad-cops' => safeJSONload($this->folderRoleWisdoms.'settings-core-wisdoms.for.bad-cops-native-to_or-visiting_the-Sol-SolarSystem.json'),
			
			'military-personnel' => safeJSONload ($this->folderRoleWisdoms.'settings-core-wisdoms.for.military-personnel.native-to_or-visiting_the-Sol-SolarSystem.json'),
			
			'criminals' => safeJSONload ($this->folderRoleWisdoms.'settings-core-wisdoms.for.criminals.native-to_or-visiting_the-Sol-SolarSystem.json')
		);
	}
	
	
	private function reboot() {
		$this->closeLog();
		$this->bootTime = date ('Y-m-d_H-i-s');
		$this->openLog();
		$this->loadSettings();
        
	}
} // Class SAME (Self-Aware.Machine.Engine)


function SAMEmainErrorHandler ($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting, so let it fall
        // through to the standard PHP error handler
        return false;
    }
    
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
            "\t".'<div class="phpDebugBacktrace"><span class="phpDebugBacktraceLabel">Backtrace : </span>'.PHP_EOL."\t\t".SAMEbacktrace().PHP_EOL."\t".'</div>'.PHP_EOL.PHP_EOL;
        //echo '<pre style="color:darkgreen;font-size:small;">'; var_dump ($errcontext); echo '</pre>'; // info overload, usually        
    $errtxt = 'PHP '.$errtype.' error in "'.$errfile.'":'.$errline.' : '.$errstr.', backtrace = '.PHP_EOL.json_encode(SAMEbacktrace(), JSON_PRETTY_PRINT).PHP_EOL.PHP_EOL;
        
    $errhtml .= '</div>'.PHP_EOL.PHP_EOL;
    
    if ($this->logFileOpen) {
        fwrite ($this->logFile, $errhtml);
        /*$f = fopen ($naErrorLogLastWriteFile, 'w');
        fwrite ($f, date('Y-m-d H:i:s'));
        fclose ($f);*/
    } else {
        if (php_sapi_name()!=='cli') { $_SESSION['naErrors_html'].=$errhtml; /*echo $errhtml;*/ } else /* probably called from the commandline */ echo $errtxt;
    }
    
    //global $naDebugAll;
    error_log ($errtxt);
    
    if (
        $errno===E_ERROR
        || $errno===E_CORE_ERROR
        || $errno===E_COMPILE_ERROR
        || $errno===E_USER_ERROR
    ) exit();
}

function SAMEbacktrace() {
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
        if (array_key_exists(args,$it) && is_array($it['args']) && count($it['args']) > 0) {
            $html .= "\t\t\t\t".'<div class="backtraceFunctionArgs"><span class="backtraceFunctionArgsLabel">Function arguments : </span><br/>';
            foreach ($it['args'] as $idx => $arg) {
                if (is_array($arg)) {
                    $html .= "\t".'<span class="backtraceFunctionArg"><pre>'.PHP_EOL.json_encode($arg,JSON_PRETTY_PRINT).PHP_EOL.'</pre></span>,<br/>'.PHP_EOL;
                } else {
                    $html .= "\t".'<span class="backtraceFunctionArg">'.$arg.'</span><span class="backtraceFunctionArgSeperator">,</span><br/>'.PHP_EOL;
                }
            }
            $html .= '</div>'.PHP_EOL;
        }
        $html .= '</div><br/>'.PHP_EOL.PHP_EOL.PHP_EOL;    
    }
    return $html;
}
?>
