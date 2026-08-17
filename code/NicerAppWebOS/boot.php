<?php
/*
THIS FILE MAY NOT BE CHANGED BY ANYONE EXCEPT The Owner OF
HTTPS://GITHUB.COM/NICERAPP AND HTTPS://NICERAPP.
IF YOU DO NEED THIS FILE CHANGED, EMAIL THE COMPLETE AND CHANGED FILE 
TO THE Current Owner AT THE FOLLOWING EMAIL ADDRESS :
rene.veerman.netherlands@gmail.com

NicerApp WebOS from Nicer Enterprises
*/

    require_once (__DIR__.'/businessLogic/vendor/autoload.php');
    use Jaybizzle\CrawlerDetect\CrawlerDetect;
    define ("SESSION_ERRORS_ID", "NicerApp_WebOS_errors_PHP");
    define ("SEID", SESSION_ERRORS_ID);

    define ("FILE_FORMATS_code", "/^(.*\.html)|(.*\.css)|(.*\.js)|(.*\.php)|(.*\.json)$/");
    define ("FILE_FORMATS_photos", "/^(.*\.png)|(.*\.gif)|(.*\.jpg)|(.*\.jpeg)$/");
    define ("FILE_FORMATS_mp3s", "/^(.*\.mp3)$/");
    define ("FILE_FORMATS_texts", "/^(.*\.txt)$/");
    define ("FILE_FORMATS_html", "/^(.*\.html)$/");
    define ("FILE_FORMATS_photos_texts", "/^(.*\.png)|(.*\.gif)|(.*\.jpg)|(.*\.jpeg)|(.*\.txt)$/");
    define ("FILE_FORMATS_NO_thumbs", '/(?!.*thumbs).*/');
    define ("FILE_FORMATS_NO_siteMeda_3rdParty", '/(?!.*siteMedia)|(?!.*3rd-party).*/');
    global $na_full_init;
    if (!isset($na_full_init)) $na_full_init = true;


    global $naSettings;
    global $rootPath_na;
    try {
        if (array_key_exists('DOCUMENT_ROOT',$_SERVER) && $_SERVER['DOCUMENT_ROOT']!=='') {
            $dcFolderName = basename($_SERVER['DOCUMENT_ROOT']);
            $settingsFilePath = $_SERVER['DOCUMENT_ROOT'].'/domainConfig/settings.json';
        } else {
            $dcFolderName = explode('/',$_SERVER['PWD'])[5];
            $dir = realpath(pathinfo($_SERVER['PATH_TRANSLATED'])['dirname']);
            $bn = array_key_exists('PWD', $_SERVER)?$dcFolderName:explode('/',$dir)[5];
            //echo '<pre>'; var_dump ($_SERVER); var_dump ($dcFolderName); echo '</pre>'; die();

            //ugly hack:
            if ($bn=='NicerAppWebOS') $bn = 'said.by';

            if (
                strpos($dir,'/news')!==false
                || strpos($dir,'/musicPlayer.beatPulse')!==false
            ) {
                $settingsFilePath = $dir.'/../../../../../../../domains/'.$bn.'/domainConfig/settings.json';
            } else {
                $settingsFilePath = $dir.'/../../../domains/'.$bn.'/domainConfig/settings.json';
            }
        }
        //echo '<pre>';var_dump ($_SERVER);echo '</pre>';
        //echo $dcFolderName; die();

        // TODO : Place $settingsFilePath file in
        //  dirname(__FILE__).'/PHPserverConfigs/hostname.DOMAIN_TLD/settings.JSON
        //  using techniques from .../NicerAppWebOS/logic.filePhoenix
        //$settingsFilePath = dirname(__FILE__).'/domainConfigs/'.$dcFolderName.'/settings.json';
        //var_dump ($settingsFilePath); die();
        if (false) {
            echo $settingsFilePath;
            ob_flush();
            ob_end_flush();
            ob_clean();
            ob_start();
        }


        $naSettings = json_decode(file_get_contents($settingsFilePath), true);
        //$rootPath_na = realpath(dirname(__FILE__).'/..');
        $thisServer = $naSettings['phpServers'][0];
        global $naThisServer; $naThisServer = $thisServer;
        //echo '<pre>'; var_dump ($thisServer); die();
        $qs = $thisServer['rootPath'];
        if (substr($qs,strlen($qs)-1,1)!=='/')
            trigger_error ('$thisServer["rootPath"] needs to end with "/" (without the quotes).', E_USER_ERROR);
        $qs = $thisServer['relativePathUnderRootPath'];
        if (substr($qs,strlen($qs)-1,1)!=='/')
            trigger_error ('$thisServer["relativePathUnderRootPath"] needs to end with "/" (without the quotes).', E_USER_ERROR);
        $rootPath_na = $thisServer['rootPath'].$thisServer['relativePathUnderRootPath'];
        $rootPath_na = str_replace('//','/',$rootPath_na);
        if (!is_dir($rootPath_na))
            trigger_error ('$rootPath_na does not exist on this PHP server. ("'.$rootPath_na.'")', E_USER_ERROR);
        //echo $rootPath_na;
        //echo '</pre>';
        //exit();
    } catch (Exception $e) {
        echo '<h1>NicerApp WebOS boot.php INITIALIZATION FATAL ERROR :</h1>';
        echo '<pre>';
        var_dump ($e);
        echo '</pre>';
    };


    //echo '<h1>'.$rootPath_na.'</h1>'.PHP_EOL;
    require_once($rootPath_na.'/NicerAppWebOS/lib_duration.php');
    require_once($rootPath_na.'/NicerAppWebOS/functions.php');
    require_once($rootPath_na.'/NicerAppWebOS/businessLogic/class.NicerAppWebOS.errorHandler.php');
    //echo '<h2>'.$rootPath_na.'</h2>'.PHP_EOL;
    $naDebugAll = true;
    global $naDebugAll;
    $naBypassMainErrorHandler = true;
    global $naBypassMainErrorHandler;
    //$naBypassMainErrorHandler = false;
    if ($naDebugAll) {
            ini_set('display_errors', 1); // 0 == false, 1 == true
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);


        /*
        global $mainErrorLogFilepath; global $mainErrorLogLastWriteFilepath;
        $mainErrorLogFilepath = realpath(dirname(__FILE__)).'/siteLogs/error.'.date('Y-m-d_H.i.s').'.html.log';
        $mainErrorLogLastWriteFilepath = realpath(dirname(__FILE__)).'/siteLogs/error.'.date('Y-m-d_H.i.s').'.lastModified.txt';
        */
    }
    if (!isset($naBypassMainErrorHandler) || $naBypassMainErrorHandler)
      $old_error_handler = set_error_handler ('mainErrorHandler');




    //ini_set ('log_errors', true);
    //var_dump (["test1a"=>true]); echo PHP_EOL; exit();

    require_once($rootPath_na.'/NicerAppWebOS/businessLogic/class.NicerAppWebOS.log.php');

    global $naIsLatestVersion;
    $naIsLatestVersion = (/*$naWebOS->domain*/$_SERVER['SERVER_NAME']=='new.nicer.app');


    $rootPath_na_dbs = $rootPath_na.'/NicerAppWebOS/businessLogic/databases/uDB-2.0.0';
    require_once ($rootPath_na_dbs.'/class.database_API.php');
    require_once ($rootPath_na_dbs.'/uDB-2.5.0.class.byGrok.php');
    //require_once ($rootPath_na_dbs.'/connectors/forFuture_design_coding_debugging_and_usage/class.fileSystemDB-1.0.0.php');
    //require_once ($rootPath_na_dbs.'/connectors/forFuture_design_coding_debugging_and_usage/class.adodb5_1.0.0.php');
    //require_once ($rootPath_na.'/NicerAppWebOS/3rd-party/adodb5/adodb.inc.php');
    require_once ($rootPath_na_dbs.'/plugins/class.couchdb-3.2.2_2.0.0.php');
    // Sag, the business code layer that i use towards the couchdb.apache.org database system.
    require_once($rootPath_na_dbs.'/plugins/CouchDB-specific/sag/src/Sag.php');
    require_once ($rootPath_na_dbs.'/plugins/CouchDB-specific/Sag-support-functions.php');
    //echo '<h1>'.$rootPath_na_dbs.'</h1>'; exit();


    require_once ($rootPath_na.'/NicerAppWebOS/apps/NicerAppWebOS/userInterfaces/siteComments-3.0.0/boot.php');
    //echo 'YES'.$rootPath_na; exit();


    global $naGlobals;
    $naGlobals = [
        'cdb_designDoc_logentries' => '_design/4ed57c36e325319b9e5b0730589fb06ae6b177ee'
    ];
    //var_dump ($naGlobals); echo PHP_EOL; exit();


    //require_once ($rootPath_na.'/NicerAppWebOS/apps/NicerAppWebOS/application-programmer-interfaces/technology/codeTranslationSystems/boot.php');

    //require_once($rootPath_na.'/NicerAppWebOS/3rd-party/vendor/autoload.php'); // loads up a whole bunch of PHP libraries, including birke-rememberme.
    //require_once($rootPath_na.'/NicerAppWebOS/3rd-party/birke/rememberme/src/LoginResult.php'); // small change of my own in the birke-rememberme modern encrypted login system for web 4.0.

    // the main() class
    require_once($rootPath_na.'/NicerAppWebOS/businessLogic/class.NicerAppWebOS.php');
    //echo 'ohhNo'.$rootPath_na; exit();

    global $naIP;
    global $naIPforDB;
    if (
        function_exists('apache_request_headers')
        && array_key_exists('X-Forwarded-For',apache_request_headers())
    ) {
        $naIP = apache_request_headers()['X-Forwarded-For'];
    } elseif (array_key_exists('REMOTE_ADDR', $_SERVER)) {
        $naIP = $_SERVER['REMOTE_ADDR'];
    } else {
        $naIP = 'OS commandline probably';
    }
    $naIPforDB = str_replace('.','_',$naIP);
    $naIPforDB = str_replace(':','-',$naIPforDB);

    //var_dump($naIP); echo PHP_EOL; die();



    if (php_sapi_name() !== 'cli') {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.gc_maxlifetime', 3600 * 24 * 7);
            session_start();


            if (isset($_COOKIE)
                && is_array($_COOKIE)
                && array_key_exists('cdb_loginName', $_COOKIE)
                && is_string($_COOKIE['cdb_loginName'])
                && $_COOKIE['cdb_loginName']!=''
                && array_key_exists('cdb_authSession_cookie', $_COOKIE)
                && is_string($_COOKIE['cdb_authSession_cookie'])
                && $_COOKIE['cdb_authSession_cookie']!=''
            ) {
                $_SESSION['cdb_authSession_cookie'] = $_COOKIE['cdb_authSession_cookie'];
                $_SESSION['cdb_loginName'] = $_COOKIE['cdb_loginName'];
            } else {
                $_SESSION['cdb_authSession_cookie'] = null;
                $_SESSION['cdb_loginName'] = 'Guest';
            }
        } else {
            ini_set('session.gc_maxlifetime', 3600 * 24 * 7);
            session_start();
        }

        if ($_SERVER['SCRIPT_NAME']=='/NicerAppWebOS/index.php') {
        }
    }
    //var_dump (["test1b"=>true]); echo PHP_EOL; exit();

    $filePerms_ownerUser = 'gavan';
    $filePerms_ownerGroup = 'www-data'; 
    $filePerms_perms_readonly = 0640;
    $filePerms_perms_readWrite = 0640;
    $filePerms_perms_publicWriteableExecutable = 0770; // note : these are the file permissions for PUBLICLY ACCESSIBLE FILES only!

    global $filePerms_ownerUser;
    global $filePerms_ownerGroup;
    global $filePerms_perms_publicWriteableExecutable;
    global $filePerms_perms_readonly;
    global $filePerms_perms_readWrite;

    global $na_error_log_filepath_html;
    global $na_error_log_filepath_txt;
    global $naWebOS;
    //var_dump (["test2a"=>true]); echo PHP_EOL; //exit();

    try {
        $naWebOS = new NicerAppWebOS();
        $naWebOS->__construct2();
    } catch (Exception $e) {
        echo '<h1>NicerApp WebOS boot.php INITIALIZATION FATAL ERROR :</h1>';
        echo '<pre>';
        var_dump ($e);
        echo '</pre>';
    };
    //echo '<pre style="color:lime;background:green;">'; var_dump ($_SERVER); echo '</pre>';
    $lanConfigFilepath = $naWebOS->domainPath.'/domainConfig/naLAN.json';
    $lanConfigExampleFilepath = $naWebOS->domainPath.'/domainConfig/naLAN.EXAMPLE.json';
    if (!file_exists($lanConfigFilepath)) {
        $msg = '"'.$lanConfigFilepath.'" does not exist. See "'.$lanConfigExampleFilepath.'" for a template.';
        echo $msg;
        trigger_error ($msg, E_USER_ERROR);
    }
    $lanConfigRaw = file_get_contents($lanConfigFilepath);
    $lanConfig = json_decode($lanConfigRaw, true);
    checkForJSONerrors($lanConfigRaw, $lanConfigFilepath, $lanConfigExampleFilepath);
    //echo '<pre>'; var_dump ($lanConfigFilepath); var_dump ($lanConfigRaw); var_dump ($lanConfig); var_dump (json_last_error_msg()); exit();
    global $naLAN;
    $naLAN = (
        $naIP === '::1'
        || $naIP === '127.0.0.1'
        || in_array($naIP, $lanConfig)
    );
    //$naLAN = false;


    $_SESSION['started'] = time();//microtime(true);
    global $date;
    $now = DateTime::createFromFormat('U', $_SESSION['started']);
    $now->setTimezone(new DateTimeZone(exec('date +%z')));
    //$date = $now->format("Y-m-d_H:i:s.u");
    $dateLog =
        $now->format("Y/m/d-l/H/i-s_")
        .str_replace(
            '+','plus',
            preg_replace('/.*\s/','',date(DATE_RFC2822))
        );
    $date =
        $now->format("Y/m/d-l H:i:s-")
        .' (Amsterdam.NL timezone; CEST)';
            /*
        .str_replace(
            '+','plus',
            preg_replace('/.*\s/','',date(DATE_RFC2822))
        );*/


    if (
        $_SERVER['SCRIPT_NAME']=='/NicerAppWebOS/index.php'
        || $_SERVER['SCRIPT_NAME']=='/NicerAppWebOS/db_init.php'
    ) {
        $_SESSION['startedID'] = cdb_randomString(50);

        $naBot = stripos($_SERVER['HTTP_USER_AGENT'], 'bot')!==false;
        $detect = new CrawlerDetect();

        // Or pass a specific User-Agent
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $naIsBot = $detect->isCrawler($userAgent);
        // Get the name of the bot (very useful)
        $name = $detect->getMatches(); // Returns the matching bot name/pattern

        //echo '<pre>t584'; var_dump($naBot); die();

        if (!$naBot) {
            $na_error_log_filepath_html =
                $naWebOS->path.'/NicerAppWebOS/siteLogs/'
                .$date./*'-'.$appName.*/'-'.$naIP.($naBot?'-BOT':'').'.html';
            $na_error_log_filepath_txt =
                $naWebOS->path.'/NicerAppWebOS/siteLogs/'
                .$date./*'-'.$appName.*/'-'.$naIP.($naBot?'-BOT':'').'.txt';
        } else {
            $na_error_log_filepath_html = null;
            $na_error_log_filepath_txt = null;
        }
        //echo $date.'<br/>'; exit();

        $_SESSION['dbgNum'] = 0;
        $_SESSION['dbgNum2'] = 0;
        $_SESSION['hmTopIdx'] = 0;


        $_SESSION['logsInitialized'] = false;
        $_SESSION[SEID] = [];
        $_SESSION['naWebOS_errors_startup'] = [];

        // outdated?
        $_SESSION['naErrors'] = [];
        $_SESSION['naErrors_startup'] = [];
        $_SESSION['naErrors_js'] = [ 'bootup' => [] ];
    } elseif ($_SERVER['SCRIPT_NAME']=='/NicerAppWebOS/db_init.php') {

        $na_error_log_filepath_html =
            $naWebOS->path.'/siteLogs/'
            .$date.'-'.$naIP.'-db_init.html';
        $na_error_log_filepath_txt =
            $naWebOS->path.'/siteLogs/'
            .$date.'-'.$naIP.'-db_init.txt';

        $_SESSION['dbgNum'] = 0;
        $_SESSION['dbgNum2'] = 0;
        $_SESSION['hmTopIdx'] = 0;


        $_SESSION['logsInitialized'] = false;
        $_SESSION[SEID] = [];
        $_SESSION['naWebOS_errors_startup'] = [];

        // outdated?
        $_SESSION['naErrors'] = [];
        $_SESSION['naErrors_startup'] = [];
        $_SESSION['naErrors_js'] = [ 'bootup' => [] ];
    }

    if ($na_full_init) {
        // MUST be in the following order:
        $naWebOS->initializeDatabases();
        $naWebOS->initializeGlobals();
    }

    $appName = '[UNKNOWN_APP]';
    if (is_object($naWebOS) && is_array($naWebOS->view))
    foreach ($naWebOS->view as $an => $ar) {
        if ($an=='/') $appName = '-';
        else {
            $ap = explode('/', $an);
            $appName = $ap[count($ap)-1];
        }
        break;
    }

    /*
    $dbg = null;
    $dbg = [
        '$_SERVER' => $_SERVER,
        '$_GET' => $_GET,
        '$_POST' => $_POST,
        '$naWebOS->view' => $naWebOS->view
    ];
    */
    //echo '<pre style="color:skyblue;background:navy;">'; var_dump ($dbg); echo '</pre>';
    //$msg = 'NEW REQUEST :<br/>'.hmJSON($dbg,'$dbg');

    global $naIsBot; global $naIsDesktop; global $naIsMobile; global $naBrowserMarketSharePercentage;
    $naIsDesktop = false;
    $naIsMobile = false;
    $naBrowserMarketSharePercentage = -1;
    if (!array_key_exists('HTTP_USER_AGENT',$_SERVER)) $naIsBot = true;
    else {
        /*
        $naIsBot = preg_match('/bot/i', $_SERVER['HTTP_USER_AGENT']) === 1;
        if (!$naIsBot)
            $naIsBot = preg_match('/\+https:\/\//i', $_SERVER['HTTP_USER_AGENT']) === 1;
        if (!$naIsBot)
            $naIsBot = preg_match('/externalhit/i', $_SERVER['HTTP_USER_AGENT']) === 1;
        if (!$naIsBot)
            $naIsBot = preg_match('/crawler/i', $_SERVER['HTTP_USER_AGENT']) === 1;
        if (!$naIsBot)
            $naIsBot = preg_match('/spider/i', $_SERVER['HTTP_USER_AGENT']) === 1;
        if (!$naIsBot)
            $naIsBot = preg_match('/scaninfo\@paloanetworks\.com/i', $_SERVER['HTTP_USER_AGENT']) === 1;
        */

        $fn1 = dirname(__FILE__).'/apps/NicerAppWebOS/applications/2D/logs/userAgents.desktop.2023-12-02.json';
        $json1 = json_decode(file_get_contents($fn1), true);
        $fn2 = dirname(__FILE__).'/apps/NicerAppWebOS/applications/2D/logs/userAgents.mobile.2023-12-02.json';
        $json2 = json_decode(file_get_contents($fn2), true);

        //$naIsBot = true;
        foreach ($json1 as $idx => $jrec) {
            if ($jrec['ua']===$_SERVER['HTTP_USER_AGENT']) {
                //$naIsBot = false;
                $naIsDesktop = true;
                $naBrowserMarketSharePercentage = $jrec['pct'];
            }
        }
        foreach ($json2 as $idx => $jrec) {
            if ($jrec['ua']===$_SERVER['HTTP_USER_AGENT']) {
                //$naIsBot = false;
                $naIsMobile = true;
                $naBrowserMarketSharePercentage = $jrec['pct'];
            }
        }
    }
    //  echo '<p style="color:purple">'; var_dump($naIsBot); echo '</p>';

    global $naURL;
    $naURL = 'https://'.$_SERVER['HTTP_HOST'].(array_key_exists('REDIRECT_URL',$_SERVER)?$_SERVER['REDIRECT_URL']:'/');

    global $naBrowserInfo;
    $naBrowserInfo = [
        'HTTP_HOST' => $_SERVER['HTTP_HOST'],
        'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
        'SERVER_SIGNATURE' => $_SERVER['SERVER_SIGNATURE'],
        'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'],
        'SERVER_NAME' => $_SERVER['SERVER_NAME'],
        'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],
        'SERVER_PORT' => $_SERVER['SERVER_PORT'],
        'SERVER_PROTOCOL' => $_SERVER['SERVER_PROTOCOL'],
        'GATEWAY_INTERFACE' => $_SERVER['GATEWAY_INTERFACE'],
        'naIsBot' => $naIsBot,
        'naIsDesktop' => $naIsDesktop,
        'naBrowserMarketSharePercentage-2023-12' => $naBrowserMarketSharePercentage,
        '_COOKIE' => $_COOKIE
    ];
    if (array_key_exists('HTTP_SEC_CH_UA_PLATFORM', $_SERVER)) $naBrowserInfo['HTTP_SEC_CH_UA_PLATFORM'] = $_SERVER['HTTP_SEC_CH_UA_PLATFORM'];
    if (array_key_exists('HTTP_SEC_CH_UA', $_SERVER)) $naBrowserInfo['HTTP_SEC_CH_UA'] = $_SERVER['HTTP_SEC_CH_UA'];
    if (array_key_exists('HTTP_SEC_CH_UA_MOBILE', $_SERVER)) $naBrowserInfo['HTTP_SEC_CH_UA_MOBILE'] = $_SERVER['HTTP_SEC_CH_UA_MOBILE'];



    $naSiteUnderConstruction = false;
    if (!$naLAN && $naSiteUnderConstruction) {
        echo '<h1>NicerApp WebOS is currently down for maintenance and/or upgrades; sorry!</h1>';
        exit();
    };

    if ($naLAN && array_key_exists('logsInitialized', $_SESSION) && !$_SESSION['logsInitialized']) {
        /*
        $html = $naWebOS->getSite();
            //'<html><head>'
            //.PHP_EOL.$naWebOS->getLinks($naWebOS->cssFiles)
            //.PHP_EOL.$naWebOS->getLinks($naWebOS->javascriptFiles).PHP_EOL
            //.'</head><body style="overflow:visible"><div id="siteBackground"></div>';*/
        //$html = '<script type="text/javascript" src="/NicerAppWebOS/logic.business-5.8.z/debug-1.0.0.source.js?c='.date('Ymd_His',filemtime(dirname(__FILE__).'/logic.business-5.8.z/debug-1.0.0.source.js')).'"></script>';
        //file_put_contents ($na_error_log_filepath_html, $html, FILE_APPEND);

        $_SESSION['logsInitialized'] = true;
    }


    global $naSettings;
    $fnSettingsMusicPlayer2D = realpath(dirname(__FILE__)).'/apps/NicerAppWebOS/applications/2D/musicPlayer.beatPulse/settings.json';
    $folderAppMusicPlayer2D = '.../NicerWebAppOS/apps/NicerWebAppOS/applications/2D/musicPlayer.beatPulse';
    $naSettings = [ 'apps' => json_decode(file_get_contents($fnSettingsMusicPlayer2D),true) ];
    $rcl = 'respectLatestDutchCopyrightLaws'; // not all that important - see screenshot file in musicPlayer code folder.
    global $naSettings_app2D_musicPlayer_respectDutchCopyright;
    $naSettings_app2D_musicPlayer_respectDutchCopyright = $naSettings['apps'][$folderAppMusicPlayer2D]['defaults'][$rcl];
    global $naSettings_app2D_musicPlayer;
    $naSettings_app2D_musicPlayer = $naSettings['apps'][$folderAppMusicPlayer2D];
    //echo '<pre>'; var_dump ($app2DmusicPlayer_respectDutchCopyright); echo '</pre>'.PHP_EOL.PHP_EOL;
    //echo '<pre>'; var_dump ($naSettings); echo '</pre>'.PHP_EOL.PHP_EOL;
    //exit();



    global $phpScript_startupTime;
    global $naIP;
    global $naVersionNumber;
    $time = microtime(true) - $phpScript_startupTime;
    //var_dump (dirname(__FILE__).'/errors.css');
    //date_default_timezone_set('UTC');
    $dtz = new DateTime('now');//new DateTimeZone(date_default_timezone_get());
    $dtz_offset = $dtz->getOffset();
    //$unixTimeStamp = time();//date(DATE_ATOM);//date(DATE_RFC2822);//date('Y-m-d H:i:sa');
    $timestamp = date(DATE_RFC2822);

        $headers_list = [];
        if (php_sapi_name() !== 'cli')
        foreach (getallheaders() as $name => $value) {
            array_push($headers_list, array("name" => $name, "value" => $value));
        }
    /*
    //if (!$naIsBot) {
    if (false) { // TODO : Get this done via /view/logs on an as-needed basis instead!

        $fn = dirname(__FILE__).'/domainConfigs/'.$naWebOS->domainFolder.'/apiKey.whatismybrowser.txt';
        $api_key = file_get_contents($fn);
        $headers = [
            'X-API-KEY: '.$api_key,
        ];

        # -- Create the JSON Structure to POST
        $post_data = array(
            "headers" => $headers_list,
        );

        # -- Create a CURL handle containing your API Key and the data to send
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, "https://api.whatismybrowser.com/api/v3/detect");
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        # -- Make the request
        $result = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        curl_close($ch);

        # -- Decode the api response as json
        $result_json = json_decode($result);

        # -- Echo the Simple Software String
        if (is_object($result_json) && property_exists($result_json, 'detection')) $bd = $result_json->detection->simple_software_string; else $bd = '';
        //echo '<pre>'; var_dump ($result_json); echo '</pre>';


        $db = $naWebOS->dbs->findConnection('couchdb');
        $cdb = $db->cdb;

        $debug = false;
        $dbName = $db->dataSetName('ip_info');
        try {
            $cdb->setDatabase($dbName);
        } catch (Throwable $e) {
        } catch (Exception $e) {
        }

        // fetch dataRecord
        $findCommand = [
            'selector' => [
                'ip' => $naIP
            ],
            'fields' => ['_id'],
            'use_index' => $naWebOS->globals['cdbDesignDocs']['logentries_pageLoad']
        ];
        //echo '<pre style="padding:8px;border-radius:10px;background:rgba(255,255,255,0.5);color:green;">'; var_dump ($findCommand); echo '</pre>'; exit();
        try {
            $call = $cdb->find ($findCommand);
            //echo '<pre>'; var_dump ($call); echo '</pre>'; exit();
        } catch (Exception $e) {
            //$msg = ' FAILED (boot.php) while trying to find in \''.$dbName.'\' : '.$e->getMessage();
            //trigger_error ($msg, E_USER_ERROR);
            //echo $msg;
            //return false;
            //die();
        }

        $ipInfo = null;
        if (isset($call) && property_exists($call, 'body'))
        foreach ($call->body->docs as $docIdx => $doc) {
            $call2 = $cdb->get($doc->_id);
            //echo '<pre>'; var_dump($call2); echo '</pre>';
            if (property_exists($call2, 'body') && property_exists($call2->body, 'ipInfo'))
                $ipInfo = $call2->body->ipInfo;
            break;
        }

        if (is_null($ipInfo)) {
            $fn = dirname(__FILE__).'/apps/NicerAppWebOS/applications/2D/analytics/apiKey.ipinfo.io.txt';
            if (file_exists($fn)) {
                $apiKey = trim(file_get_contents($fn));
                $xec = 'curl -H "X-Forwarded-For: nicer.app" ipinfo.io/'.$naIP.'?token='.$apiKey;
                exec ($xec, $output, $result);
                $ipInfo = json_decode(join('',$output), true);

                $rec = [
                    'ip' => $naIP,
                    'ipInfo' => $ipInfo
                ];
                if (isset($call)) $cdb->post($rec);
            } else {
                $rec = [
                    'ip' => $naIP,
                    'ipInfo' => ['UNKNOWN' => 'UNKNOWNS']
                ];
                if (isset($call)) $cdb->post($rec);
            }
        }
    };
    */

    if ($naThisServer['log']['dbActivity']) {
        $now = DateTime::createFromFormat('U.u', microtime(true));
        while (!is_bool($now)) {//for testing solution
            $t = microtime(true);
            $now = DateTime::createFromFormat('U.u', $t);
        }
        if (is_bool($now)) {//the problem
            $now = DateTime::createFromFormat('U', $t);//the solution
        }    $s3 = (int)$now->format("u"); // milliseconds after 's2' listed below here.
        $err = [
            'type' => 'New request',
            's1' => (
                session_status() === PHP_SESSION_NONE
                ? microtime(true)
                : $_SESSION['started']
            ),
            's2' => time(),//microtime(true),
            's3' => $s3,
            'i' => (
                session_status() === PHP_SESSION_NONE
                ? false
                : $_SESSION['startedID']
            ),
            'isIndex' => $_SERVER['SCRIPT_NAME']==='/NicerAppWebOS/index.php',
            'isBot' => $naIsBot,
            'isLAN' => $naLAN,
            'isDesktop' => $naIsDesktop,
            'isMobile' => $naIsMobile,
            'headers' => $headers_list,
            //'headersResult' => $result_json,
            //'bd' => $bd,
            //'bdDetails' => json_decode(json_encode($result_json), true),
            //'ipInfo' => $ipInfo,
            //'browserMarketSharePercentage' => $naBrowserMarketSharePercentage,
            'to' => $dtz_offset,
            'ts' => $timestamp,
            'ip' => $naIP,
            'sid' => session_id(),
            'nav' => $naVersionNumber,
            'request' => $dbg
        ];
        global $naLog;
        $naLog->add ( [ $err ] );
        //trigger_error ($msg, E_USER_NOTICE);
        //echo '<pre>'; var_dump ($_SERVER); die();

        // TODO : re-enable ini_set('error_log') at some point perhaps?
        //ini_set ('error_log', $na_error_log_filepath_txt);
    }
    // at the *bottom* of this file (that's for good reasons),
    // you will find : require_once(dirname(__FILE__).'/apps/nicer.app/api.paymentSystems/boot.php');
    
    ini_set('memory_limit','5000M'); // hacker deterrence by keeping it at 5G (of 64G).
    set_time_limit(70); // 70 seconds; also for hacker deterrence. can be overridden by individual ajax scripts though!

    //echo '<pre>'; var_dump ($_SERVER); exit();
    

    // overrides by the site operator go here :
    // NOTE : YOU WILL LIKELY HAVE TO CHANGE global $filePerms_ownerUser, defined in this file.
    $fn = dirname(__FILE__).'/apps/siteOperator_boot.php';
    if (file_exists($fn)) require_once ($fn);

    /*
    require_once(dirname(__FILE__).'/apps/NicerAppWebOS/application-programmer-interfaces/technology/authentication/paymentSystems/boot.php');
    
    
    // oAuth like login systems and others like that :
    // everything excluding the NicerApp and NicerApp->couchdb login systems, basically.
    require_once(dirname(__FILE__).'/apps/NicerAppWebOS/application-programmer-interfaces/technology/authentication/loginSystems/boot.php');
    */
?>
