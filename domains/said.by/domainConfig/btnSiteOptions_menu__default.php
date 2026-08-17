<?php
    require_once (realpath(dirname(__FILE__).'/..').'/NicerAppWebOS/boot.php');
    global $naWebOS;
    error_reporting (E_ALL);
?>
<form id="myForm" name="myForm">
<div id="btnOptions_menu__background" class="vividDialogPopup_background" style="margin:0;padding:0;"></div>

<?php
    global $naWebOS;
    echo $naWebOS->html_vividButton (
        0, 'align-items:center;justify-content:center;margin-right:10px;margin-left:10px;',

        'btnClearCookies',
        'vividButton_icon_50x50 grouped btnDelete forum', '_50x50', 'grouped',
        '',
        'na.m.clearCookies(event)',
        '',
        '',

        201, 'Reset cookies.',


        'btnCssVividButton_outerBorder.png',
        'btnCssVividButton.blue1a.png',
        null,//'btnCssVividButton_iconBackground.png',
        'btnTrashcan_red.png',

        '',

        'Reset cookies.',
        'grouped btnDelete themes',
        ''
    );
    global $naLAN;
    if ($naLAN) echo $naWebOS->html_vividButton (
        0, 'align-items:center;justify-content:center;margin-right:10px;margin-left:10px;',

        'btnFullResetOfAllThemes',
        'vividButton_icon_50x50 grouped btnDelete forum', '_50x50', 'grouped',
        '',
        'na.site.onclick_btnFullResetOfAllThemes(event)',
        '',
        '',

        201, 'Reset to default themes.',


        'btnCssVividButton_outerBorder.png',
        'btnCssVividButton.blue1b.png',
        null,//'btnCssVividButton_iconBackground.png',
        'btnTrashcan_red.png',

        '',

        'Reset to default themes.',
        'grouped btnDelete themes',
        ''
    );
    echo $naWebOS->html_vividButton (
        0, 'align-items:center;justify-content:center;margin-right:10px;margin-left:10px;',

        'btnDeleteThisTheme',
        'vividButton_icon_50x50 grouped btnDelete forum', '_50x50', 'grouped',
        '',
        'na.site.onclick_btnDeleteThisTheme(event)',
        '',
        '',

        202, 'Delete this theme.',


        'btnCssVividButton_outerBorder.png',
        'btnCssVividButton.blue1b.png',
        null,//'btnCssVividButton_iconBackground.png',
        'btnTrashcan_red.png',

        '',

        'Delete this theme.',
        'grouped btnDelete themes',
        ''
    );
    if ($naLAN) echo $naWebOS->html_vividButton (
        0, 'align-items:center;justify-content:center;margin-right:10px;margin-left:10px;',

        'btnDeleteAllUserThemes',
        'vividButton_icon_50x50 grouped btnDelete forum', '_50x50', 'grouped',
        '',
        'na.site.onclick_btnDeleteAllUserThemes(event)',
        '',
        '',

        203, 'Delete all user themes.',


        'btnCssVividButton_outerBorder.png',
        'btnCssVividButton.blue1b.png',
        null,//'btnCssVividButton_iconBackground.png',
        'btnTrashcan_red.png',

        '',

        'Delete all user themes.',
        'grouped btnDelete themes',
        ''
    );
    echo $naWebOS->html_vividButton (
        0, 'align-items:center;justify-content:center;margin-right:10px;margin-left:10px;',

        'btnDeleteAllOfMyThemes',
        'vividButton_icon_50x50 grouped btnDelete forum', '_50x50', 'grouped',
        '',
        'na.site.onclick_btnDeleteAllOfMyThemes(event)',
        '',
        '',

        204, 'Delete all of my client-specific themes.',


        'btnCssVividButton_outerBorder.png',
        'btnCssVividButton.blue1b.png',
        null,//'btnCssVividButton_iconBackground.png',
        'btnTrashcan_red.png',

        '',

        'Delete only my own themes.',
        'grouped btnDelete themes',
        ''
    );
?>
<?php
    echo $naWebOS->html_vividButton (
        0, 'align-items:center;justify-content:center;margin-right:10px;margin-left:10px;',

        'btnShowErrors',
        'vividButton_icon_50x50 grouped btnDelete forum', '_50x50', 'grouped',
        '',
        'na.site.onclick_displayErrors(event)',
        '',
        '',

        205, 'View PHP errors.',

        'btnCssVividButton_outerBorder.png',
        'btnCssVividButton.yellow1a.png',
        null,//'btnCssVividButton_iconBackground.png',
        'btnZoomIncrease.png', //!! !!

        '',

        'View PHP errors.',
        'grouped btnDelete menuLayoutData',
        ''
    );
?><?php
    /*echo $naWebOS->html_vividButton (
        0, 'align-items:center;justify-content:center;margin-right:10px;margin-left:10px;',

        'btnShowErrors',
        'vividButton_icon_50x50 grouped btnDelete forum', '_50x50', 'grouped',
        '',
        'na.site.onclick_displayPHPerrors(event)',
        '',
        '',

        203, 'View all PHP errors of the past 2 hours',

        'btnCssVividButton_outerBorder.png',
        'btnCssVividButton.yellow1a.png',
        null,//'btnCssVividButton_iconBackground.png',
        'na.question-mark.svg.png', //!! !!

        '',

        'View all PHP errors of the past 2 hours',
        'grouped btnDelete menuLayoutData',
        ''
    );*/
?>

