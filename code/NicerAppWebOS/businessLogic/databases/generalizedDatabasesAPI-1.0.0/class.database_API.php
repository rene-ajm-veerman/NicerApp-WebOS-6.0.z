<?php
$myPath_BLdbs = dirname(__FILE__).'/../../..'; global $myPath_BLdbs;
require_once ($myPath_BLdbs.'/NicerAppWebOS/boot.php');

class class_NicerAppWebOS_database_API {

    public $cn = 'class_NicerAppWebOS_database_API';
    public $settings = [ "dbsConfigFile" => "/dbsConfigFile.json", "exampleConfigFile" => "/exampleConfigFile.json" ];
    public $connections = [];

    public function __construct( $username='Guest', $dbsConfigFile=null ) {
        $this->connections = $this->connectToDatabases ($username, $dbsConfigFile);
        return $this;
    }

    public function connectToDatabases ( $username='Guest', $dbsConfigFile=null ) {
        $fncn = $this->cn.'->connectToDatabase($p)';
        global $myPath_BLdbs;
        global $naWebOS;
        $ret = [];

        $domainConfigsPath = $myPath_BLdbs.'/../domains/'.$naWebOS->domainFolder.'/domainConfig/';
        $configFilename = 'databases.username-'.$username.'.json';
        //echo '<pre style="color:green">'; var_dump($configFilename); var_dump(file_exists($configFilename)); echo '</pre>';
        if (!file_exists($domainConfigsPath.'/'.$configFilename)) $configFilename = 'databases.username-Guest.json';
        //echo '<pre style="color:lime;background:green;">'; var_dump($configFilename); echo '</pre>';
        if (is_null($dbsConfigFile)) $dbsConfigFile = $domainConfigsPath.'/'.$configFilename;

        $exampleConfigFile = $domainConfigsPath.'/databases.jsonConfigFiles.EXAMPLES/'.$configFilename;

        $p = safeLoadJSONfile ($dbsConfigFile, $exampleConfigFile);
        $this->settings = $p;
        //echo '<pre style="color:lime;background:navy;">';var_dump ($username); var_dump ($dbsConfigFile); var_dump ($p); echo '</pre>';

        $dbt = $p['databases'];
        foreach ($dbt as $ct => $cRec) {
            $ret[] = [
                'ct' => $ct,
                'cRec' => $cRec,
                'conn' => $this->connectToDatabase ( $username, $ct, $cRec )
            ];
        };
        $this->connections = $ret;

        return $this->connections;
    }

