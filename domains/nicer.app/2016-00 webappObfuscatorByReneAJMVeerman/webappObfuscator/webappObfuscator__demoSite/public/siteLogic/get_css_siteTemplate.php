<?php
require_once (dirname(__FILE__).'/../../globals.php');
require_once ($dfoSecretHD.'siteLogic/functions.php');

$dfoSourcesCSS = array (
	$siteRootHD.'index.css'
);
header ('Content-Type: text/css');
echo dfoGetCSS ($dfoSourcesCSS, 'true');
?>