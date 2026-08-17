// TODO : include vividButton-4.1.0.source.js in this file!
/*--
 * i'm not differentiating the different types of buttons in vividButton-5.y.z by 'version number' anymore,
 * i'm differentiating them by HTML class name (that always alongside a 'vividButton' class name) :
    (version 3: )
    * HTML CLASS vividButton_icon_png ===
        * Javascript CLASSES vividButton + vividButton_icon,
    * HTML CLASS vividButton_text,

    and for the latest web-technologies :
    (versions 4 & 5: )
    * vividButton_icon_canvas
    * vividButton_icon_svg
    * vividButton_icon_webgl
    which can *also* be combined with vividButton_icon_text
---*/

class naVividButton {
    constructor(el,html,parent,createHTML,idx) {
        var t = this;
        t.p = parent;
        if (createHTML===undefined) createHTML = true;
        if (createHTML && typeof html=='string' && html!=='') {
            var jqHTML = $(html);
            $(parent).append(jqHTML);
            t.el = jqHTML[0];
        } else {
            t.el = el;
        };
        
        var 
        $el = $('.vividButton_icon_50x50',$(t.el).parent()), // $el should be $(t.el)
        $el2 = $('.vividButton_icon_50x50_text',$(t.el).parent()),
        $el3 = $('.vividButton_icon_50x50_text > div',$(t.el).parent());

        if (!$el[0]) {
            var
            $el = $('.vividButton_icon_100x100',$(t.el).parent()), // $el should be $(t.el)
            $el2 = $('.vividButton_icon_100x100_text',$(t.el).parent()),
            $el3 = $('.vividButton_icon_100x100_text > div',$(t.el).parent());
        }
        
        $('img[srcPreload]',$(t.el).parent()).each(function(idx,el4) {
            $(el4).attr('src', $(el4).attr('srcPreload')).removeAttr('srcPreload');
        });        

        if ($(el).is('.vividButton4')) {
            t.icon_svg = new naVividButton_icon_svg (this.el, this);
        } else  if (createHTML) {
            t.el.this = t;
            t.theme = $(this.el).attr('theme');
            t.type = $(this.el).is('.vividButton_icon_50x50, .vividButton_icon_50x50_siteTop, .vividButton_icon_100x100') ? 'icon' : 'text';
            if ($('.vividButton_icon_50x50_text div', this.el).html()!=='') t.type = 'text';
            t.suffix = '';
            if ($el.is('.vividButton_icon_50x50')) t.suffix = '_50x50';
            if ($el.is('.vividButton_icon_50x50_siteTop')) t.suffix = '_50x50';
            if ($el.is('.vividButton_icon_100x100')) t.suffix = '_100x100';
            t.iconComponents = '.vividButton_icon_borderCSS'+t.suffix+', .vividButton_icon_imgBorder'+t.suffix+', .vividButton_icon_imgTile'+t.suffix+', .vividButton_icon_imgButtonIconBG'+t.suffix+', .vividButton_icon_imgButtonIcon'+t.suffix + ', .vividButton_icon_imgButtonIcon'+t.suffix+'_sup1'+ ', .vividButton_icon_imgButtonIcon'+t.suffix+'_sup2';
            t.icon = new naVividButton_icon(this.el, this);
            
            t.w = $(this.el).is('.vividButton_icon_100x100')
                ? 100 
                : $(this.el).is('.vividButton_icon_50x50, .vividButton_icon_50x50_siteTop')
                    ? 50
                    : $(this.el).width(); 
            t.h = $(this.el).is('.vividButton_icon_100x100')
                ? 100 
                : $(this.el).is('.vividButton_icon_50x50, .vividButton_icon_50x50_siteTop')
                    ? 50
                    : $(this.el).height(); 
        } else {
            t.icon = new naVividButton_icon(this.el, this);
        };
        //if (t.suffix && t.suffix=='_100x100') debugger;
        //if (t.el.id=='btnViewResult') debugger;
        //if (t.el.id=='btnFullResetOfAllThemes2') debugger;

        switch (this.type) {
            case 'icon' : 
                break;
                
            case 'text' : 
                /*
                if (
                    createHTML
                    && $el.length > 0
                    && $el.html()!==''
                ) {
                    var label = $el3.html();

                    var h = parseFloat($el3.height()) + parseFloat($el2.css('paddingTop')) + parseFloat($el2.css('paddingBottom'));
                    var thm = t.doTextHeightMeasurement(label);
                    //console.log('t1:',label, t.el.innerText, thm);
                    if ($(t.el).is('.vividButton_icon_50x50_text')) {
                        $(t.el).css({height:thm.h,fontSize:thm.fontSize}).attr('customHeight', thm.h);
                        //$el2.css({height:h}).attr('customHeight', h);
                    } else {
                        $(t.el).css({height:thm.h,fontSize:thm.fontSize}).attr('customHeight', thm.h);
                        //$el2.css({height:h/*,top:(50-(h))/2* /}).attr('customHeight', h);
                    }
                } else {
                    if ($('a',t.el).length>0) {
                        var label = $('a', t.el)[0].innerText;
                        var thm = t.doTextHeightMeasurement(label);
                        //console.log('t2:',label,thm);
                        $(t.el).add($('a span', t.el)).css({height:thm.h,fontSize:thm.fontSize}).attr('customHeight', thm.h);
                    } else if (
                        $el.length > 0
                        && $el.html()!==''
                    ) {
                        var label = $el3.html();
                        var thm = t.doTextHeightMeasurement(label);
                        //console.log('t3:',label, thm);
                        $(t.el).css({height:thm.h,fontSize:thm.fontSize}).attr('customHeight', thm.h);
                    } else {
                        var label = t.el.innerText;
                        var thm = t.doTextHeightMeasurement(label);
                        //console.log('t4:',label, thm);
                        $(t.el).css({height:thm.h,fontSize:thm.fontSize}).attr('customHeight', thm.h);
                    }
                }
                */
                break;
        }
        //debugger;
    }

