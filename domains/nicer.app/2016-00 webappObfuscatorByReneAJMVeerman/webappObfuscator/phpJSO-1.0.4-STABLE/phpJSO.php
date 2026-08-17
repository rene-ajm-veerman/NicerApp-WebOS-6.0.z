<?php
/**
 * phpJSO - The Javascript Obfuscator written in PHP. Although
 * it effectively obfuscates Javascript code, it is meant to compress
 * code to save disk space rather than hide code from end-users.
 *
 * @started: Mon, May 23, 2005
 * @copyright: Copyright (c) 2004-2006 Cortex Creations, All Rights Reserved
 * @website: www.cortex-creations.com/phpjso
 * @license: Free, zlib/libpng license - see LICENSE
 * @version: 0.9
 * @subversion: $Id: phpJSO.php 70 2006-10-10 01:35:37Z josh $
 
 * @started: 2015 May 16
 * @copyright : Copyright (c) 2015 SeductiveApps.com
 * @website : seductiveapps.com
 * @version : 1.0.4
 * @subversion : $Id: phpJSO.php 71 2015-05-27 23:28:??Z rene_veerman <phpjso@seductiveapps.com> $
 * @changed : true obfuscation - replace all variable names with random strings except those in a whitelist..
 * @status : 2015 May 28 - being worked on.
 * @plannedChanges : 
 
 see http://seductiveapps.com/tools/webappObfuscator (and a link to it's homepage _without_ the 10 to 15MB of pretty artwork for my site, is at http://seductiveapps.com/webappObfuscator )
 
 */
global $randomStringLength;
$randomStringLength=3; // good for (26+26+1)^3=148877 $tokens.
global $randomStringLength;

/**
 * Main phpJSO compression function. Pass Javascript code to it, and it will
 * return compressed code.
 */
function phpJSO_compress ($code, &$messages, $encoding_type, $fast_decompress, $collapse_blocks, $collapse_math_constants)

/*
	parameters : -subject to change in nearby future-

	$code = your original sourcecode. tested up to 1.5MB of sourcecode for seductiveapps.com site
	$messages = pass empty array to be filled with output of the obfuscation process.
	$encoding_type = always set to '1' if u want anything done.
	$fast_decompress = always set to true
	$collapse_blocks = always set to false - dont work yet.
	$collapse_math_constants = always set to false - dont work yet.

	returns : see bottom of this function - an array containing multiple versions of the output :p
		- for now only 'obfuscated_fast' has been tested.. does not work flawlessly yet for my site, although my code will run and boot up the visuals, the userinteractions from my custom menu component dont work yet...
		
	process description :
		stage 1 : strip out all actual strings and regular expressions into a single numbered array. replace them with the index number of the array they're moved into.
		stage 2 : grab everything that is a variable name (methods/properties too) and any other words in the sourcecode like var, function, etc. put 'm all into an array $tokens
		stage 3 : replace everything in $tokens except what hits the whitelist $ignoreList with a random string.
		stage 4 : put the regular expressions and string from stage 1 back in.
		stage 5 : enjoy.
		
*/	

{
	echo ('<pre>');

	// Start timer
	$start_time = phpJSO_microtime_float();
	
	// Array of tokens - alphanumeric
	$tokens = array();
	//for ($i=0;$i<999;$i++) { $tokens[] = ''; }
	
	// Array of only numeric tokens, that are only inserted to prevent being
	// wrongly replaced with another token. For example: the integer 0 will
	// be replaced with whatever is at token index 0.
	$numeric_tokens = array();
	
	//$code = str_replace ('var', 'var ', $code);
	$code = preg_replace("/var\r/", 'var ', $code);
	
	$original_code = $code;
	
	// Save original code length
	$original_code_length = strlen($code);
	
	// Remove strings and multi-line comments from code before performing operations
	$str_array = array();
	phpJSO_strip_strings_and_comments($code, $str_array, substr(md5(time()), 10, 2));
	$code_minified = $code;
	/*
	foreach ($str_array as $i=>$s) {
		echo '$str_array__'.$i.' : '.htmlentities($s).'<br/>';
	}
	*/
	
	// Strip junk from JS code
	phpJSO_strip_junk($code, true);
	if ($collapse_blocks)
	{
		$collapsed_blocks = 0;
		$code = phpJSO_collapse_blocks($code, $collapsed_blocks);
		$messages[] = 'Block collapse mode on: ' . $collapsed_blocks . ' blocks were collapsed.';
	}
	phpJSO_strip_junk($code);

	
	// Compress math constants in code?
	if ($collapse_math_constants)
	{
		$collapsed_math_constants = 0;
		$code = phpJSO_collapse_math($code, $collapsed_math_constants);
		$messages[] = 'Math constant collapse mode on: ' . $collapsed_math_constants . ' math constants were collapsed.';
	}
	
	/*
	// Add strings back into code - NOT HERE EH!
	echo ('t3.1 $code<br/>');
	echo ($code.'<br/>');
	//die();
	
	
	//phpJSO_restore_strings($code, $str_array);
	
	echo ('t3.2 $code<br/>');
	echo ($code.'<br/>');
	*/
	
	// BUG FIX: If a modulus is in the code, it will break obfuscation because the browser treats it as escaping of characters
	$code = str_replace('%', '% ', $code);
	$code = str_replace('% 20', '%20', $code);
	
	
	// Compressed code
	$compressed_code = $code;
	$code_minus_strings = $code;
	$code_with_tokens_replaced = $code;

	// Should we encode?
	if ($encoding_type == '1')
	{
		
		
		//echo ('t4.1 $code_minus_strings===$code <br/>');
		//echo (html_entities($code_minus_strings).'<br/>');

		phpJSO_strip_strings ($code_minus_strings);

		//echo ('t4.2 $code_minus_strings <br/>');
		//echo (htmlentities($code_minus_strings).'<br/>');
		
		// Find all tokens in code
		phpJSO_get_tokens($code_minus_strings, $numeric_tokens, $tokens);
		
		//!!!! $tokens is what we work with
		$key = array_search('Function', $tokens);
		if ($key!==false) {
			unset ($tokens[$key]);
		}
	
		
		
		// Insert numeric tokens into token array
		// phpJSO_merge_token_arrays($tokens, $numeric_tokens); // SUSPECTED BUGGY
		//$tokens += $numeric_tokens;

		//$tokensOffset = array();
		//for ($i=0;$i<999;$i++) { $tokensOffset[] = ''; }
		//array_splice($tokens, 0, 0, $tokensOffset);

		usort($tokens,'sortByStringLength');
		
		$tokens2 = array();
		foreach ($tokens as $i=>$t) {
			global $randomStringLength;
			$newkey = randomStringJSO ($randomStringLength);
			while (
				array_key_exists($newkey, $tokens2)
				|| strpos ($code, $newkey)!==false
			) $newkey = randomStringJSO ($randomStringLength);
			$tokens2[$newkey] = $t;
		};
		$tokens = $tokens2;
		
		
		
		reset($tokens);
		$tokensObfuscated = $tokens;
		//array_splice($tokensObfuscated, 0, 0, $tokensOffset);
		reset($tokensObfuscated);

		foreach ($tokens as $i=>$t){
			echo 'tokens_'.$i.' : '.htmlentities($t).'<br/>';
		};
		
		// Replace all tokens with their token index
		phpJSO_replace_tokens($tokens, $code_with_tokens_replaced);
		//echo ('t5.2 $code_with_tokens_replaced <br/>');
		//echo (($code_with_tokens_replaced).'<br/>');
		
		
		
		reset($tokens);
		
		phpJSO_obfuscate_tokens ($tokens, $tokensObfuscated, $code);

		
		$tokensKeys = array_keys($tokens);
		$tokensObfuscatedKeys = array_keys($tokensObfuscated);

		$code_obfuscated = $code_with_tokens_replaced;
		
		// for current $code_obfuscated_fast
		foreach ($tokens as $k=>$original) {
			if ($tokensObfuscated[$k]==$original) {
			/*
				$t = array(
					'$k' => $k,
					'$original' => $original
				);
				var_dump ($t);
			*/
				$code_obfuscated = preg_replace('#\b'.$k.'\b#', $original, $code_obfuscated);
			}
		}
		
		
		phpJSO_restore_strings($code_obfuscated, $str_array);
		phpJSO_restore_strings($code_compressed, $str_array);
		
		//$code_with_tokens_replaced_and_strings_restored = $code_obfuscated;
		//phpJSO_restore_strings($code_with_tokens_replaced_and_strings_restored, $str_array);
		//echo ('t5.3 $code_with_tokens_replaced_and_strings_restored<br/>');
		//echo (htmlentities($code_with_tokens_replaced_and_strings_restored).'<br/>');
		//$code_compressed = $code_with_tokens_replaced_and_strings_restored;
		//$code_obfuscated = $code_with_tokens_replaced_and_strings_restored;
		
		
		
		//var_dump ($tokens);
		
		// Insert decompression code
		$compressed_code_double_slash = '"'.str_replace(array('\\', '"'), array('\\\\', '\\"'), $code_compressed).'"';
		$compressed_code_single_slash = "'".str_replace(array('\\', "'"), array('\\\\', "\\'"), $code_compressed)."'";
		$code_compressed_fixed = (strlen($compressed_code_double_slash) < strlen($compressed_code_single_slash) ? $compressed_code_double_slash : $compressed_code_single_slash);
		
		/*
		$compressed_code_double_slash = '"'.str_replace(array('\\', '"'), array('\\\\', '\\"'), $code_obfuscated).'"';
		$compressed_code_single_slash = "'".str_replace(array('\\', "'"), array('\\\\', "\\'"), $code_obfuscated)."'";
		$code_obfuscated_fixed = (strlen($compressed_code_double_slash) < strlen($compressed_code_single_slash) ? $compressed_code_double_slash : $compressed_code_single_slash);
		*/
		
		
			$code_compressed_fast = "eval(function(a,b,c,d,e, f){if(false && !''.replace(/^/,String)){e=function(f){return c[f]&&typeof(c[f])=='string'?c[f]:f};b=1;alert(b);};;;while(b--){if(c[b]||e){var rege=new RegExp(f+(e?'\\w+':d[b])+f,'g');a=a.replace(rege,e||c[b])}};return a}($code_compressed_fixed,".count($tokens).",'".implode('|',$tokens)."'.split('|'),'".implode('|',$tokensKeys)."'.split('|'),0,'\\\\b'));";
			
			
			//$code_obfuscated_fast = "eval(function(a,b,c,d,e,f){if(false && !''.replace(/^/,String)){e=function(f){return c[f]&&typeof(c[f])=='string'?c[f]:f};b=1;alert(b);};;;while(b--){if(c[b]||e){var rege=new RegExp(f+(e?'\\w+':d[b])+f,'g');a=a.replace(rege,e||c[b])}};return a}($code_obfuscated_fixed,".count($tokensObfuscated).",'".implode('|',$tokensObfuscated)."'.split('|'),'".implode('|',$tokensObfuscatedKeys)."'.split('|'),0,'\\\\b'));";
			
			
			$code_obfuscated_fast = $code_obfuscated; //"eval(function(a,b,c,d,e,f){if(false && !''.replace(/^/,String)){e=function(f){return c[f]&&typeof(c[f])=='string'?c[f]:f};b=1;alert(b);};;;while(b--){if(c[b]||e){var rege=new RegExp(f+(e?'\\w+':d[b])+f,'g');a=a.replace(rege,e||c[b])}};return a}($code_obfuscated_fixed,".count($tokensObfuscated).",'".implode('|',$tokensObfuscated)."'.split('|'),'".implode('|',$tokensObfuscatedKeys)."'.split('|'),0,'\\\\b'));";

			/* TODO : pass both $tokens and $tokensKeys to make these statements work.. see commented-out code for $code_obfuscated_fast
			//nevermind, slow to decode in browser anyway
			$code_compressed = "eval(function(a,b,c,d){while(b--)if(c[b])a=a.replace(new RegExp(d+b+d,'g'),c[b]);return a}($code_compressed_fixed,".count($tokens).",'".implode('|',$tokens)."'.split('|'),'\\\\b'));";
			$code_obfuscated = "eval(function(a,b,c,d){while(b--)if(c[b])a=a.replace(new RegExp(d+b+d,'g'),c[b]);return a}($code_obfuscated_fixed,".count($tokensObfuscated).",'".implode('|',$tokensObfuscated)."'.split('|'),'\\\\b'));";
			*/
		
		// Which is smaller: compressed code or uncompressed code?
		/*
		if (strlen($code) < strlen($compressed_code))
		{
			$messages[] = 'The uncompressed code (with only comments and whitespace removed)
				was smaller than the fully compressed code. Using uncompressed code.';
			//$compressed_code = $code;
		}
		//$compressed_code = $code;
		*/
	}
	
	// End timer
	$execution_time = phpJSO_microtime_float() - $start_time;
	
	// Message about how long compression took
	$messages[] = "Compressed code in $execution_time seconds.";
	
	/*
	// Message reporting compression sizes
	$compressed_length = strlen($compressed_code);
	$ratio = $compressed_length / $original_code_length;
	$messages[] = "Original code length: $original_code_length.
		Compressed code length: $compressed_length.
		Compression ratio: $ratio.";
	*/
	
	echo ('</pre>');
	
	$code_obfuscated_fast = str_replace ('},', "},\r\n", $code_obfuscated_fast);
	
	$r = array (
		'source' => $code,
		'minus_strings' => $code_minus_strings,
		'tokens_replaced' => $code_with_tokens_replaced,
		'minified' => $code_minified,
		'compressed_fast' => $code_compressed_fast,
		'obfuscated_fast' => $code_obfuscated_fast//,
		//'compressed' => $code_compressed,
		//'obfuscated' => $code_obfuscated
	);
	return $r;
}

