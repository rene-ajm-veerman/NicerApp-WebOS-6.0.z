if (!na.apps) na.apps = {};
//na.m.waitForCondition ( 'na.m.desktopInitialized() for api.comments.v1.0.0', na.m.desktopInitialized, function () {
        
//na.analytics.logMetaEvent ('api.comments.v1.0.0 : init-stage-1');

delete na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/userInterfaces/comments'];
na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/userInterfaces/comments'] = na.comments = {

    globals : {
        about : {
            version : '1.0.0'
        },
        baseURL : '/NicerAppWebOS/apps/NicerAppWebOS/userInterfaces/comments',
        jsMe : 'na.apps.loaded[\'api.comments.v1.0.0\']'
    },

    settings : {
        config : false,
        perPage : 50,
        showSpeed : 'normal',
        loadedIn : {
            '#siteContent' : {
                onload : function () {
                    na.site.settings.current.app = 'api.comments.v1.0.0';

                    setTimeout (function() {
                        $('#siteContent .vividButton_icon_50x50').each(function(idx,el){
                            if (!na.site.settings.buttons['#'+el.id]) na.site.settings.buttons['#'+el.id] = new naVividButton(el);
                        });

                        $('#siteContent img[srcPreload]').each(function(idx,el2) {
                            na.site.settings.current.preLoad++;
                            //$(el2).attr('onload', 'na.site.settings.current.srcPreload--; if (na.site.settings.current.preLoad===0) na.desktop.init();');
                            if (!el2.src || el2.src==='') {
                                $(el2).attr('src', $(el2).attr('srcPreload'));
                                $(el2).removeAttr('srcPreload');
                            }
                        });

                        na.comments.onload_loadLibraries();
                    }, 1000);
                },
                ondestroy : function () {
                    $('#siteContent .vividButton_icon_50x50').each(function(idx,el){
                        if (na.site.settings.buttons['#'+el.id]) delete na.site.settings.buttons['#'+el.id];
                    });
                    
                    setTimeout (function() {
                        delete na.apps.loaded['api.comments.v1.0.0'];
                    }, 100);
                }
            }
        }
    },

    onload_loadLibraries : function () {
        var
        fncn = 'na.comments.onload_loadLibraries()',
        url = na.comments.globals.baseURL + '/ajax_loadLibraries.php',
        ac = {
            type : 'GET',
            url : url,
            success : function (data, ts, xhr) {
                $('HEAD').append(data);
            },
            error : function (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }
        };
        $.ajax (ac);
    },

    onload_editorLoaded : function () {
    }
}
//}, 100);
