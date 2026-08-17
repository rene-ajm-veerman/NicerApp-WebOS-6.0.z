        // Copyright (C) 2002-2025, MIT-licensed by Rene AJM Veerman <rene.veerman.netherlands@gmail.com>
class naVividMenu__behavior_defaultColors {
    constructor(el, themeDefinitions, callback7) {
        var t = this;
        na.site.c.menus['#'+el.id] = this;
        t.el = el;
        t.el.t = t;
        if (typeof themeDefinitions=='object' && themeDefinitions.naDataType == 'vividMenu_themeDefinitions')
            t.themeDefinitions = $.extend({}, themeDefinitions);
            // add only a copy of the 'themeDefinitions' constructor function parameter data please, not just some javascript memory pointer.
            // only with a *copy* of the data, you're protected against Javascript code eval() threats!
        else
            t.themeDefinitions = {
                // in reality still preloaded from index.css.json for now
                // keys are case-insensitive in this entire data-format,
                //  that is set to become THE datastructure language
                //  for nicer.app-5.8.z-* branches of this repository.
                /*
                    ( ReadMe.MD : .../NicerAppWebOS/scripts.maintenance# ./setPermissions.sh )
                    ( is : ) root@anyMachine:/var/www/nicer.app-5.8.z/nicer.app-5.8.0-alpha001c/NicerAppWebOS/scripts.maintenance# ./setPermissions.sh
                    --- ..
                    END OF ./setPermissions.sh

                    root@anyMachine:/var/www/nicer.app-5.8.z/nicer.app-5.8.0-alpha001c/NicerAppWebOS/scripts.maintenance# ./ga.sh "small bugfixes, huge discovery for na5.8.z webOS final data format : case insensitive keynames with underscore strategy instead of CSS dash strategy for data key names"
                */

                na_dataType : 'vividMenu_themeDefinitions',
                na_browserLanguage_CSS : {
                    relativeURL : '/domainConfig/nicer.app-5.8.0-alpha001c/vividMenu-6.y.z_domain-nicer.app_defaultColors.css'
                }
            };
        t.theme = $(el).attr('theme');
        t.type = $(el).attr('type') === 'vertical' ? 'vertical' : 'horizontal';
        t.debugMe = false;

        t.useDelayedShowingAndHiding = true;
        t.sensitivitySpeedOpen = 400;
        t.sensitivitySpeedClose = 200;

        t.useFading = false; // na.site.saveTheme() gets called way more often during startup if this is set to true :-(
        t.fadingSpeed = 200;

        t.percentageFor_rainbowPanels =
            !na.site.settings.theme || na.site.settings.theme.menusUseRainbowPanels
            ? 100
            : 0;
        t.items = [];
        t.children = {};
        t.childPanels = {};
        t.shownChildren = {};
        t.shownMenuItems = {};

        // may be duplicates; TODO : normalize
        t.timeout_showSubMenu = {};
        t.timeout_showPanel = {};

        t.timeout_hideAll = {};
        t.timeout_hideSubMenu = {};
        t.timeout_onmouseout = {};
        t.panelsShown = {};

        //t.initWatchFunctions(t);
        t.initItems(t);
        if (!$(t.el).is('.noInitialShowing')) t.showMenu ();
        var dbg = {
            't.el'  : t.el,
            '1' : (!$(t.el).is('.noInitialShowing')) ,
            't' : t
        };

        $(el).not('.noFlex').css({display:'flex',height:50,alignItems:'center'});

        if (typeof callback7=='function') callback7(t.el);

        return this;
    }



    showMenu (t, showMeAnyways) {
        var t = this;
        //na.m.waitForCondition('showMenu : htmlIdle()',  na.m.HTMLidle, function () {
            var r =  null;
            var x1 = null;
            var r1 = [];
            $('.vividMenu_item', t.el).each(function(idx,itEl) {
                if (itEl.it && itEl.it.level===1 && ( !$(t.el).is('.noInitialShowing') || showMeAnyways)) {
                    var
                    dim = t.getDimensions(t, itEl, false);

                    $(itEl).css({position:'absolute',opacity:0.0001});
                    var x = t.showMenuItem (t, itEl.it, dim, { currentTarget : null });
                    if (!r) r = x;
                    if (!x1) x1 = x;
                    r1.push(x);

                    t.prevDisplayedEl = itEl;
                    if (t.useFading)
                        $(itEl).stop(true,true).fadeIn(t.fadingSpeed);
                    else
                        $(itEl).css({display:'block',opacity:0.0001});
                }
            });
            if (r1.length===0) return false;

            var
            dbgStrPart1 = 'naVividMenu.showMenu() : placing items in #'+r1[0].it.b.el.parent.id+' : ',
            dbgStrPart2 = '';
            for (var i=0; i<r1.length; i++) {
                if (dbgStrPart2!=='') dbgStrPart2 += ', ';
                dbgStrPart2 += '"'+r1[i].it.label+'"';
            }
            na.m.log (1120, dbgStrPart1+dbgStrPart2);

            if ($(t.el).is('.noInitialShowing')) {
                $(t.el).css ({
                    left : $(t.el.parentNode).position().left + 10,
                    top : $(t.el.parentNode).position().top + 60,
                    width : 200
                });

                var
                panelID = t.el.id+'__panel__'+t.el.id,
                html = '<div id="'+panelID+'" class="vividMenu_subMenuPanel">&nbsp;</div>';
                $('#'+panelID).remove();

                //if (!panel[0]) {
                    //if (!panel[0]) {
                        t.childPanels[t.el.id] = $(t.el).append(html);
                        var panel = $('#'+panelID)[0];
                        panel.it = t.el;

                        t.el.it = { parentDiv : t.el.parentNode };
                        if (r) {
                            t.timeout_showPanel[r.it.idx] = setTimeout(function(t, panel, r, x1) {
                                t.showPanel (
                                    t, event, panel, r.it, {idx : t.el.id, b : { el : t.el }}, r.dim, r.numColumns, (r.numKids / r.numColumns),
                                    $(x1.it.b.el.el).offset().left - $(t.el).offset().left,
                                    $(x1.it.b.el.el).offset().top - $(t.el).offset().top
                                );
                            }, t.sensitivitySpeedOpen, t, panel, r, x1);
                        }
                    //}
                //}
            };
        //});
    }

    initWatchFunctions_circularReplacer(key,value) {
        if (key=='parentDiv' || key=='el') {
            return getCircularReplacer(key,value);
        } else {
            //console.log (key, value);
            return getCircularReplacer(key,value);
        }
    }

    initWatchFunctions(t) {
        na.m.waitForCondition('report on major variables', function () {
            var
            t1 = $.extend({},t),
            dbg = {
                msg : 'naVividMenu.initWatchFunctions() : report on major variables',
                t : t1
            },
            dbgJSON = JSON.stringify(dbg, t.initWatchFunctions_circularReplacer);

            if (dbgJSON!==t.cache_dbg) {
                t.cache_dbg = dbgJSON;
                console.log (dbg);
            }
            return false;
        }, function () {}, 3333);
    }

