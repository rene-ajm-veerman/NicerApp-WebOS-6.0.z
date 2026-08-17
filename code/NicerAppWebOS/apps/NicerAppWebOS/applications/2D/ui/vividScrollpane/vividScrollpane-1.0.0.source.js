seductiveapps.sp = seductiveapps.vividScrollpane = {
	about : {
		whatsThis : 'seductiveapps.sp = seductiveapps.vividScrollpane = A PNG-artwork scrollpane component in javascript without dependencies',
		copyright : '(c) (r) 2011-2018 by [the owner of seductiveapps.com] <seductiveapps@gmail.com>',
		license : 'http://seductiveapps.com/LICENSE.txt',
		noWarranty : 'NO WARRANTY EXPRESSED OR IMPLIED. USE ONLY AT YOUR OWN RISK.',
		version : '1.2.0',
		firstReleased : '2011 June',
		lastUpdated : '2013 April 8, 08:57 CEST'
	},
	settings : {
		animatedSliderMove : [],
		animatedScrollTo : [],
		timers : {},
		touchDeviceScroll : {}
	},
	
	log : function (errLevel, errData) {
		sa.m.log (errLevel, errData);
	},
	
	init : function (scrollpane, initID) { // scrollpane = Component Instance HTML Element
	
		/*
		if (
			sa.s.c.settings.booting
			|| sa.desktop.settings.animating
		) {
			sa.m.waitForCondition ('waiting for startup or animations to complete', function () {
				return (
					!sa.s.c.settings.booting
					&& !sa.desktop.settings.animating
				);
			}, function () {
				sa.sp.init (scrollpane, initID);
			}, 500);
			
		}*/
	
		if (
			document.getElementById (scrollpane.id+'__scrollpane__container')
			|| 
			scrollpane.style.display=='none'
		) {
			sa.m.log (2, { msg : 'sa.vividScrollpane.init(): for initID='+initID+'; NOT initializing element with id="'+scrollpane.id+'" and class="'+scrollpane.className+'", because .style.display=="none" OR component is already initialized.' } );
			sa.vcc.componentFullyInitialized (initID, scrollpane.id);
			return false;
		} else {
			sa.m.log (2, { msg : 'sa.vividScrollpane.init(): for initID='+initID+'; Initializing element with id="'+scrollpane.id+'" and class="'+scrollpane.className+'".' } );
			
			if (sa.vcc.newInstanceNeeded(scrollpane)) {
				sa.vcc.newInstance (scrollpane, initID);
			}

		}
		//sa.sp.startPreloadOfTheme (scrollpane, initID);
		sa.sp.themeLoaded (scrollpane);
	},
	
	startPreloadOfTheme : function (scrollpane, initID) {
		//debugger;
		if (jQuery('#'+scrollpane.id+'__container').length>0) return false;
		if (sa.vcc.newInstanceNeeded(scrollpane)) {
			sa.vcc.newInstance (scrollpane, initID);
		} /*else {
		// causes sa.site.code.setOverallTheme to be full of errors
			return false;
		}*/
		var s = sa.vcc.settings[scrollpane.id];
		if (!s.themeName) { debugger; return null; }
		var t = sa.vcc.getVividTheme (s.themeName); 
        if (!t) {
            debugger;
            return false;
        }
		s.theme = t;
		if (!t) {
			sa.m.log (2, { msg : 'sa.vividScrollpane.init(): for initID='+initID+'; INVALID THEME "'+s.themeName+'" for element with id="'+scrollpane.id+'" and class="'+scrollpane.className+'".' } );
			debugger;
			return false;
		};
		if (t.loaded) {
			sa.sp.themeLoaded (scrollpane);			
			return false;
		};
		if (t.loading) {
			sa.m.waitForCondition ('vividTheme "'+s.themeName+'" loading', function () {
				return t.loaded;
			}, function () {
				debugger;
				sa.sp.themeLoaded (scrollpane);
			}, Math.floor(Math.random(150,200)));
		};
		t.loading = true;
		s = sa.vcc.newInstanceThemeOverrides (s, t);
		//debugger;
		
		var d = document.createElement('DIV');
		d.id = scrollpane.id+'__images';
		d.style.display = 'none';
		scrollpane.appendChild (d);
		
		s.images = t.images;
		s.imagesLoaded = {};
		if (typeof sa.tracer!=='undefined') {
			var ua = sa.tracer.findUA(arguments);	
		} else {
			var ua = false;
		};
		var html = '';
		var gotAll = false;
		for (var imgID in s.images) {
			var url = s.theme.baseURL + '/' + s.images[imgID].url;
			
			var img = document.createElement('img');
			img.src = url;
			img.id = imgID;
			if (img.complete || img.readyState===4) {
				//gotAll = true;
				
				sa.sp.imageLoaded (scrollpane, img);
				continue;
				//s.imagesLoaded[s.imagesLoaded.length] = imgID;
			} else {
				if (ua) {
					html += '<img style="width:1px;height:1px" id="'+scrollpane.id+'__'+imgID+'" src="'+url+'" onload="var t=this;sa.m.reAttachUA('+ua.uaIdx+', function(){sa.sp.imageLoaded(document.getElementById(\''+scrollpane.id+'\'), t)})();"/>';
				} else {
					html += '<img style="width:1px;height:1px" id="'+scrollpane.id+'__'+imgID+'" src="'+url+'" onload="sa.sp.imageLoaded(document.getElementById(\''+scrollpane.id+'\'), this);"/>';
				}
			}
		};

		if (gotAll) {
			sa.sp.initScrollpane (scrollpane);
		} else {
			d.innerHTML += html;
		}
	},
	
	imageLoaded : function (scrollpane, img, uaIdx) {
		var s = sa.vcc.settings[scrollpane.id];
		var t = sa.vcc.getVividTheme (s.themeName); 
		
		var imgID = img.id.split('__');
		imgID = imgID[imgID.length-1];
		s.imagesLoaded[imgID] = img;
		
		var m = n = 0;
		for (var i in s.images) m++;
		for (var j in s.imagesLoaded) n++;
		sa.m.log (2, 'sa.vividScrollpane.imageLoaded() : loaded '+n+' of '+m+' images');

		if (t.loaded) {
			sa.sp.themeLoaded (scrollpane);			
		} else if (m==n /*&& !s.initialized*/ ) {
			delete t.loading;
			t.loaded = true;
			s.initialized = true;
			sa.sp.themeLoaded (scrollpane);
		}	
		
	},
	
	themeLoaded : function (scrollpane) {
		sa.sp.initScrollpane (scrollpane);
	},
	
	initScrollpane : function (scrollpane) {
		var
		baseID = scrollpane.id.replace('__container', '').replace('__scrollpane', '').replace('__dialogCreated','').replace('__partOfAwhole', '').replace('__dialog', ''),
		dialog = jQuery('#'+baseID+'__dialog')[0],
		cihe = jQuery('#'+baseID)[0],
		contentDimensions = jQuery('#'+baseID+'__contentDimensions')[0],
		scrollpaneContainer = jQuery('#'+baseID+'__scrollpane__container')[0],
		scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__partOfAwhole')[0];
		if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__dialogCreated')[0];
        /*
        if (!scrollpaneDiv) {
                var scrollpaneDiv = document.createElement ('DIV');
                scrollpaneDiv.id = baseID+'__scrollpane__dialogCreated';
                scrollpaneDiv.className = 'vsp_containerDiv';
                scrollpaneDiv.style.position = 'relative';
                scrollpaneDiv.style.width = '100%';
                scrollpaneDiv.style.height = 'auto'; // 100%;
                scrollpaneDiv.style.left = '0px';
                scrollpaneDiv.style.top = '0px';
                sa.vcc.newInstance (scrollpaneDiv, 1);
                cihe.appendChild (scrollpaneDiv);
        }
        */
		if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane')[0];
		if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID)[0]; // commented out for hm()
		var
		css3 = jQuery('#'+baseID+'__CSS3');
        
        
        //jQuery('#'+baseID).css ({ height : 'auto' });
		
		
		var s = sa.vcc.settings[baseID+'__scrollpane__partOfAwhole'];
		if (!s) var s = sa.vcc.settings[baseID+'__scrollpane__dialogCreated'];
		if (!s) var s = sa.vcc.settings[baseID+'__scrollpane'];
		//if (!s) s = sa.vcc.settings[baseID];
		if (!s) debugger; // gotta get fixed at some point
		var t = sa.vcc.getVividTheme (s.themeName); 
		
		//if (baseID=='siteYoutubeSearch') debugger;
		
		sa.m.log (2, 'sa.vividScrollpane.initScrollpane(): for initID='+s.initID+'; Constructing scrollpane HTML for id="'+scrollpane.id+'" and class="'+scrollpane.className+'".');
		
		//if (scrollpane.id=='saApp_search_youtube_thumbViewer') debugger;
	/*
		if (scrollpane.css3) {
			//debugger;
			scrollpane.parentNode.appendChild ( scrollpane.removeChild (scrollpane.css3) );
		}
		*/
    //if (baseID=='siteAds') debugger;
		if (jQuery('#'+baseID+'__scrollpane__container').length>0)  return false;
		
		var ticketID = sa.m.showAllParents (scrollpane);

		//if (baseID=='siteContent__scrollpane') debugger;
        
			var scrollpaneContainer = document.createElement ('DIV');
			scrollpaneContainer.id = baseID+'__scrollpane__container';
			scrollpaneContainer.className = 'vsp_container';
            scrollpaneContainer.style.position = 'relative';
            if (cihe && cihe.id.match(/hm_/)) {
                scrollpaneContainer.style.height = '100%';
            } else {
                scrollpaneContainer.style.height = scrollpane.style.height;
            }
			//sa.vcc.newInstance (scrollpaneContainer, 1); //2018-09-05(Wednesday) : EXPERIMENTAL TO HAVE THIS COMMENTED OUT
		
            /*
                var scrollpaneDiv = document.createElement ('DIV');
                scrollpaneDiv.id = baseID+'__scrollpane';
                scrollpaneDiv.className = 'vsp_containerDiv';
                scrollpaneDiv.style.position = 'relative';
                scrollpaneDiv.style.width = '100%';
                scrollpaneDiv.style.height = 'auto'; // 100%;
                scrollpaneDiv.style.left = '0px';
                scrollpaneDiv.style.top = '0px';

                sa.vcc.newInstance (scrollpaneDiv, 1);
                */
                
                //if (jQuery('#'+baseID+'__scrollpane')[0]) {
                    //var scrollpaneDiv = document.getElementById(baseID+'__scrollpane');
                //} else {
                
                //jQuery('#'+baseID+'__scrollpane').remove();
                
                    if (false && cihe && cihe.id.match(/hm_/)) {
                        cihe.parentNode.insertBefore (scrollpaneContainer, cihe);
                    } else {
                        scrollpane.parentNode.insertBefore (scrollpaneContainer, scrollpane);
                    }
                    //scrollpaneContainer.appendChild (scrollpaneDiv);
                    //scrollpaneContainer.appendChild (scrollpaneDiv);
                    //scrollpaneDiv.appendChild (cihe.parentNode.removeChild(cihe));

                    //if (!cihe) return false;
                    if (cihe && cihe===scrollpaneDiv) {                    
                        scrollpaneContainer.appendChild (cihe.parentNode.removeChild(cihe));
                    } else {
                        if (cihe && cihe.id.match(/hm_/)) {
                            debugger;
                            //scrollpaneContainer.appendChild (scrollpane.parentNode.removeChild(scrollpane));
                            //scrollpane.appendChild (cihe.parentNode.removeChild(cihe));
                            
                            
                            //scrollpaneContainer.appendChild (cihe.parentNode.removeChild(cihe));

                            //scrollpaneContainer.appendChild (scrollpaneDiv);
                            //scrollpaneDiv.appendChild (cihe.parentNode.removeChild(cihe));
                            
                            
                            //http://localhost/seductiveapps/ui/jsonViewer/jsonViewer_sample_data.json.php?url=http://localhost/test.json
                            jQuery(scrollpaneContainer).css ({
                                width : jQuery('#siteContent__scrollpane').width(),
                                height : jQuery('#siteContent__scrollpane').height()
                            });
                            jQuery('#siteContent').css({display:'block'});
                            scrollpaneContainer.appendChild (scrollpaneDiv.parentNode.removeChild(scrollpaneDiv));
                            scrollpaneDiv.appendChild (cihe.parentNode.removeChild(cihe));
                        } else if (cihe) {
                            scrollpaneContainer.appendChild (scrollpaneDiv.parentNode.removeChild(scrollpaneDiv));
                            scrollpaneDiv.appendChild (cihe.parentNode.removeChild(cihe));
                        } else {
                            scrollpaneContainer.appendChild (                            scrollpaneDiv.parentNode.removeChild(scrollpaneDiv));
                        }
                    }
                //}

                /*
                scrollpaneDiv.className = 'vsp_containerDiv';
                scrollpaneDiv.style.position = 'relative';
                scrollpaneDiv.style.width = '100%';
                scrollpaneDiv.style.height = 'auto'; //100%;
                scrollpaneDiv.style.left = '0px';
                scrollpaneDiv.style.top = '0px';
                scrollpaneDiv.initialWidth = scrollpaneDiv.offsetWidth;
                scrollpaneDiv.initialHeight = scrollpaneDiv.offsetHeight;
                */
            
			//debugger;
            /*
			if (jQuery('#'+baseID+'__scrollpane__container').length==1) {
			} else if (jQuery('#'+baseID+'__scrollpane').length==0) {
				cihe.parentNode.appendChild (scrollpaneContainer);
				scrollpaneContainer.appendChild (scrollpaneDiv);
				scrollpaneDiv.appendChild (scrollpane.parentNode.removeChild(scrollpane));
				if (cihe!==scrollpane) scrollpane.appendChild (cihe.parentNode.removeChild(cihe));
			} else {
				//debugger;
				scrollpane.parentNode.appendChild (scrollpaneContainer);
				if (cihe) {
					scrollpaneContainer.appendChild (scrollpane.parentNode.removeChild(scrollpane));
					if (!jQuery(scrollpane, cihe)) scrollpane.appendChild (cihe.parentNode.removeChild(cihe));
				} else if (scrollpaneDiv) {
					if (scrollpaneDiv.parentNode) {
						scrollpaneContainer.appendChild (scrollpaneDiv.parentNode.removeChild(scrollpaneDiv));
					} else {
						scrollpaneContainer.appendChild (scrollpaneDiv);
					}
				}
			}
			*/
			

			//if (!scrollpane || !scrollpane.id) {
			
			//if (jQuery('#'+baseID+'__scrollpane__container').length==0) scrollpane.parentNode.appendChild (scrollpaneContainer);
			
			//scrollpaneDiv = jQuery('#'+baseID+'__scrollpane')[0];
			//debugger;
			//setTimeout (function() {
				/*
				if (scrollpaneContainer.id.match('__scrollpane__container')) {
					scrollpane.parentNode.appendChild (scrollpaneContainer);
					scrollpaneContainer.appendChild (scrollpaneDiv);
					scrollpaneDiv.appendChild (scrollpane.parentNode.removeChild(scrollpane));
					
				} else if (scrollpane.id.match('__scrollpane')) {
					scrollpaneContainer.appendChild (scrollpane.parentNode.removeChild(scrollpane));
				} /*else {
					scrollpaneContainer.appendChild (scrollpaneDiv);
				}*/
			//}, 500);
			
			/* T32
			if (jQuery('#'+baseID+'__contentDimensions').length==1) {
				jQuery('#'+baseID+'__scrollpane, #'+baseID+'__scrollpane__dialogCreated, '+baseID+'__scrollpane__partOfwhole').css ({
					width : jQuery('#'+baseID+'__contentDimensions')[0].offsetWidth,
					height : 'auto'//jQuery('#'+baseID+'__contentDimensions')[0].offsetHeight
				});
			} else {
				jQuery('#'+baseID+'__scrollpane, #'+baseID+'__scrollpane__dialogCreated, '+baseID+'__scrollpane__partOfwhole').css ({
					width : jQuery('#'+baseID+'__scrollpane__container')[0].offsetWidth,
					height : 'auto'//jQuery('#'+baseID+'__scrollpane__container')[0].offsetHeight
				});
            }
            */
		//}
		
		
		//setTimeout (function() {
            /*
			scrollpaneContainer.style.visibility = scrollpane.style.visibility;
            //if (scrollpane.style.position=='relative') scrollpaneContainer.style.position = 'relative';
			if (scrollpane.style.width) scrollpaneContainer.style.width = scrollpane.style.width;
            if (scrollpane.style.maxWidth) scrollpaneContainer.style.maxWidth = scrollpane.style.maxWidth;
			if (scrollpane.style.height) scrollpaneContainer.style.height = scrollpane.style.height;
            if (scrollpane.style.maxHeight) scrollpaneContainer.style.maxHeight = scrollpane.style.maxHeight;
	//		scrollpaneContainer.style.height = '100%';
	//		scrollpaneContainer.style.width = '100%';
			scrollpaneContainer.style.left = scrollpane.style.left;
			scrollpaneContainer.style.top = scrollpane.style.top;
            scrollpaneContainer.style.overflow = 'hidden';
			scrollpane.style.left = '0px';
			scrollpane.style.top = '0px';
            /*
            scrollpane.style.maxWidth = '';
            scrollpane.style.maxHeight = '';
            scrollpane.style.height = '';
            //scrollpane.style.width = ' auto';
            //scrollpane.style.height = '';
            scrollpane.style.width = ' ';
            * /
            scrollpane.style.width = scrollpaneContainer.style.width - 25;
            if (scrollpane.style.maxHeight) scrollpane.style.height = scrollpane.style.maxHeight;
            //scrollpane.style.height = 'auto';
            scrollpane.style.overflow = '';
            */
            
			scrollpaneContainer.style.visibility = scrollpane.style.visibility;
            if (!(cihe && cihe.id.match(/hm_/))) {
            if (contentDimensions) {
                scrollpaneContainer.style.width = contentDimensions.offsetWidth;
                scrollpaneContainer.style.height = contentDimensions.offsetHeight;
            } else {
                scrollpaneContainer.style.width = scrollpane.style.width;
                scrollpaneContainer.style.height = scrollpane.style.height;
            }
            }
	//		scrollpaneContainer.style.height = '100%';
	//		scrollpaneContainer.style.width = '100%';
			scrollpaneContainer.style.left = scrollpane.style.left;
			scrollpaneContainer.style.top = scrollpane.style.top;
			scrollpane.style.left = '0px';
			scrollpane.style.top = '0px';
            scrollpane.style.overflow = 'hidden';
            //scrollpane.style.width = (scrollpane.offsetWidth - 30) + 'px';
            
            
            
            
            jQuery(scrollpaneDiv).css ({
                left : (
                    (t.options && t.options.ver_sliderbar_position=='left')
                    ? s.theme.images.ver_sliderbar.width + 5
                    : 0
                )//,
               // height : scrollpaneContainer.offsetHeight
                
            });
            
            /*
            if (t.options && t.options.ver_sliderbar_position=='left') {
                jQuery(scrollpaneDiv).css ({ height:'auto', paddingLeft : s.theme.images.ver_sliderbar.width + 5, width : scrollpaneDiv.offsetWidth + s.theme.images.ver_sliderbar.width + 5 });
                jQuery('#'+baseID).css ({ width :scrollpane.offsetWidth + s.theme.images.ver_sliderbar.width + 5 });
            } else {
                jQuery(scrollpaneDiv).css ({ left : scrollpaneContainer.offsetWidth });
            }*/
            
            
			sa.m.restoreAllParents (ticketID);

	/*
			scrollpane.style.width = '100%';
			scrollpane.style.height = '100%';
			scrollpane.style.left = '0px';
			scrollpane.style.top = '0px';

			var w = scrollpane.offsetWidth;
			var h = scrollpane.offsetHeight;
			if (scrollpane.style.paddingLeft) w -= parseFloat(scrollpane.style.paddingLefs.theme.replace('px',''));
			if (scrollpane.style.paddingRight) w -= parseFloat(scrollpane.style.paddingRighs.theme.replace('px',''));
			if (scrollpane.style.marginLeft) w -= parseFloat(scrollpane.style.marginLefs.theme.replace('px',''));
			if (scrollpane.style.marginRight) w -= parseFloat(scrollpane.style.marginRighs.theme.replace('px',''));
			if (scrollpane.style.paddingTop) h -= parseFloat(scrollpane.style.paddingTop.replace('px',''));
			if (scrollpane.style.paddingBottom) h -= parseFloat(scrollpane.style.paddingBottom.replace('px',''));
			if (scrollpane.style.marginTop) h -= parseFloat(scrollpane.style.marginTop.replace('px',''));
			if (scrollpane.style.marginBottom) h -= parseFloat(scrollpane.style.marginBottom.replace('px',''));
			scrollpaneContainer.style.width = w + 'px';
			scrollpaneContainer.style.height = h + 'px';
	*/
			scrollpaneContainer.style.paddingLeft = scrollpane.style.paddingLeft;
			scrollpaneContainer.style.paddingRight = scrollpane.style.paddingRight;
			scrollpaneContainer.style.paddingTop = scrollpane.style.paddingTop;
			scrollpaneContainer.style.paddingBottom = scrollpane.style.paddingBottom;
			scrollpane.style.padding = '0px';
			scrollpaneContainer.style.marginLeft = scrollpane.style.marginLeft;
			scrollpaneContainer.style.marginRight = scrollpane.style.marginRight;
			scrollpaneContainer.style.marginTop = scrollpane.style.marginTop;
			scrollpaneContainer.style.marginBottom = scrollpane.style.marginBottom;
			scrollpane.style.margin = '0px';
			
			/*
			var 
			ds = scrollpane.parentNode.removeChild (scrollpane);
			scrollpaneContainer.appendChild (ds);
			var
			scrollpane = ds;
			//scrollpaneContainer.appendChild (scrollpane);
			
			//scrollpane.style.height = 'auto';
			*/
			
			/*
			sa.m.waitForCondition ('scrollpane container '+scrollpane.id, function() {
				var
				baseID = scrollpane.id.replace('__container', '').replace('__scrollpane', '').replace('__dialogCreated','').replace('__partOfAwhole').replace('__dialog', ''),
				dialog = jQuery('#'+baseID+'__dialog')[0],
				cihe = jQuery('#'+baseID)[0],
				contentDimensions = jQuery('#'+baseID+'__contentDimensions')[0],
				scrollpaneContainer = jQuery('#'+baseID+'__scrollpane__container')[0],
				scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__partOfAwhole')[0];
				if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__dialogCreated')[0];
				if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane')[0];
				var
				css3 = jQuery('#'+baseID+'__CSS3');
				return true;//scrollpaneContainer && scrollpaneDiv;
				
			}, function () {*/
				var
				baseID = scrollpane.id.replace('__container', '').replace('__scrollpane', '').replace('__dialogCreated','').replace('__partOfAwhole').replace('__dialog', ''),
				dialog = jQuery('#'+baseID+'__dialog')[0],
				cihe = jQuery('#'+baseID)[0],
				contentDimensions = jQuery('#'+baseID+'__contentDimensions')[0],
				scrollpaneContainer = jQuery('#'+baseID+'__scrollpane__container')[0],
				scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__partOfAwhole')[0];
				if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__dialogCreated')[0];
				if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane')[0];
				if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID)[0];
				var
				css3 = jQuery('#'+baseID+'__CSS3')

                /*
				if (cihe) {
					var s = sa.vcc.settings[cihe.id];
                    //if (!s) var s = sa.vcc.settings[cihe.id.replace(/__outer/,'__scrollpane')];
                    if (!s) var s = sa.vcc.settings[scrollpaneDiv.id];
				} else {
					var s = sa.vcc.settings[scrollpaneDiv.id];
				}
				*/
			
			
			//if (baseID=='mp3s') debugger;

				if (scrollpaneContainer) scrollpaneContainer.theme = t;
				if (scrollpane) scrollpane.theme = t;

                s.containsIframe = jQuery('iframe',  scrollpaneDiv)[0];
                if (s.containsIframe) {
                    s.cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                    s.sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                };
				s.items[0] = { //holds settings for the vertical scrollbar
					type : 'vertical',
					dialog : dialog,
					css3 : css3,
					cihe : cihe,
					contentDimensions : contentDimensions,
					scrollpaneContainer : scrollpaneContainer,
					scrollpane : scrollpaneDiv,
					scrollpaneDiv : scrollpaneDiv,
					theme : s.theme,
                    s : s,
					scrollCmds : [],
					sliderbarShown : false
				};
				s.items[1] = { //holds settings for the horizontal scrollbar
					type : 'horizontal',
					dialog : dialog,
					css3 : css3,
					cihe : cihe,
					contentDimensions : contentDimensions,
					scrollpaneContainer : scrollpaneContainer,
					scrollpane : scrollpaneDiv,
					scrollpaneDiv : scrollpaneDiv,
					theme : s.theme,
                    s : s,
					scrollCmds : [],
					sliderbarShown : false
				};
				/* 2017-08 PRE-canvasDialog
				s.items[0] = { //holds settings for the vertical scrollbar
					type : 'vertical',
					scrollpane : scrollpane,
					container : container,
					theme : s.theme,
					scrollCmds : [],
					sliderbarShown : false
				};
				s.items[1] = { //holds settings for the horizontal scrollbar
					type : 'horizontal',
					scrollpane : scrollpane,
					container : container,
					theme : s.theme,
					scrollCmds : [],
					sliderbarShown : false
				};
				*/


				
				
				// causes serious bugs:
				//scrollpane.scrollTop = 0;
				//scrollpane.scrollLeft = 0;
				
				/*
				scrollpane.style.overflow = 'hidden';
				if (scrollpane.style.position=='absolute') {
					scrollpaneContainer.style.position = 'absolute';
					scrollpaneContainer.style.left = scrollpane.style.left;
					scrollpane.style.left = '0px';
					scrollpaneContainer.style.top = scrollpane.style.top;
					scrollpane.style.top = '0px';
					//scrollpane.style.position = 'relative';
					//debugger;
					// too late # scrollpane.childNodes[0].style.height = 'auto';
					scrollpane.childNodes[0].style.height = 'auto';
				} else {
					if (scrollpane.childNodes[0] && scrollpane.childNodes[0].style) {
									scrollpane.childNodes[0].style.height = 'auto';
								}
					//too late # scrollpaneContainer.style.position = 'relative';
					//scrollpane.style.height = 'auto';
				};
				*/
				
				//debugger;
				/* TODO : experimental, was supposed to solve the comments content container from being in the wrong spot
				if (scrollpaneContainer.parentNode && scrollpaneContainer.parentNode.tagName.toLowerCase()=='td') {
					scrollpaneContainer.style.left = '0px';
					scrollpaneContainer.style.top = '0px';
				};
				*/

				if (scrollpaneDiv) {
					sa.sp.renderScrollbarVertical (scrollpaneDiv);
					sa.sp.renderScrollbarHorizontal (scrollpaneDiv);
				} else if (scrollpane) {
					sa.sp.renderScrollbarVertical (scrollpane);
					sa.sp.renderScrollbarHorizontal (scrollpane);
				}
				
				/*
				if (scrollpane.className.match('saPhotoAlbum_iframe')) {
					sa.m.hookEvent (scrollpane, 'mousedown', function (evt) { sa.sp._mousedownContent(evt,scrollpane); }, true, true);
					sa.m.hookEvent (scrollpane, 'mouseup', function (evt) { sa.sp._mouseupContent(evt,scrollpane); }, true, true);
					sa.m.hookEvent (scrollpane, 'mouseout', function (evt) { sa.sp._mouseoutContent(evt,scrollpane); }, true, true);
				}*/

                if (s.containsIframe) {
                    sa.m.addEventListener (s.sp, 'mouseover', sa.m.traceFunction(function (){
                        if (!sa.sp.scrollCmds_hasAnyOfTypes (s, ['mousewheel'])) {
                            sa.sp.initAutomatedScrolling(scrollpane, 'vertical', false);
                            sa.sp.initAutomatedScrolling(scrollpane, 'horizontal', false);
                        }
                    }));
                    sa.m.addEventListener (s.sp, 'mouseout', sa.m.traceFunction(function (){
                        //if (!sa.sp.scrollCmds_hasAnyOfTypes (s, ['mousewheel'])) {
                            //debugger;
                            if (s.items[0].automatedScrolling) sa.sp.initAutomatedScrolling(scrollpane, 'vertical', true);
                            if (s.items[1].automatedScrolling) sa.sp.initAutomatedScrolling(scrollpane, 'horizontal', true);
                        //}
                    }));
                } else {
                    sa.m.addEventListener (scrollpaneContainer, 'mouseover', sa.m.traceFunction(function (){
                        if (!sa.sp.scrollCmds_hasAnyOfTypes (s, ['mousewheel'])) {
                            sa.sp.initAutomatedScrolling(scrollpane, 'vertical', false);
                            sa.sp.initAutomatedScrolling(scrollpane, 'horizontal', false);
                        }
                    }));
                    sa.m.addEventListener (scrollpaneContainer, 'mouseout', sa.m.traceFunction(function (){
                        //if (!sa.sp.scrollCmds_hasAnyOfTypes (s, ['mousewheel'])) {
                            //debugger;
                            if (s.items[0].automatedScrolling) sa.sp.initAutomatedScrolling(scrollpane, 'vertical', true);
                            if (s.items[1].automatedScrolling) sa.sp.initAutomatedScrolling(scrollpane, 'horizontal', true);
                        //}
                    }));
                }
				/*
				jQuery('img, a, p, div, span', container).each (function (idx, el) {
					sa.m.addEventListener (this, 'mouseover', function (){
						sa.sp.initAutomatedScrolling(scrollpane, 'vertical', false);
						sa.sp.initAutomatedScrolling(scrollpane, 'horizontal', false);
					});
					sa.m.addEventListener (this, 'mouseout', function (){
						if (s.items[0].automatedScrolling) sa.sp.initAutomatedScrolling(scrollpane, 'vertical', true);
						if (s.items[1].automatedScrolling) sa.sp.initAutomatedScrolling(scrollpane, 'horizontal', true);
					});
				});
				*/
				//if (scrollpane) {
					//debugger;
					/*if (scrollpaneDiv) {
                        sa.vcc.componentFullyInitialized (s.initID, scrollpaneDiv.id+'__dialogCreated');
                    } else {
                        sa.vcc.componentFullyInitialized (s.initID, scrollpaneDiv.id);		
                    }*/
                    sa.vcc.componentFullyInitialized (s.initID, scrollpaneDiv.id);		
				/*} else {
					sa.vcc.componentFullyInitialized (s.initID, scrollpaneDiv.id);		
				}*/
				
		//}, 100); // setTimeout (..., 1000) -- allow for DOM update
		//}
		
		
		/*
		sa.m.waitForCondition ('scrollpane '+scrollpaneDiv.id+' initialization containerSizeChanged', function () {
            return sa.m.settings.initialized.site && !sa.desktop.settings.animating
                && (jQuery('iframe',scrollpaneDiv).length===0  
                    || (
                        jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0]
                        && jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].innerHTML !==''
                    )
                )
        }, function () {
            sa.sp.containerSizeChanged (scrollpaneDiv, true);
        }, 200);
        */
        sa.sp.containerSizeChanged (scrollpaneDiv, true);
	},
	
	scrollCmds_hasAnyOfTypes : function (s, types) {
		for (var i=0; i<s.items.length; i++) {
			var 
			item = s.items[i];
			
			for (var j=0; j<item.scrollCmds.length; j++) {
				var sc = item.scrollCmds[j];
				for (var k=0; k<types.length; k++) {
					if (sc.type===types[k]) return true;
				}
			}
		};
		return false;
	},
	
	renderScrollbarHorizontal : function (scrollpane) {
		var
		baseID = scrollpane.id.replace('__container', '').replace('__scrollpane', '').replace('__dialogCreated','').replace('__partOfAwhole').replace('__dialog', ''),
		dialog = jQuery('#'+baseID+'__dialog')[0],
		cihe = jQuery('#'+baseID)[0],
		contentDimensions = jQuery('#'+baseID+'__contentDimensions')[0],
		scrollpaneContainer = jQuery('#'+baseID+'__scrollpane__container')[0],
		scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__partOfAwhole')[0];
		if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__dialogCreated')[0];
		if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane')[0];
		if (!scrollpaneDiv) debugger;
		var
		css3 = jQuery('#'+baseID+'__CSS3');
		
		if (!scrollpane || !scrollpaneContainer || !scrollpane) return false;

		var s = sa.vcc.settings[scrollpaneDiv.id];
        if (!s) debugger;
		
		var
		t = sa.vcc.getVividTheme (s.themeName),
		item =  s.items[1];

		if (!item) return false; // is dialog, not scrollpane
		

		
		if (item.sliderbar) {
			//if (item.sliderbar.style.display!='block') {
				if (
					jQuery(item.sliderbar).css('display')==='none' 
					|| parseFloat(jQuery(item.sliderbar).css('opacity'))==0
					|| parseFloat(jQuery(item.sliderbar).css('opacity'))==1
				) { 
					if (jQuery(item.sliderbar).css('display')==='none') jQuery(item.sliderbar).css({display:'block'});
					if (parseFloat(jQuery(item.sliderbar).css('opacity'))==0) sa.vcc.fadeIn (item.sliderbar.id, 800);
					
					item.sliderbarShown = true;
					
					// !!! done in onresize_do2() !!!
					//item.sliderbar.style.left = scrollpane.offsetWidth + 'px';
					
					/*
					setTimeout (function() {
						if (item.sliderbar.offsetLeft==0) {
							debugger;
							item.scrollpane.style.left = '25px';
						} else {
							item.scrollpane.style.left = '0px';
						};
						item.sliderbarShown = true;
					}, 500);*/
				}

				//if (baseID=='mp3s') debugger;
				if (contentDimensions) {
					s.items[0].sliderbar.style.height = '100%';
					s.items[1].sliderbar.style.width = '100%';
					
					jQuery('#'+scrollpane.id+'__sliderBottom').css ({
						top : (jQuery('#'+scrollpane.id)[0].offsetHeight - 20) + 'px'
					});
					
					if (
						jQuery('#'+s.items[1].sliderbar.id).length==1
						&& s.items[1].scrollpaneContainer.childNodes[0]!==jQuery('#'+s.items[1].sliderbar.id)[0]
					) {
						jQuery(s.items[1].scrollpaneContainer).prepend (
							jQuery('#'+s.items[1].sliderbar.id)[0].parentNode.removeChild(jQuery('#'+s.items[1].sliderbar.id)[0])
						);
					}
				}
				
                jQuery(scrollpane).css ({
                    height : jQuery(scrollpaneContainer)[0].offsetHeight - 20
                });
				
				
			//}
		} else {
			/*
			var scrollpaneDiv = document.createElement('DIV');
			scrollpaneDiv.id = baseID+'__scrollpane';
			var originalDiv = jQuery('#'+baseID)[0];
			originalDiv.parentNode.appendChild (scrollpaneDiv);
			scrollpaneDiv.appendChild(originalDiv.parentNode.removeChild (originalDiv));
			*/

			var
			baseID = scrollpane.id.replace('__container', '').replace('__scrollpane', '').replace('__dialogCreated','').replace('__partOfAwhole').replace('__dialog', ''),
			dialog = jQuery('#'+baseID+'__dialog')[0],
			cihe = jQuery('#'+baseID)[0],
			contentDimensions = jQuery('#'+baseID+'__contentDimensions')[0],
			scrollpaneContainer = jQuery('#'+baseID+'__scrollpane__container')[0],
			scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__partOfAwhole')[0];
			if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__dialogCreated')[0];
			if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane')[0];
			var
			css3 = jQuery('#'+baseID+'__CSS3'),
			s = sa.vcc.settings[baseID+'__scrollpane'];
			//if (!s) s = sa.vcc.settings[baseID];
			if (!s) debugger;

			
			var sliderbar = document.createElement ('DIV');
			sliderbar.item = item;
			sliderbar.style.zIndex = 500 * 1000 * 1000;
			sliderbar.id = scrollpane.id+'__sliderbar__hor';
			sliderbar.className = 'vsp_sliderbar';
			sliderbar.style.display = 'none';
			sliderbar.style.opacity = 0;
			sliderbar.style.filter = 'alpha(opacity=0)';
			sliderbar.style.position = 'sticky';
			sliderbar.style.height = s.theme.images.hor_sliderbar.height + 'px';
			sliderbar.style.width = '100%';
			sliderbar.style.background = 'url('+s.theme.baseURL+'/'+s.theme.images.hor_sliderbar.url+') no-repeat';
			sliderbar.style.backgroundPosition = '0px 0px';
			sliderbar.style.backgroundSize = '100% 100%';
			var sliderTop = document.createElement ('DIV'); //could be called sliderLeft, but since left goes to the top of the page and this simplifies the code...		
			sliderTop.id = scrollpane.id+'__sliderTopHor';
			sliderTop.item = item;
			sliderTop.className = 'vsp_sliderTop';
			sliderTop.style.position = 'absolute';
			sliderTop.style.width = '20px';
			sliderTop.style.height = '20px';
			sliderTop.style.background = 'url('+s.theme.baseURL+'/'+s.theme.images.hor_sliderTop.url+') no-repeat';
			sliderTop.style.zIndex = 700 * 1000 * 1000;
			var sliderBottom = document.createElement ('DIV'); //right side button, goes to bottom.
			sliderBottom.id = scrollpane.id+'__sliderBottomHor';

			sliderBottom.item = item;
			sliderBottom.className = 'vsp_sliderBottom';
			sliderBottom.style.position = 'absolute';
			sliderBottom.style.width = '20px';
			sliderBottom.style.height = '20px';
			sliderBottom.style.background = 'url('+s.theme.baseURL+'/'+s.theme.images.hor_sliderBottom.url+') no-repeat';
			sliderBottom.style.zIndex = 700 * 1000 * 1000;
			var slider = document.createElement ('DIV');
			slider.id = scrollpane.id+'__horizontal';
			slider.className = 'vsp_slider';
			slider.style.position = 'absolute';
			slider.style.width = '40px';
			slider.style.height = '15px';
			slider.style.top = '3px';
			slider.style.left = '20px';
			slider.style.background = 'url('+s.theme.baseURL+'/'+s.theme.images.hor_slider.url+') no-repeat';
			slider.style.backgroundSize = '100% 100%';
			slider.style.zIndex = 5.5 * 100 * 1000 * 1000;

			if (jQuery('#'+sliderTop.id).length==0) sliderbar.appendChild (sliderTop);
			if (jQuery('#'+slider.id).length==0) sliderbar.appendChild (slider);
			if (jQuery('#'+sliderBottom.id).length==0) sliderbar.appendChild (sliderBottom);
			/*if (scrollpaneDiv && jQuery('#'+scrollpaneDiv.id, scrollpaneContainer).length==0) scrollpaneContainer.appendChild (scrollpaneDiv);
			if (jQuery('#'+sliderbar.id).length==0) scrollpaneContainer.insertBefore (sliderbar, scrollpaneDiv);*/
            if (jQuery('#'+sliderbar.id).length==0) jQuery(scrollpaneContainer).append (sliderbar);
            jQuery(scrollpane).css ({
                height : jQuery(scrollpaneContainer)[0].offsetHeight - 20
            });
			
			sa.m.hookEvent (scrollpaneContainer, 'mousedown', sa.sp._disableDrag, true, true);
			sa.m.hookEvent (sliderbar, 'click', sa.sp._click, true, true);
			sa.m.hookEvent (sliderTop, 'click', sa.sp._click, true, true);
			sa.m.hookEvent (slider, 'mousedown', sa.sp._mousedown, true, true);
			sa.m.hookEvent (slider, 'mouseup', sa.sp._mouseup, true, true);
			sa.m.hookEvent (slider, 'mouseout', sa.sp._mouseout, true, true);
			sa.m.hookEvent (sliderBottom, 'click', sa.sp._click, true, true);
			
			item.sliderbar = sliderbar;
			item.sliderTop = sliderTop;
			item.slider = slider;
			item.sliderBottom = sliderBottom;
			//sa.vcc.fadeIn (item.sliderbar.id, 800);
		};
	},
    
    bindEvents : function (scrollpane) {
		var
		baseID = scrollpane.id.replace('__container', '').replace('__scrollpane', '').replace('__dialogCreated','').replace('__partOfAwhole').replace('__dialog', ''),
		dialog = jQuery('#'+baseID+'__dialog')[0],
		cihe = jQuery('#'+baseID)[0],
		contentDimensions = jQuery('#'+baseID+'__contentDimensions')[0],
		scrollpaneContainer = jQuery('#'+baseID+'__scrollpane__container')[0],
		scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__partOfAwhole')[0];
		if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__dialogCreated')[0];
		if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane')[0];
		if (!scrollpaneDiv) debugger;
		var
		css3 = jQuery('#'+baseID+'__CSS3');

		if (!scrollpane || !scrollpaneContainer || !scrollpane) return false;

		/*if (cihe) {
			var s1 = sa.vcc.settings[cihe.id];
		} else {*/
			var s = sa.vcc.settings[scrollpaneDiv.id];
            if (!s) debugger;
		//}
		//if (!s && scrollpaneDiv) s = sa.vcc.settings[scrollpaneDiv.id+'__dialogCreated'];
		//if (!s && scrollpaneDiv) s = sa.vcc.settings[scrollpaneDiv.id];
		if (!s) return false;
        
        jQuery(scrollpane).off();
		
                var cw = jQuery('iframe', scrollpane)[0].contentWindow;
                var sp = jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
        jQuery(sp).off();
                
                cw.scrollTo(1,0);
                cw.scrollTo(0,0);
				sa.m.addEventListener (sp, 'touchstart', function (evt) {
                    //evt.preventDefault();
                    //sa.statusbar.update ('touchstart');
					sa.sp.settings.touchDeviceScroll[scrollpane.id] = {
						s : s, 
						lastClientX : evt.touches[0].screenX,
						lastClientY : evt.touches[0].screenY
					};
					s.items[0].scrollCmds = [];
                    s.items[1].scrollCmds = [];
					clearTimeout (s.items[0].animatedScrollTimeout);
                    clearTimeout (s.items[1].animatedScrollTimeout);
					delete s.items[0].animatedScrollTimeout;
                    delete s.items[1].animatedScrollTimeout;
					
					sa.m.addEventListener (sp, 'touchmove', function(evt) {
                        sa.sp.touchDeviceScroll (evt, scrollpane);
                    });
				});
				sa.m.addEventListener (sp, 'touchend', function (evt){
					sa.m.removeEventListener (sp, 'touchmove', sa.sp.touchDeviceScroll);
					delete sa.sp.settings.touchDeviceScroll[scrollpane.id];
				});
            
				sa.m.hookScrollwheel (sp, function (e,two,three) {
                    var x = jQuery.extend ({}, e);
                    x.target = scrollpane;//jQuery('#jQueryFileUpload__scrollpane', window.top.document.body)[0];
                    window.top.sa.sp._mousewheel (x,two,three);
                    e.preventDefault();
                }, false, true);
        
    },
	
	renderScrollbarVertical : function (scrollpane) {		
		var
		baseID = scrollpane.id.replace('__container', '').replace('__scrollpane', '').replace('__dialogCreated','').replace('__partOfAwhole').replace('__dialog', ''),
		dialog = jQuery('#'+baseID+'__dialog')[0],
		cihe = jQuery('#'+baseID)[0],
		contentDimensions = jQuery('#'+baseID+'__contentDimensions')[0],
		scrollpaneContainer = jQuery('#'+baseID+'__scrollpane__container')[0],
		scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__partOfAwhole')[0];
		if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__dialogCreated')[0];
		if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane')[0];
		if (!scrollpaneDiv) debugger;
		var
		css3 = jQuery('#'+baseID+'__CSS3');

		if (!scrollpane || !scrollpaneContainer || !scrollpane) return false;

		/*if (cihe) {
			var s1 = sa.vcc.settings[cihe.id];
		} else {*/
			var s = sa.vcc.settings[scrollpaneDiv.id];
            if (!s) debugger;
		//}
		//if (!s && scrollpaneDiv) s = sa.vcc.settings[scrollpaneDiv.id+'__dialogCreated'];
		//if (!s && scrollpaneDiv) s = sa.vcc.settings[scrollpaneDiv.id];
		if (!s) return false;
		
		var
		t = sa.vcc.getVividTheme (s.themeName),
		item =  s.items[0];
		
		if (!item) {debugger; return false}; // is dialog, not scrollpane
		
		if (item.sliderbar) {
			//if (item.sliderbar.style.display!='block') {
				//if (baseID=='mp3s') debugger;
				if (
					jQuery('#'+item.sliderbar.id).css('display')==='none' 
					|| parseFloat(jQuery('#'+item.sliderbar.id).css('opacity'))==0
					|| parseFloat(jQuery('#'+item.sliderbar.id).css('opacity'))==1
				) { 
					if (jQuery('#'+item.sliderbar.id).css('display')==='none') jQuery(item.sliderbar).css({display:'block'});
					if (parseFloat(jQuery('#'+item.sliderbar.id).css('opacity'))==0) sa.vcc.fadeIn (item.sliderbar.id, 800);
					
					item.sliderbarShown = true;
					
					// !!! done in onresize_do2() !!!
					//item.sliderbar.style.left = scrollpane.offsetWidth + 'px';
					
					/*
					setTimeout (function() {
						if (item.sliderbar.offsetLeft==0) {
							debugger;
							item.scrollpane.style.left = '25px';
						} else {
							item.scrollpane.style.left = '0px';
						};
						item.sliderbarShown = true;
					}, 500);*/
				}

				//if (baseID=='siteYoutubeSearch') debugger;
				/*
				if (contentDimensions) {
					item.sliderbar.style.height = '100%';

					//setTimeout (function () {
						if (item.scrollpaneContainer.childNodes[0]!==jQuery('#'+item.sliderbar.id)[0]) {
							jQuery(item.scrollpaneContainer).prepend (
								jQuery('#'+item.sliderbar.id)[0].parentNode.removeChild(jQuery('#'+item.sliderbar.id)[0])
							);
						}
					//}, 20);

					//setTimeout (function () {
                        
                        //if (scrollpane.id=='saApp_search_youtube_thumbViewer') debugger;
						if (!t.options || t.options.ver_sliderbar_position!='left') {
							if (jQuery('#'+baseID+'__contentDimensions')[0]) {
								var left = jQuery('#'+baseID+'__contentDimensions')[0].offsetWidth - 150;
							} else {
								var left = jQuery('#'+scrollpane.id+'__container')[0].offsetWidth - 25;
							}
						} else {
                            var left = 0;//jQuery('#'+baseID+'__contentDimensions')[0].offsetLeft;
                        };
                        
                        jQuery('#'+baseID+'__scrollpane__sliderbar__ver').css ({
                            left : left
                        });
                        jQuery('#'+baseID+'__sliderbar__ver').css ({
                            left : left
                        });
                        
                        /*
						if (t.options && t.options.ver_sliderbar_position=='left') {
                            var 
                            scrollpaneEl = jQuery('#'+baseID+'__scrollpane')[0],
                            left = 5;
                            jQuery(scrollpaneEl).css ({ left : left, width : scrollpaneEl.initialWidth + jQuery('#'+baseID+'__scrollpane__sliderbar__ver').width() + left });
                            //jQuery('#'+baseID).css ({ left : 0, height : jQuery('#'+baseID+'__contentDimensions')[0].offsetHeight });
                            //jQuery('#'+baseID).css ({ left : 0, height : 'auto' });
                        } else {                      
                            jQuery(scrollpaneDiv).css ({ left : 0, height : jQuery('#'+baseID+'__contentDimensions')[0].offsetHeight });
                            //jQuery('#'+baseID).css ({ left : 0, height : jQuery('#'+baseID+'__contentDimensions')[0].offsetHeight });
                            //jQuery('#'+baseID).css ({ left : 0, height : 'auto' });
                        }* /
                        
						jQuery('#'+baseID+'__scrollpane__sliderBottom').css ({
							top : (jQuery('#'+scrollpaneContainer.id)[0].offsetHeight-20) + 'px'
						});
						jQuery('#'+baseID+'__sliderBottom').css ({
							top : (jQuery('#'+scrollpaneContainer.id)[0].offsetHeight-20) + 'px'
						});
					//}, 200);
					 
				}
				*/
			//}
		} else {
			/*
			var scrollpaneDiv = document.createElement('DIV');
			scrollpaneDiv.id = baseID+'__scrollpane';
			var originalDiv = jQuery('#'+baseID)[0];
			originalDiv.parentNode.appendChild (scrollpaneDiv);
			scrollpaneDiv.appendChild(originalDiv.parentNode.removeChild (originalDiv));
			*/

			var
			baseID = scrollpane.id.replace('__container', '').replace('__scrollpane', '').replace('__dialogCreated','').replace('__partOfAwhole').replace('__dialog', ''),
			dialog = jQuery('#'+baseID+'__dialog')[0],
			cihe = jQuery('#'+baseID)[0],
			contentDimensions = jQuery('#'+baseID+'__contentDimensions')[0],
			scrollpaneContainer = jQuery('#'+baseID+'__scrollpane__container')[0],
			scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__partOfAwhole')[0];
			if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__dialogCreated')[0];
			if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane')[0];
			var
			css3 = jQuery('#'+baseID+'__CSS3'),
			s = sa.vcc.settings[baseID+'__scrollpane'];
			if (!s) s = sa.vcc.settings[baseID];
			if (!s) debugger;
			
			var sliderbar = document.createElement ('DIV');
			sliderbar.item = item;
			sliderbar.id = scrollpane.id+'__sliderbar__ver';
			sliderbar.className = 'vsp_sliderbar';
			sliderbar.style.position = 'sticky';
			sliderbar.style.zIndex = 500 * 1000 * 1000;
			sliderbar.style.display = 'none';
			sliderbar.style.opacity = 0;
			sliderbar.style.filter = 'alpha(opacity=100)';
			/*
            */

            if (t.options && t.options.ver_sliderbar_position=='left') {
                sliderbar.style.position = 'absolute';
                sliderbar.style.left = (
                    (t.options && t.options.ver_sliderbar_position=='left')
                    ? 0
                    : scrollpaneContainer.offsetWidth + 5
                )+'px';
            } else {
                sliderbar.style.position = 'sticky';
                sliderbar.style.left = '';
                sliderbar.style.right = '';
                sliderbar.style.cssFloat = 'right';
                //sliderbar.style.right = (s.theme.images.ver_sliderbar.width+5)+'px';
            }
            
			sliderbar.style.width = s.theme.images.ver_sliderbar.width+'px';
			sliderbar.style.height = scrollpaneContainer.offsetHeight+'px';
			sliderbar.style.background = 'url('+s.theme.baseURL+'/'+s.theme.images.ver_sliderbar.url+') no-repeat';
			sliderbar.style.backgroundPosition = '0px 0px';
			sliderbar.style.backgroundSize = '100% 100%';
            
            //if (t.options && t.options.ver_sliderbar_position=='left') debugger;
            jQuery(scrollpaneDiv).css ({
                left : (
                    (t.options && t.options.ver_sliderbar_position=='left')
                    ? s.theme.images.ver_sliderbar.width + 5
                    : 0 // scrollpaneContainer.offsetWidth ????
                )
            });
			
			var sliderTop = document.createElement ('DIV');
			sliderTop.id = scrollpane.id+'__sliderTop';
			sliderTop.item = item;
			sliderTop.className = 'vsp_sliderTop';
			sliderTop.style.position = 'absolute';
			sliderTop.style.width = '20px';
			sliderTop.style.height = '20px';
			sliderTop.style.background = 'url('+s.theme.baseURL+'/'+s.theme.images.ver_sliderTop.url+') no-repeat';
			sliderTop.style.zIndex = 700 * 1000 * 1000;
			var sliderBottom = document.createElement ('DIV');
			sliderBottom.id = scrollpane.id+'__sliderBottom';
			sliderBottom.item = item;
			sliderBottom.className = 'vsp_sliderBottom';
			sliderBottom.style.position = 'absolute';
			sliderBottom.style.width = '20px';
			sliderBottom.style.height = '20px';
			sliderBottom.style.background = 'url('+s.theme.baseURL+'/'+s.theme.images.ver_sliderBottom.url+') no-repeat';
			sliderBottom.style.zIndex = 700 * 1000 * 1000;
			var slider = document.createElement ('DIV');
			slider.id = scrollpane.id+'__vertical';
			slider.className = 'vsp_slider';
			slider.style.position = 'absolute';
			slider.style.width = '15px';
			slider.style.height = '40px';
			slider.style.left = '2px';
			slider.style.top = '20px';
			slider.style.background = 'url('+s.theme.baseURL+'/'+s.theme.images.ver_slider.url+') no-repeat';
			slider.style.backgroundSize = '100% 100%';
			slider.style.zIndex = 5.5 * 100 * 1000 * 1000;
	
			if (jQuery('#'+sliderTop.id).length==0) sliderbar.appendChild (sliderTop);
			if (jQuery('#'+slider.id).length==0) sliderbar.appendChild (slider);
			if (jQuery('#'+sliderBottom.id).length==0) sliderbar.appendChild (sliderBottom);
			
			if (jQuery('#'+sliderbar.id).length==0) jQuery(scrollpaneContainer).prepend (sliderbar);

			sliderBottom.style.top = (scrollpaneContainer.offsetHeight - 20) + 'px';
            
            if (s.containsIframe) {
                s.cw.scrollTo(1,0);
                s.cw.scrollTo(0,0);
				sa.m.addEventListener (s.sp, 'touchstart', function (evt) {
                    //evt.preventDefault();
                    //sa.statusbar.update ('touchstart');
					sa.sp.settings.touchDeviceScroll[item.scrollpane.id] = {
						item : item, 
						lastClientX : evt.touches[0].screenX,
						lastClientY : evt.touches[0].screenY
					};
					item.scrollCmds = [];
					clearTimeout (item.animatedScrollTimeout);
					delete item.animatedScrollTimeout;
					
					sa.m.addEventListener (s.sp, 'touchmove', function(evt) {
                        sa.sp.touchDeviceScroll (evt, scrollpane);
                    });
				});
				sa.m.addEventListener (s.sp, 'touchend', function (evt){
					sa.m.removeEventListener (s.sp, 'touchmove', sa.sp.touchDeviceScroll);
					delete sa.sp.settings.touchDeviceScroll[item.scrollpane.id];
				});
				sa.m.hookScrollwheel (s.cw, sa.sp._mousewheel, false, true);
            } else {
            
			//if (
			//	true // touchstart is only enabled on phones, and in desktop view on a phone, 
                        // the user-agent is spoofed by the phone to make it appear like a desktop
                        // machine.
				/*sa.m.userDevice.isPhone
				|| sa.m.userDevice.isPad*/
			//) {
                if (typeof item.scrollpaneContainer.scrollTo=='function') {
                    item.scrollpaneContainer.scrollTo(1,0);
                    item.scrollpaneContainer.scrollTo(0,0);
                }
				sa.m.addEventListener (item.scrollpaneContainer, 'touchstart', function (evt) {
                    //evt.preventDefault();
                    //sa.statusbar.update ('touchstart');
					sa.sp.settings.touchDeviceScroll[item.scrollpane.id] = {
						item : item, 
						lastClientX : evt.touches[0].screenX,
						lastClientY : evt.touches[0].screenY
					};
					item.scrollCmds = [];
					clearTimeout (item.animatedScrollTimeout);
					delete item.animatedScrollTimeout;
					
					sa.m.addEventListener (item.scrollpaneContainer, 'touchmove', sa.sp.touchDeviceScroll);
				});
				sa.m.addEventListener (item.scrollpaneContainer, 'touchend', function (evt){
					sa.m.removeEventListener (item.scrollpaneContainer, 'touchmove', sa.sp.touchDeviceScroll);
					delete sa.sp.settings.touchDeviceScroll[item.scrollpane.id];
				});
			//} else {
                // hook both!
				sa.m.hookScrollwheel (scrollpaneContainer, sa.sp._mousewheel, false, true);
			//}
            }
            
            
			sa.m.hookEvent (scrollpaneContainer, 'mousedown', sa.sp._disableDrag, true, true);
			sa.m.hookEvent (sliderbar, 'click', sa.sp._click, true, true);
			sa.m.hookEvent (sliderTop, 'click', sa.sp._click, true, true);
			sa.m.hookEvent (slider, 'mousedown', sa.sp._mousedown, true, true);
			sa.m.hookEvent (slider, 'mouseup', sa.sp._mouseup, true, true);
			sa.m.hookEvent (slider, 'mouseout', sa.sp._mouseout, true, true);
			sa.m.hookEvent (sliderBottom, 'click', sa.sp._click, true, true);
			
			item.sliderbar = sliderbar;
			item.sliderTop = sliderTop;
			item.slider = slider;
			item.sliderBottom = sliderBottom;
			//item.scrollpane.style.height = 'auto';

			//sa.vcc.fadeIn (item.sliderbar.id, 800);
		};
	},
	
	touchDeviceScroll : function (evt,scrollpane) {
		/*var s = sa.vcc.settings[scrollpane.id+'__dialogCreated'];
		if (!s) s = sa.vcc.settings[scrollpane.id];
		if (!s || s.items.length!=2) s = sa.vcc.settings[scrollpane.id.replace('__scrollpane','')];
        */
        //sa.statusbar.update ('touchDeviceScroll');
        
        if (!scrollpane) {
            var 
            scrollpane = document.getElementById(evt.currentTarget.id.replace('__container','')),
            scrollpane = (
                scrollpane && sa.sp.settings.touchDeviceScroll[scrollpane.id]
                ? scrollpane
                : document.getElementById(evt.currentTarget.id.replace('__scrollpane__container',''))
            );
        };
        
        var
		offsetX = Math.round((
			sa.sp.settings.touchDeviceScroll[scrollpane.id].lastClientX
			- evt.touches[0].clientX
		)),
		offsetY = Math.round((
			sa.sp.settings.touchDeviceScroll[scrollpane.id].lastClientY
			- evt.touches[0].screenY
			
		)),
		s = sa.sp.settings.touchDeviceScroll[scrollpane.id].s;
        
        
        if (s && s.containsIframe) {
            offsetY = Math.round(offsetY/2);
            offsetX = Math.roudn(offsetX/2);
        }
            
        sa.sp.settings.touchDeviceScroll[scrollpane.id].lastClientX = evt.touches[0].screenX;
        sa.sp.settings.touchDeviceScroll[scrollpane.id].lastClientY = evt.touches[0].screenY;
        
        debugger;
        /*
        if (offsetX > (jQuery(scrollpane).width()/2)) {
            offsetX = jQuery(scrollpane).width()/2;
        };
        if (offsetY > (jQuery(scrollpane).height()/2)) {
            offsetY = jQuery(scrollpane).height()/2;
        };
        */
        
        
        //console.log (evt.touches[0].screenY + ' - ' + sa.sp.settings.touchDeviceScroll[scrollpane.id].lastClientY + ' = ' + offsetY);
        
		//scrollpane = document.getElementById(evt.currentTarget.id.replace('__container',''));
		
        //sa.statusbar.update (offsetY, false);
        
		//item.scrollCmds = [];
		//clearTimeout (item.animatedScrollTimeout);
		//delete item.animatedScrollTimeout;

		//sa.sp.settings.touchDeviceScroll[scrollpane.id].lastClientY = evt.touches[0].clientY;
		//sa.sp.settings.touchDeviceScroll[scrollpane.id].lastClientX = evt.touches[0].clientX;
        if (s && s.containsIframe) {
            debugger;
            var cw = jQuery('iframe', scrollpane)[0].contentWindow;
            var sp = jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
            
            sa.sp.scrollToPos (scrollpane, cw.pageXOffset + (offsetX), cw.pageYOffset+ (offsetY));
        } else {
            sa.sp.scrollToPos (scrollpane, scrollpane.scrollLeft + (offsetX), scrollpane.scrollTop + (offsetY));
        }
	},
	
	hideScrollbarHorizontal : function (scrollpane) {
		var s = sa.vcc.settings[scrollpane.id+'__dialogCreated'];
		if (!s) s = sa.vcc.settings[scrollpane.id];
		if (!s || s.items.length!=2) s = sa.vcc.settings[scrollpane.id.replace('__scrollpane','')];
		
		var t = sa.vcc.getVividTheme (s.themeName); 
		if (s.items[1].sliderbarShown) sa.vcc.fadeOut(s.items[1].sliderbar.id, 400);
		s.items[1].sliderbarShown = false;
	},
	
	hideScrollbarVertical : function (scrollpane) {
		var s = sa.vcc.settings[scrollpane.id+'__dialogCreated'];
		if (!s) s = sa.vcc.settings[scrollpane.id];
		if (!s || s.items.length!=2) s = sa.vcc.settings[scrollpane.id.replace('__scrollpane','')];
		
		if (s.items[0].sliderbarShown) {
			sa.vcc.fadeOut(s.items[0].sliderbar.id, 800);
			s.items[0].scrollpane.style.left = '';
		}
		s.items[0].sliderbarShown = false;
	},
	
	containerSizeChanged : function (scrollpane, animateSlider, moveSlider, pos) {
		/*if (sa.sp.settings.timers[scrollpane.id]) clearTimeout(sa.sp.settings.timers[scrollpane.id]);
		if (scrollpane && scrollpane.id) sa.sp.settings.timers[scrollpane.id] = setTimeout(function () {
			sa.m.log (1, 't124', 'sa.sp.containerSizeChanged');
			sa.sp.onresize (scrollpane, animateSlider, moveSlider, pos);
			delete sa.sp.settings.timers[scrollpane.id];
		}, (
			sa.m.userDevice.isPhone 
			? 200
			: 200
		));*/
		sa.sp.onresize (scrollpane, animateSlider, moveSlider, pos);
	},
	
	onresize : function (scrollpane, animateSlider, moveSlider, pos) {
		//debugger;
		
		//if (sa.desktop.settings.animating) return false;
		/*
		if (
			sa.s.c.settings.booting
			|| sa.desktop.settings.animating
		) {
			sa.m.waitForCondition ('waiting for startup or animations to complete', function (scrollpane, animateSlider, moveSlider, pos) {
				var r = (
					!sa.s.c.settings.booting
					&& !sa.desktop.settings.animating
				);
				if (r) {
					scrollpane.style.opacity = 0.0001;
				} else {
					scrollpane.style.opacity = 1;
				};
				return r;
			}, function () {
				sa.sp.onresize_do (scrollpane, animateSlider, moveSlider, pos);
			}, 500);
			
		}*/

        //clearTimeout (sa.sp.settings.timeoutOnResize);
		//sa.sp.settings.timeoutOnResize = setTimeout (function () {
            sa.sp.onresize_do (scrollpane, animateSlider, moveSlider, pos);
        //}, 250);
        
		
		// 2017-07-18 (Tuesday) : dont get why this wont work properly (doesnt show scrollbars at all when using code listed below here)
		/*debugger;
		sa.m.waitForCondition ('sa.sp.onresize() --- framework and site fully initialized', function () {
			return (
				sa.m.settings.initialized.site === true
				//&& sa.s.c.settings.booting === false
			);
		}, function () {
			sa.sp.onresize_do (scrollpane, animateSlider, moveSlider, pos);
		}, 400);*/
	},
	
	onresize_do : function (scrollpane, animateSlider, moveSlider, pos) {
		//debugger;
		if (!scrollpane) return false;
		
		if (animateSlider===undefined) animateSlider = true;
		if (moveSlider===undefined) moveSlider = true;

		var s = sa.vcc.settings[scrollpane.id+'__dialogCreated'];
		if (!s) s = sa.vcc.settings[scrollpane.id];
		if (!s) return false;
		
        if (!sa.sp.settings.onresizeTimer) sa.sp.settings.onresizeTimer = {};
        
        clearTimeout(sa.sp.settings.onresizeTimer[scrollpane.id]);
        //sa.sp.settings.onresizeTimer[scrollpane.id] = setTimeout(function() {
            sa.sp.onresize_do2 (scrollpane, animateSlider, moveSlider, pos);
        //}, 50);
	},
	
	onresize_do2 : function (scrollpane, animateSlider, moveSlider, pos) {
		var
		baseID = scrollpane.id.replace('__container', '').replace('__scrollpane', '').replace('__dialogCreated','').replace('__partOfAwhole').replace('__dialog', ''),
		dialog = jQuery('#'+baseID+'__dialog')[0],
		cihe = jQuery('#'+baseID)[0],
		contentDimensions = jQuery('#'+baseID+'__contentDimensions')[0],
		scrollpaneContainer = 
            jQuery('#'+baseID+'__scrollpane__container')[0]
            || jQuery('#'+baseID+'__container')[0]
		,
		scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__partOfAwhole')[0];
		if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane__dialogCreated')[0];
		if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID+'__scrollpane')[0];
		if (!scrollpaneDiv) scrollpaneDiv = jQuery('#'+baseID)[0];
		var
		css3 = jQuery('#'+baseID+'__CSS3');

        
        if (contentDimensions) {
            scrollpaneContainer.style.width = contentDimensions.offsetWidth + 'px';
            scrollpaneContainer.style.height = contentDimensions.offsetHeight + 'px';
        } else {
            scrollpaneContainer.style.width = scrollpane.style.width;
            scrollpaneContainer.style.height = scrollpane.style.height;
        }
        
        
		
		//if (!contentDimensions) return false;
		
		//if (baseID == 'saApp_search_youtube_thumbViewer') debugger;

		//debugger;
		if (!scrollpane.id) return false;
		var s = sa.vcc.settings[scrollpane.id+'__dialogCreated'];
		if (!s) s = sa.vcc.settings[scrollpane.id];
		//if (!s || s.items.length!=2) s = sa.vcc.settings[cihe.id];
		if (!s) return false;
        
        s.containsIframe = jQuery('iframe',  scrollpaneDiv)[0];
        if (s.containsIframe) {
            s.iframe = jQuery('iframe', scrollpaneDiv)[0];
            s.containsIframe = s.iframe.src.match(sa.m.globals.urls.app);
            if (s.containsIframe) {
                s.cw = s.iframe.contentWindow;
                s.sp = s.iframe.contentWindow.document.getElementsByTagName('body')[0];
            }
        };
        
        
        
		s.pos = pos;
		var t = sa.vcc.getVividTheme (s.themeName); 
		if (!t) return false;
		if (!s || !s.items[0]) return false; // not initialized fully yes.theme..
		if (typeof animateSlider=='undefined') animateSlider = true;
		
		// reset CSS for all HTML elements involved
		
		//if (baseID=='siteAds') debugger;
		/*jQuery(scrollpane).css ({
		//	left : 0, // handled by sa.vcc.applyTheme instead, see it's cssToExtrapolate features
//			top : 0,
			width : contentDimensions.offsetWidth - s.items[0].sliderbar.offsetWidth - 5,
			height : 'auto',
			overflow : 'hidden'
		});*/
		var useContentDimensions = true;
		//if (cihe.id == 'saApp_search_youtube_thumbViewer') debugger;
        
		if (jQuery('.hm', jQuery('#'+baseID)).length>0) {		
            jQuery('#'+baseID).css({ height : '100%' });

            jQuery(scrollpaneContainer).css({
                //left : 0,
                //top : 0,
                //width : '100%',
                //height : '100%',
                width : scrollpaneContainer.parentNode.offsetWidth,
                height : scrollpaneContainer.parentNode.offsetHeight,
                zIndex : 11 * 1000 * 1000,
                overflow : 'hidden'
            });
            jQuery(scrollpane).css({
                //left : 0,
                top : 0,
                //width : '100%',
                width : scrollpaneContainer.offsetWidth - s.items[0].sliderbar.offsetWidth,
                height : scrollpaneContainer.offsetHeight,
                overflow : 'hidden'
                });
        } else {
            if (useContentDimensions) {
                if (contentDimensions) {
                    /*
                    jQuery(scrollpaneContainer).css({
                        //left : 0,
                        //top : 0,
                        //width : '100%',
                        //height : '100%',
                        width : contentDimensions.offsetWidth,
                        height : contentDimensions.offsetHeight,
                        zIndex : 11 * 1000 * 1000,
                        overflow : 'hidden'
                    });
                    if (s.items && s.items[0] && s.items[0].sliderbar) {
                        jQuery(scrollpane).css({
                            //left : 0,
                            top : 0,
                            //width : '100%',
                            width : scrollpaneContainer.offsetWidth - s.items[0].sliderbar.offsetWidth - 20,
                            height : scrollpaneContainer.offsetHeight,
                            overflow : 'hidden'
                        });
                    }*/
                } else {
                    /*
                    jQuery(scrollpaneContainer).css({
                        //left : 0,
                        //top : 0,
                        //width : '100%',
                        //height : '100%',
                        width : scrollpaneContainer.offsetWidth,
                        height : scrollpaneContainer.offsetHeight,
                        zIndex : 11 * 1000 * 1000,
                        overflow : 'hidden'
                    });
                    
                    if (s.items && s.items[0] && s.items[0].sliderbar) {
                        jQuery(scrollpane).css({
                            //left : 0,
                            top : 0,
                            //width : '100%',
                            width : scrollpaneContainer.offsetWidth - s.items[0].sliderbar.offsetWidth - 20,
                            height : scrollpaneContainer.offsetHeight,
                            overflow : 'hidden'
                        });
                    }*/
                }
            }
            //jQuery('#'+baseID).css({ height : 'auto' });
            /*
            scrollpane.style.width = (scrollpaneContainer.offsetWidth - 30) + 'px'; // disappearing scrollbars on newsApp if you do this
            if (scrollpane.id==='newsApp_content__scrollpane') setTimeout (function(){
                scrollpane.style.width = '';
            }, 400); // newsApp fix */
            
            if (scrollpane.id==='newsApp_content__scrollpane' || jQuery('#newsApp_content__scrollpane', scrollpane).length==1) {
                    scrollpane.style.width = '';
            } else if (scrollpane.id!=='siteAds__scrollpane') {
                scrollpane.style.width = (scrollpaneContainer.offsetWidth - 30) + 'px';
            }
        }
		
		jQuery(scrollpane).css({
			position : 'relative' // needed for Android 4.4
		});
		
		/*jQuery(cihe).css ({
			width : contentDimensions.offsetWidth - s.items[0].sliderbar.offsetWidth - 10,
		});*/

		var item = s.items[0];
		var c = item.scrollpaneContainer;
		if (!c) return false;
		sa.sp.log (1000100, 'resize(): now resizing id="'+c.id+'"');
		var ticketID = sa.m.showAllParents (c);

		item.scrollpaneContainer.style.zIndex = scrollpane.style.zIndex;
		
		if (scrollpane.className.match('animatedPartOf__dialog')) {
			/* RV1
			item.scrollpaneContainer.style.position = 'relative';
			item.scrollpaneContainer.style.top = '4%';
			item.scrollpaneContainer.style.left = '4%';
			item.scrollpaneContainer.style.width = '92%';//(item.scrollpaneContainer.parentNode.offsetWidth - 40) + 'px';
			item.scrollpaneContainer.style.height = '92%';//
			*/
			
			var h = item.scrollpaneContainer.parentNode.offsetHeight;
			h -= 30;
			var headerFound = false;
			for (var j=0; j<item.scrollpaneContainer.parentNode.children.length; j++) {
				var he = item.scrollpaneContainer.parentNode.children[j];
				if (he.className.match('animatedDialog__header')) {
					headerFound = true;
					h = h - he.offsetHeight - 40;
				}
			};
			if (headerFound) {
				item.scrollpaneContainer.style.height = h + 'px';
			};
			
			for (var j=0; j<scrollpane.children.length; j++) {
				var ch = scrollpane.children[j];
				//ch.style.width = '100%';
				//ch.style.height = '100%';
			};
			
			
			//scrollpane.style.width = '100%';
			//scrollpane.style.height = '100%';
			//scrollpane.style.overflow = 'hidden';
			//scrollpane.childNodes[0].style.width = '100%';
			//scrollpane.childNodes[0].style.height = '100%';
			//scrollpane.childNodes[0].style.top = '0px';
			//scrollpane.childNodes[0].style.left = '0px';
		} else {
			var w = c.offsetWidth;
			var h = c.offsetHeight;
			if (c.style.paddingLeft) w -= parseFloat(c.style.paddingLeft.replace('px',''));
			if (c.style.paddingRight) w -= parseFloat(c.style.paddingRight.replace('px',''));
			if (c.style.marginLeft) w -= parseFloat(c.style.marginLeft.replace('px',''));
			if (c.style.marginRight) w -= parseFloat(c.style.marginRight.replace('px',''));
			if (c.style.paddingTop) h -= parseFloat(c.style.paddingTop.replace('px',''));
			if (c.style.paddingBottom) h -= parseFloat(c.style.paddingBottom.replace('px',''));
			if (c.style.marginTop) h -= parseFloat(c.style.marginTop.replace('px',''));
			if (c.style.marginBottom) h -= parseFloat(c.style.marginBottom.replace('px',''));
			//if (c.style.left) w -= (2 * c.offsetLeft); // next 2 lines cause problems on dj site
			//if (c.style.top) h -= (2 * c.offsetTop);
			//scrollpane.style.width = w + 'px';
			//scrollpane.style.height = h + 'px';
		};
		
		//scrollpane.style.zIndex = 51000000;
		//scrollpane.style.width = 'inherit';
		//scrollpane.style.height = 'inherit';
		

		sa.m.log (2100, 'sa.vividScrollpane.resize(): scrollpane.id='+scrollpane.id+', scrollpane.style.height="'+scrollpane.style.height+'",  scrollpane.offsetHeight='+scrollpane.offsetHeight+', scrollpane.scrollHeight='+(s.containsIframe ? jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollHeight :scrollpane.scrollHeight)+', scrollpane.style.width='+scrollpane.style.width+', scrollpane.offsetWidth='+scrollpane.offsetWidth+', '+(s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth:scrollpane.scrollWidth)+'='+(s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth:scrollpane.scrollWidth));
		
		// --
		// --- onresize for HORIZONTAL SCROLLBAR 1of2
		// --
		if (
			(s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth : s.pos ? s.pos.width : (s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth:scrollpane.scrollWidth)) > scrollpaneContainer.offsetWidth + 60
			&& !scrollpane.className.match('animatedOptions__noXbar')
			&& scrollpane.offsetWidth > 100
		) {
			sa.sp.log (1000100, 'resize(): scrollpane.id=='+scrollpane.id+' now showing horizontal scrollbar for scrollpane.');
			sa.sp.renderScrollbarHorizontal (scrollpane);	
		} else 	{
			sa.sp.log (1000100, 'resize(): scrollpane.id=='+scrollpane.id+' now hiding horizontal scrollbar for scrollpane');
			sa.sp.hideScrollbarHorizontal (scrollpane);
		}

		
		// --
		// --- onresize for VERTICAL SCROLLBAR 1of2
		// --
		if (scrollpane.id=='newsApp_item_0__scrollpane') debugger;
		//if (cihe.id=='mp3s') debugger;
