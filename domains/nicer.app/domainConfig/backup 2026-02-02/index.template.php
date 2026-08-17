<?php
require_once (dirname(__FILE__).'/mainmenu.php');
global $naWebOS;
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="apple-touch-icon" sizes="180x180" href="/NicerAppWebOS/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/NicerAppWebOS/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/NicerAppWebOS/favicon/favicon-16x16.png">
    <link rel="manifest" href="/NicerAppWebOS/favicon/site.webmanifest">
    <link rel="mask-icon" href="/NicerAppWebOS/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="/NicerAppWebOS/favicon/favicon.ico">
    <?php echo '<link type="text/css" rel="StyleSheet" href="/NicerAppWebOS/3rd-party/jsTree-3.3.15/dist/themes/default/style.css?c='.date('Ymd_His', filemtime(dirname(__FILE__).'/../NicerAppWebOS/3rd-party/jsTree-3.3.15/dist/themes/default/style.css')).'">'.PHP_EOL; ?>
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-config" content="/NicerAppWebOS/favicon/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    <meta name="description" content="Discover an open-source CMS and WebOS that lets you create stunning apps over dynamic backgrounds. Perfect for all ages to learn, play, and innovate!">
    <title>{$title}</title>
    <!-- see fonts.google.com (thanks, Google!) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|ABeeZee|Aclonica|Acme|Actor|Advent+Pro|Akronim|Alex+Brush|Architects+Daughter|Archivo+Black|Baloo|Bebas+Neue|Caveat|Chewy|Cookie|Cormorant|Courgette|Covered+By+Your+Grace|Dancing+Script|El+Messiri|Exo|Exo+2|Galada|Gloria+Hallelujah|Great+Vibes|Handlee|Indie+Flower|Kalam|Kaushan+Script|Khula|Knewave|Krona+One|Lacquer|Lemonada|Lusitana|M+PLUS+1p|Marck+Script|Merienda+One|Modak|Montserrat|Montserrat+Alternates|Mr+Dafoe|Nanum+Pen+Script|Noto+Serif+JP|Odibee+Sans|Oleo+Script|Orbitron|PT+Sans|Parisienne|Pathway+Gothic+One|Permanent+Marker|Playball|Pridi|Quattrocento+Sans|Rock+Salt|Sacramento|Saira+Condensed|Saira+Extra+Condensed|Saira+Semi+Condensed|Satisfy|Shadows+Into+Light|Shadows+Into+Light+Two|Sigmar+One|Signika+Negative|Slabo+27px|Source+Code+Pro|Special+Elite|Spectral|Spinnaker|Sriracha|Unica+One|Acme|Lato:300,300i,400,400i|Montserrat|Mukta+Malar|Ubuntu|Indie+Flower|Raleway|Pacifico|Fjalla+One|Work+Sans|Gloria+Hallelujah|Roboto+Condensed:ital,wght@0,100;0,200;0,300;1,100;1,200;1,300&display=swap" rel="stylesheet">
    <!--<link href="https://fonts.googleapis.com/css?family=Krona+One|Open+Sans|Architects+Daughter&display=swap" rel="stylesheet" onload="$(document).ready(function(){na.site.settingstartTooltips(event);});">-->

    <!--
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    Currently unstable, see https://github.com/jquery/jquery/issues/5721
    -->

{$javascriptLinks}
<!--<script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.0/chart.js" integrity="sha512-y5GdcvG8/LoqZllcaDnZKY4hKSDowG4vWAytStZ1AZ8sxgWke7KtkMx3G9KQU0Ih3kIDfY/3EO1sSyKGRxqwJQ==" crossorigin="anonymous" referrerpolicy="no-referrer">            import { Colors } from 'chart.js';

            Chart.register(Colors);
</script>-->
<script type="module" src="https://unpkg.com/chart.js@4.2.1/dist/chart.umd.js" integrity="sha512-vCUbejtS+HcWYtDHRF2T5B0BKwVG/CLeuew5uT2AiX4SJ2Wff52+kfgONvtdATqkqQMC9Ye5K+Td0OTaz+P7cw==" crossorigin="anonymous" referrerpolicy="no-referrer">import {
  Chart,
  ChartColors,
  Colors,
  BubbleController,
  CategoryScale,
  LinearScale,
  PointElement,
  Legend
} from 'chart.js'
Chart.register(ChartColors);
Chart.register(
  Colors,
  BubbleController,
  PointElement,
  CategoryScale,
  LinearScale,
  Legend
);
 </script>

{$cssLinks}

{$pageSpecificCSS}

    <script type="text/javascript">
        function siteOnLoad () {
            na.site.initialize ({$desktopDefinition});
        };
    </script>

</head>

