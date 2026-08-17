na.an = na.analytics = {
    about : {
        whatsThis : 'a complete set of analytics routines, using couchdb as storage facility',
        firstCreated : '2020-09',
        lastModified : '2020-09',
        versionHistory : {
            '1.0.0' : '2020-09 : first release',
            '2.0.0' : 
                '2021-07 : largely rewritten to avoid pouchdb (and thus keep database login details secret, '
                +'and allow for the database server to run on LAN ip addresses only (192.168.178.*)',
            '2.1.0' : '2022-04 : small feature upgrade - now logs also to console log'
        }
    },
    
    globals : {
        loadDaysIntoPast : 14
    },
    
    settings : { current : { loadedDaysIntoPast : 0, db : {}, dbByIP : {} } },
    
    onload : function () {
        $('#siteContent .vividDialogContent').html('<div style="display:flex;align-items:center;font-weight:bold;justify-content:center;vertical-align:middle;align-content: center;align-items : center;font-size:2em"><div class="startupMsg"></div></div>');
        na.an.load_more();
    },
    
    load_more : function (date, daysIntoPast) {
        var 
        date = new Date(), 
        dip = na.an.s.c.loadedDaysIntoPast;
        if (dip < na.an.globals.loadDaysIntoPast) {
            if (dip >= 0) date.setDate (date.getDate() - dip);
            var
            dateStr = date.toISOString().slice(0, 10),
            msg = 'Now loading '+dateStr+' ('+dip+' of '+na.an.globals.loadDaysIntoPast+')';
            $('.startupMsg').html(msg);
            na.an.load_date (dateStr);
        } else {
            na.an.visualizeLoadedData();
        }
    },
    
    load_date : function(date) {
        var 
        fncn = 'na.analytics.load_date("'+date+'")',
        url = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/analytics/ajax_load_index.php',
        ac = {
            type : 'GET',
            url : url,
            data : {
                date : date
            },
            success : function (data, ts, xhr) {
                //$('#siteContent .vividDialogContent').html(data);
                var dat = {};
                dat[date] = JSON.parse(data);
                na.an.s.c.db = $.extend( dat, na.an.s.c.db );                
                
                var recs = na.an.s.c.db[date];
                for (var i=0; i<recs.length; i++) {
                    var rec = recs[i];
                    //debugger;
                    if (rec.msg.indexOf('keep-alive')===-1) {
                        if (!na.an.s.c.dbByIP[date]) na.an.s.c.dbByIP[date] = [];
                        if (!na.an.s.c.dbByIP[date][rec.ip]) na.an.s.c.dbByIP[date][rec.ip] = [];
                        if (
                            rec.msg.indexOf('userAgent')!==-1
                            && (
                                rec.msg.indexOf('Bot')!==-1
                                || rec.msg.indexOf('bot')!==-1
                            )
                        ) {
                            na.an.s.c.dbByIP[date][rec.ip].isBot = true;
                        }

                        var ipr = na.an.s.c.dbByIP[date][rec.ip];
                        ipr.push(rec);
                    }
                }
                
                na.an.s.c.loadedDaysIntoPast++;
                setTimeout (na.an.load_more, 500);
            },
            error : function (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ac);
    },
    
    visualizeLoadedData : function () {
        var
        html = '<table class="naan_rec_table">';
        for (var date in na.an.s.c.dbByIP) {
            var d = na.an.s.c.dbByIP[date];
            for (var ip in d) {
                var d2 = d[ip];
                //debugger;
                if (d2.isBot) {
                    for (var i=0; i<d2.length; i++) {
                        var d3 = d2[i];
                        html += 
                            '<tr class="naan_rec naan_bot">'
                            +'<td class="naan_datetimeStr"><p>'+(d3.timeStr?d3.timeStr:d3.datetimeStr)+'</p></td>'
                            +'<td class="naan_ip"><p>'+d3.ip+'</p></td>'
                            +'<td class="naan_msg"><p>'+d3.msg+'</p></td>'
                            +'</tr>';
                    }
                } else {
                    for (var i=d2.length-1; i>=0; i--) {
                        var d3 = d2[i];
                        html += 
                            '<tr class="naan_rec">'
                            +'<td class="naan_datetimeStr"><p>'+(d3.timeStr?d3.timeStr:d3.datetimeStr)+'</p></td>'
                            +'<td class="naan_ip" onmouseover="na.an.geoIP(event, \''+d3.ip+'\')"><p>'+d3.ip+'</p></td>'
                            +'<td class="naan_msg"><p>'+d3.msg+'</p></td>'
                            +'</tr>'
                    }
                }
            }
        }
        html += '</table>';
        $('#siteContent .vividDialogContent').html(html);
    },
    
    dt2str : function (datetime) {
        var 
        m = datetime,
        dns = Date.locale.en.day_names,
        r = m.getFullYear() + "-" +
            ("0" + (m.getMonth()+1)).slice(-2) + "-" +
            ("0" + m.getDate()).slice(-2) + "(" + dns[m.getDay()] + ') ' +
            ("0" + m.getHours()).slice(-2) + ":" +
            ("0" + m.getMinutes()).slice(-2) + ":" +
            ("0" + m.getSeconds()).slice(-2) + "." +
            na.m.padNumber(m.getMilliseconds(),4,'0');
        return r;
    },
    
    t2str : function (milliseconds) {
        // 1- Convert to seconds:
        var seconds = milliseconds / 1000;
        // 2- Extract hours:
        var hours = parseInt( seconds / 3600 ); // 3,600 seconds in 1 hour
        seconds = seconds % 3600; // seconds remaining after extracting hours
        // 3- Extract minutes:
        var minutes = parseInt( seconds / 60 ); // 60 seconds in 1 minute
        // 4- Keep only seconds not extracted to minutes:
        seconds = seconds % 60;
        //seconds = Math.round(seconds*1000)/1000;
        seconds = Math.floor(seconds);
        var r = hours+":"+minutes+":"+seconds+':'+((seconds-Math.floor(seconds))*1000);
        return r;
    },

    date2str : function (datetime) {
        var 
        m = datetime;
        
        if (typeof m!=='object') m = new Date(m);
        
        var
        r = m.getFullYear() + "-" +
            ("0" + (m.getMonth()+1)).slice(-2) + "-" +
            ("0" + m.getDate()).slice(-2);
        return r;
    },
    
    generateSessionID : function () {
        var 
        seed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        r = '';
        
        for (var i=0; i<30; i++) {
            r += seed.substr(Math.random()*seed.length, 1);
        };
        
        return r;        
    },
    
    hookEvents : function (rootElement) {
        jQuery('a', rootElement).each (function (idx, el) {
            if (!el.analytics) {
                el.analytics = true;
                na.analytics.hookEvent(el, 'click', na.analytics.logEvent, true, true);
            }
        });
        jQuery('img', rootElement).each (function(idx, el) {
            if (!el.analytics) {
                el.analytics = true;
                na.analytics.hookEvent(el, 'click', na.analytics.logEvent, true, true);
            }
        });
    },
    
 	hookEvent : function (el, eventName, handler, useCapture, add) {
		if (add) {
			if (el.addEventListener) {
			// Standards browsers
				el.addEventListener (eventName, handler, useCapture);
			} else if (el.attachEvent) {
			// IE < v9.0
				el.attachEvent ('on'+eventName, handler);
			}
		} else {
			if (el.removeEventListener) {
			// Standards browsers
				el.removeEventListener (eventName, handler, useCapture);
		}	 else if (el.detachEvent) {
				el.detachEvent ('on'+eventName, handler);
			}
		}
	},
    
    logEvent : function (evt) {
        if (!na.site.globals.naLAN
            && !navigator.userAgent.match(/bot/i)
        ) {
            var
            date = new Date(),
            timeInMilliseconds = date.getTime(),
            appRunTime = timeInMilliseconds - na.m.settings.siteStartTime,

            timeString_runningPage = na.m.secondsToTimeString (appRunTime / 1000),
            timeString_now = na.m.dateObj_toDateString (date),
            timeString = timeString_now+' (@'+timeString_runningPage+' now)',
            s = na.analytics.settings,
            myip = na.site.globals.myip.replace(/_/g,'.'),
            datetime = new Date(),
            datetimeStr = na.analytics.dt2str (datetime),
            dateStr = na.analytics.date2str (datetime),
            doc = {
                _id : 'dt_'+datetime.getTime()+'_'+datetime.getMilliseconds(),
                jsSessionID : s.jsSessionID,
                datetime : datetime.getTime(),
                datetimeStr : datetimeStr,
                milliseconds : datetime.getMilliseconds(),
                timeStr : timeString,
                date : dateStr,
                tzOffset : datetime.getTimezoneOffset(),
                userAgent : navigator.userAgent,
                ip : myip,
                htmlID : (
                    evt
                    && evt.target
                    ? typeof evt.target.id=='string' && evt.target.id !== ''
                        ? 'id='+evt.target.id
                        : typeof evt.target.href=='string' && evt.target.href !== ''
                            ? 'a href='+evt.target.href
                            : typeof evt.target.innerHTML=='string' && evt.target.innerHTML !== ''
                                ? 'innerHTML='+evt.target.innerHTML
                                : evt.srcElement
                                    ? typeof evt.srcElement.id=='string' && evt.srcElement.id !== ''
                                        ? 'id='+evt.srcElement.id
                                        : typeof evt.srcElement.href=='string' && evt.srcElement.href !== ''
                                            ? 'a href='+evt.srcElement.href
                                            : typeof evt.srcElement.innerHTML=='string' && evt.srcElement.innerHTML!==''
                                                ? 'innerHTML='+evt.srcElement.innerHTML
                                                : 'ERROR : Could not detect target element for HTML event = '+JSON.stringify (evt)
                                    : 'ERROR : Could not detect target element for HTML event = '+JSON.stringify (evt)
                    : 'ERROR : Could not detect target element for HTML event = '+JSON.stringify (evt)
                ),
                eventType : evt ? evt.type : '[no HTML event]',
                msg : '[EVENT]'
            },
            ac = {
                type : 'POST',
                url : '/NicerAppWebOS/logEvent.php',
                data : {
                    doc : JSON.stringify(doc)
                }
            };
            debugger;
            $.ajax(ac);
            na.m.log (1, 'na.analytics.logEvent() : eventType='+doc.eventType+', htmlID='+doc.htmlID, false);
        }
    },
    
    logMetaEvent : function (msg, stacktrace) {
        if (typeof stacktrace=='undefined') stacktrace = false;
        //na.analytics.pouchdb.logMetaEvent (msg);
        if (!na.site.globals.naLAN
            && !navigator.userAgent.match(/bot/i)
        ) {
            var
            date = new Date(),
            timeInMilliseconds = date.getTime(),
            appRunTime = timeInMilliseconds - na.m.settings.siteStartTime,

            timeString_runningPage = na.m.secondsToTimeString (appRunTime / 1000),
            timeString_now = na.m.dateObj_toDateString (date),
            timeString = timeString_now+' (@'+timeString_runningPage+' now)',
            s = na.analytics.settings,
            myip = na.site.globals.myip.replace(/_/g,'.');
            var
            datetime = new Date(),
            datetimeStr = na.analytics.dt2str (datetime),
            dateStr = na.analytics.date2str (datetime),
            doc = {
                _id : 'dt_'+datetime.getTime()+'_'+datetime.getMilliseconds(),
                jsSessionID : s.jsSessionID,
                datetime : datetime.getTime(),
                datetimeStr : datetimeStr,
                milliseconds : datetime.getMilliseconds(),
                date : dateStr,
                timeStr : timeString,
                tzOffset : datetime.getTimezoneOffset(),
                userAgent : navigator.userAgent,
                ip : myip,
                htmlID : '[NULL]',
                eventType : '[META]',
                msg : msg
            };
            ac = {
                type : 'POST',
                url : '/NicerAppWebOS/logEvent.php',
                data : { doc : JSON.stringify(doc) },
                success : function (data, ts, xhr) {
                    na.an.settings.performingMeta = false;
                }
            };
            na.m.waitForCondition('na.an.settings.performingMeta '+datetime.getTime(), function() {
                return !na.an.settings.performingMeta;
            }, function() {
                na.an.settings.performingMeta = true;
                $.ajax(ac);
            });
            na.m.log (1, 'na.analytics.logMetaEvent() : '+msg, stacktrace);
        }
    },
    
    view : {
        prepare : function (rootElement) {
        },
        
        fillDates : function (rootElement) {
        },

        fillDateDetails : function (rootElement, date) {
        },
        
        fillDate : function (rootElement, jsSessionID, date) {
        }
    },

    datetimeConvertToOwnersTimezone : function (evt, tzOffset, datetimeInt) {
        na.analytics.settings.evt = evt;
        var 
        x = new Date(datetimeInt/* + (tzOffset * 60 * 1000)*/),
        dateStr = x.toLocaleString('nl-NL',{timeZone:'CET'}) + '.' + x.getMilliseconds(),
        html = '<div class="datetimeLocal" style="position:absolute;top:'+(evt.layerY+30)+'px;left:'+(evt.layerX+30)+'px;background:rgba(0,0,0,0.8);border:3px ridge white;border-radius:5px;">'+dateStr+'</div>';
        
        jQuery('#siteContent .vividDialogContent').prepend(html);
    },
    
    
    geoIP : function (evt, IP) {
        var 
        dataToServer = {
            IP : IP
        },
        ajaxCmd = {
            url : '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/analytics/geoIP.html.php',
            type : 'GET',
            data : dataToServer,
            async : true,
            success : function (data, ts) {
                var 
                evt = na.analytics.settings.evt,
                html = '<div class="geoIP" style="display:none;position:absolute;top:'+evt.layerY+'px;left:'+(evt.layerX+50)+'px;background:rgba(0,0,0,0.8);border:3px ridge white;border-radius:5px;z-index:12000000000000;" onmouseout="na.an.geoIP_mouseout(event)">'+data+'</div>';
                //var div = $.parseHTML(html);
                jQuery('body').append(html);
                $('.geoIP').fadeIn('normal');
                na.an.s.c.recentlyAdded_geoIP = true;
                setTimeout(function() {
                    na.an.s.c.recentlyAdded_geoIP = false;
                }, 1000);
                //debugger;
            }
        };
        if (!$('.geoIP')[0]) {
            na.analytics.settings.evt = evt;
            $.ajax (ajaxCmd);
        }
    },
    
    geoIP_mouseout : function (evt) {
        setTimeout (function() {
            if (!na.an.s.c.recentlyAdded_geoIP)
            $('.geoIP').fadeOut('fast', function() {
                $('.geoIP').remove();
            });
        }, 1500);
    }
};
na.an.s = na.an.settings;
na.an.s.c = na.an.s.current;
