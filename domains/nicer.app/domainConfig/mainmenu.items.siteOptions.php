<?php 
    global $naWebOS;
    $dcfg = dirname(__FILE__);
?>
<li><a href="/">Site Options</a>
    <ul>
        <!--<?php echo require_return (dirname(__FILE__).'/mainmenu.items.site-options.php');?>-->
        <!--<li><a href="javascript:na.m.pageOptions();">Page Options</a></li>-->
        <!--
        <li><a href="javascript:na.m.revertToDefaults();">Use Default Settings</a>
            <ul>
            <li><a class="noPushState" href="javascript:na.site.code.reboot();">Simple Page Refresh</a></li>
            <li><a href="javascript:na.m.clearCookies();">Clear Cookies</a></li>
            <li><a class="noPushState" href="javascript:na.site.code.clearCaches();">Clear Caches</a></li>
            </ul>
        </li>
        <li><a class="noPushState" href="javascript:na.site.code.reboot();">Simple Page Refresh</a></li>
        -->
        <li><a href="javascript:na.m.clearCookies();">Revert to factory settings</a></li>
        <li><a class="noPushState" href="javascript:na.site.code.clearCaches();">Clear Caches</a></li>
        <!--<li><a vividMenu_changeStateCondition="na.site.globals.visitorIsDeveloper===true ? 'selected' : 'normal'" vividMenu_changeStateKey="#admin_VisitorIsDeveloper" href="javascript:na.site.globals.visitorIsDeveloper = !na.site.globals.visitorIsDeveloper;">Developer Mode</a></li>-->
    </ul>
</li>
