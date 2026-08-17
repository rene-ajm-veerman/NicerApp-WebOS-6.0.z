<?php
require_once (dirname(__FILE__).'/globals.php');
require_once (dirname(__FILE__).'/../opensourcedBySeductiveApps.com/tools/webappObfuscator/boot_latestDevelopment.php');

error_reporting(E_ALL);
set_error_handler ('woBasicErrorHandler');

?>
<html>
<head>
	<!-- for themes see http://code.jquery.com/ui/ -->
	<link type="text/css" rel="StyleSheet" media="screen" href="http://code.jquery.com/ui/1.11.4/themes/ui-darkness/jquery-ui.css">
	<link type="text/css" rel="StyleSheet" media="screen" href="../opensourcedBySeductiveApps.com/tools/webappObfuscator/webappObfuscator-1.0.0/webappObfuscator-1.0.0.css">
	<link type="text/css" rel="StyleSheet" media="screen" href="obfuscate.css">
	<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.4.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script type="text/javascript" src="../opensourcedBySeductiveApps.com/tools/webappObfuscator/webappObfuscator-1.0.0/lib/jQuery.jPlayer-2.9.1/dist/jplayer/jquery.jplayer.js"></script> 
	
	<script type="text/javascript">
		jQuery(document).ready(function() {
			// init :
			var 
			encodingDone = false, 
			decodingDone = false,
			d = null,
			flags = {
				startFromScratch : <?php echo array_key_exists('n',$_GET)?'true':'false'?>,
				getNewSources : <?php echo array_key_exists('ns',$_GET)?'true':'false'?>,
				calculateNewTokens : <?php echo array_key_exists('nt',$_GET)?'true':'false'?>
			};
			jQuery.jPlayer.timeFormat.showHour = true;
			jQuery("#jplayer").jPlayer({
				ready: function () {
				},
				ended: function (event) {
				},
				supplied: "mp3",
				nativeSupport: false, //attempt to force this component to using swf (flash) for playback, because that shows a progress bar as mix downloads to browser;
				swfPath: '<?php echo $obfuscatorURL?>/webappObfuscator-1.0.0/lib/jquery.jPlayer-2.9.1/'
			});
			
			// main() :
			setTimeout(startApp, 1000);
			
			function startApp () {
				jQuery( "#tabs" ).tabs();
				jQuery(window).resize(onresize);
				startEncoding();
			};
			
			function onresize () {
			  jQuery('#holder').css({top:'4%',left:'4%',width:'92%', height : 400});
			  jQuery('#background').css({width:'100%',height:'100%'});
			}
			
			function startEncoding () {
				var ajaxCommand = {
					url : 
						'<?php echo $obfuscator__settingsURL?>/ajax_obfuscate.php?params=yes'
						+(
							flags.startFromScratch
							? '&n=y'
							: ''
						)
						+(
							flags.getNewSources
							? '&ns=y'
							: ''
						)
						+(
							flags.calculateNewTokens
							? '&nt=y'
							: ''
						),
					type : 'GET'
				};
				jQuery.ajax (ajaxCommand);			
				
				setTimeout (encodingStatus, 1000);
			};
			
			function encodingStatus () {
				var ajaxCommand = {
					url : '<?php echo $obfuscator__settingsURL?>/webappObfuscator__output/status.ajax_obfuscate.html',
					type : 'GET',
					converters : {
						"* text" : window.String,
						"text json" : window.String,
						"text html" : window.String
					},
					success : function(data,ts,xhr) {
						if (typeof data=='string') data = data.replace(/\//g, ' / ').replace(/< \/ /g, '</');
						jQuery('#encodingStatus').html(data);
						jQuery('#encodingStatus')[0].scrollTop = 99999999999;
						
						if (data.match('ALL DONE')) {
							encodingDone = true;
							getAndDisplayOutput();
						}
					}
				};
				jQuery.ajax (ajaxCommand);	

				if (!encodingDone) setTimeout (encodingStatus, 1000);
			};
			
			function getAndDisplayOutput () {
				var ajaxCommand = {
					url : '<?php echo $obfuscator__settingsURL?>/webappObfuscator__output/webappObfuscatorDebugData.json',
					type : 'GET',
					success : displayOutput
				};
				jQuery.ajax (ajaxCommand);			
			};

			function displayOutput (dataAsObject) {
				decodingDone = true;
				d = dataAsObject;
				var c = 0;
				jQuery('#encodingOutput').html('<h1>Finalized token list</h1>');
				for (var k in dataAsObject.workData.tokens) {
					var v = dataAsObject.workData.tokens[k];
					jQuery('#encodingOutput').append('<p>'+k + ' = ' + v+'</p>');
					c++;
				};
				jQuery('#encodingOutput').append ('<h1>'+c+' tokens</h1>')
				
				//playSound__completed();
			};
			
			function playSound__completed (hmCmd) {
				//jQuery('#encodingOutput table:first').css({width:'100%',height:'100%'});
				
				setTimeout(function(){
					jQuery('#jplayer').jPlayer("setMedia", {
						mp3: '<?php echo $obfuscatorURL?>/sounds/dog.mp3'
					}).jPlayer("play");
				}, 1000);
			};
		});
	</script>
</head>
<body style="margin:0px;padding:0px;">
	<img id="background" src="artwork/1288.jpg" style="position:absolute;width:100%;height:100%;"/>

	<form id="dummy">
	<table id="holder" cellpadding="4" border="0" style="position:absolute;top:4%;left:4%;width:92%;height:500px;">
		<tr>
			<td>
				<div id="tabs" class="jQueryUI" style="width:100%;height:100%;">
					<ul>
						<li><a href="#tabs-a">Status</a></li>
						<li><a href="#tabs-b">Output</a></li>
					</ul>
					<div id="tabs-a" class="tabPage">
						<div id="encodingStatus" class="tabContent" style="overflow-y:scroll;"></div>
					</div>
					<div id="tabs-b" class="tabPage">
						<div id="encodingOutput" class="tabContent" style="overflow-y:scroll;"></div>
					</div>
				</div>
			</td>
		</tr>
	</table>
	</form>
	
	<div id="jplayer" class="jp-jplayer"></div>
</body>
</html>