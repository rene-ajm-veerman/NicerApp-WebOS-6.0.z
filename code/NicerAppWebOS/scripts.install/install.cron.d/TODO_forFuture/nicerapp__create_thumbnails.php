<?php
require_once (dirname(__FILE__).'/boot.php'); // reads in .../NicerAppWebOS/functions.php
$debug = false;
$verbose = false;
$showOutput = true;

set_time_limit(0);

$root = realpath(dirname(__FILE__).'../').'/siteMedia/backgrounds/';
addThumbsToFolder ($root); // from .../NicerAppWebOS/functions.php

$root = realpath(dirname(__FILE__).'../').'/NicerAppWebOS/apps/nicer.app/api.crawler.imageSearch/output/';
addThumbsToFolder ($root); // from .../NicerAppWebOS/functions.php
?>