<div class="btnOptions_menu__specificity__containerDiv" style="display:flex;align-items:center;height:auto;width:100%;">
    <!--<span class="label_specificityOrThemeChange">Specificity</span><select id="specificityChange_specificityName" type="text"></select>-->
    <span class="btnOptions_menu__label__specificity_dropdown">Specificity</span>
    <div id="btnOptions_menu__specificity_dropdown" class="na_themes_dropdown na_themes_dropdown__specificity"></div>
    <div id="btnLockSpecificity" class="vividButton4" buttonType="btn_lock" onclick="na.site.settings.lockSpecificity = !na.site.settings.lockSpecificity;" style="top:-15px"></div>
</div>
<div class="btnOptions_menu__theme__containerDiv" style="display:flex;align-items:center;height:44px;">
    <!--<span class="label_specificityOrThemeChange">Theme</span><select id="themeChange_themeName" type="text" onchange="na.te.themeSelected(event);"></select>-->
    <span class="btnOptions_menu__label__themes_dropdown">Theme</span>
    <div id="btnOptions_menu__themes_dropdown" class="na_themes_dropdown na_themes_dropdown__themes"></div>
</div>




<p style="margin:5px;padding:5px;text-align:center;width:calc(100%-10px);">Visibility of Dialog Settings icons :</p>
<div id="btnOptions_menu__showVividDialogSettings__containerDiv" style="">
    <div style="margin-left:10px;width:calc(100% - 20px);">
        <select id="vdSettings_show" name="vdSettings_show" style="background:rgba(255,255,255,0.4);color:yellow" onchange="var opacity=$('#vdSettings_show').val()=='hidden'?0.000001:$('#vdSettings_show').val()=='transparent'?0.5:1; $('.vdSettings').css({opacity:opacity}); na.site.saveTheme();">
            <option value="hidden" style="color:navy">Hidden until hovered over</option>
            <option value="transparent" style="color:navy">Semi-transparent until hovered over</option>
            <option value="shown" style="color:navy">Shown</option>
        </select>
    </div>
</div>

<p style="margin:5px;padding:5px;text-align:center;width:calc(100%-10px);">Set the time between background changes :</p>
<div id="btnOptions_menu__backgroundTimeSettingsChanged_save__containerDiv" style="">
    <div style="margin-left:10px;width:calc(100% - 20px);">
        <input id="changeBackgroundsAutomatically" type="checkbox" onchange="if (na.site.s.timeout_changeBackgroundsAutomatically) clearTimeout(na.site.s.timeout_changeBackgroundsAutomatically); na.site.s.timeout_changeBackgroundsAutomatically = setTimeout(na.site.saveTheme, 250);">
        <label id="changeBackgroundsAutomatically_label" class="smallPadding" for="changeBackgroundsAutomatically" onclick="if (na.site.s.timeout_changeBackgroundsAutomatically) clearTimeout(na.site.s.timeout_changeBackgroundsAutomatically); na.site.s.timeout_changeBackgroundsAutomatically = setTimeout(na.site.saveTheme, 250);">Change backgrounds automatically</label>
    </div>
    <div style="margin:10px;width:calc(100% - 40px);">
        <label for="backgroundChange_hours" style="">Hours<input id="backgroundChange_hours" name="backgroundChange_hours" type="number" min="0" max="23" value="0" style="width:40px;height:1em;margin-left:15px;" onchange="if (na.site.s.c.timeout_changeBackgroundsAutomatically) clearTimeout(na.site.s.c.timeout_changeBackgroundsAutomatically); na.site.s.c.timeout_changeBackgroundsAutomatically = setTimeout(na.site.saveTheme, 250);"/></label>
        <label for="backgroundChange_minutes" style="margin-left:10px;">Minutes<input id="backgroundChange_minutes" name="backgroundChange_minutes" type="number" min="1" max="59" value="5" style="width:40px;height:1em;margin-left:15px;" onchange="if (na.site.s.c.timeout_changeBackgroundsAutomatically) clearTimeout(na.site.s.c.timeout_changeBackgroundsAutomatically); na.site.s.c.timeout_changeBackgroundsAutomatically = setTimeout(na.site.saveTheme, 250);"/></label>
    </div>
</div>


<p class="smallPadding" style="width:calc(100%-10px);margin:5px;padding:3px;text-align:center;">Fading speed of menus :</p>
<input id="menusFadingSpeed" type="range" min="300" max="1000" value="400" class="sliderOpacityRange" style="left:10px;width:calc(100% - 30px);position:relative;" onchange="na.site.menusFadingSpeed_change(event);"/>

<div style="margin-left:10px;width:calc(100% - 20px)">
    <input id="menusUseRainbowPanels" type="checkbox" onchange="na.site.menusUseRainbowPanels_change(event)" CHECKED>
    <label id="menusUseRainbowPanels_label" class="smallPadding" for="menusUseRainbowPanels" onclick="setTimeout(na.site.saveTheme, 250);">Menus use rainbow colored panels</label>
    <!--
        TODO :
        i'm going to need radio-buttons (category, behavior, mode, type) *and* checkboxes (to enable specific functionality common to all these categories) in these lines.

        but what works even better is a vividMenu here, version 5.1.z, which has a setting to make vividButton in that vividMenu have an enabled/disabled setting and user-interface css declaration (an animation in yellow boxshadow for instance, or blue-cyan or whatever).

        and then a version 5.2.z, which enables vividDialogPopup dialogs to be popped up via a menu.
    -->
</div>


<p class="smallPadding" style="width:calc(100%-10px);margin:5px;padding:3px;text-align:center;">Transparency value of text background :</p>
<input id="textBackgroundOpacity" type="range" min="1" max="100" value="50" class="sliderOpacityRange" style="left:10px;width:calc(100% - 30px);position:relative;" onchange="na.te.textBackgroundOpacityChange(event);"/>
<br/>








</form>
