seductiveapps.vcc = seductiveapps.vividControls = {
	about : {
		whatsThis : 'seductiveapps.vcc = seductiveapps.vividControls = The core animation code for the vivid* series of webcomponents, in javascript without dependencies',
		copyright : '(c) (r) 2011-2013 by [the owner of seductiveapps.com] <info@seductiveapps.com>',
		license : 'http://seductiveapps.com/seductiveapps/license.txt',
		noWarranty : 'NO WARRANTY EXPRESSED OR IMPLIED. USE ONLY AT YOUR OWN RISK.',
		version : '1.0.0',
		firstReleased : '2011 June',
		lastUpdated : '2012 April 20, 12:00 CEST'
	},
	globals : {
		fadeSteps : 4, // doesnt use fade for isPhone
		stateTransitionSteps : (sa.m.userDevice.isPhone ? 1 : 4)
	},
//themes hold all available themes and their settings, populated automagically by vividControls-1.0.0/get_js.php
	themes : {
/*
		theme_name : {	
			frame : {
				width : integer,
				height : integer,
				//offsetX and Y note how much space a single sprite in the sprite sheet costs.
				offsetX : integer,
				offsetY : Integer,
			},
			states : { 
				normal:{
					frameCount : integer,
					fps : integer,
					...
				}, hover:{...}, selected:{...}, disabled:{...} }	
		}
*/
	},
//options apply to an entire component instance
	options : {
//	componentInstanceHTMLid : {
//		...settings specific to type of component...
//	}	
	}, 
//settings group all operations variables per compontent instance
	settings : {
		fadeCmds : [],
/*
		componentInstanceHTMLid : {
			items : {
				componentItemHTMLid : { // a button has 1 item; itself. A menu can have many items.
					theme : 'trees_001',
					img1, img2 : HTMLELEMENTS
					stateCurrent : 'normal',
					stateOld : null, 
					stateNew : null,
					state : {
						normal : {
							forward : true,
							frame : 0
						}, 
						hover:{...}, selected:{...}, disabled:{...}
					},
				}
			}
		}
*/
		themeOverrides : {}
	},
	inits : [
	// keeps track of when a call to .init() is completed, to call the callback function
	],
	
	setOptions : function (componentInstanceHTMLid, options) {
		sa.vcc.options[componentInstanceHTMLid] = options;
	},
	
	init : function (els, callback, doAnyway, doSelf, initID, initChildren) {
		if (typeof doSelf==='undefined') doSelf = true;
		//if (el && el.id) {
            var els2 = [];
            if (els.length) for (var i in els) els2[els2.length] = els[i].id+'.'+els[i].className;
            else els2 = els.id+'.'+els.className;
            var dbg = { 
                msg : 'sa.vcc.init() called',
                els : els2, callback : callback, doAnyway : doAnyway, doSelf : doSelf, initID : initID, initChildren : initChildren
            };
			sa.m.log (1, dbg);
			setTimeout (function() {
				sa.vcc.initDo (els, callback, doAnyway, doSelf, initID, initChildren);
			}, 20);
		//}
	},
	
	initDo : function (els, callback, doAnyway, doSelf, initID, initChildren) {
		if (!els) els=[document.body];
		if (!els.length) els = [els];

		var 
		newInit = false;
		
		if (!initID) {
			initID = sa.vcc.inits.length;		
			newInit = true;
			var sr = [];
		} else {
			var sr = sa.vcc.inits[initID].sr;
		};

		for (var i=0; i<els.length; i++) {
			var doh = false;
			for (var j=0; j<sr.length; j++) {
				if (!els[i] || sr[j].id === els[i].id) doh = true;
			};
			if (!els[i] || els[i].tagName==='BODY') doh = true;
			if (!doh) sr[sr.length] = {
				id : els[i].id,
				el : els[i]
			};
			sr = sa.vcc.scanElements(els[i], sr, doSelf);
		};
		
		var srt = '';
		for (var i in sr) {
            if (srt!=='') srt+=', ';
			srt += '#' + sr[i].id;
		};

		sa.m.log (2, { msg : 'sa.vividControls.init(): will now start to initialize ' + sa.m.count_keys(sr) + ' components ('+srt+'), initID='+initID } );
		//sa.m.trace(2);

		if (!newInit) {
			for (var i=0; i<sr.length; i++) {
				var found = false;
				for (var j=0; j<sa.vcc.inits[initID].sr.length; j++) {
					if (sa.vcc.inits[initID].sr[j].id===sr[i].id) found=true;
				}
				if (!found) {
					sa.vcc.inits[initID].sr[sa.vcc.inits[initID].sr.length] = sr[i];
				}
			};
		} else {
			sa.vcc.inits[initID] = {
				sr : sr,
				callback : callback, //called (eventually) by sa.vcc.componentFullyInitialized()
				startElapsedMilliseconds : sa.m.elapsedMilliseconds(),
				startTimecode : sa.m.secondsToTimeString(sa.m.elapsedMilliseconds()/1000),
				completedCount : 1,
				completedComponents : []
			};
		};
		//debugger;
		//var elIdx = 0;
        if (sr.length===0) {
            if (typeof callback=='function') {
                callback();
            }
        } else 
		for (var i=0; i<sr.length; i++) {
			//sa.m.log (3, 'sa.vcc.init() : Initializing '+elid);
			
			var alreadyDone = false;
			for (var j=0; j<sa.vcc.inits[initID].completedComponents.length; j++) {
				if (sa.vcc.inits[initID].completedComponents[j] === sr[i].id) alreadyDone = true;
			};
			if (alreadyDone) continue;
			
			
			//delete sa.vcc.settings[elid]; //{-NOTE:THIS IS EXPERIMENTAL-}
			var el = sr[i].el;
			if (true) { //el.className.indexOf('vividInitialized')===-1) {
				if (jQuery('span.saInit__element') && jQuery('span.saInit__element').length>0) jQuery('span.saInit__element').each (function(idx){
					if (this.innerHTML == '#' + el.id) {
						jQuery(this).addClass('saInit__element_runningInit');
					}
				});
			
				var
				proceed = true;
			
				if (sa.site.code && sa.site.code.settings && sa.site.code.settings.visible) {
					proceed = true;//sa.site.code.settings.visible['#'+el.id]
				};
				if (doAnyway===true) {
					proceed = true;
				};

				if (proceed) { // proceed
					if (el.className.indexOf('vividButton')!==-1) {
						jQuery(el).addClass ('vividInitializing');
                        sa.button.init (el, initID);
                        
					}
					if (el.className.indexOf('vividMenu')!==-1) {
						jQuery(el).addClass ('vividInitializing');
                        sa.menu.init (el, initID);
                        
					}
					if (el.className.indexOf('vividTabs')!==-1) {
						jQuery(el).addClass ('vividInitializing');
                        sa.tabs.init (el, initID);
                        
					}
					if (el.className.indexOf('vividScrollpane')!==-1
						&& el.className.indexOf('vividScrollpane_')===-1) {
						jQuery(el).addClass ('vividInitializing');
                        sa.sp.init (el, initID);
					}
					if (el.className.indexOf('vividDialog')!==-1
						) {
						jQuery(el).addClass ('vividInitializing');
                        sa.dialog.init (el, initID, initChildren);
					}
				}
			}
			
			//elIdx++;
		}
	},
	
	initReport_html : function () {
		var 
		r = '',
		inits = sa.vcc.inits;
		
		for (var initID in inits) {
			var 
			initCmd = inits[initID],
			elements = '';

			for (var i=0; i<initCmd.sr.length; i++) {
				var 
				el = initCmd.sr[i].el,
				elInitFinished = false;

				for (var j=0; i<initCmd.completedComponents.length; i++) {
					var cci = initCmd.completedComponents[j];
					if (el.id===cci) elInitFinished = true;
				};
				
				var 
				elInitReportClass = (
					elInitFinished ? 'saInit__element_initFinished' : 'saInit__element_initializing'
				);
				
				elements +=
					'<span class="saInit saInit__element '+elInitReportClass+'">'+ ('#'+el.id) + '</span>'
					+'<span class="saInit saInit__element__spacer"> </span>';
			}
			
			r += 
				'<span class="saInit saInit__initID_label">InitID</span>'
				+'<span class="saInit saInit__initID_punctuation"> = </span>'
				+'<span class="saInit saInit__initID_initID">'+initID+'</span>'
				+'<span class="saInit saInit__initID_punctuation"> : </span>'
				+'<span class="saInit saInit__elements">'+elements+'</span><br/>';
			
		}
		
		return r;
	},
	
	getVividTheme : function (themeName) {
		if (typeof themeName!=='string') {
			return false;
		}
		//debugger;
		if (sa.vcc.themes[themeName]) {
			return sa.vcc.themes[themeName];
		} else {
			if (sa.vcc.themes[themeName.replace(/vividTheme__/,'')]) {
				return sa.vcc.themes[themeName.replace(/vividTheme__/,'')]
			} else {
				return null;
			}
		};
	},
	
	applyTheme : function (componentInstanceHTMLid) {

		//sa.m.log (123, 'sa.vcc.applyTheme : componentInstanceHTMLid = '+componentInstanceHTMLid);
		//debugger;
		var 
		componentInstanceHTMLid = componentInstanceHTMLid.replace('__dialog',''),
		s = sa.vcc.settings[componentInstanceHTMLid];

		if (!s) return false;
		
		var
		dialog = jQuery('#'+componentInstanceHTMLid)[0],
		d = jQuery('#'+componentInstanceHTMLid+'__dialog')[0],
		iframe = jQuery('iframe', dialog)[0];
		//sa.m.log (123, 'sa.vcc.applyTheme : d', d);
		
		if (!d) return false;

        
		d.sidebarsGetMostSpace = false; // hardcoded for now.
		
        
        var
        availableWidth = (
            d.sidebarsGetMostSpace
            ? jQuery(dialog).width() //- maxSBLwidth - maxSBRwidth
            : jQuery(dialog).width()
        ),
        availableHeight = (
            jQuery(dialog).height()
        );
        
        
        
		if (s && dialog.className.match('vividTheme__')) {
			s.themeNameIdx1 = dialog.className.indexOf('vividTheme__');
			s.themeNameIdx2 = dialog.className.indexOf(' ', s.themeNameIdx1);	
			if (s.themeNameIdx2==-1) s.themeNameIdx2 = dialog.className.length;
			//s.themeName = dialog.className.substr(s.themeNameIdx1+12, s.themeNameIdx2-12-s.themeNameIdx1);
            s.themeName = dialog.className.substr(s.themeNameIdx1, s.themeNameIdx2-s.themeNameIdx1);
		};
		
		var t = sa.vcc.getVividTheme(s.themeName);
		s.theme = t;
		
		
		var 
		cssReset1 = {position:'absolute'},
		//cssReset2 = {left:0,top:0,width:'100%',height:'100%'},
		cssForDialogContent = {},
		cssRaw = undefined,
		dialogContentSelector = '.vividDialog',
		sbhf = jQuery('.saSidebarLeftInDialog, .saSidebarRightInDialog, .saHeaderInDialog, .saFooterInDialog',d),
		dsp = jQuery('#'+componentInstanceHTMLid+'__scrollpane',d);
		
		sbhf.css(cssReset1);
		dsp.css(cssReset1);
        
        
		try {
			iframe = (iframe && iframe.contentDocument ? iframe.contentDocument.body : undefined);
		} catch (e) {
			iframe = false;
		}
		
		// applyTheme_do_cssInner will set things like the colors for the dialog and the font types used in that dialog
		if (
			seductiveapps.site.globals
			&& seductiveapps.site.globals.desktop
			&& seductiveapps.site.globals.desktop.configuration
			&& seductiveapps.site.globals.desktop.configuration.dialogCSSinner
			&& seductiveapps.site.globals.desktop.configuration.dialogCSSinner[componentInstanceHTMLid]
		) {
			sa.vcc.applyTheme_do_cssInner (
				componentInstanceHTMLid
				, seductiveapps.site.globals.desktop.configuration.dialogCSSinner[componentInstanceHTMLid]
			);
		};
		if (s && t && t.cssInner && !$(dialog).hasClass('saDontDoCSSinner')) { 
			sa.vcc.applyTheme_do_cssInner (componentInstanceHTMLid, t.cssInner);
		};
		
		//if (dialog.id=='siteAds') debugger;
		var
		css = {}, 
		cssRaw = {};

		//if (componentInstanceHTMLid=='siteContent') debugger;
		
		if (!jQuery(dialog).is('.saBeingChanged') && s && t.cssToExtrapolate) {
			for (var selector in t.cssToExtrapolate) {   
				
				//if (componentInstanceHTMLid=='siteAds') debugger;
				
				if (selector==='.vividDialog') {
                    
					//if (sa.m.settings.initialized.site && componentInstanceHTMLid==='siteContent') debugger;

					// !!!! cssRaw excludes all sidebars, headers and footers from the calculation !!!!
					cssRaw[selector] = sa.vcc.getComputed ('#'+componentInstanceHTMLid, selector, t.cssToExtrapolate[selector], true);
					jQuery('#'+componentInstanceHTMLid+'__contentDimensions_cssRaw').css(cssRaw[selector]);
					//jQuery('#'+componentInstanceHTMLid+'__contentDimensions_cssRaw').css({background:'navy',zIndex:100});
                    
					css[selector] = sa.vcc.getComputed ('#'+componentInstanceHTMLid, selector, t.cssToExtrapolate[selector], false),
					
					//jQuery('#'+componentInstanceHTMLid+'__contentDimensions, #'+componentInstanceHTMLid+' .saContent').css(css[selector]);
					jQuery('#'+componentInstanceHTMLid+'__contentDimensions').css(css[selector]);
                    
                    var hasScrollpane = false;
                    if (
                        document.getElementById(componentInstanceHTMLid).className.match('vividScrollpane')
                        && !document.getElementById(componentInstanceHTMLid).className.match('vividScrollpane__hidden')
                        && !document.getElementById(componentInstanceHTMLid).className.match('vividScrollpane__auto')
                    ) {
                        jQuery('#'+componentInstanceHTMLid+', #'+componentInstanceHTMLid+' .saContent').css ({ top : 0, left : 0, height : 'auto' });
                        hasScrollpane = true;
                    } else if (
                        document.getElementById(componentInstanceHTMLid).className.match('vividScrollpane__hidden')
                    ) {
                        jQuery('#'+componentInstanceHTMLid+', #'+componentInstanceHTMLid+' .saContent').css ({ top : 0, left : 0});
                    }
					//jQuery('#'+componentInstanceHTMLid+'__contentDimensions').css({background:'blue',opacity:0.5,zIndex:101});
					//jQuery('#'+componentInstanceHTMLid).css(css[selector]); // BUG_002__LOC_006
                    
                    if (
                        //sa.desktop.settings.animating === false 
                         sbhf.length>0 
                        && selector==dialogContentSelector
                    ) {
                        //if (componentInstanceHTMLid=='siteAds') debugger;
                            jQuery('.sa_tiedToContentDimensions_cssRaw', jQuery('#'+componentInstanceHTMLid)).css(cssRaw[selector]); //the area indicated by the vividDialog's theme setting cssPropsToExtrapolate
                            jQuery('.sa_tiedToContentDimensions_css', jQuery('#'+componentInstanceHTMLid)).css(css[selector]); //the area between footers, sidebars and headers, and that all within cssRaw.
                            sa.vcc.applyTheme_do_sidebarsHeadersFootersContent (componentInstanceHTMLid, selector);
                    };

                    
                    /*
					if (dialog.id=='siteAds') debugger;
					if (
						//isFinite(css[selector].top) && isFinite(css[selector].left) && isFinite(css[selector].width) && isFinite(css[selector].height)
                        sbhf.length == 0
					) {
						//debugger;
						var leftSide = false;
						var topSide = false;
						var 
						s1 = sa.vcc.settings[dialog.id],
						w = 0;
						if (
							s1
							&& s1.items 
							&& s1.items.length==2 
							&& s1.items[0].type=='vertical' 
							&& s1.items[0].sliderbarShown
						) {
							w = 25;
							if (s.items[0].sliderbar.offsetLeft<100) {
								leftSide = true;
							}
						} else {
							s1 = sa.vcc.settings[dialog.id+'__scrollpane'];
							if (
								s1
								&& s1.items 
								&& s1.items.length==2 
								&& s1.items[0].type=='vertical' 
								&& s1.items[0].sliderbarShown
							) {
								w = 25;
								if (s.items[0].sliderbar.offsetLeft<100) {
									leftSide = true;
								}
							}
						}
						var 
						s1 = sa.vcc.settings[dialog.id],
						h = 0;
						if (
							s1
							&& s1.items 
							&& s1.items.length==2 
							&& s1.items[1].type=='horizontal' 
							&& s1.items[1].sliderbarShown
						) {
							h = 25;
							if (s1.items[1].sliderbar.offsetTop<100) {
								topSide = true;
							}
						} else {
							s1 = sa.vcc.settings[dialog.id+'__scrollpane'];
							if (
								s1
								&& s1.items 
								&& s1.items.length==2 
								&& s1.items[1].type=='horizontal' 
								&& s1.items[1].sliderbarShown
							) {
								h = 25;
								if (s1.items[1].sliderbar.offsetTop<100) {
									topSide = true;
								}
							}
						}
							
						if (
							s1
							&& s1.items 
							&& s1.items.length==2 
							&& s1.items[0].type=='vertical' 
							&& s1.items[0].sliderbarShown
						) {
							css[selector].left = css[selector].left + (leftSide ? 0 : w);
							css[selector].width = css[selector].width - w;//- (leftSide ? (w) : 0);
						}
						if (
							s1
							&& s1.items 
							&& s1.items.length==2 
							&& s1.items[1].type=='horizontal' 
							&& s1.items[1].sliderbarShown
						) {
							css[selector].top = css[selector].top + (topSide ? 0 : h);
							css[selector].height = css[selector].height;// - (topSide ? h : 0);
						}
						
						
						css['CSS3'] = sa.m.cloneObject (css[selector]);
						//delete css['CSS3'].top;
						//delete css['CSS3'].left;
						
						jQuery('#'+componentInstanceHTMLid+'__contentDimensions').css(css[selector]);
						
						//jQuery('#'+componentInstanceHTMLid+'__CSS3').css(css['CSS3']);

						//jQuery('#'+componentInstanceHTMLid+'__scrollpane__container').css(css[selector]);
						
						//debugger;
						/*
						jQuery('#'+componentInstanceHTMLid).css({
							top : 0,
							left : 0
						});* /
						
					} else {
						jQuery('#'+componentInstanceHTMLid+'__contentDimensions').css(cssRaw[selector]);
					}
					jQuery('#'+componentInstanceHTMLid+'__contentDimensions_cssRaw').css(cssRaw[selector]);*/
				/*} else {
					css[selector] = sa.vcc.getComputed ('#'+componentInstanceHTMLid, selector, t.cssToExtrapolate[selector]);
				};*/
                }
				
				if (componentInstanceHTMLid=='mp3s') return true; // APPS FIX 1 : seductiveapps/apps/seductiveapps/musicPlayer
				
				
				var
				selectorToUse = sa.vcc.getComputed_adjustSelector (selector, d),
				css2 = sa.m.cloneObject(css);
				
				//if (dialog.id==='siteAds') debugger;
				
				if (
					selectorToUse==='.vividDialog'
					|| selectorToUse==='.vsp_container'
				) {
					//if (isFinite(css[selector].top) && isFinite(css[selector].left) && isFinite(css[selector].width) && isFinite(css[selector].height)) {
                    if (sbhf.length > 0) {
						cssForDialogContent = css[selector];
					} else {
						cssForDialogContent = cssRaw[selector];
					}
				}
				//cssForDialogContent = css;
                //if (componentInstanceHTMLid=='siteAds') debugger;
				
				//if (selectorToUse==='.vsp_container') dialogContentSelector = selectorToUse; 
					// keep at .vividDialog or sidebars, headers & footers arent gonna get resized properly

                /*
				//if (d.id=='siteAds__dialog') debugger;
                if (!css[selectorToUse]) continue;
				jQuery(selectorToUse+':first', d).css(css[selectorToUse]);
				*/
				//jQuery(selectorToUse, d).first().css(cssForDialogContent );
				
                /*
				if (selectorToUse=='.vsp_container' && css[selector] && css[selector].top!==0 && jQuery(d).parents('.saWall').length==1) {
					jQuery(d).css({height : jQuery(d).height() + css[selector].top * 1.5});
				};
				
				if (selectorToUse=='.vsp_container' && css[selector]) {
					css2.top = 0;
					css2.left = 0;
					//ccs2.position = 'absolute';
					
					jQuery(selectorToUse,d).not(':first').css(css2[selector]);
					jQuery(selector,d).css(css2[selector]);
				};
				//console.log ('bug #1', 'sa.vcc.applyTheme', selector, d, css);
                */
/*
				if (d.id==='siteComments__dialog') {
					debugger;
					s.afterResize();
				};
*/				
				var 
				parentsSaWall = jQuery(selector+':first', d).parents('.saWall');
				
				
				
				//if (selector=='.vd_btns') debugger;
				if (selector=='.vd_btns' && !parentsSaWall.length>0 && !dialog.className.match('saNoVDbtns') /*&& !s.hasEventListenerOnHover*/ ) {
					s.hasEventListenerOnHover = true;
					jQuery(selector,d).css ({display:'block', opacity:0.01}).hover(function() {
						jQuery(selector, d).animate({opacity:1});
					}, function () {
						jQuery(selector, d).animate({opacity:0.01});
					});
				};
				if (selector=='.vd_btns' && parentsSaWall.length==1) {
					jQuery(selector,d).css({display:'none'});
				}
			}
		}; 
		
		jQuery('#'+componentInstanceHTMLid+'__backgrounds').css(cssRaw['.vividDialog']);
		
		/*
        var
		mainEl = jQuery('#'+componentInstanceHTMLid)[0],
		scrollpaneEl = jQuery('#'+componentInstanceHTMLid+'__scrollpane')[0],
		scrollpaneContainerEl = jQuery('#'+componentInstanceHTMLid+'__scrollpane__container')[0];
		if (
			mainEl.className.match('vividScrollpane__')
			&& 
			! (
// 				mainEl.className.match('vividScrollpane__hidden')
				//|| mainEl.className.match('vividScrollpane__')
				|| t.saVividThemeType == 'canvasDialog'
			)
		) {
			//if (mainEl.id=='siteContent') debugger;
			
			jQuery(scrollpaneContainerEl).css(css['.vividDialog']).css ({
				position : 'absolute'
			});
			/*
			//css['.vividDialog']['width'] = css['.vividDialog']['width'] - 200;
			jQuery(scrollpaneEl).css(css['.vividDialog']).css ({
				position : 'absolute'
			});
			
			
			//css['.vividDialog'].width -= 200;
			jQuery(mainEl).css(css['.vividDialog']).css ({
				position : 'absolute',
				height : 'auto' 
			});* /
		}
		*/
		//if (componentInstanceHTMLid=='infoWindow_mp3desc') debugger;
		//debugger;
        
        var
		mainEl = jQuery('#'+componentInstanceHTMLid)[0],
		scrollpaneEl = jQuery('#'+componentInstanceHTMLid+'__scrollpane')[0],
		scrollpaneContainerEl = jQuery('#'+componentInstanceHTMLid+'__scrollpane__container')[0];
        
        
        //debugger;
		if (t.saVividThemeType == 'canvasDialog') {
			//debugger;
			
			// no longer needed, only messes up the layout of scrollbars
			/*
			var cssReset3 = sa.m.cloneObject (cssRaw['.vividDialog']);
			cssReset3.height = jQuery('#'+componentInstanceHTMLid+'__contentDimensions').height();
			jQuery(scrollpaneContainerEl).css(cssReset3);
			*/
			
			// you can't do this here without referencing back into the 'jurisdiction' of vividScrollpane 
            var 
            d = jQuery('#'+componentInstanceHTMLid+'__dialog')[0],
            dialog = jQuery(dialogContentSelector, d)[0],
            sbhf = jQuery('.saSidebarLeftInDialog, .saSidebarRightInDialog, .saHeaderInDialog, .saFooterInDialog',d),
            cd = jQuery('#'+componentInstanceHTMLid+'__contentDimensions')[0],
            cdRaw = jQuery('#'+componentInstanceHTMLid+'__contentDimensions_cssRaw')[0],
            cssReset3 = {
                position : 'absolute',
                top : cd.offsetTop, 
                left : cd.offsetLeft,
                width : cd.offsetWidth, // add slider bar width here or vertical sliderbars show only half of their width
                height : cd.offsetHeight
            },
            cssReset4 = {
                position : 'absolute',
                //top : jQuery('#'+componentInstanceHTMLid+'__contentDimensions')[0].offsetTop, 
                //left : jQuery('#'+componentInstanceHTMLid+'__contentDimensions')[0].offsetLeft,
                width : cd.offsetWidth, // add slider bar width here or vertical sliderbars show only half of their width
                height : cd.offsetHeight
            },
            cssReset5 = {
                //position : 'absolute',
                left : 0,
                top : 0,
                width : cd.offsetWidth,
                height : cd.offsetHeight
            },
            cssReset6 = {
                position : 'absolute',
                left : cd.offsetLeft,
                top : cd.offsetTop,
                width : cd.offsetWidth,
                height : cd.offsetHeight
            },
            cssReset7 = {
                position : 'absolute',
                left : 0,
                top : 0,
                width : cd.offsetWidth,
                height : hasScrollpane?'auto':cd.offsetHeight //jQuery('#'+componentInstanceHTMLid)[0].scrollHeight //'auto' // cd.offsetHeight // 'auto' //jQuery('#'+componentInstanceHTMLid)[0].scrollHeight
            };
            //if (componentInstanceHTMLid=='siteContent') debugger;;
            
            if (cdRaw) {
                var
                cssReset8 = {
                    position : 'absolute',
                    top : cdRaw.offsetTop,
                    left : cdRaw.offsetLeft,
                    width : cdRaw.offsetWidth,
                    height : cdRaw.offsetHeight
                },
                cssReset9 = {
                    position : 'absolute',
                    width : cdRaw.offsetWidth,
                    height : cdRaw.offsetHeight
                },
                cssReset10 = {
                    left : 0,
                    top : 0,
                    width : cdRaw.offsetWidth - 50,
                    height : cdRaw.offsetHeight
                },
                sliderVer = jQuery('#'+componentInstanceHTMLid+'__scrollpane__sliderbar__ver')[0],
                sliderWidth = sliderVer /* && jQuery(sliderVer).css('opacity')==1*/ ? parseFloat(sliderVer.style.width) + 10 : 0,
                cssReset11 = {
                    position : 'absolute',
                    left : cdRaw.offsetLeft,
                    top : cdRaw.offsetTop,
                    width : cdRaw.offsetWidth - sliderWidth,
                    height : cdRaw.offsetHeight,
                    overflow : 'hidden'
                },
                cssReset12 = {
                    // position : 'absolute',
                    left : 0,
                    top : 0,
                    width : cdRaw.offsetWidth,
                    height : cdRaw.offsetHeight,//'', //jQuery('#'+componentInstanceHTMLid)[0].scrollHeight
                    overflow : ''
                },
                cssReset13 = {
                    position : 'relative',
                    left : cdRaw.offsetLeft,
                    top : cdRaw.offsetTop,
                    height : 
                        hasScrollpane ? 'auto' : cdRaw.offsetHeight,
                        /*
                        componentInstanceHTMLid=='siteStatus'
                        || jQuery('#'+componentInstanceHTMLid).hasClass('vividScrollpane__hidden')
                        ? cdRaw.offsetHeight
                        : componentInstanceHTMLid=='siteToolbar'
                          || location.href.match(/musicPlayer/)
                          || location.href.match(/tarot/)
                            ? cdRaw.offsetHeight - 30
                            : 'auto',//cdRaw.offsetHeight - 25,
                        */
                    width : cdRaw.offsetWidth - 30//,
                    //overflow : 'hidden'
                },
                cssReset14 = {
                    // position : 'absolute',
                    width : cdRaw.offsetWidth - sliderWidth,
                    height : cdRaw.offsetHeight,//'', //jQuery('#'+componentInstanceHTMLid)[0].scrollHeight
                    overflow : ''
                }
            }
            if (componentInstanceHTMLid=='infoWindow_mp3desc') debugger;
            var
            timeDelay = 10,
            timeDelayAdded = 5;

            //debugger;
            if (jQuery('iframe', mainEl).length>0
                && jQuery('iframe', mainEl)[0].src.match(sa.m.globals.urls.app)
            ) {
                var cw = jQuery('iframe', mainEl)[0].contentWindow;
                var sp = jQuery('iframe', mainEl)[0].contentWindow.document.getElementsByTagName('body')[0];
                
                if (sbhf.length>0) {						
                    jQuery(mainEl).css (cssReset6);
                    jQuery('iframe', mainEl).css(cssReset6);
                } else {
                    jQuery(mainEl).css (cssReset8);
                    jQuery('iframe', mainEl).css(cssReset8);
                }
                
            } else var sp = null;
                
                        //if (sbhf.length > 0) {
                if (d.timeoutScrollpaneUpdates) clearTimeout(d.timeoutScrollpaneUpdates);
                if (sbhf.length>0) {						
                    //setTimeout (function () {
                        //if (componentInstanceHTMLid=='siteYoutubeSearch') {
                        if (
                            (
                                !sp 
                                && document.getElementById(componentInstanceHTMLid).className.match('vividScrollpane')
                                && !document.getElementById(componentInstanceHTMLid).className.match('vividScrollpane__hidden')
                                && !document.getElementById(componentInstanceHTMLid).className.match('vividScrollpane__auto')
                            )
                            || document.getElementById(componentInstanceHTMLid).className.match('saZeroTopLeft')
                        ) {
                            jQuery(mainEl).css(cssReset7);
                        } else {
                            jQuery(mainEl).css(cssReset6);
                        }
                        /*
                        if (
                            jQuery('.saContent',mainEl).length===0
                        ) jQuery(mainEl).css({height:'auto'});
                        */ // tarot bugfix
                    //}, timeDelay);

                    //setTimeout (function () {
                        jQuery(scrollpaneContainerEl).css(cssReset6);
                    //}, timeDelay + (1 * timeDelayAdded));
                    
                    //setTimeout (function () {
                        jQuery(scrollpaneEl).css(cssReset4);
                        jQuery(scrollpaneEl).css({overflow:'hidden'});
                    //}, timeDelay + (2 * timeDelayAdded));
                    
                } else {
                    //setTimeout (function () {
                        //if (mainEl.id=='siteContent') debugger;
                        if (!sp && mainEl.className.indexOf('saNoResize')===-1) {
                            if (
                                (
                                    mainEl.className.indexOf('vividScrollpane')!==-1
                                    && mainEl.className.indexOf('vividScrollpane__hidden')===-1
                                    && mainEl.className.indexOf('vividScrollpane__auto')===-1
                                    && mainEl.className.indexOf('vividScrollpane__visible')===-1
                                )
                                ||mainEl.className.indexOf('saZeroTopLeft')===-1
                            ) {
                                cssReset13.left = 0;
                                cssReset13.top = 0;
                            };

                            jQuery(mainEl).css(cssReset13);
                        }
                        //jQuery(mainEl).css({height:'auto'});
                    //}, timeDelay);

                    //setTimeout (function () {
                        jQuery(scrollpaneContainerEl).css(cssReset11);
                    //}, timeDelay + (1 * timeDelayAdded));
                    
                    //setTimeout (function () {
                        jQuery(scrollpaneEl).css(cssReset14); //was cssReset14
                        
                    //}, timeDelay + (2 * timeDelayAdded));
                    
                }
            
            

		
		
            //sa.sp.onresize (jQuery('#'+mainEl.id+'__scrollpane')[0]);
            //setTimeout (function () {
            
            /* done by saDesktop
                for (var appID in sa.apps.loaded) {
                    var app = sa.apps.loaded[appID];
                    
                    if (app.settings) {
                        for (var divID in app.settings) {
                            var appInstance = app.settings[divID];
                            
                            if (appInstance && typeof appInstance.onresize=='function') {
                                appInstance.onresize();
                            }
                        }
                    }
                }
            */
            //}, 1000);
        }
        
    },
	
	
	applyTheme_do_sidebarsHeadersFootersContent : function (componentInstanceHTMLid, dialogContentSelector) {
		var
		//dialog = jQuery('#'+componentInstanceHTMLid)[0],
		d = jQuery('#'+componentInstanceHTMLid+'__dialog')[0],
		cd = jQuery('#'+componentInstanceHTMLid+'__contentDimensions')[0],
		cdRaw = jQuery('#'+componentInstanceHTMLid+'__contentDimensions_cssRaw')[0],
		dialog = jQuery(dialogContentSelector, d)[0],
		sbhf = jQuery('.saSidebarLeftInDialog, .saSidebarRightInDialog, .saHeaderInDialog, .saFooterInDialog',d),
		sbl = jQuery('.saSidebarLeftInDialog', d),
		sbr = jQuery('.saSidebarRightInDialog', d),
		headers = jQuery('.saHeaderInDialog', d),
		footers = jQuery('.saFooterInDialog', d),
		content = jQuery('#'+componentInstanceHTMLid+'__content')[0],
		dsp = (
			jQuery('#'+componentInstanceHTMLid+'__scrollpane__container',d).length>=1
			? jQuery('#'+componentInstanceHTMLid+'__scrollpane__container',d)
			: jQuery('#'+componentInstanceHTMLid+'__content',d)
		);
        
		sbl.each(function(idx,el) {
			var
			componentInstanceHTMLid = el.id.split('__')[0],
			//dialog = jQuery('#'+componentInstanceHTMLid)[0],
			d = jQuery('#'+componentInstanceHTMLid+'__dialog')[0],
			dialog = jQuery(dialogContentSelector, d)[0],
			sbl = jQuery('.saSidebarLeftInDialog', d),
			sbr = jQuery('.saSidebarRightInDialog', d),
			headers = jQuery('.saHeaderInDialog', d),
			footers = jQuery('.saFooterInDialog', d),
			dsp = (
				jQuery('#'+componentInstanceHTMLid+'__scrollpane__container',d).length>=1
				? jQuery('#'+componentInstanceHTMLid+'__scrollpane__container',d)
				: jQuery('#'+componentInstanceHTMLid+'__content',d)
			),
			maxSBLwidth = 0,
			maxSBRwidth = 0,
			maxHeaderHeight = 0;
			maxFooterHeight = 0;
            var
            availableWidth = (
                jQuery(cdRaw).width()
            ),
            availableHeight = (
                jQuery(cdRaw).height()
            );

		   
			for (var i=0; i<sbl.length; i++) {
				if (jQuery(sbl[i]).width() > maxSBLwidth) maxSBLwidth = jQuery(sbl[i]).width();
			};			
			for (var i=0; i<sbr.length; i++) {
				if (jQuery(sbr[i]).width() > maxSBRwidth) maxSBRwidth = jQuery(sbr[i]).width();
			};
			for (var i=0; i<headers.length; i++) {
				if (jQuery(headers[i]).height() > maxHeaderHeight) maxHeaderHeight = jQuery(headers[i]).height();
			};
			for (var i=0; i<footers.length; i++) {
				if (jQuery(footers[i]).height() > maxFooterHeight) maxFooterHeight = jQuery(footers[i]).height();
			};
            /*
			if (maxSBLwidth>0) maxSBLwidth += 5;
			if (maxSBRwidth>0) maxSBRwidth += 5;
			if (maxHeaderHeight>0) maxHeaderHeight += 5;
			if (maxFooterHeight>0) maxFooterHeight += 5;
                 */
			
			var
			availableHeight = (
				d.sidebarsGetMostSpace
				? jQuery(dialog).height()
				: jQuery(dialog).height - maxHeaderHeight - maxFooterHeight
			),
			h = el.style.height,
			css = {
				left : cdRaw.offsetLeft,
				//width : jQuery(cdRaw).width() - maxSBLwidth - maxSBRwidth,
				height : (
					d.sidebarsGetMostSpace
					? (
                        cdRaw.offsetHeight
                    )
                    : (
                        cdRaw.offsetHeight - maxHeaderHeight - maxFooterHeight
                    )
				),
				top : (
					d.sidebarsGetMostSpace
					? (
						idx === 0
						? cdRaw.offsetTop
						: cdRaw.offsetTop + jQuery(sbl[idx-1])[0].offsetTop + jQuery(sbl[idx-1]).height()
					)
					: (
						idx === 0
						? cdRaw.offsetTop + maxHeaderHeight
						: cdRaw.offsetTop + maxHeaderHeight + jQuery(sbl[idx-1])[0].offsetTop + jQuery(sbl[idx-1]).height()
					)
				)
			};
			
			jQuery(el).css(css);			
		});
		
		sbr.each(function(idx,el) {
			var
			componentInstanceHTMLid = el.id.split('__')[0],
			//dialog = jQuery('#'+componentInstanceHTMLid)[0],
			d = jQuery('#'+componentInstanceHTMLid+'__dialog')[0],
			dialog = jQuery(dialogContentSelector, d)[0],
			sbl = jQuery('.saSidebarLeftInDialog', d),
			sbr = jQuery('.saSidebarRightInDialog', d),
			headers = jQuery('.saHeaderInDialog', d),
			footers = jQuery('.saFooterInDialog', d),
			dsp = (
				jQuery('#'+componentInstanceHTMLid+'__scrollpane__container',d).length>=1
				? jQuery('#'+componentInstanceHTMLid+'__scrollpane__container',d)
				: jQuery('#'+componentInstanceHTMLid+'__content',d)
			),
			maxSBLwidth = 0,
			maxSBRwidth = 0,
			maxHeaderHeight = 0;
			maxFooterHeight = 0;

            var
            availableWidth = (
                jQuery(cdRaw).width()
            ),
            availableHeight = (
                jQuery(cdRaw).height()
            );

            
			for (var i=0; i<sbl.length; i++) {
				if (jQuery(sbl[i]).width() > maxSBLwidth) maxSBLwidth = jQuery(sbl[i]).width();
			};			
			for (var i=0; i<sbr.length; i++) {
				if (jQuery(sbr[i]).width() > maxSBRwidth) maxSBRwidth = jQuery(sbr[i]).width();
			};
			for (var i=0; i<headers.length; i++) {
				if (jQuery(headers[i]).height() > maxHeaderHeight) maxHeaderHeight = jQuery(headers[i]).height();
			};
			for (var i=0; i<footers.length; i++) {
				if (jQuery(footers[i]).height() > maxFooterHeight) maxFooterHeight = jQuery(footers[i]).height();
			};
            /*
 			if (maxSBLwidth>0) maxSBLwidth += 5;
			if (maxSBRwidth>0) maxSBRwidth += 5;
			if (maxHeaderHeight>0) maxHeaderHeight += 5;
			if (maxFooterHeight>0) maxFooterHeight += 5;
                 */
			

            var
            h = el.style.height,
			css = {
				left : cdRaw.offsetLeft + cdRaw.offsetWidth - maxSBRwidth,
				//width : jQuery(cdRaw).width() - maxSBLwidth - maxSBRwidth,
				height : (
					d.sidebarsGetMostSpace
					? (
                        cdRaw.offsetHeight
                    )
                    : (
                        cdRaw.offsetHeight - maxHeaderHeight - maxFooterHeight
                    )
				),
				top : (
					d.sidebarsGetMostSpace
					? (
						idx === 0
						? cdRaw.offsetTop
						: cdRaw.offsetTop + jQuery(sbr[idx-1])[0].offsetTop + jQuery(sbr[idx-1]).height()
					)
					: (
						idx === 0
						? cdRaw.offsetTop + maxHeaderHeight
						: cdRaw.offsetTop + maxHeaderHeight + jQuery(sbr[idx-1])[0].offsetTop + jQuery(sbr[idx-1]).height()
					)
				)
			};
			
			jQuery(el).css(css);			
		});	
		
		headers.each(function(idx,el) {
			var
			componentInstanceHTMLid = el.id.split('__')[0],
			//dialog = jQuery('#'+componentInstanceHTMLid)[0],
			d = jQuery('#'+componentInstanceHTMLid+'__dialog')[0],
			dialog = jQuery(dialogContentSelector, d)[0],
			sbl = jQuery('.saSidebarLeftInDialog', d),
			sbr = jQuery('.saSidebarRightInDialog', d),
			headers = jQuery('.saHeaderInDialog', d),
			footers = jQuery('.saFooterInDialog', d),
			dsp = (
				jQuery('#'+componentInstanceHTMLid+'__scrollpane__container',d).length>=1
				? jQuery('#'+componentInstanceHTMLid+'__scrollpane__container',d)
				: jQuery('#'+componentInstanceHTMLid+'__content',d)
			),
			maxSBLwidth = 0,
			maxSBRwidth = 0,
			maxHeaderHeight = 0;
			maxFooterHeight = 0;
		   
            var
            availableWidth = (
                jQuery(cdRaw).width()
            ),
            availableHeight = (
                jQuery(cdRaw).height()
            );

			for (var i=0; i<sbl.length; i++) {
				if (jQuery(sbl[i]).width() > maxSBLwidth) maxSBLwidth = jQuery(sbl[i]).width();
			};			
			for (var i=0; i<sbr.length; i++) {
				if (jQuery(sbr[i]).width() > maxSBRwidth) maxSBRwidth = jQuery(sbr[i]).width();
			};
			for (var i=0; i<headers.length; i++) {
				if (jQuery(headers[i]).height() > maxHeaderHeight) maxHeaderHeight = jQuery(headers[i]).height();
			};
			for (var i=0; i<footers.length; i++) {
				if (jQuery(footers[i]).height() > maxFooterHeight) maxFooterHeight = jQuery(footers[i]).height();
			};
			if (maxSBLwidth>0) maxSBLwidth += 5;
			if (maxSBRwidth>0) maxSBRwidth += 5;
			if (maxHeaderHeight>0) maxHeaderHeight += 5;
			if (maxFooterHeight>0) maxFooterHeight += 5;
					 
			//if (componentInstanceHTMLid=='siteYoutubeSearch') debugger;
            var
            h = jQuery(headers[idx])[0].style.height,
			css = {
				left : (
                    sbl.length > 0 || sbr.length > 0
                    ? (
                        d.sidebarsGetMostSpace
                        ? (
                            idx === 0
                            ? cdRaw.offsetLeft + maxSBLwidth
                            : cdRaw.offsetLeft + jQuery(headers[idx-1])[0].offsetLeft + jQuery(headers[idx-1]).width()
                        )
                        : (
                            idx === 0
                            ? cdRaw.offsetLeft
                            : cdRaw.offsetLeft + jQuery(headers[idx-1])[0].offsetLeft + jQuery(headers[idx-1]).width()
                        )

                    )
                    : cdRaw.offsetLeft 
				),
               /*
				width : (
                    sbl.length > 0 || sbr.length > 0    
                    ? (
                        d.sidebarsGetMostSpace
                        ? (
                            idx === 0
                            ? availableWidth - (cd.offsetLeft + maxSBLwidth)
                            : avaliableWidth - (cd.offsetLeft + jQuery(headers[idx-1])[0].offsetLeft + jQuery(headers[idx-1]).width())
                        )
                        : (
                            idx === 0
                            ? availableWidth - (cd.offsetLeft)
                            : availableWidth - (cd.offsetLeft + jQuery(headers[idx-1])[0].offsetLeft + jQuery(headers[idx-1]).width())
                        )

                    )
                    : availableWidth
                ), 
               */
                width : (
                    d.sidebarsGetMostSpace
                    ? availableWidth - maxSBLwidth - maxSBRwidth
                    : availableWidth
                ),
				height : h /*(
					typeof h === 'string'
					? Math.floor ( ( jQuery(cdRaw).height() - (sbr.length * 5) ) / sbr.length )
					: typeof h === 'number'
						? parseFloat(h)
						: Math.floor ( ( jQuery(cdRaw).height() - (sbr.length * 5) ) / sbr.length )
				)*/,
				top : (
					idx === 0
					? cdRaw.offsetTop
					: cdRaw.offsetTop + jQuery(headers[idx-1])[0].offsetTop + jQuery(headers[idx-1]).height()
				)
			};
            //if (componentInstanceHTMLid=='siteContent') debugger;
			jQuery(el).css(css);			
		});	
		
		footers.each(function(idx,el) {
			var
			componentInstanceHTMLid = el.id.split('__')[0],
			//dialog = jQuery('#'+componentInstanceHTMLid)[0],
			d = jQuery('#'+componentInstanceHTMLid+'__dialog')[0],
			dialog = jQuery(dialogContentSelector, d)[0],
			sbl = jQuery('.saSidebarLeftInDialog', d),
			sbr = jQuery('.saSidebarRightInDialog', d),
			headers = jQuery('.saHeaderInDialog', d),
			footers = jQuery('.saFooterInDialog', d),
			dsp = (
				jQuery('#'+componentInstanceHTMLid+'__scrollpane__container',d).length>=1
				? jQuery('#'+componentInstanceHTMLid+'__scrollpane__container',d)
				: jQuery('#'+componentInstanceHTMLid+'__content',d)
			),
			maxSBLwidth = 0,
			maxSBRwidth = 0,
			maxHeaderHeight = 0;
			maxFooterHeight = 0;
            
            var
            availableWidth = (
                jQuery(cdRaw).width()
            ),
            availableHeight = (
                jQuery(cdRaw).height()
            );
            
		   
			for (var i=0; i<sbl.length; i++) {
				if (jQuery(sbl[i]).width() > maxSBLwidth) maxSBLwidth = jQuery(sbl[i]).width();
			};			
			for (var i=0; i<sbr.length; i++) {
				if (jQuery(sbr[i]).width() > maxSBRwidth) maxSBRwidth = jQuery(sbr[i]).width();
			};
			for (var i=0; i<headers.length; i++) {
				if (jQuery(headers[i]).height() > maxHeaderHeight) maxHeaderHeight = jQuery(headers[i]).height();
			};
			for (var i=0; i<footers.length; i++) {
				if (jQuery(footers[i]).height() > maxFooterHeight) maxFooterHeight = jQuery(footers[i]).height();
			};
			if (maxSBLwidth>0) maxSBLwidth += 5;
			if (maxSBRwidth>0) maxSBRwidth += 5;
			if (maxHeaderHeight>0) maxHeaderHeight += 5;
			if (maxFooterHeight>0) maxFooterHeight += 5;
					 
					 
			var
			h = el.offsetHeight,
			css = {
				left : (
					d.sidebarsGetMostSpace
					? (
						idx === 0
						? cdRaw.offsetLeft + maxSBLwidth
						: cdRaw.offsetLeft + jQuery(headers[idx-1])[0].offsetLeft + jQuery(headers[idx-1]).width()
					)
					: (
						idx === 0
						? cdRaw.offsetLeft 
						: cdRaw.offsetLeft + jQuery(headers[idx-1])[0].offsetLeft + jQuery(headers[idx-1]).width()
					)
				),
                width : (
                    d.sidebarsGetMostSpace
                    ? availableWidth - maxSBLwidth - maxSBRwidth
                    : availableWidth
                ),
				height : h,
				top : cdRaw.offsetTop + cdRaw.offsetHeight - h
			};
			//if (componentInstanceHTMLid=='siteContent') debugger;
			jQuery(el).css(css);			
		});			
        
        
        /*
        setTimeout (function () {
            var 
            l = 0, ml = 0,
            t = 0, mt = 0,
            lw = 0, mlw = 0,
            rw = 0, mrw = 0,
            tf = 0, mtf = 0,
            h = 0, mh =0;
            
            jQuery('.saHeaderInDialog', d).each(function (idx, el) {
                if (el.offsetTop > mt) mt = el.offsetTop;
                if (el.offsetHeight > mh) mh = el.offsetHeight;
            });
            jQuery('.saSidebarLeftInDialog', d).each(function (idx, el) {
                if (el.offsetLeft > mlw ) mlw = el.offsetLeft;
            });
            jQuery('.saSidebarRightInDialog', d).each(function (idx, el) {
                if (mrw === 0) mrw = d.offsetWidth - el.offsetLeft - el.offsetWidth;
                if (el.offsetLeft - el.offsetWidth < mrw) mrw = d.offsetWidth - el.offsetLeft - el.offsetWidth;
            });
            jQuery('.saFooterInDialog', d).each(function (idx, el) {
                if (mtf === 0) mtf = el.offsetTop;
                if (el.offsetTop < mtf) mtf = el.offsetTop;
            });
            
            if (componentInstanceHTMLid=='siteYoutubeSearch') debugger;
            jQuery('#'+componentInstanceHTMLid+'__content').css ({ // content window inside vividDialog
                top : mt + mh + 5,
                height : jQuery(dialog).height - mt - mh - 10 - (mtf===0?0:mtf+5),
                left : mlw,
                width : jQuery(cd).width()
            });
            jQuery('#'+componentInstanceHTMLid+'__scrollpane, #'+componentInstanceHTMLid+'__scrollpane__container').css ({ // content window inside vividDialog
                height : jQuery(dialog).height - mt - mh - 10 - (mtf===0?0:mtf+5),
                width : d.offsetWidth - mlw - mrw 
            });
            jQuery('#'+componentInstanceHTMLid+'__scrollpane').css ({ height : 'auto' });
            setTimeout (function() {
                sa.sp.containerSizeChanged (jQuery('#'+componentInstanceHTMLid+'__scrollpane')[0], true);
            }, 100);
        }, 300);
        */
        
	},
	
	applyTheme_do_cssInner : function (componentInstanceHTMLid, cssSettings) {
		var 
		s = sa.vcc.settings[componentInstanceHTMLid],
		dialog = jQuery('#'+componentInstanceHTMLid)[0],
		d = jQuery('#'+componentInstanceHTMLid+'__dialog')[0],
		iframe = jQuery('iframe', dialog)[0];
		try {
			iframe = (iframe && iframe.contentDocument ? iframe.contentDocument : undefined);
		} catch(e) {
			iframe = undefined;
		}
		
		
		for (var i=0; i<cssSettings.length; i++) {
			var 
			cssInner = cssSettings[i],
			sel = ' '+cssInner.jQuerySelector,
			css = cssInner.css,
			inIframe = cssInner.inIframe,
			notSelIn = cssInner.jQueryFilter_notSelectorIn,
			notSelInSel = (
				typeof notSelIn === 'object'
				? notSelIn.jQuerySelector
				: undefined
			),
			notSelInIframe = (
				typeof notSelIn === 'object'
				? notSelIn.inIframe
				: undefined
			);
			
			if (typeof notSelIn==='object') {
				var 
				selp = sel.split(',');
				for (var j=0; j<selp.length; j++) {
					selp[j] = notSelInSel + selp[j];
				}
				var 
				notSelInSelR = selp.join(', ');
			}
			
			
			var
			cmd = 
				'jQuery("'+sel+'"'
				+ (
					inIframe
					? ', iframe'
					: ', d'
				)
				+ ')'
				+ '.not(".vividMenu td").not(".mceText")'
				+ (
					typeof notSelIn==='object'
					? '.not("'+notSelInSelR+'"'
					: ''
				)
				+ (
					typeof notSelIn==='object'
					&& notSelInIframe
					? ', iframe'
					: ''
				)
				+ (
					typeof notSelIn==='object'
					? ')'
					: ''
				)
				+ '.css(css);';
			//console.log ('sa.vcc.applyTheme', componentInstanceHTMLid, sel, cssInner, css, cmd, iframe, jQuery('.vividDialog',d),  jQuery(" a", iframe).not(".externalSite a", iframe));
			//debugger;
			if ((
				inIframe
				? iframe
				: d
			)) eval (cmd);	
		}
	},
	
	hookEvent : function (el, eventName, handler, useCapture, add) {
		if (add) {
			if (el.addEventListener) {
			// Standards browsers
				el.addEventListener (eventName, handler, useCapture);
			} else if (el.attachEvent) {
			// IE < v9.0
				el.attachEvent ('on'+eventName, handler);
			}
		} else {
			if (el.removeEventListener) {
			// Standards browsers
				el.removeEventListener (eventName, handler, useCapture);
			} else if (el.detachEvent) {
				el.detachEvent ('on'+eventName, handler);
			}
		}
	},
	
	scanElements : function (el, scanResult, doSelf) {
		if (!scanResult) scanResult=[];
        if (!el) debugger; //return scanResult;
		if (!el.tagName) debugger;
		if (el.tagName.toLowerCase()!='svg') {
			var found = false;
			for (var i=0; i<scanResult.length; i++) {
				if (scanResult[i].id===el.id) found = true;
			};
		
			if (
				doSelf
				&& el.tagName!=='body'
                && el.className.indexOf('saNoImmediateInit')===-1
                && el.style.display!=='none'
				&& !found
				&& (	
					el.className.indexOf('vividButton')!==-1
					|| (
						el.className.indexOf('vividMenu')!==-1
						&& el.className.indexOf('vividMenu_item')===-1
					)
					|| el.className.indexOf('vividTabs')!==-1
					|| el.className.indexOf('vividScrollpane')!==-1
					|| (
						el.className.indexOf('vividDialog')!==-1
						&& el.className.indexOf('vividDialog_dialog')===-1
					)
				) && el.className.indexOf('vividWidget_initialized')===-1
				&& el.className.indexOf('vividInitializing')===-1

			) {
				scanResult[scanResult.length] = {
					id : el.id,
					el : el
				};
			}
			if (
                el.className.indexOf('saNoImmediateInit')===-1
                && el.style.display!=='none'
                && el.children.length>0
            ) {
				for (var i=0; i<el.children.length; i++) {
					if (el.children[i].tagName.toUpperCase()!=='IFRAME') {
						if (typeof sa.tracer!=='undefined' && sa.lucidLog && sa.lucidLog.globals.available) {
                            var args = [el.children[i], scanResult];
    						args.ua= sa.tracer.findUA(arguments);
    						//if (!args.ua) debugger;
    						var passUAfunc = function(scanResult) {
    							return sa.vividControls.scanElements(el.children[i], scanResult, doSelf);
    						};
    						passUAfunc.ua = args.ua;
    						scanResult = passUAfunc(scanResult);
						} else {
    					    scanResult = sa.vcc.scanElements (el.children[i], scanResult, doSelf);   
						}
					}
				}
			}
		}
		return scanResult;
	},
	
	newInstanceNeeded : function (cihe) {
		var 
		s = sa.vcc.settings[cihe.id],
		r = true;
		
		if (typeof s==='object') {
			if (typeof s.themeName==='string') r = false;
		};
		
		return r;		
	},
	
	newInstance : function (componentInstanceHTMLelement, initID) {
		var t = componentInstanceHTMLelement.className;
		sa.m.log (3980, { msg : 'sa.vividControls.newInstance(): newInstance for '+componentInstanceHTMLelement.id } );
                
        //debugger;
		var start = t.indexOf('vividTheme__');
		if (start===-1) {
			sa.m.log (1, { warning : 'sa.vividControls.newInstance(): WARNING : No theme set for '+componentInstanceHTMLelement.id } );
		} else {
			var 
			end = t.indexOf(' ', start+1);
			
			if (end===-1) end = t.length;

			var
			themeName = t.substr(start, end-start),
			theme = sa.vcc.themes[themeName];
			
			//debugger;
			if (sa.vcc.settingOverrides) themeOverride = sa.vcc.settingOverrides['#'+componentInstanceHTMLelement.id]; else themeOverride = null;
			
			var
			s = sa.vcc.settings[componentInstanceHTMLelement.id];
			
			if (!s) {
				sa.vcc.settings[componentInstanceHTMLelement.id] = {};
			}

			var
			s = sa.vcc.settings[componentInstanceHTMLelement.id],
			items = (s ? s.items : null);
			//debugger;
			
			if (s) {			
				sa.vcc.settings[componentInstanceHTMLelement.id] = {
					initID : initID,
					themeName : themeName,
					theme : (
						themeOverride
						? sa.m.negotiateOptions (theme, themeOverride)
						: theme
					),				
					items : [],
					originalContent : s.originalContent,
					afterDesktopResize : s.afterDesktopResize,
					onload : s.onload
				}
			} else {
				sa.vcc.settings[componentInstanceHTMLelement.id] = {
					initID : initID,
					themeName : themeName,
					theme : (	
						themeOverride	
						? sa.m.negotiateOptions (theme, themeOverride)
						: theme
					),				
					items : []
				}
			};
		}
	},
	
	
	newInstanceThemeOverrides : function (s, t) {
		return s;
		/*
		s.theme = (
			s.themeOverride
			? sa.m.negotiateOptions (t, s.themeOverride)
			: t
		);
		return s;*/
	},
	
	componentFullyInitialized : function (initID, componentInstanceHTMLid, goDeep) {
		//setTimeout (function () {
			sa.vcc.componentFullyInitialized_do (initID, componentInstanceHTMLid, goDeep);
		//}, 50);
	},
	
	componentFullyInitialized_do : function (initID, componentInstanceHTMLid, goDeep) {
		var init = sa.vcc.inits[initID];
        if (!init) return false;
        
        //if (init.callbackTimeout) clearTimeout (init.callbackTimeout);
        //init.callbackTimeout = setTimeout (init.callback, 1000); // ends up calling callback twice!
		
		if (typeof goDeep=='undefined') goDeep=false; // WAS true
		jQuery('#'+componentInstanceHTMLid).addClass('vividInitialized');
		if (!init) {
			sa.m.log (1, { msg : 'sa.vividControls.componentFullyInitialized(): invalid initID '+initID } );
			sa.m.trace(1);
		} else {
			var 
			found = undefined;
				
			if (!init.srToDo) init.srToDo = sa.m.cloneObject (init.sr);
			
			while (found!==false) {
				//debugger;
				if (init.srToDo.length > 1) {
					for (var i=0; i<init.srToDo.length; i++) {
						if (init.srToDo[i] && init.srToDo[i].id===componentInstanceHTMLid) {
							found = i;
							delete init.srToDo[i];
							i--;
							break;
						}				
					}
				}

				//debugger;
			
				if (found!==false) {
					init.completedComponents[init.completedComponents.length] = componentInstanceHTMLid;
					init.completedCount++;
					if ((init.completedCount)>=init.sr.length) {
						if (init.completedFired) {
							//debugger;
							sa.m.log (1, { msg : 'sa.vividControls.componentFullyInitialized(): initID='+initID+'; HTML .id to complete just now ='+componentInstanceHTMLid+', ***count***='+sa.m.count_keys(init.sr)+', completedCount='+(init.completedCount-1)+', init.completedFired='+(init.completedFired?'true':'false') });
						} else {
							init.completedFired = true;
							init.completedElapsedMilliseconds = sa.m.elapsedMilliseconds();
							init.completedTimecode = sa.m.secondsToTimeString(sa.m.elapsedMilliseconds()/1000);
							//if (sa.desktop) sa.desktop.resize();
							if (typeof init.callback == 'function') {
								sa.m.log (2, { msg : 'sa.vividControls.componentFullyInitialized(): all components for initID='+initID+' are now ready, calling callback function' } );
								var c = '';
								for (var i=0; i<init.completedComponents.length; i++) {
									if (c!='') c+=',';
									c += '#'+init.completedComponents[i]+'__container.vsp_container';
								}
								// BUG321 jQuery(c).css({opacity:1});
									//if (componentInstanceHTMLid=='iframeContent') debugger;
                                clearTimeout (init.callbackTimeout);
								init.callback()
							} else {
								sa.m.log (2, { msg : 'sa.vividControls.componentFullyInitialized(): all components for initID='+initID+' are now ready, no callback function specified though..' } );
							}
						}
					}
				};
				if (goDeep) {
					for (var iID=0; iID<sa.vcc.inits.length; iID++) {
						if (iID!=initID) sa.vcc.componentFullyInitialized (iID, componentInstanceHTMLid, false);
					};
				}

				var 
				found = false;


			}
			
			var srt = '{none}';
            for (var i in init.srToDo) {
                var td = init.srToDo[i];
                if (td && td.id) {
                    if (srt=='{none}') srt = td.id; else srt = ', '+td.id;
                }
            };
                    
			
			//debugger;
			sa.m.log (3, { 
				msg : 'sa.vividControls.componentFullyInitialized(): initID='+initID+'; componentInstanceHTMLid='+componentInstanceHTMLid+', count='+init.sr.length+', completedCount='+(init.completedCount-1)+', init.completedFired='+(init.completedFired?'true':'false') +', componentsYetToDo='+srt,
				componentsYetToDo : init.srToDo
			});
		}
	},
	
	getComputed_adjustSelector : function (selector, d) {
		var r = selector;
		switch (selector) {
			case '.vividDialog':
				r = (
					jQuery('.vsp_container', d).length > 0
					&& jQuery('.vsp_container', d)[0].id == d.id.replace('__dialog','')+'__scrollpane__container'
					//|| d.id=='siteAds__dialog'
					? '.vsp_container'
					: selector // checked for bug #1; not the cause
				);			
				break;
		};
			
		return r;
	},
	
	getComputed : function (dialogJqueryID, selector, cssPropsToExtrapolate, ignoreSidebarsHeadersAndFooters) {
		var 
		dialog = jQuery(dialogJqueryID)[0],
		d = jQuery(dialogJqueryID+'__dialog')[0],
		bp = dialog.className.indexOf ('vividTheme__'),
		ep = dialog.className.indexOf (' ', bp),
		ep = ( ep === -1 ? dialog.className.length : ep),
		vividTheme = dialog.className.substr (bp, ep-bp),
		t = sa.vcc.themes[vividTheme],
		w = false,
		wr = {};
		
		if (!t) { debugger; return {
			vividTheme : vividTheme,
			dialogJqueryID : dialogJqueryID,
			top : 0,
			left : 0,
			width : '100%',
			height : '100%'
		}};
		
		var
		wr = {},
		wrRaw = {};
		
		for (var prop in cssPropsToExtrapolate) {

            //if (dialogJqueryID=='#siteYoutubeSearch' && ignoreSidebarsHeadersAndFooters === true) debugger;
			if (ignoreSidebarsHeadersAndFooters === true) {
				var
				pv = cssPropsToExtrapolate[prop],
				pvp = ( pv.split ? pv.split('-') : pv );
				
				if (!pvp.length || pvp.length===1) {
					var
					pvr = sa.vcc.getComputed_value (d, dialog, selector, t, prop, pv, true);
				} else {
					var
					pvr0 = sa.vcc.getComputed_value (d, dialog, selector, t, prop, pvp[0], true),
					pvr1 = sa.vcc.getComputed_value (d, dialog, selector, t, prop, pvp[1], true),				
					pvr = pvr0 - pvr1;
				};
				
				wrRaw[prop] = pvr;
				
			} else {
				
				var
				pv = cssPropsToExtrapolate[prop],
				pvp = ( pv.split ? pv.split('-') : pv );
				
				if (!pvp.length || pvp.length===1) {
					var
					pvr = sa.vcc.getComputed_value (d, dialog, selector, t, prop, pv, false);
				} else {
					var
					pvr0 = sa.vcc.getComputed_value (d, dialog, selector, t, prop, pvp[0], false),
					pvr1 = sa.vcc.getComputed_value (d, dialog, selector, t, prop, pvp[1], false),				
					pvr = pvr0 - pvr1;
				};
				//if (Math.round(pvr)===78) debugger;
				
				//if (prop=='top' && jQuery(dialogJqueryID).parents('.saWall').length==1) {
					//debugger;
				//};
				wr[prop] = pvr;
			}
			
			//if (dialogJqueryID=='#siteContent')
			//sa.m.log (1, 't47', dialogJqueryID, selector, prop, pv, pvp, pvr0, pvr1, pvr, wr, d.offsetWidth, d.offsetHeight);
		};
		
		//if (Math.round(wr.left)==78) debugger;
        //if (dialogJqueryID=='#siteAds') debugger;
		if (ignoreSidebarsHeadersAndFooters === true ) return wrRaw; else return wr;
	},
	
	getComputed_value : function (d, dialog, selector, t, prop, pv, ignoreSidebarsHeadersAndFooters) {
		/*if (t.spriteImage) {
			var
			nw = t.spriteImage.frames.width,
			nh = t.spriteImage.frames.height;
		} else {*/
			var
			nw = d.offsetWidth,
			nh = d.offsetHeight;
		//};

        
        
        /* // used to work prior to jQuery-3.2.1 now the , d context results in 0 hits for the : clause of the ? : if statement
		var
		aw = (
			ignoreSidebarsHeadersAndFooters
			? d.offsetWidth
			: d.offsetWidth - jQuery('.saSidebarLeftInDialog, .saSidebarRightInDialog',d).width()
		),
		ah = (
			ignoreSidebarsHeadersAndFooters
			? d.offsetHeight
			: d.offsetHeight - jQuery('.saHeaderInDialog, .saFooterInDialog', d).height()
		);
		*/ 
		var 
		aw = d.offsetWidth,
        ah = d.offsetHeight;
        //if (d.id=='siteAds__dialog') debugger;
        if (ignoreSidebarsHeadersAndFooters!==true) {
            
            var
            elementsClassnamesHor = '.saSidebarLeftInDialog, .saSidebarRightInDialog',            
            elementsClassnamesVer = '.saHeaderInDialog, .saFooterInDialog';
            jQuery (elementsClassnamesHor,d).each (function (idx, el) {
                    aw = aw - el.offsetWidth;
            });
            
            jQuery (elementsClassnamesVer,d).each (function (idx, el) {
                    ah = ah - el.offsetHeight;
            });
        }

        
        
		if (pv.match && pv.match('%')) {
			var
			pvr = parseFloat(pv.replace('%','')),
			pvret = (
				prop === 'top'
				|| prop === 'height'
				? (
					((ah * pvr) / 100)
				) 
				: (
					((aw * pvr) / 100)					
				)
			);/*,
			pvret = (
				prop === 'top'
				|| prop === 'height'
				? (
					((ah * c1) / 100)
				) 
				: (
					((aw * c1) / 100)					
				)
			);*/
			
		} else {
			var 
			pv = parseFloat(pv),
			c1 = (
				prop === 'top'
				|| prop === 'height'
				? (
					((pv * 100) / nh)
				) 
				: (
					((pv * 100) / nw)					
				)
			), 
			pvret = (
				prop === 'top'
				|| prop === 'height'
				? (
					((ah * c1) / 100)
				) 
				: (
					((aw * c1) / 100)					
				)
			);
			
			//if (Math.round(pvret)===78) debugger;
			//if (Math.round(pvret)===39) debugger;
			
			//if (dialog.id=='#siteContent')
			//sa.m.log (1, 't48', dialog.id, selector, prop, pv, nh, nw, c1, ah, aw, pvret);
		};

		if (ignoreSidebarsHeadersAndFooters !== true) {
        	switch (prop) {
				case 'left' : 
					jQuery('.saSidebarLeftInDialog', d).each (function (idx, el) {
                        pvret += el.offsetWidth;
                    });
					break;
				case 'top' : 
					jQuery('.saHeaderInDialog', d).each (function (idx, el) {
                        pvret += el.offsetHeight;
                    });
					break;
				case 'width' : 
					jQuery('.saSidebarLeftInDialog, .saSidebarRightInDialog', d).each (function (idx, el) {
                        pvret -= el.offsetWidth;
                    });
					break;
				case 'height' : 
					jQuery('.saHeaderInDialog, .saFooterInDialog', d).each (function (idx, el) {
                        pvret -= el.offsetHeight;
                    });
					break;
			};
		};

		return pvret;
	},

	getItemIndex : function (componentItemHTMLelement) {
		if (!componentItemHTMLelement) {
			sa.m.log (1, { warning : 'sa.vividControls.getItemIndex(): componentItemHTMLelement missing. This COULD be a bug yet-to-be-fixed, sorry. Usually non-fatal.' } );
			return false;
		}
		var id = componentItemHTMLelement.id;
		id = id.split('__');
		i = id[id.length-1];
		if (isNaN(i)) i = id[id.length-2];
		return parseInt(i);
	},

	animateItem : function (componentInstanceHTMLelement, componentItemHTMLelement, forward, isKickOff) {
		if (componentItemHTMLelement.style.display==='none') return false;
	
		var ci = componentInstanceHTMLelement; 
        if (ci) {
            var s = sa.vcc.settings[ci.id];
        } else {
            ci = componentItemHTMLelement; 
            var s = sa.vcc.settings[ci.id]; 
        };
		if (!s) return false;
		var o = sa.vcc.options[ci.id];
		var item = s.items[sa.vcc.getItemIndex(componentItemHTMLelement)];
		if (!item) return false;
		var t = item.theme?item.theme:s.theme;//sa.vcc.themes[item.themeName?item.themeName:s.themeName];
		
		if (
			!item.stateCurrent 
			&& !item.stateNew
			&& !item.stateOld
		) {
			item.stateCurrent = 'normal';
			item.state= {
				'normal' : {
					frame : 0, forward : true
				},
				'hover' : {
					frame : 0, forward : true
				},
				'selected' : {
					frame : 0, forward : true
				},
				'disabled' : {
					frame : 0, forward : true
				}
			}
		}

		var animationProceeds = true;
		if (o && o.items && o.items && o.items[componentItemHTMLelement.id]
			&& o.items[componentItemHTMLelement.id].events 
			&& o.items[componentItemHTMLelement.id].events.initialized
		) {
			var e = o.items[componentItemHTMLelement.id].events.initialized;
			delete o.items[componentItemHTMLelement.id].events.initialized; //prevent endless loop
			animationProceeds = false;
			e (
				ci,
				item
			);
		}

		var frameSlowness = 0;
		if (item.stateCurrent) {
		// We're in a mode of using a single button state
			var st = item.state[item.stateCurrent]; // st = state
			
			st.resetDone = false;
			
			// advance animation 1 step/frame			
			st.newFrame = st.frame;
			if (forward === false) st.forward = false;
			if (forward === true) st.forward = true;
			if (st.forward) st.newFrame++; else st.newFrame--;

			// bounce between forward=true and forward=false when running out of frames			
			var th = sa.vcc.themes[s.themeName].state[item.stateCurrent];
            fts = th.framesToSlow,            
			animationProceeds = (
				(st.forward || th.animationLoopsBackAndForth || th.animationLoopsForward)
				|| 
				(t.animateToFirstFrameOnMouseOut && !st.forward && st.newFrame>0)
			);
            if (typeof fts!=='number') fts = 0;
            if (fts < 7) fts = 7; // 7 seems to be a magic number here
            
			if (st.newFrame >= (th.frameCount- fts)) {
				if (th.animationLoopsBackAndForth) {
					st.forward = false;
					st.newFrame = th.frameCount - fts;
				} else if (th.animationLoopsForward) {
					st.newFrame = 0;
					if (
						typeof th.stateTransitionSteps=='undefined'
						|| th.stateTransitionSteps>0
					) {
						st.resetDone = true;
						st.transitionStepsDone = 0;
						//st.frame=0;
						//if (componentItemHTMLelement.id==='siteMenu__item__18') debugger;
						sa.vcc.animateItemToFirstFrame (componentInstanceHTMLelement, componentItemHTMLelement);
						return false; //don't mess with the operations done by animateItemToFirstFrame
					}
				} else {
					st.newFrame = th.frameCount -1;	
					animationProceeds = false;
				}
			};
			if (st.newFrame < 0) {
				if (th.animationLoopsBackAndForth) {
					st.forward = true;
				};
				st.newFrame = 0;
			};
			
			
			if (th.animationLoopsBackAndForth) {
				// start slowing the framerate at the beginning and the end of every cycle
				if (st.newFrame < th.framesToSlow ) {
					frameSlowness = th.framesToSlow - st.newFrame;
				}
				if (st.newFrame > (th.frameCount - th.framesToSlow)) {
					frameSlowness = th.framesToSlow  - ( th.frameCount - st.newFrame );
				}
			}	
			var fps = th.fps;
			if (frameSlowness>0) {
				fps = 
					th.fps
					-( 
						( ( th.fps - th.fpsSlow ) 
						     / th.framesToSlow
						) * frameSlowness
					);
			};
			
			// note where we are in the animation
			st.frame = st.newFrame;				
			st.transitionStepsDone++;

			// get the right sprite from the sprite sheet s.image.url
			st.framePos = sa.vcc.getFramePos (componentInstanceHTMLelement, componentItemHTMLelement, item.stateCurrent);
			
			//bugfix:
			item.img1 = document.getElementById(componentInstanceHTMLelement.id + '__item__'+i+'__img1');
			item.img2 = document.getElementById(componentInstanceHTMLelement.id + '__item__'+i+'__img2');

			if (item.img1 && item.img2) {
				if (!item.type || item.type!='dialog') {
				//Buttons, Menus
					// Standards Browsers:
					item.img1.style.backgroundPosition = (-1 * st.framePos.x) + 'px ' + (-1 * st.framePos.y) + 'px';
					// Internet Explorer: 
					item.img1.style.backgroundPositionX = (-1 * st.framePos.x) + 'px';
					item.img1.style.backgroundPositionY = (-1 * st.framePos.y) + 'px';
				} else {			
				//Dialogs
					if (st.frame<st.frameCount) {
						var n = '00'; if (st.frame>=10) n='0'; if (st.frame>=100) n='';
						item.img1.src = 
							t.state[item.stateCurrent].frames
							&& t.state[item.stateCurrent].frames[st.frame]
							&& sa.s.c.settings.online
							? t.state[item.stateCurrent].frames[st.frame]
							: t.baseURL + (item.themeName?item.themeName:s.themeName) + '/frame_'+item.stateCurrent+'_'+n+st.frame+'.png';
					}
				}
				sa.vcc.setOpacity (item.img1, (t.state && t.state[item.stateCurrent] && t.state[item.stateCurrent].opacity?t.state[item.stateCurrent].opacity:1));
				sa.vcc.setOpacity (item.img2, 0);
			}
			//sa.m.log ('sa.vividControls.animateItem(): frame = '+st.frame);
			//debugger;

		} else {
		// we're transitioning between button states
			var stn = item.state[item.stateNew];
			var sto = item.state[item.stateOld];
			var tn = sa.vcc.themes[s.themeName].state[item.stateNew];
			var to = sa.vcc.themes[s.themeName].state[item.stateOld];
			
			// advance animation 1 step/frame (for both states)
			stn.newFrame = stn.frame;
			if (forward === false) stn.forward = false;
			if (forward === true) stn.forward = true;
			if (stn.forward) stn.newFrame++; else stn.newFrame--;
			sto.newFrame = sto.frame;
			if (sto.forward) sto.newFrame++; else sto.newFrame--;
			
			// bounce between forward=true and forward=false when running out of frames (for both states)
			timeDivisor = tn.frameCount;
			stn.animationProceeds = (
				stn.forward 
				|| 
				tn.animationLoopsBackAndForth
				||
				tn.animationLoopsForward
				||
				tn.animateToFirstFrameOnMouseOut
			);
			sto.animationProceeds = (
				sto.forward 
				|| 
				to.animationLoopsBackAndForth
				||
				to.animationLoopsForward
				||
				to.animateToFirstFrameOnMouseOut
			);
			animationProceeds = stn.animationProceeds || sto.animationProceeds;

			if (stn.newFrame >= tn.frameCount) {
				if (tn.animationLoopsBackAndForth) {
					stn.forward = false;
				} 
				stn.newFrame = tn.frameCount - 1;
			};
			if (stn.newFrame < 0) {
				if (tn.animationLoopsBackAndForth) {
					stn.forward = true;
				}; 
				stn.newFrame = 0;
			};
			if (sto.newFrame >= to.frameCount) {
				if (to.animationLoopsBackAndForth) {
					sto.forward = false;
				} 
				sto.newFrame = to.frameCount - 1;
			};
			if (sto.newFrame < 0) {
				if (to.animationLoopsBackAndForth) {
					sto.forward = true;
				};
				sto.newFrame = 0;
			};
			if (
				stn.newFrame==0 
				&& !stn.forward 
				&& !tn.animationLoopsBackAndForth
				&& sto.newFrame==0 
				&& !sto.forward
				&& !to.animationLoopsBackAndForth
			) {
				animationProceeds = false;
			}
			
			var fps = tn.fps;
			var changeFPS = false;
			if (sto.animationLoopsBackAndForth) {
				// start slowing the framerate at the beginning and the end of every cycle
				if (sto.newFrame < to.framesToSlow ) {
					frameSlowness = to.framesToSlow - sto.newFrame;
					changeFPS = true;
				}
				if (sto.newFrame > (to.frameCount - to.framesToSlow)) {
					frameSlowness = to.framesToSlow - (to.frameCount - sto.newFrame);
					changeFPS = true;
				}
				if (changeFPS) {
					fps = 
						to.fps
						-( 
							( ( to.fps - to.fpsSlow ) 
							     / to.framesToSlow
							) * frameSlowness
						);
				};
			}	
			if (stn.animationLoopsBackAndForth) {
				// start slowing the framerate at the beginning and the end of every cycle
				changeFPS = false;
				if (stn.newFrame < tn.framesToSlow ) {
					frameSlowness = tn.framesToSlow - stn.newFrame;
					changeFPS = true;
				}
				if (stn.newFrame > (tn.frameCount - tn.framesToSlow)) {
					frameSlowness = tn.framesToSlow - (tn.frameCount - stn.newFrame);
					changeFPS = true;
				}
				if (changeFPS) {
					fps = 
						tn.fps
						-( 
							( ( tn.fps - tn.fpsSlow ) 
							     / tn.framesToSlow
							) * frameSlowness
						);
				};
			};
			
			// note where we are in the animation
			stn.frame = stn.newFrame;				
			sto.frame = sto.newFrame;				

			//bugfix:
			item.img1 = document.getElementById(componentInstanceHTMLelement.id + '__item__'+i+'__img1');
			item.img2 = document.getElementById(componentInstanceHTMLelement.id + '__item__'+i+'__img2');

			if (item.img1 && item.img2) {
				if (!item.type || item.type!='dialog') {
				//Buttons, Menus
					// get the right sprites from the sprite sheet s.image.url
					stn.framePos = sa.vcc.getFramePos (componentInstanceHTMLelement, componentItemHTMLelement, item.stateNew);
					sto.framePos = sa.vcc.getFramePos (componentInstanceHTMLelement, componentItemHTMLelement, item.stateOld);
					// Standards Browsers:
					item.img1.style.backgroundPosition = (-1 * stn.framePos.x) + 'px ' + (-1 * stn.framePos.y) + 'px';
					item.img2.style.backgroundPosition = (-1 * sto.framePos.x) + 'px ' + (-1 * sto.framePos.y) + 'px';
					// Internet Explorer: 
					item.img1.style.backgroundPositionX = (-1 * stn.framePos.x) + 'px';
					item.img1.style.backgroundPositionY = (-1 * stn.framePos.y) + 'px';
					item.img2.style.backgroundPositionX = (-1 * sto.framePos.x) + 'px';
					item.img2.style.backgroundPositionY = (-1 * sto.framePos.y) + 'px';
		
				} else {
				//Dialogs
					
					
					if (stn.frame<stn.frameCount) {
						var nn = '00'; if (stn.frame>=10) nn='0'; if (stn.frame>=100) nn='';
						item.img1.src = t.baseURL + item.theme + '/frame_'+item.stateNew+'_'+nn+stn.frame+'.png';
					}

					if (sto.frame<sto.frameCount) {
						var on = '00'; if (sto.frame>=10) on='0'; if (stn.frame>=100) on='';
						item.img2.src = t.baseURL + item.theme + '/frame_'+item.stateOld+'_'+on+sto.frame+'.png';
					}
				};
	
				sts = sa.vcc.globals.stateTransitionSteps;
				if (tn.stateChange) {
					sts = tn.stateChange.steps;
				}
				o1 = (tn.opacity?tn.opacity:1);
				o2 = 0;
				o1 -=stn.transitionStepsDone / sts;
				o2 +=stn.transitionStepsDone / sts;
	
				// the magic swap: (don't touch!)
				sa.vcc.setOpacity (item.img2, o1);
				sa.vcc.setOpacity (item.img1, o2);
			};
		
			stn.transitionStepsDone++;
			if (stn.transitionStepsDone>sts) {
				item.stateCurrent = item.stateNew;
				item.stateNew = null;
				item.stateOld = null;
			}
		}

		// continue with next frame after a split-second delay
		if (
			isKickOff 
			||
			(
				animationProceeds
				&& !item.animationTimeout
			)
		) {
			clearTimeout(item.animationTimeout);
			item.animationTimeout = setTimeout (function () {
				delete item.animationTimeout;
				sa.vcc.animateItem (componentInstanceHTMLelement, componentItemHTMLelement, null, false);
			}, 1000/fps);
		}
	},
	
	animateItemToFirstFrame : function (componentInstanceHTMLelement, componentItemHTMLelement) {
		var ci = componentInstanceHTMLelement; 
		var s = sa.vcc.settings[ci.id];
		if (!s) return false;
		var item = s.items[sa.vcc.getItemIndex(componentItemHTMLelement)];
		if (!item) return false;
		if (item.stateCurrent) {
			var st = item.state[item.stateCurrent];
			var t = sa.vcc.themes[s.themeName].state[item.stateCurrent];
			st.transitionStepsDone++;

			//bugfix:
			if (!item.img1 || !item.img1.parentNode) 
				item.img1 = document.getElementById(componentItemHTMLelement.id.replace(/__td/,'__img1'));
			if (!item.img2 || !item.img2.parentNode) 
				item.img2 = document.getElementById(componentItemHTMLelement.id.replace(/__td/,'__img2'));

			// get the right sprites from the sprite sheet s.image.url
			st.frame = 0;
			st.framePos = sa.vcc.getFramePos (componentInstanceHTMLelement, componentItemHTMLelement, item.stateCurrent);
			item.img1.style.backgroundPosition = (-1 * st.framePos.x) + 'px ' + (-1 * st.framePos.y) + 'px';
			item.img1.style.backgroundPositionX = (-1 * st.framePos.x) + 'px';
			item.img1.style.backgroundPositionY = (-1 * st.framePos.y) + 'px';

			st.frame = t.frameCount-1;
			st.framePos = sa.vcc.getFramePos (componentInstanceHTMLelement, componentItemHTMLelement, item.stateCurrent);
			item.img2.style.backgroundPosition = (-1 * st.framePos.x) + 'px ' + (-1 * st.framePos.y) + 'px';
			item.img2.style.backgroundPositionX = (-1 * st.framePos.x) + 'px';
			item.img2.style.backgroundPositionY = (-1 * st.framePos.y) + 'px';
			
			st.frame = 0;

			var o2 = 1 - (st.transitionStepsDone / (t.stateTransitionSteps?t.stateTransitionSteps:sa.vcc.globals.stateTransitionSteps));
			//if (componentItemHTMLelement.id==='siteMenu__item__18') sa.m.log (1, 't25', o2, st.transitionStepsDone, t.stateTransitionSteps, sa.vcc.globals.stateTransitionSteps);
			sa.vcc.setOpacity (item.img1, 1);
			sa.vcc.setOpacity (item.img2, o2);
			//if (componentItemHTMLelement.id==='siteMenu__item__18') debugger;

			if (st.transitionStepsDone < (t.stateTransitionSteps?t.stateTransitionSteps:sa.vcc.globals.stateTransitionSteps)) {
				// the magic swap: (don't touch!)
				clearTimeout(st.animationTimeout);
				st.animationTimeout = setTimeout (sa.m.traceFunction(function () {
					sa.vcc.animateItemToFirstFrame(componentInstanceHTMLelement, componentItemHTMLelement);
				}), 1000/t.fps);
			} else {
				clearTimeout(st.animationTimeout);
				st.animationTimeout = setTimeout (sa.m.traceFunction(function () {
					sa.vcc.animateItem(componentInstanceHTMLelement, componentItemHTMLelement, true, true);
				}), 1000/t.fps);
			}
		}		
	},
	
	setOpacity : function (el, opacity) {
		if (el) {
		//if (el.filters) {
		// Internet Explorer
//			el.filters.item(0).opacity = opacity * 100;
		//} else {
		// Standards Browsers
			el.style.opacity = opacity;
		//}
		}
	},
	
	populateThemes : function (themes) {
		sa.vcc.themes = themes;
		for (var tn in themes) {
			var t = sa.vcc.themes[tn];
                        if (typeof t!=='object') continue;
			//t.baseURL = sa.vcc.themes.baseURL + t.baseURL; // no longer needed (20170502)
			
			/*if (sa.s.c.settings.online) {
				switch (t.themeType) {
					case 'vividButtonOrMenu' : t.baseURL = 'http://seductiveapps.byethost14.com/siteImages/vividThemes/buttonsAndMenus/'; break;
					case 'vividDialog' : t.baseURL = 'http://seductiveapps.byethost14.com/siteImages/vividThemes/dialogs/'; break;
					case 'vividScrollpane' : t.baseURL = 'http://seductiveapps.byethost14.com/siteImages/vividThemes/scrollpane/'; break;
					case 'vividScrollpane' : t.baseURL = 'http://seductiveapps.byethost14.com/siteImages/vividThemes/vividTabs/'; break;
					default :
						sa.m.log (1, 'sa.vcc.populateThemes() : INVALID THEME for "'+tn+'"');
						break;
				};
			};*/
			//t.url = t.baseURL + tn.replace('vividTheme__','') + '.png';
			if (typeof t==='object') {
                            /*if (t.saVividThemeType=='vividDialog') {
                                t.url = t.baseURL + t.state.normal.frames[0];// .replace('.png','') + '/frame_normal_000.png';
                            } else {
                                //debugger;*/
								
                            //}
                        }
			for (var s in t.state) {
				var st = t.state[s];
					
				if (!st.fps) st.fps = 18; // frames per second; reasonably fluid, not too stressing on the CPU&GPU
				if (!st.fpsSlow) st.fpsSlow = 10;
				if (!st.fpsFramesToSlow) st.fpsFramesToSlow = 5;
			}
		}
	},

	getFramePos : function (componentInstanceHTMLelement, componentItemHTMLelement, state) {
	// gets the position of the wanted sprite in the sprite sheet
		var s = sa.vcc.settings[componentInstanceHTMLelement.id];
		var item = s.items[sa.vcc.getItemIndex(componentItemHTMLelement)];
		var t = sa.vcc.themes[s.themeName];
		var ts = t.state[state];
        if (!t.spriteImage) return false;

		var frameIdx = sa.vcc.getFrameIndex(componentInstanceHTMLelement, componentItemHTMLelement, state) + item.state[state].frame;

		// 2015/06/17 23:28 CET - latest chrome update dont like for-loops being called all the time..
		var 
		maxFitY = Math.floor ( 2040 / t.spriteImage.frames.offsetY ),
		bottomLeftOver = 2040 - maxFitY,
		wantYspace = frameIdx * t.spriteImage.frames.offsetY,
		fittedColumns = Math.floor (frameIdx / maxFitY);

		var
		// FAILS : y = wantYspace - Math.floor ( wantYspace / maxFitY ),
		y = wantYspace - ( (fittedColumns ) * (maxFitY * t.spriteImage.frames.offsetY)),
		x = (fittedColumns  ) * t.spriteImage.frames.offsetX;

		// there *is* a reason i quit practical university. math.


		// 2004 - 2015/06/17
		/* FAIL (also with Math.ceil() ):
		var x = Math.floor ( (t.spriteImage.frames.offsetY * frameIdx) / 2040 ) * t.spriteImage.frames.offsetX;
		var y = (t.spriteImage.frames.offsetY * frameIdx) - Math.floor ( (t.spriteImage.frames.offsetY * frameIdx) / 2040 );
		*/ 

		
		/*var x = y = 0;
		for (var i = 0; i < frameIdx; i++) {
			y += t.spriteImage.frames.offsetY;
			if (y > (2040-t.spriteImage.frames.offsetY)) {
				x += t.spriteImage.frames.offsetX;
				y = 0;//-1 * t.spriteImage.frames.offsetY;
			};
		}*/
		if (y<0) y=0;
		
		//debugger;
		var r = {
			x : x,
			y : y
		};
		
		return r;
	},

	getFrameIndex : function (componentInstanceHTMLelement, componentItemHTMLelement, state) {
	// finds the index of the first frame for any given ItemState
		var r = 0;
		switch (state) {
			case 'hover' : 
				r += sa.vcc.getFrameCountForItemState (componentInstanceHTMLelement, componentItemHTMLelement, 'normal');
				break;
			case 'selected' : 
				r += sa.vcc.getFrameCountForItemState (componentInstanceHTMLelement, componentItemHTMLelement, 'normal');
				r += sa.vcc.getFrameCountForItemState (componentInstanceHTMLelement, componentItemHTMLelement, 'hover');
				break;
			case 'disabled' : 
				r += sa.vcc.getFrameCountForItemState (componentInstanceHTMLelement, componentItemHTMLelement, 'normal');
				r += sa.vcc.getFrameCountForItemState (componentInstanceHTMLelement, componentItemHTMLelement, 'hover');
				r += sa.vcc.getFrameCountForItemState (componentInstanceHTMLelement, componentItemHTMLelement, 'selected');
				break;

		};
		return r;
	},
	
	getFrameCountForItemState : function (componentInstanceHTMLelement, componentItemHTMLelement, state) {
		var s = sa.vcc.settings[componentInstanceHTMLelement.id];
		var item = s.items[sa.vcc.getItemIndex(componentItemHTMLelement)];
		var t = sa.vcc.themes[s.themeName];
		switch (typeof t.state[state]) {
			case 'string' : 
				return 0;
			case 'object' : 
				return t.state[state].frameCount;
		}
		return false;
	},
	
	fadeIn : function (ids, duration) {
		var cmd = sa.vcc.setupFadeCmd(ids,duration,true);
		sa.vcc.doFade (cmd);		
	},
	
	fadeOut : function (ids, duration) {
		var cmd = sa.vcc.setupFadeCmd(ids,duration,false);
		sa.vcc.doFade (cmd);		
	},
	
	setupFadeCmd : function (ids,duration,fadeIn) {
		
		
		var cmd = {items:[],startOpacities:[],duration:duration,animationStepsDone:0,fadeIn:fadeIn};
		var idx = sa.vcc.settings.fadeCmds.length;
		//sa.m.log (1, 't13.8', cmd, idx);
		idz = ids.split(',');
		for (var i in idz) {
			var el = document.getElementById(idz[i]);
			if (el) {
				cmd.items[cmd.items.length] = el;
				//if (el.filters) {
				// Internet Explorer
				//	cmd.startOpacities[cmd.startOpacities.length] = parseInt(el.filters.item(0).opacity) / 100;
				//} else {
					cmd.startOpacities[cmd.startOpacities.length] = parseFloat(el.style.opacity);
				//}
			}
		};
		
		for (var i=0; i<idx; i++) {
			var c = sa.vcc.settings.fadeCmds[i];
			
			var found = c.items.length == cmd.items.length;
			if (found) {
				for (var j=0; j<c.items.length && found; j++) {
					if (c.items[j] !== cmd.items[j]) found = false;
				}
			}
			
			if (found) c.cancelled = true;
		};

		sa.vcc.settings.fadeCmds[idx] = cmd;
		
		return cmd;
	},
	
	doFade : function (cmd) {
		cmd.animationStepsDone++;
		for (var i=0; i<cmd.items.length; i++) {
			//i = parseInt(i);
			var item = cmd.items[i];

			if (cmd.animationStepsDone==1) {
				if (cmd.fadeIn) {
					//sa.m.log (1, 't13.7');
					item.style.display='block';
					for (var j=0; j<cmd.items.length; j++) {
						var 
						it = cmd.items[j],
						pcihe = it.id.split('__'),
						cihe = document.getElementById(pcihe[0]);
						
						
						sa.vcc.animateItem(cihe, it, true, true);
					};
					
					cmd.startOpacities[i] = 0; // hack to make it work, copying jquery functionality
				}
			}

			if (cmd.animationStepsDone==sa.vcc.globals.fadeSteps) {
				//sa.m.log (1, 't13.6', cmd);
				if (!cmd.fadeIn) item.style.display='none';
			}

			if (cmd.animationStepsDone<=sa.vcc.globals.fadeSteps) {
				if (cmd.fadeIn) {
					var nextOpacity = 
						cmd.startOpacities[i] 
						+ (( 
								(1 - cmd.startOpacities[i]) / sa.vcc.globals.fadeSteps 
							) * cmd.animationStepsDone);
				} else {
					var nextOpacity = 
						parseFloat(cmd.startOpacities[i])
						- (( 
								cmd.startOpacities[i] / sa.vcc.globals.fadeSteps 
							) * cmd.animationStepsDone);
				};
				
				//sa.m.log (1, 't13.5', item, nextOpacity);
				
				sa.vcc.setOpacity (item, nextOpacity);
			}
		}
		if (!cmd.cancelled && cmd.animationStepsDone<sa.vcc.globals.fadeSteps) {
			setTimeout (sa.m.traceFunction(function () {
				sa.vcc.doFade (cmd);				
			}), cmd.duration/sa.vcc.globals.fadeSteps);
		}
	},
	
	addEventListener : function (componentInstanceHTMLelement, componentItemHTMLelement, event, callback) {
		var s = sa.vcc.settings[componentInstanceHTMLelement.id];
		var item = s.items[sa.vcc.getItemIndex(componentItemHTMLelement)]; 
		var t = sa.vcc.themes[s.themeName];
		
		if (!item.events) item.events={};
		item.events[event] = callback;
	},
	
	changeState : function (componentInstanceHTMLelement, componentItemHTMLelement, newState) {
		if (!componentInstanceHTMLelement) {
			var ciid = componentItemHTMLelement.id.split('__')[0];
			componentInstanceHTMLelement = document.getElementById(ciid);
		}
		if (!componentInstanceHTMLelement || !componentItemHTMLelement) {
			sa.m.log (1, { warning : 'sa.vividControls.changeState(): WARNING; invalid items to change state passed, maybe the item is not initialized yet?' } );
			sa.m.trace(1);
			//debugger;
			return false;
		};
		var s = sa.vcc.settings[componentInstanceHTMLelement.id];
		var item = s.items[sa.vcc.getItemIndex(componentItemHTMLelement)]; 
		if (!item.state[newState]) {
			sa.m.log (1, { error : 'sa.vividControls.changeState(): Invalid newState="'+newState+'"' } );
			//sa.m.trace(1);
			return false;
		};
		//sa.m.log (1100, { sample : 1, item : item } );
		if (!item.state) {
			sa.m.trace(1200);
			return false;
		};
		
		var t = sa.vcc.themes[s.themeName];
		if (typeof t.state[newState]==='string') {
			debugger;
			sa.vcc.error (componentInstanceHTMLelement.id, 'changeState', 'requested state "'+newState+'" reports "'+t.state[newState]+'". ');
		} else {

			if (jQuery(componentInstanceHTMLelement).parents('#siteStatus').length==0) {
				if (
					newState==='hover' 
					&& item.url  
					&& (
						item.url !== '#'
						&& item.url !== sa.m.globals.urls.app+'#'
					)
				) {
					sa.statusbar.update (item.url, false, false);
				} else {
                    // done instead when the menu has fully collapsed back.
					//if (sa.statusbar.settings.current.msg) sa.statusbar.update (sa.statusbar.settings.current.msg, false, false, true);
				};
			};
		/*
			sa.m.log ('changeState(): newState='+newState);
			sa.m.log ('changeState(): oldState='+item.stateOld);
			sa.m.log ('changeState(): s.stateCurrent='+item.stateCurrent);
		*/
        	clearTimeout (item.animationTimeout);
            
            
            // 2018-January
            // vividTheme__menu_005 combined with
            // regular video buttons/menu-items,
            // won't work properly unless
            // this section is kept exactly as is.
            
            
			if (item.stateCurrent) {
                item.stateOld = item.stateCurrent;
                item.statePrevious = item.stateCurrent;
                item.stateCurrent = null;
				item.stateNew = newState;
				item.state[newState].transitionStepsDone = 0;
				item.state[newState].frame = 0;
				item.state[newState].forward = true;
			} else {
			// already in a transition, cancel that one and set up for the new transition
                /* 2018-01-29 : can't get one frame per state buttons to work properly.
                var 
                sts = sa.vcc.globals.stateTransitionSteps,
                its = item.stateNew,//item.stateOld;//( item.newState ? item.newState : newState);
                its2 = ( item.newState ? item.newState : newState);
                //debugger;
                if (s.theme.state[its].frameCount > sts
                    
                    && !(s.theme.state[its2].frameCount > sts)
                    
                ) {*/
                if (
                    item.stateNew!=='selected'
                    && item.stateNew!=='disabled'
                ) {
                    item.stateOld = item.stateNew;
                    item.statePrevious = item.stateNew;
                    item.stateNew = newState;
                    item.state[newState].transitionStepsDone = 0;
                    item.state[newState].frame = 0;
                    item.state[newState].forward = true;
                }
                //}
			};
            
			if (newState=='hover') {
				jQuery(componentItemHTMLelement).addClass('hover');
			} else {
				jQuery(componentItemHTMLelement).removeClass('hover');
			};
			if (newState=='selected') {
				jQuery(componentItemHTMLelement).addClass('selected');
			} else {
				jQuery(componentItemHTMLelement).removeClass('selected');
			};

			
			clearTimeout(s.animationTimeout);
			sa.vcc.animateItem(componentInstanceHTMLelement, componentItemHTMLelement, true, true);
		}
	}
	
};
