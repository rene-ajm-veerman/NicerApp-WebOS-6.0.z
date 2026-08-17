seductiveapps.dialog = seductiveapps.vividDialog = {
	about : {
		whatsThis : 'seductiveapps.dialog = seductiveapps.vividDialog = A PNG-animated-sprite dialog component in javascript without dependencies',
		copyright : '(c) (r) 2011-2017 by Rene A.J.M. Veerman <seductiveapps@gmail.com>',
		license : 'http://seductiveapps.com/seductiveapps/license.txt',
		noWarranty : 'NO WARRANTY EXPRESSED OR IMPLIED. USE ONLY AT YOUR OWN RISK.',
		version : '2.0.0',
		firstReleased : '2011 June',
		lastUpdated : '2017 May'
	},
	globals : {
		timeToClose : 500,
		timeToOpenClose : 500,
		scrollpaneTheme : 'vividTheme__scroll_black'
	},
	settings : {},
	settings_cDialog : {
		// border width : search for shape.strokeWidth in this file.
		current : {
			borders : {
				strokeColor : 'orange',
				strokeFillColor : 'white',
				strokeWidth : 4
			},
			borderRadius : '20px',
            border : '2px rgba(196,240,255,1)',
			boxShadow : '2px 2px 2px 0px rgba(196,240,255,1), -2px -2px 2px 0px rgba(196,240,255,1), inset 1px 1px 1px 1px rgba(0,0,0,0.8), 4px 4px 4px 4px rgba(0, 0, 0, 0.7)',

			fps : 10,
			
			hoverOverBorder : {
				strokeColor : {
					range : 'saColorgradientSchemeOrangeYellow',
					steps : 4
				},
				blurFilter : {
					range : [ 0, 8]
				},
				fadeTo : 700, // milliseconds
				fadeBack : 700 // milliseconds
			},
			hoveringOver : null
		}
	},
	
	
	init : function (componentInstanceHTMLelement, initID, initChildren) {
		//console.log ('t123', componentInstanceHTMLelement.id, initID, componentInstanceHTMLelement);
		if (document.getElementById(componentInstanceHTMLelement.id+'__dialog')) {
			sa.vcc.componentFullyInitialized (initID, componentInstanceHTMLelement.id);
			return false;
		};
		
		if (componentInstanceHTMLelement.style.display=='none') {
			sa.m.log (2, { msg : 'sa.vividDialog.init(): for initID='+initID+'; NOT initializing element with id="'+componentInstanceHTMLelement.id+'" and class="'+componentInstanceHTMLelement.className+'", because .style.display=="none".' } );
			sa.vcc.componentFullyInitialized (initID, componentInstanceHTMLelement.id);
			return false;
		} else {
			sa.m.log (2, { msg : 'sa.vividDialog.init(): for initID='+initID+'; Initializing element with id="'+componentInstanceHTMLelement.id+'" and class="'+componentInstanceHTMLelement.className+'".' } );
		}
		sa.vcc.options[componentInstanceHTMLelement.id] = {
			items : {}
		};

		//sa.dialog.loadDialog    Graphics (componentInstanceHTMLelement); // CAUSES <A HREF> and <A ONCLICK> IN DIALOG CONTENT TO BECOME BUGGY!
		if (sa.vcc.newInstanceNeeded(componentInstanceHTMLelement)) {
			sa.vcc.newInstance (componentInstanceHTMLelement, initID);
		}
		
		sa.dialog.initDialog(componentInstanceHTMLelement.id, initID, initChildren);
	},
	
	loadDialogGraphics : function (componentInstanceHTMLelement) {
		// NO LONGER USED, CAUSES BUGS IN MOST BROWSERS; <a onclick> WONT WORK ANYMORE! :(
		/*
		if (!sa.vcc.settings[componentInstanceHTMLelement.id]) sa.vcc.newInstance (componentInstanceHTMLelement);
		var s = sa.vcc.settings[componentInstanceHTMLelement.id];
		if (!s) sa.m.log (1, 'sa.vividDialog.loadDialogGraphics() : !s');
		var t = sa.vcc.getVividTheme (s.themeName); 
		var b = t.baseURL + s.themeName + '/';
		if (document.getElementById(componentInstanceHTMLelement.id+'__item__0')) {
			s.imageLoaded = true;
			sa.dialog.initDialog(componentInstanceHTMLelement.id);
		} else {
			var html = '';
			html += '<div class="vdialog_images" style="display:none">';
			for (var i=0; i<t.state.highlighted.frameCount; i++) {
				var n = '00'; if (i>=10) n = '0'; if (i>=100) n = ''; n += i;
				html += '<img src="' + b+'frame_highlighted_'+n+'.png"/>';
			}
			for (var i=1; i<t.state.normal.frameCount; i++) {
				var n = '00'; if (i>=10) n = '0'; if (i>=100) n = ''; n += i;
				html += '<img src="' +b+ 'frame_normal_'+n+'.png"/>';
			}
			html += '<img src="' +b+ 'frame_normal_000.png" onload="sa.dialog.initDialog(\''+componentInstanceHTMLelement.id+'\');"/>';
			html += '</div>';
			componentInstanceHTMLelement.innerHTML += html;
		}
		*/
	},
	
	initDialog : function (componentInstanceHTMLid, initID, initChildren) {
		//if (componentInstanceHTMLid==='sitePopup_confirm') alert('-3-');;
		//console.log ('t1234', componentInstanceHTMLid);
		//console.trace();
		//debugger;

		var componentInstanceHTMLelement = jQuery('#'+componentInstanceHTMLid)[0];
		if (!sa.vcc.settings[componentInstanceHTMLid]) sa.vcc.newInstance (componentInstanceHTMLelement);
		
        //componentInstanceHTMLelement.className += ' sa_tiedToContentDimensions_cssRaw'; NOT NEEDED
		
		var 
		ci = document.getElementById(componentInstanceHTMLid),
		s = sa.vcc.settings[componentInstanceHTMLid];
		if (!s) return false;
		
		var
		o = sa.vcc.options[componentInstanceHTMLid],
		sl = sa.dialog.settings[componentInstanceHTMLid],
		t = sa.vcc.getVividTheme (s.themeName); 
		if (!t) {
            debugger;
            return false;
        }
		
		//if (componentInstanceHTMLid=='siteAds') debugger;
		if (t && t.saVividThemeType)
		if (t.saVividThemeType === 'canvasDialog') {
			//sa.dialog.init_cDialog (ci);
			//return false;
		}
		
		
		//debugger;
		/*var
		b = t.baseURL + '/',
		url = 
			t.state.normal.frames 
			&& sa.s.c.settings.online
			? t.state.normal.frames[0]
			: b + 'frame_normal_000.png';
                
                //url = b + t.state.normal.frames[0];
				//url = t.url;
				//url = b + 'frame_normal_000.png';
				url = t.baseURL + t.saVividThemeName + '/' + 'frame_normal_000.png';
					*/
				
		if (!t) return false;
		var b = t.url, url = t.url; 
		//debugger;
		if (typeof s.themeName==='object') debugger;
		s = sa.vcc.newInstanceThemeOverrides (s, t);
		
		if (initChildren) sa.vcc.init (ci, undefined, true, false, initID, true);
		
		var scrollpaneTheme = null;
		
		//TODO-item1
		
		if (ci.className.match('vividScrollpane__hidden')) {
			scrollpaneTheme = 'hidden';
		} else if (ci.className.match('vividScrollpane__auto')) {
			scrollpaneTheme = 'auto';
		} else if (ci.className.match('vividScrollpane__visible')) {
			scrollpaneTheme = 'visible';
		} else if (ci.className.match('vividScrollpane__')) {
			
			var pStart = ci.className.indexOf ('vividScrollpane__');
			var pEnd = ci.className.indexOf (' ', pStart);
			if (pEnd===-1) pEnd = ci.className.length;
			scrollpaneTheme = 'vividTheme__'+ci.className.substr(pStart, pEnd-pStart);//.replace (/.*__/,'');
			scrollpaneTheme = scrollpaneTheme.replace(/vividScrollpane__/,'');
			//ci.style.height = 'auto'; 
			//debugger;
		} else if (t.options && t.options.scrollpaneTheme) {
			scrollpaneTheme = t.options.scrollpaneTheme;
		} else scrollpaneTheme = sa.dialog.globals.scrollpaneTheme;

		
		//sa.m.log (2, { msg : 'sa.vividDialog.initDialog(): using scrollpane theme "'+scrollpaneTheme+'" to initialize the dialog with id="'+ci.id+'" and class="'+ci.className+'". Use class="[...] animatedScrollpane__auto [...]" to use the browser\'s default scrollbars for this dialog, or class="[...] animatedScrollpane__visible [...]" or class="[...] animatedScrollpane__hidden [...]" to not use any scrollbars at all.' } );
		s.items[0] = {
			type : 'dialog',
			theme : t,
			scrollpaneTheme : scrollpaneTheme,
			hasAnimatedScrollpane : (scrollpaneTheme!='auto' && scrollpaneTheme!='hidden' && scrollpaneTheme!='visible'),
			stateCurrent  : 'normal',
			statePrevious : 'normal',
			state : {
				normal : { frame : 0, forward : true, frameCount : (t.state&&t.state.normal?t.state.normal.frameCount:0) },
				highlighted : { frame : 0, forward : true, frameCount : (t.state&&t.state.highlighted?t.state.highlighted.frameCount:0) }
			},
			stateOld : null,
			stateNew : null,
			animationTimeout : null
		};
		var item = s.items[0];


		if (s.imageLoaded) {
			sa.m.log (1, { error : 's.imageLoaded - skipping init', s : s } );
		} else {
			s.imageLoaded = true;
			
			var addHTML = document.getElementById(componentInstanceHTMLid+'__item__0');
		
			var d = document.createElement('DIV');
			var e = document.createElement('DIV');
			
			d.style.visibility = ci.style.visibility;
            if (typeof ci.style.right == 'string' && ci.style.right !== '') d.style.right = ci.style.right;
            if (typeof ci.style.left == 'string' && ci.style.left !== '') d.style.left = ci.style.left;
            if (typeof ci.style.bottom == 'string' && ci.style.bottom!== '') d.style.bottom = ci.style.bottom;
            if (typeof ci.style.top == 'string' && ci.style.top !== '') d.style.top = ci.style.top;
            
			d.style.width = ci.offsetWidth + 'px';
			//if (componentInstanceHTMLid=='siteAds') debugger;
			d.style.height = ci.offsetHeight + 'px';
			d.id = componentInstanceHTMLid+'__dialog';
			d.className = 'vividDialog_dialog';
			d.style.position = ci.style.position;


			var 
			ieFilter = 'filter:progid:DXImageTransform.Microsoft.Alpha(opacity=100);',
			html = 
				'<div id="'+componentInstanceHTMLid+'__contentDimensions_cssRaw" style="position:absolute;"></div>'
				+'<div id="'+componentInstanceHTMLid+'__contentDimensions" style="position:absolute;"></div>'
				+'<div id="'+componentInstanceHTMLid+'__CSS3" class="vd_CSS3" style="position:absolute;width:100%;height:100%;z-index:-1100000"></div>'
				+'<div id="'+componentInstanceHTMLid+'__CSS3b" class="vd_CSS3b" style="position:absolute;width:100%;height:100%;z-index:-1000000"></div>'
                
				+'<div id="'+componentInstanceHTMLid+'__backgrounds" style="position:absolute;width:100%;height:100%;z-index:-1;"></div>';
			if (ci.style.position=='relative') ci.style.position = 'absolute';
            /* back from the days where a dialog's background was determined not by a texture but by a much larger PNG 
             * that in the end didn't even resize well across all possible browser sizes.
			if (t.state&&t.state.normal.frameCount>0) {
				html += 
				'<div id="'+componentInstanceHTMLid+'__item__0" style="position:absolute;z-index:-2; width:100%;height:100%;">'
			 	+'<img id="'+componentInstanceHTMLid+'__item__0__img1" style="'+ieFilter+'display:none;position:absolute;width:100%;height:100%;" src="'+url+'"/>'
				+'<img id="'+componentInstanceHTMLid+'__item__0__img2" style="'+ieFilter+'position:absolute;width:100%;height:100%;" src="'+url+'"/>'
				+'</div>';
			} else {
				html += 
				'<div id="'+componentInstanceHTMLid+'__item__0" style="z-index:-2; position:absolute; width:100%;height:100%;">'
			 	+'<img id="'+componentInstanceHTMLid+'__item__0__img1" style="'+ieFilter+'display:none;position:absolute;width:100%;height:100%;" src="data:image/png;base64,R0lGODlhFAAUAIAAAP///wAAACH5BAEAAAAALAAAAAAUABQAAAIRhI+py+0Po5y02ouz3rz7rxUAOw=="/>'
				+'<img id="'+componentInstanceHTMLid+'__item__0__img2" style="'+ieFilter+'position:absolute;width:100%;height:100%;" src="data:image/png;base64,R0lGODlhFAAUAIAAAP///wAAACH5BAEAAAAALAAAAAAUABQAAAIRhI+py+0Po5y02ouz3rz7rxUAOw=="/>'
				+'</div>';
			};
            */
			
			if (item.hasAnimatedScrollpane) {
				html += '<div id="'+componentInstanceHTMLid+'__scrollpane" class="vividScrollpane animatedPartOf__dialog '+item.scrollpaneTheme+'" style="'+(item.scrollpaneTheme.match(/left/)?'position:absolute;':'')+'width:100%;height:100%;"></div>';
				
			} else {
				//html += '<div id="'+componentInstanceHTMLid+'__content"></div>';
			};
			html +=
				'<div id="'+componentInstanceHTMLid+'__dialogBtns" class="vd_btns" style="position:absolute;z-index:99000000;width:80px;left:20px;top:-20px;height:25px;display:none">'
				//+'<img src="'+sa.m.globals.urls.app+'siteImages/btnDialogOptions.png" style="position:absolute;right:30px;width:25px;" onclick="sa.m.dialogOptions(\'#'+componentInstanceHTMLid+'\');"/>'
					+'<img src="'+sa.m.globals.urls.framework+'/siteMedia/btnDialogOptions.png" style="position:absolute;right:60px;width:25px;" onclick="sa.m.dialogOptions(\'#'+componentInstanceHTMLid+'\');"/>'
					+'<img src="'+sa.m.globals.urls.framework+'/siteMedia/btnDialogShowOrHideContent.png" style="position:absolute;right:30px;width:25px;" onclick="sa.s.c.showOrHideContent(\''+componentInstanceHTMLid+'\');"/>'
				+'<img src="'+sa.m.globals.urls.framework+'/siteMedia/btnDialogMinimize.png" style="position:absolute;right:0px;width:25px;" onclick="sa.s.c.toggleVisible([\'#'+componentInstanceHTMLid+'\']);"/>'
				+'</div>';
			d.innerHTML = html;
            //debugger;
            
            
            setTimeout (function () {
                var 
                ciheCSS3 = jQuery('#'+componentInstanceHTMLid+'__CSS3')[0],
                ciheCSS3b = jQuery('#'+componentInstanceHTMLid+'__CSS3b')[0];
                ci.css3 = ciheCSS3;
                ci.css3b = ciheCSS3b;
                
                if (sa.m.globals.urls.upstream) {
                    var mediaRoot = sa.m.globals.urls.upstream.media;
                } else {
                    var mediaRoot = sa.m.globals.urls.media;
                }			
                ciheCSS3.style.backgroundImage = 'url('+mediaRoot + '/' + t.texture.file+')';
                if (t.texture.backgroundSize) {
                    ciheCSS3.style.backgroundSize = t.texture.backgroundSize;
                }
                ciheCSS3.style.backgroundRepeat = 'repeat';
                if (t.borderRadius) {
                    ciheCSS3.style.borderRadius = t.borderRadius;
                    d.style.borderRadius = t.borderRadius;
                } else {
                    ciheCSS3.style.borderRadius = sa.dialog.settings_cDialog.current.borderRadius; 
                    d.style.borderRadius = sa.dialog.settings_cDialog.current.borderRadius; 
                }

                if (t.border && t.border!='') {
                    d.style.border = t.border;
                } else {
                    d.style.border = sa.dialog.settings_cDialog.current.border; 
                }
                
                if (t.boxShadow && t.boxShadow!='') {
                    d.style.boxShadow = t.boxShadow;
                } else {
                    d.style.boxShadow = sa.dialog.settings_cDialog.current.boxShadow;
                }
                
                
                
                //ciheCSS3.style.border = '2px solid rgba(255, 255, 255, 1)';
                if (t.texture.opacity) {
                    ciheCSS3.style.opacity = t.texture.opacity;
                } else {
                    ciheCSS3.style.opacity = 1;
                }
                ciheCSS3.style.zIndex = -1 * 1000 * 1000;
                //return true; // works well
            }, 10); // was 200 
            
            
			
//debugger;
			//setTimeout (function () {
				ci.parentNode.insertBefore (d, ci); // add the dialog's holder DIV before the original html code/content of this dialog
				d.appendChild (ci.parentNode.removeChild(ci));
				
				
				if (item.hasAnimatedScrollpane) {
					e = document.getElementById(componentInstanceHTMLid+'__scrollpane');
					item.scrollpane = e;
				} else {
					e = document.getElementById(componentInstanceHTMLid+'__content');
				}
				jQuery('#'+componentInstanceHTMLid+'__dialogBtns').css({opacity:0.01});
			
			//}, 1000);
			
			
			
			//debugger;
			/* when you don't need it, you don't need it (aka : take only from defs in seductiveapps/com/ui/vivid/themes/dialogs)
			if (!t.cssToExtrapolate) {
				t.cssToExtrapolate = {
					".vividDialog" : {
						"top" : 45,
						"left" : 45,
						"width" : "100%-45",
						"height" : "100%-45"
					},
					".vd_btns" : {
						"top" : 20,
						"right" : 30
					}
				}
			};
			*/
			
			// rip out any sidebars / headers / footers from the original html code and place them in the dialog (not the scrollpane that might go with the dialog)
			var sbhf = jQuery('.saSidebarLeftInDialog, .saSidebarRightInDialog, .saHeaderInDialog, .saFooterInDialog',ci);
			jQuery(sbhf).detach().appendTo(d);
			
			// move the original html code into the holder DIV (scrollpane for dialog, or if that's nonexistant for this dialog, id="*.__content")
			//e.appendChild (ci.parentNode.removeChild(ci)); 
			
			for (var j=0; j<ci.children.length; j++) {
				var coi = ci.children[j];
				if (coi.className.match ('animatedDialog__header')) {
					if (d.children.length>1) {
						d.insertBefore (coi.parentNode.removeChild(coi), d.children[2]);
					} else {
						d.appendChild (coi.parentNode.removeChild(coi));
					};
					coi.style.position = 'relative';
					//coi.style.marginTop = '4%';
					//coi.style.marginLeft = '4%';
					coi.style.zIndex = 20000;
				}
			}
			//sa.m.log (1, { 'item.scrollpaneTheme' : item.scrollpaneTheme } );
			switch (item.scrollpaneTheme) {
				case 'auto' : ci.style.overflow = 'auto'; break;
				case 'visible' : ci.style.overflow = 'visible'; break;
				case 'hidden' : 
                    if (ci.style.overflow !== 'visible') {
                        ci.style.overflow = 'hidden';
                    } else {
                        ci.style.overflow = 'visible';
                    }
                    break;
				default: ci.style.overflow = 'hidden'; break;
			};

			//d.style.position = '';
			d.style.display = ci.style.display;
			d.style.visibility = ci.style.visibility;
			d.style.left = ci.style.left;
			ci.style.left = '0px';
			d.style.right = ci.style.right;
			d.style.top = ci.style.top;
            d.style.bottom = ci.style.bottom;
			d.style.width = ci.offsetWidth + 'px';
			if (ci.style.minWidth!=='') {
				d.style.minWidth = (parseFloat(ci.style.minWidth)+20 ) + 'px';
			};
			//debugger;
			d.style.height = (
				ci.className.indexOf('vdialog_relative')===-1 || ci.className.indexOf('vdialog_relative')===false
				? ci.offsetHeight + 'px'
				: (ci.scrollHeight + 40) + 'px'
				
			);
			d.style.zIndex = parseInt(ci.style.zIndex);

            /*
            var dbb = document.getElementById(componentInstanceHTMLid+'__item__0');
			if (ci.style.minWidth!=='') {
				dbb.style.minWidth = (parseFloat(ci.style.minWidth)+20 ) + 'px';
			};
			dbb.style.width = '100%';//ci.offsetWidth + 'px';
			dbb.style.height = '100%'; //ci.offsetHeight + 'px';
			dbb.style.zIndex = (parseInt(d.style.zIndex)>0?parseInt(d.style.zIndex):0) + 1;
            */
			//ci.style.position = 'absolute';//'relative';			
			if (ci.className.indexOf('vdialog_relative')===-1 || ci.className.indexOf('vdialog_relative')===false) {
				d.style.position = 'absolute';
			} else {
				d.style.position = 'relative';
			};
			
			//debugger;
			
			if (d===e) {
				var f = ci;
			} else {
				var f = e;
				ci.style.left = '0px';
				ci.style.top = '0px';
				//ci.style.width = jQuery(ci).parent('.saWall').length==1?'95%':'100%'; // BUG_002__LOC_001
				//ci.style.height = '100%';
				
			};



			/*
			f.style.left = '2%';
			f.style.top = '2%';
			f.style.width = '96%';
			f.style.height = '96%';
			*/
			
			
			/* OUTDATED - this was from before css for internal sizes was put into the dialog json definition files in seductiveapps/com/ui/vivid/themes/dialogs
			
			var themeOverride = sa.vcc.settingtOverrides['#'+componentInstanceHTMLid];
			jQuery(f).css(sa.m.negotiateOptions(
				sa.dialog.globals.css.contentForDialog,
				( 	themeOverride 
					? themeOverride.css.contentForDialog
					: {}
				),
				(
					seductiveapps.site.globals
					&& seductiveapps.site.globals.desktop
					&& seductiveapps.site.globals.desktop.configuration
					&& seductiveapps.site.globals.desktop.configuration.dialogInnerSizes
					? seductiveapps.site.globals.desktop.configuration.dialogInnerSizes('#'+componentInstanceHTMLid)
					: {}
				)
			));
			*/



			ci.style.zIndex = (parseInt(d.style.zIndex)>0?parseInt(d.style.zIndex):0) + 2;

			/*
			if (item.hasAnimatedScrollpane) {
				//ci.style.width = 'auto';
				//ci.style.height = 'auto';
			} else {
				ci.style.width = '96%';
				ci.style.height = '96%';
			}
			*/
			//if (d!==e) f.style.zIndex = parseInt(ci.style.zIndex)-1;

			if (item.hasAnimatedScrollpane) {
				//sa.s.c.settings.browsersideLoadBalance.ui.scrollpaneDelay += 500;
				//debugger;
				//alert ('sa.s.c.settings.browsersideLoadBalance.ui.scrollpaneDelay='+sa.s.c.settings.browsersideLoadBalance.ui.scrollpaneDelay);
				//setTimeout(function() {
				//debugger;
					var 
					srNewIndex = sa.vcc.inits[s.initID].sr.length;
					/*(
					var timeoutHandler = setTimeout (sa.m.traceFunction(function () {
						sa.sp.init (e, s.initID);
					}), 10);//todo : turn 1000 into a larger value at startup and a low value when already fully booted.*/
					
					//e.id += '__dialogCreated';
					var 
					scrollpaneInit = {
						id : e.id,
						el : e
					}
					
					if (document.getElementById(componentInstanceHTMLid+'__scrollpane')) {
						sa.vcc.inits[s.initID].sr[srNewIndex] = scrollpaneInit;
						//debugger;
						sa.sp.init(scrollpaneInit.el, s.initID);
					}

					
					
					//sa.vcc.inits[s.initID].completedCount++;
					
					
					
					/*
					setTimeout (sa.m.traceFunction ( function () {
						sa.vcc.applyTheme (componentInstanceHTMLid);
						sa.dialog.onresize(cihe);
						sa.vcc.componentFullyInitialized (s.initID, componentInstanceHTMLid);
					}), 3000); 
					*/
					
					//setTimeout (sa.m.traceFunction ( function () {
						//debugger;
						sa.vcc.componentFullyInitialized (s.initID, componentInstanceHTMLid);
					//}), 100); //was 1000


					
				//}, sa.s.c.settings.browsersideLoadBalance.ui.scrollpaneDelay);
			};
			
			
		};
		item.img1 = document.getElementById(componentInstanceHTMLid+'__item__0__img1');
		item.img2 = document.getElementById(componentInstanceHTMLid+'__item__0__img2');
		setTimeout (sa.m.traceFunction( function () {
			jQuery(item.img1).fadeIn(1000);
		}), 250);
		
		sa.vcc.setOpacity(item.img2,0);
		var cihe = document.getElementById(componentInstanceHTMLid);
		//sa.vcc.animateItem (cihe, document.getElementById(componentInstanceHTMLid+'__item__0'), true, true); // outdated with the development of canvasDialog
		//if (componentInstanceHTMLid==='sitePopup_confirm') debugger;
		if (!item.hasAnimatedScrollpane) sa.vcc.componentFullyInitialized (s.initID, componentInstanceHTMLid);
				
		//debugger;
	},
	
	
	animate : function (cihe, top, left, width, height, callback) {
		var s = sa.vcc.settings[cihe.id];
		var item = s.items[0];
		//jQuery('#'+cihe.id).css ({visibility:'visible'});
		//jQuery('#'+cihe.id).animate ({top:10,left:10,width:width-20,height:height-20});
		jQuery('#'+cihe.id+'__dialog').animate ({top:top,left:left,width:width,height:height}, callback);
		//jQuery('#'+item.img1.id+', #'+item.img2.id).animate ({width:width,height:height});
	},
	
	destroy : function (dialog) {
		var jQueryinnerDialog = jQuery('#'+dialog.id.replace('__dialog',''));
		if (jQueryinnerDialog.length==0) debugger;
		jQueryinnerDialog.css ({
			top : (dialog.id=='siteAds__dialog'?0:jQuery(dialog).offset().top),
			left : (dialog.id=='siteAds__dialog'?0:jQuery(dialog).offset().left),
			width : jQuery(dialog).width(),
			height : (dialog.id=='siteAds__dialog'?'auto':jQuery(dialog).height())
		});
		dialog.parentNode.appendChild (jQueryinnerDialog[0].parentNode.removeChild (jQueryinnerDialog[0]));
		dialog.parentNode.removeChild (dialog);
	},
	
	changeTheme : function (dialog, newThemeName) {
		sa.dialog.destroy(jQuery('#'+dialog.id+'__dialog')[0]);
		jQuery.cookie('saBackgroundBuiltin__'+dialog.id, newThemeName, sa.m.cookieOptions());
		jQuery(dialog).removeClass(sa.vcc.settings[dialog.id].s.themeName).addClass(newThemeName).addClass('saBeingChanged');
		console.log ('sa.dialog.changeTheme', dialog.id, newThemeName);
		setTimeout (sa.m.traceFunction (function () {
			var afterResize = sa.vcc.settings[dialog.id].afterResize;
			delete sa.vcc.settings[dialog.id];
			//debugger;
			sa.vcc.init (dialog, sa.m.traceFunction(function () {
				if (!sa.vcc.settings[dialog.id]) sa.vcc.settings[dialog.id] = {};
				sa.vcc.settings[dialog.id].afterResize = afterResize;
				jQuery(dialog).removeClass('saBeingChanged');
				sa.desktop.resize();
			}));
		}), 500);
		if (dialog.id=='siteContent') {
			setTimeout (sa.m.traceFunction(function() {
				sa.site.code.loadContent (document.location.href, sa.m.traceFunction (function () {
					sa.dialog.onresize(dialog);
				}));
			}), 1000);
		}
		if (dialog.id=='siteAds') {
			setTimeout (sa.m.traceFunction(function() {
				sa.sp.initAutomatedScrolling (jQuery('#siteAds__scrollpane')[0], 'vertical', true);
			}), 1000);
		}
	},
	
	onresize : function (componentInstance) {
        if (typeof componentInstance==='string') {
			componentInstance = document.getElementById (componentInstance.replace('#',''));
		}
		
		var 
		s = sa.vcc.settings[componentInstance.id.replace('__dialog','')];
		
		if (!s) {
			sa.m.log (1, 'sa.dialog.onresize : invalid componentInstance "'+componentInstance.id+'"');
			return false;
		};

		if (typeof s.beforeResize=='function') {
			s.beforeResize(componentInstance.id);
		};
		
		if (!s.items) return false;
		
		var	
		item = s.items[0],
		img = jQuery('#'+componentInstance.id+'__item__0__img1')[0],
		img2 = jQuery('#'+componentInstance.id+'__item__0__img2')[0],
		d = jQuery('#'+componentInstance.id+'__dialog')[0],
		css3 = jQuery('#'+componentInstance.id+'__CSS3')[0],
		css3b = jQuery('#'+componentInstance.id+'__CSS3b')[0],
		sp = jQuery('#'+componentInstance.id+'__scrollpane')[0],
		spc = jQuery('#'+componentInstance.id+'__scrollpane__container')[0];
		
		jQuery(d).css({
			right : d.style.right,
			bottom : d.style.bottom
		});
		
		jQuery('#'+componentInstance.id+'__CSS3, #'+componentInstance.id+'__CSS3b').css ({
			width : d.offsetWidth,
			height : d.offsetHeight
		});
		
		var
		o = sa.vcc.options[componentInstance.id],
		sl = sa.dialog.settings[componentInstance.id],
		t = sa.vcc.getVividTheme (s.themeName); 
		if (!t) debugger;

		if (t.saVividThemeType == 'canvasDialog') {
			var 
			resize_cDialogEventData = {
				saEventType : 'resize_cDialog',
				cihe : componentInstance
			},
			resize_cDialogEvent =  {
				data : resize_cDialogEventData
			};
			
            sa.vcc.applyTheme (componentInstance.id);
            
			
			//sa.dialog.resize_cDialog (resize_cDialogEvent);
            
			//setTimeout (function() {
				var cihe = componentInstance;
				/*
				cihe.parentNode.parentNode.appendChild(
					cihe.css3.parentNode.removeChild (cihe.css3)
				);
				*/
				css3.style.position = 'absolute';
				//cihe.parentNode.style.overflow = 'hidden';
			
				
				
				//debugger;
				/*
				if (
					jQuery('#'+componentInstance.id+'__scrollpane__dialogCreated').length==1
					&& sa.vcc.settings[componentInstance.id+'__scrollpane__dialogCreated']
					&& sa.vcc.settings[componentInstance.id+'__scrollpane__dialogCreated'].items
					&& sa.vcc.settings[componentInstance.id+'__scrollpane__dialogCreated'].items.length==2
					&& (
						sa.vcc.settings[componentInstance.id+'__scrollpane__dialogCreated'].items[0].sliderbarShown
						|| sa.vcc.settings[componentInstance.id+'__scrollpane__dialogCreated'].items[1].sliderbarShown
					)
				) {
					jQuery(componentInstance).css ({ height : 'auto' });
				} else {
					jQuery(componentInstance).css ({ height : '100%' });
				}
				if (
					jQuery('#'+componentInstance.id+'__scrollpane').length==1
					&& sa.vcc.settings[componentInstance.id+'__scrollpane']
					&& sa.vcc.settings[componentInstance.id+'__scrollpane'].items
					&& sa.vcc.settings[componentInstance.id+'__scrollpane'].items.length==2
					&& (
						sa.vcc.settings[componentInstance.id+'__scrollpane'].items[0].sliderbarShown
						|| sa.vcc.settings[componentInstance.id+'__scrollpane'].items[1].sliderbarShown
					)
				) {
					jQuery(componentInstance).css ({ height : 'auto' });
				} else {
					jQuery(componentInstance).css ({ height : '100%' });
				}
				*/


				
				//jQuery(componentInstance).css ({ height : '100%' }); // BUG_002__LOC_002a_LINE_001of001
				//jQuery(componentInstance).css ({position:'absolute'}); // BUG_002__LOC_002b_LINE_001of001
				jQuery('#'+componentInstance.id+'__scrollpane').css ({ overflow : 'hidden' });
				jQuery('#'+componentInstance.id+'__scrollpane__dialogCreated').css ({ overflow : 'hidden' });
				jQuery('#'+componentInstance.id+'__scrollpane__container').css ({ position : 'absolute' });
				if (jQuery('#'+componentInstance.id+'__scrollpane__partOfAwhole').length==1) {
					sa.sp.containerSizeChanged (jQuery('#'+componentInstance.id+'__scrollpane__partOfAwhole')[0], true);
				} else if (jQuery('#'+componentInstance.id+'__scrollpane__dialogCreated').length==1) {
					sa.sp.containerSizeChanged (jQuery('#'+componentInstance.id+'__scrollpane__dialogCreated')[0], true);
				} else {
					sa.sp.containerSizeChanged (jQuery('#'+componentInstance.id+'__scrollpane')[0], true);
				}
			//}, 2000);
			
			
		} else {
				
				
				var
				b = t.baseURL + '/',
				/*url = t.state.normal.frames 
					? t.state.normal.frames[0]
					: b + 'frame_normal_000.png';
						*/
						url = b + t.state.normal.frames[0];

			sa.vcc.applyTheme (componentInstance.id);
		}

		if (typeof s.afterResize=='function') {
			s.afterResize(componentInstance.id);
		}
	},		
	
	onClick : function (el,evt) {
	},
	
	onMouseover : function (el,evt) {
	return false;
		var buttonHTMLid = el.id;
		buttonHTMLid = buttonHTMLid.replace(/__dialog/,'');
		var s = sa.vcc.settings[buttonHTMLid];
		var item = s.items[0];
		var t = sa.vcc.getVividTheme (s.themeName); 
		if (
			typeof t.state.highlighted=='object' 
			&& item.stateCurrent!='disabled' 
			&& item.stateCurrent!='selected'
			&& item.stateNew!='disabled'
			&& item.stateNew!='selected'
		) {
			item.statePrevious = item.stateCurrent || item.stateNew;
			sa.vcc.changeState (document.getElementById(buttonHTMLid), document.getElementById(buttonHTMLid+'__item__0'), 'highlighted');
		}
	},
	
	onMouseout : function (el,evt) {
	return false;
		var buttonHTMLid = el.id;
		buttonHTMLid = buttonHTMLid.replace(/__dialog/,'');
		var s = sa.vcc.settings[buttonHTMLid];
		var item = s.items[0];
		if (
			item.stateCurrent!='disabled' 
			&& item.stateCurrent!='selected'
			&& item.stateNew!='disabled'
			&& item.stateNew!='selected'
		) {
			sa.vcc.changeState (document.getElementById(buttonHTMLid), document.getElementById(buttonHTMLid+'__item__0'), item.statePrevious);
		}
	},
	
	// canvasDialog functions below here
	init_cDialog : function (cihe) {
		var 
		ci = cihe,
		s = sa.vcc.settings[cihe.id];
		if (!s) debugger;
		
		var
		o = sa.vcc.options[cihe.id],
		sl = sa.dialog.settings[cihe.id],
		t = sa.vcc.getVividTheme (s.themeName); 
		if (!t) debugger;
		
		if (typeof cihe=='string') cihe = jQuery('#'+cihe)[0];
		/*setTimeout (function () {
			var 
			ciheCSS3 = cihe.css3 = jQuery('#'+cihe.id+'__CSS3')[0],
			ciheScrollpane = cihe.scrollpane = jQuery('#'+cihe.id+'__scrollpane')[0];
			
			cihe.style.height = 'auto';
			cihe.style.overflow = 'hidden';
			ciheScrollpane.style.overflow = 'hidden';
		}, 5000);*/
		


        
		cihe.theme = t;
        cihe.style.overflow = 'visible';
		
		
			//cihe.parentNode.style.overflow = 'hidden';
		
	
		cihe.hoverOverBorderSegmentTheme = sa.colorGradients.themes[sa.dialog.settings_cDialog.current.hoverOverBorder.strokeColor.range],
		cihe.hoverOverBorderSegmentData = sa.colorGradients.generateList_basic (cihe.hoverOverBorderSegmentTheme, sa.dialog.settings_cDialog.current.hoverOverBorder.strokeColor.steps);
		
		//1 canvas easeljs shape object that covers the entire cDialog.offsetWidth & .offsetHeight
		var shape = new createjs.Shape();
		shape.strokeWidth = 3; // width (and height) of canvas stroke
		shape.strokeColor = 'orange';
		shape.strokeFillColor = 'white';
		
		shape.width = cihe.offsetWidth - (shape.strokeWidth * 2);
		shape.height = cihe.offsetHeight - (shape.strokeWidth * 2);
		shape.scaleX = 1;
		shape.scaleY = 1;

		var 
		canvasForStage = document.createElement('canvas'),
		ctx2D = canvasForStage.getContext('2d');
		
		ctx2D.globalCompositeOperation = 'source-over';
		ctx2D.globalAlpha = 1;
	
		canvasForStage.id = 'canvasForDialog__' + cihe.id;
		canvasForStage.width = shape.width;
		canvasForStage.height = shape.height;
		//canvasForStage.style.width = shape.width + 'px';
		//canvasForStage.style.height = shape.height + 'px';
		//canvasForStage.style.zIndex = 1.2 * 1000 * 1000;
		cihe.css3.appendChild (canvasForStage);
		cihe.css3.canvas = canvasForStage;
		
		var stage = new createjs.Stage(canvasForStage.id);
		stage.width = cihe.offsetWidth;
		stage.height = cihe.offsetHeight;
		//stage.shadow = new createjs.Shadow("#000000", 0, 0, 0);
		stage.shadow = new createjs.Shadow("#000000", 3, 3, 1);

		// many roads leading to Rome principle.. :
		cihe.css3.stage = stage;
		cihe.css3.shape = shape;
		//cihe.style.zIndex = 2000 * 1000 * 1000;
		stage.addChild (shape);
		cihe.css3.stage.update();
		
		cihe.css3.stage = stage;
		cihe.css3.stageCanvas = canvasForStage;
		cihe.ctx2D = ctx2D;
		
		// add hover ability to the border of the dialog
		cihe.saObjectType = 'vividDialog2';
		cihe.saInteractiveBorders = {
				n : document.createElement('div'),
				ne : document.createElement('div'),
				e : document.createElement('div'),
				se : document.createElement('div'),
				s : document.createElement('div'),
				sw : document.createElement('div'),
				w : document.createElement('div'),
				nw : document.createElement('div')
		};
		//debugger;
		
		// pre-init_cDialogialize some settings_cDialog for cihe.saInteractiveBorders items
		for (var id in cihe.saInteractiveBorders) {
			var ab = cihe.saInteractiveBorders[id];
			ab.id = 'divForDialogBorders__'+cihe.id+'__'+id;
			ab.aid = id;
			ab.cihe = ab;
			ab.style.position = 'absolute';
			cihe.css3.appendChild (ab);
			
			ab.onmouseover = sa.dialog.onmouseover;
			ab.onmousedown = sa.dialog.onmousedown;
			ab.onmouseout = sa.dialog.onmouseout;
			
			ab.animationsettings_cDialog = sa.m.cloneObject(sa.dialog.settings_cDialog.current.hoverOverBorder);
			ab.animationsettings_cDialog.strokeColor.atStep = 0;
			ab.animationsettings_cDialog.blurX = 0;
			ab.animationsettings_cDialog.blurY = 0;
			ab.animating = true;
			ab.animateTo = true;
			ab.animateToBeginning = true;
			ab.animateBack = true;
			
			var 
			canvasForItemStage = document.createElement('canvas'),
			ctx2D = canvasForItemStage.getContext('2d');
			ctx2D.globalCompositeOperation = 'source-over';
			ctx2D.globalAlpha = 1;
			
			canvasForItemStage.id = 'canvasForDialog__' + cihe.id+'__'+id;
			//canvasForItemStage.width = shape.width;
			//canvasForItemStage.height = shape.height;
			//canvasForStage.style.width = shape.width + 'px';
			//canvasForStage.style.height = shape.height + 'px';
			//canvasForStage.style.zIndex = 1.2 * 1000 * 1000;
			ab.appendChild (canvasForItemStage);
			
			var stage = new createjs.Stage(canvasForItemStage.id);
			stage.width = cihe.offsetWidth;
			stage.height = cihe.offsetHeight;
			ab.stage = stage;
			ab.canvas = canvasForItemStage;
			
			var shape = new createjs.Shape();
			shape.strokeWidth = sa.dialog.settings_cDialog.current.borders.strokeWidth; // width (and height) of canvas stroke
			shape.strokeColor = sa.dialog.settings_cDialog.current.borders.strokeColor;
			shape.strokeFillColor = sa.dialog.settings_cDialog.current.borders.strokeFillColor;
			shape.width = parseInt(ab.style.width);
			shape.height = parseInt(ab.style.height);
			ab.shape = shape;
			ab.stage.addChild (shape);
			
			ab.strokeColor = shape.strokeColor;
			ab.strokeWidth = shape.strokeWidth;
			ab.strokeFillColor = shape.strokeFillColor;
			
			if (typeof t.borders == 'object') { // if (show shadows)
				var width = !t.borders ? 0 : t.borders.borderEmbossWidth;
				switch (id) {
					case 'nw':
						ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", width, width, 1);
						break;
					case 'n':
						ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", 0, width, 1);
						break;
					case 'ne' :
						ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", -1*width, width, 1);
						break;
					case 'e' :
						ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", -1*width, 0, 1);
						break;
					case 'se':
						ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", -1*width, -1*width, 1);
						break;
					case 's':
						ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", 0, -1*width, 1);
						break;
					case 'sw':
						ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", width, -1*width, 1);
						break;
					case 'w':
						ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", width, 0, 1);
						break;
				}
			}
			
			setTimeout (function () {
				for (var aid in cihe.saInteractiveBorders) {
					var abCtx = cihe.saInteractiveBorders[aid];
					
					abCtx.animating = true;
					abCtx.animateTo = false;
					abCtx.animateToBeginning = false;
					abCtx.animateBack = true;
				}
			}, 2000);
			
			
		};
		//return true;

		// setup proper data routining routes to routines like this.resize_cDialog()
		var 
		resize_cDialogEventData = {
			saEventType : 'resize_cDialog',
			cihe : cihe
		},
		resize_cDialogEvent =  {
			data : resize_cDialogEventData
		};
		
		/*
		// resize_cDialog at startup too plz
		jQuery(window).resize (resize_cDialogEvent, function() {
				//sa.dialog.resize_cDialog(resize_cDialogEvent)
		});
		//sa.dialog.resize_cDialog(resize_cDialogEvent); only messes up the startup dialog positioning & resizing
		*/
		
		//cihe.style.borderLeft = cihe.style.borderTop =  '5px solid yellow';
		//cihe.style.boxShadow = '-5px, -5px, -5px, black';
		
		// set up animations
		/*
		setInterval (function () {
			var eventTick = {
				data : {
					cihe : cihe
				}
			};
			
			sa.dialog.animationTick (eventTick);
		}, 1000 / sa.dialog.settings_cDialog.current.fps  ); // 30 animation frames per second please
		*/
	},
	
	animationTick : function (eventTick) {
		var 
		cihe = eventTick.data.cihe,
		shape = cihe.css3.shape,
		ib = cihe.saInteractiveBorders;
		
		for (var id in ib) {
			var ab = ib[id]; // ab = a border
			if (ab.animating) {
				if (ab.animateTo) {
					ab.animationsettings_cDialog.blurX += 1;
					ab.animationsettings_cDialog.blurY += 1;
					
					if (ab.animationsettings_cDialog.strokeColor.atStep < ab.animationsettings_cDialog.strokeColor.steps) {
						ab.animationsettings_cDialog.strokeColor.atStep++;
					} else {
						ab.animationsettings_cDialog.strokeColor.atStep--;
						ab.animating = true;
						ab.animateTo = false;
						ab.animateToBeginning = true;
						ab.animateBack = false;
						
					}
				} else if (ab.animateToBeginning) {
					if (ab.animationsettings_cDialog.blurX >= 0) ab.animationsettings_cDialog.blurX--;
					if (ab.animationsettings_cDialog.blurY >= 0) ab.animationsettings_cDialog.blurY--;
					if (ab.animationsettings_cDialog.strokeColor.atStep >= 0) ab.animationsettings_cDialog.strokeColor.atStep--;
					if (
						ab.animationsettings_cDialog.blurX <= 0
						&& ab.animationsettings_cDialog.blurY <= 0
						&& ab.animationsettings_cDialog.strokeColor.atStep <= 0 
					) {
						ab.animating = true;
						ab.animateTo = true;
						ab.animateToBeginning = false;
						ab.animateBack = false;
					};
					
				} else if (ab.animateBack) {
					if (ab.animationsettings_cDialog.blurX >= 0) ab.animationsettings_cDialog.blurX--;
					if (ab.animationsettings_cDialog.blurY >= 0) ab.animationsettings_cDialog.blurY--;
					if (ab.animationsettings_cDialog.strokeColor.atStep >= 0) ab.animationsettings_cDialog.strokeColor.atStep--;
					
					if (
						ab.animationsettings_cDialog.blurX <= 0
						&& ab.animationsettings_cDialog.blurY <= 0
						&& ab.animationsettings_cDialog.strokeColor.atStep <= 0 
					) {
						ab.animationsettings_cDialog.blurX = 0;
						ab.animationsettings_cDialog.blurY = 0;
						ab.animationsettings_cDialog.strokeColor.atStep = 0;
						ab.animating = false;
						ab.animateTo = false;
						ab.animateToBeginning = false;
						ab.animateBack = false;
					};
					
				} 
			};

			if (ab.animating) {
				if (cihe.hoverOverBorderSegmentData[ab.animationsettings_cDialog.strokeColor.atStep] ) {
					ab.strokeColor = cihe.hoverOverBorderSegmentData[ab.animationsettings_cDialog.strokeColor.atStep].color;
					var 
					resize_cDialogEventData = {
						saEventType : 'resize_cDialog',
						cihe : cihe
					},
					resize_cDialogEvent =  {
						data : resize_cDialogEventData
					};
					sa.dialog.resize_cDialog (resize_cDialogEventData, ab.aid);
				};
				
				
				var 
				blurXwidth = ab.animationsettings_cDialog.blurX,
				blurYwidth = ab.animationsettings_cDialog.blurY,
				blurX = 0,
				blurY = 0,
				blurEnlarger = 2;
				
				switch (id) {
					case 'nw':
						blurX = 1 * blurEnlarger * blurXwidth;
						blurY = 1 * blurEnlarger * blurYwidth;
						//ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", 1 * blurEnlarger, 1 * blurEnlarger, 1);
						break;
					case 'n':
						blurX = 0 * blurXwidth;
						blurY = 1 * blurEnlarger * blurYwidth;
						//ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", 0, 1 * blurEnlarger, 1);
						break;
					case 'ne' :
						blurX = -1 * blurEnlarger * blurXwidth;
						blurY = 1 * blurEnlarger * blurYwidth;
						//ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", -1 * blurEnlarger, 1 * blurEnlarger, 1);
						break;
					case 'e' :
						blurX = -1 * blurEnlarger * blurXwidth;
						blurY = 0 * blurYwidth;
						//ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", -1 * blurEnlarger, 0, 1);
						break;
					case 'se':
						blurX = -1 * blurEnlarger * blurXwidth;
						blurY = -1 * blurEnlarger * blurYwidth;
						//ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", -1 * blurEnlarger, -1 * blurEnlarger, 1);
						break;
					case 's':
						blurX = 0 * blurXwidth;
						blurY = -1 * blurEnlarger * blurYwidth;
						//ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", 0, -1 * blurEnlarger, 1);
						break;
					case 'sw':
						blurX = -1 * blurEnlarger * blurXwidth;
						blurY = -1 * blurEnlarger * blurYwidth;
						//ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", 1 * blurEnlarger, -1 * blurEnlarger, 1);
						break;
					case 'w':
						blurX = 1 * blurEnlarger * blurXwidth;
						blurY = 0 * blurYwidth;
						//ab.stage.shadowInnerBorder = new createjs.Shadow("#000000", 2, 0, 1);
						break;
				};
				if (ab.animationsettings_cDialog.blurX<=0) {
					blurX = 0;
					blurY = 0;
				}
				if (ab.animationsettings_cDialog.blurY<=0) {
					blurX = 0;
					blurY = 0;
				}
				
				var blurFilter = new createjs.BlurFilter(blurX, blurY, 1);
				ab.blurFilter = blurFilter;
				ab.shape.filters = [ab.blurFilter];
				var bounds = blurFilter.getBounds();
				ab.shape.cache(-50, -50, 100+ab.shape.width, 100+ab.shape.height);
				ab.stage.update();
				
			}
			
		};
		cihe.css3.stage.update();
	},
	
	onmouseover : function (eventData) {
		var 
		cihe = eventData.currentTarget.parentNode,
		ab = eventData.target.parentNode,
		id = ab.id,
		aid = ab.aid,
		stage = ab.stage,
		shape = ab.shape,
		canvas = ab.canvas,
		ib = cihe.saInteractiveBorders;
		
		ab.animating = true;
		ab.animateTo = true;
		ab.animateToBeginning = false;
		ab.animateBack = false;
	},
	
	onmousedown : function (eventData) {
		var 
		cihe = eventData.currentTarget.parentNode,
		ab = eventData.target.parentNode,
		id = ab.id,
		aid = ab.aid,
		stage = ab.stage,
		shape = ab.shape,
		canvas = ab.canvas,
		ib = cihe.saInteractiveBorders;
		
	},
	
	onmouseout : function (eventData) {
		var 
		cihe = eventData.currentTarget.parentNode,
		ab = eventData.target.parentNode,
		id = ab.id,
		aid = ab.aid,
		stage = ab.stage,
		shape = ab.shape,
		canvas = ab.canvas,
		ib = cihe.saInteractiveBorders;

		ab.animating = true;
		ab.animateTo = false;
		ab.animateToBeginning = false;
		ab.animateBack = true;
	},
	
	resize_cDialog : function (resize_cDialogEventData, borderXYZonly) {
		if (resize_cDialogEventData.data) {
			var 
			cihe = resize_cDialogEventData.data.cihe;
		} else {
			var
			cihe = resize_cDialogEventData.cihe;
		};		
		
		var
		shape = cihe.shape,
		ib = cihe.saInteractiveBorders;
		
		var 
		ci = cihe,
		s = sa.vcc.settings[cihe.id];
		if (!s) debugger;
		
		var
		o = sa.vcc.options[cihe.id],
		sl = sa.dialog.settings[cihe.id],
		t = sa.vcc.getVividTheme (s.themeName); 
		if (!t) debugger;
		
		var 
		cdRaw = jQuery('#'+cihe.id+'__contentDimensions_cssRaw')[0],
		cd = jQuery('#'+cihe.id+'__contentDimensions')[0];
        
		/*if (cdRaw) {
			//causes cihe.css3.offsetHeight to be equal to content height; crap..
			cihe.css3.style.width = (cdRaw.offsetWidth) + 'px';
			cihe.css3.style.height = (cdRaw.offsetHeight) + 'px';
		} else {
			cihe.css3.style.width = (cihe.offsetWidth) + 'px';
			cihe.css3.style.height = (cihe.offsetHeight) + 'px';
		}*/
		//cihe.css3.style.zIndex = -1;
		if (cihe.canvas) {
			cihe.canvas.width = parseInt(cihe.css3.style.width);
			cihe.canvas.height = parseInt(cihe.css3.style.height);
		}
		if (cihe.stage) {
			cihe.stage.width = parseInt(cihe.css3.style.width);
			cihe.stage.height = parseInt(cihe.css3.style.height);
		}
		if (cihe.shape) {
			cihe.shape.width = parseInt(cihe.css3.style.width);
			cihe.shape.height = parseInt(cihe.css3.style.height);
		}

        /*
		if (jQuery('#'+cihe.id+'__dialog').length==1) {
			if (t.border) {
                /*
				var borderDetails = t.border.split(' ');
				if (borderDetails[0].match('px')) {
					var borderWidth = parseInt(borderDetails[0]);
					cihe.css3.style.position = 'absolute';
					cihe.css3.style.left = borderWidth + 'px';
					cihe.css3.style.top = borderWidth + 'px';
					cihe.css3.style.width = (jQuery('#'+cihe.id+'__dialog')[0].offsetWidth - (borderWidth * 2) ) + 'px';
					cihe.css3.style.height = (jQuery('#'+cihe.id+'__dialog')[0].offsetHeight - (borderWidth * 2) ) + 'px';
				}* /
				//ciheCSS3.style.border = t.border;
			} else {
				cihe.css3.style.width = jQuery('#'+cihe.id+'__dialog')[0].offsetWidth + 'px';
				cihe.css3.style.height = jQuery('#'+cihe.id+'__dialog')[0].offsetHeight + 'px';
			}
		} else {
			cihe.css3.style.width = '100%';
			cihe.css3.style.height = '100%';
		}*/
		//cihe.css3.style.zIndex = -1;
		
		// messes up everything! (the *entire* cascading of resize events from desktop.resize() to dialog.resize() and into scrollpane.resize()
		//cihe.style.width = (cihe.css3.offsetWidth) + 'px';
		//cihe.style.height = (cihe.css3.offsetHeight) + 'px';
		
		cihe.css3.canvas.width = (cihe.css3.offsetWidth) + 'px';
		cihe.css3.canvas.height = (cihe.css3.offsetHeight) + 'px';
		cihe.css3.stage.width = (cihe.css3.offsetWidth) + 'px';
		cihe.css3.stage.height = (cihe.css3.offsetHeight) + 'px';
		cihe.css3.shape.width = (cihe.css3.offsetWidth) + 'px'
		cihe.css3.shape.height = (cihe.css3.offsetHeight) + 'px';
		//jQuery('#'+cihe.id+'__dialog').css ({ overflow : 'visible' });
		//BUG_001__LOC_001
		//jQuery('#'+cihe.id+'__scrollpane').css ({ overflow : 'hidden' });

		
		if (
			jQuery('#'+cihe.id+'__scrollpane').length==1
		) {
			jQuery(cihe).css ({ overflow : 'hidden' });
		} else {
            /*
            jQuery(cihe).css ({ height : '100%' }); // BUG_002__LOC_005a_LINE_001of001
            //BUG_002__LOC_005b_BEGIN
			if (jQuery('#'+cihe.id+'__contentDimensions_cssRaw').length==1) {
                //jQuery(cihe).css ({ height : '100%' }); 
            } else {
                jQuery(cihe).css ({ height : '100%' }); 
            }
            //BUG_002__LOC_005b_END
            */
		}
		//jQuery(cihe).css ({ height : 'auto', overflow : 'hidden' });
		//jQuery('#'+cihe.id).css ({ overflow : 'hidden', height : '100%' });

		
		
		cihe.css3.stage.update();
		//cihe.style.border = '1px solid purple';
		
		var 
		br = sa.dialog.breakDownBorderRadius(cihe.css3.style.borderRadius),
		brw = { // br winners
			n : (br.nw > br.ne ? br.nw : br.ne),
			e : (br.ne > br.se ? br.ne : br.se),
			s : (br.se > br.sw ? br.se : br.sw),
			w : (br.sw > br.nw ? br.sw : br.nw)
		};
		
		//debugger;
		for (var id in ib) {
			if (
				borderXYZonly!==undefined
				&& id!==borderXYZonly
			) continue; // only process 1 border in the code below here, instead of all of them
			
			var 
			ab = ib[id],
			stage = ab.stage,
			shape = ab.shape,
			canvas = ab.canvas;
			
			cihe.style.zIndex = 100 * 1000 * 1000; // keep it at 100 million too, you dont want your content to disable the ability to resize_cDialog dialogs
			if (false) {
				ab.style.backgroundColor = 'white';
				//ab.style.border = '1px solid black';
			}
			ab.style.zIndex = 90 * 1000 * 1000;
			
			var cbbn = false; // canvas borders behave normally (zIndex of corners slightly higher than zIndex of middle segments (n,s,w,e))
			switch (id) {
				case 'nw':
					ab.style.left = (-1 * br.nw) + 'px';
					ab.style.top = (-1 * br.nw) + 'px';
					ab.style.height = (br.nw * 2) + 'px';
					ab.style.width = (br.nw * 2) + 'px';
					ab.style.zIndex = 1.1 * 1000 * 1000;
					
					canvas.width = parseInt(ab.style.width);
					canvas.height = parseInt(ab.style.height);
					canvas.top = 0;
					//canvas.style.border = '1px solid red';
					shape.width = parseInt(ab.style.width);
					shape.height = parseInt(ab.style.height);
					shape.top = 0;
					stage.width = parseInt(ab.style.width);
					stage.height = parseInt(ab.style.height);
					stage.top = 0;
					
					//stage.shadow = new createjs.Shadow("#000000", 1, 4, 5);
					stage.update();

					shape.graphics
						.beginStroke(ab.strokeColor)
						.setStrokeStyle (ab.strokeWidth);
					shape.graphics
						.moveTo ( br.nw, br.nw*2);
					shape.graphics	
						.curveTo (
							br.nw +sa.dialog.settings_cDialog.current.borders.strokeWidth, br.nw
							, br.nw*2, br.nw, 
							br.nw
						);
					stage.update();
					
					break;
				case 'n':
					ab.style.left = br.nw + 'px';
					//ab.style.top = (-1 * ab.strokeWidth) + 'px';
					ab.style.top = (-1 * brw.n) + 'px';
					ab.style.height = (brw.n * 2) + 'px';
					ab.style.width = (cihe.offsetWidth - br.nw - br.ne) + 'px';
					if (cbbn===true) ab.style.zIndex = 1.05 * 1000 * 1000; else ab.style.zIndex = 1.15 * 1000 * 1000;					

					canvas.width = parseInt(ab.style.width);
					canvas.height = parseInt(ab.style.height);
					canvas.top = 0;
					//canvas.style.border = '1px solid red';
					shape.width = parseInt(ab.style.width);
					shape.height = parseInt(ab.style.height);
					shape.top = 0;
					stage.width = parseInt(ab.style.width);
					stage.height = parseInt(ab.style.height);
					stage.top = 0;
					stage.update();
					
					shape.graphics
						.beginStroke(ab.strokeColor)
						.setStrokeStyle (ab.strokeWidth);
					shape.graphics
						//.moveTo ( 0, brw.n)
						.moveTo ( 0, brw.n + 1 )
						.lineTo ( parseInt(ab.style.width), brw.n +1  );
					ab.stage.update();
					
					break;
				case 'ne':
					ab.style.left = (parseInt(cihe.offsetWidth) - (brw.n) ) + 'px';
					ab.style.top = (-1 * (br.ne)) + 'px';
					ab.style.height = (br.ne * 2) + 'px';
					ab.style.width = (br.ne * 2) + 'px';
					ab.style.zIndex = 1.1 * 1000 * 1000;
					
					canvas.width = parseInt(ab.style.width);
					canvas.height = parseInt(ab.style.height);
					//canvas.style.border = '1px solid red';
					shape.width = parseInt(ab.style.width);
					shape.height = parseInt(ab.style.height);
					stage.width = parseInt(ab.style.width);
					stage.height = parseInt(ab.style.height);
					stage.update();
					
					shape.graphics
						.beginStroke(ab.strokeColor)
						.setStrokeStyle (ab.strokeWidth);
					shape.graphics
						.moveTo ( 
							0
							, br.ne
						);
					shape.graphics	
						.curveTo (
							br.ne - sa.dialog.settings_cDialog.current.borders.strokeWidth, br.ne
							, br.ne - sa.dialog.settings_cDialog.current.borders.strokeWidth, (br.ne * 2) 
							, br.ne
						);
					stage.update();
					
					break;
				case 'e' :
					ab.style.left = (cihe.offsetWidth - br.ne ) + 'px';
					ab.style.top = (br.ne) + 'px'
					ab.style.height = (cihe.offsetHeight - br.ne - br.se) + 'px';
					ab.style.width = (brw.e*2) + 'px';
					if (cbbn===true) ab.style.zIndex = 1.05 * 1000 * 1000; else ab.style.zIndex = 1.15 * 1000 * 1000;
					
					
					canvas.width = parseInt(ab.style.width);
					canvas.height = parseInt(ab.style.height);
					//canvas.style.border = '1px solid red';
					shape.width = parseInt(ab.style.width);
					shape.height = parseInt(ab.style.height);
					stage.width = parseInt(ab.style.width);
					stage.height = parseInt(ab.style.height);
					stage.update();
					
					shape.graphics
						.beginStroke(ab.strokeColor)
						.setStrokeStyle (ab.strokeWidth);
					shape.graphics
						.moveTo ( br.ne - sa.dialog.settings_cDialog.current.borders.strokeWidth , 0 )
						.lineTo ( br.ne -sa.dialog.settings_cDialog.current.borders.strokeWidth, parseInt(ab.style.height) );							
					stage.update();
					break;
				case 'se':
					ab.style.left = (parseInt(cihe.offsetWidth) - (brw.s) ) + 'px';
					ab.style.top = (parseInt(cihe.offsetHeight) - (br.se)) + 'px';
					ab.style.height = (br.se * 2) + 'px';
					ab.style.width = (br.se * 2) + 'px';
					
					ab.style.zIndex = 1.1 * 1000 * 1000;
					
					canvas.width = parseInt(ab.style.width);
					canvas.height = parseInt(ab.style.height);
					//canvas.style.border = '1px solid red';
					shape.width = parseInt(ab.style.width);
					shape.height = parseInt(ab.style.height);
					stage.width = parseInt(ab.style.width);
					stage.height = parseInt(ab.style.height);
					stage.update();

					shape.graphics
						.beginStroke(ab.strokeColor)
						.setStrokeStyle (ab.strokeWidth);
					shape.graphics
						.moveTo ( br.se -sa.dialog.settings_cDialog.current.borders.strokeWidth, 0 );
					shape.graphics	
						.curveTo (
							br.se - sa.dialog.settings_cDialog.current.borders.strokeWidth, br.se -sa.dialog.settings_cDialog.current.borders.strokeWidth
							, -1 * sa.dialog.settings_cDialog.current.borders.strokeWidth, br.se - sa.dialog.settings_cDialog.current.borders.strokeWidth
							, br.se
						);
					stage.update();
						
					break;
				case 's':
					ab.style.left = (br.sw) + 'px';
					ab.style.top = (parseInt(cihe.offsetHeight) - (brw.s) ) + 'px';
					ab.style.height = (brw.s*2) + 'px';
					ab.style.width = (parseInt(cihe.offsetWidth) - (br.sw) - (br.se) ) + 'px';
					if (cbbn===true) ab.style.zIndex = 1.05 * 1000 * 1000; else ab.style.zIndex = 1.15 * 1000 * 1000;					
					
					canvas.width = parseInt(ab.style.width);
					canvas.height = parseInt(ab.style.height);
					//canvas.style.border = '1px solid red';
					shape.width = parseInt(ab.style.width);
					shape.height = parseInt(ab.style.height);
					stage.width = parseInt(ab.style.width);
					stage.height = parseInt(ab.style.height);
					stage.update();
					
					shape.graphics
						.beginStroke(ab.strokeColor)
						.setStrokeStyle (ab.strokeWidth);
					shape.graphics
						.moveTo ( 0, brw.s - sa.dialog.settings_cDialog.current.borders.strokeWidth )
						.lineTo ( parseInt(ab.style.width), brw.s -sa.dialog.settings_cDialog.current.borders.strokeWidth );							
					stage.update();
					break;
				case 'sw': 
					ab.style.left = (-1 * (br.sw)) + 'px';
					ab.style.top = (parseInt(cihe.offsetHeight) - (br.sw)) + 'px';
					ab.style.height = (brw.w * 2) + 'px';
					ab.style.width = (brw.w * 2) + 'px';
					ab.style.zIndex = 1.1 * 1000 * 1000;
					
					canvas.width = parseInt(ab.style.width);
					canvas.height = parseInt(ab.style.height);
					//canvas.style.border = '1px solid red';
					shape.width = parseInt(ab.style.width);
					shape.height = parseInt(ab.style.height);
					stage.width = parseInt(ab.style.width);
					stage.height = parseInt(ab.style.height);
					stage.update();
					
					shape.graphics
						.beginStroke(ab.strokeColor)
						.setStrokeStyle (ab.strokeWidth);
					shape.graphics
						.moveTo ( 
							br.sw*2, br.sw - sa.dialog.settings_cDialog.current.borders.strokeWidth
						);
					shape.graphics	
						.curveTo (
							br.sw, br.sw - sa.dialog.settings_cDialog.current.borders.strokeWidth
							, br.sw, (-1 * sa.dialog.settings_cDialog.current.borders.strokeWidth)
							, br.sw
						);
						/*
						.curveTo (
							shape.strokeWidth, parseInt(shape.height) - (shape.strokeWidth)
							, br.sw + shape.strokeWidth, parseInt(shape.height) - (shape.strokeWidth)
							, br.sw
						);*/
					stage.update();
					break;
				case 'w':
					//ab.style.left = (-1 * (brw.w)) + 'px';
					ab.style.left = (-1 * (brw.w)) + 'px';
					ab.style.top = (parseInt(br.nw)) + 'px'
					ab.style.height = (parseInt(cihe.offsetHeight) - br.nw - br.sw) + 'px';
					ab.style.width = (brw.w*2) + 'px';
					if (cbbn===true) ab.style.zIndex = 1.05 * 1000 * 1000; else ab.style.zIndex = 1.15 * 1000 * 1000;					
					
					canvas.width = parseInt(ab.style.width);
					canvas.height = parseInt(ab.style.height);
					//canvas.style.border = '1px solid red';
					shape.width = parseInt(ab.style.width);
					shape.height = parseInt(ab.style.height);
					stage.width = parseInt(ab.style.width);
					stage.height = parseInt(ab.style.height);
					stage.update();
					
					shape.graphics
						.beginStroke(ab.strokeColor)
						.setStrokeStyle (ab.strokeWidth);
					shape.graphics
						.moveTo ( brw.e + 2 , parseInt(shape.height) )
						.lineTo ( brw.e + 2, 0 );							
					stage.update();
					break;
			};
			
			if (!ab.blurFilter) {
				var blurFilter = new createjs.BlurFilter(1, 1, 1);
				shape.filters = [blurFilter];
				var bounds = blurFilter.getBounds();
				shape.cache(-50, -50, 100+shape.width, 100+shape.height);
				
				ab.blurFilter = blurFilter;
			}
			
			ab.stage.update();
			
			cihe.css3.stage.update();
		}
	},
	
	breakDownBorderRadius : function (borderRadiusStyleProperty) {
		borderRadiusStyleProperty = borderRadiusStyleProperty.replace('px', '');
		var 
		ret = {},
		split = borderRadiusStyleProperty.split(' ');
		switch (split.length) {
			case 4:
				ret.nw = split[0];
				ret.ne = split[1];
				ret.se = split[2];
				ret.sw = split[3];
				break;
			case 3:
				ret.nw = split[0];
				ret.ne = ret.sw = split[1];
				ret.se = split[2];
				break;
			case 2:
				ret.nw = ret.se = split[0];
				ret.ne = ret.sw = split[1];
				break;
			case 1:
				ret.nw = ret.se = ret.ne = ret.sw = split[0];
				break;
		}
		for (var id in ret) {
			ret[id] = parseInt(ret[id]);
			//ret[id] = parseInt(ret[id]/2); // width of edge/border detection divs
		}
		
		return ret;
	}
	
	
};