    initItems(t) {
        $('.vividMenu_item', t.el).not('#siteMenu_vbChecker').remove();
        var
        html = '',
        rootItems = 0,
        LIs = $('.vividMenu_mainUL', t.el).find('li');

        LIs.each(function(idx,li) {
            $(li).attr('id', t.el.id+'__li__'+idx);
            var btnType = $(li).attr('buttonType');
            if (!btnType || btnType=='') btnType = 'vividButton_text';
            //var html2 = '<div id="'+t.el.id+'__'+idx+'" class="vividButton '+btnType+' vividMenu_item backdropped"  theme="dark" style="opacity:0.0001;"><div class="vividDialogBackground1" style="z-index:-1"></div>'+$(li).children('a')[0].outerHTML.replace('<a ', '<a style="z-index:-1" ').replace($(li).children('a')[0].innerHTML+'</a>', '<span class="contentSectionTitle3_span" style="z-index:-1">'+$(li).children('a')[0].innerText+'</span></a>').replace('class="', 'class="linkToNewPage contentSectionTitle3_a ')+'</div>';
            var html2 = '<div id="'+t.el.id+'__'+idx+'" class="vividButton '+btnType+' vividMenu_item"><div class="vividDialogBackground1" style="z-index:-1"></div>'+$(li).children('a')[0].outerHTML.replace('class="', 'class="linkToNewPage ')+'</div>';
            html += html2;
            debugger;


            t.items[idx] = {
                idx : idx,
                label : $('a',li)[0].innerText,
                li : li,
                level : jQuery(li).parents('ul').length,
                path : ''
            };
            li.it = t.items[idx];
        });

        LIs.each(function(idx,li) {
            var
            it = t.items[idx],
            itp = null,
            p = jQuery(li).parents('ul > li'),
            psIt = [];

            if (p.length > 0) {
                t.items[idx].parent = p[0];
                t.items[idx].parentDiv = $('#'+t.el.id+'__'+p[0].id.replace(/.*__/,''))[0];

                for (var pIdx=0; pIdx < p.length; pIdx++) {
                    var
                    pHTMLid = p[pIdx].id,
                    idx2 = parseInt(pHTMLid.replace(/.*__/,''));
                    if (t.items[idx2]) {
                        psIt.push (t.items[idx2]);
                    }
                }
                t.items[idx].parents = psIt;
            }

            if (it && it.level === 1) {
                rootItems++;
                if (!t.children[t.el.id]) t.children[t.el.id] = [];
                t.children[t.el.id].push(it);
                it.levelIdx = t.children[t.el.id].length;
            } else if (it && it.parent && it.parent.id==='') {
                var
                itp_idx = parseInt(it.li.id.replace(/.*__/,'')),
                itp = t.items[itp_idx];
                if (!itp) debugger;
                if (!t.children[itp.idx]) t.children[itp.idx] = [];
                t.children[itp.idx].push(it);
                it.levelIdx = t.children[itp.idx].length;
            } else if (it && it.parent) {
                var
                itp_idx = parseInt(it.parent.id.replace(/.*__/,'')),
                itp = t.items[itp_idx];
                if (!itp) debugger;
                if (!t.children[itp.idx]) t.children[itp.idx] = [];
                t.children[itp.idx].push(it);
                it.levelIdx = t.children[itp.idx].length;
            } //else debugger;
        });

        $('#menu__'+t.el.id).remove();
        $(t.el).append('<div id="menu__'+t.el.id+'" class="vividMenu_menu"></div>');
        var elbcr = t.el.getBoundingClientRect();
        if (t.el.parentNode===document.body)
            $('#menu__'+t.el.id).css({
                position : 'relative',
                top : 0,/*(
                    t.el.parentNode===document.body
                    || t.el.parentNode.id === t.el.parentNode.id+'_containerDiv'
                    ? elbcr.top
                    : 0
                ),//(t.el.parentNode===document.body?elbcr.top:0),*/
                left : 0//elbcr.left,//(t.el.parentNode===document.body?elbcr.left:0),
            }).append(html);
        else {
            $('#menu__'+t.el.id).css({
                position : 'relative'
            }).append(html);
        }



        LIs.each(function(i,li) {
            var it = t.items[i];
            //console.log (it.level+' - '+it.label);
            if (it.level===1) {
                if (!it.b) t.createVividButton (t, i, li);
            }
        });


        var btns = $('#menu__'+t.el.id).find('div');
        btns.each (function(i,btn) {
            var it = t.items[i];
            btn.it = it;
        })

    }

    createVividButton (t, i, li) {
        var
        el = $('#'+t.el.id+'__'+i)[0],
        it = t.items[i];

        el.it = it;

        el.idx = i;
        el.b = it.b = new vividUserInterface_2D_button({
            el:el,
            html:null,
            parent:t.el,
            createHTML:true,
            opacity : parseFloat($(el).css('opacity'))
        });

        it.b.el.menu = t;
        it.b.el.it = it;
        it.b.el.t = t;
        it.b.el.idx = it.idx;
        it.b.el.subMenuIdx = i;
        it.b.el.level = it.level;
        if (it.level === 1 && !$(t.el).is('.noInitialShowing')) $(it.b.el).css({ display : 'block' }); else $(it.b.el).css({ display : 'none' });

        $(it.b.el.el).css({opacity:it.b.opacity});

        $(it.b.el.el).bind('mouseenter', function(event) {
            event.stopPropagation();



            var
            idx = parseInt(event.currentTarget.id.replace(/.*__/,'')),
            id = event.currentTarget.id.replace('__'+idx,''),
            t = na.site.components.menus['#'+id],
            it = t.items[idx];

            if (t.debugMe) na.m.log(20,'naVividMenu.createVividButton : mouseenter '+it.label, false);

            $('.vividMenu_item a').removeClass('contentSectionTitle1_a');//.addClass('contentSectionTitle3_a');
            //$('.vividMenu_item span').removeClass('contentSectionTitle1_span').addClass('contentSectionTitle3_span');
            if (it.parents)
            for (var i=0; i < it.parents.length; i++) {
                $('a', it.parents[i].b.el.el)
                    .removeClass('contentSectionTitle3_a');
                    //.addClass('contentSectionTitle1_a');
                $('span', it.parents[i].b.el.el)
                    .removeClass('contentSectionTitle3_span');
                    //.addClass('contentSectionTitle1_span');
            }
            $('a', it.b.el.el)
                .removeClass('contentSectionTitle3_a');
                //.addClass('contentSectionTitle1_a');
            $('span', it.b.el.el)
                .removeClass('contentSectionTitle3_span');
                //.addClass('contentSectionTitle1_span');

            if (t.currentEl_cssItem) $(event.currentTarget).css(t.currentEl_cssItem);
            if (!it) return false;

            for (var elIdx in t.timeout_hideSubMenu) {
                if (typeof elIdx === 'number') {
                    var
                    it2 = t.items[elIdx],
                    to = t.timeout_hideSubMenu[elIdx];
                    clearTimeout(to);
                }
            }
            t.timeout_hideSubMenu = {};

            for (var elIdx in t.timeout_showSubMenu) {
                if (typeof elIdx === 'number') {
                    var
                    it2 = t.items[elIdx],
                    to = t.timeout_showSubMenu[elIdx];
                    clearTimeout(to);
                }
            }
            t.timeout_showSubMenu = {};

            t.prevEl = t.currentEl;
            if (t.currentEl_cssItem) $(t.prevEl).css(t.currentEl_cssItem);
            t.currentEl = event.currentTarget;

            t.shownChildren[event.currentTarget.id] = event.currentTarget;

            if (t.useDelayedShowingAndHiding) {
                for (var id in t.timeout_showSubMenu) {
                    clearTimeout (t.timeout_showSubMenu[id]);
                };

                if (t.debugMe) na.m.log (1120, 'naVividMenu.createVividButton() : bind("mouseover") : showing sub-menu for "'+it.label+'" after '+t.sensitivitySpeedOpen+'ms.', false);
                t.timeout_showSubMenu[it.idx] = setTimeout(function(t,idx,evt){

                    if (t.debugMe) na.m.log (1120, 'naVividMenu.createVividButton() : bind("mouseover") : showing sub-menu for "'+it.label+'".', false);
                    t.onmouseover (evt);
                    delete t.timeout_showSubMenu[idx];
                }, t.sensitivitySpeedOpen, t, it.idx, $.extend({},event.originalEvent));
            } else {
                if (t.debugMe) na.m.log (1120, 'naVividMenu.createVividButton() : bind("mouseover") : showing sub-menu for "'+it.label+'".', false);

                t.onmouseover ($.extend({},event.originalEvent));
            }
        });


        $(it.b.el.el).bind('mouseleave', function(event) {
            event.stopPropagation();

            var
            idx = parseInt(event.currentTarget.id.replace(/.*__/,'')),
            id = event.currentTarget.id.replace('__'+idx,''),
            t = na.site.components.menus['#'+id],
            it = t.items[idx];
            //if (it.label=='Background'||it.label=='Landscape') debugger;

            if (t.currentEl_cssItem) $(event.currentTarget).css(t.currentEl_cssItem);
            if (!it) return false;

            if (it.level > 1 ) {
                if (t.useDelayedShowingAndHiding) {
                    if (t.debugMe) na.m.log (1120, 'naVividMenu.createVividButton() : bind("mouseover") : hiding sub-menu for "'+t.prevEl.it.label+'" after '+t.sensitivitySpeed+'ms.', false);

                    var evt = event;
                    if (t.timeout_hideSubMenu[it.idx]) clearTimeout (t.timeout_hideSubMenu[it.idx]);
                    t.timeout_hideSubMenu[it.idx] = setTimeout(function(t,idx,evt){
                        t.onmouseout(evt);
                        delete t.timeout_hideSubMenu[idx]
                    }, t.sensitivitySpeedClose, t, it.idx, event);

                } else {
                    t.onmouseout(event);

                    delete t.timeout_hideSubMenu[it.idx];
                }
            }
        });

        $(it.b.el.el).bind('click', function() {
            t.onclick(it);
        });

    }