function randomStringJSO ($numChars) {
	$sourcepool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvw_';
	$r = '';
	for ($i = 0; $i < $numChars; $i++) {
		$random = rand (0, strlen($sourcepool)-1);
		$r .= substr($sourcepool, $random, 1);
	};
	return $r;	
}

function phpJSO_obfuscate_tokens (&$tokens, &$tokensObfuscated, &$code) {
		
	//var_dump ($tokens);
	foreach ($tokens as $i => $t) {
		echo 'before_'.$i.' : '.$t.'<br/>';
	}


	foreach ($tokens as $idx => $token) {
		$ignoreList = Array (
			'', 			
			/* javascript core */ 'major', 'minor', 'title', 'parseInt', 'parseFloat', 'constructor', 'toExponential', 'toFixed', 'toLocaleString', 'toPrecision', 'toString', 'valueOf', 'Boolean', 'Integer', 'Float', 'Number', 'String', 'Object', 'Array', 'Infinity', 'NaN', 'undefined', 'decodeURI', 'decodeURIComponent', 'encodeURI', 'encodeURIComponent', 'escape', 'eval', 'isFinite', 'isNaN', 'unescape', 'hasOwnProperty', 'createStyleSheet', 'QUOTA_EXCEEDED_ERR', 'arguments', 'callee', 'caller', 
			'null', 'false', 'true', 'undefined', 'instanceof', 'new', 'typeof', 'var', 'string', 'number', 'delete', 'unset', 'prototype', 'throw', 'Event', 'Error', 'event', 'preventDefault', 'Infinity', 'Date', 'getDate', 'getTime', 'Array', 'Function', 'Object', 'String', 'Image', 'fromCharCode', 'match', 'replace', 'indexOf', 'substr', 'function', 'if', 'else', 'while', 'for', 'as', 'switch', 'case', 'default', 'continue', 'break', 'return', 'try', 'catch', 'this', 'length', 'trim', 'append', 'top', 'left', 'width', 'height', 'css', 'documentElement', 'innerHTML', 'src'
			, 'cookie', 'each', 'alert', 'navigator', 'userAgent', 'console', 'log', 'window', 'push', 'slice', 'concat', 'call', 'apply', 'style', 'color', 'document', 'html', 'href', 'createElement', 'attachEvent', 'detachEvent', 'addEventListener', 'removeEventListener', 'debugger', 'Math', 'abs', 'sin', 'asin', 'pow', 'sqrt', 'PI', 'parentNode', 'removeChild', 'appendChild', 'target', 'remove', 'DOMParser', 'rgba', 'span', 'text', 'in', 'test', 'extend', 'callee', 'caller', 'before', 'random', 'RegExp', 'always', 'progress', 'unbind', 'plugin', 'iframe', 'focus', 'isNaN', 'webgl', 'first', 'title', 'opera', 'value'	, 'input', 'swing', 'default', 'delete', 'void', 'with', 'event', 
			'naturalWidth', 'naturalHeight', 
			
			/* future reserved by javascript */ 'class', 'enum', 'extends', 'super', 'const', 'export', 'import', 'implements', 'let', 'private', 'public', 'yield', 'interface', 'package', 'protected', 'static', 
			
			/* firefox error */ 'Error', 'EvalError', 'InternalError', 'RangeError', 'ReferenceError', 'SyntaxError', 'TypeError', 'URIError', 'columnNumber', 'fileName', 'lineNumber', 'message', 'name', 'stack', 'toSource', 'toString', 
			
			/* internet explorer error */ 'Error', 'constructor', 'prototype', 'description', 'message', 'name', 'number', 'stack', 'stackTraceLimit', 'toString', 'valueOf', // more at https://msdn.microsoft.com/en-us/library/htbw4ywd(v=vs.94).aspx
			
			/* try..catch.. */ 'stack', 'throw', 'try', 'catch', 'finally', 
			
			/* google excanvas */ 'getContext', 
			
			
			/* DOMparser */ 'DOMParser', 'parseFromString', 'async', 'loadXML', 
			
			/* XMLHttpRequest */ 'XMLHttpRequest', 'abort', 'getAllResponseHeaders', 'getResponseHeader', 'open', 'send', 'setRequestHeader', 'onreadystatechange', 'readyState', 'responseText', 'responseXML', 'status', 'statusText', 
			
			/* Boolean Object */ 'toSource', 'toString', 'valueOf', 
			
			/* String HTML wrappers */ 'anchor', 'big', 'blink', 'bold', 'fixed', 'fontcolor', 'fontsize', 'italics', 'link', 'small', 'strike', 'sub', 'sup', 
		
			/* Object */ 'constructor', 'length', 'prototype', 'assign', 'create', 'defineProperty', 'defineProperties', 'freeze', 'getOwnPropertyDescriptor', 'getOwnPropertyNames', 'getOwnPropertySymbols', 'getPrototypeOf', 'is', 'isExtensible', 'isFrozen', 'isSealed', 'keys', 'observe', 'preventExtensions', 'seal', 'setPrototypeOf',
			
			/* Array Object */ 'concat', 'indexOf', 'join', 'lastIndexOf', 'pop', 'push', 'reverse', 'shift', 'slice', 'sort', 'splice', 'toString', 'unshift', 'valueOf',
		
			/* String Object */ 'charAt', 'charCodeAt', 'concat', 'fromCharCode', 'indexOf', 'lastIndexOf', 'localeCompare', 'match', 'replace', 'search', 'slice', 'split', 'substr', 'substring', 'toLocaleLowerCase', 'toLocaleUpperCase', 'toLowerCase', 'toString', 'toUpperCase', 'trim', 'valueOf',
			
			/* Date Object*/ 'getDate', 'getDay', 'getFullYear', 'getHours', 'getMilliseconds', 'getMinutes', 'getMonth', 'getSeconds', 'getTime', 'getTimezoneOffset', 'getUTCDate', 'getUTCDay', 'getUTCFullYear', 'getUTCHours', 'getUTCMilliseconds', 'getUTCMinutes', 'getURCMonth', 'getUTCSeconds', 'getYear', 'parse', 'setDate', 'setFullYear', 'setHours', 'setMilliseconds', 'setMinutes', 'setMonth', 'setSeconds', 'setTime', 'setUTCDate', 'setUTCFullYear', 'setUTCHours', 'setUTCMilliseconds', 'setUTCMinutes', 'setUTCMonth', 'setUTCSeconds', 'setYear', 'toDateString', 'toGMTString', 'toISOString', 'toJSON', 'toLocaleDateString', 'toLocaleTimeString', 'toLocaleString', 'toString', 'toTimeString', 'toUTCString', 'UTC', 'valueOf', 'locale', 
			
			/* Date.locale extension */ 
				'en', 
					'month_names', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 
					'month_names_short', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 
			
			/*Math Object*/ 'LN2', 'LN10', 'LOG2E', 'LOG10E', 'PI', 'SQRT1_2', 'SQRT2', 'abs', 'acos', 'asin', 'atan', 'atan2', 'ceil', 'cos', 'exp', 'floor', 'log', 'max', 'min', 'pow', 'random', 'round', 'sin', 'sqrt', 'tan',
			
			/* console Object (CHROME) */ 'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log', 'profile', 'profileEnd', 'time', 'timeEnd', 'timeStamp', 'trace', 'warn', 'debugger',
			
			/* HTMLElement */ 'accessKey', 'addEventListener', 'appendChild', 'attributes', 'blur', 'childElementCount', 'childNodes', 'children', 'classList', 'className', 'click', 'clientHeight', 'clientLeft', 'clientTop', 'clientWidth', 'coneNode', 'compareDocumentPosition', 'contains', 'contentEditable', 'dir', 'firstChild', 'firstElementChild', 'focus', 'getAttribute', 'getAttributeNode', 'getElementsByClassName', 'getElementsByTagName', 'getFeature', 'hasAttribute', 'hasAttributes', 'hasChildNodes', 'id', 'innerHTML', 'insertBefore', 'isContentEditable', 'isDefaultNamespace', 'isEqualNode', 'isSameNode', 'isSupported', 'lang', 'lastChild', 'lastElementChild', 'namespaceURI', 'nextSibling', 'nextElementSibling', 'nodeName', 'nodeType', 'nodeValue', 'normalize', 'offsetHeight', 'offsetWidth', 'offsetLeft', 'offsetParent', 'offsetTop', 'ownerDocument', 'parentNode', 'parentElement', 'previousSibling', 'previousElementSibling', 'querySelector', 'querySelectorAll', 'removeAttribute', 'removeAttributeNode', 'removeChild', 'replaceChild', 'removeEventListener', 'scrollHeight', 'scrollLeft', 'scrollTop', 'scrollWidth', 'setAttribute', 'setAttributeNode', 'style', 'tabIndex', 'tagName', 'textContent', 'title', 'toString', 'item', 'length',


			/* HTML events */ 'onload', 'onclick', 'onmousemove', 'onmouseenter', 'onmouseout', 'onmouseover', 'onmousewheel', 'onwheel', 'oncontextmenu', 'ondblclick', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmouseup', 'onkeydown', 'onkeypress', 'onkeyup', 'onabort', 'onbeforeunload', 'onerror', 'onhashchange', 'onload', 'onpageshow', 'onpagehide', 'onresize', 'onscroll', 'onunload', 'onblur', 'onchange', 'onfocus', 'onfocusin', 'onfocusout', 'oninput', 'oninvalid', 'onreset', 'onselect', 'onsubmit', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'oncopy', 'oncut', 'onpaste', 'onafterprint', 'onbeforeprint', 'onabort', 'oncanplay', 'oncanplaythrough', 'ondurationchange', 'onemptied', 'onended', 'onerror', 'onloadeddata', 'onloadedmetadata', 'onloadstart', 'onpause', 'onplay', 'onplaying', 'onprogress', 'onratechange', 'onseeked', 'onseeking', 'onstalled', 'onsuspend', 'ontimeupdate', 'onvolumechange', 'onwaiting', 'animationend', 'animationiteration', 'animationstart', 'transitioned', 'onerror', 'onmessage', 'onopen', 'onmessage', 'ononline', 'onoffline', 'onpopstate', 'onshow', 'onstorage', 'ontoggle', 'onwheel', 'ontouchcancel', 'ontouchend', 'ontouchmove', 'ontouchstart', 'CAPTURING_PHASE', 'AT_TARGET', 'BUBBLING_PHASE', 'touches', 'changedTouches', 
			
			/* HTML Event Object */ 'Event', 'bubbles', 'cancelable', 'currentTarget', 'defaultPrevented', 'eventPhase', 'explicitOriginalTarget', 'originalTarget', 'target', 'timestamp', 'timeStamp', 'type', 'isTrusted', 'initEvent', 'preventBubble', 'preventCapture', 'preventDefault', 'stopImmediatePropagation', 'stopPropagation', 'getPreventDefault', 
			
			/* HTML UIEvent */ 'UIEvent', 'cancelBubble', 'detail', 'isChar', 'layerX', 'layerY', 'pageX', 'pageY', 'view', 'which', 'initUIEvent', 
			
			/* HTML MouseEvent */ 'MouseEvent', 'altKey', 'button', 'buttons', 'clientX', 'clientY', 'ctrlKey', 'metaKey', 'movementX', 'movementY', 'region', 'relatedTarget', 'screenX', 'screenY', 'shiftKey', 'which', 'mozPressure', 'mozInputSource', 'MOZ_SOURCE_UNKNOWN', 'MOZ_SOURCE_MOUSE', 'MOZ_SOURCE_PEN', 'MOZ_SOURCE_ERASER', 'MOZ_SOURCE_CURSOR', 'MOZ_SOURCE_TOUCH', 'MOZ_SOURCE_KEYBOARD', 'getModifierState', 'initMouseEvent', 
			
			/* HTML WheelEvent */ 'WheelEvent', 'deltaX', 'deltaY', 'deltaZ', 'deltaMode', 'DOM_DELTA_PIXEL', 'DOM_DELTA_LINE', 'DOM_DELTA_PAGE', 'wheelDelta', 
			
			/* HTML .style */ 'style', 'css', 'alignContent', 'alignItems', 'alignSelf', 'animation', 'animationDelay', 'animationDirection', 'animationDuration', 'animationFillMode', 'animationIterationCount', 'animationName', 'animationTimingFunction', 'animationPlayState', 'background', 'backgroundAttachment', 'backgroundColor', 'backgroundImage', 'backgroundPosition', 'backgroundRepeat', 'backgroundClip', 'backgroundOrigin', 'backgroundSize', 'backfaceVisibility', 'border', 'borderBottom', 'borderBottomColor', 'borderBottomLeftRadius', 'borderBottomRightRadius', 'borderBottomStyle', 'borderBottomWidth', 'borderCollapse', 'borderColor', 'borderImage', 'borderImageOutset', 'borderImageRepeat', 'borderImageSlice', 'borderImageSource', 'borderImageWidth', 'borderLeft', 'borderLeftColor', 'borderLeftStyle', 'borderLeftWidth', 'borderRadius', 'borderRight', 'borderRightColor', 'borderRightStyle', 'borderRightWidth', 'borderSpacing', 'borderStyle', 'borderTop', 'borderTopColor', 'borderTopLeftRadius', 'borderTopRightRadius', 'borderTopStyle', 'borderTopWidth', 'borderWidth', 'bottom', 'boxDecorationBreak', 'boxShadow', 'boxSizing', 'captionSide', 'clear', 'clip', 'color', 'columnCount', 'columnFill', 'columnGap', 'columnRule', 'columnRuleColor', 'columnRuleStyle', 'columnRuleWidth', 'columns', 'columnSpan', 'columnWidth', 'content', 'counterIncrement', 'counterReset', 'cursor', 'direction', 'display', 'emptyCells', 'flex', 'flexBasis', 'flexDirection', 'flexFlow', 'flewGrow', 'flexShrink', 'flexWrap', 'cssFloat', 'font', 'fontFamily', 'fontSize', 'fontStyle', 'fontVariant', 'fontSizeAdjust', 'fontStretch', 'hangingPunctuation', 'height', 'hyphens', 'icon', 'imageOrientation', 'justifyContent', 'left', 'letterSpacing', 'lineHeight', 'listStyle', 'listStyleImage', 'listStylePosition', 'listStyleType', 'margin', 'marginBottom', 'marginLeft', 'marginRight', 'marginTop', 'maxHeight', 'maxWidth', 'minHeight', 'minWidth', 'navDown', 'navIndex', 'navLeft', 'navRight', 'navUp', 'opacity', 'order', 'orphans', 'outline', 'outlineColor', 'outlineOffset', 'outlineStyle', 'outlineWidth', 'overflow', 'overflowX', 'overflowY', 'padding', 'paddingBottom', 'paddingLeft', 'paddingRight', 'paddingTop', 'pageBreakAfter', 'pageBreakBefore', 'pageBreakInside', 'perspective', 'perspectiveOrigin', 'position', 'quotes', 'resize', 'right', 'tableLayout', 'tabSize', 'textAlign', 'textAlignLast', 'textDecoration', 'textDecorationColor', 'textDecorationLine', 'textDecorationStyle', 'textIndent', 'textJustify', 'textOverflow', 'textShadow', 'textTransform' ,'top', 'transform', 'transformOrigin', 'transformStyle', 'transition', 'transitionProperty', 'transitionDuration', 'transitionTimingFunction', 'transitionDelay', 'unicodeBidi', 'verticalAlign', 'visibility', 'whiteSpace', 'width', 'wordBreak', 'wordSpacing', 'wordWrap', 'widows', 'zIndex', 
			
			/* Navigator Object */ 'navigator', 'appCodeName', 'appName', 'appVersion', 'cookieEnabled', 'geolocation', 'language', 'onLine', 'platform', 'product', 'userAgent', 'javaEnabled', 'taintEnabled',
			
			/* Screen Object */ 'Screen', 'availHeight', 'availWidth', 'colorDepth', 'height', 'pixelDepth', 'width', 
			
			/* History Object (BROWSER) */ 'length', 'back', 'forward', 'go', 
			
			/* Location Object */ 'hash', 'host', 'hostname', 'href', 'origin', 'pathname', 'port', 'protocol', 'search', 'assign', 'reload', 'replace', 			
			
			/* Window Object */ 'closed', 'defaultStatus', 'document', 'frameElement', 'frames', 'history', 'innerHeight', 'innerWidth', 'length', 'location', 'name', 'navigator', 'opener', 'outerHeight', 'outerWidth', 'pageXOffset', 'pageYOffset', 'parent', 'screen', 'screenLeft', 'screenTop', 'screenX', 'screenY', 'scrollX', 'scrollY', 'self', 'status', 'top', 'alert', 'atob', 'blur', 'btoa', 'clearInterval', 'clearTimeout', 'close', 'confirm', 'createPopup', 'focus', 'moveBy', 'moveTo', 'open', 'print', 'prompt', 'resizeBy', 'resizeTo', 'scroll', 'scrollBy', 'scrollTo', 'setInterval', 'setTimeout', 'stop',
			
			/* Document Object */ 'activeElement', 'addEventListener', 'adoptNode', 'anchors', 'applets', 'baseURI', 'body', 'close', 'cookie', 'createAttribute', 'createComment', 'createDocumentFragment', 'createElement', 'createTextNode', 'doctype', 'documentElement', 'documentMode', 'documentURI', 'domain', 'domConfig', 'embeds', 'forms', 'getElementById', 'getElementsByClassName', 'getElementsByName', 'getElementsByTagName', 'hasFocus', 'head', 'images', 'implementation', 'importNode', 'inputEncoding', 'lastModified', 'links', 'normalize', 'normalizeDocument', 'open', 'querySelector', 'querySelectorAll', 'readyState', 'referrer', 'removeEventListener', 'renameNode', 'scripts', 'strictErrorChecking', 'title', 'URL', 'write', 'writeln', 'attributes', 'hasAttributes', 'nextSibling', 'nodeName', 'nodeType', 'nodeValue', 'ownerDocument', 'ownerElement', 'parentNode', 'previousSibling', 'textContent',
			
			/* RegExp Object */ 'constructor', 'global', 'ignoreCase', 'lastIndex', 'multiline', 'source', 'compile', 'exec', 'test', 'toString',
			
			
			
			/*jQuery*/ 'jQuery', 'History', 'getState', 'add', 'addBack', 'addClass', 'after', 'ajaxComplete', 'ajaxError', 'ajaxSend', 'ajaxStart', 'ajaxStop', 'ajaxSuccess', 'andSelf', 'animate', 'append', 'appendTo', 'attr', 'before', 'bind', 'blur', 'callbacks', 'add', 'disable', 'disabled', 'emtpy', 'fire', 'fired', 'fireWith', 'has', 'lock', 'locked', 'remove', 'change', 'children', 'clearQueue', 'click', 'clone', 'closest', 'contents', 'context', 'css', 'data', 'dblclick', 'deferred', 'always', 'done', 'fail', 'isRejected', 'isResolved', 'notify', 'notifyWith', 'pipe', 'progress', 'promise', 'reject', 'rejectWith', 'resolve', 'resolveWith', 'state', 'then', 'delay', 'delegate', 'dequeue', 'detach', 'die', 'each', 'empty', 'end', 'eq', 'error', 'event', 'currentTarget', 'data', 'delegateTarget', 'isDefaultPrevented', 'isImmediatePropagationStopped', 'isPropagationStopped', 'metaKey', 'namespace', 'pageX', 'pageY', 'preventDefault', 'relatedTarget', 'result', 'stopImmediatePropagation', 'stopPropagation', 'target', 'timestamp', 'type', 'which', 'fadeIn', 'fadeOut', 'fadeTo', 'fadeToggle', 'filter', 'find', 'finish', 'first', 'focus','focusin', 'focusout', 'get', 'has', 'hasClass', 'height', 'hide', 'hover', 'html', 'index', 'innerHeight', 'outerHeight', 'innerWidth', 'outerWidth', 'insertAfter', 'insertBefore', 'is', 'jQuery', 'ajax', 'ajaxPrefilter', 'ajaxSetup', 'ajaxTransport', 'boxModel', 'browser', 'Callbacks', 'contains', 'cssHooks', 'cssNumber', 'data', 'Deferred', 'dequeue', 'each', 'error', 'extend', 'fn', 'interval', 'off', 'get', 'getJSON', 'getScript', 'globalEval', 'grep', 'hasData', 'holdReady', 'inArray', 'isArray', 'isEmptyObject', 'isFunction', 'isNumeric', 'isPlainObject', 'isWindow', 'isXMLDoc', 'makeArray', 'map', 'merge', 'noConflict', 'noop', 'now', 'param', 'parseHTML', 'parseJSON', 'parseXML', 'post', 'proxy', 'queue', 'removeData', 'sub', 'support', 'trim', 'type', 'unique', 'when', 'keydown', 'keypress', 'keyup', 'last', 'length', 'live', 'load', 'map', 'mousedown', 'mouseenter', 'mouseleave', 'mousemove', 'mouseout', 'mouseover', 'mouseup', 'next', 'nextAll', 'nextUntil', 'not', 'off', 'offset', 'offsetParent', 'on', 'one', 'parent', 'parents', 'parentsUntil', 'position', 'prepend', 'prependTo', 'prev', 'prevAll', 'prevUntil', 'promise', 'prop', 'pushStack', 'queueu', 'ready', 'remove', 'removeAttr', 'removeClass', 'removeData', 'removeProp', 'replaceAll', 'replaceWith', 'resize', 'scroll', 'scrollLeft', 'scrollTop', 'select', '.serialize', 'serializeArray', 'show', 'siblings', 'size', 'slice', 'slideDown', 'slideToggle', 'slideUp', 'stop', 'submit', 'text', 'toArray', 'toggle', 'toggleClass', 'trigger', 'triggerHandler', 'unbind', 'undelegate', 'unload', 'unwrap', 'val', 'width', 'wrap', 'wrapAll', 'wrapInner', 'Tween', /*'tween',*/ 'init', 'cur', 'run', 'propHooks', '_default', 'get', 'set', 'scrollTop', 'scrollLeft', 'easing', 'linear', 'swing', 'fx', 'step', 'fn', 'noop', 'isPlainObject', 'isReady', 'expando', 'error', 'isWindow', 'isEmptyObject', 'type', 'globalEval', 'camelCase', 'nodeName', 'each', 'trim', 'makeArray', 'inArray', 'merge', 'grep', 'map', 'guid', 'proxy', 'now', 'support', 'Sizzle', 'isXML', 'setDocument', 'matches', 'matchesSelector', 'contains', 'attr', 'uniqueSort', 'getText', 'selectors', 'tokenize', 'done', 'duration', 'ajax', 'url', 'settings', 'accepts', 'async', 'beforeSend', 'cache', 'complete', 'contents', 'contentType', 'context', 'converters', 'crossDomain', 'data', 'dataFilter', 'dataType', 'error', 'global', 'headers', 'ifModified', 'isLocal', 'jsonp', 'jsonpCallback', 'method', 'mimeType', 'password', 'processData', 'scriptCharset', 'statusCode', 'success', 'timeout', 'traditional', 'type', 'url', 'username', 'xhr', 'xhrFields', 'jqXHR', 'done', 'fail', 'always', 'then', 
			
			/* colors-list (saColorGradients-1.0.0.source.js) */ 'AliceBlue', 'AntiqueWhite','Aqua', 'Aquamarine', 'Azure', 'Beige', 'Bisque', 'Black', 'BlanchedAlmond', 'Blue', 'BlueViolet', 'Brown', 'BurlyWood', 'CadetBlue', 'Chartreuse', 'Chocolate', 'Coral', 'CornflowerBlue', 'Cornsilk', 'Crimson', 'Cyan', 'DarkBlue', 'DarkCyan', 'DarkGoldenRod', 'DarkGray', 'DarkGreen', 'DarkKhaki', 'DarkMagenta', 'DarkOliveGreen','DarkOrange', 'DarkOrchid', 'DarkRed', 'DarkSalmon', 'DarkSeaGreen', 'DarkSlateBlue', 'DarkSlateGray', 'DarkTurquoise', 'DarkViolet', 'DeepPink', 'DeepSkyBlue', 'DimGray', 'DodgerBlue', 'FireBrick', 'FloralWhite', 'ForestGreen', 'Fuchsia', 'Gainsboro', 'GhostWhite', 'Gold', 'GoldenRod', 'Gray', 'Green', 'GreenYellow', 'HoneyDew', 'HotPink', 'IndianRed', 'Indigo', 'Ivory', 'Khaki', 'Lavender', 'LavenderBlush', 'LawnGreen', 'LemonChiffon', 'LightBlue', 'LightCoral', 'LightCyan', 'LightGoldenRodYellow', 'LightGrey', 'LightGreen', 'LightPink', 'LightSalmon', 'LightSeaGreen', 'LightSkyBlue', 'LightSlateGray', 'LightSteelBlue', 'LightYellow', 'Lime', 'LimeGreen', 'Linen', 'Magenta', 'Maroon', 'MediumAquaMarine', 'MediumBlue', 'MediumOrchid', 'MediumPurple', 'MediumSeaGreen', 'MediumSlateBlue', 'MediumSpringGreen', 'MediumTurquoise', 'MediumVioletRed', 'MidnightBlue', 'MintCream', 'MistyRose', 'Moccasin', 'NavajoWhite', 'Navy', 'OldLace', 'Olive', 'OliveDrab', 'Orange', 'OrangeRed', 'Orchid', 'PaleGoldenRod', 'PaleGreen', 'PaleTurquoise', 'PaleVioletRed', 'PapayaWhip', 'PeachPuff', 'Peru', 'Pink', 'Plum', 'PowderBlue', 'Purple', 'Red', 'RosyBrown', 'RoyalBlue', 'SaddleBrown', 'Salmon', 'SandyBrown', 'SeaGreen', 'SeaShell', 'Sienna', 'Silver', 'SkyBlue', 'SlateBlue', 'SlateGray', 'Snow', 'SpringGreen', 'SteelBlue', 'Tan', 'Teal', 'Thistle', 'Tomato', 'Turquoise', 'Violet', 'Wheat', 'White', 'WhiteSmoke', 'Yellow', 'YellowGreen',
			
			/* seductiveapps */ 
			'sa', 'ga', 
				'dismissCookieWarning', 'hideSiteStatus', 

			'site', 'code', 'startApp', 'pushState', 'reboot', 'pushForm', 

			
			'siteCode', 
				'skipAds',

				'globals', 'urls', 'os', 'subURL', 'serverIsForDevelopment', 'visitorIsDeveloper', 'db', 'pageSettings', 'iosOntouchmoveBody', 'initialized', 
				'transformLinks', 
				'selectMusic',
				'setVisible', 
				'setDesiredDesktopConfiguration', 'toggleVisible', 'all', 'contentTreeview', 'musicComments', 'musicAndMusicSearch', 'background', 'comments', 'content', 'contentMusicAndMusicSearch', 'contentMusicComments', 'contentComments', 'ads', 'adsComments', 'adsMusicComments', 'adsMusicAndMusicSearch', 'showStatusbar', 
				'onPage', 
				/*ajax request parameters*/ 'urlToCURL', 
				
			'apps', 
				'loaded', 
					'cardgame_tarot', 'globals', 'rootURL', 'request_uri', 'url', 'settings', 'current', 'reading', 'getURLparameters', 'nested', 'rootURL', 'getURL', 
				'loadedIn', 'contentLoaded', 

				'search_youtube', '---default---', '---all---', 
						
				
				'serviceLog', 'entries', 'makeNew', 
				
			
				'apps',
					'search', 'youtube', 'thumbView', 'videoIDs', 
					'tubeplayer', 'defaults', 'afterReady', 'initialVideo', 'autoPlay', 'showControls', 'showRelated', 'annotations', 'showinfo', 'modestbranding', 'loop', 'onPlayerPlaying', 'onYouTubePlayerReady', 

				'backgrounds', 'next', 'groups', 'guests', 'favorites', 'onlyVideoHD', 'onlyCGI', 'onlyLandscape', 'onlyPortrait', 'onlyTiled', 'calculate', 'stages', 
					
				
				'bg', 'backgrounds', 'next_do_populate', 'next', 'css', 'el_id', 'toUse', 'order', 'layers', 'selectionEngine', 'blank', 'url', 'file', 'random', 'chances', 'extraSearchterms', 'chancesExtraSearchterms', 'goto', 'orderCurrentIdx', 'callback', 'fadeTime', 'elJqueryID', 
				
				'settings', 'searchterms',
				'tools', 'db', 
				'desktop', 'animating', 'systemDialogs', 'hide', 'show', 
				'sl', 'current', 'file',
				'acs', 'runAnimations', 
					
				'jsonViewer', 
				
				
				'seductiveapps_appContent', 'appCode', 'app', 'appContentCode', 'seductiveapps_appCode_jsonViewer', 
				
				'options', 'ver_sliderbar_position', 
				
					
				/* for JSON validator : */ 'statusbar', 'update', 'globals', 'defaultOpacity', 
				
				
				'm', 'misc', 
					'browserWidth', 'browserHeight', 
					'fireAppEvent', 'fireAppEvent_do', 'divName', 'eventName', 
					'initBootScreen', 'initApps', 'initDBs', 'clearCookies', 'goFullscreen', 
					'userDevice', 'canDo', 'isPhone', 'isWindows', 'windowsVersion', 'isChrome', 'isFirefox', 'isIE10', 'isIE11', 'isIE11win7', 'isIE11win8', 'isIE11win81', 'isIE', 'ieOlderVersion', 'isCompatibleBrowser', 'isInvertedMousewheel', 'isNonMouse', 'canDo_webgl', 'canDo_canvas', 
					'addEventListener', 'removeEventListener', 'hookEvent', 'hookScrollwheel', 
					'walkObject', 'val', 'progressbarHTMLid', 'callbackScan', 'callbackProcessUpdate', 'callbackKey', 'callbackValue', 'callbackProcessDone', 
					'displayError', 'confirm', 'pageOptions', 'traceFunction', 'showAllParents', 'restoreAllParents', 
					'cloneObject', 'cloneObjectAsync', 
					'elapsedMilliseconds', 'secondsToTime', 'secondsToTimeString', 'negotiateOptions', 'sizeHumanReadable', 'size_format', 'number_format', 'trace', 'log', 'stacktrace', 
					'dateForLog', 'padNumber', 
					'waitForCondition', 
					'traceAll', 
					'searchString', 
					'urlEncodeJSON', 'urlDecodeJSON', 
					
				/* vividControls setOptions() */ 'level_0', 'orientation', 'animspeed', 
				
				'cg', 'themes', 'saColorgradientSchemeGreen', 'saColorgradientSchemeGreen2', 'saColorgradientSchemeGreen_leaf', 'saColorgradientSchemeIce', 'saColorgradientSchemeRed', 'saColorgradientSchemeRed2', 'saColorgradientSchemeWhiteToNavy', 'saColorgradientSchemeWhiteToBrown', 'saColorgradientSchemeYellow', 'saColorgradientSchemeYellow_forTrace', 'saColorgradientSchemeYellow', 'saColorgradientSchemeFullRange', 'saColorgradientSchemeFullRange_forTrace', 'saColorgradientSchemeFullRangeWhiteBackground', 'saColorgradientSchemeBlue', 'saColorgradientSchemeBlue_bright', 'saColorgradientScheme_navy', 'saColorgradientScheme_text_001', 'saColorgradientScheme_text_002', 'saColorgradientScheme_text_003', 'saColorgradientScheme_text_004', 'saColorgradientScheme_text_005', 
				
				'resize', 'onresize', 
			
				'json', 'urlEncode', 'decode', 'big', 'small', 'encode',
				'serviceLog', 'sl', 'makeNew', 
				'vcc', 'vividControls', 'setOptions', 'init', 'afterResize', 'afterDesktopResize', 'afterResize', 
				'button', 'vividButton', 'initButton', 
				'menu', 'vividMenu', 'initMenu', 'populateMenuWithJSON', 'merge', 'csc', 'hideNode', 'showNode', 
				'sp', 'vividScrollpane', 'containerSizeChanged',
				'vividText', 'vt', 'globals', 'animationTypes', 'theme', 'animationType', 'animationSpeed', 'initElement', 'el', 
				
				'photoAlbum', 'pa', 'init', 'albumsContents', 
					'div', 'album', 'topLeftImageIndexInAlbumsContents', 'rootURL', 'db', 'lowres', 
					'image', 'imageIndexInAlbumsContents', 
					'setImageButtonLinks', 'setImageDimensions', 
					//'sizeRestraint', 'min', 'max', 'x', 'y', 
				'jsonViewer', 'hms', 'hm', 
				
				
				/* treeDB.all.json */ 'seductiveapps', 'backgrounds', 'files', 'com', 'ui', 'photoAlbum', 'albumsNestedList', 'site', 'music', 'apps', 'cardgame_tarot', 'contentSettings', 'urls', 'youtube', 'musicMenus', 'default', 'globals', 'perMenu', 'itemsPre', 'itemsPost', 'settings', 
				
				/* photoAlbum.all.json */ 'files', 
				
				/* page_*.tpl.json */ 'divs', 'settingsPerURLmatched', 'perBrowserDimensions', 'paramsfor_sa.s.c.setVisible', 'element', 'visible', 'resize', 
				
				/* ultiCache_FAT.json */ 'keys', 'url', 'get', 'template', 'want', 'post', 'context', 'file', 
				
				/* button vividTheme JSON */ 'baseURL', 'themeType', 'frame', 'width', 'height', 'offsetX', 'offsetY', 'state', 'normal', 'hover', 'selected', 'disabled', 'frameCount', 'animationLoopsBackAndForth', 'animationLoopsForward', 'animationLoopsForwardDelayBetweenSequences', 'fps', 'fpsSlow', 
				
				/* dialog vividTheme JSON */ 'displayOnFirstImageLoaded', 'opacity', 'cssInner', 'jQuerySelector', 'jQueryFilter', 'inIframe', 'jQueryFilter_notSelectorIn', 'cssToExtrapolate', 'frames', 
				
				/* vividScrollpane vividTheme JSON */ 'images', 'ver_slider', 'ver_sliderbar', 'ver_sliderTop', 'ver_sliderBottom', 'hor_slider', 'hor_sliderbar', 'hor_sliderTop', 'hor_sliderBottom', 
				
				/* vividTabs vividTheme JSON */ 'menuItemTheme', 'contentBackgroundImage', 'menuBackgroundImage', 
				
				/* jsonViewer */ 'htmlID', 'fastInit', 'hmd', 'date', 'time', 'title', 'options', 'hmdOrigin', 
				
				'apps', 'settings', 
				
				/* apps : search */ 'search', 'appContentHTMLelement', 'appContentCode', 'search_youtube', 
				/* apps : tarot game */ 'ts', 'getNewReading', 'loadAds', 'menuHTMLid', 'preprocess', 'menu', 
				
				/* apps : JSON validator */ 'seductiveapps_appCode_jsonViewer', 'tools', 'validateJSON', 'hms', 'ui', 'expandAll', 'expand', 
				
				/* jquery history */ 'History', 'Adapter', 'bind', 'stateChange', 'pushState', 'originalEvent', 'JSON', 'sessionStorage', 'setItem', 'removeItem', 
					
				
				'imageLoaded', 'Origin', 'changeStateKey', 

			//, 'calls'
			//, 'scope'
			//, 'xml'
			//,'Keys',
			
			/* tinyMCE 3.x */ 'tinyMCE', 'mode', 'theme', 'plugins', 'skin', 'init_instance_callback', 'theme_advanced_buttons1', 'theme_advanced_buttons2', 'theme_advanced_buttons3', 'theme_advanced_buttons4', 'theme_advanced_toolbar_location', 'theme_advanced_toolbar_align', 'font_size_style_values', 'keep_style', 'content_css', 'editor_css', 'inline_styles', 'theme_advanced_resize_horizontal', 'theme_advanced_resizing', 'apply_source_formatting', 'convert_fonts_to_spans', 'get', 'getContent', 
			
			/* Canvas */ 'fillStyle', 'strokeStyle', 'shadowColor', 'shadowBlur', 'shadowOffsetX', 'shadowOffsetY', 'createLinearGradient', 'createPattern', 'createRadialGradient', 'addColorStop', 'lineCap', 'lineJoin', 'lineWidth', 'miterLimit', 'rect', 'fillRect', 'strokeRect', 'clearRect', 'fill', 'stroke', 'beginPath', 'moveTo', 'closePath', 'lineTo', 'clip', 'quadraticCurveTo', 'bezierCurveTo', 'arc', 'arcTo', 'isPointInPath', 'scale', 'rotate', 'translate', 'transform', 'setTransform', 'font', 'textAlign', 'textBaseline', 'fillText', 'strokeText', 'measureText', 'drawImage', 'width', 'height', 'data', 'createImageData', 'getImageData', 'putImageData', 'globalAlpha', 'globalCompositeOperation', 'save', 'restore', 'createEvent', 'getContext', 'toDataURL'
		);
	
		if (array_search ($token, $ignoreList)===false) {
			global $randomStringLength;
			$randomToken = randomStringJSO ($randomStringLength);
			while (
				array_search($randomToken, $tokens)!==false
				|| strpos ($code, $randomToken)!==false
			) {
				$randomToken = randomStringJSO ($randomStringLength);
			};
			$tokensObfuscated[$idx] = $randomToken;
		}
	};

/*	
	foreach ($tokensObfuscated as $i => $t) {
		echo 'after_'.$i.' : '.$t.'<br/>';
	}
*/
}


