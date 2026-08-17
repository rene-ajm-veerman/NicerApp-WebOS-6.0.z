<?php 
    require_once (dirname(__FILE__).'/../../../boot.php');
    require_once (dirname(__FILE__).'/functions.php');
    global $naWebOS;
    
    $theme = 'default';
    if (array_key_exists('cdb_loginName', $_SESSION) && ($_SESSION['cdb_loginName']!=='' || $_SESSION['cdb_loginName']!=='Guest')) {
        $theme = $_SESSION['cdb_loginName'];
    };
    //if (array_key_exists('theme',$_POST)) $theme = $_POST['theme'];
    //if (array_key_exists('theme',$_GET)) $theme = $_GET['theme'];
    
?>
<!--<html>
<head>-->
    <link type="text/css" rel="stylesheet" media="screen" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!--<link type="text/css" rel="stylesheet" media="screen" href="/NicerAppWebOS/index.css">-->
    <link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/webmail-default.css?changed=<?php echo webmail_get_current_datetime_stamp();?>">
    <link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/webmail-<?php echo $theme?>.css?changed=<?php echo webmail_get_current_datetime_stamp();?>">
    <!-- see fonts.google.com -->
    <link href="https://fonts.googleapis.com/css?family=ABeeZee|Aclonica|Acme|Actor|Advent+Pro|Akronim|Alex+Brush|Architects+Daughter|Archivo+Black|Baloo|Bebas+Neue|Caveat|Chewy|Cookie|Cormorant|Courgette|Covered+By+Your+Grace|Dancing+Script|El+Messiri|Exo|Exo+2|Galada|Gloria+Hallelujah|Great+Vibes|Handlee|Indie+Flower|Kalam|Kaushan+Script|Khula|Knewave|Krona+One|Lacquer|Lemonada|Lusitana|M+PLUS+1p|Marck+Script|Merienda+One|Modak|Montserrat|Montserrat+Alternates|Mr+Dafoe|Nanum+Pen+Script|Noto+Serif+JP|Odibee+Sans|Oleo+Script|Orbitron|PT+Sans|Parisienne|Pathway+Gothic+One|Permanent+Marker|Playball|Pridi|Quattrocento+Sans|Rock+Salt|Sacramento|Saira+Condensed|Saira+Extra+Condensed|Saira+Semi+Condensed|Satisfy|Shadows+Into+Light|Shadows+Into+Light+Two|Sigmar+One|Signika+Negative|Slabo+27px|Source+Code+Pro|Special+Elite|Spectral|Spinnaker|Sriracha|Unica+One|Acme|Lato:300,300i,400,400i|Montserrat|Mukta+Malar|Ubuntu|Indie+Flower|Raleway|Pacifico|Fjalla+One|Work+Sans|Gloria+Hallelujah&display=swap" rel="stylesheet"> <!-- see index.css and na.site.code.source.js (tinyMCE section) for where it's (primarily) used -->
    <script type="text/javascript"  src="/NicerAppWebOS/3rd-party/tinymce-4.9.11/js/tinymce/tinymce.min.js"></script>
    <link rel="stylesheet" href="/NicerAppWebOS/3rd-party/tinymce-4/themes/charcoal/skin.min.css">
    <script src="/NicerAppWebOS/3rd-party/jQuery/jquery-ui-1.12.1/jquery-ui.js"></script>
    <script type="text/javascript"  src="/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/naWebMail-1.1.0.source.js?changed=<?php echo webmail_get_current_datetime_stamp();?>"></script>
    <!--<script src="https://smtpjs.com/v3/smtp.js"></script>-->
