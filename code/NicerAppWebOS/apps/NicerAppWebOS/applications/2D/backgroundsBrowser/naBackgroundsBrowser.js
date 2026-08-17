var naBackgroundsBrowser = na.bb = {
    settings : {

    },
    view : function (data) {
        na.bb.s.data = logData;
        na.m.waitForCondition ('naLog.view() : na.m.desktopIdle()?', na.m.desktopIdle, function() {
            var dat = na.bb.s.data, html = '';
            for (var i=0; i<dat.length; i++) {
                var dit = dat[i];
                debugger;
            }
            $('#siteContent > .vividDialogContent').append(html);
            na.desktop.settings.visibleDivs.push ('#siteToolbarLeft');
            na.desktop.resize();
            naLog.settings.intervalReload = setInterval(function() {
                naLog.reload();
            }, 10 * 60 * 1000)
            //debugger;
        }, 100);
    },
    process_msg : function (msg, dit) {
        var r = '', prefix1 = 'Starting bootup process for ', prefix2 = /Background set to "(.*?)";\s(.*)/, m = [];
        if (msg.indexOf(prefix1)===0) {
            r = { msg : prefix1, documentLocation : JSON.parse(msg.replace(prefix1,'')), ipinfo : dit.ipinfo[0].ip_info, 'ipinfo count' : dit.ipinfo.length}
        } else if (m = msg.match(prefix2)) {
            r = { msg : msg, onclickHTML : na.site.displayWallpaper(m[2])};
        } else r = msg;
        return r;
    },
    reload : function (evt,begin,end) {
        var
        url = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/logs/ajax_siteContent.php',
        dat = {

        },
        ac = {
            type : 'GET',
            url : url,
            data : dat,
            success : function (data, ts, xhr) {
                $('#siteContent .vividDialogContent').html(data);
            },
            error : function (xhr, textStatus, errorThrown) {
            }
        };
        $.ajax(ac);
    }
};
