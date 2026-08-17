<?php
    global $cms;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" style="width:100%;height:100%;">
<head>
<!--
https://nicer.app and https://said.by are Copyrighted (c) 2002-2022 by Rene A.J.M. Veerman from The Netherlands.

LICENSE : see https://nicer.app/LICENSE.txt
-->

<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />-->
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
{$viewport}
<meta name="HandheldFriendly" content="true" />
{$cssLinks}
{$javascriptLinks}

<script id="jsBasicGlobals" type="text/javascript">
na.site.globals = $.extend(na.site.globals, {
    referer : '<?php echo (array_key_exists('HTTP_REFERER',$_SERVER)?$_SERVER['HTTP_REFERER']:'');?>',
    myip : '<?php echo str_replace('.','_',(array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']))?>',
    domain : '{$domain}'
});
</script>
{$pageSpecificCSS}


    <link rel="apple-touch-icon" sizes="180x180" href="/nicerapp/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/nicerapp/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/nicerapp/favicon/favicon-16x16.png">
    <link rel="manifest" href="/nicerapp/favicon/site.webmanifest">
    <link rel="mask-icon" href="/nicerapp/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="/nicerapp/favicon/favicon.ico">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-config" content="/nicerapp/favicon/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    <title>{$title}</title>
</head>
<body onload="na.site.onload(event);" onresize="if (na && na.site.settings.desktopReady) na.site.onresize({reloadMenu:true})">
    <div class="lds-facebook"><!-- thanks for allowing CC0 license usage : https://loading.io/css/ --><div></div><div></div><div></div></div> 

    <!-- BEGIN https://nicer.app customer HTML -->
    {$customerHTML}
    <!-- END https://nicer.app customer HTML -->

    <div id="siteBackground"> 
        <div id="siteBackground_bg"></div>
        <div id="siteBackground_bg2"></div>
        <script id="siteBackground_iframe_js" type="text/javascript"></script>
        <iframe id="siteBackground_iframe"></iframe>
        <img class="bg_first" alt=""/>
        <img class="bg_last" alt=""/>
    </div>
    <div id="siteContent" class="vividDialog">
    <div class="vividDialogContent vividScrollpane">
{$div_siteContent}    
    </div>
    </div>
    
    <div id="siteLoginLogout" class="vividDialog" style="display:none;z-index:2000;">
    <div class="vividDialogContent vividScrollpane">
    </div>
    </div>

    <div id="siteYoutubePlayer" class="vividDialog" style="display:none;justify-content:center;align-items:center;text-align:center;">
        {$div_siteYoutubePlayer}
    </div>

    <div id="siteYoutubeSearch" class="vividDialog" style="display:none;justify-content:center;align-items:center;text-align:center;">
        {$div_siteYoutubeSearch}
    </div>

    
    <div id="siteComments" class="vividDialog" style="display:none;justify-content:center;align-items:center;text-align:center;">
        {$div_siteComments}
    </div>
    
    <div id="siteToolbarTop" class="vdToolbar vividDialog">
    <div class="vividDialogContent vividScrollpane">
        {$div_siteToolbarTop}
    </div>
    </div>

    <div id="siteToolbarLeft" class="vdToolbar vividDialog">
    <div class="vividDialogContent vividScrollpane">
        {$div_siteToolbarLeft}
    </div>
    </div>
    
    <div id="siteToolbarRight" class="vdToolbar vividDialog">
    <div class="vividDialogContent vividScrollpane">
        {$div_siteToolbarRight}
    </div>
    </div>
    
    <div id="siteErrors" class="vividDialog">
    <div class="vividDialogContent vividScrollpane">
        ($div_siteErrors}
    </div>
    </div>

    <div id="siteToolbarDialogSettings" class="vdToolbar vividDialog">
    <div class="vividDialogContent vividScrollpane" style="overflow-x:hidden">
        <div class="sds_dialogTitle" style="vertical-align:middle;">
            <!--<img srcPreload="/nicerapp/siteMedia/btnSettingsBorder.png"/>-->
            <span id="btnViewResult" class="vividButton_icon_sdsdt tooltip" title="View result" alt="View result" onclick="if (!$(this).is('.disabled')) { if (!na.desktop.settings.visibleDivs.includes('#siteContent'))  na.desktop.settings.visibleDivs.push('#siteContent'); na.desktop.settings.visibleDivs.remove('#siteToolbarDialogSettings'); na.site.settings.activeDivs=['#siteContent']; na.desktop.resize();}">
                <img class="cvbImgButton" srcPreload="/nicerapp/siteMedia/btnBack.png"/>
            </span>
            <span id="sds_dialogTitle">Theme Settings</span>
        </div>
        <div class="flexBreak"></div>
        <div id="specificitySettings" class="dialogSettingsComponent_alwaysVisible" style="font-size:15px;flex-wrap:wrap;">
            <span id="specificityForDiv_label">For : </span><span id="specificityForDiv"></span>
            <div class="flexBreak"></div>
            
            <!--
            <label id="label_cb_app" class="specificityCB" style="order:0">
            <input type="checkbox" id="cb_app" class="specificityCB" onclick="na.ds.cbSelected(event)"></input>
            <span id="span_cb_app"></span>
            </label>
            <div class="flexBreak"></div>
            -->
            
            <!--<label id="label_cb_specificity" for="cb_specificity" class="specificityCB" stye="order:1;vertical-align:middle;">
                <input type="checkbox" id="cb_specificity" class="specificityCB" checked onclick="na.ds.cbSelected(event)" style="order:1;margin-top:8px;"></input>-->
                <label for="specificity" class="specificityLabel" style="order:1;vertical-align:middle;font-weight:bold">Specificity</label>
                <select id="specificity" class="select" onchange="na.ds.specificitySelected(event)" style="order:1;vertical-align:middle;"></select>
                <div id="btnDeleteSpecificity" class="vividButton_icon tooltip" title="Delete all cosmetic settings for this specificity" alt="Delete all cosmetic settings for this specificity" onclick="if (!$(this).is('.disabled')) na.ds.deleteSpecificity(event)" style="float:right;order:1;margin-left:auto;vertical-align:middle;width:40px;height:40px;position:relative;display:inline-block;">
                    <div class="cvbBorderCSS" style="width:40px;height:40px;"></div>
                    <img class="cvbImgBorder" srcPreload="/nicerapp/siteMedia/btnCssVividButton_outerBorder.png" style="width:40px;height:40px;"/>
                    <img class="cvbImgTile" srcPreload="/nicerapp/siteMedia/btnCssVividButton.png" style="width:40px;height:40px;"/>
                    <img class="cvbImgButton" srcPreload="/nicerapp/siteMedia/iconDelete.png" style="position:absolute;top:2px;left:2px;width:36px;height:36px;"/>
                </div>
                
            <!--</label>-->
            
            <label id="labelTheme" for="themes" class="specificityLabel" style="order:2;font-weight:bold;vertical-align:middle">Theme</label>
            <select id="themes" class="select" onchange="na.ds.themeSelected(event)" style="order:2;vertical-align:middle;">
                <option id="theme_default" name="theme_default" value="default">default</option>
            </select>
            <div id="btnSetPermissionsForTheme" class="vividButton_icon tooltip" title="Create or delete theme, and set permissions for current theme" alt="Create or delete theme, and set permissions for this theme" onclick="if (!$(this).is('.disabled')) na.ds.setPermissionsForTheme(event)" style="order:2;margin-left:auto;vertical-align:middle;width:40px;height:40px;position:relative;display:inline-block;float:right;">
                <div class="cvbBorderCSS" style="width:40px;height:40px;"></div>
                <!--<img class="cvbImgBorder" srcPreload="/nicerapp/siteMedia/btnCssVividButton_outerBorder.png" style="width:70px;height:70px;"/>-->
                <img class="cvbImgTile" srcPreload="/nicerapp/siteMedia/btnCssVividButton.red1b.png" style="width:40px;height:40px;opacity:0.67"/>
                <img class="cvbImgButton" srcPreload="/nicerapp/siteMedia/1660_blk_19329_zoom.upperBodyOnly.256x256.png" style="position:absolute;top:2px;left:2px;width:36px;height:36px;z-index:2020;"/>
                <img class="cvbImgButton_sup1" srcPreload="/nicerapp/siteMedia/btnTrashcan2_white_lowres.png" style="position:absolute;width:15px;height:19px;z-index:2021;"/>
                <img class="cvbImgButton_sup2" srcPreload="/nicerapp/siteMedia/documentAdd_lowres.png" style="position:absolute;left:30px;width:20px;height:20px;z-index:2021;"/>
                
            </div>
            
            <div class="navbar" style="order:5">
                <!--<img srcPreload="/nicerapp/siteMedia/btnSettingsBorder.png"/>-->
                <!--<div id="btnViewResult" class="vividButton_icon_sdsnav tooltip" title="View result" alt="View result" onclick="if (!$(this).is('.disabled')) { if (!na.desktop.settings.visibleDivs.includes('#siteContent'))  na.desktop.settings.visibleDivs.push('#siteContent'); na.desktop.settings.visibleDivs.remove('#siteToolbarDialogSettings'); na.site.settings.activeDivs=['#siteContent']; na.desktop.resize();}">
                    <div class="cvbBorderCSS"></div>
                    <! --<img class="cvbImgTile" srcPreload="/nicerapp/siteMedia/btnCssVividButton.green2a.png"/>-- >
                    <img class="cvbImgButton" srcPreload="/nicerapp/siteMedia/btnBack.png"/>
                </div>
                -->
                <div id="btnSelectBorderSettings" class="vividButton_icon_sdsnav tooltip" title="Set border settings" alt="Set border settings"  onclick="if (!$(this).is('.disabled')) na.dialogSettings.selectBorderSettings(event)">
                    <div class="cvbBorderCSS"></div>
                    <!--<img class="cvbImgTile" srcPreload="/nicerapp/siteMedia/btnCssVividButton.grey2a.png"/>-->
                    <img class="cvbImgButton" srcPreload="/nicerapp/siteMedia/btnSettingsBorder3.png"/>
                </div>
                <div id="btnSelectBoxShadowSettings" class="vividButton_icon_sdsnav tooltip" title="Set border shadow" alt="Set border shadow"  onclick="if (!$(this).is('.disabled')) na.dialogSettings.selectBoxShadowSettings(event)">
                    <div class="cvbBorderCSS"></div>
                    <img class="cvbImgTile" srcPreload="/nicerapp/siteMedia/btnCssVividButton.orange1c.png"/>
                </div>
                <div id="btnSelectBackgroundColor" class="vividButton_icon_sdsnav tooltip" title="Set background color" alt="Set background color"  onclick="if (!$(this).is('.disabled')) na.dialogSettings.selectBackground_color(event)">
                    <div class="cvbBorderCSS"></div>
                    <img class="cvbImgTile" srcPreload="/nicerapp/siteMedia/btnCssVividButton.yellow1a.png"/>
                    <img class="cvbImgButton" srcPreload="/nicerapp/siteMedia/btnColorPicker.png"/>
                </div>
                <div id="btnSelectBackgroundFolder" class="vividButton_icon_sdsnav tooltip" title="Set background image folder" alt="Set background image folder" onclick="if (!$(this).is('.disabled')) na.dialogSettings.selectBackground_folder(event)">
                    <div class="cvbBorderCSS"></div>
                    <img class="cvbImgTile" srcPreload="/nicerapp/siteMedia/btnCssVividButton.yellow1b.png"/>
                    <img class="cvbImgButton" srcPreload="/nicerapp/siteMedia/fileTree_1b.png"/>
                </div>
                <div id="btnSelectBackgroundImage" class="vividButton_icon_sdsnav tooltip" title="Set background image" alt="Set background image"  onclick="if (!$(this).is('.disabled')) na.dialogSettings.selectBackground_image(event)">
                    <div class="cvbBorderCSS"></div>
                    <img class="cvbImgTile" srcPreload="/nicerapp/siteMedia/btnCssVividButton.yellow1a.png"/>
                    <img class="cvbImgButton" srcPreload="/nicerapp/siteMedia/btnBackground.png"/>
                </div>
                <div id="btnSelectTextSettings" class="vividButton_icon_sdsnav tooltip" title="Set text settings" alt="Set text settings"  onclick="if (!$(this).is('.disabled')) na.dialogSettings.selectTextSettings(event)">
                    <div class="cvbBorderCSS"></div>
                    <img class="cvbImgTile" srcPreload="/nicerapp/siteMedia/btnCssVividButton.yellow1a.png"/>
                    <img class="cvbImgButton" srcPreload="/nicerapp/siteMedia/btnSettingsText.png"/>
                </div>
                <div id="btnSelectTextShadowSettings" class="vividButton_icon_sdsnav tooltip" title="Set text shadow" alt="Set text shadow"  onclick="if (!$(this).is('.disabled')) na.dialogSettings.selectTextShadowSettings(event)">
                    <div class="cvbBorderCSS"></div>
                    <img class="cvbImgTile" srcPreload="/nicerapp/siteMedia/btnCssVividButton.yellow1a.png"/>
                    <img class="cvbImgButton" srcPreload="/nicerapp/siteMedia/btnSettingsTextShadow.png"/>
                </div>
            </div>
            
            <!--
            <label id="labelWhichSetting" for="whichSetting" class="specificityLabel" style="order:3;vertical-align:middle;">Set</label>
            <select id="whichSetting" class="select" onchange="na.ds.whichSettingSelected(event)" style="order:3;vertical-align:middle;">
                <option id="set_border" value="border">Border</option>
                <option id="set_boxShadow" value="boxShadow">Border shadow</option>
                <option id="set_backgroundColor" value="backgroundColor" selected>Background color</option>
                <option id="set_backgroundFolder" value="backgroundFolder">Background folder</option>
                <option id="set_backgroundImage" value="backgroundImage">Background image</option>
                <option id="set_textSettings" value="text">Text</option>
                <option id="set_textShadowSettings" value="textShadow">Text shadow</option>
                <! --<option id="set_scrollbar" value="scrollbar">Scrollbars</option>- ->
            </select>
            <div id="btnSpacer2" class="vividButton_icon tooltip" style="width:40px;order:4;margin-left:auto;height:17px;vertical-align:middle;position:relative;display:inline-block">
            </div>
            -->
            
            <label for="dialogSettings_specificity_dialog" class="labelDialogSettings2" style="order:4">Dialog
                <input type="radio" id="dialogSettings_photoSpecificity_dialog" name="sdad" class="radioInput" value="dialog" checked="checked" style="order:4"/>
            </label>
            <label for="dialogSettings_specificity_allDialogs" class="labelDialogSettings2" style="order:4;white-space:nowrap;">All dialogs
                <input type="radio" id="dialogSettings_photoSpecificity_allDialogs" name="sdad" class="radioInput" value="dialog" style="order:4"/>
            </label>
            <div id="btnSpacer1" class="vividButton_icon tooltip" style="order:4;margin-left:auto;width:70px;height:20px;vertical-align:middle;position:relative;display:inline-block">
            </div>

        </div>
        <div class="flexBreak"></div>
        <div id="borderSettings" class="dialogSettingsComponent vividScrollpane" style="top:auto;">
            <input id="borderColorpicker" class="dialogSettingsComponent dialogSettings_colorPicker" style="position:absolute;"></input>
            <div class="flexBreak" style="height:5px;"></div>
            
            <label id="labelBorderType" for="borderType" class="boxSettingsLabel">Type</label>
            <select class="select" id="borderType" onchange="na.ds.borderSettingsSelected()">
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
            <div class="flexBreak"></div>

            <label id="labelBorderWidth" for="borderWidth" class="boxSettingsLabel">Width</label>
            <input id="borderWidth" type="range" min="0" max="20" value="3" class="sliderOpacityRangeBorderSettings" onchange="na.ds.borderSettingsSelected();"/>
            <div class="flexBreak"></div>
            
            <label id="labelBorderRadius" for="borderRadius" class="boxSettingsLabel">Radius</label>
            <input id="borderRadius" type="range" min="0" max="50" value="20" class="sliderOpacityRangeBorderSettings" onchange="na.ds.borderSettingsSelected();"/>
        </div>
        <div class="flexBreak"></div>
        <div id="boxShadowSettings" class="dialogSettingsComponent vividScrollpane" style="top:auto;">
            <label id="labelBoxShadow" class="boxSettingsLabel">Box shadows</label>
            <div id="boxShadowControls" style="display:table-cell;">
                <img srcPreload="/nicerapp/siteMedia/iconCreate.png" onclick="na.ds.addBoxShadow()"/>
                <img srcPreload="/nicerapp/siteMedia/iconDelete.png" onclick="na.ds.deleteBoxShadow()"/>
                <div id="boxShadow_0" class="boxShadow" style="background:rgba(200,200,200,1);border:1px solid lime; box-shadow:2px 2px 2px 2px rgba(0,0,0,0.5); border-radius:10px; margin : 5px; padding : 5px;" onclick="na.ds.boxSettingsSelected(event);">ABC XYZ</div>
            </div>
            <div class="flexBreak"></div>
            
            <label id="labelBoxShadowInset" class="boxSettingsLabel" for="boxShadowInset">Inset</label>
            <input id="boxShadowInset" type="checkbox" onchange="na.ds.boxSettingsChanged();"/>
            <span id="boxShadowInsetClear">&nbsp;</span>
            <div class="flexBreak"></div>
            
            <label id="labelBoxShadowXoffset" class="boxSettingsLabel" for="boxShadowXoffset">Hor-offset</label>
            <input id="boxShadowXoffset" type="range" min="-10" max="10" value="2" class="sliderOpacityRangeBorderSettings" onchange="na.ds.boxSettingsChanged();"/>
            <div class="flexBreak"></div>

            <label id="labelBoxShadowYoffset" class="boxSettingsLabel" for="boxShadowYoffset">Ver-offset</label>
            <input id="boxShadowYoffset" type="range" min="-10" max="10" value="2" class="sliderOpacityRangeBorderSettings" onchange="na.ds.boxSettingsChanged();"/>
            <div class="flexBreak"></div>

            <label id="labelBoxShadowSpreadRadius" class="boxSettingsLabel" for="boxShadowSpreadRadius">Spread</label>
            <input id="boxShadowSpreadRadius" type="range" min="0" max="10" value="2" class="sliderOpacityRangeBorderSettings" onchange="na.ds.boxSettingsChanged();"/>
            <div class="flexBreak"></div>

            <label id="labelBoxShadowBlurRadius" class="boxSettingsLabel" for="boxShadowBlurRadius">Blur</label>
            <input id="boxShadowBlurRadius" type="range" min="0" max="10" value="2" class="sliderOpacityRangeBorderSettings" onchange="na.ds.boxSettingsChanged();"/>
            <div class="flexBreak"></div>

            <label id="labelBoxShadowColor" class="boxSettingsLabel" for="boxShadowColor">Color</label>
            <input id="boxShadowColorpicker" class="dialogSettingsComponent" style="position:absolute;top:95px;"></input>
        </div>
        <div style="text-align:center;">
            <input id="colorpicker" class="dialogSettingsComponent dialogSettings_colorPicker" style="position:absolute;top:auto;"></input>
        </div>
        <div id="dialogSettings_jsTree" class="dialogSettingsComponent vividScrollpane" style="position:absolute;top:auto;display:none;"></div>
        <div id="dialogSettings_photoAlbum_specs" class="dialogSettingsComponent vividScrollpane" style="height:6.4em;flex-flow: wrap row;position:absolute;top:auto;display:none;">
            <label id="label_dialogSettings_photoOpacity" class="labelDialogSettings" for="dialogSettings_photoOpacity">Opacity</label>
            <input id="dialogSettings_photoOpacity" type="range" min="1" max="100" value="50" class="sliderOpacityRangeDialogSettings" oninput="if (na.ds.settings.current.selectedImage) na.ds.imageSelected(na.ds.settings.current.selectedImage);"/>
            <div class="flexBreak"></div><br/>
            
            <label id="label_dialogSettings_photoScaleX" class="labelDialogSettings" for="dialogSettings_photoScaleX">Scale hor</label>
            <input id="dialogSettings_photoScaleX" type="range" min="25" max="200" value="100" class="sliderOpacityRangeDialogSettings" style="top:30px;" oninput="if (na.ds.settings.current.selectedImage) na.ds.imageSelected(na .ds.settings.current.selectedImage);"/>
            <div class="flexBreak"></div>
            
            <label id="label_dialogSettings_photoScaleY" class="labelDialogSettings" for="dialogSettings_photoScaleY">Scale ver</label>
            <input id="dialogSettings_photoScaleY" type="range" min="25" max="200" value="100" class="sliderOpacityRangeDialogSettings" style="top:63px;" oninput="if (na.ds.settings.current.selectedImage) na.ds.imageSelected(na.ds.settings.current.selectedImage);"/>
            <div class="flexBreak"></div>
            
            <!--
            <div class="flexColumns" style="display:inline-flex;top:65px">
                <label for="dialogSettings_photoSpecificity_dialog" class="labelDialogSettings2">Dialog
                <input type="radio" id="dialogSettings_photoSpecificity_dialog" name="psdp" class="radioInput" value="dialog" checked="checked"/>
                </label>
                
                <label for="dialogSettings_photoSpecificity_page" class="labelDialogSettings2">Page
                <input type="radio" id="dialogSettings_photoSpecificity_page" name="psdp" class="radioInput" value="dialog"/>
                </label>
            </div>
            -->
        </div>
        <iframe id="dialogSettings_photoAlbum" class="dialogSettingsComponent" style="position:absolute;top:calc(230px + 4em);height:calc(100% - 230px - 4em); display:none;border:0px"></iframe>
        <div id="textSettings" class="dialogSettingsComponent vividScrollpane" style="position:absolute;top:auto;display:none;">
            <label id="labelTextFontFamily" class="textSettingsLabel" for="textFontFamily">Font</label>
            <select class="select" id="textFontFamily" onchange="na.ds.textSettingsSelected_updateDialog()">
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
            <div class="flexBreak"></div>

            <label id="labelTextSize" class="textSettingsLabel" for="textSize">Size</label>
            <input id="textSize" type="range" min="5" max="40" value="12" class="sliderOpacityRangeBorderSettings" onchange="na.ds.textSettingsSelected();"/>
            <div class="flexBreak" style="height:8px;"></div>

            <label id="labelTextWeight" class="textSettingsLabel" for="textWeight">Boldness</label>
            <input id="textWeight" type="range" min="3" max="10" value="4" class="sliderOpacityRangeBorderSettings" onchange="na.ds.textSettingsSelected();"/>
            <div class="flexBreak" style="height:8px;"></div>

            <label id="labelTextColor" class="textColorpicker" for="fontFamily">Color</label>
            <input id="textColorpicker" class="dialogSettingsComponent dialogSettings_colorPicker" style="position:absolute;top:95px;"></input>
            <div class="flexBreak"></div>
        </div>
        <div id="textShadowSettings" class="dialogSettingsComponent vividScrollpane" style="position:absolute;top:auto;display:none;">
            <label id="labelTextShadow" class="textSettingsLabel">Text shadow</label>
            <div id="textShadowControls">
                <img srcPreload="/nicerapp/siteMedia/iconCreate.png" onclick="na.ds.addTextShadow(event)"/>
                <img srcPreload="/nicerapp/siteMedia/iconDelete.png" onclick="na.ds.deleteTextShadow(event)"/>
                <div class="flexBreak"></div>
                <div id="textShadow_0" class="textShadow" style="background:navy;border:1px solid white; border-radius:10px; margin : 5px; padding : 5px;" onclick="na.ds.selectTextShadow(event)">ABC XYZ</div>
            </div>
            <div class="flexBreak"></div>
            
            <label id="labelTextShadowXoffset" class="textSettingsLabel2" for="textShadowXoffset">Hor-offset</label>
            <input id="textShadowXoffset" type="range" min="-10" max="10" value="2" class="sliderOpacityRangeBorderSettings" onchange="na.ds.textSettingsSelected();"/>
            <div class="flexBreak"></div>

            <label id="labelTextShadowYoffset" class="textSettingsLabel2" for="textShadowYoffset">Ver-offset</label>
            <input id="textShadowYoffset" type="range" min="-10" max="10" value="2" class="sliderOpacityRangeBorderSettings" onchange="na.ds.textSettingsSelected();"/>
            <div class="flexBreak"></div>

            <label id="labelTextShadowBlurRadius" class="textSettingsLabel2" for="textShadowBlurRadius">Blur</label>
            <input id="textShadowBlurRadius" type="range" min="0" max="10" value="2" class="sliderOpacityRangeBorderSettings" onchange="na.ds.textSettingsSelected();"/>
            <div class="flexBreak"></div>

            <label id="labelTextShadowColor" class="textSettingsLabel2" for="textShadowColor">Color</label>
            <input id="textShadowColorpicker" class="dialogSettingsComponent dialogSettings_colorPicker" style="position:absolute;top:95px;"></input>
            <div class="flexBreak"></div>
        </div>
        <div id="themePermissions" class="dialogSettingsComponent vividScrollpane" style="position:absolute;top:auto;display:none;">
            <label id="labelThemeControls" class="themePermissionsLabel" for="themePermissionsControls">Themes</label>
            <div id="themePermissionsControls">
                <img srcPreload="/nicerapp/siteMedia/iconCreate.png" onclick="na.ds.addTheme(event)"/>
                <img srcPreload="/nicerapp/siteMedia/iconDelete.png" onclick="na.ds.deleteTheme(true)"/>
                <div class="flexBreak"></div>
                <div id="theme_0" class="themeItem" onclick="na.ds.themeSelected(event)">default</div>
            </div>
            <div class="flexBreak"></div>

            <label id="labelThemeName" class="themePermissionsLabel" for="themeName">Name</label>
            <input id="themeName" class="themeStringInput" onchange="na.ds.onchange_themeName(event);"></input>
        </div>
    </div>
    </div>
    
    <div id="siteStatusbar" class="vividDialog"><div class="vividDialogContent vividScrollpane"></div></div>
    
    <div id="siteDateTime" class="vividDialog"><div class="vividDialogContent vividScrollpane"></div></div>
    
    <div id="btnOptions" class="vividButton_icon_siteTop" onclick="na.site.onclick_btnOptions()">
        <div class="cvbBorderCSS"></div>
        <img class="cvbImgBorder" srcPreload="/nicerapp/siteMedia/btnCssVividButton_outerBorder.png"/>
        <img class="cvbImgTile" srcPreload="/nicerapp/siteMedia/btnCssVividButton.png"/>
        <img class="cvbImgButtonBG" srcPreload="/nicerapp/siteMedia/btnCssVividButton_iconBackground.png"/>
        <img class="cvbImgButton" srcPreload="/nicerapp/siteMedia/btnOptions2.png"/>
    </div>

    <div id="btnLoginLogout" class="vividButton_icon_siteTop" onclick="na.site.displayLogin()">
        <div class="cvbBorderCSS"></div>
        <img class="cvbImgBorder" srcPreload="/nicerapp/siteMedia/btnCssVividButton_outerBorder.png"/>
        <img class="cvbImgTile" srcPreload="/nicerapp/siteMedia/btnCssVividButton.png"/>
        <img class="cvbImgButtonBG" srcPreload="/nicerapp/siteMedia/btnCssVividButton_iconBackground.png"/>
        <img class="cvbImgButton" srcPreload="/nicerapp/siteMedia/btnLogin2.png"/>
    </div>
    
    <div id="btnChangeBackground" class="vividButton_icon_siteTop" onclick="na.site.changeBackground()">
        <div class="cvbBorderCSS"></div>
        <img class="cvbImgBorder" srcPreload="/nicerapp/siteMedia/btnCssVividButton_outerBorder.png"/>
        <img class="cvbImgTile" srcPreload="/nicerapp/siteMedia/btnCssVividButton.png"/>
        <img class="cvbImgButtonBG" srcPreload="/nicerapp/siteMedia/btnCssVividButton_iconBackground.png"/>
        <img class="cvbImgButton" srcPreload="/nicerapp/siteMedia/btnBackground.png"/>
    </div>

    
    <div id="siteMenu" class="vividMenu" theme="{$theme}">
{$div_siteMenu}
    </div>
    
    <div id="siteRegistration" class="vividDialogPopup vividScrollpane">
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
            <br/>
            <button id="btnSrfSubmit" type="button" class="button" onclick="na.site.register();"><span>Register! <img srcPreload="/nicerapp/3rd-party/tinymce-4/plugins/naEmoticons/img/happy.gif"/></span></div>
        </div>
        
    </div>

    <div id="siteLogin" class="vividDialogPopup vividScrollpane" style="" onmouseenter="$(event.currentTarget).css({opacity:1});">
        <div id="siteLoginMsg"></div>
        <div id="siteLoginContainer">
            <form id="siteLoginForm" name="siteLoginForm" method="post" action="/login" autocomplete="on" onkeydown="if (event.keyCode==13) na.site.login()">
                <label for="username">Name</label>
                <input id="username" name="username" autocomplete="on" type="text"><br/>

                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="on"><br/>
                
                <input type="checkbox" id="rememberme" name="rememberme" CHECKED>
                <label for="rememberme">Remember me</label><br/>
                
                <input type="submit" value="Submit" style="opacity:0.0001">
            </form>
            <div class="buttonHolder">
                <button id="btnNewAccount" type="button" class="button" onclick="na.site.newAccount();"><span>New account</span></button>
                <button id="btnLogin" type="button" class="button" onclick="na.site.login();"><span>Log in</span></button>
            </div>
        </div>
    </div>

    <div id="siteLoginSuccessful" class="vividDialogPopup vividScrollpane">Login Successful! <img srcPreload="/nicerapp/3rd-party/tinymce-4/plugins/naEmoticons/img/happy.gif"/></div>
    
    <div id="siteLoginFailed" class="vividDialogPopup vividScrollpane">Login failed..</div>
    
    <div id="btnOptions_menu" class="vividDialogPopup vividScrollpane">
        <!-- left to be filled in by people who use nicerapp to run a website -->
        <?php 
            global $cms;
            $fn = realpath(dirname(__FILE__).'/../../../..').'/nicerapp/domainConfigs/'.$cms->domain.'/customerDiv__btnOptions_menu.php';
            if (file_exists($fn)) echo execPHP($fn);
        ?>
    </div>
    
    <!-- see fonts.google.com (thanks, Google!) -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans|ABeeZee|Aclonica|Acme|Actor|Advent+Pro|Akronim|Alex+Brush|Architects+Daughter|Archivo+Black|Baloo|Bebas+Neue|Caveat|Chewy|Cookie|Cormorant|Courgette|Covered+By+Your+Grace|Dancing+Script|El+Messiri|Exo|Exo+2|Galada|Gloria+Hallelujah|Great+Vibes|Handlee|Indie+Flower|Kalam|Kaushan+Script|Khula|Knewave|Krona+One|Lacquer|Lemonada|Lusitana|M+PLUS+1p|Marck+Script|Merienda+One|Modak|Montserrat|Montserrat+Alternates|Mr+Dafoe|Nanum+Pen+Script|Noto+Serif+JP|Odibee+Sans|Oleo+Script|Orbitron|PT+Sans|Parisienne|Pathway+Gothic+One|Permanent+Marker|Playball|Pridi|Quattrocento+Sans|Rock+Salt|Sacramento|Saira+Condensed|Saira+Extra+Condensed|Saira+Semi+Condensed|Satisfy|Shadows+Into+Light|Shadows+Into+Light+Two|Sigmar+One|Signika+Negative|Slabo+27px|Source+Code+Pro|Special+Elite|Spectral|Spinnaker|Sriracha|Unica+One|Acme|Lato:300,300i,400,400i|Montserrat|Mukta+Malar|Ubuntu|Indie+Flower|Raleway|Pacifico|Fjalla+One|Work+Sans|Gloria+Hallelujah&display=swap" rel="stylesheet" onload="$(document).ready(function(){setTimeout(function(){na.site.startTooltips(event);},4000);});">
    <!--<link href="https://fonts.googleapis.com/css?family=Krona+One|Open+Sans|Architects+Daughter&display=swap" rel="stylesheet" onload="$(document).ready(function(){nas.startTooltips(event);});">-->

</body>
</html>