<body onload="setTimeout(siteOnLoad,20);">
    <img style="z-index:-10;position:absolute;opacity:0.001;" src="https://said.by/NicerAppWebOS/documentation/screenshots-diary/2025%20A.D./Screenshot_20251101_124721-800x450.png"/>
    <div id="siteContent" class="vividDialog">
        <div class="vividDialogBackground1"></div>
        <div class="vividDialogContent vividScrollpane" style="display:inline-block;position:relative;">
            {$div_siteContent}
        </div>
    </div>

    <div id="siteToolbarTop" class="vividDialog">
        <div class="vividDialogBackground1"></div>
        <div class="vividDialogContent vividScrollpane">
            {$div_siteToolbarTop}
        </div>
    </div>

    <div id="siteToolbarLeft" class="vividDialog">
        <div class="vividDialogBackground1"></div>
        <div class="vividDialogContent vividScrollpane">
            {$div_siteToolbarLeft}
        </div>
    </div>

    <div id="siteToolbarRight" class="vividDialog">
        <div class="vividDialogBackground1"></div>
        <div class="vividDialogContent vividScrollpane">
            {$div_siteToolbarRight}
        </div>
    </div>



    <div class="lds-facebook"><!-- loading icon, thanks for allowing CC0 license usage : https://loading.io/css/ --><div></div><div></div><div></div></div>

    <div id="siteBackground">
        <div id="siteBackground_bg"></div>
        <div id="siteBackground_bg2"></div>
        <script id="siteBackground_iframe_js" type="text/javascript"></script>
        <iframe id="siteBackground_iframe"></iframe>
        <img class="bg_first" alt=""/>
        <img class="bg_last" alt=""/>
    </div>

    <div id="siteTaskbar" class="vividDialog naTaskbar naNoSettings" style="max-height:80px;">
      <div class="vividDialogBackground1"></div>
      <video class="naBorder_dont"
        hidden
        muted
        playsinline
        webkit-playsinline
        type="video/webm"
        >
        <!-- omitted attributes : autoplay, loop -->
        <source src="https://said.by/siteMedia/videos/output1.webm" type="video/webm"></source>
      </video>
      <canvas class="naBorder"></canvas>
      <video class="naBackground_dont"
        hidden
        muted
        playsinline
        webkit-playsinline
        type="video/webm"
        >
        <source src="https://said.by/siteMedia/videos/output3.webm" type="video/webm"></source>
      </video>
      <canvas class="naBackground"></canvas>
        <?php

        $desktopSettings = [
            'handlers' => [
                '#btnShowStartMenu' => [
                    'onmouseover' => '',
                    'onmouseout' => '',
                    'onclick' => ''
                ],
                '#btnSettings' => [
                    'onmouseover' => '',
                    'onmouseout' => '',
                    'onclick' => ''
                ]
            ]
        ];


        echo $naWebOS->html_vividButton (
            4, 'margin:0;position:relative;float:left;',

            'btnShowStartMenu', 'vividButton_icon_50x50 btnCloseWindow grouped', '_50x50', 'grouped',
            '',
            'if (!$(this).is(\'.disabled\')) { $(\'#siteLogin\').fadeOut(\'fast\').delay(20).animate({top:-750}); }',
            '',//'$(\'#siteStartMenu\').removeClass(\'hidden\').addClass(\'shown\')',
            '',//'$(\'#siteStartMenu\')[0].vividUserInterface_2D_dialog.hide({evt:event,checkHeldUp:\'#siteStartMenu\'});',

            7, null,

            null,//'btnCssVividButton_outerBorder.png',
            null,//'btnCssVividButton.png',
            null,//'btnCssVividButton.grey2a.png',
            'btnStartMenu.png',

            '',

            null,
            null,
            null
        );
        echo $naWebOS->html_vividButton (
            5, 'margin:0;position:relative;float:left;',

            'btnSettings', 'vividButton_icon_50x50 btnCloseWindow grouped', '_50x50', 'grouped',
            '',
            'if (!$(this).is(\'.disabled\')) { $(\'#siteLogin\').fadeOut(\'fast\').delay(20).animate({top:-750}); }; na.site.settings.heldUp[\'#siteSettingsMenu\'] = true; $(\'#siteSettingsMenu\').removeClass(\'hidden\').addClass(\'shown\')',
            '',
            '',//'$(\'#siteSettingsMenu\')[0].vividUserInterface_2D_dialog.hide({evt:event,checkHeldUp:\'#siteSettingsMenu\'});',

            7, null,

            null,//'btnCssVividButton_outerBorder.png',
            null,//'btnCssVividButton.png',
            'btnCssVividButton.purple3a.png',
            'btnSettings2.png',

            '',

            null,
            null,
            null
        );
        ?>
        <div id="siteDatetime"></div>
        <?php
        echo $naWebOS->html_vividButton (
            4, 'position:absolute;right:-8px;top:-8px;z-index:9000000',

            'btnInformation', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
            '',
            'if (!$(this).is(\'.disabled\')) { $(\'#siteLogin\').fadeOut(\'fast\').delay(20).animate({top:-750}); }',
            '',//'$(\'#siteStartMenu\').removeClass(\'hidden\').addClass(\'shown\')',
            '',//'$(\'#siteStartMenu\')[0].vividUserInterface_2D_dialog.hide({evt:event,checkHeldUp:\'#siteStartMenu\'});',

            7, null,

            null,//'btnCssVividButton_outerBorder.png',
            null,//'btnCssVividButton.png',
            null,//'btnCssVividButton.grey2a.png',
            'btnInformation.png',

            '',

            null,
            null,
            null
        );
        ?>
    </div>

    <div id="siteSettingsMenu" class="vividDialog naStartMenu naNoSettings hidden">
      <div class="vividDialogBackground1"></div>
        <?php

        $desktopSettings = [
            'handlers' => [
                '#btnSiteOptions' => [
                    'onmouseover' => '',
                    'onmouseout' => '',
                    'onclick' => ''
                ],
                '#btnLogin' => [
                    'onmouseover' => '',
                    'onmouseout' => '',
                    'onclick' => ''
                ]
            ]
        ];

        echo $naWebOS->html_vividButton (
            4, 'position:relative;',

            'btnSiteOptions', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
            '',
            '$(\'#btnSiteOptions_menu\').css({bottom : $(window).height()  - $(\'#btnSiteOptions\').offset().top, left : $(\'#btnSiteOptions\').offset().left}).fadeIn(\'normal\'); if (!$(this).is(\'.disabled\')) { $(\'#siteLogin\').fadeOut(\'fast\').animate({top:-750}); }',
            '',//'na.site.settings.heldUp[\'#btnOptions_menu\'] = true; if (!$(this).is(\'.disabled\')) { var me = $(\'#btnSiteOptions\'); o = me.offset(); $(\'#btnOptions_menu\').fadeIn(\'fast\').animate({bottom:$(window).height()-o.top-me.height()-25,left:o.left+me.width()+25}); }',
            '',//'if (!$(this).is(\'.disabled\')) { var me = $(\'#btnSiteOptions\'); o = me.offset(); /*$(\'#btnOptions_menu\').fadeOut(\'fast\').animate({top:-750 };*/ debugger; $(\'#btnOptions_menu\')[0].vividUserInterface_2D_dialog.hide({evt:event,checkHeldUp:\'#btnOptions_menu\'});',


            7, 'Site Options',

            'btnCssVividButton_outerBorder.png',
            'btnCssVividButton.png',
            null,//'btnCssVividButton.grey2a.png',
            'btnSettings2.png',

            '',

            'Site settings',
            'grouped mouseout',
            null
        );
        echo $naWebOS->html_vividButton (
            4, 'position:relative;',

            'btnLogin', 'vividButton_icon_50x50 btnCloseWindow grouped', '_50x50', 'grouped',
            '',
            'if (!$(this).is(\'.disabled\')) { $(\'#siteLogin\').fadeIn(\'fast\').animate({top:$(window).height()/2-$(\'#siteLogin\').height()/2}); }; $(\'#btnSiteOptions_menu\').fadeOut(\'normal\')',
            '',
            '',

            7, 'Login',

            'btnCssVividButton_outerBorder.png',
            'btnCssVividButton.png',
            null,//'btnCssVividButton.grey2a.png',
            'btnLogin.png',

            '',

            'Login',
            'grouped mouseout',
            null
        );
        ?>
    </div>


    <div id="btnSiteOptions_menu" class="vividDialogPopup anchored vividScrollpane" style="overflow:visible;padding:0;margin:0;"
        onmouseover="clearTimeout(na.site.settings.timeout_hide__btnSiteOptions_menu);"
        onmouseout="na.site.settings.timeout_hide__btnSiteOptions_menu = setTimeout(function(){ $('#btnSiteOptions_menu').fadeOut('fast'); }, 250);">
        <?php
        //onmouseover="na.site.onmouseover_btnOptions(event);" onmouseout="na.site.onmouseout_btnOptions(event)"
            global $naWebOS;
            $fn = realpath(dirname(__FILE__)).'/btnSiteOptions_menu__default.php';
            if (file_exists($fn)) echo require_return($fn);
        ?>
    </div>


    <div id="siteMenu" class="vividMenu vertical" controlledBy="na.desktop" tabindex="5" theme="{$theme}" avoid='{$siteMenu_avoid}'>
        <div id="siteMenu_vbChecker" class="vividButton vividButton_text vividMenu_item" theme="'+t.t+'" style="opacity:0.0001;position:absolute;">abc XYZ</div>
        <?php mainmenu_includeAllResolutionsAndSegments(); ?>
        <ul class="vividMenu_mainUL" style="display:none;"></ul>
    </div>

    <div id="siteRegistration" class="vividDialogPopup vividScrollpane">
        <div id="siteRegistration_background" class="vividDialogPopup_background"></div>
        <div id="siteRegistrationContainer">
            <form id="siteRegistrationForm" name="siteRegistrationForm" action="/register.php" method="POST">
                <label for="srf_loginName">Name</label>
                <input id="srf_loginName" name="srf_loginName" type="text"/><br/>

                <!--<label for="srf_email" class="tooltip" tooltipTheme="mainTooltipTheme" title="We'll be sending you a confirmation link to this address">E-mail</label>
                <input id="srf_email" name="srf_email" type="text" class="tooltip" tooltipTheme="mainTooltipTheme" title="We'll be sending you a confirmation link to this address"/><br/>-->
                <label for="srf_email">E-mail</label>
                <input id="srf_email" name="srf_email" type="text"/><br/>

                <label for="srf_pw1">Password</label>
                <input id="srf_pw1" name="srf_pw1" type="password" autocomplete="new-password"/><br/>

                <label for="srf_pw2">Repeat password</label>
                <input id="srf_pw2" name="srf_pw2" type="password" autocomplete="new-password"/><br/>
            </form>
            <p id="siteRegistrationError"></p>
            <?php
                global $naWebOS;
                echo $naWebOS->html_vividButton (
                    4, 'right:5px;',

                    'btnRegister', 'vividButton_icon_50x50 btnCloseWindow grouped', '_50x50', 'grouped',
                    '',
                    'if (!$(this).is(\'.disabled\')) { na.site.register(); }',
                    '',
                    '',

                    17, 'Register!',

                    'btnCssVividButton_outerBorder.png',
                    'btnCssVividButton.png',
                    null,//'btnCssVividButton.grey2a.png',
                    'btnRegister.png',

                    '',

                    'Register',
                    'grouped mouseout',
                    null
                );
            ?>
        </div>
    </div>


    <div id="siteLogin" class="vividDialogPopup vividScrollpane" style="" onmouseenter="$(event.currentTarget).css({opacity:1});">
        <div id="siteLogin_background" class="vividDialogPopup_background"></div>
        <?php
