<?php
// Copyright (C) 2002-2023 and All Rights Reserved (R) by Rene AJM Veerman <rene.veerman.netherlands@gmail.com>

// This file is part of jsonViewer
//	Written & copyrighted (c) 2010-2013 by Rene AJM Veerman <rene.veerman.netherlands@gmail.com>
//	License: LGPL, free for any type of use
//	Disclaimer: NO WARRANTY EXPRESSED OR IMPLIED. USE ONLY AT YOUR OWN RISK.
//	Download: http://nicerapp.com/jsonViewer/

// ---
//      PUBLIC functions :
function hm (&$var, $title, $options=null, $settings=array('direct'=>'')) {
  jsonViewer_dump ($var, $title, $options, $settings);
}

function hmJSON (&$jsonString, $title, $options=null) {
	$settings = [ 'mem' => '' ];
	global $hmOutput;
	$hmOutput = '';
	jsonViewer_dump ($jsonString, $title, $options, $settings);
	return $hmOutput;
}





global $hmConfig; $hmConfig = null;
global $hmOutput; $hmOutput = '';
// ---
//      PRIVATE:
global $hmTopIdx; $hmTopIdx = 0;
global $jesx_idPrefix;
global $jesx_chunkSize; $jesx_chunkSize = 1 * 1024 * 1024;
global $jesx_cntChunkBytesDone; $jesx_cntChunkBytesDone = 0;
global $jesx_cntChunks; $jesx_cntChunks = 0;

function noErr () {
return false;
}
function jsonViewer_dump ($var, $title, $options=null, $outputSettings=array('direct'=>''), $hmSettings=null, $direct=true) {
//set_error_handler ('noErr');
	if (!$hmSettings) $hmSettings = jsonViewer_config();
	
	global $hmTopIdx;
	$_SESSION['hmTopIdx']++;
	$id = $_SESSION['hmTopIdx'];
	$htmlID = 'scope'.$id;
	
	global $jesx_idPrefix;
	
	global $hmOutput;
	$hmOutput = '';
	global $jesx_cntChunkBytesDone;
	$jesx_cntChunkBytesDone = 1;
	//$hmOutput = '<span style="display:none;">'.json_encode($options).'</span>';
	//json_encode_xxl_do($options, array('mem'=>''), false);
	$options = ($options ? json_encode($options) : null);
	$traceData=debug_backtrace();
	$traceData = jsonViewer_filterTrace_forPHP($traceData, array(
		'filter' => array(
			'badResult' => 'removeCompletely',
			'nonFatalErrorHandler' => 'removeCompletely',
			'jsonViewer_dump' => 'removeCompletely',
			'hm' => 'removeCompletely'
		)
	));
	$trace = array(
		'hmOverrides' => array (
			'themeName' => '--trace--'
		 ),
		 'traceData' => $traceData
	);
	json_encode_xxl_output ('<div id="'.$htmlID.'" class="jsonViewer hm hmPreInit">', $outputSettings);
	json_encode_xxl_output ('<div id="'.$htmlID.'_hmPreInit" class="hmPreInit">', $outputSettings);
	json_encode_xxl_output ('<div id="'.$htmlID.'_titleSpan" class="hmPItitle"><a href="https://nicer.app/">jsonViewer</a> '.$title.'</div> ', $outputSettings);
	if (2==1) {
		$b = strlen(serialize($var)); //i suspect serialize to be slow and prone to memory hogging.
		json_encode_xxl_output ('<div id="'.$htmlID.'_dataSize" class="hmPIdataSize">Receiving '.$b.' bytes, '.filesizeHumanReadable($b).' of data.<br/><br/></div>', $outputSettings);
	} else {
		json_encode_xxl_output ('<div id="'.$htmlID.'_dataSize" class="hmPIdataSize">Receiving data.<br/><br/></div>', $outputSettings);
	}
	json_encode_xxl_output ('<div id="'.$htmlID.'_preInitStatus" class="hm hmPreInitStatus" style="display:none">', $outputSettings);
	json_encode_xxl_output ('<span id="'.$htmlID.'_longMsg" class="hmPIlongMsg"> </span><br/>', $outputSettings);
	json_encode_xxl_output ('<span id="'.$htmlID.'_shortMsg" class="hmPIshortMsg"> </span>', $outputSettings);  
	json_encode_xxl_output ('</div>', $outputSettings);
	$jesx_idPrefix = $htmlID.'_tracedata_';
	$traceJSON = nicerapp_json_prepare($trace);
	json_encode_xxl ($traceJSON, $outputSettings);
	$jesx_idPrefix = $htmlID.'_data_';
	//echo '<pre>'; var_dump ($var); echo '</pre>';
	$var = nicerapp_json_prepare($var);
	json_encode_xxl ($var, $outputSettings);
	json_encode_xxl_output ("\n".'</div>'."\n", $outputSettings);
	json_encode_xxl_output ('</div>', $outputSettings);
	json_encode_xxl_output ('<script type="text/javascript">', $outputSettings);
	json_encode_xxl_output ('var hmData = {', $outputSettings);
	json_encode_xxl_output (' "title" : "'.$title.'", ', $outputSettings);
	json_encode_xxl_output (' "date" : "'.date('r').'",', $outputSettings);
	json_encode_xxl_output (' "time" : "'.getDuration().'",', $outputSettings);
	json_encode_xxl_output (' "options" : '.($options?$options:'null').', ', $outputSettings);
	json_encode_xxl_output (' "trace" : null, ', $outputSettings);
	json_encode_xxl_output (' "id" : "'.$htmlID.'", ', $outputSettings);
	json_encode_xxl_output (' "dataOrigin" : "php"', $outputSettings);
	json_encode_xxl_output ('};'."\n", $outputSettings);
	json_encode_xxl_output ('setTimeout(function(hmData) { na.jsonViewer.processWhenReady (hmData); }, 500, hmData);'."\n", $outputSettings);
	json_encode_xxl_output ('</script>'."\n", $outputSettings);
	if ($outputSettings) return true; else return $hmOutput;
}


