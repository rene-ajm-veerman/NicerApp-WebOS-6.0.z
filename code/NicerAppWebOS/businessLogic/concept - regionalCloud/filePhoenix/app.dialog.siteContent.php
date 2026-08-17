<?php
    set_time_limit(2*3600); // 2 hours max execution time for this script. takes up little CPU and network activity.
    ini_set('memory_limit', '2GB');
    require_once(dirname(__FILE__).'/../boot.php');
    require_once (dirname(__FILE__).'/cronjobs_calculate_traceroute_averages.php');
?>
