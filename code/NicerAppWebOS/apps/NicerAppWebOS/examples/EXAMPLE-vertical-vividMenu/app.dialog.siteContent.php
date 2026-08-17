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
<h1>Vertical menu demo on nicer.app</h1>
<script type="text/javascript">
    $(document).ready(function() {
        setTimeout (function() {
            na.d.s.visibleDivs.push ('#siteToolbarLeft');
            na.d.s.visibleDivs.push ('#siteToolbarRight');
            na.desktop.resize();
        }, 1000);
    });
</script>
