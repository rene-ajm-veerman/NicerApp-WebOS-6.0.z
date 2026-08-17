<?php
// Copyright (C) 2002-2023 and All Rights Reserved (R) by Rene AJM Veerman <rene.veerman.netherlands@gmail.com>

// 	This file is part of jsonViewer
//	Written & copyrighted (c) 2010-2013 by [the owner of NicerApp.com] <NicerApp@gmail.com>
//	License: LGPL, free for any type of use
//	NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.
//	Download: http://NicerApp.com/products/jsonViewer/

//define ('SA_SHOW_CONSTANTS', true); //uncomment if you want to see what define()s the NicerApp framework exposes
require_once (dirname(__FILE__).'/../../../../../../boot.php');
$_SESSION['errors'] = array();
require_once (dirname(__FILE__).'/jsonViewer.php');
$hmConfig = jsonViewer_config (array(
	 'developerVisitors' => array (
	 // ONLY if your webbrowser IP ($_SERVER['REMOTE_ADDR']) is listed here, 
	 // is the browser allowed the use of random-array generator functions (index.php?fresh=true)
		'localhost' => array(),
		'192.168.1.33' => array(), // array() === for future extension, overrides of settings per visitor ip.
		'82.161.37.94' => array()
	 ),
	 'debug' => true  // use source versions.
));

// PUBLIC interface for index.php:

// index.php?fresh=true		
//	Generates new random data, not too large.

// index.php?fresh=true&mem=900
//	Will set the memory limit to 900Mb, and use max 898Mb memory
//	default (when omitted) is 13Mb

// index.php?fresh=true&mem=900&grace=4
//	Will set the memory limit to 900Mb, and use max 896Mb memory

// index.php?fresh=true&duration=5
//	Will allow the script to generate for 5 minutes
//	When not specified, the script runs until (almost) out of memory.

// index.php?fresh=true&keys=200000
//	Allows the script to generate 200-thousand keys max.
//	default is 1 billion.

// index.php?fresh=true&deep=12
//	Set max depth of generated array to 12. 
//	default is 7.

// index.php?fresh=true&nosdt=yes
//	Removes the "strange data types (html, json)" from the test-array.
//	default is to include them.

// index.php?fresh=true&themeOverride=someTheme
//	Uses the theme "someTheme" for the "large" array generated.
//	default is to NOT override the top-level theme.

// if ?fresh=true, you may supply any to all of 
//	&mem, &grace, &duration, &keys, &deep, &nosdt and/or &themeOverride.



// ---------------------------------------------------------------------------------------------------------


// private, implementation:

if (!array_key_exists('rootURL', $_GET)) $_GET['rootURL'] = dirname(dirname(dirname(dirname($_SERVER['REQUEST_URI']))));
/*
		<link rel="stylesheet" href="<?php echo $_GET['rootURL'];?>/code/libraries/jquery-ui-1.8.16/css/ui-lightness/jquery-ui-1.8.16.custom.css" type="text/css" media="all" />
		<link rel="stylesheet" href="<?php echo $_GET['rootURL'];?>/code/libraries/jquery-ui-1.8.16/development-bundle/themes/base/jquery.ui.theme.css" type="text/css" media="all" />
    	<script type="text/javascript" src="<?php echo $_GET['rootURL'];?>/code/libraries/jquery-ui-1.8.16/development-bundle/ui/jquery-ui-1.8.16.custom.js"></script>
*/