    showMenuItem (t, it, dim /* dimensions */, evt /* event */, useFading) {
        var
        offsetX = 0,
        offsetY = 0,
        tel_bcr = t.el.getBoundingClientRect(),
        itp = null,
        i = it.levelIdx;
        if (typeof useFading=='undefined') useFading = true;
        if (!it.b) {
            t.createVividButton (t, it.idx, it.li);
            if (!t.prevDisplayedEl) t.prevDisplayedEl = it.b.el;
        }

        if (it.parents && it.parents[0]) {
            var
            parentItem = $('#'+t.el.id+'__'+it.parents[0].idx)[0];
            if (parentItem) {
                var p_bcr = parentItem.getBoundingClientRect();
            } else {
                var p_bcr = {
                    left : $(t.el).offset().left,
                    top : $(t.el).offset().top,
                    width : $(it.b.el).width(),
                    height : $(it.b.el).height()
                }
            };
            var
            itp_idx = it.parents[0].idx,
            itp = t.items[itp_idx],
            offsetY =
                t.type=='vertical'
                ? dim.verDirection == 'south'
                    ? na.d.g.margin + ( ( $(parentItem).parent().height() - $(parentItem).height() ) / 2 )
                    : na.d.g.margin - ( ( $(parentItem).parent().height() - $(parentItem).height() ) / 2 )
                : dim.verDirection == 'south'
                    ? /*p_bcr.top + */na.d.g.margin - ( ( $(parentItem).parent().height() - $(parentItem).height() ) / 2 )
                    : /*p_bcr.top - */ na.d.g.margin - ( ( $(parentItem).parent().height() - $(parentItem).height() ) / 2 ),
            numKids = Object.keys(t.children[itp_idx]).length,
            sqrtNumKids = Math.ceil(Math.sqrt(numKids)),
            h = dim.space2bottom > dim.space2top ? dim.space2bottom : dim.space2top,
            w = dim.space2left > dim.space2right ? dim.space2left : dim.space2right;
            if (!$('div#'+t.el.id+'__panel__'+itp_idx)[0]) {
                var container = document.createElement('div');
                container.id = t.el.id+'__panel__'+itp_idx;
                container.className = 'vividMenu_subMenuPanel';
                $('body').append(container);
            } else {
                var container = $('div#'+t.el.id+'__panel__'+itp_idx)[0];
            };

            if (t.useDelayedShowingAndHiding) {
                for (var id in t.timeout_showSubMenu) {
                    clearTimeout (t.timeout_showSubMenu[id]);
                };
                if (t.timeout_onmouseout[parseInt(it.idx)]) clearTimeout (t.timeout_onmouseout[parseInt(it.idx)]);
                t.timeout_onmouseout[parseInt(it.idx)] = setTimeout (function(t, evt) {
                    var toHide = t.mustHide (t, t.currentEl.it, evt);
                    if (t.currentEl.it.level > 1 || $(t.el).is('.noInitialShowing')) {
                        na.m.log (1120, 'naVividMenu.onmouseout() : hiding sub-menu for "'+toHide.prevEl[0].it.label+'"', false);
                        if (t.debugMe) debugger;
                        if (
                            toHide.currentEl.length>0
                            || toHide.prevEl.length>0
                        ) {
                            t.onmouseout_do(evt, toHide);
                        }
                    }
                }, t.sensitivitySpeedClose, t, evt);
            }

            //if (!it.b.el.el.parentNode || it.b.el.el.parentNode.id!==container.id) {
                $(it.b.el.el)
                    .css ({display : 'block', opacity:1})
                    .appendTo(container);
                //if (t.debugMe) na.m.log (1120, 'naVividMenu.showMenuItem() : placing "'+it.label+'"'+/*', parent.id=#'+it.b.el.el.parentNode.id+*/' into #'+container.id, false);
                $(container).css({ width : 'fit-content', height: 'fit-content' });
                $(container).css({ width : 'auto', height: 'auto' });
            //}


            var
            numRows = (h/(($(it.b.el.el).height() + na.d.g.margin))  ),
            numColumns = Math.sqrt(numKids);
            if (Math.floor(numColumns)===0) numColumns = 1;
            //var numRows = Math.ceil(numKids/numColumns);
            //console.log ('t333', numKids, numColumns, numRows);

            $(it.b.el.el).css({display:'inline-block'});
            $(container).css ({ left : 10, width : ($(it.b.el.el).outerWidth() * Math.floor(numColumns)) +  (na.d.g.margin * Math.floor(numColumns)), height : 'auto' });
            var bcr = container.getBoundingClientRect();
        } else {
            // first level of menu
            var
            container = $('#menu__'+t.el.id)[0];

            if ($(t.el).is('.noInitialShowing')) {
                numRows = $('#'+t.el.id+' > .vividMenu_mainUL > li').length;
                numColumns = 1;
            }
            if (it.b.el.el.parentNode.id!==container.id) {
                //if (t.debugMe) na.m.log (1120, 'naVividMenu.showMenuItem() : placing "'+it.label+'"'/*+', parent.id=#'+it.b.el.el.parentNode.id*/+' into #'+container.id, false);
                $(it.b.el.el).css({display:'inline-block',position:'absolute'}).appendTo(container);
            }
        };
        var
        position = (
            it.b.el.el.parentNode===document.body
            ? 'relative'
            : 'relative'
        );

        /*
        if (t.useFading && useFading) {
        //if (false) {
            $(it.b.el.el).css ({
                position : position,
                opacity : 1,
                display : 'none',
                //float : 'left',
                zIndex : t.el.style.zIndex + (it.level * 5)
            });
            $(it.b.el.el).stop(true,true).fadeIn(t.fadingSpeed);
        } else {*/
            $(it.b.el.el).css ({
                opacity : 1,
                display : 'inline-block',
                //float : 'left',
                position : position,
                zIndex : t.el.style.zIndex + (it.level * 5)
            });
        //}

        return {
            it : it,
            dim : dim,
            numKids : numKids,
            numColumns : numColumns
        };
    }

    getElementsLabels (t, a) {
        var r = {};
        for (var i=0; i<a.length; i++) {
            var it = a[i].it;
            r[it.idx] = it.label;
        }
        return r;
    }
    getIndexesLabels (t, a) {
        var r = {};
        for (var i=0; i<a.length; i++) {
            var it = t.items[parseInt(a[i])];
            r[a[i]] = it.label;
        }
        return r;
    }

