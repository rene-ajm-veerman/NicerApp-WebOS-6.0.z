<?php
require_once (dirname(__FILE__).'/../../../../../boot.php');
global $naWebOS;
global $naLAN;
if (!$naLAN) die('403 Forbidden.');

require_once(dirname(__FILE__).'/app.dialog.siteContent.php');
?>
