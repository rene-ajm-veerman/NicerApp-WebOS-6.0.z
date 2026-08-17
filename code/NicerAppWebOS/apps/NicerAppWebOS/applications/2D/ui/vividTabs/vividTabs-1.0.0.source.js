seductiveapps.tabs = seductiveapps.vividTabs = {
	about : {
		whatsThis : 'seductiveapps.tabs = seductiveapps.vividTabs = A HTML "tabs" component in javascript without dependencies that uses sa.vividButton to switch tabs with. Also supports PNG backgrounds.',
		copyright : '(c) (r) 2011-2015 by [the owner of seductiveapps.com] <seductiveapps@gmail.com>',
		license : 'http://seductiveapps.com/LICENSE.txt',
		noWarranty : 'NO WARRANTY EXPRESSED OR IMPLIED. USE ONLY AT YOUR OWN RISK.',
		version : '1.0.0',
		firstReleased : '2011 June',
		lastUpdated : '2015 June 4'
	},
	
	settings : {},

	init : function (componentInstanceHTMLelement, initID) {
		if (
			document.getElementById(componentInstanceHTMLelement.id+'_-_top_img')
			|| componentInstanceHTMLelement.style.display==='none'
		) {
			sa.m.log ('sa.vividTabs.init(): for initID='+initID+'; NOT initializing element with id="'+componentInstanceHTMLelement.id+'" and class="'+componentInstanceHTMLelement.className+'" because .style.display==="none" or because element has already been initialized.');
			sa.vcc.componentFullyInitialized (initID, componentInstanceHTMLelement.id);		
		} else {
			sa.m.log ('sa.vividTabs.init(): for initID='+initID+'; Initializing element with id="'+componentInstanceHTMLelement.id+'" and class="'+componentInstanceHTMLelement.className+'".');
			sa.tabs.initTabs (componentInstanceHTMLelement.id, initID);
		}
	},
	
	initTabs : function (ciid, initID) {
		var ci = componentInstanceHTMLelement = document.getElementById(ciid);
		if (!ci) return false;
		if (sa.vcc.newInstanceNeeded(ci)) sa.vcc.newInstance (ci, initID);
		var s = sa.vcc.settings[ciid];
		var t = sa.vcc.getVividTheme (s.themeName); 
        if (!t) {
            debugger;
            return false;
        }
		s = sa.vcc.newInstanceThemeOverrides (s, t);

		// in tabs, items are pages

		ci.style.position = 'relative';
		ci.style.display = 'block';

		var menu = document.createElement('DIV');
		s.menu = menu;
		menu.style.height = '48px';
		var html = '';
		if (s.theme.menuBackgroundImage) {
			html+= '<img id="' + ciid + '_-_top_img" src="' + s.theme.baseURL + s.theme.menuBackgroundImage + '" style="position:absolute;width:100%;height:48px;"/>';	
		} else {
			html+= '<img id="' + ciid + '_-_top_img" src="data:image/png;base64,R0lGODlhFAAUAIAAAP///wAAACH5BAEAAAAALAAAAAAUABQAAAIRhI+py+0Po5y02ouz3rz7rxUAOw==" style="position:absolute;width:100%;height:48px;"/>';	
		};

	
		html += '<table style="overflow:visible;padding-left:50px;">';
		html += '<tr>';

		var ul = ci.children[0];
		ul.style.display='none';
		for (var i=0; i<ul.children.length; i++) {
			//fetch the pages from the DOM
			var it = ul.children[i];
			var item = {
				gotoTab : it.children[0].href.replace(/.*#/,'').replace('\');', ''),
				label : it.children[0].innerHTML
			};
			s.items[s.items.length] = item;
			
			var tab = document.getElementById(item.gotoTab);
			if (tab) {
				tab.style.display = (i==0 ? 'block' : 'none');
				tab.style.position = 'relative';
			}
			
			//create vividButtons for each page, to be placed at the top of the container.
			html += '<td id="' + ciid + '_-_control_-_' + item.gotoTab + '" style="padding-bottom:3px;vertical-align:bottom">';
			html += '<div id="' + ciid + '_-_button_-_' + item.gotoTab + '" class="vividButton ' + s.theme.menuItemTheme + '" style="position:relative;">';
			html += '<a href="javascript:sa.tabs.gotoTab(\'' + ciid + '\',\'' + i + '\');">' + item.label + '</a>';
			html += '</div></td>';
		};
		
		html += '</tr>';
		html += '</table>';
		menu.innerHTML = html;
		//menu.style.backgroundImage = 'url()';
		ci.insertBefore (menu, ci.children[1]);
		for (var i in s.items) {
			var item = s.items[i];
			var el = document.getElementById(ciid+'_-_button_-_'+item.gotoTab);
			//sa.vcc.inits[s.initID].sr[el.id] = el;
			sa.button.init (el, s.initID);
		};
		
		
		var scrollpane = document.createElement('DIV');
		s.scrollpane = scrollpane;
		scrollpane.id = ciid + '__scrollpane';
		scrollpane.className = 'vividScrollpane vividTheme__scroll_black';
		scrollpane.style.width = 'auto';
		scrollpane.style.height = (ci.offsetHeight - menu.offsetHeight - 10) + 'px';
		//scrollpane.style.margin = '10px';
		ci.insertBefore (scrollpane, ci.children[2]);

		for (var i=0; i<ci.children.length; i++) {
			var ch = ci.children[i];
			if (ch.className.match('tabPage')) {
			
				// create a margin/padding for .tabPage content without messing up width=100% for that content
				var div = document.createElement ('div');
				div.innerHTML = ch.innerHTML;
				div.style.position = 'absolute';
				div.style.left = '0%';
				div.style.width = '100%';
				div.style.top = '0%';
				div.style.height = '100%';
				ch.innerHTML = '';
				ch.appendChild (div);
				
				scrollpane.appendChild (ch.parentNode.removeChild(ch));
				i--;
			}
		};
		
		sa.vcc.inits[s.initID].sr[scrollpane.id] = scrollpane;
		sa.sp.init (scrollpane, s.initID);
		jQuery(window).bind ('resize', function () {
			//jQuery(scrollpane).css ({height : jQuery('#'+scrollpane.id+'__container').height(), width : jQuery('#'+scrollpane.id+'__container').width ()});
			//jQuery(scrollpane).css ({height : jQuery(ci).height(), width : jQuery(ci).width ()});
			//sa.sp.containerSizeChanged(scrollpane);
		});

		var img = document.createElement ('IMG');
		s.img = img;
        debugger;
		if (s.theme.contentBackgroundImage) {
			img.src = s.theme.baseURL + s.theme.contentBackgroundImage;	
		} else {
			img.src = "data:image/png;base64,R0lGODlhFAAUAIAAAP///wAAACH5BAEAAAAALAAAAAAUABQAAAIRhI+py+0Po5y02ouz3rz7rxUAOw==";
		};		
		img.style.position = 'absolute';
		img.style.width = '100%';
		img.style.height = (ci.offsetHeight - menu.offsetHeight) + 'px';
		ci.insertBefore (img, ci.children[2]);
		img.className = 'vividTabs_background';
		
		
		setTimeout(function() {
			//jQuery(scrollpane).css ({height : jQuery('#'+scrollpane.id+'__container').height(), width : jQuery('#'+scrollpane.id+'__container').width ()});
			sa.tabs.gotoTab (ciid, 0);
			ci.style.display = 'block';
			sa.vcc.componentFullyInitialized (s.initID, ciid);		
		}, 500);
	},
	
	onresize : function (ci) {
		//debugger;
		var css = {height : jQuery(ci).height() - 50, width : jQuery(ci).width()-35};
		//debugger;
		jQuery(ci+'__scrollpane, '+ci+'__scrollpane__container, .vividTabs_background').css (css);
		
		//debugger;
		//jQuery('.tabPage .vsp_container', jQuery(ci+'__scrollpane__container')).css({top:0}); // does not work, overridden by getComputed() in vividControls-1.0.0.source.js
		
		jQuery('.tabPage', jQuery(ci+'__scrollpane')).css (css);
		sa.sp.containerSizeChanged(jQuery(ci+'__scrollpane')[0]);
		// {-TODO-} must cascade into resize of dialog..
	},
	
	gotoTab : function (ciid, itemIndex) {
        debugger;
		var ci = componentInstanceHTMLelement = document.getElementById(ciid);
		var s = sa.vcc.settings[ciid];
		var item = s.items[itemIndex];
		var tab = document.getElementById(item.gotoTab);
		for (var i in s.items) {
			var it = s.items[parseInt(i)];
            if (it.gotoTab) {
                // make the page visible
                var itPage = document.getElementById(it.gotoTab);
                if (itPage && it.gotoTab!==item.gotoTab) sa.vcc.fadeOut (itPage.id, 500);
                
                // update it's menu button's state
                var itMenuButton = document.getElementById(ciid+'_-_button_-_'+it.gotoTab);
                var itMenuButtonItem = document.getElementById(ciid+'_-_button_-_'+it.gotoTab+'__item__0');
                sa.vcc.changeState (itMenuButton, itMenuButtonItem, (it.gotoTab==item.gotoTab ? 'selected' : 'normal'));
            } else {
                var itMenuButton = document.getElementById(ciid+'_-_button_-_'+i);
                var itMenuButtonItem = document.getElementById(ciid+'_-_button_-_'+i+'__item__0');
                sa.vcc.changeState (itMenuButton, itMenuButtonItem, (i==item.gotoTab ? 'selected' : 'normal'));
            };

		};
		setTimeout (function () {
			s.scrollpane.scrollTop = 0;
			sa.vcc.fadeIn (tab.id, 500);
			setTimeout (function () {
				sa.sp.containerSizeChanged (s.scrollpane);
				if (typeof sa.vividTabs.settings.onTabChange=='function') {
					sa.vividTabs.settings.onTabChange();
				};
			}, 500);
		}, 500);
	},
	
	containerSizeChanged : function (cihe) {
		var ci = componentInstanceHTMLelement = document.getElementById(cihe.id);
		var s = sa.vcc.settings[cihe.id];
		s.img.style.height = (ci.offsetHeight - s.menu.offsetHeight)	+'px';
	}
};
