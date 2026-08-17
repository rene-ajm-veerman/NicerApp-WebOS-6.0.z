<?php global $naWebOS; require_once ($naWebOS->domainPath.'/domainConfig/pageHeader.php');?>
<div class="license" style="width:calc(100% - 32px);">
<?php
    $fn = dirname(__FILE__).'/LICENSE.html';
    $fc = file_get_contents($fn);
    $fileFinal = str_replace('{LASTMODIFIED_LICENSE_html}', date('Ymd-His', filemtime($fn)), $fc);
    echo $fileFinal;
?>
</div>
