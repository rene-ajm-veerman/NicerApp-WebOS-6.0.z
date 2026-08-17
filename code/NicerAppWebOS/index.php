<?php
require_once(dirname(__FILE__).'/boot.php');

global $naWebOS;
global $useRememberMe;

$siteCurrentlyDown = false;
$debugMe = false;

if ($siteCurrentlyDown) {
    $debugMe = true;
?>
<div id="siteContent_indexFooter1" class="naVividDialogContent_footer__1">
<h1>Site currently down for maintenance.</h1>
<p>
2026-06-10 09:45CEST (Amsterdam.NL AMS):<br/>
I'm working on a major renovation of the base layers of my code.<br/>
Site will be offline for approx a few days to 4 weeks, as this is an AI oversight job that'll require much human editing by yours truly ;)<br/>
Even the entire PHP and JS errorhandling will have to be rewritten to allow for huge logs of such data.
</p>
<p>
After these important changes are applied, NicerApp should be far more stable software. :-)
</p>
<style>
    pre {
        margin : 10px;
        padding : 5px;
        border-radius : 7px;
        background : rgba(0,0,50,0.7);
        text-shadow : 0px 0px 7px rgba(255,255,255,0.9995), 2px 2px 4px rgba(0,0,0,0.8);
    }
</style>
</div>
<?php
    $useRememberMe = true;
    if ($debugMe) {
        echo '<pre style="color:lime;">$naWebOS->about='.json_encode($naWebOS->about, JSON_PRETTY_PRINT).'</pre>';
        echo '<pre style="color:skyblue;">$naWebOS->path='.$naWebOS->path.'</pre>';
        echo '<pre style="color:skyblue;">$naWebOS->codePath='.$naWebOS->codePath.'</pre>';
        echo '<pre style="color:skyblue;">$naWebOS->webPath='.$naWebOS->webPath.'</pre>';
        echo '<pre style="color:skyblue;">$naWebOS->domainPath='.$naWebOS->domainPath.'</pre>';
        echo '<pre style="color:skyblue;">$naWebOS->domainFolder='.$naWebOS->domainFolder.'</pre>';
        echo '<pre style="color:lime;">$_GET='.json_encode($_GET,JSON_PRETTY_PRINT).'</pre>';
        echo '<pre style="color:yellow;">$_POST='.json_encode($_POST,JSON_PRETTY_PRINT).'</pre>';
        exit();
    }
    echo $naWebOS->getSite();

} else {
    echo $naWebOS->getSite();
}
?>
<!--
-->
