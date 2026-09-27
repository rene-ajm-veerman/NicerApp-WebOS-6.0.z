<?php 
// see .../.htaccess for the latest interpretations of content viewing that should get handled by this file.
//echo '<pre style="color:orange;">'; var_dump ($_GET); echo '</pre>'; die();

if (false) {
    global $naDebugAll;
    $debugMyStartupRoutines = false;
    if ($debugMyStartupRoutines) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }
}
$rootPathNA = realpath(dirname(__FILE__).'/../../..').'/NicerAppWebOS';
require_once ($rootPathNA.'/boot.php');

global $naWebOS;
echo json_encode($naWebOS->getContent(false), JSON_PRETTY_PRINT);
?>
