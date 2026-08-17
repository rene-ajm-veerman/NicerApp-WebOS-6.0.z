<?php
require_once (dirname(__FILE__).'/../../../boot_stage_001.php');
require_once (dirname(__FILE__).'/saComments-1.0.0.php');

$sn = $_POST['subscription'];
$entry = saComments_acceptNewComment ($_POST);
$idx = saComments_addCommentToSubscription ($sn, $entry);
echo saComments_echoEntry ($sn, $idx, $entry);
?>
