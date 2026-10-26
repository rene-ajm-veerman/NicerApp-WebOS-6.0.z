if (typeof na!=='object') { var NicerApp_WebOS = nicerapp = na = {}; }
na.desktop = na.d = {

    settings : { // Do not touch any of these startup values here. use na.d.initialize() instead!
        visibleDivs : [ '#siteContent', '#siteTaskbar' ],
        animating : true


    },

    initialize (settings) {
        var t = this;
        t.globals = t.g = {
            animationSpeed : 'fast',
            divs : [ '#siteTaskbar', '#siteDateTime', '#siteErrors', '#btnOptions', '#btnLoginLogout', '#btnChangeBackground', '#siteContent', '#siteYoutubePlayer', '#siteYoutubeSearch', '#siteComments', '#siteStatusbar', '#siteToolbarThemeEditor', '#siteToolbarLeft', '#siteToolbarRight', '#siteToolbarTop' ],
            originalDivs : [ '#siteStatusbar', '#siteTaskbar', '#siteContent' ],
            visibleDivs : [ '#siteStatusbar', '#siteTaskbar', '#siteContent' ],
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
            defaultPos : function() {
                return {
                '#siteDateTime' : {
                    top : -100,
                    left : 10,
                    display : 'none'
                },
                '#siteContent' : {
                    top : ($(window).height()/2)-50,
                    left : ($(window).width()/2)-50,
                    width : 100,
                    height : 100,
                    display : 'none'
                },
                '#siteErrors' : {
                    top : -1 * ( $(window).height() * 2),
                    left : ($(window).width()/2) - ( $(window).width()/10/2 ),
                    width : $(window).width()/10,
                    height : $(window).height()/10,
                    display : 'none'
                },
                '#siteYoutubePlayer' : {
                    top : $('#siteDateTime').height()+20,
                    left : $(window).width()+100,
                    height : $(window).height()-120,
                    display : 'none'
                },
                '#siteYoutubeSearch' : {
                    top : $('#siteDateTime').height()+20+$('#siteYoutubePlayer').height()+10,
                    left : $(window).width()+100,
                    width : !na.m.userDevice.isPhone ? 300 : $(window).width() - 50,
                    height : $(window).height()-120,
                    display : 'none'
                },
                '#siteComments' : {
                    top : $('#siteDateTime').height()+20,
                    left : $(window).width()+100,
                    width : $(window).width()/2,
                    height : $(window).height()-120,
                    display : 'none'
                },
                '#siteToolbarThemeEditor' : {
                    top : $('#siteDateTime').height()+20,
                    left : -1000,
                    height : $(window).height()-120,
			width : 550,
                    display : 'none'
                },
                '#siteToolbarLeft' : {
                    top : 20,
                    left : -1000,
                    height : $(window).height()-120,
                    display : 'none'
                },
                '#siteToolbarRight' : {
                    top : 20,
                    left : $(window).width()+100,
                    height : $(window).height()-120,
                    width : 600,//!na.m.userDevice.isPhone ? 300 : $(window).width() - 50,
                    display : 'none'
                },
                '#siteToolbarTop' : {
                    top : -250,
                    left : 10,
                    height : 200,
                    width : $(window).width() - 20,
                    display : 'none'
                },
                '#siteTaskbar' : {
                    top : $(window).height() - 100
                },
                '#siteStatusbar' : {
                    top : $(window).height() + 250,
                    height : '3em'
                }
                };
            },
            margin : 30
        };

        for (var divIdx=0; divIdx < t.g.divs.length; divIdx++) {
            var el_id = t.g.divs[divIdx];
            if (t.g.defaultPos()[el_id]) $(el_id).css(t.g.defaultPos()[el_id]);
        };

        t.s = t.settings = $.extend(t.settings, {
            animate : !na.m.userDevice.isPhone,
            animating : true,
            showVideoBackgroundControls : false,
            masterCallbacks : [],
            callbacks : [],
            callbacksProgress : [],
            cmds : []
        }, settings);

        //$(window).resize(t.resize);
        //setTimeout (t.resize, 200);

        window.removeEventListener('deviceorientation', na.desktop.reloadMenu);
        window.addEventListener('deviceorientation', na.desktop.reloadMenu);
        window.removeEventListener('devicemotion', na.desktop.reloadMenu);
        window.addEventListener('devicemotion', na.desktop.reloadMenu, false);
        window.removeEventListener('gesturechange', na.desktop.gestureChange);
        window.addEventListener('gesturechange', na.desktop.gestureChange, false);
        window.removeEventListener('gestureend', na.desktop.gestureChange);
        window.addEventListener('gestureend', na.desktop.gestureChange, false);

        document.removeEventListener('touchmove', na.desktop.gestureChange);
        document.addEventListener('touchmove', na.desktop.gestureChange, false);
        window.visualViewport.addEventListener("resize", na.desktop.gestureChange);
        /*
        $('body').hammer().on('pinchin', '.vividDialog', na.desktop.gestureChange);
        $('body').hammer().on('pinchout', '.vividDialog', na.desktop.gestureChange);
        na.site.settings.zingtouch = new ZingTouch.Region(document.body);
        na.site.settings.zingtouch.bind ($('#siteContent')[0], 'distance', na.desktop.gestureChange);
        */

    },

    reloadMenu : function () {
       // na.site.onresize({ reloadMenu : true });
    },

    gestureChange : function (e) {
        if (e.scale < 1.0) {
            // User moved fingers closer together
            na.site.settings.na3D['.na3D'].orbitControls.handleTouchMovePan (e);

        } else if (e.scale > 1.0) {
            // User moved fingers further apart
            na.site.settings.na3D['.na3D'].orbitControls.handleTouchMovePan (e);
        }
    },

    /**
     * Resize a canvas so its backing store matches the display size × devicePixelRatio.
     * Call this whenever the canvas is created or when the window/container resizes.
     *
     * @param {HTMLCanvasElement} canvas
     * @param {number} [maxDPR=3]  - optional cap (useful on very high-DPI phones)
     * @returns {boolean} true if the canvas was actually resized
     */
    setCanvasToDevicePixels : function (canvas, maxDPR = 3) {
        if (!canvas) return false;

        const dpr = Math.min(window.devicePixelRatio || 1, maxDPR);
        const rect = canvas.getBoundingClientRect();

        // Fallback if the  canvas is not yet in the layout
        const cssWidth  = rect.width  || canvas.clientWidth  || parseFloat(canvas.style.width)  || canvas.width;
        const cssHeight = rect.height || canvas.clientHeight || parseFloat(canvas.style.height) || canvas.height;

        const displayWidth  = Math.round(cssWidth  * dpr);
        const displayHeight = Math.round(cssHeight * dpr);

        if (canvas.width !== displayWidth || canvas.height !== displayHeight) {
            canvas.width  = displayWidth;
            canvas.height = displayHeight;

            // Keep the CSS size the same (important!)
            canvas.style.width  = cssWidth  + 'px';
            canvas.style.height = cssHeight + 'px';

            return true;
        }
        return false;
    },

    getStableViewportSize :function () {
        if (window.visualViewport) {
            return {
                width:  Math.round(window.visualViewport.width),
                height: Math.round(window.visualViewport.height)
            };
        }
        // fallback
        return {
            width:  window.innerWidth,
            height: window.innerHeight
        };
    },

    resize : function (callback, animate, reset) {
        if (na.desktop.settings.timeout_resize) clearTimeout (na.desktop.settings.timeout_resize);
        na.desktop.settings.timeout_resize = setTimeout (async function (callback, animate, reset) {
            //var x = String(na.m.stacktrace()).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');           alert(x);

            na.desktop.resize_do (callback, animate, reset)
        }, 100, callback, animate, reset)
    },

    resize_do : async function (callback, animate, reset) {
        var
        currentTime = performance.timeOrigin + performance.now(),
        fncn = 'na.desktop.resize()',
        t = na.desktop;
        //$('#siteContent .vividDialogContent').fadeOut('fast');

        na.d.s.animating = true;
        if (reset === undefined) reset = true;

        $('#siteBackground, #siteBackground iframe, #siteBackground img, #siteBackground div').css({
            width : $(window).width(),
            height : $(window).height()
        });

        if (!na.d.s.visibleDivs.includes('#siteTaskbar')) na.d.s.visibleDivs.push('#siteTaskbar');
        if (!na.d.s.visibleDivs.includes('#siteContent')) na.d.s.visibleDivs.push('#siteContent');

        var
        //cr = JSON.parse(JSON.stringify(na.desktop.settings['jsCodeParamsFor:::na.desktop.parseOptions']))
        cr = na.desktop.settings['jsCodeParamsFor:::na.desktop.parseOptions'];
        if (!cr) return false;

        var
        scr = JSON.stringify(cr);
        while (
            cr
            && (
                scr.indexOf('conditions')!==-1
                || scr.indexOf('jsCodeFunction')!==-1
            )
        ) {
            //debugger;
            cr = t.parseOptions(t, cr);
            scr = JSON.stringify(cr);
        }
        var calculationResults = na.desktop.settings.calculationResults = {
            calculationResults_visible : cr
        };

        cr[1001] = {// Append inside your desktop layout configurations or inside initialization definitions
            "#siteYoutubeContentManager" : {
                "mode" : 1,
                "snapTo" : [
                    { "element" : "body", "edge" : "top" },
                    { "element" : "body", "edge" : "left" }
                ],
                "growTo" : "max", // Forces it to match dimensions of #siteBackground
                "growToLimits" : [
                    { "element" : "#siteTaskbar", "edge" : "top" }
                ]
            }
        };
       cr.order = [];
       if (na.d.s.visibleDivs.includes('#siteStatusbar')) cr.order.push ('#siteStatusbar');
       cr.order.push ('#siteTaskbar');
       //cr.order.push ('#siteToolbarThemeEditor');
       if (na.d.s.visibleDivs.includes('#siteToolbarTop')) cr.order.push('#siteToolbarTop');
       if (na.d.s.visibleDivs.includes('#siteToolbarLeft')) cr.order.push('#siteToolbarLeft');
       if (na.d.s.visibleDivs.includes('#siteToolbarThemeEditor')) cr.order.push('#siteToolbarThemeEditor');
       if (na.d.s.visibleDivs.includes('#siteToolbarRight')) cr.order.push('#siteToolbarRight');
       if (na.d.s.visibleDivs.includes('#siteComments')) cr.order.push('#siteComments');
       if (na.d.s.visibleDivs.includes('#siteYoutubeContentManager')) cr.order.push('#siteYoutubeContentManager');
       cr.order.push ('#siteContent');
       //debugger;

        if (cr['#siteContent']) {
            let gtl = cr['#siteContent'].growToLimits;

            //if (visibleDivs.includes('#siteToolbarTop')) gtl.push ({ element : '#siteToolbarTop', edge : 'bottom' });
        }
        //debugger;

        let divs = {};
        for (var sectionID in calculationResults) {
            let section = calculationResults[sectionID];
            for (var sectionIdx in section) {

                /*
                var sectionKeys = Object.keys(calculationResults);
                for (var j=0; j<sectionKeys.length; j++) {
                    if (sectionKeys[j]===sectionID) var sectionIdx = j;
                };
                */

                /*
                if (!na.site.s.c.booted)
                for (var divID in na.d.g.defaultPos) {
                    $(divID).css(na.d.g.defaultPos[divID]);
                }*/

                for (var i=0; i<section.order.length; i++) {
                    var divID = section.order[i];
                    if (
                        !(
                            section[sectionIdx] && section[sectionIdx][divID]
                        )
                    ) continue;

                    //if (!section[divID]) { debugger; continue; };
                    divs[divID] = { top : 0, left : 0, width : $(divID).width(), height : $(divID).height() };
                    //debugger;

                    if (
                        section[sectionIdx]
                        && section[sectionIdx][divID]
                        && section[sectionIdx][divID].snapTo
                    )
                    for (var j=0; j<section[sectionIdx][divID].snapTo.length; j++) {
                        var
                        sn = section[sectionIdx][divID].snapTo[j],
                        offsetY = section[sectionIdx][divID].offsetY ? section[sectionIdx][divID].offsetY : 0,
                        offsetX = section[sectionIdx][divID].offsetX ? section[sectionIdx][divID].offsetX : 0;
                        //if (divID=='#btnOptions' || divID=='#siteMenu') debugger;
                        //if (divID=='#siteContent') debugger;
                        //if (divID=='#siteTaskbar') debugger;
                        switch (sn.edge) {
                            case 'top':
                                if (sn.element==='body') divs[divID].top = na.d.g.margin; else divs[divID].top = divs[sn.element].top - divs[divID].height - na.d.g.margin;
                                break;
                            case 'bottom':
                                if (sn.element==='body') {
                                    divs[divID].top = $(window).height() - $(divID).height() - na.d.g.margin;
                                } else {
                                    if (divs[sn.element]) divs[divID].top = divs[sn.element].top + $(sn.element).height() + na.d.g.margin;
                                }
                                break;
                            case 'left':
                                if (sn.element==='body') divs[divID].left = na.d.g.margin;
                                else if (divs[sn.element]) divs[divID].left = divs[sn.element].left + $(sn.element).width() + na.d.g.margin;
                                else divs[divID].left = na.d.g.margin;
                                break;
                            case 'right':
                                if (sn.element=='body') {
                                    divs[divID].left = $(window).width() - $(divID).width() - na.d.g.margin+ offsetX;
                                } else {
                                    if (divs[sn.element])
                                        divs[divID].left = divs[sn.element].left + divs[sn.element].width + offsetX;
                                    else
                                        divs[divID].left = na.d.g.margin + offsetX;
                                }
                                break;
                            case 'rightNegative':
                                divs[divID].left = divs[sn.element].left - $(divID).width() - na.d.g.margin + offsetX;
                                break;
                        }
                    }

                    if (
                        section[sectionIdx]
                        && section[sectionIdx][divID]
                        && section[sectionIdx][divID].growTo
                    )
                    switch (section[sectionIdx][divID].growTo) {
                        case 'max':
                            divs[divID].width = $(window).width() - divs[divID].left - na.d.g.margin;
                            divs[divID].height = $(window).height() - divs[divID].top - na.d.g.margin;
                                break;
                        case 'maxX':
                            divs[divID].width = $(window).width() - divs[divID].left - na.d.g.margin;
                            divs[divID].height = $(divID).height();
                            break;
                        case 'maxY':
                            /* drunken code :
                            if ($(window).width() < na.site.globals.reallySmallDeviceWidth)
                                divs[divID].width = $(window).width() - ( na.d.g.margin)
                            else
                                divs[divID].width = $(divID).width();
                            */

                            divs[divID].height = $(window).height() - divs[divID].top;
                            break;
                    }

                    var maxTop = 0, maxGTL = 0, minBottom = minCheck = 99999999999, minGTL;
                    if (
                        section[sectionIdx]
                        && section[sectionIdx][divID]
                        && section[sectionIdx][divID].growToLimits
                    )
                    for (var j=0; j<section[sectionIdx][divID].growToLimits.length; j++) {
                        var gtl = section[sectionIdx][divID].growToLimits[j];
                        if (gtl && divs[gtl.element])
                        switch (gtl.edge) {
                            case 'left': if (divs[gtl.element]) divs[divID].width -= ($(window).width() - (divs[gtl.element].left?divs[gtl.element].left:0)); break;
                            case 'top':
                                if (maxTop===0) {
                                    maxTop = divs[gtl.element];
                                    maxGTL = gtl;
                                }
                                if (
                                    typeof maxTop=='object'
                                    && divs[gtl.element].top > maxTop.top
                                ) {
                                    maxTop = divs[gtl.element];
                                    maxGTL = gtl;
                                }
                                break;
                            case 'bottom':
                                if (minBottom===minCheck) {
                                    minBottom = divs[gtl.element];
                                    minGTL = gtl;
                                }
                                if (
                                    divs[gtl.element]
                                    && divs[gtl.element].top < minBottom.top
                                ) {
                                    minBottom = divs[gtl.element];
                                    minGTL = gtl;
                                }
                                break;
                            case 'right': break; // doesnt need this either : divs[divID].width -= $(gtl.element).width(); break;
                        }
                    }
                    if (maxTop!==0) {
                        divs[divID].height = maxTop.top - divs[divID].top - na.d.g.margin;
                    }
                    if (minBottom!==minCheck) {
                        divs[divID].height -= minBottom.top + na.d.g.margin;
                    }

                    if (
                        section[sectionIdx]
                        && section[sectionIdx][divID]
                        && section[sectionIdx][divID].xMinLeft
                    ) divs[divID].left -= section[sectionIdx][divID].xMinLeft;
                    if (
                        section[sectionIdx]
                        && section[sectionIdx][divID]
                        && section[sectionIdx][divID].yMinTop
                    ) divs[divID].top -= section[sectionIdx][divID].yMinTop;
                    if (
                        section[sectionIdx]
                        && section[sectionIdx][divID]
                        && section[sectionIdx][divID].xMinWidth
                    ) divs[divID].width -= section[sectionIdx][divID].xMinWidth;
                    if (
                        section[sectionIdx]
                        && section[sectionIdx][divID]
                        && section[sectionIdx][divID].yMinHeight
                    ) divs[divID].height -= section[sectionIdx][divID].yMinHeight;
                    na.m.log (10010, fncn+' : calculated sections', false);


                    var
                    pl = parseInt($(divID).css('paddingLeft')),
                    pt = parseInt($(divID).css('paddingTop')),
                    pr = parseInt($(divID).css('paddingRight')),
                    pb = parseInt($(divID).css('paddingBottom'));


                    if (divs[divID].left>=na.d.g.margin*2) divs[divID].left = divs[divID].left - pl - pr; else divs[divID].width -= (pl + pr);
                    divs[divID].height = divs[divID].height - pt - pb;


                    var
                    ml = parseInt($(divID).css('marginLeft')),
                    mt = parseInt($(divID).css('marginTop')),
                    mr = parseInt($(divID).css('marginRight')),
                    mb = parseInt($(divID).css('marginBottom'));

                    divs[divID].left = divs[divID].left - ml - mr;
                    divs[divID].height = divs[divID].height - mt - mb;

                    if (divID=='#siteContent') {
                        if (na.d.s.visibleDivs.includes('#siteToolbarLeft')) {
                            divs[divID].left += na.d.g.margin;
                            divs[divID].width -= na.d.g.margin;
                        }
                        if (na.d.s.visibleDivs.includes('#siteToolbarThemeEditor')) {
                            divs[divID].left += na.d.g.margin;
                            divs[divID].width -= na.d.g.margin;
                        }
                    }

                    /*
                    switch (divID) {
                        case '#siteMenu':
                            //divs[divID].top += na.d.g.margin;
                            divs[divID].top = 'auto';
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
                        case '#siteToolbarLeft':
                        case '#siteToolbarThemeEditor':
                            divs[divID].height -= (1*na.d.g.margin);
                            break;
                        case '#siteContent':
                            divs[divID].height -= (2 * na.d.g.margin) - 6;
                            divs[divID].width -= na.d.g.margin;
                            //if (visibleDivs.includes('#siteDateTime')) {
                                //divs[divID].top += na.d.g.margin;
                                //divs[divID].height -= na.d.g.margin;
                            //}
                            if (
                                na.d.s.visibleDivs.includes('#siteComments')
                                || na.d.s.visibleDivs.includes('#siteToolbarRight')
                            ) {
                                //divs[divID].width -= ( na.d.g.margin );
                            }
                            break;
                        case '#siteYoutubePlayer':
                            divs[divID].left -= (na.d.g.margin);
                            divs[divID].top += (2 * na.d.g.margin );
                            break;
                        case '#siteYoutubeSearch':
                        case '#siteToolbarRight':
                            divs[divID].height -= na.d.g.margin;
                            break;
                        case '#siteComments':
                            divs[divID].height -= na.d.g.margin;
                            divs[divID].left += na.d.g.margin;
                            divs[divID].width -= (2 * na.d.g.margin);
                            if (na.d.s.visibleDivs.includes('#siteYoutubePlayer')) divs[divID].top += na.d.g.margin;
                            break;

                        case '#siteStatusbar':
                            divs[divID].top -= na.d.g.margin;
                            break;

                        case '#siteTaskbar':
                            divs[divID].top -= na.d.g.margin;
                            //divs[divID].left += na.d.g.margin;
                            //divs[divID].width -= (2 * na.d.g.margin);
                            divs[divID].height = 'auto';
                            break;
                    }*/
                }
            }

            na.m.log (101, fncn+' : calculated divs : '+JSON.stringify(divs), false);

            /*
            for (var i=0; i<na.d.g.divs.length; i++) {
                var divID = na.d.g.divs[i], shown = false;
                for (var divID2 in divs) if (divID2==divID) shown = true;
                //if (shown) debugger;
                if (shown) $(divID).css({ display : 'block' });
                else $(divID).css({ display : 'none' });
            }
            */

            var divsDone = [];


            // DIV position and dimensions calculations are done, now start to animate everything :
            na.d.s.masterCallbackIdx = 0;
            if (reset) na.d.s.animatingDivs = {};


            // reset all DIVs to their defaultPos
            for (var divIDx in na.d.g.defaultPos()) {
                if (!divs[divIDx]) {
                    var css = na.d.g.defaultPos()[divIDx];
                    $(divIDx).animate(css,'normal');
                }
            }

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
                //for (var i=0; i < divsDone.length; i++) if (divsDone[i]==divID) haveFiredAnimationsForDivAlready = true;
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

                            na.m.log (2100, 'na.c.desktop.resize (divID='+divID+', shown='+shown+')', false);


                            //if (divID=='#siteContent') debugger;
                            var
                            hasValidAvoidClause = false;
                            try {
                                var
                                avoidClause = $(divID).attr('avoid');
                                if (avoidClause) {
                                    avoidClause = JSON.parse(avoidClause);
                                    hasValidAvoidClause = true;
                                }
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
                                            na.d.masterCallback(callback, $(divID)[0], calculationResults, sectionIdx, section, i);
                                        }
                                };

                                var css = na.d.g.defaultPos()[divIDx];
                                if (na.d.s.animate) {
                                    $(divID).stop(true,true).animate (css,options);
                                } else {
                                    na.d.s.animatingDivs[divID] = false;
                                    $(divID).css(css);
                                }

                            } else {
                                // for mobile phones, use plain $(...).css() calls, for desktops, use $(...).animate() calls,
                                // and don't forget to call the callback functions of course
                                var options = {
                                        queue : true,
                                        duration : na.d.g.animationSpeed,
                                        easing : 'swing',
                                        complete : function() {
                                            na.d.s.animatingDivs[divID] = false;
                                            na.d.masterCallback(callback, $(divID)[0], calculationResults, sectionIdx, section, i);
                                            /*
                                            if (!na.site.s.c.booted)
                                                if (na.d.s.animate) {
                                                    $(divID).stop(true,true,false).animate(na.d.g.defaultPos[divID],options);
                                                } else {
                                                    na.d.s.animatingDivs[divID] = false;
                                                    $(divID).stop(true,true,false).css(na.d.g.defaultPos[divID]);
                                                }*/
                                        }
                                };
                                /*
                                if (na.site.s.c.booted)
                                    if (na.d.s.animate) {
                                        $(divID).stop(true,true,false).animate(na.d.g.defaultPos[divID],options);
                                    } else {
                                        na.d.s.animatingDivs[divID] = false;
                                        $(divID).stop(true,true,false).css(na.d.g.defaultPos[divID]);
                                    }*/

                                if (divID=='#siteContent') {
                                    if (na.d.s.animate) {
                                        $(divID).css ({
                                            display : 'block'
                                        }).stop(true,true).animate ({
                                            top : divs[divID].top,
                                            left : divs[divID].left,
                                            width : divs[divID].width,
                                            height : divs[divID].height,
                                            opacity : 1
                                        }, {
                                            queue : true,
                                            duration : na.d.g.animationSpeed,
                                            complete : function () {
                                                na.d.s.animatingDivs[divID] = false;
                                                na.d.masterCallback(callback, $(divID)[0], calculationResults, sectionIdx, section, i);
                                            }
                                        });
                                        if (na.site.settings.na3D)
                                        for (var id in na.site.settings.na3D) {
                                            var el = na.site.settings.na3D[id];
                                            $('canvas', el.p)
                                                .stop(true,true,false)
                                                .animate (
                                                    { width : $(el.p).width(), height : $(el.p).height() },
                                                    { queue : false, duration : na.d.g.animationSpeed }
                                                ).attr('width', $(el.p).width())
                                                .attr('height', $(el.p).height());
                                            na.m.waitForCondition ('na.site.settings.na3D available', function() { return el && el.camera; }, function() {
                                                el.camera.aspect = $(el.p).width() / $(el.p).height();
                                                el.camera.updateProjectionMatrix();
                                                el.renderer.setSize  ($(el.p).width(), $(el.p).height());
                                            }, 100);
                                        };

                                    } else {
                                        let divID2 = divID;
                                        $(divID).css ({
                                            opacity : 1,
                                            display : 'block'
                                        }).stop(true,true).animate({
                                            top : divs[divID].top,
                                            left : divs[divID].left,
                                            width : divs[divID].width,
                                            height : divs[divID].height,
                                        },{
                                            queue : true,
                                            duration : na.d.g.animationSpeed,
                                            easing : 'swing',
                                            complete : function () {
                                                if (na.site.settings.na3D)
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
                                        display : 'block'
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
                                        height : divs[divID].height,
                                        display : 'block'
                                    }, options = {
                                        queue : true,
                                        duration : na.d.g.animationSpeed,
                                        easing : 'swing',
                                        complete : function() {
                                            na.d.s.animatingDivs[divID] = false;
                                            na.d.masterCallback(callback, $(divID)[0], calculationResults, sectionIdx, section, i)
                                        }
                                    };
                                    if (divID.substr(0,4)!=='#btn' || !na.m.userDevice.isPhone) props.opacity = 1;
                                    $(divID).css({display:'block'}).stop(true,true).animate (props, options);
                                }
                            }
                        //}, 10); // ready to animate next DIV wait time between steps (in milliseconds)
                    } else {
                        masterCallbackIdx++;

                    }
            }

        }
    },

    masterCallback : function (callbackFunction, div, calculationResults, sectionIdx, section, divOrderIdx) {
        var
        fncn = 'na.desktop.masterCallback()',
        eh = na.site.settings.eventHandlers;

        na.m.log (2100, 'na.c.desktop.masterCallback (divID='+div.id+')');
        if (eh) {
            for (var i=0; i<eh.length; i++) {
                for (var j in eh[i]) {
                    var
                    cb = eh[i][j],
                    ehSplit = j.split(':'),
                    component = ehSplit[0],
                    eventName = ehSplit[1];

                    if (component=='na.c.desktop' && eventName=='afterResize') cb(null, div);
                }
            }
        }

        // call desktop.registerCallback() callbackFunctions,
        //  same as jQuery.animate({progress:callbackFunction});
        for (var i=0; i<na.d.s.callbacks.length; i++) {
            var cb = na.d.s.callbacks[i];
            if (cb.divID=='#'+div.id && typeof cb.callback=='function') {
                cb.callback(cb, div, calculationResults, sectionIdx, section, divOrderIdx);
            }
        };


        var allCompleted = true;
        for (var i=0; i<na.d.s.visibleDivs.length; i++) {
            var did = na.d.s.visibleDivs[i];
            var ds = na.d.s.animatingDivs[did];
            if (ds) allCompleted = false;
        }
        //na.m.log (556, fncn + ' : na.c.desktop.settings.animatingDivs='+JSON.stringify(na.d.s.animatingDivs, null, 2), false);
        //debugger;
        //na.m.log (50, fncn + ' : allCompleted='+(allCompleted?'true':'false')+', na.m.HTMLidle()='+(na.m.HTMLidle()?'true':'false'), false);
        if (!allCompleted) {
            na.d.s.animating = true;
            return false;
        } else {
            na.d.s.animating = false;
        }



        // and now call the na.c.desktop equivalent of jQuery.animate({complete:callbackFunction})
        //  for all #div.id, AFTER allCompleted==true and na.m.HTMLidle()===true
        na.m.waitForCondition('na.c.desktop.masterCallback() : #'+div.id+' : na.m.desktopIdle()?',
            na.m.desktopIdle, function () {
                na.desktop.masterCallback_do (div, calculationResults, sectionIdx, section, divOrderIdx);
            },
        50);

        //debugger;
        if (allCompleted) {
            $('#siteContent .vividDialogContent').show();//fadeIn('normal');
            if (typeof callbackFunction=='function') callbackFunction();

            // inside na.desktop.resize_do (or after the sizes are applied)
            na.d.setCanvasToDevicePixels(document.querySelector('canvas.naBorder'));
            na.d.setCanvasToDevicePixels(document.querySelector('canvas.naBackground'));

            // and any other canvases you create later
            na.d.s.masterCallbacks = [];
        }
        /*
        na.m.waitForCondition('na.c.desktop.masterCallback() : na.m.desktopIdle() && all masterCallbacks called?', function () {
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
        //na.m.log (15, 'na.c.desktop.masterCallback_do (divID='+div.id+')',false);
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
    },

    registerProgress : function (name, func) {
        var entry = { name : name, callback : func };
        na.d.deleteProgress(name);
        na.d.s.callbacksProgress.push (entry); // na.d.s = na.d.sktop.settings
    },

    deleteProgress : function (name) {
        for (var i=0; i<na.d.s.callbacksProgress.length; i++) {
            if (na.d.s.callbacksProgress[i].name == name) { na.d.s.callbacksProgress.splice(i,1); i--; if (i==na.d.s.callbacksProgress.length-1) break;}
        }
    },

    registerCallback : function (name, divID, func) {
        var entry = { name : name, divID : divID, callback : func };
        //na.d.s.callbacks.push (entry); // na.d.s = na.d.sktop.settings
    },

    deleteCallback : function (name) {
        for (var i=0; i<na.d.s.callbacks.length; i++) {
            if (na.d.s.callbacks[i].name == name) { na.d.s.callbacks.splice(i,1); i--; if (i==na.d.s.callbacks.length-1) break;}
        }
    },



    parseOptions : function (t, desktopDefinition) {
        var dd = $.extend({}, desktopDefinition);

        var p = { t : t, ld2 : {}, idxPath : '', idxPath2 : '/0' };
//debugger;
        na.m.walkArray (dd, dd, null, t.parseOptions_walkValue, false, p);
//debugger;

        var
        dd2 = t.arrayUnique ( [].concat(dd) ),
        dd3 = [{}];
        for (var i in dd2[0]) {
            dd3[0] = Object.assign(dd3[0], dd2[0][i]);
        }

        //debugger;
        return dd;
    },

    parseOptions_walkValue : function (cd) {
        const kc = 'jsCodeParam:::conditions';
        const kcm1 = 'jsCodeParam:::conditionsMet';
        const kcm2 = 'jsCodeParam_conditionsMet:::';
        const kcf = 'jsCodeParam:::conditionsFailed';
        const kcc = 'conditions';
        const kcm3 = 'conditionsMet';
        const kcf2 = 'conditionsFailed';
        var ka1 = parseInt(cd.k);


        if (typeof cd.v == 'object')
            var ka2 = 0, ka3 = 0;
            for (var k1 in cd.v) {
                if (k1 === undefined) continue;
                if (k1===kc) {
                    var vResult = cd.params.t.parseOptions_conditions(cd);
                    if (vResult) cd.at[cd.k] = cd.v[kcm1]; else cd.at[cd.k] = cd.v[kcf];
                    return true;
                }
                if (k1===kcc) {
                    var vResult = cd.params.t.parseOptions_conditions2(cd);
                    if (vResult) cd.at[cd.k] = cd.v[kcm3]; else cd.at[cd.k] = cd.v[kcf2];
                    return true;
                }
                if (k1.substr(0,'jsCodeFunction:::'.length)==='jsCodeFunction:::') {
                    var
                    p = k1.split(':::'),
                    p1 = p[1].match(/^(.*?)\(\'(.*?)\'\).*?$/i),
                    p2 = p1[2].split(',,'),
                    p7 = {};
                    ka2++;
                    ka3 = 0;
                    for (var i=0; i<p2.length; i++) {
                        var
                        p3 = p2[i].split('::'),
                        p4 = p3[1].split(','),
                        p5 = { 'jsCodeParam:::conditions' : [], 'jsCodeParam:::conditionsMet' : cd.v[k1], 'jsCodeParam:::conditionsFailed' : {} },
                        p5a = { "jsVar" : "na.d.s.visibleDivs", "jsVarIncludes" : "#siteContent" },
                        p6 = { 'jsCodeParam:::conditions' : [], 'jsCodeParam:::conditionsMet' : cd.v[k1], 'jsCodeParam:::conditionsFailed' : {} },
                        p6a = { "jsVar" : "na.d.s.visibleDivs", "jsVarExcludes" : "#siteContent" };
                        if (!cd.at[''+(ka1+ka2)]) cd.at[''+(ka1+ka2)] = {};
                        if (!cd.at[''+(ka1+ka2)][kc]) cd.at[''+(ka1+ka2)][kc] = {};
                        if (p3[0]==='visible')
                            for (var j=0; j<p4.length; j++) {
                                ka3++;
                                cd.at[''+(ka1+ka2)][kc][''+ka3] = $.extend({},p5a);
                                cd.at[''+(ka1+ka2)][kc][''+ka3].jsVarIncludes = p4[j];
                            };
                        if (p3[0]==='invisible')
                            for (var j=0; j<p4.length; j++) {
                                ka3++;
                                cd.at[''+(ka1+ka2)][kc][''+ka3] = $.extend({},p6a);

                                cd.at[''+(ka1+ka2)][kc][''+ka3].jsVarExcludes = p4[j];
                            };

                        //p7 = $.extend(p7,p5);
                        //cd.at[''+(ka1+ka2)] = $.extend (cd.at[''+(ka1+ka2)], p5);;
                        };
                    //debugger;
                    if (
                        typeof cd.v[k1]=='string'
                        && cd.v[k1].substr(0,kcm2.length)===kcm2
                    ) {
                        //debugger;
                        if (!cd.at[''+(ka1+ka2-1)]) cd.at[''+(ka1+ka2-1)]= {};
                        cd.at[''+(ka1+ka2)][kcm1] = eval(cd.v[k1].split(':::')[1]+'()');
                        cd.at[''+(ka1+ka2)][kcf] = {};
                    };
                }
                //var pointer = na.m.chaseToPath (cd.root, cd.path+'/'+cd.k);
                var pointer = na.m.chaseToPath (cd.root, cd.path);
                if (cd.k.match('jsCodeFunction:::')) {
                    var p2 = cd.path.split('/');
                    var p3 = cd.root;
                    for (var i=1; i<p2.length-2; i++) var p3 = p3[p2[i]];
                    delete p3[p2[i]];
                }

                //delete cd.at[''+(ka1+ka2)];
            };
    },

    parseOptions_conditions : function (cd) {
        const kc = 'jsCodeParam:::conditions';
        for (var i in cd.v[kc]) {
            var clauseResult = cd.params.t.parseOptions_clauseResult (cd, i);
            if (!clauseResult) return false;
        }
        return true;
    },

    parseOptions_clauseResult : function (cd, i) {
        const kc = 'jsCodeParam:::conditions';
        if (typeof cd.v[kc][i].jsVar=='string') {
            if (!cd.v[kc][i].jsVar.match(/[\w\.]+/)) {
                return false;
            }
            if (
                typeof cd.v[kc][i].jsVarIncludes=='string'
                && eval(cd.v[kc][i].jsVar).includes(cd.v[kc][i].jsVarIncludes)
            ) {
                return true;
            }
            if (
                typeof cd.v[kc][i].jsVarExcludes=='string'
                && !eval(cd.v[kc][i].jsVar).includes(cd.v[kc][i].jsVarExcludes)
            ) {
                return true;
            }
        }

        if (typeof cd.v[kc][i].htmlSelector=='string'
            && typeof cd.v[kc][i].cssPropertyName=='string'
            && cd.v[kc][i].isNotExactly
            && $(cd.v[kc][i].htmlSelector).css(cd.v[kc][i].cssPropertyName) !== cd.v[kc][i].isNotExactly
        ) return true;

        return false;
    },

    parseOptions_conditions2 : function (cd) {
        const kcc = 'conditions';
        const kcm3 = 'conditionsMet';
        const kcf2 = 'conditionsFailed';
        for (var i=0; i < cd.v[kcc].length; i++) {
            var clauseResult = cd.params.t.parseOptions_clauseResult2 (cd, i);
            if (!clauseResult) return false;
        }
        return true;
    },


    parseOptions_clauseResult2 : function (cd, i) {
        const kc = 'jsCodeParam:::conditions';
        const kcc = 'conditions';
        const kcm3 = 'conditionsMet';
        const kcf2 = 'conditionsFailed';
        if (typeof cd.v[kcc][i].jsVar=='string') {
            if (!cd.v[kcc][i].jsVar.match(/[\w\.]+/)) {
                return false;
            }
            if (
                typeof cd.v[kcc][i].jsVarIncludes=='string'
                && eval(cd.v[kcc][i].jsVar).includes(cd.v[kcc][i].jsVarIncludes)
            ) {
                return true;
            }
            if (
                typeof cd.v[kcc][i].jsVarExcludes=='string'
                && !eval(cd.v[kcc][i].jsVar).includes(cd.v[kcc][i].jsVarExcludes)
            ) {
                return true;
            }
        }

        if (typeof cd.v[kcc][i].htmlSelector=='string'
            && typeof cd.v[kcc][i].cssPropertyName=='string'
            && cd.v[kcc][i].isNotExactly
            && $(cd.v[kcc][i].htmlSelector).css(cd.v[kcc][i].cssPropertyName) !== cd.v[kcc][i].isNotExactly
        ) return true;

        return false;
    },

     arrayUnique : function (array) {
        var a = array.concat();
        for(var i=0; i<a.length; ++i) {
            for(var j=i+1; j<a.length; ++j) {
                if(
                    JSON.stringify(a[i]) === JSON.stringify(a[j])
                    || JSON.stringify(a[j]) === '{}'
                )
                    a.splice(j--, 1);
            }
        }

        return a;
    }
}

na.ytManager = {
    currentVideoId: null,
    lastQuery: '',
    nextContinuation: null,
    isLoadingMore: false,

    init() {
        $('#txtYoutubeManagerSearch').off('keydown.ytMgr').on('keydown.ytMgr', function(e) {
            if (e.which === 13) na.ytManager.search(true); // true = fresh search
        });

            // Infinite-scroll style: load more when user scrolls near bottom of results
            let resultsPane = $('#ytManagerSearchResults');
            resultsPane.off('scroll.ytMgr').on('scroll.ytMgr', function() {
                let el = this;
                if (el.scrollTop + el.clientHeight >= el.scrollHeight - 80) {
                    na.ytManager.loadMore();
                }
            });
    },

    async search(isFresh = true) {
        let query = $('#txtYoutubeManagerSearch').val().trim();
        if (!query && isFresh) return;

        if (isFresh) {
            this.lastQuery = query;
            this.nextContinuation = null;
        }

        let targetUrl = '/NicerAppWebOS/businessLogic/ajax/ajax_youtubeSearch_innertube.byGrok.php';
        let resultsPane = $('#ytManagerSearchResults');

        if (isFresh) {
            resultsPane.html('<div style="color:yellow; text-align:center; grid-column:1/-1;">Searching Innertube nodes...</div>');
        } else {
            // show a small loading indicator at the bottom
            if (!$('#ytMgrLoadMoreSpinner').length) {
                resultsPane.append('<div id="ytMgrLoadMoreSpinner" style="grid-column:1/-1; text-align:center; color:#aaa; padding:12px;">Loading more…</div>');
            }
        }

        let data = { maxResults: 50 };
        if (isFresh) {
            data.q = this.lastQuery;
        } else {
            data.q = this.lastQuery;          // still send q (harmless)
            data.continuation = this.nextContinuation;
        }

        try {
            let response = await $.ajax({
                url: targetUrl,
                type: 'GET',
                data: data,
                dataType: 'json'
            });

            $('#ytMgrLoadMoreSpinner').remove();

            if (isFresh) resultsPane.empty();

            if (!response.items || response.items.length === 0) {
                if (isFresh) {
                    resultsPane.html('<div style="color:red; grid-column:1/-1;">No results found.</div>');
                }
                this.nextContinuation = null;
                this._updatePagerUI(false);
                return;
            }

            response.items.forEach(item => {
                let vId     = item.id.videoId;
                let title   = item.snippet.title;
                let thumb   = item.snippet.thumbnails.medium.url;
                let channel = item.snippet.channelTitle;

                let itemHtml = `
                <div class="ytResultCard" onclick="na.ytManager.playVideo('${vId}')"
                style="cursor:pointer; background:rgba(255,255,255,0.2); padding:8px; border-radius:6px; transition:background 0.2s;">
                <img src="${thumb}" style="width:100%; border-radius:4px; aspect-ratio:16/9; object-fit:cover;" />
                <div style="font-size:x-large; font-weight:bold; margin-top:6px; color:#fff; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; height:auto;">${title}</div>
                <div style="font-size:large; color:cyan; margin-top:2px;">${channel}</div>
                </div>`;
                resultsPane.append(itemHtml);
            });

            this.nextContinuation = response.continuation || null;
            debugger;
            this._updatePagerUI(!!this.nextContinuation);

        } catch (err) {
            console.error(err);
            $('#ytMgrLoadMoreSpinner').remove();
            if (isFresh) {
                resultsPane.html('<div style="color:red; grid-column:1/-1;">Error querying source endpoint.</div>');
            }
        } finally {
            this.isLoadingMore = false;
        }
    },

    loadMore() {
        if (this.isLoadingMore || !this.nextContinuation) return;
        this.isLoadingMore = true;
        this.search(false); // false = continuation request
    },

    _updatePagerUI(hasMore) {
        // Optional explicit "Load more" button (works even without scroll)
        let btn = $('#ytMgrLoadMoreBtn');
        if (!btn.length) {
            $('#ytManagerSearchResults').after(
                '<button id="ytMgrLoadMoreBtn" type="button" ' +
                'style="display:none; margin:10px auto; padding:8px 18px; border-radius:6px; border:1px solid #555; background:rgba(255,255,255,0.555); color:cyan; text-shadow:0px 0px 3px rgba(0,0,0,0.555), 2px 2px 4px rgba(0,0,0,0.7); cursor:pointer;">' +
                'Load more results</button>'
            );
            btn = $('#ytMgrLoadMoreBtn');
            btn.on('click', () => na.ytManager.loadMore());
        }
        btn.toggle(hasMore);
        this.loadMore();
    },

    playVideo(videoId) {
        this.currentVideoId = videoId;
        $('#ytContentPlayer').attr('src', `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`);
        // … keep the rest of your existing playVideo logic …
    },

    addCurrentVideoToPlaylist() {
        if (!this.currentVideoId) {
            alert('Please select and play a video first.');
            return;
        }
        let refTree = $('#ytManagerTreeContainer').jstree(true);
        let selectedNode = refTree.get_selected(true)[0];

        if (!selectedNode) {
            alert('Please highlight a playlist destination node inside the CMS database tree layout.');
            return;
        }

        // Logic action execution post back handler routine destination payload
        console.log(`Mapping Video ${this.currentVideoId} inside Target Location Container context:`, selectedNode.id);

        // Execute AJAX call to save node allocation record data structure to back-end here
        alert(`Saved video directly into container: ${selectedNode.text}`);
    },

    treeAction(actionType) {
        let refTree = $('#ytManagerTreeContainer').jstree(true);
        let selected = refTree.get_selected(true)[0];

        if (actionType !== 'addFolder' && actionType !== 'addPlaylist' && !selected) {
            alert('Select target node tracking row entry item component context layer loop reference framework node execution points.');
            return;
        }

        switch(actionType) {
            case 'addFolder':
                refTree.create_node(selected || null, { "text": "New Folder", "type": "default" }, "last");
                break;
            case 'addPlaylist':
                refTree.create_node(selected || null, { "text": "New Playlist", "type": "file" }, "last");
                break;
            case 'delete':
                if(confirm(`Remove entry node "${selected.text}" entirely from operational context layers?`)) {
                    refTree.delete_node(selected);
                }
                break;
        }
    }
};

// Global management routine toggle hook injected alongside desktop.js context handlers
na.desktop.toggleYoutubeContentManager = function () {
    let dialog = $('#siteYoutubeContentManager');
    if (dialog.is(':visible')) {
        dialog.fadeOut('fast');
        // Delete item out of calculation visibility checks index references
        let idx = na.desktop.settings.visibleDivs.indexOf('#siteYoutubeContentManager');
        if (idx !== -1) na.desktop.settings.visibleDivs.splice(idx, 1);
    } else {
        dialog.fadeIn('fast', function() {
            dialog.css({display:'flex'});
            // Lazy load dependencies mapping hookups only when opened
            if(!$('#ytManagerTreeContainer').data('jstree')) {
                na.ytManager.init();
            }
        });
        na.desktop.settings.visibleDivs.push('#siteYoutubeContentManager');
    }
    na.desktop.resize();
};


na.d.s = na.d.settings;
na.ytManager.init();