/**
 * Strip strings and comments from code
 */
function phpJSO_strip_strings_and_comments (&$str, &$strings, $comment_delim)
{
	$num_strings = count($strings);
	$in_string = $last_quote_pos = $in_comment = $in_regex = false;
	$removed = 0;
	$invalid = array();

	
	// Find all occurances of comments and quotes. Then loop through them and parse.
	$quotes_and_comments = phpJSO_sort_occurances($str, array('/', '//', '/*', '*/', '"', "'"));

	// Loop through occurances of quotes and comments
	foreach ($quotes_and_comments as $location => $token)
	{
		// Parse strings
		if ($in_string !== false)
		{
			if ($token == $in_string)
			{
				// First, we'll pull out the string and save it, and replace it with a number.
				$replacement = '`' . $num_strings . '`';
				$string_start_index = $last_quote_pos - $removed;
				$string_length = ($location - $last_quote_pos) + 1;
				
				if ($string_length>0) {
					$strings[$num_strings] = substr($str, $string_start_index, $string_length);
					++$num_strings;

					// Remove the string completely
					$str = substr_replace($str, $replacement, $string_start_index, $string_length);
				
					// Clean up time...
					$removed += $string_length - strlen($replacement);
					$in_string = $last_quote_pos = false;
				}
				
			}
		}
		// Parse multi-line comments
		else if ($in_comment !== false)
		{
			// If it's the end of a comment, replace it with a single space
			// We replace it with a space in case a comment is between two tokens: test/**/test
			if ($token == '*/')
			{
				$comment_start_index = $in_comment - $removed;
				$comment_length = ($location - $in_comment) + 2;
				$str = substr_replace($str, ' ', $comment_start_index, $comment_length);
				$removed += $comment_length - 1;
				$in_comment = false;
			}
		}
		// Parse regex
		else if ($in_regex !== false)
		{
			// Should be end of the regex, unless it's escaped
			// If it is the end... don't do anything except stop parsing
			// We just don't want strings inside of regex to be removed,
			// like: /["']*/ -- VERY bad when mistaken as a string
			if ($token == '/')
			{
				$string_start_index = $in_regex - $removed;
				$string_length = ($location - $in_regex) + 1;
				$in_regex = false;
				
				



/*
				$replacement = '`' . $num_strings . '`';
				$string_start_index = $last_quote_pos - $removed;
				$string_length = ($location - $last_quote_pos) + 1;
				
				if ($string_length>0) {
					$strings[$num_strings] = substr($str, $string_start_index, $string_length);
					++$num_strings;

					// Remove the string completely
					$str = substr_replace($str, $replacement, $string_start_index, $string_length);
				
					// Clean up time...
					$removed += $string_length - strlen($replacement);
					$in_string = $last_quote_pos = false;
				}
				
				
*/

				
				
				
				
			}
		}
		else
		{
			// Make sure string hasn't been extracted by another operation...
			if (substr($str, $location - $removed, strlen($token)) != $token)
			{
				continue;
			}
			
			// This string shouldn't have been escaped...
			if ($location && $str[$location - $removed - 1] == '\\')
			{
				continue;
			}
			
			// See what this token is ...
			// Start of multi-line comment?
			if ($token == '/*')
			{
				$in_comment = $location;
			}
			// Start of a string?
			else if ($token == '"' || $token == "'")
			{
				$in_string = $token;
				$last_quote_pos = $location;
			}
			// A single-line comment?
			else if ($token == '//')
			{
				$comment_start_position = $location - $removed;
				$newline_pos = strpos($str, "\n", $comment_start_position);
				$comment_length = ($newline_pos !== false ? $newline_pos - $comment_start_position : $comment_start_position);
				$str = substr_replace($str, '', $comment_start_position, $comment_length);
				$removed += $comment_length;
			}
			// Start of a regex expression?
			// Note that the second part of this conditional fixes a bug: if there
			// is a regex sequence followed by a comment of the EXACT SAME length,
			// it will try to parse the regex sequence a second time...
			else if ($token == '/' && (!isset($quotes_and_comments[$location - 1]) || ($quotes_and_comments[$location - 1] != '//' && $quotes_and_comments[$location - 1] != '*/')))
			{
				// Only start a regex sequence if there was NOT
				// an alphanumeric sequence before.
				// var regex = /pattern/
				// string.match(/pattern/)
				if (preg_match('#[(=]#', $str[$location - $removed - 1]))
				{
					$in_regex = $location;
				}
			}
		}
	}

	// get rid of regular expressions (shove into $strings)
	$matches = array();
	$r = preg_match_all ('#/.*?/[a-zA-Z]*?[\),\.;]#', $str, $matches);
	foreach ($matches[0] as $idx=>$m) {
		if ($m!=='//.') {
			echo $idx.' : '.htmlentities($m).'<br/>';
			$replacement = '`' . $num_strings . '`';
			$strings[$num_strings] = $m;
			$str = str_replace ($m, $replacement, $str);
			++$num_strings;
		}
	}
	//die();
	
	// get rid of any "about" sub-objects
	$matches = array();
	$r = preg_match_all ('#about\s:\s{.*?},#', $str, $matches);
	foreach ($matches[0] as $idx=>$m) {
		if ($m!=='//.') {
			echo 'REMOVED_ENTIRELY : '.$idx.' : '.htmlentities($m).'<br/>';
			$replacement = '`' . $num_strings . '`';
			//$strings[$num_strings] = $m;
			$str = str_replace ($m, '', $str);
			++$num_strings;
		}
	}
	
	// get rid of "debugger;" statements
	// $str = str_replace ('debugger;', '', $str); // DONT!!! some IF statements are followed ONLY by "debugger;"
}