    mustHide (t, it, evt) {
        var
        toBeHidden = [], // stores HTML elements (vividButtons and panel elements)
        items_currentEl = [],//$.extend( [], t.items_currentEl ),
        items_prevEl = [],//$.extend( [], t.items_prevEl ), // stores the idx part of t.items[idx]
        items_final = [];//$.extend( [], t.items_final ); // stores the idx part of t.items[idx]

        if (t.prevEl && t.prevEl.it.parents && t.prevEl.it.parents.length)
        for (var i=0; i < t.prevEl.it.parents.length; i++) {
            var parentIdx = parseInt(t.prevEl.it.parents[i].idx);
            items_prevEl.push(parentIdx);

            var
            parentIt = t.items[parentIdx],
            parentKids = parentIt ? t.children[parentIt.idx] : t.children[t.el.id];

            for (var kidsIdx in parentKids) items_prevEl.push(parseInt(kidsIdx));
        }

        if (t.prevEl) {
            items_prevEl.push(t.prevEl);
            var
            idx = parseInt(t.prevEl.it.idx),
            panelID = '#'+t.el.id+'__panel__'+idx,
            panel = $(panelID)[0];
            //if (panel) items_prevEl.push (panel);
        }

        if (t.currentEl && t.currentEl.it.parents && t.currentEl.it.parents.length)
        for (var i=0; i < t.currentEl.it.parents.length; i++) {
            var parentIdx = parseInt(t.currentEl.it.parents[i].idx);
            items_currentEl.push(parentIdx);

            var
            parentIt = t.items[parentIdx],
            parentKids = parentIt ? t.children[parentIt.idx] : t.children[t.el.id];

            for (var kidsIdx in parentKids) items_currentEl.push(kidsIdx);
        }

        if (t.currentEl) {
            items_currentEl.push(t.currentEl);
            var
            idx = parseInt(t.currentEl.it.idx),
            panelID = '#'+t.el.id+'__panel__'+idx,
            panel = $(panelID)[0];
        }

        var items_final = $.extend([], items_prevEl);
        for (var i=0; i < items_currentEl.length; i++) {
            items_final = arrayRemove (items_final, items_currentEl[i]);
        }

        if (t.debugMe) debugger;
        return { // from naVividMenu.mustHide()
            currentEl : [ t.currentEl ],
            prevEl : [ t.prevEl ]
        };
    }

    cancelHidings (t) {
        for (var idx in t.timeout_hideSubMenu) {
            clearTimeout(t.timeout_hideSubMenu[idx]);
            delete t.timeout_hideSubMenu[idx];
        }
        t.timeout_hideSubMenu = {};

        for (var bp_id in t.timeout_hideAll) {
            if (!t.timeout_hideAll[bp_id]) {
                t.timeout_hideAll[bp_id] = [];
            } else {
                for (var i=0; i<t.timeout_hideAll[bp_id].length; i++) {
                    clearTimeout(t.timeout_hideAll[bp_id][i]);
                };
                t.timeout_hideAll[bp_id] = [];
            };
        }

        /* JUST DON'T!
        for (var it_id in t.timeout_onmouseout) {
            clearTimeout (t.timeout_onmouseout[it_id]);
            delete t.timeout_onmouseout[it_id];
        }
        */
    }

    cancelShowings (t) {
        for (var idx in t.timeout_showPanel) {
            clearTimeout(t.timeout_showPanel[idx]);
            delete t.timeout_showPanel[idx];
        }

        for (var idx in t.timeout_showSubMenu) {
            clearTimeout(t.timeout_showSubMenu[idx]);
            delete t.timeout_showSubMenu[idx];
        }

        for (var idx in t.timeout_onmouseover) {
            clearTimeout(t.timeout_onmouseover[idx]);
            delete t.timeout_onmouseover[idx];
        }
    }

