seductiveapps.button = seductiveapps.vividButton = {
	about : {
		whatsThis : 'seductiveapps.button = seductiveapps.vividButton = A PNG-animated-sprite button component in javascript without dependencies',
		copyright : '(c) (r) 2011-2013 by [the owner of seductiveapps.com] <seductiveapps@gmail.com>',
		license : 'http://seductiveapps.com/seductiveapps/license.txt',
		noWarranty : 'NO WARRANTY EXPRESSED OR IMPLIED. USE ONLY AT YOUR OWN RISK.',
		version : '2.0.0',
		firstReleased : '2008',
		lastUpdated : '2017 November'
	},
	settings : {
		initID : null // used to keep track of when to call the "callback" function for sa.vividControls.init(rootElement, callback)
	},

	init : function (componentInstanceHTMLelement, initID) {
		if (
			document.getElementById(componentInstanceHTMLelement.id+'__img')
			|| componentInstanceHTMLelement.style.display=='none'
		) {
			sa.m.log (2, 'sa.vividButton.init(): for initID='+initID+'; NOT initializing element with id="'+componentInstanceHTMLelement.id+'" and class="'+componentInstanceHTMLelement.className+'", because .style.display=="none" or because element has already been initialized.');
			sa.vcc.componentFullyInitialized (initID, componentInstanceHTMLelement.id);
			return false;
		} else {
			sa.m.log (2, 'sa.vividButton.init(): for initID='+initID+'; Initializing element with id="'+componentInstanceHTMLelement.id+'" and class="'+componentInstanceHTMLelement.className+'".');
		};

		sa.button.loadButtonGraphics (componentInstanceHTMLelement, initID);
	},
	
	loadButtonGraphics : function (componentInstanceHTMLelement, initID) {
		if (sa.vcc.newInstanceNeeded(componentInstanceHTMLelement)) sa.vcc.newInstance (componentInstanceHTMLelement, initID);
		var s = sa.vcc.settings[componentInstanceHTMLelement.id];
		s.imageLoaded = false;
		var t = sa.vcc.getVividTheme (s.themeName); 
		s = sa.vcc.newInstanceThemeOverrides (s, t);
		if (componentInstanceHTMLelement.children[0] && componentInstanceHTMLelement.children[0].className=='vbutton_item') {
			s.partiallyPreInitialized = true;
			var item = componentInstanceHTMLelement.children[0];
			item.id = componentInstanceHTMLelement.id+'__item__0';
			jQuery('.vbutton_img', componentInstanceHTMLelement).each (function() {
				this.id = componentInstanceHTMLelement.id+'__item__0__im' + this.id.substr(this.id.length-2);
			});
			s.buttonText = jQuery('td',componentInstanceHTMLelement).html();
			sa.button.initButton(componentInstanceHTMLelement.id);
		} else if (componentInstanceHTMLelement.children.length==2 && componentInstanceHTMLelement.children[1].tagName.toLowerCase()=='img') {
				s.imageLoaded = false;
				sa.button.initButton(componentInstanceHTMLelement.id);
		} else {
			if (!t.url) alert(t);
			if (typeof sa.tracer!=='undefined') {
				var ua = sa.tracer.findUA(arguments);	
			} else {
				var ua = false;
			};
			
			var img = document.createElement('img');
			//img.src = s.theme.url;
			img.src = 
				t.spriteImage
				&& t.spriteImage.frames
				&& t.spriteImage.frames.url
				&& sa.s.c.settings.online
				? t.spriteImage.frames.url
				: t.url;


			s.buttonText = componentInstanceHTMLelement.innerHTML;
			if (img.complete || img.readyState === 4) {
				sa.button.initButton(componentInstanceHTMLelement.id);
			} else {
				if (false && ua) {
					var html = '<img id="' + componentInstanceHTMLelement.id + '__img" class="vbutton_image" src="' + img.src + '" style="position:absolute;visibility:hidden;width:1px;height:1px;" onload="var t=this;sa.m.reAttachUA('+ua.uaIdx+', function(){sa.button.initButton(\''+componentInstanceHTMLelement.id+'\')})();"/>';
				} else {
					var html = '<img id="' + componentInstanceHTMLelement.id + '__img" class="vbutton_image" src="' + img.src + '" style="position:absolute;visibility:hidden;width:1px;height:1px;" onload="sa.button.initButton(\''+componentInstanceHTMLelement.id+'\');"/>';
				}
				componentInstanceHTMLelement.innerHTML += html;
				//componentInstanceHTMLelement.style.position = 'relative';
				
                                // EXPERIMENTAL :
                                sa.button.initButton(componentInstanceHTMLelement.id);
			}
			
			
		}
	},

	initButton : function (componentInstanceHTMLid) {
		var s = sa.vcc.settings[componentInstanceHTMLid];
		//var t = sa.vcc.getVividTheme (s.themeName); 
		var t = sa.vcc.getVividTheme (s.themeName); 
		s.theme = t;

		var itemID = componentInstanceHTMLid+'__item__0';

			s.imageLoaded = true;

			s.items[0] = {
				theme : s.theme
			};
			var item = s.items[0];
		
		//if (!s.imageLoaded) {
		//debugger;
		//if (!item.img1) {
			s.imageLoaded = true;

			s.items[0] = {
				theme : s.theme
			};
			var item = s.items[0];
			
			var c = sa.m.jqGetElements('#'+componentInstanceHTMLid)[0];

			if (c.children.length==1 || c.children.length==2) {
				//debugger;
				//if (c.children[0].tagName.toUpperCase()=='A') {
					var 
					o = c.children[0].href;
					
					if (!o || o.match('javascript:')) {
						
					} else {
						var
						thisSite = 'http://'+location.hostname+(location.port ? ':'+location.port: '')+'/',
						isCompleteURL = o.match(thisSite);
						baseURL = (
							isCompleteURL 
							? (
								o.match(sa.m.globals.urls.app+'url/')
								? o.replace(sa.m.globals.urls.app+'url/','')
								: o.replace(thisSite, document.location.href.replace(sa.m.globals.urls.app+'url/',''))
							)
							: ''
						),
						pos = baseURL.indexOf('/'),
						baseURL = (pos === -1 ? baseURL : baseURL.substr(0,pos+1)),
						baseURL = baseURL.substr(baseURL.length-1,1)==='/' ? baseURL : baseURL + '/',
						baseURL = baseURL.substr(0,1)==='/' ? baseURL.substr(1) : baseURL,
						newURL = sa.m.globals.urls.subURL + (document.location.href.match('url/')?'url/':'') + baseURL + o.replace(thisSite, '').replace('http://','').replace('https://','');
						isFrame = false,
						p = isIframe ? 'window.parent.window.' :'',

						n = newURL.replace('/.*javascript:/','');
					
						//debugger;
						s.items[0].url = n; // c.children[0].href.replace("'", "\\'");
						s.buttonText = c.children[0].innerHTML;
						if (s.buttonText = 'Skip Ads') debugger;
						jQuery(c).attr('url', item.url);
					}
				/*} else {
			// partiallyPreInitialized
				//debugger;
					s.items[0].url = jQuery(c).attr('url').replace("'", "\\'");
				};*/
			};
			
			c.style.textAlign = 'left';

			item.state = {
				normal : { frame : 0, forward : true },
				hover : { frame : 0, forward : true },
				selected : { frame : 0, forward : true },
				disabled : { frame : 0, forward : true }
			};
			item.stateCurrent = 
				item.statePrevious = 'normal';
			item.stateOld = null;
			item.stateNew = null;
			item.animationTimeout = null;
			
			var events = 
				'onclick="sa.button.onclick(this,event);" '
				+'onmouseover="sa.button.onmouseover(this,event);" '
				+'onmouseout="sa.button.onmouseout(this,event);" ';			

			var div = sa.m.jqGetElements('#'+componentInstanceHTMLid)[0];
			
			var ieFilter = 'filter:progid:DXImageTransform.Microsofs.theme.Alpha(opacity=100);';
			var html = '';
			html += '<div id="' + itemID + '" class="vbutton_item" url="'+item.url+'" style="text-align:left;cursor:pointer;position:absolute; width:' + s.theme.spriteImage.frames.width + 'px; height:' + s.theme.spriteImage.frames.height + 'px;">';
			html += '<table style="cursor:pointer;position:absolute;width:100%;height:100%;z-index:1100"><tr><td id="'+itemID+'__td" style="text-align:center;vertical-align:middle;filter:alpha(opacity=100);z-index:1101;" '+events+'>'+s.buttonText+'</td></tr></table>';
			html += '<div id="' + componentInstanceHTMLid + '__item__0__img1" class="vbutton_img" style="text-align:left;z-index:100;position:absolute; width:' + s.theme.spriteImage.frames.width + 'px; height:' + s.theme.spriteImage.frames.height + 'px; ' + ieFilter + '"></div>';
			html += '<div id="' + componentInstanceHTMLid + '__item__0__img2" class="vbutton_img" style="text-align:left;z-index:101;position:absolute; width:' + s.theme.spriteImage.frames.width + 'px; height:' + s.theme.spriteImage.frames.height + 'px; ' + ieFilter + '"></div>';
			html += '</div>';
			div.innerHTML = html;
			div.style.width = s.theme.spriteImage.frames.width + 'px';
			div.style.height = s.theme.spriteImage.frames.height + 'px';
			div.className += ' vividWidget_initialized';
			item.img1 = sa.m.jqGetElements('#'+componentInstanceHTMLid+'__item__0__img1')[0];
			item.img2 = sa.m.jqGetElements('#'+componentInstanceHTMLid+'__item__0__img2')[0];
			
			
			if (sa.m.globals.urls.upstream) {
				var mediaRoot = sa.m.globals.urls.upstream.media;
			} else {
				var mediaRoot = sa.m.globals.urls.media;
			}		
			s.theme.url = s.theme.url.replace('http://localhost/seductiveapps/siteMedia', mediaRoot);
//debugger;			

			// show first frame of animation, "normal" state
			item.img1.style.backgroundColor = 'transparent';
			item.img1.style.backgroundImage = 'url(' + s.theme.url + ')';
			item.img2.style.backgroundColor = 'transparent';
			item.img2.style.backgroundImage = 'url(' + s.theme.url + ')';
			sa.vcc.setOpacity(item.img2,0);
		//}; 
		
		

		sa.vcc.animateItem (document.getElementById(componentInstanceHTMLid), document.getElementById(itemID), true, true);
		sa.vcc.componentFullyInitialized (s.initID, componentInstanceHTMLid);		
	},
	
	enableButton : function () {
	},
	
	onclick : function (el, evt) {
		var buttonHTMLid = el.id;
		buttonHTMLid = buttonHTMLid.replace(/__.*/,'');
		var s = sa.vcc.settings[buttonHTMLid];
		var idx = sa.vcc.getItemIndex(el);
		
		var el = document.getElementById(buttonHTMLid);
		
		var url = s.buttonText.replace ('<a href="', '').replace(/">.*/, '').replace(/" .*/, '');
		if (url.match('<div')) return false;
		
		//debugger;
		//if (buttonHTMLid=='btnDismiss_cookieWarning') debugger;
		
		// s.items[idx].ignoreClickEvent = true is used to prevent the button doing a "click" after a drag and drop operation (with jQuery sortable)

		debugger;
		if (url && url != "''" && url != '' && !s.items[idx].ignoreClickEvent) {
			if (url.substr(0,11)=='javascript:') {
				sa.m.log (2, 'sa.vividButton : '+buttonHTMLid+' was clicked; executing ' + url.substr(11).replace(/%20/g, ' '));
				eval(url.substr(11).replace(/%20/g, ' '));
				//if (mp3site) setTimeout (mp3site.onWindowResize, 1000);
			} else {
				document.location = url;
			};
		};
	},
	
	onmouseover : function (el, evt) {
		var buttonHTMLid = el.id;
		buttonHTMLid = buttonHTMLid.replace(/__.*/,'');
		var s = sa.vcc.settings[buttonHTMLid];
		var idx = sa.vcc.getItemIndex(el);
		var item = s.items[idx];

		var t = sa.vcc.getVividTheme (s.themeName); 

		if (
			typeof s.theme.state.hover=='object' 
			&& item.stateCurrent!='disabled' 
			&& item.stateCurrent!='selected'
			&& item.stateNew!='disabled'
			&& item.stateNew!='selected'
		) {
			item.statePrevious = item.stateCurrent || item.stateNew;
			sa.vcc.changeState (document.getElementById(buttonHTMLid), el, 'hover');
		}
	},
	
	onmouseout : function (el, evt) {
		var buttonHTMLid = el.id;
		buttonHTMLid = buttonHTMLid.replace(/__.*/,'');
		var s = sa.vcc.settings[buttonHTMLid];
		var idx = sa.vcc.getItemIndex(el);
		var item = s.items[idx];
		if (
			item.stateCurrent!='disabled' 
			&& item.stateCurrent!='selected'
			&& item.stateNew!='disabled'
			&& item.stateNew!='selected'
		) {
			sa.vcc.changeState (document.getElementById(buttonHTMLid), el, item.statePrevious);
		}
	}
};
