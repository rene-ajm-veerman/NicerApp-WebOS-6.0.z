<?php
global $naWebOS;
global $naLAN;
$debugMe = false;
$openToPublic = true;

if (!$openToPublic && !$naLAN) {
    echo '<h1>NicerApp WebOS logs</h1>';
    echo 'This data is unavailable outside nicer.app\'s LAN, sorry.';
    exit();
}
?>

<link rel="StyleSheet" href="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/logs/naLog.css?m=<?=filemtime(dirname(__FILE__).'/naLog.css')?>"/>
<script type="text/javascript" src="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/logs/naLog.source.js?m=<?=filemtime(dirname(__FILE__).'/naLog.source.js')?>"></script>
<script type="text/javascript">
    var view = <?=json_encode($naWebOS->view);?>;
    na.m.waitForCondition('na initialized?', na.m.HTMLidle, function () { naLog.reload(); }, 500);
</script>