<!--</head>
<body onload="$(document).ready(function() { debugger; nawm.onload()});" onresize="naWebMail.onresize(event);">-->
    <div id="wmSettings">
        <form id="form_wmSettings">
            <span class="wms_header_form">Server Settings</span><br/><br/>
            
            <label class="wms_label" for="wms_displayName">Your name : </label>
            <input id="wms_displayName" name="wms_displayName" type="text" autocomplete="name"/><br/>

            
            
            <span class="wms_header">Local IMAP server settings</span><br/>
            <label class="wms_label" for="wms_localIMAP_domain">Server name : </label>
            <input id="wms_localIMAP_domain" name="wms_localIMAP_domain" type="text" value="<?php echo $naWebOS->domainFolder?>"/><br/>
            
            <label class="wms_label" for="wms_localIMAP_port">Port : </label>
            <input id="wms_localIMAP_port" name="wms_localIMAP_port" type="number" min="0" max="65535" value="993"/><br/>
            
            <label class="wms_label" for="wms_localIMAP_requiresSSL">SSL : </label>
            <input id="wms_localIMAP_requiresSSL" name="wms_localIMAP_requiresSSL" type="checkbox" checked/><br/>
            
            <label class="wms_label" for="wms_localIMAP_sslCertificateCheck">SSL Certificate : </label>
            <input id="wms_localIMAP_sslCertificateCheck" name="wms_localIMAP_sslCertificateCheck" type="checkbox" checked/><br/><br/>

            <label class="wms_label" for="wms_localIMAP_userID">userID (this user will be created in the OS and in the IMAP server if it does not exist there already) : </label>
            <input id="wms_localIMAP_userID" name="wms_localIMAP_userID" type="text" autocomplete="email"/><br/>
            
            <label class="wms_label" for="wms_localIMAP_userPassword">Password : </label>
            <input id="wms_localIMAP_userPassword" name="wms_localIMAP_userPassword" type="password" autocomplete="off"/><br/><br/>
            
            
            
            

            <span class="wms_header">Remote IMAP server settings (optional)</span><br/>
            <span class="wms_header_sub_line">(Remote IMAP server account data will be auto-synchronized with the local server every 5 minutes, and remote IMAP servers often provide a more stable data storage option for a startup company than relying solely on your own IMAP server)</span><br/>
            <label class="wms_label" for="wms_remoteIMAP_enable">Enable : </label>
            <input id="wms_remoteIMAP_enable" name="wms_remoteIMAP_enable" type="checkbox"/><br/>

            <label class="wms_label" for="wms_remoteIMAP_domain">Server name : </label>
            <input id="wms_remoteIMAP_domain" name="wms_remoteIMAP_domain" type="text" value="gmail.com"/><br/>
            
            <label class="wms_label" for="wms_remoteIMAP_port">Port : </label>
            <input id="wms_remoteIMAP_port" name="wms_remoteIMAP_port" type="number" min="0" max="65535" value="993"/><br/>
            
            <label class="wms_label" for="wms_remoteIMAP_requiresSSL">SSL : </label>
            <input id="wms_remoteIMAP_requiresSSL" name="wms_remoteIMAP_requiresSSL" type="checkbox" checked/><br/>
            
            <label class="wms_label" for="wms_remoteIMAP_sslCertificateCheck">SSL Certificate : </label>
            <input id="wms_remoteIMAP_sslCertificateCheck" name="wms_remoteIMAP_sslCertificateCheck" type="checkbox" checked/><br/><br/>
            
            <label class="wms_label" for="wms_remoteIMAP_userID">userID : </label>
            <input id="wms_remoteIMAP_userID" name="wms_remoteIMAP_userID" type="text" autocomplete="email"/><br/>
            
            <label class="wms_label" for="wms_remoteIMAP_userPassword">Password : </label>
            <input id="wms_remoteIMAP_userPassword" name="wms_remoteIMAP_userPassword" type="password" autocomplete="off"/><br/><br/>
            

            
            
            
            <span class="wms_header">Local SMTP server settings</span><br/>
            <label class="wms_label" for="wms_smtp_domain">Server name : </label>
            <input id="wms_smtp_domain" name="wms_smtp_domain" type="text" value="<?php echo $naWebOS->domainFolder?>"/><br/>
            
            <label class="wms_label" for="wms_smtp_requiresAuthentication">Requires authentication : </label>
            <input id="wms_smtp_requiresAuthentication" name="wms_smtp_requiresAuthentication" type="checkbox" checked/><br/>

            <label class="wms_label" for="wms_smtp_requiresSSL">SSL : </label>
            <input id="wms_smtp_requiresSSL" name="wms_smtp_requiresSSL" type="checkbox" checked/><br/>

            <label class="wms_label" for="wms_smtp_sslPort">SSL Port : </label>
            <input id="wms_smtp_sslPort" name="wms_smtp_sslPort" type="number" min="0" max="65535" value="465"/><br/>
            
            <label class="wms_label" for="wms_smtp_requiresTLS">TLS : </label>
            <input id="wms_smtp_requiresTLS" name="wms_smtp_requiresTLS" type="checkbox" checked/><br/>

            <label class="wms_label" for="wms_smtp_tlsPort">TLS Port : </label>
            <input id="wms_smtp_tlsPort" name="wms_smtp_tlsPort" type="number" min="0" max="65535" value="587"/><br/>

            <label class="wms_label" for="wms_smtp_usePEAR">Use PEAR : </label>
            <input id="wms_smtp_usePEAR" name="wms_smtp_usePEAR" type="checkbox" checked/><br/>
            
            
            
            <div class="buttonHolder">
                <button id="btnSaveSettings" type="button" class="button" onclick="na.apps.loaded['app.2D.webmail.v1.0.0'].saveSettings(event);" style="margin:0"><span>Save</span></button>
            </div>
            
        </form>
    </div>
    <table id="wmOuter" cellpadding="10">
    <tr id="tr_top" style="">
        <td id="td_right_top">
            <div id="wmWriteMail">
                <table id="wmWriteMail_header_table" style="width:calc(100%);margin-bottom:4px;">
                <tr>
                    <th>From : </th>
                    <th><select id="select_mailFrom" style="background:white;color:black;width:calc(100% - 50px)"></select></th>
                    <th>To : </th>
                    <th><input id="input_mailTo"></input></th>
                    <th>Subject : </th>
                    <th><input id="input_mailSubject"></input></th>
                </tr>
                </table>
                <textarea id="tinymce" name="tinymce"></textarea>
            </div>
            <div id="wmThreadInfo" style="position:absolute;"></div>
            <div id="wmMails_header">
                <div id="wmMails_header_table" style="width:100%;">
                    <div id="btnWriteMail" class="vividButton_icon_50x50 tooltip" title="Write mail" alt="Write mail"  onclick="if (!$(this).is('.disabled')) naWebMail.writeMail()">
                        <div class="vividButton_icon_borderCSS_50x50"></div>
                        <img class="vividButton_icon_imgTile_50x50" src="/siteMedia/btnCssVividButton.grey3a.png"/>
                        <img class="vividButton_icon_imgButtonIconBG_50x50" src="/siteMedia/btnCssVividButton_iconBackground.png"/>
                        <img class="vividButton_icon_imgButtonIcon_50x50" src="/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/images/writeMail6.png"/>
                    </div>
                    <div id="btnSendMail" class="vividButton_icon_50x50 tooltip" title="Send mail" alt="Send mail"  onclick="if (!$(this).is('.disabled')) naWebMail.sendMail()">
                        <div class="vividButton_icon_borderCSS_50x50"></div>
                        <img class="vividButton_icon_imgTile_50x50" src="/siteMedia/btnCssVividButton.grey3a.png"/>
                        <img class="vividButton_icon_imgButtonIconBG_50x50" src="/siteMedia/btnCssVividButton_iconBackground.png"/>
                        <img class="vividButton_icon_imgButtonIcon_50x50" src="/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/images/sendMail.png"/>
                    </div>
                    <div id="btnReplyMail" class="vividButton_icon_50x50 tooltip" title="Reply to mail" alt="Reply to mail"  onclick="if (!$(this).is('.disabled')) naWebMail.replyMail()">
                        <div class="vividButton_icon_borderCSS_50x50"></div>
                        <img class="vividButton_icon_imgTile_50x50" src="/siteMedia/btnCssVividButton.grey3a.png"/>
                        <img class="vividButton_icon_imgButtonIconBG_50x50" src="/siteMedia/btnCssVividButton_iconBackground.png"/>
                        <img class="vividButton_icon_imgButtonIcon_50x50" src="/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/images/replyMail.png"/>
                    </div>
                    <div id="btnForwardMail" class="vividButton_icon_50x50 tooltip" title="Forward mail" alt="Forward mail"  onclick="if (!$(this).is('.disabled')) naWebMail.replyMail()">
                        <div class="vividButton_icon_borderCSS_50x50"></div>
                        <img class="vividButton_icon_imgTile_50x50" src="/siteMedia/btnCssVividButton.grey3a.png"/>
                        <img class="vividButton_icon_imgButtonIconBG_50x50" src="/siteMedia/btnCssVividButton_iconBackground.png"/>
                        <img class="vividButton_icon_imgButtonIcon_50x50" src="/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/images/forwardMail.png"/>
                    </div>
                    <div id="btnPageUp" class="vividButton_icon_50x50 tooltip" title="Page up" alt="Page up"  onclick="if (!$(this).is('.disabled')) naWebMail.getMailboxContent_nextPage()">
                        <div class="vividButton_icon_borderCSS_50x50"></div>
                        <img class="vividButton_icon_imgTile_50x50" src="/siteMedia/btnCssVividButton.grey3a.png"/>
                        <img class="vividButton_icon_imgButtonIconBG_50x50" src="/siteMedia/btnCssVividButton_iconBackground.png"/>
                        <img class="vividButton_icon_imgButtonIcon_50x50" src="/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/images/btnUp.png"/>
                    </div>
                    <div id="btnPageDown" class="vividButton_icon_50x50 tooltip" title="Page down" alt="Page down"  onclick="if (!$(this).is('.disabled')) naWebMail.getMailboxContent_prevPage()">
                        <div class="vividButton_icon_borderCSS_50x50"></div>
                        <img class="vividButton_icon_imgTile_50x50" src="/siteMedia/btnCssVividButton.grey3a.png"/>
                        <img class="vividButton_icon_imgButtonIconBG_50x50" src="/siteMedia/btnCssVividButton_iconBackground.png"/>
                        <img class="vividButton_icon_imgButtonIcon_50x50" src="/NicerAppWebOS/apps/nicer.app/app.2D.webmail.v1.0.0/images/btnDown.png"/>
                    </div>
                    <div id="btnFilter" class="vividButton_icon_50x50 tooltip" title="Filter settings" alt="Filter settings"  onclick="if (!$(this).is('.disabled')) naWebMail.displayFilterSettings()">
                        <div class="vividButton_icon_borderCSS_50x50"></div>
                        <img class="vividButton_icon_imgTile_50x50" src="/siteMedia/btnCssVividButton.green2a.png"/>
                        <img class="vividButton_icon_imgButtonIconBG_50x50" src="/siteMedia/btnCssVividButton_iconBackground.png"/>
                        <img class="vividButton_icon_imgButtonIcon_50x50" src="/siteMedia/filter.png"/>
                    </div>
                
                </div>
                <table id="wmMails_header_table2" style="width:100%">
                    <tr>
                        <th class="mailDate">Sent</th>
                        <th class="mailFrom">From</th>
                        <th class="mailSubject">Subject</th>
                        <th class="totalMsgsInThread">Total Msgs</th>
                    </tr>
                </table>                    
            </div>
            <div id="wmMails" class="vividScrollpane" onscroll="naWebMail.bgMailInfoScroll(event);">
                <table id="wmMails_table" style="width:100%">
                <tbody>
                </tbody>
                </table>                    
            </div>
        </td>
    </tr>
    <tr style="">
        <td id="td_right_bottom" cellpadding="5">
        <iframe id="wmEmail" style="width:97%;height:100%;"></iframe>
        </td>
    </tr>
    </table>
<!--</body>
</html>-->
