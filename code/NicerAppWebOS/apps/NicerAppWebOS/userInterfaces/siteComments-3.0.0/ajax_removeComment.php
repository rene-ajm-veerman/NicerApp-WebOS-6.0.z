<?php
require_once (__DIR__.'/../../../../boot.php');
global $naWebOS;
//$naWebOS->comments->addIndex();
$naWebOS->comments->remove   ($_POST);
?>
