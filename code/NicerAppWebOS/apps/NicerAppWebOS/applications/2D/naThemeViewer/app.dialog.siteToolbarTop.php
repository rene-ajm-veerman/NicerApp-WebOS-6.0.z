<?php 
require_once (dirname(__FILE__).'/../../../boot.php');
require_once (dirname(__FILE__).'/functions.php');
$view = json_decode (decode_base64_url($_GET['apps']), true);

global $naIP;
global $naLAN;

// tvGetTableRows() means : themeViewerGetTableRows()
//$tableRows = tvGetTableRows();
?>
<div class="lds-facebook"><!-- thanks for allowing CC0 license usage : https://loading.io/css/ --><div></div><div></div><div></div></div> 

<!--
<link type="text/css" rel="StyleSheet" href="/domainConfig/nicer.app/index.css?c=<?php echo date('Ymd_His',filemtime(realpath(dirname(__FILE__).'/../../../').'/domainConfigs/nicer.app/index.css'))?>">
<link type="text/css" rel="StyleSheet" href="/domainConfig/nicer.app/index.dark.css?c=<?php echo date('Ymd_His',filemtime(realpath(dirname(__FILE__).'/../../../').'/domainConfigs/nicer.app/index.dark.css'))?>">
-->

<script type="text/javascript" src="/NicerAppWebOS/apps/nicer.app/naThemeViewer/na.themeViewer.source.js?c=<?php echo date('Ymd_His',filemtime(dirname(__FILE__).'/na.themeViewer.source.js'));?>"></script>

<div class="tvContainer"></div>
<script type="text/javascript">
    na.m.waitForCondition (
        'na.themeViewer loading',
        function () {
            return na.tv;
        },
        function () {
            na.site.registerEventHandler ({'na.desktop:afterResize' : na.tv.onload});
        },
        100
    );
        
</script>