/**
 * Strips junk from code
 */
function phpJSO_strip_junk (&$str, $whitespace_only = false)
{
	// Remove unneeded spaces and semicolons
	$find = array
	(
		'/([^a-zA-Z0-9_$]|^)\s+([^a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
		'/([^a-zA-Z0-9_$]|^)\s+([a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
		'/([a-zA-Z0-9_$]|^)\s+([^a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
		'/([^a-zA-Z0-9_$]|^)\s+([^a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
		'/([^a-zA-Z0-9_$]|^)\s+([a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
		'/([a-zA-Z0-9_$]|^)\s+([^a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
		'/[\r\n]/s', // Unneeded newlines
		"/\t+/" // replace tabs with spaces
	);
	// Unneeded semicolons
	if (!$whitespace_only)
	{
		$find[] = '/;(\}|$)/si';
	}
	$replace = array
	(
		'$1$2',
		'$1$2',
		'$1$2',
		'$1$2',
		'$1$2',
		'$1$2',
		'',
		' ',
		'$1',
	);
	$str = preg_replace($find, $replace, $str);
}

/**
 * Collapses code blocks.
 */
function phpJSO_collapse_blocks ($code, &$collapse_count)
{
	
	// The :parenthetical: is replaced dynamically in the loop below.
	// The key values mean this: the first and second values in the array are the indexes
	// of the parenthetical subscripts, and the third value is the replace value
	// for the regex.
	$regex = array
	(
		// When there is one command inside a block, remove brackets
		'#((if|for|while)\(:paren0:\))\{([^;{}]*;)\}#si' => array(3, 0, '$1$5', 5, 0),
		'#((if|for|while)\(:paren0:\))\{([^;{}]*)\}(?!;)#si' => array(3, 0, '$1$5;', 5, 0),
		'#((if|for|while)\(:paren0:\))\{([^;{}]*)\}(?=;)#si' => array(3, 0, '$1$5', 5, 0),
		// Collapse brackets with else and do statements
		'#(do|else)\{([^;{}]*)\}#si' => array(0, 0, '$1 $2;', 2, 0),
		'#(do|else)\{([^;{}]*;)\}#si' => array(0, 0, '$1 $2', 2, 0),
		// Remove brackets when a block is inside a block, EG if(1){if(2){}}
		'#((if|for|while)\(:paren0:\))\{((if|for|while|function [a-zA-Z_$][a-zA-Z0-9_$]*)\(:paren1:\))\{([^{}]*)\}\}(?!else)#si' => array(3, 7, '$1$5{$9}', 0, 0),
		'#((if|for|while)\(:paren0:\))\{((if|for|while|function [a-zA-Z_$][a-zA-Z0-9_$]*)\(:paren1:\))([^{};]*);?\}(?!else)#si' => array(3, 7, '$1$5$9;', 0, 0),
		'#((if|for|while)\(:paren0:\))\{([^;{]*)\{([^{}]*)\};?\}(?!else)#siU' => array(3, 0, '$1$5{$6};$7', 0, 0),
		// Remove brackets when a block is inside a block with no parentheticals, EG else{if(2){}}
		'#(else|do)\{((if|for|while|function [a-zA-Z_$][a-zA-Z0-9_$]*)\(:paren0:\))\{([^{}]*)\}\}#si' => array(4, 0, '$1 $2{$6}', 0, 0),
		'#(else|do)\{((if|for|while|function [a-zA-Z_$][a-zA-Z0-9_$]*)\(:paren0:\))([^{};]*);?\}#si' => array(4, 0, '$1 $2$6;', 0, 0),
		'#(else|do)\{([^;{}]*)\{([^{}]*)\};?\}#si' => array(0, 0, '$1 $2{$3};', 0, 0)
	);

	// Collapse all blocks when possible
	while (1)
	{
		$original_code = $code;

		// Loop through all patterns
		foreach ($regex as $find => $regex_data)
		{
			// Match all occurences of pattern
			$matches = array();
			$find_all = str_replace(':paren0:', '([^{}()]*(\([^{}]*)?)', $find);
			$find_all = str_replace(':paren1:', '([^{}()]*(\([^{}]*)?)', $find_all);
			preg_match_all($find_all, $code, $matches);
			
			// Loop through all matches, and if the number of opening and closing
			// parentheses is even, collapse the block
			for ($i = 0; isset($matches[0][$i]); ++$i)
			{
				// Don't find nested loops in some patterns
				if ($regex_data[3] && preg_match('#^if#si', $matches[$regex_data[3]][$i]))
				{
					continue;
				}
				
				// If loops are immediately followed by "else", don't continue
				if ($regex_data[4] && strtolower($matches[$regex_data[4]][$i]) == 'else')
				{
					continue;
				}
				
				$complete_match = true;
				$find_complete = $find;
				for ($j = 0; $j != 2; ++$j)
				{
					if ($regex_data[$j])
					{
						$parenthetical = &$matches[$regex_data[$j]][$i];
						if (!($parenthetical = phpJSO_is_valid_parenthetical($parenthetical)))
						{
							$complete_match = false;
						}
						$find_complete = str_replace(':paren'.$j.':', '((' . preg_quote($parenthetical) . '))', $find_complete);
					}
				}
				if ($complete_match)
				{
					$code = preg_replace($find_complete, $regex_data[2], $code);
					++$collapse_count;
				}
			}
		}
		break;

		if ($original_code === $code)
		{
			break;
		}
	}
	return $code;
}

