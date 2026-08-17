sa.apps.tools = {
	settings : {
		current :{}
	},
	
	init : function (settings) {
		sa.apps.tools.settings = sa.m.negotiateOptions (sa.apps.tools.settings, settings);
		//sa.apps.tools.loadDB();
	},
	
	loadDB : function () {
		var ajaxCommand = {
			type : 'GET',
			url : sa.m.globals.urls.app + 'apps/search/appLogic/boot_treeDB.php',
			success : sa.m.traceFunction(function (retData, ts2) {
				sa.json.decode.small (retData, null, 'sa.apps.tools.loadDB::ajaxCommand.success()', null, function (data) {
					sa.apps.tools.settings.db = data;
				});
			})
		};
		jQuery.ajax (ajaxCommand);
	},
	
	onPage : function (page, callback) {
		//if (sa.apps.tools.settings.current.page !== page) {
			sa.apps.tools.settings.current.page = page;
			var css = {
				height : 150
			};
			switch (page) {
				case 'json' :
					sa.apps.settings.usingToolbar = true;
					sa.apps.tools.createToolbar();
					sa.s.c.setVisible ([{element:'#siteToolbar',visible:true}], true, sa.m.traceFunction(function(){sa.s.c.resizeToolbar(css);}));//,undefined, function() { sa.apps.tools.onPageCallback(callback) } );
					break;
				default:
					//sa.s.c.removeToolbar();
					sa.s.c.setVisible ([{element:'#siteToolbar',visible:false}], true, sa.m.traceFunction(function(){sa.s.c.resizeToolbar(css);}));//,undefined, function() { sa.apps.tools.onPageCallback(callback) } );
					break;
			}
		//}
	},
	
	onresize : function () {
		var page = sa.apps.tools.settings.current.page;
		switch (page) {
			case 'json':
				sa.apps.tools.resizeToolbar();
				break;
		}
	},
	
	resizeToolbar : function () {
		var 
		jQuerysiteToolbar = jQuery('#siteToolbar'),
		dialogBorderRadius = 15,
		t = 15,
		l = 15,
		w = jQuerysiteToolbar.width(),
		h = jQuerysiteToolbar.height();
		
		//debugger;
		
		var
		iw = w,
		ih = h - 40;/*vividButton in tools dialog*/
		// centers vertically : 
		//  t = Math.round((jQuerysiteToolbar.height() - ih)/2),
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
        debugger;
		var 
		h = window.location.href.replace(/.*url'base64/,'').replace("')#",'').replace("')",''),
		h = atob(h);
		
		var
		url = (
			typeof h==='string'
			&& (
                h.indexOf('http://')!==-1
                || h.indexOf('https://')!==-1
            )
			? h
			: sa.m.globals.urls.framework+'/ui/jsonViewer/jsonViewer_sample_data.json.php?url=jsonViewer_sample_data.json'
            //http://seductiveapps.com/seductiveapps/ui/jsonViewer/jsonViewer_sample_data.json.php?url=jsonViewer_sample_data.json
		);
		delete sa.vcc.settings['btnFormSearchSubmit'];
		var
		html = 
			//'<div id="toolbar" style="width:100%;height:100%;opacity:0.01">'
			'<textarea id="inputJSON" class="" type="text" wrap="hard" style="z-index : 30000">'+url+'</textarea><br/>'
			//+'<div id="btnFormSearchSubmit" class="vividButton vividTheme__menu_006" style="z-index:20100"><a href="javascript:sa.apps.tools.validateJSON(jQuery(\'#inputJSON\').val());">Validate</a></div>';
            +'<div id="btnFormSearchSubmit" class="vividButton vividTheme__menu_006" style="z-index:20100"><a href="javascript:sa.apps.tools.loadCurrent(\''+h+'\');">Validate</a></div>';
			//+'</div>';
		sa.s.c.createToolbar (html, {height:150,inputHeight:70}, '#inputJSON');
        /*setTimeout (function() {
            sa.apps.tools.validateJSON(h);
        }, 500);*/
	},
    
    loadCurrent : function (h) {
        var
        url2 = sa.m.globals.urls.app + '/jsonViewer(url\'base64'+btoa(jQuery('#inputJSON').val())+'\')';
        if (window.location.href!==url2) {
            window.History.pushState (null, h, url2);
            var durl = jQuery('#inputJSON').val();
            setTimeout (function () {
                sa.apps.tools.validateJSON(durl);
            }, 5000);
        } else {
            sa.apps.tools.validateJSON(jQuery('#inputJSON').val());
        }
    },
	
	validateJSON : function (val) {
/*		if (false && val.indexOf('http://')!==-1) {
			var addressURLencoded = jQuery('#inputJSON').val();
			addressURLencoded = addressURLencoded.replace('http://','');
			//window.location.hash = '#'+addressURLencoded;
			
			//History.Adapter.unbind('hashchange', History.onHashChange);
			//window.location.hash = '#'+addressURLencoded;
			//!!! sa.apps.tools.settings.contentPage === iframe #seductiveapps_appContent
			sa.apps.tools.settings.contentPage.validateJSON (val);
//			History.pushState ({val:val}, addressURLencoded, '#'+addressURLencoded);
			//jQuery(window).bind('hashchange', History.hashchange);
		} else {
			sa.apps.tools.settings.contentPage.validateJSON (val);
		}
*/
		// !! sa.apps.tools.settings.appContentCode === iframe #seductiveapps_appContent
		//debugger;
		sa.apps.tools.settings.appContentCode.validateJSON (val);
	},
	
	/*validateJSON : function (val) {
		debugger;
		if (typeof val!=='string') return false;
		sa.m.waitForCondition ('window.parent.window.sa.apps.tools.settings.db loaded', function () {
			return (
				typeof window.parent.window.sa.apps.tools.settings.db !== 'undefined'
				&& !sa.desktop.settings.animating
			);
				
		}, function () {
			var db = eval('('+val+')');//window.parent.window.sa.apps.search.settings.db;
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
			url : sa.m.globals.urls.app + 'apps/search/appLogic/ajax_search.php',
			data : {
				searchterm : searchterm
			},
			success : sa.m.traceFunction(sa.apps.tools.process.forTerm)
		};
		debugger;
		jQuery.ajax (ajaxCommand);
		
	},*/
	
	process : {
		forTerm : function (retData, ts2) {
			sa.json.decode.small (retData, null, 'sa.apps.tools.loadDB::ajaxCommand.success()', null, function (data) {
				sa.apps.tools.settings.db = data;
			});
		}
	}
};
jQuery(document).ready(function () {
	if (sa.apps && sa.apps.tools) sa.apps.tools.init();
});