    showPanel (t, evt, panel, it, pit, dim /* dimensions */, numColumns, numRows, offsetX, offsetY) {
        var
        dim = t.getDimensions(t, pit.b.el, false),
        dim2 = t.getDimensions(t, pit.b.el, false),
        i = pit.levelIdx;
        panel.it = pit;
        panel.opacity = parseFloat($(panel).css('opacity'));
        t.cancelHidings(t);

        $(panel).css( { height : 'auto' });
        $(panel).bind('mouseover', function (event) {
            t.cancelHidings(t);
            if (!t.timeout_hideAll[t.el.id])
                t.timeout_hideAll[t.el.id] = [];

            /*
            // cancel showings too!
            for (var elIdx in t.timeout_showSubMenu) {
                if (typeof elIdx === 'number') {
                    var
                    it2 = t.items[elIdx],
                    to = t.timeout_showSubMenu[elIdx];
                    clearTimeout(to);
                }
            }
            t.timeout_showSubMenu = {};
            */

            //debugger;
            $('#'+t.el.id+'__backPanel').remove();
            t.showBackPanel(t, t.currentEl);

            var panel = event.currentTarget;
            if (!t.panelsShown[panel.id]) t.panelsShown[panel.id] = {
                panel : panel,
                hideAll : null,
                hideMe : null,
                hideKids : null
            };


        });
        $(panel).bind('mouseout', function (event) {
            var
            panel = event.currentTarget,
            elIdx = parseInt(panel.id.replace(/.*__/,''));//,
            //panelKids = t.children[elIdx],
            //firstPanelKidIdx = parseInt(Object.keys(t.children[elIdx])[0]);

            if (!t.panelsShown[panel.id]) t.panelsShown[panel.id] = {};
            if (t.panelsShown[panel.id].hideAll) clearTimeout (t.panelsShown[panel.id].hideAll);
        });
        //$('.vividMenu_item', panel).css({display:'inline-block', float:'left'});
        var
        itsKids = t.children[pit.idx],
        container = panel,
        bcr = container.getBoundingClientRect(),
        numRows = itsKids[0].numRows,
        numKids = itsKids.length,
        started = false;

        while (!started || bcr.left + bcr.width > $(window).width()) {
            started = true;
            if (it.parents && it.parents[0]) {
                var
                parentItem = $('#'+t.el.id+'__'+it.parents[0].idx)[0];
            }

            //$(panel).css({ top : p_bcr.top - $(panel).height() });

            if (parentItem) {
                var p_bcr = parentItem.getBoundingClientRect();
            } else {
                var p_bcr = {
                    left : $(t.el).offset().left,
                    top : $(t.el).offset().top,
                    width : $(it.b.el).width(),
                    height : $(it.b.el).height()
                }
            };

            var
            background1 = 'rgba('+(100+Math.random()*150)+','+(100+Math.random()*150)+','+(100+Math.random()*150)+', 0.55)',
            background2 = 'rgba('+(100+Math.random()*150)+','+(100+Math.random()*150)+','+(100+Math.random()*150)+', 0.4)',
            background2a1 = ''+Math.ceil(Math.random()*255)+','+Math.ceil(Math.random()*255)+','+Math.ceil(Math.random()*255),
            background2a = 'rgba('+background2a1+',0.55)',
            background2b = 'rgba('+background2a1+',0.7)',
            background3 = 'rgba(0,0,0,0.0001)',
            border = '2px solid '+background2b,
            numVer = Math.floor(Object.keys(t.children[pit.idx]).length/numColumns),
            c = t.children[pit.idx],
            k = Object.keys(c),
            k = k.sort(),
            x1 = c[parseInt(k[0])],
            x2 = c[parseInt(k[k.length-1])],
            cssPanelWidth =
                t.el.parentNode!==document.body
                ? ($(x1.b.el.el).width() * (it.numColumns)) + na.d.g.margin + (na.d.g.margin*it.numColumns)
                : ($(x1.b.el.el).width() * (it.numColumns)) + na.d.g.margin + (na.d.g.margin*it.numColumns);

            //$(panel).css({width:cssPanelWidth, height : 'auto'}).delay(50);
            var
            x1_bcr = x1.b.el.el.getBoundingClientRect(),
            x2_bcr = x2.b.el.el.getBoundingClientRect(),
            tel_bcr = t.el.getBoundingClientRect(),
            itp_bcr = pit.b.el.el.getBoundingClientRect(),
            offsetX =
                it.parents && it.parents[0]
                ? dim.horDirection=='east'
                    ? p_bcr.left - (t.el.parentNode===document.body?tel_bcr.left:0) - (na.d.g.margin * ((it.column-1)))
                    : p_bcr.left - (t.el.parentNode===document.body?tel_bcr.left:0) - (na.d.g.margin * ((it.column-1)))
                : (
                    t.el.parentNode===document.body
                    || t.el.parentNode.id === t.el.parentNode.id+'_containerDiv'
                    ? na.d.g.margin * 2 * it.levelIdx
                    : 0//tel_bcr.left
                ),
            left = (
                (
                    t.type=='vertical'
                    ? it.level === 1
                        ? 0
                        : $(it.b.el.el).outerWidth() * 0.7
                    : it.level === 1
                        ? 0
                        : it.level===2
                            ? 0
                            : ( $(it.b.el.el).outerWidth() * 0.7 * it.column ) + (2 * it.column * na.d.g.margin )
                )
            ),
            x1t = x1_bcr.top - tel_bcr.top,
            x1l = x1_bcr.left - tel_bcr.left,
            x2t = x2_bcr.top - tel_bcr.top,
            x2l = x2_bcr.left - tel_bcr.left;
            //if (pit.label=='Background') debugger;
            //if (pit.label=='Landscape') debugger;
            var
            cssPanel = {
                position : 'absolute',
                display : 'block',
                //border : border,
                //borderRadius : 8,
                //background : background2a,
                boxShadow : 'inset 0px 0px 3px 2px rgba(0,0,0,0.8), 4px 4px 2px 2px rgba(0,0,0,0.7)',
                width : 'auto',
                height : 'auto',
                //padding : 5,
                zIndex : it.b.el.el.style.zIndex-1,
                opacity : 0.001

            },
            cssPanelPseudo_before = {
                display : 'block',
                zIndex : it.b.el.el.style.zIndex-2,
                position : 'absolute',
                top : 0,
                left : 0,
                width : '100%',
                height : '100%',
                background : "url('/siteMedia/backgrounds/tiled/orange/467781133_4f4354223e.jpg')",
                opacity : 0.7
            },
            cssItem = {
                //border : border,
                //boxShadow : 'inset 2px 2px 4px 4px rgba(255,255,255,0.4), 2px 2px 1px 1px rgba(0,0,0,0.55)'
            },
            panelID = it.parents ? t.el.id+'__panel__'+it.parents[0].idx : t.el.id+'__panel__'+t.el.id,
            itID = t.el.id+'__'+pit.idx,
            html = '<div id="'+panelID+'" class="vividMenu_subMenuPanel" lvl="'+it.level+'">&nbsp;</div>';
            /*
            if (t.percentageFor_rainbowPanels===0) {
                cssPanel.borderRadius = 0;
                cssPanel.background = background3;
                cssPanel.border = '0px solid transparent';
                cssPanel.boxShadow = 'none';
            };
            */
            t.currentEl_cssItem = cssItem;

            var
            itsKids = t.children[pit.idx],
            kids = [];
            for (var kidIdx=0; kidIdx<itsKids.length; itsKids++) {
                var itKid = itsKids[kidIdx];
                kids.push (itKid.b.el.el);
            }
            //if (t.percentageFor_rainbowPanels>0) $(kids).css(cssItem);
            //t.currentEl_cssItem.border = '2px solid rgba(255,255,255,0.7)';

            $(panel).css(cssPanel).addClass('shown');
            $(':before', panel).css(cssPanelPseudo_before);
            var bcr = container.getBoundingClientRect();

            if (bcr.left + bcr.width > $(window).width()) {
                numRows++;
                numColumns = Math.floor(numKids/numRows);
                if (numColumns === 0) numColumns = 1;
                $(container).css ({ width : ($(it.b.el.el).outerWidth() * Math.floor(numColumns)) +  (na.d.g.margin * Math.floor(numColumns)), height : 'auto' });
                var bcr = container.getBoundingClientRect();
            }
            for (var kidIdx=0; kidIdx<itsKids.length; itsKids++) {
                var itKid = itsKids[kidIdx];
                itKid.numRows = numRows;
                itKid.numColumns = numColumns;
            }

            var idx = (
                it.parents && it.parents[0]
                ? it.parents[0].idx
                : it.idx
            );
        }
        if (t.useFading) {
            $(panel).css(cssPanel);
            cssPanel = {
                left : (
                    it.level > 2
                    ? dim.horDirection=='east'
                        ? itp_bcr.left
                            + ($(x2.b.el.el).outerWidth()*0.85)
                        : itp_bcr.left
                            - (2 * na.d.g.margin)

                    : dim.horDirection=='east'
                        ? x2l + (2 * na.d.g.margin)
                        : x1l - (2 * na.d.g.margin)

                ),
                top : (
                    it.level > 2
                    ? dim.verDirection=='north'
                        ? p_bcr.top - $(panel).height() - 5 - (1.5 * na.d.g.margin)
                        : p_bcr.top + 20 + (1.5 * na.d.g.margin)
                    : dim.verDirection=='north'
                        ? p_bcr.top - $(panel).height() - 305 - (1.5 * na.d.g.margin)
                        : p_bcr.top + 20 + (1.5 * na.d.g.margin)
                )
            };
            cssPanel.maxWidth = 'calc(90% - '+cssPanel.left+'px)';
            $(panel).css(cssPanel);
            cssPanel = {
                left : (
                    it.level > 2
                    ? dim.horDirection=='east'
                        ? itp_bcr.left
                            + ($(x2.b.el.el).outerWidth()*0.85)
                        : itp_bcr.left
                            - (2 * na.d.g.margin)

                    : dim.horDirection=='east'
                        ? x2l + (2 * na.d.g.margin)
                        : x1l - (2 * na.d.g.margin)

                ),
                top : (
                    it.level > 2
                    ? dim.verDirection=='north'
                        ? p_bcr.top - $(panel).height() - 5 - (1.5 * na.d.g.margin)
                        : p_bcr.top + 20 + (1.5 * na.d.g.margin)
                    : dim.verDirection=='north'
                        ? p_bcr.top - $(panel).height() - 305 - (1.5 * na.d.g.margin)
                        : p_bcr.top + 20 + (1.5 * na.d.g.margin)
                )
            };
            cssPanel.maxWidth = $(panel).width()/3.75;
            var mw = 0;
            $('div.vividButton', panel).each(function(idx,menuItem) {
                if (mw<$(menuItem).width()) mw = $(menuItem).width();
            });
            mw += parseInt($(panel).css('paddingLeft')) + parseInt($(panel).css('paddingRight'));
            if (cssPanel.maxWidth<mw) cssPanel.maxWidth = mw;
            $(panel).css(cssPanel);
            cssPanel.top = (
                    it.level > 2
                    ? dim.verDirection=='north'
                        ? p_bcr.top - $(panel).height() - 5 - (1.5 * na.d.g.margin)
                        : p_bcr.top + 20 + (1.5 * na.d.g.margin)
                    : dim.verDirection=='north'
                        ? p_bcr.top - $(panel).height()*3 - 5 - (1.5 * na.d.g.margin)
                        : p_bcr.top + 20 + (1.5 * na.d.g.margin)
                );
            //alert (JSON.stringify(cssPanel));
            $(panel).css(cssPanel).css({opacity:panel.opacity}).fadeIn(t.fadingSpeed);
        } else {
            $(panel).css(cssPanel);
            cssPanel = {
                left : (
                    it.level > 2
                    ? dim.horDirection=='east'
                        ? itp_bcr.left
                            + ($(x2.b.el.el).outerWidth()*0.85)
                        : itp_bcr.left
                            - (2 * na.d.g.margin)

                    : dim.horDirection=='east'
                        ? x2l + (2 * na.d.g.margin)
                        : x1l - (2 * na.d.g.margin)

                ),
                top : (
                    it.level > 2
                    ? dim.verDirection=='north'
                        ? p_bcr.top - $(panel).height() - 5 - (1.5 * na.d.g.margin)
                        : p_bcr.top + 20 + (1.5 * na.d.g.margin)
                    : dim.verDirection=='north'
                        ? p_bcr.top - $(panel).height() - 305 - (1.5 * na.d.g.margin)
                        : p_bcr.top + 20 + (1.5 * na.d.g.margin)
                )
            };
            cssPanel.maxWidth = 'calc(90% - '+cssPanel.left+'px)';
            $(panel).css(cssPanel);
            cssPanel = {
                left : (
                    it.level > 2
                    ? dim.horDirection=='east'
                        ? itp_bcr.left
                            + ($(x2.b.el.el).outerWidth()*0.85)
                        : itp_bcr.left
                            - (2 * na.d.g.margin)

                    : dim.horDirection=='east'
                        ? x2l + (2 * na.d.g.margin)
                        : x1l - (2 * na.d.g.margin)

                ),
                top : (
                    it.level > 2
                    ? dim.verDirection=='north'
                        ? p_bcr.top - $(panel).height() - 5 - (1.5 * na.d.g.margin)
                        : p_bcr.top + 20 + (1.5 * na.d.g.margin)
                    : dim.verDirection=='north'
                        ? p_bcr.top - $(panel).height() - 5 - (1.5 * na.d.g.margin)
                        : p_bcr.top + 20 + (1.5 * na.d.g.margin)
                )
            };
            cssPanel.maxWidth = $(panel).width()/3.75;
            var mw = 0;
            $('div.vividButton', panel).each(function(idx,menuItem) {
                if (mw<$(menuItem).width()) mw = $(menuItem).width();
            });
            mw += parseInt($(panel).css('paddingLeft')) + parseInt($(panel).css('paddingRight'));
            if (cssPanel.maxWidth<mw) cssPanel.maxWidth = mw;
            $(panel).css(cssPanel);
            cssPanel.top = (
                    it.level > 2
                    ? dim.verDirection=='north'
                        ? p_bcr.top - $(panel).height() - 5 - (1.5 * na.d.g.margin)
                        : p_bcr.top + 20 + (1.5 * na.d.g.margin)
                    : dim.verDirection=='north'
                        ? p_bcr.top - $(panel).height()*3 - 5 - (1.5 * na.d.g.margin)
                        : p_bcr.top + 20 + (1.5 * na.d.g.margin)
                );
            //alert (JSON.stringify(cssPanel));
            $(panel).css(cssPanel).css({opacity:panel.opacity}).fadeIn(t.fadingSpeed);
        }
    }

