<?php 
require_once (dirname(__FILE__).'/functions.php');

class wallpaperScraper_plugin_imagesGoogleCom implements wallpaperScraper_plugin {
    private $rootPath;
    
    private $db = [];
    
    public function __construct() {
        $this->rootPath = dirname(__FILE__);
    }
    
    public function readDB() {
        $this->db = [
            'pluginSpecificSettings' => safeLoadJSONfile ($this->rootPath.'/settings/plugin.images.google.com.settings.json'),
            'crawlHistory' => safeLoadJSONfile ($this->rootPath.'/settings/crawlHistory.images.google.com.json', false),
            'searchResults' => safeLoadJSONfile ($this->rootPath.'/settings/searchResults.images.google.com.json', false)
        ];
    }
    
    public function saveDB() {
        file_put_contents ($this->rootPath.'/settings/crawlHistory.images.google.com.json', json_encode($this->db['crawlHistory'], JSON_PRETTY_PRINT));
    }
    
    public function doDailyDownload() {
        $fncn = 'wallpaperScraper_plugin_imagesGoogleCom->getContent()';
        $debug = false;
        
        global $filePerms_ownerUser;
        global $filePerms_ownerGroup;
        global $filePerms_perms_publicWriteableExecutable;
        global $filePerms_perms_readonly;
        
        
        set_time_limit (60 * 60);
        ini_set("log_errors", 1);
        ini_set("error_log", "/var/log/php-error.log");
        ini_set( 'user_agent', 'nicer.app-api.crawler.imageSearch/1.0 (https://nicer.app/; rene.veerman.netherlands@gmail.com)' );
        
        $today = date ('Y/m/d');
        $pss = $this->db['pluginSpecificSettings'];
        $jrp = $pss['jsonRequestParameters'];
        $chFile = dirname(__FILE__).'/settings/crawlHistory.images.google.com.json';
        if (file_exists($chFile)) $ch = json_decode(file_get_contents($chFile),true); else $ch=[];
        
        $sr = &$this->db['searchResults'];
        
        $outputFiles = getFilePathList ($this->rootPath.'/output', true, FILE_FORMATS_photos, null, array('file'));
        $outputFiles_basenames = [];
        foreach ($outputFiles as $idx => $filepath) {
            $outputFiles_basenames[$idx] = basename($filepath);
        }
        
        $jrp['searchtermsByPercentage'] = [];
        $searchTermsTotalValue = array_sum($jrp['searchterms']);
        foreach ($jrp['searchterms'] as $k => $v) {
            if ($v > 0) $jrp['searchtermsByPercentage'][$k] = ($v * 100) / $searchTermsTotalValue;
        }
        $array = $jrp['searchtermsByPercentage'];
        shuffle($array); // randomize
        uasort($array, function($a, $b){
            if($a === $b) {
                return rand(0, 1);
            }
            return $a < $b;
        });        
        
        $nextSearchtermIdx = 0;
        $nextMustIncludeIdx = 0;
        $totalNumberOfTransactions = 0;
        $page = 0;
        $historyKey = false;
        $fail = false;
        
        while ($totalNumberOfTransactions < $jrp['restrictions']['maxTransactionsPerDay']) {

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
            
            $a1 = $jrp['searchtermsByPercentage'];
            $nextSearchtermIdx = rand(0, count($a1)-1);
            $nextSearchterm = array_keys($a1)[$nextSearchtermIdx];
            $url = $this->makeURL ($nextSearchterm, $page);
            
            $historyKey = is_array($ch) && count($ch) > 0 && array_search ($url, $ch);
            while ($historyKey!==false) {
                $historyKey2 = false;
                $page = 0;
                $start = ($page) * $jrp['restrictions']['numberOfResultsPerTransaction'];
                $nextSearchtermIdx = rand(0, count($a1)-1);
                $nextSearchterm = array_keys($a1)[$nextSearchtermIdx];
                $url = $this->makeURL ($nextSearchterm, $page);
                $historyKey2 = is_array($ch) && count($ch) > 0 && array_search ($url, $ch);
                while ($historyKey2!==false) {
                    $nextSearchtermIdx = rand(0, count($a1)-1);
                    $nextSearchterm = array_keys($a1)[$nextSearchtermIdx];
                    $url = $this->makeURL ($nextSearchterm, $page);
                    $page++;
                    $historyKey2 = is_array($ch) && count($ch) > 0 && array_search ($url, $ch);
                }
                $url = $this->makeURL ($nextSearchterm, $page);
                $historyKey = is_array($ch) && count($ch) > 0 && array_search ($url, $ch);
            }
            
            // fetch results for previously unsearched combination of search criteria
            $logstr = $fncn.' : NOW CRAWLING '.$url.PHP_EOL;
            echo $logstr; error_log ($logstr);

            $ch[] = $url;
            $fn = dirname(__FILE__).'/settings/crawlHistory.images.google.com.json';
            file_put_contents ($fn, json_encode($ch));
            chown ($fn, $filePerms_ownerUser);
            chgrp ($fn, $filePerms_ownerGroup);
            chmod ($fn, $filePerms_perms_publicWriteableExecutable);
            
            // fetch results via curl (curl is the data transport mechanism)
            $curlCh = curl_init();
            curl_setopt($curlCh, CURLOPT_URL, $url);
            curl_setopt($curlCh, CURLOPT_RETURNTRANSFER, 1);
            $jsonText = curl_exec($curlCh);
            $logstr = $fncn.' : INFO $jsonText='.$jsonText.PHP_EOL;
            //echo $logstr; error_log ($logstr);
            curl_close($curlCh);         
            
            // data integrity check
            $imageSearchCommandResults_asJSON = json_decode ($jsonText, true);
            if (json_last_error()!==JSON_ERROR_NONE) {
                //trigger_error ($fncn.' : Could not decode JSON result from URL='.$url.', ERROR='.json_last_error_msg(), E_USER_ERROR);
                $logstr = $fncn.' : WARNING : Could not decode JSON result from URL='.$url.', ERROR='.json_last_error_msg().', JSON='.$jsonText.PHP_EOL;
                echo $logstr; error_log ($logstr);
            } else {
            
                // now process the individual search results (download the high res files, store them, thumbnail them)
                $sr = &$imageSearchCommandResults_asJSON;
                $fail = false;
                
                if (
                    array_key_exists('error',$sr)
                ) {
                    $e = $sr['error'];
                    if (array_key_exists('code', $e) && $e['code']==429) {
                        $logstr = $fncn.' : NOTICE : Daily quota exhausted.'.PHP_EOL;
                        $this->logNotice ($logstr);
                        exit();
                    }
                } elseif (!array_key_exists('items', $sr) 
                    && array_key_exists('searchInformation',$sr) 
                    && $sr['searchInformation']['totalResults'] === "0"
                ) {
                    $logstr = $fncn.' : NOTICE : 0 results returned.'.PHP_EOL;
                    $this->logNotice ($logstr);
                    
                    /*
                    $page++;
                    while ($page <= $jrp['restrictions']['maxResultsPerSearchterm'] / $jrp['restrictions']['numberOfResultsPerTransaction']) {
                        $start = ($page) * $jrp['restrictions']['numberOfResultsPerTransaction'];
                        $url = 
                            'https://customsearch.googleapis.com/customsearch/v1?rights='.$jrp['rights'].'&imgSize='.$jrp['imgSize'].'&searchType='.$jrp['searchType'].'&imgType='.$jrp['imgType'].'&filter='.$jrp['filter'].'&start='.$start.'&num='.$jrp['num'].'&q='.$nextSearchterm.'&dateRestrict='.$jrp['dateRestrict']./*'&exactTerms=4K[PREVENTS MOST SEARCHQUERIES FROM WORKING]'* /'&cx='.$jrp['cx'].'&key='.$jrp['key'].'&date='.$today.'&siteSearchFilter=i&siteSearch='.$currentSite;
                        $ch[] = $url;
                        $page++;
                    }
                    file_put_contents (dirname(__FILE__).'/setttings/crawlHistory.images.google.com.json', json_encode($ch));
                    
                    $page = 0;
                    $nextSearchtermIdx++;
                    $nextSearchterm = array_keys($a1)[$nextSearchtermIdx];
                    if (is_null($nextSearchterm)) {
                        $nextSearchtermIdx = 0;
                        $nextSearchterm = array_keys($a1)[$nextSearchtermIdx];
                        $page = 0;
                        $siteIdx++;
                        if ($siteIdx >= count($sites)) die('No more sources to query.');
                    }
                    $start = ($page) * $jrp['restrictions']['numberOfResultsPerTransaction'];*/
                    $fail = true;
                    
                } elseif (!array_key_exists('items', $sr)) {
                    $logstr = $fncn.' : ERROR : results do not contain an "items" sub-array. Can not use these results. ABORTING.'.PHP_EOL;
                    $this->logError ($logstr);

                    $fail = true;
                } elseif (!is_array($sr['items'])) {
                    $logstr = $fncn.' : ERROR : results "items" sub-array is not an array itself. Can not use these results. ABORTING.'.PHP_EOL;
                    $this->logError ($logstr);

                    $fail = true;
                }
                
                if ($fail) {
                    //$logstr = $fncn.' : $sr='.json_encode($sr, JSON_PRETTY_PRINT).PHP_EOL;
                    $logstr = $fncn.' : FAILED : $jsonText='.$jsonText.PHP_EOL;
                    $this->logError ($logstr);
                } else {
                    if (array_key_exists('items',$sr)) {
                    
                        // crawlHistory data and search result data even more, grow *huge* over time, so it's kept in a couchdb.apache.org installation.
                        global $naWebOS;
                        $naWebOS = new NicerAppWebOS();
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
                        $dbName = strtolower($cdbDomain.'___app__wallpaperScraper_plugin_imagesGoogleCom');
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
                                $logstr = $fncn.' : CouchDB record add status : Failed with exception "'.$e->getMessage().'".'.PHP_EOL;
                                $this->logError ($logstr);
                            }
                        }
                        if ($debug) { echo '<pre>$call3='; var_dump ($call3); var_dump($_POST); var_dump(json_last_error()); echo '</pre>'.PHP_EOL.PHP_EOL; }
                                
                        if ($call3->headers->_HTTP->status=='201') {
                            $logstr = $fncn.' : CouchDB record add status : Success.'.PHP_EOL;
                            //$this->log ($logstr);
                        } else {
                            $logstr = $fncn.' : CouchDB record add status : Failed (HTTP return code '.$call3->headers->_HTTP->status.').'.PHP_EOL;
                            $this->logError ($logstr);
                        }
                    
                    
                        // now download the actual images found
                        foreach ($sr['items'] as $itemIdx => $it) {
                            //foreach ($it['pagemap']['imageobject'] as $pageIdx => $page) {
                                $fileURL = $it['link'];
                                $logstr = $fncn.' : NOW DOWNLOADING '.$fileURL.PHP_EOL;
                                $this->log ($logstr);
                                
                                $filename = basename(strtok($fileURL, '?'));
                                
                                $filename = str_replace('File:', '', $filename);
                                
                                $pathinfo = pathinfo ($filename);
                                $filename = substr ($pathinfo['filename'], 0, 100).'.'.$pathinfo['extension'];
                                
                                // cloggs up the logs
                                //$logstr = $fncn.' : Downloading '.json_encode ($it, JSON_PRETTY_PRINT).PHP_EOL;
                                //echo $logstr; error_log ($logstr);
                                
                                // download $fileURL into $dir, IF the file as indicated by it's basename() hasn't been downloaded before.
                                $dir = $this->rootPath.'/output/'.$today.'/';
                                createDirectoryStructure ($dir, $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms_publicWriteableExecutable);
                                createDirectoryStructure ($dir.'landscape/', $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms_publicWriteableExecutable);
                                createDirectoryStructure ($dir.'portrait/', $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms_publicWriteableExecutable);
                                
                                if (!array_key_exists('image',$it)) {
                                    $dir2 = $dir;
                                } elseif ($it['image']['width'] > $it['image']['height']) {
                                    $dir2 = $dir.'landscape/';
                                } else {
                                    $dir2 = $dir.'portrait/';
                                }                        
                                
                                $save_file_loc = $dir2.$filename;
                                $thumb_file_loc = $dir2.'thumbs/'.$filename;
                                $descriptionFilepath = $dir2.'descriptions/'.$filename.'.txt';
                                
                                createDirectoryStructure ($dir2.'thumbs/', $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms_publicWriteableExecutable);
                                createDirectoryStructure ($dir2.'descriptions/', $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms_publicWriteableExecutable);
                                
                                $haveOutput = array_search ($filename, $outputFiles_basenames) !== false;
                                $logstr = $fncn.' : $haveOutput='.($haveOutput===true?'true':'false').PHP_EOL;
                                //echo $logstr; error_log ($logstr);
                                //if ($haveOutput) exit();
                                if (!$haveOutput) {
                                    /*
                                    $fp = fopen($save_file_loc, 'wb');
                                    $curlCh = curl_init($fileURL);
                                    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
                                    curl_setopt($curlCh, CURLOPT_FILE, $fp);
                                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                    //curl_setopt($curlCh, CURLOPT_HEADER, 0);
                                    curl_exec($curlCh);
                                    */
                                    $downloadSuccess = $this->download ($fileURL, $save_file_loc);
                                    //if (curl_errno($curlCh)!==0) {
                                    if (!$downloadSuccess) {
                                        //trigger_error ($fncn.' : Could not download "'.$fileURL.'"', E_USER_WARNING);
                                        //$logstr = $fncn.' : WARNING : Could not download "'.$fileURL.'" : CURL-ERROR='.$curl_error($curlCh).'. ADDING THIS DOWNLOAD TO DATABASE FOR FUTURE RETRY.'.PHP_EOL;
                                        $logstr = $fncn.' : WARNING : Could not download "'.$fileURL.'". ADDING THIS DOWNLOAD TO DATABASE FOR FUTURE RETRY.'.PHP_EOL;
                                        $this->logWarning ($logstr);
                                        
                                        //curl_close($curlCh);
                                        //fclose($fp);
                                        unlink ($save_file_loc);
                                        $this->addFileToFailedDownloadsList($it, $save_file_loc, $thumb_file_loc, $descriptionFilepath);
                                    } else {
                                        //curl_close($curlCh);
                                        //fclose($fp);
                                        chown ($save_file_loc, $filePerms_ownerUser);
                                        chgrp ($save_file_loc, $filePerms_ownerGroup);
                                        chmod ($save_file_loc, $filePerms_perms_readonly);
                                        
                                        if (array_key_exists('snippet',$it)) file_put_contents ($descriptionFilepath, $it['snippet']);
                                        
                                        $logstr = 'Download ('.$it['image']['width'].'x'.$it['image']['height'].'px) from '.$fileURL.' saved into "'.$save_file_loc.'"'.PHP_EOL;
                                        $this->log ($logstr);
                                        
                                        $logstr = 'Description file = "'.$descriptionFilepath.PHP_EOL;
                                        //$this->log ($logstr);
                                        
                                        $outputFiles[] = $save_file_loc;
                                        $outputFiles_basenames[] = $filename;
                                        
                                        // create thumbnail use linux imagemagick 'convert' commandline tool.
                                        createDirectoryStructure ($dir2.'thumbs', $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms_publicWriteableExecutable);
                                        $xec = 'convert "'.$save_file_loc.'" -resize 200 "'.$thumb_file_loc.'"';
                                        $output = -1;
                                        $result = [];
                                        sleep (1);
                                        exec ($xec, $output, $result);
                                        if ($result!==0) {
                                            //trigger_error ($fncn.' : Could not create thumbnail "'.$thumb_file_loc.'" : $result='.$result.', $output='.json_encode($output), E_USER_WARNING);
                                            $logstr = $fncn.' : WARNING : Could not create thumbnail "'.$thumb_file_loc.'" : $xec='.$xec.', $result='.$result.', $output='.json_encode($output, JSON_PRETTY_PRINT).'.';// DELETING FILES "'.$save_file_loc.'", "'.$thumb_file_loc.'" and "'.$descriptionFilepath.'" FOR NOW, AND ADDING THIS DOWNLOAD TO THE DATABASE FOR FUTURE RETRY.'.PHP_EOL;
                                            $this->logWarning ($logstr);
                                            
                                            //unlink ($save_file_loc);
                                            //unlink ($thumb_file_loc);
                                            //unlink ($descriptionFilepath);
                                            $this->addFileToFailedDownloadsList($it, $save_file_loc, $thumb_file_loc, $descriptionFilepath);
                                        } else {
                                            chown ($thumb_file_loc, $filePerms_ownerUser);
                                            chgrp ($thumb_file_loc, $filePerms_ownerGroup);
                                            chmod ($thumb_file_loc, $filePerms_perms_readonly);
                                            $logstr = $fncn.' : Thumbnail created in "'.$thumb_file_loc.'"'.PHP_EOL;
                                            //$this->log ($logstr);
                                        }
                                    }
                                }
                            //}
                        }
                    } 
                }
                
                $totalNumberOfTransactions++;
                //usleep (1000 / $jrp['restrictions']['maxTransactionsPerSecond']);
                sleep(1);
            } // search results json decoding success check
        } // main while loop
        $this->saveDB();
    }
    
    public function logstr ($str, $html) {
        echo $str;
        error_log ($str);
        $filepath = dirname(__FILE__).'/log.html';
        $fp = fopen ($filepath, 'a');
        fwrite ($fp, $html);
        fclose ($fp);
    }
    
    public function log ($str) {
        $html = '<p>'.$str.'</p>';
        $this->logstr ($str, $html);
    }
    public function logNotice ($str) {
        $html = '<p class="naLog_notice">'.$str.'</p>';
        $this->logstr ($str, $html);
    }
    public function logWarning ($str) {
        $html = '<p class="naLog_warning">'.$str.'</p>';
        $this->logstr ($str, $html);
    }
    public function logError ($str) {
        $html = '<p class="naLog_error">'.$str.'</p>';
        $this->logstr ($str, $html);
    }
        
    public function download($file_source, $file_target) {
        $rh = fopen($file_source, 'rb');
        $wh = fopen($file_target, 'w+b');
        if (!$rh || !$wh) {
            return false;
        }

        while (!feof($rh)) {
            if (fwrite($wh, fread($rh, 4096)) === FALSE) {
                return false;
            }
        }

        fclose($rh);
        fclose($wh);

        return true;
    }    
    
    public function makeURL ($nextSearchterm, $page) {
        $pss = $this->db['pluginSpecificSettings'];
        $jrp = $pss['jsonRequestParameters'];
        $today = date('Y/m/d');
        $sites = $jrp['sites'];
        $siteIdx = rand(0, count($jrp['sites'])-1);
        $currentSite = $sites[$siteIdx];
        $start = ($page) * $jrp['restrictions']['numberOfResultsPerTransaction'];
        $url =
            'https://customsearch.googleapis.com/customsearch/v1?rights='.$jrp['rights'].'&imgSize='.$jrp['imgSize'].'&searchType='.$jrp['searchType'].'&imgType='.$jrp['imgType'].'&filter='.$jrp['filter'].'&start='.$start.'&num='.$jrp['num'].'&q='.$nextSearchterm.'&dateRestrict='.$jrp['dateRestrict']./*'&exactTerms=4K[PREVENTS MOST SEARCHQUERIES FROM WORKING]'*/'&cx='.$jrp['cx'].'&key='.$jrp['key'];//.'&date='.$today;//.'&siteSearchFilter=i&siteSearch='.$currentSite;
        return $url;
    }
    
    public function addFileToFailedDownloadsList($it, $save_file_loc, $thumb_file_loc, $descriptionFilepath) {
        $fncn = 'wallpaperScraper_plugin_imagesGoogleCom->addFileToFailedDownloadsList()';
        $debug = false;
        
        $rec = [
            'dateFirstDownloaded' => date('Y/m/d'),
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
        $dbName = strtolower($cdbDomain.'___app__wallpaperscraper_plugin_imagesgooglecom_faileddownloads');
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
                $logstr = $fncn.' : CouchDB record add status : Failed with exception "'.$e->getMessage().'".'.PHP_EOL;
                echo $logstr; error_log ($logstr);
                exit();
            }
        }
        if ($debug) { echo '<pre>$call3='; var_dump ($call3); var_dump($_POST); var_dump(json_last_error()); echo '</pre>'.PHP_EOL.PHP_EOL; }
                
        if ($call3->headers->_HTTP->status=='201') {
            $logstr = $fncn.' : CouchDB record add status : Success.'.PHP_EOL;
            echo $logstr; error_log ($logstr);
        } else {
            $logstr = $fncn.' : CouchDB record add status : Failed (HTTP return code 201).'.PHP_EOL;
            echo $logstr; error_log ($logstr);
            exit();
        }
    
    }
    
    public function retryFailedDownloads() {
        $fncn = 'wallpaperScraper_plugin_imagesGoogleCom->retryFailedDownloads()';
        $debug = false;
        
    
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
        $dbName = strtolower($cdbDomain.'___app__wallpaperScraper_plugin_imagesgooglecom_faileddownloads');
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
