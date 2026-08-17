na.apps.googleSearch = {
	settings : {
		current :{}
	},
	
	init : function (settings) {
		na.apps.googleSearch.settings = na.m.negotiateOptions (na.apps.googleSearch.settings, settings);
		//na.apps.googleSearch.loadDB();
	},
	
	loadDB : function () {
		var ajaxCommand = {
			type : 'GET',
			url : na.m.globals.urls.app + 'apps/search/appLogic/boot_treeDB.php',
			success : na.m.traceFunction(function (retData, ts2) {
				na.json.decode.small (retData, null, 'na.apps.googleSearch.loadDB::ajaxCommand.success()', null, function (data) {
					na.apps.googleSearch.settings.db = data;
				});
			})
		};
		jQuery.ajax (ajaxCommand);
	},
	
	onPage : function (page, callback) {
		//if (na.apps.googleSearch.settings.page !== page) {
			na.apps.googleSearch.settings.page = page;
			var css = {
				height : 150
			};
			switch (page) {
				case 'googleSearch' :
					na.apps.settings.usingToolbar = true;
					na.apps.googleSearch.createToolbar();
					na.s.c.setVisible ([{element:'#siteTools',visible:true}], true, na.m.traceFunction(function(){na.s.c.resizeToolbar(css);}));//,undefined, function() { na.apps.googleSearch.onPageCallback(callback) } );
					break;
				default:
					//na.s.c.removeToolbar();
					na.s.c.setVisible ([{element:'#siteTools',visible:false}], true, na.m.traceFunction(function(){na.s.c.resizeToolbar(css);}));//,undefined, function() { na.apps.googleSearch.onPageCallback(callback) } );
					break;
			}
		//}
	},
	
	onresize : function () {
		var page = na.apps.googleSearch.settings.page;
		switch (page) {
			case 'json':
				na.apps.googleSearch.resizeToolbar();
				break;
		}
	},
	
	resizeToolbar : function () {
		var 
		jQuerysiteTools = jQuery('#siteTools'),
		dialogBorderRadius = 15,
		t = 15,
		l = 15,
		w = jQuerysiteTools.width(),
		h = jQuerysiteTools.height();
		
		//debugger;
		
		var
		iw = w,
		ih = h - 40;/*vividButton in tools dialog*/
		// centers vertically : 
		//  t = Math.round((jQuerysiteTools.height() - ih)/2),
		jQuery('#inputJSON').css({opacity:1, zIndex:10200, position:'absolute', backgroundColor:'transparent'});
		jQuery('#bgToolbar1').css({opacity:0.2, zIndex:10120, position:'absolute', backgroundColor:'white'});
		jQuery('#bgToolbar2').css({opacity:0.4, zIndex:10119, position:'absolute', backgroundColor:'white'});
		//jQuery('#bgToolbar3').css({opacity:0.1, zIndex:10118, position:'absolute', backgroundColor:'white'});
		//jQuery('#bgToolbar4').css({opacity:0.1, zIndex:10117, position:'absolute', backgroundColor:'white'});
		
		var
		borderPadding = 3;
		jQuery('#bgToolbar1').css({top:t+(borderPadding*1), left:(borderPadding*1), width:iw-(borderPadding*1*2), height:ih-(borderPadding*1*2) });
		jQuery('#bgToolbar2').css({top:t+(borderPadding*2), left:(borderPadding*2), width:iw-(borderPadding*2*2), height:ih-(borderPadding*2*2) });
		//jQuery('#bgToolbar3').css({top:t+borderPadding, left:borderPadding, width:iw-(borderPadding*2), height:ih-(borderPadding*2) });
		//jQuery('#bgToolbar4').css({top:t+borderPadding, left:borderPadding, width:iw-(borderPadding*2), height:ih-(borderPadding*2) });
		jQuery('#inputJSON').css({top:t+(borderPadding*3), left:(borderPadding*3), width:iw-(borderPadding*3*2), height:ih-(borderPadding*3*2) });

		jQuery('#btnFormSearchSubmit').css({position:'absolute',top : ih+10, left : 20});
	},
	
	createToolbar : function () {
        var 
		h = window.location.href.replace(/.*query'/,'').replace("')#",'').replace("')",'');
		
		
		var
		url = h;/*(
			typeof h==='string'
			&& (
                h.indexOf('http://')!==-1
                || h.indexOf('https://')!==-1
            )
			? h
			: na.m.globals.urls.framework+'/ui/jsonViewer/jsonViewer_sample_data.json.php?url=jsonViewer_sample_data.json'
            //http://nicer.app/NicerAppWebOS/ui/jsonViewer/jsonViewer_sample_data.json.php?url=jsonViewer_sample_data.json
		);*/
		delete na.vcc.settings['btnFormSearchSubmit'];
		var
		html = 
			//'<div id="toolbar" style="width:100%;height:100%;opacity:0.01">'
			'<textarea id="inputJSON" class="" type="text" wrap="hard" style="z-index : 30000">'+url+'</textarea><br/>'
			//+'<div id="btnFormSearchSubmit" class="vividButton vividTheme__menu_006" style="z-index:20100"><a href="javascript:na.apps.googleSearch.getSearchResults(jQuery(\'#inputJSON\').val());">Validate</a></div>';
            +'<div id="btnFormSearchSubmit" class="vividButton vividTheme__menu_006" style="z-index:20100"><a href="javascript:na.apps.googleSearch.loadCurrent(\''+h+'\');">Validate</a></div>';
			//+'</div>';
		na.s.c.createToolbar (html, {height:150,inputHeight:70}, '#inputJSON');
        /*setTimeout (function() {
            na.apps.googleSearch.getSearchResults(h);
        }, 500);*/
	},
    
    loadCurrent : function (h) {
        var
        url2 = na.m.globals.urls.app + '/googleSearch(query\''+jQuery('#inputJSON').val()+'\')';
        if (window.location.href!==url2) {
            window.History.pushState (null, h, url2);
            var durl = jQuery('#inputJSON').val();
            setTimeout (function () {
                na.apps.googleSearch.getSearchResults(durl);
            }, 5000);
        } else {
            na.apps.googleSearch.getSearchResults(jQuery('#inputJSON').val());
        }
    },
	
	getSearchResults : function (val) {
/*		if (false && val.indexOf('http://')!==-1) {
			var addressURLencoded = jQuery('#inputJSON').val();
			addressURLencoded = addressURLencoded.replace('http://','');
			//window.location.hash = '#'+addressURLencoded;
			
			//History.Adapter.unbind('hashchange', History.onHashChange);
			//window.location.hash = '#'+addressURLencoded;
			//!!! na.apps.googleSearch.settings.contentPage === iframe #nicerapp_appContent
			na.apps.googleSearch.settings.contentPage.getSearchResults (val);
//			History.pushState ({val:val}, addressURLencoded, '#'+addressURLencoded);
			//jQuery(window).bind('hashchange', History.hashchange);
		} else {
			na.apps.googleSearch.settings.contentPage.getSearchResults (val);
		}
*/
		// !! na.apps.googleSearch.settings.appContentCode === iframe #nicerapp_appContent
		//debugger;
		na.apps.googleSearch.settings.appContentCode.getSearchResults (val);
	},
	
	/*getSearchResults : function (val) {
		debugger;
		if (typeof val!=='string') return false;
		na.m.waitForCondition ('window.parent.window.na.apps.googleSearch.settings.db loaded', function () {
			return (
				typeof window.parent.window.na.apps.googleSearch.settings.db !== 'undefined'
				&& !na.desktop.settings.animating
			);
				
		}, function () {
			var db = eval('('+val+')');//window.parent.window.na.apps.search.settings.db;
			var opt = { 
				htmlID : 'yum', 
				opacity : 0.65 
			}
			hm (db, 'JSON input', opt );
		}, 400);		
	},

	forTerm : function (searchterm) { // from apps/search!!!
		var ajaxCommand = {
			type : 'GET',
			url : na.m.globals.urls.app + 'apps/search/appLogic/ajax_search.php',
			data : {
				searchterm : searchterm
			},
			success : na.m.traceFunction(na.apps.googleSearch.process.forTerm)
		};
		debugger;
		jQuery.ajax (ajaxCommand);
		
	},*/
	
	process : {
		forTerm : function (retData, ts2) {
			na.json.decode.small (retData, null, 'na.apps.googleSearch.loadDB::ajaxCommand.success()', null, function (data) {
				na.apps.googleSearch.settings.db = data;
			});
		}
	}
};
jQuery(document).ready(function () {
	if (na.apps && na.apps.googleSearch) na.apps.googleSearch.init();
});
