na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'] = {
	about : {
		whatsThis : 'Application code for this news app (RSS reader)',
		copyright : 'Copyrighted (c) 2011-2022 by Rene AJM Veerman, Amsterdam, Netherlands',
		license : 'https://nicer.app/LICENSE.txt',
		firstCreated : '2018',
		lastModified : '2022-10-24(Monday)',
        version : '3.1.1'
	},
	globals : {
        dtIntervalSeconds : 60,
        contentOpacity : 0,
        contentBorder : '0px solid black',
        contentBoxShadow : '0px 0px 0px 0px rgba(0,0,0,0)',
        readHistory_numHours : 1,
    },
	settings : {
		loadedIn : {
			'#siteContent' : {
				settings : {
					initialized : false 
				},
				saConfigUpdate : function (settings) {
					nicerapp.site.globals.desktop.configUpdate();
				},

                mergeMenu : function () {
                        var
                        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
                        loadedIn = s.loadedIn['#siteContent'],
                        mainmenu = $('#newsApp_mainmenu')[0];
                        //na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].transformLinks ($('#newsApp_mainmenu')[0]);
                        
                        if (!mainmenu.linksTransformedAlready) {
                            na.menu.preprocess('newsApp_mainmenu');
                            window.top.na.s.c.transformLinks ($('#newsApp_mainmenu')[0]);
                            mainmenu.linksTransformedAlready = true;
                        }
                        $('#newsApp_mainmenu').css({display:'none'});
                        na.menu.merge ('siteMenu', 'na.site.code.transformLinks', '-saLinkpoint-appMenu', $('#newsApp_mainmenu')[0], undefined, false);
                        na.desktop.onresize({reloadMenu : true});
                },
                
				onload : function (settings) {
                    $('.lds-facebook').fadeIn('normal');
                    na.m.waitForCondition ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news start', na.m.HTMLidle, function () {
                        var
                        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
                        loadedIn = s.loadedIn['#siteContent'];
                        
                        s.settings_onload = settings;
                        settings.onHold = true; // signals a wait for na.site.loadTheme() has started
                        na.site.settings.current.app = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news';

                        $('#siteContent__header').fadeIn('normal', function () {
                            $('#siteContent__header').css({display:'flex'});
                        });


                        na.m.waitForCondition('news app : siteContent dialog reappearance', function () {
                            return (
                                /*$('#jsPageSpecific').length > 0
                                &&*/ $('#siteContent__content').length > 0
                                //&& na.m.settings.initialized.site
                            );
                            //return true;
                        }, function () {
                            na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.loadedIn['#siteContent'].settings.initialized = true;
                            na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.loadedIn['#siteContent'].settings.ready = true;
                            document.addEventListener ('keyup', na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].onkeyup);

                            delete na.m.settings.locked_displayNewNewsItems;
                            
                            na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].nestedStartApp();
na.site.onresize_doContent({});
na.m.preventScreenLock();
                            if (typeof settings.callback=='function') settings.callback('siteContent');
                            na.analytics.logMetaEvent ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news (version '+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].about.version+' is starting.');
                            
                            settings.onHold = false; // signals a wait for na.site.loadTheme() has ended
                            //});
                        }, 30);
                    }, 30);
				},
                ondestroy : function (settings) {
                    var
                    na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;
                    
                    $('#siteContent .vividButton_icon_50x50').each(function(idx,el){
                        var elID = el.id;
                        $('#'+elID).remove();
                        delete na.site.settings.buttons['#'+elID];
                    });
                    
                    clearInterval(c.countDownInterval);
                    clearTimeout (s.refreshTimer);
                    clearTimeout (c.adsTimer1);
                    clearTimeout (c.adsTimer2);
                    clearInterval(c.adsInterval1);
                    clearTimeout (c.timerDisplayNews_loop);
                    clearTimeout (c.timerLoadNews_read_loop);
                    clearTimeout (c.timerDisplayNewsItem);
                    clearTimeout (c.timerDisplayItem);
                    clearTimeout (c.timerAnimateItemIn);
                    clearTimeout (c.timerCheck);
                    clearInterval (c.newItemsInterval);
                    clearInterval (c.intervalMailLogCountdown);

                    document.removeEventListener ('keyup', na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].onkeyup);
                    $(na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.loaderIcon).remove();
                },
				onresize : function (settings) {
					// TODO : what's settings.isManualResize ???
					//if ($('#appGame').css('display')=='none') $('#appGame').fadeIn('slow');
					na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].onresize(settings);
                    if (typeof settings=='object' && typeof settings.callback=='function')
                        settings.callback (settings.callbackParams[0], settings.callbackParams[1]);
                    
				}
			}
		},
		current : {
            displayDebugInfo : false,
            pages : [{
                idx : 0,
                items : []
            }],
            page : 1,
            idx : 0,
            db : [], // as of version 2.1.0, we store news as it comes in via ajax_get_items.php, as a recursive array only.
            its3 : [],
            tries : 0,
            maxDisplayCount : 0,
            onScreen : 30,
            locked : false,
            countDown : 100,
            countDownStr : '',
            readHistory_numHours__multiplier : 1,
            readHistory_numHours__numberOfRecentlyFailedContentLoadAttempts : 0
        }
	},
	
	nestedStartApp : function () {
        na.m.waitForCondition ('siteContent dialog.changeTheme()', function () {
            return $('#siteContent')[0];
        }, na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].startApp, 20);
	},
    
    startApp : function () {
        var
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        dtBegin = new Date(),
        //dtQuit = new Date(dtBegin.getTime() - 1000 * 60 * 60 * 1.5), //(na.m.userDevice.isPhone ? 2.5 : 24.5)),
        urlp = na1.getURLparameters(),
        settings = urlp[0];
        
        na.analytics.logMetaEvent ('start app : applications/2D/news.v'+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].about.version);
        
            $('#siteContent .vividButton_icon_50x50').each(function(idx,el){
                na.site.settings.buttons['#'+el.id] = new naVividButton(el);
            });
        /*
        var loaderIconTheme = na.s.c.globals.loaderIconTheme('appLoading');
        na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.loaderIcon = na.acs.addIcon(
            true, //whether or not to absolutely position
            $('#siteContent__dialog')[0], //parent element to stick icon to (will be positioned in the middle of the parent element)
            180, 180, //width and height in pixels
            loaderIconTheme, //see var theme above
            true //start running immediately
        );*/
        
        //na1.onresize(null, false);

        c.dtCurrent = new Date(new Date().getTime() - (1000 * 60 * 60 * g.readHistory_numHours * c.readHistory_numHours__multiplier)); // read back 1 hour initially
        c.dtEnd = new Date();
        c.lastCurrentGet = new Date();
        

        if (!c.dtStart) c.dtStart = new Date();
        if (!c.intervalMailLogCountdown) c.intervalMailLogCountdown = setInterval (na1.intervalMailLogCountdown, 250);

        if (!c.read_loop_minutesIntoPast) c.read_loop_minutesIntoPast = 60 * g.readHistory_numHours * c.readHistory_numHours__multiplier;
        if (!c.read_loop_millisecondsToDoNext) c.read_loop_millisecondsToDoNext = 1000;
        
        c.firstRun = true;
        c.loads = 0;

        na1.loadNews_read_loop ();

        var
        dc = {},
        di = {},
        params = {
            dc : dc,
            di : di
        };
        na.m.walkArray (db, db, undefined, na1.displayNews_getDisplayCounts, false, params);
        var
        ks = Object.keys(dc),
        total = 0,
        highest = 10;
        ks = ks.sort(function(a,b){ b - a });
        c.displayCounts = '';
        for (k in ks) {
            if (c.displayCounts!=='') c.displayCounts +=', ';
            c.displayCounts += dc[ks[k]];
        };
        c.displayCountInt = parseInt(c.displayCounts)- g.numItemsThatFitScreen;
        c.displayCounts = '<span class="newsApp__header__displayCounts">' + c.displayCountInt + '</span>';
        $('#newsApp_timer').html(
            '<span class="newsApp__header__dateRange">'+na1.formatDateForHeader()+'</span>'
            +' ' +(c.displayCounts)
            +' <span class="newsApp__header__timer" style="opacity:0.0001;width:0px;">'+c.countDownStr+'</span>'
        );

        //na1.onresize();
        if (typeof s.settings_onload.callback=='function') {
            s.settings_onload.callback ('siteContent');
            s.settings_onload.callback_doneAlready = s.settings_onload.callback;
            delete s.settings_onload.callback;
        };
        
        
    },
    
    onkeyup : function (evt) {
        //debugger;
        if (
            (evt.altKey)
            && (evt.key=='n')
        ) na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].gotoNextPage();
        if (evt.key==' ') na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].toggleLock();
    },
    
    viewSearchbar : function (evt) {
        if ($('#div_newsApp_searchbar').css('display')=='block') {
            na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].clearSearch(evt);
        } else {
            $('#div_newsApp_searchbar').css({width:$('#td_newsApp_info').width()}).fadeIn('normal');
            $('#div_newsApp_info').fadeOut('normal');
        }
    },
    
    onSearch : function (evt) {
        var 
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        urlp = na1.getURLparameters(),
        settings = { url : urlp[0] },
        input = $('#newsApp_searchbar');
        s.url = urlp[0];
        
        clearTimeout (c.timerLoadNews_read_loop);
        clearInterval (c.intervalMailLogCountdown);
        clearTimeout (c.timerDisplayNews_loop);
        clearTimeout (c.timerDisplayNews_testing_loop);
        clearTimeout (c.timerDisplayNewsItem);
        clearTimeout (c.timerDisplayItem);
        clearTimeout (c.timerCheck);
        clearTimeout (c.timerAnimateItemIn);
        clearInterval (c.newItemsInterval);
        clearInterval (c.countDownInterval);
        
        c.searchQuery = input.val().replace(' ', '%20');
        c.dtCurrent = new Date(new Date().getTime() - (1000 * 60 * 60 * g.readHistory_numHours * c.readHistory_numHours__multiplier));
        c.dtEnd = new Date();
        c.lastCurrentGet = new Date();
        c.locked = false;
        c.firstRun = true;
        c.tries = 0;
        c.loads = 0;
        c.failedLoads = 0;
        na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.current.db = [];
        
        $('.newsApp__item__outer').remove();
        
        na1.loadNews_searchResults_loop (c.dtCurrent, c.dtEnd, settings);
    },
    
    loadNews_searchResults_loop : function (dtBegin, dtEnd, settings) {
        var 
        fncn = 'na.apps.loaded["applications/2D/news"].loadNews_searchResults_loop(dtBegin,dtEnd,settings)',
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        input = $('#newsApp_searchbar');
        settings.url = input.val().replace(' ', '%20');
        c.searchQuery = input.val().replace(' ', '%20');
        c.lastCurrentGet = new Date();
        c.loads++;
        
        dc = { 0 : 0 },
        di = { 0 : [] },
        params = {
            dc : dc,
            di : di
        },
        ow = ($('#siteContent')[0].offsetWidth)-20,
        oh = $('#siteContent')[0].offsetHeight;
        

        g.searchQuery = settings.url;
        g.numItemsThatFitScreen = Math.round( ( ow / 310 ) * (oh/400) );
        if (g.numItemsThatFitScreen < 5) g.numItemsThatFitScreen = 5; // smartphone bugfix
        // get newest news items
        if (
            !c.lastCurrentGet
            || c.lastCurrentGet.getTime() < dtBegin.getTime() - (1000 * 60 * g.readHistory_numHours * c.readHistory_numHours__multiplier)
        ) {
            c.lastCurrentGet = dtBegin;
            na1.loadNews_searchResults_loop (new Date(dtBegin.getTime() - (1000 * 60 * g.readHistory_numHours * c.readHistory_numHours__multiplier)), dtBegin, settings);
        };        

        na.m.waitForCondition ('news app not loading?', function () {
            return !s.loading
        }, function() {
            s.loading = true;
            var
            dtBeginURL = na1.formatDateForLoading(dtBegin),//('' + dtBegin).replace('+', '%2B'),
            dtEndURL = na1.formatDateForLoading(dtEnd),//('' + dtEnd).replace('+', '%2B'),
            url = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news/ajax_get_items_search.php',
            data = {
                loads : c.loads,
                direction : 'past',
                section : s.url.section.replace(/-/g,'/'.replace(/ /g, '_')),
                dateBegin : dtBeginURL,
                dateEnd : dtEndURL,
                q : $('#newsApp_searchbar').val().replace(' ', '%20')
            };

            na.analytics.logMetaEvent ('newsApp : loadNews_searchResults() url='+url);
            ajaxCommand = {
                type : 'GET',
                url : url,
                data : data,
                success : function (data, textStatus, jqXHR) {
                    // ajax_get_items.php sets a header to content-type: application/json
                    // that will make $ provide the data as already decoded json js object data[1]
                    // if you ever have problems with news not showing up it could be due to data-translation errors.
                    // eventually you'll have to debug in crontabEntry_manageDatabase.php and class.newsApp-2.php as well,
                    // they're in the same folder as ajax_get_items.php and this file.

                    delete s.loading;

                    na.analytics.logMetaEvent ('newsApp : loadNews_searchResults() data fetched sucessfully for itemsLoadedCount='+itemsLoadedCount+' and url='+url);
                    na.m.extend (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.current.db, data);

                    $('.loader, .loaderAfter').remove();
                    $('#siteContent .vividDialogContent').css ({
                        display : 'block',
                        justifyContent : '',
                        alignItems : '',
                        textAlign : '',
                        background : 'rgba(0,0,0,0)'
                    });
                    $('#siteContent__content').css({
                        top : $('#siteContent__header').height() + 10,
                        left : 10,
                        width : $('#siteContent').width() - 20,
                        height : $('#siteContent').height() - $('#siteContent__header').height() - 20,
                        opacity : 1
                    });

                    var idxStart = c.idx;
                    na.m.walkArray (data, data, undefined, na1.loadNews_get_forDateTimeRange_walkValue);
                    var itemsLoadedCount = c.idx - idxStart;

                    na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.current.db = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.current.db.concat(data);

                    c.dtEnd = c.dtCurrent;
                    c.dtCurrent = new Date(c.dtEnd.getTime() - (1000 * 60 * c.read_loop_minutesIntoPast));


                    if (parseInt(c.dtCurrent.getFullYear())<2022) {
                        clearInterval (c.intervalMailLogCountdown);
                        clearTimeout (c.timerDisplayNews_loop);
                        $('#newsAppInfo').html('Loaded all the news available by now. Press F5 to start over.');
                    } else if (itemsLoadedCount < 50) {
                        c.read_loop_minutesIntoPast = 60 * g.readHistory_numHours * c.readHistory_numHours__multiplier;
                        c.read_loop_millisecondsToDoNext = 2 * 1000;
                    } else if (itemsLoadedCount < 100) {
                        c.read_loop_minutesIntoPast = 60 * g.readHistory_numHours * c.readHistory_numHours__multiplier;
                        c.read_loop_millisecondsToDoNext = 2 * 1000;
                    } else {
                        c.read_loop_minutesIntoPast = 60 * g.readHistory_numHours * c.readHistory_numHours__multiplier;
                        c.read_loop_millisecondsToDoNext = 2 * 1000;
                    };


                    if (data.trim() === '') {
                        na1.loadNews_searchResults_loop (c.dtCurrent, c.dtEnd, settings);
                    } else {
                        clearInterval (c.intervalMailLogCountdown);
                        $('#newsApp_debug').animate({opacity:0.001}, {complete :function () {
                            $('#newsApp_debug').html('').delay(50);
                        }});

                        var
                        dc = {},
                        di = {},
                        params = {
                            dc : dc,
                            di : di
                        };

                        na.m.walkArray (db, db, undefined, na1.displayNews_getDisplayCounts, false, params);

                        var
                        ks = Object.keys(dc),
                        total = 0,
                        highest = 10;

                        ks = ks.sort(function(a,b){ b - a });
                        c.displayCounts = '';
                        for (k in ks) {
                            if (c.displayCounts!=='') c.displayCounts +=', ';
                            c.displayCounts += dc[ks[k]];
                        };
                        c.displayCountInt = parseInt(c.displayCounts)- g.numItemsThatFitScreen;
                        $('.newsApp__header__dateRange').html(na1.formatDateForHeader());
                        $('.newsApp__header__displayCounts').html (c.displayCountInt);
                        //$('.newsApp__header__timer').html(c.countDownStr);

                        clearTimeout (c.timerDisplayNews_loop);
                        clearTimeout (c.timerLoadNews_read_loop);
                        c.timerLoadNews_read_loop = setTimeout (function() {
                            na1.loadNews_searchResults_loop (c.dtCurrent, c.dtEnd, settings);
                        }, c.read_loop_millisecondsToDoNext);

                        var
                        dnf = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].displayNews_full(),
                        full = dnf.full,
                        removed = dnf.removed;

                        clearInterval (c.intervalMailLogCountdown);
                        clearTimeout (c.timerDisplayNews_loop);
                        $('#newsAppDebug').animate({opacity:0.001});
                    }
                },
                error : function (xhr, textStatus, errorThrown) {
                    debugger;
                    var
                    na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;

                    c.readHistory_numHours__numberOfRecentlyFailedContentLoadAttempts++
                    na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
                }
            };
            $.ajax (ajaxCommand);
        }, 100);
    },
    
    clearSearch : function (evt) {
        var 
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        input = $('#newsApp_searchbar');
        
        c.searchQuery = input.val().replace(' ', '%20');
        c.dtCurrent = new Date(new Date().getTime() - (1000 * 60 * 60 * 2));
        c.dtEnd = new Date();
        c.lastCurrentGet = new Date();

        c.locked = true;
        $('.newsApp__item__outer').fadeOut('slow', function() {
            $('.newsApp__item__outer').remove();
            c.locked = false;
            c.firstRun = true;
            c.db = [];
            c.loads = 0;
            na1.loadNews_read_loop();
        });
    },
    
    filterNews__userInterfaceStage_level1 : function (html, regEx, replaceStr) { // algorithm sub-concious code owned by Nicer Enterprises.
        
        // purpose : search for the function name of *this* function elsewhere in this file.
        if (typeof html=='string' && html!=='') return html.replace (regEx, replaceStr); else return '';
    },
    
    gotoNextPage : function () {
        var
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;
        c.gotoNextPage = true;
    },
    
    toggleLock : function () {
        var
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        lock = $('#newsApp_lock .vividButton_icon_imgButtonIcon_50x50')[0];
        if (lock.src.match('_on')) {
            lock.src = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news/btnLock_off.png';
            c.locked = false;            
        } else {
            lock.src = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news/btnLock_on.png';
            c.locked = true;            
        }
        
    },
    
    intervalMailLogCountdown : function () {
        var
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        ms = /*c.dtCurrent.getTime(),*/na.m.elapsedMilliseconds(),
        msgTime = na.m.secondsToTimeString(ms/1000);
        //return false;
        
        c.mailLogMsg = msgTime + ' has passed, mailing log of what happened at around 15 to 20 seconds.';
        if (parseFloat($('#newsApp_debug').css('z-index'))!==10 * 1000 * 1000) {
            $('#newsApp_debug').css ({
                opacity : 0.001,
                zIndex : 10 * 1000 * 1000,
                height : '3em',
                padding : '7px'
                
            }).animate({opacity:0.75,color:'white',backgroundColor:'green'});
        }
        
        $('#newsApp_debug').html (c.mailLogMsg);
    },
    
    loadNews_read_loop : function () {
        var
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        urlp = na.site.globals.app,
        dtBegin = new Date(),
        dc = { 0 : 0 },
        di = { 0 : [] },
        params = {
            dc : dc,
            di : di
        },
        ow = ($('#siteContent')[0].offsetWidth)-20,
        oh = $('#siteContent')[0].offsetHeight;

        for (var k in urlp) {
            var settings = urlp[k];
            break;
        }
//debugger;
        settings.direction = 'past';
        
        g.numItemsThatFitScreen = Math.round( ( ow / 440 ) * (oh/400) );
        if (g.numItemsThatFitScreen < 5) g.numItemsThatFitScreen = 5; // smartphone bugfix

        
        na.m.walkArray (db, db, undefined, na1.displayNews_getDisplayCounts, false, params);
        var
        ks = Object.keys(dc),
        total = 0;
        
        //ks = ks.sort(function(a,b){ return b - a }); // BAD IDEA!
        
        var
        unread = dc[ks[0]];
        c.unread = unread;

        if (!c.dtEnd || unread < 100) {
            if (!c.dtCurrent) c.dtCurrent = new Date();
            c.dtEnd = c.dtCurrent;
            c.dtCurrent = new Date(c.dtEnd.getTime() - (1000 * 60 * 60 * 2));//c.read_loop_minutesIntoPast));
        }
        if (unread < 100) {
            na1.loadNews_get_forDateTimeRange (c.dtCurrent, c.dtEnd, settings);
            return false;
            /*
            $('#newsApp_content').html('').delay(50);
            if (c.timeout_loadNews) clearTimeout(c.timeout_loadNews);
            c.timeout_loadNews = setTimeout(function() {
                na1.displayNewNewsItems();
                na1.countDown();
                na1.onresize();
            }, 400);
            return false;
            */
        } else {

            // get older news items when needed
            if (c.firstRun) {
                c.firstRun = false;
                c.failedLoads = 0;
                na1.loadNews_get_forDateTimeRange(c.dtCurrent, c.dtEnd, settings);
            } else {
                clearInterval (c.intervalMailLogCountdown);
                clearTimeout (c.timerDisplayNews_loop);

                $('#newsAppDebug').animate({opacity:0.001});

                //if ($('#newsApp_content .newsApp__item__outer').length===0) c.timerDisplayNews_loop = setTimeout (na1.displayNews_loop, 250);
                if ($('#newsApp_content .newsApp__item__outer').length===0) {
                    c.tries = 0;
                    $('#newsApp_content').html('').delay(50);
                    setTimeout(function() {
                        na1.displayNewNewsItems();
                    }, 250);
                }
            }
        }

        if (unread > 10) {
            c.tries = 0;
//debugger;
            na1.displayNewNewsItems();
        }

        // get newest news items
        settings.direction = 'future';
        if (
            !c.lastCurrentGet
            || c.lastCurrentGet.getTime() < dtBegin.getTime() - 1000 * 60 * g.readHistory_numHours
        ) {
            c.lastCurrentGet = dtBegin;
            na1.loadNews_get_forDateTimeRange (new Date(dtBegin.getTime() - 1000 * 60 * g.readHistory_numHours), dtBegin, settings);
        };



    },
    
    /*
    loadNews_read_loop_old : function (dtBegin, dtEnd, dtQuit, settings, waitTimeInSeconds, dtOffsetInSeconds) {
        var
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        doNextToo = true;
        
        //c.dtCurrent = dtBegin;
        //c.dtEnd = dtEnd;
        na1.loadNews_get_forDateTimeRange (dtBegin, dtEnd, settings);
        
        if (dtOffsetInSeconds < 0 && dtQuit) {
            doNextToo = dtBegin.getTime() > dtQuit.getTime();
        };
        
        if (doNextToo) setTimeout (function() {
            c.dtEnd = new Date(dtEnd.getTime() + 1000 * dtOffsetInSeconds);
            c.dtBegin = new Date(dtBegin.getTime() + 1000 * dtOffsetInSeconds);
            
            na1.loadNews_read_loop(c.dtBegin, c.dtEnd, dtQuit, settings, waitTimeInSeconds, dtOffsetInSeconds);
        }, 1000 * waitTimeInSeconds);
    },
    */
    
    loadNews_get_forDateTimeRange : function (dtBegin, dtEnd, settings) {
        //debugger;
        var
        fncn = 'na.apps.loaded["applications/2D/news"].loadNews_get_forDateTimeRange(dtBegin,dtEnd,settings)',
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;
        c.loads++;
        
        na.m.waitForCondition ('has the news app finished loading?', function () {
            var r = !s.loading;
            return r;
        }, function() {
            s.loading = true;

            var
            dtBeginURL = na1.formatDateForLoading(dtBegin),//('' + dtBegin).replace('+', '%2B'),
            dtEndURL = na1.formatDateForLoading(dtEnd),//('' + dtEnd).replace('+', '%2B'),
            url = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news/ajax_get_items.php';//?loads='+c.loads+'&section='+settings.section.replace(/-/g,'/').replace(/ /g, '_')+'&dateBegin='+dtBeginURL+'&dateEnd='+dtEndURL;


            s.url = settings.section;

            if (c.searchQuery) url += '&q='+c.searchQuery;

            var
            ajaxCommand = {
                type : 'GET',
                url : url,
                data : {
                    loads : c.loads,
                    direction : settings.direction,
                    section : settings.section.replace(/-/g,'/').replace(/ /g, '_'),
                    dateBegin : dtBeginURL,
                    dateEnd : dtEndURL
                },
                success : function (data, textStatus, jqXHR) {
                    // ajax_get_items.php sets a header to content-type: application/json
                    // that will make $ provide the data as already decoded json js object data[1]
                    // if you ever have problems with news not showing up it could be due to data-translation errors.
                    // eventually you'll have to debug in crontabEntry_manageDatabase.php and class.newsApp-2.php as well,
                    // they're in the same folder as ajax_get_items.php and this file.

                    delete s.loading;

                    $('.loader, .loaderAfter').remove();
                    $('#newsApp_info, #newsApp_timer').css({display:'block'});
                    $('#newsApp_title, #newsApp_searchbar, #newsApp_header_buttons').css({display:'table-cell'});
                    $('#siteContent .vividDialogContent').css ({
                        display : 'block',
                        justifyContent : '',
                        alignItems : '',
                        textAlign : '',
                        background : 'rgba(0,0,0,0)'
                    });
                    $('#siteContent__content').css({
                        top : $('#siteContent__header').height() + 10,
                        left : 10,
                        width : $('#siteContent').width() - 20,
                        height : $('#siteContent').height() - $('#siteContent__header').height() - 20,
                        opacity : 1
                    });



                    var
                    idxStart = c.idx;

                    //na.m.log (2, 'loadNews_get_forDateTimeRange() : data=' + data);

                    var dat = JSON.parse(data,null,4);
                    dat.fncn = fncn;
                    na.m.log (2, dat);

                    try {
                        dataText = data;
                        data = JSON.parse(data);
                    } catch (err) {
                        debugger;
                    }

                    if (data.length > 0) {
                        na.m.walkArray (data, data, undefined, na1.loadNews_get_forDateTimeRange_walkValue);

                        var itemsLoadedCount = c.idx - idxStart;

                        na.analytics.logMetaEvent ('newsApp : loadNews_get_forDateTimeRange() data fetched sucessfully for itemsLoadedCount='+itemsLoadedCount+' and url='+url);
                    } else {
                        itemsLoadedCount = 0;
                    };


                    if (itemsLoadedCount < 50) {
                        c.read_loop_minutesIntoPast = 60 * g.readHistory_numHours;
                        c.read_loop_millisecondsToDoNext = 1000;
                    } else if (itemsLoadedCount < 200) {
                        c.read_loop_minutesIntoPast = 60 * (g.readHistory_numHours/2);
                        c.read_loop_millisecondsToDoNext = 1000;
                    } else {
                        c.read_loop_minutesIntoPast = 60;
                        c.read_loop_millisecondsToDoNext = 2 * 60 * 1000;
                    };

                    na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.current.db = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.current.db.concat(data);

                    if (itemsLoadedCount===0) {
                        c.timerLoadNews_read_loop = setTimeout (na1.loadNews_read_loop, c.read_loop_millisecondsToDoNext);
                        return false;
                    } else {
                        clearInterval (c.intervalMailLogCountdown);
                        $('#newsApp_debug').animate({opacity:0.001}, {complete :function () {
                            $('#newsApp_debug').html('').delay(50);
                        }});
                    }

                    var
                    db = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.current.db,
                    dc = {},
                    di = {},
                    params = {
                        dc : dc,
                        di : di
                    },

                    dc = { 0 : 0 },
                    di = { 0 : [] },
                    params = {
                        dc : dc,
                        di : di
                    };


                    na.m.walkArray (db, db, undefined, na1.displayNews_getDisplayCounts, false, params);

                    var
                    ks = Object.keys(dc),
                    total = 0;

                    ks = ks.sort(function(a,b){ b - a });

                    var
                    unread = dc[ks[0]];
                    c.unread = unread;


                    var
                    dbg = {
                        fncn : fncn,
                        appID : '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news (version '+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].about.version+')',
                        loads : c.loads,
                        section : settings.section.replace(/-/g,'/').replace(/ /g, '_'),
                        numLoaded : c.unread,
                        dateBegin : dtBegin,
                        dateEnd : dtEnd
                    };
                    //console.log (dbg);



                    var
                    ks = Object.keys(dc),
                    total = 0,
                    highest = 10;

                    ks = ks.sort(function(a,b){ b - a });
                    c.displayCounts = '';
                    for (k in ks) {
                        if (c.displayCounts!=='') c.displayCounts +=', ';
                        c.displayCounts += dc[ks[k]];
                    };
                    c.displayCountInt = parseInt(c.displayCounts)- g.numItemsThatFitScreen;

                    $('.newsApp__header__dateRange').html(na1.formatDateForHeader());
                    $('.newsApp__header__displayCounts').html (c.displayCountInt);
                    //$('.newsApp__header__timer').html(c.countDownStr);

                    if (!c.dtCurrent) c.dtCurrent = new Date();
                    c.dtEnd = c.dtCurrent;
                    c.dtCurrent = new Date(c.dtEnd.getTime() - (1000 * 60 * 60 * 1.5));//c.read_loop_minutesIntoPast));
                    if (unread < 100) {
                        na1.loadNews_read_loop();
                    } else {
                        clearTimeout (c.timerLoadNews_read_loop);
                        c.timerLoadNews_read_loop = setTimeout (na1.loadNews_read_loop, c.read_loop_millisecondsToDoNext);
                    }
                },
                error : function (xhr, textStatus, errorThrown) {
                    debugger;
                    var
                    na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;

                    c.readHistory_numHours__numberOfRecentlyFailedContentLoadAttempts++
                    c.timerLoadNews_read_loop = setTimeout (na1.loadNews_read_loop, c.read_loop_millisecondsToDoNext);
                    //don't need to see this : na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
                }
            };
        //na.m.log (20, url);
    //debugger;
        $.ajax (ajaxCommand);
        }, 100);
    },
    
    loadNews_get_forDateTimeRange_walkValue : function (cd) {
        var 
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        urlp = na.site.globals.app;//na1.getURLparameters(),

        for (var k in urlp) {
            var settings = {
                section : urlp[k].section.replace(/__/g,'/').replace(/_/g,' ')
            };
            break;
        }
        if (typeof cd.v=='function') return false;
        if (typeof cd.v=='object' && cd.v!==null && typeof cd.v.length=='number') debugger;
        if (cd.v) {
            var it = cd.v;
            c.idx++;
            it.idx = c.idx;
            it.rssURL = cd.v._id;
            if (it.p!=='') {
                it.path = settings.section;
            } else {
                it.path = settings.section + '/' + it.p;
            };
            it.level = cd.level;
            it.displayCount = 0;
        }
    },
    
    displayNews_getDisplayCounts : function (cd) {
        var 
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;
        if (cd.v && typeof cd.v==='object') {
            //for (var i=0; i<cd.v.items.length; i++) {
                var it = cd.v;
                //debugger;

                if (!it.displayCount) it.displayCount = 0;
                var k = it.displayCount;

                if (typeof cd['params']['dc'][k]=='undefined')
                    cd['params']['dc'][k] = 1;
                else
                    cd['params']['dc'][k]++;

                if (typeof cd['params']['di'][k]=='undefined')
                    cd['params']['di'][k] = [ it ];
                else {
                    cd['params']['di'][k].push(it);
                }
            //}
        }        
    },

    displayNewNewsItems : function () {
        if (na.m.settings.locked_displayNewNewsItems) return false;
        na.m.settings.locked_displayNewNewsItems = true;

        var
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;

        var
        min = 20,
        max = 30,
        randomValue = Math.floor(Math.random() * (max - min + 1)) + min,

        min = parseFloat(c.delayNewItemsMin),
        max = parseFloat(c.delayNewItemsMax),
        randomValue2 = (
            c.delayNewItemsDisplayToPage
            ? Math.floor(Math.random() * (max - min + 1)) + min
            : 0
        );
        /*c.pages.push ({
            idx : c.pages.length,
            items : []
        });
        c.page++;*/

        $('.newsApp__item__outer a:not(:has(img))').animate({opacity:0.7});


        if (c.tries === 0) c.randomValue = randomValue;
        if (c.tries < c.randomValue) {
            if (!na1.displayNewNewsItem()) { c.unread++; };
            setTimeout (function() {
                if (!c.pages[c.page-1]) debugger;
                c.pages[c.page-1].items.push ({
                    dnfi : c.dnfi,
                    dnf : c.dnf,
                    it : c.it,
                    itEl : c.itEl
                });
                if (!c.dnf.full) {
                    na.m.settings.locked_displayNewNewsItems = false;
                    na1.displayNewNewsItems();
                } else {
                    $('.newsApp__header__timer')
                        .html(c.countDownStr)
                        .delay(20)
                        .css({opacity:0.0001,textAlign:'right'})
                        .animate({opacity:1});

                    c.pages.push ({
                        idx : c.pages.length,
                        items : []
                    });
                    c.page++;

                    na1.countDown();

                    na1.loadNews_read_loop();
                }
            }, c.dnf.removed?0:randomValue2);

        } else {
            $('.newsApp__header__timer').css({opacity:0.0001,textAlign:'right'}).animate({opacity:1});
            c.pages.push ({
                idx : c.pages.length,
                items : []
            });
            c.page++;
            na1.countDown();
            na1.loadNews_read_loop();
        }
    },

    displayNewNewsItem : function () {
        var
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'],
        g = na1.globals, s = na1.settings, c = s.current, db = c.db, app = na.site.globals.app,
        min = 10,
        max = 300,
        delay = Math.floor(Math.random() * (max - min + 1)) + min;


        $('.lds-facebook').fadeOut('slow');
        $('.newsApp__header__countDownStr').animate({opacity:0.001});

        for (var k in app) {
            $('#newsApp_title').html (app[k].section.replace(/__/g, ' ').replace(/_/g, ' '));
            break;
        }

        if (s.loaderIcon) {
            $(s.loaderIcon).fadeOut('slow', function() {
                $(s.loaderIcon).remove();
                delete s.loaderIcon;
            });
        }

        var
        // dc means displayCount, an elaborate way to make sure entries that havent been shown too often end up on the screen quickly.
        dc = { 0 : 0 },
        di = { 0 : [] },
        params = {
            dc : dc,
            di : di
        };

        c.tries++;

        na.m.walkArray (db, db, undefined, na1.displayNews_getDisplayCounts, false, params);

        var
        ks = Object.keys(dc),
        total = 0,
        highest = 10,
        unread = dc[ks[0]];

        c.displayCounts = '';
        for (k in ks) {
            if (c.displayCounts!=='') c.displayCounts +=', ';
            c.displayCounts += dc[ks[k]];
        };
        c.displayCountInt = parseInt(c.displayCounts)- g.numItemsThatFitScreen;
        /*
        c.displayCounts = '<span class="newsApp__header__displayCounts">' + c.displayCountInt + '</span>';
        $('#newsApp_timer').html(
            na1.formatDateForHeader()
            + ' ' +(c.displayCounts)
            +'<span class="newsApp__header__timer">'+c.countDownStr+'</span>'
        );*/
        $('.newsApp__header__dateRange').html(na1.formatDateForHeader());
        $('.newsApp__header__displayCounts').html (c.displayCountInt);
        $('.newsApp__header__timer').html(c.countDownStr);

        let
        found = false,
        it = null;

        var l = 0;
        while (di[l] && di[l].length==0) l++;
        its = di[l];

        if (!its) {
            if (c.displayDebugInfo) na.site.setStatusMsg ('na1.displayNewNewsItem : c.tries==='+c.tries+', !its : return false');
            return false;
        }

        while (it===null) {
            var
            key = Math.floor(Math.random() * its.length);

            it = its[key];
            if (!it) debugger;
            if (!it.displayCount) it.displayCount = -1;
            it.displayCount++;

            if (typeof it.displayCount=='number' && it.displayCount > 0) {
                it = null;
            } else {
                var els2 = $('#newsApp_content .newsApp__item__outer');
                for (var j=0; j<els2.length; j++) {
                    if (els2[j].innerHTML.indexOf(it.t)!==-1) found = true;
                }
            }
        };

        if (found || !na1.match_searchCriteria(it)) {
            return false;
        } else {
            var
            thumbWidth = (
                na.m.userDevice.isPhone
                ? 80
                : 150
            ),
            prefix = '',
            dnfi = na1.displayNews_formatItem (it, ''), // displayNews_formatItem() also puts the formatted item into the DOM!
            html = dnfi.html,
            htmlTooltip = dnfi.html2;
            c.dnfi = dnfi;

            if (!html) {
                if (c.displayDebugInfo) na.site.setStatusMsg ('na1.displayNewNewsItem : c.tries==='+c.tries+', !html : return false');
                return false;
            }
            html = html.replace ('<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>','');
            html = html.replace (/http:\/\//g, 'https://');


            var el = $(dnfi.id)[0];
            $('.newsApp__item__title, .newsApp__item__contentContainer, .newsApp__item__mediaSingle, .vividScrollpane, .newsApp__item__footer',el)
                .css({
                    display : 'block',
                    opacity : 1
                }).delay(50);
            var
            ms = $('.newsApp__item__mediaSingle',el),
            w = $(el).width() - 14;

            //if (ms[0]) w -= ms.width();
            //console.log (w);
            //if ($('.newsApp__item__mediaSingle img', el)[0]) $('.vividScrollpane', el).add($('.newsApp__item__mediaSingle img', el)).css ({width : '40%'}); else $('.vividScrollpane', el).css ({width : w});

            var
            h = $('#siteContent .vividDialogContent').height() - $('#newsApp_title').outerHeight();
            if ($(window).width() < na.site.globals.reallySmallDeviceWidth) {
                w = $('#newsApp_content').width() - 10;
            } else {
                var w1 =
                        (
                            $('.newsApp__item__mediaSingle', $(dnfi.id)[0]).width()
                            + $('.vividScrollpane', $(dnfi.id)[0]).width()
                        )
                        || $('.newsApp__item__title', $(dnfi.id)[0]).width()
                        || 0;

                if (w < w1) w = w1;
            }

            //if (c.tries>20) debugger;

            var
            itEl = $('#newsApp__item__'+it.idx)[0],
            sp = $('.vividScrollpane', itEl)[0],
            dnf = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].displayNews_full(''),
            full = dnf.full,
            removed = dnf.removed;
            c.dnf = dnf;
            c.it = it;
            c.itEl = itEl;
            if (removed) {
                if (c.displayDebugInfo) na.site.setStatusMsg ('na1.displayNewNewsItem : c.tries==='+c.tries+', removed : return false');
            }


            var sph1 = 0;
            $('.newsApp__item__outer').each(function(idx3, el3) {
                var count = true;
                if ($(el3).index() !== $(itEl).index()+1) count = false;
                if (count) sph1 += $(el3).outerHeight();
            });
            h -= sph1;

            if (!dnfi) {
                if (c.displayDebugInfo) na.site.setStatusMsg ('na1.displayNewNewsItem : c.tries==='+c.tries+', !dnfi : return false');
                return false;
            };

            if (!c.getToTry) c.getToTry = g.numItemsThatFitScreen;

            //console.log (full,removed);
            if (full || removed) {
                $(itEl).remove();
                return false;
            }

            it.displayCount++;
            if (it.displayCount > c.maxDisplayCount) c.maxDisplayCount = it.displayCount;


            if (!na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news']) return false;
            if (!itEl) {
                return false;
            }

            //if (sp && sp.scrollHeight > 300) {
                var
                jel2 = $('.newsApp__item__mediaSingle',itEl),
                jels =
                    $('img, iframe', sp || itEl)
                    .not('[src*="track"]')
                    .not('.newsApp__item__mediaSingle__0')
                    .not('[width="1"]')
                    .not('[border="1"]')
                    .not('.dontResize');
                jels.each(function(idx,el) {
                //  debugger;
                    var
                    pctg = Math.floor(100 / jels.length),
                    th = $('.news__item__title', itEl).height(),
                    fh = $('.newsApp__item__footer', itEl).height(),
                    ph = $('p, div', itEl).height(),
                    ph2 = 20 * $('p, div', itEl).length,
                    mh = Math.round((th?parseInt(th)+20:0) + (fh+20?parseInt(fh):0) + (ph?parseInt(ph)+20:0) + (ph2?parseInt(ph2):0) + 60);

                    $(el).css ({
                        margin : 0,
                        border : '0px solid black',
                        float : '',
                        maxWidth : '100%',//$(el).parent('div').width(),
                        maxHeight : 'calc('+pctg+'% - '+mh+'px)'
                    });
                    el.removeAttribute('height');
                    el.removeAttribute('width');
                });
            //}
        };

        if (!$('div > div > iframe', itEl)[0]) {
            var
            minWidth = null,
            ms = $('.newsApp__item__mediaSingle', itEl),
            imgs = $('img', itEl);

            if (imgs.length==0 && ms[0] && it.m.length && it.m.length>0) {
                var m1 = it.m[it.m.length-1];
                for (var i=0; i<it.m.length; i++) {
                    var m = it.m[i];
                    if (typeof m.u=='string') {
                        if (!minWidth && typeof m.width=='number') minWidth = parseInt(m.width);
                        if (parseInt(m.width) < minWidth) {
                            minWidth = parseInt(m.width);
                            var m1 = it.m[i];
                        }
                        if (m.content) {
                            var m1 = it.m[i];
                            minWidth = thumbWidth;
                        }
                    };
                }
                if (!minWidth) minWidth=thumbWidth;
                if (minWidth>thumbWidth) minWidth=thumbWidth;

                if (typeof m1.u=='string') {
                    m1.u = m1.u.replace ('http://', 'https://');

                    var
                    html = '<a class="nomod" target="_new" href="'+it.u+'"><img src="'+m1.u+'" class="newsApp__item__mediaSingle__0" style="width:150px;"/></a>';

                    $(ms)[0].innerHTML = html;
                }
            };
        }
        return true;
    },

    match_searchCriteria : function (it) {
        var 
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        input = $('#newsApp_searchbar');
        
        if (input.val()!=='') {
            searchQueryLookArounds = '',
            searchQueryLookAheads = '',
            searchQueryRegx = '',
            searchQueryParts = input.val().split(' ');
            for (var idx in searchQueryParts) {
                if (typeof searchQueryParts[idx]!=='string') continue;
                var 
                searchQueryPart = searchQueryParts[idx];
                
                if (searchQueryPart.indexOf('-')!==0) {
                    if (searchQueryLookArounds!=='') searchQueryLookArounds += '|';
                    searchQueryLookArounds += '.*('+searchQueryPart+').*';
                } else {
                    searchQueryPartCorrected = searchQueryPart.substr(1, searchQueryPart.length-1);
                    if (searchQueryLookAheads!=='') searchQueryLookAheads += '|';
                    searchQueryLookAheads += '((?!'+searchQueryPartCorrected+').)';
                }
            };
            if (searchQueryLookArounds!=='') {
                searchQueryRegx = '^'+searchQueryLookAheads+searchQueryLookArounds;
            } else {
                searchQueryRegx = '^.*('+searchQueryLookAheads + ').*$';
            }
            searchQueryRegExp = new RegExp (searchQueryRegx, 'gmi');
        } else {
            searchQueryRegx = null;
        }
        
        //if (it.de.indexOf('hina')!==-1 || it.t.indexOf('hina')!==-1) debugger;
        if (searchQueryRegx!==null) {
            if (
                (
                    it.de && it.de.match (searchQueryRegExp) 
                ) || (
                    it.t && it.t.match(searchQueryRegExp)
                ) 
            ) {
                return true;
            } else {
                //it.displayCount++;
                return false;                
            }
        } else {
            return true;
        }
    },
    
    countDown : function (restart) {
        var
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        container = $('#newsApp_content'),
        l = $('.newsApp__item__outer', container).length,
        countDown = (
            l < 15
            ? 15
            : 30
        );


        countDown = 10 + Math.round(l * 5);
        c.countDown = countDown;
        c.countDownStr = ' - '+countDown;
            $('.newsApp__header__timer').html(c.countDownStr);

        c.onScreen = l;
        c.shownBGonly = false;
        c.showBGonlySecs = (
            c.blankScreenBeforePageChange
            ? Math.random() * (c.blankScreenMax - c.blankScreenMin + 1) + c.blankScreenMin
            : 0
        );

        if (c.countDownInterval) clearInterval(c.countDownInterval);

        //debugger;
        //if (!na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.dnf.full) return false;
        
        
        c.countDownInterval = setInterval (function() {
            if (!na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news']) return false;

            if (!c.displayed) c.displayed=[];
            c.displayed.push({
                idx : c.displayed.length,
                items : []
            });

            if (!c.locked) countDown--;
            c.countDown = countDown;
            c.countDownStr = ' - '+countDown;


            /*
            $('#newsApp_timer').html(
                na1.formatDateForHeader()
                + ' ' +(c.displayCounts)
                +'<span class="newsApp__header__timer">'+c.countDownStr+'</span>'
            );*/
            $('.newsApp__header__dateRange').html(na1.formatDateForHeader());
            //$('.newsApp__header__displayCounts').html (c.displayCountInt);
            $('.newsApp__header__timer').html(c.countDownStr);
            if (countDown<=c.showBGonlySecs || c.gotoNextPage) {
                if (!c.shownBGonly) {
                    $('.newsApp__item__outer').fadeOut('normal', function() {
                        $(container).html('').delay(20);
                    });
                    c.shownBGonly = true;
                }
            }
            if (countDown==0 || c.gotoNextPage) {
                clearInterval(c.countDownInterval);
                $('.newsApp__header__timer').animate({opacity:0.0001});
                $(container).fadeOut(500, function() {
                    container.css({opacity:0.001,display:'block'});
                    c.its3 = [];
                    c.resize = {};
                    c.tries = 0;

                    delete c.gotoNextPage;


                    setTimeout (function() {
                        var
                        container = $('#newsApp_content'),
                        l = $('.newsApp__item__outer', container).length,
                        countDown = 10 + Math.round(l * 2.2);
                        c.countDown = countDown;

                        c.tries = 0;
                        delete na.m.settings.locked_displayNewNewsItems;

                        $(container).animate({opacity:1}, 'normal');
                        na1.displayNewNewsItems();

                        //$('.newsApp__header__countDownStr').animate({opacity:1});
                        //na1.onresize();
                        //na1.loadNews_read_loop();
                        //na1.countDown();
                    }, 100);
                });
            }
        }, 1 * 1000);
    },

    displayNews_full : function (prefix) {
        var
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        container = (
            prefix == ''
            ? $('#newsApp_content')
            : $('#newsApp_content')//$('#newsApp_content_shadow')
        ),
        its = $('.newsApp__item__outer', container),
        full = false,
        removed = false,
        its2 = [],
        maxY = 0, max2Y = 0,
        maxX = 0, max2X = 0;
        
        for (var i=0; i<its.length; i++) {
            var 
            it = its[i],
            x = $(it).position().left + $(it).outerWidth(),
            x2 = $(it).position().left,
            y = $(it).position().top + $(it).outerHeight(),
            y2 = $(it).position().top;
            
            if (x > maxX) maxX = x;
            if (x2 > max2X) max2X = x2;
            if (y > maxY) maxY = y;
            if (y2 > max2Y) max2Y = y2;
            if (c.its3.length > 0 && y > container[0].offsetHeight - 40 ) { // - 20 for margin and padding of news item
                var found = false;
                for (var j=0; j<c.its3.length; j++) {
                    if (c.its3[j].id === it.id) found = true;
                };
                if (!found) {
                    //debugger;
                    its2[its2.length] = it;
                }

            } else {
                var found2 = false;
                for (var j=0; j<c.its3.length; j++) {
                    if (c.its3[j].id === it.id) found2 = true;
                }
                if (!found2) c.its3[c.its3.length] = it;
            }
            //if ($(it).position().top > container[0].offsetHeight) {
                //full = true;
                //its2[its2.length] = it;
            //}
        };

        for (var i=0; i<its2.length; i++) {
            var it = its2[i];
            removed = true;
            //debugger;
            if (it.parentNode) it.parentNode.removeChild (it);
        }
        //console.log ('prefix='+prefix);
        //console.log ('max='+max);
        //console.log ('c='+(container.height()-50));
        //if (max2 > container.height() - 50) full = true;
        if (prefix=='' && $(it).position().top > container.height()-50) { full = true; }
        //if (prefix=='' && maxX > container.width()-20) { full = true; }

        return {
            full : full,
            removed : removed 
        };
    },
    
    displayNews_getRandomItems : function () {
        var 
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        db2 = na1.displayNews_getRandomItems_traverse(db, 6);
        
        return db2;     
    },
    
    displayNews_getRandomItems_traverse : function (db, maxDepth, depth) {
        var 
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current,
        keys = Object.keys(db),
        key = Math.floor(Math.random() * keys.length),
        it = db[keys[key]];
        
        if (!depth) depth = 0;
        
        if (it && !it.items) {
            return na1.displayNews_getRandomItems_traverse (it, maxDepth, depth + 1);
        };
        
        return it;
    },
    
    displayNews_formatItem : function (it, prefix) {
        var 
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current,         container = (
            prefix == ''
            ? $('#newsApp_content')
            : $('#newsApp_content_shadow')
        ),
        db = c.db,
        fontSize = (
            na.m.userDevice.isPhone
            ? ';font-size:0.8rem;'
            : ';font-size:1rem;'
        ),
        w = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.itemWidth;
        
        if (
                ( !it.de || (typeof it.de=='string' && it.de.trim()=='') )
                && ( !it.t || (typeof it.t=='string' && it.t.trim()=='') )
        ) {
            debugger;
            return false;
        }
        
        if (!it) return false;
        if (typeof it.de=='string') {
            //console.log (it.idx, it.de);
            it.de = na1.filterNews__userInterfaceStage_level1(it.de, /tabIndex=.*;/g, '');
            
            // beware : more algorithm sub-concious code owned by Nicer Enterprises :
            // these are the visual reformatting sections of that computer code.
            it.de = it.de.replace(/<a?.*><br\/><\/a>/g, '');
            it.de = it.de.replace(/<p>\s*<br.>\s*<\/p>/g, '');
            it.de = it.de.replace(/6f6f6f/g, 'BBBBBB');
            it.de = it.de.replace(/style=".*?"/g, '');
            it.de = it.de.replace('div class="feedflare"', 'div class="feedflare" style="display:none"');
            it.de = it.de.replace(/<img/g, '<img style="" onload="var na1 = na.apps.loaded[\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news\'], g = na1.globals, s = na1.settings, c = s.current;  var ni = $(this).parents(\'.newsApp__item__outer:first\');  if (ni[0]) { var dnf = na1.displayNews_full(\''+prefix+'\'), id = ni[0].id;  if (prefix==\'\') na.apps.loaded[\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news\'].settings.dnf = dnf;};"');
            //console.log (it.idx, it.de);
        }
        
        //var html = '<div id="newsApp__layoutGuide1" style="width:1px;height:400px;display:inline-block"></div>';
        var
        html = '',
        min = 300,
        max = 800,
        w = Math.floor(Math.random() * (max - min + 1)) + min,
        h = Math.floor(Math.random() * (max - min + 1)) + min;

        var
        //min = 500,
        //max = 700,
        min = 0.3,
        max = 0.4,
        mp1 = Math.random() * (max - min + 1) + min,
        min2 = ($('#siteContent__content').width()-100)/(
            $(window).width() < na.site.globals.reallySmallDeviceWidth
            ? 1 * mp1
            : $(window).width() < na.site.globals.smallDeviceWidth
                ? 2 * mp1
                : $(window).width() < 2000
                    ? 4 * mp1
                    : 5 * mp1
        ),
        min = 0.9,
        max = 1,
        mp1 = Math.random() * (max - min + 1) + min,
        max = ($('#siteContent__content').width()-100)/(
            $(window).width() < na.site.globals.reallySmallDeviceWidth
            ? 1 * mp1
            : $(window).width() < na.site.globals.smallDeviceWidth
                ? 2 * mp1
                : $(window).width() < 2000
                    ? 4 * mp1
                    : 5 * mp1
        ),
        w = Math.floor(Math.random() * (max - min2 + 1)) + min2,
        tw = 400,//$('.newsApp__item__title',el).outerWidth(),
        w1 = $('#siteContent .vividDialogContent').width()-50,


        min = 0.3,
        max = 0.55,
        mp1 = Math.random() * (max - min + 1) + min,
        min2 = ($('#siteContent__content').height()-200)/ (
            $(window).width() < na.site.globals.reallySmallDeviceWidth
            ? 1 * mp1
            : $(window).width() < na.site.globals.smallDeviceWidth
                ? 1.1 * mp1
                : $(window).width() < 2000
                    ? 1.3 * mp1
                    : 1.4 * mp1
        ),
        min = 0.3,
        max = 0.55,
        mp2 = Math.random() * (max - min + 1) + min,
        max = ($('#siteContent__content').height()-200)/(
            $(window).width() < na.site.globals.reallySmallDeviceWidth
            ? 1 * mp2
            : $(window).width() < na.site.globals.smallDeviceWidth
                ? 1.2 * mp2
                : $(window).width() < 2000
                    ? 1.4 * mp2
                    : 1.5 * mp2
        ),
        h = Math.floor(Math.random() * (max - min2 + 1)) + min2,
        h2 =
            !c.lastEl
            ? $('#newsApp_content').height() - 20
            : $('#newsApp_content').height() - c.lastEl.offset().top - 20;
        //console.log (el2.id+' - '+h+' - '+hA+' - '+h2+' - '+w+' - '+w1);
        //if (w > tw) w = tw;
        if (w > w1) w = w1;
        if (
            $(window).width() < na.site.globals.reallySmallDeviceWidth
            || $(window).width() < na.site.globals.smallDeviceWidth
        ) {
            if (w < w1) w = w1;
            if (
                h > h2
                || (
                    h > h2 / 2
                    && h < h2
                )
            ) var hf = h2; else var hf = h;
        } else var hf = h;
        //if (w < 300) w = 300;
        //if (h > h2) h= h2;
            //h = 'calc(100% - 20px)';
        var hf = h2;
        if (hf < 300) hf = 300;
        if (w < 320) w = 320;

        html += '<div id="newsApp__item__'+it.idx+'" class="newsApp__item__outer" style="width:'+w+'px;'+fontSize+';opacity:0.00001;">';
        html += '<div id="newsApp__item__'+it.idx+'__bg" class="newsApp__item__outer__bg" style="">';
            html += '<div id="newsApp__item__'+it.idx+'__bg___1" class="newsApp__item__outer__bgFile1" style="">';
            html += '&nbsp;</div>';
            html += '<div id="newsApp__item__'+it.idx+'__bg___2" class="newsApp__item__outer__bgFile2" style="">';
            html += '&nbsp;</div>';
        html += '</div>';

        if (typeof it.de=='string' && it.de.trim()!=='') {
            if (typeof it.t=='string' && it.t!='' && it.de.indexOf(it.t)===-1) html+= '<p class="newsApp__item__title"><a class="nomod" target="newsAppItem_'+it.idx+'" href="' + it._id+'">' + it.t.replace(/\&#39;/g, '\'').replace(/#39;/g, '\'')+ '</a></p>';
        } else {
            if (typeof it.t=='string' && it.t!='') html+= '<p class="newsApp__item__title newsApp__item__noPaint"><a class="nomod" target="newsAppItem_'+it.idx+'" href="' + it._id+'">' + it.t.replace(/\&#39;/g, '\'').replace(/#39;/g, '\'') + '</a></p>';
        }
        if (typeof it.de=='string' && it.de.trim()!=='') html+= '<div class="newsApp__item__contentContainer"><div class="newsApp__item__mediaSingle"></div><div id="newsApp_item_'+it.idx+'__scrollpane" class="newsApp__item__desc vividScrollpane" style="width:100%;"><div>' + it.de.replace(/\&#39;/g, '\'').replace(/#39;/g, '\'') + '</div></div></td></tr></table><div class="newsApp__item__noPaint" style="height:5px;overflow:hidden;"></div></div>';
        var appSettings = na.site.globals.app[na.site.globals.appPrefix+'applications/2D/news'];
        //debugger;
        if (appSettings.seoValue) {
            var seov = appSettings.seoValue;
            if (typeof seov=='object' && typeof seov.length=='number' && seov.length > 0) seov = seov[0];
        } else {
            var seov = appSettings.section.replace(/.*__/,'').replace('__','-').replace('_','-').toLowerCase();
        };
        html += '<div class="newsApp__item__footer">';
        html += '<span class="newsApp__item__copy">';
            //html += '<a class="nomod noPushState" href="javascript:debugger;var el = $(\'#newsApp__item__'+it.idx+'\')[0];">';


            html += '<a class="nomod noPushState" href="javascript:var el = $(\'#newsApp__item__'+it.idx+'\')[0], textarea = $(\'#siteContent__textareaCopy\')[0]; if (!textarea) { var el2=document.createElement(\'textarea\'); window.top.document.append(el2); textarea=el2 }; var el_html = el.innerHTML; el.innerHTML = el.innerHTML.replace(\/<span class..newsApp__item__copy.>.*<.a><.span>\/,\'\') + \'<span class=\\\'newsApp__item__date\\\'>' + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].formatDate(it).replace(/"/g, '\\\'').replace(/\>/g, '><br/>')+'</span><br/>Found via <a href=\\\'https://nicer.app/'+seov+'\\\' target=\\\'_new\\\'>https://nicer.app/'+seov+'</a>\n\'; var selection = window.getSelection(); var range = document.createRange(); range.selectNodeContents(el); selection.removeAllRanges(); selection.addRange(range); window.top.document.execCommand(\'copy\'); setTimeout(function(){selection.removeAllRanges(); el.innerHTML=el_html;},1000);">';

                html+= '<span class="newsApp__item__category"><span class="newsApp__item__categoryTitle">Category</span> <span class="newsApp__item__categoryColon">:</span> <span class="newsApp__item__categoryValue">'+it.path.substr(0, it.path.length)+'</span></span><br/>';
                html += '<span class="newsApp__item__date">' + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].formatDate(it)+'</span><br/>';
            html += 'Copy to clipboard</a></span>';
        html +='</span> ';
        html+= '</div>';
         
        // tooltipster HTML
        var html2 = '';
        html2+= '<span class="newsApp__item__url"><a class="nomod" target="newsAppItem_'+it.idx+'" href="' + it._id+'">Article</a> discovered via ';
        html2+= '<a class="nomod" target="newsAppItem_'+it.idx+'_rssURL" href="' + it.rssURL+'">'+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].urlToDomainName(it.rssURL)+'</a></span>';
        html2+= '<br/>';
        html2+= '<span class="newsApp__item__category"><span class="newsApp__item__categoryTitle">Category</span> <span class="newsApp__item__categoryColon">:</span> <span class="newsApp__item__categoryValue">'+it.path.substr(0, it.path.length)+'</span></span><br/>';
        var pd = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].formatDate(it);
        html2 += '<span class="newsApp__item__date">' + pd + '</span><br/>';

        $(container).append(html).delay(50);
        c.lastEl = $('#newsApp__item__'+it.idx);


        var
        el = $('#newsApp__item__'+it.idx)[0],
        th = $('.news__item__title', el).height(),
        fh = $('.newsApp__item__footer', el).height(),
        ph = $('p, div', el).height(),
        ph2 = 20 * $('p, div', el).length,
        //mh = Math.round((th?parseInt(th):0) + (fh?parseInt(fh):0) + (ph?parseInt(ph):0) + (ph2?parseInt(ph2):0) + 60);
        mh = hf - Math.round((th?parseInt(th)+20:0) + (fh?parseInt(fh)+20:0) + 120);

        $(el).css({maxHeight:hf,height:''});
        $('.newsApp__item__desc',el).css({maxHeight:mh});
        $('.newsApp__item__contentContainer',el).css({maxHeight:mh});

        $(el).find('div, p, span')
            .not('.vividScrollpane, .newsApp__item__outer__bg, .newsApp__item__outer__bgFile1, .newsApp__item__outer__bgFile2, .newsApp__item__noPaint')
            .each(function(idx,el2) {
                if (!$(el2).find('*')[0]) /*if (!$(el2).parent().is('div, p, cite'))*/ $(el2).addClass('newsApp__item__paint');
                if ($(el2).html().trim()=='') $(el2).remove();
            });
        $('.newsApp__item__title > p > a', el).each(function(idx,el) {
            var $p = $(el).parent('.newsApp__item__title > p');
            if ($p[0].innerHTML.replace(/<a>?.*<\/a>/g, '').replace(/<br\/>/g,'').trim() === '') {
                $p.addClass('disabled');
            }
        });

        $(el).find('table').css ({
            width : '100%',
            height : '100%'
        });

        /*
        $(el).find('img, iframe').each(function(idx,el){
            var
            p = $(el).parents('p, div')[0],
            l = $(el).offset().left - $(p).offset().left,
            w = $(p).width() - (2 * l);


            if ($(el).width() > w - 30)
                $(el).css({width:w-38});
        });
        */

        c.lastEl.css({opacity:1,display:'none'});
        $('#newsApp__item__'+it.idx).fadeIn('normal');

        return {
            // beware : algorithm sub-concious code owned by Nicer Enterprises.
            id : '#newsApp__item__'+it.idx,
            html : html,
            html2 : html2
        };
    },
    
    urlToDomainName : function (url) {
        if (typeof url!=='string' || url=='') return '';
        return url.replace('http://','').replace('https://','').replace(/\/.*/,'');
    },
    
    formatDateForHeader : function () {
        var 
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        m = c.dtEnd.getTime() > c.lastCurrentGet.getTime() ? c.dtEnd : c.lastCurrentGet,//new Date(),
        dns = Date.locale.en.day_names,
        tz = m.getTimezoneOffset() * 60 /*seconds*/ * 1000 /*milliseconds*/,
        tx = '' + ((-1 * m.getTimezoneOffset()) / 60),
        m1 = c.dtCurrent,
        style = $(window).width() < na.site.globals.smallDeviceWidth ? ' style="display:none"' : '',
        r1 = '<span class="newsApp__header__datetime" '+style+'>' + m.getFullYear() + "-" +
            ("0" + (m.getMonth()+1)).slice(-2) + "-" +
            ("0" + m.getDate()).slice(-2) + "(" + dns[m.getDay()] + ') ' +
            ("0" + m.getHours()).slice(-2) + ":" +
            ("0" + m.getMinutes()).slice(-2) + ":" +
            ("0" + m.getSeconds()).slice(-2) + '</span>',
        r2 = '<span class="newsApp__header__datetime" '+style+'>' + m1.getFullYear() + "-" +
            ("0" + (m1.getMonth()+1)).slice(-2) + "-" +
            ("0" + m1.getDate()).slice(-2) + "(" + dns[m1.getDay()] + ') ' +
            ("0" + m1.getHours()).slice(-2) + ":" +
            ("0" + m1.getMinutes()).slice(-2) + ":" +
            ("0" + m1.getSeconds()).slice(-2) + '</span>',
        r3 = '<span class="newsApp__header__datetime" '+style+'>' + m.getFullYear() + "-" +
            ("0" + (m.getMonth()+1)).slice(-2) + "-" +
            ("0" + m.getDate()).slice(-2) + "(" + dns[m.getDay()] + ') ' +
            ("0" + m1.getHours()).slice(-2) + ":" +
            ("0" + m1.getMinutes()).slice(-2) + ":" +
            ("0" + m1.getSeconds()).slice(-2) + "<span class=\"newsApp__header__datetime\" "+style+"> to </span>" +
            ("0" + m.getHours()).slice(-2) + ":" +
            ("0" + m.getMinutes()).slice(-2) + ":" +
            ("0" + m.getSeconds()).slice(-2) + '</span>';
                       
        rr = (
            m.getFullYear() === m1.getFullYear()
            && m.getMonth() === m1.getMonth()
            && m.getDate() === m1.getDate()
            ? r3
            : r2 + '<span class="newsApp__header__datetime" '+style+'> to </span>' + r1
        );
        
        return rr;
    },
    
    formatDateForLoading : function (dateStr, noPubDate, no2ndLine) {
        var 
        m = new Date(dateStr),
        dns = Date.locale.en.day_names,
        tz = m.getTimezoneOffset() * 60 /*seconds*/ * 1000 /*milliseconds*/,
        tx = '' + ((-1 * m.getTimezoneOffset()) / 60),
        r1 = m.getFullYear() + "-" +
            ("0" + (m.getMonth()+1)).slice(-2) + "-" +
            ("0" + m.getDate()).slice(-2) + '%20' +
            ("0" + m.getHours()).slice(-2) + ":" +
            ("0" + m.getMinutes()).slice(-2) + ":" +
            ("0" + m.getSeconds()).slice(-2) + '%20' +
            ( tx > 0 
                ? 'GMT%2B' + ("0" + tx).slice(-2) + '00'
                : tx < -9
                    ? 'GMT-0' + Math.abs(tx) + '00'
                    : 'GMT'+tx+'00'
            );
            
        return r1;
    },
    
    
    formatDate : function (it, noPubDate, no2ndLine) {
        var 
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        m = new Date(it.pd * 1000);
        
        //if (parseInt(m.getFullYear())==1970) return '<span class="newsApp__item__date__remote">[no pubDate field found for this news item]</span>';

        var
        dns = Date.locale.en.day_names,
        tz = m.getTimezoneOffset() * 60 /*seconds*/ * 1000 /*milliseconds*/,
        tx = '' + ((-1 * m.getTimezoneOffset()) / 60),
        m1 = new Date(m.getTime() + tz),
        r3 = '<span class="newsApp__item__date__local newsApp__item__noPaint">Local : ' + m.getFullYear() + "-" +
            ("0" + (m.getMonth()+1)).slice(-2) + "-" +
            ("0" + m.getDate()).slice(-2) + "(" + dns[m.getDay()] + ') ' +
            ("0" + m.getHours()).slice(-2) + " : " +
            ("0" + m.getMinutes()).slice(-2) + " : " +
            ("0" + m.getSeconds()).slice(-2) + '</span>', // + ' ' + tx
        /*
        r2 = '<span class="newsApp__item__date__remote">Local : ' + m1.getFullYear() + "-" +
            ("0" + (m1.getMonth()+1)).slice(-2) + "-" +
            ("0" + m1.getDate()).slice(-2) + "(" + dns[m1.getDay()] + ') ' +
            ("0" + m1.getHours()).slice(-2) + " : " +
            ("0" + m1.getMinutes()).slice(-2) + " : " +
            ("0" + m1.getSeconds()).slice(-2) + '</span>',
        r3 = '<span class="newsApp__item__date__remote">Remote : ' + m.toLocaleDateString('en-US') + ' ' + m.toLocaleTimeString('en-US') + '</span>',
        */
        r2 = '<span class="newsApp__item__date__local newsApp__item__noPaint">Local : ' + na1.formatDateTime(m1) + '</span>',
        r1 = '<span class="newsApp__item__date__remote newsApp__item__noPaint">Remote : ' + ( it.pubDate ? it.pubDate : na1.formatDateTime(m) ) + '</span>',
        
        r4 = (
            no2ndLine
            ? r1
            : noPubDate
                ? '<span class="newsApp__item__date__remote newsApp__item__noPaint">[no pubDate field found for news item]</span>'
                //? r1 + '<br/>' + r3 + '<br/><span class="newsApp__item__date__remote">[no pubDate field found for news item]</span>'
                : r1 + '<br/>' + r3
        );

        return r4;
    },
    
    formatDateTime : function (dt) {
        var dns = Date.locale.en.day_names;
        return dt.getFullYear() + "-" +
            ("0" + (dt.getMonth()+1)).slice(-2) + "-" +
            ("0" + dt.getDate()).slice(-2) + "(" + dns[dt.getDay()] + ') ' +
            ("0" + dt.getHours()).slice(-2) + " : " +
            ("0" + dt.getMinutes()).slice(-2) + " : " +
            ("0" + dt.getSeconds()).slice(-2);
    },
    
	getURLparameters : function () {
        var 
        prefix = '/NicerAppWebOS/apps/NicerAppWebOS/';
        x = na.site.globals.app[prefix+'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'];
        //debugger;
        return [
            x // just use this instead
        ];
	},
    
    preResize : function () {
    },

    hideOptionsDialog : function() {
        setTimeout (function () {
            var
            na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;
            if (!c.dontHide) {
                var el = $('#siteContent__btnOptions_menu');
                if (el.css('display')!='none') el.fadeOut('slow');
            }
        }, 1000);
    },

    showOptionsDialog : function() {
        var
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;
        c.dontHide = true;
        var el = $('#siteContent__btnOptions_menu');
        if (el.css('display')=='none') el.fadeIn('slow');

    },

    settingsChanged : function (evt) {
        var
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings,
        c = s.current, db = c.db;

        var theme = na.site.globals.themes[$('#themeChange_themeName')[0].innerText];
        if (!theme.apps) theme.apps = { '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news' : { } };
        else if (!theme.apps['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'])
            theme.apps['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'] = {};

        var tApp = theme.apps['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'];
        tApp.blankScreenBeforePageChange = $('#blankScreenBeforePageChange')[0].checked;
        tApp.blankScreenMin = $('#blankScreenMin').val();
        tApp.blankScreenMax = $('#blankScreenMax').val();
        tApp.delayNewItemsDisplayToPage = $('#delayNewItemsDisplayToPage')[0].checked;
        tApp.delayNewItemsMin = $('#delayNewItemsMin').val();
        tApp.delayNewItemsMax = $('#delayNewItemsMax').val();
        na.site.saveTheme();
        na1.themeAppsChanged();
    },
    
    themeAppsChanged : function () {
        var
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        theme = na.site.globals.themes[$('.na_themes_dropdown > .vividDropDownBox_selected').html()];

        if (theme && theme.apps) {
            var tApp = theme.apps['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'];
            if (tApp) {
                $('#blankScreenBeforePageChange')[0].checked = tApp.blankScreenBeforePageChange;
                $('#blankScreenMin').val(tApp.blankScreenMin);
                $('#blankScreenMax').val(tApp.blankScreenMax);

                c.blankScreenBeforePageChange = tApp.blankScreenBeforePageChange;
                c.blankScreenMin = tApp.blankScreenMin;
                c.blankScreenMax = tApp.blankScreenMax;

                $('#delayNewItemsDisplayToPage')[0].checked = tApp.delayNewItemsDisplayToPage;
                $('#delayNewItemsMin').val(tApp.delayNewItemsMin);
                $('#delayNewItemsMax').val(tApp.delayNewItemsMax);

                c.delayNewItemsDisplayToPage = tApp.delayNewItemsDisplayToPage;
                c.delayNewItemsMin = tApp.delayNewItemsMin;
                c.delayNewItemsMax = tApp.delayNewItemsMax;
            }
        }
    },

    onresize : function (settings, displayNews) {
        var
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings,
        c = s.current, db = c.db;

        if (displayNews!==false) displayNews = true;

        $('#siteContent__btnOptions_menu').detach().appendTo('body').css({display:'block',opacity:0.0001});
            $('.vividButton, .vividButton_icon_50x50_siteTop, .vividButton_icon_50x50', $('#siteContent__btnOptions_menu')[0])
                .each(function(idx,el){
                    if (!na.site.settings.buttons['#'+el.id])
                        na.site.settings.buttons['#'+el.id] = new naVividButton(el);
                });
        setTimeout (function() {
            $('#siteContent__btnOptions_menu').css({display:'block',opacity:0.0001});
            var bcr = $('#newsApp_options')[0].getBoundingClientRect();
            $('#siteContent__btnOptions_menu').css({display:'none',opacity:1}).animate({
                left : bcr.left + bcr.width - $('#siteContent__btnOptions_menu').outerWidth(),
                top : bcr.top + bcr.height + 5
            });
        }, 200);

        na1.themeAppsChanged();

        /*
        if (na.m.userDevice.isPhone) $('#btnOptions, #btnLoginLogout, #btnChangeBackground').css({opacity:1})
        else $('#btnOptions, #btnLoginLogout, #btnChangeBackground').animate({opacity:1},'normal');
        */
                       
        var 
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        ow = ($('#siteContent .vividDialogContent')[0].offsetWidth)-20,
        oh = $('#siteContent .vividDialogContent')[0].offsetHeight, 
        dw = (
            na.m.userDevice.isPhone
            ? ow - 2
            : ow > 430 ? 430 : ow
        ),  
        wf = ow / dw,
        iw = ( ow / wf ), // iw = initial width of news item (in pixels)
        iw = iw < dw ? dw : iw,
        iw = dw,
        wo = ow, // wo = width outer container
        wo = (
            true // $('#newsApp_content__sliderbar__ver')[0].style.display!=='none'
            ? wo
            : wo
        ),
        wt = 0, // wt = width target
        i = 0; // counter of news items measured horizontally
        
        while (wt < wo) {
            wt += iw;
            i++;
        }
        wt -= iw;
        i--;
        
        var
        wu = wt, // wu = width used
        wl = wo - wu, // wl = width left over
        we = (wl) / i; // we = width extra per news item (above iw)
        //w = iw + we - 60; // 50 or 60 is the magic number here.. don't know why..
        w = $('.newsApp__item__outer').width();


        na.m.waitForCondition ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news.onresize() : na.m.HTMLidle()?', na.m.HTMLidle, function () {
            $('#siteContent__header').css({
                display : 'inline-block',
                width : ow
            });
            $('#newsApp_content_shadow, #newsApp_content, #siteContent__content').css({
                height : oh - $('#siteContent__header').height() - 20,
                width : ow
            });
            na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.itemWidth = w;

            if (displayNews && c.dtCurrent) 
                na.m.waitForCondition(
                    '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news::onresize() : have news items to display?',
                    function () {
                        var r = c.unread > 0;
                        return r;
                    },
                    function () {
                        var
                        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'],
                        g = na1.globals, s = na1.settings, c = s.current, db = c.db, app = na.site.globals.app;


                        setTimeout (function() {
                            na1.onresize_do(s.dnf?s.dnf.id:undefined);
                            var doCountDown = false;
                            $('.newsApp__item__outer').each(function(idx3, el3) {
                                if (
                                    (
                                        $(el3).offset().top + $(el3).height()
                                        > $('#siteContent .vividDialogContent').offset().top
                                            + $('#siteContent .vividDialogContent').height()
                                    ) || (
                                        $(el3).offset().left + $(el3).width()
                                        > $('#siteContent .vividDialogContent').offset().left
                                            + $('#siteContent .vividDialogContent').width()
                                    )
                                ) {
                                    c.unread += 1;
                                    $(el3).remove();
                                    doCountDown = true;
                                }
                            });
                            if (doCountDown) {
                                $('.newsApp__header__countDownStr').animate({opacity:1});
                                na1.countDown();
                                //na1.loadNews_read_loop();
                            }
                        }, 50);

                        //$els2.css ({minWidth : '', width : 'auto'});
                    }, 50);
            if (typeof settings=='object' && settings!==null && typeof settings.callback=='function') settings.callback (settings);
        }, 50);
    },

    onresize_do : function (sel) {
        var
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;
//debugger;
        c.tries = 0;
        na1.displayNewNewsItems();
    }

};	