//        if (sa.m.settings.initialized.site && cihe.id == 'siteContent') debugger;
        if ((s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollHeight :scrollpane.scrollHeight) === 0) return false;
        if ((s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth:(s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth:scrollpane.scrollWidth))=== 0) return false;

        if (
            (s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollHeight : s.pos ? s.pos.height : scrollpane.scrollHeight) > scrollpaneContainer.offsetHeight + 60
			&& !scrollpane.className.match('animatedOptions__noYbar')
			&& scrollpane.offsetHeight > 100            
            
            /*
			scrollpane.scrollHeight > scrollpaneContainer.offsetHeight + 60
			&& !scrollpane.className.match('animatedOptions__noYbar')
            */
		) {
			sa.sp.log (1000100, 'resize(): scrollpane.id=='+scrollpane.id+' now showing vertical scrollbar for scrollpane');
			//sa.m.log (1, 't13.3 resize(): scrollpane.id='+scrollpane.id+', now showing vertical scrollbar');
			//jQuery(scrollpane).css({ width : scrollpaneContainer.offsetWidth - 20});
			//setTimeout (function () {
				//debugger;
				sa.sp.renderScrollbarVertical(scrollpane); 
			//}, 750);
		} else {
			sa.sp.log (1000100, 'resize(): scrollpane.id=='+scrollpane.id+' now hiding vertical scrollbar for scrollpane');
			//sa.m.log (1, 't13.3 resize(): scrollpane.id='+scrollpane.id+', now hiding vertical scrollbar');
			//jQuery(scrollpane).css({ width : scrollpaneContainer.offsetWidth});
			sa.sp.hideScrollbarVertical (scrollpane);
		};		
		/*
		if (
			(s.pos ? s.pos.height : cih.scrollHeight) > scrollpane.offsetHeight + 30
			&& !cih.className.match('animatedOptions__noYbar')
d			&& cih.offsetHeight > 100
		) {
			sa.sp.log (10, 'resize(): cih.id='+cih.id+', scrollpane.id='+scrollpane.id+' now showing vertical scrollbar for scrollpane');
			//sa.m.log (1, 't13.3 resize(): scrollpane.id='+scrollpane.id+', now showing vertical scrollbar');
			sa.sp.renderScrollbarVertical(scrollpane); 
		} else {
			sa.sp.log (10, 'resize(): cih.id='+cih.id+', scrollpane.id='+scrollpane.id+' now hiding vertical scrollbar for scrollpane');
			//sa.m.log (1, 't13.3 resize(): scrollpane.id='+scrollpane.id+', now hiding vertical scrollbar');
			sa.sp.hideScrollbarVertical (scrollpane);
		};*/

		// --
		// --- onresize for VERTICAL SCROLLBAR 2of2
		// --
		
		var item = s.items[0];
		//debugger;
		if (item.sliderbarShown) {		
			var offsetTop = offsetLeft = 0;
			if (jQuery(item.scrollpaneContainer).parent()[0].tagName.toUpperCase()=='TD') {
				if (
					jQuery(item.scrollpaneContainer).position().left===0
					|| jQuery(item.scrollpaneContainer).parent().hasClass('vividScrollpane__offsetLeftZero')
				) {
					var offsetLeft = 0;//jQuery(item.scrollpaneContainer).parents().closest('table').position().left;
				} else {
					var offsetLeft = jQuery(item.scrollpaneContainer).position().left - jQuery(item.scrollpaneContainer).parents().closest('table').position().left;
				}
				if (jQuery(item.scrollpaneContainer).position().top===0) {
					var offsetTop = 0;//jQuery(item.scrollpaneContainer).parents().closest('table').position().top;
				} else {
					var offsetTop = jQuery(item.scrollpaneContainer).position().top - jQuery(item.scrollpaneContainer).parents().closest('table').position().top;
				}
				
				jQuery(item.sliderbar).css({left : offsetLeft, top : offsetTop });
			} else {
				sa.sp.log (1000100, 'resize(): item.sliderbarShown==true, now showing vertical scrollbar for scrollpane');
				//sa.m.log (1, 't13.3 resize(): scrollpane.id='+scrollpane.id+', now showing vertical scrollbar');
				sa.sp.renderScrollbarVertical(scrollpane); 
				
			}
			
			//if (item.scrollpane.id==='saAgenda_items') debugger;
			//if (baseID=='mp3s') debugger;
            //if (scrollpane.id=='siteAds__scrollpane') debugger;
            //if (cihe.id == 'newsApp_content__scrollpane') debugger;
			var s1 = sa.vcc.settings[scrollpane.id];
			if (s1.theme.options && s1.theme.options.ver_sliderbar_position=='left') {
				item.sliderbar.style.left = '0px';//(item.scrollpaneContainer.style.paddingLeft ? item.scrollpane.style.paddingLeft : '0px');
				item.sliderbar.style.cssFloat = '';
                item.sliderbar.style.position = 'absolute';
                item.scrollpane.style.position = 'absolute';
				item.scrollpane.style.left = (offsetLeft + s1.theme.images.ver_sliderbar.width + 5 ) + 'px';
				//item.scrollpane.style.width = (item.scrollpaneContainer.offsetWidth - item.sliderbar.offsetWidth) + 'px';
			} else if (item.scrollpane.style.position=='absolute') {
				//item.sliderbar.style.display = 'block';
				//item.sliderbar.style.left = (offsetLeft + item.scrollpaneContainer.offsetWidth - ( item.sliderbar.offsetWidth ) ) + 'px';
                //item.sliderbar.style.left = (offsetLeft + item.scrollpaneContainer.offsetWidth - ( item.sliderbar.offsetWidth ) - 20 ) + 'px';
				//item.scrollpane.style.width = (item.scrollpaneContainer.offsetWidth - 35 - item.sliderbar.offsetWidth) + 'px';
			} else {
				jQuery('#'+item.sliderbar.id).css({
					//position:'absolute'
					//left : (offsetLeft + item.scrollpaneContainer.offsetWidth - ( item.sliderbar.offsetWidth ) )
				});
                /*
				jQuery('#'+item.cihe.id).css({
					width : (item.scrollpaneContainer.offsetWidth - 25 - item.sliderbar.offsetWidth)
				});*/
			}
			//item.scrollpane.style.width = (item.scrollpaneContainer.offsetWidth - item.sliderbar.offsetWidth - 10) + 'px';

			if (item.scrollpane.className.indexOf('noSliderResize')===-1) {
                if (s.items[1].sliderbarShown) {
                    if (item.scrollpane.theme && item.scrollpane.theme.saVividThemeType == 'canvasDialog') {
                        item.sliderbar.style.height = (item.scrollpaneContainer.offsetHeight - s.items[1].sliderbar.offsetHeight) + 'px';
                        item.sliderBottom.style.top = (item.scrollpaneContainer.offsetHeight - s.items[1].sliderbar.offsetHeight - item.sliderBottom.offsetHeight) + 'px';
                    } else {
                        item.sliderbar.style.height = (item.scrollpaneContainer.offsetHeight - s.items[1].sliderbar.offsetHeight) + 'px';
                        item.sliderBottom.style.top = (item.scrollpaneContainer.offsetHeight - s.items[1].sliderbar.offsetHeight - item.sliderBottom.offsetHeight) + 'px';
                    }
                } else {
                    if (item.contentDimensions) {
                        item.sliderbar.style.height = (item.contentDimensions.offsetHeight) + 'px';
                        item.sliderBottom.style.top = (item.contentDimensions.offsetHeight - item.sliderBottom.offsetHeight) + 'px';
                    } else {
                        item.sliderbar.style.height = (item.scrollpaneContainer.offsetHeight) + 'px';
                        item.sliderBottom.style.top = (item.scrollpaneContainer.offsetHeight - item.sliderBottom.offsetHeight) + 'px';
                    }
                }
            };
            
			
			//if (item.scrollpane.id=='siteContent__scrollpane') debugger;
			//item.scrollpane.style.width = (item.scrollpaneContainer.offsetWidth - item.scrollpaneContainer.offsetLeft - 15 - s.items[0].sliderbar.offsetWidth) + 'px';
			//if (cihe) cihe.style.width = item.scrollpane.offsetWidth; //BUGHUNT001
			
			if (item) {
				var percentageDown = (
					((s.containsIframe ? jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth:item.scrollpane.scrollTop) * 100)
					/  ((s.containsIframe ? jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollHeight :item.scrollpane.scrollHeight)-item.scrollpane.offsetHeight)
				);
				var top = (
					( percentageDown 
						* (item.sliderBottom.offsetTop - item.sliderTop.offsetHeight - item.slider.offsetHeight)
					) / 100			
				);
				if (moveSlider) {
					if (animateSlider) {
						sa.sp.animatedSliderMove (item, item.sliderTop.offsetHeight + top);
					} else {
						item.slider.style.top = (item.sliderTop.offsetHeight + top) + 'px';
					}
				}
			}
			
		}
		
		// --
		// --- onresize for HORIZONTAL SCROLLBAR 2of2
		// --
		item = s.items[1];
		if (item.sliderbarShown) {		
			if (item.scrollpaneContainer.style.position=='absolute') {
				item.sliderbar.style.top = (item.scrollpaneContainer.offsetHeight - item.sliderbar.offsetHeight) + 'px';
			} else {
				item.sliderbar.style.top = (item.scrollpaneContainer.offsetTop + item.scrollpaneContainer.offsetHeight - item.sliderbar.offsetHeight) + 'px';
			};
			//debugger;
			//item.scrollpane.style.height = (item.scrollpaneContainer.offsetHeight - item.sliderbar.offsetHeight) + 'px';
			if (s.items[0].sliderbar) {
				item.sliderbar.style.width = (item.scrollpaneContainer.offsetWidth - s.items[0].sliderbar.offsetWidth) + 'px';
				item.sliderBottom.style.left = (item.scrollpaneContainer.offsetWidth - s.items[0].sliderbar.offsetWidth - item.sliderBottom.offsetWidth) + 'px';
			} else {
				item.sliderbar.style.width = (item.scrollpaneContainer.offsetWidth) + 'px';
				item.sliderBottom.style.left = (item.scrollpaneContainer.offsetWidth - item.sliderBottom.offsetWidth) + 'px';
			};

			if (item) {
                
                if (s.containsIframe) {
                    var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                    var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                    
                    var percentageLeft = (
                        (sp.scrollLeft * 100)
                        /  (sp.scrollWidth-sp.offsetWidth)
                    );
                } else {
                    var percentageLeft = (
                        (item.scrollpane.scrollLeft * 100)
                        /  (item.scrollpane.scrollWidth-item.scrollpane.offsetWidth)
                    );
                };
				var top = (
					( percentageLeft 
						* (item.sliderBottom.offsetLeft - item.sliderTop.offsetWidth - item.slider.offsetWidth)
					) / 100			
				);
				if (moveSlider) {
					if (animateSlider) {
						sa.sp.animatedSliderMove (item, item.sliderTop.offsetWidth + top);
					} else {
						item.slider.style.left = (item.sliderTop.offsetWidth + top) + 'px';
					}
				}
			}
		};
		
		//jQuery(scrollpaneContainer).css({ top : 0, left : 0 });
		//jQuery(scrollpane).css({ top : 0, left : 0 });
		
		

		// 20131218 -checked- for lucidLog
		//scrollpane.style.width = item.scrollpaneContainer.offsetWidth - (s.items[0].sliderbarShown?s.items[0].sliderbar.offsetWidth+5:0) + 'px';
		//scrollpane.style.height = item.scrollpaneContainer.offsetHeight - (s.items[1].sliderbarShown?s.items[1].sliderbar.offsetHeight+5:0) + 'px';
		jQuery('#'+scrollpane.id+'__backgrounds').css({height : scrollpane.style.height, width : scrollpane.style.width});

		sa.m.restoreAllParents (ticketID);
	},
	
	getItem : function (el, itemIdx) {
		var scrollpane = null;
		while (!scrollpane) {
			if (el && el.className && el.className.indexOf('vsp_container')!==-1
                && el.className.match('vividScrollpane')
            ) {
				scrollpane = el.children[el.children.length-1];
			}
			el = el.parentNode;
		};
		var s = sa.vcc.settings[scrollpane.id];
		var item = s.items[itemIdx];
		return item;
	},
	
	getSliderbarSizeVertical : function (scrollpane) {
		var s = sa.vcc.settings[scrollpane.id];
		var item = s.items[0];
		return item.sliderbar.offsetHeight - item.sliderTop.offsetHeight - item.sliderBottom.offsetHeight;		
	},
	
	scroll_adjustSliderbar : function (scrollpane, axis) {
		var s = sa.vcc.settings[scrollpane.id];
		var itemIdx = axis=='vertical'?0:1;
		var item = s.items[itemIdx];
        
        
        if (!jQuery('#'+item.slider.id)[0]) return false;
		if (axis=='vertical') {
            
            //var x = scrollpane.style.height;
            //scrollpane.style.height = 'auto';
			var percentageDown = (
				((s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.pageYOffset:scrollpane.scrollTop) * 100)
				/  ((s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollHeight :scrollpane.scrollHeight)-item.scrollpaneContainer.offsetHeight)
			);
            
            //scrollpane.style.height = x;
            /*
			var percentageDown = (
				(scrollpane.scrollTop * 100)
				/  (scrollpane.scrollHeight-item.scrollpane.offsetHeight)
			);*/ // division by zero, no scrollbar slider activity
			
			var top = (
				( percentageDown 
					* (
						jQuery('#'+item.sliderBottom.id)[0].offsetTop 
						- jQuery('#'+item.sliderTop.id)[0].offsetHeight 
						- jQuery('#'+item.slider.id)[0].offsetHeight
					)
				) / 100			
			);
			jQuery('#'+item.slider.id).css({
				top : (jQuery('#'+item.sliderTop.id)[0].offsetHeight + top)
			});
			sa.sp.log (2099422, 'scroll_adjustSliderbar(): item.slider.id='+item.slider.id+', item.slider.style.top='+item.slider.style.top);
		} else {
			/*var percentageDown = (
				(scrollpane.scrollLeft * 100)
				/  ((s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth:scrollpane.scrollWidth)-item.scrollpaneContainer.offsetWidth)
			);*/
            
            if (s.containsIframe) {
                var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                
                var percentageDown = (
                    (sp.scrollLeft * 100)
                    /  (sp.scrollWidth-sp.offsetWidth)
                );
            } else {
                var percentageDown = (
                    (scrollpane.scrollLeft * 100)
                    /  (scrollpane.scrollWidth-item.scrollpane.offsetWidth)
                );
            };
            
			var top = (
				( percentageDown 
					* (
						jQuery('#'+item.sliderBottom.id)[0].offsetLeft 
						- jQuery('#'+item.sliderTop.id)[0].offsetWidth
						- jQuery('#'+item.slider.id)[0].offsetWidth
					)
				) / 100			
			);
			jQuery('#'+item.slider.id).css({
				top : (jQuery('#'+item.sliderTop.id)[0].offsetHeight + top)
			});

			sa.sp.log (2099422, 'scroll_adjustSliderbar(): item.slider.id='+item.slider.id+', item.slider.style.top='+item.slider.style.top);
		}

	},
	
	scroll_filterScrollCmds : function (item, what, types) {
		if (!item.scrollCmds) return false;
		var r = [];
		for (var i=0; i<item.scrollCmds.length; i++) {
			var sc = item.scrollCmds[i];
			if (sc && !sc.completed) {
				var found = false;
				for (var j=0; j<types.length; j++) {
					var t = types[j];
					if (t==sc.type) {
						found = true;
						break;
					}
				}
				if (what=='no') {
					if (!found) {
						r[r.length] = sc;
					}				
				}
					
			}
		};
		item.scrollCmds = r;
		return item.scrollCmds;
	},
	
	addScrollCmd : function (sc) {
		var s = sa.vcc.settings[sc.scrollpane.id];
		if (!s) debugger;
		
		var itemIdx = sc.axis=='vertical'?0:1;
		var item = s.items[itemIdx];
		sc.idx = item.scrollCmds.length;
		sc.initialScrollPos = (sc.axis=='vertical'?(s.containsIframe ? jQuery('iframe', sc.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth:item.scrollpane.scrollTop):(s.containsIframe ? jQuery('iframe', sc.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth:item.scrollpane.scrollLeft));
		//sc.scrollCmds = item.scrollCmds;
		
		//console.log ('sa.vividScrollpane.addScrollCmd(): ', sc);
		//if (sc.endScrollPos == 0) debugger;

		//var types = ['bothWays','scrollToPos']; // WORKS 20131201
		var types = ['bothWays','scrollToPos']; // WORKS 20131201
		//if (sc.type == 'scrollToPos') types[types.length] = 'mousewheel';
		
		
		//var types = ['scrollToPos']; //!!BUGS TOO
		item.scrollCmds = sa.sp.scroll_filterScrollCmds(item, 'no', types);

		sc.idx = item.scrollCmds.length;
		item.scrollCmds[sc.idx] = sc;

		//if (sc.scrollpane && sc.scrollpane.id!=='siteAds__scrollpane') debugger;
		
		if (item.animatedScrollTimeout===undefined) {
			item.scrollCmdIdx = 0;
			sa.sp.automatedScroll (sc);
		};
		//if (sc.scrollCmds.length>10) debugger;
		//sa.m.log (1, 't22', sc.scrollCmds.length);
	},
	
	reshuffleScrollCmds : function (sc) {
		if (typeof sc!=='object') return false;
        if (sc.completed && sc.axis!=='string') return false;
		if (sc.completed) {
			var s = sa.vcc.settings[sc.scrollpane.id];
			var itemIdx = sc.axis=='vertical'?0:1;
			var item = s.items[itemIdx];
			
			//if (item.scrollCmds.length>sc.idx+1) {
			if (item.scrollCmds.length>=sc.idx) {
				item.scrollCmds = [];
				clearTimeout (item.animatedScrollTimeout); // 20150524 2124 : EXPERIMENTAL
				delete item.animatedScrollTimeout;
				s.items = {};
				return false; // no more scrollCmds to execute
			} else {
				item.scrollCmdIdx = sc.idx+1;
				item.animatedSrollTimeout = setTimeout (sa.m.traceFunction(function () {
					sa.sp.automatedScroll (item.scrollCmds[item.scrollCmdIdx]);
				}), 102);
				return false;
			}
		} else {
			return true;
		}
	},
	
	automatedScroll : function (sc) {
		if (
			!sa.sp.reshuffleScrollCmds(sc)
		) {
			return false; 
			// if sa.sp.reshuffleScrollCmds() returns false, this means that 
			// another automatedScrollCmd was run that has now taken priority.
		};
		//if (sc.scrollpane.id=='siteContent__scrollpane') debugger;
		/*!!! DOES NOT ALLOW FOR scrollpanes that are not direct descendant of vividDialog!!
		if (sa.m.settings.current.visible && !sa.m.settings.current.visible['#'+sc.scrollpane.id.replace('__scrollpane','')]) {
			setTimeout (function () {
				sa.sp.automatedScroll (sc);
			}, 1000);
		} else {*/
			switch (sc.type) {
				case 'toDownOrRight':	sa.sp.automatedScroll_toDownOrRight(sc); break;
				case 'toTopOrLeft' : sa.sp.automatedScroll_toUpOrLeft(sc); break;
				case 'bothWays' : sa.sp.automatedScroll_bothWays(sc); break;
				case 'mousewheel' : sa.sp.automatedScroll_toPosition(sc); break;
				case 'scrollToPos' : sa.sp.scrollToPosStep(sc); break;
			}
		//}
	},	
	
	automatedScroll_bothWays : function (sc) {
		var s = sa.vcc.settings[sc.scrollpane.id];
		var itemIdx = sc.axis=='vertical'?0:1;
		var item = s.items[itemIdx];
		var offset = 2; // pixels
		
		
		if (sc.direction=='downOrRight') {
			if (sc.axis=='vertical') {
                if (s.containsIframe) {
                    var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                    var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                    //sp.scrollTop = sp.scrollTop + offset;
                    cw.scrollTo (cw.pageXOffset, cw.pageYOffset + offset);
                    if (sp.scrollTop >= sp.scrollHeight - sp.offsetHeight) {
                        sc.direction = 'upOrLeft';
                    }
                } else {
                    item.scrollpane.scrollTop = item.scrollpane.scrollTop + offset;
                    if (item.scrollpane.scrollTop >= item.scrollpane.scrollHeight - item.scrollpane.offsetHeight) {
                        sc.direction = 'upOrLeft';
                    }
				}
			} else {
                if (s.containsIframe) {
                    var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                    var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                
                    //sp.scrollLeft += offset;
                    cw.scrollTo (cw.pageXOffset + offset, cw.pageYOffset);
                    if (sp.scrollLeft >= sp.scrollWidth - sp.offsetWidth) {
                        sc.direction = 'upOrLeft';
                    }
                
                } else {
                
                    item.scrollpane.scrollLeft += offset;
                    if (item.scrollpane.scrollLeft >= item.scrollpane.scrollWidth - item.scrollpane.offsetWidth) {
                        sc.direction = 'upOrLeft';
                    }
				}
			}
		} else {
			if (sc.axis=='vertical') {
                if (s.containsIframe) {
                    var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                    var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                    //sp.scrollTop -= offset;
                    cw.scrollTo (cw.pageXOffset, cw.pageYOffset - offset);
                    if (sp.scrollTop <= 0) {
                        sc.direction = 'downOrRight';
                    }
                } else {

                    item.scrollpane.scrollTop -= offset;
                    if (item.scrollpane.scrollTop <= 0) {
                        sc.direction = 'downOrRight';
                    }
                }
			} else {
                if (s.containsIframe) {
                    var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                    var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                    //sp.scrollLeft -= offset;
                    cw.scrollTo (cw.pageXOffset - offset, cw.pageYOffset);
                    if (sp.scrollLeft <= 0) {
                        sc.direction = 'downOrRight';
                    }
                } else {

                    item.scrollpane.scrollLeft -= offset;
                    if (item.scrollpane.scrollLeft <= 0) {
                        sc.direction = 'downOrRight';
                    }
                }
			}
		};
		//console.log ('sa.vividScrollpane.automatedScroll_bothWays()', item.scrollpane.scrollTop, item.scrollpane.scrollLeft, sc);
		//debugger;

		item.automatedScrollDirection = sc.direction;
		
		sa.sp.scroll_adjustSliderbar (sc.scrollpane, sc.axis);
		
		item.animatedScrollTimeout = setTimeout (sa.m.traceFunction(function () {
			sa.sp.automatedScroll (sc);
		}), 250);//38);
	},
	
	automatedScroll_toPosition : function (sc) {
		var s = sa.vcc.settings[sc.scrollpane.id];
		var itemIdx = sc.axis=='vertical'?0:1;
		var item = s.items[itemIdx];
		var offset = 4; // pixels


		
        if (item.scrollpane===item.scrollpaneDiv) {
            var scrollpane = item.scrollpane;
        } else {
            var scrollpane = item.scrollpaneDiv;
        }
		if (s.containsIframe) {
		    var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
            var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
            if (sc.axis=='vertical') {
                if ( 
                    ((cw.pageYOffset - offset) <= sc.endScrollPos)
                    && ((cw.pageYOffset + offset) >= sc.endScrollPos)
                ) {
                    cw.scrollTo (cw.pageXOffset, sc.endScrollPos);
                    sc.completed = true;
                } else {
                    if (s.cw.pageYOffset <= sc.endScrollPos) {
                        offset = (sc.endScrollPos - cw.pageYOffset) / 10;
                        if (isNaN(offset)) sc.completed = true;
                        if (offset<4 && offset>=0) offset = 4;
                        if (offset>-4 && offset<0) offset = -4;
                        cw.scrollTo (cw.pageXOffset, cw.pageYOffset + offset);
                    } else {
                        offset = (s.cw.pageYOffset - sc.endScrollPos) / 10;
                        if (isNaN(offset)) sc.completed = true;
                        if (offset<4 && offset>=0) offset = 4;
                        if (offset>-4 && offset<0) offset = -4;
                        cw.scrollTo (cw.pageXOffset, cw.pageYOffset - offset);
                    }
                }
            } else {
                if ( 
                    ((s.cw.pageXOffset - offset) <= sc.endScrollPos)
                    && ((s.cw.pageXOffset + offset) >= sc.endScrollPos)
                ) {
                    cw.scrollTo (sc.endScrollPos, cw.pageYOffset);
                    sc.completed = true;
                } else {
                    if (cw.pageXOffset < sc.endScrollPos) {
                        cw.scrollTo (cw.pageXOffset + offset);
                    } else {
                        cw.scrollTo (cw.pageXOffset - offset);
                    }
                }
            };
        } else {
		
            if (sc.axis=='vertical') {
                if ( 
                    ((scrollpane.scrollTop - offset) <= sc.endScrollPos)
                    && ((scrollpane.scrollTop + offset) >= sc.endScrollPos)
                ) {
                    //item.cihe.scrollTop = sc.endScrollPos; // removed experimentally
                    if (s.containsIframe) {
                        cw.scrollTo (cw.pageXOffset, sc.endScrollPos);
                    } else {
                        scrollpane.scrollTop = sc.endScrollPos;
                    }
                    sc.completed = true;
                } else {
                    if (scrollpane.scrollTop <= sc.endScrollPos) {
                        offset = (sc.endScrollPos - scrollpane.scrollTop) / 10;
                        if (isNaN(offset)) sc.completed = true;
                        if (offset<2 && offset>=0) offset = 2;
                        if (offset>-2 && offset<0) offset = -2;
                        //sa.m.log (undefined, 't<=p : '+offset);
                        if (s.containsIframe) {
                            cw.scrollTo (cw.pageXOffset, cw.pageYOffset + offset);
                        } else {
                            scrollpane.scrollTop += offset;
                        }
                    } else {
                        offset = (scrollpane.scrollTop - sc.endScrollPos) / 10;
                        if (isNaN(offset)) sc.completed = true;
                        if (offset<2 && offset>=0) offset = 2;
                        if (offset>-2 && offset<0) offset = -2;
                        //sa.m.log (undefined, 't>p : '+offset);
                        if (s.containsIframe) {
                            cw.scrollTo (cw.pageXOffset, cw.pageYOffset - offset);
                        } else {
                            scrollpane.scrollTop -= offset;
                        }
                    }
                }
            } else {
                if ( 
                    ((scrollpane.scrollLeft - offset) <= sc.endScrollPos)
                    && ((scrollpane.scrollLeft + offset) >= sc.endScrollPos)
                ) {
                    if (s.containsIframe) {
                        cw.scrollTo (sc.endScrollPos, cw.pageYOffset);
                    } else {
                        scrollpane.scrollLeft = sc.endScrollPos;
                    };
                    sc.completed = true;
                } else {
                    if (s.containsIframe) {
                        if (scrollpane.scrollLeft < sc.endScrollPos) {
                            cw.scrollTo (cw.pageXOffset + offset);
                        } else {
                            cw.scrollTo (cw.pageXOffset - offset);
                        }
                    } else {
                        if (scrollpane.scrollLeft < sc.endScrollPos) {
                            scrollpane.scrollLeft += offset;
                        } else {
                            scrollpane.scrollLeft -= offset;
                        }
                    }
                }
            };
        };
			
		sa.sp.scroll_adjustSliderbar (scrollpane, sc.axis);
		if (!sc.completed) {
			item.animatedScrollTimeout = setTimeout (sa.m.traceFunction(function () {
				sa.sp.automatedScroll (sc);
			}), 38);
		}
	},
	
	initAutomatedScrolling : function (scrollpane, axis, start, direction, type) {
		if (!scrollpane) return false;
		var s = sa.vcc.settings[scrollpane.id];
		if (!s) return false;
        if (axis!=='vertical' && axis!=='horizontal') debugger;
		var itemIdx = axis=='vertical'?0:1;
		var item = s.items[itemIdx];
		if (!item) return false;
		
		//sa.m.log (undefined, 'sa.vividScrollpane.initAutomatedScrolling', item);
		if (typeof item.automatedScrollDirection==='string') direction = item.automatedScrollDirection;
		if (typeof type=='undefined') type = 'bothWays';
		if (typeof direction=='undefined') {
			if (type=='bothWays') direction = 'downOrRight';
			if (type=='toDownOrRight') direction = 'downOrRight';
			if (type=='toUpOrLeft') direction = 'upOrLeft';
		};
			
		if (start===false) {
			var types = [type];
			if (item.automatedScrolling) {
				types[types.length] = 'bothWays';
				types[types.length] = 'mousewheel';
				types[types.length] = 'scrollToPos';
			}
			item.scrollCmds = sa.sp.scroll_filterScrollCmds(item, 'no', types);
			if (item.scrollCmds.length==0) {
				clearTimeout (item.animatedScrollTimeout);
				delete item.animatedScrollTimeout;
			} else {
				if (!item.animatedScrollTimeout) sa.sp.automatedScroll (item.scrollCmds[item.scrollCmdIdx]);
			};
			return false;			
		} else {
			//debugger;

			if (type=='bothWays' || type=='vertical') {
				//scrollpane.style.height = 'auto';
			} else if (type=='bothWays' || type=='horizontal') {
				scrollpane.style.width = 'auto';
			}			
			item.automatedScrolling = true;
		};
		
		var sc = {
			scrollpane : scrollpane,
			axis : axis,
			type : type,
			direction : direction
		};
		item.automatedScrollDirection = direction;
		setTimeout(function() {
			sa.sp.addScrollCmd (sc);
		}, 100);
	},
	
	animatedSliderMove : function (item, scrollPos) {
		var sc = {
			scrollpane : item.scrollpane,
			axis : item.type,
			type : 'scrollToPos',
			endScrollPos : scrollPos						
		};
		//debugger;
		sa.sp.addScrollCmd (sc);
	},
	
	_mousewheel : function (evt,two,three) {
		var 
		id = (
            evt.target.className.indexOf('vividScrollpane')!==-1
            && jQuery('#'+jQuery(evt.target)[0].id+'__container').length>0
            ? evt.target.id
            : jQuery('#'+jQuery(evt.target).parents('.vividScrollpane')[0].id+'__container').length>0
                ? jQuery(evt.target).parents('.vividScrollpane')[0].id
                : evt.currentTarget.id
        ),
        id = id.replace('__container', ''),//.replace('__scrollpane',''),
		s = sa.vcc.settings[id],
		types = [ 'bothWays' ],
		item = s.items[0];
		item.scrollCmds = sa.sp.scroll_filterScrollCmds(item, 'no', types);
        
        
        if (item.scrollpane===item.scrollpaneDiv) {
            var scrollpane = item.scrollpane;
        } else {
            var scrollpane = item.scrollpaneDiv;
        }
        
		
		//console.log ('sa.vividScrollpane._mousewheel', this.id, item.sliderbarShown);
		if (item.mousewheelDisabled===true) return false;
		//if (jQuery(item.scrollbar).css('display')==='none' || jQuery(item.scrolbar).css('opacity')!=1) return false;

		if (!item.sliderbarShown) {
			//debugger;
			return false;
		};

		evt = evt ? evt : window.event;
		var rolled = 0;
		if ('wheelDelta' in evt) {
			rolled = evt.wheelDelta;
		} else {
			rolled = -20 * evt.detail;
		};
		rolled = (sa.m.userDevice.isInvertedMousewheel ? 1 : -1) * rolled;
		rolled = rolled * 0.57;
		
		var last = false;
		for (var i=0; i<item.scrollCmds.length; i++) {
			var sc = item.scrollCmds[i];
			if (sc.type=='mousewheel') {
				last = sc.endScrollPos;
			}
		};
		if (parseFloat(last)!==0) {
			item.scrollCmds = sa.sp.scroll_filterScrollCmds(item, 'no', ['mousewheel']);
			clearTimeout (item.animatedScrollTimeout);
			delete item.animatedScrollTimeout;
		};
		if (last===false) {
            if (s.containsIframe) {
                var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];

                if (item.type=='vertical') {
                    last = cw.pageYOffset;
                } else {
                    last = cw.pageXOffset;
                    
                }
            } else {
                if (item.type=='vertical') {
                    last = scrollpane.scrollTop;
                } else {
                    last = scrollpane.scrollLeft;
                    
                }
            }
		}
		if (item.type=='vertical') {
			if (last+rolled<0) var lr = 0; else if (last + rolled > (s.containsIframe ? jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollHeight :scrollpane.scrollHeight)-scrollpane.offsetHeight+20) var lr = (s.containsIframe ? jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollHeight :scrollpane.scrollHeight)-scrollpane.offsetHeight+20; else var lr = last+rolled;
			
		} else {
			if (last+rolled<0) var lr = 0; else if (last + rolled > (s.containsIframe ? jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth:scrollpane.scrollWidth)-scrollpane.offsetWidth+20) var lr = (s.containsIframe ? jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth:scrollpane.scrollWidth)-scrollpane.offsetWidth+20; else var lr = last+rolled;
		}
		
		
		var sc = {
			scrollpane : item.scrollpane,
			axis : item.type,
			type : 'mousewheel',
			endScrollPos : lr
		};
		sa.sp.addScrollCmd (sc);

		//if (typeof evt.preventDefault=='function') evt.preventDefault();
	},
	
	_mousedown : function (evt) {
		var el = sa.m.getEventTarget (evt);
		var item = sa.sp.getItem (el, el.id.match(/horizontal/)?1:0);
		sa.sp.draggedItem = item;
		item.scrollCmds=sa.sp.scroll_filterScrollCmds(item,'no', ['mousewheel']);
		//clearTimeout (item.animatedScrollTimeout);
		//delete item.animatedScrollTimeout;
		
		item.sliderBeginClientX = evt.clientX;
		item.sliderBeginClientY = evt.clientY;			
		item.sliderBeginOffsetLeft = el.offsetLeft;;
		item.sliderBeginOffsetTop = el.offsetTop;
		
		sa.sp.log (321, '_mousedown #' + el.id + '.' + el.className.replace('\ ','.'));
		sa.m.hookEvent (window, 'mousemove', sa.sp._mousemove, true, true);
		sa.m.hookEvent (window, 'mouseup', sa.sp._mouseup, true, true);
		
		//prevent default drag and drop functionality of the browser
		if (evt.preventDefault) evt.preventDefault();
		return false; 
	},

	_mousedownContent : function (evt,scrollpane) {
		//item.scrollCmds=sa.sp.scroll_filterScrollCmds(item,'no', ['mousewheel']);
		//clearTimeout (item.animatedScrollTimeout);
		//delete item.animatedScrollTimeout;
		
		var s = sa.vcc.settings[scrollpane.id], el = scrollpane;
		//debugger;
		var item = s.items[0];
		item.sliderBeginClientX = evt.clientX;
		item.sliderBeginClientY = evt.clientY;			
		item.sliderBeginOffsetLeft = evt.clientX;//el.offsetLeft;;
		item.sliderBeginOffsetTop = evt.clientY;	//el.offsetTop;
		var item = s.items[1];
		item.sliderBeginClientX = evt.clientX;
		item.sliderBeginClientY = evt.clientY;			
		item.sliderBeginOffsetLeft = evt.clientX;//el.offsetLeft;;
		item.sliderBeginOffsetTop = evt.clientY;	//el.offsetTop;
		
		sa.sp.log (321, '_mousedownContent #' + el.id + '.' + el.className.replace('\ ','.'));
		sa.m.hookEvent (window, 'mousemove', function (evt) { sa.sp._mousemoveContent(evt,scrollpane); }, true, true);
		sa.m.hookEvent (window, 'mouseup', function (evt) { sa.sp._mouseupContent(evt,scrollpane); }, true, true);
		
		//prevent default drag and drop functionality of the browser
		if (evt.preventDefault) evt.preventDefault();
		return false; 
	},
	
	
	_mouseup : function (evt) {
		sa.m.hookEvent (window, 'mousemove', sa.sp._mousemove, true, false);

		var el = sa.m.getEventTarget (evt);
		sa.sp.log (321, 'sa.vividScrollpane._mouseup #' + el.id + '.' + (el.className?el.className.replace('\ ','.'):''));
		setTimeout ("delete sa.sp.draggedItem",1);
	},

	_mouseupContent : function (scrollpane) {
		sa.m.hookEvent (window, 'mousemove', function (evt) { sa.sp._mousemoveContent(evt,scrollpane) }, true, false);

		var el = scrollpane;
		sa.sp.log (321, 'sa.vividScrollpane._mouseupContent #' + el.id + '.' + (el.className?el.className.replace('\ ','.'):''));
	},

	_mousemove : function (evt) {
		var item = sa.sp.draggedItem;
		var s = sa.vcc.settings[item.scrollpane.id];
		//debugger;
		if (item.type=='vertical') {
			item.slider.style.top = (item.sliderBeginOffsetTop + (evt.clientY - item.sliderBeginClientY))  + 'px';
			if (item.slider.offsetTop < item.sliderTop.offsetHeight) item.slider.style.top = item.sliderTop.offsetHeight + 'px';
			if (item.slider.offsetTop > item.sliderBottom.offsetTop - item.slider.offsetHeight) item.slider.style.top = (item.sliderBottom.offsetTop - item.slider.offsetHeight) + 'px';
			
			var percentageDown = (
				((item.slider.offsetTop-item.sliderTop.offsetHeight) * 100)
				/ (item.sliderBottom.offsetTop - item.sliderTop.offsetHeight - item.slider.offsetHeight)
			);
			var scrollTop = Math.round(
				(percentageDown * ((s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollHeight :item.scrollpane.scrollHeight)-item.scrollpane.offsetHeight))
				/ 100
			);
                debugger;
            if (s.containsIframe) {
                var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                cw.scrollTo (sp.scrollLeft, scrollTop);
            } else { 
                item.scrollpane.scrollTop = scrollTop;
            }
		} else {
		//horizontal
			item.slider.style.left = (item.sliderBeginOffsetLeft + (evt.clientX - item.sliderBeginClientX)) + 'px';
			if (item.slider.offsetLeft < item.sliderTop.offsetWidth) item.slider.style.left = item.sliderTop.offsetWidth + 'px';
			if (item.slider.offsetLeft > item.sliderBottom.offsetLeft - item.slider.offsetWidth) item.slider.style.left = (item.sliderBottom.offsetLeft - item.slider.offsetWidth) + 'px';

			var percentageDown = (
				((item.slider.offsetLeft - item.sliderTop.offsetWidth) * 100)
				/ (item.sliderBottom.offsetLeft - item.sliderTop.offsetWidth - item.slider.offsetWidth)
			);
			var scrollLeft = Math.round(
				(percentageDown * ((s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth:item.scrollpane.scrollWidth) - item.scrollpane.offsetWidth))
				/ 100
			);
            if (s.containsIframe) {
                var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                //sp.scrollLeft = scrollLeft;
                cw.scrollTo (scrollLeft, cw.pageYOffset);
            } else { 
                item.scrollpane.scrollLeft = scrollLeft;
            }
		};
		sa.sp.log (321, '_mousemove '+percentageDown+' '+scrollTop);
		return false;
	},
	
	_mousemoveContent : function (evt,scrollpane) {
		var s = sa.vcc.settings[scrollpane.id];
		//debugger;
		var item = s.items[0];
		if (item.type=='vertical') {
			item.slider.style.top = (item.sliderBeginOffsetTop + (evt.clientY - item.sliderBeginClientY))  + 'px';
			if (item.slider.offsetTop < item.sliderTop.offsetHeight) item.slider.style.top = item.sliderTop.offsetHeight + 'px';
			if (item.slider.offsetTop > item.sliderBottom.offsetTop - item.slider.offsetHeight) item.slider.style.top = (item.sliderBottom.offsetTop - item.slider.offsetHeight) + 'px';
			
			var percentageDown = (
				((item.slider.offsetTop-item.sliderTop.offsetHeight) * 100)
				/ (item.sliderBottom.offsetTop - item.sliderTop.offsetHeight - item.slider.offsetHeight)
			);
			var scrollTop = Math.round(
				(percentageDown * ((s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollHeight :item.scrollpane.scrollHeight)-item.scrollpane.offsetHeight))
				/ 100
			);
            debugger;
            if (s.containsIframe) {
                var sp = jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                sp.scrollTop = scrollTop;
            } else { 
                item.scrollpane.scrollTop = scrollTop;
            }
		};
		item = s.items[1];
		if (item.type=='horizontal') {
			item.slider.style.left = (item.sliderBeginOffsetLeft + (evt.clientX - item.sliderBeginClientX)) + 'px';
			if (item.slider.offsetLeft < item.sliderTop.offsetWidth) item.slider.style.left = item.sliderTop.offsetWidth + 'px';
			if (item.slider.offsetLeft > item.sliderBottom.offsetLeft - item.slider.offsetWidth) item.slider.style.left = (item.sliderBottom.offsetLeft - item.slider.offsetWidth) + 'px';

			var percentageDown2 = (
				((item.slider.offsetLeft - item.sliderTop.offsetWidth) * 100)
				/ (item.sliderBottom.offsetLeft - item.sliderTop.offsetWidth - item.slider.offsetWidth)
			);
			var scrollLeft = Math.round(
				(percentageDown2 * ((s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth:item.scrollpane.scrollWidth) - item.scrollpane.offsetWidth))
				/ 100
			);
			if (scrollLeft == 0) debugger;
            if (s.containsIframe) {
                var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                //sp.scrollLeft = scrollLeft;
                debugger;
                cw.scrollTo (scrollLeft, cw.pageYOffset);
            } else { 
                item.scrollpane.scrollLeft = scrollLeft;
            }
		};
		sa.sp.log (321, '_mousemoveContent '+percentageDown, scrollTop, percentageDown2, scrollLeft);
		return false;
	},
	_mouseout : function (evt) {
		var el = sa.m.getEventTarget (evt);
		sa.sp.log (321, '_mouseout #' + el.id + '.' + el.className.replace('\ ','.'));
		sa.m.hookEvent (el, 'mousemove', sa.sp._mousemove, false, false);
	},		

	_mouseoutContent : function (evt,scrollpane) {
		var el = scrollpane;
		sa.sp.log (321, '_mouseoutContent #' + el.id + '.' + el.className.replace('\ ','.'));
		sa.m.hookEvent (el, 'mousemove', sa.sp._mousemove, false, false);
	},		
	
	_click : function (evt) { 
		var el = sa.m.getEventTarget (evt);
		var item = el.item;
        var s = el.item.s;
		if (!item) return false; 

		if (!sa.sp.draggedItem && el.className=='vsp_sliderbar') {
			if (item.type=='vertical') {
                
                if (s.containsIframe) {
                    var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                    var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                    
                    var offset = 0;
                    var y = evt.clientY - item.sliderbar.offsetTop; 
                    if (y < item.slider.offsetTop) offset = -1 * item.scrollpaneContainer.offsetHeight;
                    if (y > item.slider.offsetTop + item.slider.offsetHeight) offset = item.scrollpaneContainer.offsetHeight;
                    
                    //sp.scrollTop += offset;
                    cw.scrollTo (cw.pageXOffset, cw.pageYOffset + offset);
                    
                    var percentageDown = (
                        (sp.scrollTop * 100)
                        /  (sp.scrollHeight-item.scrollpaneContainer.offsetHeight)
                    );
                    
                    
                } else { 
                    var offset = 0;
                    var y = evt.clientY - item.sliderbar.offsetTop; 
                    if (y < item.slider.offsetTop) offset = -1 * item.scrollpane.offsetHeight;
                    if (y > item.slider.offsetTop + item.slider.offsetHeight) offset = item.scrollpane.offsetHeight;
                    
                    item.scrollpane.scrollTop += offset;

                    var percentageDown = (
                        (item.scrollpane.scrollTop * 100)
                        /  (item.scrollpane.scrollHeight-item.scrollpaneContainer.offsetHeight)
                    );
                }

				var top = (
					( percentageDown 
						* (item.sliderBottom.offsetTop - item.sliderTop.offsetHeight - item.slider.offsetHeight)
					) / 100			
				);
				item.slider.style.top = (item.sliderTop.offsetHeight + top) + 'px';
				if (item.slider.offsetTop < item.sliderTop.offsetHeight) item.slider.style.top = item.sliderTop.offsetHeight + 'px';
				if (item.slider.offsetTop > item.sliderBottom.offsetTop - item.slider.offsetHeight) item.slider.style.top = (item.sliderBottom.offsetTop - item.slider.offsetHeight) + 'px';
			} else {
			//horizontal
                if (s.containsIframe) {
                    var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                    var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                    
                    var offset = 0;
                    var x = evt.clientX - item.sliderbar.offsetLeft;
                    if (x < item.slider.offsetLeft) offset = -1 * sp.offsetWidth;
                    if (x > item.slider.offsetLeft + item.slider.offsetWidth) offset = sp.offsetWidth;
                    
                    //sp.scrollLeft += offset;
                    cw.scrollTo (cw.pageXOffset + offset, cw.pageYOffset);
                    var percentageLeft = (
                        (sp.scrollLeft * 100)
                        /  (sp.scrollWidth-item.scrollpaneContainer.offsetWidth)
                    );
                } else { 
                    var offset = 0;
                    var x = evt.clientX - item.sliderbar.offsetLeft;
                    if (x < item.slider.offsetLeft) offset = -1 * item.scrollpane.offsetWidth;
                    if (x > item.slider.offsetLeft + item.slider.offsetWidth) offset = item.scrollpane.offsetWidth;
                    
                    item.scrollpane.scrollLeft += offset;
                    var percentageLeft = (
                        (item.scrollpane.scrollLeft * 100)
                        /  (item.scrollpane.scrollWidth-item.scrollpaneContainer.offsetWidth)
                    );
                };

				var left = (
					( percentageLeft
						* (item.sliderBottom.offsetLeft - item.sliderTop.offsetWidth - item.slider.offsetWidth)
					) / 100			
				);
				item.slider.style.left = (item.sliderTop.offsetWidth + left) + 'px';
				if (item.slider.offsetLeft < item.sliderTop.offsetWidth) item.slider.style.left = item.sliderTop.offsetWidth + 'px';
				if (item.slider.offsetLeft > item.sliderBottom.offsetLeft - item.slider.offsetWidth) item.slider.style.left = (item.sliderBottom.offsetLeft - item.slider.offsetWidth) + 'px';
			};
		} else {
			if (el.className.indexOf('sliderTop')!==-1) var offset = -10;
			if (el.className.indexOf('sliderBottom')!==-1) var offset = 10;
			
			if (item.type=='vertical') {
				item.slider.style.top = (parseFloat(item.slider.style.top) + offset) + 'px';
				if (item.slider.offsetTop < item.sliderTop.offsetHeight) item.slider.style.top = item.sliderTop.offsetHeight + 'px';
				if (item.slider.offsetTop > item.sliderBottom.offsetTop - item.slider.offsetHeight) item.slider.style.top = (item.sliderBottom.offsetTop - item.slider.offsetHeight) + 'px';
				
				var percentageDown = (
					((item.slider.offsetTop-item.sliderTop.offsetHeight) * 100)
					/ (item.sliderBottom.offsetTop - item.sliderTop.offsetHeight - item.slider.offsetHeight)
				);
				var scrollTop = Math.round(
					(percentageDown * ((s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollHeight :item.scrollpane.scrollHeight)-item.scrollpane.offsetHeight))
					/ 100
				);
                
                if (s.containsIframe) {
                    var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                    var sp = jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                    //sp.scrollTop = scrollTop;
                    cw.scrollTo (cw.pageXOffset, scrollTop);
                    
                } else { 
                    item.scrollpane.scrollTop = scrollTop;
                }
			} else {
			//horizontal
				item.slider.style.left = (parseFloat(item.slider.style.left) + offset) + 'px';
				if (item.slider.offsetLeft < item.sliderTop.offsetWidth) item.slider.style.left = item.sliderTop.offsetWidth + 'px';
				if (item.slider.offsetLeft > item.sliderBottom.offsetLeft - item.slider.offsetWidth) item.slider.style.left = (item.sliderBottom.offsetLeft - item.slider.offsetWidth) + 'px';
	
				var percentageDown = (
					((item.slider.offsetLeft - item.sliderTop.offsetWidth) * 100)
					/ (item.sliderBottom.offsetLeft - item.sliderTop.offsetWidth - item.slider.offsetWidth)
				);
				var scrollLeft = Math.round(
					(percentageDown * ((s.containsIframe ? jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth:item.scrollpane.scrollWidth) - item.scrollpane.offsetWidth))
					/ 100
				);
                
                if (s.containsIframe) {
                    var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                    var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                    //sp.scrollLeft = scrollLeft;
                    cw.scrollTo (scrollLeft, cw.pageYOffset);
                } else { 
                    item.scrollpane.scrollTop = scrollLeft;
                }
                
			}
		};
		sa.sp.log (321, '_click #' + el.id + '.' + el.className.replace('\ ','.'));
	},
	
	scrollTo : function (scrollpane, what, callback) {
		sa.m.showAllParents(what);
		var w = what, x = 0, y = 0, found=false;
		
		if (what=='top') {
			y = 0; x = 0;
		} else {
			while (!found) {
				x += w.offsetLeft;
				y += w.offsetTop;
				sa.sp.log (321, 'scrollTo(): scrollpane.id="'+scrollpane.id+'", what.id="'+what.id+'", x='+x+', y='+y);
				var op = w.offsetParent, cur = w, found2=false;
				while (cur !== op && !found2) {
					if (cur.className.match('vividScrollpane')) {
						found2=true;
					} else {
						cur = cur.parentNode;
					};
				};
				if (found2) {
					found=true;
				} else {
					w = w.offsetParent;
				};
			};
			y -= 40; x -= 40;
		}
		if (x<0) x = 0;
        if (y<0) y = 0;
		sa.sp.log (321, 'scrollTo(): scrollpane.id="'+scrollpane.id+'", what.id="'+what.id+'", x='+x+', y='+y);
		sa.sp.scrollToPos (scrollpane, x, y, callback);
		
	},
	
	scrollToPos : function (scrollpane, x, y, callback, top, left) {
		var types = ['mousewheel','bothWays', 'scrollToPos'];
		var s = sa.vcc.settings[scrollpane.id];
		if (!s) return false;
		s.items[0].scrollCmds = sa.sp.scroll_filterScrollCmds(s.items[0], 'no', types);
		s.items[1].scrollCmds = sa.sp.scroll_filterScrollCmds(s.items[1], 'no', types);
        debugger;

        if (s.containsIframe) {
            var cw = jQuery('iframe', scrollpane)[0].contentWindow;
            var sp = jQuery('iframe', scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0]
            var sc = { 
                type:'scrollToPos', 
                callback : callback, 
                scrollpane : scrollpane, 
                targetX : (left?(-1*left)+x:x), 
                targetY : (top?(-1*top)+y:y), 
                beginX : cw.pageXOffset, 
                beginY : cw.pageYOffset, 
                stepsDone : 1, 
                steps : 10,
                numStepsToSpeedUp : 3,
                numStepsToSlowDown : 3
            };
            
        } else {
            var sc = { 
                type:'scrollToPos', 
                callback : callback, 
                scrollpane : scrollpane, 
                targetX : (left?(-1*left)+x:x), 
                targetY : (top?(-1*top)+y:y), 
                beginX : scrollpane.scrollLeft, 
                beginY : scrollpane.scrollTop, 
                stepsDone : 1, 
                steps : 10,
                numStepsToSpeedUp : 3,
                numStepsToSlowDown : 3
            };
        };
		sa.sp.addScrollCmd(sc);
		//var cmdIdx = sa.sp.settings.animatedScrollTo.length;
		//sa.sp.settings.animatedScrollTo[cmdIdx] = scrollCmd;
		//sa.sp.scrollToPosStep (cmdIdx);	
	},
	
	scrollToPosStep : function (sc) {
		//var sc = sa.sp.settings.animatedScrollTo[cmdIdx];
		var s = sa.vcc.settings[sc.scrollpane.id];
		if (!s || !s.items[0]) return false; // not initialized fully yes.theme..
		var t = sa.vcc.getVividTheme (s.themeName); 

		//debugger;
		if (!sc.completed && sc.stepsDone <= sc.steps) {
			var item = s.items[0];
			if (item) {
//				item.scrollpane.scrollTop += ((sc.targetY-sc.beginY)/sc.steps);
				
				var offset = (sc.targetY - sc.beginY) / sc.steps; // pixels
				debugger;

                if (s.containsIframe) {
                    var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                    var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];

                    //sp.scrollTop += offset;
                    cw.scrollTo (cw.pageXOffset, cw.pageYOffset + offset);
                } else { 
                    item.scrollpane.scrollTop += offset;
                }
				/*
				if ( 
					((item.scrollpane.scrollTop - offset) <= sc.targetY)
					&& ((item.scrollpane.scrollTop + offset) >= sc.targetY)
				) {
					item.scrollpane.scrollTop = sc.targetY;
					sc.completed = true;
				} else {
					if (item.scrollpane.scrollTop <= sc.targetY) {
						offset = (sc.targetY - sc.beginY) / sc.steps;
						if (offset<2 && offset>=0) offset = 2;
						if (offset>-2 && offset<0) offset = -2;
						//sa.m.log (undefined, 't<=p : '+offset);
						item.scrollpane.scrollTop += offset;
					} else {
						offset = (sc.beginY - sc.targetY) / sc.steps;
						if (offset<2 && offset>=0) offset = 2;
						if (offset>-2 && offset<0) offset = -2;
						//sa.m.log (undefined, 't>p : '+offset);
						item.scrollpane.scrollTop -= offset;
					}
				}*/				
				
				//sa.sp.log (321, 'scrollToPosStep(): stepsDone='+sc.stepsDone+', scrollTop='+item.scrollpane.scrollTop);
/*
				var percentageDown = (
					(item.scrollpane.scrollTop * 100)
					/  (item.scrollpane.scrollHeight-item.scrollpane.offsetHeight)
				);
				var top = (
					( percentageDown 
						* (item.sliderBottom.offsetTop - item.sliderTop.offsetHeight - item.slider.offsetHeight)
					) / 100			
				);
				item.slider.style.top = (item.sliderTop.offsetHeight + top) + 'px';
*/				
				sa.sp.scroll_adjustSliderbar (sc.scrollpane, 'vertical');
			}
	
			var item = s.items[1];
			if (item) {
				//item.scrollpane.scrollLeft += ((sc.targetX-sc.beginX)/sc.steps);
				
				var offset = (sc.targetX - sc.beginX) / sc.steps; // pixels
                if (s.containsIframe) {
                    var cw = jQuery('iframe', item.scrollpane)[0].contentWindow;
                    var sp = jQuery('iframe', item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                    //sp.scrollLeft += offset;
                    cw.scrollTo (cw.pageXOffset + offset, cw.pageYOffset);
                } else { 
                    item.scrollpane.scrollLeft += offset;
                }

				/*
				if ( 
					((item.scrollpane.scrollLeft - offset) <= sc.targetX)
					&& ((item.scrollpane.scrollLeft + offset) >= sc.targetX)
				) {
					item.scrollpane.scrollLeft = sc.targetX;
					sc.completed = true;
				} else {
					if (item.scrollpane.scrollLeft <= sc.targetX) {
						offset = (sc.targetX - sc.beginX) / sc.steps;
						if (offset<2 && offset>=0) offset = 2;
						if (offset>-2 && offset<0) offset = -2;
						//sa.m.log (undefined, 't<=p : '+offset);
						item.scrollpane.scrollLeft += offset;
					} else {
						offset = (sc.beginX - sc.targetX) / sc.steps;
						if (offset<2 && offset>=0) offset = 2;
						if (offset>-2 && offset<0) offset = -2;
						//sa.m.log (undefined, 't>p : '+offset);
						item.scrollpane.scrollLeft -= offset;
					}
				}
				*/
				/*
				var percentageLeft = (
					(item.scrollpane.scrollLeft * 100)
					/  (item.(s.containsIframe ? jQuery('iframe',scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0].scrollWidth:scrollpane.scrollWidth)-item.scrollpane.offsetWidth)
				);
				var top = (
					( percentageDown 
						* (item.sliderBottom.offsetLeft - item.sliderTop.offsetWidth - item.slider.offsetWidth)
					) / 100			
				);
				item.slider.style.left = (item.sliderTop.offsetHeight + top) + 'px';
				*/
				sa.sp.scroll_adjustSliderbar (sc.scrollpane, 'horizontal');
			}

			var o2 = 50;
			if (sc.stepsDone > (sc.steps/2)) {
				if (sc.stepsDone > (sc.steps-sc.numStepsToSlowDown)) {
					var o = o2 - (o2 / (sc.stepsDone -(sc.steps - sc.stepsDone)))
				} else {
					var o = 0;
				}
			} else {
				if (sc.stepsDone < sc.numStepsToSpeedUp) {
					var o = o2 / sc.stepsDone
				} else {
					var o = 0;
				}
			}
			
			sc.stepsDone++;
			setTimeout (sa.m.traceFunction(function() {
				sa.sp.automatedScroll (sc);
			}), 38+o);
		} else {
            sc.completed = true;
            //alert (sc.type);
			if (sc.type=='scrollToPos') return false;
			
			var item = s.items[0];
			if (item.sliderbarShown) {
                if (s.containsIframe) {
                    var cw = jQuery('iframe',item.scrollpane)[0].contentWindow;
                    var sp = jQuery('iframe',item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                    //sp.scrollTop = sc.targetY;
                    cw.scrollTo (cw.pageXOffset, sc.targetY);
                    sa.sp.log (321, 'scrollToPosStep(): scrollTop='+sp.scrollTop);
                    var percentageDown = (
                        (sp.scrollTop * 100)
                        /  (sp.scrollHeight-sp.offsetHeight)
                    );
                } else {
                    item.scrollpane.scrollTop = sc.targetY;
                    sa.sp.log (321, 'scrollToPosStep(): scrollTop='+item.scrollpane.scrollTop);
                    var percentageDown = (
                        (item.scrollpane.scrollTop * 100)
                        /  (item.scrollpane.scrollHeight-item.scrollpane.offsetHeight)
                    );
                }
				var top = (
					( percentageDown 
						* (item.sliderBottom.offsetTop - item.sliderTop.offsetHeight - item.slider.offsetHeight)
					) / 100			
				);
				item.slider.style.top = (item.sliderTop.offsetHeight + top) + 'px';
			}
	
			var item = s.items[1];
			if (item.sliderbarShown) {
                if (s.containsIframe) {
                    var cw = jQuery('iframe',item.scrollpane)[0].contentWindow;
                    var sp = jQuery('iframe',item.scrollpane)[0].contentWindow.document.getElementsByTagName('body')[0];
                    //sp.scrollLeft = sc.targetX;
                    cw.scrollTo (sc.targetX, cw.pageYOffset);
                    var percentageLeft = (
                        (sp.scrollLeft * 100)
                        /  (sp.scrollWidth-sp.offsetWidth)
                    );
                } else {
                    item.scrollpane.scrollLeft = sc.targetX;
                    var percentageLeft = (
                        (item.scrollpane.scrollLeft * 100)
                        /  (item.scrollpane.scrollWidth-item.scrollpane.offsetWidth)
                    );
                }
				var top = (
					( percentageLeft 
						* (item.sliderBottom.offsetLeft - item.sliderTop.offsetWidth - item.slider.offsetWidth)
					) / 100			
				);
				item.slider.style.left = (item.sliderTop.offsetWidth + top) + 'px';
			}
			
			sc.completed = true;
			if (typeof sc.callback=='function') {
				var types = ['scrollToPos','mousewheel'];
				sc.callback (sc);
				item.scrollCmds = sa.sp.scroll_filterScrollCmds(sc, 'no', types);
			}
		}
	}
};