    public function connectToDatabase ( $username, $ct = null, $cRec = null ) {
        $fncn = $this->cn.'->connectToDatabase()';
        global $myPath_BLdbs;
        global $naWebOS;

        $db = null;
        if (strpos('couchdb', $ct)!==false) {
            $db = new class_NicerAppWebOS_database_API_couchdb_3_2 ( clone $naWebOS, $username, $cRec );
        }
        if (strpos('fsdb', $ct)!==false) {
            $db = new class_NicerAppWebOS_database_API_fileSystemDB_version1 ( clone $naWebOS, $username, $cRec);
        }
        if (strpos('adodb', $ct)!==false) {
            $db = new class_NicerAppWebOS_database_API_adodb ( clone $naWebOS, $username, $cRec);
        }


        return $db;

        /* DO NOT TOUCH THIS LENGTHY COMMENT SECTION.
         * IT IS NEEDED LATER TO PROPERLY INTEGRATE adodb AND fsdb SUPPORT!
         *
        if ($ct==='fsdb') {
            $myPath_domainConfigs_MYDOMAIN_TLD_fsdb_settings_file = $myPath_BLdbs.'/domainConfigs/'.$naWebOS->domainFolder.'/database.fileSystem.settings.json';
            if (!file_exists($myPath_domainConfigs_MYDOMAIN_TLD_fsdb_settings_file) || !is_readable($myPath_domainConfigs_MYDOMAIN_TLD_fsdb_settings_file)) {
                $exampleFile = str_replace ('.json', '.EXAMPLE.json', $myPath_domainConfigs_MYDOMAIN_TLD_fsdb_settings_file);
                $this->throwError ($fncn.' : !file_exists("'.$myPath_domainConfigs_MYDOMAIN_TLD_fsdb_settings_file.'"), see "'.$exampleFile.'" for an example.', E_USER_NOTICE);
                $this->throwError ($fncn.' : Could not initialize .../NicerAppWebOS/3rd-party/adodb5/*', E_USER_WARNING);
                $this->hasFSdb = false;
            } else {
                $this->fsDBsettings = safeLoadJSONfile ($myPath_domainConfigs_MYDOMAIN_TLD_fsdb_settings_file);
                $this->hasFSdb = true;
            }
        }

        if ($ct==='adodb') {
            $myPath_domainConfigs_MYDOMAIN_TLD_adodb_sslJSONfile = $myPath_BLdbs.'/domainConfigs/'.$naWebOS->domainFolder.'/database.SQL.adodb-5.22--sslSettings.json';
            if (!file_exists($myPath_domainConfigs_MYDOMAIN_TLD_adodb_sslJSONfile) || !is_readable($myPath_domainConfigs_MYDOMAIN_TLD_adodb_sslJSONfile)) {
                $exampleFile = str_replace ('.json', '.EXAMPLE.json', $myPath_domainConfigs_MYDOMAIN_TLD_adodb_sslJSONfile);
                $this->throwError ($fncn.' : !file_exists("'.$myPath_domainConfigs_MYDOMAIN_TLD_adodb_sslJSONfile.'"), see "'.$exampleFile.'" for an example.', E_USER_NOTICE);
                $this->throwError ($fncn.' : Could not initialize .../NicerAppWebOS/3rd-party/adodb5/*', E_USER_WARNING);
                $this->hasAdodb = false;
            } else {
                $this->adodbSSLsettings = safeLoadJSONfile ($myPath_domainConfigs_MYDOMAIN_TLD_adodb_sslJSONfile);
                $this->hasAdodb = true;
            }

            if ($this->hasAdodb) {
                if (in_array('msqli', $dbConnectionTypes)) {
                    $this->adodb_host = $this->adodbSSLsettings['dbHost'] || $dbHost;
                    $this->adodb_dbName = $this->dataSetName ($dbName);

                    $this->adodb_user = $this->adodbSSLsettings['user'] || $dbUser;
                    $this->adodb_password = $this->adodbSSLsettings['password'] || $dbPassword;

                    $this->adodb_sslKeyFile = $this->adodbSSLsettings['adodb_sslKeyFile'] || $sslKeyFile;
                    $this->adodb_sslCertFile = $this->adodbSSLsettings['adodb_sslCertFile'] || $sslCertFile;
                    $this->adodb_sslCA = $this->adodbSSLsettings['adodb_sslCA'] || $sslCA;
                    $this->adodb_sslCApath = $this->adodbSSLsettings['adodb_sslCApath'] || $sslCA;
                    $this->adodb_sslCipher = $this->adodbSSLsettings['adodb_sslCipher'] || $sslCipher;

                    $db = newAdoConnection('mysqli');
                    $db->ssl_key = $this->adodb_sslKeyFile;
                    $db->ssl_cert = $this->adodb_sslCertfile;
                    $db->ssl_ca = $this->adodb_sslCA;
                    $db->ssl_capath = $this->adodb_sslCApath;
                    $db->ssl_cipher = $this->adodb_sslCipher;
                    $db->connect($host, $user, $password, $database);

                    $dbs[] = $db;
                    $dbRec = [
                        'dbType' => 'adodb',
                        'dbConnectionType' => 'msqli',
                        'dbHost' => $this->adodb_host,
                        'dbName' => $this->adodb_dbName,
                        'user' => $this->adodb_user,
                        'password' => $this->adodb_password,
                        'config' => [
                            'adodb_sslKeyFile' => $this->adodb_sslKeyFile,
                            'adodb_sslCertFile' => $this->adodb_certFile,
                            'adodb_sslCA' => $this->adodb_sslCA,
                            'adodb_sslCApath' => $this->adodb_sslCApath,
                            'adodb_sslCipher' => $this->adodb_sslCipher
                        ],
                        'idx' => count($dbs)
                    ];
                    $dbRecs[] = $dbRec;
                }
                if (in_array('postgresql9', $dbConnectionTypes)) {
                    $this->adodb_host = $this->adodbSSLsettings['dbHost'] || $dbHost;
                    $this->adodb_dbName = $this->dataSetName ($dbName);

                    $this->adodb_user = $this->adodbSSLsettings['user'] || $dbUser;
                    $this->adodb_password = $this->adodbSSLsettings['password'] || $dbPassword;

                    $this->adodb_sslKeyFile = $this->adodbSSLsettings['adodb_sslKeyFile'] || $sslKeyFile;
                    $this->adodb_sslCertFile = $this->adodbSSLsettings['adodb_sslCertFile'] || $sslCertFile;
                    $this->adodb_sslCA = $this->adodbSSLsettings['adodb_sslCA'] || $sslCA;
                    $this->adodb_sslCApath = $this->adodbSSLsettings['adodb_sslCApath'] || $sslCA;
                    $this->adodb_sslCipher = $this->adodbSSLsettings['adodb_sslCipher'] || $sslCipher;

                    $db = newAdoConnection('pdo');
                    $dsn  = 'pgsql:host='.$this->adodb_host.';dbname='.$this->adodb_dbName;
                    $user = $this->adodb_user;
                    $pass = $this->adodb_password;
                    $db->connect($dsn,$user,$pass);

                    $dbs[] = $db;
                    $dbRec = [
                        'dbType' => 'adodb',
                        'dbConnectionType' => 'postgresql9',
                        'dbHost' => $this->adodb_host,
                        'dbName' => $this->adodb_dbName,
                        'user' => $this->adodb_user,
                        'password' => $this->adodb_password,
                        'idx' => count($dbs)
                    ];
                    $dbRecs[] = $dbRec;
                }
            }
        }
*/
    }


/*
    public function throwError ($msg, $errorLevel) {
        //echo '<pre class="nicerapp_error__database">$msg='.$msg.', $errorLevel='.$errorLevel.'</pre>';
        trigger_error ($msg, $errorLevel);
    }

    public function dbName ($dbSuffix) {
        $dbName = str_replace('.','_',$this->settings['wcs']->domain).'___'.str_replace('.','_',$dbSuffix);
        $dbName = strToLower($dbName);
        return $dbName;
    }
*/

