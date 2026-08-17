<div id="jsonViewer">
	<h1>Google Search</h1>
	<p class="important">
	experimental
	</p>
</div>
<script type="text/javascript">
	//var nicerapp_appCode_jsonViewer = {
	na.apps.loaded.googleSearch = {
		settings : {
			loadedIn : {
				'#siteContent' : {
					settings : {
						initialized : true,
						ready : true
					},
					onload : function (settings) {
                        jQuery('#siteContent').addClass('saContent');
                        na.vcc.settings['siteContent'].containsIframe = true;
					},
					ondestroy : function (settings) {
                        jQuery('#siteContent').removeClass ('saContent');
                        na.vcc.settings['siteContent'].containsIframe = false;
					},
					onresize : function (settings) {
						//na.desktop.resize();
						
						//jQuery('#inputJSON').css({height:50,position:'relative'});
						//see the appCode that goes along with this 
						
						var container = jQuery('#siteContent__contentDimensions')[0];
						
						if (na.apps.loaded.googleSearch.settings.cmd) {
							jQuery('#siteContent__scrollpane__container, #siteContent__scrollpane, #siteContent,  #'+na.apps.loaded.googleSearch.settings.cmd.scrollpaneID+'__container').css({
								width : container.offsetWidth,
								height : container.offsetHeight
							});
							
							
							if (settings && settings.callHM !== false) setTimeout (function() {
								na.hms.tools.onResize (na.apps.loaded.googleSearch.settings.cmd.id);
							}, 740);
						}
					}
				}
			}
		},
	
		getSearchResults : function (val) {
		
			if (typeof val!=='string') return false;
			
			// check for http:// links and translate them onto the URL as # 
			//	; mind the History object's use of # in older browsers 
			//  (altho the new browser compatibility check in saCore.source.js prevents that being a problem, so ehm, never mind, this month)
			return na.apps.loaded.googleSearch.getSearchResults_iframe (val);
			/*
			return na.apps.loaded.googleSearch.curl ({
					query : val/*,
					done : na.m.traceFunction (function(cmd) {
						na.apps.loaded.googleSearch.getSearchResults_text(cmd.result);
					})* /
				});
			if (val.indexOf('http://')!==-1) {
				return na.apps.loaded.googleSearch.curl ({
					query : val/*,
					done : na.m.traceFunction (function(cmd) {
						na.apps.loaded.googleSearch.getSearchResults_text(cmd.result);
					})* /
				});
			} else {
				return na.apps.loaded.googleSearch.getSearchResults_text (val);
			}*/
		},
		
		curl : function (cmd) {
			if (true) {
				var ac = {
					url : na.m.globals.urls.framework+'/apps/NicerAppWebOS/googleSearch/appLogic/ajax_googleSearch.php?q='+cmd.query,
					type : 'GET',
					success : na.m.traceFunction(function(JSONstring) {
					//debugger;
						var msg = na.m.dateForLog()+ ' : Received all content at <a href="' + cmd.url+'" class="nomod" target="_blank">'+cmd.url+'</a>';
						na.statusbar.update (msg);
						debugger;
						na.apps.loaded.googleSearch.getSearchResults_text (JSONstring);
					})
				};
				jQuery.ajax(ac);
			} /*else {
				var iframeCurl = document.createElement('iframe');
				cmd.iframeCurl = iframeCurl;
				jQuery('body').append(iframeCurl);
				jQuery(iframeCurl).css({position:'absolute',opacity:0.01,width:10,height:10,overflow:'hidden',zIndex:-1});
				iframeCurl.onload = na.m.traceFunction(function () { 
					//debugger;
					na.apps.loaded.googleSearch.curl_onload (cmd);
				});
				//debugger;
				
				
				cmd.msg = 'Now loading ' + cmd.url;
				na.statusbar.update (cmd.msg);
				
				iframeCurl.src = cmd.url;
			}*/
		},
		/*
		curl_onload : function (cmd) {
			cmd.result = cmd.iframeCurl.contentWindow.document.body.innerHTML;
			if (typeof cmd.done==='function') cmd.done(cmd);
			
		},*/

		getSearchResults_iframe : function (query) {
            var 
            url = na.m.globals.urls.framework+'/apps/NicerAppWebOS/googleSearch/appLogic/ajax_googleSearch.php?q='+query,
            html = '<iframe id="googleSearchIFRAME" src="'+url+'" style="width:100%;height:100%;"></iframe>';
            
            jQuery('#siteContent').html(html);
		},
		
		getSearchResults_text : function (val) {
			if (typeof val!=='string') return false;
			na.m.waitForCondition ('window.parent.window.na.apps.googleSearch.settings.db loaded', function () {
				return (
					//typeof window.parent.window.na.apps.googleSearch.settings.db !== 'undefined'
					na.m.settings.initialized.site 
					//&& !na.desktop.settings.animating
				);
					
			}, function () {
                jQuery('#siteContent').html(val);
					setTimeout (function(){
                        na.sp.containerSizeChanged (jQuery('#siteContent__scrollpane')[0], true);
					},5);
			}, 400);		
		}
	};
	jQuery(document).ready(function(){
		na.m.waitForCondition ('window.parent.window.na.m.settings.initialized.site', function () {
			return (
				//window.parent.window.na.m.settings.initialized.site===true
				//&& !window.parent.window.na.desktop.settings.animating
				na.m.settings.initialized.site===true
				&& !na.desktop.settings.animating
			);
		}, function () {
			var 
			pw = window.parent.window,
			pd = window.parent.document,
			psc = pw.na.s.c,
			psa = pw.sa;
			
			//pna.apps.googleSearch.settings.appContentCode = na.apps.loaded.googleSearch;
			//pna.apps.googleSearch.onPage('json');
			//setTimeout (function() {
				na.apps.googleSearch.settings.appContentCode = na.apps.loaded.googleSearch;
				//debugger;
				na.apps.googleSearch.onPage('googleSearch');
			//}, 4000);
		}, 500);
	});
	
</script>


