<?php
//require_once (dirname(__FILE__).'/1.0.0/functions.php');
//require_once (dirname(__FILE__).'/functions__internalErrorHandling.php');
$supressWoErrors = false; global $supressWoErrors;

function errorHandlingStyleLinks () {
?>
<link type="text/css" rel="StyleSheet" media="screen" href="<?php echo SA_SITE_WEB?>/css_saErrorHandler.css"> 
<?php 
}

function reportVariable ($varName, $varValue, $stacktrace=false) {
    echo '<div class="reportVariable">'."\r\n";
		echo "\t".'<span class="reportVariable__varName">'."\r\n";
		echo "\t".$varName."\r\n";
		echo "\t".'</span> = '."\r\n";
		echo "\t".'<div class="reportVariable__varValue">'."\r\n";
		echo "\t".'<pre>'."\r\n";
		echo svar_dump ($varValue)."\r\n";
		echo "\t".'</pre>'."\r\n";
		echo "\t".'</div>'."\r\n";
		echo "\t".'<div class="reportVariable__stacktrace">'."\r\n";
		if ($stacktrace) {
			$stacktrace = debug_backtrace();
			echo woBasicErrorHandler__prettyStacktrace ($stacktrace)."\r\n";
		}
		echo "\t".'</div>'."\r\n";
    echo '</div>'."\r\n"."\r\n";
}

function woError ($errCode, $errMsgOrArray) {
	if (is_array($errMsgOrArray)) {

		if (ob_get_length()>0) ob_end_flush();
		ob_start();
		var_dump ($errMsgOrArray);
		$errMsg = ob_get_clean();
	
	
	} else if (
		is_string($errMsgOrArray)
		|| is_int($errMsgOrArray)
		|| is_float($errMsgOrArray)
	) {
		$errMsg = '' . $errMsgOrArray;
	} else if (
		is_bool($errMsgOrArray)
	) {
		$errMsg = (
			$errMsgOrArray
			? 'TRUE'
			: 'FALSE'
		);
	}
	
	//echo 't1200';
	//echo '<pre>'; var_dump (debug_backtrace()); echo '</pre>'; in badResult() instead
	global $supressWoErrors;
	if ($supressWoErrors==false) {
            trigger_error ($errMsg, $errCode);
        }
		
	unset ($_COOKIE['browserWidth']);
	unset ($_COOKIE['browserHeight']);
		
}

function woBasicErrorHandler ($errno, $errstr, $errfile, $errline, $errcontext) {
  //echo 'woBasicErrorHandler.main:start';
	$stacktrace = debug_backtrace();
	$errType = wo_php_errorType_humanReadable($errno);
	$errSeverity = 'woErrorSeverity__error';
	if (stripos($errType, 'warning')!==false) $errSeverity = 'woErrorSeverity__warning';
	if (stripos($errType, 'notice')!==false) $errSeverity = 'woErrorSeverity__notice';

	if (stripos($errType, 'user')!==false) $errSeverity .= ' woErrorSeverity__user';
	if (stripos($errType, 'parse')!==false) $errSeverity .= ' woErrorSeverity__parse';
	if (stripos($errType, 'core')!==false) $errSeverity .= ' woErrorSeverity__core';
	if (stripos($errType, 'compile')!==false) $errSeverity .= ' woErrorSeverity__compile';
	
	$html = 
		'<div class="woError '.$errSeverity.'"><h1>webappObfuscator error</h1>'
		.'<p>'.$errType.' : '.$errstr.'</p>'
		.woBasicErrorHandler__prettyStacktrace ($stacktrace)
		.'</div>';
		
	echo $html;
	//reportStatus (1, $html); // RV : where the F is that function??
	
	//die();
	
	
	
	/*
	echo '<h1>dfo error</h1><p>'.errorType_humanReadable($errno).' : '.$errstr.'<br/>stacktrace:</p><pre>';
	var_dump ($stacktrace);
	echo '</pre>';*/
	unset ($_COOKIE['browserWidth']);
	unset ($_COOKIE['browserHeight']);
}