function jsonViewer_filterTrace_forPHP (&$t,$s=null) {
// $t = trace data from debug_backtrace()
// $s = settings
		$r = array();
		$t = array_reverse ($t);
		foreach ($t as $k => &$l) {

			$skip = false;
			if (array_key_exists('filter', $s)) {
				foreach ($s['filter'] as $k2=>$v) {
					if ($l['function']==$k2) {
						if ($v=='removeCompletely') { $skip = true; break; };
						if ($v=='removeArg0') { $l['args'][0] = 'jsonViewer: removed to eliminate redundancy.'; };
					}
				}
			}

			if (!$skip) {
				if (array_key_exists('removeFromFilePath', $s) && array_key_exists('file', $l)) {
					$l['file'] = str_replace($s['removeFromFilePath'], '', $l['file']);
				} 
				
				
				$context = array();
				$context['function'] = $l['function'];
				if (array_key_exists('file',$l)) $context['file'] = $l['file'];
				if (array_key_exists('line',$l)) $context['line'] = $l['line'];
				$id = json_encode($context);
				
				if (array_key_exists('args',$l)) $r[$id] = $l['args'];
			}
		}

return $r;
		
    return array_merge(array(
			'hmOverrides'=>array(
				'themeName'=>'--trace--'
			)),$r
		);
}

function nicerapp_json_prepare ($v) {
  $r = nicerapp_json_prepare_forPHP ($v);
  return $r;
}

function nicerapp_json_prepare_forPHP ($v, $level=0) {
  if (is_array($v)) {
    foreach ($v as $k=>$w) {
		if ($k!==0 && ($k=='' || is_null($k))) {
			unset ($v[$k]);
			$k='__nullKey__';
		};
		//var_dump (array('$k'=>$k,'$level'=>$level));
		$v[$k] = nicerapp_json_prepare_forPHP ($w, $level+1);
    }
  } elseif (is_object($v)) {
		$v = '[php object]';
  } elseif (is_resource($v)) {
    $v = '[php resource '.get_resource_type($v).' '.$v.']';
  }
  return $v;
}