global $naWebOS;
define ('SA_WEB', 'https://'.$naWebOS->domainFolder);
?>
		<title><?php readfile (dirname(__FILE__).'/index.php.title.txt');?></title>
		<?php require (dirname(__FILE__).'/index.php.meta.php');?>
		<link type="text/css" rel="StyleSheet" media="screen" href="<?php echo SA_WEB?>/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/ui/jsonViewer/css.index.css">

		<script type="text/javascript">
			function startApp () {
				startAppDo();
			};
			function startAppDo() {
				na.m.settings.logLevel = 30; //see what a "300" does to your console log here;
				na.vividControls.init(document.body, function () {
					na.m.settings.initialized.site = true;
					na.hms.startProcessing();
					jQuery('div.jsonViewer').css ({opacity:0.7});
					na.sp.containerSizeChanged (jQuery('#tabs__scrollpane')[0]);
				});
				jQuery(window).resize(function(evt) {
					na.tabs.containerSizeChanged(jQuery('#tabs')[0]);
					var h = jQuery('#tabs')[0].children[2].offsetHeight;
					jQuery('#tabs__scrollpane__container').css ({height:h});
				//debugger;
					na.sp.containerSizeChanged(jQuery('#tabs__scrollpane')[0], true);
					if (
						jQuery('#scope0')[0]
						&& jQuery('#scope0')[0].children[0]
						&& jQuery('#scope0')[0].children[0].children[2]
					) na.sp.containerSizeChanged(jQuery('#scope0')[0].children[0].children[2], true);
					if (
						jQuery('#selfTest')[0]
						&& jQuery('#selfTest')[0].children[0]
						&& jQuery('#selfTest')[0].children[0].children[2]
					) na.sp.containerSizeChanged(jQuery('#selfTest')[0].children[0].children[2], true);
				})
			}
		</script>
	<div id="tabs" class="vividTabs vividTheme__" style="width:100%;height:100%">
		<ul>
			<li><a href="#tabs-2">PHP Demo</a></li>
			<li><a href="#tabs-3">JS Demo</a></li>
			<li><a href="#tabs-1">Description</a></li>
			<li><a href="#tabs-5">News</a></li>
			<li><a href="#tabs-6">Change log</a></li>
		</ul>
		<div id="tabs-1" class="tabPage">
			<center>
			<h1><?php readfile (dirname(__FILE__).'/index.php.title.txt');?></h1>
			<p style="font-style:italic;">
			It's the end of sitting on a wooded hill, 
			and looking only from close-range at the bark of the nearest tree.
			</p>
			</center>

			<div>
			This web component allows you to do a dump of (very large) arrays with many sub-levels.<br/>
			It also:<br/>
			<ul>
			<li>Hides sub-levels of arrays until you click to see them.</li>
			<li>Provides (reasonably accurate) byte sizes, and has navigation options built-in.</li>
			<li>Can handle strings of HTML and JSON in those arrays. You can make keys or values JSON strings, and they too will be *safely* decoded (no eval) into the viewer on the fly.<br/>
			Also supports integers, floats, booleans, javascript functions (so can be used for documentation purposes as well), and full ASCII and unicode tables.</li>
			<li>Can show any sub-array with a different (color range) theme.<br/>
			Since 1.2.0, all themes are auto-generated in js from simple color-range/steps definitions.<br/>
			This means that for any given depth starting anywhere, the correct between-steps are calculated from the color range.</li>
			</ul>
			<p>
			This component provides best response times in Google's Chrome browser, but also works in Firefox, Safari and Opera.<br/>
			Internet explorer 10 on Windows 8 will run the latest jsonViewer, but Internet explorer 8, 9, and before, in all modes, on Windows 7 or earlier, all have limitations that prevent jsonViewer from running properly.<br/>
			<br/>
			<a href="http://NicerApp.com/" target="_parent">Download</a>.<br/>
	       <br/>
			</p>
			</div>
			
			<h2>Purpose</h2>
			
			<p>
			With jsonViewer, you can do lots of work on large data sets more easily.<br/>
			The idea is you build up an array as large and as deep as necessary to completely describe and account for the tasks you're having the computer perform for you. A recursive object to-do list combined with all necessary intermediate calculations results in a sort of in-memory folder structure.<br/>
			jsonViewer eases this process by providing truely safe (no evals) and non-browser-freezing JSON encode and decode routines for data sizes up to at least 100MB of transport data, and of course a in-browser viewer for such large data structures that you can call up at any point during working on the data, or write out to a file on the server to view later.<br/>
			Since version 1.5.4, jsonViewer is able to correctly display arrays with more than a thousand keys (on a single level, not a nested key count).<br/>
			</p>


			<h2>Usage</h2>
			
			<h3>Requirements</h3>

			At the top of the script that generates your page, add this:
			<pre>