    doTextHeightMeasurement (label) {
        var
        t = this,
        h = false,
        hInitial = $(t.el).height(),
        h1 = hInitial,
        h2 = false,
        textGrown = false,
        textShrunk = false,
        divGrown = false,
        minFontSize = '0.8rem',
        maxFontSize = '1rem';

        $(t.el).css({fontSize:minFontSize});

        var
        fontSize = parseInt($(t.el).css('fontSize')), // === 10 (!!!) .... .
        minFontSizePx = fontSize,
        w = $(t.el).width(),
        h = hInitial;

        //if (typeof label=='string' && label.indexOf('Night Sky')!==-1) debugger;
        //$(t.el).css({height:h1,fontSize:fontSize});
        while (
            !textGrown
            && !divGrown
        ) {
            $('.textCheck').remove();
            $('body').append(
                '<span id="textCheck__'+fontSize+'" class="textCheck vividButton vividButton_text" style="display:block;opacity:0.0001;font-size:'+fontSize+'px;width:'+w+'px;height:'+h1+'px;"><span class="hm" style="font-size:'+fontSize+'px;">'+label+'</span></span>'//height:'+$(t.el).height()+'px;
            );
            var span = $('#textCheck__'+fontSize+' > .hm');
            try {
                var h = span.height();
            } catch (e) {
                debugger;
            }
            $('body').append(
                '<span id="textCheck__default" class="textCheck vividButton vividButton_text" style="display:block;opacity:0.0001;font-size:'+maxFontSize+';width:'+w+'px;height:'+h1+'px;"><span class="hm" style="font-size:'+maxFontSize+';">'+label+'</span></span>'//height:'+$(t.el).height()+'px;
            );
            var span = $('#textCheck__default > .hm');
            try {
                var maxFontSizePx = span.height();
            } catch (e) {
                debugger;
            }

            if (!textGrown) {
                while (h < h1 - 4 && fontSize < maxFontSizePx) {
                    $('.textCheck').remove();
                    fontSize = fontSize + 1;
                    $('body').append(
                        '<span id="textCheck_'+t.el.id+'__'+fontSize+'" class="textCheck vividButton vividButton_text" style="opacity:0.0001;display:block;width:'+w+'px;height:'+h1+'px;font-size:'+fontSize+'px;"><span class="hm" style="font-size:'+fontSize+'px;">'+label+'</span></span>'//height:'+ih+'px;
                    );
                    var span = $('#textCheck_'+t.el.id+'__'+fontSize+' > .hm');

                    try {
                        var h = span.height();
                    } catch (e) {
                        debugger;
                    }
                    h2 = h;
                }
                textGrown = true;
            }
            if (!textShrunk) {
                //alert (minFontSizePx);
                while (h >= h1 - 4 && fontSize > minFontSizePx) {
                    fontSize = fontSize - 2;
                    $('.textCheck').remove();
                    $('body').append(
                        '<span id="textCheck_'+t.el.id+'__'+fontSize+'" class="textCheck vividButton vividButton_text" style="opacity:0.0001;display:block;width:'+w+'px;height:'+h1+'px;font-size:'+fontSize+'px;"><span class="hm" style="font-size:'+fontSize+'px;">'+label+'</span></span>'//height:'+ih+'px;
                    );
                    var span = $('#textCheck_'+t.el.id+'__'+fontSize+' > .hm');

                    try {
                        var h = span.height();
                    } catch (e) {
                        debugger;
                    }
                    h2 = h1;
                }
                textShrunk = true;
            }
            if (textShrunk && !divGrown) {
                while (h >= h1 - 4) {
                    h1 += 4;
                    $('.textCheck').remove();
                    $('body').append(
                        '<span id="textCheck_'+t.el.id+'__'+fontSize+'" class="textCheck vividButton vividButton_text" style="opacity:0.0001;display:block;width:'+w+'px;height:'+h1+'px;font-size:'+fontSize+'px;"><span class="hm" style="font-size:'+fontSize+'px;">'+label+'</span></span>'//height:'+ih+'px;
                    );
                    var span = $('#textCheck_'+t.el.id+'__'+fontSize+' > .hm');

                    try {
                        var h = span.height();
                    } catch (e) {
                        debugger;
                    }
                    h2 = h1;
                }
                divGrown = true;
            }
            while (h <= h1 - 4 && h1 > hInitial) {
                h1 -= 4;
                $('.textCheck').remove();
                $('body').append(
                    '<span id="textCheck_'+t.el.id+'__'+fontSize+'" class="textCheck vividButton vividButton_text" style="opacity:0.0001;display:block;width:'+w+'px;height:'+h1+'px;font-size:'+fontSize+'px;"><span class="hm" style="font-size:'+fontSize+'px;">'+label+'</span></span>'//height:'+ih+'px;
                );
                var span = $('#textCheck_'+t.el.id+'__'+fontSize+' > .hm');

                try {
                    var h = span.height();
                } catch (e) {
                    debugger;
                }
                h2 = h1;
            }
        }
        $('.textCheck').remove();
        //$(t.el).css({fontSize : fontSize, height : h1});
        if (!h2) h2 = h1;
        return {
            h : h2,
            fontSize : fontSize
        };
    }

    
    disable () {
        $(this.el).addClass('disabled');
        
        //$('.vividButton_icon_borderCSS_50x50, .vividButton_icon_imgBorder_50x50, .vividButton_icon_imgTile_50x50, .vividButton_icon_imgButtonIcon_50x50', this.el).css({filter:'grayscale(100%)'});
        if (this.icon) this.icon.disableButton (this.icon);
    }
    
