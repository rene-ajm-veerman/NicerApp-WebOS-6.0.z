<?php
require_once (realpath(dirname(__FILE__).'/../../../../../../').'/NicerAppWebOS/boot.php');
require_once (dirname(__FILE__).'/class.newsApp-3.php');
$fncn = 'nicerapp/apps/nicer.app/applications/2D/news/crontabEntry_manageDatabase.php';

$newsApp3_factorySettings_fn = dirname(__FILE__).'/config.factorySettings.json';
$newsApp3_factorySettings = json_decode(file_get_contents($newsApp3_factorySettings_fn), true);

$newsApp3 = new newsApp3_class($newsApp3_factorySettings);

$fetchIntervalInMinutes = 5;

$fnLock = dirname(__FILE__).'/crontabEntry_manageDatabase.lock.txt';
if (file_exists($fnLock)) unlink ($fnLock);

//sleep (1);
if (file_exists($fnLock)) {
    $logstr = 'Lockfile exists. exiting.';
    echo $logstr;
    //error_log ($logstr);
} else {
    file_put_contents ($fnLock, 'locked');
    //$logstr = $fncn.' : Reading data from disk'.PHP_EOL;
    //echo $logstr;
    //error_log ($logstr);
    //echo '123'; exit();
    //$newsApp3->readFromDisk();
    sleep (1);

    $doo = file_exists($fnLock);
    //while ($doo) {
        startDuration('fetch');
        $logstr = '\033[42;1;4m'.$fncn.' : Fetching news for '.file_get_contents(dirname(__FILE__).'/mainmenu.valueCount.txt').' RSS pages, '.file_get_contents(dirname(__FILE__).'/mainmenu.keyCount.txt').' menu-items.\033[37;49m'.PHP_EOL;
        echo $logstr;
        error_log ($logstr);
        $newCount = $newsApp3->fetch();
        //echo '<pre style="color:blue">'; var_dump ($newsApp3->d); echo '</pre>'.PHP_EOL; exit();
        $doo = file_exists($fnLock);
        //if ($doo) {
            $duration = getDuration('processing');
            $waitTime = round((60*$fetchIntervalInMinutes)-$duration);
            $minutes = floor($waitTime/60);
            $secs = $waitTime - ($minutes * 60);
            $minutesSpent = floor($duration/60);
            $secondsSpent = round($duration - ($minutesSpent * 60));
            
            $logstr = 'Fetching RSS data for '.file_get_contents(dirname(__FILE__).'/mainmenu.valueCount.txt').' RSS pages, '.file_get_contents(dirname(__FILE__).'/mainmenu.keyCount.txt').' menu-items, '.$newCount.' new news-items, took '.$minutesSpent.' minutes, '.$secondsSpent.' seconds.'.PHP_EOL;
            //echo $logstr;
            //error_log ($logstr);
        
            $logstr = 'Writing data to database.'.PHP_EOL;
            //echo $logstr;
            //error_log ($logstr);
            
            startDuration('write');
            //$newsApp3->writeToDisk();
            $newsApp3->uploadToDB();

            //$startkey = [ intval(date('Y')), intval(date('m')), intval(date('d')), intval(date('H')), intval(date('i')), intval(date('s')) ];
            //$endkey = [ intval(date('Y')), intval(date('m')), intval(date('d'))-1, 0, 0, 0 ];
            //$ret = $newsApp3->cdb->getView ('view_001', 'by_date', $startkey, $endkey);
            //echo '<pre style="color:blue">'; var_dump ($ret); echo '</pre>'.PHP_EOL;
            
            //$newsApp3->deleteOldNewsFromRAM();
            //$newsApp3->deleteOldNews();
            
            $duration = getDuration('write');
            $waitTime = round((60*$fetchIntervalInMinutes)-$duration);
            $minutes = floor($waitTime/60);
            $secs = $waitTime - ($minutes * 60);
            $minutesSpent = floor($duration/60);
            $secondsSpent = $duration - ($minutesSpent * 60);

            $logstr = 'Writing took '.$minutesSpent.' minutes, '.$secondsSpent.' seconds.'.PHP_EOL;
            //echo $logstr;
            //error_log ($logstr);

            
            $duration = getDuration('fetch');
            $waitTime = round((60*$fetchIntervalInMinutes)-$duration);
            $minutes = floor($waitTime/60);
            $secs = $waitTime - ($minutes * 60);
            $minutesSpent = floor($duration/60);
            $secondsSpent = round($duration - ($minutesSpent * 60));
            
            //$logstr = 'Sleeping for '.$minutes.' minutes, '.$secs.' seconds, to fetch news about every '.$fetchIntervalInMinutes.' minutes.'.PHP_EOL;
            //echo $logstr;
            //error_log ($logstr);
            echo '\x1b[42;1;4m'.$fncn.' : Done.\x1b[37;49m'.PHP_EOL;
            error_log ('\x1b[42;1;4m'.$fncn.' : Done.\x1b[37;49m'.PHP_EOL);
            
            //sleep ($waitTime);
        //}
    //}
    unlink ($fnLock);
}
?>