    public function getNewRandomIDs ($relTableName, $fieldName) {
        $r = [];
        foreach ($this->connections as $idx => $connRec) {
            if (is_string($connRec))
                trigger_error ($this->cn.'->findConnection("'.$ct.'") : $connRec='.$connRec);
            else {
                $rc = $connRec['conn']->getNewRandomID ($relTableName, $fieldName);
                $r = array_merge ($r, [
                    'ct' => $connRec['ct'],
                    'host' => $connRec['cRec']['username'].':*****@'.$connRec['cRec']['host'].':'.$connRec['cRec']['port'],
                    'randomID' => $rc
                ] );
            }
        }
        return $r;
    }



    public function getAllDatabases () {
        $r = [];
        foreach ($this->connections as $idx => $c) {
            $x = $c['conn']->getAllDatabaseNames ();
            $r = array_merge ($r, [[ 'c' => $c, 'x' => $x ]] );
        }
        return $r;
    }

    public function listDatabases ($allDBs, $dbs, $dbsReset) {
        global $naWebOS;

        $dbsLowerCased = [];
        foreach ($dbs as $dbName => $mustDo) {
            $dbsLowerCased[] = strtolower($dbName);
        };
        $dbsResetLowerCased = [];
        foreach ($dbs as $dbName => $mustDo) {
            $dbsResetLowerCased[] = strtolower($dbName);
        };

        $html = '<link type="text/css" rel="StyleSheet" href="/NicerAppWebOS/db_init.css?c='.date('Ymd_His').'">'.PHP_EOL;
        foreach ($allDBs as $cIdx => $dbsRec) {
            $html .= '<div id="listDatabases_cIdx__'.$cIdx.'" class="listDatabases_forConnection">'.PHP_EOL;
                $html .= "\t".'<div id="listDatabases_cIdxConnectionDetails__'.$cIdx.'" class="listDatabases_connectionSettings">'.PHP_EOL;
                    $html .= "\t\t".'server connection <span class="dbConnectionType">'.$dbsRec['c']['ct'].'</span> : <span class="dbConnectionAddress">'.$dbsRec['c']['cRec']['username'].':*****@'.$dbsRec['c']['cRec']['host'].':'.$dbsRec['c']['cRec']['port'].'</span>'.PHP_EOL;
                $html .= "\t".'</div>'.PHP_EOL;


            $debugMe = false;
            if ($debugMe) {
                echo '<pre style="color:yellow;background:navy;">'; var_dump ($dbsRec['x']); echo '</pre>';
            } else
            foreach ($dbsRec['x']->body as $dbIdx => $dbName) {
                $dbDomainName = preg_replace('/___.*$/','',$dbName);
                $strippedDBname = preg_replace('/.*___/','',$dbName);

                $dbName2 = null;
                foreach ($dbs as $dbName3 => $mustDoDB) {
                    if (strtolower($dbName3)===$strippedDBname) $dbName2 = $dbName3;
                }
                $dbName4 = null;
                foreach ($dbs as $dbName5 => $mustDoDB) {
                    if (strtolower($dbName5)===$strippedDBname) $dbName4 = $dbName5;
                }

                $inJurisdiction = $dbsRec['c']['conn']->dataSetName_domainName($naWebOS->domainFolder) === $dbDomainName;

                $affected =
                    $inJurisdiction
                    && in_array($strippedDBname, $dbsLowerCased)
                    && !is_null($dbName2)
                    && $dbs[$dbName2];
                $resetAffected =
                    $inJurisdiction
                    && in_array($strippedDBname, $dbsResetLowerCased)
                    && !is_null($dbName4)
                    && $dbsReset[$dbName4];
                $toBeDeleted =
                    $inJurisdiction
                    && !in_array($strippedDBname, $dbsLowerCased);
                $className = (
                    $inJurisdiction
                    ? $toBeDeleted
                        ? 'dbToBeDeleted'
                        : ($affected
                            ? 'dbAffected'
                            : 'dbUnaffected'
                        )
                    : 'dbOutsideJurisdictionOfThisCall'
                );
                $detail = (
                    $inJurisdiction
                    ? $toBeDeleted
                        ? 'will be deleted.'
                        : ($affected
                            ? 'exists and will be deleted and re-initialized.'
                            : ($resetAffected
                                ? 'exists and will be reset into default settings except for the user data it contains.'
                                : 'exists and will be unaffected.'
                            )
                        )
                    : 'is not in the jurisdiction of this call.'
                );

                $html .= '<div class="couchdbDatabase   Name">Database <span class="'.$className.'">'.$dbName.'</span> '.$detail.'</div>'.PHP_EOL;
            }


            $html .= '</div>'.PHP_EOL;
        }
        return $html;
    }

