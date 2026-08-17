// Copyright (C) 2002-2023 and All Rights Reserved (R) by Rene AJM Veerman <rene.veerman.netherlands@gmail.com>
if (!na) var na = nicerapp = {};
if (!na.ui) na.ui = {};

if (!na.ui.vividButton) 
na.ui.vividButton = na.ui.vb = {
    
    globals : {
        debug : false,
        themes : {}
    },
    
    buttonTypes : {},
    
    settings : {
        buttonIdx : 0,
        buttons : {}
    },
    
    init : async function (buttonHTMLid) {
        let el = $('#'+buttonHTMLid)[0];
        if ( ! $(el).is('.vividButton4')) return false;
        
        var
        buttonType = $(el).attr('buttonType');
        btnCode = na.ui.vb.buttonTypes[buttonType];
        
        if (!btnCode) {
            na.site.fail ('Configuration error : na.ui.vividButton.buttonTypes["'+buttonType+'"] not found.');
            debugger;
            return false;
        }
        
        na.ui.vb.settings.buttons['#'+buttonHTMLid] = {
            el : el,
            type : buttonType,
            btnCode : btnCode,
            circumstance : 'normal',
            to : { circumstance : 'hover' }
        };
        let b = na.ui.vb.settings.buttons['#'+buttonHTMLid];
        
        b.state = b.btnCode.startupState;
        b.circumstance = b.btnCode.startupCircumstance;
        
        let 
        c = b.btnCode.states[b.state].circumstances,
        l = b.btnCode.states[b.state].circumstances[b.circumstance].layers,
        ato = l.circleIcon_background.animTo.hover;

        na.ui.vb.calculateAnimSteps(b);
        await na.ui.vb.redraw(b);

        $('.circleIcon_background', b.el)[0].style.background = ato.steps[0];

        let layer = na.ui.vb.getLayer(b, l.circleIcon_svg.layerID);
        if (typeof layer.startupCode=='function') layer.startupCode();
        

        /*$(el).hover (function() {
            na.ui.vb.hoverOver (b);
        }, function() {
            na.ui.vb.hoverOut (b);
        });*/
        $('.circleIcon_svg',el).hover(function() {
            na.ui.vb.hoverOver (b);
        }, function() {
            na.ui.vb.hoverOut (b);
        });

        $(el).click(function() { na.ui.vb.onclick(event) });
    },

    getLayer : function (b, layerID) {
        switch (layerID) {
            case 'b.btnCode.circumstances.normal.layers.circleIcon_background' : return b.btnCode.circumstances.normal.layers.circleIcon_background;
            case 'b.btnCode.circumstances.hover.layers.circleIcon_background' : return b.btnCode.circumstances.hover.layers.circleIcon_background;
            case 'b.btnCode.layers.circleIcon_svg' : return b.btnCode.layers.circleIcon_svg;
        }
        return false;
    },
    
    hoverOver : function (b) {
        b.circumstance = 'normal';
        b.to.circumstance = 'hover';

        /*
        clearTimeout (na.ui.vb.settings.timeoutSetHoverClass);
        na.ui.vb.settings.timeoutSetHoverClass = setTimeout(function(){
            if (na.ui.vb.settings.hoverOutFiredRecently) {
                var l2 = b.btnCode.layers.circleIcon_svg;
                if (typeof l2.onmouseout=='function') l2.onmouseout();
            } else {
                $(b.el).addClass('hover');
            }
        }, 1000);*/
        $(b.el).addClass('hover');

        var 
        c = b.btnCode.states[b.state].circumstances,
        scl = b.btnCode.states[b.state].circumstances[b.circumstance].layers.circleIcon_background,
        l = na.ui.vb.getLayer(b, scl.layerID),
        ato = scl.animTo[b.to.circumstance];
        if (!ato) debugger;

        if (na.ui.vb.globals.debug) debugger;
        ato.step = 0;
        l.animDirection = 'increase';
        if (
            !$(b.el).is('.disabled') 
            && !$(b.el).is('.selected') 
        ) {
            if (na.ui.vb.globals.debug) debugger;
            na.ui.vb.anim_increaseGradient(b, l, ato);
        }
        

        if (na.ui.vb.globals.debug) debugger;
        var l2 = b.btnCode.layers.circleIcon_svg;
        if (!$(b.el).is('.disabled') && typeof l2.onmouseover=='function') l2.onmouseover();
    },
    
    hoverOut : function (b) {
        if ($(b.el).is('.selected')) return false;
        if ($(b.el).is('.recentlyClicked')) return false;
        b.circumstance = 'hover';
        b.to.circumstance = 'normal';

        /*
        na.ui.vb.settings.hoverOutFiredRecently = true;
        clearTimeout (na.ui.vb.settings.timeoutSetHoverOut);
        na.ui.vb.settings.timeoutSetHoverOut = setTimeout(function(){
            na.ui.vb.settings.hoverOutFiredRecently = false;
        }, 1000);*/
        
        var 
        c = b.btnCode.states[b.state].circumstances,
        scl = b.btnCode.states[b.state].circumstances[b.circumstance].layers.circleIcon_background,
        l = na.ui.vb.getLayer(b, scl.layerID),
        ato = scl.animTo[b.to.circumstance];
        if (!ato) debugger;
        
        if (na.ui.vb.globals.debug) debugger;
        ato.step = 0;
        l.animDirection = 'decrease';
        if (
            (
                !$(b.el).is('.disabled') 
                && !$(b.el).is('.selected')
                && !$(b.el).is('.recentlyClicked')
                //&& !$(b.el).is('.'+b.btnCode.selectedState) 
                //&& !$(b.el).is('.'+b.btnCode.startupState)
            )
            
        ) {
            if (na.ui.vb.globals.debug) debugger;
            na.ui.vb.anim_decreaseGradient(b, l, ato);
        }
        
        if (na.ui.vb.globals.debug) debugger;
        var l2 = b.btnCode.layers.circleIcon_svg;
        if (
            typeof l2.onmouseout == 'function'
            && (
                !$(b.el).is('.disabled')
                || $(b.el).is('.'+b.btnCode.startupState)
            )
            && $(b.el).is('.hover')
            
        ) l2.onmouseout();
        
        $(b.el).removeClass('hover').removeClass('recentlyClicked');
    },
    
    onclick : function (evt) {
        var b = na.ui.vb.settings.buttons['#'+$(evt.currentTarget)[0].id];
        var selected = (b.state == b.btnCode.selectedState);
        
        b.circumstance = 'normal';
        b.to.circumstance = 'hover';


        b.state = selected?b.btnCode.startupState:b.btnCode.selectedState;
        selected = (b.state == b.btnCode.selectedState);
        debugger;
        
        if (selected) $(b.el).addClass('selected'); else $(b.el).removeClass('selected');
        
        
        var
        buttonType = $(b.el).attr('buttonType'),
        b = na.ui.vb.settings.buttons['#'+b.el.id];

        
        //$('.circleIcon_background', b.el)[0].style.background = ato.steps[0];
        let 
        c = b.btnCode.states[b.btnCode.selectedState].circumstances,
        l = b.btnCode.states[b.btnCode.selectedState].circumstances[selected?'normal':'hover'].layers,
        layer = na.ui.vb.getLayer(b, l.circleIcon_svg.layerID),
        ato = l.circleIcon_background.animTo[selected?'hover':'normal'];
    

        if (na.ui.vb.globals.debug) debugger;
        if (selected) {
            if ( !$(b.el).is('.hover') ) {
                b.circumstance = 'normal';
                b.to.circumstance = 'hover';
                    
                ato.step = 0;
                l.animDirection = 'increase';
                if (na.ui.vb.globals.debug) debugger;
                na.ui.vb.anim_increaseGradient(b, l, ato);
            }
        } else {
            ato.step = 0;
            l.animDirection = 'decrease';
            if (na.ui.vb.globals.debug) debugger;
            na.ui.vb.anim_decreaseGradient(b, l, ato);
        }

        setTimeout (function() {
            $(b.el).removeClass(b.btnCode.startupState).removeClass(b.btnCode.selectedState).addClass(b.state);
        }, 1000);
        if ( $(b.el).is('.featureIsActive') ) $(b.el).removeClass('featureIsActive'); else $(b.el).addClass('featureIsActive');

        //if (!selected) {
            $(b.el).addClass('recentlyClicked');
            clearTimeout (na.ui.vb.settings.timeoutRecentlyClicked);
            na.ui.vb.settings.timeoutRecentlyClicked = setTimeout(function() {
                $(b.el).removeClass('recentlyClicked');
            }, 3000);
        //}


        if (na.ui.vb.globals.debug) debugger;
        l = b.btnCode.layers.circleIcon_svg;
        if (typeof l.onclick=='function') {
            l.onclick();
        }
        
    },
    
    anim_increaseGradient : function (b, l, ato) {
        if (typeof ato.step=='undefined') ato.step = -1;
        ato.step++;
        b.circumstance = b.to.circumstance;
        if (ato.step < ato.animSteps && l.animDirection=='increase') setTimeout (function () {
            $('.circleIcon_background', b.el)[0].style.background = ato.steps[ato.step];
            na.ui.vb.anim_increaseGradient (b, l, ato);
        }, ato.animInterval); 
    },
    
    anim_decreaseGradient : function (b, l, ato) {
        if (typeof ato.step=='undefined') ato.step = -1;
        ato.step++;
        b.circumstance = b.to.circumstance;
        if (ato.step < ato.animSteps && l.animDirection=='decrease') setTimeout (function () {
            $('.circleIcon_background', b.el)[0].style.background = ato.steps[ato.step];
            na.ui.vb.anim_decreaseGradient (b, l, ato);
        }, ato.animInterval); 
    },
    
    calculateAnimSteps : function (b) {
        for (var stateName in b.btnCode.states) {
            var state = b.btnCode.states[stateName];
            
            for (var circumstanceName in state.circumstances) {
                var circumstance = state.circumstances[circumstanceName];
                
                for (layerName in circumstance.layers) {
                    var 
                    stateLayer = circumstance.layers[layerName],
                    layer = eval (stateLayer.layerID);
                    
                    for (var stateLayer_animTo_stateName in stateLayer.animTo) {
                        var 
                        stateLayerAnimTo = stateLayer.animTo[stateLayer_animTo_stateName];
                    
                        switch (layerName) {
                            case 'circleIcon_background' :
                                switch (layer.animType) {
                                    case 'css : radial-gradient' :
                                        
                                        for (var animTo_stateName in stateLayer.animTo) {
                                            var 
                                            a = stateLayer.animTo[animTo_stateName],
                                            stops = layer.linearColorStops,
                                            toCircumstance = b.btnCode.states[stateName].circumstances[animTo_stateName],
                                            toLayer = eval(toCircumstance.layers[layerName].layerID),
                                            toStops = toLayer.linearColorStops,
                                            v = { colorSteps : [] };
                                             
                                            if (!a.animSteps) a.animSteps = Math.round(a.animDuration / a.animInterval);
                                            
                                            for (var i=0; i<a.animSteps; i++) {
                                                var 
                                                stepCSS = 'radial-gradient(' + layer.shape + ' ' + layer.size + ' at ' + layer.position + ', ';
                                                
                                                for (var j=0; j<stops.length; j++) {
                                                    var 
                                                    s1a = stops[j].length[0],
                                                    s1b = stops[j].length[1],
                                                    s2a = toStops[j].length[0],
                                                    s2b = toStops[j].length[1],
                                                    cgTheme = {
                                                        themeName: 'vividButton4_cgTheme',
                                                        cssGeneration: {
                                                            colorLevels: {
                                                                // This sets "stops" for color gradients. 
                                                                //	   0 = outer level of display, 
                                                                //	 100 = deepest level of display.
                                                                0: { color: stops[j].color },
                                                                100: { color: toStops[j].color }
                                                                //Rules:
                                                                // 1: only css COLOR properties allowed here.
                                                                // 		color names allowed, for a list see http://www.w3schools.com/css/css_colornames.asp
                                                                // 2: properties used anywhere in a list like this must be present in both 0: and 100:
                                                            }
                                                        }
                                                    };
                                                    if (!v.colorSteps[j]) v.colorSteps.push(na.cg.generateList_basic (cgTheme, a.animSteps));

                                                    v.color = v.colorSteps[j][i].color;
                                                    v.stopA = Math.round( s1a + ( ( s2a - s1a ) / a.animSteps * i ) );
                                                    v.stopB = Math.round( s1b + ( ( s2b - s1b ) / a.animSteps * i ) );
                                                    
                                                    if (j!==0) stepCSS += ', ';
                                                    stepCSS += v.color + ' ' + v.stopA + '% ' + v.stopB + '%';
                                                    
                                                };
                                                
                                                stepCSS += ')';
                                                a.steps.push (stepCSS);
                                            }
                                        }
                                        break;
                                    default :
                                        alert ('na.ui.vividButton.calculateAnimSteps() : invalid layer.animType : '+layer.animType);
                                        break;
                                }
                                break;
                        }
                    }
                }
            }
        }
    },
    
    redraw : async function (b) {
        var 
        cs = b.btnCode.states[b.state],        
        cc = cs.circumstances[b.circumstance],        
        cl = cc.layers,        
        ccState = b.class+'--state-'+b.state,
        ccCircumstance = ccState+'--circumstance-'+b.circumstance,
        html = '<div class="vividButton_currentState">';
        
        //debugger;
        
        for (var layerClass in cl) {
            na.ui.vb.settings.buttonIdx++;

            var 
            htmlInner = '',
            style = '',
            scl = cl[layerClass],
            l = na.ui.vb.getLayer(b, scl.layerID),
            htmlID = 'btnLayer_'+na.ui.vb.settings.buttonIdx;
            
            if (l.img_src) style += 'background-image:url('+l.img_src+');';
            
            if (layerClass=='circleIcon_svg') {
                var 
                circleIconLayerID = htmlID,
                loadLayer = async function () {
                    let result;
                    try {
                        var dt = na.ui.vb.changedDateTime_current();
                        result = await $.ajax ({
                            type : 'GET',
                            url : l.src+'?c='+dt, 
                            // DO NOT REMOVE THE ?c='+dt, BECAUSE THIS WILL CAUSE CHANGES IN YOUR BUTTON SVG / WEBGL CODE 
                            // NOT TO BE USED BY THE BROWSER (CACHING ISSUES).
                            //url : l.src, // PRODUCTION ONLY. USE AT YOUR OWN RISK!
                            success : function (data, ts, xhr) {
                                $('#'+circleIconLayerID).html(xhr.responseText);
                            },
                            error : function (xhr, ajaxOptions, thrownError) {
                                //na.site.fail ('"'+l.src+'" : '+thrownError.message+'<br/>'+xhr.responseText);
                                console.log ('na.ui.vb.redraw()::circleIcon_svg::WARNING : '+ajaxOptions+' in "'+l.src+'"');                               
                                console.log (thrownError);
                                $('#'+circleIconLayerID).html(xhr.responseText);
                            }
                        });
                        return true;
                    } catch (e) {
                        return false;
                    }
                };
            } else var loadLayer = null;
          
            html += '<div id="'+htmlID+'" class="'+b.class+' '+ccState+' '+ccCircumstance+' '+layerClass+'" '+(style!==''?'style="'+style+'"':'')+'>'+htmlInner+'</div>';
        }
        html += '</div>';
        
        $(b.el).html(html).attr('buttonClass', b.btnCode.selfAttrButtonClass);
        if (typeof loadLayer == 'function') {
            await loadLayer()
        }
    },
    
    changedDateTime_current : function () {
        var 
        d = new Date(),
        r = d.getFullYear() 
            + ('0' + d.getMonth()+1).slice(-2)
            + ('0' + d.getDate()).slice(-2)
            + ('0' + d.getHours()).slice(-2)
            + ('0' + d.getMinutes()).slice(-2)
            + ('0' + d.getSeconds()).slice(-2);
        return r;
    }    
};
na.ui.vb.g = na.ui.vb.globals;
na.ui.vb.s = na.ui.vb.settings;
na.ui.vb.s.b = na.ui.vb.s.buttons;