    enable () {
        $(this.el).removeClass('disabled');
        
        //$('.vividButton_icon_borderCSS_50x50, .vividButton_icon_imgBorder_50x50, .vividButton_icon_imgTile_50x50, .vividButton_icon_imgButtonIcon_50x50', this.el).css({filter:'grayscale(0%)'});
        if (this.icon) this.icon.enableButton (this.icon);
    }    
    
    select () {
        $(this.el).addClass('selected');
        debugger;
        $('.vividButton_icon_borderCSS'+this.suffix, this.el).css({backgroundImage : 'radial-gradient(circle 70px at center, rgba(255, 166, 0, 1), rgba(255,166,0,1)', boxShadow : '0px 0px 2px 2px rgba(255,166,0,0.7)'});
    }    

    deselect () {
        $(this.el).removeClass('selected');
        $('.vividButton_icon_borderCSS'+this.suffix, this.el).css({backgroundImage : 'radial-gradient(circle 10px at center, rgba(0, 255, 0, 1), rgba(0,0,0,0)', boxShadow : ''});
    }    
}

class naVividButton_icon {
    constructor (el, parentObject) {
        var t = this;
        t.p = parentObject;
        t.el = el;
        t.elText = $('#'+el.id+'_text')[0];
        //if (t.el.id=='btnChangeBackground') debugger;
        //if (t.p.suffix=='_100x100') debugger;
        t.gradientRadius = 10;
        t.grayScale = 0;
        //if (t.el.id=='btnSelectBoxShadowSettings') debugger;
        if (t.el.id=='btnPlayPause') debugger;
        $(t.p.iconComponents, el).add(t.elText).hover(function () { t.hoverStarts(t) }, function () { t.hoverEnds(t) });
    }
    
