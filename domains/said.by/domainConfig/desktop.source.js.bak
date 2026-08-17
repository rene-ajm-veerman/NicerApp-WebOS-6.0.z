na.desktop = {
    globals : {
        animationSpeed : 300,//'slow',
        divs : [ '#siteDateTime', '#siteErrors', '#btnOptions', '#btnLoginLogout', '#btnChangeBackground', '#siteContent', '#siteYoutubePlayer', '#siteYoutubeSearch', '#siteComments', '#siteStatusbar', '#siteToolbarThemeEditor', '#siteToolbarLeft', '#siteToolbarRight', '#siteToolbarTop' ],
        visibleDivs : [ '#siteDateTime', '#btnOptions', '#btnLoginLogout', '#btnChangeBackground', '#siteContent', '#siteStatusbar' ],
        configs : {
            'background' : [ ],
            'all' : [ '#siteContent', '#siteYoutubePlayer', '#siteYoutubeSearch', '#siteStatusbar' ],
            'content' : [ '#siteContent' ],
            'contentStatusbar' : [ '#siteContent', '#siteStatusbar' ],
            'contentMusicAndMusicSearch' : [ '#siteContent', '#siteYoutubePlayer', '#siteYoutubeSearch' ],
            'contentMusicComments' : [ '#siteContent', '#siteYoutubePlayer', '#siteComments' ],
            'contentComments' : [ '#siteContent', '#siteComments' ],
            'comments' : [ '#siteComments' ],
            'musicAndMusicSearch' : [ '#siteYoutubePlayer', '#siteYoutubeSearch' ],
            'musicComments' : [ '#siteYoutubePlayer', '#siteComments' ],
            'contentAndToolbarRight' : [ '#siteContent', '#siteToolbarRight' ]
        },
        defaultPos : {
            '#siteDateTime' : {
                top : 20,
                left : 10,
                opacity : 0.0001
            },
            '#siteContent' : {
                top : ($(window).height()/2)-50,
                left : ($(window).width()/2)-50,
                width : 100,
                height : 100,
                opacity : 0.0001
            },
            '#siteErrors' : {
                top : -1 * ( $(window).height() * 2),
                left : ($(window).width()/2) - ( $(window).width()/10/2 ),
                width : $(window).width()/10,
                height : $(window).height()/10,
                opacity : 0.0001
            },
            '#siteYoutubePlayer' : {
                top : $('#siteDateTime').height()+20,
                left : $(window).width()+100,
                opacity : 0.0001
            },
            '#siteYoutubeSearch' : {
                top : $('#siteDateTime').height()+20+$('#siteYoutubePlayer').height()+10,
                left : $(window).width()+100,
                width : !na.m.userDevice.isPhone ? 300 : $(window).width() - 50,
                opacity : 0.0001
            },  
            '#siteComments' : {
                top : $('#siteDateTime').height()+20,
                left : $(window).width()+100,
                width : !na.m.userDevice.isPhone ? 300 : $(window).width() - 50,
                opacity : 0.0001
            },
            '#siteToolbarThemeEditor' : {
                top : $('#siteDateTime').height()+20,
                left : -400,
                height : $(window).height()-120,
                width : !na.m.userDevice.isPhone ? 340 : $(window).width() - 50,
                opacity : 0.0001
            },
            '#siteToolbarLeft' : {
                top : $('#siteDateTime').height()+20,
                left : -420,
                height : $(window).height()-120,
                width : !na.m.userDevice.isPhone ? 400 : $(window).width() - 50,
                opacity : 0.0001
            },
            '#siteToolbarRight' : {
                top : $('#siteDateTime').height()+20,
                left : $(window).width()+100,
                height : $(window).height()-120,
                width : !na.m.userDevice.isPhone ? 300 : $(window).width() - 50,
                opacity : 0.0001
            },
            '#siteToolbarTop' : {
                top : -250,
                left : 10,
                height : 200,
                width : $(window).width() - 20,
                opacity : 0.0001
            },
            '#siteStatusbar' : {
                top : $(window).height() + 50,
                opacity : 0.0001
            }
        },                
        margin : (na.m.userDevice.isPhone?10:25)
    },
    settings : {
        animate : na.m.userDevice.isPhone,
        animating : true,
        showVideoBackgroundControls : false,
        visibleDivs : [ '#btnOptions' ],
        masterCallbacks : [],
        callbacks : [],
        callbacksProgress : [],
        cmds : []
    },
    
    init : function () {
        var divs = [], cookies = false;
        for (var divIdx in na.d.g.divs) {
            if (typeof na.d.g.divs[divIdx]!=='string') continue;
            var divID = na.d.g.divs[divIdx];
            if (na.d.g.defaultPos[divID]) $(divID).css(na.d.g.defaultPos[divID]);
            var cookie = $.cookie('visible_'+divID.substr(1));            
            if (cookie=='true') {
                cookies = true;
                divs[divs.length] = divID;
            }
        }
        if (!cookies) divs[0] = '#btnOptions';
        na.d.s.visibleDivs = divs;
        
        /*
        na.d.s.bypassScreenResolutionSizesLoading = true;
        if (!na.d.s.bypassScreenResolutionSizesLoading) {
            na.d.s.screenResolutionDataLoaded = false;
            var
            ajaxCmd = {
                type : 'GET',
                url : '/NicerAppWebOS/screenResolutionSizes.json?date='+na.m.changedDateTime_current(),
                success : function (data, ts) {
                    debugger;
                    try {
                        var dataDecoded = JSON.parse(data);
                        debugger;
                        na.d.s.screenResolutionSizes = dataDecoded;
                        na.d.s.screenResolutionDataLoaded = true;
                    } catch (err) {
                        debugger;
                        na.site.fail (err.message);
                    }    
                },
                failure : function (xhr, ajaxOptions, errorThrown) {
                    debugger;
                    na.site.onload_phase2();
                    na.site.fail (errorThrown, xhr, ajaxOptions);
                }
            };
            $.ajax (ajaxCmd);
        } else {
            
        }*/
        na.d.calculateWhichTopIconsToShow();
    },
    
    calculateWhichTopIconsToShow : function() {
        var 
        w = $(window).width(), 
        h = $(window).height(),
        wt = 0;
        
        $('#siteDateTime').css({display:'block', top : 20});
        $('.vividButton_icon_50x50_siteTop').each(function(idx,el){
            var el = $('#'+el.id);
            wt += el.width();
        });
        
        wt += 220; // menu width;
        
        if (wt + $('#siteDateTime').width() > w) {
            $('#siteDateTime').css({display:'none'});
        };
         
        return true;
        //na.d.goto (divs);
    },
    
    registerProgress : function (name, func) {
        var entry = { name : name, callback : func };
        na.d.deleteProgress(name);
        na.d.s.callbacksProgress.push (entry); // na.d.s = na.desktop.settings
    },
    
    deleteProgress : function (name) {
        for (var i=0; i<na.d.s.callbacksProgress.length; i++) {
            if (na.d.s.callbacksProgress[i].name == name) { na.d.s.callbacksProgress.splice(i,1); i--; if (i==na.d.s.callbacksProgress.length-1) break;}
        }
    },

    registerCallback : function (name, divID, func) {
        var entry = { name : name, divID : divID, callback : func };
        na.d.s.callbacks.push (entry); // na.d.s = na.desktop.settings
    },
    
    deleteCallback : function (name) {
        for (var i=0; i<na.d.s.callbacks.length; i++) {
            if (na.d.s.callbacks[i].name == name) { na.d.s.callbacks.splice(i,1); i--; if (i==na.d.s.callbacks.length-1) break;}
        }
    },
    
    isVisible : function (id) {
        return na.desktop.settings.visibleDivs.includes(id);
    },
    
    setConfig : function (configName) {
        var divs = [];
        for (var i=0; i<na.d.g.divs.length; i++) { // na.d.g = na.desktop.globals
            var divID = na.d.g.divs[i];
            $.cookie ('visible_'+divID.substr(1), 'false', na.m.cookieOptions());
        }
        for (var i=0; i<na.d.g.configs[configName].length; i++) { 
            var divID = na.d.g.configs[configName][i]; 
            divs[divs.length] = divID; 
            $.cookie ('visible_'+divID.substr(1), 'true', na.m.cookieOptions());
        };
        na.d.goto (divs, false);
        if (na.site.settings.menus['#siteMenu']) na.site.settings.menus['#siteMenu'].updateItemStates();
    },
    
    inConfig : function (configName) {
        var vdivs = [];
        for (var i=0; i<na.d.s.visibleDivs.length; i++) {
            var div = na.d.s.visibleDivs[i];
            if (div!=='#siteDateTime' && div!=='#siteStatusbar') vdivs.push(div);
        };
        var 
        json1 = JSON.stringify(vdivs),
        json2 = JSON.stringify(na.d.g.configs[configName]),
        r = json1 === json2;

        return r;
    },
    
    resize : function (callback, animate, reset) {
        na.d.s.animating = true;
        if (reset === undefined) reset = true;
        var anims = na.d.calculateWhichTopIconsToShow();
        if (animate===null) animate = anims;
        na.m.log (100, 'called : na.desktop.resize (callback, animate, reset); - animationSpeed='+na.d.g.animationSpeed, false);
        na.d.goto(na.d.s.visibleDivs,callback, animate, reset);
    },
    
    goto : function (visibleDivs, callback, animate, reset) {
        var fncn = 'na.desktop.goto (visibleDivs, callback, animate, reset)';
        if ($(window).width() > $(window).height()) {
            if (na.site.settings.current.orientation!=='landscape') {
                na.site.settings.current.orientation = 'landscape';
                if (na.site.globals.backgroundSearchKey.indexOf('portrait')!==-1) na.site.globals.backgroundSearchKey = 'landscape';
                na.site.loadTheme();
            }
        } else {
            if (na.site.settings.current.orientation !== 'portrait') {
                na.site.settings.current.orientation = 'portrait';
                if (na.site.globals.backgroundSearchKey.indexOf('landscape')!==-1) na.site.globals.backgroundSearchKey = 'portrait';
                na.site.loadTheme();
            }
        }

        if (animate===null) animate = na.m.userDevice.isPhone;
        na.d.s.animating = animate;
        if (typeof callback=='function') {
            var found = false;
            for (var i=0; i < na.d.s.masterCallbacks.length; i++) {
                var mcf = na.d.s.masterCallbacks[i];
                if (mcf === callback) found = true;
            }
            if (!found) {
                delete callback.masterCallbacksCalled;
                na.d.s.masterCallbacks.push (callback);
            }
        }

        if (
            $.cookie('agreedToPolicies')!=='true'
            || $.cookie('showStatusbar')==='true'
        ) {
            if (!visibleDivs.includes('#siteStatusbar')) visibleDivs.push('#siteStatusbar');
        }
        $('#siteStatusbar').css({height:'auto'}).delay(200);
        
        if ($('#siteDateTime').css('display')!=='none') visibleDivs.push('#siteDateTime');
        
        if (!visibleDivs.includes('#btnOptions')) visibleDivs.push('#btnOptions');
        if (!visibleDivs.includes('#btnLoginLogout')) visibleDivs.push('#btnLoginLogout');
        if (!visibleDivs.includes('#btnChangeBackground')) visibleDivs.push('#btnChangeBackground');
        
        if (!visibleDivs.includes('#siteContent')) visibleDivs.push('#siteContent');
        

        na.d.s.visibleDivs = visibleDivs;
        na.m.log (10010, fncn+' : calculated visibleDivs', false);


        na.d.g.defaultPos = {
            '#siteDateTime' : {
                top : 20,
                left : 10,
                opacity : 0.0001
            },
            '#siteContent' : {
                top : ($(window).height()/2)-50,
                left : ($(window).width()/2)-50,
                width : 100,
                height : 100,
                opacity : 0.0001
            },
            '#siteErrors' : {
                top : -1 * ( $(window).height() * 2),
                left : ($(window).width()/2) - ( $(window).width()/10/2 ),
                width : $(window).width()/10,
                height : $(window).height()/10,
                opacity : 0.0001
            },
            '#siteYoutubePlayer' : {
                top : $('#siteDateTime').height()+20,
                left : $(window).width()+100,
                opacity : 0.0001
            },
            '#siteYoutubeSearch' : {
                top : $('#siteDateTime').height()+20+$('#siteYoutubePlayer').height()+10,
                left : $(window).width()+100,
                width : !na.m.userDevice.isPhone ? 300 : $(window).width() - 50,
                opacity : 0.0001
            },  
            '#siteComments' : {
                top : $('#siteDateTime').height()+20,
                left : $(window).width()+100,
                width : !na.m.userDevice.isPhone ? 300 : $(window).width() - 50,
                opacity : 0.0001
            },
            '#siteToolbarThemeEditor' : {
                top : $('#siteDateTime').height()+20,
                left : -400,
                height : $(window).height()-120,
                width : !na.m.userDevice.isPhone ? 340 : $(window).width() - 50,
                opacity : 0.0001
            },
            '#siteToolbarLeft' : {
                top : $('#siteDateTime').height()+20,
                left : -420,
                height : $(window).height()-120,
                width : !na.m.userDevice.isPhone ? 400 : $(window).width() - 50,
                opacity : 0.0001
            },
            '#siteToolbarRight' : {
                top : $('#siteDateTime').height()+20,
                left : $(window).width()+100,
                height : $(window).height()-120,
                width : !na.m.userDevice.isPhone ? 300 : $(window).width() - 50,
                opacity : 0.0001
            },
            '#siteToolbarTop' : {
                top : -250,
                left : 10,
                height : 200,
                width : $(window).width() - 20,
                opacity : 0.0001
            },
            '#siteStatusbar' : {
                top : $(window).height() + 50,
                opacity : 0.0001
            }
        };
        //debugger;
        na.m.log (10010, fncn+' : set defaultPos', false);

        var calculationResults = {
            'calculationResults_visible' : na.m.negotiateOptions( // TODO : clean up, reduce number of evaluations
                (
                    $('#siteDateTime').css('display')!=='none'
                    ? {
                        mode : 0,
                        '#siteDateTime' : {
                            snapTo : [
                                { element : 'body', edge : 'top' },
                                { element : 'body', edge : 'left' }
                            ], 
                            offsetY : 5
                        },
                        '#btnOptions' : {
                            snapTo : [
                                { element : 'body', edge : 'top' },
                                { 
                                    element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none' ? '#siteDateTime' : 'body',
                                    edge : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none' ? 'right' : 'left'
                                }
                            ],
                            offsetY : 3
                        },
                        '#btnLoginLogout' : {
                            snapTo : [
                                { element : 'body', edge : 'top' },
                                { element : '#btnOptions', edge : 'right' }
                            ],
                            offsetY : 3
                        },
                        '#btnChangeBackground' : {
                            snapTo : [
                                { element : 'body', edge : 'top' },
                                { element : '#btnLoginLogout', edge : 'right' }
                            ],
                            offsetY : 3
                        },
                        '#siteMenu' : {
                            snapTo : [
                                { element : 'body', edge : 'top' },
                                { element : '#btnChangeBackground', edge : 'right' }
                            ],
                            growTo : 'maxX',
                            offsetY : 10
                        }
                    }
                    
                    : {
                        mode : 1,
                        '#btnOptions' : {
                            snapTo : [
                                { element : 'body', edge : 'top' },
                                { element : 'body', edge : 'left' }
                            ], 
                            offsetY : 3
                        },
                        '#siteMenu' : {
                            snapTo : [
                                { element : 'body', edge : 'top' },
                                { element : '#btnOptions', edge : 'right' }
                            ],
                            growTo : 'maxX',
                            offsetY : 10
                        },
                        '#btnLoginLogout' : {
                            snapTo : [
                                { element : 'body', edge : 'top' },
                                { element : 'body', edge : 'right' }
                            ],
                            offsetY : 3
                        },
                        '#btnChangeBackground' : {
                            snapTo : [
                                { element : 'body', edge : 'top' },
                                { element : '#btnLoginLogout', edge : 'rightNegative' }
                            ],
                            offsetY : 3
                        },
                    }
                     
                ),
            /*),
            'calculationResults_3rd_visible' : na.m.negotiateOptions(*/
                (
                    visibleDivs.includes('#siteStatusbar')
                    ? {
                        '#siteStatusbar' : {
                            mode : 0,
                            snapTo : [
                                { element : 'body', edge : 'left' },
                                { element : 'body', edge : 'bottom' }
                            ],
                            growTo : 'maxX',
                            yMinTop : ( na.desktop.settings.showVideoBackgroundControls ? 200 : 0 )
                        }
                    }
                    : {}
                ), (
                    visibleDivs.includes('#siteYoutubePlayer')
                    && visibleDivs.includes('#siteYoutubeSearch')
                    && !visibleDivs.includes('#siteToolbarRight')
                    && !visibleDivs.includes('#siteToolbarTop')
                    && !visibleDivs.includes('#siteErrors')
                    ? {
                        mode : 0,
                        '#siteYoutubePlayer' : {
                            snapTo : [
                                { element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none' ? '#siteDateTime' : '#btnOptions', edge : 'bottom' },
                                { element : 'body', edge : 'right' }
                            ]
                        },
                        '#siteYoutubeSearch' : {
                            snapTo : [
                                { element : '#siteYoutubePlayer', edge : 'bottom' },
                                { element : 'body', edge : 'right' }
                            ],
                            growTo : 'maxY',
                            growToLimits : (
                                visibleDivs.includes('#siteStatusbar') && $('#siteStatusbar').css('display')!=='none'
                                ? [ { element : '#siteStatusbar', edge : 'top' } ]
                                : []
                            )
                        }
                    }
                    : {}
                ), (
                    visibleDivs.includes('#siteYoutubePlayer')
                    && visibleDivs.includes('#siteComments')
                    && !visibleDivs.includes('#siteToolbarRight')
                    && !visibleDivs.includes('#siteToolbarTop')
                    && !visibleDivs.includes('#siteErrors')
                    ? {
                        mode : 1,
                        '#siteYoutubePlayer' : {
                            snapTo : [
                                { element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none' ? '#siteDateTime' : '#btnOptions', edge : 'bottom' },
                                { element : 'body', edge : 'right' }
                            ]
                        },
                        '#siteComments' : {
                            snapTo : [
                                { element : '#siteYoutubePlayer', edge : 'bottom' },
                                { element : 'body', edge : 'right' }
                            ],
                            growTo : 'maxY',
                            growToLimits : (
                                visibleDivs.includes('#siteStatusbar') && $('#siteStatusbar').css('display')!=='none'
                                ? [ { element : '#siteStatusbar', edge : 'top' } ]
                                : []
                            )
                        }
                    }
                    : {}
                ), (
                    visibleDivs.includes('#siteComments')
                    && !visibleDivs.includes('#siteYoutubePlayer')
                    && !visibleDivs.includes('#siteToolbarRight')
                    && !visibleDivs.includes('#siteToolbarTop')
                    && !visibleDivs.includes('#siteErrors')
                    ? {
                        mode : 2,
                        '#siteComments' : {
                            snapTo : [
                                { element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none' ? '#siteDateTime' : '#btnOptions', edge : 'bottom' },
                                { element : 'body', edge : 'right' }
                            ],
                            growTo : 'maxY',
                            growToLimits : (
                                visibleDivs.includes('#siteStatusbar') && $('#siteStatusbar').css('display')!=='none'
                                ? [ { element : '#siteStatusbar', edge : 'top' } ]
                                : []
                            )
                        }
                    }
                    : {}
                ), (
                    visibleDivs.includes('#siteToolbarRight')
                    ? {
                        mode : 0,
                        '#siteToolbarRight' : {
                            snapTo : [
                                { element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none' ? '#siteDateTime' : '#btnOptions', edge : 'bottom' },
                                {element : 'body', edge:'right'}
                            ],
                            growTo : 'maxY',
                            growToLimits : (
                                visibleDivs.includes('#siteStatusbar') && $('#siteStatusbar').css('display')!=='none'
                                ? [
                                    { element : '#siteStatusbar', edge : 'top' }
                                ]
                                : []
                            )
                        }
                    }
                    : {}
                ), (
                    visibleDivs.includes('#siteToolbarThemeEditor')
                    ? {
                        mode : 0,
                        '#siteToolbarThemeEditor' : {
                            snapTo : [
                                { element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none' ? '#siteDateTime' : '#btnOptions', edge : 'bottom' },
                                { element : 'body', edge : 'left' }
                            ],
                            growTo : 'maxY',
                            growToLimits : 
                                visibleDivs.includes('#siteStatusbar') && $('#siteStatusbar').css('display')!=='none'
                                ? [
                                    { element : '#siteStatusbar', edge : 'top' }
                                ]
                                : []
                        }
                    }
                    : {}
                ), (
                    visibleDivs.includes('#siteToolbarLeft')
                    && !visibleDivs.includes('#siteToolbarThemeEditor')
                    ? {
                        mode : 0,
                        '#siteToolbarLeft' : {
                            snapTo : [
                                { element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none' ? '#siteDateTime' : '#btnOptions', edge : 'bottom' },
                                { element : 'body', edge : 'left' }
                            ],
                            growTo : 'maxY',
                            growToLimits : 
                                visibleDivs.includes('#siteStatusbar') && $('#siteStatusbar').css('display')!=='none'
                                ? [
                                    { element : '#siteStatusbar', edge : 'top' }
                                ]
                                : []
                        }
                    }
                    : {}
                ), (
                    visibleDivs.includes('#siteToolbarLeft')
                    && visibleDivs.includes('#siteToolbarThemeEditor')
                    ? {
                        mode : 1,
                        '#siteToolbarLeft' : {
                            snapTo : [
                                { element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none' ? '#siteDateTime' : '#btnOptions', edge : 'bottom' },
                                { element : '#siteToolbarThemeEditor', edge : 'left' }
                            ],
                            growTo : 'maxY',
                            growToLimits : 
                                visibleDivs.includes('#siteStatusbar') && $('#siteStatusbar').css('display')!=='none'
                                ? [
                                    { element : '#siteStatusbar', edge : 'top' }
                                ]
                                : []
                        }
                    }
                    : {}
                ), (
                    visibleDivs.includes('#siteToolbarTop')
                    ? {
                        mode : 0,
                        '#siteToolbarTop' : {
                            snapTo : [
                                { element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none' ? '#siteDateTime' : '#btnOptions', edge : 'bottom' },
                                { element : 'body', edge : 'left' }
                            ],
                            growTo : 'maxX',
                            growToLimits : 
                                visibleDivs.includes('#siteYoutubePlayer') && $('#siteYoutubePlayer').css('display')!=='none'
                                ? [
                                    { element : '#siteYoutubePlayer', edge : 'left' }
                                ]
                                : visibleDivs.includes('#siteComments') && $('#siteComments').css('display')!=='none'
                                    ? { element : '#siteComments', edge : 'left' }
                                    : []
                        }
                    }
                    : {}
                ), (
                    visibleDivs.includes('#siteErrors')
                    ? {
                        mode : 0,
                        '#siteErrors' : {
                            snapTo : [
                                { 
                                    element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none'
                                        ? '#siteDateTime'
                                        : '#btnOptions',
                                    edge : 'bottom'
                                },
                                { element : 'body', edge : 'left' },
                            ],
                    
                            growTo : 'max',
                            growToLimits : 
                                visibleDivs.includes('#siteStatusbar') && $('#siteStatusbar').css('display')!=='none'
                                ? [
                                    { element : '#siteStatusbar', edge : 'top' }
                                ]
                                :  []
                        }
                    }
                    : {}
                ), (
                    visibleDivs.includes('#siteContent')
                    && !visibleDivs.includes('#siteToolbarTop')
                    && !visibleDivs.includes('#siteErrors')
                    ? {
                        '#siteContent' : {
                            mode : 0,
                            snapTo : 
                                visibleDivs.includes('#siteToolbarLeft')
                                ? [
                                        {
                                            element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none'
                                                    ? '#siteDateTime'
                                                    : '#btnOptions',
                                            edge : 'bottom'
                                        },
                                        { element : '#siteToolbarLeft', edge : 'right' }
                                ]
                                : visibleDivs.includes('#siteToolbarThemeEditor')
                                    ? [
                                        {
                                            element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none'
                                                    ? '#siteDateTime'
                                                    : '#btnOptions',
                                            edge : 'bottom'
                                        },
                                        { element : '#siteToolbarThemeEditor', edge : 'right' }
                                    ]
                                    : [
                                        {
                                            element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none'
                                                    ? '#siteDateTime'
                                                    : '#btnOptions',
                                            edge : 'bottom'
                                        },

                                        { element : 'body', edge : 'left' }
                                    ],
                            growTo : 'max',
                            growToLimits : []
                        }
                    }
                    : {}
                ), (
                    visibleDivs.includes('#siteContent')
                    && visibleDivs.includes('#siteToolbarTop')
                    && !visibleDivs.includes('#siteErrors')
                    ? {
                        '#siteContent' : {
                            mode : 1,
                            snapTo : 
                                visibleDivs.includes('#siteToolbarLeft')
                                ? [
                                        {
                                            element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none'
                                                    ? '#siteDateTime'
                                                    : '#btnOptions',
                                            edge : 'bottom'
                                        },
                                        { element : '#siteToolbarLeft', edge : 'right' }
                                ]
                                : visibleDivs.includes('#siteToolbarThemeEditor')
                                    ? [
                                        {
                                            element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none'
                                                    ? '#siteDateTime'
                                                    : '#btnOptions',
                                            edge : 'bottom'
                                        },
                                        { element : '#siteToolbarThemeEditor', edge : 'right' }
                                    ]
                                    : [
                                        {
                                            element : visibleDivs.includes('#siteDateTime') && $('#siteDateTime').css('display')!=='none'
                                                    ? '#siteDateTime'
                                                    : '#btnOptions',
                                            edge : 'bottom'
                                        },
                                        { element : 'body', edge : 'left' }
                                    ],
                            growTo : 'max',
                            growToLimits : []
                        }
                    }
                    : {}
                )
            ) // calculationResults_visible
        };
        //debugger;
        na.m.log (10010, fncn+' : calculated calculationResults_visible', false);

        var 
        w = $(window).width(),
        w1 = na.site.globals.reallySmallDeviceWidth,
        w2 = na.site.globals.smallDeviceWidth,
        crv = calculationResults.calculationResults_visible,
        vd = visibleDivs;
        //debugger;

        var c = calculationResults['calculationResults_visible'];
        c.order = $('#siteDateTime').css('display')!=='none'
                    ? [ '#siteDateTime', '#btnOptions', '#btnLoginLogout', '#btnChangeBackground', '#siteMenu' ]
                    : [ '#btnOptions', '#siteMenu', '#btnLoginLogout', '#btnChangeBackground' ];

        c.order.push ('#siteStatusbar');
        c.order.push ('#siteToolbarThemeEditor');
        c.order.push ('#siteToolbarLeft');
        c.order.push ('#siteToolbarRight');
        c.order.push ('#siteToolbarTop');
        c.order.push ('#siteErrors');
        /*
        if (visibleDivs.includes('#siteToolbarLeft')) c.order.push('#siteToolbarLeft');
        if (visibleDivs.includes('#siteToolbarRight')) c.order.push('#siteToolbarRight');
        if (visibleDivs.includes('#siteToolbarTop')) c.order.push('#siteToolbarTop');
        c.order.push ('#siteYoutubePlayer');
        c.order.push ('#siteYoutubeSearch');
        */
        c.order.push ('#siteComments');
        c.order.push ('#siteContent');
        
        if (c['#siteContent']) {
            let gtl = c['#siteContent'].growToLimits;

            if (visibleDivs.includes('#siteToolbarTop')) gtl.push ({ element : '#siteToolbarTop', edge : 'bottom' });
            if (visibleDivs.includes('#siteErrors')) gtl.push ({ element : '#siteErrors', edge : 'bottom' });
            if (visibleDivs.includes('#siteToolbarRight')) gtl.push ({ element : '#siteToolbarRight', edge : 'left' });
            if (visibleDivs.includes('#siteYoutubePlayer')) gtl.push ({ element : '#siteYoutubePlayer', edge : 'left' });
            if (visibleDivs.includes('#siteYoutubeSearch')) gtl.push ({ element : '#siteYoutubeSearch', edge : 'left' });
            if (visibleDivs.includes('#siteComments')) gtl.push ({ element : '#siteComments', edge : 'left' });
            if (visibleDivs.includes('#siteStatusbar')) gtl.push ({ element : '#siteStatusbar', edge : 'top' });
        }
        
        let divs = {};
        for (var sectionID in calculationResults) {
            let section = calculationResults[sectionID];
            
            var sectionKeys = Object.keys(calculationResults);
            for (var j=0; j<sectionKeys.length; j++) {
                if (sectionKeys[j]===sectionID) var sectionIdx = j;
            };            
            
            for (var i=0; i<section.order.length; i++) {
                var divID = section.order[i];

                if (!section[divID]) { continue; };
                divs[divID] = { top : 0, left : 0, width : $(divID).width(), height : $(divID).height() };
                //debugger;
            
                for (var j=0; j<section[divID].snapTo.length; j++) {
                    var 
                    sn = section[divID].snapTo[j],
                    offsetY = section[divID].offsetY ? section[divID].offsetY : 0,
                    offsetX = section[divID].offsetX ? section[divID].offsetX : 0;
                    //if (divID=='#btnOptions' || divID=='#siteMenu') debugger;
                   // if (divID=='#siteContent' || divID=='#siteToolbarThemeEditor') debugger;
                    switch (sn.edge) {
                        case 'top':
                            if (sn.element==='body') divs[divID].top = na.d.g.margin; else divs[divID].top = divs[sn.element].top + $(sn.element).height() + na.d.g.margin;
                            break;
                        case 'bottom':
                            if (sn.element==='body') {
                                divs[divID].top = $(window).height() - $(divID).height();
                            } else {
                                if (divs[sn.element]) divs[divID].top = divs[sn.element].top + $(sn.element).height() + na.d.g.margin;
                            }
                            break;
                        case 'left':
                            if (sn.element==='body') divs[divID].left = na.d.g.margin; 
                            else divs[divID].left = divs[sn.element].left + $(sn.element).width() + na.d.g.margin;
                            break;
                        case 'right':
                            if (sn.element=='body') {
                                divs[divID].left = $(window).width() - $(divID).width() - na.d.g.margin+ offsetX;
                            } else {
                                if (!divs[sn.element]) debugger;
                                divs[divID].left = divs[sn.element].left + $(sn.element).width() + na.d.g.margin + offsetX;
                            }
                            break;
                        case 'rightNegative':
                            if (!divs[sn.element]) debugger;
                            divs[divID].left = divs[sn.element].left - $(divID).width() - na.d.g.margin + offsetX;
                            break;
                    }
                }

                switch (section[divID].growTo) {
                    case 'max':
                        divs[divID].width = $(window).width() - divs[divID].left - na.d.g.margin;
                        divs[divID].height = $(window).height() - divs[divID].top;
                        break;
                    case 'maxX':
                        divs[divID].width = $(window).width() - divs[divID].left - na.d.g.margin;
                        divs[divID].height = $(divID).height();
                        break;
                    case 'maxY':
                        if ($(window).width() < na.site.globals.reallySmallDeviceWidth)
                            divs[divID].width = $(window).width() - (2 * na.d.g.margin)
                        else 
                            divs[divID].width = $(divID).width();
                        
                        divs[divID].height = $(window).height() - divs[divID].top;
                        break;
                }

                if (section[divID].growToLimits)
                for (var j=0; j<section[divID].growToLimits.length; j++) {
                    var gtl = section[divID].growToLimits[j];
                    switch (gtl.edge) {
                        case 'left': divs[divID].width -= ($(window).width() - divs[gtl.element].left); break;
                        case 'top': divs[divID].height -= ($(window).height() - divs[gtl.element].top); break;
                        case 'bottom': break; // doesnt need this : divs[divID].height -= $(gtl.element).height(); break;
                        case 'right': break; // doesnt need this either : divs[divID].width -= $(gtl.element).width(); break;
                    }
                }

                if (section[divID].xMinLeft) divs[divID].left -= section[divID].xMinLeft;
                if (section[divID].yMinTop) divs[divID].top -= section[divID].yMinTop;
                if (section[divID].xMinWidth) divs[divID].width -= section[divID].xMinWidth;
                if (section[divID].yMinHeight) divs[divID].height -= section[divID].yMinHeight;
//debugger;
                na.m.log (10010, fncn+' : calculated sections', false);

                switch (divID) {
                    case '#siteMenu':
                        //divs[divID].top += na.d.g.margin;
                        divs[divID].left += na.d.g.margin;
                        break;
                    case '#btnOptions':
                        if ($('#siteDateTime').css('display')!=='none') divs[divID].left += na.d.g.margin;
                        //divs[divID].top += na.d.g.margin;
                        break;
                    case '#siteDateTime':
                    case '#btnLoginLogout':
                    case '#btnChangeBackground':
                        //divs[divID].top += na.d.g.margin;
                        break;
                    case '#siteContent':
                        divs[divID].height -= na.d.g.margin;
                        //divs[divID].left += na.d.g.margin;
                        //divs[divID].width -= (2 * na.d.g.margin);
                        //if (visibleDivs.includes('#siteDateTime')) {
                                //divs[divID].top += na.d.g.margin;
                                //divs[divID].height -= na.d.g.margin;
                        //}
                        if (
                            visibleDivs.includes('#siteComments')
                            || visibleDivs.includes('#siteToolbarRight')                            
                        ) {
                            divs[divID].width -= ( na.d.g.margin );
                        }
                        break;
                    case '#siteYoutubePlayer':
                        divs[divID].left -= (na.d.g.margin);
                        divs[divID].top += (2 * na.d.g.margin );
                        break;
                    case '#siteToolbarLeft':
                    case '#siteToolbarThemeEditor':
                        //divs[divID].top += (na.d.g.margin);
                        divs[divID].height -= (2*na.d.g.margin);
                        break;
                    case '#siteYoutubeSearch':
                    case '#siteToolbarRight':
                        divs[divID].left -= na.d.g.margin;
                        divs[divID].top += na.d.g.margin;
                        divs[divID].height -= (3 * na.d.g.margin);
                        break;
                    case '#siteComments':
                        divs[divID].height -= (2*na.d.g.margin);
                        divs[divID].left += na.d.g.margin;
                        divs[divID].width -= (2 * na.d.g.margin);
                        if (visibleDivs.includes('#siteYoutubePlayer')) divs[divID].top += na.d.g.margin;
                        //if (visibleDivs.includes('#siteDateTime')) {
                            divs[divID].top += (2 * na.d.g.margin );
                            divs[divID].height -= (2 * na.d.g.margin );
                        //}
                        if (visibleDivs.includes('#siteStatusbar')) {
                            divs[divID].height -= (na.d.g.margin );
                        }
                        break;
                    case '#siteStatusbar':
                        divs[divID].top -= na.d.g.margin;
                        divs[divID].left += na.d.g.margin;
                        divs[divID].width -= (2 * na.d.g.margin);
                        divs[divID].height = 'auto';
                        break;
                }
            }
            //debugger;
            na.m.log (10010, fncn+' : calculated divs', false);


                
                
            for (var i=0; i<na.d.g.divs.length; i++) {
                var divID = na.d.g.divs[i], shown = false;
                for (var divID2 in divs) if (divID2==divID) shown = true;
                //if (shown) debugger;
                if (shown) $(divID).css({ display : 'block' });
            }

            var divsDone = [];
            

            // DIV position and dimensions calculations are done, now start to animate everything :
            na.d.s.masterCallbackIdx = 0;
            if (reset) na.d.s.animatingDivs = {};

            //debugger;

            if (reset)
            for (var masterCallbackIdx=0; masterCallbackIdx<section.order.length; masterCallbackIdx++) {
                let divID = section.order[masterCallbackIdx];
                na.d.s.animatingDivs[divID] = true;
            };

            /*
            var dp = $('.vividDialogPopup').not('#siteErrors');
            var dpa = 0;
            dp.each(function(idx,el) {
                if ($(el).css('display')!=='none') {
                    dpa++
                    $(el).stop().animate({
                        top : ( $(window).height() - $(el).height() ) / 2,
                        left : ( $(window).width() - $(el).width() ) / 2
                    });
                }
            });*/

            for (var masterCallbackIdx=0; masterCallbackIdx<section.order.length; masterCallbackIdx++) {
                na.m.log (10010, fncn+' : issuing animation calls for masterCallbackIdx='+masterCallbackIdx, false);
                let divID = section.order[masterCallbackIdx];
                var haveFiredAnimationsForDivAlready = false;
                for (var i=0; i < divsDone.length; i++) if (divsDone[i]==divID) haveFiredAnimationsForDivAlready = true;
                if (!haveFiredAnimationsForDivAlready) { 
                    divsDone.push(divID);
                    /*na.m.waitForCondition ('ready to animate divID='+divID,
                        function () {
                            return na.d.s.masterCallbackIdx < masterCallbackIdx
                        },
                        function () {
                        */
                            // HIDE all <div> that needs to be hidden
                            var shown = false;
                            for (var divID2 in divs) if (divID==divID2 && divID2==divID2) { shown = true; break; }

                            //na.m.log (15, 'na.desktop.goto (divID='+divID+', shown='+shown);
                            
                            
                            //if (divID=='#siteContent') debugger;
                            var
                            hasValidAvoidClause = false;
                            try {
                                var
                                avoidClause = $(divID).attr('avoid');
                                avoidClause = JSON.parse(avoidClause);
                                hasValidAvoidClause = true;
                            } catch (error) {
                                avoidClause = false;
                            }
                            if (avoidClause) {
                                for (var j=0; j < avoidClause.length; j++) {
                                    var divID2 = avoidClause[j];
                                    if (
                                        divs[divID]
                                        && divs[divID2]
                                        && divs[divID].left < divs[divID2].left
                                        && divs[divID].left + divs[divID].width > divs[divID2].left
                                    ) {
                                        var w1 = divs[divID2].left - divs[divID].left - na.d.g.margin;
                                        console.log ('t6543210:'+divID+':'+w1);
                                        divs[divID].width = w1;
                                    }
                                }
                            }

                            //if (divID!=='#siteContent') debugger;


                            if (!shown /*|| !visibleDivs.includes(divID)*/) {
                                var options = {
                                        queue : false,
                                        duration : na.d.g.animationSpeed,
                                        easing : 'swing',
                                        complete : function() {
                                            na.d.s.animatingDivs[divID] = false;
                                            na.d.masterCallback(callback, $(divID)[0], calculationResults, sectionIdx, section, i)
                                        }
                                };
                                if (na.d.s.animate && na.site.settings.current.siteInitialized)
                                    $(divID).stop(true,true,false).animate(na.d.g.defaultPos[divID],options);
                                else {
                                    na.d.s.animatingDivs[divID] = false;
                                    $(divID).stop(true,true,false).css(na.d.g.defaultPos[divID]);
                                }
                            } else {
                                // for mobile phones, use plain $(...).css() calls, for desktops, use $(...).animate() calls, 
                                // and don't forget to call the callback functions of course
                                if (divID=='#siteContent') {
                                    if (na.d.s.animate) {
                                        $(divID).css ({
                                            display : 'flex'
                                        }).animate ({
                                            top : divs[divID].top,
                                            left : divs[divID].left,
                                            width : divs[divID].width,
                                            height : divs[divID].height,
                                            opacity : 1
                                        }, { 
                                            queue : false,
                                            duration : na.d.g.animationSpeed,
                                            complete : function () {
                                                na.d.s.animatingDivs[divID] = false;
                                                na.d.masterCallback(callback, $(divID)[0], calculationResults, sectionIdx, section, i);
                                            }
                                        }); 
                                        for (var id in na.site.settings.na3D) {
                                            var el = na.site.settings.na3D[id];
                                            $('canvas', el.p)
                                                .animate (
                                                    { width : $(el.p).width(), height : $(el.p).height() }, 
                                                {
                                                    queue : false,
                                                    duration : na.d.g.animationSpeed
                                                }
                                                ).attr('width', $(el.p).width())
                                                .attr('height', $(el.p).height());
                                            el.camera.aspect = $(el.p).width() / $(el.p).height();
                                            el.camera.updateProjectionMatrix();
                                            el.renderer.setSize  ($(el.p).width(), $(el.p).height());
                                        };
                                        
                                    } else {
                                        let divID2 = divID;
                                        $(divID).stop(true,true,false).animate ({
                                            top : divs[divID].top,
                                            left : divs[divID].left,
                                            width : divs[divID].width,
                                            height : divs[divID].height,
                                            opacity : 1
                                        }, {
                                        queue : false,
                                            duration : na.d.g.animationSpeed,
                                            easing : 'swing',
                                            progress : function () {
                                                for (var i=0; i<na.desktop.settings.callbacksProgress.length; i++) {
                                                    var cb = na.desktop.settings.callbacksProgress[i].callback;
                                                    if (typeof cb=='function') cb ($(divID)[0]);
                                                }
                                            },
                                            complete : function () {
                                                for (var id in na.site.settings.na3D) {
                                                    var el = na.site.settings.na3D[id];
                                                    $('canvas', el.p)
                                                        .css ({ width : $(el.p).width(), height : $(el.p).height() })
                                                        .attr('width', $(el.p).width())
                                                        .attr('height', $(el.p).height());
                                                    el.camera.aspect = $(el.p).width() / $(el.p).height();
                                                    el.camera.updateProjectionMatrix();
                                                    el.renderer.setSize  ($(el.p).width(), $(el.p).height());
                                                };
                                                na.d.s.animatingDivs[divID] = false;
                                                na.d.masterCallback(callback, $(divID)[0], calculationResults, sectionIdx, section, i);
                                            }
                                        });
                                    }
                                } else if (!na.d.s.animate) {
                                    var props = {
                                        top : divs[divID].top,
                                        left : divs[divID].left,
                                        width : divs[divID].width,
                                        height : divs[divID].height,
                                        display : 'flex'
                                    };
                                    if (divID.substr(0,4)!=='#btn' || !na.m.userDevice.isPhone) props.opacity = 1;
                                    $(divID).css(props);
                                    na.d.s.animatingDivs[divID] = false;
                                    na.d.masterCallback(callback, $(divID)[0], calculationResults, sectionIdx, section, i);
                                        
                                } else {
                                    var props = {
                                        top : divs[divID].top,
                                        left : divs[divID].left,
                                        width : divs[divID].width,
                                        height : divs[divID].height 
                                    }, options = {
                                        queue : false,
                                        duration : na.d.g.animationSpeed,
                                        easing : 'swing',
                                        progress : function () {
                                            for (var i=0; i<na.desktop.settings.callbacksProgress.length; i++) {
                                                var cb = na.desktop.settings.callbacksProgress[i].callback;
                                                if (typeof cb=='function') cb ($(divID)[0]);
                                            }
                                        },
                                        complete : function() {
                                            na.d.s.animatingDivs[divID] = false;
                                            na.d.masterCallback(callback, $(divID)[0], calculationResults, sectionIdx, section, i)
                                        }
                                    };
                                    if (divID.substr(0,4)!=='#btn' || !na.m.userDevice.isPhone) props.opacity = 1;                         
                                    $(divID).stop(true,true,false).animate (props, options);
                                }
                            }
                        //}, 10); // ready to animate next DIV wait time between steps (in milliseconds)
                    } else {
                        masterCallbackIdx++;

                    }
            }
            
        }
    }, // goto()
    
    masterCallback : function (callbackFunction, div, calculationResults, sectionIdx, section, divOrderIdx) {
        var 
        fncn = 'na.desktop.masterCallback()',
        eh = na.site.settings.current.eventHandlers;
        //na.m.log (15, 'na.desktop.masterCallback (divID='+div.id+')');
        if (eh) {
            for (var i=0; i<eh.length; i++) {
                for (var j in eh[i]) {
                    var 
                    cb = eh[i][j],
                    ehSplit = j.split(':'),
                    component = ehSplit[0],
                    eventName = ehSplit[1];
                    
                    if (component=='na.desktop' && eventName=='afterResize') cb(null, div);
                }
            }
        }
        
        var allCompleted = true;
        for (var did in na.d.s.visibleDivs) {
            var ds = na.d.s.animatingDivs[did];
            if (ds) allCompleted = false;
        }
        //na.m.log (556, fncn + ' : na.desktop.settings.animatingDivs='+JSON.stringify(na.d.s.animatingDivs, null, 2), false);
        //debugger;
        //na.m.log (50, fncn + ' : allCompleted='+(allCompleted?'true':'false')+', na.m.HTMLidle()='+(na.m.HTMLidle()?'true':'false'), false);
        if (!allCompleted) {
            na.d.s.animating = true;
            return false;
        } else {
            na.d.s.animating = false;
        }


        // call desktop.registerCallback() callbackFunctions, 
        //  same as jQuery.animate({progress:callbackFunction});
        for (var i=0; i<na.d.s.callbacks.length; i++) {
            var cb = na.d.s.callbacks[i];
            if (cb.divID=='#'+div.id && typeof cb.callback=='function') cb.callback(cb, div, calculationResults, sectionIdx, section, divOrderIdx);
        };


        // and now call the na.desktop equivalent of jQuery.animate({complete:callbackFunction}) 
        //  for all #div.id, AFTER allCompleted==true and na.m.HTMLidle()===true
        na.m.waitForCondition('na.desktop.masterCallback() : #'+div.id+' : na.m.desktopIdle()?',
            na.m.desktopIdle, function () {
                na.desktop.masterCallback_do (div, calculationResults, sectionIdx, section, divOrderIdx);
            },
        50);

        if (allCompleted) na.d.s.masterCallbacks = [];
        /*
        na.m.waitForCondition('na.desktop.masterCallback() : na.m.desktopIdle() && all masterCallbacks called?', function () {
            var allDone = allCompleted;
            for (var i=0; i < na.d.s.masterCallbacks.length; i++) {
                var cf = na.d.s.masterCallbacks[i];
                for (var divID in calculationResults.calculationResults_visible) {
                    //debugger;
                    if ( divID == 'mode' || divID == 'order') continue;
                    if (
                        typeof cf.masterCallbacksCalled=='object'
                        && !(divID in cf.masterCallbacksCalled)
                    ) allDone = false;
                }
            }
            //debugger;
            return allDone;
        }, function () {
            na.d.s.masterCallbacks = [];
        }, 50);
        */
        na.d.s.masterCallbackIdx++;
    },

    masterCallback_do : function (div, calculationResults, sectionIdx, section, divOrderIdx) {
        //na.m.log (15, 'na.desktop.masterCallback_do (divID='+div.id+')',false);
        for (var i=0; i < na.d.s.masterCallbacks.length; i++) {
            var cf = na.d.s.masterCallbacks[i];
            if (!cf) debugger;
            if (!cf.masterCallbacksCalled) {
                cf.masterCallbacksCalled = {};
            }
            //debugger;
            if (
                '#'+div.id in calculationResults.calculationResults_visible
                && !('#'+div.id in cf.masterCallbacksCalled)
            ) {
                cf (div, calculationResults, sectionIdx, section, divOrderIdx);
                cf.masterCallbacksCalled['#'+div.id] = true;
            }
        }
    }
};
na.d = na.desktop;
na.d.g = na.d.globals;
na.d.s = na.d.settings;
na.d.s.animate = !na.m.userDevice.isPhone;
