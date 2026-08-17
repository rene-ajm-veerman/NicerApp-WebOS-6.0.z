seductiveapps.menu = seductiveapps.vividMenu = {
	about : {
		whatsThis : 'seductiveapps.menu = seductiveapps.vividMenu = A PNG-animated-sprite menu component in javascript without dependencies',
		copyright : '(c) (r) 2011-2014 by [the owner of seductiveapps.com] <info@seductiveapps.com>',
		license : 'http://seductiveapps.com/seductiveapps/license.txt',
		noWarranty : 'NO WARRANTY EXPRESSED OR IMPLIED. USE ONLY AT YOUR OWN RISK.',
		version : '2.1.1',
		dateOfBirth : '2011 June',
		lastUpdated : '2015 June 4'
	},
	globals : {
		// numbers here are times in milliseconds
		menuOpensLapse : 500, 
		menuCloseLapse : 3500, 
		menuImmediateChildCloseLapse : 1000,
		menuWalkbackLapse : 1500,
		debugMouseOpeningAndClosing : true,
		timeToOpenClose : 100,
		keepAllSubMenusOpen : false
	},
	
	init : function (cihe, initID) {
		if (
			//document.getElementById(cihe.id+'__img') // DOH! prevents sa.menu.merge
			cihe.style.display=='none'
		) {
			sa.m.log (2, { msg : 'sa.vividMenu.init(): for initID='+initID+'; NOT initializing element with id="'+cihe.id+'" and class="'+cihe.className+'", because .style.display=="none".' } );
			sa.vcc.componentFullyInitialized (initID, cihe.id);
			return false;
		} else {
			sa.m.log (2, { msg : 'sa.vividMenu.init(): for initID='+initID+'; Initializing element with id="'+cihe.id+'" and class="'+cihe.className+'".' } );
		};
		//sa.menu.loadMenuGraphics (cihe, initID);
		sa.menu.initMenu (cihe.id, initID);
	},
	
	initMenu : function (componentInstanceHTMLid, initID) {
		var cihe = jQuery('#'+componentInstanceHTMLid)[0]
		
		//if (sa.s.c.settings.mayDisplayMenus) jQuery(cihe).css({opacity:0.001});
		sa.menu.preprocess (cihe.id);
		
		if (sa.vcc.newInstanceNeeded(cihe)) {
			sa.vcc.newInstance (cihe, initID);
		}
		var s = sa.vcc.settings[cihe.id];
		//sa.m.log (6, 'vividMenu.loadMenuGraphics 2', cihe, initID, s, sa.vcc.settings);
		var t = sa.vcc.themes[s.themeName];
        if (!t) {
            debugger;
            return false;
        }
		s = sa.vcc.newInstanceThemeOverrides (s, t);
		sa.menu.getitems (jQuery('#'+componentInstanceHTMLid)[0]);
		s.theme = t;
		
		var div = document.getElementById(componentInstanceHTMLid);

		s.imageLoaded = true;
		s.menuOpens = {};
		s.menuOpensTemp = {};
		
		jQuery('#'+componentInstanceHTMLid+' .vividMenu_item').remove();

		if (sa.m.userDevice.isNonMouse) {
			var events = 
				'onclick="sa.menu.onclick(this,event);" '
		} else {
			var events = 
				'onclick="sa.menu.onclick(this,event);" '
				+'onmouseover="sa.menu.onmouseover(this,event); " '
				+'onmouseout="sa.menu.onmouseout(this,event); " ';			
		}
		var 
		ieFilter100 = 'filter:progid:DXImageTransform.Microsofs.theme.Alpha(opacity=100);',
		ieFilter0 = 'filter:progid:DXImageTransform.Microsofs.theme.Alpha(opacity=0);',
		html = '',
		level = [],
		firstInLevel_xShift = true;;

		for (var i in s.items) {
			var item = s.items[i];
			if (!item.state) {
				item.themeName = s.themeName;
				item.theme = s.theme;
				item.state = {
					normal : { frame : 0, forward : true },
					hover : { frame : 0, forward : true },
					selected : { frame : 0, forward : true },
					disabled : { frame : 0, forward : true }
				};
				
				var
				csc = item.tags.a.getAttribute ('vividMenu_changeStateCondition'),
				csk = item.tags.a.getAttribute ('vividMenu_changeStateKey'),
				initialState = (
					typeof csc==='string'
					&& csc!==''
					? eval ('('+csc.replace('changeStateKey', '"'+csk+'"')+')')
					: 'normal'
				);
				
				item.stateCurrent = 
					item.statePrevious = initialState;
				item.stateOld = null;
				item.stateNew = null;
				item.animationTimeout = null;
			}
			
			var 
			extraHTML = '',
			extraHTMLoptions = {};

			item.i = i;
			//item.orientationOfLevel = 'vertical';//levels[p].orientationOfLevel;
			html += '<div id="' + componentInstanceHTMLid + '__item__' + i + '" class="vividMenu_item" vividMenu_changeStateCondition="'+item.changeStateCondition+'" vividMenu_changeStateKey="'+item.changeStateKey+'" vividMenu_itempath="'+item.path+'" style="text-align:center; vertical-align:middle; position:absolute; width:' + s.theme.spriteImage.frames.width + 'px; height:' + s.theme.spriteImage.frames.height + 'px;' + ieFilter100 + ';px;display:'+(item.level==0?'block':'none')+';z-index:'+(item.level+99991000000)+';" '+ '>';
			html += '<table style="cursor:pointer;z-index:99110000002;position:absolute;width:100%;height:100%"><tr><td id="' + componentInstanceHTMLid + '__item__'+i+'__td" class="vividMenu_item_td" style="background-color:transparent; text-align:center;vertical-align:middle;filter:alpha(opacity=100);" '+events+'>'+item.label+'</td></tr></table>';
			html += '<div id="' + componentInstanceHTMLid + '__item__'+i+'__img1" style="z-index:991100000;position:absolute; width:' + s.theme.spriteImage.frames.width + 'px; height:' + s.theme.spriteImage.frames.height + 'px; ' + ieFilter100 + '"></div>';
			html += '<div id="' + componentInstanceHTMLid + '__item__'+i+'__img2" style="z-index:991100001;position:absolute; width:' + s.theme.spriteImage.frames.width + 'px; height:' + s.theme.spriteImage.frames.height + 'px; opacity:0; ' + ieFilter0 + '" ' + '></div>';
			html += '</div>';
		};

		//sa.m.log (1, 't10', level);
		//sa.m.log (1, 't11', html);
		jQuery('.vividMenu_item',div).remove();
		div.innerHTML += html;
		//div.level = level;
		div.style.zIndex  = 999999999999;
        setTimeout (function() {
            jQuery('.vividMenu_item',div).css({zIndex:999999999999});
        }, 400);
			
		for (var i in s.items) {
			var item = s.items[i];
			item.img1 = document.getElementById(componentInstanceHTMLid + '__item__'+i+'__img1');
			item.img2 = document.getElementById(componentInstanceHTMLid + '__item__'+i+'__img2');
			item.element = document.getElementById(componentInstanceHTMLid + '__item__'+i);

			// show first frame of animation, "normal" state
			item.img1.style.backgroundColor = 'transparent';
			item.img1.style.backgroundImage = 'url(' + s.theme.url + ')';
			item.img2.style.backgroundColor = 'transparent';
			item.img2.style.backgroundImage = 'url(' + s.theme.url + ')';
			//sa.vcc.setOpacity(item.img2,0);
			
			if (sa.s.c.settings.mayDisplayMenus) sa.vcc.animateItem (document.getElementById(componentInstanceHTMLid), document.getElementById(componentInstanceHTMLid+'__item__'+i));
		};
		div.className += ' vividWidget_initialized';
		
		if (sa.m.settings.initialized.site) {
			sa.menu.onresize(componentInstanceHTMLid);
		};
		
		/*
		setTimeout(function () {
			delete sa.vcc.settings['pageMenu'];
			$('#pageMenu').removeClass('vividWidget_initialized');
			sa.vcc.init (jQuery('#iframe-content')[0]);
			sa.vcc.componentFullyInitialized (s.initID, componentInstanceHTMLid);		
		}, 250);
		*/
		sa.vcc.componentFullyInitialized (initID, componentInstanceHTMLid);		
	},
	
	startAnimations : function (componentInstanceHTMLid) {
		var 
		s = sa.vcc.settings[componentInstanceHTMLid];
		
		//jQuery(componentInstanceHTMLid).css({display:'none',opacity:1,visiblity:'visible'}).fadeIn();
		
		if (s && s.items) 
		for (var i=0; i<s.items.length; i++) {
			if (sa.s.c.settings.mayDisplayMenus) sa.vcc.animateItem (document.getElementById(componentInstanceHTMLid), document.getElementById(componentInstanceHTMLid+'__item__'+i));
		};
		
	},
	
	onresize : function (componentInstanceHTMLid) {
	
		if (!sa.vcc.settings[componentInstanceHTMLid]) {
			sa.m.log (1, 'sa.menu.onresize called with invalid componentInstanceHTMLid "'+componentInstanceHTMLid+'"');
			return false;
		}
		if (typeof sa.vcc.settings[componentInstanceHTMLid].beforeResize=='function') {
			sa.vcc.settings[componentInstanceHTMLid].beforeResize(componentInstanceHTMLid);
		}
	
		var 
		s = sa.vcc.settings[componentInstanceHTMLid],
		t = sa.vcc.themes[s.themeName],
		div = document.getElementById(componentInstanceHTMLid),
		parentWidth = jQuery(jQuery(div).parent()[0].offsetParent).width(),
		parentHeight = jQuery(jQuery(div).parent()[0].offsetParent).height();
		
		if (!s.theme) return false;
		
		var
		w = s.theme.spriteImage.frames.width,
		h = s.theme.spriteImage.frames.height,
		levels = {};
		
		if (jQuery(div).parent()[0].offsetParent===null) {
			parentWidth = jQuery(window).width();
			parentHeight = jQuery(window).height();
		};

		// initialize var level
		for (var i=0; i<s.items.length; i++) {
			var
			it = s.items[i],
			el = it.element,
			level = it.level,
			path = it.path,
			parent = it.parent,
			lp = path.lastIndexOf(',') === -1 ? 'root' : path,
			cc = 0,
			clp = 0,
			cp = path.indexOf(',', cc),
			furthest = { x : 0, y : 0 };
			
			//debugger;
			
			while (cp!==-1) {
				cc++;
				clp = cp;
				cp = path.indexOf (',', clp+1);
			};	
			if (cc==1) {
				lp = 'root';
			};

			if (!levels[path]) {
				if (path!=lp) {
					if (cc==1) {
						levels[path] = sa.m.cloneObject(levels['root']);
						levels[path].items = {};
						levels[path].itemCount = 1;
					} else {
						if (!levels['root']) levels['root'] = {
							x : 0, //jQuery(div).position().left,
							y : 0, //jQuery(div).position().top,
							itemCount : 0,
							items : {}
						}
					}
				} else {
					levels[path] = sa.m.cloneObject(levels[path.substr(0,path.lastIndexOf(','))]);
					levels[path].items = {};
					levels[path].itemCount = 1;

				}
			} 
			
			if (!levels[path]) {
				levels[lp].orientation = sa.menu.orientationOfLevel(componentInstanceHTMLid, 0);
				levels[lp].itemCount++;
				levels[lp].items[i] = it;
			} else {
				levels[path].orientation = sa.menu.orientationOfLevel(componentInstanceHTMLid, level);
				levels[path].itemCount++;
				levels[path].items[i] = it;
			}
			
		} // for i
		
		// adjust levels[path] and item.x and item.y to their final coordinates
		for (var i=0; i<s.items.length; i++) {
			var
			it = s.items[i],
			el = it.element,
			level = it.level,
			path = it.path,
			parent = it.parent,
			lp = path.lastIndexOf(',') === -1 && i==path ? 'root' : path,
			lpParent = (
				path.lastIndexOf(',')===-1
				? ''
				: path.substr(0,path.lastIndexOf(','))
			),
			lpParent = (
				lpParent === ''
				? 'root'
				: lpParent
			),
			cc = 0,
			clp = 0,
			cp = path.indexOf(',', cc);
			
			if (!levels[lp]) {
				levels[lp] = sa.m.cloneObject(levels['root']);
				levels[lp].items = {};
				levels[lp].itemCount = 1;
				levels[lp].column = 0;
				levels[lp].row = 1;
				levels[lp].orientation = sa.menu.orientationOfLevel (componentInstanceHTMLid, level);
				levels[lp].x = s.items[parseInt(it.path)].x;
				levels[lp].y = s.items[parseInt(it.path)].y;
			} else {
				if (
					s.items[it.parent]
					&& levels[lp].x == levels[lp].x
					&& levels[lp].y == levels[lp].y
				) {
					levels[lp].x = s.items[it.parent].x;
					levels[lp].y = s.items[it.parent].y;
				}
			}
			
			while (cp!==-1) {
				cc++;
				clp = cp;
				cp = path.indexOf (',', clp+1);
			};	
			/*
			if (cc==1) {
				lp = 'root';
			};
			*/
			
			// initialize options, optionsResults; calculate surface areas available in quadrants topLeft, topRight, bottomRight, bottomLeft from the position of where the level would be painted (left/right of the parent item)
			var
			itemCount = levels[lp].itemCount,
			side = 'right',
			options = [ 'topLeft', 'topRight', 'bottomRight', 'bottomLeft' ],
			optionsResults = {};
			
			for (j=0; j<options.length; j++) {
				var
				opt = options[j];
				
				switch (opt) {
					case 'topLeft':
					case 'bottomLeft':
						var	x = levels[lp].x + (w * 0.2);
						break;
					
					case 'topRight':
					case 'bottomRight':
						var	x = levels[lp].x + (w * 0.8);
						break;
				};
				var
				y= levels[lp].y + (h / 2),
				lw = (
					opt=='topLeft' || opt=='bottomLeft'
					? x
					: parentWidth - x
				),
				lh = (
					opt=='topLeft' || opt=='topRight'
					? y
					: parentHeight - y
				);
				
				optionsResults[opt] = {
					x : x,
					y : y,
					levelWidth : lw,
					levelHeight : lh,
					surface : lw * lh
				};
			} // for j
			

			// determine best option (paint level of menu topLeft/topRight/bottomRight/bottomLeft)
			var 
			option = null,
			optionResult = null,
			offsetX = 4,
			offsetY = 4;
			
			for (var j=0; j<options.length; j++) {
				var opti = options[j];
				if (option===null) {
					optionResult = optionsResults[opti];
					option = opti;
				} else {
					if (optionResult.surface < optionsResults[opti].surface) {
						optionResult = optionsResults[opti];
						option = opti;
					}					
				}
			}
			
			
			switch (levels[lp].orientation) {
				case 'horizontal' :
					if (typeof levels[lp].row == 'undefined') levels[lp].row = 1;
					if (typeof levels[lp].column == 'undefined') {
						levels[lp].column = 0;
					} else {
						levels[lp].column++;
					};
					switch (option) {
						case 'topLeft':
							it.y = levels[lp].y - offsetY;
							it.x = levels[lp].x - offsetX - ((levels[lp].column) * (w));
							if (it.x < 3) {
								levels[lp].column = 0;
								levels[lp].row++;
								it.x = levels[lp].x - offsetX;
								it.y = levels[lp].y - offsetY - (levels[lp].row * h);
							};
							break;
						case 'topRight':
							it.y = levels[lp].y - offsetY;
							it.x = levels[lp].x + offsetX + ((levels[lp].column) * (w));
							if (it.x + w > parentWidth) {
								levels[lp].column = 0;
								levels[lp].row++;
								it.x = levels[lp].x + offsetX;
								it.y = levels[lp].y - offsetY - (levels[lp].row * h);
							};
							break;
						case 'bottomRight':
							it.y = levels[lp].y + offsetY;
							it.x = levels[lp].x + offsetX + ((levels[lp].column) * (w));
							if (it.x + w > parentWidth) {
								levels[lp].column = 0;
								levels[lp].row++;
								it.x = levels[lp].x + offsetX;
								it.y = levels[lp].y + offsetY + (levels[lp].row * h);
							};
							break;
						case 'bottomLeft':
							it.y = levels[lp].y + offsetY;
							it.x = levels[lp].x - offsetX - ((levels[lp].column) * (w));
							if (it.x < 3) {
								levels[lp].column = 0;
								levels[lp].row++;
								it.x = levels[lp].x - offsetX;
								it.y = levels[lp].y + offsetY + (levels[lp].row * h);
							};
							break;
					};
					break;
				
				case 'vertical' : 
					
					if (typeof levels[lp].column == 'undefined') levels[lp].column = 1;
					if (typeof levels[lp].row == 'undefined') {
						levels[lp].row = 1;
					} else {
						levels[lp].row++;
					};
					switch (option) {
						case 'topLeft':
							it.y = levels[lp].y - offsetY - ((levels[lp].row - 1) * h);
							it.x = levels[lp].x - offsetX - (levels[lp].column * (w));
							if (it.y < 3) {
								levels[lp].row = 1;
								levels[lp].column++;
								it.y = levels[lp].y - offsetY - ( (levels[lp].row - 1) * h );
								it.x = levels[lp].x - offsetX - ( levels[lp].column * (w) )
							};
							break;
						case 'topRight':
							it.y = levels[lp].y - offsetY - ((levels[lp].row - 1) * h);
							it.x = levels[lp].x + offsetX + (levels[lp].column * (w));
							if (it.y < 3) {
								levels[lp].row = 1;
								levels[lp].column++;
								it.y = levels[lp].y - offsetY - ( (levels[lp].row - 1) * h );
								it.x = levels[lp].x + offsetX + ( levels[lp].column * (w) )
							};
							break;
						case 'bottomRight':
							it.y = levels[lp].y + offsetY + ((levels[lp].row - 1) * h); 
							it.x = levels[lp].x + offsetX + ((levels[lp].column) * (w));
							if (it.y + h > parentHeight) {
								if (levels[lpParent].orientation=='horizontal') {
									levels[lp].row = 2;
								} else {
									levels[lp].row = 1;
								};
								levels[lp].column++;
								it.y = levels[lp].y + offsetY + ( (levels[lp].row - 1) * h );
								it.x = levels[lp].x + offsetX + ( levels[lp].column * (w) )
							};
							break;
						case 'bottomLeft':
							it.y = levels[lp].y + offsetY + ((levels[lp].row - 1) * h);
							it.x = levels[lp].x - offsetX - (levels[lp].column * (w));
							if (it.y + h > parentHeight) {
								if (levels[lpParent].orientation=='horizontal') {
									levels[lp].row = 2;
								} else {
									levels[lp].row = 1;
								};
								levels[lp].column++;
								it.y = levels[lp].y + offsetY + ( (levels[lp].row - 1) * h );
								it.x = levels[lp].x - offsetX - ( levels[lp].column * (w) )
							};
							break;
					};
					break;
			}

			it.x += ((levels[lp].column) * 3);
			it.y += ((levels[lp].row-1) * 2);
			
			jQuery(it.element).css ({
				top : it.y,
				left : it.x
			});
		} // for i
		
		// now calculate how much extra space is needed in parent div
		var 
		furthestAway = { x : 0, y : 0 },
		resized = false;
		for (var i=0; i<s.items.length; i++) {
			var it = s.items[i];
			if (it.level===0) {
				if (it.x > furthestAway.x) {
					furthestAway.x = it.x;
				};
				if (it.y > furthestAway.y) {
					if (furthestAway.y!==0) resized = true;
					furthestAway.y = it.y;
				};
			}
		};

        // resize outer div (div)
        var 
        p = jQuery(div).position(),
        css = {
            overflow : 'visible'
        },
        el1 = jQuery('#'+componentInstanceHTMLid)[0].offsetParent,
        el2 = (el1?el1.offsetParent:null);
        if (el1 && el2) {
            jQuery('#'+el1.id+', #'+el2.id).css (css);	
        } else if (el1 && el1.id) {
            jQuery('#'+el1.id).css (css);	
        } else if (el1 && el1.tagName && el1.tagName.toLowerCase()=='body') {
            jQuery('body').css (css);	
        } else {
            sa.m.log (1, {msg : 'sa.menu.onresize() : el1 and el2 were not available - afterResize({resized:false});!', el1 : el1, el2 : el2});
            resized = false;
        };
        var
        css = { 
            width : furthestAway.x + w + 4,
            height : h + 7//,
            //opacity : 1
        };
        jQuery(div).css(css);
		

		
		if (typeof sa.vcc.settings[componentInstanceHTMLid].afterResize=='function') {
			sa.vcc.settings[componentInstanceHTMLid].afterResize({
				cihe : jQuery('#'+componentInstanceHTMLid)[0],
				resized : resized
			});
		}
	},
	
	preprocess : function (componentInstanceHTMLid) {
		var
		s = sa.vcc.settings[componentInstanceHTMLid];
		
		if (!s) {
			s = sa.vcc.settings[componentInstanceHTMLid] = {};
		}
		
		if (!s.originalContent) {
			s.originalContent = (
                jQuery('#'+componentInstanceHTMLid)[0].children[0]
                ? jQuery('#'+componentInstanceHTMLid)[0].children[0].innerHTML
                : ''
            );
		}
		
		//var html = s.originalContent.replace(/<li class="saLinkpoint"><a href="-saLinkpoint-">-saLinkpoint- .*<\/a><\/li>/g, '');
		jQuery('li.saLinkpoint', jQuery('#'+componentInstanceHTMLid)[0]).remove();
		
	},
	
	merge : function (componentInstanceHTMLid, transformLinks, valueToFind, otherMenu, iframeHTMLid, targetIsInsideIframe) {
		var
		lp = '<li class="saLinkpoint"><a href="-saLinkpoint-">'+valueToFind+'</a></li>',
		s = sa.vcc.settings[componentInstanceHTMLid],
		iframe = (
			iframeHTMLid !== undefined
			? jQuery('#'+iframeHTMLid, window.top.document)[0].contentWindow
			: jQuery('body')[0]
		),
		menuContent = otherMenu ? otherMenu.children[0].innerHTML : '',
		menuContentFixed = (
			iframeHTMLid !== undefined
			? menuContent
				.replace(/javascript:return false;/g, 'java_script:return false;')
				.replace(/javascript:/g,'javascript:var iframe=jQuery("#'+iframeHTMLid+'", window.top.document)[0].contentWindow;iframe.')
				.replace(/java_script:return false;/g, 'javascript:return false;')
				.replace(/<a class="/g,'<a class="nomod ')
				.replace(/<a href="/g, '<a class="nomod" href="')
			: menuContent
				.replace(/<a class="/g,'<a class="nomod ')
				.replace(/<a href="/g, '<a class="nomod" href="')
		),
		tl = transformLinks + '(jQuery("#'+componentInstanceHTMLid+'")[0])',
		html = s.originalContent.replace(lp, menuContentFixed);
				
		//jQuery('#'+componentInstanceHTMLid).fadeOut('slow');
		jQuery('#'+componentInstanceHTMLid)[0].children[0].innerHTML = html;
		eval (tl);
	
		/*
		sa.m.log (1, 't31', s, lp);
		sa.m.log (1, 't32 menuContentFixed', menuContentFixed);
		sa.m.log (1, 't33 html', html);
		debugger;
		*/
	
        delete sa.vcc.settings[componentInstanceHTMLid];
		sa.menu.init(jQuery('#'+componentInstanceHTMLid)[0]);
	},
	
	updateMenuItemsStates : function (cihe, data) {
		for (var k in data) {
			sa.menu.updateMenuItemsStates_scanLevel (cihe, data[k]);
		}
	},
	
	updateMenuItemsStates_scanLevel : function (cihe, data) {
		if (data.settings) {
			sa.vcc.changeState (cihe, jQuery('#'+cihe.id+'__item__'+data.settings.menuItemIdx)[0], (data.settings.menuItemSelected?'selected':'normal'));
		}
		if (data.subItems) {
			for (var k in data.subItems) {
				sa.menu.updateMenuItemsStates_scanLevel(cihe, data.subItems[k]);
			}
		}
	},
	
	populateMenuWithJSON : function (jQueryIDmenu, data) {
		var html = sa.menu.populateMenuWithJSON_scanIndex (jQueryIDmenu, data, data.settings);
		jQuery('ul', jQuery(jQueryIDmenu)).html(html);
		//debugger;
		delete sa.vcc.settings[jQueryIDmenu];
		jQuery(jQueryIDmenu).removeClass('vividWidget_initialized');
		/*
		var elToInit = jQuery(jQueryIDmenu)[0];
		setTimeout (function () {
			sa.vcc.init(elToInit);
		}, 1000);
		*/
	},
	
	populateMenuWithJSON_scanIndex : function (jQueryIDmenu, dataRoot, data) {
		var html = '';
		for (var key in data) {
			html += sa.menu.populateMenuWithJSON_scanLevel(jQueryIDmenu, dataRoot, data[key], key);
		};
		return html;
	},
	
	populateMenuWithJSON_scanLevel : function (jQueryIDmenu, dataRoot, data, k) {
		var html = '';
		if (typeof data=='object') {
			html += '<li><a href="#">'+k+'</a>';
			html += '<ul>';
			for (key in data) {
				html += sa.menu.populateMenuWithJSON_scanLevel (jQueryIDmenu, dataRoot, data[key], key);
			}
			html += '</ul>';
			html += '</li>';
		} else {
			html += '<li><a href="'
				+dataRoot.globals.perMenu[jQueryIDmenu].itemsPre
				+data
				+dataRoot.globals.perMenu[jQueryIDmenu].itemsPost
				+';">'+k+'</a></li>';
		}
		
		return html;
	},
	
	populateMenuWithObject : function (cihe, data, key, handler) {
		var html = sa.menu.populateMenuWithObject_scanIndex (data, key, {idx:0}, handler);
		jQuery('ul', cihe).html (html);
	},
	
	populateMenuWithObject_scanIndex : function (data, key, cmd, handler) {
		var html = '';
		for (var k in data) {
			html += sa.menu.populateMenuWithObject_scanLevel(data[k], key, cmd, handler);
		};
		return html;
	},
	
	populateMenuWithObject_scanLevel : function (data, key, cmd, handler) {
		var html = '';
		if (typeof data=='object' && typeof data.settings=='object') {
			html += '<li><a href="javascript:'+handler+'('+cmd.idx+');">'+data.settings[key]+'</a>';
			data.settings.menuItemIdx = cmd.idx;
			cmd.idx++;
			if (data.subItems) {
				html += '<ul>';
				for (k in data.subItems) {
					html += sa.menu.populateMenuWithObject_scanLevel (data.subItems[k], key, cmd, handler);
				}
				html += '</ul>';
			}
			html += '</li>';
		}
		return html;
	},
	
	getitems : function (cihe) {
	// called by sa.vcc.init()
		var s = sa.vcc.settings[cihe.id];
		var t = sa.vcc.themes[s.themeName];
		
		sa.m.log (undefined, { sample : 1, s : s } );
		//if (typeof s.items!=='object' || s.items.length===0) {
			s.items = [];
			sa.menu.getitemsForUL (cihe, cihe.children[0], '', 0, null);	
			sa.m.log (undefined, { sample : 2, s : s } );
		//}
		cihe.children[0].style.display = 'none';
	},
	
	getitemsForUL : function (cihe, el, path, level, parent) {
		for (var i = 0; i<el.children.length; i++) {
			if (el.children[i].tagName.toUpperCase()==='LI') {
				var path2 = '' + path;
				sa.menu.getitemsForLI (cihe, el.children[i], i, path2, level, parent);
			};
		}
	},
	
	getitemsForLI : function (cihe, el, idx, path, level, parent) {
		var s = sa.vcc.settings[cihe.id];
		var t = sa.vcc.themes[s.themeName];
		
		var 
		item = {},
		pathChanged = false;
		
		item.tags = {
			a : el.children[0],
			li : el
		};
		if (path=='') {
			path = ''+(s.items.length);
			pathChanged = true;
		};
		item.parent = parent;
		item.path = path;
		item.level = level;
		item.levelIdx = idx;
		item.label = el.children[0].innerHTML;
		var href = el.children[0].href;
        //debugger;
		
		item.dontKeepSelected = el.children[0].className.indexOf('menu__dontKeepSelected')!==-1;
			//sa.m.log (1, 't35', href, sa.m.globals.urls.app + '#');
		item.url = (
			href == '#'
			|| href == sa.m.globals.urls.app + '#'
			? false
			: href
		);
		item.changeStateKey = jQuery(el.children[0]).attr('vividMenu_changeStateKey');
		item.changeStateCondition = jQuery(el.children[0]).attr('vividMenu_changeStateCondition');
		//item.active = el.children[0].className.match (/active/); // obsoleted... never used, i think..
		item.id = s.items.length;//==0 ? 1 : s.items[s.items.length-1].id + 1);
		
		s.items[s.items.length] = item;
		//sa.m.log (undefined, { s : s, item : item } );
		
		if (el.children.length==2) {
			if (el.children[1].tagName.toUpperCase()==='UL') {
				sa.menu.getitemsForUL (cihe, el.children[1], pathChanged?path:path+','+item.id, level+1, item.id); //path=='' ? ''+item.id : path+','+item.id
			}			
		}
	},
	
	loadMenuGraphics : function (cihe, initID) {
		//sa.m.log (6, 'vividMenu.loadMenuGraphics 1', cihe, initID, sa.vcc.settings);
		if (sa.vcc.newInstanceNeeded(cihe)) sa.vcc.newInstance (cihe, initID);
		var s = sa.vcc.settings[cihe.id];
		//sa.m.log (6, 'vividMenu.loadMenuGraphics 2', cihe, initID, s, sa.vcc.settings);
		var t = sa.vcc.themes[s.themeName];
		s = sa.vcc.newInstanceThemeOverrides (s, t);
		sa.menu.getitems (cihe);
		s.theme = t;
		//sa.m.log (6, 'vividMenu.loadMenuGraphics 3', cihe, initID, s, sa.vcc.settings);
		
		var img = document.createElement('img');
		img.src = s.theme.spriteImage.url;
		if (img.complete || img.readyState===4) {
			sa.menu.initMenu(cihe.id);	
		} else {
			
			if (typeof sa.tracer!=='undefined') {
				var ua = sa.tracer.findUA(arguments);	
			} else {
				var ua = false;
			};
			/*if (ua) {
				var html = '<img id="' + cihe.id + '__img" src="' + s.theme.url + '" style="width:1px;height:1px;visibility:hidden;" onload="var t=this;sa.m.reAttachUA('+ua.uaIdx+', function(){sa.menu.initMenu(\''+cihe.id+'\')})();"/>';
			} else {
				var html = '<img id="' + cihe.id + '__img" src="' + s.theme.url + '" style="width:1px;height:1px;visibility:hidden;" onload="sa.menu.initMenu(\''+cihe.id+'\');"/>';
			}; BROWSERBUG : onload keeps firing in Chrome 2015-05-13*/
			if (ua) {
				var html = '<img id="' + cihe.id + '__img" src="' + s.theme.url + '" style="width:1px;height:1px;visibility:hidden;" />';
			} else {
				var html = '<img id="' + cihe.id + '__img" src="' + s.theme.url + '" style="width:1px;height:1px;visibility:hidden;" />';
			};
			cihe.innerHTML += html;
			cihe.children[0].style.display = 'none'; // hides the <UL> menu
			sa.menu.initMenu(cihe.id);
		}
	},
	
	itemLabelToClass : function (label) {
		var r = label;
		r = r.replace (' ', '-_-');
		return r;
	},
	
	classToItemLabel : function (cl) {
		var r = cl;
		r = r.replace ('-_-', ' ');
		return r;
	},
	
	destroyMenu : function (componentInstanceHTMLid) {
		var d = document.getElementById(componentInstanceHTMLid);
		while (d.children.length>2) {
			d.removeChild (d.children[2]);
		};
		if (d.children[1].tagName.toUpperCase()!=='SCRIPT') d.removeChild (d.children[1]);
		d.className = d.className.replace(/vividWidget_initialized/,'');
		delete sa.vcc.settings[componentInstanceHTMLid];
	},
	
	orientationOfLevel : function (componentInstanceHTMLid, level) {
		var o = sa.vcc.options[componentInstanceHTMLid];
		var s = sa.vcc.settings[componentInstanceHTMLid];
		var result = 'vertical';
		var name = 'level_'+level;
		//console.log ('sa.menu.orientationOfLevel', name);
		if (o && o[name]) {
			if (o[name].orientation) result = o[name].orientation;
			//console.log ('sa.menu.orientationOfLevel.1', result);
		};
		return result;
	},
	
	onclick : function (el,evt) {
		var menuHTMLid = el.id;
		menuHTMLid = menuHTMLid.replace(/__.*/,'');
		var s = sa.vcc.settings[menuHTMLid];
		var idx = sa.vcc.getItemIndex(el);

		
		var doClick = false;		
		if (sa.m.userDevice.isNonMouse) {
			if (s.menuOpens[idx])	{
				doClick = true;
			} else {
				var r = sa.menu.onmouseover (el, evt);
				doClick = !r.doClick;
			}
		} else {
			doClick = true;
		};
		
		if (sa.m.userDevice.isNonMouse /*&& doClick*/) {
			jQuery('.vividMenu_item.selected').each(function(idx,el){
				var
				idSplit = el.id.split('__'),
				elID = parseInt(idSplit[idSplit.length-1]);
			
				sa.vcc.changeState (
					document.getElementById(menuHTMLid),
					s.items[elID].element,
					'normal'
				);
			});
		
			for (var elID in s.menuOpens) {
                /*
				if (elID==r.openingMenu) continue;
				
				var closeThis = true;
				if (r.openingMenu) {
					var 
					om = s.items[r.openingMenu],
					omp = om.path,
					it = s.items[elID],
					itp = it.path,
					closeThis = (
						(
							!omp.match(',')
							&& !itp.match (',')
							&& omp!==itp
						)
						|| (
							!(omp+','+r.openingMenu).match(itp)
						)
					)
				};
				if (!closeThis) continue;
				
				elID = parseInt(elID);
				//if (elID!=idx) {
					//alert (s.items[elID].label+' - '+sa.json.encode(s.items[elID].dontKeepSelected)); / / .dontKeepSelected!==true?'selected':'normal'));
					sa.menu.hideNode (menuHTMLid, elID);
					sa.vcc.changeState (
						document.getElementById(menuHTMLid),
						s.items[elID].element,
						(
							s.items[elID].dontKeepSelected!==true
							? 'selected'
							: 'normal'
						)
					);
					delete s.menuOpens[elID];
				//}
                */
			};
			var elID = idx;
			sa.vcc.changeState (
				document.getElementById(menuHTMLid),
				s.items[elID].element,
				(
					s.items[elID].dontKeepSelected!==true
					? 'selected'
					: 'normal'
				)
			);
			
			for (var elID in s.menuOpensTemp) {
				if (elID!=idx) {
					sa.menu.hideNode (menuHTMLid, elID);
					delete s.menuOpens[elID];
				}
			};
		};
		
		if (doClick) {
			//debugger;
			var url = s.items[idx].url;
			if (url != "''") {
				if (url.substr(0,11)=='javascript:') {
					var js = decodeURI(url.substr(11));
					eval(js);
				} else {
					document.location = url;
				}
				/*
				for (var elID in s.menuOpens) {
					sa.menu.hideNode(menuHTMLid, elID);
				}
				*/

				if (!sa.m.userDevice.isNonMouse) {
					var
					item = s.items[idx],
					csc = item.tags.a.getAttribute ('vividMenu_changeStateCondition'),
					csk = item.tags.a.getAttribute ('vividMenu_changeStateKey'),
					newState = (
						typeof csc==='string'
						&& csc!==''
						? eval ('('+csc.replace('changeStateKey', '"'+csk+'"')+')')
						: 'normal'
					);
					
					//console.log ('r10102', csc, csk, newState, item);
					
					sa.vcc.changeState (document.getElementById(menuHTMLid),el,newState);
				}
				
			}
			
		}
	},
	
	onmouseover : function (el,evt) {
		var menuHTMLid = el.id;
		menuHTMLid = menuHTMLid.replace(/__.*/,'');
		var 
		s = sa.vcc.settings[menuHTMLid],
		idx = sa.vcc.getItemIndex(el),
		item = s.items[idx],
		parent = null,
		menuOpensLapse = sa.menu.globals.menuOpensLapse,
		menuCloseLapse = sa.menu.globals.menuCloseLapse,
		ps = s.items[idx].path.split(','),
		parent = parseInt(ps[ps.length-2]);

		if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseover(): onmouseover ' + idx + '('+s.items[idx].label+')');

		if (s.walkbackTimeout) {
			clearTimeout (s.walkbackTimeout);
		};
		
		
		if (
			s.immediateChildHidingTimeout
			&& !sa.m.userDevice.isPhone
			&& (
				//s.items[s.previousIdx].parent===null
				s.items[idx].parent == s.previousIdx
				/*|| s.items[s.previousIdx].path!=s.items[idx].path
				|| !(
					s.items[s.previousIdx].path.indexOf(',')===-1
					&& s.items[s.idx].path.indexOf(',')===-1
				)
				|| !(
					s.items[s.previousIdx].parent===null
					&& s.items[idx].parent===null
				)*/
			)
			&& !sa.menu.globals.keepAllSubMenusOpen
		) {
			clearTimeout(s.immediateChildHidingTimeout);
			if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseover: cancelled s.immediateChildHidingTimeout');
		};
		
		
		if (s.previousIdx && s.menuOpens[s.previousIdx]) {
			if (sa.menu.globals.debugMouseOpeningAndClosing) console.log ('sa.vividMenu.onmouseover clearTimeout(s.menuOpens[s.previousIdx].timeout)', s.previousIdx, s.items[s.previousIdx].label);
			clearTimeout(s.menuOpens[s.previousIdx].timeout);
			delete s.menuOpens[s.previousIdx].timeout;
		};
		if (s.previousIdx && s.menuOpensTemp[s.previousIdx]) {
			if (sa.menu.globals.debugMouseOpeningAndClosing) console.log ('sa.vividMenu.onmouseover clearTimeout(s.menuOpensTemp[s.previousIdx].timeout)', s.previousIdx, s.items[s.previousIdx].label);
			clearTimeout(s.menuOpensTemp[s.previousIdx]);
			delete s.menuOpensTemp[s.previousIdx];
		};
		
//		setTimeout(function(){
			s.previousIdx = idx;
//		}, 300);
		
		//if (!sa.menu.globals.keepAllSubMenusOpen) {
			for (var idx2 in s.menuOpens) {
				if (s.items[idx2].path===s.items[idx].path && idx2!=s.items[idx].parent) {
					sa.menu.hideNode (menuHTMLid, idx2);
					delete s.menuOpens[idx2];
					sa.vcc.changeState (document.getElementById(menuHTMLid),s.items[idx2].element,'normal');
				}
			}
		//}
		
		if (sa.menu.globals.keepAllSubMenusOpen) {
			clearTimeout(s.closeAllOpenMenuItems);
		};
			
		
		/*
		if (s.menuOpens[parent] && s.menuOpens[parent].timeout) {
			clearTimeout (s.menuOpens[parent].timeout);
			delete s.menuOpens[parent].timeout;
		}
		if (s.menuOpensTemp[parent]) {
			clearTimeout (s.menuOpensTemp[parent]);
			delete s.menuOpensTemp[parent];
		};
		*/

		//cancel hiding of menu (because we're now hovering over a child of a path that thus needs to stay open).
		if (s.menuOpens[idx]) {
			if (s.menuOpens[idx].timeout) {
				if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseover(): cancelling[1] close of menu '+idx+'('+s.items[idx].label+')');
				clearTimeout (s.menuOpens[idx].timeout);	
				delete s.menuOpens[idx].timeout;
			}
		};
		for (var idx2 in s.menuOpens) {
			if (s.items[idx].path.indexOf (s.items[idx2].id)!==-1) {
				if (s.menuOpens[idx2].timeout) {
					if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseover(): cancelling[2] close of menu '+idx2+ '('+s.items[idx2].label+')');
					clearTimeout (s.menuOpens[idx2].timeout);
					//delete s.menuOpens[idx2].timeout;
					sa.menu.logMenuOpens('onmouseover 0', s, idx, idx2);
				}
			}
		};
		
		//prepare the opening of a potential sub-menu:
		var 
		initialLevel = s.items[idx].level,
		childIDs = '',
		childLabels = '',
		keepGoing = true,
		done = false,
		targetPath = s.items[idx+1].path,
		openingMenu = null;
		
		//debugger;
		for (var i=idx+1; i<s.items.length && s.items[i] && s.items[i].level > initialLevel; i++) {
				//sa.m.log (1, 't13', s.items[i].path, s.items[idx].path+','+idx);
			if (s.items[i].path==targetPath) {
				var menuItemHTMLid = menuHTMLid+'__item__'+i;
				openingMenu = idx;
				if (typeof s.menuOpens[idx] == 'undefined') {
					s.menuOpens[idx] = {
						children : new Array()
					};
				};
				if (i!==idx) {
					s.menuOpens[idx].children[menuItemHTMLid] = s.items[i].label;
					if (childIDs!='') childIDs += ',';
					if (childLabels!='') childLabels += ',';
					childIDs += menuItemHTMLid;
					childLabels += i + ' (' + s.items[i].label + ')';
				}
				
			}
		};
		if (sa.menu.globals.debugMouseOpeningAndClosing) sa.menu.logMenuOpens('onmouseover 1', s, idx, i);
		
		if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseover() : setting/replacing timeout on s.menuOpenTimeout for childLabels=="'+childLabels+'".');
		if (s.menuOpenTimeout) clearTimeout(s.menuOpenTimeout);
		
		item.menuOpenTimeout = setTimeout(function() {
			if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseover() : fading in (s.menuOpenTimeout) childLabels=="'+childLabels+'".');
			if (childIDs!='') {
				sa.m.log (3, 'sa.vividMenu.onmouseover(): opening : '+idx+', '+childIDs);
				sa.vcc.fadeIn (childIDs, sa.menu.globals.timeToOpenClose);
			}
		//}, 700);	
		
			if (!sa.menu.globals.keepAllSubMenusOpen) {
			//if (s.menuTooSmall) {
				for (var elID in s.menuOpensTemp) {
					if (s.menuOpensTemp[elID]) {
						if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseover() : clearing s.menuOpensTemp for '+elID+' ('+s.items[elID].label+'), s.menuOpensTemp["'+elID+'"]==="'+s.menuOpensTemp[elID]+'".');
						clearTimeout(s.menuOpensTemp[elID]); 
						delete s.menuOpensTemp[elID];
						sa.menu.logMenuOpens('onmouseover 2', s, idx, elID);
					}
				};

				if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseover(): ps = '+s.items[idx].path);
				
				for (var idx5 = 0; idx5 < ps.length - 1; idx5++) {
					var 
					elID = parseInt(ps[idx5]);
					
					if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseover(): clearing s.menuOpensTemp for '+elID+' ('+s.items[elID].label+')');
					if (s.menuOpensTemp[elID]) clearTimeout(s.menuOpensTemp[elID]);
					if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseover(): s.menuOpensTemp set on '+elID+' ('+s.items[elID].label+')');
					s.menuOpensTemp[elID] = setTimeout(
						'sa.menu.hideNode("'+menuHTMLid+'", "'+elID+'", true);'
						,sa.menu.globals.menuOpensLapse
					);
					sa.menu.logMenuOpens('onmouseover 3', s, idx, elID);
				}
			//}
			}
		
            /*
			// highlighting of current item
			item.timerToHoverState = setTimeout (function () {
                var doe = false;
                if (item.stateCurrent) if (item.stateCurrent!=='disabled' && item.stateCurrent!=='selected') doe = true;
                if (item.stateNew) if (item.stateNew!=='disabled' && item.stateNew!=='selected') doe = true;
                if (doe) {
                    item.statePrevious = item.stateCurrent || item.stateNew;
                    //sa.vcc.animateItem (document.getElementById(menuHTMLid), el, true, true);
                    sa.vcc.changeState (document.getElementById(menuHTMLid), el, 'hover');
                }
            }, 500);
            */
		}, 700);

        item.timerToHoverState = setTimeout (function () {
            var doe = false;
            if (item.stateCurrent) if (item.stateCurrent!=='disabled' && item.stateCurrent!=='selected') doe = true;
            if (item.stateNew) if (item.stateNew!=='disabled' && item.stateNew!=='selected') doe = true;
            if (doe) {
                item.statePrevious = item.stateCurrent || item.stateNew;
                //sa.vcc.animateItem (document.getElementById(menuHTMLid), el, true, true);
                sa.vcc.changeState (document.getElementById(menuHTMLid), el, 'hover');
            }
        }, 700);
        
        
		return {
			doClick : (childIDs!=''),
			openingMenu : openingMenu
		};
	},
	
	onmouseout : function (el,evt) {
		var menuHTMLid = el.id;
		menuHTMLid = menuHTMLid.replace(/__.*/,'');
		
		var
		s = sa.vcc.settings[menuHTMLid],
		idx = sa.vcc.getItemIndex(el),
		item = s.items[idx],
		menuOpensLength = 0,
		menuOpensLapse = sa.menu.globals.menuOpensLapse,
		menuCloseLapse = sa.menu.globals.menuCloseLapse,
		menuWalkbackLapse = sa.menu.globals.menuWalkbackLapse;

		if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseout(): onmouseout ' + idx + '('+s.items[idx].label+')');
		
		clearTimeout (item.menuOpenTimeout);
        clearTimeout (item.timerToHoverState);
        clearTimeout (s.showNodeTempTimeout);
        var doe = false;
        if (item.stateCurrent) if (item.stateCurrent!=='disabled' && item.stateCurrent!=='selected') doe = true;
        if (doe) {
            sa.vcc.changeState (document.getElementById(menuHTMLid), el, 'normal');
        }   

		for (var elID in s.menuOpens) {
			menuOpensLength++;
		};
		
		var extraTime = menuCloseLapse * menuOpensLength;
		
		var keys = Object.keys(s.menuOpens);
		
		for (var j=0; j<keys.length; j++) {
			var idx2 = keys[j];
			for (var idx3 in s.menuOpensTemp) {
				var mot = s.menuOpensTemp[idx3];
				if (mot == 'done') {
					//debugger;
					if (s.showNodeTimeout) clearTimeout (s.showNodeTimeout);
					if (!sa.menu.globals.keepAllSubMenusOpen) {
						s.showNodeTimeout = setTimeout (function () {
							
							if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseout(): showing node '+idx3+' ('+s.items[idx3].label+'), because s.menuOpensTemp[idx3]==="done"');
							sa.menu.showNode (menuHTMLid, idx3, false);
							delete s.menuOpensTemp[idx3];
							//delete s.menuOpens[idx3]; // DONT
							sa.menu.logMenuOpens('onmouseout 0', s, idx, idx3);
						}, menuOpensLapse);
					}
				}
			}
			extraTime -= menuOpensLapse;
		}
		
		if (
			s.immediateChildHidingTimeout
			&& (
				s.items[s.previousIdx].path!=s.items[idx].path 
				/*&& !(
					s.items[s.previousIdx].parent===null
					&& s.items[idx].parent===null
				)*/
			)
		) {
				clearTimeout(s.immediateChildHidingTimeout);
				if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseout: cancelled s.immediateChildHidingTimeout');
			
		}
		
		
		sa.m.log (1, 't555', 's.previousIdx='+s.previousIdx+', idx='+idx+', s.items[idx].label='+s.items[idx].label);
		if (
			s.items[idx]
			&& s.items[s.previousIdx]
			&& (
				s.items[s.previousIdx].path!=s.items[idx].path
				|| (
					s.items[s.previousIdx].parent===null
					&& s.items[idx].parent===null
				)
			)
			//&& !sa.menu.globals.keepAllSubMenusOpen
		) {
		
			if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseout(): s.immediateChildHidingTimeout to hide set[2] on '+idx+' ('+s.items[idx].label+')');
			//debugger;
			s.immediateChildHidingTimeout = setTimeout (function() {
				sa.menu.hideNode(menuHTMLid, idx, true);
			}, sa.menu.globals.menuImmediateChildCloseLapse ); // OUTDATED COMMENT : * 2 because startWalkback needs to have fired first, and that's on * 1.
			sa.menu.logMenuOpens('onmouseout immediateChildHidingTimeout', s, idx, idx);
		}
		
		if (!sa.menu.globals.keepAllSubMenusOpen) {
		//if (s.menuTooSmall) {
			var
			menuOpensLength = 0;
			for (var elID in s.menuOpens) {
				menuOpensLength++
			};
			var
			ps = s.items[idx].path.split(',');
			
			if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseout(): ps = '+s.items[idx].path);
			
			for (var idx5 = 0; idx5 <= ps.length - 1; idx5++) { 
				var elID = parseInt(ps[idx5]);
				
				if (s.menuOpensTemp[elID]) {
					clearTimeout(s.menuOpensTemp[elID]);
					if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseout(): clearing s.menuOpensTemp for '+elID+' ('+s.items[elID].label+')');
				};

				if (s.items[elID]) {
					if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseout(): s.menuOpensTemp set for hideNode on '+elID+' ('+s.items[elID].label+')');
					s.menuOpensTemp[elID] = setTimeout(
						'sa.menu.hideNode("'+menuHTMLid+'", "'+elID+'", true);'
						,menuWalkbackLapse);
					sa.menu.logMenuOpens('onmouseout 2', s, idx, elID);
				}
			}

			elID = parseInt(ps[ps.length-3]);
			if (ps.length>=3) {
				if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseout(): clearing s.menuOpensTemp for '+elID+' ('+s.items[elID].label+')');
				if (s.menuOpensTemp[elID]) clearTimeout(s.menuOpensTemp[elID]);
				if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.onmouseout(): s.menuOpensTemp set for showNode on '+elID+' ('+s.items[elID].label+')');
				s.menuOpensTemp[elID] = setTimeout(
					'sa.menu.showNode("'+menuHTMLid+'", "'+elID+'", false, true);',
					menuOpensLapse);
				sa.menu.logMenuOpens('onmouseout 2.1', s, idx, elID);
			}
			
			s.walkbackTimeout = setTimeout (function () {
                if (sa.statusbar.settings.current.msg) sa.statusbar.update (sa.statusbar.settings.current.msg, false, false, true);

				sa.menu.startWalkback(menuHTMLid, s, idx);
				sa.vcc.settings[menuHTMLid].menuWalkback = {
					menuHTMLid : menuHTMLid,
					s : s,
					idx : idx
				};
			}, sa.menu.globals.menuWalkbackLapse );
		//}
		};
		
		if (sa.menu.globals.keepAllSubMenusOpen) {
			s.closeAllOpenMenuItems = setTimeout (function() {
				jQuery('.vividMenu_item', jQuery('#'+menuHTMLid)[0]).each (function (idx, el) {
					var 
					ip = jQuery(el).attr('vividmenu_itempath'),
					idp = el.id.split('__');
					
					idp = idp[2];
					
					//debugger;
					if (
						ip!==''
						&& ip!=idp
					) {
						jQuery(el).fadeOut(sa.menu.globals.menuCloseLapse);
					} 
					
				});
			}, sa.menu.globals.menuCloseLapse);
		};

		
		if (
			item.stateCurrent!='disabled' 
			&& item.stateCurrent!='selected'
			&& item.stateNew!='disabled'
			&& item.stateNew!='selected'
		) {
			sa.vcc.animateItem (document.getElementById(menuHTMLid), el, false, true);
			//sa.vcc.changeState (document.getElementById(menuHTMLid), el, item.statePrevious);
		}
	},
	
	startWalkback : function (menuHTMLid, s, idx) {
        

        
		// s.menuOpens = children that are open
		// s.menuOpensTemp = parents that have been hidden
		if (s.immediateChildHidingTimeout) {
			clearTimeout (s.immediateChildHidingTimeout);
		};
		
		if (!s.walkbackIdxTemp) {
			var sMenuOpensTempLength = 0;
			for (var elID in s.menuOpensTemp) {
				sMenuOpensTempLength++;
			};
			s.walkbackIdxTemp = sMenuOpensTempLength;
		}
		if (!s.walkbackIdx) {
			var sMenuOpensLength = 0;
			for (var elID in s.menuOpens) {
				sMenuOpensLength++;
			};
			s.walkbackIdx = sMenuOpensLength;
			sa.menu.hideNode(menuHTMLid, idx, true);
		}
		
		s.walkbackIdxTemp--;
		s.walkbackIdx--;
		
		if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.startWalkback(): s.walkbackIdxTemp='+s.walkbackIdxTemp+', s.walkbackIdx='+s.walkbackIdx);
		sa.menu.logMenuOpens('startWalkback', s, idx, idx);
		
		if (s.walkbackIdx >= 0) {	
			var 
			elID = null,
			aidx = 0;
			for (var elID1 in s.menuOpens) {
				if (aidx==s.walkbackIdx) elID = elID1;
				aidx++;
			};
			
			if (elID) {
				elID = parseInt(elID);
				if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.startWalkback(): calling hideNode for '+elID+' ('+s.items[elID].label+')');
				setTimeout (function () {
					sa.menu.hideNode(menuHTMLid, elID, true);
				}, sa.menu.globals.menuCloseLapse * 2);
			}
		} else {
			delete s.walkbackIdx;
		}
		if (s.walkbackIdxTemp >= 0) {
			var 
			elID = null,
			aidx = 0;
			
			for (var elID1 in s.menuOpensTemp) {
				if (aidx==s.walkbackIdxTemp) elID = elID1;
				aidx++;
			};
			if (elID) {
				elID = parseInt(elID);
				if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.startWalkback(): calling showNodeTemp for '+elID+' ('+s.items[elID].label+')');
				s.showNodeTempTimeout = setTimeout (function () {
					sa.menu.showNodeTemp(menuHTMLid, elID, true);
				}, sa.menu.globals.menuOpensLapse);
			}
		} else {
			delete s.walkbackIdxTemp;
		};
		
		
		if (s.walkbackIdx >= 0 || s.walkbackIdxTemp >= 0) {
			if (sa.menu.globals.debugMouseOpeningAndClosing) sa.m.log (3, 'sa.vividMenu.startWalkback(): setting s.walkbackTimeout');
			s.walkbackTimeout = setTimeout(function() {
				sa.menu.startWalkback (menuHTMLid, s, idx);
			}, sa.menu.globals.menuWalkbackLapse);
		} 
	},
	
	logMenuOpens : function (fromWhere, s, idx, idx2) {
		// RESULTS IN CONSOLE.LOG VIEW ARE ALWAYS THE CURRENT AT TIME OF VIEWING THEM, NOT RECORDED AS IN LUCIDLOG ON THE MOMENT OF EXECUTION.
		/* TOXIC TO DEBUGGING EFFORTS:	
		if (sa.menu.globals.debugMouseOpeningAndClosing) console.log ('sa.menu.logMouseOpens("'+fromWhere+'")', s.items[idx].label, s, idx, s.items[idx]?s.items[idx].label:'NOTFOUND', idx2, s.items[idx2]?s.items[idx2].label:'NOTFOUND');
		*/
	},
	
	instaHide : function (menuHTMLid) {
		var s = sa.vcc.settings[menuHTMLid];
		if (!s) return false;
		if (s.menuOpens) {
			for (var idx in s.menuOpens) {
				var ids = '', idsLabels = '';
				for (var c in s.menuOpens[idx].children) {
					if (ids!='') ids+=',';
					if (idsLabels!='') idsLabels+=', ';
					idsLabels += c + ' (' + s.menuOpens[idx].children[c] + ')';
					ids += c;
				};
				
				var p = s.items[idx].path;

				if (ids!='') {
					sa.m.log (3, 'sa.vividMenu.hideNode(): ' +idx+ ' --> '+ idsLabels);
					if (sa.m.userDevice.isPhone) {
						jQuery('#'+ids.replace(/,/g,',#')).css({display:'none'});
					} else {
						sa.vcc.fadeOut (ids, sa.menu.globals.timeToOpenClose);
					}
				};
				
				delete s.menuOpens[idx];
			}
		}
		
		if (s.menuOpensTemp) {
			for (var idx in s.menuOpensTemp) {
				var ids = '', idsLabels = '';
				for (var c in s.menuOpensTemp[idx].children) {
					if (ids!='') ids+=',';
					if (idsLabels!='') idsLabels+=', ';
					idsLabels += c + ' (' + s.menuOpensTemp[idx].children[c] + ')';
					ids += c;
				};
				
				var p = s.items[idx].path;

				if (ids!='') {
					sa.m.log (3, 'sa.vividMenu.hideNode(): ' +idx+ ' --> '+ idsLabels);
					if (sa.m.userDevice.isPhone) {
						jQuery('#'+ids.replace(/,/g,',#')).css({display:'none'});
					} else {
						sa.vcc.fadeOut (ids, sa.menu.globals.timeToOpenClose);
					}
				};
				
				delete s.menuOpensTemp[idx];
			}
		}
	},
	
	hideNode : function (menuHTMLid, idx, del, done) {
		var s = sa.vcc.settings[menuHTMLid];
		if (s.menuOpens[idx]) {
			var ids = '', idsLabels = '';
			for (var c in s.menuOpens[idx].children) {
				if (ids!='') ids+=',';
				if (idsLabels!='') idsLabels+=', ';
				idsLabels += c + ' (' + s.menuOpens[idx].children[c] + ')';
				ids += c;
			};
			
			var p = s.items[idx].path;

			if (ids!='') {
				sa.m.log (3, 'sa.vividMenu.hideNode(): ' +idx+ ' --> '+ idsLabels);
				if (sa.m.userDevice.isPhone) {
					jQuery('#'+ids.replace(/,/g,',#')).css({display:'none'});
				} else {
					sa.vcc.fadeOut (ids, sa.menu.globals.timeToOpenClose);
				}
			}

			if (del) {
				clearTimeout (s.menuOpens[idx].timeout);
				clearTimeout (s.menuOpensTemp[idx]);
				delete s.menuOpens[idx];
				delete s.menuOpensTemp[idx];
				sa.menu.logMenuOpens('hideNode 0', s, idx, idx);
			}
			if (done) {
				s.menuOpensTemp[idx] = 'done';
				sa.menu.logMenuOpens('hideNode 1', s, idx, idx);
			}
		}
	},
	
	showNode : function (menuHTMLid, idx, del) {
		var s = sa.vcc.settings[menuHTMLid];
		if (s.menuOpens[idx]) {
			var ids = '', idsLabels = '';
			for (var c in s.menuOpens[idx].children) {
				if (ids!='') ids+=',';
				if (idsLabels!='') idsLabels+=', ';
				idsLabels += c + ' (' + s.menuOpens[idx].children[c] + ')';
				ids += c;
			};
			
			
			var p = s.items[idx].path;

			if (ids!='') {
				sa.m.log (3, 'sa.vividMenu.showNode(): ' +idx+ ' --> '+ idsLabels);
				if (sa.m.userDevice.isPhone) {
					jQuery('#'+ids.replace(/,/g,',#')).css({display:'block'});
				} else {
					sa.vcc.fadeIn (ids, sa.menu.globals.timeToOpenClose);
				};
			}

			if (del) {
				clearTimeout (s.menuOpens[idx].timeout);
				clearTimeout (s.menuOpensTemp[idx].timeout);
				delete s.menuOpens[idx];
				delete s.menuOpensTemp[idx];
				sa.menu.logMenuOpens('showNode', s, idx, idx);
			}
		}
	},

	showNodeTemp : function (menuHTMLid, idx, del) {
		var s = sa.vcc.settings[menuHTMLid];
		if (s.menuOpensTemp[idx]) {
			var initialLevel = s.items[idx].level, childIDs = '', childLabels = '';
			for (var i=idx+1; i<s.items.length && s.items[i] && s.items[i].level > initialLevel; i++) {
				if (s.items[i].level == (initialLevel + 1)) {
					var menuItemHTMLid = menuHTMLid+'__item__'+i;
					if (childIDs!='') childIDs += ',';
					if (childLabels!='') childLabels += ',';
					childIDs += menuItemHTMLid;
					childLabels += i + ' (' + s.items[i].label + ')';
					//sa.menu.logMenuOpens('onmouseover 1', s, idx, i);
				}
			};

			//debugger;
			if (childIDs!='') {
				sa.m.log (3, 'sa.vividMenu.showNodeTemp(): ' +idx+ ' --> '+ childLabels);
				if (sa.m.userDevice.isPhone) {
					jQuery('#'+childIDs.replace(/,/g,',#')).css ({display:'block'});
				} else {
					sa.vcc.fadeIn (childIDs, sa.menu.globals.timeToOpenClose);
				}
			};

			if (del) {
				clearTimeout (s.menuOpensTemp[idx].timeout);
				delete s.menuOpensTemp[idx];
				var children = childIDs.split(', ');
				var children2 = new Array();
				for (var i=0; i<children.length; i++) {
					var 
					c = children[i],
					cp = c.split('__'),
					label = cp[cp.length];
					
					children2[c] = label;
				};
				s.menuOpens[idx] = {
					children : children2,
					timeout : setTimeout(function() {
						sa.menu.hideNode(menuHTMLid, idx, true);
					}, sa.menu.globals.menuCloseLapse) // mouseout's gotta clearTimeout(s.menuOpens[s.previousIdx].timeout);
				};
				sa.menu.logMenuOpens('showNodeTemp', s, idx, idx);
			}
		}
	},

	vividText : function (componentInstanceHTMLid, vividTextTheme) {
		//alert (jQuery('#'+componentInstanceHTMLid+' .vividMenu_item_td').length);
		jQuery('#'+componentInstanceHTMLid+' .vividMenu_item_td').each(function(idx) {
			var ajtCommand = {
				el : this,
				theme : sa.cg.themes[vividTextTheme],
				animationType : 'animatedDoubleColor',
				animationSpeed : 1200
			};
			sa.vs.theme.initElement (ajtCommand);		
		});
	}
};
