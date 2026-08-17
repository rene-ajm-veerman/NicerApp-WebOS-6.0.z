// Copyright (C) 2002-2023 and All Rights Reserved (R) by Rene AJM Veerman <rene.veerman.netherlands@gmail.com>
class naVividDialog {
    constructor(el,html,parent) {
        if ($('.vdSettings',el)[0]) return this;

        var t = this;
        t.p = parent;
        if (typeof html=='string' && html!=='') {
            var h = $(html);
            $(parent).append(h);
            t.el = h[0];
        } else {
            t.el = el;
        };
        t.t = $(this.el).attr('theme');
        t.settings = { current : {} };
        
        var html = '';
        if (!$(t.el).find('.vdBackground')[0]) html += '<div class="vdBackground"></div>';
        
        var idx = false;
        if (na.site.globals.themesDBkeys)
        for (var i in na.site.globals.themesDBkeys) {
            if (na.site.globals.themesDBkeys[i].specificityName == na.site.globals.specificityName) idx = i;
        }
        debugger;
        if (idx!==false && na.site.globals.themesDBkeys[idx].display) {
            var opacity = (
                $('#vdSettings_show').val()=='hidden'
                ? 0.000001
                : $('#vdSettings_show').val()=='transparent'
                    ? 0.5
                    : 1
            );
            html += 
                '<div class="vdSettings" style="z-index:10000;opacity:'+opacity+';">'
                    +'<img class="btnSettings" src="/siteMedia/btnPickColor.png" onclick="na.site.settings.activeDivs = [\'#siteToolbarThemeEditor\']; var d = na.site.settings.dialogs[\'#'+this.el.id+'\']; d.displaySettingsDialog(d, \''+t.el.id+'\')"/>'
                    +'<input type="range" min="1" max="100" value="50" class="sliderOpacityRange" onchange="na.te.opacityChange(event);"/>'
                +'</div>'
                +(
                    !$(t.el).is('.naNoComments')
                    ? '<div class="vdTools" style="z-index:10000;opacity:'+opacity+';">'
                        +'<img class="btnComments" src="/siteMedia/pencil.png" onclick="if (na.d.s.visibleDivs.includes(\'siteComments\')) arrayRemove(na.d.s.visibleDivs,\'#siteComments\'); else na.d.s.visibleDivs.push(\'#siteComments\'); na.desktop.resize();"></div>'
                    : ''
                );
            if (
                t.el.id!=='#siteToolbarThemeEditor' 
                && !$('.vdSettings',t.el)[0]
            ) $(t.el).prepend(html).delay(200);
            $('.vdSettings', t.el).fadeIn('slow');
            setTimeout(function() {
                $(t.el).css({overflow:'visible'});
            }, 800);
        } else {
            $(t.el).find('.vdSettings').remove();
            $(t.el).prepend(html);
        }


        var
        bg = t.el,
        rgbaRegEx = /rgba\((\d{1,3})\,\s*(\d{1,3})\,\s*(\d{1,3})\,\s*([\d.]+)\)(.*)/,
        rgbRegEx = /rgb\((\d{1,3})\,\s*(\d{1,3})\,\s*(\d{1,3})\)(.*)/;
        if (bg && $(bg).children('.vdBackground')[0]) bg = $(bg).children('.vdBackground');
        var bg1 = $(bg).css('background');
        if (typeof bg1=='string' && bg1!=='' && !bg1.match('url')) {
            var bg2 = '', bg2a = bg1.match(rgbaRegEx), bg2b = bg1.match(rgbRegEx);
            if (bg2a) {
                var opacity = bg2a[4];
                $(bg).add('.boxShadow_bg, .textShadow_bg').css({ background : 'rgba('+bg2a[1]+', '+bg2a[2]+', '+bg2a[3]+', '+bg2a[4]+')'+bg2a[5] });
            } else {
                var opacity = 0.5;
                $(bg).add('.boxShadow_bg, .textShadow_bg').css({ background : 'rgba('+bg2b[1]+', '+bg2b[2]+', '+bg2b[3]+', 0.5)'+bg2b[4] });
            }
        } else { 
            var opacity = parseFloat($(bg).css('opacity'));
            $(bg).add('.boxShadow_bg, .textShadow_bg').css({ opacity : opacity });
        }
        var dialog = $(bg).parents('.vividDialog')[0];
        $(dialog).find('.sliderOpacityRange').val(parseInt(opacity*100));
        
        var el = t.el;
        var opacity = (
            $('#vdSettings_show').val()=='hidden'
            ? 0.000001
            : $('#vdSettings_show').val()=='transparent'
                ? 0.5
                : 1
        );


        $('.vdSettings, .vdTools', t.el).hover (function() {
            $(this).stop(true,true).animate({ opacity : 1 }, 'fast');
        }, function() {
            var opacity = (
                $('#vdSettings_show').val()=='hidden'
                ? 0.000001
                : $('#vdSettings_show').val()=='transparent'
                    ? 0.5
                    : 1
            );
            $(this).stop(true,true).animate({ opacity : opacity }, 'fast');
        });

        $('.vdSettings', t.el).click (function() {
            var opacity = (
                $('#vdSettings_show').val()=='hidden'
                ? 0.000001
                : $('#vdSettings_show').val()=='transparent'
                    ? 0.5
                    : 1
            );
            $(this).stop(true,true).animate({opacity:opacity});
        }).each(function(idx,el) {
            var opacity = (
                $('#vdSettings_show').val()=='hidden'
                ? 0.000001
                : $('#vdSettings_show').val()=='transparent'
                    ? 0.5
                    : 1
            );
            $(this).stop(true,true).animate({opacity:opacity});
        });
        
        $('.vividDialogContent', t.el).css({display:'block'});

        return this;
    };
    
