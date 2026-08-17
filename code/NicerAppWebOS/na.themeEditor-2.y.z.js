class naThemeEditor {
/* (C) and (R) +-2022 to 2025 Rene AJM Veerman <rene.veerman.netherlands@gmail.com>
 * price-tag for this component and all UI code and graphics examples for this nifty web dialog =
 * big-tech (as defined by .../NicerAppWebOS/LICENSE.html) : five million euros for a perpetual license for all your users and staff.
 * mediumsized online tech companies (as defined by .../NicerAppWebOS/LICENSE.html) : 1 million euros for a perpetual license for all your users and staff.
 * small online tech companies (as defined by .../NicerAppWebOS/LICENSE.html) : one hundred thousand euros for a perpetual license for all your users and staff.
 */
    settings = {
        current : { 
            firstRun : true, 
            forDialogID : 'siteContent',
            //selectedButtonID : 'btnSelectBackgroundColor', // OBSOLETED
            selectedSetting : 'selectorSet',
            selectedThemeName : na.site.globals.themeName,
            elementsCSS : {}
        } 
    }

    constructor(cmd) {
        var t = this;
        t.cmd = cmd;
        t.s = t.settings;
        t.s.c = t.settings.current;

        return t;
    }

    onload (forDialogID) {
        na.m.waitForCondition ('na.te.onload(): $.spectrum()?', function () {
            var r =
                typeof jQuery.spectrum=='object'
                && typeof jQuery.jstree=='object'
                && typeof $('#themeEditor_jsTree_selectors').jstree('get_json')=='object';
            return r;
        }, function() {
            na.te.onload_do(forDialogID);
        }, 200);
    }

    onload_do  (forDialogID) {
        var
        date = new Date(),
        timeInMilliseconds = date.getTime();

        this.s = this.settings;

        na.m.settings.startTime = timeInMilliseconds;
        if (typeof forDialogID=='string') {
            var
            fncn = 'na.te.onload("'+forDialogID+'")';
            na.te.s.c.forDialogID = forDialogID;
            $('#specificityForDiv').html ('#'+forDialogID);
        }
        
        $('.themeEditorComponent, .themeEditorComponent_containerDiv2').css({display:'none'});
        //na.te.makeThemesList( na.te.s.c.selectedThemeName );
        for (var appName in na.site.globals.app) break;
        $('#span_cb_app').html ('<b>App : </b>'+appName);

        $('#siteToolbarThemeEditor__specificity_dropdown > .vividDropDownBox_selected').css({
            width : $('#siteToolbarThemeEditor > .vividDialogContent').width()
                - $('.siteToolbarThemeEditor__label__specificity').width()
                - 70
        });

        /*
        //setTimeout(function() {
                    delete na.site.c.menus['#siteToolbarThemeEditor__selector'];
                    na.site.c.menus['#siteToolbarThemeEditor__selector'] =
                        new naVividMenu ($('#siteToolbarThemeEditor__selector')[0], true);
        //}, 1000);
        */
        if (!na.site.c.menus['#textFontFamily'])
            na.site.c.menus['#textFontFamily'] =
                new CascadingMenu ($('#textFontFamily')[0]);


        $('#textFontFamily')[0].addEventListener('mouseover', function() {
            clearTimeout (na.te.s.c.timeout_hover_textFontFamily);
            $('#siteToolbarThemeEditor > .vividDialogContent').css({ overflow : 'visible' });
        });
        $('#textFontFamily')[0].addEventListener('mouseout', function() {
            clearTimeout (na.te.s.c.timeout_hover_textFontFamily);
            na.te.s.c.timeout_hover_textFontFamily = setTimeout (function() {
                $('#siteToolbarThemeEditor > .vividDialogContent').css({ overflow : 'visible auto' });
            }, 1000);
        });

        /* REPLACED BY new RegExp(/.*clamp.* /) CSS font-size value.
        na.site.desktop.registerProgress ('[ThemeEditor]', function() {
            var
            itEl = $('#textFontFamily')[0],
            t = na.site.c.menus['#textFontFamily'];
            if (t) {
                var itEl2 = t.items[0].b.el;
                t.showMenuItem (
                    t,
                    t.items[0],
                    t.getDimensions (t, itEl2, false),
                    false
                );
            }

            /*
            itEl = $('#siteToolbarThemeEditor__selector')[0],
            t = na.site.c.menus['#siteToolbarThemeEditor__selector'];
            if (t) {
                var itEl2 = t.items[0].b.el;
                //debugger;
                t.showMenuItem (
                    t,
                    t.items[0],
                    t.getDimensions (t, itEl2, false),
                    false
                );
            }* /
        });
        na.site.desktop.registerCallback ('[ThemeEditor]', '#siteToolbarThemeEditor', function() {
            var
            itEl = $('#textFontFamily')[0],
            t = na.site.c.menus['#textFontFamily'];
            if (t) {
                var itEl2 = t.items[0].b.el;
                t.showMenuItem (
                    t,
                    t.items[0],
                    t.getDimensions (t, itEl2, false)
                );
            }

            /*
            itEl = $('#siteToolbarThemeEditor__selector')[0],
            t = na.site.c.menus['#siteToolbarThemeEditor__selector'];
            if (t) {
                var itEl2 = t.items[0].b.el;
                t.showMenuItem (
                    t,
                    t.items[0],
                    t.getDimensions (t, itEl2, false)
                );
            }
            * /
        });*/

        /*
        var
        url = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/blogEditor/ajax_getTreeNodes.php',
        ac = {
            type : 'GET',
            url : url,
            success  (data, ts, xhr) {
        */
                let dat2 = na.te.transform_siteGlobalsThemes_to_jsTree();
                na.te.s.c.dbSelectors = dat2;
                let dat = dat2.dat;
                let did = dat2.did;
                //na.te.s.c.db = dat;

                var lastFolder = null;

                if (na.te.s.c.backgroundFolder) {
                    var
                    x = na.te.s.c.backgroundFolder.split('/'),
                    lastFolder = null;

                    for (var i=x.length-1; i>=0; i--) {
                        for (var j=0; j<dat.length; j++) {
                            if (dat[j].text == x[i]) {
                                if (!lastFolder) {
                                    lastFolder = dat[j];
                                    var path = na.te.currentPath (lastFolder);
                                    if (path == na.te.s.c.backgroundFolder) break;
                                }
                            }
                        }
                    }
                };

                if ($.jstree) $.jstree.defaults.core.error = function (a,b,c,d) {
                    //debugger;
                };
                na.te.initSelectorsTree (dat);

                if (did) setTimeout (function() {
                    $('#themeEditor_jsTree_selectors').jstree('deselect_all').jstree('select_node', did);
                }, 200);


                $('#siteToolbarLeft .lds-facebook').fadeOut('slow');
          /*  },
            error  (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }
        };
        $.ajax(ac);
        */

        var 
        url = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_getTreeNodes.php',
        ac = {
            type : 'GET',
            url : url,
            success  (data, ts, xhr) {
                let dat = JSON.parse(data);
                na.te.s.c.db = dat;
                
                var lastFolder = null;
                
                if (na.te.s.c.backgroundFolder) {
                    var 
                    x = na.te.s.c.backgroundFolder.split('/'),
                    lastFolder = null;
                    
                    for (var i=x.length-1; i>=0; i--) {
                        for (var j=0; j<dat.length; j++) {
                            if (dat[j].text == x[i]) { 
                                if (!lastFolder) {
                                    lastFolder = dat[j];
                                    var path = na.te.currentPath (lastFolder);
                                    if (path == na.te.s.c.backgroundFolder) break;
                                }
                            }
                        }
                    }
                };
                    
                if ($.jstree) $.jstree.defaults.core.error = function (a,b,c,d) {
                    //debugger;
                };
                $('#themeEditor_jsTree_backgrounds').css({
                    height : $('#siteToolbarLeft .vividDialogContent').height() - $('#jsTree_navBar').height()
                }).jstree('destroy').jstree({
                    core : {
                        data : dat,
                        check_callback : true,
                        multiple : false
                    },
                    types : {
                        "naSystemFolder" : {
                            "icon" : "/siteMedia/na.site.view.tree.naSystemFolder.png",
                            "valid_children" : []
                        },
                        "naUserRootFolder" : {
                            "max_depth" : 14,
                            "icon" : "/siteMedia/na.site.view.tree.naUserRootFolder.png",
                            "valid_children" : ["naFolder", "naMediaAlbum", "naDocument"]
                        },
                        "naGroupRootFolder" : {
                            "max_depth" : 14,
                            "icon" : "/siteMedia/na.site.view.tree.naGroupRootFolder.png",
                            "valid_children" : ["naFolder", "naMediaAlbum", "naDocument"]
                        },
                        "naFolder" : {
                            "icon" : "/siteMedia/na.site.view.tree.naFolder.png",
                            "valid_children" : ["naFolder", "naMediaAlbum", "naDocument"]
                        },
                        "naDialog" : {
                            "icon" : "/siteMedia/na.site.view.tree.naSettings.png",
                            "valid_children" : []
                        },
                        "naSettings" : {
                            "icon" : "/siteMedia/na.site.view.tree.naSettings.png",
                            "valid_children" : []
                        },
                        "naTheme" : {
                            "icon" : "/siteMedia/na.site.view.tree.naVividThemes.png",
                            "valid_children" : []
                        },
                        "naVividThemes" : {
                            "icon" : "/siteMedia/na.site.view.tree.naVividThemes.png",
                            "valid_children" : []
                        },
                        "naMediaAlbum" : {
                            "icon" : "/siteMedia/na.site.view.tree.naMediaAlbum.png",
                            "valid_children" : [ "naMediaAlbum" ]
                        },
                        "naDocument" : {
                            "icon" : "/siteMedia/na.site.view.tree.naDocument.png",
                            "valid_children" : []
                        },
                        "saApp" : {
                            "icon" : "/siteMedia/na.site.view.tree.naApp.png",
                            "valid_children" : []
                        }
                    },
                    "plugins" : [
                        "contextmenu", "dnd", "search",
                        "state", "types", "wholerow", "multiselect"
                    ]
                }).on('changed.jstree', function (e, data) {
                    $('img[srcpreload]').each (function (idx,el) {
                        el.src = el.srcpreload;
                    })
                    if (data.action=='select_node') {
                        na.te.s.c.selectedBackground = data;
                        for (var i=0; i<data.selected.length; i++) {
                            var
                            d = data.selected[i],
                            rec = data.instance.get_node(d),
                            btn = na.site.c.buttons['#btnSelectBackgroundImage'];

                            $('#documentTitle').val(rec.original.text);
                            na.te.s.c.selectedTreeNode = rec;
                            if (rec.original.type=='naDocument') {
                                if (btn) btn.disable();
                            } else if (rec.original.type=='naMediaAlbum') {
                                if (btn) btn.enable();
                                var
                                path = na.te.currentPath(rec),
                                path = path.replace(/ /g, '%20'),
                                src = '/NicerAppWebOS/businessLogic/vividUserInterface/v5.y.z/photoAlbum/4.0.0/index.php?codePath='+path,
                                el = $('#themeEditor_photoAlbum')[0];
                                el.onload = setTimeout(function(el) {
                                    na.te.onresize()
                                }, 10, el);
                                el.src = src;
                            } else {
                                if (btn) btn.disable();
                            }

                        };
                    };
                });
                
                /*
                if (lastFolder) setTimeout (function() {
                    $('#themeEditor_jsTree_backgrounds').jstree('deselect_all').jstree('select_node', lastFolder.id);
                }, 200);
                */
                
                $('#siteToolbarLeft .lds-facebook').fadeOut('slow');
            },
            error  (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ac);

        if (na.te.s.c.forDialogID) {
            var
            div = $('#'+na.te.s.c.forDialogID),
            bg = $('#'+na.te.s.c.forDialogID+' > .vdBackground')[0];
        } else {
            var
            div = $(na.te.s.c.forElements),
            bg = $(na.te.s.c.forElements)[0];
        }
        if ($(bg).css('background')) {
            var
            rgbaRegEx = /rgba\(\d{1,3}\,\s*\d{1,3}\,\s*\d{1,3}\,\s*([\d\.]+)\).*/,
            rgbRegEx = /rgb\(\d{1,3}\,\s*\d{1,3}\,\s*\d{1,3}\).*/,
            scaleRegEx = /(\d+)px\s(\d+)px/,
            bgRegEx = /.*\/(Groups.*?)"\)/,
            test1a = $(bg).css('background').match(rgbaRegEx),
            test1b = $(bg).css('backgroundColor').match(rgbaRegEx),
            test2a = $(div).css('border').match(rgbaRegEx),
            test2b = $(div).css('borderTopColor').match(rgbaRegEx),
            test2c = $(div).css('border').match(rgbRegEx),
            test2d = $(div).css('borderTopColor').match(rgbRegEx),
            test3a = $(bg).css('backgroundSize').match(scaleRegEx),
            c = test1a ? $(bg).css('background') : test1b ? $(bg).css('backgroundColor') : 'rgba(0,0,0,0.5)',
            c2 = (
                test2a
                ? $(div).css('border')
                : test2b
                    ? $(div).css('borderTopColor')
                    : test2c
                        ? $(div).css('border')
                        : test2d
                            ? $(div).css('borderTopColor')
                            : 'black'
            ),
            c3 = $(bg).css('backgroundImage'),
            c3a = c3.match(bgRegEx);

            if (c3a) {
                var
                c4a = c3a[1].lastIndexOf('/'),
                c4b = c3a[1].substr(0, c4a);

                na.te.s.c.backgroundFolder = c4b;
            };

            na.te.s.c.borderColor = c2;

            var bgSrc = $(bg).css('backgroundImage');
            bgSrc = bgSrc.replace('url("', '');
            bgSrc = bgSrc.replace('")', '');
            bgSrc = bgSrc.replace("url'", '');
            bgSrc = bgSrc.replace("')", '');
            var bgEl = document.createElement('img');
            bgEl.onload = function () {
                if (na.te.s.c.forDialogID) {
                    var bg = $('#'+na.te.s.c.forDialogID+' > .vdBackground')[0];
                } else {
                    var bg = $(na.te.s.c.forElements)[0];
                }
                scaleRegEx = /(\d+)px\s(\d+)px/,
                test3a = $(bg).css('backgroundSize').match(scaleRegEx);

                if (test3a) {
                    na.te.s.c.scaleX = (parseInt(test3a[1]) * 100) / bgEl.naturalWidth;
                    na.te.s.c.scaleY = (parseInt(test3a[2]) * 100) / bgEl.naturalHeight;
                } else {
                    na.te.s.c.scaleX = 100;
                    na.te.s.c.scaleY = 100;
                };
                $('#themeEditor_photoScaleX').val(na.te.s.c.scaleX);
                $('#themeEditor_photoScaleY').val(na.te.s.c.scaleY);
            };
            bgEl.src = bgSrc;
        } else if ($(bg).css('backgroundColor')) {
            debugger;
        }
        
        $('#siteToolbarThemeEditor').css({ display : 'flex', flexDirection : 'row', flexWrap : 'wrap' });
        
        var x = $('#colorpicker').css('display'), y = 'abc';
        if (typeof c==='undefined') c = 'rgba(0,0,0,0.5)';
        if ($.spectrum) $('#colorpicker').css({display:'block'}).spectrum ({
            color:c, 
            flat : true,
            type:'flat',
            showAlpha : true,
            showPalette : false,
            clickoutFiresChange : false, 
            change  (color) {
                if (typeof color=='object') color = 'rgba('+color._r+', '+color._g+', '+color._b+', '+color._a+')';
                if (na.te.s.c.forDialogID) {
                    var bg = $('#'+na.te.s.c.forDialogID+' > .vdBackground')[0];
                } else {
                    var bg = $(na.te.s.c.forElements);
                };
                debugger;
                $(bg).css({ background : color, opacity : 1 });
                na.site.saveTheme({callback:na.site.loadTheme});
            }});
        //if (na.te.s.c.selectedButtonID!=='btnSelectBackgroundColor') $('#colorpicker').next().css({display:x});
        var x = $('#borderColorpicker').css('display');
        if (typeof c2==='undefined') c2 = 'black';
        if ($.spectrum) $('#borderColorpicker').css({display:'block'}).spectrum ({
            color:c2, 
            flat : true,
            type: "flat", 
            showAlpha : true,
            showPalette : false, 
            clickoutFiresChange : false, 
            change : na.te.borderSettingsSelected
        });
        //if (na.te.s.c.selectedButtonID!=='btnSelectBorderSettings') $('#borderColorpicker').next().css({display:x});
        
        //if ($(window).width() < na.site.globals.reallySmallDeviceWidth) $('.sp-container').css({width:$(window).width()-35});
        //$('.sp-container').addClass('themeEditorComponent').css({position:'absolute'});

        na.te.s.c.borderColor = c2;

        /*
        var 
        div = $('#'+na.te.s.c.forDialogID),
        rgbaRegEx = /(rgba\(\d{1,3}\,\s*\d{1,3}\,\s*\d{1,3}\,\s*[\d.]+\)).* /,
        rgbRegEx = /(rgb\(\d{1,3}\,\s*\d{1,3}\,\s*\d{1,3}\)).* /,
        test1 = $(div).css('textShadow').match (rgbaRegEx),
        test2 = $(div).css('textShadow').match (rgbRegEx);
        if (test1) {
            var textShadowColor = test1[1];
        } else if (test2) {
            var textShadowColor = test2[1];
        } else {
            var textShadowColor = 'black';
        };
        na.te.s.c.textShadowColor = textShadowColor;
        */
        
        var
        p1 = $(div).find('td').css('color'),
        p2 = $(div).css('color');
        if (p1) {
            var textColor = p1
        } else if (p2) {
            var textColor = p2;
        } else {
            var textColor = 'white';
        };
        na.te.s.c.textColor = textColor;
        if (!na.te.s.c.selectedTextShadow) {
            na.te.s.c.selectedTextShadow = $('#textShadow_0')[0];
            $('#textShadow_0').css({ textShadow : $(div).css('textShadow') });
        }
        setTimeout (function() {
            $('.mediaThumb', $('#themeEditor_photoAlbum')[0].contentWindow.document).each(function(idx,el) {
                //na.m.log (300, 'el.src='+el.src.replace('thumbs/', ''));
                if (x && x.indexOf(el.src.replace('thumbs/', ''))!==-1) {
                    var scale = $('#'+forDialogID+' > .vdBackground').css('backgroundSize').match(/\d+/);
                    if (scale) na.te.s.c.scale = scale[0];
                    na.te.s.c.selectedImage = el;
                    na.m.log (300, 'na.te.s.c.selectedImage = '+el.src);
                }
            });
        }, 750);
        


        if (!na.te.s.c.specificity) {
            na.te.s.c.specificity = na.site.globals.themeDBkeys;
        }

        var s = na.te.s.c.specificity;
        if (na.site.components.buttons['#btnDeleteSpecificity'])
            if (!s || (!s.role && !s.user)) {
                na.site.components.buttons['#btnDeleteSpecificity'].disable();
            } else {
                na.site.components.buttons['#btnDeleteSpecificity'].enable();
            }
        
        $('#btnViewResult .vividButton_icon_borderCSS_50x50').css({
            boxShadow : '0px 0px 0px 0px rgba(0,0,0,0)'
        });
        
        var tabPage = na.te.s.c.selectedSetting;
        na.te.whichSettingSelected(tabPage);

        var theme = na.site.globals.themes[ $('.na_themes_dropdown__themes > .vividDropDownBox_selected').html() ];
        na.te.s.c.selectedBoxShadowID = (theme && theme.selectedBoxShadowID) || 'boxShadow_0';
        na.te.s.c.selectedTextShadowID = (theme && theme.selectedTextShadowID) || 'textShadow_0';
        //if (!na.te.s.c.boxSettings) na.te.s.c.boxSettings = $(na.te.s.c.selectedBoxShadowID)[0];
        
        //setTimeout (na.te.selectBorderSettings, 100);

        setTimeout (na.te.onresize, 200);
    }

    initSelectorsTree  (dat) {
        na.te.s.c.dbSelectors = dat;
        na.m.waitForCondition ('na.te.initSelectorsTree(): $.jstree()?', function() {
            return typeof $.jstree=='object' && typeof $.jstree.create=='function'
        }, function() {
            $('#themeEditor_jsTree_selectors').css({
                height : $('#siteToolbarLeft .vividDialogContent').height() - $('#jsTree_navBar').height()
            }).jstree('destroy').jstree({
                core : {
                    data : dat,
                    check_callback : true,
                    multiple : false
                },
                types : {
                    'naSelectorSet' : {
                        'icon' : '/siteMedia/na.site.view.tree.selectorSet.png',
                        'valid_children' : [ 'naElement', 'naSelectorSet', 'naCSS' ]
                    },
                    'naCSS' : {
                        'icon' : '/siteMedia/na.site.view.tree.css.png',
                        'valid_children' : [ 'naElement' ]
                    },
                    "naElement" : {
                        'icon' : '/siteMedia/na.site.view.tree.element.png',
                        'valid_children' : []
                    }
                },
                "plugins" : [ // " or ', that very often doesn't matter.
                    "contextmenu", "dnd", "search",
                    "state", "types", "wholerow", "multiselect"
                ]
            }).on('changed.jstree', function (e, data) {
                na.te.s.c.selectedSelector = data;
                na.te.enableDisableButtons('selectedSelector');
            });
        }, 200);
    }

    enableDisableButtons  (which) {
        var x = na.te.s.c;

        if (which=='selectedSelector') {
            var data = na.te.s.c.selectedSelector;
            if (data.action=='ready') {
                na.te.disableAllButtons();
                var l = data.selected.length, rec = null;
                for (var i=0; i<l; i++) {
                    var d = data.selected[i], rec2 = data.instance.get_node(d);
                    if (rec2 && rec2.original) rec = rec2;
                }
                if (rec && rec.type=='naSelectorSet' && rec.text!=='Dialogs' && rec.text!=='App') {
                    na.te.enableButtons ([ '#btnAddCSS' ]);
                }
                if (rec && rec.type=='naCSS' && rec.text!=='main') {
                    na.te.enableButtons ([ '#btnDeleteCSS' ]);
                }
                if (rec && rec.type=='naCSS')
                    na.te.enableButtons([
                        '#btnAddElement', '#btnSelectBackgroundColor',
                        '#btnSelectBorderSettings' , '#btnSelectBoxShadowSettings',
                        '#btnSelectTextSettings', '#btnSelectTextShadowSettings',
                        '#btnSelectBackgroundFolder' , '#btnSelectBackgroundImage'
                    ]);

                var inExtras = false;
                for (var idx in rec.parents) {
                    var it = data.instance.get_node(rec.parents[idx]);
                    if (it.text=='Extras') inExras = true;
                }

                if (rec && rec.type=='naElement')
                    if (
                        rec.text.match(/#site[\w\d]+$/)
                        || rec.text.match(/#app__[\w\d]+$/)
                        || inExtras
                    )
                        na.te.enableButtons([
                            '#btnAddElement', '#btnDeleteElement',
                            '#btnSelectBackgroundColor', '#btnSelectBorderSettings' , '#btnSelectBoxShadowSettings',
                            '#btnSelectTextSettings', '#btnSelectTextShadowSettings'
                        ]);
                    else if (
                        rec.text.match(/#site.*\s\>\s\.vdBackground$/)
                        || rec.text.match(/#app__.*\s\>\s\.vdBackground$/)
                    )
                        na.te.enableButtons([
                            '#btnSelectBackgroundFolder' , '#btnSelectBackgroundImage'
                        ]);
            }
            if (data.action=='select_node') {
                na.te.disableAllButtons();
                if (data.node && data.node.type=='naSelectorSet' && data.node.text!=='Dialogs' && data.node.text!=='App') {
                    na.te.enableButtons ([ '#btnAddGraphics' ]);
                }
                if (data && data.node.type=='naCSS' && data.node.text!=='main') {
                    na.te.enableButtons ([ '#btnDeleteGraphics' ]);
                }
                if (data.node && data.node.type=='naCSS')
                    na.te.enableButtons([
                        '#btnAddElement', '#btnSelectBackgroundColor',
                        '#btnSelectBorderSettings' , '#btnSelectBoxShadowSettings',
                        '#btnSelectTextSettings', '#btnSelectTextShadowSettings',
                        '#btnSelectBackgroundFolder' , '#btnSelectBackgroundImage'
                    ]);
                if (data.node && data.node.type=='naElement') {
                    var
                    regExSite = /#site([\w\d]+)$/,
                    regExApps = /#app__([\w\d]+)__([\w\d]+)$/;

                    if (
                        data.node.text.match(regExSite)

                    ) {
                        na.te.s.c.forDialogID = data.node.text.replace('#','');
                        na.te.s.c.forElements = null;
                        na.te.enableButtons([
                            '#btnAddElement', '#btnDeleteElement',
                            '#btnSelectBackgroundFolder' , '#btnSelectBackgroundImage',
                            '#btnSelectBackgroundColor', '#btnSelectBorderSettings' , '#btnSelectBoxShadowSettings',
                            '#btnSelectTextSettings', '#btnSelectTextShadowSettings'
                        ]);
                    } else if (data.node.text.match(/:before/)) {
                        na.te.s.c.forDialogID = null;
                        na.te.s.c.forElements = data.node.text;
                        na.te.enableButtons([
                            '#btnDeleteElement',
                            '#btnSelectBackgroundFolder' , '#btnSelectBackgroundImage',
                            '#btnSelectBackgroundColor', '#btnSelectBorderSettings' , '#btnSelectBoxShadowSettings',
                            '#btnSelectTextSettings', '#btnSelectTextShadowSettings'

                        ]);
                        if (data.instance.get_node(data.node.parent).text!=='main') {
                            //debugger;
                            na.te.enableButtons([
                                '#btnDeleteElement'
                            ]);
                        }
                    } else if (data.node.text.match(regExApps)) {
                        na.te.s.c.forDialogID = data.node.text.replace('#','');
                        //debugger;
                        na.te.s.c.forElements = null;
                        na.te.enableButtons([
                            '#btnSelectBackgroundFolder' , '#btnSelectBackgroundImage',
                            '#btnSelectBackgroundColor', '#btnSelectBorderSettings' , '#btnSelectBoxShadowSettings',
                            '#btnSelectTextSettings', '#btnSelectTextShadowSettings'
                        ]);
                    } else {
                        na.te.s.c.forDialogID = null;
                        na.te.s.c.forElements = data.node.text;
                        na.te.enableButtons([
                            '#btnDeleteElement',
                            '#btnSelectBackgroundFolder' , '#btnSelectBackgroundImage',
                            '#btnSelectBackgroundColor', '#btnSelectBorderSettings' , '#btnSelectBoxShadowSettings',
                            '#btnSelectTextSettings', '#btnSelectTextShadowSettings'

                        ]);
                        if (data.instance.get_node(data.node.parent).text!=='main') {
                            //debugger;
                            na.te.enableButtons([
                                '#btnDeleteElement'
                            ]);
                        }
                    }
                }
            }
        } else if (which=='selectedBackground') {
            var data = na.te.s.c.selectedBackground;

        }
    //    debugger;
    }

    applySelector  (rec) {
        if (rec && rec.type=='naElement') {
            var
            regExSite = /#site([\w\d]+)$/,
            regExApps = /#app__([\w\d]+)__([\w\d]+)$/;

            if (
                rec.text.match(regExSite)

            ) {
                //na.te.s.c.forDialogID = rec.text.match(regExSite)[1];
                //na.te.s.c.forElements = null;
            } else if (rec.text.match(regExApps)) {
                //na.te.s.c.forDialogID = rec.text.match(regExApps);
                debugger;
                //na.te.s.c.forElements = null;
            } else {
                //na.te.s.c.forDialogID = null;
                //na.te.s.c.forElements = rec.text;
                if (na.te.s.c.elementsCSS[rec.id]) {
                    //setTimeout (function () {

                        let
                        bg = $('#'+na.te.s.c.forDialogID+' > .vdBackground'),
                        bg3 = $(rec.text),
                        src = na.te.s.c.elementsCSS[rec.id].background;

                        //debugger;
                        if (src.match(/rgba\(/))
                            $(bg3).css({
                                background : src,
                                opacity : 1
                            });
                        else if (rec.text.match(/\s+\.newsApp__item__outer\s+>\s+.vdBackground\s*/)) {
                            $(bg3).css({
                                background : src.match(/url\(/)
                                    ? src.match(/url\(.*\).*%/)
                                        ? src
                                        : src.replace(')',') 0% 0% / ')
                                    : 'url("'+src+'") repeat',
                                opacity : na.te.s.c.elementsCSS[rec.id].opacity,
                                //opacity : parseInt($('#themeEditor_photoOpacity').val())/100,
                                backgroundSize : na.te.s.c.elementsCSS[rec.id].backgroundSize
                            });
                        }
                        //console.log ('t3333:'+rec.text);

                    //}, 200);
                };
            }
        }
    }

    reApplySelectorsTree  () {
        var jsonNodes = $('#themeEditor_jsTree_selectors').jstree(true).get_json('#', { flat: true });
        $.each(jsonNodes, function (i, val) {
            na.te.applySelector (val);
        });
    }

    disableAllButtons  () {
        $('#siteToolbarThemeEditor .navbar .vividButton_icon_50x50').each(function(idx,el) {
            switch (el.id) {
                case 'btnSelectSelectorSet' : break;
                default : na.site.components.buttons['#'+el.id].disable(); break;
            }
        });
        $('#siteToolbarThemeEditor .naNavBar_darkenedBG .vividButton_icon_50x50').each(function(idx,el) {
            na.site.components.buttons['#'+el.id].disable();
        });
    }

    enableButtons (buttons) {
        for (var i=0; i<buttons.length; i++) {
            na.site.components.buttons[buttons[i]].enable();
            $('img[srcpreload]').each (function (idx,el) {
                el.src = $(el).attr('srcpreload');
            })

        }
    }

    transform_siteGlobalsThemes_to_jsTree (specifier) {
        var
        themeName = na.site.globals.themeName,
        themeData = na.site.loadTheme_fetchDialogs(themeData);
        //IS THIS NECESSARY?? na.site.loadTheme_applySettings (themeData, null, false); // apply theme changes, all except .background in this case.
        na.site.globals.themes[na.site.globals.themeName] = $.extend({}, themeData);
        var
        inputData = na.site.globals.themes[themeName];

        if (!inputData) inputData = na.site.globals.themes.default;
        inputData = inputData.themeSettings;

        var
        outputData = na.te.transform_siteGlobalsThemes_to_jsTree__recurse(
            {dat:inputData,did:null},
            {dat:[],did:null},
            'Selectors', '#', 'naSelectorSet'
        );
        return outputData;
    }

    transform_siteGlobalsThemes_to_jsTree__recurse  (inputData, outputData, parentName, parentID, type) {
        var did = inputData.did;

        for (var key in inputData.dat) {
            var value = inputData.dat[key], newID = na.m.randomString();
            //if (typeof value.length!=='undefined') continue;
            if (key=='css') {
                for (var key2 in value) break;
                outputData.dat.push ({
                    id : newID,
                    parent : parentID,
                    text : 'main',
                    state : {
                        opened : true,
                        selected : (
                            'site'+parentName==na.te.s.c.forDialogID
                        )
                    },
                    type : 'naCSS'
                });
                if ('site'+parentName==na.te.s.c.forDialogID) outputData.did = newID;
                for (var divSel in value) {
                    //if (divSel.match(/\> .vdBackground/)) continue;
                    var newID2 = na.m.randomString();
                    outputData.dat.push ({
                        id : newID2,
                        parent : newID,
                        text : divSel,
                        state : {
                            opened : true
                        },
                        type : 'naElement'
                    });
                    if (divSel=='#'+na.te.s.c.forDialogID) outputData.did = newID2;
                }
            } else if (key=='Extras' || key=='Apps') {
                outputData.dat.push ({
                    id : newID,
                    parent : parentID,
                    text : key,
                    state : {
                        opened : true
                    },
                    type : type
                });
                for (var cssText in value) {
                    if (cssText.match(/\> .vdBackground/)) continue;
                    var vdata = value[cssText].css;
                    var newID2 = na.m.randomString();
                    outputData.dat.push ({
                        id : newID2,
                        parent : newID,
                        text : cssText,
                        state : {
                            opened : true
                        },
                        type : 'naCSS'
                    });
                    if (divSel=='#'+na.te.s.c.forDialogID) outputData.did = newID2;

                    for (var divSel in vdata) {
                        var newID3 = na.m.randomString();
                        outputData.dat.push ({
                            id : newID3,
                            parent : newID2,
                            text : divSel,
                            state : {
                                opened : true
                            },
                            type : 'naElement'
                        });
                        na.te.s.c.elementsCSS[newID3] = vdata[divSel];
                        if (divSel=='#'+na.te.s.c.forDialogID) outputData.did = newID3;
                    }
                }
            } else {
                outputData.dat.push ({
                    id : newID,
                    parent : parentID,
                    text : key,
                    state : {
                        opened : true
                    },
                    type : type
                });
                if (typeof value=='object') {
                    var call = na.te.transform_siteGlobalsThemes_to_jsTree__recurse (
                        {dat:value,did:outputData.did}, outputData, key, newID, type
                    );
                    $.extend (outputData.dat, call.dat)
                    if (call.did) outputData.did = call.did;
                };
            };
        };
        //if (outputData.dat.length > 50) debugger;
        return outputData;
    }

    transform_jsTree_to_siteGlobalsThemes () {
        var
        jsonNodes = $('#themeEditor_jsTree_selectors').jstree(true).get_json('#', { flat: true }),
        themeSettings = {};
        for (var i in jsonNodes) {
            var it = jsonNodes[i];
            na.te.transform_jsTree_to_siteGlobalsThemes__do (it, themeSettings);
        }
        return themeSettings;
    }

    transform_jsTree_to_siteGlobalsThemes__do  (it, themeSettings) {

        /*
         * na.site.loadTheme(), .saveTheme() and .fetchTheme() distribute and collect the settings under the
         * 'Dialogs' and 'Apps' nodes of the Theme Editor's main tree view (the selectors jstree).
         *
         * that leaves us with only the custom built selectors to pick up here,
         * to be used in na.site.saveTheme() and na.site.loadTheme()
         */

        switch (it.type) {
            /*
            case 'naSelectorSet' :
                var data = {};
                data[it.text] = {};
                themeSettings = $.extend (themeSettings, data);
                break;
            case 'naCSS' :
                var data = {};
                data[it.text] = {};
                themeSettings = $.extend (themeSettings, data);
                break;
            */
            case 'naElement' :
                var
                parent = $('#themeEditor_jsTree_selectors').jstree(true).get_node(it.parent),
                regExDialogs = /#site(.*)[\s\w\.\#\d\>]*/,
                regExApps = /#app__(.*)__(.*)$/;

                if (
                    !it.text.match(regExDialogs)
                    && !it.text.match(regExApps)
                    && !it.text.match(/Dialog/)
                    //&& !it.text.match(/Extras/)
                ) {
                    if (!themeSettings[parent.text]) themeSettings[parent.text] = { css : {} };
                    if (!themeSettings[parent.text].css) themeSettings[parent.text].css = {};
                    themeSettings[parent.text].css = $.extend (
                        themeSettings[parent.text].css, na.site.fetchTheme(it.text)
                    );
                }
                break;
        }
        return themeSettings;
    }
    
    hide  (event) {
        if (!$(this).is('.disabled')) { 
            if (!na.d.s.visibleDivs.includes('#siteContent')) na.d.s.visibleDivs.push('#siteContent');
            
            na.d.s.visibleDivs = arrayRemove (na.d.s.visibleDivs, '#siteToolbarThemeEditor');
            na.d.resize();
        }        
    }
    
    onresize () {
        var 
        t = this,
        display = $('#themeEditor_photoAlbum').css('display'),
        doc = $('#themeEditor_photoAlbum')[0].contentWindow.document;
        $('.vividScrollpane div', doc).css({width:100,height:130});
        $('.vividScrollpane div img', doc).css({width:90,height:100}).each(function(idx,el){
            el.onclick = function () {
                na.te.imageSelected(el);
            };
        });
        /*$('#themeEditor_photoOpacity')[0].oninput = function () {
            if (na.te.s.c.selectedImage) na.te.imageSelected(na.te.s.c.selectedImage);
        };
        $('#themeEditor_photoScale')[0].oninput = function () {
            na.te.s.c.scale = parseInt($('#themeEditor_photoScale').val());
            if (na.te.s.c.selectedImage) na.te.imageSelected(na.te.s.c.selectedImage);
        };*/
        $('#themeEditor_jsTree_backgrounds').css({
            width : $('#siteToolbarThemeEditor > .vividDialogContent').width(),
            height : 
                $('#siteToolbarThemeEditor > .vividDialogContent').height()
                - $('#themeEditor_jsTree_backgrounds').position().top
                + 10
        });
        
        $('.themeEditor_colorPicker').next().css ({ width : 230, zIndex : 1100  });
        //$('#siteToolbarThemeEditor label', t.el).not('.specificityCB').css ({ float : 'left' });
        
        $('.themeEditorComponent, .themeEditorComponent_containerDiv2').css({
            height : 
                $('#siteToolbarThemeEditor > .vividDialogContent').height()
                - $('.nate_dialogTitle').height() - 10
                - $('#specificitySettings').height() - 8
                - 18
        });
        /*
        $('.boxSettings_label_containerDiv, .textSettings_label_containerDiv, .textShadowSettings_label_containerDiv').css ({
            width : '60px'
        });
        */
        $('.boxSettings_input_containerDiv, .textSettings_input_containerDiv, .textShadowSettings_input_containerDiv').css ({
            //width : 'calc(100%)',
        });
            
        var psy = $('#label_themeEditor_photoScaleY')[0];
        var psyn = $(psy).position().top + $(psy).height() + na.d.g.margin;
        $('#themeEditor_photoAlbum').css({
            display : 'flex',
            //width : $('#siteToolbarThemeEditor > .vividDialogContent').width(),
            height : '100%',
                //$('#siteToolbarThemeEditor > .vividDialogContent').height()
                //- $('#themeEditor_photoAlbum').position().top,
            top : 'auto'
        }).css({display:display});
    }
    
    onclick  (el) {
        if (!el) return false;
        if (na.te.s.c.selectedButtonID) {
            var b = na.site.c.buttons['#'+na.te.s.c.selectedButtonID];
            if (b) b.deselect();
        }
        
        var b = na.site.components.buttons['#'+el.id];
        if (b) {
            na.te.settings.current.selectedButtonID = el.id;
            b.select();
            $('#'+el.id).click(event);
        }
    }
    
    whichSettingSelected  (event) {
        if (typeof event=='object') var whichSetting = $(event.currentTarget).val(); else var whichSetting = event;
        switch (whichSetting) {
            case 'selectorSet' : na.te.selectSelectorSet(event); break;
            case 'element' : na.te.selectElement(event); break;
            case 'border' : na.te.selectBorderSettings(event); break;
            case 'boxShadow' : na.te.selectBoxShadowSettings(event); break;
            case 'backgroundColor' : na.te.selectBackground_color(event); break;
            case 'backgroundFolder' : na.te.selectBackground_folder(event); break;
            case 'backgroundImage' : na.te.selectBackground_image(event); break;
            case 'text' : na.te.selectTextSettings(event); break;
            case 'textShadow' : na.te.selectTextShadowSettings(false); break;
            //case 'scrollbars' : break;
        };
    }
    
    currentPath  (node) {
        var me = na.te, s = me.settings, c = s.current;
        
        var
        path = [ ],
        n = node;
        while (n.parent!=='#') {
            path.push(n.text);
            var n2 = n;
            for (var idx in c.db) {
                var st = c.db[idx];
                if (st.id && st.id == n.parent) {
                    n = st;
                    break;
                }
            }
            if (n2 === n) {
                console.log ('ERROR : na.site.tree.currentPath(iid, ) : n2===n');
                debugger;
                break;
            }
        };
        path.push (n.text);
        path = path.reverse().join('/');
        return path;//.replace('Users/','');
        //return path; // only paths being used right now already include the username in that path (from the tree node under 'Users')
    }
    
    specificitySelected  (event) {
        var sn = $(event.currentTarget).html() || event;
        if (!sn) return false;

        na.te.s.c.specificity = null;
        na.site.globals.themeSpecificityName = null;

        for (var idx in na.site.globals.themesDBkeys)
            if (na.site.globals.themesDBkeys[idx].specificityName === sn) break;
        if (na.site.globals.themesDBkeys[idx].specificityName !== sn) idx = null;
        if (idx) {
            var s = na.te.s.c.specificity = na.site.globals.themesDBkeys[idx];
            na.site.globals.themeSpecificityName = sn;
            na.site.globals.specificityName = sn;
            if (s.themeName) na.site.globals.themeName = s.themeName;
            if (!s || (!s.role && !s.user)) {
                na.site.c.buttons['#btnDeleteSpecificity'].disable();
            } else {
                na.site.c.buttons['#btnDeleteSpecificity'].enable();
            }




            na.site.loadTheme (function () { // **POSSIBLY** NOT NEEDED
                var btn = $('#'+na.te.s.c.selectedButtonID)[0];
                if (btn) na.te.onclick(btn, false);
                debugger;
                na.site.setSpecificity();
            }, s.themeName);


            //na.site.setSiteLoginLogout();
        }
    }
    /*
    themeSelected  (event) {
        na.site.globals.themeName = $(event.currentTarget)[0].innerText;
        $('.na_themes_dropdown__themes > .vividDropDownBox_selected').html (na.site.globals.themeName);

        na.site.loadTheme (function () { // **POSSIBLY** NOT NEEDED
            var btn = $('#'+na.te.s.c.selectedButtonID)[0];
            if (btn) na.te.onclick(btn, false);
        });

        na.site.setSiteLoginLogout();
    },*/

    deleteSpecificity  (event, callback) {
        var
        fncn = 'na.te.deleteSpecificity(event, callback)',
        s = na.te.s.c.specificity,
        themeData = {};
        
        if (s.url) themeData.url = s.url;
        if (s.role) themeData.role = s.role;
        if (s.user) themeData.user = s.user;

        var
        url = '/NicerAppWebOS/businessLogic/ajax/ajax_delete_vividDialog_settings.php',
        ac = {
            type : 'POST',
            url : url,
            data : themeData,
            success  (data, ts, xhr) {
                var 
                state = History.getState(),
                url = state.url.replace(document.location.origin,'').replace('/apps/', ''),
                url2 = url.replace(document.location.origin,'').replace(document.location.host,'').replace('/apps/', '');
                
                var ac2 = {
                    type : 'GET',
                    url : '/NicerAppWebOS/businessLogic/ajax/ajax_get_pageSpecificSettings.php',
                    data : {
                        apps : url2
                    },
                    success  (data, ts, xhr) {
                        $('#cssPageSpecific, #jsPageSpecific').remove();
                        $('head').append(data);
                        setTimeout(function () {
                            na.site.loadTheme (function () {
                                var 
                                btn = $('#'+na.te.s.c.selectedButtonID)[0],
                                evt = { currentTarget : $('#specificity')[0] };
                                
                                na.te.specificitySelected(evt);
                                //na.te.onclick(btn, false);
                                var tabPage = na.te.s.c.selectedSetting;
                                na.te.whichSettingSelected(tabPage);
                                
                                if (typeof callback=='function') callback (themeData, data);
                            }); 
                        }, 250);
                    },
                    failure  (xhr, ajaxOptions, thrownError) {
                    }
                };
                //setTimeout (function() { 
                    $.ajax(ac2);
                //}, 250);
                
            },
            error  (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ac);
    }
    
    cbSelected  (event) {
        $('input.specificityCB').each(function(idx,el){ el.checked = el === event.currentTarget });
        var theme = $('.na_themes_dropdown__themes > .vividDropDownBox_selected').html();
        na.site.loadTheme(function() {
            var btn = $('#'+na.te.s.c.selectedButtonID)[0];
            if (btn) na.te.onclick(btn, false);
        }, theme);
    }
    
    makeThemesList  (themeName) {
        var 
        fncn = 'na.te.makeThemesList("'+themeName+'")',
        url = '/NicerAppWebOS/businessLogic/ajax/ajax_getThemesList.php',
        ac = {
            type : 'GET',
            url : url,
            success  (data, ts, xhr) {
                $('.na_themes_dropdown__themes > .vividDropDownBox_selector').html(data);
                var t = $('.na_themes_dropdown__themes > .vividDropDownBox_selector > div');
                for (var i=0; i<t.length; i++) {
                    if ((!themeName || themeName=='default') && $(t[i]).attr('value')==na.site.globals.themeName) { $(t[i]).addClass('selected'); break; }
                    if (themeName && $(t[i]).attr('value')==themeName) { $(t[i]).addClass('selected'); break; }
                }

                na.te.s.c.selectedThemeName = themeName || na.site.globals.themeName;
                $('#theme_'+i).html (na.te.s.c.selectedThemeName);
                
                na.site.setSiteLoginLogout();
                //setTimeout (na.site.setSpecificity,250);
            },
            error  (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ac);
    }
    themeSelected  (evt) {
        var
        //theme = $(evt.currentTarget).html();
        themeName = na.site.globals.themeName;
        //$('#themeName').val(theme);

        na.site.saveTheme(function() {
            var
            theme = $('.na_themes_dropdown__themes > .vividDropDownBox_selected').html();

            setTimeout (function(theme) {
                na.site.loadTheme(function() {
                    var btn = $('#'+na.te.s.c.selectedButtonID)[0];
                    if (btn) na.te.onclick(btn, false);

                    na.te.s.c.selectedThemeName = theme;
                    na.site.globals.themeName = theme;
                }, theme);
            }, 100, theme);
        },themeName);
    }
    deleteTheme  (event) {
        var
        fncn = 'na.te.deleteTheme(event)',
        url = '/NicerAppWebOS/businessLogic/ajax/ajax_database_deleteAllThemes_byName.php';
        var
        themeName = $('.themeItem.onfocus input')[0].value,
        ajaxCmd = {
            type : 'POST',
            url : url,
            data : {
                themeName : themeName
            },
            success  (data, textStatus, xhr) {
                if (data == 'status : Success.') {
                    $('.na_themes_dropdown__themes > .vividDropDownBox_selector > .vividScrollpane > div').each(function(idx,optEl){
                        if ($(optEl).html() === themeName) $(optEl).remove();
                    });
                    $('.themeItem input').each(function(idx,inputEl){
                        if ($(inputEl).val() === themeName) $(inputEl).parent().remove();
                    });
                } else na.site.fail(fncn+' : '+url+' : '+data, xhr, textStatus, null);
            },
            error  (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ajaxCmd);            
        
    }
    themeNameSelected  (themeID) {
        var themeName = $('#'+themeID).val();
        if (themeName === na.site.globals.themeName) return false;
        na.site.saveTheme (function() {
            na.site.globals.themeName = themeName;
            na.te.s.c.selectedThemeName = themeName;
            
            $('.themeItem').removeClass('onfocus');
            setTimeout(function() {
                //var themeName = $('.na_themes_dropdown__themes > .vividDropDownBox_selected').html(); // JUST DONT
                $('.na_themes_dropdown__themes > .vividDropDownBox_selected').html(themeName); // much better idea
                $('.themeItem').each(function(idx,ti) {
                    if ($('input', ti).val()==themeName)
                        $(ti).addClass('onfocus');
                });

                na.site.loadTheme(function() {
                    var btn = $('#'+na.te.s.c.selectedButtonID)[0];
                    if (btn) na.te.onclick(btn, false);
                }, themeName);
            }, 200);
        
        }, na.site.globals.themeName);
    }
    themeNameChanged  (themeIdx, themeNameID) {
        var
        fncn = 'na.te.themeNameChanged()',
        oldThemeName = null,
        newThemeName = $(event.currentTarget).val();
        debugger;
        $('.na_themes_dropdown__themes > .vividDropDownBox_selector > .vividScrollpane > div').each(function(idx,optEl) {
            if (themeIdx===idx) oldThemeName=$(optEl).html();
        });
        
        var
        url = '/NicerAppWebOS/businessLogic/ajax/ajax_change_themeName.php',
        ajaxCmd = {
            type : 'POST',
            url : url,
            data : {
                oldThemeName : oldThemeName,
                newThemeName : newThemeName
            },
            success  (data, ts, xhr) {
                if (data == 'status : Success.') {
                    $('.na_themes_dropdown__themes > .vividDropDownBox_selector > .vividScrollpane > div').each(function(idx,optEl){
                        if ($(optEl)[0].innerText === oldThemeName) $(optEl).html(newThemeName);
                    });
                    $('.na_themes_dropdown__themes > .vividDropDownBox_selected').html(newThemeName);
                    na.site.globals.themeName = newThemeName;
                    na.te.s.c.selectedThemeName = newThemeName;
                };                    
            },
            error  (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ajaxCmd);            
    }
    setPermissionsForTheme  (event) {
        $('.themeEditorComponent, .themeEditorComponent_containerDiv2').not('#themePermissions').fadeOut('fast');
        $('.themeEditor_colorPicker').next().fadeOut('fast');
        $('#themePermissions').fadeIn('fast', 'swing', function () {
            na.te.s.c.oldThemeNames = [];
            na.te.s.c.selectedButtonID = 'btnSetPermissionsForTheme';
            $('.themeItem').remove();
            var 
            t = $('#btnOptions_menu__themes_dropdown > .vividDropDownBox_selector > .vividScrollpane > div'),
            html = '';
            for (var i=0; i<t.length; i++) {
                var
                sel = (
                    $('#btnOptions_menu__themes_dropdown > .vividDropDownBox_selected').html() === $(t[i])[0].innerText
                    ? ' selected'
                    : ''
                );
                html += '<div id="theme_'+i+'_div" class="themeItem'+sel+'"><input id="theme_'+i+'" type="text" onclick="na.te.themeNameSelected(\'theme_'+i+'\')" onchange="na.te.themeNameChanged('+i+', \'theme_'+i+'\')" value="'+$(t[i])[0].innerText+'"/></div>';
                //if ($(t[i]).is('.selected')) $('#themeName').val($(t[i]).html());
                na.te.s.c.oldThemeNames.push ($(t[i]).html());
            }
            $('#themePermissionsControls').append(html);
        });        
    }
    addTheme  (event) {
        var 
        opt = document.createElement('div'),
        i = $('#btnOptions_menu__themes_dropdown > .vividDropDownBox_selector > .vividScrollpane > div').length;
        
        opt.id = 'div_theme_'+i;
        $(opt).html('new theme');
        $(opt).addClass('selected');
        
        $('.na_themes_dropdown__themes > .vividDropDownBox_selector > .vividScrollpane > div').removeClass('selected');
        $('.na_themes_dropdown__themes > .vividDropDownBox_selector > .vividScrollpane').append(opt);
        na.te.s.c.oldThemeNames.push (opt.text);
        
        var html = '<div id="theme_'+i+'_div" class="themeItem"><input id="theme_'+i+'" type="text" onclick="na.te.themeNameSelected(\'theme_'+i+'\')" onchange="na.te.themeNameChanged('+i+', \'theme_'+i+'\')" value="new theme"></div>';
        $('#themePermissionsControls').append(html);
        $('#theme_'+i).focus();
        
        var evt = { currentTarget : opt };
        na.te.themeSelected (evt);

        $('.na_themes_dropdown__themes > .vividDropDownBox_selected').html('new theme'); // much better idea
        $('.themeItem').removeClass('onfocus');
        $('.themeItem').each(function(idx,ti) {
            if ($('input', ti).val()=='new theme')
                $(ti).addClass('onfocus');
        });



    }
    deleteCurrentTheme_bySpecificity  (removeFromThemesList, callback) {
        var
        fncn = 'na.te.deleteTheme(removeFromThemesList, callback)';
        /*
        var opts = $('#themes')[0].options;
        for (var i=0; i<opts.length; i++) {
            if (opts[i].id === na.te.s.c.selectedTheme.id) break;
        }
        if (i < opts.length) $(opts[i]).remove();
        if (removeFromThemesList) {
            $('#theme_'+i).slideUp('normal', function() {
                $('#theme'+i).remove();
            });
        }*/

        
        var
        s = na.te.s.c.specificity,
        themeData = {   
            theme : na.te.s.c.selectedThemeName
        };
        if (s.app) themeData.app = s.app;
        if (s.url) themeData.url = s.url;
        if (s.role) themeData.role = s.role;
        if (s.user) themeData.user = s.user;
        
        var
        url = '/NicerAppWebOS/businessLogic/ajax/ajax_delete_vividDialog_settings.php',
        ac = {
            type : 'POST',
            url : url,
            data : themeData,
            success  (data, ts, xhr) {
                if (typeof callback=='function') callback (themeData, data);
            },
            error  (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ac);
    }
    onchange_themeName  (event) {
        var 
        el = event.currentTarget,
        newName = $(el).val();
        
        na.te.deleteTheme(false, function() {
            na.site.saveTheme (function(themeData,data) {
                na.te.makeThemesList(newName);
            }, newName);
        });
    }
    onchange_applicationRange  (event) {
        var 
        el = event.currentTarget,
        elVal = $(el).val();
        
        debugger;
    }
    
    cssExtract  (elID) {
        if (!elID) return {};
        if (elID.match(/\./))
            var $el = $(elID);
        else
            var $el = $('#'+elID);

        if ($el.css('boxShadow') && $el.css('boxShadow')!=='')
            var bs = $el.css('boxShadow').split(', rgb');
        else
            var bs = '2px 2px 2px 2px rgba(0,0,0,0.7)'.split(', rgb');
        if ($el.css('textShadow') && $el.css('textShadow')!=='')
            var ts = $el.css('textShadow').split(', rgb');
        else
            var ts = '2px 2px 2px rgba(0,0,0,0.7)'.split(', rgb');

        var
        b = $el.css('border');
        
        //console.log ('#'+el.id+'.boxShadow='+$(el).css('boxShadow'));
        
        // NOTE (for beginners) : re1a through re2b are regular expressions. you can test your regexs at https://regex101.com/
        var 
        re1a = /^rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d\.]+)\)\s+(\d+px)\s+(\d+px)\s+(\d+px)\s+(\d+px)\s*(\w+)*,?.*$/,
        re1b = /^rgb\((\d+),\s*(\d+),\s*(\d+)\)\s+(\d+px)\s+(\d+px)\s+(\d+px)\s*(\w+)?,?.*$/,
        re2a = /^(\d+)px\s*(\w+)\s*rgba\((\d+),\s*(\d+),\s+(\d+),\s+(\d+)\)$/,
        re2b = /^(\d+)px\s*(\w+)\s*rgb\((\d+),\s*(\d+),\s+(\d+)\)$/,
        re3a = /^rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d\.]+)\)\s+(\d+px)\s+(\d+px)\s+(\d+px)\s*(\w+)*,?.*$/,
        re3b = /^rgb\((\d+),\s*(\d+),\s*(\d+)\)\s+(\d+px)\s+(\d+px)\s*(\w+)?,?.*$/,
        m2a = typeof b !== 'string' || b==='' ? '' : b.match(re2a),
        m2b = typeof b !== 'string' || b==='' ? '' : b.match(re2b),
        boxShadow = [],
        boxShadowColor = [],
        boxShadowSliders = [],
        textShadow = [],
        textShadowColor = [],
        textShadowSliders = [],
        r = {};
        
        if (typeof $el.css('boxShadow')=='string' && $el.css('boxShadow')!=='') {
            for (var j=0; j < bs.length; j++) {
                if (j > 0) bs[j] = 'rgb'+bs[j];
                //console.log (bs[j]);
                var m1 = typeof bs[j] !== 'string' || bs[j]==='' 
                    ? bs[j] 
                    : bs[j].match(re1a) !== null
                        ? bs[j].match(re1a) 
                        : bs[j].match(re1b);
                    
                if (typeof m1==='string') sliders.push('ERROR - '+bs[j]); 
                else if (m1[8]) boxShadowSliders.push([ 
                    parseInt(m1[5].replace('px','')),
                    parseInt(m1[6].replace('px','')),
                    parseInt(m1[7].replace('px','')),
                    parseInt(m1[8].replace('px',''))
                ]); else boxShadowSliders.push([ 
                    parseInt(m1[5].replace('px','')),
                    parseInt(m1[6].replace('px','')),
                    parseInt(m1[7].replace('px',''))
                ]);

                boxShadow.push (bs[j]);
                boxShadowColor.push(
                    bs[j].match(re1a) 
                    ? 'rgba('+m1[1]+', '+m1[2]+', '+m1[3]+', '+m1[4]+')'
                    : 'rgb('+m1[1]+', '+m1[2]+', '+m1[3]+')'
                );
            };
            r.boxShadow = boxShadow;
            r.boxShadowColor = boxShadowColor;
            r.boxShadowSliders = boxShadowSliders;
        };
        
        if (typeof $el.css('textShadow')=='string' && $el.css('textShadow')!=='') {
            //debugger;
            for (var j=0; j < ts.length; j++) {
                if (j > 0) ts[j] = 'rgb'+ts[j];
                //console.log (bs[j]);
                var m1 = typeof ts[j] !== 'string' || ts[j]==='' 
                    ? ts[j] 
                    : ts[j].match(re3a) 
                        ? ts[j].match(re3a) 
                        : ts[j].match(re3b);

                if (m1===null) textShadowSliders.push ([ 2, 2, 2, 2 ])
                else if (m1[7]) textShadowSliders.push([ 
                    parseInt(m1[5].replace('px','')),
                    parseInt(m1[6].replace('px','')),
                    parseInt(m1[7].replace('px','')) 
                ])
                else if (typeof m1==='string') sliders.push('ERROR - '+ts[j])
                else textShadowSliders.push([
                    parseInt(m1[5].replace('px','')),
                    parseInt(m1[6].replace('px',''))
                ]);

                if (ts[j]=='none') textShadowColor.push( 'rgba(0,0,0,0.8)' )
                else {
                    textShadow.push (ts[j]);
                    textShadowColor.push(
                        ts[j].match(re3a)
                        ? 'rgba('+m1[1]+', '+m1[2]+', '+m1[3]+')'
                        : 'rgb('+m1[1]+', '+m1[2]+')'
                    );
                }
            };
            r.textShadow = textShadow;
            r.textShadowColor = textShadowColor;
            r.textShadowSliders = textShadowSliders;
        };
        
        if (typeof b=='string' && b!=='') {
            var borderColor =
                b.match(re2a)
                ? 'rgba('+m2a[3]+', '+m2a[4]+', '+m2a[5]+', '+m2a[6]+')'
                : b.match(re2b)
                    ? 'rgb('+m2b[3]+', '+m2b[4]+', '+m2b[5]+')'
                    : 'lime';
            r.borderColor = borderColor;
        };
            
        return r;
    }
    
    selectBorderSettings  (event) {
        var ct = $('#btnSelectBorderSettings')[0];
        if ($(ct).is('.disabled')) return false;
        na.te.onclick(ct);
        na.te.s.c.selectedSetting = 'border';
        $('.themeEditor_colorPicker').next().css ({ width : 230, zIndex : 1100 });
        var w1 = $('#siteToolbarThemeEditor .vividDialogContent').width();
        var h1 = 
            $('#siteToolbarThemeEditor .vividDialogContent').height() 
            - $('.sds_dialogTitle').outerHeight() 
            - $('#specificitySettings').outerHeight() 
            - ( 4 * $('.flexBreak').outerHeight() );
        $('.themeEditorComponent, .themeEditorComponent_containerDiv2').css({ width : w1/*, height : h1 */});
        //$('#borderSettings').children().css({ width : w1 });
        $('.themeEditorComponent, .themeEditorComponent_containerDiv2').not('#borderSettings').fadeOut('fast');
        $('.themeEditor_colorPicker').next().fadeOut('fast');
        $('#borderSettings').fadeIn('fast', 'swing');/*, function () {
            $('#borderSettings > .themeEditorComponent_containerDiv > *')
                .not('.boxSettings_label_containerDiv, #borderColorpicker, .sp-container')
                .css({width:320-135,verticalAlign:'middle',display:'inline-block'});
        });*/
        if ($.spectrum) $('#borderColorpicker').spectrum ({
            color : na.te.s.c.borderColor,
            type: "flat", 
            showAlpha : true,
            showPalette : false, 
            clickoutFiresChange : false, 
            change : na.te.borderSettingsSelected
        });
        var evt2 = { currentTarget : $('#'+na.te.s.c.forDialogID)[0] };
        na.te.borderSettingsSelected (evt2, false); //event.currentTarget === ct
    }
    borderSettingsSelected  (color) {
        if (color) na.te.s.c.borderColor = color; else color = na.te.s.c.borderColor;
        if (typeof color=='object') color = 'rgba('+color._r+', '+color._g+', '+color._b+', '+color._a+')'; // firefox bugfix

        if (na.te.s.c.forDialogID) {
            var
            bg = $('#'+na.te.s.c.forDialogID),
            newBorder = $('#borderWidth').val() + 'px ' + $('#borderType').val() + ' ' + color,
            newBorderRadius = parseInt($('#borderRadius').val());

            $(bg).css({ border: '', borderRadius: '' });
            //$(bg).css({ border : newBorder, borderRadius : newBorderRadius });
            //$('#'+na.te.s.c.forDialogID).css({borderRadius : Math.round((newBorderRadius/4)*3) });
            //$('.boxShadow', bg).css({ border : newBorder, borderRadius : newBorderRadius });
            $('#'+na.te.s.c.forDialogID+' > .vdBackground').css({ border:newBorder, borderRadius : newBorderRadius });
            $(bg).css({borderRadius:newBorderRadius});
        } else {
            var
            bg = $(na.te.s.c.forElements),
            newBorder = $('#borderWidth').val() + 'px ' + $('#borderType').val() + ' ' + color,
            newBorderRadius = parseInt($('#borderRadius').val());

            $(bg).css({ border: '', borderRadius: '' });
            //$(bg).css({ border : newBorder, borderRadius : newBorderRadius });
            //$('#'+na.te.s.c.forDialogID).css({borderRadius : Math.round((newBorderRadius/4)*3) });
            //$('.boxShadow', bg).css({ border : newBorder, borderRadius : newBorderRadius });
            $(na.te.s.c.forElements).css({ border:newBorder, borderRadius : newBorderRadius });
            debugger;
            $(na.te.s.c.forElements+' > .vdBackground').css({ borderRadius : newBorderRadius });
            //$(bg).css({borderRadius:newBorderRadius});
        }
        /*if (na.te.s.c.fireSaveTheme) */na.site.saveTheme(na.site.loadTheme);
    }
    
    selectBoxShadowSettings  (event, updateHTML) {
        if (updateHTML!==false) updateHTML = true;
        var ct = $('#btnSelectBoxShadowSettings')[0];
        if ($(ct).is('.disabled')) return false;
        na.te.onclick(ct);
        na.te.s.c.selectedSetting = 'boxShadow';
        $('.themeEditor_colorPicker').next().css ({ width : 230, zIndex : 1100  });
        var w1 = $('#siteToolbarThemeEditor .vividDialogContent').width();
        var h1 = $('#siteToolbarThemeEditor .vividDialogContent').height() - $('.nate_dialogTitle').outerHeight() - $('#specificitySettings').outerHeight() - ( 4 * $('.flexBreak').outerHeight() );
        //$('.themeEditorComponent').css({ width : w1, height : h1 });
        //$('#boxShadowSettings').children().css({ width : w1 });
        $('.themeEditorComponent, .themeEditorComponent_containerDiv2').css({ width : w1 });
        $('.themeEditorComponent, .themeEditorComponent_containerDiv2').not('#boxShadowSettings').fadeOut('fast');
        $('.themeEditor_colorPicker').next().fadeOut('fast');
        $('#boxShadowSettings').css({display:'inline-block',height:h1}).fadeIn('fast', 'swing', function () {
            if (na.te.s.c.forDialogID) {
                var
                div = $('#'+na.te.s.c.forDialogID),
                bg = $('#'+na.te.s.c.forDialogID+' > .vdBackground');
            } else {
                var
                div = $(na.te.s.c.forElements),
                bg = $(na.te.s.c.forElements);
            };
            var
            bg1 = $(bg).css('background').replace(/"/g, '\''),
            bs = $(div).css('boxShadow').split(', rgb');
            opacity = bg1.indexOf('url(')!==-1 ? bg.css('opacity') : 1,
            border = $('#borderWidth').val()+'px '+$('#borderSettings select').val()+' '+$('#borderColorpicker').val(),//na.te.s.c.borderColor,
            br = parseInt($('#borderRadius').val());
            
            for (var i=1; i<bs.length; i++) { 
                bs[i] = 'rgb'+bs[i]; 
            };
            if (updateHTML) $('.boxShadow_containerDiv').remove();
            for (let i=0; i<bs.length; i++) {
                //var html = '<div id="boxShadow_'+i+'_containerDiv" class="boxShadow_containerDiv"><div id="boxShadow_'+i+'" i="'+i+'" class="boxShadow" onclick="na.te.boxSettingsSelected(event)" style="position:relative;"><div id="boxShadow_'+i+'_bg" class="boxShadow_bg" style="border:'+border+';background:'+bg1+';opacity:'+opacity+';border-radius:'+br+'px;box-shadow:'+bs[i]+'"></div><span class="boxShadow_label" style="vertical-align:middle;text-align:center;display:table-cell;">abc XYZ</span></div></div>';
                var html = '<div id="boxShadow_'+i+'_containerDiv" class="boxShadow_containerDiv"><div id="boxShadow_'+i+'" i="'+i+'" class="boxShadow" onclick="na.te.boxSettingsSelected(event)" style="position:relative;"><div class="boxShadow_label" style="border:'+border+';background:'+bg1+';opacity:'+opacity+';border-radius:'+br+'px;box-shadow:'+bs[i]+';vertical-align:middle;text-align:center;padding:5px;margin:8px;">abc XYZ</div></div>';
                
                if (updateHTML) $('#boxShadowControls').append(html);
                
                setTimeout (function () { 
                    if (na.te.s.c.selectedBoxShadowID=='boxShadow_'+i) {
                        na.te.s.c.selectedBoxShadowID = 'boxShadow_'+i;
                        
                        var evt2 = { currentTarget : $('#'+na.te.s.c.selectedBoxShadowID)[0] };
                        na.te.boxSettingsSelected (evt2, false); //event.currentTarget === ct
                    };
                    
                    /*
                    $('#boxShadow_'+i+'_containerDiv, #boxShadow_'+i+'_bg, #boxShadow_'+i+' span').css({ 
                        width : $('#boxShadow_'+i).width(),
                        height : $('#boxShadow_'+i+' span').height()
                    });*/
                }, 10 + (i * 25));
            };
            /*
            $('#boxShadowSettings > .themeEditorComponent_containerDiv > *')
                .not('#boxShadowInset, .boxSettingsLabel_containerDiv, #boxShadowControls, #boxShadowColorpicker, .sp-container')
                .css({width:290-150,verticalAlign:'middle',display:'inline-block'});
            */

        });
    }
    boxSettingsSelected  (event, saveTheme) {
        //debugger;
        if (event.currentTarget.id!==na.te.s.c.forDialogID) na.te.s.c.selectedBoxShadowID = event.currentTarget.id;
        if (saveTheme!==false) saveTheme = true;
        
        var bc = $('#borderColorpicker').val();
        $('.boxShadow .boxShadow_bg').css ({ borderColor : 'grey' });
        $('.boxShadow_bg', event.currentTarget).css ({ borderColor : bc });
        
        $('.boxShadow_containerDiv').removeClass('selected');
        $('#'+event.currentTarget.id+'_containerDiv').addClass('selected');
        
        na.te.s.c.borderColor = $('#borderColorpicker').val();
        
        var 
        bs = $('div.boxShadow_label', event.currentTarget).css('boxShadow'),
        b = $('div.boxShadow_label', event.currentTarget).css('border');
        
        if (b && b.indexOf('none')!==-1) 
            b = $(event.currentTarget).css('borderTopWidth')+' '
            +$(event.currentTarget).css('borderTopStyle')+' '
            +$(event.currentTarget).css('borderTopColor');
        
        if (bs == 'none') {
            var
            cssExtract = {
                boxShadowSliders : [ [ 2, 2, 4, 2 ] ],
                boxShadowColor : [ '2px 2px 4px 2px rgba(0, 0, 0, 0.7)' ]
            },
            ctI = 0;
            $('#boxShadowInset')[0].checked = false;
        } else {
            if (bs && b) {
                if (bs.match('inset')) $('#boxShadowInset')[0].checked = true; else $('#boxShadowInset')[0].checked = false;

                if (na.te.s.c.forDialogID)
                    var
                    cssExtract = na.te.cssExtract(na.te.s.c.forDialogID),
                    ctI = parseInt($(event.currentTarget).attr('i'));
                else
                    var
                    cssExtract = na.te.cssExtract(na.te.s.c.forElements),
                    ctI = parseInt($(event.currentTarget).attr('i'));

                na.te.s.c.boxShadowColor = cssExtract.boxShadowColor[ctI];
                $('#boxShadowXoffset').val(cssExtract.boxShadowSliders[ctI][0]);
                $('#boxShadowYoffset').val(cssExtract.boxShadowSliders[ctI][1]);
                $('#boxShadowSpreadRadius').val(cssExtract.boxShadowSliders[ctI][2]);
                $('#boxShadowBlurRadius').val(cssExtract.boxShadowSliders[ctI][3]);
                if ($.spectrum) $('#boxShadowColorpicker').spectrum ({
                    color : na.te.s.c.boxShadowColor
                });
            }
        };
        
        na.te.s.c.boxShadowColor = cssExtract.boxShadowColor[ctI];
        if ($('#boxShadowSettings .sp-container').length > 0)
            if ($.spectrum) $('#boxShadowColorpicker').spectrum('set', na.te.s.c.boxShadowColor);
        else if ($.spectrum) $('#boxShadowColorpicker').spectrum ({
            color : na.te.s.c.boxShadowColor,
            type: "flat", 
            showAlpha : true,
            showPalette : false, 
            clickoutFiresChange : false, 
            change : na.te.boxSettingsChanged_shadowColor
        });
        
        if (saveTheme) na.te.boxSettingsChanged (na.te.s.c.boxShadowColor);
        /*
        var
        b = $('div.boxShadow_bg', event.currentTarget).css('border'),
        br = $('div.boxShadow_bg', event.currentTarget).css('borderRadius');
        if (!b) 
            b = $('div.boxShadow_bg', event.currentTarget).css('borderTopWidth')+' '
            +$('div.boxShadow_bg', event.currentTarget).css('borderTopStyle')+' '
            +$('div.boxShadow_bg', event.currentTarget).css('borderTopColor');
        
        if (b.indexOf('none')!==-1) {
            var bw = b.match(/^\d+/)[0];
            $('#borderWidth').val(parseInt(bw));
        }
        if (br!=='') {
            $('#borderRadius').val(parseInt(br));
        }*/
    }
    
    boxSettingsChanged  (color) {
        if (color) $('#'+na.te.s.c.selectedBoxShadowID)[0].style.boxShadowColor = color;
        else {
            color = $('#'+na.te.s.c.selectedBoxShadowID+' div').css('boxShadow');
            if (color && color.match('#')) color = color.match(/#.*\s/)[0];
            if (color && color.match('rgba')) color = color.match(/rgba\(.*\)/)[0];
            else if (color && color.match('rgb')) color = color.match(/rgb\(.*\)/)[0];
        };
        if (typeof color=='object') color = 'rgba('+color._r+', '+color._g+', '+color._b+', '+color._a+')'; 
        na.te.s.c.boxShadowColor = color;
        
        var
        newBoxSetting = 
            ( $('#boxShadowInset')[0].checked ? 'inset ' : '' )
            + $('#boxShadowXoffset').val() + 'px '
            + $('#boxShadowYoffset').val() + 'px '
            + $('#boxShadowSpreadRadius').val() + 'px '
            + $('#boxShadowBlurRadius').val() + 'px '
            + color;
        $('#'+na.te.s.c.selectedBoxShadowID+' > .boxShadow_label').css ({ boxShadow : newBoxSetting });
    //debugger;
        newBoxSetting = '';
        $('.boxShadow').each(function(idx,el) {
            if (newBoxSetting!=='') newBoxSetting += ', ';
            newBoxSetting += $('#'+el.id+' div').css('boxShadow');
        });
        if (na.te.s.c.forDialogID)
            $('#'+na.te.s.c.forDialogID).css ({ boxShadow : newBoxSetting });
        else
            $(na.te.s.c.forElements).css ({ boxShadow : newBoxSetting });


        na.site.saveTheme({callback:na.site.loadTheme});
        
    }
    boxSettingsChanged_shadowColor  (color) {
        na.te.boxSettingsChanged(color);
    }

    addBoxShadow  () {
        var last = 0;
        $('.boxShadow').each(function(idx,el) {
            var idx2 = parseInt(el.id.replace('boxShadow_',''));
            if (idx2 > last) last = idx2;
        });

        var
        div = $('#'+na.te.s.c.forDialogID),
        bg =  $('#'+na.te.s.c.forDialogID+' > .vdBackground'),
        bg1 = bg.css('background').replace(/\'/g, '\\\'').replace(/"/g, '\''),
        opacity = bg1.indexOf('url(')!==-1 ? bg.css('opacity') : 1,
        border = div.css('border'),
        borderRadius = bg.css('borderRadius'),
        i = last + 1,
        //html = '<div id="boxShadow_'+i+'_containerDiv" class="boxShadow_containerDiv"><div id="boxShadow_'+i+'" i="'+i+'" class="boxShadow" onclick="na.te.boxSettingsSelected(event)" style="height:1.5em;margin:5px;padding:5px;position:relative;"><div id="boxShadow_'+i+'_bg" class="boxShadow_bg" style="border:'+border+';background:'+bg1+';opacity:'+opacity+';border-radius:'+br+'px;;position:absolute;"></div><span class="boxShadow_bg" style="position:absolute;padding:5px;vertical-align:middle;text-align:center;display:table-cell;">abc XYZ</span></div></div>';
        html = '<div id="boxShadow_'+i+'_containerDiv" class="boxShadow_containerDiv"><div id="boxShadow_'+i+'" i="'+i+'" class="boxShadow" onclick="na.te.boxSettingsSelected(event)" style="position:relative;"><div class="boxShadow_label" style="border:'+border+';background:'+bg1+';opacity:'+opacity+';border-radius:'+br+'px;box-shadow:2px 2px 2px 2px rgba(0,0,0,0.8);vertical-align:middle;text-align:center;padding:5px;margin:8px;">abc XYZ</div></div>';
        
        $('#boxShadowControls').append(html);
        /*
        setTimeout (function () { 
            $('#boxShadow_'+i+'_bg, #boxShadow_'+i+' span').css({ 
                width : $('#boxShadow_'+i).width(),
                height : $('#boxShadow_'+i).height()
            });
        }, 50);*/
        
        na.te.s.c.boxSettings = $('#boxShadow_'+(last+1))[0];
        $('#boxShadowXoffset').val(2);
        $('#boxShadowYoffset').val(2);
        $('#boxShadowSpreadRadius').val(2);
        $('#boxShadowBlurRadius').val(2);

        na.te.boxSettingsChanged();
    }
    deleteBoxShadow (evt) {
        $('#'+na.te.s.c.selectedBoxShadowID+'_containerDiv').remove();
        
        $('.boxShadow_containerDiv').each(function(idx,el){
            el.id = 'boxShadow_'+idx+'_containerDiv';
            $('div.boxShadow', el)[0].id = 'boxShadow_'+idx;
            if ($('div.boxShadow_bg', el)[0])
                $('div.boxShadow_bg', el)[0].id = 'boxShadow_'+idx+'_bg';
        });
        
        na.te.s.c.selectedBoxShadowID = 'boxShadow_0';
        var evt2 = { currentTarget : $('#'+na.te.s.c.selectedBoxShadowID)[0] };
        na.te.boxSettingsSelected (evt2, false); //event.currentTarget === ct

        na.te.boxSettingsChanged();
    }
    
    selectBackground_color  (event) {
        var ct = $('#btnSelectBackgroundColor')[0];
        if ($(ct).is('.disabled')) return false;
        na.te.onclick(ct);
        na.te.s.c.selectedSetting = 'backgroundColor';
        $('.themeEditorComponent, .themeEditorComponent_containerDiv2').not('#themeEditor_backgroundColor').fadeOut('fast', function () {
            $('.themeEditor_colorPicker').next().css ({ width : 230, zIndex : 1100  });
            if ($('#themeEditor_backgroundColor').css('display')==='none')
                $('#themeEditor_backgroundColor').css({top:8,opacity:1}).fadeIn('fast', function() {
                    $('#themeEditor_backgroundColor .sp-container').fadeIn('slow', 'swing', function() {
                        if (na.te.s.c.forDialogID) {
                            var
                            bg =  $('#'+na.te.s.c.forDialogID+' > .vdBackground'),
                            bg1 = bg.css('backgroundColor');
                        } else {
                            var
                            bg = $(na.te.s.c.forElements),
                            bg1 = bg.css('backgroundColor');
                        };

                        if (bg1)
                            if ($('#themeEditor_backgroundColor  .sp-container').length > 0)
                                if ($.spectrum) $('#colorpicker').spectrum('set', bg1);
                            else if ($.spectrum) $('#colorpicker').spectrum ({
                                color : bg1, 
                                type: "flat", 
                                showAlpha : true,
                                showPalette : false, 
                                clickoutFiresChange : false, 
                                change : na.te.boxSettingsChanged_shadowColor
                            });
                    });
                });
        });
    }

    selectBackground_folder  (event) {
        var ct = $('#btnSelectBackgroundFolder')[0];
        if ($(ct).is('.disabled')) return false;
        na.te.onclick(ct);
        na.te.s.c.selectedSetting = 'backgroundFolder';
        $('.themeEditorComponent, .themeEditorComponent_containerDiv2').not('#themeEditor_jsTree_backgrounds').fadeOut('fast');
        $('.themeEditor_colorPicker').next().fadeOut('fast');
        $('#themeEditor_jsTree_backgrounds').fadeIn('fast');
        setTimeout(na.te.onresize,250);
    }
    
    selectBackground_image  (event) {
        var ct = $('#btnSelectBackgroundImage')[0];
        if ($(ct).is('.disabled')) return false;
        na.te.onclick(ct);
        na.te.s.c.selectedSetting = 'backgroundImage';

        $('.themeEditorComponent, .themeEditorComponent_containerDiv2').not('#themeEditor_photoAlbum, #themeEditor_photoAlbum_specs').fadeOut('fast');
        setTimeout (function () {
            $('.themeEditor_colorPicker').next().fadeOut('fast');
            $('#themeEditor_photoAlbum, #themeEditor_photoOpacity, #themeEditor_photoAlbum_specs').fadeIn('fast', function () {
                resizeIFrameToFitContent($('#themeEditor_photoAlbum')[0]);
            });

            setTimeout(function() {
                $('#themeEditor_photoAlbum_specs').css({
                    display : 'flex',
                    flexWrap : 'wrap',
                    boxSizing: 'border-box',
                    width : '97%'
                });
                //$('.labelthemeEditor').css ({ width : 170, flexShrink : 0, flexGrow : 0 });
                
                //$('#label_themeEditor_photoOpacity').css ({ top : 4, position : 'absolute' });
                $('#themeEditor_photoOpacity').css({
                    display : 'block',
                    width:$('#siteToolbarThemeEditor .vividDialogContent').width() - 70 - 40,
                    left : 70
                });

                //$('#label_themeEditor_photoScaleX').css ({ top : 37, position : 'absolute' });
                $('#themeEditor_photoScaleX').css({
                    display : 'block',
                    width:$('#siteToolbarThemeEditor .vividDialogContent').width() - 70 - 40,
                    left : 70
                }).val(na.te.s.c.scaleX).fadeIn('fast');
                
                //$('#label_themeEditor_photoScaleY').css ({ top : 66, position : 'absolute' });
                $('#themeEditor_photoScaleY').css({
                    display : 'block',
                    width:$('#siteToolbarThemeEditor .vividDialogContent').width() - 70 - 40,
                    left : 70
                }).val(na.te.s.c.scaleX).fadeIn('fast');
                
                setTimeout(na.te.onresize,100);
            }, 100);
        }, 100);
            
    }
    
    opacityChange  (evt) {
        var 
        bg = $(evt.currentTarget).parents('.vividDialog')[0],
        rgbaRegEx = /rgba\((\d{1,3})\,\s*(\d{1,3})\,\s*(\d{1,3})\,\s*([\d\.]+)\)(.*)/,
        rgbRegEx = /rgb\((\d{1,3})\,\s*(\d{1,3})\,\s*(\d{1,3})\)(.*)/,
        opacity = $(evt.currentTarget).val()/100;
        
        if (bg && $(bg).children('.vdBackground')[0]) bg = $(bg).children('.vdBackground');
        
        var bg1 = $(bg).css('background');        
        
        if (typeof bg1=='string' && bg1!=='' && !bg1.match('url')) {
            var bg2 = '', bg2a = bg1.match(rgbaRegEx), bg2b = bg1.match(rgbRegEx);
            if (bg2a) {
                $(bg).add('.boxShadow_bg, .textShadow_bg').css({ background : 'rgba('+bg2a[1]+', '+bg2a[2]+', '+bg2a[3]+', '+opacity+')'+bg2a[5] });
            } else {
                $(bg).add('.boxShadow_bg, .textShadow_bg').css({ background : 'rgba('+bg2b[1]+', '+bg2b[2]+', '+bg2b[3]+', '+opacity+')'+bg2b[4] });
            }
        } else { 
            $(bg).add('.boxShadow_bg, .textShadow_bg').css({ opacity : opacity });
        }
        /*if (na.te.s.c.fireSaveTheme) */na.site.saveTheme({callback:na.site.loadTheme});
    }
    
    imageSelected  (el) {
        na.te.s.c.selectedImage = el;

        let 
        bg = $('#'+na.te.s.c.forDialogID+' > .vdBackground'),
        bg2 = $(na.te.s.c.forElements+' > .vdBackground'),
        bg3 = $(na.te.s.c.forElements),
        bg4 = bg2.length > 0 ? bg2 : bg3,
        src = el.src.replace('thumbs/','');

/*
        var jsonNodes = $('#themeEditor_jsTree_selectors').jstree(true).get_json('#', { flat: true });
        $.each(jsonNodes, function (i, val) {
            if (val.text===na.te.s.c.forElements) {
debugger;
                na.te.s.c.elementsCSS[val.id].background = src;
            }
        });*/

        /*var bgSrc = $(bg).css('backgroundImage');
        bgSrc = bgSrc.replace('url("', '');
        bgSrc = bgSrc.replace('")', '');
        bgSrc = bgSrc.replace("url'", '');
        bgSrc = bgSrc.replace("')", '');*/
        var bgEl = document.createElement('img');
        bgEl.onload = function () {
            na.te.s.c.scaleX = Math.round((parseInt($('#themeEditor_photoScaleX').val()) * bgEl.naturalWidth) / 100);
            na.te.s.c.scaleY = Math.round((parseInt($('#themeEditor_photoScaleY').val()) * bgEl.naturalHeight) / 100);


            var s = na.te.s.c.selectedSelector;
            if (s && s.node && na.te.s.c.elementsCSS[s.node.id]) {
                na.te.s.c.elementsCSS[s.node.id].background = 'url("'+src+'") repeat';
                na.te.s.c.elementsCSS[s.node.id].opacity =
                    parseInt($('#themeEditor_photoOpacity').val())/100;
                na.te.s.c.elementsCSS[s.node.id].backgroundSize =
                    na.te.s.c.scaleX+'px '+na.te.s.c.scaleY+'px';
            };

            if ($('#themeEditor_photoSpecificity_dialog')[0].checked) {
                $(bg).add(bg4).css({
                    background : 'url("'+src+'") repeat', 
                    opacity : parseInt($('#themeEditor_photoOpacity').val())/100, 
                    backgroundSize : na.te.s.c.scaleX+'px '+na.te.s.c.scaleY+'px'
                });
            } else {
                na.site.backgrounds.next ('#siteBackground', na.site.globals.backgroundSearchKey, src);
            }
            /*if (na.te.s.c.fireSaveTheme) */
            setTimeout (na.site.saveTheme, 200);
        };


        bgEl.src = src;
    }
    
    selectTextSettings  () {
        var ct = $('#btnSelectTextSettings')[0];
        if ($(ct).is('.disabled')) return false;
        na.te.onclick(ct);
        na.te.s.c.selectedSetting = 'text';
        $('.themeEditor_colorPicker').next().css ({ width : 230, zIndex : 1100  }).fadeOut('fast');;
        $('.themeEditorComponent, .themeEditorComponent_containerDiv2').not('#textSettings').fadeOut('fast', function () {
            if ($('#textSettings').css('display')==='none')
                $('#textSettings').fadeIn('fast', 'swing', function () {
                    
                    na.te.s.c.textColor = $('#'+na.te.s.c.forDialogID).css('color');
                    if ($.spectrum) $('#textColorpicker').spectrum ({
                        color:na.te.s.c.textColor,
                        type: "flat", 
                        showAlpha : true,
                        showPalette : false, 
                        clickoutFiresChange : false, 
                        change : na.te.textSettingsSelected_textColor
                    });
                    var evt2 = { currentTarget : $('#textSettings')[0] };

                    setTimeout(function() {
                        //if (!na.site.c.menus['#textFontFamily'])
                        na.site.c.menus['#textFontFamily'] = new naVividMenu('textFontFamily', 'textFontFamily', function(menu) {
                        });
                    }, 500);


                    na.te.updateTextSettingsControls(evt2);

                    
                });
            
        });
    }
    
    selectTextShadowSettings  (updateHTML) {
        if (updateHTML!==false) updateHTML = true;
        var ct = $('#btnSelectTextShadowSettings')[0];
        if ($(ct).is('.disabled')) return false;
        na.te.onclick(ct);
        na.te.s.c.selectedSetting = 'textShadow';
        $('.themeEditorComponent, .themeEditorComponent_containerDiv2').not('#textShadowSettings').fadeOut('fast', function () {
        
            //$('.themeEditor_colorPicker').next().fadeOut('fast');
            if ($('#textShadowSettings').css('display')==='none')
                $('#textShadowSettings').fadeIn('fast', 'swing', function() {
                    if (na.te.s.c.forDialogID) {
                        var
                        div = $('#'+na.te.s.c.forDialogID),
                        bg =  $('#'+na.te.s.c.forDialogID+' > .vdBackground');
                    } else {
                        var
                        div = $(na.te.s.c.forElements),
                        bg =  $(na.te.s.c.forElements);
                    };
                    var
                    bg1 = bg.css('background').replace(/\'/g, '\\\'').replace(/"/g, '\''),
                    opacity = bg1.indexOf('url(') !== -1 ? bg.css('opacity') : 1,
                    border = div.css('border'),
                    br = bg.css('borderRadius'),
                    ts = $(div).css('textShadow').split(', rgb');
                    if (updateHTML) $('.textShadow_containerDiv').remove();
                    for (var i=1; i<ts.length; i++) { 
                        ts[i] = 'rgb'+ts[i]; 
                    };
                    for (let i=0; i<ts.length; i++) {
                        var
                        j = i,
                        fw = div.css('fontWeight'),
                        font = div.css('fontFamily').replace(/"/g, '\''),
                        html = '<div id="textShadow_'+j+'_containerDiv" class="textShadow_containerDiv"><div id="textShadow_'+j+'" i="'+j+'" class="textShadow" onclick="na.te.textSettingsSelected(event)" style="position:relative;"><div id="textShadow_'+j+'_bg" class="textShadow_bg" style="border:'+border+';background:'+bg1+';opacity:'+opacity+';border-radius:'+br+';font-weight:'+fw+';font-family:'+font+';vertical-align:middle;text-align:center;display:table-cell;text-shadow:'+ts[i]+';padding:10px;">abc XYZ</div></div>';
                        
                        if (updateHTML) $('#textShadowControls').append(html);
                    };

                    if (!na.te.s.c.selectedTextShadowID)
                        na.te.s.c.selectedTextShadowID = 'textShadow_0';
                    var cssExtract = na.te.cssExtract(na.te.s.c.forDialogID);
                    na.te.s.c.textShadowColor = cssExtract.textShadowColor;
                    if ($.spectrum) $('#textShadowColorpicker').spectrum ({
                        color:na.te.s.c.textShadowColor,
                        type: "flat", 
                        showPalette : false, 
                        showAlpha : true,
                        clickoutFiresChange : false, 
                        change : na.te.textSettingsSelected_textShadowColor
                    });
                    na.te.updateTextSettingsControls();
                    na.te.textSettingsSelected();
                });
        });
    }

    updateTextSettingsControls  () {
        if (na.te.s.c.forDialogID) {
            var
            el = $('#'+na.te.s.c.forDialogID),
            el2 = $('#'+na.te.s.c.forDialogID+' > .vividDialogContent'),
            el3 = $('#'+na.te.s.c.forDialogID+' td');
        } else {
            var el = el2 = el3 = $(na.te.s.c.forElements);
        };

        ts = $(el).css('textShadow').split(', rgb');
        for (var i=1; i<ts.length; i++) {
            ts[i] = 'rgb'+ts[i];
        };
        var        
        selID = parseInt(na.te.s.c.selectedTextShadowID.match(/\d+$/)[0]),
        el_ts = $(el).css('fontSize'),
        el2_ts = $(el2).css('fontSize'),
        el3_ts = $(el3).css('fontSize'),
        el_fw = $(el).css('fontWeight'),
        el2_fw = $(el2).css('fontWeight'),
        el3_fw = $(el3).css('fontWeight'),
        re1a = /^rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d\.]+)\)\s+(\-?\d+px)\s+(\-?\d+px)\s+(\-?\d+px)$/,
        re1b = /^rgb\((\d+),\s*(\d+),\s*(\d+)\)\s+(\-?\d+px)\s+(\-?\d+px)\s+(\-?\d+px)$/,
        re2a = /^rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d\.]+)\)$/,
        re2b = /^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/,
        test1a = ts[selID] ? ts[selID].match(re1a) : null,
        test1b = ts[selID] ? ts[selID].match(re1b) : null,
        test2a = $(el).css('color').match(re2a),
        test2b = $(el).css('color').match(re2b),
        newTextShadowColor = 
            test1a 
            ? 'rgba('+test1a[1]+', '+test1a[2]+', '+test1a[3]+', '+test1a[4]+')'
            : test1b
                ? 'rgb('+test1b[1]+', '+test1b[2]+', '+test1b[3]+')'
                : 'black',
        newTextColor = test2a ? test2a[0] : test2b ? test2b[0] : 'white',
        newFontFamily = 
            el3[0]
            ? $(el3).css('fontFamily')
            : el2[0]
                ? $(el2).css('fontFamily')
                : el[0]
                    ? $(el).css('fontFamily')
                    : 'ABeeZee',
        newFontFamily = newFontFamily.split(', ')[0].replace(/"/g,'');
        na.te.s.c.selectedFontFamily = newFontFamily;

        $('#textFontFamily')
            //.css({width:$('#textSettings').width() - $('#labelTextFontFamily').width() - 20 })
            .val(newFontFamily);
        $('#textSize')
            //.css({width:$('#textSettings').width() - $('#labelTextSize').width() - 20 })
            .val(typeof el3_ts == 'string' && el3_ts!=='' 
                    ? parseInt(el3_ts.replace('px','')) 
                    : typeof el2_ts == 'string' && el2_ts!==''
                        ? parseInt(el2_ts.replace('px',''))
                        : typeof el_ts == 'string' && el_ts!==''
                            ? parseInt(el_ts.replace('px'))
                            : 12
                );
        var tw = parseInt((el3_fw!=='' ? el3_fw : el2_fw!=='' ? el2_fw : el_fw!=='' ? el_fw : 500))/100;
        $('#textWeight')
            //.css({width:$('#textSettings').width() - $('#labelTextWeight').width() - 20 })
            .val (tw);
        $('#textShadowXoffset')
            .css({width:$('#textSettings').width() - $('#labelTextShadowXoffset').width() - 40 })
            .val(test1a ? parseInt(test1a[5].replace('px','')) : test1b ? parseInt(test1b[4].replace('px','')) : 2);
        $('#textShadowYoffset')
            .css({width:$('#textSettings').width() - $('#labelTextShadowYoffset').width() - 40 })
            .val(test1a ? parseInt(test1a[6].replace('px','')) : test1b ? parseInt(test1b[5].replace('px','')) : 2);
        $('#textShadowBlurRadius')
            .css({width:$('#textSettings').width() - $('#labelTextShadowBlurRadius').width() - 40 })
            .val(test1a ? parseInt(test1a[7].replace('px','')) : test1b ? parseInt(test1b[6].replace('px','')) : 4);
            
        var 
        ts = $(el).css('textShadow');
        
        if (ts == 'none') {
            var
            cssExtract = {
                textShadowSliders : [ [ 2, 2, 1 ] ],
                textShadowColor : [ 'rgba(0, 0, 0, 0.7)' ]
            },
            ctI = 0;
        } else {
            if (ts) {
                var 
                cssExtract = na.te.cssExtract(na.te.s.c.forDialogID);
                if (Object.keys(cssExtract).length===0) cssExtract = na.te.cssExtract(na.te.s.c.forElements);
                
                na.te.s.c.textShadowColor = cssExtract.boxShadowColor[0];
                //if ($.spectrum) $('#textShadowColorpicker').spectrum('set', cssExtract.textShadowColor[ctI]);
                $('#textShadowXoffset').val(cssExtract.textShadowSliders[0][0]);
                $('#textShadowYoffset').val(cssExtract.textShadowSliders[0][1]);
                $('#textShadowBlurRadius').val(cssExtract.textShadowSliders[0][2]);
            }
        };
    }

    addTextShadow  (evt) {
        var last = 0;
        $('.textShadow').each(function(idx,el) {
            var idx2 = parseInt(el.id.replace('textShadow_',''));
            if (idx2 > last) last = idx2;
        });

        if (na.te.s.c.forDialogID) {
            var
            div = $('#'+na.te.s.c.forDialogID),
            bg =  $('#'+na.te.s.c.forDialogID+' > .vdBackground');
        } else {
            var div = bg = $(na.te.s.c.forElements);
        };
        var
        bg1 = bg.css('background').replace(/\'/g, '\\\'').replace(/"/g, '\''),
        opacity = bg1.match(/url\(/) ? bg.css('opacity') : 1,
        border = div.css('border'),
        br = bg.css('borderRadius'),
        fw = div.css('fontWeight'),
        font = div.css('fontFamily'),
        j = last + 1,
        html = '<div id="textShadow_'+j+'_containerDiv" class="textShadow_containerDiv"><div id="textShadow_'+j+'" i="'+j+'" class="textShadow" onclick="na.te.textSettingsSelected(event)" style="position:relative;"><div id="textShadow_'+j+'_bg" class="textShadow_bg" style="border:'+border+';background:'+bg1+';opacity:'+opacity+';border-radius:'+br+';font-weight:'+fw+';font-family:'+font+';vertical-align:middle;text-align:center;display:table-cell;text-shadow:2px 2px 2px rgba(0,0,0,0.7);padding:5px;">abc XYZ</div></div>';

        $('#textShadowControls').append(html);

        setTimeout (function () { 
            $('#textShadow_'+j+'_containerDiv, #textShadow_'+j+', #textShadow_'+j+'_bg, #textShadow_'+j+' span').css({ 
                width : $('#textShadow_'+j).width(),
                height : $('#textShadow_'+j+' span').height()
            });

            na.te.s.c.selectedTextShadowID = 'textShadow_'+j;

            var 
            el = $('#textShadow_'+j)[0],
            evt2 = { currentTarget : el };
            
            na.te.s.c.selectedTextShadow = el;
            na.te.textSettingsSelected_updateDialog();
            na.te.textSettingsSelected(evt2,false);
            na.te.selectTextShadowSettings(false);
            na.site.saveTheme({callback:na.site.loadTheme});
        }, 100);
        
    }
    deleteTextShadow  (evt) {
        var 
        toDel = $('#'+na.te.s.c.selectedTextShadowID),
        nextSelected = toDel.next('.textShadow_containerDiv');
        if (!nextSelected[0]) nextSelected = toDel.prev('.textShadow_containerDiv');
        
        $(toDel).remove();
        na.te.s.c.selectedTextShadowID = nextSelected.id;
            na.te.textSettingsSelected_updateDialog();
            na.te.textSettingsSelected(evt2);
            na.te.selectTextShadowSettings(false);

        //na.te.updateTextSettingsControls(evt);
    }

    textSettings_changeFont  (evt, newFontFamily) {
        na.te.textSettingsSelected (evt, newFontFamily)
    }

    textSettingsSelected  (evt, newFontFamily) {
        if (
            evt
            && (
                $(evt.currentTarget).is('.textShadow')
                || $(evt.currentTarget).is('.textShadow_containerDiv')
            )
        ) na.te.s.c.selectedTextShadowID = evt.currentTarget.id;
        if (na.te.s.c.forDialogID) {
            var
            el = $('#'+na.te.s.c.forDialogID),
            el2 = $('#'+na.te.s.c.forDialogID+' > .vividDialogContent'),
            el3 = $('#'+na.te.s.c.forDialogID+' td');
        } else {
            var el = el2 = el3 = $(na.te.s.c.forElements);
        };
        var
        newTextShadow =
            $('#textShadowXoffset').val()+'px '
            +$('#textShadowYoffset').val()+'px '
            +$('#textShadowBlurRadius').val()+'px '
            +na.te.s.c.textShadowColor,
        newFontSize = $('#textSize').val(),
        newFontWeight = parseInt($('#textWeight').val()) * 100,
        newFontFamily = newFontFamily ? newFontFamily : na.te.s.c.selectedFontFamily, //$('#textFontFamily').val(),//.replace(/ /g, '+'),
        els = $('#'+na.te.s.c.selectedTextShadowID+' > div')
                .add(el).add(el2).add(el3);
        debugger;
        if (newFontFamily) na.te.s.c.selectedFontFamily = newFontFamily;

        els.css ({
            textShadow : newTextShadow,
            fontWeight : newFontWeight,
            fontSize : newFontSize+'px',
            fontFamily : newFontFamily
        });
        na.site.saveTheme({callback:na.site.loadTheme});

        $('.textShadow_containerDiv').removeClass('selected');
        $('#'+na.te.s.c.selectedTextShadowID+'_containerDiv').addClass('selected');
        //na.te.s.c.textShadowColor = cssExtract.textShadowColor[0];
        if ($('#textShadowSettings .sp-container').length > 0)
            if ($.spectrum) $('#textShadowColorpicker').spectrum('set', na.te.s.c.textShadowColor);
        else if ($.spectrum) $('#textShadowColorpicker').spectrum ({
            color : na.te.s.c.textShadowColor,
            type: "flat",
            showAlpha : true,
            showPalette : false,
            clickoutFiresChange : false,
            change : na.te.boxSettingsChanged_shadowColor
        });

        na.te.textSettingsSelected_updateDialog(evt);
    }
    
    textSettingsSelected_updateDialog  () {
        var
        el = $('#'+na.te.s.c.forDialogID),
        el2 = $('#'+na.te.s.c.forDialogID+' > .vividDialogContent, #'+na.te.s.c.forDialogID+' td'),
        newFontSize = $('#textSize').val(),
        newFontWeight = parseInt($('#textWeight').val()) * 100,
        newFontFamily = na.te.s.c.selectedFontFamily,//$('#textFontFamily').val(),//.replace(/ /g, '+'),
        newTextShadow = '';
        
        $('.textShadow')
            //.css({ fontWeight : newFontWeight, fontSize : newFontSize+'px', fontFamily : newFontFamily })
            .each(function(idx,el) {
                if (newTextShadow!=='') newTextShadow+=', ';
                newTextShadow += $('#'+el.id+' > div').css('textShadow');
            });
            
        $(el).add(el2).css({ fontWeight : newFontWeight, fontSize : newFontSize+'px', fontFamily : newFontFamily });
        $(el).add(el2).css({ textShadow : newTextShadow });
        /*if (na.te.s.c.fireSaveTheme) */na.site.saveTheme({callback:na.site.loadTheme});
        window.dispatchEvent(new Event('resize'));        
    }
    
    textSettingsSelected_textColor  (color) {
        debugger;
        if (color) na.te.s.c.textColor = color; else color = na.te.s.c.textColor;
        if (typeof color=='object') color = 'rgba('+color._r+', '+color._g+', '+color._b+', '+color._a+')'; // firefox bugfix
        if (na.te.s.c.forDialogID) {
            var
            el = $('#'+na.te.s.c.forDialogID),
            el2 = $('#'+na.te.s.c.forDialogID+' > .vividDialogContent'),
            el3 = $('#'+na.te.s.c.forDialogID+' td');
        } else {
            var el = el2 = el3 = $(na.te.s.c.forElements);
        };
        $(el).add(el2).add(el3).css ({ color : color });
        /*if (na.te.s.c.fireSaveTheme) */na.site.saveTheme({callback:na.site.loadTheme});
    }
    
    textSettingsSelected_textShadowColor  (color) {
        if (color) {
            na.te.s.c.textShadowColor = color;
            na.te.textSettingsSelected();
        }
    }
    
    textBackgroundOpacityChange  (evt) {
        var 
        fncn = 'na.te.textBackgroundOpacityChange(evt)->na.site.saveTheme(*callback*)',
        opacityValue = $('#textBackgroundOpacity').val() / 100;

        na.te.s.c.textBackgroundOpacity = opacityValue;
        $('li span, p, h1, h2, h3').css ({
            background : 'rgba(0,0,0,'+opacityValue+')'
        });
        
        na.site.saveTheme(function() {
            /*
            na.site.loadTheme (function () { // also calls ajax_get_pageSpecificSettings.php
                var
                btn = $('#'+na.te.s.c.selectedButtonID)[0],
                evt = { currentTarget : $('#specificity')[0] };

                na.te.specificitySelected(evt);
                //na.te.onclick(btn, false);
                var tabPage = na.te.s.c.selectedSetting;
                na.te.whichSettingSelected(tabPage);
            });
            */

            var
            state = History.getState(),
            url = state.url.replace(document.location.origin,'').replace('/apps/', ''),
            url2 = url.replace(document.location.origin,'').replace(document.location.host,'').replace('/apps/', ''),
            url3 = '/NicerAppWebOS/businessLogic/ajax/ajax_get_pageSpecificSettings.php',
            ac2 = {
                type : 'GET',
                url : url3,
                data : {
                    apps : url2
                },
                success  (data, ts, xhr) {
                    $('#cssPageSpecific, #jsPageSpecific').remove();
                    $('head').append(data);
                    setTimeout(function () {
                        na.site.loadTheme (function () {
                            var 
                            btn = $('#'+na.te.s.c.selectedButtonID)[0],
                            evt = { currentTarget : $('#specificity')[0] };
                            
                            na.te.specificitySelected(evt);
                            //na.te.onclick(btn, false);
                            var tabPage = na.te.s.c.selectedSetting;
                            na.te.whichSettingSelected(tabPage);
                        }); 
                    }, 250);
                },
                error  (xhr, textStatus, errorThrown) {
                    na.site.ajaxFail(fncn, url3, xhr, textStatus, errorThrown);
                }                
            };
            //setTimeout (function() { 
                $.ajax(ac2);
            //}, 250);
        });
        
    }

    selectSelectorSet  (event) {
        var ct = $('#btnSelectSelectorSet')[0];
        if ($(ct).is('.disabled')) return false;
        na.te.onclick(ct);
        na.te.s.c.selectedSetting = 'selectorSet';
        var w1 = $('#siteToolbarThemeEditor .vividDialogContent').width() - 30;
        var h1 =
            $('#siteToolbarThemeEditor .vividDialogContent').height()
            - $('.sds_dialogTitle').outerHeight()
            - $('#specificitySettings').outerHeight()
            - ( 4 * $('.flexBreak').outerHeight() );
        $('.themeEditorComponent, .themeEditorComponent_containerDiv2').css({ width : w1/*, height : h1 */});
        //$('#borderSettings').children().css({ width : w1 });
        $('.themeEditorComponent, .themeEditorComponent_containerDiv2').not('#nate_selectorSet').fadeOut('fast');
        $('#nate_selectorSet').add('#themeEditor_jsTree_selectors').fadeIn('fast', 'swing');/*, function () {
            $('#borderSettings > .themeEditorComponent_containerDiv > *')
                .not('.boxSettings_label_containerDiv, #borderColorpicker, .sp-container')
                .css({width:320-135,verticalAlign:'middle',display:'inline-block'});
        });*/
        var evt2 = { currentTarget : $('#'+na.te.s.c.forDialogID)[0] };
        na.te.selectorSettingsSelected (evt2, false); //event.currentTarget === ct
    }

    selectorSettingsSelected  (evt, doe) {
    }

    onclick_btnAddGraphics  (event) {
        var newNodeID = na.m.randomString();
        $('#themeEditor_jsTree_selectors').jstree().create_node(na.te.s.c.selectedSelector.node.id, {
            id : newNodeID,
            text : 'New Graphics',
            type : 'naCSS'
        }, 'last');
        $('#themeEditor_jsTree_selectors').jstree('deselect_all').jstree('select_node', newNodeID);
        $('#themeEditor_jsTree_selectors').jstree(true).edit(na.te.s.c.selectedSelector.node);
    }

    btnAddElement_clickElement  (evt) {
        //works just fine :
        if (evt) na.m.log (100,
            '\n#'+evt.target.id+'.'+evt.target.className.replace(' ', '.')+'\n'
            +'#'+evt.currentTarget.id+'.'+evt.currentTarget.className.replace(' ', '.')+'\n',
                           false
        );
        if (
            evt.target.tagName!==evt.currentTarget.tagName
            || evt.target.id!==evt.currentTarget.id
            || evt.target.className!==evt.currentTarget.className
        ) {
            if (
                !na.te.s.c.pickedElement
                || (
                    na.te.s.c.pickedElement.length>0
                    && na.te.s.c.lastPickedElement!==evt.target
                )
            ) {
                na.te.s.c.pickedElement = [ { evt : $.extend({},evt) } ];
                na.te.s.c.lastPickedElement = evt.target;
            } else na.te.s.c.pickedElement.push ({ evt : $.extend({},evt) });
            evt.preventDefault();
            debugger;
        } else {
            na.te.s.c.pickedElement.push ({ evt : $.extend({},evt) });
            na.te.s.c.lastPickedElement = evt.target;
            var msg = '';
            $('#siteToolbarThemeEditor__elementPicker').html('<div class="vividListSelector vividScrollpane"></div>').delay(50);
            for (var i=0; i<na.te.s.c.pickedElement.length; i++) {
                var ev = na.te.s.c.pickedElement[i].evt;
                msg +=
                //'\n#'+ev.target.id+'.'+ev.target.className.replace(' ', '.')+'\n'
                '\n'+ev.currentTarget.tagName+'#'+ev.currentTarget.id+'.'+ev.currentTarget.className.replace(' ', '.')+'\n';
                var itemHTML = '<div class="vividButton" style="display:inline-block;width:fit-content;position:relative;z-index:900000">'+ev.currentTarget.tagName+'</div><div class="vividButton" style="display:inline-block;width:fit-content;position:relative;"><div class="vdBackground"></div><span style="opacity:1">#'+ev.currentTarget.id+'</span></div><div class="vividButton" style="display:inline-block;width:fit-content;position:relative;"><div class="vdBackground"></div><span style="opacity:1">.'+ev.currentTarget.className.replace(' ', '</span></div><div class="vividButton" style="display:inline-block;width:fit-content;position:relative;"><div class="vdBackground"></div><span style="opacity:1">.')+'</div>';
                var divEl = document.createElement('div');
                $(divEl).html(itemHTML);
                $('.vividButton4, .vividButton, .vividButton_icon_50x50_siteTop, .vividButton_icon_50x50', divEl).each(function(idx,el){
                    if (na.site.c.buttons['#'+el.id]) delete na.site.c.buttons['#'+el.id];
                    if (!na.site.c.buttons['#'+el.id]) {
                        na.site.c.buttons['#'+el.id] = new vividUserInterface_2D_button(el);
                        el.addEventListener ('click', na.te.btnAddElement_clickSelector, {capture:true});
                    };
                });
                $('#siteToolbarThemeEditor__elementPicker > .vividListSelector').append(divEl);
            }

            var itemHTML = '<div class="vividButton btnSave" style="display:inline-block;width:fit-content;position:relative;z-index:900000">Save</div>';
            var divEl = document.createElement('div');
            $(divEl).html(itemHTML);
            $('.vividButton', divEl).each(function(idx,btnEl){
                btnEl.addEventListener ('click', function() {
                    na.te.s.c.addingElements = false;
                    $('#siteToolbarThemeEditor__elementPicker').fadeOut('normal');
                    $('div, p, span, li, ol, ul, h1, h2, h3, h4').css({cursor:'inherit'}).each (function(){this.removeEventListener('click',na.te.btnAddElement_clickElement)});
                } )
            });
            $('#siteToolbarThemeEditor__elementPicker > .vividListSelector').append(divEl);

            na.m.log (110, msg);
            var br = evt.target.getBoundingClientRect();
            $('#siteToolbarThemeEditor__elementPicker').css({position:'absolute',left:br.left,top:br.top+br.height,zIndex:900000}).fadeIn('slow');
            evt.preventDefault();

            na.te.s.c.newElementID = na.m.randomString();
            $('#themeEditor_jsTree_selectors').jstree().create_node(na.te.s.c.selectedSelector.node, {
                id : na.te.s.c.newElementID,
                text : 'New Element',
                type : 'naElement',
            }, 'last');
            na.te.s.c.elementsCSS[na.te.s.c.newElementID] = {
                background : $(ev.currentTarget).css('background')
            };

            //$('#themeEditor_jsTree_selectors').jstree('deselect_all').jstree('select_node', na.te.s.c.newElementID);

        }
    }

    onclick_btnAddElement  () {
        if ($('div, p, span, li, ol, ul, h1, h2, h3, h4').css('cursor').match(/grab/)) {
            $('div, p, span, li, ol, ul, h1, h2, h3, h4').css({cursor:'inherit'}).each (function(){this.removeEventListener('click',na.te.btnAddElement_clickElement)});
        } else {
            na.te.s.c.addingElements = true;
            $('div, p, span, li, ol, ul, h1, h2, h3, h4').css({cursor:'url(/siteMedia/btnSettings2.32x32.png) 16 16, grab'}).each (function(idx,el) { this.addEventListener('click',na.te.btnAddElement_clickElement,{capture:true})});
        }
    }

    btnAddElement_clickSelector  (event) {
        event.target.parentNode.this.select();

        // edit the treeview node
        var selector = '';
        $('#siteToolbarThemeEditor__elementPicker > .vividListSelector > div').each(function(idx,divEl) {
            var hitsOnThisLine = false;
            var selectorPart = '';
            $('.vividButton', divEl).each(function(idx2,btnEl) {
                if ($(btnEl).is('.selected')) {
                    hitsOnThisLine = true;
                    selectorPart += btnEl.innerText;
                }
            });
            if (hitsOnThisLine) {
                if ( na.te.s.c.lastLineWithHits == idx - 1) selector += ' > ';
                na.te.s.c.lastLineWithHits = idx;
            } else selector += ' ';
            selector += selectorPart;
        });
        $('#themeEditor_jsTree_selectors').jstree().rename_node (na.te.s.c.newElementID, selector);
    }

    onclick_btnDeleteGraphics  (evt) {
        na.te.deleteElement(evt);
    }

    onclick_btnDeleteElement  (evt) {
        na.te.deleteElement(evt);
    }

    deleteElement  (nit) {
        if (nit.currentTarget) nit = na.te.s.c.selectedSelector.node;
        $(nit.text).css ({
            color : '',
            background : ''
        });

        var jsonNodes =
            $('#themeEditor_jsTree_selectors')
            .jstree(true)
            .get_json(na.te.s.c.selectedSelector.node.id, { flat: true });

        var node =
            $('#themeEditor_jsTree_selectors')
            .jstree()
            .get_node(na.te.s.c.selectedSelector.node.id);

        $('#themeEditor_jsTree_selectors')
            .jstree('delete_node', node);

        na.site.saveTheme ({callback:na.site.loadTheme});
    }

};