    // ---
    // --- HIGHLIGHTING OF BORDERS OF BUTTONS ONHOVER :    
    // ---
    hoverStarts (t, setDirection) {
        t.anim_border_direction = 'increase';
        if (!$(t.el).is('.disabled') && !$(t.el).is('.selected')) {
            t.increaseGradient(t);
            if (t.elText) {
                $(t.elText).addClass('hover');
            }            
        }
    }
    
    hoverEnds(t, setDirection) {
        t.anim_border_direction = 'decrease';
        if (!$(t.el).is('.disabled')  && !$(t.el).is('.selected')) {
            t.decreaseGradient(t);
            if (t.elText) {
                $(t.elText).removeClass('hover');
            }            
        }
    }
    
    increaseGradient(t) {
        t.gradientRadius += 2;
        if (t.gradientRadius <= 70 && t.anim_border_direction=='increase') setTimeout (function () {
            t.setGradient(t);
            if (t.elText) t.setTextElementColor(t);
            t.increaseGradient(t);
        }, 20); 
    }
    
    decreaseGradient(t) {
        t.gradientRadius -= 2;
        if (t.gradientRadius >= 10 && t.anim_border_direction=='decrease') setTimeout (function () {
            t.setGradient(t);
            if (t.elText) t.setTextElementColor(t);
            t.decreaseGradient(t);
        }, 20); 
    }
    
    setGradient(t) {
        var style = 'radial-gradient(circle '+t.gradientRadius+'px at center, rgba(0,255,0,1), rgba(0,0,0,0))';
        //na.m.log (1000, '$("#'+t.el.id+'.vividButton_icon_borderCSS_50x50'+t.p.suffix+'")[0].style.backgroundImage = "'+style+'"');
        $('.vividButton_icon_borderCSS'+t.p.suffix, t.el)[0].style.backgroundImage = style;
    }

    setTextElementColor(t) {
        if (!t.cgTheme) {
            if ($(t.elText).is('.btnRename.forumCategory')) t.cgTheme = na.cg.themes.naColorgradientScheme_btnRename_forumCategory;
            else if ($(t.elText).is('.btnRename.forum')) t.cgTheme = na.cg.themes.naColorgradientScheme_btnRename_forum;
            else if ($(t.elText).is('.btnDelete')) t.cgTheme = na.cg.themes.naColorgradientScheme_btnDelete;
            else if ($(t.elText).is('.btnAdd')) t.cgTheme = na.cg.themes.naColorgradientScheme_btnAdd;
            else t.cgTheme = na.cg.themes.naColorgradientScheme_btnAdd;
        }
        if (!t.cgData) t.cgData = na.cg.generateList_basic (t.cgTheme, (70-10)/2);
        var style = t.cgData[((t.gradientRadius-10)/2)];
        if (style) $('div',t.elText)[0].style.color = style.color;
    }
    
    // ---
    // --- ENABLING / DISABLING ICON BUTTONS :    
    // ---
    disableButton (t) {
        t.anim_grayScale_direction = 'increase';
        t.increaseGrayScale(t);
    }
    
    enableButton (t) {
        t.anim_grayScale_direction = 'decrease';
        t.decreaseGrayScale(t);
    }
    