/**
 * Collapse math constants in code.
 */
function phpJSO_collapse_math ($code, &$collapsed)
{
	preg_match_all('#(^|[^a-zA-Z0-9_\$])(([()]|([\+\-\/\*\%])?(\-)?(0x[0-9a-fA-F]+|[0-9]+(\.[0-9]+)?))+)([^a-zA-Z0-9_\$]|$)#s', $code, $matches);

	// Loop through all matches
	for ($i = 0; isset($matches[0][$i]); ++$i)
	{
		$match = $matches[2][$i];

		// Make sure it is a valid math block
		if (!($match = phpJSO_is_valid_parenthetical($match)))
		{
			continue;
		}

		// Must end and begin with parentheses or numbers
		if ($match{0} != '(' && !is_numeric($match{0}))
		{
			continue;
		}
		$last_index = strlen($match) - 1;
		if ($match{$last_index} != ')' && !is_numeric($match{strlen($match) - 1}) && !ctype_alnum($match{$last_index}))
		{
			continue;
		}

		// Must be more than just symbols or just numbers
		//if (!preg_match('#[0-9]#', $match) || preg_match('#^[0-9]+$#', $match))
		//{
		//	continue;
		//}
		if (preg_match('#\(\)#', $match))
		{
			continue;
		}

		// Convert hex to dec if the dec is smaller
		preg_match_all('#0x[0-9a-fA-F]+#', $code, $hex_matches);
		foreach ($hex_matches[0] as $hex_match)
		{
			$dec = hexdec($hex_match);
			if (strlen($dec) <= strlen($hex_match))
			{
				$code = str_replace($hex_match, $dec, $code);
				$match = str_replace($hex_match, $dec, $match);
			}
		}

		// Parse it, replace it
		$code = @preg_replace('#'.preg_quote($match).'#e', $match, $code);
		++$collapsed;
	}
	
	return $code;
}