function woBasicErrorHandler__prettyStacktrace ($st) {
	global $errorscodePath;
	
	$r = 
		'<div class="woStacktrace">'
		.'<span class="woStacktrace__codePath">All filenames are under : '.$errorscodePath.'</span><br/>';
		
	foreach ($st as $stackNumber => $stackData) {
		if (array_key_exists('file', $stackData)) {
			$relPath = '...'.str_replace($errorscodePath, '', $stackData['file']);
		} else {
			$relPath = '.../';
		};
		
		if (array_key_exists('line', $stackData)) {
			$line = '<span class="woStacktrace__line">line '.$stackData['line'].'</span>';
		} else {
			$line = '';
		}
		
		$file = '<span class="woStacktrace__file">__FILE__ : '.$relPath.'</span> ';
		$function = 
			'<span class="woStacktrace__function">'.$stackData['function'].'( '
			.(
					array_key_exists('args',$stackData)
					? woBasicErrorHandler__prettyStacktrace__arguments ($stackData['args'])
					: ''
			)
			.' )</span>';
			
		//if ($stackNumber > 0) { // ignore the call to saBasicErrorHandler() itself
			$r .= 
				'<div class="woStacktrace__item">'
				.'<b>'.$line.' in '
				.$file.'</b> '
				.' calls '
				.$function
				.'</div><br/>';
		//}
	};
	
	$r .= '</div>';
	return $r;
}

function woBasicErrorHandler__prettyStacktrace__arguments ($args) {
	$r = '<span class="woStacktrace__args">';
	foreach ($args as $argIdx => $arg) {
		if (is_array($arg)) {
			$r .= '<span class="woStacktrace__arg">'.htmlentities(json_encode($arg)).'</span>';
		} elseif (is_object($arg)) {
			$r .= '<span class="woStacktrace__arg">'.htmlentities(json_encode($arg)).'</span>';
		} else {
			$r .= '<span class="woStacktrace__arg">'.htmlentities($arg).'</span>';	
		}
		$r .= '<span class="woStacktrace__argSeperator">, </span>';
	}
	$r .= '</span>';
	return $r;
}

function wo_php_json_last_error_humanReadable ($errNo) {
	// taken from http://php.net/manual/en/function.json-last-error.php
	// on 2015 July 9th, valid for php version up to 5.5.0
	$errorTypes = array (
		JSON_ERROR_NONE => array (
			'errorCode' => 'JSON_ERROR_NONE',
			'msg' => 'No error has occurred'
		),
		JSON_ERROR_DEPTH => array (
			'errorCode' => 'JSON_ERROR_DEPTH',
			'msg' => 'The maximum stack depth has been exceeded'
		),
		JSON_ERROR_STATE_MISMATCH => array (
			'errorCode' => 'JSON_ERROR_STATE_MISMATCH',
			'msg' => 'Invalid or malformed JSON'
		),
		JSON_ERROR_CTRL_CHAR => array (
			'errorCode' => 'JSON_ERROR_CTRL_CHAR',
			'msg' => 'Control character error, possibly incorrectly encoded'
		),
		JSON_ERROR_SYNTAX => array (
			'errorCode' => 'JSON_ERROR_SYNTAX',
			'msg' => 'Syntax error'
		),
		JSON_ERROR_UTF8 => array (
			'errorCode' => 'JSON_ERROR_UTF8',
			'msg' => 'Malformed UTF-8 characters, possibly incorrectly encoded'
		)/*,
		JSON_ERROR_RECURSION => array (
			'errorCode' => 'JSON_ERROR_RECURSION',
			'msg' => 'One or more recursive references in the value to be encoded'
		),
		JSON_ERROR_INF_OR_NAN => array (
			'errorCode' => 'JSON_ERROR_INF_OR_NAN',
			'msg' => 'One or more NAN or INF values in the value to be encoded'
		),
		JSON_ERROR_UNSUPPORTED_TYPE => array (
			'errorCode' => 'JSON_ERROR_UNSUPPORTED_TYPE',
			'msg' => 'A value of a type that cannot be encoded was given'
		)*/
	);
	if ($errNo===0) {
		
		$r = $errorTypes[0]; 
	} else {
	
		$r = 
			array_key_exists ($errNo, $errorTypes)
			? $errorTypes[$errNo]
			: array (
				'errorCode' => 'ERROR_UNKNOWN_ERROR',
				'msg' => 'json_last_error() returned a code that is unknown to fucntions__basicErrorHandling.php::wo_php_json_last_error_humanReadable()'
			);
	};
	return $r;
			
}

