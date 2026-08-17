<?php
$fncn = '.../NicerAppWebOS/ajax_getConcatenatedFiles.php';

define ('NA_FULL_INIT', false);
require_once (dirname(__FILE__).'/boot.php');
global $naWebOS;

switch ($_GET['indexType']) {
    case 'css' : echo $naWebOS->getConcatenatedLinksContent ($naWebOS->cssFiles); break;
    case 'javascript' : echo $naWebOS->getConcatenatedLinksContent ($naWebOS->javascriptFiles); break;
    default : trigger_error ($fncn.' : invalid $_GET["indexType"] ("'.$_GET['indexType'].'").', E_USER_ERROR); break;
}

?>
