<?php
require_once (realpath(dirname(__FILE__).'/../../../../../../').'/NicerAppWebOS/boot.php');
require_once (dirname(__FILE__).'/functions.php');
require_once (dirname(__FILE__).'/sources-list.php');
    
global $naWebOS;
$view = $naWebOS->view;

/*
global $saSiteHTTP; global $saSiteDomain; global $saSiteRootFolder; global $saFrameworkFolder;
global $saSiteHD; global $saFrameworkHD; global $saSiteURL; global $saFrameworkURL;
global $saIsLocalhost; global $saHTDOCShd;
global $saServerOperatingSystem; global $saDeveloperMode;

global $saUpstreamRootURL; global $locationbarInfo;
global $saUIdefaults;
*/

//echo '<pre>'; var_dump ($view); exit();
$pageTitle = str_replace('__', '&nbsp;', $view['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news']['section']);
$pageTitle = str_replace('_', '&nbsp;', $pageTitle);

?>
        <link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news/index.css?changed=<?php echo $naWebOS->fileDateTimeStamp(dirname(__FILE__).'/index.css');?>"/>
                <script type="text/javascript">
                if ($(window).width() < na.site.globals.smallDeviceWidth) {
                    setTimeout(function() {
                        $('#div_newsApp_info, .newsApp__header__datetime').css({display:'none'});
                    }, 200);
                } else {
                    /*
                    setTimeout(function() {
                        var vividTextCmd = {
                                el : jQuery('#newsApp_info')[0],
                                theme : na.cg.themes.naColorgradientSchemeOrangeYellow, 
                                animationType : na.vividText.globals.animationTypes[0],
                                animationSpeed : 4 * 1000
                        };
                        na.vividText.initElement (vividTextCmd);
                    }, 500);
                    */
                }
                </script>                
        <script type="text/javascript" src="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news/news_siteContent.source.js?changed=<?php echo $naWebOS->fileDateTimeStamp(dirname(__FILE__).'/news_siteContent.source.js');?>"></script>

    
            <div id="siteContent__header" class="saHeaderInDialog" style="display:none;flex-direction:column;width:100%;">
                <div class="content_containerDiv_container evenly">
                    <div class="content_containerDiv_item" style="order:1;">
                        <h1 id="newsApp_title" class="newsApp_header backdropped" style="padding:0px;margin:0px;vertical-align:middle;"><?php echo $pageTitle;?></h1>
                    </div>
                    <div id="div_newsApp_searchbar" class="content_containerDiv_item backdropped" style="order:2;display:none;">
                        <label for="newsApp_searchbar" id="label_newsApp_searchbar" class="label_newsApp" style="font-size:120%;font-weight:bold;padding:5px;">Search for : </label>
                        <input id="newsApp_searchbar" style="display:none; background:rgba(255,255,255,1); border-radius:7px; border : 1px solid black;vertical-align:middle" onchange="na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].onSearch(event)"/>
                        <img id="newsApp_searchbar__enterQuery" title="search the entire news database (up to 14 days into the past)" src="/siteMedia/na.question-mark.svg.png" style="height:30px;vertical-align:middle" onclick="na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].onSearch(event)"/>
                        <img id="newsApp_searchbar__abandonQuery" title="abandon the current search query and display the very latest news again" src="/siteMedia/na.reset.png" style="height:30px;vertical-align:middle" onclick="na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'].clearSearch(event)"/>
                    </div>
                    <div id="div_newsApp_info" class="content_containerDiv_item backdropped" style="order:2;">
                        <span id="newsApp_info" class="contentSectionTitle1_span">This app will load up older news-items whenever needed</span>
                    </div>
                    <div class="content_containerDiv_item" style="order:3;">
                        <span id="newsApp_timer" class="backdropped newsApp_header" style="font-weight:bold;"></span>                    
                    </div>
                    <div class="content_containerDiv_item" style="order:4;justify-content:flex-center;">
                        <div class="navbar" style="width:100%">
