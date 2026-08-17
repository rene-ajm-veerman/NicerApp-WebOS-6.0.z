<?php
// Copyright (C) 2002-2023 and All Rights Reserved (R) by Rene AJM Veerman <rene.veerman.netherlands@gmail.com>
//
function jsonViewer_mayExec () {
  global $hmSettings;
  $may = false;
  $ipz = $hmSettings['permissions']['execIPs'];
  foreach ($ipz as $k=>$ip) {
    if ($_SERVER['REMOTE_ADDR']==$ip) $may=true;
  }
  return $may;
}

function jsonViewer_hackingDetected($errStr) {
  global $hmSettings;

  if (!array_key_exists('scriptOperators',$hmSettings)) return false;
  if (!array_key_exists('allErrorsAndHacks', $hmSettings['scriptOperators'])) return false;
  $ea = $hmSettings['scriptOperator']['allErrorsAndHacks'];

  $eas = '';
  foreach ($ea as $k=>$e) {
    if ($eas!='') $eas.=', ';
    $eas.= $e;
  }

  $headers = '';
  $headers .= "Content-type: text/html\r\n";
  $headers .= "Cc: seductiveapps@gmail.com\r\n";

  $subject = 'jsonViewer: hacking detected from '.$_SERVER['REMOTE_IP'];

  $html = 
    $errStr.
    '<br/><br/>'.
    htmlBackTraceTable().
    '<br/><br/>';
    

  return mail ($eas, $subject,$html, $headers);
}

function htmlBackTraceTable ($level=0) {
	$tr = debug_backtrace();
	//$tr = array (1 => array ("file"=>"test", "function"=>"test", "args"=>""));

	$pre = "";
	for ($i=0; $i<=$level; $i++) $pre.="\t";

	$html = "$pre<table class='backTrace'>\n";
	foreach ($tr as $eventNo => $event ) {//array record of varying contents
		if ($eventNo >= 0) {
			//skip the first two events, they are part of the error reporting system itself.
			$html.= "$pre\t<tr><td class='btFunction'>";
			if (strpos($event["file"], "adodb")==0) { //removes clutter
				if (isset ($event["file"]) ) {
					$html.= "<span class='btFile'>file ".$event["file"].":".$event["line"]."</span><br/>";
				}
				if (isset ($event["function"])) {
					$html.= "<span class='btFunction'>function ".$event["function"]." (<pre>";
					if (isset ($event["args"])) {
						foreach ($event["args"] as $argNo => $arg) {
							//$html.= "<pre>".svar_dump($arg)."</pre>";
							$html.= svar_dump($arg);
							if ($argNo < count($event["args"])-1) $html.=", \n";
						}	
					}
					$html.= "</pre>);</span><br/>\n";
				}
				$html.="$pre\t</td></tr>\n";
			}
		}
	}
	$html.= "$pre</table>";
	return $html;
}

?>