    showBackPanel (t, el) {
        // show backpanel
        var html = '<div id="'+t.el.id+'__backPanel" class="vividMenu_backPanel">&nbsp;</div>';
        var bp = $('#'+t.el.id+'__backPanel');
        if (!bp[0]) {
            $('body').append(html);
            var bp = $('#'+t.el.id+'__backPanel');

            $(bp).css({
                position : 'fixed',
                left : 0,
                top : 0,
                width : window.innerWidth,
                height : window.innerHeight,
                zIndex : parseFloat($('#'+t.el.id).css('zIndex')) - 20 ,
                background : 'rgba(0,0,0,0.0001)'
            });
            $(bp).bind('mouseover', function (event) {
                var bp = event.currentTarget;

                t.cancelHidings(t);
                if (!t.timeout_hideAll[t.el.id])
                    t.timeout_hideAll[t.el.id] = [];
                t.timeout_hideAll[t.el.id][t.timeout_hideAll[t.el.id].length] =
                    setTimeout (function (t,bp) {
                        t.hideAll(t,bp);
                    }, t.sensitivitySpeedClose, t, bp);
            });
        }

    }

    hideAll (t, bp, delay) {
        if (!delay) delay = t.sensitivitySpeedClose;
        if (!bp || !(typeof bp.id=='string')) return false;
        if (!t.timeout_hideAll[bp.id]) {
            t.timeout_hideAll[bp.id] = [];
        } else {
            for (var i=0; i<t.timeout_hideAll[bp.id].length; i++) {
                clearTimeout(t.timeout_hideAll[bp.id][i]);
            };
            t.timeout_hideAll[bp.id] = [];
        };
        var to = t.timeout_hideAll[bp.id];
        t.timeout_hideAll[bp.id].push( setTimeout(function (t, bp) {
            var hiding = [];
            $('.vividMenu_subMenuPanel').each(function(idx,panel) {
                if (panel) hiding.push(panel);
            });

            $('.vividMenu_backPanel').each(function(idx,el){
                hiding.push(el);
            });

            if (t.useFading) {
                $(hiding).stop(true,true).fadeOut(t.fadingSpeed);
                $('.vividMenu_subMenuPanel').fadeOut(t.fadingSpeed);
            } else {
                $(hiding).css({display:'none'});
            }
            $('.vividMenu_backPanel').remove();
            t.shownChildren = {};
        }, delay, t, bp));
        var ha = t.timeout_hideAll[bp.id];
        return ha[ha.length-1];
    }



    getDimensions (t, el, restrict) {
        var
        t = this,
        vbCheckerID = t.el.id+'_vbChecker',
        bws = na.m.borderWidths($('#'+vbCheckerID)[0]),
        el = el.el ? el.el : el,
        e = el,//.it && el.it.parentDiv ? el.it.parentDiv : el,
        ebcr = e.getBoundingClientRect(),
        pbcr = (
            el.it && el.it.parentDiv
            ? el.it.parentDiv.getBoundingClientRect()
            : t.el.getBoundingClientRect()
        ),
        dim = { // dimensions
            space2right :
                $(e).attr('controlledBy')=='na.desktop'
                ? $(e).width()
                : Math.abs(window.innerWidth - ebcr.left - bws.left - bws.right - 20),
            space2left : Math.abs(ebcr.left),
            space2top : Math.abs(ebcr.top - bws.top),
            space2bottom : Math.abs(window.innerHeight - ebcr.top - $(e).height() - bws.top - bws.bottom),
        };
        if (el.it) {
            if (el.it.numColumns) {
                dim.space2right -= (na.d.g.margin * el.it.numColumns);
                dim.space2left -= ($(e).width()*el.it.numColumns);
            };
            if (el.it.level > 2) {
                dim.space2right -= ($(e).outerWidth()*0.8);
                dim.space2left += ($(e).outerWidth()*0.8);
            };
        } else {
            dim.space2right -= na.d.g.margin;
            dim.space2left -= $(e).width();
        };
        dim.bws = bws;

        var
        avoid1 = $(el).attr('avoid'),
        oEl = $(el).offset(),
        oElH = el.outerHeight,
        oElW = el.outerWidth,
        closest = {
            left : null,
            right : null,
            top : null,
            bottom : null
        },
        closestGap = {
            left : null,
            right : null,
            top : null,
            bottom : null
        };
        if (typeof avoid1 == 'string' && avoid1 !== '') {
            var avoid2 = JSON.parse(avoid1);
            for (var i=0; i < avoid2.length; i++) {
                var
                avoid3 = avoid2[i],
                avoidEl = $(avoid3),
                o = avoidEl.offset(),
                oh = avoidEl.height(),
                ow = avoidEl.outerWidth();

                // TODO : (LOW-PRIORITY) evaluate y-axis as well when evaluating x-axis, and vice-versa.


                // evaluate dim.space2left
                if (o.left < oEl.left) {
                    if (!closest.left) {
                        closest.left = avoidEl;
                        closestGap.left = oEl.left - ( $(closest.left).offset().left + closest.left.outerWidth() );
                    } else {
                        var
                        gapClosest = oEl.left - ( $(closest.left).offset().left + closest.left.outerWidth() ),
                        gapCurrent = oEl.left - ( o.left + ow );
                        if (gapCurrent < gapClosest) {
                            closest.left = avoidEl;
                            closestGap.left = gapCurrent;
                        }
                    }
                }

                // evaluate dim.space2right
                if (oEl.right < o.right) {
                    if (!closest.right) {
                        closest.right = avoidEl;
                        closestGap.right = $(closest.right).offset().right - (oEl.right + oElW);

                    } else {
                        var
                        gapClosest = $(closest.right).offset().right - (oEl.right + oElW),
                        gapCurrent = o.right - (oEl.right + oElW);
                        if (gapCurrent < gapClosest) {
                            closest.right = avoidEl;
                            closestGap.right = gapCurrent;
                        }
                    }
                }
            } // for i < avoid2.length

            if (closestGap.left) dim.space2left = closestGap.left;
            if (closestGap.right) dim.space2right = closestGap.right;
            if (closestGap.top) dim.space2top = closestGap.top;
            if (closestGap.bottom) dim.space2bottom = closestGap.bottom;
        }

        if (typeof el.idx == 'number') {
            var
            it = t.items[el.idx],
            parentLI = $('#'+t.el.idx+'__li__'+el.idx).parents('ul > li')[0];

            if (parentLI) {
                var
                parentIdx = parseInt(parentLI.id.replace(/.*__/,'')),
                itp = t.items[parentIdx];
            }
        }

        dim.horDirection = dim.space2right > dim.space2left ? 'east' : 'west';
        dim.verDirection = dim.space2top > dim.space2bottom ? 'north' : 'south';

        if (restrict) {
            if (
                t.type === 'horizontal'
                && el !== t.el
                || (el.it
                    && el.it.level
                    && el.it.level === 2
                )
            ) dim.horDirection = null;
            if (el === t.el && t.type==='vertical') dim.horDirection = null;
            if (el === t.el && t.type==='horizontal') dim.verDirection = null;
        }

        return dim;
    }