    public function clearOutDatabases ($dbs) {
        return $this->callAllDataSets ('clearOutDatabases', [$dbs]);
    }

    public function setGlobals ($username) {
        return $this->callAllDataSets ('setGlobals', [$username]);
    }

    public function createUsers($users, $groups) {
        return $this->callAllDataSets ('createUsers', [$users, $groups]);
    }

    public function createDatabases ($dbs) {
        $r = [];
        $failedAtLeastOne = false;
        foreach ($this->connections as $idx => $c) {
            foreach ($dbs as $dbName => $mustDo) {
                if ($mustDo) {
                    $fncName = 'createDataSet_'.$dbName;

                    global $naWebOS;
                    $un1 = strtolower($naWebOS->ownerInfo['OWNER_NAME']);
                    $un1 = str_replace('.', '__', $un1);
                    $un1 = str_replace(' ', '_', $un1);

                    $isAdmin = strpos($dbName, '_'.$un1)!==false;
                    $adminUser = preg_replace('/.*___/','',$dbName);
                    $params = [ $adminUser ];
                    if ($isAdmin) $fncName = preg_replace('/___'.$un1.'/','',$fncName);
                    //echo $fncName.'<br/>';
                    $x = call_user_func_array ( [ $c['conn'], $fncName ], $params );
                    $localCheck = $this->standardResultHandling($c, $x);
                    $r = array_merge ($r, [$localCheck]);
                    if ($localCheck['result']!==true) $failedAtLeastOne = true;
                }
            }
        }
        return $failedAtLeastOne;
    }

