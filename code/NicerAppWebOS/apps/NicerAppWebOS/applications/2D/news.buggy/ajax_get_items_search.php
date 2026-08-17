<?php
    require_once (dirname(__FILE__).'/../../../../../boot.php');
    require_once (dirname(__FILE__).'/class.newsApp-3.php');
//var_dump ($_REQUEST); die();

    header ('Content-Type: application/json');
    ob_start("ob_gzhandler");
    ini_set ('memory_limit', '2G');

    set_time_limit (20);

    $newsApp3_factorySettings_fn = dirname(__FILE__).'/config.factorySettings.json';
    $newsApp3_factorySettings = json_decode(file_get_contents($newsApp3_factorySettings_fn), true);

    $newsApp3 = new newsApp3_class($newsApp3_factorySettings);
    echo $newsApp3->searchNewsItems ($_REQUEST['q']);
?>