    increaseGrayScale(t) {
        t.grayScale += 2;
        t.setGrayScale(t);
        if (t.grayScale <= 98 && t.anim_grayScale_direction=='increase') setTimeout (function () {
            t.increaseGrayScale(t);
        }, 10);
    }
    
    decreaseGrayScale(t) {
        t.grayScale -= 2;
        t.setGrayScale(t);
        if (t.grayScale >= 2 && t.anim_grayScale_direction=='decrease') setTimeout (function () {
            t.decreaseGrayScale(t);
        }, 10);
    }
    
    setGrayScale(t) {
        $(t.p.iconComponents, t.el).each (function (idx,el) {
            el.style.filter = 'grayscale('+t.grayScale+'%)';
        });
    }
    
}


class naVividButton_icon_svg {
    constructor (el, parentObject) {
        this.init (el.id, parentObject);
    }

    async init (buttonHTMLid, parentObject) {
        var
        t = this;
        t.p = parentObject;

        t.globals = {
            debug : true,
            themes : {}
        };

        t.buttonTypes = {};

        t.settings = {
            buttonIdx : 0,
            buttons : {}
        };



        let el = $('#'+buttonHTMLid)[0];
        if ( !$(el).is('.vividButton4')) return false;

        var
        buttonType = $(el).attr('buttonType'),
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
        t.settings.buttons['#'+buttonHTMLid] = na.ui.vb.settings.buttons['#'+buttonHTMLid];
        let b = t.settings.buttons['#'+buttonHTMLid];

        b.state = b.btnCode.startupState;
        b.circumstance = b.btnCode.startupCircumstance;

        let
        c = b.btnCode.states[b.state].circumstances,
        l = b.btnCode.states[b.state].circumstances[b.circumstance].layers,
        ato = l.circleIcon_background.animTo.hover;

        t.calculateAnimSteps(b);
        await t.redraw(b);

        $('.circleIcon_background', b.el)[0].style.background = ato.steps[0];

        let layer = t.getLayer(b, l.circleIcon_svg.layerID);
        if (typeof layer.startupCode=='function') layer.startupCode();


        /*$(el).hover (function() {
            t.hoverOver (b);
        }, function() {
            t.hoverOut (b);
        });*/
        $('.circleIcon_svg',el).hover(function() {
            t.hoverOver (b);
        }, function() {
            t.hoverOut (b);
        });

        $(el).click(function() { t.onclick(event) });
    }

    getLayer (b, layerID) {
        var
        t = this;
        switch (layerID) {
            case 'b.btnCode.circumstances.normal.layers.circleIcon_background' :
                return b.btnCode.circumstances.normal.layers.circleIcon_background;
            case 'b.btnCode.circumstances.hover.layers.circleIcon_background' :
                return b.btnCode.circumstances.hover.layers.circleIcon_background;
            case 'b.btnCode.layers.circleIcon_svg' :
                return b.btnCode.layers.circleIcon_svg;
        }
        return false;
    }

    hoverOver (b) {
        var
        t = this;
        b.circumstance = 'normal';
        b.to.circumstance = 'hover';

        /*
        clearTimeout (t.settings.timeoutSetHoverClass);
        t.settings.timeoutSetHoverClass = setTimeout(function(){
            if (t.settings.hoverOutFiredRecently) {
                var l2 = b.btnCode.layers.circleIcon_svg;
                if (typeof l2.onmouseout=='function') l2.onmouseout();
            } else {
                $(b.el).addClass('hover');
            }
        }, 1000);*/

        var
        c = b.btnCode.states[b.state].circumstances,
        scl = b.btnCode.states[b.state].circumstances[b.circumstance].layers.circleIcon_background,
        l = t.getLayer(b, scl.layerID),
        ato = scl.animTo[b.to.circumstance];
        if (!ato) debugger;

        ato.step = 0;
        if (
            !$(b.el).is('.'+b.btnCode.selectedState)
            //&& !$(b.el).is('.'+b.btnCode.startupState)
        ) {
            if (
                !$(b.el).is('.disabled')
                //&& !$(b.el).is('.selected')
                && !$(b.el).is('.'+b.btnCode.startupState)

            ) {
                l.animDirection = 'increase';
                t.anim_increaseGradient(b, l, ato);
            } else {
                l.animDirection = 'decrease';
                b.circumstance = 'hover';
                b.to.circumstance = 'normal';
                t.anim_decreaseGradient(b, l, ato);
            };
        };

        var l2 = b.btnCode.layers.circleIcon_svg;
        if (!$(b.el).is('.disabled') && typeof l2.onmouseover=='function') l2.onmouseover();

        $(b.el).addClass('hover');
    }

