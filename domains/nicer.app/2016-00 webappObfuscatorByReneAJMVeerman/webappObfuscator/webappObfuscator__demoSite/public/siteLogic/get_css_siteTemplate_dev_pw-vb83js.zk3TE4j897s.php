<?php
require_once (dirname(__FILE__).'/../../globals.php');
global $dfoSecretHD;
global $siteRootURL;

require_once ($dfoSecretHD.'siteLogic/functions.php');

echo file_get_contents ($siteRootURL.'index.css');
?>
