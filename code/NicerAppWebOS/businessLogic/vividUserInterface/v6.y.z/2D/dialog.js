class vividUserInterface_2D_dialog {
    constructor (cmd) {
        var t = this;
        if (cmd.id && cmd.className) {
            t.cmd = {
                el : [cmd]
            }
        } else if (cmd.naSite) {
            t.cmd = cmd;
            t.cmd.el = [cmd.el];
        }
        t.el = t.cmd.el[0];
        t.el.vividUserInterface_2D_dialog = t;
        t.settings = {};


        if (!$('canvas',t.el)[0]) {
            var c = document.createElement('canvas');
            $(c).css({
                position : 'absolute',
                height : '100%',
                width : '100%',
                top : 0,
                left : 0

            });
            //t.cmd.el.push(c);
            t.canvas = c;
        } else {
            t.canvas = $('canvas',t.cmd.el)[0];
        }



        var html = '';
        if (!$(t.el).find('.vdBackground')[0]) html += '<div class="vdBackground"></div>';
        $(t.el).css({background:'none'});

        var idx = false;
        if (na.site.globals.themesDBkeys)
        for (var i in na.site.globals.themesDBkeys) {
            if (na.site.globals.themesDBkeys[i].specificityName == na.site.globals.specificityName) idx = i;
        }
        //debugger;
        //if (idx!==false && na.site.globals.themesDBkeys[idx].display) {
            let
            opacity = (
                $('#vdSettings_show').val()=='hidden'
                ? 0.000001
                : $('#vdSettings_show').val()=='transparent'
                    ? 0.5
                    : 1
            );

            $('.vdTools, .vdSettings', t.el).remove();

            let
            roBtnCloseWindow = $(t.el).is('.naHas_btnCloseWindow') ? 5 : 0,
            roBtnGrok = (
                roBtnCloseWindow && $(t.el).is('.madeBy_Grok')
                ? 55
                : (
                    $(t.el).is('.madeBy_Grok')
                    ? 5
                    : 0
                )
            ),
            roBtnComments = (
                roBtnGrok && roBtnCloseWindow && $(t.el).is('.naHas_btnComments')
                ? 110
                : (
                    (roBtnGrok || roBtnCloseWindow) && $(t.el).is('.naHas_btnComments')
                    ? 55
                    : 5
                )
            );

            html += (
                    !$(t.el).is('.naNoSettings')
                    ? '<div class="vdSettings" style="z-index:10000;opacity:'+opacity+';position:absolute;top:-15px;left:30px;z-index:7000000">'
                            +'<img class="btnSettings" style="width:50px;" src="/siteMedia/btnPickColor.png" onclick="na.d.s.visibleDivs = arrayRemove (na.d.s.visibleDivs,\'#siteToolbarThemeEditor\');na.d.s.visibleDivs.push (\'#siteToolbarThemeEditor\'); na.desktop.resize(); na.site.settings.activeDivs = [\'#siteToolbarThemeEditor\']; var d = na.site.c.dialogs[\'#'+this.el.id+'\']; d.displaySettingsDialog(d, \''+t.el.id+'\')"/>'
                            +'<input type="range" min="1" max="100" value="50" class="sliderOpacityRange" style="width:140px;" onchange="na.te.opacityChange(event);"/>'
                        +'</div>'
                    : ''
                )
                +(
                    '<div class="vdTools" style="z-index:10000;opacity:'+opacity+';position:absolute;right:40px;top:-20px;z-index:7000000">'
                        +(
                            $(t.el).is('.naHas_btnCloseWindow')
                            ? na.m.html_vividButton (
                                4, 'position:absolute;right:'+roBtnCloseWindow+'px;',

                                t.el.id+'__btnCloseWindow', 'vividButton_icon_50x50 btnCloseWindow grouped', '_50x50', 'grouped',
                                '',
                                'if (na.d.s.visibleDivs.includes(\'#'+t.el.id+'\')) na.d.s.visibleDivs=arrayRemove(na.d.s.visibleDivs,\'#'+t.el.id+'\'); else na.d.s.visibleDivs.push(\'#'+t.el.id+'\'); if (!$(this).is(\'.disabled\')) { $(event.target).parents(\'.vividDialog, .vividDialogPopup\').fadeOut(\'fast\').animate({right:-1000}); };debugger;  na.desktop.resize();',
                                '',
                                '',

                                7, 'Close Window',

                                'btnCssVividButton_outerBorder.png',
                                'btnCssVividButton.png',
                                null,//'btnCssVividButton.grey2a.png',
                                'btnDelete2.png',
                                '', null, null, null
                            )
                            :''
                        )
                        /* example button only; x.ai, creators of Grok.com, do not allow unauthorized branding +(
                            $(t.el).is('.madeBy_Grok')
                            ? na.m.html_vividButton (
                                4, 'position:absolute;right:'+roBtnGrok+'px;',

                                t.el.id+'__btnMadeBy_Grok', 'vividButton_icon_50x50 btnMadeBy_Grok grouped', '_50x50', 'grouped',
                                '',
                                'window.open(\'https://grok.com\', \'_blank\', \'width=1200,height=800,noopener,noreferrer\');',
                                '',
                                '',

                                7, 'Made by Grok.com and Rene AJM Veerman, MIT-licensed',

                                'btnCssVividButton_outerBorder.png',
                                'btnCssVividButton.png',
                                'btnCssVividButton.grey2a.png',
                                'Grok-Logo-xAI-Futuristic-AI-785-modded-by-NicerAppSoftware.png',
                                '', null, null, null
                            )
                            :''
                        )*/
                        +(
                            $(t.el).is('.naHas_btnComments')
                            ? na.m.html_vividButton (
                                4, 'position:absolute;right:'+roBtnComments+'px;',

                                t.el.id+'__btnComments', 'vividButton_icon_50x50 btnComments grouped', '_50x50', 'grouped',
                                '',
                                'if (na.d.s.visibleDivs.includes(\'#siteComments\')) na.d.s.visibleDivs=arrayRemove(na.d.s.visibleDivs,\'#siteComments\'); else { na.d.s.visibleDivs.push(\'#siteComments\'); $(\'#siteComments\').fadeIn(\'slow\')}; na.desktop.resize();',
                                '',
                                '',

                                7, 'Comments',

                                'btnCssVividButton_outerBorder.png',
                                'btnCssVividButton.png',
                                null,//'btnCssVividButton.grey2a.png',
                                'pencil.png',
                                '', null, null, null
                            )
                            :''
                        )
                    +'</div>');
            $(t.el).prepend(html).delay(2000);
            $('.vividButton4, .vividButton_icon_50x50, .vividButton_icon_100x100', t.el).each(function(idx,el) {
                var c = na.site.components;
                if (!c.buttons['#'+el.id]) c.buttons['#'+el.id] = new vividUserInterface_2D_button ({ naSite : na.site, el : el });
            });
        //} else {
            //$(t.el).find('.vdSettings').remove();
            //$(t.el).find('.vdBackground').remove();
            //$(t.el).prepend(html);
        //}






        t.borderVideo = $('video.naBorder',t.cmd.el)[0];
        t.backgroundVideo = $('video.naBackground',t.cmd.el)[0];
        if (
            navigator && navigator.connection
            && (
                navigator.connection.downlink < 2
                || navigator.connection.type && (
                    navigator.connection.type == 'bluetooth'
                    || navigator.connection.type == 'cellular'
                )
                || navigator.connection.saveData
            )
        ) {
            // todo : display a tiled background for this div.
            return this;
        } else if (t.borderVideo) {
            t.init_borderVideo(t);
            t.init_vividUserInterface_2D_dialogBorder_lavaLamp(t);
        };
        return this;
    }

    init_vividUserInterface_2D_dialogBorder_lavaLamp (t) {
        const ctx = t.canvas.getContext('2d');
        ctx.canvas.width = $(t.el).width();
        ctx.canvas.height = $(t.el).height();
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        const w = ctx.canvas.width;

        const h = $('#siteTaskbar').height();
        const dlw = 3;

        ctx.strokeStyle = ctx.createPattern(t.borderVideo,'no-repeat');
        ctx.lineWidth = dlw;
    }

    init_borderVideo (t) {
        if (t.borderVideo) {
            t.backgroundVideo.onloadstart = function (evt) {
                if (t.canvas) {
                    t.canvasInterval = window.setInterval(() => {
                        t.drawImage (t);
                    }, 1000 / 30);
                    t.borderVideo.play();
                    t.backgroundVideo.play();
                }
            };
            t.borderVideo.load();
            t.backgroundVideo.load();
        }
    }

    drawImage (t) {
        //requestAnimationFrame (function(p) { t.drawImage(t) } );
        const ctx = t.canvas.getContext('2d');
        ctx.canvas.width = $('#siteTaskbar').width();
        ctx.canvas.height = $('#siteTaskbar').height();
        ctx.setTransform(1, 0, 0, 1, 0, 0);
        const w = ctx.canvas.width;

        const h = $('#siteTaskbar').height();
        const dlw = 3;

        ctx.strokeStyle = ctx.createPattern(t.borderVideo,'no-repeat');
        ctx.lineWidth = dlw;

        var
        borderRadius = 10,
        x = 4, y = 4, // these determine the width and height of the video borders
        width = ctx.canvas.width-(x*2),
        height = ctx.canvas.height-(y*2);

        ctx.beginPath();
        ctx.moveTo(x + borderRadius, y);
        ctx.lineTo(x + width - borderRadius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + borderRadius);
        ctx.lineTo(x + width, y + height - borderRadius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - borderRadius, y + height);
        ctx.lineTo(x + borderRadius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - borderRadius);
        ctx.lineTo(x, y + borderRadius);
        ctx.quadraticCurveTo(x, y, x + borderRadius, y);
        ctx.stroke();
        ctx.closePath();

        ctx.beginPath();
        ctx.moveTo(x + borderRadius, y);
        ctx.lineTo(x + width - borderRadius, y);
        ctx.quadraticCurveTo(x + width, y, x + width, y + borderRadius);
        ctx.lineTo(x + width, y + height - borderRadius);
        ctx.quadraticCurveTo(x + width, y + height, x + width - borderRadius, y + height);
        ctx.lineTo(x + borderRadius, y + height);
        ctx.quadraticCurveTo(x, y + height, x, y + height - borderRadius);
        ctx.lineTo(x, y + borderRadius);
        ctx.quadraticCurveTo(x, y, x + borderRadius, y);
        ctx.closePath();

        ctx.clip();
        ctx.drawImage (t.backgroundVideo, x, y, ctx.canvas.width, ctx.canvas.height);
    }

    hide (cmd) {
        var t = this;
        setTimeout (function() {
            if (
                !t.cmd.naSite.settings.heldUp
                || !cmd.checkHeldUp
                || t.cmd.naSite.settings.heldUp[cmd.checkHeldUp]
            ) return false;
            $(cmd.checkHeldUp).removeClass('shown').addClass('hidden');
        }, 300);

    }

    show (cmd) {
        var t = this;
        setTimeout (function() {
            if (
                !t.cmd.naSite.settings.heldUp
                || !cmd.checkHeldUp
                || t.cmd.naSite.settings.heldUp[cmd.checkHeldUp]
            ) return false;
            var me = cmd.checkHeldUp, o = cmd.btn.offset();
            $(cmd.checkHeldUp).css({
                display : 'flex',
                zIndex : 950000,
                position : 'absolute',
                left : o.left + na.d.g.margin,
                top : o.top - me.height() - na.d.g.margin
            }).removeClass('hidden').addClass('show');
        }, 300);

    }

    displaySettingsDialog (t, dialogID) {
        //na.site.settings.dialogs['#siteToolbarThemeEditor'].settings.current.dialogID = dialogID;
        var d = na.site.c.dialogs['#siteToolbarThemeEditor'];
        d.settings.dialogID = dialogID;
        na.d.s.visibleDivs = arrayRemove(na.d.s.visibleDivs, '#siteContent');
        na.d.s.visibleDivs = arrayRemove(na.d.s.visibleDivs, '#siteToolbarThemeEditor');
        na.d.s.visibleDivs.push ('#siteContent');
        na.d.s.visibleDivs.push ('#siteToolbarThemeEditor');
        na.te.onload(dialogID);
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
}

class vividUserInterface_2D_borderVideo extends HTMLVideoElement {
    constructor (cmd) {
        super ({});
        var t = this;
        t.cmd = cmd;

        $('video source', t.cmd.el).last().on('error', function(e) {
            t.failed(e);
        });
    }
    failed(e) {
   // video playback failed - show a message saying why
        switch (e.target.error.code) {
            case e.target.error.MEDIA_ERR_ABORTED:
            alert('You aborted the video playback.');
            break;
            case e.target.error.MEDIA_ERR_NETWORK:
            alert('A network error caused the video download to fail part-way.');
            break;
            case e.target.error.MEDIA_ERR_DECODE:
            alert('The video playback was aborted due to a corruption problem or because the video used features your browser did not support.');
            break;
            case e.target.error.MEDIA_ERR_SRC_NOT_SUPPORTED:
            alert('The video could not be loaded, either because the server or network failed or because the format is not supported.');
            break;
            default:
            alert('An unknown error occurred.');
            break;
        }
    }
}
