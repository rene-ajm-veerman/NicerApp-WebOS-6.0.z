var naLogData = {};
var naLog = {
    settings : {

    },
    view : function (logData, view) {
        if (typeof logData=='string') naLog.data = JSON.parse(logData); else naLog.data = logData;
        naLog.dataByIP = {};
        naLog.dataByURL = {};
        naLog.dataByCountry = {};
        naLog.dataByDate = {};
        na.m.waitForCondition ('naLog.view() : na.m.desktopIdle()?', na.m.desktopIdle, function() {
            var
            dat = naLog.data,
            d2 = naLog.dataByIP,
            d4 = naLog.dataByCountry,
            d5 = naLog.dataByDate,
            html = '',
            html2 =
                '<h1>NicerApp WebOS Logs for '+na.site.globals.domain+'</h1>';


            switch (view) {
                case 'stats':
                case 'mostVisited':
                case 'details':
                    break;
                default:
                    view = 'stats';
                    break;
            };

            dat.sort (function (a,b) {
                var
                    c = a._id.split(' '),
                    d = b._id.split(' '),
                    c1 = new Date(c[1]+' '+c[2]).getTime()/1000 + (parseInt(c[4].replace('m',''))*60),
                    d1 = new Date(d[1]+' '+d[2]).getTime()/1000 + (parseInt(d[4].replace('m',''))*60);
                a.t = c1;
                b.t = d1;
                return d1 - c1;
            });
            for (var i=0; i<dat.length; i++) {
                var
                dit = dat[i],
                date = na.m.dateObj_toDateString(
                    new Date(parseInt(dit.millisecondsSinceEpoch))
                ).match(/\d\d\d\d-\d\d-\d\d/)[0]; // remove that 'match' to get highly detailed time info.


                dit.msgProcessed = naLog.process_msg (dit.msg, dit);

                if (typeof dit.stacktrace=='string')
                    dit.stacktrace = '<pre>'+dit.stacktrace.replace('\\n','\n')+'</pre>';


                if (typeof dit.ipinfo=='string') {
                    dit.ipinfo = JSON.parse(dit.ipinfo);
                }
                    if (dit && dit.ipinfo && !d2[dit.ip]) d2[dit.ip] = {
                        millisecondsSinceEpoch : dit.millisecondsSinceEpoch,
                        numInits : 0,
                        numPageLoads : 0,
                        numContentLoads : 0,
                        loc : dit.ipinfo.city+', '+dit.ipinfo.region+', '+dit.ipinfo.country
                    };
                    var d2ip = d2[dit.ip];
                    if (dit && dit.ipinfo && dit.ipinfo.country) {
                        if (!d4[dit.ipinfo.country]) d4[dit.ipinfo.country] = {
                            numContentLoads : 0
                        };
                        var d4tld = d4[dit.ipinfo.country];
                    }
                    if (dit.msgProcessed.documentLocation) {
                        var hr = dit.msgProcessed.documentLocation;
                        if (!naLog.dataByURL[hr]) naLog.dataByURL[hr] = {
                            numContentLoads : 0
                        }
                        var d2hr = naLog.dataByURL[hr];
                    }
                    if (!d5[date]) d5[date] = {
                        numContentLoads : 0
                    }

                    if (dit.msg.match(/Fully started/)) {
                        if (dit.msgProcessed.documentLocation) d2hr.numContentLoads++;
                        d2ip.numPageLoads++;
                        d2ip.numInits++;
                        d4tld.numContentLoads++;
                        d5[date].numContentLoads++;
                    };
                    if (dit.msg.match(/na.site.stateChange/)) d2ip.numContentLoads++;
                    if (
                        dit.msg.match(/noPushState/)
                        && !dit.msg.match(/javascript:/i)
                    ) {
                        d2ip.numContentLoads++;
                        d4tld.numContentLoads++;
                        d5[date].numContentLoads++;


                    };
                    if (dit.msgProcessed.documentLocation) d2hr.numContentLoads++;

                //};

                if (view=='details' && dit && dit.t) {
                    var dto = new Date(dit.t * 1000),
                    dt = dto.format("yyyy-mm-dd HH:MM:ss.l");

                    if (dto > (new Date().getTime()-8*3600*1000)) {

                        html +=
                            '<div class="naIPlog_entry '+dit.htmlClasses+'">';
                        if (dit.msgProcessed && dit.msgProcessed.onclickHTML) {

                            html +=
                                '<span class="naIPlog_header2a">'
                                    +'<span class="naIPlog_millisecondsSinceEpoch">'+dt+'</span> '
                                    +'<span class="naIPlog_timezoneOffset">'+dit.dateTZ+'m</span> '
                                    +'<span class="naIPlog_address">'+dit.ip+'</span>'
                                    +'<span id="naIPlog_msg__'+dit.millisecondsSinceEpoch+'" class="naIPlog_backgroundSetTo" onclick="'+dit.msgProcessed.onclickHTML+'">'+dit.msgProcessed.msg+'</span>'
                                    +'<span class="naIPlog_referrer">referrer : '+dit.referrer+'</span> '
                                +'</span><br>';

                        } else {
                            var
                            dt = new Date(parseInt(dit.t*1000)),
                            dt = dt.format("yyyy-mm-dd HH:MM:ss.l"),
                            info3 = { referrer : dit.referrer, stacktrace : dit.stacktrace, ipinfo : dit.ipinfo };

                            if (dit.msg.match('Fully started for')) {
                                try {
                                    var
                                    x = dit.msg.match(/href=\\"(https:\/\/.*?)\\"/),
                                    x1 = x[1].replace(/https:\/\/.*?\/view\//,''),
                                    jsonStr = na.m.decode_base64_url(x1),
                                    json = JSON.parse(jsonStr);

                                    for (var app in json) break;

                                    //if (typeof json=='object') dit.msg = dit.msg.replace(new RegExp('[^"]'+x[1]+'[^"]'), '>'+JSON.stringify(json)+'<');
                                    if (typeof json=='object') dit.msg = dit.msg.replace(new RegExp('[^"]'+x[1]+'[^"]'), '>'+app+'<');


                                    var
                                    dt = new Date(parseInt(dit.t*1000)),
                                    dt = dt.format("yyyy-mm-dd HH:MM:ss.l"),
                                    info3 = { referrer : dit.referrer, target : json, stacktrace : dit.stacktrace, ipinfo : dit.ipinfo };
                                    //dit.msg = 'NicerApp WebOS Fully started.';
                                } catch (e) {
                                }
                            } else {

                                if (!dit.msg) dit.msg = 'NicerApp WebOS Fully started.';

                                var
                                dt = new Date(parseInt(dit.t*1000)),
                                dt = dt.format("yyyy-mm-dd HH:MM:ss.l"),
                                info3 = { referrer : dit.referrer, stacktrace : dit.stacktrace, ipinfo : dit.ipinfo };

                                if (!dit.msg) dit.msg = 'NicerApp WebOS Fully started.';
                            }

                            html +=
                            // '<span class="naIPlog_header2">'
                            //     +'<span class="naIPlog_millisecondsSinceEpoch">'+dt+'</span> '
                            //     +'<span class="naIPlog_timezoneOffset">'+dit.dateTZ+'m</span> '
                            //     +'<span class="naIPlog_address">'+dit.ip+'</span> '
                            //     +(dit.ipinfo
                            //         ?'<span class="naIPlog_ipinfo">'+dit.ipinfo.country+', '+dit.ipinfo.region+', '+dit.ipinfo.city+'</span>'
                            //         :''
                            //     )+'</span><br>'

                            '<span id="naIPlog_msg__'+dit.millisecondsSinceEpoch+'"></span>'
                            +'<script type="text/javascript" language="javascript">'
                            +'setTimeout(function() {'
                            +'var hms_tst_js = { info : '+JSON.stringify(info3)+'};'
                            +'hm (hms_tst_js, "<div class=\\"naIPlog_header\\">'+dt+', '+dit.msg+' <span class=\\"naIPlog_address\\">'+dit.ip+'</span> <span class=\\"naIPlog_origin\\">'+d2ip.loc+'</span></div>", { htmlID : "naIPlog_msg__'+dit.millisecondsSinceEpoch+'", fastInit : true, header : \'minimal\' })},500);</script></span>';

                        }
                        html +=
                            //'<pre class="naIPlog_stacktrace">'+dit.stacktrace+'</pre>'
                            '</div>';
                    }
                }
            }

            // var c1 = 'uneven';
            // for (var ahr in naLog.dataByURL) {
            //     var d2hr = naLog.dataByURL[ahr];
            //     c1 = c1 == 'even' ? 'uneven' : 'even';
            //     html2 += '<div class="'+c1+'"><div><div>'+ahr+'</div><div title="Number of content loads">'+d2hr.numContentLoads+'</div></div></div>';
            // }

            naLog.dataByDate = d5 = Object.keys(d5).sort().reduce((r, k) => (r[k] = d5[k], r), {});


            if (view=='stats') {
                html2 += '<div style="height:500px"><canvas id="viewsByDate"></canvas></div>';
                html2 += '<div style="height:500px"><canvas id="viewsByCountry"></canvas></div>';
            }
            //html2 += '<div style="height:500px"><canvas id="viewsByPage"></canvas></div>';
            if (view=='mostVisited') html2 += `
            <div id="mostVisitedContainer" style="margin-top: 30px;">
            <h2 style="margin-bottom:20px;">🔥 Most Visited Links</h2>
            <div id="mostVisitedList" class="vividScrollpane" style="max-height: calc(100% - 40px); overflow-y: auto;"></div>
            </div>
            `;
            /*html2 += '<div class="naIPlog_header" style="clear:both;height:fit-content;display:flex;flex-wrap:wrap;">';
            var c1 = 'uneven';
            for (var aip in d2) {
                var dip = d2[aip];
                c1 = c1 == 'even' ? 'uneven' : 'even';
                html2 += '<div class="'+c1+'"><div><div title="IP" alt="IP"><a href="javascript:na.site.scrollContent(event,\'#naIPlog_msg__\'+na.log.dataByIP[\''+aip+'\'].millisecondsSinceEpoch)">'+aip+'</a></div><div title="Number of initializations" alt="Number of initializations">'+dip.numInits+'</div><div title="Number of page loads" alt="Number of page loads">'+dip.numPageLoads+'</div><div title="Number of content loads">'+dip.numContentLoads+'</div><div>'+dip.loc+'</div></div></div>';
            }
            html2 += '</div>';
            */

            $('#siteContent > .vividDialogContent').html(html2 + html).delay(100);
            na.site.startTooltips();


            // This runs after naLog.view() has populated naLog.dataByURL
            function renderMostVisited() { // (C) 2026 Rene AJM Veerman and grok.com
                const container = document.getElementById('mostVisitedList');
                if (!naLog || !naLog.dataByURL) {
                    container.innerHTML = '<div style="color:#6c7086; padding:40px; text-align:center;">No visit data available yet.</div>';
                    return;
                }

                const stats = [];

                // Convert naLog.dataByURL object into array
                for (let fullUrl in naLog.dataByURL) {
                    const entry = naLog.dataByURL[fullUrl];
                    if (entry.numContentLoads > 0) {
                        // Create nice partial display
                        let partial = naLog.truncateUrl(fullUrl);

                        let target = naLog.process_location(fullUrl);
                        if (typeof target=='object') target='<pre>'+JSON.stringify(target,null,2)+'</pre>';

                        stats.push({
                            fullUrl: fullUrl,
                            partial: target,
                            visits: entry.numContentLoads
                        });
                    }
                }

                // Sort by visits (most visited first)
                stats.sort((a, b) => b.visits - a.visits);

                let html = '';

                stats.forEach((item, index) => {
                    html += `
                    <div class="link-item" style="background:rgba(0,0,0,0.3); border:1px solid #45475a; border-radius:12px; margin:10px 0; padding:16px 20px; display:flex; align-items:center;">
                    <div style="rgba(0,0,0,0.3); color:ivory; text-shadow:0px 0px 3px rgba(0,0,0,1), 0px 0px 0px yellow, 2px 2px 4px rgba(0,0,0,0.8);font-size:large;font-weight:bold; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-right:16px; flex-shrink:0;">
                    ${index + 1}
                    </div>
                    <div style="flex:1; overflow:hidden;">
                    <a href="${item.fullUrl}"
                    target="_new"
                    class="nomod noPushState"
                    style="text-decoration:none; color:inherit;">
                    <div style="color:ivory; text-shadow:0px 0px 3px rgba(0,0,0,1), 0px 0px 0px yellow, 2px 2px 4px rgba(0,0,0,0.8); font-family:monospace; font-size:large;font-weight:bold;; word-break:break-all;">
                    ${item.partial}
                    </div>
                    </a>
                    </div>
                    <div style="background:rgba(137,180,250,0.2); color:#89b4fa; padding:6px 14px; border-radius:9999px; font-weight:600; margin-left:16px; white-space:nowrap;">
                    ${item.visits.toLocaleString()} visits
                    </div>
                    </div>
                    `;
                });

                container.innerHTML = html || '<div style="color:#6c7086; padding:40px; text-align:center;">No pages recorded yet.</div>';
            }

            // Auto-run after naLog finishes rendering
            //setTimeout(() => {
                //if (typeof naLog !== 'undefined' && naLog.dataByURL) {
                    if (view=='mostVisited') renderMostVisited();
                //} else {
                    // Fallback: wait a bit longer
                //    setTimeout(renderMostVisited, 800);
              //  }
            //}, 600);

            if (view=='stats') {
                (async function() {
                    const data = [];
                    for (var dateStr in naLog.dataByDate) {
                        data[data.length] = { date : dateStr, count : naLog.dataByDate[dateStr].numContentLoads};
                    };
                    new Chart( document.getElementById('viewsByDate'), {
                        type: 'bar',
                        data: {
                            labels: data.map(row => row.date),
                            datasets: [
                            {
                                label: 'Views by date',
                                data: data.map(row => row.count)
                            }
                            ],
                        },
                        options : { // thanks go to x.com/grok
                            plugins: {
                                colors: {
                                forceOverride: true
                                }
                            },
                            color : 'rgb(255,255,255)',
                            scales: { x: { ticks: { color: 'rgb(255,255,255)' } }, y: { ticks: { color: 'rgb(255,255,255)' } } }
                        }
                    });
                })();

                (async function() {
                    const data = [];
                    for (var atld in naLog.dataByCountry) {
                        data[data.length] = { tld : atld, count : naLog.dataByCountry[atld].numContentLoads};
                    };
                    new Chart( document.getElementById('viewsByCountry'), {
                        type: 'bar',
                        data: {
                            labels: data.map(row => row.tld),
                            datasets: [
                            {
                                label: 'Views by country',
                                data: data.map(row => row.count)
                            }
                            ],
                        },
                        options : { // thanks go to x.com/grok
                            plugins: {
                                colors: {
                                forceOverride: true
                                }
                            },
                            color : 'rgb(255,255,255)',
                            scales: { x: { ticks: { color: 'rgb(255,255,255)' } }, y: { ticks: { color: 'rgb(255,255,255)' } } }
                        }
                    });
                })();

                /*
                (async function() {
                    const data = [];
                    for (var ahr in naLog.dataByURL) {
                        data[data.length] = { url : ahr, count : naLog.dataByURL[ahr].numContentLoads};
                    };
                    new Chart( document.getElementById('viewsByPage'), {
                        type: 'bar',
                        data: {
                            labels: data.map(row => row.url),
                            datasets: [
                            {
                                label: 'Views by page',
                                data: data.map(row => row.count)
                            }
                            ]
                        },
                        options : { // thanks go to x.com/grok
                            plugins: {
                                colors: {
                                forceOverride: true
                                }
                            },
                            color : 'rgb(255,255,255)',
                            textShadow : '2px 2px 3px rgba(0,0,0,0.8)',
                            scales: { x: { ticks: { color: 'rgb(255,255,255)' } }, y: { ticks: { color: 'rgb(255,255,255)' } } }
                        }
                    });
                })();*/
            };

            na.desktop.settings.visibleDivs.push ('#siteToolbarLeft');
            na.desktop.resize();
            //debugger;
        }, 100);
    },
    process_location : function (href) {
        if (href.match(document.location.origin+'/view/')) {
            var jsonEncoded = href.replace(document.location.origin+'/view/', '');
            try {
                var json = JSON.parse(na.m.decode_base64_url(jsonEncoded.replace(/\?.*/,'')));
                return json;//JSON.stringify(json, undefined, 2).replace(/"/g, '\\"');
                return href;
            } catch (e) {}
        };
        return href;
    },
    process_msg : function (msg, dit) {
        var r = '', prefix1a = 'NicerAppWebOS Fully started for <a href=\\"', prefix1b = '\\">', prefix1c = /\\\".*?<\/a>/, prefix2 = /Background set to "(.*?)";\s(.*)/, m = [];
        if (msg.indexOf(prefix1a)===0 && dit.ipinfo) {
            try {
                var href = msg.replace(prefix1a,'').replace(prefix1b,'').replace(prefix1c,'');

                r = { msg : msg, documentLocation : href, ipinfo : typeof dit.ipinfo=='string'?JSON.parse(dit.ipinfo):dit.ipinfo, 'ipinfo count' : dit.ipinfo.length};
            } catch (e) {debugger;};
        } else if (m = msg.match(prefix2)) {
            r = { msg : msg, onclickHTML : 'na.site.displayWallpaper(\''+m[2]+'\');', ipinfo : dit.ip_info};
        } else r = msg;
        return r;
    },
    reload : function (evt,begin,end) {
        var
        url = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/logs/ajax_getLogData.php',
        dat = {

        },
        ac = {
            type : 'GET',
            url : url,
            data : dat,
            success : function (data, ts, xhr) {
                naLog.view(data);
            },
            error : function (xhr, textStatus, errorThrown) {
            }
        };
        $.ajax(ac);
    },
    truncateUrl(url, maxLength = 100) {
        if (!url) return '';
        if (url.length <= maxLength) return url;

        try {
            const urlObj = new URL(url);

            const hostname = urlObj.hostname;
            const pathname = urlObj.pathname + urlObj.search + urlObj.hash;

            // If we can fit the domain + some path
            if (hostname.length + 15 >= maxLength) {
                // Very long domain → just truncate domain
                return hostname.length > maxLength
                ? hostname.slice(0, maxLength - 3) + '...'
                : hostname;
            }

            const available = maxLength - hostname.length - 4; // room for ".../"

            if (pathname.length <= available) {
                return urlObj.origin + pathname;
            }

            // Smart middle truncation: keep start of path + end of path
            const start = pathname.slice(0, Math.floor(available * 0.5));
            const end = pathname.slice(-Math.floor(available * 0.5));

            return urlObj.origin + start + '...' + end;

        } catch (e) {
            // Not a valid URL → simple truncation
            return url.length > maxLength
            ? url.slice(0, maxLength - 3) + '...'
            : url;
        }
    }
};
na.log = naLog;