function jsonViewer_config ($overrides=null) {
    global $hmConfig;
    if (!$hmConfig) {
		$hmConfig = jsonViewer_config_authorsDefaults();
    }
    if (is_array($overrides)) {
		$hmConfig = negotiateOptions(
		  $hmConfig,
		  $overrides
		);
    }
    if (!array_key_exists('developerVisitors', $hmConfig)) {
		$hmConfig['developerVisitors'] = array (
		// ONLY if your webbrowser IP ($_SERVER['REMOTE_ADDR']) is listed here, 
		// is the browser allowed the use of random-array generator functions (index.php?fresh=true)

		  '192.168.1.33' => array(), // array() === for future extension, overrides of settings per visitor ip.
		  '82.161.37.94' => array()
		);
	}
	if (!array_key_exists('debug', $hmConfig)) {
		$hmConfig['debug'] = true;
	}	
    return $hmConfig;
}

function jsonViewer_config_authorsDefaults () {
	$hmConfig = array (
	'debug' => true, //use FALSE to use the minimized version of the script
		'developerVisitors' => array (
		// debug === true for any developer visitors.
		  '192.168.1.33' => array(), // array() === for future extension, overrides of settings per visitor ip.
		  '82.161.37.94' => array()
		)
	);

    $hmConfig['version'] = '1.5.5';
    $hmConfig['releaseDate'] = '2012 December 19, 08:08 CET';
		//'jv.php'.': '.date('r',filectime (HD_ROOT.'code/libraries_rv/jsonViewer-1.3.3/jv.php')).', '.
		//'jv.source.js'.': '.date('r',filectime (HD_ROOT.'code/libraries_rv/jsonViewer-1.3.3/jv.source.js'));
	global $naWebOS;
    $hmConfig['baseDir'] = $naWebOS->codePath.'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/ui/jsonViewer';
    $hmConfig['baseURL'] = 'https://'.$naWebOS->domainFolder.'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/ui/jsonViewer';
    
    return $hmConfig;

};

function jsonViewer_visitorIsDeveloper() {
  $cfg = jsonViewer_config();
  foreach ($cfg['developerVisitors'] as $ip=>$ipsettings) {
    if ($_SERVER['REMOTE_ADDR']==$ip) {
      return true;
    }
  }
  return false;
}





// ----------
// JSON output functions
function json_encode_xxl (&$a, $outputSettings=array('direct'=>''), $baseID=null, $extraEscaping=false, $isKey=false) {
	global $jesx_cntChunkBytesDone;
	$jesx_cntChunkBytesDone = 0;
	global $jesx_cntChunks;
	$jesx_cntChunks = 0;
	global $jesx_idPrefix;
	if (is_string($baseID)) $jesx_idPrefix = $baseID;
	json_encode_xxl_do ($a, $outputSettings, $extraEscaping, $isKey);
	json_encode_xxl_seperatorEnd ($outputSettings);
}

function json_encode_xxl_output ($str, $settings) {
	if (array_key_exists('file', $settings)) {
		fwrite ($settings['file'], $str);
	} 
	if (array_key_exists('direct', $settings)) {
		echo $str; 
	} 
	if (array_key_exists('mem', $settings)) {
		global $hmOutput;
		$hmOutput.=$str;
	} 
}

function json_encode_xxl_seperatorStart ($outputSettings) {
	if (json_encode_xxl_mustDisplaySeperator($outputSettings)) {
		global $jesx_cntChunks;
		global $jesx_idPrefix;
	 	json_encode_xxl_output ('<div id="'.$jesx_idPrefix.$jesx_cntChunks.'" style="display:none"><!-- ', $outputSettings);
	}
}

function json_encode_xxl_seperatorEnd ($outputSettings) {
  if (json_encode_xxl_mustDisplaySeperator($outputSettings)) json_encode_xxl_output (' --></div>'."\n", $outputSettings);
}

function json_encode_xxl_mustDisplaySeperator($outputSettings) {
	$r = !array_key_exists('excludeSeperators', $outputSettings);
	return $r;
}

