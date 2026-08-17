<?php 
require_once (dirname(__FILE__).'/functions.php');

class wallpaperScraper_plugin_bingComImages implements wallpaperScraper_plugin {
    private $rootPath;
    
    private $db = [];
    
    public function __construct() {
        $this->rootPath = dirname(__FILE__);
    }
    
    public function readDB() {
        $this->db = [
            'pluginSpecificSettings' => safeLoadJSONfile ($this->rootPath.'/settings/plugin.bing.com.images.settings.json'),
            'crawlHistory' => safeLoadJSONfile ($this->rootPath.'/settings/crawlHistory.bing.com.images.json', false),
            'searchResults' => safeLoadJSONfile ($this->rootPath.'/settings/searchResults.bing.com.images.json', false)
        ];
    }
    
    public function saveDB() {
        file_put_contents ($this->rootPath.'/settings/crawlHistory.bing.com.images.json', json_encode($this->db['crawlHistory'], JSON_PRETTY_PRINT));
    }
    
    public function doDailyDownload() {
        $fncn = 'wallpaperScraper_plugin_bingComImages->getContent()';
        $debug = false;
        
        global $filePerms_ownerUser;
        global $filePerms_ownerGroup;
        global $filePerms_perms;
        
        $pss = $this->db['pluginSpecificSettings'];
        $jrp = $pss['jsonRequestParameters'];
        $ch = &$pss['crawlHistory'];
        
        $sr = &$this->db['searchResults'];
        
        $outputFiles = getFilePathList ($this->rootPath.'/output', true, FILE_FORMATS_photos, null, array('file'));
        $outputFiles_basenames = [];
        foreach ($outputFiles as $idx => $filepath) {
            $outputFiles_basenames[$idx] = basename($filepath);
        }
        
        $jrp['searchtermsByPercentage'] = [];
        $searchTermsTotalValue = array_sum($jrp['searchterms']);
        foreach ($jrp['searchterms'] as $k => $v) {
            $jrp['searchtermsByPercentage'][$k] = ($v * 100) / $searchTermsTotalValue;
        }
        $array = $jrp['searchtermsByPercentage'];
        shuffle($array); // randomize
        uasort($array, function($a, $b){
            if($a === $b) {
                return rand(0, 1);
            }
            return $a < $b;
        });        
        $temp = array_filter($array, function ($v) {
            return $v > 0;
        });
        $numSearchterms = count($temp);
        
        
        $jrp['mustIncludeByPercentage'] = [];
        $mustIncludeTotalValue = array_sum($jrp['mustInclude']);
        foreach ($jrp['mustInclude'] as $k => $v) {
            $jrp['mustIncludeByPercentage'][$k] = ($v * 100) / $mustIncludeTotalValue;
        }
        $array = $jrp['mustIncludeByPercentage'];
        shuffle($array); // randomize
        uasort($array, function($a, $b){
            if($a === $b) {
                return rand(0, 1);
            }
            return $a < $b;
        });        
        $temp = array_filter($array, function ($v) {
            return $v > 0;
        });
        $numMustInclude = count($temp);
        
        
        $nextSearchtermIdx = 0;
        $nextMustIncludeIdx = 0;
        $totalNumberOfTransactions = 0;
        
        // search history to see how far we've already downloaded results for these particular search parameters 
        $page = 0;
        $today = date('Y/m/d');
        $historyKey = false;
        $fail = false;
            
        while (!$fail && $nextSearchtermIdx <= $numSearchterms && $totalNumberOfTransactions < $jrp['restrictions']['maxTransactionsPerDay']) {

            // use weighted but also randomized search criteria
            $array = $jrp['searchtermsByPercentage'];
            shuffle($array); // randomize
            uasort($array, function($a, $b){
                if($a === $b) {
                    return rand(0, 1);
                }
                return $a < $b;
            });        
            $temp = array_filter($array, function ($v) {
                return $v > 0;
            });
            $numSearchterms = count($temp);
            
            
            $array = $jrp['mustIncludeByPercentage'];
            shuffle($array); // randomize
            uasort($array, function($a, $b){
                if($a === $b) {
                    return rand(0, 1);
                }
                return $a < $b;
            });        
            $temp = array_filter($array, function ($v) {
                return $v > 0;
            });
            $numMustInclude = count($temp);
        
            $a1 = $jrp['searchtermsByPercentage'];
            $a2 = &$ch;
            //return '<pre>'.json_encode($a, JSON_PRETTY_PRINT).'</pre>';
            $nextSearchterm = array_keys($a1)[$nextSearchtermIdx];
            
            $start = ($page + 1) * $jrp['restrictions']['numberOfResultsPerTransaction'];

            /*$url = 
                'https://customsearch.googleapis.com/customsearch/v1?rights='.$jrp['rights'].'&imgSize='.$jrp['imgSize'].'&searchType='.$jrp['searchType'].'&imgType='.$jrp['imgType'].'&filter='.$jrp['filter'].'&start='.$start.'&num='.$jrp['num'].'&q='.$nextSearchterm.'&dateRestrict='.$jrp['dateRestrict'].'&exactTerms=4K&cx='.$jrp['cx'].'&key='.$jrp['key'].'&date='.$today;*/
            $url =
                'https://api.bing.microsoft.com/v7.0/images/search?q='.$nextSearchterm.'&freshness=Day&imageType=Photo&license=Share';
                
            $historyKey = is_array($a2) && count($a2) > 0 && array_search ($url, $a2);
            if ($historyKey !== false) {
                $page++;
                if ($page > $jrp['restrictions']['maxResultsPerSearchterm'] / $jrp['restrictions']['numberOfResultsPerTransaction']) {
                    $nextSearchtermIdx++;
                    $page = 0;
                }
            } else {
                // fetch results for previously unsearched combination of search criteria
                $logstr = $fncn.' : Now crawling '.$url.PHP_EOL;
                echo $logstr; error_log ($logstr);
                
                // fetch results via curl (curl is the data transport mechanism)
                $curlCh = curl_init();
                curl_setopt($curlCh, CURLOPT_HTTPHEADER, array(
                    'Ocp-Apim-Subscription-Key: '.$jrp['key2']
                ));                
                curl_setopt($curlCh, CURLOPT_URL, $url);
                curl_setopt($curlCh, CURLOPT_RETURNTRANSFER, 1);
                $jsonText = curl_exec($curlCh);
                curl_close($curlCh); 
                
                $logstr = $fncn.' : $jsonText='.$jsonText.PHP_EOL;
                echo $logstr; error_log ($logstr);
                exit();
                
                // data integrity check
                $imageSearchCommandResults_asJSON = json_decode ($jsonText, true);
                if (json_last_error()!==JSON_ERROR_NONE) {
                    //trigger_error ($fncn.' : Could not decode JSON result from URL='.$url.', ERROR='.json_last_error_msg(), E_USER_ERROR);
                    $logstr = $fncn.' : WARNING : Could not decode JSON result from URL='.$url.', ERROR='.json_last_error_msg().', JSON='.$jsonText.PHP_EOL;
                    echo $logstr; error_log ($logstr);
                } else {
                    $ch[] = $url;
                
                    // crawlHistory data and search result data even more, grow *huge* over time, so it's kept in a couchdb.apache.org installation.
                    global $naWebOS;
                    $naWebOS = new NicerAppWebOS();
                    $naWebOS->init();
                    $cdbDomain = str_replace('.','_',$naWebOS->domainFolder);

                    $couchdbConfigFilepath = realpath(dirname(__FILE__).'/../../..').'/domainConfigs/'.$naWebOS->domainFolder.'/couchdb.json';
                    if (!file_exists($couchdbConfigFilepath)) trigger_error ($fncn.' : "'.$couchdbConfigFilepath.'" does not exist.', E_USER_ERROR);
                    $cdbConfig = json_decode(file_get_contents($couchdbConfigFilepath), true);
                    if (json_last_error()!==0) trigger_error ($fncn.' : JSON decoding of file "'.$couchdbConfigFilepath.'" resulted in error '.json_last_error_msg(), E_USER_ERROR);

                    if ($debug) { echo 'info : '.__FILE__.' : $debug = true.<br/>'.PHP_EOL;  }


                    $cdb = new Sag($cdbConfig['domain'], $cdbConfig['port']);
                    $cdb->setHTTPAdapter($cdbConfig['httpAdapter']);
                    $cdb->useSSL($cdbConfig['useSSL']);
                    //return '<pre>'.json_encode($_COOKIE, JSON_PRETTY_PRINT).'</pre>';
                    /*
                    if (!array_key_exists('cdb_authSession_cookie', $_COOKIE)) {
                        echo 'Browser cookies do not contain database connection settings.';
                        exit();
                    }*/

                    if (session_status() === PHP_SESSION_NONE) {
                        ini_set('session.gc_maxlifetime', 3600 * 24 * 7);
                        session_start();
                        $_SESSION['cdb_authSession_cookie'] = $_COOKIE['cdb_authSession_cookie'];
                    };

                    //login ($cdb); can't use this from the commandline.
                    $cdb->login ($cdbConfig['adminUsername'], $cdbConfig['adminPassword']);

                    //$dbName = $cdbDomain.'___themeData__user___'.strtolower($username);
                    $dbName = strtolower($cdbDomain.'___app__wallpaperScraper_plugin_bingComImages');
                    try {
                        $cdb->setDatabase($dbName, false);
                    } catch (Exception $e) {
                        echo 'status : Failed : '.$e->getMessage();
                        //you should never leak library/sub-system specific error messages to the browser, they tends to be used in hacking efforts against you and/or your clients.
                        echo 'Could not connect to database. Please login again.';
                        exit();
                    }
                    
                    $rec = [
                        "url" => $url,
                        "downloadedOn" => $today,
                        "results" => $imageSearchCommandResults_asJSON
                    ];
                    
                    try {
                        $call3 = $cdb->post($rec);
                    } catch (Exception $e) {
                        if ($debug) {
                            echo 'status : Failed : could not update record in database ('.$dbName.').<br/>'.PHP_EOL;
                            echo '$rec = <pre style="color:blue">'.PHP_EOL; var_dump ($rec); echo PHP_EOL.'</pre>'.PHP_EOL;
                            echo '$call3 = <pre style="color:red">'.PHP_EOL; var_dump ($call3); echo PHP_EOL.'</pre>'.PHP_EOL;
                            echo '$e = <pre style="color:red">'.PHP_EOL; var_dump ($e); echo PHP_EOL.'</pre>'.PHP_EOL; 
                            exit();
                        
                        } else {
                            $logstr = $fncn.' : CouchDB record add status : Failed.';
                            echo $logstr; error_log ($logstr);
                            exit();
                        }
                    }
                    if ($debug) { echo '<pre>$call3='; var_dump ($call3); var_dump($_POST); var_dump(json_last_error()); echo '</pre>'.PHP_EOL.PHP_EOL; }
                            
                    if ($call3->headers->_HTTP->status=='201') {
                        $logstr = $fncn.' : CouchDB record add status : Success.';
                        echo $logstr; error_log ($logstr);
                    } else {
                        $logstr = $fncn.' : CouchDB record add status : Failed.';
                        echo $logstr; error_log ($logstr);
                    }
                        
                    // now process the individual search results (download the high res files, store them, thumbnail them)
                    $sr = &$imageSearchCommandResults_asJSON;
                    $fail = false;
                    
                    $logstr = $fncn.' : $sr='.json_encode($sr, JSON_PRETTY_PRINT).PHP_EOL;
                    echo $logstr; error_log ($logstr);
                    exit();
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    if (!array_key_exists('items', $sr)) {
                        $logstr = $fncn.' : ERROR : results do not contain an "items" sub-array. Can not use these results. ABORTING.'.PHP_EOL;
                        echo $logstr; error_log ($logstr);

                        $fail = true;
                    }
                    if (!is_array($sr['items'])) {
                        $logstr = $fncn.' : ERROR : results "items" sub-array is not an array itself. Can not use these results. ABORTING.'.PHP_EOL;
                        echo $logstr; error_log ($logstr);

                        $fail = true;
                    }
                    
                    if ($fail) {
                        $logstr = $fncn.' : $sr='.json_encode($sr, JSON_PRETTY_PRINT).PHP_EOL;
                        echo $logstr; error_log ($logstr);
                    } else
                    foreach ($sr['items'] as $itemIdx => $it) {
                        $fileURL = $it['link'];
                        
                        $logstr = $fncn.' : Downloading '.json_encode ($it, JSON_PRETTY_PRINT).PHP_EOL;
                        echo $logstr; error_log ($logstr);
                        
                        // download $fileURL into $dir, IF the file as indicated by it's basename() hasn't been downloaded before.
                        $dir = $this->rootPath.'/output/'.$today.'/';
                        createDirectoryStructure ($dir, $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms);
                        $save_file_loc = $dir.basename(strtok($fileURL, '?'));
                        $haveOutput = array_search (basename($fileURL), $outputFiles_basenames) !== false;
                        $logstr = $fncn.' : $haveOutput='.($haveOutput===true?'true':'false');
                        echo $logstr; error_log ($logstr);
                        if (!$haveOutput) {
                            $fp = fopen($save_file_loc, 'wb');
                            $curlCh = curl_init($fileURL);
                            curl_setopt($curlCh, CURLOPT_FILE, $fp);
                            curl_setopt($curlCh, CURLOPT_HEADER, 0);
                            curl_exec($curlCh);
                            fclose($fp);
                            if (curl_errno($curlCh)!==0) {
                                //trigger_error ($fncn.' : Could not download "'.$fileURL.'"', E_USER_WARNING);
                                $logstr = $fncn.' : NOTICE : Could not download "'.$fileURL.'" : ERROR='.$curl_error($curlCh).PHP_EOL;
                                echo $logstr; error_log ($logstr);
                                
                                curl_close($curlCh);
                                unlink ($save_file_loc);
                            } else {
                                curl_close($curlCh);
                                if (array_key_exists('snippet',$it)) file_put_contents ($save_file_loc.'.description.txt', $it['snippet']);
                                
                                $logstr = 'Download saved into "'.$save_file_loc.'"'.PHP_EOL;
                                echo $logstr; error_log ($logstr);
                                
                                $logstr = 'Description file = "'.$save_file_loc.'.description.txt'.PHP_EOL;
                                echo $logstr; error_log ($logstr);

                                $outputFiles[] = $save_file_loc;
                                $outputFiles_basenames[] = basename($url);
                                
                                // create thumbnail use linux imagemagick 'convert' commandline tool.
                                createDirectoryStructure ($dir.'thumbs', $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms);
                                $thumb_file_loc = $dir.'thumbs/'.basename($fileURL);
                                $xec = 'convert "'.$save_file_loc.'" -resize 200 "'.$thumb_file_loc.'"';
                                $output = -1;
                                $result = [];
                                exec ($xec, $output, $result);
                                if ($result!==0) {
                                    //trigger_error ($fncn.' : Could not create thumbnail "'.$thumb_file_loc.'" : $result='.$result.', $output='.json_encode($output), E_USER_WARNING);
                                    $logstr = $fncn.' : WARNING : Could not create thumbnail "'.$thumb_file_loc.'" : $xec='.$xec.', $result='.$result.', $output='.json_encode($output, JSON_PRETTY_PRINT).PHP_EOL;
                                    echo $logstr; error_log ($logstr);
                                } else {
                                    $logstr = $fncn.' : Thumbnail created in "'.$thumb_file_loc.'"'.PHP_EOL;
                                    echo $logstr; error_log ($logstr);
                                }
                                $logstr = PHP_EOL;
                                echo $logstr; error_log ($logstr);
                            }
                        }
                    }
                    
                    $totalNumberOfTransactions++;
                    usleep (1000 / $jrp['restrictions']['maxTransactionsPerSecond']);
                    //exit(); // for testing phase only
                } // search results json decoding success check
            } // download action loop
        } // main while loop
        $this->saveDB();
    }
    