&lt;?php
require_once (dirname(__FILE__).'/[RELATIVE_PATH_TO]/NicerApp/sitewide/boot.php');
require_once (dirname(__FILE__).'/[RELATIVE_PATH_TO]/NicerApp/com/jsonViewer/jv.php');
?&gt;
			</pre>

			Next, whereever you generate the &lt;head&gt; section of your output HTML, include these statements:
			<pre>
&lt;link type="text/css" rel="StyleSheet" media="screen" href="&lt;?php echo SA_WEB?&gt;/get_css.php?want=jsonViewer"/&gt;
&lt;script type="text/javascript" src="&lt;?php echo SA_WEB?&gt;/get_javascript.php?want=jsonViewer"&gt;&lt;/script&gt;
			</pre>
			or, when you want to include logAndHandler to see any errors that might crop up;
			<pre>
&lt;link type="text/css" rel="StyleSheet" media="screen" href="&lt;?php echo SA_WEB?&gt;/get_css.php?want=jsonViewer,logAndHandler"/&gt;
&lt;script type="text/javascript" src="&lt;?php echo SA_WEB?&gt;/get_javascript.php?want=jsonViewer,logAndHandler"&gt;&lt;/script&gt;
			</pre>
			or, you might want to include my entire NicerApp WebOS;
			<pre>
&lt;link type="text/css" rel="StyleSheet" media="screen" href="&lt;?php echo SA_WEB?&gt;/get_css.php?want=all"/&gt;
&lt;script type="text/javascript" src="&lt;?php echo SA_WEB?&gt;/get_javascript.php?want=all"&gt;&lt;/script&gt;
			</pre>
			
			<h3>To create a dump from PHP</h3>
			<pre>
hm ($bigArray, $title);

//or:
hm ($bigArray, $title, array (
	'themeName' => 'someTheme'
));
			</pre>
			<br/>

			<h3>To create a dump from Javascript</h3>
			<pre>
hm (bigArrayOrObject, title);

