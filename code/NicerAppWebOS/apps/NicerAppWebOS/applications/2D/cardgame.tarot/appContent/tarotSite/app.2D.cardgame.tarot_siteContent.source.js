na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'] = {
	about : {
		whatsThis : 'Application game code for this tarot card game',
		copyright : '(c) and (r) 2012-2022 by [the owner of nicer.app]',
		license : 'only allowed to be used on http://nicer.app/tarot',
		lastModified : '2022 April 20th, 16:50 CEST'
	},
	globals : {
        animateCardsDuration : 330,
		maxWidthFactor : 0.50,
		zoomCardMinimumContainerWidth : 1, // never display that zoomCard.
		modeCardDisplayMaxCardsHeight : 0.67,
		nested : true, //(window!==window.top),
		fancybox : {
			'width' : '85%',
			'height'	: '85%',
			'autoScale' : false,
			'transitionIn' : 'elastic',
			'transitionOut' : 'elastic'
		},
		prettyPhoto : {
			showTitle : false,
			allow_resize : true
		},

		desktop : {
			configuration : {
				margins : {
					bottom : 0,
					bottomDefault : 0
				},
				spacing : 10,
				outOfView : {
					spacing : 100
				}
			},
			calculate : {
				configsSettings : function (el) {
					var cs = na.cloneObject(na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.desktop.configuration);
					return cs;
				},
				configs : function (el) {
					return na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.desktopConfigurations;
				}
			}
		},
		desktopConfigurations : {
			default : {
				id : 'maximizedContent',
				desktop : { size : { width : 1000, height : 1000 }, margin : 5 },
				sizes : { // INNER sizes of dialogs, actually!
					'#siteLogo' : { top : '2%', left : '2%', width : '100%', height : '100%' },
					'#saAppMenu__app_2D_cardgame_tarot' : { top : '2%', left : '2%', width : '100%', height : '100%' },
					'#socializethis' : { top : '2%', left : '2%', width : 275, height : 40 },
					'#dialog-content' : { top : 0, left : 0, width : '100%', height : '100%' },
					'#dialog-ads' : { top : '2%', left : '2%', width : '96%', height : '96%' }
				},
				elements : {
					firstLine : {
						'#saAppMenu__app_2D_cardgame_tarot' : { snapTo : [ { element : 'body', edge : 'left' } ] },
						"#dialog-content" : { snapTo : [ { element : '#saAppMenu__app_2D_cardgame_tarot', edge : 'bottom' }, { element : 'body', edge : 'left' } ], growTo : 'max' }
					}
				}
			}
		}
	},
	settings : {
		mode : 'initial',
		loadedIn : {
			'#siteContent' : {
				settings : {
					initialized : false
				},
				saConfigUpdate : function (settings) {
					na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.desktop.configs = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.desktop.calculate.configs();
					na.desktop.settings.allConfigs = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.desktop.configs;
				},
				onload : function () {
                    //na.analytics.logMetaEvent ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot : init-stage-2-javascript-0');
					debugger;
					na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].nestedStartApp();
				},
                ondestroy : function (settings) { 
                    // never put any asynchronous calls in an ondestroy handler, except for this statement here :
                    // as this can mess up na.s.c.loadContent()'s work.
                    setTimeout (function() {
                        delete na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'];
                    }, 10);
                },
				onresize : function (settings) {
					// TODO : what's settings.isManualResize ???
					if (jQuery('#appGame').css('display')=='none') jQuery('#appGame').fadeIn('slow');
					na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].onresize();
				}
			}
		},
		current : {},
		cardHighestZindex : 1000
	},
	
	nestedStartApp : function () {
		na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].startApp();
		/*
		configUpdate();
		
		console.log ('na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].nestedStartApp 1:', typeof window.onloadCallback);
		if (typeof window.onloadCallback=='function') window.onloadCallback(na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].startApp); else {
			console.log ('na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].nestedStartApp 2:', window === window.top?'true':'false', window.parent, window.parent.window, window.top.sa);
			if (window === window.top) {
				na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].startApp();
			} else if (
				window.parent 
				&& window.parent.window
				&& window.top.sa
			) {
				
				var f = window.top.na.m.settings.settings.iframeLoaded;
				console.log ('na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].nestedStartApp 3:', ''+f);
				$('#iframe-content', window.top.document).addClass('saDontHijackLinksInThis');
				if (typeof f==='function') f(na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].startApp);
			}
		}*/
	},
	
	startApp : function () {
        //jQuery('#siteContent').addClass('saNoResize').css({ top:0, left:0, width : jQuery('#siteContent__contentDimensions')[0].offsetWidth - 25, height : jQuery('#siteContent__contentDimensions')[0].offsetHeight });
		$('#siteContent .vividDialogContent').show();
        if (jQuery('#appGame').css('display')=='none') jQuery('#appGame').fadeIn('fast');
        //na.analytics.logMetaEvent ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot : startApp()');
		if (jQuery('#app_mainmenu').length==0) return false;
	
		//alert ('-1-');
		//na.m.globals.urls.settings = na.m.globals.urls.app + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.url.replace('/content/','');
		na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.mode = 'initial';
		
		//jQuery('#siteContent .vd_btns', window.top).css({display:'none'});
		/*
		jQuery('#siteContent').click(function() {
			var 
			menuHTMLid = 'siteMenu',
			mwb = window.top.na.vcc.settings[menuHTMLid].menuWalkback; // BUGGY 
			
			window.top.na.menu.startWalkback(mwb.menuHTMLid, mwb.s, mwb.idx);
		});*/
		
		//na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].maybeShowAds(true);
		
		//na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].onresize();
		//na.desktop.setConfig ('contentStatusbar'); // place your ads in the statusbar please, heighten it a bit more if you need to.
		/*
        na.site.setVisible([
            {element:'#siteAds',visible:!na.m.userDevice.isPhone},
            {element:'#siteContent',visible:true}
        ], false, undefined, 3000/*, true, na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].onresize* /);*/
		//na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.timeout_maybeShowAds = setTimeout (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].maybeShowAds, 1 * 60 * 1000, true);
		
		// shove the game's menu into the main menu in window.top:
		if (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.nested) {
            /*
			jQuery('#saAppMenu__app_2D_cardgame_tarot ul').html(
				'<li><a class="menu__dontKeepSelected" href="#">Tarot</a><ul>'
				+jQuery('#saAppMenu__app_2D_cardgame_tarot ul')[0].innerHTML.replace('Tarot Decks', 'Decks')
				+'</ul></li>'
			);*/
		};
        
        /*
		if (!na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.linksTransformed) {
            na.menu.preprocess('saAppMenu__app_2D_cardgame_tarot');
		    na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].transformLinks (jQuery('#saAppMenu__app_2D_cardgame_tarot')[0]);
            na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.linksTransformed = true;
        }
		if (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.nested) {
			//jQuery('#saAppMenu__app_2D_cardgame_tarot').css({display:'none'});
			window.top.na.menu.merge ('siteMenu', 'na.site.code.transformLinks', '-saLinkpoint-appMenu', jQuery('#saAppMenu__app_2D_cardgame_tarot')[0], undefined, false);
			na.menu.onresize('siteMenu');
			//jQuery('#siteMenu').css({width
		}*/

		//var newURL = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.url.replace('/siteLogic/get_content.php?url=tarot','').replace('/siteLogic/get_content.php?url=tarot','').replace(/\&rootURL.*/,'').replace('content/tarot/','');
		//window.top.History.pushState (null, 'Free Tarot', '/tarot'+newURL);

		////debugger;
		na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].bindEvents();
		
		/*
		var scv = jQuery('#siteContent__dialog').css('display');
		jQuery('#siteContent__dialog').css({display:'block'});
		na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].onresize();
		jQuery('#siteContent__dialog').css({display:scv});
		//alert ('-2-');
		*/
		
		//na.sp.containerSizeChanged (jQuery('#siteContent__scrollpane')[0]);
		
		//na.vcc.setOptions('saAppMenu__app_2D_cardgame_tarot',{ level_0 : { orientation : 'horizontal', animspeed : 400 } });
		
		//na.vcc.setOptions('contentMenu',{ level_0 : { orientation : 'horizontal', animspeed : 400 } });

		//na.vcc.init(document.body, na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].vividControlsInitialized);
		
		jQuery('#appGame').css({display:'none',visibility:'visible'}).fadeIn('slow');
		
		na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.loadedIn['#siteContent'].settings.initialized = true;
        
        na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.mode = 'doit';
        na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].showContentPage('initial');

        /*
		setTimeout(function() {
			/*na.m.fireAppEvent ({
				divName : '#siteContent',
				eventName : 'onresize'
			});
			//debugger;* /
			setTimeout (function() {
				jQuery('#siteContent, #siteContent__scrollpane__container').css ({
					position:'absolute',
					top : jQuery('#siteContent__contentDimensions')[0].offsetTop,
					left : jQuery('#siteContent__contentDimensions')[0].offsetLeft,
					width : jQuery('#siteContent__contentDimensions')[0].offsetWidth,
					height : jQuery('#siteContent__contentDimensions')[0].offsetHeight
				});
			}, 1000);
		}, 1000);
        */
	},
	
	/*
	vividControlsInitialized : function () {
		na.vcc.settings['dialog-content'].afterResize = function () {
			var 
			d = $('#dialog-content__dialog'),
			c = $('#dialog-content__scrollpane__container'),
			s = $('#dialog-content__scrollpane'),
			cs = $('#dialog-content__scrollpane__container, #dialog-content__scrollpane'),
			h = $('.animatedDialog__header');

			$('#dialog-content').css ({
				top : 0,
				left : 0,
				width : 'auto',
				height : 'auto',
				position:'relative'
			});
			$('#dialog-content__scrollpane__container').css({
				position:'absolute', 
				top: 0,
				left:0,
				width:'100%',
				height:'100%',
				zIndex:5500
			});
			
			na.sp.containerSizeChanged($('#dialog-content__scrollpane')[0], true);
		}	

		na.desktop.init({  // will bind itself into window.onresize() as well
			currentConfigName : 'default',
			bestDesktopConfig : 'default',
			configUpdate : configUpdate,
			stayOnCurrentConfiguration : true,
			callback : function () {
				//na.menu.onresize('saAppMenu__app_2D_cardgame_tarot');
			}
		});

		//$('#dialog-content').css ({top:0, left:0, overflow:'hidden',width:'100%',height:'100%'});
		$('#siteLoadingMsg,#siteBootLog').fadeOut ('normal');
		var ids = '#socializethis';
		for (var id in na.desktop.options) {
			if (id!=='desktop') {
				if (ids!='') ids+=', ';
				ids += id+', '+id+'__dialog';
			}
		}
		ids += ', #siteBackground, #dialog-content__dialog, #dialog-ads__dialog';

		$(ids).css({display:'block',opacity:1,visibility:'visible'});//.fadeIn('slow', function () {

		//$('#aswift_0_anchor, #aswift_1_anchor').css({visibility:'visible',opacity:1});
		na.m.desktopIdle() = true;
		//na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].showContentPage (1);
		na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].transformLinks (document.body);
		window.onresize = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].onresize;
		na.desktop.resize({callback:na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].onresize});
		//na.menu.onresize ('saAppMenu__app_2D_cardgame_tarot');
		na.m.settings.settings.visible = {
			'#dialog-content' : true,
			'#dialog-ads' : true
		};
	},*/
	
	onresize : function () {
		//alert (556);
		if (na.m.desktopIdle()) na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].showContentPage();
		/*
		$('#siteBackground').css ({
			width : $(window).width() + 'px',
			height : $(window).height()+ 'px'
		});
		*/

		/*
		var
		parent = jQuery('#siteContent')[0],
		ph = ((jQuery(parent).height() )), // 90 is hardcoded from cssToExtrapolate
		pw = ((jQuery(parent).width() ));
		
		jQuery('#card').css({display:(pw>na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.zoomCardMinimumContainerWidth?'none':'block')});

		switch (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.mode) {
			case 'initial' : 
				var
				w = pw * 0.98;
				na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].resizeCards(w, w);
				break;
			case 'cardDisplay':
				var
				w = pw * 0.98;
				
				na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].resizeCards(w, w);
				
				break;
		}*/
	},
	
	bindEvents : function () {
		jQuery('.backOfCard').click(na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].onclick_card_back);
		jQuery('.frontOfCard').click(na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].onclick_card_front);
	},
    
	/*
    resizeIntro : function () {
                var 
                w = 'calc(100% - '+(
                    ( parseInt($('#cards').width())+30 )
                    + ( parseInt($('#siteContent .vividDialogContent').css('margin')) * 2 )
                    + ( parseInt($('#siteContent p').css('padding')) * 2 )
                )+'px)',
                w2 = 'calc(100% - '+(
                    ( parseInt($('#cards').width())+30 )
                    + ( parseInt($('#siteContent .vividDialogContent').css('margin')) * 2 )
                    + ( parseInt($('#siteContent p').css('padding')) * 2 )
                    - 16
                )+'px)';
                $('#intro h1').css({width:w});
                $('#intro p').css({width:w2});//animate({width:w, duration:na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.animateCardsDuration});
    },*/

	showContentPage : function (mode, el, evt) {
		var modeUnchanged = false, elUnchanged = false;
		if (mode===undefined) {
			mode = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.mode;
            modeUnchanged = true;
		} else {
			if (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.mode == mode) {
				modeUnchanged = true;
			} else {
				na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.mode = mode;
			}
		};
        
        //if (modeUnchanged && elUnchanged) {
        na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].resizeCards();
        //} else 
        //jQuery('#cards').fadeIn(150, function () {
            //na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].resizeIntro();
            //setTimeout (function() {
                //na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].resizeIntro();
            //}, na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.animateCardsDuration + 500);
            
            if (el===undefined) {
                el = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.el;
                if (!evt) evt = {};
                evt.currentTarget = el;
            } else {
                if (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.el == el) {
                    elUnchanged = true;
                } else {
                    na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.el = el;
                }
            };
            
        //});
		
		if (modeUnchanged && elUnchanged) {
			return false;
		};
		
		var
		cardIdx = parseInt(jQuery(el).attr('idx')),
		cr = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.reading,
		parent = jQuery('#siteContent')[0],
		scrollpane = jQuery('#siteContent__scrollpane')[0];
		
        /*
		jQuery(parent).css ({
			left : jQuery('#siteContent__CSS3')[0].offsetLeft,
			top : jQuery('#siteContent__CSS3')[0].offsetTop,
			width : jQuery('#siteContent__CSS3')[0].offsetWidth,
			height : jQuery('#siteContent__CSS3')[0].offsetHeight
		});
        */
			
		
		//jQuery('#siteContent__scrollpane').css({height:'auto'});
		//na.sp.containerSizeChanged (parent, true);
		/*
		ph = jQuery(parent).height(), 
		pw = jQuery(parent).width(),
		w = (
			jQuery(parent).width()
			- (
				jQuery(parent).width()>na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.zoomCardMinimumContainerWidth
				? 0
				: jQuery('#card').width()
			)- 20
		) * na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.maxWidthFactor;
		*/

		//alert (w);
		switch (mode) {
			case 'initial':
				na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].resizeCards();
				delete na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.cardIdx;
                var
				w = 'calc(100% - '+(
					( parseInt($('#cards').width())+30 )
					+ ( parseInt($('#siteContent .vividDialogContent').css('margin')) * 2 )
					+ ( parseInt($('#siteContent p').css('padding')) * 2 )
					+ 200
				)+'px)',
				w2 = 'calc(100% - '+(
					( parseInt($('#cards').width())+30 )
					+ ( parseInt($('#siteContent .vividDialogContent').css('margin')) * 2 )
					+ ( parseInt($('#siteContent p').css('padding')) * 2 )
					+ 200
				)+'px)';

				jQuery('#readingExplanation').fadeOut (500, function() {
					$('#cardSpotInReadingTitle h1, #intro p').css({width:w});
					$('#cardExplanation p, #readingExplanation p').css({width:w2});
					$('div.text p').addClass('backdropped');
				});

				jQuery('#cardZoom1').attr('idx',cardIdx);//.click(na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].onclick_card_front);

                if (!modeUnchanged) {
                    jQuery('.text, #card').fadeOut(500);
                    jQuery('#intro, #cards').fadeIn(500);
                }
				break;

			case 'cardDisplay':
				var
				parent = jQuery('#siteContent')[0],
				hZoomedCard = (jQuery(parent).height() ) * 0.40,
				htmlCard = '<img id="cardZoom1" src="'+el.src.replace('/r','/')+'" style="position:relative;float:right;padding-right:0px;height:'+(Math.round(hZoomedCard*1.3))+'px;"/>',
				title = (
					typeof cr.cards[cardIdx].title=='number'
					? 'Card '+cr.cards[cardIdx].title
					: cr.cards[cardIdx].title
				),
				isReverse = el.src.match(/\/r\d/);

                if (!modeUnchanged) {
                    jQuery('#cards').fadeIn(500);
                    if (jQuery('#intro').css('display')!=='none') jQuery('#intro').slideUp(200).fadeOut(500);
                }
				//jQuery('#card').html(htmlCard).css({width:'auto',height:hZoomedCard});
				
				
				/*
				if (
					jQuery(parent).width()>na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.zoomCardMinimumContainerWidth
				) {
					jQuery('#saZoomCard').fadeOut(500);
				} else {
					jQuery('#saZoomCard').fadeIn(500);
				};
				*/
                
                if (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.cardIdx !== cardIdx) {
                    jQuery('#cardSpotExplanation').fadeOut(500, function(){
                        jQuery('#cardSpotExplanation').html (cr.cards[cardIdx].readingSpotExplanation).fadeIn(500);
                    });
                    
                    jQuery('#cardSpotInReadingTitle').fadeOut(500, function() {
                        jQuery('#cardSpotInReadingTitle').html('<h1 class="nicerEnterprises_tarot_cardExplanation" style="color:lime;display:block;">'+title+'</h1>').fadeIn(500);
                    });
                    
                    jQuery('#cards').fadeIn(150);
                    jQuery('#cardExplanation').slideUp(250).fadeOut(250, function(){
						setTimeout(function() {
							var
							html = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].cardExplanation(cr, cardIdx, isReverse),
							w = 'calc(100% - ' + (
								( parseInt($('#cards').width())+30 )
								+ ( parseInt($('#siteContent .vividDialogContent').css('margin')) * 2 )
								+ ( parseInt($('#siteContent p').css('padding')) * 2 )
								+ 200
							)+'px)',
							w2 = 'calc(100% - ' + (
								( parseInt($('#cards').width())+30 )
								+ ( parseInt($('#siteContent .vividDialogContent').css('margin')) * 2 )
								+ ( parseInt($('#siteContent p').css('padding')) * 2 )
								+ 200
							)+'px)';

							$('#cardExplanation').html(html);
							setTimeout (function() {
								$(
									'#cardSpotInReadingTitle h1, '
									+'#cardExplanation h1, #cardExplanation h2, #cardExplanation h3, '
									+'#readingExplanation h1, #readingExplanation h2, #readingExplanation h3'
								).css({width:w});
								$('#cardExplanation p, #readingExplanation p').addClass('backdropped').css({width:w2});

								$('#cardExplanation').slideDown(250).fadeIn(250);/*, function() {
									$('#appGame').fadeIn(330);
								});*/
							}, 200);
						}, 500)
                    });
                    
                    jQuery('#readingExplanation').fadeOut (500, function() {
                        jQuery('#readingExplanation').html(cr.heading).fadeIn(500);
                    });
                    
                    jQuery('#cardZoom1').attr('idx',cardIdx);//.click(na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].onclick_card_front);

                }
                    na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].resizeCards();
                
                $('#appGame').css({display:'block'});
                na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.cardIdx = cardIdx;
                
				break;
			case 'zoomCard':
				if (!modeUnchanged) {
                    jQuery('.text, #card, #cards').fadeOut(500);
                }
				na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].zoomCard(evt, modeUnchanged);
                delete na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.cardIdx;
				break;
		};
        na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.mode = mode;

		
		//jQuery('#siteContent').css({position:'relative',height:'auto'});
		
		/*
		if (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.resizeTimer) clearTimeout(na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.resizeTimer);
		na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.resizeTimer = setTimeout(function() {
			if (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.mode!=='zoomCard') na.sp.onresize(jQuery('#siteContent__scrollpane')[0], true);
			delete na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.resizeTimer;
		}, 200);
		*/
		
        /*
		jQuery(parent).css ({
			left : jQuery('#siteContent__CSS3')[0].offsetLeft,
			top : jQuery('#siteContent__CSS3')[0].offsetTop,
			width : jQuery('#siteContent__CSS3')[0].offsetWidth,
			height : jQuery('#siteContent__CSS3')[0].offsetHeight
		});
        */
			
		
	},
	
	onclick_card_back : function(evt) {
		var
		el = evt.currentTarget,
		elsp = el.id.split('__'),
		idx = elsp[1];

        na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.cardHighestZindex++;
		jQuery(el).parent().css({zIndex:na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.cardHighestZindex});

		jQuery('#backOfCard__'+idx).css({display:'none'});
		jQuery('#frontOfCard__'+idx).css({display:'block'});
		//if (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.mode=='cardDisplay') {
			evt.currentTarget = jQuery('#frontOfCard__'+idx)[0];
			//alert ('t1');
			na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].onclick_card_front (evt);
            na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.mode = 'cardDisplay';
		//}
	},
	
	onclick_card_front : function (evt) {
		var
		el = (
			evt.currentTarget.link
			? evt.currentTarget.link
			: evt.currentTarget
		),				
		cardIdx = parseInt(jQuery(el).attr('idx')),
		cr = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.reading,
		isReverse = el.src.match(/\/r\d/);
        
		na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.cardHighestZindex++;
		jQuery(el).parent().css({zIndex:na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.cardHighestZindex});
		
		//if (typeof na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.lastCardViewed == 'undefined') na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.lastCardViewed = cardIdx;
		if (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.lastCardViewed !== cardIdx) {
			na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.lastCardViewed = cardIdx;
			na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.mode = 'initial';
			//na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].showContentPage('initial', el);
		}
		
		switch (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.mode) {
			case 'initial':	na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].showContentPage ('cardDisplay', el, evt);	break;
			case 'cardDisplay':	na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].showContentPage ('zoomCard', el, evt); break;
			case 'zoomCard': na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].showContentPage ('cardDisplay', el, evt); break;
		}
	},
	
	cardExplanation : function (cr, cardIdx, isReverse){
		var
		r = cr.cards[cardIdx].explanation;
		search = (
			isReverse 
			? '<h3>Negative</h3>'
			: '<h3>Positive</h3>'
		),
		p1 = r.indexOf(search);
		if (p1!==-1) {
			var
			p2 = r.indexOf('</p>', p1);
			if (p2!==-1) {
				r = 
				r.substr(0, p1).replace('<h1>', '<h1 style="color:yellow">') 
				+ '<div style="color:lime;font-weight:bold;">'
				+ r.substr(p1,p2-p1)
				+ '</div>'
				+ r.substr(p2+4, r.length-p2-4)
			}
		};
		return r.replace('<h2>Explanation</h2>','');
	},
	
	zoomCard : function (evt, modeUnchanged) {
		var
		link = evt.currentTarget,
		url = link.src.replace('/r','/'),
		html = '<img id="saZoomImg" src="'+url+'" style="display:none;"/>',
		img = document.createElement ('img');
		
		var resize = function () {
			jQuery('#saAppMenu__app_2D_cardgame_tarot, #appGame, #saZoomImg').fadeOut(500);
			setTimeout(function() {
				jQuery('#saZoomCard').css({display:'block',width:'100%',height:'100%'}).html(html);
				jQuery('#saZoomImg')[0].link = link;
				jQuery('#saZoomImg').click(function(evt) {
					jQuery('#saZoomCard').fadeOut('normal', function() {
						jQuery('#saAppMenu__app_2D_cardgame_tarot, #appGame').css({display:'block'});
						//jQuery('#siteContent').css({height:'auto'});
						na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].showContentPage('cardDisplay');
						//na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].onclick_card_front(evt);
						//na.sp.containerSizeChanged($('#siteContent__scrollpane')[0], true);
					});;
				});
				

				//jQuery('#siteContent').css({height:jQuery('#siteContent__scrollpane__container').height()});
				if (!modeUnchanged) setTimeout (function(){
					//jQuery('#siteContent').css({height:jQuery('#siteContent__scrollpane__container').height()});
					jQuery('#saZoomImg').css({display:'block',visibility:'hidden', width:'',height:'100%'});
					if (jQuery('#saZoomImg')[0].offsetWidth > jQuery('#siteContent')[0].offsetWidth) {
						jQuery('#saZoomImg').css({width:'100%',height:''});
					};
					jQuery('#saZoomImg').css({display:'none',visibility:'visible',opacity:1}).fadeIn(500);
				}, 10);
			},505);
			////debugger;
		};
		
		img.onload = resize;
		img.src = url;
		
		if (img.complete || img.readyState===4) {
			resize();
		};
		
		
		//jQuery('#saZoomImg').load(resize);
		//resize();
	},

	transformLinks : function (rootElement) {
		var debugLevel = 3000;
		$('a', rootElement).each (function (idx) {
			if (!this.href.match('javascript:')) {
				var o = this.href;
				//this.href = this.href.replace("deck'Original-Rider-Waite',reading'3-Cards'", "");
				//urlpa = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getURLparameters(this.href);
				//urlp = (urlpa===null?[]:urlpa);
				
				p1 = this.href.indexOf('deck\'');
				p2 = this.href.indexOf('\'', p1 + 5);
				deck = this.href.substr(p1+5,p2-p1-5);
				
				p1 = this.href.indexOf('reading\'');
				p2 = this.href.indexOf('\'', p1 + 8);
				reading = this.href.substr(p1+8,p2-p1-8);
				
				var urlp = [
					'',
					deck,
					reading
				];
				
				//console.log ('na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].transformLinks',this,urlpa);
				if (this.className.match('selectDeck')) {
					this.href = 'javascript:na.apps.loaded[\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot\'].selectDeck(\'' + urlp[1] + '\');';
				} else if (this.className.match('selectReading')) {
					this.href = 'javascript:na.apps.loaded[\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot\'].selectReading(\'' + urlp[2] + '\');';
				} else {
				}
				//if (o!=this.href) na.m.log (debugLevel, 'na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].transformLinks: changing link with class="'+this.className+'" and href="'+o+'" to href="'+this.href+'".');
			}
		});
	},
	
	getNewReading : function (newURL) {
        na.analytics.logMetaEvent ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot : getNewReading() newURL='+newURL);
		newURL = (typeof newURL==='string'?newURL.replace('http://nicer.app/',''):undefined);
		
		var u = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getURLparameters( (typeof newURL=='string' ? newURL : null) );

		window.top.na.serviceLog.entries.makeNew ('apps__tarot__contentLoad', {
			deck : u[1],
			reading : u[2],
			majors : u[3]
		});
		
		var ajaxCommand = {
			type : 'GET',
			url : na.m.globals.urls.app+'apps//NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite/ajax_get_reading.php',
			data : {
				deck : u[1],
				reading : u[2],
				majors : u[3]
			},
			success : function (dataAsText, textStatus) {
				var data = eval('(' + dataAsText + ')');
				na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.reading = data.reading;
				$('#cards').fadeOut (500, function() {
					$('#cards').replaceWith (data.html_cards);//.css({display:'none',opacity:0}).fadeIn(500);
					$('#deckExplanation').html (data.deck);
					$('#readingExplanation').html (data.reading.heading);
					na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].showContentPage('initial');
					na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].bindEvents();
					na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].resizeCards();
					window.top.na.m.globals.urls.current = na.m.globals.urls.app + newURL;
					window.top.na.s.c.setVisible ([
						{ element : '#siteContent', visible : true }
					]);
					//na.m.googleAnalyticsMakeHit();
				});
			}
		};
		na.m.log (1, 'na.apps.loaded[\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot\'].getNewReading : Getting new reading "'+u[2]+'" for deck "'+u[1]+'".');
		jQuery.ajax(ajaxCommand);
	},
	
	selectReading : function (newReading) {
		var urlp = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getURLparameters(location.href);
		urlp[2] = newReading;
		
		if (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.nested) {
			window.top.History.pushState (null, na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getTitle(urlp), '/tarot' + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getURL(urlp)+'');
		} else {
			window.History.pushState (null, na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getTitle(urlp), na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.rootURL + '(' + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getURL(urlp)+')');
		}
	},
	
	selectDeck : function (newDeck) {
		////debugger;
		var urlp = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getURLparameters(location.href);
		urlp[1] = newDeck;

		if (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.nested) {
			//console.log ('selectDeck 4', na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.rootURL + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getURL(urlp));
			//na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getNewReading(na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.rootURL + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getURL(urlp));
			window.top.History.pushState (null, na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getTitle(urlp), '/tarot' + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getURL(urlp)+'');
		} else {
			//console.log ('selectDeck 5', na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getTitle(urlp), na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.rootURL + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getURL(urlp));
			window.History.pushState (null, na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getTitle(urlp), na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.rootURL +'(' + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getURL(urlp)+')');
		}
	},
	
	setOptions : function (optName, optValue) {
		var urlp = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getURLparameters();
		if (urlp===null) urlp = [];
		if (optName=='majors') {
			urlp[3] = optValue;
		};
		if (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.nested) {
			//na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getNewReading(na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.rootURL + '/' + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getURL(urlp));
			window.top.History.pushState (null, na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getTitle(urlp), na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.rootURL + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getURL(urlp));
		} else {
			window.History.pushState (null, na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getTitle(urlp), na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.rootURL + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getURL(urlp));
		}
	},
	
	appURLdecode : function (str) {
		return (typeof str==='string' ? str.replace('-',' ') : '');
	},
	
	getTitle : function (urlp) {
		return 'Free Tarot Reading (Deck : '+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].appURLdecode(urlp[1])+' - Reading : '+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].appURLdecode(urlp[2]) + ') ' + (urlp[3] ? ' (Major cards only)' : '');
	},
	
	getURL : function (urlp) {
		//return 'deck/' + urlp[1] + '/reading/' + urlp[2] + (urlp[3]?'/majors':'/');
		return ('(deck\''+ urlp[1]+'\',reading\''+ urlp[2]+'\')');//+urlp[3] (?",majors\'only\')":')');
	},
	
	getURLparameters : function (url) {
		p1 = url.indexOf('deck\'');
		p2 = url.indexOf('\'', p1 + 5);
		deck = url.substr(p1+5,p2-p1-5);
		
		p1 = url.indexOf('reading\'');
		p2 = url.indexOf('\'', p1 + 8);
		reading = url.substr(p1+8,p2-p1-8);
		
		return [
			'',
			deck,
			reading
		];
	},
	
	getNewShorthandLinkID : function () {
	// thanks :), http://stackoverflow.com/questions/1349404/generate-a-string-of-5-random-characters-in-javascript
		var text = "";
		var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

		for( var i=0; i < 5; i++ )
			text += possible.charAt(Math.floor(Math.random() * possible.length));

		return text;
	},
	
	getCurrentStateShorthandLinkID : function () {						
		return na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getNewShorthandLinkID();
	},
	
	/*
	addOrUpdateSocialShareToolbar_currentState_openInNewTab : function (href, shorthandLinkID) {
		var ajaxCommand = {
			type : 'GET',
			url : na.m.globals.urls.app + 'ajax_storeState.php',
			data : {
				href : href,
				shorthandLinkID : shorthandLinkID
			},
			success : function (data, ts) {
				
			}
		};
		jQuery.ajax(ajaxCommand);
	},
	
	addOrUpdateSocialShareToolbar_currentState_getHREF : function (href, shorthandLinkID) {
		return 'javascript:na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].addOrUpdateSocialShareToolbar_currentState_openInNewTab("'+href+'", "'+shorthandLinkID+'");';
	},

	addOrUpdateSocialShareToolbar_currentState : function () {
		var 
		shorthandLinkID = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].getCurrentStateShorthandLinkID(),
		url = window.location.href+'#'+shorthandLinkID,
		host =  window.location.hostname,
		title = $('title').text();
		
		title = escape(title); //clean up unusual characters
	 
		var twit = 'http://twitter.com/home?status='+title+'%20'+url;
		var facebook = 'http://www.facebook.com/sharer.php?u='+url
		var digg = 'http://digg.com/submit?phase=2&url='+url+'&amp;title='+title;
		var stumbleupon = 'http://stumbleupon.com/submit?url='+url+'&amp;title='+title;
		var buzz = 'http://www.google.com/reader/link?url='+url+'&amp;title='+title+'&amp;srcURL='+host;
		var delicious  = 'http://del.icio.us/post?url='+url+'&amp;title='+title;
		twit = addOrUpdateSocialShareToolbar_currentState_getHREF (twit, shorthandLinkID);
		facebook = addOrUpdateSocialShareToolbar_currentState_getHREF (facebook, shorthandLinkID);
		digg = addOrUpdateSocialShareToolbar_currentState_getHREF (digg, shorthandLinkID);
		stumbleupon = addOrUpdateSocialShareToolbar_currentState_getHREF (stumbleupon, shorthandLinkID);
		buzz = addOrUpdateSocialShareToolbar_currentState_getHREF (buzz, shorthandLinkID);
		delicious = addOrUpdateSocialShareToolbar_currentState_getHREF (delicious, shorthandLinkID);
	 
		//var tbar = '<a id="socializethis"><span>Share<br /><a  href="#min" id="minimize" title="Minimize"> <img src="'+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.rootURL+'/NicerAppWebOS/lib/jquery.socialShareToolbar/minimize.png" /> </a></span><a id="sicons">';
		var tbar = '<table><tr><td>Share</td>';
		tbar += '<td><a  href="'+twit+'" target="_new" id="twit" title="Share on twitter"><img src="'+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.rootURL+'/NicerAppWebOS/lib/jquery.socialShareToolbar/twitter.png"  alt="Share on Twitter" width="32" height="32" /></a></td>';
		tbar += '<td><a  href="'+facebook+'" target="_new" id="facebook" title="Share on Facebook"><img src="'+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.rootURL+'/NicerAppWebOS/lib/jquery.socialShareToolbar/facebook.png"  alt="Share on facebook" width="32" height="32" /></a></td>';
		tbar += '<td><a  href="'+digg+'" target="_new" id="digg" title="Share on Digg"><img src="'+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.rootURL+'/NicerAppWebOS/lib/jquery.socialShareToolbar/digg.png"  alt="Share on Digg" width="32" height="32" /></a></td>';
		tbar += '<td><a  href="'+stumbleupon+'" target="_new" id="stumbleupon" title="Share on Stumbleupon"><img src="'+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.rootURL+'/NicerAppWebOS/lib/jquery.socialShareToolbar/stumbleupon.png"  alt="Share on Stumbleupon" width="32" height="32" /></a></td>';
		tbar += '<td><a  href="'+delicious+'" target="_new" id="delicious" title="Share on Del.icio.us"><img src="'+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.rootURL+'/NicerAppWebOS/lib/jquery.socialShareToolbar/delicious.png"  alt="Share on Delicious" width="32" height="32" /></a></td>';
		tbar += '<td><a  href="'+buzz+'" target="_new" id="buzz" title="Share on Buzz"><img src="'+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.rootURL+'/NicerAppWebOS/lib/jquery.socialShareToolbar/google-buzz.png"  alt="Share on Buzz" width="32" height="32" /></a></td></tr></table>';
	 
		// Add the share tool bar.
		if ($('#socializethis').length==0) $('body').append('<a id="socializethis_currentState" style="visibility:hidden;width:275px;height:40px;vertical-align:middle;">' + tbar+ '</a'); else $('#socializethis').html(tbar);
	},
	*/
	
	resizeCards : function (maxWidth, maxHeight) {
		var 
		cr = $.extend ( {}, na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].settings.reading),
		optimal = null,
		fx = 0,
		fy = 0,
		done = false,
		cards = [],
		shrunk = false,
		shrinkage = {
			h : 100,
			w : 100
		},
		jqueryCards = jQuery('.card'),
		parent = jQuery('#siteContent .vividDialogContent')[0],
		ph = jQuery(parent).height(), 
		pw = jQuery(parent).width(), 
		wAndH = ( // DEFAULT width-and-height made available for #cards
			jQuery(parent).width()
			- (
				jQuery(parent).width()>na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.zoomCardMinimumContainerWidth
				? 0
				: jQuery('#card').width()
			)- 20
		);// * na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.maxWidthFactor;
        
        if (!cr.theme) return false; // not ready yet.
        
        if (pw > 1500) {
            if (wAndH > 1000) wAndH = 1000;
        } else if (pw > 800) {
            if (wAndH > 550) wAndH = 550;
        } else if (pw > 500) {
            if (wAndH > 350) wAndH = 350;
        } else {
            if (wAndH > 250) wAndH = 250;
        }
        
		while (!done) {
			var
			overlap = false,
			w1 = (
				optimal === null 
				? cr.theme.cardWidth 
				: optimal.w
			),
			h1 = (
				optimal === null
				? cr.theme.cardHeight
				: optimal.h
			);
			
			// set fx and fy to the max dimensions as in /apps//NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite/tarot_reading.*.json
			jQuery('.card').each(function (idx, el) {
				var
				cardIdxSplit = el.id.split('__'),
				cardIdx = parseInt(cardIdxSplit[1]),
				t = (
					optimal === null 
					? cr.cards[cardIdx].top
					: (cr.cards[cardIdx].top * w1) / cr.theme.cardWidth
				),
				r = (
					optimal === null
					? cr.cards[cardIdx].right
					: (cr.cards[cardIdx].right * w1) / cr.theme.cardWidth
				),
				w = w1,
				h = h1,
				ch = t + h,
				cw = r + w;
				
				if (fx < cw) fx = cw;
				if (fy < ch) fy = ch;
			});
			
			// set in ph and pw what space is available on the screen, and what to use of it.
			var
			parent = jQuery('#siteContent')[0],
			ph = wAndH - shrinkage.h, //((jQuery(parent).height() ) * 0.5) - shrinkage.h, // 90 is hardcoded from cssToExtrapolate
			pw = wAndH - shrinkage.w; //((jQuery(parent).width() ) * 0.99) - shrinkage.w;
			
			//if (ph > 700) ph = 400;
			//if (pw > 700) pw = 600;
			
			if (maxWidth) pw = maxWidth - shrinkage.w;
			if (maxHeight) ph = maxHeight - shrinkage.h;
			
		
			// calculate cards' new positions and dimensions with current optimal values
			jQuery('.card').each(function (idx, el) {
				var
				cardIdxSplit = el.id.split('__'),
				cardIdx = parseInt(cardIdxSplit[1]),
				t = (
					optimal === null 
					? cr.cards[cardIdx].top
					: (cr.cards[cardIdx].top * w1) / cr.theme.cardWidth
				),
				r = (
					optimal === null
					? cr.cards[cardIdx].right
					: (cr.cards[cardIdx].right * w1) / cr.theme.cardWidth
				),
				w = w1,
				h = h1;
				
				if (ph>pw) { 
					// long live math teachers and the crossing multiplication trick on paper
					var	r = ((pw * r) / fx), t = (ph * t) / fy, w = (pw * w) / fx, h = (w * h) / w1;
				} else {
					// BUGzzzz
					var	
					t = (ph * t) / fy, 
					h = (ph * h) / fy, 
					w = (w * h) / h1,	
					r = (pw * r) / fx;
				};
				
				var	css = {	top : t, right : r,	width : w, height : h, duration : na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'].globals.animateCardsDuration };
				cards[cardIdx] = css;
			});
			
			// if needed, re-adjust var optimal for overlapping cards
			for (var cardIdx=0; cardIdx<jqueryCards.length; cardIdx++) {
				var
				css = cards[cardIdx];
				
				//if (optimal === null) optimal = { w : css.width, h : css.height };
				
				for (var cardIdx2=0; cardIdx2<jqueryCards.length; cardIdx2++) {
					var 
					css2 = cards[cardIdx2];
					
					// check for overlaps
					if (!overlap && cardIdx2 !== cardIdx) {
						overlap = (
							(css.top >= css2.top && css.top <= css2.top + css2.height)
							&& (css.right >= css2.right - 5 && css.right <= css2.right + css2.width + 10)
						);
					}
				};
			};

			// overlaps can't always be prevented (yet), and perhaps shouldn't be eliminated entirely (onclick = change z-index of card)
			var 
			fx = 0,
			fy = 0;
			for (var j=0; j<cards.length; j++) {
				
				var 
				card = cards[j],
				ch = card.top + card.height,
				cw = card.right + card.width;
				
				if (fx < cw) fx = cw;
				if (fy < ch) fy = ch;
			};
			if (fx < 50 && fy < 50) overlap = false;

			
			//console.log ('[1] fx='+fx+', fy='+fy+', overlap='+(overlap?'true':'false'));
			if (overlap) {
				shrinkage.h += 10;
			} else {
				done = true;
			}
		} // while (!done)
		
		
		// adjust css.right
		var 
		newSmallestR = 15,
		smallestR = 10000,
		smallestCardIdx = null;
		
		for (var cardIdx=0; cardIdx<jqueryCards.length; cardIdx++) {
			var c = cards[cardIdx];
			if (c.right < smallestR) {
				smallestR = c.right;
				smallestCardIdx = cardIdx;
			}
		};
		for (var cardIdx=0; cardIdx<jqueryCards.length; cardIdx++) {
			var c = cards[cardIdx];
			c.right = c.right - (smallestR - newSmallestR);
		};

		// apply found position()s and dimensions of cards to cards
		jQuery('.card').each(function(idx,el){
			var
			cardIdxSplit = el.id.split('__'),
			cardIdx = parseInt(cardIdxSplit[1]),
			css = $.extend ( {}, cards[cardIdx]);

			jQuery(el).animate(css);
			delete css.top;
			delete css.right;
			////debugger;
			if (jQuery(el.children[0]).css('display')=='block') {
				jQuery(el.children[0]).animate(css);
				jQuery('img', el.children[0]).stop().animate(css);
			};
			if (jQuery(el.children[1]).css('display')=='block') {
				jQuery(el.children[1]).animate(css);
				jQuery('img', el.children[1]).stop().animate(css);
			}
			
		});
		
		
		var 
		fx = 0,
		fy = 0;
		
		jQuery('.card').each(function (idx, el) {
			var 
			cardIdxSplit = el.id.split('__'),
			cardIdx = parseInt(cardIdxSplit[1]),
 			css = $.extend ( {}, cards[cardIdx]),
			ch = css.top + css.height,
			cw = css.right + css.width;
			////debugger;
			
			if (fx < cw) fx = cw;
			if (fy < ch) fy = ch;
		});
		
		////debugger;
		var cw1 = fx + (pw > 1500 ? 60 : pw > 800 ? 30 : pw > 500 ? 20 : 10);
		jQuery('#cards').css({
			right : 10,
			width : cw1,
			height : fy + 10
		});
        //jQuery('#siteContent').css({ top:0, left:0, width : jQuery('#siteContent__scrollpane')[0].offsetWidth - jQuery('#siteContent__scrollpane__sliderbar__ver')[0].offsetWidth - 5, height : 'auto' });
        //na.sp.containerSizeChanged($('#siteContent__scrollpane')[0], true);
		
	}
};	

