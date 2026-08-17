<?php 
require_once (dirname(__FILE__).'/../../globals.php');
require_once (dirname(__FILE__).'/../../../webappObfuscator-1.0.0/functions__basicErrorHandling.php');

error_reporting (E_ALL);
set_error_handler ('woBasicErrorHandler');

if (strpos($_GET['url'], '?')!==false) $queryChar='&'; else $queryChar='?';

$url = $_GET['url'];
if (substr($url,0,1)==='/') $url = substr($url,1);

$hd = $siteRootURL.$url;

//echo 'get_content.php t700.init: $_GET=<pre>'; var_dump ($_GET); echo '</pre>'; 
//echo 'get_content.php t700.init: $hd.$queryChar.\'wo_contentOnly=yes\';=<pre>'; var_dump ($hd.$queryChar.'wo_contentOnly=yes'); echo '</pre>'; 
//echo $hd.$queryChar.'contentOnly=yes';

echo file_get_contents ($hd.$queryChar.'wo_contentOnly=yes');
?>