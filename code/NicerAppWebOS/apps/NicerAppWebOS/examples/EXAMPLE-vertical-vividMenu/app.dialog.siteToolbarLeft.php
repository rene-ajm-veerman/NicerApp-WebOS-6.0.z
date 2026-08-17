<?php 
require_once (dirname(__FILE__).'/../../../boot.php');
require_once (dirname(__FILE__).'/functions.php');
$view = json_decode (decode_base64_url($_GET['apps']), true);

global $naIP;
global $naLAN;

$debug = false;

/* BUSINESS LOGIC (talking to the operating system and databases) GOES BELOW HERE */
//$tableRows = tvGetTableRows(); // tvGetTableRows() means : themeViewerGetTableRows()

?>
<div class="lds-facebook"><!-- thanks for allowing CC0 license usage : https://loading.io/css/ --><div></div><div></div><div></div></div> 


<div id="verticalMenu" class="vividMenu" theme="dark" type="verticalMenu">
<ul style="display:none;">
<?php 
    global $naWebOS;
    $fn = $naWebOS->codePath.'/domainConfig/'.$naWebOS->domainFolder.'/mainmenu.items.new-background.php';
    if ($debug) {    
        echo '<pre>$fn='; var_dump ($fn); echo '</pre><br/>'.PHP_EOL.PHP_EOL;
        echo '<pre>file_exists($fn)='; var_dump (file_exists($fn)); echo '</pre><br/>'.PHP_EOL.PHP_EOL;
    }
    echo require_return($fn);

    $fn = $naWebOS->codePath.'/domainConfig/'.$naWebOS->domainFolder.'/mainmenu.items.dialogs.php';
    if ($debug) {    
        echo '<pre>$fn='; var_dump ($fn); echo '</pre><br/>'.PHP_EOL.PHP_EOL;
        echo '<pre>file_exists($fn)='; var_dump (file_exists($fn)); echo '</pre><br/>'.PHP_EOL.PHP_EOL;
    }
    echo require_return($fn);
?>
</ul>
</div>


<script type="text/javascript">

    function startVerticalMenu (callback) {
        if (!na.site.settings.menus['#verticalMenu'])
        na.site.settings.menus['#verticalMenu'] = new naVividMenu($('#verticalMenu')[0], true, function() {
            $('#siteToolbarLeft .lds-facebook').fadeOut('normal');
            
            var 
            topLevelItemCount = 0,
            na_js__menuItemWidth = 150;
            
            $('#verticalMenu .vividButton.level1').each (function() { topLevelItemCount++ });
            $('#verticalMenu').css({ width : (topLevelItemCount * na_js__menuItemWidth) + 10})
            if (typeof callback=='function') callback($('#verticalMenu')[0]);
        });
    }

    na.m.waitForCondition('#siteToolbarLeft na.m.HTMLidle()?', na.m.HTMLidle, startVerticalMenu, 100);
</script>
