na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'] = {
	about : {
		whatsThis : 'Application code for this news app (RSS reader)',
		copyright : 'Copyrighted (c) 2011-2022 by Rene AJM Veerman, Amsterdam, Netherlands',
		license : 'https://nicer.app/LICENSE.txt',
		firstCreated : '2018',
		lastModified : '2022-05-30 12:05 CEST (Amsterdam.NL timezone, summertime)',
        version : '3.1.1'
	},
	globals : {
        dtIntervalSeconds : 60,
        contentOpacity : 0,
        contentBorder : '0px solid black',
        contentBoxShadow : '0px 0px 0px 0px rgba(0,0,0,0)',
        readHistory_numHours : 1
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
                        setTimeout (function() {
                            na.menu.onresize('siteMenu');
                        }, 350);
                        na.desktop.onresize({reloadMenu : false});
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
                            
                            na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].nestedStartApp();
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
        
        na1.onresize();
        if (typeof s.settings_onload.callback=='function') {
            s.settings_onload.callback ('siteContent');
            s.settings_onload.callback_doneAlready = s.settings_onload.callback;
            delete s.settings_onload.callback;
        };
        
        
    },
    
    onkeyup : function (evt) {
        //debugger;
        if (evt.code=='AltRight' || evt.code=='AltLeft') na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].gotoNextPage();
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
        g.buffer = Math.round( ( ow / 440 ) * (oh/400) );
        if (g.buffer < 5) g.buffer = 5; // smartphone bugfix
        
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
                        top : $('#siteContent__header').height(),
                        left : 0,
                        width : $('#siteContent').width(),
                        height : $('#siteContent').height() - $('#siteContent__header').height(),
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
                            $('#newsApp_debug').html('');
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
                        c.displayCounts = '<span class="newsApp__header__displayCounts">' + (-1 * (g.buffer-parseInt(c.displayCounts))) + '</span>';
                        $('#newsApp_timer').html(na1.formatDateForHeader()+ ' ' +(c.displayCounts)+c.countDownStr);


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

                        if ($('#newsApp_content .newsApp__item__outer').length===0) c.timerDisplayNews_loop = setTimeout (na1.displayNews_loop, 250);

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
        c.dtCurrent = new Date(new Date().getTime() - (1000 * 60 * 10));
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

        settings.direction = 'past';
        
        g.buffer = Math.round( ( ow / 440 ) * (oh/400) );
        if (g.buffer < 5) g.buffer = 5; // smartphone bugfix
        
        
        na.m.walkArray (db, db, undefined, na1.displayNews_getDisplayCounts, false, params);
        var
        ks = Object.keys(dc),
        total = 0;
        
        ks = ks.sort(function(a,b){ b - a });
        
        var
        unread = dc[ks[0]];
        c.unread = unread;
        
        // get older news items when needed
        if (c.firstRun) {
            c.firstRun = false;
            c.failedLoads = 0;
            na1.loadNews_get_forDateTimeRange(c.dtCurrent, c.dtEnd, settings);
        } else if (parseInt(c.dtCurrent.getFullYear()) < 2022) {
            clearInterval (c.intervalMailLogCountdown);
            clearTimeout (c.timerDisplayNews_loop);
            $('#newsAppInfo').html('Loaded all the news available by now. Press F5 to start over.');
        } else if (unread < g.buffer) {
            c.dtEnd = c.dtCurrent;
            c.dtCurrent = new Date(c.dtEnd.getTime() - (1000 * 60 * c.read_loop_minutesIntoPast));
            na1.loadNews_get_forDateTimeRange (c.dtCurrent, c.dtEnd, settings);
            return false;
        } else {
            clearInterval (c.intervalMailLogCountdown);
            clearTimeout (c.timerDisplayNews_loop);
            
            $('#newsAppDebug').animate({opacity:0.001});
            
            if ($('#newsApp_content .newsApp__item__outer').length===0) c.timerDisplayNews_loop = setTimeout (na1.displayNews_loop, 250);
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
                        top : $('#siteContent__header').height(),
                        left : 0,
                        width : $('#siteContent').width(),
                        height : $('#siteContent').height() - $('#siteContent__header').height(),
                        opacity : 1
                    });



                    var
                    idxStart = c.idx;

                    na.m.log (2, 'loadNews_get_forDateTimeRange() : data=' + data);

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
                        c.read_loop_millisecondsToDoNext = 2000;
                    } else if (itemsLoadedCount < 100) {
                        c.read_loop_minutesIntoPast = 60 * (g.readHistory_numHours/2);
                        c.read_loop_millisecondsToDoNext = (1 * 1000 * 60) * 30;
                    } else {
                        c.read_loop_minutesIntoPast = 60;
                        c.read_loop_millisecondsToDoNext = (1 * 1000 * 60) * 60;
                    };

                    na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.current.db = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.current.db.concat(data);

                    if (itemsLoadedCount===0) {
                        c.timerLoadNews_read_loop = setTimeout (na1.loadNews_read_loop, c.read_loop_millisecondsToDoNext);
                        return false;
                    } else {
                        clearInterval (c.intervalMailLogCountdown);
                        $('#newsApp_debug').animate({opacity:0.001}, {complete :function () {
                            $('#newsApp_debug').html('');
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
                    console.log (dbg);



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
                    c.displayCounts = '<span class="newsApp__header__displayCounts">' + (-1 * (g.buffer-parseInt(c.displayCounts))) + '</span>';
                    $('#newsApp_timer').html(na1.formatDateForHeader()+ ' ' +(c.displayCounts)+c.countDownStr);


                    c.timerLoadNews_read_loop = setTimeout (na1.loadNews_read_loop, c.read_loop_millisecondsToDoNext);
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
        if (cd.v) {
            //for (var i=0; i<cd.v.items.length; i++) {
                var it = cd.v;
                
                var k = it.displayCount;
                
                if (typeof cd['params']['dc'][k]=='undefined') 
                    cd['params']['dc'][k] = 1; 
                else 
                    cd['params']['dc'][k]++;

                if (typeof cd['params']['di'][k]=='undefined') 
                    cd['params']['di'][k] = []; 
                else {
                    var key = cd['params']['di'][k].length;
                    cd['params']['di'][k][key] = it;
                }
            //}
        }        
    },
    
    displayNews_loop : function (state) {
        if (state === undefined) state='displaying';
        var 
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        urlp = na.site.globals.app,//na1.getURLparameters(),
        container = $('#newsApp_content');
        na.m.log (610, 'displayNews_loop() : state='+state);

        if (typeof state == 'undefined') state = c.state;
        if (typeof state == 'undefined') state = 'testing';
        c.state = state;

        //$('#siteContent__content').css ({ top : 0 });
        for (var k in urlp) {
            $('#newsApp_title').html (urlp[k].section.replace(/__/g, ' ').replace(/_/g, ' '));
            break;
        }
        //$('#newsApp_content .newsApp__item__outer').animate({opacity:1},'normal');

        if (s.loaderIcon) {
            $(s.loaderIcon).fadeOut('slow', function() {
                $(s.loaderIcon).remove();
                delete s.loaderIcon;
            });
        }
        
        c.tries++;
    
        var
        // dc means displayCount, an elaborate way to make sure entries that havent been shown too often end up on the screen quickly.
        dc = { 0 : 0 },
        di = { 0 : [] },
        params = {
            dc : dc,
            di : di
        };
        
        na.m.walkArray (db, db, undefined, na1.displayNews_getDisplayCounts, false, params);
        
        var
        ks = Object.keys(dc),
        total = 0,
        highest = 10,        
        ks = ks.sort(function(a,b){ b - a }),        
        unread = dc[ks[0]];

        if (!g.searchQuery) {
            if (unread < g.buffer) {
                na1.loadNews_read_loop();
                //return false;
            }
        } else {
            if (unread < g.buffer) {
                c.dtEnd = c.dtCurrent;
                c.dtCurrent = new Date(c.dtEnd.getTime() - (1000 * 60 * c.read_loop_minutesIntoPast));
                na1.loadNews_searchResults_loop(c.dtCurrent, c.dtEnd, s);
            }
        }
        
        c.displayCounts = '';
        for (k in ks) {
            if (c.displayCounts!=='') c.displayCounts +=', ';
            c.displayCounts += dc[ks[k]];
        };
        c.displayCounts = '<span class="newsApp__header__displayCounts">' + (-1 * (g.buffer-parseInt(c.displayCounts))) + '</span>';
        $('#newsApp_timer').html(na1.formatDateForHeader()+ ' ' +c.displayCounts+c.countDownStr);

        let 
        //numKeys = Object.keys(params.di).length-1,
        //its = di[c.maxDisplayCount-1>0?c.maxDisplayCount-1:0], 
        found = false,
        it = null;
        
        var l = 0;
        //if (!di[i]) na1.loadNews_read_loop();
        while (di[l] && di[l].length==0) l++;
        its = di[l];

        if (!its) return false;

        while (it===null) {
            var
            key = Math.floor(Math.random() * its.length);
            
            it = its[key];
            it.displayCount = 0;
            
            if (!it) debugger;
            if (typeof it.displayCount=='number' && it.displayCount > 0) {
                it = null;
            } else {
                var els2 = $('#newsApp_content .newsApp__item__outer');
                for (var j=0; j<els2.length; j++) {
                    if (els2[j].innerHTML.indexOf(it.t)!==-1) found = true;
                }
            }
            //console.log ('displayNews_loop() (3)', its, key, it);
        };
        //itEl = $('#newsApp__item__'+it.idx)[0];
        
                
        if (found || !na1.match_searchCriteria(it)) {
            //debugger;
            c.timerDisplayNews_testing_loop = setTimeout (function () {
                c.tries--;
                na1.displayNews_loop ('testing');
            }, 50);
            return false;
        } else {
            var dnfi = na1.displayNews_formatItem (it, '');
            //na.m.log (1, {msg:'displayNews_loop(): state='+state, dnfi:dnfi});
            if (!dnfi) {
                //debugger;
                var state = 'testing';
                c.state = state;
                //na.m.log (1, {msg:'displayNews_loop(): state reset ='+state});
                na1.displayNews_loop(state);
                return false;
            };
                
            var 
            states = [ 'testing', 'displaying', 'finished' ];
            //debugger;
            if (!state) var state = 'testing';
            c.state = state;
            //console.log ('NEW: '+state + ' - ' +it.idx);
            //console.log ('0: c.tries='+c.tries+', getToTry='+getToTry);

            if (!c.getToTry) c.getToTry = g.buffer;

            if (c.tries === c.getToTry) {
                //debugger;
                na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.dnf.full = true;
                na1.countDown();
                return true;
            }


            
            function displayNewsItem(state) {
                if (!na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news']) return false;
                       
                //na.m.log (1, {msg : 'displayNewsItem(state='+state+')'});

                var 
                na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;
                
                if (state=='testing') {
                    var prefix = it.idx+'___';
                } else if (state=='displaying') {
                    var prefix = '';
                    it.displayCount++;
                    if (it.displayCount > c.maxDisplayCount) c.maxDisplayCount = it.displayCount;                       
                } else return true;
                         
                var
                dnfi = na1.displayNews_formatItem (it, prefix),
                html = dnfi.html,
                htmlTooltip = dnfi.html2;
                       
                if (!c.resize) c.resize={};
                c.resize[prefix+'newsApp__item__'+it.idx] = function (t, event, itIDX, prefix) {
                    if (!na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news']) return false;

                    var 
                    itEl = $('#'+prefix+'newsApp__item__'+itIDX)[0],
                    sp = $('.vividScrollpane', itEl)[0];
                    
                    if (!itEl) {
                        return false;
                    }
                    //na.m.log (1, {msg : 'c.resize['+prefix+'newsApp__item__'+it.idx+']', dnfi:dnfi, state:state});
                    if (sp && sp.scrollHeight > 300) {
                        var
                        jel2 = $('.newsApp__item__mediaSingle',itEl);
                        $('img, iframe', sp).not('.newsApp__item__mediaSingle__0').not('[width="1"]').not('[border="1"]').not('.dontResize').each(function(idx,el) {
                            $(el).css ({
                                margin : 0,
                                border : '0px solid black',
                                float : '',
    //                            width : sp.offsetWidth - 30,
                                    width : '100%',//$(itEl).width() - jel2.width() - $(el).position().left - 50,
                                height : ''
                            });
                            el.removeAttribute('height');
                            el.removeAttribute('width');
                        });
                    } 
                };
                
                html = html.replace ('<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>','');
                html = html.replace (/http:\/\//g, 'https://');
                try {
                    if (state=='testing') {
                        $('#newsApp_content_shadow').append(html.replace(/id="/g, 'id="'+prefix));
                    } else if (state=='displaying') {
                        $('#newsApp_content').append(html);
                        //if (html.indexOf('vividScrollpane')!==-1) debugger;
                        
                    } else return true;
                } catch (e) {
                    state='testing';
                    c.state = state;
                    na1.displayNews_loop(state);
                    return false;
                }

                //console.log ('1: c.tries='+c.tries+', getToTry='+getToTry);   
                       
                if (c.timerDisplayNewsItem) clearTimeout(c.timerDisplayNewsItem);
                c.timerDisplayNewsItem = setTimeout (function (it, prefix, state) {
                    if (!na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news']) return false;
                    
                    var 
                    itEl = $('#'+(state=='testing'?prefix:'')+'newsApp__item__'+it.idx)[0],
                    thumbWidth = (
                        na.m.userDevice.isPhone
                        ? 80
                        : 150
                    );
                    if (!itEl) {
                        //debugger;
                        state = 'testing';
                        c.state = state;

                        na1.displayNews_loop(state);
                        return false;
                    };
                    
                    //na.m.log (1, {msg : 'c.timerDisplayNewsItem() : itEl.id='+itEl.id, state:state});
                    
                    $('.feedflare', itEl).remove();
                    
                    itEl.db = it;
                    //console.log ('dni : '+state + ' - ' +itEl.id);
                    itEl.htmlTooltip = htmlTooltip;
                    
                    if (state==='displaying') {
                        $(itEl).tooltipster({
                            theme : 'newsAppTooltipTheme',
                            contentAsHTML : true,
                            content : htmlTooltip
                        });                        
                    }
                    
                    
                    $('img', itEl).each (function (idx,el) {
                        if (this.src.match(/cnn_freevideo/)) $(this).addClass('dontResize');
                        if (this.src.match(/cnn_latest/)) $(this).addClass('dontResize');
                    });
                    
                    // resize item
                    var 
                    imgs = $('img, iframe', itEl).not('.newsApp__item__mediaSingle__0').not('[width="1"]').not('[border="1"]').not('.dontResize');
                    
                    imgs.each(function(idx,el){
                        if ($(this).width() > na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.itemWidth - 10) {
                            $(this).css ({
                                width : na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.itemWidth - 10,
                                height : ''
                            })
                            el.removeAttribute('height');
                            el.removeAttribute('width');
                        }
                    })
                    
                    $('.vividScrollpane td > img', itEl).not('.newsApp__item__mediaSingle__0').not('[width="1"]').not('.dontResize').css ({
                        width : thumbWidth,
                        height : ''
                    }).each(function(idx,el){
                        el.removeAttribute('height');
                        el.removeAttribute('width');
                    });

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
                                
                                //$(ms).append($.parseHTML(html, document, true));
                                $(ms)[0].innerHTML = html;
                            }
                        };
                    }
                    
                    //if (state=='displaying') {
                    if (c.timerDisplayNewsItem) clearTimeout (c.timerDisplayNewsItem);
                    c.timerDisplayItem = setTimeout (function (it, itEl, prefix) {
                        //if (itEl.offsetTop + itEl.offsetHeight < container.offsetHeight) {
                        if (!na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news']) return false;

                        var id = prefix+'newsApp__item__'+it.idx;
                        c.resize[id](undefined, undefined, it.idx, prefix);
                        //}
                    }, (state=='testing'?10:10), it, itEl, prefix);
                    //}
                    //console.log ('2: c.tries='+c.tries+', getToTry='+getToTry);
                    if (c.timerCheck) clearTimeout (c.timerCheck);
                    c.timerCheck = setTimeout (function(itEl, prefix, state) {
                        if (!na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news']) return false;
                                  
                        var 
                        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
                        dnf = na1.displayNews_full(prefix),
                        full = dnf.full,
                        removed = dnf.removed;

                        //console.log ('prefix='+prefix+', full='+(full?'true':'false')+', removed='+(removed?'true':'false'));

                        if (prefix=='') {
                            na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.dnf = dnf;
                            if (full) na1.countDown();
                        };                        
                    
                        //na.m.log (1, {msg : 'c.timerCheck() : itEl.id='+itEl.id, dnf:dnf, state:state});
                        clearTimeout(c.timerAnimateItemIn);
                        //debugger;
                        if (!full && !removed) 
                        c.timerAnimateItemIn = setTimeout (function(itEl, removed, state) {
                            var 
                            na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;
                            if (!na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news']) return false;
                    
                            if (!removed && state=='displaying') {
                                //if (!itEl || !itEl.className || itEl.className.indexOf('newsApp__item__outer')!==-1 || !itEl.id.match(/newsApp__item__\d+.*/))
                                  //  $(itEl).animate({opacity:1}, 500);
                                $('.newsApp__item__outer', $('#newsApp_content')[0]).css({height:'',maxHeight:450}).animate({opacity:1}, 500);
                            } 

                            //debugger;
                            if (!removed && state=='testing') {
                                $('.lds-facebook').fadeOut('normal');
                                state = 'displaying';

                                c.getToTry = g.buffer;

                                if ($('img, iframe', itEl).not('[width="1"]').not('[border="1"]').not('.dontResize').length>0) {
                                    c.timerDisplayNewsItem =setTimeout (displayNewsItem, 500, state);
                                } else displayNewsItem(state);
                                return false;
                            };
                            
                            if (state=='displaying' && c.tries < c.getToTry) {
                                //debugger;
                                state = 'testing';
                                c.state = state;
                                na1.displayNews_loop(state);
                                return true;
                            }                                
                        }, (state=='testing'?100:100), itEl, removed, state);
                        
                        if (prefix!=='' && full) {
                            na1.countDown();
                        } else if (removed && c.tries < c.getToTry) {
                            state = 'testing';
                            c.state = state;
                            na1.displayNews_loop(state);
                            return false;
                        } 
                        
                        //delete c.timerDisplayNewsItem;
                    }, 50, itEl, prefix, state)
                }, 50, it, prefix, state);
            };
            displayNewsItem(state);
        };
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
    
    countDown : function () {
        let 
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        container = $('#newsApp_content'),
        l = $('.newsApp__item__outer', container).length,
        countDown = (
            l < 15
            ? 15
            : 30
        );
        countDown = 10 + Math.round(l * 2.2);
        c.onScreen = l;
        
        if (c.countDownInterval) clearInterval(c.countDownInterval);
        //debugger;
        if (!na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.dnf.full) return false;
        
        
        c.countDownInterval = setInterval (function() {
            if (!na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news']) return false;

            if (!c.locked) countDown--;
            c.countDown = countDown;
            c.countDownStr = ' -'+countDown;
            $('#newsApp_timer').html(na1.formatDateForHeader()+ ' ' +(c.displayCounts)+c.countDownStr);
            if (countDown==0 || c.gotoNextPage) {
                clearInterval(c.countDownInterval);
                $(container).fadeOut(500, function() {
                    container.html ('').fadeIn('fast');
                    $('#newsApp_content_shadow').html('');
                    c.its3 = [];
                    c.resize = {};
                    c.tries = 0;
                    delete c.gotoNextPage;
                    na1.onresize();
                    state = 'testing';
                    na1.displayNews_loop();
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
            : $('#newsApp_content_shadow')
        ),
        its = $('.newsApp__item__outer', container),
        full = false,
        removed = false,
        its2 = [],
        max = 0, max2 = 0;
        
        for (var i=0; i<its.length; i++) {
            var 
            it = its[i],
            y = $(it).position().top + it.offsetHeight,
            y2 = $(it).position().top;
            
            if (y > max) max = y;
            if (y2 > max2) max2 = y2;
            if (c.its3.length > 0 && y > container[0].offsetHeight - 20 ) { // - 20 for margin and padding of news item
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
        if (prefix=='' && max > container.height()-50) full = true;

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
        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
        fontSize = (
            na.m.userDevice.isPhone
            ? ';font-size:0.8rem;'
            : ';font-size:1rem;'
        ),
        w = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.itemWidth;
        
        if (
                ( !it.de || (typeof it.de=='string' && it.de=='') )
                && ( !it.t || (typeof it.t=='string' && it.t=='') )
        ) {
            debugger;
            return false;
        }
        
        if (!it) return false;
        if (typeof it.de=='string') {
            it.de = na1.filterNews__userInterfaceStage_level1(it.de, /tabIndex=.*;/g, '');
            
            // beware : more algorithm sub-concious code owned by Nicer Enterprises :
            // these are the visual reformatting sections of that computer code.
            it.de = it.de.replace(/<a?.*><br\/><\/a>/g, '');
            it.de = it.de.replace(/<p>\s*<\/p>/g, '');
            it.de = it.de.replace(/<p><\/p>/g, '');
            it.de = it.de.replace(/<p>\s*<br\/>\s*<\/p>/g, '');
            it.de = it.de.replace(/6f6f6f/g, 'BBBBBB');
            it.de = it.de.replace(/style=".*?"/g, '');
            it.de = it.de.replace('div class="feedflare"', 'div class="feedflare" style="display:none"');
            it.de = it.de.replace(/<img/g, '<img style="width:auto" onload="var na1 = na.apps.loaded[\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news\'], g = na1.globals, s = na1.settings, c = s.current;  var ni = $(this).parents(\'.newsApp__item__outer:first\');  if (ni[0]) { var dnf = na1.displayNews_full(\''+prefix+'\'), id = ni[0].id, resize = c.resize[id];  if (prefix==\'\') na.apps.loaded[\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news\'].settings.dnf = dnf; if (!dnf.removed && typeof resize==\'function\') setTimeout(function() { resize(this,event,'+it.idx+',\''+prefix+'\'); }, 100); if (dnf.removed) { ni.remove(); }; if (dnf.removed && c.tries < g.onScreen + 1000) na1.displayNews_loop(); /*else { if (dnf.removed) na1.countDown(); }*/}"');
        }
        
        //var html = '<div id="newsApp__layoutGuide1" style="width:1px;height:400px;display:inline-block"></div>';
        var html = '';
        html += '<div id="newsApp__item__'+it.idx+'" class="newsApp__item__outer" style="height:400px;width:'+w+'px;'+fontSize+';opacity:0.0001">';
        html += '<div id="newsApp__item__'+it.idx+'__bg" class="newsApp__item__outer__bg" style="">&nbsp;</div>';
        
        if (typeof it.de=='string') {
            if (typeof it.t=='string' && it.t!='' && it.de.indexOf(it.t)===-1) html+= '<p class="newsApp__item__title"><a class="nomod" target="newsAppItem_'+it.idx+'" href="' + it._id+'">' + it.t.replace(/\&#39;/g, '\'').replace(/#39;/g, '\'')+ '</a></p>';
        } else {
            if (typeof it.t=='string' && it.t!='') html+= '<p class="newsApp__item__title"><a class="nomod" target="newsAppItem_'+it.idx+'" href="' + it._id+'">' + it.t.replace(/\&#39;/g, '\'').replace(/#39;/g, '\'') + '</a></p>';
        }
        if (typeof it.de=='string') html+= '<table><tr><td class="newsApp__item__mediaSingle"></td><td><div id="newsApp_item_'+it.idx+'__scrollpane" class="newsApp__item__desc vividScrollpane" style="max-width:'+(w-25)+'px;max-height:300px">' + it.de.replace(/\&#39;/g, '\'').replace(/#39;/g, '\'') + '</div></td></tr></table><div style="height:1px;overflow:hidden;">&nbsp;</div>';
        var appSettings = na.site.globals.app[na.site.globals.appPrefix+'applications/2D/news'];
        //debugger;
        if (appSettings.seoValue) {
            var seov = appSettings.seoValue;
        } else {
            var seov = appSettings.section.replace(/.*__/,'').replace('__','-').replace('_','-').toLowerCase();
        };
        html += '<span class="newsApp__item__date"><a class="nomod" target="newsAppItem_'+it.idx+'" href="' + it.u+'">' + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].formatDate(it)+'</a></span><br/>';
        html += '<span class="newsApp__item__copy"><a class="nomod" href="javascript:var el = $(\'#newsApp__item__'+it.idx+'\')[0], textarea = $(\'#siteContent__textareaCopy\')[0]; if (!textarea) { var el2=document.createElement(\'textarea\'); window.top.document.append(el2); textarea=el2 }; el_html = el.innerHTML; debugger; el.innerHTML = el.innerHTML.replace(\/<span class..newsApp__item__copy.>Copy to clipboard<.a><.span>>\/,\'\') + \'Found via <a href=\\\'https://nicer.app/'+seov+'\\\' target=\\\'_new\\\'>https://nicer.app/'+seov+'</a>\'; var selection = window.getSelection(); var range = document.createRange(); range.selectNodeContents(el); selection.removeAllRanges(); selection.addRange(range); window.top.document.execCommand(\'copy\');setTimeout(function(){selection.removeAllRanges(); el.innerHTML=el_html;},1000);">Copy to clipboard</a></span> ';
        html+= '</div>';
         
        // tooltipster HTML
        var html2 = '';
        html2+= '<span class="newsApp__item__url"><a class="nomod" target="newsAppItem_'+it.idx+'" href="' + it._id+'">Article</a> discovered via ';
        html2+= '<a class="nomod" target="newsAppItem_'+it.idx+'_rssURL" href="' + it.rssURL+'">'+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].urlToDomainName(it.rssURL)+'</a></span>';
        html2+= '<br/>';
        html2+= '<span class="newsApp__item__category"><span class="newsApp__item__categoryTitle">Category</span> <span class="newsApp__item__categoryColon">:</span> <span class="newsApp__item__categoryValue">'+it.path.substr(0, it.path.length)+'</span></span><br/>';
        var pd = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].formatDate(it);
        html2 += '<span class="newsApp__item__date">' + pd + '</span><br/>';
        
        
        return {
            // beware : algorithm sub-concious code owned by Nicer Enterprises.
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
        r1 = '<span class="newsApp__item__date__local">Local : ' + m.getFullYear() + "-" +
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
        r2 = '<span class="newsApp__item__date__remote">Local : ' + na1.formatDateTime(m1) + '</span>',
        r3 = '<span class="newsApp__item__date__remote">Remote : ' + ( it.pubDate ? it.pubDate : na1.formatDateTime(m) ) + '</span>',
        
        r4 = (
            no2ndLine
            ? r1
            : noPubDate
                ? '<span class="newsApp__item__date__remote">[no pubDate field found for news item]</span>'
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
        var $els1 = $('#newsApp_content_shadow, #newsApp_content, #siteContent__header');
        //$els1.css({display:'none'});
    },
    
    onresize : function (settings, displayNews) {
        na.desktop.resize();
        if (displayNews!==false) displayNews = true;
                       
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
        w = iw + we - 60; // 50 or 60 is the magic number here.. don't know why..
        
        
        var 
        $els2 = $('.newsApp__item__outer'),
        $els3 = $('#newsApp_content > .newsApp__item__outer');
        $('#newsApp_content_shadow').css({ overflow : 'hidden', opacity : 0.00001 });
        na.m.waitForCondition ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news.onresize() : na.m.HTMLidle()?', na.m.HTMLidle, function () {
            $('#siteContent__header').css({
                display : 'inline-block',
                width : ow
            });
            $('#newsApp_content_shadow, #newsApp_content').css({
                height : oh - $('#siteContent__header').height(),
                width : ow
            });
            na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.itemWidth = w;

            $els2.css({height:'',width:''}).fadeIn('fast');
            $els3.css({height:'',width:'', opacity:1});
            $els3.find('img').each(function(idx,el){
                if ($(el).parent('p')[0]) {
                    var w =
                        $(el).parents('.newsApp__item__outer').width()
                        - $(el).parent('p').position().left
                        - parseInt($(el).parent('p').css('paddingLeft'))
                        - parseInt($(el).parent('p').css('paddingRight'))
                        - parseInt($(el).parent('p').css('marginLeft'))
                        - parseInt($(el).parent('p').css('marginRight'));
                    $(el).css({width:w});
                }
            });
            
            
            if (displayNews && c.dtCurrent) 
                na.m.waitForCondition(
                    '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news::onresize() : have news items to display?',
                    function () {
                        var r = c.unread > 0;
                        return r;
                    },
                    na1.displayNews_loop,
                    50
                );
            if (typeof settings=='object' && settings!==null && typeof settings.callback=='function') settings.callback (settings);
        }, 50);
    }
    
};	
