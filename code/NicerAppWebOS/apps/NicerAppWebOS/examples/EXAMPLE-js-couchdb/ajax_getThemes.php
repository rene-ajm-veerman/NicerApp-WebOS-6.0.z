<?php 
require_once (dirname(__FILE__).'/../../../boot.php');
require_once (dirname(__FILE__).'/functions.php');
$view = json_decode (decode_base64_url($_GET['apps']), true);

global $naIP;
global $naLAN;

// tvGetTableRows() means : themeViewerGetTableRows()
$tableRows = tvGetTableRows();
echo json_encode($tableRows, JSON_PRETTY_PRINT);
?>
