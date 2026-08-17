<?php
require_once (dirname(__FILE__).'/../../../../boot.php');
require_once (dirname(__FILE__).'/../saAgenda-1.0.0.php');

$userViewed = $_REQUEST['userViewed'];
echo saAgenda__initialize($userViewed);
?>