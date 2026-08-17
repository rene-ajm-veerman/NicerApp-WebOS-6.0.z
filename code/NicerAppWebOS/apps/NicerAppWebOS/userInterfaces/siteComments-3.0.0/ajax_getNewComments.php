<?php
require_once (__DIR__.'/../../../../boot.php');
global $naWebOS;
//global $naURL; echo json_encode($naWebOS->view, JSON_PRETTY_PRINT);
$naWebOS->comments->mode = 'ajax-PHP-Apache-nginx';
echo $naWebOS->comments->getHTMLandCSS ($_POST); // TODO : replace with 'GetNewest', which would accept POST data with all the _id and _rev for comments already shown
?>
