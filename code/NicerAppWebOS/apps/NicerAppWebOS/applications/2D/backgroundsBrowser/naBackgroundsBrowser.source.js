var naBackgroundsBrowser = na.bb = {
    settings : {

    },
    view : function (data) {
        na.m.waitForCondition ('naLog.view() : na.m.desktopIdle()?', function () {
            var r =
                na.m.desktopIdle()
                && na.backgrounds
                && na.backgrounds.data;
            return r;
        }, function() {
            na.bb.s.data = na.backgrounds.data;
            na.bb.s.list = {};
            var
            dat = na.bb.s.data,
            jsOnChange = 'na.bb.viewTags($("#naBackgroundsBrowser_search").val());',
            html = '<div style="margin:15px;"><div style="height:60px;">&nbsp;</div><form id="naBackgroundsBrowser_form"><input id="naBackgroundsBrowser_search" type="text" value="4k" oninput=\''+jsOnChange+'\'/></form><div class="saitToolbarLeft"></div></div>',
            html2 = '';
            for (var i=0; i<dat[0].files.length; i++) {
                var dit = dat[0].files[i];
                for (var rp in dit) break;
                var
                rps = rp.replace(/\..*?$/,'').replace(/\//g,' ').replace(/_/g,' ').replace(/-/g,' ').split(' '),
                ext = rp.match(/\.(.*?)$/),
                ext = ext && ext[1] ? ext[1].toLowerCase() : '',
                v = '/'+dat[0].root+'/'+rp;
                if (ext=='jpg' || ext=='jpeg' || ext=='gif' || ext=='png')
                    for (var j=0; j<rps.length; j++) {
                        var k = rps[j].toLowerCase().trim();
                        na.bb.s.list[ k ] = v;
                    };
            };
            Object.keys(na.bb.s.list)
                .sort()
                .forEach(function(k, i) {
                    k = k.replace('(','\\(').replace(')','\\)');
                    if (
                        k!=='4k'
                    ) return;
                    var v = na.bb.s.list[k],
                    jsOnClick = 'na.bb.viewTag("'+k+'");';
                    html2 += '<span class="naBackgroundsBrowser_keyword" onclick=\''+jsOnClick+'\'>'+k+'</span> ';
                });
            $('#siteToolbarLeft > .vividDialogContent').html('').append(html).css({width:$(window).width()/3});
            $('.saitToolbarLeft').html('').append(html2);
            na.desktop.settings.visibleDivs.push ('#siteToolbarLeft');
            na.desktop.resize();
        }, 100);
    },

    viewTags : function (searchVal) {
        var
        sv = searchVal;
        jsOnChange = 'na.bb.viewTags($("#naBackgroundsBrowser_search").val());',
        html = '<div style="margin:15px;"><div style="height:60px;">&nbsp;</div><form id="naBackgroundsBrowser_form"><input id="naBackgroundsBrowser_search" type="text" value="'+sv+'" oninput=\''+jsOnChange+'\'/></form><div class="saitToolbarLeft"></div></div>',
        html2 = '';
        Object.keys(na.bb.s.list)
            .sort()
            .forEach(function(k, i) {
                k = k.replace('(','\\(').replace(')','\\)');
                var
                v = na.bb.s.list[k],
                vt = typeof v!=='string'?'':v.replace(/\..*?$/,'').replace(/\//g,' ').replace(/_/g,' ').replace(/-/g,' '),
                jsOnClick = 'na.bb.viewTag("'+k+'");';

                if (
                    typeof k=='string'
                    && k!==''
                    && !(searchVal==='' || searchVal.match(k) || vt.match(' '+searchVal) || vt.match(searchVal+' '))
                    || (
                        k.match(/\d+/)
                    )
                ) return;
                html2 += '<span class="naBackgroundsBrowser_keyword" onclick=\''+jsOnClick+'\'>'+k+'</span> ';
            });
        $('.saitToolbarLeft').html('').append(html2);
    },

    viewTag : function (k) {
        var dat = na.bb.s.data, html = '';
        $('.naBackgroundsBrowser_keyword').removeClass('shown');
        $(event.currentTarget).addClass('shown');
        for (var i=0; i<dat[0].files.length; i++) {
            var dit = dat[0].files[i];
            for (var rp in dit) break;
            if (rp.indexOf(k)!==-1) {
                var
                v = document.location.origin+dat[0].root+'/'+rp,
                jsOnClick = 'na.bg.next("#siteBackground", null, "'+v+'");';
                html += '<img src="'+v+'" class="naBackgroundsBrowser_img" onclick=\''+jsOnClick+'\' title="'+v+'"/>'
            }
        }
        $('#siteContent .saitContent').html('').append(html);
        na.site.startUIvisuals();
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
na.bb.s = na.bb.settings;
