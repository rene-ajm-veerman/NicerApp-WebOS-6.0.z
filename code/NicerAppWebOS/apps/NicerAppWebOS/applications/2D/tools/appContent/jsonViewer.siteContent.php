<div id="jsonViewer">
	<h1>jsonViewer : JSON Validator &amp; Viewer</h1>
	<p class="important">
	JSON entered on this site is <b>NOT</b> transmitted to the server that served this page, nor requested via that server; 
	not even the URL you request is transmitted to http://seductiveapps.com.<br/>
	</p>
	
	<p>
	You can paste your JSON or URL providing JSON that needs to be validated / viewed, in the textarea field above here, and then click on the "Validate" button of course.<br/>
	You can hide the toolbar via the "Dialogs" menu at the top.
	</p>
	<p style="color:lime;">
	When requesting a URL of your own, you *must*, *before sending the JSON data (and only the JSON data)*, provide a http header to allow seductiveapps' javascript to work with that data.<br/>
	In PHP, this is done like this:<br/>
	<br/>
	&lt;?php
	header ('Access-Control-Allow-Origin: http://seductiveapps.com');
	?&gt;
	</p>
	
	<h2>Advanced Validation and Formatting</h2>
	<p>
	Available through <a class="nomod" target="jfcc" href="https://jsonformatter.curiousconcept.com/">jsonformatter.curiousconcept.com</a>, and <a class="nomod" target="gsjf" href="https://www.google.nl/search?q=json+formatter&oq=json+formatter&aqs=chrome..69i57j69i65j0l4.1833j0j7&sourceid=chrome&ie=UTF-8">google search results for 'json formatter'</a>.
	</p>
	
	
	<h2>jsonViewer's handling of Large JSON files</h2>
	
	<p>
	This seductiveapps web-UI framework that this JSON validator is part of &amp; included with, comes with it's own JSON random-data generator.. Which at the moment is capped by it's design that creates sample JSON data as an actual in-PHP-memory datastructure before encoding it with a custom (simple-PHP based) encoder. This means that I can only generate datasets smaller than 1GB of JSON data at the moment, but it's still useful to test JSON datasizes between 10MB and 800MB.
	</p>
	
	<p>
	I have pre-generated and uploaded some large (starting at around 100MB) JSON datafiles that do not contain JSON errors and come preloaded with all the datatypes that the default JSON dataformat can handle (including a complete Unicode table).
	</p>
	
	<p>
	These large JSON example files can be downloaded from <a class="nomod" target="largeJSON" href="https://github.com/seductiveapps/largeJSON/">https://github.com/seductiveapps/largeJSON/</a>; be sure to also read the brief <a class="nomod" target="largeJSON" href="https://github.com/seductiveapps/largeJSON/wiki">https://github.com/seductiveapps/largeJSON/wiki</a> to see which JSON validator apps can currently handle files this large..
	</p>