/**
 * Get all the tokens in code and put them in two arrays - one array
 * for just numeric tokens, and another array for all the rest.
 */

function sortByStringLength ($a,$b){
	return strlen($b)-strlen($a);
}
 
function phpJSO_get_tokens ($code, &$numeric_tokens, &$tokens)
{
	//preg_match_all('#(?!\/.*?\/)([\s\.;,\(\)\x5b\x5d\x3d]*)([a-zA-Z0-9\_\$]{2,})([\x3d,\(\)\x5b\x5d\s\.;]*)#s', $code, $match);
	preg_match_all('#([\s\.;,\(\)\x5b\x5d\x3d\x3a]*)([a-zA-Z0-9\_\$]{2,})([\/\x3a\x3d,\(\)\x5b\x5d\s\.;]*)#s', $code, $match);
	//var_dump ($match);
	$matched_tokens = array_values(array_unique($match[2]));
	
	/*
	foreach ($match[2] as $i=>$m) {
		echo 'matched_tokens_'.$i.' : '.htmlentities($m).'<br/>';
	};
	*/
	//die();
	
	
	//phpJSO_count_duplicates($duplicates, $match[2]);
	

	//echo 'phpJSO_get_tokens::$matched_tokens<br/>';
	//var_dump ($matched_tokens);
	//die();
	
	foreach ($matched_tokens as $token)
	{
	
		if (array_search($token,$tokens)===false) {
			if (preg_match('#^([0-9]*|0)$#', $token)===0) {
			//|| preg_match('#^([[:xdigit:]])$#', $token)===0) {
				global $randomStringLength;
				$key = randomStringJSO ($randomStringLength);
				while (
					array_key_exists($key,$tokens)
					|| strpos($code, $key)!==false
				) $key = randomStringJSO ($randomStringLength);
				$tokens[$key] = $token;
			}
/*		
			//var_dump ($token{1});
			// If token is an integer, we do replacements differently
			if (preg_match('#^([1-9][0-9]*|0)$#', $token))
			{
				$numeric_tokens[$token] = 1;
			}
			// We can place token in the array normally (but it's only worth doing
			// a replacement if the token isn't just one character).
			// It's also only worth doing a replacement if the token appears more than once in code.
			else //if (isset($token{1}) && $duplicates[$token] > 1)
			{
				$tokens[] = $token;
			}
		*/
		}
	}
}