<?php
global $naWebOS;
echo $naWebOS->html_vividButton (
    7, 'display:inline-block;',

    'newsApp_options',
    'vividButton_icon_50x50 grouped btnOptions', '_50x50', 'grouped',
    '',
    'na.apps.loaded[\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news\'].gotoNextPage()',
    'na.apps.loaded[\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news\'].showOptionsDialog()',
    'na.apps.loaded[\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news\'].hideOptionsDialog()',

    500, 'Alt-o brings up the options dialog',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.yellow4a.png',
    null,//'btnCssVividButton_iconBackground.png',
    '../siteMedia/btnOptions.png',

    '',

    null, null, null
);
echo $naWebOS->html_vividButton (
    7, 'display:inline-block',
    
    'newsApp_search', 
    'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.apps.loaded[\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news\'].viewSearchbar(event)',
    '',
    '',
    
    501, 'Search for specific news',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.yellow4a.png',
    null,//'btnCssVividButton_iconBackground.png',
    '../apps/NicerAppWebOS/applications/2D/news/btnSearch.png',
    
    '',
    
    null, null, null
);
echo $naWebOS->html_vividButton (
    7, 'display:inline-block;',
    
    'newsApp_lock', 
    'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.apps.loaded[\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news\'].toggleLock()',
    '',
    '',
    
    502, 'SPACE key pauses',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.yellow4a.png',
    null,//'btnCssVividButton_iconBackground.png',
    '../apps/NicerAppWebOS/applications/2D/news/btnLock_off.png',
    
    '',
    
    null, null, null
);
echo $naWebOS->html_vividButton (
    7, 'display:inline-block;',
    
    'newsApp_next', 
    'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'na.apps.loaded[\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news\'].gotoNextPage()',
    '',
    '',
    
    503, 'Alt-n goes to next page',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.yellow4a.png',
    null,//'btnCssVividButton_iconBackground.png',
    '../apps/NicerAppWebOS/applications/2D/news/btnNext.png',
    
    '',
    
    null, null, null
);
?>
<!--
                            <img id="newsApp_search" src="nicerapp/apps/NicerAppWebOS/applications/2D/news/btnSearch.png" style="vertical-align:middle;height:2em" onclick="na.apps.loaded['news'].viewSearchbar(event)" title="Search for specific news" alt="Search for specific news"/>
                            <img id="newsApp_lock" class="btn" src="nicerapp/apps/NicerAppWebOS/applications/2D/news/btnLock_off.png" onclick="na.apps.loaded['news'].toggleLock();" style="vertical-align:middle;height:2em;display:none;z-index:9999999999999;" title="[SPACE] pauses" alt="SPACE key pauses"/>
                            <img id="newsApp_next" class="btn" src="nicerapp/apps/NicerAppWebOS/applications/2D/news/btnNext.png" onclick="na.apps.loaded['news'].gotoNextPage();" style="vertical-align:middle;height:2em;display:none;z-index:9999999999999;" title="[ALT] goes to next page" alt="ALT key goes to next page"/>
-->
                        </div>
                    </div>
                </div>
                          
<!--                          
                <table id="newsApp_title_table" style="width:calc(100% - 20px);">
                    <tr>
                        <td id="td_newsApp_title" style="width:1%;white-space:nowrap;margin:5px;vertical-align:middle;">
                        <h1 id="newsApp_title" class="newsApp_header" style="display:none;padding:0px;margin:0px;vertical-align:middle;"><?php echo $pageTitle;?></h1>
                        </td>
                        <td id="td_newsApp_searchbar_spacer" style="width:20px;display:none;">&nbsp;</td>
                        <td id="td_newsApp_searchbar" style="vertical-align:middle;display:none;">
                            <label for="newsApp_searchbar" id="label_newsApp_searchbar" class="label_newsApp" style="width:80px;">Search :</label>
                            <input id="newsApp_searchbar" style="display:none; background:rgba(255,255,255,1); border-radius:7px; border : 1px solid black;vertical-align:middle" onchange="na.apps.loaded['news'].onSearch(event)"/>
                            <img id="newsApp_searchbar__enterQuery" title="search the entire news database (up to 14 days into the past)" src="/siteMedia/na.question-mark.svg.png" style="height:30px;vertical-align:middle" onclick="na.apps.loaded['news'].onSearch(event)"/>
                            <img id="newsApp_searchbar__abandonQuery" title="abandon the current search query and display the very latest news again" src="/siteMedia/na.reset.png" style="height:30px;vertical-align:middle" onclick="na.apps.loaded['news'].clearSearch(event)"/>
                        </td>
                        <td id="td_newsApp_info" style="text-align:left;vertical-align:middle;">
                            <span id="newsApp_info" style="text-align:left;display:none;">This app will load up older news-items whenever needed</span>
                        </td>
                        <!--
                        <td><span id="newsApp_debug" class="newsApp_header" style="font-weight:bold;"></span></td>
                        -- >
                        <td style="text-align:right;vertical-align:middle;">
                            <span id="newsApp_timer" class="newsApp_header" style="display:none;font-weight:bold;"></span>
                        </td>
                        <td style="width:110px;vertical-align:middle;" id="newsApp_header_buttons">
                            <img id="newsApp_search" src="nicerapp/apps/NicerAppWebOS/applications/2D/news/btnSearch.png" style="vertical-align:middle;height:2em" onclick="na.apps.loaded['news'].viewSearchbar(event)" title="Search for specific news" alt="Search for specific news"/>
                            <img id="newsApp_lock" class="btn" src="nicerapp/apps/NicerAppWebOS/applications/2D/news/btnLock_off.png" onclick="na.apps.loaded['news'].toggleLock();" style="vertical-align:middle;height:2em;display:none;z-index:9999999999999;" title="[SPACE] pauses" alt="SPACE key pauses"/>
                            <img id="newsApp_next" class="btn" src="nicerapp/apps/NicerAppWebOS/applications/2D/news/btnNext.png" onclick="na.apps.loaded['news'].gotoNextPage();" style="vertical-align:middle;height:2em;display:none;z-index:9999999999999;" title="[ALT] goes to next page" alt="ALT key goes to next page"/>
                        </td>
                    </tr>
                </table>
-->                
                <div id="app_mainmenu" class="vividMenu" theme="dark" style="position:absolute;width:200px;height:35px;opacity:0.0001;display:none;">
                    <?php echo require_return(dirname(__FILE__).'/mainmenu.php');
                    ?>
                </div>
            </div>
            <!--
            <div id ="siteContent__sidebar__left" class="saSidebarLeftInDialog" style="position:absolute;top:0px;left:0px;width:30px;height:10px;background:yellow;z-index:200;"></div>
            <div id ="siteContent__sidebar__right" class="saSidebarRightInDialog" style="position:absolute;top:0px;left:0px;width:30px;height:10px;background:yellow;z-index:200;"></div>
            -->

            <!--
<div class="loading">
         <div class="loader">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="loaderAfter">
        Now Loading
        </div>
</div>          
-->
            <div id ="siteContent__content" class="saContent" style="position:absolute;top:0px;left:0px;width:100%;height:100%;">
                <div id="newsApp_content_shadow" class="" style="position:absolute;width:100%;height:100%;opacity:0.0001;z-index:-1"></div>
                <div id="newsApp_content" class="" style="position:absolute;width:100%;height:100%;z-index:10"></div>
            </div> 
            <div id="siteContent__textarea" style="display:none;position:relative;opacity:0">
                <textarea id="siteContent__textareaCopy"></textarea>
            </div>

            <div id="siteContent__btnOptions_menu" class="vividDialogPopup anchored vividScrollpane" style="display:none;" onmouseover="var na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db; c.dontHide = true;" onmouseout="var na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db; c.dontHide = false; na1.hideOptionsDialog();">
                <?php
                    global $naWebOS;
                    $fn = dirname(__FILE__).'/btnOptions_menu__default.php';
                    if (file_exists($fn)) echo require_return($fn);
                ?>
            </div>


            
            <!--
            <div id="newsApp_content" class="saContent" style="width:100%;height:auto">
                Under construction.
            </div>
            -->
            <!--
            <div id ="siteContent__footer" class="saFooterInDialog" style="position:absolute;height:40px;background:green;z-index:200;"></div>
            -->
        
