<?php
/*

	STRONGLY RECOMMENDED you run this on a seperate machine, not your webserver.
	For windows, there's http://wampserver.com/en



	Below is the code to work with phpJSO-1.0.4!
*/

require_once (dirname(__FILE__).'/boot_stable.php');
set_time_limit (600);


$source = 
	file_get_contents('http://YOURSITE.COM/YOURFRAMEWORK/get_allofmyframeworkscripts_pw-DJz834hz.php?want=all&developer=false')
	.file_get_contents('http://YOURSITE.COM/siteLogic/get_allofmysitelogic_pw-JDKFyzkldjs.php?template=index&want=all&developer=false');
	
//var_dump ($source); die();
	
$messages = array();
$output = phpJSO_compress ($source, $messages, 1, false, false, false);
//var_dump ($messages);

/*
file_put_contents (dirname(__FILE__).'/output_stable/sources.originals.js', $output['source']);
file_put_contents (dirname(__FILE__).'/output_stable/sources.minusStrings.js', $output['minus_strings']);
file_put_contents (dirname(__FILE__).'/output_stable/sources.tokensReplaced.js', $output['tokens_replaced']);
file_put_contents (dirname(__FILE__).'/output_stable/sources.minified.js', $output['minified']);
file_put_contents (dirname(__FILE__).'/output_stable/sources.compressed.fast.js', $output['compressed_fast']);
*/

// THE ONLY TESTED OUTPUT FOR THE MOMENT. Sorry.
file_put_contents (dirname(__FILE__).'/output_stable/sources.obfuscated.fast.js', $output['obfuscated_fast']); 

var_dump ($output); // optional
?>