    public function addFileToFailedDownloadsList($it, $save_file_loc, $thumb_file_loc, $descriptionFilepath) {
        $fncn = 'wallpaperScraper_plugin_bingComImages->addFileToFailedDownloadsList()';
        $debug = false;
        
        $rec = [
            'it' => $it,
            'save_file_loc' => $save_file_loc,
            'thumb_file_loc' => $thumb_file_loc,
            'descriptionFilepath' => $descriptionFilepath
        ];
        
        // save $rec in couchdb
        global $naWebOS;
        $naWebOS = new NicerAppWebOS();
        $naWebOS->init();
        $cdbDomain = str_replace('.','_',$naWebOS->domainFolder);

        $couchdbConfigFilepath = realpath(dirname(__FILE__).'/../../..').'/domainConfigs/'.$naWebOS->domainFolder.'/couchdb.json';
        if (!file_exists($couchdbConfigFilepath)) trigger_error ($fncn.' : "'.$couchdbConfigFilepath.'" does not exist.', E_USER_ERROR);
        $cdbConfig = json_decode(file_get_contents($couchdbConfigFilepath), true);
        if (json_last_error()!==0) trigger_error ($fncn.' : JSON decoding of file "'.$couchdbConfigFilepath.'" resulted in error '.json_last_error_msg(), E_USER_ERROR);

        if ($debug) { echo 'info : '.__FILE__.' : $debug = true.<br/>'.PHP_EOL;  }


        $cdb = new Sag($cdbConfig['domain'], $cdbConfig['port']);
        $cdb->setHTTPAdapter($cdbConfig['httpAdapter']);
        $cdb->useSSL($cdbConfig['useSSL']);
        //return '<pre>'.json_encode($_COOKIE, JSON_PRETTY_PRINT).'</pre>';
        /*
        if (!array_key_exists('cdb_authSession_cookie', $_COOKIE)) {
            echo 'Browser cookies do not contain database connection settings.';
            exit();
        }*/

        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.gc_maxlifetime', 3600 * 24 * 7);
            session_start();
            $_SESSION['cdb_authSession_cookie'] = $_COOKIE['cdb_authSession_cookie'];
        };