    hoverOut (b) {
        var
        t = this;
       // if ($(b.el).is('.selected')) return false;
        //if ($(b.el).is('.recentlyClicked')) return false;
        b.circumstance = 'hover';
        b.to.circumstance = 'normal';
        $(b.el).removeClass('selected');

        /*
        t.settings.hoverOutFiredRecently = true;
        clearTimeout (t.settings.timeoutSetHoverOut);
        t.settings.timeoutSetHoverOut = setTimeout(function(){
            t.settings.hoverOutFiredRecently = false;
        }, 1000);*/

        var
        c = b.btnCode.states[b.state].circumstances,
        scl = b.btnCode.states[b.state].circumstances[b.circumstance].layers.circleIcon_background,
        l = t.getLayer(b, scl.layerID),
        ato = scl.animTo[b.to.circumstance];
        if (!ato) debugger;

        ato.step = 0;
        l.animDirection = 'decrease';
        if (
            (
                !$(b.el).is('.disabled')
                //&& !$(b.el).is('.selected')
//                 //&& !$(b.el).is('.recentlyClicked')
                && !$(b.el).is('.'+b.btnCode.selectedState)
                //&& !$(b.el).is('.'+b.btnCode.startupState)
            )

        ) {
            t.anim_decreaseGradient(b, l, ato);
        }

        var l2 = b.btnCode.layers.circleIcon_svg;
        if (
            typeof l2.onmouseout == 'function'
            && (
                !$(b.el).is('.disabled')
                //|| $(b.el).is('.'+b.btnCode.startupState)
            )
            && $(b.el).is('.hover')

        ) l2.onmouseout();

        $(b.el).removeClass('hover').removeClass('recentlyClicked');
    }

    onclick (evt) {
        var
        t = this;
        var b = na.ui.vb.settings.buttons['#'+$(evt.currentTarget)[0].id];
        var selected = (b && b.state == b.btnCode.selectedState);

        b.circumstance = 'normal';
        b.to.circumstance = 'hover';


        b.state = selected?b.btnCode.startupState:b.btnCode.selectedState;
        selected = (b.state == b.btnCode.selectedState);

        if (selected) $(b.el).addClass('selected'); else $(b.el).removeClass('selected');


        var
        buttonType = $(b.el).attr('buttonType'),
        b = t.settings.buttons['#'+b.el.id];


        //$('.circleIcon_background', b.el)[0].style.background = ato.steps[0];
        let
        c = b.btnCode.states[b.btnCode.selectedState].circumstances,
        l = b.btnCode.states[b.btnCode.selectedState].circumstances[selected?'normal':'hover'].layers,
        layer = t.getLayer(b, l.circleIcon_svg.layerID),
        ato = l.circleIcon_background.animTo[selected?'hover':'normal'];


        if (selected) {
            //if ( !$(b.el).is('.hover') ) {
                b.circumstance = 'normal';
                b.to.circumstance = 'hover';

                ato.step = 0;
                l.animDirection = 'increase';
                t.anim_increaseGradient(b, l, ato);
            //}
        } else {
            ato.step = 0;
            l.animDirection = 'decrease';
            t.anim_decreaseGradient(b, l, ato);
        }

        l = b.btnCode.layers.circleIcon_svg;
        if (typeof l.onclick=='function') {
            l.onclick();
        }

        setTimeout (function() {
            $(b.el).removeClass(b.btnCode.startupState).removeClass(b.btnCode.selectedState).addClass(b.state);
        }, 100);
        if ( $(b.el).is('.featureIsActive') ) $(b.el).removeClass('featureIsActive'); else $(b.el).addClass('featureIsActive');

        //if (!selected) {
            $(b.el).addClass('recentlyClicked');
            clearTimeout (t.settings.timeoutRecentlyClicked);
            t.settings.timeoutRecentlyClicked = setTimeout(function() {
                $(b.el).removeClass('recentlyClicked');
            }, 300);
        //}
    }