    displaySettingsDialog (t, dialogID) {
        //na.site.settings.dialogs['#siteToolbarThemeEditor'].settings.current.dialogID = dialogID;
        var d = na.site.settings.dialogs['#siteToolbarThemeEditor'];
        d.settings.current.dialogID = dialogID;
        t.displaySettingsDialog_displayDialog(t);
/*
        var html =
            '<div class="vdSettingsScripts">'
            +'<link rel="stylesheet" href="/NicerAppWebOS/3rd-party/jsTree-3.2.1/dist/themes/default/style.css" onload="var d = na.site.settings.dialogs[\'#siteToolbarThemeEditor\']; d.displaySettingsDialog_scriptLoaded(d);"/> <!-- has style.min.css -->'
            +'<script type="text/javascript" src="/NicerAppWebOS/3rd-party/jsTree-3.2.1/dist/jstree.min.js?c='+na.m.changedDateTime_current()+'" onload="var d = na.site.settings.dialogs[\'#siteToolbarThemeEditor\'];  d.displaySettingsDialog_scriptLoaded(d);"></script> <!-- has jstree.min.js -->'
            +'</div>';
        if ($('.vdSettingsScripts').length<1) {
            $(t.el).prepend(html);

            //na.m.addJS (null, "/NicerAppWebOS/3rd-party/jQuery/spectrum/dist/spectrum.min.js?c="+na.m.changedDateTime_current(), null, function () { d.displaySettingsDialog_scriptLoaded(d); });
            //na.m.addJS (null, "/NicerAppWebOS/3rd-party/jsTree-3.2.1/dist/jstree.min.js?c="+na.m.changedDateTime_current(), null, function () { d.displaySettingsDialog_scriptLoaded(d); });
            //na.m.addJS (null, "/NicerAppWebOS/themeEditor.js?c="+na.m.changedDateTime_current(), null, function () { d.displaySettingsDialog_scriptLoaded(d); });
            
        } else {
            var d = na.site.settings.dialogs['#siteToolbarThemeEditor'];
            d.settings.current.dialogID = d.el.id;
            t.displaySettingsDialog_displayDialog(t);
        }*/
    }
    
    displaySettingsDialog_scriptLoaded (t) {
        if (!t.scriptLoadedCount) t.scriptLoadedCount = 1; else t.scriptLoadedCount++;
        if (t.scriptLoadedCount==2) t.displaySettingsDialog_displayDialog(t);
    }
    
    displaySettingsDialog_displayDialog (t) {
        if (!na.desktop.settings.visibleDivs.includes('#siteToolbarThemeEditor')) na.desktop.settings.visibleDivs.push('#siteToolbarThemeEditor');
        var did = na.site.settings.dialogs['#siteToolbarThemeEditor'].settings.current.dialogID;
        
        //$('#themeEditor_photoSpecificity_dialog').val(did);
        //$('#themeEditor_photoSpecificity_dialog').parent().html('#'+did+$('#themeEditor_photoSpecificity_dialog')[0].outerHTML);
        
        var el = $('#siteToolbarThemeEditor')[0];
        $('img[srcPreload]',el).each(function(idx,el2) {
            $(el2).attr('src', $(el2).attr('srcPreload'));
            $(el2).removeAttr('srcPreload');
        });
        na.themeEditor.onload(did);
        
        na.desktop.resize(function(el) {
            /*if (el && el.id=='siteToolbarThemeEditor') {
                $('img[srcPreload]',el).each(function(idx,el2) {
                    $(el2).attr('src', $(el2).attr('srcPreload'));
                    $(el2).removeAttr('srcPreload');
                });
                na.themeEditor.onload(t.settings.current.dialogID);
            }*/
        });
    }
}