function wo_php_errorType_humanReadable ($errNo) {

    if (phpversion() < '4.0.0') {
	$errorTypes = array (
			1   =>  'Error',
			2   =>  'Warning',
			4   =>  'Parsing Error',
			8   =>  'Notice',
		     2047   => 	'E_ALL'
	);
    } elseif (phpversion() < '5.0.0') {
	$errorTypes = array (
			1   =>  'Error',
			2   =>  'Warning',
			4   =>  'Parsing Error',
			8   =>  'Notice',
			16  =>  'Core Error',
			32  =>  'Core Warning',
			64  =>  'Compile Error',
			128 =>  'Compile Warning',
			256 =>  'User Error',
			512 =>  'User Warning',
			1024=>  'User Notice',
			2047=> 	'E_ALL'
	);

    } elseif (phpversion() < '5.2.0') {
	$errorTypes = array (
			1   =>  'Error',
			2   =>  'Warning',
			4   =>  'Parsing Error',
			8   =>  'Notice',
			16  =>  'Core Error',
			32  =>  'Core Warning',
			64  =>  'Compile Error',
			128 =>  'Compile Warning',
			256 =>  'User Error',
			512 =>  'User Warning',
			1024=>  'User Notice',
			2048=> 	'Strict',
			2047=> 	'E_ALL'
	);

    } elseif (phpversion() < '5.3.0') {
	$errorTypes = array (
			1   =>  'Error',
			2   =>  'Warning',
			4   =>  'Parsing Error',
			8   =>  'Notice',
			16  =>  'Core Error',
			32  =>  'Core Warning',
			64  =>  'Compile Error',
			128 =>  'Compile Warning',
			256 =>  'User Error',
			512 =>  'User Warning',
			1024=>  'User Notice',
			2048=> 	'Strict',
			4096=> 	'Recoverable',
			6143=> 	'E_ALL'
	);

    } elseif (phpversion() >= '5.3.0' && phpversion() < '6.0.0') {
	$errorTypes = array (
			1   =>  'Error',
			2   =>  'Warning',
			4   =>  'Parsing Error',
			8   =>  'Notice',
			16  =>  'Core Error',
			32  =>  'Core Warning',
			64  =>  'Compile Error',
			128 =>  'Compile Warning',
			256 =>  'User Error',
			512 =>  'User Warning',
			1024=>  'User Notice',
			2048=> 	'Strict',
			4096=> 	'Recoverable',
			8192=> 	'Depracated',
		       16384=>	'User-level Depracated',
		       30719=> 	'E_ALL'
	);

    } elseif (phpversion() >= '6.0.0') {
	$errorTypes = array (
			1   =>  'Error',
			2   =>  'Warning',
			4   =>  'Parsing Error',
			8   =>  'Notice',
			16  =>  'Core Error',
			32  =>  'Core Warning',
			64  =>  'Compile Error',
			128 =>  'Compile Warning',
			256 =>  'User Error',
			512 =>  'User Warning',
			1024=>  'User Notice',
			2048=> 	'Strict',
			4096=> 	'Recoverable',
			8192=> 	'Depracated',
		       16384=>	'User-level Depracated',
		       32767=> 	'E_ALL'
	);
    }

    return $errorTypes[$errNo];
}

?>