global $naWebOS;
echo $naWebOS->html_vividButton (
    4, 'position:absolute;right:5px;',

    'btnCloseLoginWindow', 'vividButton_icon_50x50 btnCloseWindow grouped', '_50x50', 'grouped',
    '',
    'if (!$(this).is(\'.disabled\')) { $(\'#siteLogin\').fadeOut(\'fast\').animate({top:-750}); }',
    '',
    '',

    7, 'Close Window',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    null,//'btnCssVividButton.grey2a.png',
    'btnDelete2.png',

    '',

    null,
    null,
    null
);

        ?>

        <div id="siteLoginContainer" style="display:flex;flex-direction:row;">
            <form id="siteLoginForm" name="siteLoginForm" method="post" action="/login" autocomplete="on" onkeydown="if (event.keyCode==13) na.site.login()" style="display:flex;order:1;flex-direction:column;">
                <div style="order:1">
                <label for="username">Name</label>
                <input id="username" name="username" autocomplete="on" type="text" tabindex="1"><br/>
                </div>

                <div style="order:2">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="on" tabindex="2"><br/>
                </div>

                <div style="order:3">
                <input type="checkbox" id="rememberme" name="rememberme" CHECKED style="width:auto" tabindex="3">
                <label for="rememberme">Remember me</label><br/>
                </div>

                <div class="buttonHolder" style="order: 4">
                    <!--<button id="btnNewAccount" type="button" class="button" style="height:2.5em" onclick="na.site.newAccount();" tabindex="5"><span>New account</span></button>
                    <button id="btnLogin" type="button" class="button" onclick="na.site.login();" style="height:2.5em;" tabindex="6"><span>Log in</span></button>-->
                    <?php
                        echo $naWebOS->html_vividButton (
                                4, 'position:relative;',

                            'btnLogin_do', 'vividButton_icon_50x50 btnCloseWindow grouped', '_50x50', 'grouped',
                            '',
                            'if (!$(this).is(\'.disabled\')) { na.site.login(); }',
                            '',
                            '',

                            11, 'Login',

                            'btnCssVividButton_outerBorder.png',
                            'btnCssVividButton.png',
                            null,//'btnCssVividButton.grey2a.png',
                            'btnLogin.png',

                            '',

                            'Login',
                            'grouped mouseout',
                            null
                        );
                        echo $naWebOS->html_vividButton (
                            4, 'position:relative;',

                            'btnCreateAccount', 'vividButton_icon_50x50 btnCloseWindow grouped', '_50x50', 'grouped',
                            '',
                            'if (!$(this).is(\'.disabled\')) { $(\'#siteLogin\').fadeOut(\'fast\').animate({top:-750}); $(\'#siteRegistration\').fadeIn(\'fast\').animate({top:$(window).height()/2-$(\'#siteRegistration\').height()/2});  }',
                            '',
                            '',

                            12, 'Create Account',

                            'btnCssVividButton_outerBorder.png',
                            'btnCssVividButton.png',
                            null,//'btnCssVividButton.grey2a.png',
                            'btnRegister.png',

                            '',

                            'Create Account',
                            'grouped mouseout',
                            null
                        );
                        ?>
                </div>
            </form>

            <div id="siteLoginMsg" style="order:2;"></div>

            <div class="explanationContainer" style="">
                <p style="font-size:200%">Create an account or login to be able to create your own theme settings for this site.</p>
            </div>
        </div>
    </div>
    <div id="siteLoginSuccessful" class="vividDialogPopup vividScrollpane">Login Successful! <img srcPreload="/NicerAppWebOS/3rd-party/tinymce-4/plugins/naEmoticons/img/happy.gif"/></div>
    <div id="siteLoginFailed" class="vividDialogPopup vividScrollpane">Login failed..</div>













    <div id="siteToolbarThemeEditor__elementPicker" class="vividDialogPopup_noBorder" style="display:none;">
        <div id="siteToolbarThemeEditor__elementPicker_dropdown" class="na_elementPicker_dropdown vividScrollpane" style="position:relative;height:auto;white-space:normal;"></div>
    </div>

    <div id="siteToolbarThemeEditor" class="vdToolbar vividDialog" style="display:none">
    <div class="vividDialogBackground1"></div>
    <div class="vividDialogContent vividScrollpane" style="overflow:visible;overflow-y:auto;">
    <!--
        <div class="sds_dialogTitle">
<?php
global $naWebOS;
echo $naWebOS->html_vividButton (
    4, 'width:100%;margin:5px;align-items:center;',

    'btnViewResult',
    'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.te.hide(event)',
    '',
    '',

    400, 'View result.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.grey2a.png',
    null,//'btnCssVividButton_iconBackground.png',
    'btnBack.png',

    '',

    'View result',
    'grouped btnHide themeEditor',
    ''
);

?>
        </div>
        <div class="flexBreak"></div>
        -->
        <div id="specificitySettings" class="themeEditorComponent_alwaysVisible" style="font-size:15px;flex-wrap:wrap;box-shadow:inset 0px 0px 4px 2px rgba(0,0,0,0.6);">
<?php
global $naLAN;
if (false && $naLAN) {
    global $naWebOS;
    echo $naWebOS->html_vividButton (
        4, 'justify-items:center;width:100%;margin-top:5px !important',

        'btnFullResetOfAllThemes2',
        'vividButton_icon_50x50 grouped btnDelete forum', '_50x50', 'grouped',
        '',
        'na.site.onclick_btnFullResetOfAllThemes(event)',
        '',
        '',

        401, 'Reset the website back to it\'s factory theme settings.',

        'btnCssVividButton_outerBorder.png',
        'btnCssVividButton.blue1b.png',
        null,//'btnCssVividButton_iconBackground.png',
        'btnTrashcan_red.png',

        '',

        'Reset the website back to it\'s factory theme settings',
        'grouped btnDelete themes',
        'margin-left:10px !important;margin-right:10px !important;'
    );
};
?>
            <div class="flexBreak"></div>


            <!--
            <div style="order:0;display:flex;align-items:center;justify-content:center;">
                <label for="themeEditor_specificity_dialog" class="labelthemeEditor2" style="order:0">Dialog
                    <input type="radio" id="themeEditor_photoSpecificity_dialog" name="sdad" class="radioInput" value="dialog" checked="checked" onchange="na.te.onchange_applicationRange(event);" style="order:0;display:none"/>
                </label>
                <label for="themeEditor_specificity_allDialogs" class="labelthemeEditor2" style="order:4;white-space:nowrap;">All dialogs
                    <input type="radio" id="themeEditor_photoSpecificity_allDialogs" name="sdad" class="radioInput" value="allDialogs" onchange="na.te.onchange_applicationRange(event);" style="order:1;display:none"/>
                </label>
            </div>
            -->

            <div style="display:flex;justify-content:space-between;width:100%;align-items:center;margin-top:5px;">
                <!--<label id="labelTheme" for="themes" class="specificityLabel" style="order:2;font-weight:bold;">Theme</label>
                <select id="themes" class="select themeEditor mainBar_forThemeEditor" onchange="na.te.themeSelected(event)" style="order:2">
                    <option id="theme_default" name="theme_default" value="default">default</option>
                </select>-->
                <!--<span class="siteToolbarThemeEditor__label__themes">Theme</span>-->
                <?php
