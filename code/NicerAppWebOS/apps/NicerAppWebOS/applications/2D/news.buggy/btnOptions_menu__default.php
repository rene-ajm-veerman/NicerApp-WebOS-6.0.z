<?php
    require_once (realpath(dirname(__FILE__).'/../../../../../..').'/NicerAppWebOS/boot.php');
    global $naWebOS;
    error_reporting (E_ALL);
?>
<div id="btnOptions_menu__background"></div>

<p style="margin:5px;padding:5px;text-align:center;width:calc(100%-10px);">Set the time to show only the background during page switches :</p>
<div id="btnOptions_menu__backgroundTimeSettingsChanged_save__containerDiv">
    <div style="margin-left:10px;width:calc(100% - 20px)">
        <input id="blankScreenBeforePageChange" type="checkbox" onchange="var na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db; na1.settingsChanged(event);">
        <label id="blankScreenBeforePageChange_label" class="smallPadding" for="blankScreenBeforePageChange" onclick="setTimeout(function() {var na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db; na1.settingsChanged(event);}, 250);">Blank the screen several seconds before changing page?</label>
    </div>
    <div style="margin:10px;width:calc(100% - 40px)">
        <span>Min</span><input id="blankScreenMin" type="number" min="1" max="20" value="1" style="width:40px;height:1em;margin-left:15px" onchange="var na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db; na1.settingsChanged(event);"></input>
        <span style="margin-left:10px;">Max</span><input id="blankScreenMax" type="number" min="3" max="59" value="3" style="width:40px;height:1em;margin-left:15px" onchange="var na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db; na1.settingsChanged(event);"></input>
    </div>
</div>

<p style="margin:5px;padding:5px;text-align:center;width:calc(100%-10px);">Speed of putting new items on screen :</p>
<div id="btnOptions_menu__backgroundTimeSettingsChanged_save__containerDiv">
    <div style="margin-left:10px;width:calc(100% - 20px)">
        <input id="delayNewItemsDisplayToPage" type="checkbox" onchange="var na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db; na1.settingsChanged(event);">
        <label id="delayNewItemsDisplayToPage_label" class="smallPadding" for="delayNewItemsDisplayToPage" onclick="/*setTimeout(function() {var na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db; na1.settingsChanged(event);}, 250);*/">Delay before setting new news items on the page?</label>
    </div>
    <div style="margin:10px;width:calc(100% - 40px)">
        <span>Min</span><input id="delayNewItemsMin" type="number" min="0" max="2000" value="200" style="width:100px;height:1em;margin-left:15px" onchange="var na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db; na1.settingsChanged(event);"></input>
        <span style="margin-left:10px;">Max</span><input id="delayNewItemsMax" type="number" min="0" max="2000" value="500" style="width:100px;height:1em;margin-left:15px" onchange="var na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'], g = na1.globals, s = na1.settings, c = s.current, db = c.db; na1.settingsChanged(event);"></input>
    </div>
</div>