    public function resetDatabases ($dbs) {
        $r = [];
        $failedAtLeastOne = false;
        foreach ($this->connections as $idx => $c) {
            foreach ($dbs as $dbName => $mustDo) {
                if ($mustDo) {
                    $fncName = 'resetDataSet_'.$dbName;

                    global $naWebOS;
                    $un1 = strtolower($naWebOS->ownerInfo['OWNER_NAME']);
                    $un1 = str_replace('.', '__', $un1);
                    $un1 = str_replace(' ', '_', $un1);

                    $isAdmin = strpos($dbName, '_'.$un1)!==false;
                    $adminUser = preg_replace('/.*___/','',$dbName);
                    $params = [ $adminUser ];
                    if ($isAdmin) $fncName = preg_replace('/___'.$un1.'/','',$fncName);
                    //echo $fncName.'<br/>';
                    $x = call_user_func_array ( [ $c['conn'], $fncName ], $params );
                    $localCheck = $this->standardResultHandling($c, $x);
                    $r = array_merge ($r, [$localCheck]);
                    if ($localCheck['result']!==true) $failedAtLeastOne = true;
                }
            }
        }
        return $failedAtLeastOne;
    }

    //--- CALL-ROUTING, ERROR HANDLING AND LOGGING FUNCTIONS
    public function findConnection ($ct) {
        foreach ($this->connections as $idx => $connRec) {
            if (is_string($connRec))
                trigger_error ($this->cn.'->findConnection("'.$ct.'") : $connRec='.$connRec, E_USER_WARNING);
            else if ($connRec['ct'] === $ct) return $connRec['conn'];
        }
        return false;
    }

    public function standardResultHandling ($c, $resultValue) {
        if (
            is_array($resultValue)
            && count($resultValue) > 0
            && (
                !array_key_exists('result', $resultValue)
                || $resultValue['result'] === true
            )
        ) return [ 'c' => $c, 'resultValue' => $resultValue, 'result' => true ];

        if (
            is_object($resultValue)
            || is_string($resultValue)
            || $resultValue === true
        ) return [ 'c' => $c, 'resultValue' => $resultValue, 'result' => true ];

        return ['c'=>$c, 'resultValue'=>$resultValue, 'result' => false];
    }

    public function callAllDataSets ($functionName, $params=null) {
        global $naErr; global $naLog;
        $r = [];
        $failedAtLeastOne = false;
        if (is_null($params)) $params = [];

        foreach ($this->connections as $idx => $c) {
            $x = call_user_func_array ( [ $c['conn'], $functionName ], $params );
            $localCheck = $this->standardResultHandling($c, $x);
            $r = array_merge ($r, [$localCheck]);
            if ($localCheck['result']!==true) {
                $failedAtLeastOne = true;
                $r['origin'] = $functionName;
                if (
                    stripos($_SERVER['HTTP_USER_AGENT'], 'bot')===false
                    && stripos($_SERVER['SCRIPT_NAME'], 'logs.php')===false
                ) {
                    $err = $naErr->addStandardResults ($r);
                    $naLog->add ( [ $err ] );
                }
            }
        }

        return $r;
    }

    public function callDataSet ($ct, $functionName, $params=null) {
        global $naErr; global $naLog;
        $r = [];
        $failedAtLeastOne = false;
        if (is_null($params)) $params = [];

        foreach ($this->connections as $idx => $c) {
            if ($c['ct'] == $ct) {
                $x = call_user_func_array ( [ $c['conn'], $functionName ], $params );
                $localCheck = $this->standardResultHandling($c, $x);
                $r = array_merge ($r, [$localCheck]);
                if ($localCheck['result']!==true) {
                    $failedAtLeastOne = true;
                    $r['origin'] = $functionName;
                    if (
                        stripos($_SERVER['HTTP_USER_AGENT'], 'bot')===false
                        && stripos($_SERVER['SCRIPT_NAME'], 'logs.php')===false
                    ) {
                        $err = $naErr->addStandardResults ($r);
                        $naLog->add ( [ $err ] );
                    }
                }
            }
        }

        return $r;
    }

