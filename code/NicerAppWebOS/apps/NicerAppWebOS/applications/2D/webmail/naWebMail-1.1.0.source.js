na.m.waitForCondition ( 'na.m.desktopInitialized() for app.2D.webmail.v1.0.0', na.m.desktopInitialized, function () {        
        
na.analytics.logMetaEvent ('app.2D.webmail.v1.0.0 : init-stage-1');

delete na.apps.loaded['app.2D.webmail.v1.0.0'];
na.apps.loaded['app.2D.webmail.v1.0.0'] = {
	about : {
		whatsThis : 'Application code for this webmail feature set of the NicerApp CMS',
		copyright : 'Copyrighted (c) and All Rights Reserved (r) 2011-2022 by Rene AJM Veerman, Amsterdam, Netherlands',
		license : 'https://nicer.app/LICENSE.txt',
		firstCreated : '2021',
		lastModified : '2022',
        version : '1.0.0'
	},
	globals : {
    },
    settings : {
        currentConfig_mailserverIdx : -1,
        config : false,
        mailboxes : [],
        mails : [],
        perPage : 50,
        readyToPaint__wmMails_table : true,
        
        loadedIn : {
            '#siteToolbarLeft' : {
                onload : function () {
                    if (
                        !$.cookie('cdb_loginName')
                        || $.cookie('cdb_loginName')==''
                        || $.cookie('cdb_loginName')=='Guest'
                    ) {
                        na.site.displayLogin ('You must register and/or login to use the webmail features.');
                    }
                    
                    var header = 
                        '<div class="header" style="position:absolute;height:65px;width:calc(100% - 20px);background:rgba(0,0,0,0.4);border-radius:8px;">'
                            +'<span id="btnBack_fromWebMail" class="vividButton_icon_50x50 tooltip" title="Go back" alt="Go back"  onclick="if (!$(this).is(\'.disabled\')) na.themeEditor.selectTextShadowSettings(event)">'
                                +'<span class="vividButton_icon_borderCSS_50x50"></span>'
                                +'<img class="vividButton_icon_imgTile_50x50" src="/siteMedia/btnCssVividButton.yellow1a.png"/>'
                                +'<img class="vividButton_icon_imgButtonIconBG_50x50" src="/siteMedia/btnCssVividButton_iconBackground.png"/>'
                                +'<img class="vividButton_icon_imgButtonIcon_50x50" src="/siteMedia/btnBack.png"/>'
                            +'</span>'            
                            +'<span id="btnAddServer" class="vividButton_icon_50x50 tooltip" title="Add server" alt="Add server"  onclick="if (!$(this).is(\'.disabled\')) na.apps.loaded[\'app.2D.webmail.v1.0.0\'].addServer(event);">'
                                +'<span class="vividButton_icon_borderCSS_50x50"></span>'
                                +'<img class="vividButton_icon_imgTile_50x50" src="/siteMedia/btnCssVividButton.greenYellow.png"/>'
                                +'<img class="vividButton_icon_imgButtonIconBG_50x50" src="/siteMedia/btnCssVividButton_iconBackground.png"/>'
                                +'<img class="vividButton_icon_imgButtonIcon_50x50" src="/siteMedia/blue-plus-icon-12.png"/>'
                            +'</span>'            
                            +'<span id="btnEditServer" class="vividButton_icon_50x50 tooltip" title="Edit server settings" alt="Edit server settings"  onclick="if (!$(this).is(\'.disabled\')) na.apps.loaded[\'app.2D.webmail.v1.0.0\'].editServer(event);">'
                                +'<span class="vividButton_icon_borderCSS_50x50"></span>'
                                +'<img class="vividButton_icon_imgTile_50x50" src="/siteMedia/btnCssVividButton.blue1a.png"/>'
                                +'<img class="vividButton_icon_imgButtonIconBG_50x50" src="/siteMedia/btnCssVividButton_iconBackground.png"/>'
                                +'<img class="vividButton_icon_imgButtonIcon_50x50" src="/siteMedia/settings.png"/>'
                            +'</span>'            
                            +'<span id="btnDeleteServer" class="vividButton_icon_50x50 tooltip" title="Delete server" alt="Delete server"  onclick="if (!$(this).is(\'.disabled\')) na.apps.loaded[\'app.2D.webmail.v1.0.0\'].deleteServer(event)">'
                                +'<span class="vividButton_icon_borderCSS_50x50"></span>'
                                +'<img class="vividButton_icon_imgTile_50x50" src="/siteMedia/btnCssVividButton.red1a.png"/>'
                                +'<img class="vividButton_icon_imgButtonIconBG_50x50" src="/siteMedia/btnCssVividButton_iconBackground.png"/>'
                                +'<img class="vividButton_icon_imgButtonIcon_50x50" src="/siteMedia/btnTrashcan2.png" style="width:auto;height:28px;left:12px;top:10px"/>'
                            +'</span>'            
                            +'<h1 class="appTitle" style="position:absolute;display:inline-block;width:180px;text-align:right;top:-7px;font-size:2em; text-shadow:2px 2px 2px rgba(0,0,0,0.7)">'
                            +$('#siteToolbarLeft h1').html()
                            +'</h1>'
                        +'</div>';
                    $('#siteToolbarLeft h1').remove();
                    $('#siteToolbarLeft .vividDialogContent').removeClass('vividScrollpane').prepend (header);
                    var top = $('#siteToolbarLeft .header').position().top + $('#siteToolbarLeft .header').height() + 20;
                    $('#wmLeft').addClass('vividScrollpane').css({ 
                        position : 'absolute',
                        top : top,
                        height : $('#siteToolbarLeft .vividDialogContent').height() - top 
                    });

                    $('.vividButton_icon_50x50 img').each(function(idx, el) {
                        var theme = $(el).attr('tooltipTheme');
                        if (!theme) theme = 'mainTooltipTheme';
                        var ptSettings = {
                            theme : theme,
                            contentAsHTML : true,
                            content : $(el).attr('title')
                        };
                        $(el).tooltipster(ptSettings).tooltipster('show').tooltipster('hide');
                    });

                    var div = $('#siteToolbarLeft')[0];
                    $('.vividButton, .vividButton_icon_50x50', div).each(function(idx,el){
                        var btn = new naVividButton(el);
                        na.site.settings.buttons['#'+el.id] = btn;  
                        if (el.id=='btnDeleteServer' || el.id=='btnEditServer') btn.disable();
                    });
                    var div = $('#siteContent')[0];
                    $('.vividButton, .vividButton_icon_50x50', div).each(function(idx,el){
                        var btn = new naVividButton(el);
                        na.site.settings.buttons['#'+el.id] = btn;
                        btn.disable();
                    });
                        
                    na.d.s.visibleDivs.push ('#siteToolbarLeft');
                    na.desktop.registerProgress ('webmail resize progress', na.apps.loaded['app.2D.webmail.v1.0.0'].onresize);
                    na.desktop.registerCallback ('webmail start content init', '#siteToolbarLeft', function (cb, div, calculationResults, sectionIdx, section, divOrderIdx) {
                        if (div.id == 'siteToolbarLeft') {
                            na.apps.loaded['app.2D.webmail.v1.0.0'].onresize();
                            na.apps.loaded['app.2D.webmail.v1.0.0'].init();
                            na.desktop.deleteCallback ('webmail start content init'); 
                        }
                    });
                    na.apps.loaded['app.2D.webmail.v1.0.0'].onresize();
                    na.desktop.resize();
                } // #siteToolbarLeft onload()
            }
        }
    },


    init : function () {
        na.apps.loaded['app.2D.webmail.v1.0.0'].onresize();
        na.apps.loaded['app.2D.webmail.v1.0.0'].readConfig();
    },

    onresize : function (evt) {
        $('#siteContent .vividDialogContent').css({
            margin : 0,
            padding : 0,
            overflow : 'visible'
        });
        
        $('#siteToolbarLeft .vividDialogContent').css({
            top : 0,
            width : $('#siteToolbarLeft').width() - 27,
            height : $('#siteToolbarLeft').height() - 25
        });
        $('#siteToolbarLeft .vdBackground').css({
            top : 0,
            height : $('#siteToolbarLeft').height()
        });

        var top = $('#siteToolbarLeft .header').position().top + $('#siteToolbarLeft .header').height() + 20;
        $('#wmLeft').css({ 
            position : 'absolute',
            top : top,
            width : 'calc(100% - 20px)',
            height : $('#siteToolbarLeft .vividDialogContent').height() - top 
        });
        
        $('#siteToolbarLeft .appTitle').css({
            width : $('#siteToolbarLeft .header').width() - (55*4) - 10
        });
        
        jQuery('#wmMails_header_table .pictogramButton__td').css({
            width : 46, height : 46
        });
        jQuery('.pictogramButton__td').css({
            width : 46
        });
        jQuery('.pictogramButton__td img').css({
            marginLeft : 2, marginTop : 2
        });
        jQuery('.pictogramButton').css ({
            width : 40, height : 40
        });
        
        $('#wmOuter, #td_right_top, #td_right_bottom, #wmMails, #bgMailInfo, #wmMails_header').css ({ 
            padding : 0,
            margin : 0,
            width : $('#siteContent').width() - 48
        });
        $('#wmMails_header').css({ width : $('#siteContent').width() - 30 });
        $('#wmOuter').css({
            marginLeft : 10,
            overflow : 'visible'
        });
        $('#wmMails').css({
            padding : 8
        });
        $('#wmEmail').css({
            padding : 0,
            margin : 0,
            width : '100%'
        });
        $('#bgMailInfo').css({
            width : $('#siteContent').width() - 65
        });
        $('#wmMails, #td_right_bottom').css({
            borderRadius : 10,
            border : '1px solid rgba(255,255,255,0.5)',
            boxShadow : '1px 1px 2px 2px rgba(0,0,0,0.5), inset 2px 2px 2px 2px rgba(0,0,0,0.5)'
        });
        $('iframe', $('#siteContent')[0]).css({
            borderRadius : 10,
            border : '0px solid rgba(255,255,255,0)'
        });

        $('#wmOuter').css ({ height : '100%' });
        jQuery('#wmMails').css({
            height : ($('#siteContent .vividDialogContent').height()/2) - 50 - $('#wmMails_header').height()
        });
        
        var
        mailFrom = jQuery('td.mailFrom').width(),
        mailSubject = jQuery('td.mailSubject').width(),
        mailDate = 80,//jQuery('td.mailDate').width(),
        mailTotalMsgsInThread = 50;
        
        jQuery('.mailFrom').css ({
            width : mailFrom
        });
        jQuery('.mailSubject').css ({
            width : mailSubject
        });
        jQuery('.mailDate').css ({
            minWidth : mailDate
        });
        jQuery('.totalMsgsInThread').css({
            width : 88
        });
    },

    addServer : function (evt) {
        na.apps.loaded['app.2D.webmail.v1.0.0'].settings.currentConfig_mailserverIdx++;
        //if (!na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config) na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config = { mailServers : [] };
        if (!na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config.mailServers) na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config.mailServers = [];
        
        $('#btnSaveSettings').attr('add', 'true');
        
        $('#wmOuter').fadeOut('normal', function() {
            $('#wmSettings').fadeIn('normal');
        });
    },

    editServer : function (evt, add) {
        var 
        msIdx = parseInt( $('.mailserverName.selected').parent()[0].id.replace('mailserver__', '') ),
        ms = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config.mailServers[msIdx];
        
        $('#btnSaveSettings').attr('add', 'false');
        na.apps.loaded['app.2D.webmail.v1.0.0'].settings.currentConfig_mailserverIdx = msIdx;
        
        $('#wmOuter').fadeOut('normal', function() {
            $('#wms_displayName').val(ms['Display Name']);
            $('#wms_userID').val(ms.userID);
            $('#wms_userPassword').val(ms.userPassword);
            $('#wms_imap_domain').val(ms.IMAP.domain);
            $('#wms_imap_port').val(ms.IMAP.port);
            $('#wms_imap_requiresSSL')[0].checked = ms.IMAP.requiresSSL;
            $('#wms_imap_sslCertificateCheck')[0].checked = ms.IMAP.sslCertificateCheck;
            $('#wms_smtp_domain').val(ms.SMTP.domain);
            $('#wms_smtp_requiresAuthentication')[0].checked = ms.SMTP['requires authentication'];
            $('#wms_smtp_requiresSSL')[0].checked = ms.SMTP['requires SSL'];
            $('#wms_smtp_requiresTLS')[0].checked = ms.SMTP['requires TLS'];
            $('#wms_smtp_sslPort').val(ms.SMTP['port for SSL']);
            $('#wms_smtp_tlsPort').val(ms.SMTP['port for TLS']);
            $('#wms_smtp_usePEAR')[0].checked = ms.SMTP.usePEAR;
            $('#wmSettings').fadeIn('normal');
        });
    },

    deleteServer : function (evt) {
        var msIdx = parseInt( $('.mailserverName.selected').parent()[0].id.replace('mailserver__', '') );
        
        $('.bgMailboxName').fadeOut('normal', function() {
            $('.bgMailboxName').remove();
        });
        $('.mailserverName.selected').parent().slideUp('normal', function() {
            $('.mailserverName.selected').parent().remove();
        });

        na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config.mailServers.splice(msIdx,1);
        na.apps.loaded['app.2D.webmail.v1.0.0'].saveSettings (evt, false);
    },

    saveSettings : function (evt) {
        var add = $('#btnSaveSettings').attr('add') == 'true';
        debugger;
        na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config.mailServers[na.apps.loaded['app.2D.webmail.v1.0.0'].settings.currentConfig_mailserverIdx] = {
            "Display Name" : $('#wms_displayName').val(),
            "userID" : $('#wms_userID').val(),
            "userPassword" : $('#wms_userPassword').val(),
            "localIMAP" : {
                "domain" : $('#wms_localIMAP_domain').val(),
                "port" : parseInt($('#wms_localIMAP_port').val()),
                "requiresSSL" : $('#wms_localIMAP_requiresSSL')[0].checked?true:false,
                "sslCertificateCheck" : $('#wms_localIMAP_sslCertificateCheck')[0].checked?true:false,
                "userID" : $('#wms_localIMAP_userID').val(),
                "password" : $('#wms_localIMAP_userPassword').val()
            },
            "remoteIMAP" : {
                "enable" : $('#wms_remoteIMAP_enable')[0].checked?true:false,
                "domain" : $('#wms_remoteIMAP_domain').val(),
                "port" : parseInt($('#wms_remoteIMAP_port').val()),
                "requiresSSL" : $('#wms_remoteIMAP_requiresSSL')[0].checked?true:false,
                "sslCertificateCheck" : $('#wms_remoteIMAP_sslCertificateCheck')[0].checked?true:false,
                "userID" : $('#wms_remoteIMAP_userID').val(),
                "password" : $('#wms_remoteIMAP_userPassword').val()
            },
            "SMTP" : {
                "domain" : $('#wms_smtp_domain').val(),
                "requires SSL" : $('#wms_smtp_requiresSSL')[0].checked?true:false,
                "requires TLS" : $('#wms_smtp_requiresTLS')[0].checked?true:false,
                "requires authentication" : $('#wms_smtp_requiresAuthentication')[0].checked?true:false,
                "port for SSL" : parseInt($('#wms_smtp_sslPort').val()),
                "port for TLS" : parseInt($('#wms_smtp_tlsPort').val()),
                "usePEAR" : $('#wms_smtp_usePEAR')[0].checked?true:false
            }
        };

        
        var ac = {
            type : 'POST',
            url : '/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/ajax_editConfig.php',
            data : {
                config : JSON.stringify(na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config)
            },
            success : function (data, ts, xhr) {
                if (data!=='status : Success') {
                    na.site.fail ('Could not save webmail settings.');
                } else {
                    na.site.displayLogin ('Webmail settings will not be backed up until you login again.');
                }
            },
            error : function (xhr, ajaxOptions, errorThrown) {
                na.site.fail ('Could not save webmail settings (HTTP error).');
                debugger;
            }
        };
        $.ajax(ac);
    },

    readConfig : function () {
        var 
        fncn = "na.apps.loaded['app.2D.webmail.v1.0.0'].readConfig()",
        url1 = '/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/ajax_get_config.php';
        jQuery.ajax({
            type : 'GET',
            url : url1,
            success : function (data, ts, xhr) {
                if (data==='') {
                    na.site.fail ('Could not load webmail settings.');
                    return false;
                };

                try {
                    na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config = JSON.parse(data);
                } catch (err) {
                    na.site.fail ('Configuration error : '+data);
                };
                
                if (na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config === false) {
                    na.site.fail ('Could not load webmail settings.');
                    return false;
                } else if (na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config.ERROR) {
                    na.site.fail (na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config.ERROR);
                    return false;
                } else if (na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config && !na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config.mailServers) {
                    na.site.fail ('No webmail settings found. Please enter them now, for at least one of your accounts.');
                    $('#btnAddServer').trigger('click');
                    return false;
                } else {
                    var s = na.apps.loaded['app.2D.webmail.v1.0.0'].settings, config = s.config, html = '';
                    jQuery('#wmLeft').html('');

                    na.apps.loaded['app.2D.webmail.v1.0.0'].settings.currentConfig_mailserverIdx = config.mailServers.length - 1;
                    for (var i=0; i<config.mailServers.length; i++) {
                        html += '<div id="mailserver__'+i+'"><div class="mailserverName" onclick="na.apps.loaded[\'app.2D.webmail.v1.0.0\'].highlightMailserver(event);">'/*+config.mailServers[i].IMAP.domain+' '*/+config.mailServers[i].userID+'</div></div>';
                    }
                    
                    jQuery('#wmLeft').html(html);
                    
                    na.site.settings.buttons['#btnWriteMail'].enable();
                    na.site.settings.buttons['#btnFilter'].enable();

                    setTimeout(function(){
                        for (var i=0; i<config.mailServers.length; i++) {
                            s.mailboxes[i] = na.apps.loaded['app.2D.webmail.v1.0.0'].getMailboxes(config, i);
                            
                            var
                            domain = config.mailServers[i].SMTP.domain.replace(/smtp./,''),
                            userID = config.mailServers[i].userID.replace(/@.*/,'');
                            html = '<option domain="'+domain+'" value="'+userID+'@'+domain+'">'+userID+'@'+domain+'</option>';
                            jQuery('#select_mailFrom').append (html);
                        }
                    }, 100);
                }
            },
            error : function (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url1, xhr, textStatus, errorThrown);
            }
        });
    },

    getMailboxes : function (config, serverIdx) {
        var serverConfig = config.mailServers[serverIdx];
        jQuery.ajax({
            type : 'POST',
            url : '/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/ajax_get_mailboxes.php',
            data : {
                config : serverConfig
            },
            success : function (data, ts, xhr) {
                var 
                d = JSON.parse(data),
                sc = serverConfig,
                html = '';
                
                for (var i=0; i<d.length; i++) {
                    d[i] = d[i].replace(/{.*}/,'');
                    html += '<div id="mailbox__'+serverIdx+'__'+i+'" class="mailboxName" onclick="na.apps.loaded[\'app.2D.webmail.v1.0.0\'].switchToMailbox(event); na.apps.loaded[\'app.2D.webmail.v1.0.0\'].getMailboxContent('+serverIdx+', '+i+', 0, '+na.apps.loaded['app.2D.webmail.v1.0.0'].settings.perPage+');" style="padding-left:8px">'+d[i]+'</div>';
                };
                jQuery('#mailserver__'+serverIdx).append(html);
                na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mailboxes[serverIdx] = d;
                
                if (serverIdx === config.mailServers.length-1) {
                    na.apps.loaded['app.2D.webmail.v1.0.0'].onresize();
                    setTimeout (function() {
                        $('#siteToolbarLeft .lds-facebook').fadeOut('normal', 'swing');
                    }, 500);
                    
                };
                
            },
            error : function (xhr, ajaxOptions, thrownError) {
                debugger;
            }
        });
    },

    getMailboxContent : function (serverIdx, mailboxIdx, pageIdx, perPage) {
        var config = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config;
        na.apps.loaded['app.2D.webmail.v1.0.0'].settings.view = {
            serverIdx : serverIdx,
            mailboxIdx : mailboxIdx,
            pageIdx : pageIdx
        };
        jQuery.ajax ({
            type : 'POST',
            url : '/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/ajax_get_mailbox_content.php',
            data : {
                serverConfig : config.mailServers[serverIdx],
                serverIdx : serverIdx,
                mailboxes : na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mailboxes[serverIdx],
                mailboxIdx : mailboxIdx,
                pageIdx : pageIdx,
                perPage : perPage   
            },
            success : function (data, ts, xhr) {
                var html = '', d = JSON.parse(data), merge = true;
                if (!na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx]) na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx] = [];
                if (!na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx]) na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx] = [];
                for (var i=0; i<na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx].length; i++) {
                    if (na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx][i].message_id === d[0].message_id) merge = false;
                };
                if (merge) {
                    for (var i=0; i<d.length; i++) {
                        na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx].push (d[i]);
                    }
                };
                for (var i=0; i<na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx].length; i++) {
                    if (!na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx][i].totalMsgsInThread) na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx][i].totalMsgsInThread = 1;
                    for (var j=0; j<d.length; j++) {
                        if (
                            typeof d[j].references == 'string' 
                            && d[j].references!==''
                            && d[j].references.indexOf (na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx][i].message_id)!==-1
                        ) {
                            na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx][i].totalMsgsInThread++;
                            d[j].referenceIdx = i;
                        }
                    }
                };
                
                var els = jQuery('.mailInfo, .naWebMail_sent, .naWebMail_from');
                els.fadeOut('normal', function() {
                    els.remove();
                });
                for (var i=d.length-1; i>=0; i--) {
                    if (d[i].references || d[i].in_reply_to) continue;
                        
                    let
                    escaped = {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        "'": '&#39;',
                        '"': '&quot;'
                    },
                    fromAddress = d[i].from.replace(/[&<>'"]/g, function (m) {
                        return escaped[m];
                    }),
                    toAddress = d[i].to.replace(/[&<>'"]/g, function (m) {
                        return escaped[m];
                    });  
                    
                    //debugger;
                    html += 
                        '<tr class="mailInfo '+(d[i].seen?'mailSeen':'')+'" onclick="na.apps.loaded[\'app.2D.webmail.v1.0.0\'].highlightNoMails(); na.apps.loaded[\'app.2D.webmail.v1.0.0\'].highlightMail(event); na.apps.loaded[\'app.2D.webmail.v1.0.0\'].showEmail(event, '+serverIdx+', '+mailboxIdx+', '+d[i].msgno+', true);">'
                        + '<td class="mailDate">'+na.apps.loaded['app.2D.webmail.v1.0.0'].sent(d[i].date, i)+'</td>'
                        + '<td class="mailFrom" alt="From : '+d[i].from.replace(/"/g, '&quot;')+', To : '+d[i].to.replace(/"/g, '&quot;')+'"><span class="mailFrom_fromAddress" idx="'+i+'" fromAddress="'+fromAddress+'" toAddress="'+toAddress+'" ><span class="mailFrom_fromAddress_escaped">'+fromAddress+'</span><span class="mailFrom_toAddress_escaped">'+toAddress+'</span>'+d[i].from+'</span></td>'
                        //+ '<td class="mailSubject" onmouseover="na.apps.loaded[\'app.2D.webmail.v1.0.0\'].showDetails(event, '+serverIdx+', '+mailboxIdx+', '+i+');" onmouseout="na.apps.loaded[\'app.2D.webmail.v1.0.0\'].hideDetails();">'+d[i].subject+'</td>'
                        + '<td class="mailSubject">'+d[i].subject+'</td>'
                        + '<td class="totalMsgsInThread">'+d[i].totalMsgsInThread+'</td>'
                        +'</tr>';
                };
                na.m.waitForCondition ('clearing of #wmMails_table', 
                    function () { return na.apps.loaded['app.2D.webmail.v1.0.0'].settings.readyToPaint__wmMails_table; },
                    function () {

                        jQuery('#wmMails_table > tbody')[0].innerHTML += html;
                        
                        $('.sentDifference').hover(function(evt) {
                            var 
                            el = evt.currentTarget,
                            div = document.createElement('div'),
                            html = 
                                '<span class="naWebMail_datetimeRemote">Remote : '+$(el).attr('dateSent')+'</span><br/>'
                                +'<span class="naWebMail_datetimeLocal">Local : '+$(el).attr('dateSentLocal')+'</span><br/>'
                                +'<span class="naWebMail_datetimeDiffLong">'+$(el).attr('dateDiff')+'</span>';
                            
                            $(div)
                                .css({
                                    position:'absolute',
                                    top : $(el).position().top+$(el).parent().position().top+10-$('#wmMails')[0].scrollTop,
                                    left : $(el).position().left + $(el).parent().position().left
                                })
                                .attr('id', 'naWebMail_sent__'+$(el).attr('idx'))
                                .appendTo($('#td_right_top')[0])
                                .addClass('naWebMail_sent')
                                .html(html);
                        }, function (evt) {
                            //let el2 = $('#naWebMail_sent__'+$(evt.currentTarget).attr('idx'))[0];
                            $('.naWebMail_sent').fadeOut('normal', function() {
                                $('.naWebMail_sent').remove();
                            });
                        });
                        
                        $('.mailFrom_fromAddress').hover(function(evt) {
                            var
                            el = evt.currentTarget,
                            id = 'naWebMail_from_'+$(el).attr('idx'),
                            html = 
                                '<div class="naWebMail_from" id="'+id+'">'
                                +'<span class="naWebMail_fromLabel">From : </span'
                                +'<span class="naWebMail_fromAddress">'+$('.mailFrom_fromAddress_escaped', el).html()+'</span>'
                                +'<br/>'
                                +'<span class="naWebMail_toLabel">To : </span'
                                +'<span class="naWebMail_toAddress">'+$('.mailFrom_toAddress_escaped',el).html()+'</span>'
                                +'</div>';
                            
                            clearTimeout(na.apps.loaded['app.2D.webmail.v1.0.0'].settings.timeoutAddMailFromHoverDialog);
                            na.apps.loaded['app.2D.webmail.v1.0.0'].settings.timeoutAddMailFromHoverDialog = setTimeout(function() {
                                if (!$('#'+id)[0]) {
                                    $('#td_right_top').append(html);
                                    $('#'+id).css ({
                                        position:'absolute',
                                        top : $(el).position().top+$(el).parent().position().top-$('#wmMails')[0].scrollTop,
                                        left : $(el).position().left + $(el).parent().position().left
                                    });
                                }
                            }, 500);
                        }, function (evt) {
                            clearTimeout(na.apps.loaded['app.2D.webmail.v1.0.0'].settings.timeoutAddMailFromHoverDialog);
                            $('.naWebMail_from').fadeOut('normal', function() {
                                $('.naWebMail_from').remove();
                            });
                        });

                        na.apps.loaded['app.2D.webmail.v1.0.0'].highlightNoMails();
                        jQuery('#wmThreadInfo').fadeOut('normal');
                        
                        jQuery('#btnReplyMail, #btnForwardMail').addClass('pictogramButton__changing');
                        //window.top.na.s.c.grayscale ('pictogramButton__changing', 50, true, document);
                        setTimeout (function() {
                            jQuery('#btnReplyMail, #btnForwardMail').addClass ('pictogramButton__off').removeClass('pictogramButton__changing');
                        }, 2500);           
                        

                        jQuery('.mailInfo .pictogramButton__td').css ({
                            width : 46
                        });
                        jQuery('.mailFrom, .mailSubject, .mailDate').css ({
                            width : 'auto'//Math.round((((jQuery('#wmMails_header_table').width() - (3 * 46)) / 4) / 100) * 85)
                        });
                        jQuery('.totalMsgsInThread').css ({
                            width : 50//Math.round((((jQuery('#wmMails_header_table').width() - (3 * 46)) / 4) / 100 ) * 5)
                        });
                        na.apps.loaded['app.2D.webmail.v1.0.0'].onresize();
                        jQuery('#wmMails_header_table').animate({opacity:1},'slow');
                        
                        //jQuery('#wmMails_table_header').fadeIn('slow');
                        if (pageIdx===0) { 
                            jQuery('.btnUp').addClass('pictogramButton__off').addClass('pictogramButton__changing2'); 
                            //window.top.na.s.c.grayscale('pictogramButton__changing2',50,true,document);
                        } else { 
                            jQuery('.btnUp').removeClass('pictogramButton__off').addClass('pictogramButton__changing2'); 
                            //window.top.na.s.c.grayscale('pictogramButton__changing2',50,false,document); 
                            setTimeout (function() {
                                jQuery('.btnUp').removeClass('pictogramButton__changing2'); 
                            }, 1000);
                        };
                        if (d[0] && pageIdx===d[0].totalMsgs/perPage) {
                            jQuery('.btnDown').addClass('pictogramButton__off').addClass('pictogramButton__changing3'); 
                            //window.top.na.s.c.grayscale('pictogramButton__changing3',50,true,document);
                        } else { 
                            jQuery('.btnDown').removeClass('pictogramButton__off').addClass('pictogramButton__changing3'); 
                            //window.top.na.s.c.grayscale('pictogramButton__changing3',50,false,document); 
                            setTimeout (function() {
                                jQuery('.btnDown').removeClass('pictogramButton__changing3'); 
                            }, 1000);
                        };
                    }, 50);
            },
            error : function (xhr, ajaxOptions, thrownError) {
                debugger;
            }
        });
        $('#wmWriteMail').fadeOut('normal');
        $('#wmMails, #td_right_bottom, #wmMails_header_table2').fadeIn('normal');
    },

    getMailboxContent_prevPage : function () {
        var v = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.view;
        v.pageIdx++;
        na.apps.loaded['app.2D.webmail.v1.0.0'].getMailboxContent (v.serverIdx, v.mailboxIdx, v.pageIdx, na.apps.loaded['app.2D.webmail.v1.0.0'].settings.perPage);
    },

    getMailboxContent_nextPage : function () {
        var v = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.view;
        v.pageIdx--;
        na.apps.loaded['app.2D.webmail.v1.0.0'].getMailboxContent (v.serverIdx, v.mailboxIdx, v.pageIdx, na.apps.loaded['app.2D.webmail.v1.0.0'].settings.perPage);
    },

    showDetails : function (evt, serverIdx, mailboxIdx, mailIdx) {
        var html = 
            '<div class="mailDetails">'
            + JSON.stringify (na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx][mailIdx], null, 4).replace(/\n/g,'<br/>').replace(/ /g,'&nbsp;')
            + '</div>';
        jQuery ('body').append (html);
        jQuery ('.mailDetails').css ({
            top : evt.layerY,
            left : evt.layerX
        });
    },

    hideDetails : function () {
        jQuery ('.mailDetails').remove();
    },

    showEmail : function (evt, serverIdx, mailboxIdx, mailIdx, updateThreadInfo) {
        var config = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config;
        
        if (updateThreadInfo) {
            var html = '<table cellpadding="5"><tr><th>From</th><th>Sent</th></tr>';
            for (var i=0; i<na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx].length; i++) {
                var 
                m = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx][i]
                if (m.msgno===mailIdx) {
                    var 
                    j = i,
                    doUpdateThreadInfo = m.totalMsgsInThread > 1;
                    break;
                };
            };
            //debugger;
            if (doUpdateThreadInfo) {
                for (var i=j; i<na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx].length; i++) {
                    var 
                    m = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx][i]
                    mOrig = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx][j];
                    if (m.references && m.references.indexOf(mOrig.message_id)!==-1) {
                        html += '<tr class="mailThreadInfo" onclick="na.apps.loaded[\'app.2D.webmail.v1.0.0\'].highlightNoMailThreads(); na.apps.loaded[\'app.2D.webmail.v1.0.0\'].highlightMailThread(event); na.apps.loaded[\'app.2D.webmail.v1.0.0\'].showEmail(event, '+serverIdx+', '+mailboxIdx+', '+m.msgno+', false);"><td>'+m.from+'</td><td>'+na.apps.loaded['app.2D.webmail.v1.0.0'].sent(m.date)+'</td></tr>';
                    };
                };
                html += '</table>';
                jQuery('#wmThreadInfo').html(html).css({
                    display : 'block',
                    opacity : 0.0001,
                    left : evt.layerX,
                    top : evt.layerY + jQuery('#wmMails_header_table').height() + jQuery(evt.currentTarget).height()  
                }).animate({ opacity : 1}, 'slow');
            } else {
                jQuery('#wmThreadInfo').fadeOut('normal');
            }
        };    
        
        na.apps.loaded['app.2D.webmail.v1.0.0'].settings.currentMailData = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx][j];
        var ac = {
            type : 'POST',
            url : '/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/ajax_get_mail_content.php',
            data : {
                serverConfig : config.mailServers[serverIdx],
                serverIdx : serverIdx,
                mailboxes : na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mailboxes[serverIdx],
                mailboxIdx : mailboxIdx,
                mailIdx : mailIdx
            },
            success : function (data, ts, xhr) {
                //jQuery('#wmEmail').html(JSON.stringify(na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails[serverIdx][mailboxIdx][mailIdx], null, 4));
                //var d = JSON.parse(data);
                data2 = '<style>body { overflow : auto; }</style>'
                        +'<div id="div_wmEmail" class="vividScrollpane" style="height:100%;width:100%;">'
                        + data
                        +'</div>';            
                na.apps.loaded['app.2D.webmail.v1.0.0'].settings.currentMail = data;
                jQuery('#wmEmail').css({display:'none'});
                jQuery('#wmEmail')[0].contentDocument.open();
                jQuery('#wmEmail')[0].contentDocument.write(data2);
                jQuery('#wmEmail')[0].contentDocument.close();
                
                $('#wmEmail').contents().find('body').prepend(
                    '<link type="text/css" rel="StyleSheet" href="/domainConfig/nicer.app/index.css?c=20210711_231212">'
                    +'<link type="text/css" rel="StyleSheet" href="/domainConfig/nicer.app/index.dark.css?c=20210711_231212">'
                );
                jQuery('#wmEmail').contents().find('body,table,td,div,span,center').css({maxWidth:'100%',color:'black',background:'rgba(255,255,255,0)',textShadow:'1px 1px 1px rgba(0,0,0,0.4)'});
                jQuery('#wmEmail').contents().find('a').css({color:'rgb(0,50,0)'});
                jQuery('#wmEmail').css({display:'block'});
            },
            error : function (xhr, ajaxOptions, thrownError) {
                debugger;
            }
        };
        $.ajax(ac);
        
        var ac2 = {
            type : 'POST',
            url : '/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/ajax_get_mail_headers.php',
            data : {
                serverConfig : config.mailServers[serverIdx],
                serverIdx : serverIdx,
                mailboxes : na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mailboxes[serverIdx],
                mailboxIdx : mailboxIdx,
                mailIdx : mailIdx
            },
            success : function (data, ts, xhr) {
                na.apps.loaded['app.2D.webmail.v1.0.0'].settings.headers = {};
                var dat = data.split('\r\n');
                for (var i=0; i<dat.length; i++) {
                    var line = dat[i].split(':');
                    if (line[0].substr(0,1)!==' ') na.apps.loaded['app.2D.webmail.v1.0.0'].settings.headers[line[0]] = line[1];
                }
            },
            error : function (xhr, ajaxOptions, thrownError) {
                debugger;
            }
        };
        $.ajax(ac2);

        jQuery('#btnReplyMail, #btnForwardMail').addClass('pictogramButton__changing');
        //window.top.na.s.c.grayscale ('pictogramButton__changing', 50, false, document);
        setTimeout (function() {
            jQuery('#btnReplyMail, #btnForwardMail').removeClass ('pictogramButton__off').removeClass('pictogramButton__changing');
        }, 2500);
    },

    switchToMailbox : function (event) {
        na.apps.loaded['app.2D.webmail.v1.0.0'].highlightNoMailboxes(event);
    },

    highlightNoMailboxes : function (event) {
        jQuery('#wmLeft div.selected').removeClass('selected');

        na.apps.loaded['app.2D.webmail.v1.0.0'].highlightNoMails();
        
        $('#siteContent .vividButton_icon_50x50').each(function(idx,el) {
            na.site.settings.buttons['#'+el.id].disable();
        });
        
        jQuery('#wmEmail')[0].contentDocument.open();
        jQuery('#wmEmail')[0].contentDocument.write('');
        jQuery('#wmEmail')[0].contentDocument.close();
        
        na.apps.loaded['app.2D.webmail.v1.0.0'].settings.readyToPaint__wmMails_table = false;
        jQuery('#wmMails_table').fadeOut('fast', function () {
            jQuery('#wmMails_table > tbody')[0].innerHTML = '';
            na.apps.loaded['app.2D.webmail.v1.0.0'].settings.readyToPaint__wmMails_table = true;
            jQuery('#wmMails_table').fadeIn('fast');
        });
        
        let evt = event;
        if ( !jQuery('.bgMailboxName')[0] ) {
            na.apps.loaded['app.2D.webmail.v1.0.0'].highlightMailbox (evt);
        } else {
            jQuery('.bgMailboxName').fadeOut('normal', function() {
                jQuery('.bgMailboxName').remove();
                na.apps.loaded['app.2D.webmail.v1.0.0'].highlightMailbox (evt);
            });
        }
    },

    highlightMailbox : function (evt) {
        if (!jQuery('.bgMailboxName')[0]) {
            var bgHTML = '<div class="bgMailboxName" style="position:absolute;width:'+($('#siteToolbarLeft').width()-42)+'px;height:'+jQuery(evt.currentTarget).height()+'px;z-index:-1">&nbsp;</div>';
            jQuery('#wmLeft').append (bgHTML);
        }
            
        var t = evt.target;//evt.currentTarget;
        jQuery(t).addClass ('selected');
        jQuery('.bgMailboxName').css ({
            top : jQuery(t).position().top,// + jQuery('#wmLeft').position().top,
            left : jQuery(t).position().left
        });
        
        var s = na.site.settings;
        s.buttons['#btnWriteMail'].enable();
        s.buttons['#btnPageUp'].enable();
        s.buttons['#btnPageDown'].enable();
        s.buttons['#btnFilter'].enable();
        s.buttons['#btnEditServer'].disable();
        s.buttons['#btnDeleteServer'].disable();
        
        jQuery('.mailserverName').removeClass('selected');
        jQuery(t).parent().find('.mailserverName').addClass('selected');
        
        $('#wmMails_header_table2 th').fadeIn('normal');
    },

    highlightMailserver : function (evt) {
        if (!jQuery('.bgMailboxName')[0]) {
            var bgHTML = '<div class="bgMailboxName" style="position:absolute;width:'+($('#siteToolbarLeft').width()-42)+'px;height:'+jQuery(evt.currentTarget).height()+'px;z-index:-1">&nbsp;</div>';
            jQuery('#wmLeft').append (bgHTML);
        }
        
        var t = evt.currentTarget;
        jQuery('.bgMailboxName').css ({
            top : jQuery(t).position().top,// + jQuery('#wmLeft').position().top,
            left : jQuery(t).position().left
        });
            
        var t = evt.currentTarget;
        jQuery('.mailserverName, .mailboxName').removeClass('selected');
        jQuery(t).addClass ('selected');
        
        var s = na.site.settings;
        s.buttons['#btnEditServer'].enable();
        s.buttons['#btnDeleteServer'].enable();
        
        var div = $('#siteContent')[0];
        $('.vividButton, .vividButton_icon_50x50', div).each(function(idx,el){
            var btn = new naVividButton(el);
            na.site.settings.buttons['#'+el.id] = btn;
            btn.disable();
        });
        
        jQuery('#wmEmail')[0].contentDocument.open();
        jQuery('#wmEmail')[0].contentDocument.write('');
        jQuery('#wmEmail')[0].contentDocument.close();
        
        jQuery('#wmMails_table').fadeOut('fast', function () {
            jQuery('#wmMails_table > tbody')[0].innerHTML = '';
            jQuery('#wmMails_table').fadeIn('fast');
        });
    },

    highlightNoMails : function () {
        jQuery('#td_right_top tr.selected').removeClass('selected');
        jQuery('.bgMailInfo').fadeOut('normal',function() {
            $('.bgMailInfo').remove();
        });
    },

    highlightMail : function (evt) {
        if (!jQuery('.bgMailInfo')[0]) {
            var bgHTML = '<div class="bgMailInfo" style="position:absolute;width:'+($('#siteContent .vividDialogContent').width() )+'px;height:'+jQuery(evt.currentTarget).height()+'px;z-index:-1">&nbsp;</div>';
            jQuery('#wmMails').append (bgHTML);
        }
            
        var t = evt.currentTarget;
        jQuery(t).addClass ('selected');
        jQuery('.bgMailInfo').css ({
            background : 'rgba(255,255,255,0.7)',
            color : 'lime',
            top : jQuery(t).position().top + jQuery('#wmMails_table').position().top,
            left : jQuery(t).position().left + jQuery('.mailDate').position().left + 12,
            width : $('#siteContent').width() - 50
        });
        
        var s = na.site.settings;
        s.buttons['#btnReplyMail'].enable();
        s.buttons['#btnForwardMail'].enable();
    },

    highlightNoMailThreads : function () {
        jQuery('#wmMailThreads tr.selected').removeClass('selected');
        jQuery('.bgMailInfo').remove();
    },

    highlightMailThread : function (evt) {
        if (!jQuery('.bgMailThreadInfo')[0]) {
            var bgHTML = '<div class="bgMailThreadInfo" style="position:absolute;width:100%;height:'+jQuery(evt.currentTarget).height()+'px;z-index:-1">&nbsp;</div>';
            jQuery('#wmThreadInfo').append (bgHTML);
        }
            
        var t = evt.currentTarget;
        jQuery(t).addClass ('selected');
        jQuery('.bgMailThreadInfo').css ({
            top : jQuery(t).position().top + 10,
            left : jQuery(t).position().left 
        });
    },

    bgMailInfoScroll : function (evt) {
        if (!jQuery('.mailInfo.selected')[0]) return false;
        var 
        t = jQuery('.mailInfo.selected')[0], 
        top = jQuery(t).position().top + jQuery('#wmMails_table').position().top + 10,
        opacity = top > $(t).height() - top && top < $('#wmMails').height() + top - $(t).height() ? 1 : 0.001;
        if (t) jQuery('.bgMailInfo')
            .stop(true,true)
            .animate({opacity : opacity}, 'fast')
            .css({ top : top });
    },

    bgMailboxNameScroll : function (evt) {
        if (!jQuery('.mailboxName.selected')[0]) return false;
        var 
        t = jQuery('.mailboxName.selected')[0],
        top = jQuery(t).position().top,
        opacity = top > $(t).height() - top && top < $('#wmLeft').height() + top - $(t).height() ? 1 : 0.001;
        if (t) jQuery('.bgMailboxName')
            .stop(true,true)
            .animate({opacity : opacity}, 'fast')
            .css ({
                top : top,
                left : jQuery(t).position().left + jQuery('#td_left').position.left
            });
    },

    replyMail : function () {
        var
        mail = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.currentMail,
        md = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.currentMailData,
        mail = 
            'In reply to '+md.from+' dated '+md.date+' :<br/>\r\n'
            +mail.replace('\r\n','\r\n');
        
        jQuery('#btnForwardMail').addClass('pictogramButton__changing7');
        //window.top.na.s.c.grayscale ('pictogramButton__changing7', 50, true, document);
        setTimeout (function() {
            jQuery('#btnForwardMail').addClass ('pictogramButton__off').removeClass('pictogramButton__changing7');
        }, 1000);
            
        na.apps.loaded['app.2D.webmail.v1.0.0'].writeMail (null, mail);
    },

    forwardMail : function () {
        var
        mail = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.currentMail,
        md = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.currentMailData,
        mail = 
            '<br/>\r\n<br/>\r\n<br/>\r\n<br/>\r\n'
            +'----------------------------------------<br/>\r\n'
            +'Forwarded message, originally from '+md.from+' dated '+md.date+' :<br/>\r\n'
            +'----------------------------------------<br/>\r\n'
            +'\r\n'
            +mail.replace('\r\n','\r\n');
            
        jQuery('#btnReplyMail').addClass('pictogramButton__changing7');
        //window.top.na.s.c.grayscale ('pictogramButton__changing7', 50, true, document);
        setTimeout (function() {
            jQuery('#btnReplyMail').addClass ('pictogramButton__off').removeClass('pictogramButton__changing7');
        }, 1000);
        
        na.apps.loaded['app.2D.webmail.v1.0.0'].writeMail (null, mail);
    },

    writeMail : function (evt, editorData) {
        var 
        s = na.apps.loaded['app.2D.webmail.v1.0.0'].settings,
        pathTiny = '/NicerAppWebOS/3rd-party/tinymce-4/',
        tinyMCEid = 'tinymce';
        
        jQuery('#wmWriteMail').css ({ 
            height : jQuery('#td_right').height() - (jQuery('#menubar').height()/2), // - jQuery('#siteMessages__dialog', window.top.document).position().top,
            width : jQuery('#td_right').width() 
        });
        
        jQuery('#wmMails, #wmThreadInfo, #td_right_bottom, #wmMails_header_table2').fadeOut('fast', function () {
            jQuery('th.mailFrom, th.mailSubject, th.mailDate, th.totalMsgsInThread').fadeOut('normal');
            jQuery('#wmWriteMail').fadeIn('fast').css({ 
                width : jQuery('#td_right_top').width()-10, 
                height : jQuery('#wmOuter').height()-jQuery('#wmMails_header_table').height()-90, 
                top : jQuery('#wmMails_header_table').height()+5 
            });
        });

        jQuery('#btnWriteMail, .btnUp, .btnDown').addClass('pictogramButton__changing4');
        //window.top.na.s.c.grayscale ('pictogramButton__changing4', 50, true, document);
        setTimeout (function() {
            jQuery('#btnWriteMail, .btnUp, .btnDown').addClass ('pictogramButton__off').removeClass('pictogramButton__changing4');
        }, 1000);
        
        jQuery('#btnSendMail').addClass('pictogramButton__changing5');
        //window.top.na.s.c.grayscale ('pictogramButton__changing5', 50, false, document);
        setTimeout (function() {
            jQuery('#btnSendMail').removeClass ('pictogramButton__off').removeClass('pictogramButton__changing5');
        }, 1000);
        

        
        var postTinyInit = function () {
            //debugger;
            //tinymce.get(tinyMCEid).setContent (editorData);
            $('.mce-edit-area').css({ height : $('#wmWriteMail').height() - $('#wmWriteMail_header_table').height() });
            var s = na.site.settings;
            s.buttons['#btnSendMail'].enable();
            
        };
        
        if (!s.tmce && jQuery('#'+tinyMCEid)[0]) {
        s.tmce = tinymce.init({
            selector: '#'+tinyMCEid,
            //init_instance_callback : na.s.c.HTMLeditorInitialized,
            init_instance_callback : postTinyInit,
            allow_script_urls : true,
            height: '100%',
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor textcolor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table contextmenu paste code help'
            ],
            external_plugins : {
                'emoticons' : pathTiny+'plugins/naEmoticons/plugin.min.js'
            },
            toolbar: 'insert | undo redo | cut copy | formatselect | fontselect | fontsizeselect | emoticons | bold italic backcolor forecolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            toolbar_items_size: 'small',
            content_css: [
                pathTiny+'themes/whiteTransparent/content.min.css',
                'https://fonts.googleapis.com/css?family=ABeeZee|Aclonica|Acme|Actor|Advent+Pro|Akronim|Alex+Brush|Architects+Daughter|Archivo+Black|Baloo|Bebas+Neue|Caveat|Chewy|Cookie|Cormorant|Courgette|Covered+By+Your+Grace|Dancing+Script|El+Messiri|Exo|Exo+2|Galada|Gloria+Hallelujah|Great+Vibes|Handlee|Indie+Flower|Kalam|Kaushan+Script|Khula|Knewave|Krona+One|Lacquer|Lemonada|Lusitana|M+PLUS+1p|Marck+Script|Merienda+One|Modak|Montserrat|Montserrat+Alternates|Mr+Dafoe|Nanum+Pen+Script|Noto+Serif+JP|Odibee+Sans|Oleo+Script|Orbitron|PT+Sans|Parisienne|Pathway+Gothic+One|Permanent+Marker|Playball|Pridi|Quattrocento+Sans|Rock+Salt|Sacramento|Saira+Condensed|Saira+Extra+Condensed|Saira+Semi+Condensed|Satisfy|Shadows+Into+Light|Shadows+Into+Light+Two|Sigmar+One|Signika+Negative|Slabo+27px|Source+Code+Pro|Special+Elite|Spectral|Spinnaker|Sriracha|Unica+One|Acme|Lato:300,300i,400,400i|Montserrat|Mukta+Malar|Ubuntu|Indie+Flower|Raleway|Pacifico|Fjalla+One|Work+Sans|Gloria+Hallelujah&display=swap',
                //'http://www.tinymce.com/css/codepen.min.css',
                pathTiny+'themes/whiteTransparent/content.na.css'
            ],
            font_formats: 'ABeeZee=ABeeZee;Aclonica=Aclonica;Actor=Actor;Advent Pro=Advent Pro;Akronim=Akronim;Alex Brush=Alex Brush;Architects Daughter=Architects Daughter;Archivo Black=Archivo Black;Baloo=Baloo;Bebas Neue=Bebas Neue;Caveat=Caveat;Chewy=Chewy;Cookie=Cookie;Cormorant=Cormorant;Courgette=Courgette;Covered By Your Grace=Covered By Your Grace;Dancing Script=Dancing Script;El Messiri=El Messiri;Exo=Exo;Exo 2=Exo 2;Galada=Galada;Great Vibes=Great Vibes;Kalam=Kalam;Kaushan Script=Kaushan Script;Khula=Khula;Knewavel=Knewavel;Krona One=Krona One;Lacquer=Lacquer;Lemonada=Lemonada;Lusitana=Lusitana;M PLUS 1p=M PLUS 1p;Marck Script=Marck Script;Merienda One=Merienda One;Modak=Modak;Montserat Alternates=Montserrat Alternates;Mr Dafoe=Mr Dafoe;Nanum Pen Script=Nanum Pen Script;Noto Serif JP=Noto Serif JP;Odibee Sans=Odibee Sans;Oleo Script=Oleo Script;Orbitron=Orbitron;PT Sans=PT Sans;Parisienne=Parisienne;Pathway Gothic One=Pathway Gothic One;Permanent Marker=Permanent Marker;Playball=Playball;Pridi=Pridi;Quattrocento Sans=Quattrocento Sans;Rock Salt=Rock Salt;Sacramento=Sacramento;Saira Condensed=Saira Condensed;Saira Extra Condensed=Saira Extra Condensed;Saira Semi Condensed=Saira Semi Condensed;Satisfy=Satisfy;Shadows Into Light=Shadows Into Light;Shadows Into Light Two=Shadows Into Light Two;Sigmar Once=Sigmar One;Signika Negative=Signika Negative;Slabo 27px=Slabo 27px;Source Code Pro=Source Code Pro;Special Elite=Special Elite;Spectral=Spectral;Spinnaker=Spinnaker;Sriracha=Sriracha;Unica One=Unica One;Acme=Acme;Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Fjalla One=Fjalla One;Georgia=georgia,palatino;Gloria Hallelujah=Gloria Hallelujah;Helvetica=helvetica;Impact=impact,chicago;Indie Flower=Indie Flower;Montserrat=Montserrat;Mukta Malar=Mukta Malar;Pacifico=Pacifico;Raleway=Raleway;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Ubuntu=Ubuntu;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats;Work Sans=Work Sans',
            editor_css : pathTiny+'themes/whiteTransparent/editor.na.css',
            skin_url: pathTiny + 'themes/whiteTransparent',
            //link_list : na.tree.settings.tinyMCE_link_list,
            relative_urls : false
        }); } else postTinyInit();
    },

    sendMail : function (editorData) {
        var
        config = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.config,
        mboxes = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mailboxes,
        mails = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.mails,
        view = na.apps.loaded['app.2D.webmail.v1.0.0'].settings.view;
        
        mailFrom = jQuery('#select_mailFrom'),
        mailFromDomain = mailFrom.val().replace(/.*@/,''),
        mailTo = jQuery('#input_mailTo'),
        mailSubject = jQuery('#input_mailSubject'),
        mailHeaders = $.extend({},na.apps.loaded['app.2D.webmail.v1.0.0'].settings.headers);
        
        mailHeaders['X-Original-From'] = mailHeaders.From;
        delete mailHeaders.From;
        mailHeaders['X-Original-To'] = mailHeaders.To;
        delete mailHeaders.To;
        mailHeaders.subject = mailSubject.val();//'Fwd: '+mailHeaders.subject;
        mailHeaders['Return-Path'] = mailFrom.val();
        
        for (var i=0; i<config.mailServers.length; i++) {
            var ms = config.mailServers[i];
            if (ms.SMTP.domain.indexOf(mailFromDomain)!==-1) break;
        };

        if (i!==config.mailServers.length) {
            var ac = {
                type : 'POST',
                url : '/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/ajax_send_mail.php',
                data : {
                    serverConfig : ms,
                    mailFrom : mailFrom.val(),
                    mailTo : mailTo.val(),
                    mailSubject : mailSubject.val(),
                    mailHeaders : mailHeaders,
                    mailBody : na.apps.loaded['app.2D.webmail.v1.0.0'].mailHTML (tinymce.get('tinymce').getContent())
                },
                success : function (data, ts, xhr) {
                    if (data!=='SUCCESS') alert (data);
                },
                error : function (xhr, ajaxOptions, thrownError) {
                    debugger;
                }
            };
            debugger;
            $.ajax(ac);
        };
    },

    mailHTML : function (bodyHTML) {
        var utf8 = 
            '<link href="https://fonts.googleapis.com/css?family=Architects+Daughter|ABeeZee|Aclonica|Acme|Actor|Advent+Pro|Akronim|Alex+Brush|Archivo+Black|Baloo|Bebas+Neue|Caveat|Chewy|Cookie|Cormorant|Courgette|Covered+By+Your+Grace|Dancing+Script|El+Messiri|Exo|Exo+2|Galada|Gloria+Hallelujah|Great+Vibes|Handlee|Indie+Flower|Kalam|Kaushan+Script|Khula|Knewave|Krona+One|Lacquer|Lemonada|Lusitana|M+PLUS+1p|Marck+Script|Merienda+One|Modak|Montserrat|Montserrat+Alternates|Mr+Dafoe|Nanum+Pen+Script|Noto+Serif+JP|Odibee+Sans|Oleo+Script|Orbitron|PT+Sans|Parisienne|Pathway+Gothic+One|Permanent+Marker|Playball|Pridi|Quattrocento+Sans|Rock+Salt|Sacramento|Saira+Condensed|Saira+Extra+Condensed|Saira+Semi+Condensed|Satisfy|Shadows+Into+Light|Shadows+Into+Light+Two|Sigmar+One|Signika+Negative|Slabo+27px|Source+Code+Pro|Special+Elite|Spectral|Spinnaker|Sriracha|Unica+One|Acme|Lato:300,300i,400,400i|Montserrat|Mukta+Malar|Ubuntu|Indie+Flower|Raleway|Pacifico|Fjalla+One|Work+Sans|Gloria+Hallelujah&display=swap" rel="stylesheet">\r\n'
            +bodyHTML;
        return utf8;
    },

    sent : function (date, idx) {
        var
        now = new Date(),
        sent = new Date(date),
        nowStr = now.toUTCString(),
        sentStr = now.toUTCString(),
        nowUTC = new Date(Date.UTC(now.getFullYear(), now.getMonth(), now.getDay(), now.getHours(), now.getMinutes(), now.getSeconds(), now.getMilliseconds())),
        sentUTC = new Date(Date.UTC(sent.getFullYear(), sent.getMonth(), sent.getDay(), sent.getHours(), sent.getMinutes(), sent.getSeconds(), sent.getMilliseconds())),
        delta = Math.abs((nowUTC.getTime() - sentUTC.getTime())/1000);
        
        var days = Math.floor(delta/86400);
        delta -= days * 86400;
        
        var hours = Math.floor(delta/3600);
        delta -= hours * 3600;
        
        var minutes = Math.floor(delta/60) % 60;
        delta -= minutes * 60;
        
        var seconds = Math.round(delta % 60);
        
        var 
        dateRemote = date,//new Intl.DateTimeFormat ('en-GB', { dateStyle : 'full', timeStyle : 'long'}).format(sent),
        dateLocal =  new Intl.DateTimeFormat ('en-GB', { dateStyle : 'full', timeStyle : 'long'}).format(new Date(sent.toLocaleString()));
        
        var str = '<span class="sentDifference naWebMail_datetimeDiffShort" idx="'+idx+'" dateSent="'+dateRemote+'" dateSentLocal="'+dateLocal+'" dateDiff="';
        if (days>1) str += days+' days, ';
        else if (days===1) str += days+' day, ';
        if (hours>1) str += hours+' hours, ';
        else if (hours===1) str += hours+' hour, ';
        if (minutes>1) str += minutes+' minutes, ';
        else if (minutes===1) str += minutes+' minute, ';
        if (seconds>1) str += seconds+' seconds';
        else if (seconds===1) str += seconds+' second';
        str += ' ago.">-';
        if (days>0) str += days+'d ';
        if (hours>0) str += hours+'h ';
        if (minutes>0) str += minutes+'m ';
        //if (seconds>0) str += seconds+'s';
        str += '</span>';
        
        return str;
    }
}
}, 100);