</div>
<script type="text/javascript">
	//var seductiveapps_appCode_jsonViewer = {
	sa.apps.loaded.jsonViewer = {
		settings : {
			loadedIn : {
				'#siteContent' : {
					settings : {
						initialized : true,
						ready : true
					},
					onload : function (settings) {
                        jQuery('#siteContent').addClass('saContent');
					},
					ondestroy : function (settings) {
                        jQuery('#siteContent').removeClass ('saContent');
					},
					onresize : function (settings) {
						//sa.desktop.resize();
						
						//jQuery('#inputJSON').css({height:50,position:'relative'});
						//see the appCode that goes along with this 
						
						var container = jQuery('#siteContent__contentDimensions')[0];
						
						if (sa.apps.loaded.jsonViewer.settings.cmd) {
							jQuery('#siteContent__scrollpane__container, #siteContent__scrollpane, #siteContent,  #'+sa.apps.loaded.jsonViewer.settings.cmd.scrollpaneID+'__container').css({
								width : container.offsetWidth,
								height : container.offsetHeight
							});
							
							
							if (settings && settings.callHM !== false) setTimeout (function() {
								sa.hms.tools.onResize (sa.apps.loaded.jsonViewer.settings.cmd.id);
							}, 740);
						}
					}
				}
			}
		},
	
		validateJSON : function (val) {
            debugger;
		
			if (typeof val!=='string') return false;
			
			// check for http:// links and translate them onto the URL as # 
			//	; mind the History object's use of # in older browsers 
			//  (altho the new browser compatibility check in saCore.source.js prevents that being a problem, so ehm, never mind, this month)
			if (val.indexOf('http://')!==-1) {
				return sa.apps.loaded.jsonViewer.curl ({
					url : val/*,
					done : sa.m.traceFunction (function(cmd) {
						sa.apps.loaded.jsonViewer.validateJSON_text(cmd.result);
					})*/
				});
			} else {
				return sa.apps.loaded.jsonViewer.validateJSON_text (val);
			}
		},
		
		curl : function (cmd) {
			if (true) {
				var ac = {
					url : cmd.url,
					type : 'GET',
					success : sa.m.traceFunction(function(JSONstring) {
					//debugger;
						var msg = sa.m.dateForLog()+ ' : Received all content at <a href="' + cmd.url+'" class="nomod" target="_blank">'+cmd.url+'</a>';
						sa.statusbar.update (msg);
						debugger;
						sa.apps.loaded.jsonViewer.validateJSON_text (JSONstring);
					})
				};
				jQuery.ajax(ac);
			} /*else {
				var iframeCurl = document.createElement('iframe');
				cmd.iframeCurl = iframeCurl;
				jQuery('body').append(iframeCurl);
				jQuery(iframeCurl).css({position:'absolute',opacity:0.01,width:10,height:10,overflow:'hidden',zIndex:-1});
				iframeCurl.onload = sa.m.traceFunction(function () { 
					//debugger;
					sa.apps.loaded.jsonViewer.curl_onload (cmd);
				});
				//debugger;
				
				
				cmd.msg = 'Now loading ' + cmd.url;
				sa.statusbar.update (cmd.msg);
				
				iframeCurl.src = cmd.url;
			}*/
		},
		/*
		curl_onload : function (cmd) {
			cmd.result = cmd.iframeCurl.contentWindow.document.body.innerHTML;
			if (typeof cmd.done==='function') cmd.done(cmd);
			
		},*/
		
		validateJSON_text : function (val) {
			if (typeof val!=='string') return false;
			sa.m.waitForCondition ('window.parent.window.sa.apps.tools.settings.db loaded', function () {
				return (
					//typeof window.parent.window.sa.apps.tools.settings.db !== 'undefined'
					sa.m.settings.initialized.site 
					//&& !sa.desktop.settings.animating
				);
					
			}, function () {
				//var db = eval('('+val+')');//window.parent.window.sa.apps.search.settings.db;
				var d = sa.json.decode.small (val, undefined, 'sa.apps.loaded.jsonViewer.validateJSON', undefined, function (d) {
					var opt = { 
						htmlID : 'jsonViewer', 
						fastInit : true,
						opacity : sa.m.globals.jsonViewer.defaultOpacity,
						themeName : 'saColorgradientScheme_BlueToNavyBG_white' // doesnt actually work yet (to put that setting here. for now use jsonViewer's (jv.source.js) hmDefault setting.
					}
					jQuery('#siteContent').css({
						height : '100%',
						width : '100%'
					});
					jQuery('#jsonViewer').css({
						height : '98%',
						width : '98%'
					});
					sa.apps.loaded.jsonViewer.settings.cmd = hm (d, 'This is valid JSON data', opt );
					setTimeout (function(){
                        sa.sp.containerSizeChanged (jQuery('#siteContent__scrollpane')[0], true);
					},5);
				}, function (errMsg) {
					jQuery('#jsonViewer').html (errMsg);
				});
			}, 400);		
		}
	};
	jQuery(document).ready(function(){
		sa.m.waitForCondition ('window.parent.window.sa.m.settings.initialized.site', function () {
			return (
				//window.parent.window.sa.m.settings.initialized.site===true
				//&& !window.parent.window.sa.desktop.settings.animating
				sa.m.settings.initialized.site===true
				&& !sa.desktop.settings.animating
			);
		}, function () {
			var 
			pw = window.parent.window,
			pd = window.parent.document,
			psc = pw.sa.s.c,
			psa = pw.sa;
			
			//psa.apps.tools.settings.appContentCode = sa.apps.loaded.jsonViewer;
			//psa.apps.tools.onPage('json');
			//setTimeout (function() {
				sa.apps.tools.settings.appContentCode = sa.apps.loaded.jsonViewer;
				//debugger;
				sa.apps.tools.onPage('json');
			//}, 4000);
		}, 500);
	});
	
</script>