    anim_increaseGradient (b, l, ato) {
        var
        t = this;
        if (typeof ato.step=='undefined') ato.step = -1;
        ato.step++;
        b.circumstance = b.to.circumstance;
        if (ato.step < ato.animSteps && l.animDirection=='increase') setTimeout (function () {
            $('.circleIcon_background', b.el)[0].style.background = ato.steps[ato.step];
            t.anim_increaseGradient (b, l, ato);
        }, ato.animInterval);
    }

    anim_decreaseGradient (b, l, ato) {
        var
        t = this;

        if (typeof ato.step=='undefined') ato.step = -1;
        ato.step++;
        b.circumstance = b.to.circumstance;
        if (ato.step < ato.animSteps && l.animDirection=='decrease') setTimeout (function () {
            $('.circleIcon_background', b.el)[0].style.background = ato.steps[ato.step];
            t.anim_decreaseGradient (b, l, ato);
        }, ato.animInterval);
    }

    calculateAnimSteps (b) {
        var
        t = this;

        for (var stateName in b.btnCode.states) {
            var state = b.btnCode.states[stateName];

            for (var circumstanceName in state.circumstances) {
                var circumstance = state.circumstances[circumstanceName];

                for (var layerName in circumstance.layers) {
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
    }

    async redraw (b) {
        var
        t = this,
        cs = b.btnCode.states[b.state],
        cc = cs.circumstances[b.circumstance],
        cl = cc.layers,
        ccState = b.class+'--state-'+b.state,
        ccCircumstance = ccState+'--circumstance-'+b.circumstance,
        html = '<div class="vividButton_currentState">';

        for (var layerClass in cl) {
            t.settings.buttonIdx++;

            var
            htmlInner = '',
            style = '',
            scl = cl[layerClass],
            l = t.getLayer(b, scl.layerID),
            htmlID = 'btnLayer_'+t.settings.buttonIdx;

            if (l.img_src) style += 'background-image:url('+l.img_src+');';

            if (layerClass=='circleIcon_svg') {
                var
                circleIconLayerID = htmlID,
                loadLayer = async function () {
                    let result;
                    try {
                        var dt = t.changedDateTime_current();
                        result = await $.ajax ({
                            type : 'GET',
                            url : l.src+'?c='+dt,
                            // DO NOT REMOVE THE ?c='+dt, BECAUSE THIS WILL CAUSE CHANGES IN YOUR BUTTON SVG / WEBGL CODE
                            // NOT TO BE USED BY THE BROWSER (CACHING ISSUES).
                            //url : l.src, // PRODUCTION ONLY. USE AT YOUR OWN RISK!
                            success : function (data, ts, xhr) {
                                $('.'+circleIconLayerID, t.p.el).html(xhr.responseText);
                            },
                            error : function (xhr, ajaxOptions, thrownError) {
                                //na.site.fail ('"'+l.src+'" : '+thrownError.message+'<br/>'+xhr.responseText);
                                console.log ('t.redraw()::circleIcon_svg::WARNING : '+ajaxOptions+' in "'+l.src+'"');
                                console.log (thrownError);
                                $('.'+circleIconLayerID, t.p.el).html(xhr.responseText);
                            }
                        });
                        return true;
                    } catch (e) {
                        return false;
                    }
                };
            } else var loadLayer = null;

            html += '<div class="'+htmlID+' '+b.class+' '+ccState+' '+ccCircumstance+' '+layerClass+'" '+(style!==''?'style="'+style+'"':'')+'>'+htmlInner+'</div>';
        }
        html += '</div>';

        $(b.el).html(html).attr('buttonClass', b.btnCode.selfAttrButtonClass);
        if (typeof loadLayer == 'function') {
            await loadLayer()
        }
    }

    changedDateTime_current () {
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


}
