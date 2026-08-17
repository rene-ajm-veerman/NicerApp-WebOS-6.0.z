<?php
$rp = dirname(__FILE__).'/../../../../..';
require_once ($rp.'/boot.php');

global $naWebOS;
global $naLAN;
if (!$naLAN) {
    exit ('HTTPS ERROR 403 - Forbidden; *YOU* do not have permission to run this code.');
} else {
    $naWebOS->dbsAdmin->listUsers_debugFormat();
}
?>