/**
 * Merges the two token arrays: numeric tokens and regular tokens.
 * Specifically this function will take all the numeric tokens and
 * POSSIBLY put them in the token array if that's necessary.
 */
function phpJSO_merge_token_arrays (&$tokens, &$numeric_tokens)
{
	// Sort numeric token array
	ksort($numeric_tokens);

	// Loop through all numeric tokens
	$num_tokens = count($tokens);
	foreach ($numeric_tokens as $int=>$void)
	{
		if ($num_tokens < $int)
		{
			// We may not need to consider ANY more numeric tokens, if this
			// one is lower than the number of tokens, since the numeric tokens
			// are sorted already. This can potentially save a lot of time.
			if (strlen(strval($num_tokens)) >= strlen(strval($int)))
			{
				break;
			}
			else
			{
				$tokens[] = $int;
				continue;
			}
		}
		phpJSO_insert_token($tokens, '', $int);
		++$num_tokens;
	}
}

/**
 * Inserts a token into the token array. Shifts all the other tokens
 * and puts it somewhere in the middle, based on token_index.
 */
function phpJSO_insert_token (&$token_array, $token, $token_index)
{
	// Loop through array and shift all indexes up one spot until we reach the
	// index we are inserting at
	$jump = 1;
	$token_index_count = $token_index - 1;
	for ($i = count($token_array) - 1; $i > $token_index_count; --$i)
	{
		if ($token_array[$i] == '')
		{
			++$jump;
			continue;
		}
		$token_array[$i+$jump] = $token_array[$i];
		$jump = 1;
	}
	$token_array[$token_index] = $token;
}