    onmouseover (event) {
        var
        t = this,
        evt = event,
        elDiv   = event.currentTarget,
        elIdMorphed = elDiv.id.replace('siteMenu__','siteMenu__li__'),
        el = $('#'+elIdMorphed)[0],
        myKids = t.children[el.parent?el.parent.el.id:elDiv.it.idx];

        //debugger;

        if (myKids && myKids.sort)
            myKids = myKids.sort(function(a,b){ return a.label - b.label; });

        if (!el.it) {
            na.m.log (1, '.../logic.vividUserInterface/v6.y.z/2D/vividMenu-5.y.z--behavior-rainbowPanels-1.1.0.js::onMouseOver (event) : #'+el.id+' RETURNED FALSE. RESULT : MELTDOWN! :P');
            return false;
        }

        for (var panelID in t.panelsShown) {
            var p = t.panelsShown[panelID];
            if  (p.hideAll) clearTimeout(p.hideAll);
            delete t.panelsShown[panelID];
        }

        if (!t.timeout_onmouseover) t.timeout_onmouseover = {};
        if (t.timeout_onmouseover[el.it.idx]) clearTimeout (t.timeout_onmouseover[el.it.idx]);
        t.cancelShowings(t);
        t.cancelHidings(t);
        t.timeout_onmouseover[el.it.idx] = setTimeout(function(t, el, evt) {
            //if (t.debugMe) na.m.log (1120, 'naVividMenu.onmouseover() : showing sub-menu for "'+el.it.label+'"', false);

            t.prevDisplayedEl = t.currentEl.el || t.currentEl;
            t.currentDisplayedEl = t.currentEl = evt.currentTarget;
            t.currentDisplayedEl_negativeOffsetY = null;

            var
            r = null,
            dim = t.getDimensions(t, el, false);
            t.prevDisplayedEl = null;

            var
            pit = t.currentDisplayedEl,
            pidx = pit.it?pit.it.idx:pit.idx,
            it2 = t.items[pidx],
            panelID = t.el.id+'__panel__'+pidx;
            //$('#'+panelID).css({display:'block'});


            var
            dbgStrPart1 = 'naVividMenu.onmouseover() : for sub-menu "'+pit.it.label+'"; showing menu items ',
            dbgStrPart2 = '';
            t.columnDisplayed = 1;
            if (myKids && myKids.length > 0)
            for (var i=0; i<myKids.length; i++) {
                var
                it = myKids[i];
                if (dbgStrPart2!=='') dbgStrPart2+=', ';
                dbgStrPart2 += '"'+it.label+'"';

                var r = t.showMenuItem (t, it, dim, evt);
                t.prevDisplayedEl = it.b.el.el;
            }
            t.prevDisplayedEl = t.currentEl.el;
            na.m.log (1120, dbgStrPart1+dbgStrPart2);

            var
            pit = t.currentEl;
            if (pit && t.children[pidx]) {
                var
                c = t.children[pidx],
                k = Object.keys(c),
                x1 = t.children[pidx][parseInt(k[0])],
                x2 = t.children[pidx][parseInt(k[k.length-1])];

                if (r && r.it.idx === x2.idx) {
                    var
                    it2 = t.items[pidx],
                    panelID = t.el.id+'__panel__'+pidx,
                    html = '<div id="'+panelID+'" class="vividMenu_subMenuPanel">&nbsp;</div>',
                    panel = $('#'+panelID);
                    if (!panel[0]) t.childPanels[it2.idx] = $(t.el).append(html);
                    panel = $('#'+panelID)[0];
                    panel.it = c[0].parent.it;

                    t.showPanel (
                        t, event, panel, r.it, c[0].parent.it, r.dim, r.numColumns, (r.numKids / r.numColumns),
                        $(x1.b.el.el).offset().left - $(t.el).offset().left,
                        $(x1.b.el.el).offset().top - $(t.el).offset().top
                    );
                }
            }

            //t.showBackPanel(t, el);
        }, 100, t, el, $.extend({},event));
    }

    onmouseout (event) {
        /*
        var toHide = this.mustHide (this, this.currentEl.it, event);
        this.onmouseout_do(event, toHide);
        return true;

        */
        var t = this, el = event.currentTarget;
        if (!t.timeout_onmouseout) t.timeout_onmouseout = {};

        //t.cancelShowings(t);

        if (t.timeout_onmouseout[parseInt(el.it.idx)]) clearTimeout (t.timeout_onmouseout[parseInt(el.it.idx)]);
        t.timeout_onmouseout[parseInt(el.it.idx)] = setTimeout (function(t, evt) {
            var toHide = t.mustHide (t, t.currentEl.it, evt);
            if (t.currentEl.it.level > 1 || $(t.el).is('.noInitialShowing')) {
                na.m.log (1120, 'naVividMenu.onmouseout() : hiding sub-menu for "'+toHide.prevEl[0].it.label+'"', false);
                if (t.debugMe) debugger;
                if (
                    toHide.currentEl.length>0
                    || toHide.prevEl.length>0
                ) {
                    t.onmouseout_do(evt, toHide);
                }
            }
        }, t.sensitivitySpeedClose, t, event);

    }

