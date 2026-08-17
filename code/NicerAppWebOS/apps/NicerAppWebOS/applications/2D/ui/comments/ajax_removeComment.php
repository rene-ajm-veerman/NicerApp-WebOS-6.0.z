<?php
require_once (dirname(__FILE__).'/../../../boot_stage_001.php');
require_once (dirname(__FILE__).'/saComments-1.0.0.php');

saComments_removeCommentFromSubscription ($_GET['subscription'], $_GET['idx']);
?>