echo $naWebOS->html_vividButton (
    4, 'align-items:center;',

    'btnViewResult',
    'vividButton_icon_50x50 grouped btnDelete forum', '_50x50', 'grouped',
    '',
    'na.te.hide(event)',
    '',
    '',

    400, 'View result.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.grey2a.png',
    null,//'btnCssVividButton_iconBackground.png',
    'btnBack.png',

    '',

    '',
    'grouped btnHide themeEditor',
    ''
);
                ?>
                <!--<img src="/siteMedia/btnPickColor.png" class="vividButton" style="width:40px;"/>-->
                <div id="siteToolbarThemeEditor__themes_dropdown" class="na_themes_dropdown na_themes_dropdown__themes vividScrollpane" style="position:relative;height:auto;white-space:normal;"></div>

<?php
global $naWebOS;
echo $naWebOS->html_vividButton (
    4, 'order:2;margin-left:10px',

    'btnSetPermissionsForTheme', 'vividButton_icon_50x50 grouped btnDelete forum', '_50x50', 'grouped',
    '',
    'if (!$(this).is(\'.disabled\')) na.te.setPermissionsForTheme(event)',
    '',
    '',

    403, 'Create or delete theme, and set permissions for current theme.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.red1b.png',
    '1660_blk_19329_zoom.upperBodyOnly.256x256.png',

    '<img class="vividButton_icon_imgButtonIcon_50x50_sup1" srcPreload="/siteMedia/btnTrashcan2_white_lowres.png" style="position:absolute;left:calc(50px - 15px);width:15px;height:19px;z-index:2021;"/>'
    .'<img class="vividButton_icon_imgButtonIcon_50x50_sup2" srcPreload="/siteMedia/documentAdd_lowres.png" style="position:absolute;left:-5px;width:20px;height:20px;z-index:2021;"/>',

    null,
    null,
    null
);
?>
            </div>
            <div style="display:flex;align-items:center;">
                <!--<label for="specificity" class="specificityLabel" style="order:1;vertical-align:middle;font-weight:bold">Specificity</label>
                <select id="specificity" class="select themeEditor mainBar_forThemeEditor" onchange="na.te.specificitySelected(event)" style="order:1;display:none;"></select>-->
                <!--<span class="siteToolbarThemeEditor__label__specificity">Specificity</span>-->
                <div id="btnThemeEditor__specificity_lock" class="vividButton4" buttonType="btn_lock" onclick="na.site.settings.current.lockSpecificity = !na.site.settings.current.lockSpecificity;"></div>
                <div id="siteToolbarThemeEditor__specificity_dropdown" class="na_themes_dropdown na_themes_dropdown__specificity vividScrollpane" style="position:relative;height:auto;white-space:normal;"></div>


<?php
global $naWebOS;
echo $naWebOS->html_vividButton (
    4, 'order:1;margin-left:10px',

    'btnDeleteSpecificity',
    'vividButton_icon_50x50 grouped btnDelete forum', '_50x50', 'grouped',
    '',
    'if (!$(this).is(\'.disabled\')) na.te.deleteSpecificity(event)',
    '',
    '',

    402, 'Delete all themes for this specificity.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    null,//'btnCssVividButton_iconBackground.png',
    'iconDelete.png',

    '',

    null,
    null,
    null
);
?>
            </div>
            <!--
            <div style="position:relative;display:flex;align-items:center;margin-top:5px;height:60px;">
                <img src="/siteMedia/btnSettings.png" style="width:44px;"/>
                <div id="siteToolbarThemeEditor__selector" class="vividMenu" type="vertical" tabindex="5" theme="{$theme}" style="position:relative;margin-left:8px;z-index:2000">
                    <?php
                        global $naWebOS;
                        //echo '<pre>'; var_dump ($naWebOS->theme); echo '</pre>'; exit();
                        //TODO : RE-ENABLE : echo $naWebOS->themeSettings_UL_list($naWebOS->theme['themeSettings']);
                    ?>
                </div>

            </div>
            -->
            <div class="navbar" style="order:5;display:flex;align-items:center;justify-content:space-between;">
<?php
echo $naWebOS->html_vividButton (
    4, 'order:1',

    'btnSelectSelectorSet', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.te.selectSelectorSet(event)',
    '',
    '',

    401, 'Pick Selector Set.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.blue1a.png',
    'fileTree_1b.png',

    null,

    null,
    null,
    null
);
/*
echo $naWebOS->html_vividButton (
    4, 'order:2;',

    'btnSelectElement', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'if (!$(this).is(\'.disabled\')) na.te.selectElement(event)',
    '',
    '',

    402, 'Pick Element.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.greenBlue.png',
    'btnZoomIncrease.png',

    '',
    '',

    null,
    null,
    null
);*/
?>
           <!-- </div>
            <div class="navbar" style="order:6;display:flex;align-items:center;justify-content:center;">-->
<?php
global $naWebOS;
echo $naWebOS->html_vividButton (
    4, 'order:3',

    'btnSelectBackgroundColor', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.te.selectBackground_color(event)',
    '',
    '',

    432, 'Set background color.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.yellow1a.png',
    'btnColorPicker.png',

    null,

    null,
    null,
    null
);
echo $naWebOS->html_vividButton (
    4, 'order:4',

    'btnSelectBorderSettings', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.te.selectBorderSettings(event)',
    '',
    '',

    422, 'Set border settings.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.grey2a.png',
    'btnSettingsBorder3.png',

    null,

    null,
    null,
    null
);
echo $naWebOS->html_vividButton (
    4, 'order:5',

    'btnSelectBoxShadowSettings', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.te.selectBoxShadowSettings(event)',
    '',
    '',

    423, 'Set border shadow.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.orange1c.png',
    null,

    null,

    null,
    null,
    null
);
?>
            </div>
            <div class="navbar" style="order:7;display:flex;align-items:center;justify-content:space-between;">
<?php
echo $naWebOS->html_vividButton (
    4, 'order:4',

    'btnSelectBackgroundFolder', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.te.selectBackground_folder(event)',
    '',
    '',

    433, 'Select background image folder.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.yellow1b.png',
    'fileTree_1b.png',

    null,

    null,
    null,
    null
);
echo $naWebOS->html_vividButton (
    4, 'order:5',

    'btnSelectBackgroundImage', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.te.selectBackground_image(event)',
    '',
    '',

    434, 'Set background image.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.yellow1a.png',
    'btnBackground.png',

    null,

    null,
    null,
    null
);
echo $naWebOS->html_vividButton (
    4, 'order:6',

    'btnSelectTextSettings', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.te.selectTextSettings(event)',
    '',
    '',

    432, 'Set text settings.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.yellow1a.png',
    'btnSettingsText.png',

    null,

    null,
    null,
    null
);
echo $naWebOS->html_vividButton (
    4, 'order:7',

    'btnSelectTextShadowSettings', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.te.selectTextShadowSettings(event)',
    '',
    '',

    433, 'Set text shadow.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.yellow1a.png',
    'btnSettingsTextShadow.png',

    null,

    null,
    null,
    null
);
?>
            </div>

        </div>
        <div id="nate_selectorSet" class="themeEditorComponent_containerDiv2" style="order:8;display:block;height:auto;display:none;justify-content:space-between">
            <div class="naNavBar_darkenedBG" style="">