function json_encode_xxl_do (&$a, $outputSettings=array('direct'=>''), $extraEscaping=false, $isKey=false) {
	global $jesx_cntChunkBytesDone;
	global $jesx_chunkSize;
	global $jesx_cntChunks;
	
	if ($jesx_cntChunkBytesDone > $jesx_chunkSize) {
		json_encode_xxl_seperatorEnd($outputSettings);
		$jesx_cntChunkBytesDone = 0;
		$jesx_cntChunks++;
	}
	if ($jesx_cntChunkBytesDone==0) {
		json_encode_xxl_seperatorStart($outputSettings);
	}
	
	if (is_null($a) || is_bool($a)) {
		if (is_null($a)) $r = 'null';
		if ($a===false) $r = 'false';
		if ($a===true) $r = 'true';
		if ($isKey) {
			if ($extraEscaping) {
				$r = '\"'.$r.'\"';
			} else {
				$r = '"'.$r.'"';
			}
		} 
		json_encode_xxl_output ($r, $outputSettings);
		$jesx_cntChunkBytesDone+= strlen($r);
	} else if (is_int($a) || is_float($a)) {
		if ($isKey) {
			if ($extraEscaping) {
				json_encode_xxl_output ('\"'.$a.'\"', $outputSettings);
			} else {
				json_encode_xxl_output ('"'.$a.'"', $outputSettings);
			}
		} else {
			json_encode_xxl_output (''.$a, $outputSettings);
		}
		$jesx_cntChunkBytesDone += strlen($a);
	} else if (is_string($a)) {
		$b = '';
		if ($a=='') {
			//if ($extraEscaping) echo '\"\"'; else echo '""';
		} else {
			$esc = '\\';
			$skip = 0;
			for ($i=0; $i<strlen($a); $i++) {
				if ($skip>0) {
					$i += $skip;
					if ($i>=strlen($a)) break;
					$skip = 0;
				}
				$c = substr($a,$i,3);
				if (
					$c=='\\u' ||
					$c=='\\\\'
				) {
				//skip!
				} elseif ($c=='-->') {
					$c = '--|>'; //break HTML comment endings, coz it'll break the js decoding
					$b.= $c;
					$skip = 2;
				} else {
					$c = substr($c, 0, 1);
					if (
						$c == ' ' || 
						$c == '"' ||
						$c == "'" ||
						$c == "\t" ||
						$c == "\n" || //TODO try
						$c == "\r" ||
						$c == ')' || 
						$c == '(' || 
						( $c>='a' && $c<='z') ||
						( $c>='A' && $c<='Z') ||
						( $c>='0' && $c<='9') ||
						$c == '.' || 
						$c == '-' || 
						$c == '_' || 
						$c == '%' || 
						$c == '!' || 
						$c == '#' || 
						$c == '$' || 
						$c == '%' || 
						$c == '&' || 
						$c == '^' || 
						$c == ',' || 
						$c == ':' || 
						$c == ';' || 
						$c == '=' || 
						$c == '?' || 
						$c == '@' || 
						$c == '`' || 
						$c == '~' || 
						$c == '|' || 
						$c == '>' ||
						$c == '<' ||
						$c == '}' ||
						$c == '{' ||
						$c == ']' ||
						$c == '[' ||
						$c == '/' ||
						$c == '+' ||
						$c == '\\' 
					) {
						//if ($c=="'") $c = '\\\'';
						if ($c=='/') $c = '\\/';
						elseif ($c=='\\') $c = '\\\\';
						elseif ($c=='"') $c = '\\"';//$c= '~`';
						elseif ($c=="\n") $c = '\\n';
						elseif ($c=="\r") $c = '\\r';
						elseif ($c=="\t") $c = '\\t';
						} else {
						$c = dechex(ord($c));
						if (strlen($c)==1) $c= '000'.$c;
						elseif (strlen($c)==2) $c= '00'.$c;
						elseif (strlen($c)==3) $c= '0'.$c;
						$c = $esc.'u'.$c;
						}
						$b.=$c;
					}
				}
			}	
			if ($extraEscaping) {
				//echo '\u0022';
				json_encode_xxl_output ('\"', $outputSettings);
				json_encode_xxl_output ($b, $outputSettings);
				//echo '\u0022';
				json_encode_xxl_output ('\"', $outputSettings);
				$jesx_cntChunkBytesDone += 4 + strlen($b);
			} else {
				json_encode_xxl_output ('"'.$b.'"', $outputSettings);
				$jesx_cntChunkBytesDone += 2 + strlen($b);
			}
			return true;
		} elseif (is_array($a)) {
			$isList = true;
			$isListStartingAt1 = false;
			for ($i=0, reset($a); $i<count($a); $i++, next($a)) {
				if (key($a) !== $i) { $isList = false; break; }
			}
			if (!$isList) {
				//allow for numeric arrays starting at 1
				$isList = true;
				$isListStartingAt1 = true;
				for ($i=1, reset($a); $i<=count($a); $i++, next($a))
					if (key($a) !== $i) { $isList = false; break; }
			}
			if ($isList) {
				json_encode_xxl_output ('[', $outputSettings);
				$jesx_cntChunkBytesDone += 1;
				if ($isListStartingAt1) {
                    if (json_encode_xxl_mustDisplaySeperator($outputSettings)) {
                        if ($extraEscaping) {
                            json_encode_xxl_output ('\"hmDeleteMe\",', $outputSettings); //so the receiving end starts at 1 too
                            $jesx_cntChunkBytesDone += 15;
                        } else {
                            json_encode_xxl_output ('"hmDeleteMe",', $outputSettings); //so the receiving end starts at 1 too
                            $jesx_cntChunkBytesDone += 13;
                        }
					}
				}
				$first = true;
				foreach ($a as $v) {
					if ($first) { $first = false; } else { json_encode_xxl_output (',', $outputSettings);  $jesx_cntChunkBytesDone += 1; }
					json_encode_xxl_do ($v,$outputSettings,$extraEscaping);
				}
				json_encode_xxl_output (']', $outputSettings);
				$jesx_cntChunkBytesDone += 1;
			} else {
				json_encode_xxl_output ('{', $outputSettings);
				$jesx_cntChunkBytesDone += 1;
				$first = true;
				foreach ($a as $k=>$v) {
					if ($k=='hmOverrides') {
						if (is_array($v) && array_key_exists('jsonMe', $v)) {
							if ($v['jsonMe'] == true) $extraEscaping = true;
						}
					}
					if ($first) { $first = false; } else { json_encode_xxl_output (',', $outputSettings); }
					json_encode_xxl_do ($k,$outputSettings,$extraEscaping, true);
					json_encode_xxl_output (':', $outputSettings);
					$jesx_cntChunkBytesDone += 1;
					json_encode_xxl_do ($v,$outputSettings,$extraEscaping);
				}
				json_encode_xxl_output  ('}', $outputSettings);
				$jesx_cntChunkBytesDone += 1;
			}
		}
	return true;
}