        //login ($cdb); can't use this from the commandline.
        $cdb->login ($cdbConfig['adminUsername'], $cdbConfig['adminPassword']);

        //$dbName = $cdbDomain.'___themeData__user___'.strtolower($username);
        $dbName = strtolower($cdbDomain.'___app__wallpaperScraper_plugin_imagesGoogleCom_failedDownloads');
        try {
            $cdb->setDatabase($dbName, true);
        } catch (Exception $e) {
            echo 'status : Failed : '.$e->getMessage();
            //you should never leak library/sub-system specific error messages to the browser, they tends to be used in hacking efforts against you and/or your clients.
            echo 'Could not connect to database. Please login again.';
            exit();
        }
        
        try {
            $call3 = $cdb->post($rec);
        } catch (Exception $e) {
            if ($debug) {
                echo 'status : Failed : could not update record in database ('.$dbName.').<br/>'.PHP_EOL;
                echo '$rec = <pre style="color:blue">'.PHP_EOL; var_dump ($rec); echo PHP_EOL.'</pre>'.PHP_EOL;
                echo '$call3 = <pre style="color:red">'.PHP_EOL; var_dump ($call3); echo PHP_EOL.'</pre>'.PHP_EOL;
                echo '$e = <pre style="color:red">'.PHP_EOL; var_dump ($e); echo PHP_EOL.'</pre>'.PHP_EOL; 
                exit();
            
            } else {
                $logstr = $fncn.' : CouchDB record add status : Failed.';
                echo $logstr; error_log ($logstr);
                exit();
            }
        }
        if ($debug) { echo '<pre>$call3='; var_dump ($call3); var_dump($_POST); var_dump(json_last_error()); echo '</pre>'.PHP_EOL.PHP_EOL; }
                