<?php
/*
echo $naWebOS->html_vividButton (
    4, 'order:1',

    'btnAddSelector', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.te.onclick_btnAddSelector(event)',
    '',
    '',

    433, 'Add selector.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.greenBlue.png',
    'btnSelector.png',

    '<img class="vividButton_icon_imgButtonIcon_50x50_sup1" srcPreload="/siteMedia/btnPlus_shaded.png" style="position:absolute;left:0px;width:15px;z-index:2021;"/>',

    null,
    null,
    null
);
echo $naWebOS->html_vividButton (
    4, 'order:2',

     'btnDeleteSelector', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.te.onclick_btnDeleteSelector(event)',
    '',
    '',

    433, 'Delete selector.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.yellow4a.png',
    'btnSelector.png',

    '<img class="vividButton_icon_imgButtonIcon_50x50_sup1" srcPreload="/siteMedia/btnDelete.png" style="position:absolute;left:0px;width:15px;z-index:2021;"/>',

    null,
    null,
    null
);*/
echo $naWebOS->html_vividButton (
    4, 'order:3',

     'btnAddGraphics', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.te.onclick_btnAddGraphics(event)',
    '',
    '',

    433, 'Add graphics.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.greenBlue.png',
    'btnPickColor_canvasSquared.png',

    '<img class="vividButton_icon_imgButtonIcon_50x50_sup1" srcPreload="/siteMedia/btnPlus_shaded.png" style="position:absolute;left:0px;width:15px;z-index:2021;"/>',

    null,
    null,
    null
);echo $naWebOS->html_vividButton (
    4, 'order:4',

     'btnDeleteGraphics', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.te.onclick_btnDeleteGraphics(event)',
    '',
    '',

    433, 'Delete graphics.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.red1a.png',
    'btnPickColor_canvasSquared.png',

    '<img class="vividButton_icon_imgButtonIcon_50x50_sup1" srcPreload="/siteMedia/btnDelete.png" style="position:absolute;left:0px;width:15px;z-index:2021;"/>',

    null,
    null,
    null
);
echo $naWebOS->html_vividButton (
    4, 'order:5',

     'btnAddElement', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.te.onclick_btnAddElement(event)',
    '',
    '',

    433, 'Add element.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.greenBlue.png',
    'btnSettings2.png',

    '<img class="vividButton_icon_imgButtonIcon_50x50_sup1" srcPreload="/siteMedia/btnPlus_shaded.png" style="position:absolute;left:0px;width:15px;z-index:2021;"/>',

    null,
    null,
    null
);echo $naWebOS->html_vividButton (
    4, 'order:6',

     'btnDeleteElement', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.te.onclick_btnDeleteElement(event)',
    '',
    '',

    433, 'Delete element.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.red1a.png',
    'btnSettings2.png',

    '<img class="vividButton_icon_imgButtonIcon_50x50_sup1" srcPreload="/siteMedia/btnDelete.png" style="position:absolute;left:0px;width:15px;z-index:2021;"/>',

    null,
    null,
    null
);
?>
            </div>
            <div id="themeEditor_jsTree_selectors" class="themeEditorComponent vividScrollpane" style="top:auto;position:relative;width:auto;"></div>
        </div>
        <div id="borderSettings" class="themeEditorComponent" style="top:auto;">
            <div class="themeEditorComponent_containerDiv" style="order:1">
            <div class="themeEditor_input_containerDiv">
                <div class="borderSettings_label_containerDiv" style="display:inline-block">
                    <label id="labelBorderType" for="borderType" class="borderSettings_label">Color</label>
                </div>
                <input id="borderColorpicker" class="themeEditor_colorPicker" style="position:absolute;"></input>
            </div>
            </div>

            <div class="themeEditorComponent_containerDiv" style="order:2;height:2.5em;margin-top:8px;display:flex;align-items:center;justify-content:center;">
            <div class="themeEditor_input_containerDiv">
                <div class="borderSettings_label_containerDiv" style="display:inline-block">
                    <label id="labelBorderType" for="borderType" class="borderSettings_label">Type</label>
                </div>
                <div class="borderSettings_input_containerDiv">
                <select class="select" id="borderType" onchange="na.te.borderSettingsSelected()">
                    <option value="dotted">Dotted</option>
                    <option value="dashed">Dashed</option>
                    <option value="solid">Solid</option>
                    <option value="double">Double</option>
                    <option value="groove">3D Groove</option>
                    <option value="ridge" selected>3D Ridge</option>
                    <option value="inset">3D Inset</option>
                    <option value="outset">3D Outset</option>
                    <option value="none">None</option>
                    <option value="hidden">Hidden</option>
                </select>
                </div>
            </div>
            </div>

            <div class="themeEditorComponent_containerDiv" style="order:3">
            <div class="themeEditor_input_containerDiv">
                <div class="borderSettings_label_containerDiv" style="display:inline-block">
                    <label id="labelBorderWidth" for="borderWidth" class="borderSettings_label">Width</label>
                </div>
                <div class="borderSettings_input_containerDiv">
                    <input id="borderWidth" type="range" min="0" max="20" value="3" class="sliderOpacityRangeBorderSettings" onchange="na.te.borderSettingsSelected();"/>
                </div>
            </div>
            </div>

            <div class="themeEditorComponent_containerDiv" style="order:4">
            <div class="themeEditor_input_containerDiv">
                <div class="borderSettings_label_containerDiv" style="display:inline-block">
                    <label id="labelBorderRadius" for="borderRadius" class="borderSettings_label">Radius</label>
                </div>
                <div class="borderSettings_input_containerDiv">
                    <input id="borderRadius" type="range" min="0" max="50" value="20" class="sliderOpacityRangeBorderSettings" onchange="na.te.borderSettingsSelected();"/>
                </div>
            </div>
            </div>
        </div>
        <div id="boxShadowSettings" class="themeEditorComponent" style="top:auto;">
            <div class="themeEditorComponent_containerDiv" style="order:1">
            <div class="themeEditor_input_containerDiv">
                <div class="borderSettings_label_containerDiv" style="display:inline-block">
                    <label id="labelBoxShadow" class="borderSettings_label">Box shadows</label>
                </div>
                <div id="boxShadowControls" class="borderSettings_input" style="display:inline-block;">
                    <img srcPreload="/siteMedia/iconCreate.png" onclick="na.te.addBoxShadow()"/>
                    <img srcPreload="/siteMedia/iconDelete.png" onclick="na.te.deleteBoxShadow()"/>
                    <!--<div id="boxShadow_0" class="boxShadow" style="background:rgba(200,200,200,1);border:1px solid lime; box-shadow:2px 2px 2px 2px rgba(0,0,0,0.5); border-radius:10px; margin : 5px; padding : 5px;" onclick="na.te.boxSettingsSelected(event);">ABC XYZ</div>-->
                </div>
            </div>
            </div>

            <div class="themeEditorComponent_containerDiv" style="order:2">
            <div class="themeEditor_input_containerDiv">
                <div class="borderSettings_label_containerDiv">
                    <label id="labelBoxShadowInset" class="borderSettings_label" for="boxShadowInset">Inset</label>
                </div>
                <div class="borderSettings_input_containerDiv">
                    <input id="boxShadowInset" type="checkbox" onchange="na.te.boxSettingsChanged();"/>
                </div>
            </div>
            </div>

            <div class="themeEditorComponent_containerDiv" style="order:3">
            <div class="themeEditor_input_containerDiv">
                <div class="borderSettings_label_containerDiv">
                    <label id="labelBoxShadowXoffset" class="borderSettings_label" for="boxShadowXoffset">Hor offset</label>
                </div>
                <div class="borderSettings_input_containerDiv">
                    <input id="boxShadowXoffset" type="range" min="-10" max="10" value="2" class="sliderOpacityRangeBorderSettings" onchange="na.te.boxSettingsChanged();"/>
                </div>
            </div>
            </div>

            <div class="themeEditorComponent_containerDiv" style="order:4">
            <div class="themeEditor_input_containerDiv">
                <div class="borderSettings_label_containerDiv">
                    <label id="labelBoxShadowYoffset" class="borderSettings_label" for="boxShadowYoffset">Ver offset</label>
                </div>
                <div class="borderSettings_input_containerDiv">
                    <input id="boxShadowYoffset" type="range" min="-10" max="10" value="2" class="sliderOpacityRangeBorderSettings" onchange="na.te.boxSettingsChanged();"/>
                </div>
            </div>
            </div>

            <div class="themeEditorComponent_containerDiv" style="order:5">
            <div class="themeEditor_input_containerDiv">
                <div class="borderSettings_label_containerDiv">
                    <label id="labelBoxShadowSpreadRadius" class="borderSettings_label" for="boxShadowSpreadRadius">Spread</label>
                </div>
                <div class="borderSettings_input_containerDiv">
                    <input id="boxShadowSpreadRadius" type="range" min="0" max="10" value="2" class="sliderOpacityRangeBorderSettings" onchange="na.te.boxSettingsChanged();"/>
                </div>
            </div>
            </div>

            <div class="themeEditorComponent_containerDiv" style="order:7">
            <div class="themeEditor_input_containerDiv">
                <div class="borderSettings_label_containerDiv">
                    <label id="labelBoxShadowBlurRadius" class="borderSettings_label" for="boxShadowBlurRadius">Blur</label>
                </div>
                <div class="borderSettings_input_containerDiv">
                    <input id="boxShadowBlurRadius" type="range" min="0" max="10" value="2" class="sliderOpacityRangeBorderSettings" onchange="na.te.boxSettingsChanged();"/>
                </div>
            </div>
            </div>

            <div class="themeEditorComponent_containerDiv" style="order:8">
            <div class="themeEditor_input_containerDiv">
                <div class="borderSettings_label_containerDiv">
                    <label id="labelBoxShadowColor" class="borderSettings_label" for="boxShadowColor">Color</label>
                </div>
                <div class="borderSettings_input_containerDiv">
                    <input id="boxShadowColorpicker" class="themeEditor_colorPicker" style="position:absolute;top:95px;"></input>
                </div>
            </div>
            </div>
        </div>
        <div id="themeEditor_backgroundColor" class="themeEditorComponent" style="text-align:center;">
            <input id="colorpicker" class="themeEditor_colorPicker" style="position:absolute;top:auto;"></input>
        </div>
        <div id="themeEditor_jsTree_backgrounds" class="themeEditorComponent" style="top:auto;display:none;"></div>
        <div id="themeEditor_photoAlbum_specs" class="themeEditorComponent" style="height:auto;flex-flow: wrap row;position:relative;top:auto;display:none;">
            <div class="themeEditor_input_containerDiv flexColumns" style="display:flex;">
                <label for="themeEditor_photoSpecificity_dialog" class="labelthemeEditor2">Dialog
                <input type="radio" id="themeEditor_photoSpecificity_dialog" name="psdp" class="radioInput" value="dialog" checked="checked"/>
                </label>

                <label for="themeEditor_photoSpecificity_page" class="labelthemeEditor2">Page
                <input type="radio" id="themeEditor_photoSpecificity_page" name="psdp" class="radioInput" value="dialog"/>
                </label>
            </div>
            <div style="flex-basis:100%;height:0;">&nbsp;</div>
            <div class="themeEditor_input_containerDiv">
            <label id="label_themeEditor_photoOpacity" class="labelthemeEditor" for="themeEditor_photoOpacity">Opacity</label>
            <input id="themeEditor_photoOpacity" type="range" min="1" max="100" value="50" class="sliderOpacityRangethemeEditor" oninput="if (na.te.settings.current.selectedImage) na.te.imageSelected(na.te.settings.current.selectedImage);"/>
            </div>

            <div style="flex-basis:100%;height:0;">&nbsp;</div>
            <div class="themeEditor_input_containerDiv">
            <label id="label_themeEditor_photoScaleX" class="labelthemeEditor" for="themeEditor_photoScaleX">Scale hor</label>
            <input id="themeEditor_photoScaleX" type="range" min="25" max="200" value="100" class="sliderOpacityRangethemeEditor" style="" oninput="if (na.te.settings.current.selectedImage) na.te.imageSelected(na .te.settings.current.selectedImage);"/>
            </div>

            <div style="flex-basis:100%;height:0;">&nbsp;</div>
            <div class="themeEditor_input_containerDiv">
            <label id="label_themeEditor_photoScaleY" class="labelthemeEditor" for="themeEditor_photoScaleY">Scale ver</label>
            <input id="themeEditor_photoScaleY" type="range" min="25" max="200" value="100" class="sliderOpacityRangethemeEditor" style="" oninput="if (na.te.settings.current.selectedImage) na.te.imageSelected(na.te.settings.current.selectedImage);"/>
            </div>

            <div style="flex-basis:100%;height:0;">&nbsp;</div>
        </div>
        <script type="application/javascript">
            function resizeIFrameToFitContent( iFrame ) {
                if (
                    iFrame
                    && iFrame.src.match(/photoAlbum/)
                ) {
                    $('div.vividScrollpane', iFrame.contentWindow.document.body).css({
                        width : $(iFrame).width()+'px'
                    });
                    setTimeout (function(iFrame) {
                        iFrame.style.height = ($('div.vividScrollpane', iFrame.contentWindow.document.body)[0].scrollHeight+20)+'px';
                    }, 50, iFrame);
                };
            }
        </script>
        <iframe id="themeEditor_photoAlbum" class="themeEditorComponent" style="top:calc(230px + 4em);display:none;border:0px"></iframe>
        <div id="textSettings" class="themeEditorComponent" style="top:auto;display:none;">
            <div class="themeEditor_input_containerDiv">
            <div class="textSettings_label_containerDiv">
                <label id="labelTextColor" class="textColorpicker textSettings_label" for="fontFamily">Color</label>
            </div>
            <div class="textSettings_input_containerDiv">
                <input id="textColorpicker" class="themeEditor_colorPicker" style="position:absolute;top:95px;"></input>
            </div>
            </div>

            <div id="textHasAnimatedColor_section" class="themeEditor_input_containerDiv">
            <div class="textSettings_input_containerDiv_checkbox" style="width:70px;text-align:right">
                <input id="textHasAnimatedColor" type="checkbox" style=""></input>
            </div>
            <div class="textSettings_label_containerDiv" style="flex-grow:7;">
                <label id="labelTextAnimatedColor" class="textColorpicker textSettings_label" for="textHasAnimatedColor" style="width:calc(100% - 10px);">Animated Color</label>
            </div>
            </div>

        <div class="themeEditor_input_containerDiv">
            <div class="textSettings_label_containerDiv">
                <label id="labelTextFontFamily" class="textSettings_label" for="textFontFamily">Font</label>
            </div>
            <div class="textSettings_input_containerDiv">
                <!--<select class="select" id="textFontFamily" onchange="na.te.textSettingsSelected_updateDialog()">
                    <option value="ABeeZee">ABeeZee</option>
                    <option value="Aclonica">Aclonica</option>
                    <option value="Acme">Acme</option>
                    <option value="Actor">Actor</option>
                    <option value="Advent Pro">Advent Pro</option>
                    <option value="Akronim">Akronim</option>
                    <option value="Alex Brush">Alex Brush</option>
                    <option value="Architects Daughter">Architects Daughter</option>
                    <option value="Archivo Black">Archivo Black</option>
                    <option value="Baloo">Baloo</option>
                    <option value="Bebas Neue">Bebas Neue</option>
                    <option value="Caveat">Caveat</option>
                    <option value="Chewy">Chewy</option>
                    <option value="Cookie">Cookie</option>
                    <option value="Cormorant">Cormorant</option>
                    <option value="Courgette">Courgette</option>
                    <option value="Covered By Your Grace">Covered By Your Grace</option>
                    <option value="Dancing Script">Dancing Script</option>
                    <option value="El Messiri">El Messiri</option>
                    <option value="Exo">Exo</option>
                    <option value="Exo 2">Exo 2</option>
                    <option value="Fjalla One">Fjalla One</option>
                    <option value="Galada">Galada</option>
                    <option value="Gloria Hallelujah">Gloria Hallelujah</option>
                    <option value="Great Vibes">Great Vibes</option>
                    <option value="Handlee">Handlee</option>
                    <option value="Indie Flower">Indie Flower</option>
                    <option value="Kalam">Kalam</option>
                    <option value="Kaushan Script">Kaushan Script</option>
                    <option value="Khula">Khula</option>
                    <option value="Knewave">Knewave</option>
                    <option value="Krona One">Krona One</option>
                    <option value="Lacquer">Lacquer</option>
                    <option value="Lato:300,300i,400,400i">Lato</option>
                    <option value="Lemonada">Lemonada</option>
                    <option value="Lusitana">Lusitana</option>
                    <option value="M PLUS 1p">M PLUS 1p</option>
                    <option value="Marck Script">Marck Script</option>
                    <option value="Merienda One">Merienda One</option>
                    <option value="Modak">Modak</option>
                    <option value="Montserrat">Montserrat</option>
                    <option value="Montserrat Alternates">Montserrat Alternates</option>
                    <option value="Mr Dafoe">Mr Dafoe</option>
                    <option value="Mukta Malar">Mukta Malar</option>
                    <option value="Nanum Pen Script">Nanum Pen Script</option>
                    <option value="Noto Serif JP">Noto Serif JP</option>
                    <option value="Odibee Sans">Odibee Sans</option>
                    <option value="Oleo Script">Oleo Script</option>
                    <option value="Open Sans">Open Sans</option>
                    <option value="Orbitron">Orbitron</option>
                    <option value="Pacifico">Pacifico</option>
                    <option value="Parisienne">Parisienne</option>
                    <option value="Pathway Gothic One">Pathway Gothic One</option>
                    <option value="Permanent Marker">Permanent Marker</option>
                    <option value="Playball">Playball</option>
                    <option value="Pridi">Pridi</option>
                    <option value="PT Sans">PT Sans</option>
                    <option value="Quattrocento Sans">Quattrocento Sans</option>
                    <option value="Raleway">Raleway</option>
                    <option value="Rock Salt">Rock Salt</option>
                    <option value="Sacramento">Sacramento</option>
                    <option value="Saira Condensed">Saira Condensed</option>
                    <option value="Saira Extra Condensed">Saira Extra Condensed</option>
                    <option value="Saira Semi Condensed">Saira Semi Condensed</option>
                    <option value="Satisfy">Satisfy</option>
                    <option value="Shadows Into Light">Shadows Into Light</option>
                    <option value="Shadows Into Light Two">Shadows Into Light Two</option>
                    <option value="Sigmar One">Sigmar One</option>
                    <option value="Signika Negative">Signika Negative</option>
                    <option value="Slabo 27px">Slabo 27px</option>
                    <option value="Source Code Pro">Source Code Pro</option>
                    <option value="Special Elite">Special Elite</option>
                    <option value="Spectral">Spectral</option>
                    <option value="Spinnaker">Spinnaker</option>
                    <option value="Sriracha">Sriracha</option>
                    <option value="Unica One">Unica One</option>
                    <option value="Ubuntu">Ubuntu</option>
                    <option value="Work Sans">Work Sans</option>
                </select>
                -->
                <div id="textFontFamily_containerDiv" style="position:relative">
                <div id="textFontFamily" class="vividMenu" type="vertical" tabindex="5" theme="{$theme}" style="position:relative;z-index:920000;">
                    <ul class="vividMenu_mainUL" style="display:none;" itemsLevel1="1" menuStructure="vertical">
                        <li><a id="textFontFamily_label" href="javascript:na.te.textSettings_getFont(event)">Select Font</a>
                            <ul>
                                <li><a href="#">Bold</a>
                                    <ul>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Aclonica')" style="font-family:Aclonica">Aclonica</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Archivo Black')" style="font-family:Archivo Black">Archivo Black</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Baloo')" style="font-family:Baloo">Baloo</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Bebas Neue')" style="font-family:Bebas Neue">Bebas Neue</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Chewy')" style="font-family:Chewy">Chewy</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Galada')" style="font-family:Galada">Galada</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Krona One')" style="font-family:Krona One">Krona One</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Modak')" style="font-family:Modak">Modak</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Odibee Sans')" style="font-family:Odibee Sans">Odibee Sans</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Sigmar One')" style="font-family:Sigmar One">Sigmar One</a></li>
                                    </ul>
                                </li>
                                <li><a href="#">Handwritten</a>
                                    <ul>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Acme')" style="font-family:Acme">Acme</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Akronim')" style="font-family:Akronim">Akronim</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Alex Brush')" style="font-family:Alex Brush">Alex Brush</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Architects Daughter')" style="font-family:Architects ">Architects Daughter</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Caveat')" style="font-family:Caveat">Caveat</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Cookie')" style="font-family:Cookie">Cookie</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Courgette')" style="font-family:Courgette">Courgette</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Covered By Your Grace')" style="font-family:Covered By Your Grace">Covered By Your Grace</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Dancing Script')" style="font-family:Dancing Script">Dancing Script</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Great Vibes')" style="font-family:Great Vibes">Great Vibes</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Kalam')" style="font-family:Kalam">Kalam</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Kaushan Script')" style="font-family:Kaushan Script">Kaushan Script</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Lacquer')" style="font-family:Lacquer">Lacquer</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Lemonada')" style="font-family:Lemonada">Lemonada</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Marck Script')" style="font-family:Marck Script">Marck Script</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Merienda One')" style="font-family:Merienda One">Merienda One</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Mr Dafoe')" style="font-family:Mr Dafoe">Mr Dafoe</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Nanum Pen Script')" style="font-family:Nanum Pen Script">Nanum Pen Script</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Oleo Script')" style="font-family:Oleo Script">Oleo Script</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Parisienne')" style="font-family:Parisienne">Parisienne</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Permanent Marker')" style="font-family:Permanent Marker">Permanent Marker</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Playball')" style="font-family:Playball">Playball</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Rock Salt')" style="font-family:Rock Salt">Rock Salt</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Sacramento')" style="font-family:Sacramento">Sacramento</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Satisfy')" style="font-family:Satisfy">Satisfy</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Shadows Into Light')" style="font-family:Shadows Into Light">Shadows Into Light</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Shadows Into Light Two')" style="font-family:Shadows Into Light Two">Shadows Into Light Two</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Sriracha')" style="font-family:Sriracha">Sriracha</a></li>
                                    </ul>
                                </li>
                                <li><a href="#">Office</a>
                                    <ul>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'ABeeZee')" style="font-family:ABeeZee">ABeeZee</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Actor')" style="font-family:Actor">Actor</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Advent Pro')" style="font-family:Advent Pro">Advent Pro</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Exo')" style="font-family:Exo">Exo</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Exo 2')" style="font-family:Exo 2">Exo 2</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Khula')" style="font-family:Khula">Khula</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'M PLUS 1p')" style="font-family:M PLUS 1p">M PLUS 1p</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Montserrat Alternates')" style="font-family:Montserrat Alternates">Montserrat Alternates</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Orbitron')" style="font-family:Orbitron">Orbitron</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Pathway Gothic One')" style="font-family:Pathway Gothic One">Pathway Gothic One</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Saira Condensed')" style="font-family:Saira Condensed">Saira Condensed</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Saira Extra Condensed')" style="font-family:Saira Extra Condensed">Saira Extra Condensed</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Saira Semi Condensed')" style="font-family:Saira Semi Condensed">Saira Semi Condensed</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Signika Negative')" style="font-family:Signika Negative">Signika Negative</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Slabo 27px')" style="font-family:Slabo 27px">Slabo 27px</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Source Code Pro')" style="font-family:Source Code Pro">Source Code Pro</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Spinnaker')" style="font-family:Spinnaker">Spinnaker</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Unica One')" style="font-family:Unica One">Unica One</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Ubuntu')" style="font-family:Ubuntu">Ubuntu</a></li>
                                    </ul>
                                </li>
                                <li><a href="#">Newspaper</a>
                                    <ul>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Cormorant')" style="font-family:Cormorant">Cormorant</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'El Messiri')" style="font-family:El Messiri">El Messiri</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Knewave')" style="font-family:Knewave">Knewave</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Lusitana')" style="font-family:Lusitana">Lusitana</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Noto Serif JP')" style="font-family:Noto Serif JP">Noto Serif JP</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Pridi')" style="font-family:Pridi">Pridi</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Quattrocento Sans')" style="font-family:Quattrocento Sans">Quattrocento Sans</a></li>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Spectral')" style="font-family:Spectral">Spectral</a></li>
                                    </ul>
                                </li>
                                <li><a href="#">Movies</a>
                                    <ul>
                                        <li><a href="javascript:na.te.textSettings_changeFont(event, 'Special Elite')" style="font-family:Special Elite">Special Elite</a></li>
                                    </ul>
                                </li>

                                <li><a href="javascript:na.te.textSettings_changeFont(event, 'Fjalla One')" style="font-family:Fjalla One">Fjalla One</a></option>
                                <li><a href="javascript:na.te.textSettings_changeFont(event, 'Gloria Hallelujah')" style="font-family:Gloria Hallelujah">Gloria Hallelujah</a></option>
                                <li><a href="javascript:na.te.textSettings_changeFont(event, 'Handlee')" style="font-family:Handlee">Handlee</a></option>
                                <li><a href="javascript:na.te.textSettings_changeFont(event, 'Indie Flower')" style="font-family:Indie Flower">Indie Flower</a></option>
                                <li><a href="javascript:na.te.textSettings_changeFont(event, 'Lato:300,300i,400,400i')" style="font-family:Lato">Lato:300,300i,400,400i</a></option>
                                <li><a href="javascript:na.te.textSettings_changeFont(event, 'Montserrat')" style="font-family:Montserrat">Montserrat</a></option>
                                <li><a href="javascript:na.te.textSettings_changeFont(event, 'Mukta Malar')" style="font-family:Mukta Malar">Mukta Malar</a></option>
                                <li><a href="javascript:na.te.textSettings_changeFont(event, 'Open Sans')" style="font-family:Open Sans">Open Sans</a></option>
                                <li><a href="javascript:na.te.textSettings_changeFont(event, 'Pacifico')" style="font-family:Pacifico">Pacifico</a></option>
                                <li><a href="javascript:na.te.textSettings_changeFont(event, 'PT Sans')" style="font-family:PT Sans">PT Sans</a></option>
                                <li><a href="javascript:na.te.textSettings_changeFont(event, 'Raleway')" style="font-family:Raleway">Raleway</a></option>

                                <li><a href="javascript:na.te.textSettings_changeFont(event, 'Work Sans')" style="font-family:Work Sans">Work Sans</a></option>
                            </ul>
                        </li>
                    </ul>
                </div>
                </div>
            </div>
            </div>

            <div class="themeEditor_input_containerDiv">
            <div class="textSettings_label_containerDiv">
                <label id="labelTextSize" class="textSettings_label" for="textSize">Size</label>
            </div>
            <div class="textSettings_input_containerDiv">
                <input id="textSize" type="range" min="5" max="40" value="12" class="sliderOpacityRangeBorderSettings" onchange="na.te.textSettingsSelected(event);"/>
            </div>
            </div>

            <div class="themeEditor_input_containerDiv">
            <div class="textSettings_label_containerDiv">
                <label id="labelTextWeight" class="textSettings_label" for="textWeight">Boldness</label>
            </div>
            <div class="textSettings_input_containerDiv">
                <input id="textWeight" type="range" min="3" max="10" value="4" class="sliderOpacityRangeBorderSettings" onchange="na.te.textSettingsSelected(event);"/>
            </div>
            </div>

        </div>
        <div id="textShadowSettings" class="themeEditorComponent" style="top:auto;display:none;">
            <div class="themeEditor_input_containerDiv">
            <div class="textShadowSettings_label_containerDiv">
                <label id="labelTextShadow" class="textShadowSettings_label">Text shadow</label>
            </div>
            <div id="textShadowControls">
                <img srcPreload="/siteMedia/iconCreate.png" onclick="na.te.addTextShadow(event)"/>
                <img srcPreload="/siteMedia/iconDelete.png" onclick="na.te.deleteTextShadow(event)"/>
                <!--<div class="flexBreak"></div>
                <div id="textShadow_0" class="textShadow" style="background:navy;border:1px solid white; border-radius:10px; margin : 5px; padding : 5px;" onclick="na.te.selectTextShadow(event)">ABC XYZ</div>-->
            </div>
            </div>

            <div class="themeEditor_input_containerDiv">
            <div class="textShadowSettings_label_containerDiv">
                <label id="labelTextShadowColor" class="textShadowSettings_label" for="textShadowColor">Color</label>
            </div>
            <div class="textShadow_input_containerDiv">
                <input id="textShadowColorpicker" class="themeEditor_colorPicker" style="position:absolute;top:95px;"></input>
            </div>
            </div>

            <div class="themeEditor_input_containerDiv">
            <div class="textShadowSettings_label_containerDiv">
                <label id="labelTextShadowXoffset" class="textShadowSettings_label" for="textShadowXoffset">Hor-offset</label>
            </div>
            <div class="textShadow_input_containerDiv">
                <input id="textShadowXoffset" type="range" min="-10" max="10" value="2" class="sliderOpacityRangeBorderSettings" onchange="na.te.textSettingsSelected(event);"/>
            </div>
            </div>

            <div class="themeEditor_input_containerDiv">
            <div class="textShadowSettings_label_containerDiv">
                <label id="labelTextShadowYoffset" class="textShadowSettings_label" for="textShadowYoffset">Ver-offset</label>
            </div>
            <div class="textShadow_input_containerDiv">
                <input id="textShadowYoffset" type="range" min="-10" max="10" value="2" class="sliderOpacityRangeBorderSettings" onchange="na.te.textSettingsSelected(event);"/>
            </div>
            </div>

            <div class="themeEditor_input_containerDiv">
            <div class="textShadowSettings_label_containerDiv">
                <label id="labelTextShadowBlurRadius" class="textShadowSettings_label" for="textShadowBlurRadius">Blur</label>
            </div>
            <div class="textShadow_input_containerDiv">
                <input id="textShadowBlurRadius" type="range" min="0" max="10" value="2" class="sliderOpacityRangeBorderSettings" onchange="na.te.textSettingsSelected(event);"/>
            </div>
            </div>

        </div>
        <div id="themePermissions" class="themeEditorComponent" style="top:auto;display:none;">
            <div class="themeEditor_input_containerDiv">
            <div class="themePermissions_label_containerDiv">
                <label id="labelThemeControls" class="themePermissions_label" for="themePermissionsControls">Themes</label>
            </div>
            <div id="themePermissionsControls" class="themePermissions_input_containerDiv">
                <img srcPreload="/siteMedia/iconCreate.png" onclick="na.te.addTheme(event)"/>
                <img srcPreload="/siteMedia/iconDelete.png" onclick="na.te.deleteTheme(event)"/>
                <div class="flexBreak"></div>
                <input id="theme_0" class="themeItem onfocus" type="text" onclick="na.te.themeSelected(event)" onchange="na.te.themeNameChanged(0, 'theme_0')" value="default"/>
            </div>
            </div>
        </div>
    </div>
    </div>


</body>
</html>
