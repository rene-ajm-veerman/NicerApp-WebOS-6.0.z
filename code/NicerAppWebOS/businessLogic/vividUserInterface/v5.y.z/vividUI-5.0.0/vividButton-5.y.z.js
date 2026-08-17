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
        
        $('img[srcPreload]',$(t.el).parent()).each(function(idx,el4) {
            $(el4).attr('src', $(el4).attr('srcPreload')).removeAttr('srcPreload');
        });        
        
        if (createHTML) {
            t.el.this = t;
            t.theme = $(this.el).attr('theme');
            t.type = $(this.el).is('.vividButton_icon_50x50, .vividButton_icon_50x50_siteTop, .vividButton_icon_100x100') ? 'icon' : 'text';
            if ($('.vividButton_icon_50x50_text div', this.el).html()!=='') t.type = 'text';
            t.suffix = '';
            if ($el.is('.vividButton_icon_50x50')) t.suffix = '_50x50';
            if ($el.is('.vividButton_icon_50x50_siteTop')) t.suffix = '_50x50';
            if ($el.is('.vividButton_icon_100x100')) t.suffix = '_100x100';
            t.iconComponents = '.vividButton_icon_borderCSS'+t.suffix+', .vividButton_icon_imgBorder'+t.suffix+', .vividButton_icon_imgTile'+t.suffix+', .vividButton_icon_imgButtonIconBG'+t.suffix+', .vividButton_icon_imgButtonIcon'+t.suffix;
            t.icon = new navividButton_icon(this.el, this);
            
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
            t.icon = new navividButton_icon(this.el, this);
        };
        //if (t.suffix && t.suffix=='_100x100') debugger;
        //if (t.el.id=='btnViewResult') debugger;
        //if (t.el.id=='btnFullResetOfAllThemes2') debugger;

        switch (this.type) {
            case 'icon' : 
                break;
                
            case 'text' : 
                if (
                    createHTML
                    && $el.length > 0
                    && $el.html()!==''
                ) {
                    var label = $el3.html();

                    var h = parseFloat($el3.height()) + parseFloat($el2.css('paddingTop')) + parseFloat($el2.css('paddingBottom'));
                    if ($(t.el).is('.vividButton_icon_50x50_text')) {
                        $el2.css({height:h}).attr('customHeight', h);
                    } else {
                        $el2.css({height:h/*,top:(50-(h))/2*/}).attr('customHeight', h);
                    }
                } else {
                    if ($('a',t.el).length>0) {
                        var label = $('a', t.el)[0].innerHTML;
                        var thm = t.doTextHeightMeasurement(label);
                        $(t.el).css({/*height:thm.h,*/fontSize:thm.fontSize}).attr('customHeight', thm.h);
                    } else if (
                        $el.length > 0
                        && $el.html()!==''
                    ) {
                        var label = $el3.html();
                        var thm = t.doTextHeightMeasurement(label);
                        $el2.css({/*height:thm.h,*/fontSize:thm.fontSize}).attr('customHeight', thm.h);
                    }
                }

                break;
        }
        //debugger;
    }

    doTextHeightMeasurement (label) {
        var
        t = this,
        h = false,
        h1 = $(t.el).height(),
        fontSize = parseInt($(t.el).css('fontSize'));

        $('body').append(
            '<span id="textCheck__'+fontSize+'" class="textCheck vividButton_text" style="display:block;opacity:0.8;width:'+$('.vividButton',t.p).width()+'px;font-size:'+fontSize+'px;">'+label+'</span>'//height:'+$(t.el).height()+'px;
        );
        var span = $('#textCheck__'+fontSize);
        try {
            var h = span.height();
        } catch (e) {
            debugger;
        }


        while (h > h1 && fontSize > 8) {
            $('.textCheck').remove();
            fontSize = fontSize - 2;
            $('body').append(
                '<span id="textCheck_'+t.el.id+'__'+fontSize+'" class="textCheck vividButton_text" style="opacity:0.0001;width:'+$('.vividButton', t.p).width()+'px;display:block;font-size:'+fontSize+'px;">'+label+'</span>'//height:'+ih+'px;
            );
            var span = $('#textCheck_'+t.el.id+'__'+fontSize);
            try {
                var h = span.height();
            } catch (e) {
                debugger;
            }
        }
        $('.textCheck').remove();
        $(t.el).css({fontSize : fontSize});

        return {
            h : h,
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
        $('.vividButton_icon_borderCSS'+this.suffix, this.el).css({backgroundImage : 'radial-gradient(circle 70px at center, rgba(255, 166, 0, 1), rgba(255,166,0,1)', boxShadow : '0px 0px 2px 2px rgba(255,166,0,0.7)'});
    }    

    deselect () {
        $(this.el).removeClass('selected');
        $('.vividButton_icon_borderCSS'+this.suffix, this.el).css({backgroundImage : 'radial-gradient(circle 10px at center, rgba(0, 255, 0, 1), rgba(0,0,0,0)', boxShadow : ''});
    }    
}

class navividButton_icon {
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
