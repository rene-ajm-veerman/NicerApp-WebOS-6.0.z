<?php
// See if we're getting called via the command line
if (isset($_SERVER['SHELL']))
{
	// If no arguments are provided, display help screen
	if (count($_SERVER['argv']) < 3)
	{
		print("php phpJSO.php <in-file> <out-file>\n\n");
		print("Other options:\n");
		print("\t-encoding-type={value}           Possible values: 1 or 0. The default encoding is 1. 0 turns encoding off.\n");
		print("\t-fast-decompress={value}         Possible values: 1 or 0. Defaults to 0. 1 is on, 0 is off.\n");
		print("\t-collapse-blocks={value}         Possible values: 1 or 0. Defaults to 0. 1 is on, 0 is off.\n");
		print("\t-collapse-math={value}           Possible values: 1 or 0. Defaults to 0. 1 is on, 0 is off.\n");
	}

	// Check all options, see if they should be on or off
	$options = array
	(
		array('param' => '-encoding-type', 'value' => 1),
		array('param' => '-fast-decompress', 'value' => 0),
		array('param' => '-collapse-blocks', 'value' => 0),
		array('param' => '-collapse-math', 'value' => 0)
	);
	foreach ($_SERVER['argv'] as $argument)
	{
		foreach ($options as $k=>$option)
		{
			if (preg_match('#'.preg_quote($option['param']).'\=(.*?)$#', $argument, $match))
			{
				$options[$k]['value'] = $match[1];
			}
		}
	}

	// Check that in-file exists and out-file is writable
	$in_file = $_SERVER['argv'][1];
	$out_file = $_SERVER['argv'][2];
	if (!file_exists($in_file))
	{
		die("Make sure that in-file ($in_file) exists.");
	}
	if (!(touch($out_file) && file_exists($out_file) && is_writable($out_file)))
	{
		die("Make sure that out-file ($out_file) can be written to.");
	}

	// Open file
	$in_file_code = file_get_contents($in_file);
	
	// Compress it
	$messages = array();
	$compressed_code = phpJSO_compress($in_file_code, $messages,
		$options[0]['value'],
		$options[1]['value'],
		$options[2]['value'],
		$options[3]['value']);
	
	// Save to out file
	$out_file_handle = fopen($out_file, 'w');
	fwrite($out_file_handle, "//*\n * {your code messages/copyright here}\n// *\n// * This code was compressed by phpJSO - www.cortex-creations.com.\n\n\n".$compressed_code);
	fclose($out_file_handle);
	
	// Report stats
	$message = '';
	if (count($messages))
	{
		print("Successfully compressed code.\n");
		foreach ($messages as $k=>$m)
		{
			print("\t - $m\n");
		}
		print("\nThank you for using phpJSO! Check www.cortex-creations.com for news and updates.");
	}
}
// Only do HTML output if UNIT_TEST constant is not present
else if (!defined('UNIT_TEST'))
{
	// Uncomment to profile using APD
	//apd_set_pprof_trace('/Users/joshuagross/Desktop/APD Traces');

	$phpJSO_version = '0.9';

	// Compress javascript from a submitted form
	$compressed_js = 'Compressed code will be placed here';
	$code = 'Place your code here.';
	$messages = array();
	if (isset($_REQUEST['jscode']))
	{
		// Get JS code
		$code = $_REQUEST['jscode'];
		// Strip slashes from input?
		if (get_magic_quotes_gpc())
		{
			$code = stripslashes($code);
		}
		// Compress
		$compressed_js = phpJSO_compress($code, $messages,
			(isset($_REQUEST['encoding_type']) ? $_REQUEST['encoding_type'] :'1'),
			(isset($_REQUEST['fast_decompress']) ? true : false),
			(isset($_REQUEST['collapse_blocks']) ? true : false),
			(isset($_REQUEST['collapse_math']) ? true : false));
	}
	$compressed_js = htmlspecialchars($compressed_js);
	$code = htmlspecialchars($code);

	// Format compression messages, if any
	$message = '';
	if (count($messages))
	{
		$message = '<b>Successfully compressed code.</b><br /><ul>';
		foreach ($messages as $k=>$m)
		{
			$message .= nl2br("<li>$m</li>");
		}
		$message .= '</ul><br /><br />';
	}

	// Get HTML value of fast_decompress checkbox
	$encoding_type = (isset($_REQUEST['encoding_type']) ? $_REQUEST['encoding_type'] : '1');
	$fast_decompress_value = (isset($_REQUEST['fast_decompress']) ? 'checked="checked"' : '');
	$collapse_blocks_value = (isset($_REQUEST['collapse_blocks']) ? 'checked="checked"' : '');
	$collapse_math_value = (isset($_REQUEST['collapse_math']) ? 'checked="checked"' : '');

	// Get encoding type select options
	$encoding_options = '';
	$encoding_options .= '<option value="1" '.($encoding_type == '1' ? 'selected="selected"' : '').'>Numeric encoding: smallest possible</option>';
	$encoding_options .= '<option value="off" '.($encoding_type == 'off' ? 'selected="selected"' : '').'>No encoding</option>';

	// Show forms, including any compressed JS
	print("
		<html>
			<head>
				<title>phpJSO version $phpJSO_version</title>
				<style type=\"text/css\">
					body {
						margin: 0px;
						padding: 20px;
						background-color: #ffffff;
						color: #000000;
						font-family: Verdana, Arial, Sans;
						font-size: 11px;
					}
					textarea {
						background-color: #dddddd;
						width: 100%;
						height: 40%;
						padding: 5px;
						color: #000000;
						font-family: Verdana, Arial, Sans;
						font-size: 11px;
					}
				</style>
				<script>
				</script>
			</head>
			<body>
				<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">
					$message
					
					<b>Compressed Code:</b><br />
					<textarea rows=\"20\" cols=\"30\">$compressed_js</textarea><br /><br />
		
					<b>Place Your Code Here:</b><br />
					<textarea rows=\"20\" cols=\"30\" name=\"jscode\">$code</textarea><br /><br />
					
					<b><label for=\"id_encoding_type\">Encoding type: </label></b><select name=\"encoding_type\" id=\"id_encoding_type\">$encoding_options</select><br />
					This is the encoding type that will be used by phpJSO; it is simply how you want
					phpJSO to compress and encode your code. For VERY large code (6,000 lines or more without comments) either turn
					encoding off, or encode about 3,000 lines at a time. This will ensure that browsers
					execute the code quickly.
					Please note that encoding does NOT change how your code works AT ALL.<br /><br />
					
					<b><input type=\"checkbox\" name=\"fast_decompress\" id=\"id_fast_decompress\" $fast_decompress_value /><label for=\"id_fast_decompress\">Fast decompression</label></b><br />
					This option is recommended for large javascript files (above 1,000 lines of code without comments); the larger
					a script is, the longer it will take to decompress. you won't notice much of a speed
					difference with smaller scripts. note, however, that this option also makes the
					compressed code <i>slightly</i> larger. See the \"encoding type\" option; this
					option doesn't matter if you turn encoding off.<br /><br />

					<b><input type=\"checkbox\" name=\"collapse_blocks\" id=\"id_collapse_blocks\" $collapse_blocks_value /><label for=\"id_collapse_blocks\">Collapse Code Blocks</label></b><br />
					This option helps compress code to miniscule sizes. It \"collapses\" code blocks
					whenever possible. For example, <i>if(1){alert(1);}</i> becomes <i>if(1)alert(1);</i>.
					In short code it may not make a huge difference, but it can in longer code. It also
					makes phpJSO slightly slower.<br /><br />

					<b><input type=\"checkbox\" name=\"collapse_math\" id=\"id_collapse_math\" $collapse_math_value /><label for=\"id_collapse_math\">Collapse Math Constants</label></b><br />
					If you select this option (recommended), phpJSO will change code sections like \"1+1\" to \"2\",
					or \"100-(20+30)\" to \"50\". This is a very fast operation and can help reduce code size as
					well as speed up running times.<br /><br />
					
					<input type=\"submit\" value=\"Compress Code\" />
				</form>
			</body>
		</html>
	");
}
?>
