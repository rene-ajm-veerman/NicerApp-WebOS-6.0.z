na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/2D/3rd-party-site.wikipedia.org'] = na.wiki = {
    settings : {
        loadedIn : {
            '#siteContent' : {
                onload : function (settings) {
                    setTimeout (function() {
                        na.wiki.onload(settings);
                    }, 1000);
                }
            }
        }
    },

    onload : function (settings) {
        $('iframe').contents().find('.mw-parser-output').each(function(idx,el) {
            if ($('.infobox', el).length>0) $('p, li',el).css({width:'calc(100% - '+($('.infobox', el).width()+20)+'px)'});
        });

    },

    followLink : function (mangledURL) {
        if (mangledURL=='frontpage') mangledURL = 'https://wikipedia.org/';
        else mangledURL = 'https://' + mangledURL;
        window.open(mangledURL, '_blank').focus();
    }
}