//or:
hm (bigArrayOrObject, title, {
	themeName : 'someTheme'
});
			</pre>

			<h3>Generating test-data</h3>
			
			<p>
			Use this link to generate test-data:<br/>
			<a class="nomod" target="_parent" href="/NicerApp/com/jsonViewer/index.php?fresh=true&mem=35&file=hm_testdata_30mb.html">/NicerApp/com/jsonViewer/index.php?fresh=true&mem=35&file=hm_testdata_30mb.html</a><br/>
			You can check the source of that index.php to see more options. &mem=30 specifies to use 30mb of memory for that run.
			</p>
			
			<p>
			Alternatively, you can use .../NicerApp/com/jsonViewer/build.up.testdata.php to generate test data from the commandline. Use the source, Luke.
			</p>


			<h2 id="hmGotchas_key">Known bugs &amp; Issues</h2>
			
			<p>
			jsonViewer won't work in any version of Internet Explorer prior to the one released with Windows 8.
			</p>
			
			<p>
			The byte sizes reported by jsonViewer are not guaranteed to be fully accurate.<br/>
			There's no facility in javascript to get the byte-size of anything except strings,<br/>
			and while i could find via google that numbers (int/float) take 8 bytes per number stored,
			i could find no definitive answer on what a boolean costs (i use 1 byte for now), or what the overhead per js object is.<br/>
			</p>
			
			<p>
			The CSS&amp;HTML-byte-size (bottom-right of any hm() window) is the _text_ representation size,
			not the actual memory used by the browser to display the dump.<br/>
			Again, there's to my knowledge no way to get that information from the browser.<br/>
			But since we all work with html as text, i dare say that what i supply is an accurate indication.<br/>
			</p>
			
			<p>
			You'll have to use a OS system monitor app to check the actual RAM consumed.<br/>
			</p>
			
			<p>
			Non-latin (unicode) data is increased 300 to (often) 600% in size for transport through text/json.<br/>
			The 130mb current-limit-for-firefox is that after-increase _transport_ size.<br/>
			I'm not happy with this, but it doesn't affect me much (i work only with latin languages), 
			so i'm not going to spend the time to fix it.<br/>
			I invite the non-latin people to build a bson-decoder(1) based on the json-decoder that's in the 1.3.0
			releases. Please aim for browser compatibility.<br/>
			Until then, gzipping your output should bring some relief, although on older servers that
			might increase server-stress too much when pushing out large dumps.<br/>
			<br/>
			(1) a bson-encoder would be anything that "looks like json", but doesn't increase unicode 600% in size.<br/>
			Here's a <a href="http://unicode.org/reports/tr6/" target="_blank">experimental compression scheme for unicode</a>.<br/>
			</p>
		</div>
		<div id="tabs-2" class="tabPage">
			<?php
			$cacheFile = (array_key_exists('file', $_GET) ? dirname(__FILE__).'/'.$_GET['file'] : dirname(__FILE__).'/jv_sample_data.html');
			if (
			  jsonViewer_visitorIsDeveloper() && 
			  ( array_key_exists('fresh', $_GET) && $_GET['fresh']=='true' )
			) {
				function startTheTest ($x, $y, $f) {
					$options = array ( 'height' => '100%', 'opacity' => 0.7 );
					$outputSettings = array ( 'file' => $f );
					jsonViewer_selfTest ('paramP',array('paramQ'=>array('a'=>'b','b'=>array('c'=>'d'))), $options, $outputSettings); // in jv.php
				}
				$f = fopen ($cacheFile, 'w');
				startTheTest ('paramX', 'paramY', $f);
				fclose ($f);
			};
			readfile ($cacheFile);
			?>
		</div>
		<div id="tabs-3" class="tabPage">
			<div id="selfTest" class="jsonViewer hmPreInit" style="height:100%;">&nbsp;</div>

			<script type="text/javascript" language="javascript">
				setTimeout (function () {
					na.m.waitForCondition ('site init', function () {
						return (jQuery('#tabs-3').css('display')!=='none');
					}, function () {
						var hms_tst_js = {
							na : na
						};
						hm (hms_tst_js, 'sa dump', { htmlID : 'selfTest', opacity : 0.7 });
					},1000);
				}, 500);
			</script>
		</div>
		<div id="tabs-5" class="tabPage">
			<div class="hmText">
				<h1><span id="hmNews2012Dec22" style="color:darkred">2012 Dec 22, 14:08 CET</span></h1>
				<p>
				I'm doing another test to see how browsers hold up when jsonViewer is used to view ridiculously large nested arrays, I'm creating a test with 100MB of data now.
				</p>			
			</div>

			<div class="hmText" style="">
				<h1><span id="hmNewsFeb2_key" style="color:darkred">2010 Feb 2, 17:20 CET</span></h1>
				<p>
				My non-eval()ing chunked-JSON parser is now able to parse about 
				130 megabyte of transport data in about 10 - 15 minutes, on a 
				Intel Core Duo 4300, with about 1G real RAM free at the start of
				the test.
				</p>

				<p>
				However, pushing 330 meg through it still causes firefox to 
				stop working (for hours) at about 200Mb parsed.
				</p>

				<p>
				I'm calling it quits from here on. 120-150 meg is more than enough for 
				my needs for the forseeable future.
				</p>

				<p>
				If browser manufacturers wish to proof their browser against 
				large data-sets, they can download this component and use 
				the (documented) index.php to generate larger random datasets.
				</p>

				<p>
				And it appears IE8 does not honor the use of setTimeout()
				It still pops up highly annoying "stop script?" windows..
				I've posted this on the IE8 dev forums.
				</p>
				<span style="color:darkred">Feb11: no useful replies on the IE8 dev forums unfortunately..</span>
			</div>

		</div>
		<div id="tabs-6" class="tabPage">
			<h1>Change Log</h1>
			<table class="hmVersion" cellspacing="5" style="width:100%">
				<tr>
					<td style="width:200px;">
						<span id="v140_key" class="hmVersion">Version 1.6.0</span><br/>
						<span class="hmReleaseDate">
						April 16, 2013
						</span>
					</td>
					<td>
						<ul>
							<li>Plugged in source beautifier and highlighter</li>
							<li>Added click handlers (left and right click) for all keys displayed in hm()</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td style="width:200px;">
						<span id="v140_key" class="hmVersion">Version 1.5.9 - 1.5.16</span><br/>
						<span class="hmReleaseDate">
						January 2, 2013 - April 14, 2013
						</span>
					</td>
					<td>
						<ul>
							<li>Scrollpane issues fixed.</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td style="width:200px;">
						<span id="v140_key" class="hmVersion">Version 1.5.8</span><br/>
						<span class="hmReleaseDate">
						December 22, 2012
						</span>
					</td>
					<td>
						<ul>
							<li>Completed upgrade to allow (sub-)objects/arrays with more than a few hundred key-value pairs to be displayed without the browser crashing.</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td style="width:200px;">
						<span id="v140_key" class="hmVersion">Version 1.5.0</span><br/>
						<span class="hmReleaseDate">
						2012 June 6
						</span>
					</td>
					<td>
						<ul>
							<li>Now uses vividScrollpane.</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td style="width:200px;">
						<span id="v140_key" class="hmVersion">Version 1.4.0</span><br/>
						<span class="hmReleaseDate">
						around 2011 July 13th
						</span>
					</td>
					<td>
						<ul>
							<li>several cripling bugs finally fixed.</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td style="width:200px;">
						<span id="v134_key" class="hmVersion">Version 1.3.4</span><br/>
						<span class="hmReleaseDate">
						around 2010 oct 1st
						</span>
					</td>
					<td>
						<ul>
							<li>bug fixed: smooth scrolling for internal links</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td style="width:200px;">
						<span id="v133_key" class="hmVersion">Version 1.3.3</span><br/>
						<span class="hmReleaseDate">around 2010 oct 1st</span>
					</td>
					<td>
						<ul>
							<li>many bugs fixed.</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td style="width:200px;">
						<span id="v131_key" class="hmVersion">Version 1.3.1</span><br/>
						<span class="hmReleaseDate">around 2010 may 12th</span>
					</td>
					<td>
						<ul>
							<li>hm() can now be part of a <a href="http://www.kelvinluck.com/assets/jquery/jScrollPane/jScrollPane.html" target="_new">jScrollPane</a> and still scroll correctly internally.</li>
							<li>hm() is now used by <a href="http://NicerApp.com/lah">logAndHandler</a>.</li>
							<li>hms.json.decode.small() can now decode synchronously. Still have to adjust it to use asynchronous operations for larger strings though.</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td style="width:200px;">
						<span id="v130_key" class="hmVersion">Version 1.3.0 (all betas)</span><br/>
						<span class="hmReleaseDate">around 2010 feb 15</span>
					</td>
				<td>
					<ul>
						<li>All these were beta versions trying to deal with the large-dump issue.</li>
					</ul>
				</td>
				</tr>
				<tr>
					<td style="width:200px;">
						<span id="v121_key" class="hmVersion">Version 1.2.1</span><br/>
						<span class="hmReleaseDate">2010 jan 18, 08:15</span>
					</td>
					<td>
						<ul>
							<li>18 Jan, 19:50<br/>
							While some HTML-within-JSON remains problematic, i've been able to avert some of these problems by 
							putting the trace-data in a hidden div.<br/>
							I've also (had to ;) fix hms.tools.augmentWithStats(), to not die on seeing "stats" or "data" keys in the data / trace-data.<br/>
							Various other bugs have been fixed too, and cosmetic improvements have been made.
							</li>
							<li>17 Jan, 11:10:<br/>
							Fixed php trace display.<br/>
							Cosmetic &amp; speed improvements.<br/>
							javascript:hm() might report too large a HTML size (bottom-right), will look into that some other time.<br/>
							<br/></li>
							<li>When clicking on "nnnn values" links, a .setTimeout() is now used 
							between sub-arrays unfolding to prevent
							script-abusing-cpu warnings/errors.</li>
							<li>(javascript) Now inserts the HTML for sub-arrays into the DOM after a .setTimeout().<br/>
							That prevents script-abusing-cpu warnings/errors, and building up of xxl html.
							</li>
							<li>(php) json_encode_safe() improved by replacing preg_match() with straight IFs.<br/>
							_safe() is joined by _safer(), which does not build up a xxl json string at the server end, 
							but outputs directly to the output buffer instead.<br/>
							</li>
							<li>JSON encoding routines now no longer escape many common latin characters like . , 
							{ [ &lt; &gt; ] } etc etc.<br/>
							Saves transport space.</li>
							<li>If transport size >0.8Mb, jsonViewer skips the json-cleanup regexps.<br/>
							This allows for bigger data sizes. I've tested a mix of json/html/unicode at 1.8megs,
							and while i had brief "script abusing cpu" warnings(1), the display did complete.<br/>
							(1) = caused by eval(). won't try to find a workaround for this soon, 
							because it involves much code.<br/>
							</li>
							<li>Fixed display-options that determine how a HTML/JSON key / value should be displayed initially.<br/>
							It's now possible to specify which if any keys / values should be printed as rendered HTML or &lt;pre&gt;-HTML.
							</li>
							<li>Fixed IE(8?) support by replacing some internal variable names that IE choked on.</li>
						</ul>
					</td>
					</tr>
					<tr>
						<td style="width:200px;">
							<span id="v120_key" class="hmVersion">Version 1.2.0</span><br/>
							<span class="hmReleaseDate">2010 jan 12, 21:30</span>
						</td>
						<td>
							<ul>
								<li>12/01, 21:30:<br/>
								displaying key-names now with hms.tools.printVariable(), in anticipation of enabling auto-rendered html keys.<br/>
								and enabled html-within-json viewing.
								</li>
								<li>12/01, 18:45: some minor silent updates done.</li>
								<li>12/01, 07:25:<br/>Improved upon the json encoding routines (used for transport) quite a bit.</li>
								<li>Fixed JSON-within-JSON display.<br/><br/></li>
								<li>&lt;12/01:<br/>
								Javascript hm() support added</li>
								<li>Added ability to view HTML strings as rendered HTML.<br/>I admit there are still problems with some HTML "taking over the browser" as soon as viewed in rendered form.<br/>I hope to fix most of those problems in a later release.</li>
								<li>Added ability to put semi-binary data (see "All Ascii" in "random array" example) in values.</li>
								<li>Much improved CSS; it's now properly generated in javascript 
								instead of badly hand-crafted &amp; stored on the server.<br/>
								Themes are now generated from definitions like these:<br/>
								<br/>
								<pre>
			var sasIce = {
			themeName : 'sasIce',
			cssGeneration : {
			colorLevels : {
			// This sets "stops" for color gradients. 
			//	   0 = outer level of display, 
			//	 100 = deepest level of display.
			
			0 : {
			background : 'navy',
			color : '#FFFFFF'
			},
			45 : {
			color : 'black'
			},
			75 : {
			background : 'lime',
			color : 'navy'
			},
			100 : {
			background : 'white',
			color : 'black'
			}
			//Rules:
			// 1: only css COLOR properties allowed here.
			// 		color names allowed, for a list see http://www.w3schools.com/css/css_colornames.asp
			// 2: properties used anywhere in a list like this must be present in both 0: and 100:
			}
			},
			......
								</pre>
								( please use hms.tools.addTheme(contributerName, themeName, theme) to add a theme, 
								and load them just after jv.js is loaded. var theme = sasIce)
								</li>
						</ul>
					</td>
				</tr>
				<tr>
					<td style="width:200px;">
						<span id="v110_key" class="hmVersion">Version 1.1.0</span><br/>
						<span class="hmReleaseDate">2010 jan 9, 10:45</span>
					</td>
					<td>
						<ul>
							<li>Added ability to view a trace for any given dump.</li>
							<li>Added ability to print any sub-array with a different theme. Please pardon the CSS bloat, it's the only way to make it work, imo.</li>
							<li>Added functions:
								<ul>
									<li>htmlDump($var, $title); //outputs html table without any javascript interactions directly to page</li>
									<li>htmlDumpReturn ($var, $title); // returns HTML string that htmlDump() would output to page.</li>
								</ul>
							</li>
							<li>Fixed fatal errors when data contains \n or \r characters.</li>
							<li>Added proper error message for any other faulty-encoded json.</li>
							<li>Fixed recursion vulnerabilities;<br/>
							I made a test with a random array of 1.6Mb, displayed fine :)
							</li>
						</ul>
					</td>
				</tr>
			
				<tr>
				<td>
				<span id="v102_key" class="hmVersion">Version 1.0.2</span><br/>
				<span class="hmReleaseDate">2010 jan 6 18:05</span>
				</td>
				<td>
				<ul>
				<li>Cleaned up the CSS namespace, removed cluttering from output, added theme-ing options.</li> 
				<li>Using jQuery.scrollTo now, for smooth scrolling.</li>
				<li>Moved JSON code into hms js object, 1 less &lt;script&gt; include.</li>
				</ul>
				</td>
				</tr>
				<tr>
				<td>
				<span id="v101_key" class="hmVersion">Version 1.0.1</span><br/>
				<span class="hmReleaseDate">2010 jan 5 21:45</span>
				</td>
				<td>
				<ul>
				<li>Cleaned up the namespaces, and renamed the component from "jsonDebug" to "jsonViewer".</li>
				<li>Added siblings menu to array listings.</li>
				</ul>
				</td>
				</tr>
				<tr>
					<td>
						<span id="v100_key" class="hmVersion">Version 1.0.0</span><br/>
						<span class="hmReleaseDate">2010 jan 5 16:45</span>
					</td>
					<td>
						<ul>
						<li>Initial code done.</li>
						<li>Fixed breadcrumb functionality, and various other bugs.</li>
						</ul>
					</td>
				</tr>
			</table>
		</div>
		<!--
		<div id="tabs-7" class="tabPage">
			<div class="hmText">
			<table border="1" cellspacing="10" class="jsonViewer_status" style="border:1px solid black; margin:10px">
			<tr><td id="hmStatus_key" colspan="2" style="font-size:85%;">Written and <span style="">Copyrighted(c) 2010-2011 by [the owner of NicerApp.com]</span> [NicerApp@gmail.com]</td></tr>
			<tr><td>Languages</td><td>php5 + javascript</td></tr>
			<tr><td>Permalink</td><td>
				<a href="http://NicerApp.com/jsonViewer/">http://NicerApp.com/jsonViewer/</a><br/>
				<br/>
				Mirror : <a href="http://code.google.com/p/jsonViewer/" target="_blank">http://code.google.com/p/jsonViewer/</a><br/>
			</td></tr>
			<tr><td id="downloadHM_key">Download</td><td>
				<a href="http://NicerApp.com/downloads/">jsonViewer as part of a package of downloads.</a><br/>
			</td>
			</tr>
			<tr><td>License:</td>
			  <td>
				<a href="http://www.gnu.org/copyleft/lesser.html">LGPL</a><br/>
				<b>NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.</b><br/>

				Free to use, also for commercial purposes. <br/>
				When re-distributing, you may only re-distribute the package that this web component came in, or link to the site.<br/>
			<br/>
				Please send any improvements, or any color-schemes you want to share with the world, back to me.<br/>
				I'm especially interested in any ports of the +- 250 lines of server-side code to other languages.<br/>
			<br/>
				Feature requests are welcome, but please dont expect an immediate reply.<br/>
				<br/>
				If you use this component in a public way, i'd appreciate a url of the thing in action..<br/>
			  </td>
			</tr>
			<tr><td>To-do</td>
			  <td>
				  <ul>
							<li>Check use from javascript some more</li>
							<li>Add ability to put a html header on any sub-array</li>
							<li>Far off: XML auto-parsing/rendering.. </li>
							<li>Far off: Improve auto-try filters to display more forms of text human-readable.</li>
							<li>Maybe: Configure traces to launch your favorite editor and move to the correct line when you click on the line.</li>
							<li>Maybe: Try to strip all 'bad' js before viewing as rendered HTML.</li>
				  </ul>
			  </td>
			</tr>
			<tr><td>Acknowledgements</td>
			  <td>Thanks go to <a href="http://fmarcia.info/jsmin/test.html" target="_blank">this js minifier</a> and 
			<a href="http://jsonformatter.curiousconcept.com/" target="_blank">this json checker</a> and 
			<a href="http://jsbeautifier.org/?" target="_blank">this js beautifier/re-formatter</a>.<br/>
			</td></tr>
			</table>
			</div>
		-->
		</div>
	</div>
