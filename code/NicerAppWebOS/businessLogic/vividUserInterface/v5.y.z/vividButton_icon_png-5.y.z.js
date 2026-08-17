class navividButton_icon {
    constructor (el, parentObject) {
        var t = this;
        t.version = '5.0.0';
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
