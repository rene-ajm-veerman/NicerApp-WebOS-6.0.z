<?php
set_time_limit (0);
require_once (dirname(__FILE__).'/../../../boot.php');
require_once (dirname(__FILE__).'/class.newsApp-2.php');

$fncn = 'nicerapp/apps/nicer.app/applications/2D/news.v2.2.0/test_checkIPrange.php';

$newsApp2_factorySettings_fn = dirname(__FILE__).'/config.factorySettings.json';
$newsApp2_factorySettings = json_decode(file_get_contents($newsApp2_factorySettings_fn), true);
$newsApp2 = new newsApp2_class($newsApp2_factorySettings);

var_dump($newsApp2->checkIPrange());
?>