// ---
//      self-test functions:

function jsonViewer_selfTest ($p, $q, $options=null, $outputSettings=array('direct'=>'')) {
  set_time_limit(0);
  ob_start();

// defaults for parameters;
  $mem = 18; // memory limit in Mb, 10 minimum
  $grace = 4; // how much Mb to stay under the memory limit
  $duration = -1; // max execution time
  $keys = 999999999; // i'd rather specify how much memory to use.
  $deep = 10;
  $addStrangeDataTypes = !array_key_exists('nosdt', $_GET);
  if ($addStrangeDataTypes) $grace+=8;
  $themeOverride = true;

// possibly override params from url-commandline
  if (array_key_exists('mem', $_GET) && is_numeric($_GET['mem'])) {
    $mem = (int)$_GET['mem'] * 2;
	$grace += (int)$_GET['mem'];
  }
  if (array_key_exists('grace', $_GET) && is_numeric($_GET['grace'])) {
    $grace = (int)$_GET['grace'];
  }
  if (array_key_exists('duration', $_GET) && is_numeric($_GET['duration'])) {
    $duration = (int)$_GET['duration'];
  }
  if (array_key_exists('keys', $_GET) && is_numeric($_GET['keys'])) {
    $keys = (int)$_GET['keys'];
  }
  if (array_key_exists('deep', $_GET) && is_numeric($_GET['deep'])) {
    $deep = (int)$_GET['deep'];
  }
  if (array_key_exists('themeOverride', $_GET) ) {
    $themeOverride = $_GET['themeOverride'];
		if ($themeOverride=='false') $themeOverride=false;
  }
  $newMem = ini_set('memory_limit', $mem.'M');
 $newMem = false;
  echo "\n".'<!-- ';
  if ($newMem!==false) {
    echo "\n\t\t".'jsonViewer self-test; changed memory limit for this run to '.$mem.'Mb';
  } else {
    echo "\n\t\t".'jsonViewer self-test; could not change memory limit for this run from '.ini_get('memory_limit').' to '.$mem.'Mb';
  }
  echo "\n\t\t\t".'Will use no more than approx '.($mem-$grace).'Mb';
  echo "\n".' -->';
  echo "\n";
 $memAvailable = ($mem-$grace)*1024*1024;
  //define ('MEMORY_AVAILABLE', ($mem-$grace) * 1024 * 1024); // in bytes
 // define ('DURATION', ($duration==-1?-1:$duration * 60)); // in seconds. -1 = no time limit
	$duration = $duration==-1?-1:$duration*60;


// start generating
  $test = array();
  
  if ($addStrangeDataTypes) {
      $asciiTable='';
      for ($i=1; $i<hexdec('ff'); $i++) {
				$asciiTable.=chr($i).' ';
      }
      
      $unicodeTable = '';
      for ($i=1; $i<hexdec('ffff'); $i++) {
				$unicodeTable.=unichr($i).' ';
      }
      
      $jsonInKey = json_encode(array(
				'check' => array('this'=>'out')
      ));
      
      $test['All ASCII'] = array('here'=>$asciiTable);
      $test['All Unicode'] = array ('here'=>$unicodeTable);
      
      $test['some HTML'] = array(
		'credits' => 'http://www.immigration-usa.com/html_colors.html',
		'right-click here!' => file_get_contents('testdata.html')
      );
	  /*
      $test['HTML+JS colorpicker'] = array(
				'hmOverrides' => array (
				  'keyAsPre' => true,
				  'valRenderHTML' => true
				),
				'credits' => 'http://www.colorpicker.com/',
				'right-click here!' => file_get_contents('testdata2.html')
      );
	  */

	$jsonData =   array(
	'we'=>'go',
	'...'=>array(
	'deeper' => array(
	'and' => array (
	'deeper' => array (
	'down' => array(
	'the' => array(
	'rabbit' => 'hole',
	//'html within json' => array('here'=>file_get_contents('testdata.html')),
	'ASCII' => array('here'=>$asciiTable)
	//'Unicode' => array('here'=>$unicode)
	)))))));
	
	global $hmOutput;
	$hmOutput = '';
	json_encode_xxl($jsonData, array('mem'=>true,'excludeSeperators'=>true), '', false, false);

	$test['JSON data'] = array (
				'here' => $hmOutput);
      
      $test['img as key'] = array (
				'<div style="z-index:0;width:100%;height:100%;color:black;font-size:120%;font-weight:bold;background:url(http://nicerapp.com/nicerapp/com/ui/tools/jsonViewer/bg.gif) repeat;">key with backdrop;</div>' => 'xyz',
      );

      $test['json in key, theme override in sub'] = array(
			$jsonInKey => array (
			  'normal' => 'abc',		  
			  'theme override' => array ( 
			    'here' => jsonViewer_generateRandomArray(30,5, $duration, $memAvailable))
			  )
      );    
      $test['json in key, theme override in sub'][$jsonInKey]
			['theme override']['here']['hmo'] = array(
			  'themeName' => 'saColorgradientSchemeRed'
      );
  };

  if ($themeOverride) {
		$test['theme override'] = jsonViewer_generateRandomArray(
				//$keys, $deep, DURATION, MEMORY_AVAILABLE
				200, 7, $duration, $memAvailable
		);
		$test['theme override']['keys'] = $keys;
    $test['theme override']['hmo'] = array(
      'themeName' => 'saColorgradientSchemeWhiteToNavy'
    );
	}

	
	$test['random'] = jsonViewer_generateRandomArray(
		$keys, $deep, $duration, $memAvailable
	);

  
  //svar_dump ($data);
  //file_put_contents ('countdown.txt', 'done');
  hm ($test, 'example array', $options, $outputSettings);
  $test = null;
}


