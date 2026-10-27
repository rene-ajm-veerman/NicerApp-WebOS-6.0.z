<?php
require_once(__DIR__ . '/../../../../boot.php');
global $naWebOS;

$naWebOS->comments->exportHistory($_REQUEST);   // werkt met zowel GET als POST
?>