    public function addLogEntries ($entries) {
        #TODO 1-2a
        $dbsPostedInto = [];
        foreach ($this->connections as $idx => $c) {
            $connectionType = $c['ct']; // MySQL, postgresql9, couchdb, etc.
            $dbsPostedInto[$connectionType] = $c['conn']->addLogEntries($entries);
        }
        return $dbsPostedInto;
    }



    //--- UTILITY FUNCTIONS
    public function testDBconnection () {
        $r = [];
        foreach ($this->connections as $idx => $c) {
            $t = [
                $c['conn']->connectionType => [
                    'connectionSettings' => $c['conn']->connectionSettings,
                    'results' => $c['conn']->testDBconnection()
                ]
            ];
            $r = array_merge_recursive($r, $t);
        }
        return $r;
    }

    public function editDataSubSet ($ct=null, $relTableName=null, $findCommand=null, $recordOverlay=null) {
        $fncn = $this->cn.'::editDataSubSet($relTableName='.json_encode($relTableName).', $findCommand='.json_encode($findCommand).', $recordOverlay='.json_encode($recordOverlay).')';
        // parameter error handling
        $go = true;
        $beginEnd = '----------------------<br/>'.PHP_EOL;
        $msg = $beginEnd; $this->cn.'::'.$fncn.' : <br/>'.PHP_EOL;
        if (!is_string($relTableName) || $relTableName=='') {
            $go = false;
            $msg .= 'invalid $relTableName.<br/>'.PHP_EOL;
        }
        if (
            !is_array($findCommand)
            || !array_key_exists('selector', $findCommand)
            || !is_array($findCommand['selector'])
            || !array_key_exists('fields', $findCommand)
            || !is_array($findCommand['fields'])
        ) {
            $go = false;
            $msg .= 'invalid $findCommand.<br/>'.PHP_EOL;
        }
        if (
            isset($recordOverlay)
            && is_array($recordOverlay)
            && count($recordOverlay)===0
        ) {
            $go = false;
            $msg .= 'invalid $recordOverlay (array with 0 entries).<br/>'.PHP_EOL;
        }

        if (!$go) {
            $msg .= $beginEnd;
            trigger_error ($msg, E_USER_ERROR);
            return false;
        } else {
            return $this->callDataSet($ct, 'editDataSubSet', [ $relTableName, $findCommand, $recordOverlay ]);
        }
    }

    public function cms_editDocument () {
        return $this->callAllDataSets ('cms_editDocument');
    }

    public function editDataByUsers ($findCommand=null, $dataIDs=null, $dataIDs_idx=null) {
        return $this->callAllDataSets ('editDataByUsers', [ $findCommand, $dataIDs, $dataIDs_idx ]);
    }


    public function changeThemeName ($oldThemeName, $newThemeName) {
        foreach ($this->connections as $idx => $c) {
            $x = $c['conn']->changeThemeName($oldThemeName, $newThemeName);
            $localCheck = $this->standardResultHandling($c, $x);
            if ($localCheck!==true) return $r;
        }
        return true;
    }

    public function delete_allThemes_byName ($themeName) {
        foreach ($this->connections as $idx => $c) {
            $x = $c['conn']->delete_allThemes_byName ($themeName);
            $localCheck = $this->standardResultHandling($c, $x);
            if ($localCheck!==true) return $r;
        }
        return true;
    }

    public function getSettingsPositions () {
        foreach ($this->connections as $idx => $c) {
            $x = $c['conn']->getSettingsPositions ();
            $localCheck = $this->standardResultHandling($c, $x);
            if ($localCheck!==true) return $r;
        }
        return true;
    }


}

?>