    onmouseout_do (event, toHide) {
        if (!event) debugger;
        var
        t = this,
        el = event.currentTarget;
        if (!el.it) {
            na.m.log (1, '.../logic.vividUserInterface/v6.y.z/2D/vividMenu-5.y.z--behavior-rainbowPanels-1.1.0.js::onMouseOver (event) : RETURNED FALSE. RESULT : MELTDOWN! :P');
            return false;
        }

        var
        t = this,
        el = event.currentTarget,
        it = t.items[el.it.idx],
        menu = $('#menu_'+t.el.id)[0];

        var prevs = [], prevsLabels = [];
        for (var i=0; i < toHide.prevEl.length; i++) {
            var idxOrEl = toHide.prevEl[i];
            if (typeof idxOrEl=='object') {
                prevs.push(idxOrEl);
                prevsLabels.push (idxOrEl.id);
            } else {
                var it2 = t.items[idxOrEl];
                if (it2 && it2.b) {
                    prevs.push(it2.b.el);
                    prevsLabels.push (it2.label);
                }
            }

            currPanel = t.prevEl
                ? $('#'+t.el.id+'__panel__'+t.prevEl.it.idx)[0]
                : $('#'+t.el.id+'__panel__'+t.el.id)[0];
            prevs.push (currPanel);

            if (
                t.prevEl
                && t.prevEl.it.parents
                && t.prevEl.it.parents[0]
            ) {
                currPanel = $('#'+t.el.id+'__panel__'+t.prevEl.it.parents[0].idx)[0];
                prevs.push (currPanel);
            }

        }


        var parentPanels = [], currs = [], currsLabels = [];
        for (var i=0; i < toHide.currentEl[0].length; i++) {
            var idxOrEl = toHide.currentEl[i];
            if (typeof idxOrEl=='object') {
                currs.push (idxOrEl);
                currsLabels.push (idxOrEl.id);
            } else {
                var it2 = t.items[idxOrEl];
                if (it2.b) {
                    currs.push(it2.b.el);
                    currsLabels.push (it2.label);
                }
            }
        }

        prevsLabels = [];
        if (t.useDelayedShowingAndHiding)
            $('.vividMenu_item, .vividMenu_subMenuPanel', menu).not(rootLevel).not(currs).each(function(idx,el) {
                if (el.it && el.id.indexOf('panel')!==-1) prevsLabels.push ('PANEL:'+el.it.label);
                else if (el.it) prevsLabels.push (el.it.label);
            });
        else
            $(currs).not(prevs).each(function(idx,el) {
                if (el.it && el.id.indexOf('panel')!==-1) prevsLabels.push ('PANEL:'+el.it.label);
                else if (el.it) prevsLabels.push (el.it.label);
            });


        currsLabels = [];
        $(currs).not(prevs).each(function(idx,el) {
            if (el.it) currsLabels.push (el.it.label);
        });

        var rootLevel = [];
        $('.vividMenu_item, .vividMenu_subMenuPanel', menu).each(function(idx,div) {
            var
            idx = parseInt(div.id.replace(/.*__/,'')),
            it = t.items[idx];
            if (it && it.level === 1 && div.id.indexOf('_panel')===-1) rootLevel.push (div);
        });


        if (event.currentTarget) {
            var currPanel =
                event.currentTarget.it.parents && event.currentTarget.it.parents.length > 0
                ? $('#'+t.el.id+'__panel__'+event.currentTarget.it.parents[0].idx)[0]
                : null;
            if (currPanel) currs.push(currPanel);
            currPanel = $('#'+t.el.id+'__panel__'+event.currentTarget.it.idx)[0];
            if (currPanel && !currs.includes(currPanel)) currs.push(currPanel);

            if (event.currentTarget.it.parents && event.currentTarget.it.parents.length > 0) {
                for (var i=0; i < event.currentTarget.it.parents.length; i++) {
                    var p = event.currentTarget.it.parents[i];
                    //console.log ('t222',p);
                    parentPanels.push ($('#'+t.el.id+'__panel__'+p.idx)[0]);
                }
            }
        }

        //if (t.prevEl && t.currentEl && t.prevEl.it.level !== t.currentEl.it.level) {
        if (t.prevEl) {
            var currPanel =
                t.prevEl.it.parents && t.prevEl.it.parents.length > 0
                ? $('#'+t.el.id+'__panel__'+t.prevEl.it.parents[0].idx)[0]
                : null;
            if (currPanel) currs.push(currPanel);
            currPanel = $('#'+t.el.id+'__panel__'+t.prevEl.it.idx)[0];
            if (currPanel && !currs.includes(currPanel)) currs.push(currPanel);
        }

        var myKids = [];

        if (toHide.currentEl[0]) {
            var myKids2 = t.children[toHide.currentEl[0].it.idx];

            /*
            for (var kidIdx in myKids2) {
                var it2 = myKids2[kidIdx];
                if (it2.b) {
                    //currs.push(it2.b.el);
                    myKids.push (it2.b.el);

                };
            }*/

            currPanel = $('#'+t.el.id+'__panel__'+toHide.currentEl[0].it.idx)[0];
            myKids.push (currPanel);

            /*
            if (
                toHide.currentEl[0].it.parents
                && toHide.currentEl[0].it.parents[0]
            ) {
                currPanel = $('#'+t.el.id+'__panel__'+toHide.currentEl[0].it.parents[0].idx)[0];
                myKids.push (currPanel);
            }*/
        }

        var myPeers = [];
        var prevKids = [];
        var prevPeers = [];
        /*
        if (
            toHide.currentEl[0]
            && toHide.currentEl[0].it.parents && toHide.currentEl[0].it.parents.length > 0
        ) {
            for (var pIdx=0; pIdx<toHide.currentEl[0].it.parents.length; pIdx++) {
                var prevPeers_idxs = t.children[toHide.currentEl[0].it.parents[pIdx].idx];
                for (var peerIdx in prevPeers_idxs) {
                    var it2 = prevPeers_idxs[peerIdx];
                    if (it2 && it2.b) prevPeers.push (it2.b.el);
                }
                var panel = $('#'+t.el.id+'__panel__'+toHide.currentEl[0].it.parents[pIdx].idx);
                if (panel[0] && it2.b) prevPeers.push (it2.b.el);
            }

            var myPeers_idxs = t.children[toHide.currentEl[0].it.parents[0].idx];

            for (var peerIdx in myPeers_idxs) {
                var it2 = myPeers_idxs[peerIdx];
                if (it2 && it2.b) myPeers.push (it2.b.el);
            }
            if (toHide.currentEl[0] && toHide.currentEl[0].it.parents && toHide.currentEl[0].it.parents[0]) {
                var panel = $('#'+t.el.id+'__panel__'+toHide.currentEl[0].it.parents[0].idx);
                if (panel[0]) myPeers.push (panel[0]);
            }

            if (t.children[it2.idx] && t.children[it2.idx].length && t.children[it2.idx].length > 0) {
                var peersKids_idx = t.children[it2.idx];
                for (var peerKidIdx in peersKids_idx) {
                    var it3 = peersKids_idx[peerKidIdx];
                    if (it3 && it3.b) prevKids.push (it3.b.el);
                }
            }
        }*/

        if (
            toHide.currentEl[0]
            && t.prevEl
            && toHide.currentEl[0].it.level === t.prevEl.it.level
            && t.prevEl.it.parents && t.prevEl.it.parents.length > 0
        ) {
            var prevKids_idxs = t.children[t.prevEl.it.idx];
            /*
            for (var peerIdx in prevKids_idxs) {
                var it2 = prevKids_idxs[peerIdx];
                if (it2.b) prevKids.push (it2.b.el);
            }*/
            var panel = $('#'+t.el.id+'__panel__'+t.prevEl.it.idx);
            if (panel[0]) prevKids.push (panel[0]);
        }

        var rootPath = [];
        var panel = $('#'+t.el.id+'__panel__'+it.idx);
        if (panel[0]) rootPath.push (panel[0]);
        if (it.parents && it.parents.length > 0) {
            for (var i=0; i < it.parents.length; i++) {
                var panel = $('#'+t.el.id+'__panel__'+it.parents[i].idx);
                if (panel[0]) rootPath.push (panel[0]);
            }
        }

        //if (t.timeout_hidingPrevs) clearTimeout (t.timeout_hidingPrevs);
        //t.timeout_hidingPrevs = setTimeout (function (prevKids, myKids, rootPath) {
            var
            currs =
                //$('.vividMenu_item')
                $('.vividMenu_subMenuPanel')
                //.add(prevKids)
                .not(myKids)
                //.not(rootLevel)
                .not('#'+t.el.id+'__panel__'+t.el.id)
                //.not(myPeers)
                .not(rootPath)
                .add(prevKids),
            currsStr = '';
            for (var i=0; i<currs.length; i++) {
                var
                itsKids = $('a', currs[i].childNodes),
                itsKidsStr = '';
                for (var j=0; j<itsKids.length; j++) {
                    if (itsKidsStr!=='') itsKidsStr += ', ';
                    itsKidsStr += '"'+itsKids[j].innerHTML+'"';
                }

                if (currsStr!=='') currsStr += ',\n';
                currsStr += '#'+currs[i].id+' ('+itsKidsStr+')';
            }

            na.m.log (1122, 'naVividMenu.onmouseout_do() : hiding\n'+currsStr);

            if (t.useFading) {
                $(currs).stop(true,true).fadeOut(t.fadingSpeed);//, function() { debugger; $(this).remove(); });
            } else {
                $(currs).remove();//css({display:'none'});
            }
        //}, t.sensitivitySpeedClose, prevKids, myKids, rootPath);
    }


    onclick(it) {
        event.preventDefault();
        var a = $(it.b.el.el).children('a');
        if (
            typeof a.attr('windowName') == 'string'
            && a.attr('windowName')!==''
        ) {
            window.open(a.attr('href'),a.attr('windowName')).focus();
        } else {
            var href = a.attr('href');
            if (href.match(/javascript:/)) eval(href.replace('javascript:','')); else window.location.href = href;
        }
    }


    updateItemStates() {
        var t = this;
        $(this.el).find('li > a').each(function(idx,li) {
            let
            isc = $(li).attr('vividMenu_isSelected_condition');

            if (isc) {
                var
                menuItem = t.items[idx].b.el,
                r = eval(isc);

                if (r)
                    $(menuItem).addClass('vividButtonSelected').removeClass('vividButton');
                else
                    $(menuItem).removeClass('vividButtonSelected').addClass('vividButton')
            }
        });
    }
}
