<?php
require_once (dirname(__FILE__).'/../../globals.php');
//echo $dfoSecretHD.'siteLogic/siteCode.source.js';
echo file_get_contents($dfoSecretHD.'siteLogic/siteCode.source.js');
?>