function phpJSO_strip_strings (&$str) {
	$str = preg_replace('#`([0-9]+)`#', '', $str);
}

/**
 * Place stripped strings back into code
 */
function phpJSO_restore_strings (&$str, &$strings)
{
	//var_dump ('t1'); var_dump($str); var_dump ($strings); die();
	//do
	//{
	
	//echo (htmlentities($str).'<br/>');
	//var_dump ($strings);
		$f = function($m) use (&$strings) { 
			//var_dump ($m[1]);
			$r = array (
				1 => $strings[$m[1]],
				2 => $m[1]
			);
			var_dump ($r);
			
			return isset($strings[$m[1]]) ? $strings[$m[1]] : $m[1];
		};
		$str = preg_replace_callback ('#`([0-9]+)`#', $f, $str);
		//var_dump ($str);die();
	//}
	//while (preg_match('#`([0-9]+)`#', $str));
}

/**
 * Count duplicate values in an array
 */
function phpJSO_count_duplicates (&$dupes, $ary)
{
	foreach ($ary as $v)
	{
		//$dupes[$v] = (isset($dupes[$v]) ? $dupes[$v] : 0) + 1;
		if (isset($dupes[$v]))
		{
			++$dupes[$v];
		}
		else
		{
			$dupes[$v] = 1;
		}
	}	
}

/**
 * Replaces tokens in code with the corresponding token index.
 */
//global $tokens_flipped;
function phpJSO_replace_tokens (&$tokens, &$code)
{

	//echo ('phpJSO_replace_tokens() 1:');
	//echo (htmlentities($code));
	//echo ('<br/><br/><br/><br/><br/>');
	//var_dump ($tokens);

	//global $tokens_flipped;
	//$tokens_flipped = array_flip($tokens);
	//unset($tokens_flipped['']);
	//$tokens_flipped_again = array_flip ($tokens_flipped);
	
	/* 1.5MB of Javascript will throw "regexp too large at offset...."
	$find = '#\b('.implode('|', array_flip($tokens_flipped)).')\b#';
	$f = function($m) use ($tokens_flipped) {
		return (isset($tokens_flipped[$m[1]]) ? $tokens_flipped[$m[1]] : $m[1]);
	};
	$code = preg_replace_callback ($find, $f, $code);
	*/
	
	//usort ($tokens_flipped_again, 'sortByStringLength');
	
	foreach ($tokens as $idx=>$tf) {
		/*
			$t = array (
				'$idx' => $idx,
				'$tf' => $tf				
			);
		var_dump ($t);
		 */
			
		if ($tf!='' && preg_match('#\b'.$tf.'\b#', $code)===1) {
		
			$code = preg_replace('#\b'.$tf.'\b#', $idx, $code);//str_replace ($tf, $idx, $code);
			//'#([\s\.;,\(\)\x5b\x5d\x3d]*)([a-zA-Z0-9\_\$]{2,})([\x3d,\(\)\x5b\x5d\s\.;]*)#s'
		}
	}

	/*
	echo ('phpJSO_replace_tokens() 2:');
	echo (htmlentities($code));
	echo ('<br/><br/><br/><br/><br/>');
	*/
}

/**
 * Check whether a parenthetical is valid or not.
 */
function phpJSO_is_valid_parenthetical ($parenthetical)
{
	$open_parentheses = 0;
	
	// Get all parentheses in the string
	$parentheses = phpJSO_sort_occurances($parenthetical, array('(', ')'));

	// Loop through parentheses
	foreach ($parentheses as $index => $parenthesis)
	{
		if ($parenthesis == ')')
		{
			if (!$open_parentheses)
			{
				return ($index ? substr($parenthetical, 0, $index) : false);
			}

			--$open_parentheses;
		}
		else
		{
			++$open_parentheses;
		}
	}

	if ($open_parentheses != 0)
	{
		return false;
	}

	return $parenthetical;
}

/**
 * Finds all occurances of different strings in the first passed string and sorts
 * them by location. Returns array of locations. The key of each array element is the string
 * index (location) where the string was found; the value is the actual string, as seen below.
 *
 * [18] => "
 * [34] => "
 * [56] => /*
 * [100] => '
 */
function phpJSO_sort_occurances (&$haystack, $needles)
{
	$locations = array();
	
	foreach ($needles as $needle)
	{
		$pos = -1;
		//$needle_length = strlen($needle);
		while (($pos = @strpos($haystack, $needle, $pos+1)) !== false)
		{
			// Don't save location if string length is 1, and the needle is escaped
			if ($pos && $haystack[$pos - 1] == '\\' && $needle != '*/')
			{
				continue;
			}

			// Save location of needle
			$locations[$pos] = $needle;
		}
	}
	
	ksort($locations);
	
	return $locations;
}

/**
 * For timing compression
 */
function phpJSO_microtime_float()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}
?>