global $hmGenKeys;
$hmGenKeys = 0;
global $hmGenKeysDone;
$hmGenKeysDone = 0;
function jsonViewer_generateRandomArray ($maxKeys, $maxDepth, $maxDuration=-1, $maxMem=-1) {
  global $hmGenKeys;
  global $hmGenKeysDone;
  
  $r = array();
  $l1 = false;
  if ($maxKeys!==null) {
    $hmGenKeys = $maxKeys;
    $l1 = true;
  }
  
  $hmGenKeys--;
  if ($hmGenKeys<=0) return false;
  if ($maxDepth<=0) return false;
	
if (false) {
	$msg = 
		'k:'.number_format($hmGenKeysDone,0,'.',',')
	    .' m:'.number_format(memory_get_usage(true),0,'.',',')
		.' t:'.number_format($maxMem)
		."\n";
	echo $msg; flush(); ob_flush();
}

  if ($l1) {
    srand(jsonViewer_randMakeSeed());
    while ($hmGenKeys > 0) {
	$hmGenKeys--;
	$hmGenKeysDone++;
	if ($maxMem!=-1 && memory_get_usage(true) > $maxMem) {return $r;}
	if ($maxDuration!=-1 && $maxDuration < getDuration()) return $r;

	switch (rand(1,2)) {
	  case 1 : 
	    $next = jsonViewer_generateRandomArray (null, $maxDepth-1, $maxDuration, $maxMem);
	    if ($next!==false) 
	      $r +=  array(
		jsonViewer_randomValue(4,$maxDepth) => $next
	      );
	    break;
	  case 2 :
	    $r += array(
	      jsonViewer_randomValue(4,$maxDepth) => jsonViewer_randomValue(20,$maxDepth)
	    );
	    break;
	}
    }
  } else {
    $range = rand(0,50);
    for ($i=0; $i<$range; $i++) {
	$hmGenKeys--;
	$hmGenKeysDone++;
		
	if ($maxMem!=-1 && memory_get_usage(true) > $maxMem) return $r;
	if ($maxDuration!=-1 && $maxDuration < getDuration()) return $r;

	switch (rand(1,2)) {
	  case 1 : 
	    $next = jsonViewer_generateRandomArray (null, $maxDepth-1, $maxDuration, $maxMem);
	    if ($next!==false) 
	      $r +=  array(
		jsonViewer_randomValue(4, $maxDepth) => $next
	      );
	    break;
	  case 2 :
	    $r += array(
	      jsonViewer_randomValue(4, $maxDepth) => jsonViewer_randomValue(20, $maxDepth)
	    );
	    break;
	}
    }
  } 
  
  if (($hmGenKeysDone/7919)==round($hmGenKeysDone/7919)) sleep(1);
    
  return $r;
}

function jsonViewer_randomValue($maxLength, $maxDepth) {
  $r = '';
  switch (rand(0,9)) {
    case 0 : $r = rand (0,100000000); break;
    case 1 : $r = rand (0,100000000) / rand(1,100) / 3; break;
    default:
	switch (rand(0,1)) {
	  case 0:
	  $rnd = rand(1,$maxDepth);
	    for ($i = 0; $i < $rnd; $i++) {
	      $r.= unichr(rand(0,hexdec('ffff')));
	    }
	    break;
	  case 1: 
	    for ($i = 0; $i < $maxLength; $i++) {
	      $r.=chr(rand(ord('a'),ord('z')));;
	    }
	    break;
	}
	break;
  }
  //echo $r.'<br/>'.$maxLength.'<br/>';
  return $r;
}

function jsonViewer_randMakeSeed() {
  list($usec, $sec) = explode(' ', microtime());
  return (float) $sec + ((float) $usec * 100000);
}
?>
