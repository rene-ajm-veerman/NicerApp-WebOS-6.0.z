if (!na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'])
    na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'] = {
        about : {
            whatsThis : 'Application code for this news app (RSS reader)',
            copyright : 'Copyright (C) 2011-2025 by Rene AJM Veerman [rene.veerman.netherlands@gmail.com], Amsterdam, Netherlands',
            license : 'https://nicer.app/license [MIT]',
            firstCreated : '2018',
            lastModified : '2026-04-18(Saturday)',
            version : '3.3.0'
        },
        globals : {
            readHistory_numHours : 1
        },
        settings : {
            idx : 0,
            loadedIn : {
                '#siteContent' : {
                    settings : {
                        initialized : false,
                        idx : 0
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
                            window.top.na.s.s.transformLinks ($('#newsApp_mainmenu')[0]);
                            mainmenu.linksTransformedAlready = true;
                        }
                        $('#newsApp_mainmenu').css({display:'none'});
                        na.menu.merge ('siteMenu', 'na.site.code.transformLinks', '-saLinkpoint-appMenu', $('#newsApp_mainmenu')[0], undefined, false);
                        na.desktop.onresize({reloadMenu : true});
                    },

                    onload : function (settings) {
                        if (settings.callbackParams) {
                            settings.callback();
                            return false;
                        };

                        $('.lds-facebook').fadeIn('normal');
                        na.m.waitForCondition ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news start', na.m.HTMLidle, function () {
                            var
                            na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
                            loadedIn = s.loadedIn['#siteContent'];

                            na.site.startUIvisuals('#siteContent');

                            $('#siteContent .vividDialogContent').css({overflow:'hidden'});

                            //na.desktop.setConfig('content');
                            s.onHold = true; // signals a wait for na.site.loadTheme() has started

                            $('#siteContent__header').fadeIn('normal', function () {
                                $('#siteContent__header').css({display:'flex'});
                            });

                            na1.themeAppsChanged();


                            na.m.waitForCondition('news app : siteContent dialog reappearance', function () {
                                return (
                                    na
                                    && na.site
                                    && na.site.s.c
                                    && na.site.s.c.booted
                                    && na.desktop
                                    && !na.desktop.settings.animating



                                    /*$('#jsPageSpecific').length > 0
                                     *                                && $('#siteContent__content').length > 0
                                     *                                && na.m.desktopIdle()
                                     *                                &&
                                     */
                                );
                                return true;
                            }, function () {
                                na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.loadedIn['#siteContent'].onresize();
                                na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.loadedIn['#siteContent'].settings.initialized = true;
                                na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.loadedIn['#siteContent'].settings.ready = true;
                                document.addEventListener ('keyup', na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].onkeyup);

                                delete na.m.settings.locked_displayNewNewsItems;

                                setTimeout (na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].nestedStartApp, 500);
                                na.site.onresize_doContent({});
                                na.m.preventScreenLock();

                                if (typeof settings.callback=='function') settings.callback('siteContent');
                                //na.analytics.logMetaEvent ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news (version '+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].about.version+') is starting.');

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

                        clearInterval(s.countDownInterval);
                        clearTimeout (s.refreshTimer);
                        clearTimeout (s.adsTimer1);
                        clearTimeout (s.adsTimer2);
                        clearInterval(s.adsInterval1);
                        clearTimeout (s.timerDisplayNews_loop);
                        clearTimeout (s.timerLoadNews_read_loop);
                        clearTimeout (s.timerDisplayNewsItem);
                        clearTimeout (s.timerDisplayItem);
                        clearTimeout (s.timerAnimateItemIn);
                        clearTimeout (s.timerCheck);
                        clearInterval (s.newItemsInterval);
                        clearInterval (s.intervalMailLogCountdown);

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
                idx : 0,
                db : [], // as of version 2.1.0, we store news as it comes in via ajax_get_items.php, as a recursive array only.
                its3 : [],
                tries : 0,
                maxDisplayCount : 0,
                onScreen : 30,
                locked : false,
                countDown : 100,
                countDownStr : '',
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
            na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;
            //na.analytics.logMetaEvent ('start app : applications/2D/news.v'+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].about.version);

            $('#siteContent .vividButton_icon_50x50').each(function(idx,el){
                if (!na.site.components.buttons['#'+el.id]) na.site.s.buttons['#'+el.id] = new vividUserInterface_2D_button ({ naSite : na.site, el : el });
            });
            /*
             *        var loaderIconTheme = na.s.s.globals.loaderIconTheme('appLoading');
             *        na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.loaderIcon = na.acs.addIcon(
             *            true, //whether or not to absolutely position
             *            $('#siteContent__dialog')[0], //parent element to stick icon to (will be positioned in the middle of the parent element)
             *            180, 180, //width and height in pixels
             *            loaderIconTheme, //see var theme above
             *            true //start running immediately
             *        );*/

            na1.onresize(null, false);
            $(window).resize(na1.onresize);

            s.dtCurrent = new Date(new Date().getTime() - (1000 * 60 * 60 * g.readHistory_numHours)); // read back 1 hour initially
            s.dtEnd = new Date();
            s.lastCurrentGet = new Date();
            na1.settingsChanged();


            if (!s.dtStart) s.dtStart = new Date();
            if (!s.intervalMailLogCountdown) s.intervalMailLogCountdown = setInterval (na1.intervalMailLogCountdown, 250);
            if (!s.read_loop_minutesIntoPast) s.read_loop_minutesIntoPast = 60 * g.readHistory_numHours;
            if (!s.read_loop_millisecondsToDoNext) s.read_loop_millisecondsToDoNext = 1000;

            s.firstRun = true;
            s.loads = 0;
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
            s.displayCounts = '';
            if (ks.length > 0) {
                for (k in ks) {
                    if (s.displayCounts!=='') s.displayCounts +=', ';
                    s.displayCounts += dc[ks[k]];
                };
                s.displayCountInt = s.displayCounts;//- g.numItemsThatFitScreen;
                s.displayCounts = '<span class="newsApp__header__displayCounts">' + s.displayCountInt + '</span>';
                $('#newsApp_timer').html(
                    '<span class="newsApp__header__dateRange" style="padding:0px !important">'+na1.formatDateForHeader()+'</span>'
                    +' ' +(s.displayCounts)
                    +' <span class="newsApp__header__timer" style="opacity:0.0001; padding:0px !important">'+s.countDownStr+'</span>'
                );
            }

            //na1.onresize();
            if (s.settings_onload && typeof s.settings_onload.callback=='function') {
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

            clearTimeout (s.timerLoadNews_read_loop);
            clearInterval (s.intervalMailLogCountdown);
            clearTimeout (s.timerDisplayNews_loop);
            clearTimeout (s.timerDisplayNews_testing_loop);
            clearTimeout (s.timerDisplayNewsItem);
            clearTimeout (s.timerDisplayItem);
            clearTimeout (s.timerCheck);
            clearTimeout (s.timerAnimateItemIn);
            clearInterval (s.newItemsInterval);
            clearInterval (s.countDownInterval);

            s.searchQuery = input.val().replace(' ', '%20');
            s.dtCurrent = new Date(new Date().getTime() - (1000 * 60 * 60 * g.readHistory_numHours));
            s.dtEnd = new Date();
            s.lastCurrentGet = new Date();
            s.locked = false;
            s.firstRun = true;
            s.tries = 0;
            s.loads = 0;
            s.failedLoads = 0;
            na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.current.db = [];

            $('.newsApp__item__outer').remove();

            na1.loadNews_searchResults_loop (s.dtCurrent, s.dtEnd, settings);
        },

        loadNews_searchResults_loop : function (dtBegin, dtEnd, settings) {
            var
            fncn = 'na.apps.loaded["applications/2D/news"].loadNews_searchResults_loop(dtBegin,dtEnd,settings)',
            na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
            input = $('#newsApp_searchbar');
            settings.url = input.val().replace(' ', '%20');
            s.searchQuery = input.val().replace(' ', '%20');
            s.lastCurrentGet = new Date();
            s.loads++;

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
                !s.lastCurrentGet
                || s.lastCurrentGet.getTime() < dtBegin.getTime() - (1000 * 60 * 60 * g.readHistory_numHours)
            ) {
                s.lastCurrentGet = dtBegin;
                s.dtEnd = new Date(s.dtCurrent.getTime());
                s.dtCurrent = new Date(dtEnd.getTime() - (1000 * 60 * 60 * g.readHistory_numHours)), dtEnd
                settings.dateBegin = s.dtCurrent;
                settings.dateEnd = s.dtEnd;
                na1.loadNews_searchResults_loop (s.dtCurrent, s.dtEnd, settings);
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
                                       loads : s.loads,
                                       direction : 'past',
                                       section :   s.section.replace(/-/g,'/'.replace(/ /g, '_')),
                                   dateBegin : dtBeginURL,
                                   dateEnd : dtEndURL,
                                   q : $('#newsApp_searchbar').val().replace(' ', '%20')
                                   };

                                   //na.analytics.logMetaEvent ('newsApp : loadNews_searchResults() url='+url);
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
                                               top : $('#siteContent__header').position().top + $('#siteContent__header').height() + na.d.g.margin,
                                                                          left : 0,//$('#siteContent > .vividDialogContent').offset().left,
                                                                          width : $('#siteContent > .vividDialogContent').width(),// - $('#siteContent > .vividDialogContent').offset().left,
                                                                          height : $('#siteContent > .vividDialogContent').height() - $('#siteContent__header').height() /*- $('#siteContent__header').position().top */- (2 * na.d.g.margin),
                                                                          opacity : 1
                                           });

                                           var idxStart = s.idx;
                                           na.m.walkArray (data, data, undefined, na1.loadNews_get_forDateTimeRange_walkValue);
                                           var itemsLoadedCount = data.length;

                                           //na.analytics.logMetaEvent ('newsApp : loadNews_searchResults() data fetched sucessfully for itemsLoadedCount='+itemsLoadedCount+' and url='+url);

                                           na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.current.db = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.current.db.concat(data);

                                           // s.dtEnd = s.dtCurrent;
                                           // s.dtCurrent = new Date(s.dtEnd.getTime() - (1000 * 60 * s.read_loop_minutesIntoPast));


                                           if (parseInt(s.dtCurrent.getFullYear())<2025) {
                                               clearInterval (s.intervalMailLogCountdown);
                                               clearTimeout (s.timerDisplayNews_loop);
                                               $('#newsAppInfo').html('Loaded all the news available by now. Press F5 to start over.');
                                           } else if (itemsLoadedCount < 50) {
                                               s.read_loop_minutesIntoPast = 60 * g.readHistory_numHours;
                                               s.read_loop_millisecondsToDoNext = 300;
                                           } else if (itemsLoadedCount < 100) {
                                               s.read_loop_minutesIntoPast = 60 * g.readHistory_numHours;
                                               s.read_loop_millisecondsToDoNext = 2 * 1000;
                                           } else {
                                               s.read_loop_minutesIntoPast = 60 * g.readHistory_numHours;
                                               s.read_loop_millisecondsToDoNext = 10 * 1000;
                                           };


                                           if ((data.trim && data.trim() === '') || (data.length && data.length===0)) {
                                               na1.loadNews_searchResults_loop (s.dtCurrent, s.dtEnd, settings);
                                           } else {
                                               clearInterval (s.intervalMailLogCountdown);
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

                                               db = $.extend (db, data);

                                               na.m.walkArray (db, db, undefined, na1.displayNews_getDisplayCounts, false, params);

                                               var
                                               ks = Object.keys(dc),
                                   total = 0,
                                   highest = 10;

                                   ks = ks.sort(function(a,b){ b - a });
                                   s.displayCounts = '';
                                   s.displayCountInt = 0;
                                   for (k in ks) {
                                       if (s.displayCounts!=='') s.displayCounts +=', ';
                                       s.displayCounts += Math.abs(dc[ks[k]]);
                                       s.displayCountInt += Math.abs(dc[ks[k]]);
                                   };
                                   s.unread = s.displayCountInt;
                                   //s.displayCountInt = parseInt(s.displayCounts);//- g.numItemsThatFitScreen;
                                   $('.newsApp__header__dateRange').html(na1.formatDateForHeader());
                                   $('.newsApp__header__displayCounts').html (s.displayCountInt);
                                   //$('.newsApp__header__timer').html(s.countDownStr);
                                   debugger;
                                   if (s.displayCountInt > 0) {
                                       s.tries = 0;
                                       s.loading = false;
                                       na.m.settings.locked_displayNewNewsItems = false;
                                       na1.displayNewNewsItems();
                                       na1.countDown();
                                   }

                                   clearTimeout (s.timerDisplayNews_loop);
                                   clearTimeout (s.timerLoadNews_read_loop);
                                   s.timerLoadNews_read_loop = setTimeout (function() {
                                       na1.loadNews_searchResults_loop (s.dtCurrent, s.dtEnd, settings);
                                   }, s.read_loop_millisecondsToDoNext);

                                   var
                                   dnf = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].displayNews_full(),
                                   full = dnf.full,
                                   removed = dnf.removed;
                                   clearInterval (s.intervalMailLogCountdown);
                                   clearTimeout (s.timerDisplayNews_loop);
                                   $('#newsAppDebug').animate({opacity:0.001});
                                           }
                                       },
                                       error : function (xhr, textStatus, errorThrown) {
                                           var
                                           na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;

                                           s.readHistory_numHours__numberOfRecentlyFailedContentLoadAttempts++
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

            s.searchQuery = input.val().replace(' ', '%20');
            s.dtCurrent = new Date(new Date().getTime() - (1000 * 60 * 60 * 2));
            s.dtEnd = new Date();
            s.lastCurrentGet = new Date();

            s.locked = true;
            $('.newsApp__item__outer').fadeOut('slow', function() {
                $('.newsApp__item__outer').remove();
                s.locked = false;
                s.firstRun = true;
                c.db = [];
                s.loads = 0;
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
            s.gotoNextPage = true;
        },

        toggleLock : function () {
            var
            na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
            lock = $('#newsApp_lock .vividButton_icon_imgButtonIcon_50x50')[0];
            if (lock.srs.match('_on')) {
                lock.src = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news/btnLock_off.png';
                s.locked = false;
            } else {
                lock.src = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news/btnLock_on.png';
                s.locked = true;
            }

        },

        intervalMailLogCountdown : function () {
            var
            na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
            ms = /*s.dtCurrent.getTime(),*/na.m.elapsedMilliseconds(),
            msgTime = na.m.secondsToTimeString(ms/1000);
            //return false;

            s.mailLogMsg = msgTime + ' has passed, mailing log of what happened at around 15 to 20 seconds.';
            if (parseFloat($('#newsApp_debug').css('z-index'))!==10 * 1000 * 1000) {
                $('#newsApp_debug').css ({
                    opacity : 0.001,
                    zIndex : 10 * 1000 * 1000,
                    height : '3em',
                    padding : '7px'

                }).animate({opacity:0.75,color:'white',backgroundColor:'green'});
            }

            $('#newsApp_debug').html (s.mailLogMsg);
        },

        loadNews_read_loop : function () {
            var
            na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;
            var
            settings = $.extend(s.settings_onload?s.settings_onload:{},s),
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

            settings.direction = 'past';
            settings.dateBegin = s.dtCurrent;
            settings.dateEnd = s.dtEnd;

            s.lastCurrentGet = new Date();

            g.numItemsThatFitScreen = Math.round( ( ow / 440 ) * (oh/400) );
            if (g.numItemsThatFitScreen < 5) g.numItemsThatFitScreen = 5; // smartphone bugfix

            na.m.walkArray (db, db, undefined, na1.displayNews_getDisplayCounts, false, params);
            var
            ks = Object.keys(dc),
            total = 0;

            //ks = ks.sort(function(a,b){ return b - a }); // BAD IDEA!


            var
            unread = parseInt(dc[ks[0]]);
            s.unread = unread;
            if (s.firstRun) {
                s.firstRun = false;
                settings.dateBegin = s.dtCurrent;
                settings.dateEnd = s.dtEnd;
                if (s.searchQuery) na1.loadNews_searchResults_loop (s.dtCurrent, s.dtEnd, settings); else
                    setTimeout (function() {
                        na1.loadNews_get_forDateTimeRange (s.dtCurrent, s.dtEnd, settings);
                    }, 20);
                return false;
            } else if (!s.dtEnd || unread < 10) {
                if (!s.dtCurrent) s.dtCurrent = new Date();
                s.dtEnd = new Date(s.dtCurrent.getTime());
                s.dtCurrent = new Date(s.dtEnd.getTime() - (1000 * 60 * 60 * g.readHistory_numHours));//s.read_loop_minutesIntoPast));
                settings.dateBegin = s.dtCurrent;
                settings.dateEnd = s.dtEnd;
                s.firstRun = false;
                debugger;
                if (s.searchQuery) na1.loadNews_searchResults_loop (s.dtCurrent, s.dtEnd, settings); else
                    setTimeout (function() {
                        na1.loadNews_get_forDateTimeRange (s.dtCurrent, s.dtEnd, settings);
                    }, 20);
                return false;
            }
            if (unread > 2) {
                //$('#newsApp_content').html('').delay(50);
                s.tries = 0;
                if (s.firstRun) delete na.m.settings.locked_displayNewNewsItems;
                na1.displayNewNewsItems();
                na1.countDown();
                na1.onresize();
                return false;

            }

            // get older news items when needed
            if (s.firstRun) {
                s.firstRun = false;
                s.failedLoads = 0;
                na1.loadNews_get_forDateTimeRange(s.dtCurrent, s.dtEnd, settings);
            }

            var c = na1.settings.current;
            na1.settings = settings;
            na1.settings.current = c;

            /*
             *        // get newest news items
             *        debugger;
             *        settings.direction = 'future';
             *        if (
             *            !s.lastCurrentGet
             *            || s.lastCurrentGet.getTime() < dtBegin.getTime() - 1000 * 3600 * g.readHistory_numHours
             *        ) {
             *            s.lastCurrentGet = dtBegin;
             *            na1.loadNews_get_forDateTimeRange (new Date(dtBegin.getTime() - 1000 * 3600 * g.readHistory_numHours), dtBegin, settings);
        };
        */


        },

        /*
         *    loadNews_read_loop_old : function (dtBegin, dtEnd, dtQuit, settings, waitTimeInSeconds, dtOffsetInSeconds) {
         *        var
         *        na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
         *        doNextToo = true;
         *
         *        //s.dtCurrent = dtBegin;
         *        //s.dtEnd = dtEnd;
         *        na1.loadNews_get_forDateTimeRange (dtBegin, dtEnd, settings);
         *
         *        if (dtOffsetInSeconds < 0 && dtQuit) {
         *            doNextToo = dtBegin.getTime() > dtQuit.getTime();
    };

    if (doNextToo) setTimeout (function() {
        s.dtEnd = new Date(dtEnd.getTime() + 1000 * dtOffsetInSeconds);
        s.dtBegin = new Date(dtBegin.getTime() + 1000 * dtOffsetInSeconds);

        na1.loadNews_read_loop(s.dtBegin, s.dtEnd, dtQuit, settings, waitTimeInSeconds, dtOffsetInSeconds);
    }, 1000 * waitTimeInSeconds);
    },
    */

        loadNews_get_forDateTimeRange : function (dtBegin, dtEnd, settings) {
            //debugger;
            var
            fncn = 'na.apps.loaded["applications/2D/news"].loadNews_get_forDateTimeRange(dtBegin,dtEnd,settings)',
            appURL = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news',
            na1 = na.apps.loaded[appURL], g = na1.globals, s = na1.settings, c = s.current, db = c.db;
            s.loads++;
            na.m.waitForCondition ('has the news app finished loading?', function () {
                var r = !s.loading;
                return r;
            }, function() {
                s.loading = true;
                // s.dtBegin = dtBegin;
                // s.dtEnd = dtEnd?dtEnd:new Date();
                // s.dtCurrent = new Date(s.dtEnd.getTime() - (1000 * 60 * settings.read_loop_minutesIntoPast));
                // // s.dtBegin = new Date(s.dtCurrent.getTime());
                s.lastCurrentGet = dtEnd;

                var
                dtBeginURL = na1.formatDateForLoading(dtBegin),//('' + dtBegin).replace('+', '%2B'),
                                   dtEndURL = na1.formatDateForLoading(dtEnd),//('' + dtEnd).replace('+', '%2B'),
                                   url = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news/ajax_get_items.php';//?loads='+s.loads+'&section='+settings.section.replace(/-/g,'/').replace(/ /g, '_')+'&dateBegin='+dtBeginURL+'&dateEnd='+dtEndURL;

                                   //s.url = settings.section;

                                   if (s.searchQuery) url += '&q='+s.searchQuery;
                                   if (settings.section) {
                                       var section = settings.section;
                                   } else {
                                       if (typeof na.site.globals.app=='string')
                                           var section = JSON.parse(na.site.globals.app)[appURL]['section'];
                                       else
                                           var section = na.site.globals.app.section;
                                   };
                                   s.section = section;

                                   var
                                   ajaxCommand = {
                                       type : 'GET',
                                       url : url,
                                       data : {
                                           loads : s.loads,
                                           direction : settings.direction,
                                           section : section.replace(/-/g,'/').replace(/ /g, '_'),
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
                                               top : $('#siteContent__header').position().top + $('#siteContent__header').height() + na.d.g.margin,
                                                                          left : 0,//$('#siteContent > .vividDialogContent').offset().left,
                                                                          width : $('#siteContent > .vividDialogContent').width(),// - $('#siteContent > .vividDialogContent').offset().left,
                                                                          height : $('#siteContent > .vividDialogContent').height() - $('#siteContent__header').height() /*- $('#siteContent__header').position().top */- (2 * na.d.g.margin),
                                                                          opacity : 1
                                           });




                                           var
                                           idxStart = s.idx;

                                           //na.m.log (2, 'loadNews_get_forDateTimeRange() : data=' + data);

                                           if (typeof data=='string') {
                                               var dat = JSON.parse(data,null,4);
                                           } else {
                                               var dat = data;
                                           }


                                           dat.fncn = fncn;
                                           na.m.log (2, dat);
                                           setTimeout (function() {
                                               if (typeof data=='string')
                                               try {
                                                   dataText = data;
                                                   data = JSON.parse(data);
                                               } catch (err) {
                                                   debugger;
                                               }
                                               if (data.length > 0) {
                                                   na.m.walkArray (data, data, undefined, na1.loadNews_get_forDateTimeRange_walkValue);

                                                   var itemsLoadedCount = s.idx - idxStart;

                                                   //na.analytics.logMetaEvent ('newsApp : loadNews_get_forDateTimeRange() data fetched sucessfully for itemsLoadedCount='+itemsLoadedCount+' and url='+url);
                                               } else {
                                                   itemsLoadedCount = 0;
                                               };

                                               //if (itemsLoadedCount < 50) {
                                               s.read_loop_minutesIntoPast = 60 * g.readHistory_numHours;
                                               s.read_loop_millisecondsToDoNext = 5 * 60 * 1000;
                                               //} /*else {//if (itemsLoadedCount < 200) {
                                               //s.read_loop_minutesIntoPast = 60 * (g.readHistory_numHours/2);
                                               //s.read_loop_millisecondsToDoNext = 1000;
                                               //} /*else {
                                               //s.read_loop_minutesIntoPast = 60;
                                               //s.read_loop_millisecondsToDoNext = 2 * 60 * 1000;
                                               //};

                                               na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.current.db = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.current.db.concat(data);

                                               if (itemsLoadedCount===0) {
                                                   s.timerLoadNews_read_loop = setTimeout (na1.loadNews_read_loop, s.read_loop_millisecondsToDoNext);
                                                   return false;
                                               } else {
                                                   clearInterval (s.intervalMailLogCountdown);
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
                                                       s.unread = parseInt(unread);
                                                       na.m.log (100, 's.unread='+s.unread);

                                                       /*
                                                        *                        var
                                                        *                        dbg = {
                                                        *                            fncn : fncn,
                                                        *                            appID : '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news (version '+na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].about.version+')',
                                                        *                            loads : s.loads,
                                                        *                            section : section.replace(/-/g,'/').replace(/ /g, '_'),
                                                        *                            numLoaded : s.unread,
                                                        *                            dateBegin : dtBegin,
                                                        *                            dateEnd : dtEnd
                                           };
                                           //console.log (dbg);
                                           */


                                                       var
                                                       ks = Object.keys(dc),
                                                       total = 0,
                                                       highest = 10;

                                                       ks = ks.sort(function(a,b){ b - a });
                                                       s.displayCounts = '';
                                                       for (k in ks) {
                                                           if (s.displayCounts!=='') s.displayCounts +=', ';
                                                           s.displayCounts += dc[ks[k]];
                                                       };
                                                       s.displayCountInt = s.displayCounts;//- g.numItemsThatFitScreen;

                                                       $('.newsApp__header__dateRange').html(na1.formatDateForHeader());
                                                       $('.newsApp__header__displayCounts').html (s.displayCountInt);
                                                       $('.newsApp__header__timer').html(s.countDownStr);

                                                       // if (!s.dtCurrent || !s.dtCurrent.getTime) s.dtCurrent = new Date();
                                                       // s.dtEnd = s.dtCurrent;
                                                       // s.dtCurrent = new Date(s.dtEnd.getTime() - (1000 * 60 * 60 * 1.5));//s.read_loop_minutesIntoPast));
                                                       if (s.unread <= 10) {
                                                           na1.loadNews_read_loop();
                                                       } //else {
                                                       //clearTimeout (s.timerLoadNews_read_loop);
                                                       //s.timerLoadNews_read_loop = setTimeout (na1.loadNews_read_loop, s.read_loop_millisecondsToDoNext);
                                                       na1.displayNewNewsItems();
                                                       //}
                                           }, 200);
                                       },
                                       error : function (xhr, textStatus, errorThrown) {
                                           var
                                           na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;

                                           s.readHistory_numHours__numberOfRecentlyFailedContentLoadAttempts++
                                           s.timerLoadNews_read_loop = setTimeout (na1.loadNews_read_loop, s.read_loop_millisecondsToDoNext);
                                           //don't need to see this : na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
                                       }
                                   };
                                   //na.m.log (20, url);
                                   $.ajax (ajaxCommand);
            }, 100);
        },

        loadNews_get_forDateTimeRange_walkValue : function (cd) {
            var
            na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
            urlp = na.site.globals.app;//na1.getURLparameters(),

            /*
             *        for (var k in urlp) {
             *            var settings = {
             *                section : urlp[k].section.replace(/__/g,'/').replace(/_/g,' ')
        };
        break;
        }*/
            //debugger;
            //var settings = s.settings_onload;
            if (typeof cd.v=='function') return false;
            if (typeof cd.v=='object' && cd.v!==null && typeof cd.v.length=='number' && (cd.v.length===0 || !cd.v.t || !cd.v.de)) return false;
            if (cd.v) {
                let it = cd.v;
                s.idx++;
                it.idx = s.idx;
                it.rssURL = cd.v._id;
                if (it.p!=='') {
                    it.path = s.section.replace(/-/g,'/'.replace(/ /g, '_'));
                } else {
                    it.path = s.section.replace(/-/g,'/'.replace(/ /g, '_')) + '/' + it.p;
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
                if (!it.t && !it.d) return false;
                //debugger;

                if (!it.displayCount) it.displayCount = 0;
                var k = it.displayCount;

                if (k===0) {
                    if (typeof cd['params']['dc'][k]=='undefined')
                        cd['params']['dc'][k] = 1;
                    else
                        cd['params']['dc'][k]++;

                    if (typeof cd['params']['di'][k]=='undefined')
                        cd['params']['di'][k] = [ it ];
                    else {
                        cd['params']['di'][k].push(it);
                    }
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

            min = parseFloat(s.delayNewItemsMin),
            max = parseFloat(s.delayNewItemsMax),
            randomValue2 = (
                s.delayNewItemsDisplayToPage
                ? Math.floor(Math.random() * (max - min + 1)) + min
                : 0
            );
            /*s.pages.push ({
             *            idx : s.pages.length,
             *            items : []
        });
        s.page++;*/

            //$('.newsApp__item__outer a:not(:has(img))').animate({opacity:0.7});


            if (!s.tries) s.tries = 0;
            if (s.tries === 0) s.randomValue = randomValue;
            if (s.tries < s.randomValue) {
                if (!na1.displayNewNewsItem()) { };
                setTimeout (function() {
                    /*
                     *                if (!s.pages[s.page-1]) debugger;
                     *                s.pages[s.page-1].items.push ({
                     *                    dnfi : s.dnfi,
                     *                    dnf : s.dnf,
                     *                    it : s.it,
                     *                    itEl : s.itEl
                });
                */
                    if (!s.dnf.full) {
                        na.m.settings.locked_displayNewNewsItems = false;
                        na1.displayNewNewsItems();
                        na1.countDown();
                    } else {
                        $('.newsApp__header__timer')
                        .html(s.countDownStr)
                        .delay(20)
                        .css({opacity:0.0001,textAlign:'right'})
                        .animate({opacity:1});

                        /*
                         *                    s.pages.push ({
                         *                        idx : s.pages.length,
                         *                        items : []
                    });
                s.page++;
                */

                        na1.countDown();

                        //na1.loadNews_read_loop();
                    }
                }, s.dnf.removed?0:randomValue2);

            } else {
                $('.newsApp__header__timer').css({opacity:0.0001,textAlign:'right'}).animate({opacity:1});
                /*
                 *            s.pages.push ({
                 *                idx : s.pages.length,
                 *                items : []
            });
            s.page++;
            */
                na1.countDown();
                //na1.loadNews_read_loop();
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
                $('#newsApp_title').html (s.section.replace(/.*__/g, '').replace(/_/g, ' '));
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

            s.tries++;

            na.m.walkArray (db, db, undefined, na1.displayNews_getDisplayCounts, false, params);

            var
            ks = Object.keys(dc),
            total = 0,
            highest = 10,
            unread = dc[ks[0]];

            s.unread = unread;
            s.displayCounts = '';
            for (k in ks) {
                if (s.displayCounts!=='') s.displayCounts +=', ';
                s.displayCounts += dc[ks[k]];
            };
            s.displayCountInt = Math.abs(parseInt(s.displayCounts)/2);//- g.numItemsThatFitScreen;
            /*
             *        s.displayCounts = '<span class="newsApp__header__displayCounts">' + s.displayCountInt + '</span>';
             *        $('#newsApp_timer').html(
             *            na1.formatDateForHeader()
             *            + ' ' +(s.displayCounts)
             *            +'<span class="newsApp__header__timer">'+s.countDownStr+'</span>'
             *        );*/
            $('.newsApp__header__dateRange').html(na1.formatDateForHeader());
            $('.newsApp__header__displayCounts').html (s.displayCountInt);
            $('.newsApp__header__timer').html(s.countDownStr);

            let
            found = false,
            it = null;

            var l = 0;
            while (di[l] && di[l].length==0) l++;
            var its = di[l];

            if (!its) {
                if (s.displayDebugInfo) na.site.setStatusMsg ('na1.displayNewNewsItem : s.tries==='+s.tries+', !its : return false');
                return false;
            }

            while (it===null || !it.t || !it.de) {
                var
                key = Math.floor(Math.random() * (its.length-1));

                it = its[key];
                if (!it.displayCount) it.displayCount = -1;
                it.displayCount++;
                if (it.displayCount > 100) return false;

                if (typeof it.displayCount=='number' && it.displayCount > 0) {
                    it = null;
                } else {
                    var els2 = $('#newsApp_content .newsApp__item__outer');
                    for (var j=0; j<els2.length; j++) {
                        if (
                            it
                            && it.t
                            && typeof it.t=='string'
                            && els2[j].innerHTML.indexOf(it.t)!==-1
                        ) found = true;
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
                dnf = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].displayNews_full(''),
                full = dnf.full,
                removed = dnf.removed,
                dnfi = na1.displayNews_formatItem (it, ''), // displayNews_formatItem() also puts the formatted item into the DOM!
                html = dnfi.html,
                htmlTooltip = dnfi.html2;
                s.dnfi = dnfi;
                s.dnf = dnf;

                if (!html) {
                    if (s.displayDebugInfo) na.site.setStatusMsg ('na1.displayNewNewsItem : s.tries==='+s.tries+', !html : return false');
                    return false;
                }
                html = html.replace ('<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>','');
                html = html.replace (/http:\/\//g, 'https://');


                var el = $(dnfi.id)[0];
                $('.newsApp__item__title, .newsApp__item__contentContainer, .newsApp__item__mediaSingle, .vividScrollpane, .newsApp__item__footer',el)
                .css({
                    display : 'block',
                    opacity : 1
                }).delay(10);
                var
                ms = $('.newsApp__item__mediaSingle',el),
                w = $(el).width() - 14;

                //if (ms[0]) w -= ms.width();
                //console.log (w);
                //if ($('.newsApp__item__mediaSingle img', el)[0]) $('.vividScrollpane', el).add($('.newsApp__item__mediaSingle img', el)).css ({width : '40%'}); else $('.vividScrollpane', el).css ({width : w});

                var
                h = $('#siteContent .vividDialogContent').height() - $('#newsApp_title').outerHeight();
                if ($(window).width() < na.site.globals.reallySmallDeviceWidth) {
                    w = $('#newsApp_content').width();
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

                var
                itEl = $('#newsApp__item__'+it.idx)[0],
                sp = $('.vividScrollpane', itEl)[0],
                dnf = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].displayNews_full(''),
                full = dnf.full,
                removed = dnf.removed;
                s.dnf = dnf;
                s.it = it;
                s.itEl = itEl;
                if (removed) {
                    if (s.displayDebugInfo) na.site.setStatusMsg ('na1.displayNewNewsItem : s.tries==='+s.tries+', removed : return false');
                }


                var sph1 = 0;
                $('.newsApp__item__outer').each(function(idx3, el3) {
                    var count = true;
                    if ($(el3).index() !== $(itEl).index()+1) count = false;
                    if (count) sph1 += $(el3).outerHeight();
                });
                    h -= sph1;

                    if (!dnfi) {
                        if (s.displayDebugInfo) na.site.setStatusMsg ('na1.displayNewNewsItem : s.tries==='+s.tries+', !dnfi : return false');
                        return false;
                    };

                    if (!s.getToTry) s.getToTry = g.numItemsThatFitScreen;

                    //console.log (full,removed);
                    if (full || removed) {
                        $(itEl).remove();
                        return false;
                    }

                    it.displayCount++;
                    if (it.displayCount > s.maxDisplayCount) s.maxDisplayCount = it.displayCount;


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


            clearTimeout (s.timeout_onresize_loadTheme);
            s.timeout_onresize_loadTheme = setTimeout (function() {
                na.m.waitForCondition('na1.onresize() : safe to call na.site.loadTheme_applySettings?', function() {
                    return (
                        !na.site.settings.running_loadTheme
                        && !na.site.settings.running_saveTheme
                    );
                }, function () {
                    na.site.loadTheme_applySettings (na.site.globals.themes[na.site.globals.themeName], null, false, false, false);
                    //na.te.reApplySelectorsTree();
                }, 100);
            }, 100);
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
            countDown = 10 + Math.round(l * 5);
            s.countDown = countDown;
            s.countDownStr = ' - '+countDown;
            $('.newsApp__header__timer').html(s.countDownStr);

            s.onScreen = l;
            s.shownBGonly = false;
            s.showBGonlySecs = (
                s.blankScreenBeforePageChange
                ? Math.random() * (s.blankScreenMax - s.blankScreenMin + 1) + s.blankScreenMin
                : 0
            );

            if (!s.countDownInterval)
                s.countDownInterval = setInterval (function() {
                    if (!na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news']) return false;

                    if (!s.displayed) s.displayed=[];
                    s.displayed.push({
                        idx : s.displayed.length,
                        items : []
                    });

                    if (!s.locked) s.countDown--;
                    s.countDownStr = ' - '+s.countDown;


                    $('#newsApp_timer').html(
                        na1.formatDateForHeader()
                        + ' ' +(s.displayCounts)
                        +'<span class="newsApp__header__timer">'+s.countDownStr+'</span>'
                    );
                    $('.newsApp__header__dateRange').html(na1.formatDateForHeader());
                    //$('.newsApp__header__displayCounts').html (s.displayCountInt);
                    $('.newsApp__header__timer').html(s.countDownStr);
                    if (s.countDown<=s.showBGonlySecs || s.gotoNextPage) {
                        if (!s.shownBGonly) {
                            $('.newsApp__item__outer').fadeOut('normal', function() {
                                $(container).html('').delay(20);
                            });
                            s.shownBGonly = true;
                        }
                    }
                    if (s.countDown==0 || s.gotoNextPage) {
                        clearInterval(s.countDownInterval);
                        $('.newsApp__header__timer').animate({opacity:0.0001});
                        $(container).fadeOut(500, function() {
                            container.css({opacity:0.001,display:'block'});
                            c.its3 = [];
                            s.resize = {};
                            s.tries = 0;

                            delete s.gotoNextPage;


                            setTimeout (function() {
                                var
                                container = $('#newsApp_content'),
                                        l = $('.newsApp__item__outer', container).length,
                                        countDown = 10 + Math.round(l * 2.2);
                                        s.countDown = countDown;
                                        delete s.countDownInterval;

                                        s.tries = 0;
                                        delete na.m.settings.locked_displayNewNewsItems;

                                        $(container).animate({opacity:1}, 'normal');
                                        na1.displayNewNewsItems();

                                        $('.newsApp__header__countDownStr').animate({opacity:1});
                                        na1.onresize();
                                        na1.loadNews_read_loop();
                                        na1.countDown();
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
            if (prefix=='' && it && $(it).position().top > container.height()-50) { full = true; }
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
            w1 = $('#siteContent .vividDialogContent').width()-70,


            min = 0.2,
            max = 0.4,
            mp1 = Math.random() * (max - min + 1) + min,
            min2 = ($('#siteContent__content').height())/ (
                $(window).width() < na.site.globals.reallySmallDeviceWidth
                ? 1 * mp1
                : $(window).width() < na.site.globals.smallDeviceWidth
                ? 1.1 * mp1
                : $(window).width() < 2000
                ? 1.3 * mp1
                : 1.4 * mp1
            ),
            min = 0.4,
            max = 0.6,
            mp2 = Math.random() * (max - min + 1) + min,
            max = ($('#siteContent__content').height())/(
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
            !s.lastEl
            ? $('#newsApp_content').height() - 20
            : $('#newsApp_content').height() - 20;//s.lastEl.position().top - 20;
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
            if ($(window).width() < 400 && w < $('#newsApp_content').width()) w = $('#newsApp_content').width() - $('#newsApp_content').offset().left;
            if (hf < $('#newsApp_content').height()) hf = $('#newsApp_content').height();
            if (hf < h2) hf = h2;

            html += '<div id="newsApp__item__'+it.idx+'" class="newsApp__item__outer" style="justify-self:center;align-self:center;width:'+w+'px;'+fontSize+';height:auto;opacity:0.00001;">';
            html += '<div id="newsApp__item__'+it.idx+'__bg" class="newsApp__item__outer__bg vdBackground" style="">';
            html += '<div id="newsApp__item__'+it.idx+'__bg___1" class="newsApp__item__outer__bgFile1" style="">';
            html += '&nbsp;</div>';
            html += '<div id="newsApp__item__'+it.idx+'__bg___2" class="newsApp__item__outer__bgFile2" style="">';
            html += '&nbsp;</div>';
            html += '</div>';
            if (typeof it.de=='string' && it.de.trim()!=='') {
                if (typeof it.t=='string' && it.t!='' && it.de.indexOf(it.t)===-1) html+= '<div class="newsApp__item__title newsApp__item__noPaint"><div class="newsApp__item__title_bg">&nbsp;</div><div class="newsApp__item__link"><div class="newsApp__item__title__container"><a class="nomod" target="newsAppItem_'+it.idx+'" href="' + it._id+'" onclick="if (na.te.s.s.which==\'selectedSelector\') { na.te.onclick_btnAddGraphics(event); event.preventDefault();}" >' + it.t.replace(/\&#39;/g, '\'').replace(/#39;/g, '\'')+ '</a></div></div></div>';
            } else {
                if (typeof it.t=='string' && it.t!='') html+= '<div class="newsApp__item__title newsApp__item__noPaint"><div class="newsApp__item__title_bg">&nbsp;</div><div class="newsApp__item__title__container"><a class="nomod" target="newsAppItem_'+it.idx+'" href="' + it._id+'">' + it.t.replace(/\&#39;/g, '\'').replace(/#39;/g, '\'') + '</a></div></div>';
            }
            if (typeof it.de=='string' && it.de.trim()!=='') html+= '<div class="newsApp__item__contentContainer"><div class="newsApp__item__mediaSingle"></div><div id="newsApp_item_'+it.idx+'__scrollpane" class="newsApp__item__desc vividScrollpane" style=""><div>' + it.de.replace(/\&#39;/g, '\'').replace(/#39;/g, '\'').replace(/\<a/g, '<a target="_new" ') + '</div></div></td></tr></table><div class="newsApp__item__noPaint" style="height:5px;overflow:hidden;"></div></div>';
            /*
             *        var appSettings2 = JSON.parse(na.site.globals.app);
             *        for (var k in appSettings2) var appSettings = appSettings2[k];
             *        //debugger;
             *        if (appSettings.seoValue) {
             *            var seov = appSettings.seoValue;
             *            if (typeof seov=='object' && typeof seov.length=='number' && seov.length > 0) seov = seov[0];
        } else {
            var seov = appSettings.section.replace(/.*__/,'').replace('__','-').replace('_','-').toLowerCase();
        };*/
            var seov = na1.settings.section.replace(/.*__/,'').replace('__','-').replace('_','-').toLowerCase();
            /* old, works :
             *
            html += '<div class="newsApp__item__footer"><span class="newsApp__item__date"><a class="nomod noPushState" target="_new"  target="newsAppItem_'+it.idx+'" href="' + it.rssURL+'">' + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].formatDate(it)+'</a></span><br/>';
            html += '<span class="newsApp__item__copy"><a class="nomod noPushState" href="javascript:var el = $(\'#newsApp__item__'+it.idx+'\')[0], textarea = $(\'#siteContent__textareaCopy\')[0]; if (!textarea) { var el2=document.createElement(\'textarea\'); window.top.document.append(el2); textarea=el2 }; el_html = el.innerHTML; el.innerHTML = el.innerHTML.replace(\/<span class..newsApp__item__copy.*>.*<.a><.span>\/,\'\') + \'Found via <a href=\\\'https://nicer.app/'+seov+'\\\' target=\\\'_new\\\'>https://nicer.app/'+seov+'</a>\'; var type = \'text/html\'; var blob = new Blob([el.innerHTML], { type }); var data = [new ClipboardItem({ [type]: blob })]; navigator.clipboard.write(data).then( () => {/* success * /}, () => {/* failure * /} ); var selection = window.getSelection(); var range = document.createRange(); range.selectNodeContents(el); selection.removeAllRanges(); selection.addRange(range); window.top.document.execCommand(\'copy\');setTimeout(function(){selection.removeAllRanges(); el.innerHTML=el_html;},1000);">Copy to clipboard</a></span></div> ';
            html+= '</div>';
            */

            html += '<div class="newsApp__item__footer"><table class="newsApp__item__date"><tr><td><a class="nomod" target="newsAppItem_'+it.idx+'" href="' + it.u+'">' + na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].formatDate(it)+'</a></span></td>';

            html += '<td class="newsApp__item__copy"><div id="newsApp__copyAndPaste__'+it.idx+'" class="vividButton4 btn_copyAndPaste" buttonType="btn_copyAndPaste" style="float:right" target="newsAppItem_'+it.idx+'" onclick="var el = $(\'#newsApp__item__'+it.idx+'\')[0], textarea = $(\'#siteContent__textareaCopy\')[0]; if (!textarea) { var el2=document.createElement(\'textarea\'); window.top.document.append(el2); textarea=el2 }; el_html = el.innerHTML; el.innerHTML = el.innerHTML.replace(\/<span class..newsApp__item__copy.>Copy to clipboard<.a><.span>>\/,\'\') + \'Found via <a href=\\\'https://nicer.app/'+seov+'\\\' target=\\\'_new\\\'>https://nicer.app/'+seov+'</a>\'; var selection = window.getSelection(); var range = document.createRange(); range.selectNodeContents(el); selection.removeAllRanges(); selection.addRange(range); window.top.document.execCommand(\'copy\');setTimeout(function(){selection.removeAllRanges(); el.innerHTML=el_html;},1000);"></div></td></tr></table> ';
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
            na.site.startUIvisuals('#newsApp__copyAndPaste__'+it.idx);
            s.lastEl = $('#newsApp__item__'+it.idx);


            var
            el = $('#newsApp__item__'+it.idx)[0],
            th = $('.news__item__title', el).height(),
            fh = $('.newsApp__item__footer', el).height(),
            ph = $('p, div', el).height(),
            ph2 = 20 * $('p, div', el).length,
            //mh = Math.round((th?parseInt(th):0) + (fh?parseInt(fh):0) + (ph?parseInt(ph):0) + (ph2?parseInt(ph2):0) + 60);
            mh = hf - Math.round((th?parseInt(th)+5:0) + (fh?parseInt(fh)+125:0) + 0);

            $(el).css({maxHeight:hf,height:''});
            $('.newsApp__item__desc',el).css({maxHeight:mh});
            $('.newsApp__item__contentContainer',el).css({maxHeight:mh});

            /*
             *        $(el).find('div, span')
             *            .not('.vividScrollpane, .newsApp__item__title_bg, .newsApp__item__outer__bg, .newsApp__item__outer__bgFile1, .newsApp__item__outer__bgFile2, .newsApp__item__noPaint, a')
             *            .each(function(idx,el2) {
             *                if (!$(el2).find('*').not('a')[1]) if (!$(el2).parents().is('div, p, cite, a')) $(el2).addClass('newsApp__item__paint');
             *                if ($(el2).html().trim()=='') $(el2).remove();
        });
        */
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
            $(el).css({height:'fit-content'});

            /*
             *        $(el).find('img, iframe').each(function(idx,el){
             *            var
             *            p = $(el).parents('p, div')[0],
             *            l = $(el).offset().left - $(p).offset().left,
             *            w = $(p).width() - (2 * l);
             *
             *
             *            if ($(el).width() > w - 30)
             *                $(el).css({width:w-38});
        });
        */

            s.lastEl.css({opacity:1,display:'none'});
            $('#newsApp__item__'+it.idx).fadeIn('normal');

            return {
                // beware : algorithm subconcious code owned by Nicer.App.
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
            m = s.dtEnd.getTime() > s.lastCurrentGet.getTime() ? s.dtEnd : new Date(),//s.lastCurrentGet,//new Date(),
            dns = Date.locale.en.day_names,
            tz = m.getTimezoneOffset() * 60 /*seconds*/ * 1000 /*milliseconds*/,
            tx = '' + ((-1 * m.getTimezoneOffset()) / 60),
            m1 = s.dtCurrent,
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
             *        r2 = '<span class="newsApp__item__date__remote">Local : ' + m1.getFullYear() + "-" +
             *            ("0" + (m1.getMonth()+1)).slice(-2) + "-" +
             *            ("0" + m1.getDate()).slice(-2) + "(" + dns[m1.getDay()] + ') ' +
             *            ("0" + m1.getHours()).slice(-2) + " : " +
             *            ("0" + m1.getMinutes()).slice(-2) + " : " +
             *            ("0" + m1.getSeconds()).slice(-2) + '</span>',
             *        r3 = '<span class="newsApp__item__date__remote">Remote : ' + m.toLocaleDateString('en-US') + ' ' + m.toLocaleTimeString('en-US') + '</span>',
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
            x = na.site.globals.app['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'];
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
                if (!s.dontHide) {
                    var el = $('#siteContent__btnOptions_menu');
                    if (el.css('display')!='none') el.fadeOut('slow');
                }
            }, 1000);
        },

        showOptionsDialog : function() {
            var
            na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;
            s.dontHide = true;
            var el = $('#siteContent__btnOptions_menu');
            if (el.css('display')=='none') el.fadeIn('slow');

        },

        settingsChanged : function (evt) {
            var
            na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings,
            c = s.current, db = c.db;

            var theme = na.site.globals.themes[na.site.globals.themeName];
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
            theme = na.site.globals.themes[$('#btnOptions_menu__themes_dropdown > .vividDropDownBox_selected').html()];

            if (theme && theme.apps) {
                var tApp = theme.apps['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'];
                if (tApp) {
                    $('#blankScreenBeforePageChange')[0].checked = tApp.blankScreenBeforePageChange;
                    $('#blankScreenMin').val(tApp.blankScreenMin);
                    $('#blankScreenMax').val(tApp.blankScreenMax);

                    s.blankScreenBeforePageChange = tApp.blankScreenBeforePageChange;
                    s.blankScreenMin = tApp.blankScreenMin;
                    s.blankScreenMax = tApp.blankScreenMax;

                    $('#delayNewItemsDisplayToPage')[0].checked = tApp.delayNewItemsDisplayToPage;
                    $('#delayNewItemsMin').val(tApp.delayNewItemsMin);
                    $('#delayNewItemsMax').val(tApp.delayNewItemsMax);

                    s.delayNewItemsDisplayToPage = tApp.delayNewItemsDisplayToPage;
                    s.delayNewItemsMin = tApp.delayNewItemsMin;
                    s.delayNewItemsMax = tApp.delayNewItemsMax;
                }
            }
        },

        onresize : function (settings, displayNews) {
            var
            na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings,
            c = s.current, db = c.db;
            clearTimeout (s.timeout_onresize);
            s.timeout_onresize = setTimeout (function() { // na.desktop.resize delay buffer
                var
                na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings,
                c = s.current, db = c.db;

                if (displayNews!==true) displayNews = false;

                if ($(window).width() < 400) {
                    $('.newsAppButton').css({ display : 'none' });
                    $('.popupButtons').css({ display : 'flex' });
                } else {
                    $('.newsAppButton').css({ display : 'block' });
                    $('.popupButtons').css({ display : 'none' });
                }
                if ($(window).width()<1300) {
                    $('#div_newsApp_info').fadeOut('normal');
                }

                $('#siteContent__btnOptions_menu').detach().appendTo('body').css({display:'block',opacity:0.0001,padding:0,margin:0,borderRadius:10});
                /*$('.vividButton, .vividButton_icon_50x50_siteTop, .vividButton_icon_50x50', $('#siteContent__btnOptions_menu')[0])
                 *                .each(function(idx,el){
                 *                    if (!na.site.s.components.buttons['#'+el.id])
                 *                        na.site.s.components.buttons['#'+el.id] = new naVividButton(el);
                 *
                 *
            });*/
                setTimeout (function() {
                    $('#siteContent__btnOptions_menu').css({display:'block',opacity:0.0001});
                    var bcr = $('#newsApp_options_container')[0].getBoundingClientRect();
                    $('#siteContent__btnOptions_menu').css({display:'none',opacity:1}).animate({
                        left : bcr.left + bcr.width - $('#siteContent__btnOptions_menu').outerWidth(),
                                                                                               top : bcr.top + bcr.height + 5
                    });
                }, 200);

                na1.themeAppsChanged();

                /*
                 *            if (na.m.userDevice.isPhone) $('#btnOptions, #btnLoginLogout, #btnChangeBackground').css({opacity:1})
                 *            else $('#btnOptions, #btnLoginLogout, #btnChangeBackground').animate({opacity:1},'normal');
                 */

                var
                na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db,
                ow = ($('#siteContent .vividDialogContent')[0].offsetWidth),
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
                    height : $('#siteContent__content').height(),
                                                                                          width : ow
                });

                $('#newsApp_content_shadow, #newsApp_content').css({
                    left : $('#siteContent .vividDialogContent').position().left
                });
                na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].settings.itemWidth = w;
                if (displayNews /*&& s.dtCurrent*/)
                    na.m.waitForCondition(
                        '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news::onresize() : have news items to display?',
                                          function () {
                                              var
                                              na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'],
                                              g = na1.globals, s = na1.settings, c = s.current, db = c.db,
                                              r = s.unread > 0;
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
                                                      //if ($(window).width() < 400) $(el3).css({marginLeft:0});
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
                                                          //s.unread += 1;
                                                          $(el3).remove();
                                                          doCountDown = true;
                                                      }
                                                  });
                                                  if (doCountDown) {
                                                      $('.newsApp__header__countDownStr').animate({opacity:1});
                                                      na1.countDown();
                                                      //na1.loadNews_read_loop();
                                                  }
                                              }, 100);

                                              //$els2.css ({minWidth : '', width : 'auto'});
                                          }, 250);
                    if (typeof settings=='object' && settings!==null && typeof settings.callback=='function') settings.callback (settings);
            }, 200);
            }, 200);
        },

        onresize_do : function (sel) {
            var
            na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db;
            //debugger;
            s.tries = 0;
            na1.displayNewNewsItems();
        }

    };