        if ($call3->headers->_HTTP->status=='201') {
            $logstr = $fncn.' : CouchDB record add status : Success.'.PHP_EOL;
            echo $logstr; error_log ($logstr);
        } else {
            $logstr = $fncn.' : CouchDB record add status : Failed.'.PHP_EOL;
            echo $logstr; error_log ($logstr);
            exit();
        }
    
    }
    
    public function retryFailedDownloads() {
        $fncn = 'wallpaperScraper_plugin_bingComImages->retryFailedDownloads()';
        $debug = true;
        
    
        global $naWebOS;
        $naWebOS = new NicerAppWebOS();
        $naWebOS->init();
        $cdbDomain = str_replace('.','_',$naWebOS->domainFolder);

        $couchdbConfigFilepath = realpath(dirname(__FILE__).'/../../..').'/domainConfigs/'.$naWebOS->domainFolder.'/couchdb.json';
        if (!file_exists($couchdbConfigFilepath)) trigger_error ($fncn.' : "'.$couchdbConfigFilepath.'" does not exist.', E_USER_ERROR);
        $cdbConfig = json_decode(file_get_contents($couchdbConfigFilepath), true);
        if (json_last_error()!==0) trigger_error ($fncn.' : JSON decoding of file "'.$couchdbConfigFilepath.'" resulted in error '.json_last_error_msg(), E_USER_ERROR);

        if ($debug) { echo 'info : '.__FILE__.' : $debug = true.<br/>'.PHP_EOL;  }


        $cdb = new Sag($cdbConfig['domain'], $cdbConfig['port']);
        $cdb->setHTTPAdapter($cdbConfig['httpAdapter']);
        $cdb->useSSL($cdbConfig['useSSL']);
        //return '<pre>'.json_encode($_COOKIE, JSON_PRETTY_PRINT).'</pre>';
        /*
        if (!array_key_exists('cdb_authSession_cookie', $_COOKIE)) {
            echo 'Browser cookies do not contain database connection settings.';
            exit();
        }*/

        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.gc_maxlifetime', 3600 * 24 * 7);
            session_start();
            $_SESSION['cdb_authSession_cookie'] = $_COOKIE['cdb_authSession_cookie'];
        };

        //login ($cdb); can't use this from the commandline.
        $cdb->login ($cdbConfig['adminUsername'], $cdbConfig['adminPassword']);

        //$dbName = $cdbDomain.'___themeData__user___'.strtolower($username);
        $dbName = strtolower($cdbDomain.'___app__wallpaperscraper_plugin_bingcomimages_faileddownloads');
        try {
            $cdb->setDatabase($dbName, false);
        } catch (Exception $e) {
            echo 'status : Failed : '.$e->getMessage();
            //you should never leak library/sub-system specific error messages to the browser, they tends to be used in hacking efforts against you and/or your clients.
            echo 'Could not connect to database. Please login again.'.PHP_EOL;
            exit();
        }
        
        try {
            $call3 = $cdb->getAllDocs();
        } catch (Exception $e) {
            $logstr = $fncn.' : CouchDB could not $cdb->getAllDocs() from "'.$dbName.'" : Failed with exception "'.$e->getMessage().'".'.PHP_EOL;
            echo $logstr; error_log ($logstr);
            exit();
        }
        if ($debug) { echo '<pre>$call3='; var_dump ($call3); echo '</pre>'.PHP_EOL.PHP_EOL; }
    }
}

?>
