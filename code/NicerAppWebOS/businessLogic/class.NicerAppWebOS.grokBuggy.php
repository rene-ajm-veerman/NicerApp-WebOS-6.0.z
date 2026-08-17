<?php
class NicerAppWebOS {
    public $cn = '.../NicerAppWebOS/businessLogic/business/class.NicerAppWebOS.php::class NicerAppWebOS';
    public $dbs = null;
    public $dbsAdmin = null;

    public $codePath = '';
    public $about = [];

    public $initialized = false;
    public $globals = null;
    public $url = '-url-';
    public $debug = false;
    public $debugThemeLoading = false;
    public $browserDebug = false;
    public $showAllErrors = false;

    public $view = '-view-';

    public $baseIndentLevel = 1;
    public $webRootPath = '-webRootPath-';
    public $path = '-path-';
    public $domainFolder = '-domainFolder';
    public $domain = '-domain-';
    public $webPath = '-webPath-';
    public $domainPath = '-domainPath-';
    public $domainFolderForDB = '-domainFolderForDB-';

    public $cssFiles = [];
    public $javascriptFiles = [];

    public $ownerInfo = [];

    public $viewsMID = '-viewsMID-';
    public $viewsMIDpath = '-viewsMIDpath-';

    public $hasDB = false;
    public $theme = 'default';

    public $comments = '-pre-initialisation-';

    public function __construct () {
        $fncn = $this->cn.'->__construct()';
        $this->codePath = realpath(dirname(__FILE__).'/../..');
        $fn = dirname(__FILE__).'/../version.json';
        $this->about = file_exists($fn)?json_decode (file_get_contents($fn)):[];
        //echo '<pre style="color:purple;">'; var_dump($_GET); echo '</pre>';
        //echo '<pre style="color:red;">'; var_dump($_SERVER); echo '</pre>';
        $this->baseIndentLevel = 1;

        $p1 = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
        $this->webRootPath = $p1; // You'll need this.

        if (array_key_exists('DOCUMENT_ROOT',$_SERVER) && $_SERVER['DOCUMENT_ROOT']!=='') {
            $p2a = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..');
            $p2b = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..');
            $p2c = str_replace($p2b.DIRECTORY_SEPARATOR,'', $p2a);
            $this->path = $p2b;
            $this->domainFolder = basename($_SERVER['DOCUMENT_ROOT']);//str_replace($p2a.DIRECTORY_SEPARATOR,'', $p2b);
            $this->domain = $_SERVER['SERVER_NAME'];
            $this->webPath = $p2b;
        } else {
            $p2b = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..');
	    $this->path = $p2b;
	    $p3 = explode('/',$_SERVER['PWD']);
	    if (array_key_exists(5,$p3)) $p3 = $p3[5]; 
	    else {
	    	$p3 = explode('/',$_SERVER['PATH_TRANSLATED']);
	    	if (array_key_exists(5,$p3)) $p3 = $p3[5]; else {
			$p3 = 'UNKNOWN-FOLDER-in-logic.business..class.NicerAppWebOS.php';
		}
		}
            $this->domainFolder = $this->domain = $this->webPath = $p3;
        }

        // You'll need this too.
        $this->domainPath = $this->webRootPath.'/domains/'.$this->domainFolder;



        if (false) {
            $dbg = [
                $this->path,
                $this->domainFolder,
                $this->domain,
                $this->webPath,
                $this->webRootPath,
                $this->domainPath
            ];
            echo '<pre style="color:green;">t12:'; var_dump ($dbg); echo '</pre>'.PHP_EOL;
            //exit();
        }

    }

    public function __construct2() {
        $fncn = $this->cn.'->__construct2()';
        //echo '<pre style="color:blue;">'; var_dump($_GET); echo '</pre>';

        $rp_domain = $this->domainPath.'/domainConfig';
        $this->cssFiles = [
            [ 'indexFile' => $rp_domain.'/index.css.json', 'type' => 'css' ],
            [ 'files' => $this->getVividButtonCSSfiles(), 'type' => 'css' ] //already included in "indexFile".
        ];
        $this->javascriptFiles = [
            [ 'indexFile' => $rp_domain.'/index.javascripts.json', 'type' => 'javascript' ]//,
            //[ 'indexFile' => $rp_domain.'/index.customerJavascripts.json', 'type' => 'javascript' ],
            //[ 'files' => $this->getVividButtonJavascriptFiles(), 'type' => 'javascript' ] // already included in "indexFile"
        ];

        $dfd = strtolower($this->domainFolder);
        $dfd = str_replace('.', '_', $dfd);
        if (preg_match('/^\d/', $dfd)) {
            $dfd = 'number_'.$dfd;
            //$this->browserDebug = true; // TODO : CSS CONCATENATION DOES NOT WORK YET, FOR SOME STRANGE REASON
        }
        $this->domainFolderForDB = $dfd;
        //echo 't34:'; var_dump ($this->domainFolderForDB); echo PHP_EOL;


        $fnOwnerInfo = $this->domainPath.'/domainConfig/company.owner.json';
        //echo $fnOwnerInfo.': '; var_dump (file_exists($fnOwnerInfo)); die();

        if (!file_exists($fnOwnerInfo)) {
            $msg = $fncn.' : file "'.$fnOwnerInfo.'" missing. please re-run .../NicerAppWebOS/scripts.install/install-NicerAppWebOS???.sh from the commandline.';
            echo $msg.'<br/>';
            trigger_error ($msg, E_USER_ERROR);
            exit();
        }
        if (!is_readable($fnOwnerInfo)) {
            $msg = $fncn.' : file "'.$fnOwnerInfo.'" is not readable. please re-run .../NicerAppWebOS/scripts.maintenance/setPermissions.sh from the commandline.';
            echo $msg.'<br/>';
            trigger_error ($msg, E_USER_ERROR);
            exit();
        }
        $this->ownerInfo = safeLoadJSONfile($fnOwnerInfo);

        $midsFile =
            $this->path.'/NicerAppWebOS/apps/'
            .'manufacturerNameForDomainName_'.$this->domainFolder.'_val.txt';
        if (!file_exists($midsFile)) {
            echo '<pre>';
            print_r (debug_backtrace());
            echo '</pre>';
            $msg = $fncn.' : file "'.$midsFile.'" does not exist';
            echo $msg;
            trigger_error ($msg, E_USER_ERROR);
            exit();
        };
        if (!is_readable($midsFile)) {
            $msg = $fncn.' : file "'.$midsFile.'" is not readable. run .../NicerAppWebOS/scripts.maintenance/setPermissions.sh from the commandline please';
            echo $msg;
            trigger_error ($msg, E_USER_ERROR);
            exit();
        };
        $mfc = trim(file_get_contents ($midsFile));

        $this->viewsMIDpath =
            $this->path.'/NicerAppWebOS/apps/'
            .str_replace($this->domainFolder, $mfc, $midsFile);
        $this->viewsMID = $mfc;
    }

    public function getSite() {
        $rp_domain = $this->domainPath.'/domainConfig';
        $templateFile = $rp_domain.'/index.template.php';
        $templateCustomer = $rp_domain.'/index.template.byCustomer.php';
        $titleFile = $rp_domain.'/index.title.php';
        $descrFile = $rp_domain.'/index.description.php';
        //echo '<pre style="color:navy;background:yellow;">'; var_dump($_GET); echo '</pre>';

        if ($this->browserDebug) {
            $javascriptLinks = $this->getConcatenatedLinks ($this->javascriptFiles);
            $cssLinks = $this->getConcatenatedLinks ($this->cssFiles);
        } else {
            $javascriptLinks = $this->getLinks ($this->javascriptFiles);
            $cssLinks = $this->getLinks ($this->cssFiles);
        }

        $content = $this->getContent();
        //echo '<pre>'; var_dump ($content); echo '</pre>'; //die();

        ob_end_clean();    ob_start();    flush(); ob_flush(); ob_end_flush();
        if ($content==='') $content = [ 'siteContent' => '<h2>HTTPS Error 404 : Not found.</h2>', 'head' => '<h2>HTTPS Error 404</h2>' ];

        if (array_key_exists('_desktopDefinition',$content))
            $desktopDefinition = $content['_desktopDefinition'];
        else
            $desktopDefinition = file_get_contents ($rp_domain.'/index.desktopDefinition.default.json');

        global $naIsBot;
        $replacements = array (
            //'{$view}' => ( is_array($view) ? json_encode($view, JSON_PRETTY_PRINT) : '{}' ),
            '{$title}' =>
                array_key_exists('_title',$content)
                && is_string($content['_title'])
                && $content['_title']!==''
                    ? $content['_title']
                    : trim(execPHP($titleFile)),
            '{$description}' =>
                array_key_exists('_description',$content)
                && is_string($content['_description'])
                && $content['_description']!==''
                    ? $content['_description']
                    : trim(execPHP($descrFile)),
            '{$domain}' => $this->domain,
                '{$webPath}' => $this->webPath,
            '{$domainPath}' => $this->domainPath,
            '{$cssLinks}' => $cssLinks,
            '{$javascriptLinks}' => $javascriptLinks,
            '{$desktopDefinition}' => $desktopDefinition,
            '{$customerSpecificBodyContent}' => (file_exists($templateCustomer)?execPHP($templateCustomer):''),
            '{$pageSpecificCSS}' => (
                !$naIsBot
                && array_key_exists('head',$content)
                ? $content['head']
                : ''
            ), // uses up much CPU power and disk activity
            // '{$theme}' => $this->cssThemes
            //'{$viewport}' => $this->getMetaTags_viewport(),
            //'{$siteMenu_avoid}' => $siteMenu_avoid
        );

        foreach ($content as $divName=>$contentForDiv) {
            //$contentForDiv = htmlentities($contentForDiv);
            //$arr = array ( '{$div_'.$divName.'}' => $contentForDiv );
            $replacements['{$div_'.$divName.'}'] = $contentForDiv;// = array_merge ($replacements, $arr);
        }
        $searches = array_keys($replacements);
        $replaces = array_values($replacements);

        // CRITICAL: Make sure all values going into the template are safe
        foreach ($replacements as $k => $v) {
            if (is_object($v)) {
                $replacements[$k] = json_decode(json_encode($v), true); // convert object -> array
            }
        }

        $html = require_return($templateFile, true);

        if (false) {  // debug mode
            echo '<pre style="color:green;">$replacements='; var_dump ($replacements); echo '</pre>';
            exit();
        }

        $html5 = str_replace($searches, $replaces, $html);
        echo $html5;
        //echo htmlentities($html5);die();

        //echo '<pre>'; var_dump ($this->about); var_dump($this->path); echo '</pre>'; die();
    }


    public function initializeDatabases () {
        $fncn = $this->cn.'->initializeDatabases()';
        global $naWebOS;

        $this->hasDB = false;

        if ($this->dbsAdmin===null) {
            $this->dbsAdmin = 'initializing';
            // logged in as $cdbConfig['adminUsername']!
            // $this->dbAdmin = new class_NicerAppWebOS_database_API_couchdb_3_2 (clone $this, true);
            $this->dbsAdmin = new class_NicerAppWebOS_database_API ('admin');
            try {
                // $this->dbsAdmin = new class_NicerAppWebOS_database_API ('admin');

                if (php_sapi_name() !== 'cli') {
                    //WILL NEVER WORK; HANDLED BY logic.AJAX/ajax_testDBconnection.php! setcookie('cdb_admin_loginName' ,$this->dbsAdmin->findConnection('couchdb')->username, time() + 604800, '/');
                    $_SESSION['cdb_admin_loginName'] = $this->dbsAdmin->findConnection('couchdb')->username;
                }

                $this->hasDB = true;
            } catch (Throwable $e) {
                echo '<pre style="color:white;background:rgba(0,50,0,0.5);border-radius:10px;padding:8px;">';
                echo json_encode($e->getMessage());
                echo '</pre>';
                exit;
            } catch (Exception $e) {
                echo '<pre style="color:white;background:rgba(0,50,0,0.5);border-radius:10px;padding:8px;">';
                echo json_encode($e->getMessage());
                echo '</pre>';
                exit;
            }
        }

        if ($this->dbs===null) {
            $this->dbs = 'initializing';
            //$this->db = new class_NicerAppWebOS_database_API_couchdb_3_2 (clone $this, false);
            /*
            try {
                // log in as the end-user, but check that a Guest account exists first
                $this->dbs = new class_NicerAppWebOS_database_API ('Guest');
            } catch (Exception $e) {
                echo '<pre>';
                echo $fncn.' : Throwable $error=';//.json_encode($error,JSON_PRETTY_PRINT);;
                var_dump ($e);
                echo '</pre>';
                $this->dbsAdmin->findConnection('couchdb')->createGuestUser();
            }
            */
            try {
                /*
                if (array_key_exists('cdb_'.$naWebOS->domainFolder.'__loginName',$_COOKIE)) $un = $_COOKIE['cdb_'.$naWebOS->domainFolder.'__loginName']; else $un = 'Guest';
                $this->dbs = new class_NicerAppWebOS_database_API ($un);
                //echo '<pre>'; var_dump($_SERVER['SCRIPT_NAME']); echo '</pre>';//var_dump ($this->dbs);exit();

                if (strpos('ajax_login.php',$_SERVER['SCRIPT_NAME'])==false)
                    setcookie('cdb_'.$naWebOS->domainFolder.'__loginName', $this->dbs->findConnection('couchdb')->username, time() + 604800, '/');
                */

                global $naUsername;

                if (array_key_exists('cdb_loginName',$_COOKIE)) {
                    $un = $this->dbsAdmin->findConnection('couchdb')->translate_couchdbUserName_to_plainUserName( // NEEDED!
                        $_COOKIE['cdb_loginName']
                    );
                } else {
                    $un = 'Guest';
              }
                $this->dbs = new class_NicerAppWebOS_database_API ($un);
                //echo '<pre style="color:blue;">'; var_dump($_COOKIE); echo '</pre>';var_dump ($this->dbs->findConnection('couchdb')->username);exit();

/*
                if (strpos('ajax_login.php',$_SERVER['SCRIPT_NAME'])!==false)
                    setcookie('cdb_loginName', $this->dbs->findConnection('couchdb')->username, time() + 604800, '/');
*/

                $naUsername =
                    $this->dbsAdmin->findConnection('couchdb')->translate_couchdbUserName_to_plainUserName(
                        $this->dbs->findConnection('couchdb')->username
                    );

            //echo '<pre>'; var_dump ($this->dbs); exit();

                if (php_sapi_name() !== 'cli') {
                    //WILL NEVER WORK; HANDLED BY logic.AJAX/ajax_testDBconnection.php! setcookie('cdb_loginName' ,$this->dbs->findConnection('couchdb')->username, time() + 604800, '/');
                    $_SESSION['cdb_loginName'] = $this->dbs->findConnection('couchdb')->username;
                }

                $this->hasDB = true;
            } catch (Throwable $error) {
                echo '<pre>';
                echo $fncn.' : Throwable $error=';//.json_encode($error,JSON_PRETTY_PRINT);;
                var_dump ($error);
                echo '</pre>';
                exit();
            } catch (Exception $error) {
                echo '<pre>';
                echo $fncn.' : Exception $error=';//.json_encode($error,JSON_PRETTY_PRINT);;
                var_dump ($error);
                echo '</pre>';
                exit();
            }
        };

        $this->dbs->setGlobals($this->dbs->findConnection('couchdb')->username);

        $this->dbsAdmin->setGlobals($this->dbsAdmin->findConnection('couchdb')->username);

        $this->initialized = true;
    }

    public function initializeGlobals() {
        $fncn = $this->cn.'->initializeGlobals()';
        $view = $fncn.' : FATAL ERROR : Could not look up view settings in database.<p style="color:blue;background:rgba(255,255,255,0.65);">$_GET='.str_replace("\n", "<br/>\n", json_encode($_GET,JSON_PRETTY_PRINT)).'</p>'; // assume the worst

        $this->globals = [
            'cdbDesignDocs' => [
                'logentries_pageLoad' => '_design/b13265782ef772fafebf4ce4c02d6605f0412f73'
            ]
        ];

        //$this->comments = new class_NicerApp_WebOS_siteComments();

        global $argv;
        $this->url = array_key_exists('REQUEST_URI', $_SERVER)
            ? $_SERVER['REQUEST_URI']
            : $argv[0];
        $this->showAllErrors = !array_key_exists('sae', $_REQUEST) || $_REQUEST['sae']==='y';

        $this->view = [
            '/' => [
                'page' => 'error'
            ]
        ];
        if (array_key_exists('viewID', $_GET)) {
            if ($_GET['viewID']=='' || $_GET['viewID']=='/') {
                $this->view = ['/'=>['page'=>'index']];
            } else {
                $decoded = json_decode(decode_base64_url($_GET['viewID']), true);
                if (json_last_error()!==0) {
                    $this->view = $this->getView ($_GET['viewID']);
                } else {
                    $this->view = $decoded;
                }
            }
        } elseif (array_key_exists('seoValue',$_GET)) {
                $decoded = json_decode(decode_base64_url($_GET['seoValue']), true);
                if (json_last_error()!==0) {
                    $this->view = $this->getView($_GET['seoValue']);
                } else {
                    $this->view = $decoded;
                }
        }

        $this->comments = new class_naComments();
        $this->comments->addIndexes();

        return is_array($this->view);
    }

    public function versionLastModified() {
        $rootPath = realpath(__DIR__.'/..');
        $fileSpecsRE = FILE_FORMATS_code;
        $exclRE = FILE_FORMATS_NO_siteMeda_3rdParty;
        $files = getFilePathList ($rootPath, true, $fileSpecsRE, $exclRE, ['file']);
        $m = 0;
        foreach ($files as $idx => $fp) {
            $fm = filemtime($fp['realPath']);
            if ($fm > $m) $m = $fm;
        }
        date_default_timezone_set('CET');
        return date('Y-m(F)-d(l) H:i:s', $m);//json_encode($files, JSON_PRETTY_PRINT);
    }







    public function getContent ($divID=null, $viewID=null) {
        if (!is_null($viewID)) $_GET['viewID'] = $viewID;
        $fncn = $this->cn.'->getContent("'.$divID.'", "'.$viewID.'") $_GET='.json_encode($_GET);
        $debug = $this->debug;
        if ($debug) {
            echo '<pre style="color:orange">'; var_dump ($fncn); echo '</pre>'; //die();
            echo '<pre>'; var_dump ($this->view); echo '</pre>'; //die();
        };
        if (is_null($viewID)) $view = $this->view; else {
            $view = $this->getView($viewID);
        }
        if (!is_array($_GET)) {
            $msg = $fncn.' : FAILED (this was not called via a web-browser).';
            trigger_error ($msg, E_USER_ERROR);
            error_log ($msg);
            return $this->getContent__standardErrorMessage($msg);
        } else if (!is_array($view)) {
            return $this->getContent__standardErrorMessage($view);
            // no trigger_error() or error_log() needed here!
        } else {
            //if (!array_key_exists('viewID',$_GET))$_GET['viewID'] = '/';
            if ($this->nonEmptyStringField('commentID',$_GET))
                return $this->getContent__comment($_GET['commentID']);

            elseif (
                $this->nonEmptyStringField('username',$_GET)
                && $this->nonEmptyStringField('url1',$_GET)
                && $this->nonEmptyStringField('dataID',$_GET)
            ) return $this->getContent__data_by_users    ($_GET['username'], $_GET['url1'], $_GET['dataID']);

            elseif ($this->nonEmptyStringField('app-wikipedia_org', $_GET))
                return $this->getContent__view_wikipedia ($_GET['app-wikipedia_org']);

            elseif ($this->nonEmptyStringField('app-wikipedia_org-search', $_GET))
                return $this->getContent__view_wikipedia_search ($_GET);

            elseif ( $this->nonEmptyStringField('viewID',$_GET) )
                return $this->getContent__view ($_GET['viewID']); // this handles the front page of a website too.

            elseif ( $this->nonEmptyStringField('seoValue',$_GET) )
                return $this->getContent__view ($_GET['seoValue']); // this handles the front page of a website too.

            else return $this->getContent__standardErrorMessage(
                $fncn.' : FAILED (this was not called with the right parameters). parameters are '.json_encode($_GET)
            );
        }
    }

    public function getContent__comment ($cid) {
        $ret = [];
        $file = $this->path.'/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsComments/app.dialog.siteContent.php';
        if (!file_exists($file) || !is_readable($file)) {
            $msg = 'Could not find comments code "'.$file.'".';
            $html = '<p>&nbsp;</p><span style="background:rgba(0,0,0,0.5);border:1px solid grey; box-shadow:2px 2px 4px 2px rgba(0,0,0,0.7); padding:10px;margin:20px;border-radius:8px;color:ivory;text-shadow:2px 2px 3px rgba(0,0,0,7);">'.$msg.'</span>';
            $ret['siteContent'] = $html;
        } else {
            $fc = require_return ($file);
            $html = str_replace('{$commentID}', $cid, $fc);
            $ret['siteContent'] = $html;
        }
        return $ret;
    }

    public function getContent__standardErrorMessage ($msg) {
        $ret = [];
        $msg = '<p>&nbsp;</p><span style="background:rgba(0,0,0,0.5);border:1px solid grey; box-shadow:2px 2px 4px 2px rgba(0,0,0,0.7); padding:10px;margin:20px;border-radius:8px;color:ivory;text-shadow:2px 2px 3px rgba(0,0,0,7);">'.$msg.'</span>';
        $file = $this->domainPath.'/domainConfig/errorMessage.default.php';
        if (!file_exists($file) || !is_readable($file)) {
            $html = $msg;
            $ret['siteContent'] = $html;
        } else {
            $fc = require_return ($file);
            $html = str_replace('{$msg}', $msg, $fc);
            $ret['siteContent'] = $html;
        }
        return $ret;
    }

    public function getContent__view_wikipedia ($wiki_params=null) {
        $fncn = $this->cn.'::getContent__view_wikipedia()';
        global $naWebOS;
        // output frontpage.dialog.*.php
        $folder = $this->codePath.'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/3rd-party-site.wikipedia.org/';
        $files = getFilePathList($folder, false, '/app.dialog.*\.php/', null, array('file'), 1, 1, true)['files'];
        //{ echo $folder.'<br/>'.PHP_EOL; echo json_encode($files); echo PHP_EOL.PHP_EOL; };
        $ret = [];
        foreach ($files as $idx2 => $filepath) {
            $fileRoot = $folder;
            $filename = str_replace ($fileRoot, '', $filepath['realPath']);
            $dialogID = str_replace ('app.dialog.', '', $filename);
            $dialogID = str_replace ('.php', '', $dialogID);
            $arr = array ( $dialogID => execPHP($filepath['realPath']) );
            //var_dump (file_exists($filepath)); echo PHP_EOL;
            //var_dump ($dialogID); echo PHP_EOL;
            //$arr = array ( $dialogID => $filepath );
            $ret = array_merge ($ret, $arr);
        }

        global $naIsBot;
        if (
            !$naIsBot
            && (
                strpos($_SERVER['SCRIPT_NAME'], '/index.php')!==false
                || strpos($_SERVER['SCRIPT_NAME'], '/ajax_get_content.php')!==false
            )
        ) $ret = array_merge ($ret, [
            'head' => $this->getPageCSS(true,false)
        ]);
//echo '<pre>'; var_dump ($ret); exit();
        return $ret;
    }

    public function getContent__view_wikipedia_search ($wiki_params=null) {
        $fncn = $this->cn.'::getContent__view_wikipedia_search()';
        global $naWebOS;
        // output frontpage.dialog.*.php
        $folder = $this->codePath.'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/3rd-party-site.wikipedia.org/';
        $files = getFilePathList($folder, false, '/app.dialog.*\.php/', null, array('file'), 1, 1, true)['files'];
        //{ echo $folder.'<br/>'.PHP_EOL; echo json_encode($files); echo PHP_EOL.PHP_EOL; };
        $ret = [];
        foreach ($files as $idx2 => $filepath) {
            $fileRoot = $folder;
            $filename = str_replace ($fileRoot, '', $filepath['realPath']);
            $dialogID = str_replace ('app.dialog.', '', $filename);
            $dialogID = str_replace ('.php', '', $dialogID);
            $arr = array ( $dialogID => execPHP($filepath['realPath']) );
            //var_dump (file_exists($filepath)); echo PHP_EOL;
            //var_dump ($dialogID); echo PHP_EOL;
            //$arr = array ( $dialogID => $filepath );
            $ret = array_merge ($ret, $arr);
        }

        global $naIsBot;
        if (
            !$naIsBot
            && (
                strpos($_SERVER['SCRIPT_NAME'], '/index.php')!==false
                || strpos($_SERVER['SCRIPT_NAME'], '/ajax_get_content.php')!==false
            )
        ) $ret = array_merge ($ret, [
            'head' => $this->getPageCSS(true,false)
        ]);
//echo '<pre>'; var_dump ($ret); exit();
        return $ret;
    }

    public function getContent__view ($viewID=null) {
        $fncn = $this->cn.'::getContent__view()';
        $debug = $this->debug;
        global $naWebOS;

        if ($debug) { echo '<pre style="color:white;background:blue;">'; echo '$viewID='; var_dump ($viewID); echo '</pre>';  }
        $ret = [];
        if (!is_string($viewID) || $viewID==='') {
            $msg = $fncn.' : FAILED (invalid or empty viewID parameter).';
            trigger_error ($msg, E_USER_ERROR);
            return $this->getContent_standardErrorMessage ($msg);
        } else {
            //echo $viewID;
            if ($viewID==='/') {
                // output frontpage.dialog.*.php
                $folder = $this->domainPath.'/domainConfig/';
                //$debug = true;
                $files = getFilePathList($folder, false, '/frontpage.dialog.*\.php/', null, array('file'), 1, 1, true)['files'];
                if ($debug) { echo '<p style="color:yellow;background:red;margin:10px;padding:5px;border-radius:10px;">'.$folder.'</p><br/><pre style="color:yellow;background:red;margin:10px;padding:5px;border-radius:10px;">$files='.PHP_EOL; var_dump($files); echo '</pre>'.PHP_EOL.PHP_EOL;  };

                foreach ($files as $idx2 => $filepath) {
                    $fileRoot = $folder;
                    $filename = str_replace ($fileRoot, '', $filepath['realPath']);
                    $dialogID = str_replace ('frontpage.dialog.', '', basename($filename));
                    $dialogID = str_replace ('.php', '', $dialogID);
                    $arr = array ( $dialogID => execPHP($filepath['realPath']) );
                    //echo 't666a'; var_dump ($filepath); echo PHP_EOL;
                    //var_dump ($dialogID); echo PHP_EOL;
                    //$arr = array ( $dialogID => $filepath );
                    $ret = array_merge ($ret, $arr);
                };
                if ($debug) {
                    $xa = json_encode($ret, JSON_PRETTY_PRINT);
                    $x1 = json_decode($xa, true);
                    echo '<pre>'; var_dump (htmlentities($xa)); echo '</pre>';
                }

            } else {
                // request view settings from database
                try {
                    $view = is_object($this->view)?(array)$this->view:$this->view;
                    if ($debug) { echo '<pre style="color:white;background:green;font-weight:bold;padding:5px;margin:10px;border-radius:10px;">$view='; var_dump ($view); echo '</pre>';  }

                } catch (Exception $e2) {
                    echo '<pre style="color:white;background:red;margin:10px;padding:5px;border-radius:10px;">'; echo '<b>ERROR : '.$e2->getMessage().'</b>'; echo '<pre style="margin-left:10px;color:white;">debug_backtrace()='; var_dump (debug_backtrace()); echo '</pre><b>$view='; var_dump($view); echo '</b><p style="font-weight:bold;">probable solution : run "php .../NicerAppWebOS/scripts.maintenance/htaccess.build.php and htaccess.build-views.php again"</p>'; exit();
                }
                $ret = [ ];// DONT! 'siteContent' => '<pre>'.json_encode($view,JSON_PRETTY_PRINT).'</pre>'];
                //$debug = true;
                if (is_array($view)) {
                    if ($debug) { echo '<pre style="color:yellow;background:red;margin:10px;padding:5px;border-radius:10px;">'; var_dump ($view); echo '</pre>'; };
                    if (count($view)===3 && array_key_exists('seoValue',$view) && is_string($view['seoValue'])) {
                        $viewsFolder = $this->webPath.$view['appFolder'];
                        //echo 't3';
                    } else {
                        $viewsFolder = '-$viewsFolder NOT FOUND-';
                        if (array_key_exists('aid',$view)) {
                            switch ($view['aid']) {
                                case 0:
                                    $appFolder = $this->webPath.'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.javascriptRendering';
                                    $rf = $appFolder.'/music';
                                    $df = $rf.'/'.$view['rp'];

                                    //$rp_domain = $this->path.'/../domains/'.$this->domainFolder.'/domainConfig';
                                    //$descrFile = $rp_domain.'/index.description.php';
                                    $descrFile = $appFolder.'/app.description.php';
                                    $ret['_description'] = require_return($descrFile);


                                    $files = getFilePathList ($appFolder, false, '/app\.dialog\..*/', null, array('file'), 1, 1, true);
                                    $files = $files['files'];
                                    if ($debug) { var_dump ($viewsFolder); echo '<pre style="color:yellow;background:magenta;padding:5px;margin:10px;border-radius:10px;">'; var_dump ($files); echo '</pre>'.PHP_EOL.PHP_EOL;  };

                                    $titleFile = $appFolder.'/app.title.site.php';
                                    $desktopDefinitionFile = $appFolder.'/index.desktopDefinition.json';
                                    if (file_exists($titleFile))
                                        $ret['_title'] = require_return ($titleFile);
                                    if (file_exists($desktopDefinitionFile))
                                        $ret['_desktopDefinition'] = require_return ($desktopDefinitionFile);

                                    //echo '<pre>'; var_dump($files); die();

                                    foreach ($files as $idx3 => $contentFile) {
                                        //echo '<pre> t666b'; var_dump ($contentFile);// die();
                                        if (strpos($contentFile['realPath'], 'app.dialog.')!==false) {
                                            $divID = str_replace('app.dialog.', '', basename($contentFile['realPath']));
                                            //echo $divID.'<br/>';
                                            $divID = preg_replace('/2D.*\.php/', '', $divID);
                                            $divID = str_replace('.php', '', $divID);
                                            $divID = str_replace('.', '', $divID);
                                            //echo $contentFile['realPath'].'<br/>';
                                            $ret[$divID] = execPHP ($contentFile['realPath']);
                                        }
                                    }
                                    break;
                            }
                        } else if (array_key_exists('appFolder',$view)) {
                            $viewsFolder = $this->webPath.$view['appFolder'];
                            //echo $viewsFolder; die();

                                    $files = getFilePathList ($viewsFolder, false, '/app\.dialog\..*/', null, array('file'), 1, 1, true);
                                    if ($debug) { echo '<pre style=";color:purple;background:cyan;">'; var_dump ($viewsFolder); var_dump ($files); echo '</pre>'; }
                                    if (!array_key_exists('files', $files)) {
                                        $msg = 'HTTP error 404 : no files matching app.* in "'.$viewsFolder.'"';
                                        echo $msg;
                                        exit();
                                        trigger_error ($msg, E_USER_ERROR);
                                    };
                                    $files = $files['files'];
                                    if ($debug) { var_dump ($viewsFolder); echo '<pre style="color:yellow;background:magenta;padding:5px;margin:10px;border-radius:10px;">'; var_dump ($files); echo '</pre>'.PHP_EOL.PHP_EOL;  };

                                    $titleFile = $this->codePath.'/'.$viewsFolder.'/app.title.site.php';
                                    $desktopDefinitionFile = $this->codePath.'/'.$viewsFolder.'/index.desktopDefinition.json';
                                    if (file_exists($titleFile))
                                        $ret['_title'] = require_return ($titleFile);
                                    if (file_exists($desktopDefinitionFile))
                                        $ret['_desktopDefinition'] = require_return ($desktopDefinitionFile);

                                    //echo '<pre>'; var_dump($files); die();

                                    foreach ($files as $idx3 => $contentFile) {
                                        //echo '<pre> t666b'; var_dump ($contentFile);// die();
                                        if (strpos($contentFile['realPath'], 'app.dialog.')!==false) {
                                            $divID = str_replace('app.dialog.', '', basename($contentFile['realPath']));
                                            //echo $divID.'<br/>';
                                            $divID = preg_replace('/2D.*\.php/', '', $divID);
                                            $divID = str_replace('.php', '', $divID);
                                            $divID = str_replace('.', '', $divID);
                                            //echo $divID.'<br/>';
                                            $ret[$divID] = execPHP ($contentFile['realPath']);
                                        }
                                    }
                        } else {
                            if ($debug) {
                                echo '<pre style="margin:10px;padding:10px;border-radius:10px;background:rgba(0,0,50,0.7);color:ivory;text-shadow:2px 2px 3px rgba(0,0,0,0.8);">';
                                var_dump ($view);
                                var_dump (array_key_exists('appFolder',$view));
                                echo '</pre>';
                            }
                            if (array_key_exists('appFolder',$view)) $viewsFolder = $this->webPath.$view['appFolder'];
                            else foreach ($view as $appFolder => $app) {
                                //echo '<pre>'; var_dump ($app); echo '</pre>'; //die();
                                if (array_key_exists('codePath',$app)) $viewsFolder = $this->webPath.'/'.$app['codePath'];
                                if (array_key_exists('relPath',$app)) $viewsFolder = $this->webPath.'/'.$app['relPath'];
                                if (array_key_exists('web   Path',$app)) $viewsFolder = $this->webPath.'/'.$app['webPath'];
                                if (array_key_exists('appFolder',$app)) $viewsFolder = $this->webPath.$app['appFolder'];
                                break;
                            }

                            // do not remove this yet, due to get deleted in a next version upon code normalization.
                            if ($viewsFolder==='-$viewsFolder NOT FOUND-') {
                                foreach ($view as $appName => $appSettings) break;
                                if (array_key_exists('codePath',$appSettings)) $viewsFolder = $this->webPath.'/'.$appSettings['codePath'];
                                if (array_key_exists('relPath',$appSettings)) $viewsFolder = $this->webPath.'/'.$appSettings['relPath'];
                                if (array_key_exists('webPath',$appSettings)) $viewsFolder = $this->webPath.'/'.$appSettings['webPath'];
                                if (array_key_exists('appFolder',$appSettings)) $viewsFolder = $this->webPath.$appSettings['appFolder'];

                            }

                            //var_dump($viewsFolder2); exit();
                            foreach ($view as $viewsFolder2 => $viewSettings) {
                                foreach ($viewSettings as $appFolder => $appSettings) break;
                                //dangerous, don't remember why i put this in here atm :
                                //$dbInitPHP = $this->codePath.'/'.$viewsFolder.'/db_init.php';
                                //if (file_exists($dbInitPHP)) execPHP ($dbInitPHP);
                                if (is_dir($this->domainPath.$viewsFolder2.'/'.$appFolder))
                                    $files = getFilePathList ($this->domainPath.$viewsFolder2.'/'.$appFolder, false, '/app\.dialog\..*/', null, array('file'), 1, 1, true);
                                else
                                    $files = getFilePathList ($this->domainPath.$viewsFolder2, false, '/app\.dialog\..*/', null, array('file'), 1, 1, true);
                                //$debug = true;
                                if ($debug) { echo '<pre style=";color:purple;background:cyan;">'; var_dump ($this->domainPath.$viewsFolder2); var_dump ($files); echo '</pre>'; }
                                //$debug = false;
                                if (!array_key_exists('files', $files)) {
                                    $msg = 'HTTP error 404 : no files matching app.* in "'.$viewsFolder.'"';
                                    echo $msg;
                                    exit();
                                    trigger_error ($msg, E_USER_ERROR);
                                };
                                $files = $files['files'];
                                if ($debug) { var_dump ($viewsFolder); echo '<pre style="color:yellow;background:magenta;padding:5px;margin:10px;border-radius:10px;">'; var_dump ($files); echo '</pre>'.PHP_EOL.PHP_EOL;  };

                                $titleFile = $this->codePath.'/'.$viewsFolder.'/app.title.site.php';
                                $desktopDefinitionFile = $this->codePath.'/'.$viewsFolder.'/index.desktopDefinition.json';
                                if (file_exists($titleFile))
                                    $ret['_title'] = require_return ($titleFile);
                                if (file_exists($desktopDefinitionFile))
                                    $ret['_desktopDefinition'] = require_return ($desktopDefinitionFile);

                                //echo '<pre>'; var_dump($files); die();
                                $files2  = [];
                                foreach ($files as $idx3 => $contentFile) {
                                    //echo '<pre> t666b'; var_dump ($contentFile);// die();
                                    if (stripos($contentFile['realPath'], 'app.dialog.')!==false) {
                                        $divID = str_replace('app.dialog.', '', basename($contentFile['realPath']));
                                        //echo $divID.'<br/>';
                                        $divID = preg_replace('/2D.*\.php/', '', $divID);
                                        $divID = str_replace('.php', '', $divID);
                                        $divID = str_replace('.', '', $divID);
                                        //echo 't1:'.$divID.'<br/>';
                                        ob_end_clean();
                                        ob_start();
                                        flush(); ob_flush(); ob_end_flush();

                                        $fn = basename($contentFile['realPath']);
                                        $fn = str_replace('.php','',$fn);
                                        $fn = preg_replace('/_.*/','',$fn);
                                        if (!in_array($fn,$files2)) {
                                            $c = execPHP ($contentFile['realPath']);
                                            if (strpos($divID, '_')!==false) $divID = preg_replace('/_.*/','',$divID);
                                            //echo $divID.'<br/>'; echo $c;

                                            $ret[$divID] = $c;
                                        }
                                        $files2[] = $fn;

                                        //echo 'c:'.htmlentities($c).'<br/>';
                                        //echo 't2:'.$divID.'<br/>';
                                    }
                                }
                                //$contentFile = $rootPath.'/'.$viewsFolder.'/app.dialog.siteContent.php';
                                //$ret = [ 'siteContent' => execPHP ($contentFile['realPath']) ];
                            }
                        }
                    //echo '<pre style="color:red;background:yellow;padding:5px;border-radius:4px;">'; var_dump($viewsFolder); echo '</pre>';

                    }
                } elseif ($view==='["NOT FOUND"]') {
                    // serve up the front page!
                    $ret = ['siteContent' => 'HTTP error 404 : "'.$viewID.'" could not be found.' ];
                }



                // render view
                // ....
            }


            global $naIsBot;
            if (
                !$naIsBot
                && (
                    strpos($_SERVER['SCRIPT_NAME'], '/index.php')!==false
                    || strpos($_SERVER['SCRIPT_NAME'], '/ajax_get_content.php')!==false
                )
            ) $ret = array_merge ($ret, [
                'head' => $this->getPageCSS(true,false)
            ]);

            //echo '<pre>t331:'; debug_print_backtrace(); echo '</pre>';
            //$debug = true;
            if ($debug) {
                echo '<pre style="color:navy;background:yellow">';
                echo '</pre>';
                exit();
            }


            return $ret;

        }
    }

    public function getContent__data_by_users ($username=null, $url1=null, $dataID=null) {
        $fncn = $this->cn.'::getContent__data_by_users()';
        global $rootPath_na;
        global $naWebOS;
        $db = $naWebOS->dbs->findConnection('couchdb');
        $cdb = $db->cdb;

        $relTableName = 'data_by_users';
        $dataSetName = $db->dataSetName($relTableName);
        $cdb->setDatabase ($dataSetName, false);

        // am i called with the correct parameters, or not?
        if (
            !is_string($username)
            || $username===''
            || !is_string($url1)
            || $url1===''
            || !is_string($dataID)
            || $dataID===''
        ) {
            $dbg = [
                'username' => $username,
                'url1' => $url1,
                'dataID' => $dataID
            ];
            $msg = $fncn.' : FAILED : '.json_encode($dbg);
            trigger_error ($msg, E_USER_ERROR);
            return $this->getContent_standardErrorMessage ($msg);
        } else {

            // fetch dataRecord
            $findCommand = [
                'selector' => [ /*'user' => str_replace('-',' ',$username), */ 'url1' => $url1, 'seoValue' => $dataID ],
                'fields' => ['_id']
            ];
            //echo '<pre style="padding:8px;border-radius:10pFx;background:rgba(255,255,255,0.5);color:green;">'; var_dump ($findCommand); echo '</pre>';
            try {
                $call = $cdb->find ($findCommand);
            } catch (Exception $e) {
                $msg = $fncn.' FAILED while trying to find in \''.$dataSetName.'\' : '.$e->getMessage();
                trigger_error ($msg, E_USER_ERROR);
                echo $msg;
                return false;
            }

            //echo '<pre style="color:blue;background:rgba(255,255,255,0.7);border-radius:20px;">'; var_dump ($dataSetName); var_dump ($findCommand); var_dump ($call);var_dump (count($call->body->docs));exit();


            if (count($call->body->docs)===0) {
                $msg = 'Content could not be found.';
                return $this->getContent__standardErrorMessage ($msg);
            }

            $call2 = $cdb->get ($call->body->docs[0]->_id);
            //echo '<pre style="color:blue">'; var_dump ($call2);exit();
            $dataRecord = (array)$call2->body;
            //echo '<pre style="color:green">'; var_dump ($dataRecord);exit();



            if (array_key_exists('viewID', $dataRecord)) {
                // request view settings from database
                $viewID = $dataRecord['viewID'];

                $db = $this->dbs->findConnection('couchdb');
                $cdb = $db->cdb;
                $dataSetName = $db->dataSetName('views'); // i know, couchdb calls a 'table' a 'database'. and that sux.

                $findCommand = [
                    'selector' => [ 'viewID' => $viewID ],
                    'use_index' => 'primaryIndex',
                    'fields' => '_id'
                ];
                try {
                    $call3 = $cdb->find ($findCommand);
                } catch (Exception $e) {
                    $msg = $fncn.' FAILED while trying to find in \''.$dataSetName.'\' : '.$e->getMessage();
                    trigger_error ($msg, E_USER_ERROR);
                    echo $msg;
                    return false;
                }

                $call = $cdb->get ($call->body[0]['_id']);
                $view = $call->body[0];

                // overlay view settings, data from {$myDomain_tld}___views with
                //  data from {$myDomain_tld}___data_by_user::appParameters
                if (is_array($view['view'])) {
                    foreach ($view['view'] as $viewKey => $viewRecord) {
                        foreach ($dataRecord['appParameters'] as $drAppIdx => $drAppRecord) {
                            foreach ($drAppRecord as $viewKey2 => $viewRecord2) {
                                if ($viewKey === $viewKey2) {
                                    $view['view'][$viewKey2] = array_merge (
                                        $view['view'][$viewKey2],
                                        $viewRecord2
                                    );
                                }

                            }
                        }
                    }
                }
            } else {
                global $toArray;
                $view = $toArray($dataRecord['viewSettings']);
            }
            //echo '<pre style="color:green">'; var_dump ($view); echo '</pre>'; exit();
            $this->view = $view;

            // render the view
            if (is_array($view)) {
                if (array_key_exists('misc', $view)) {
                    $fsid = $view['misc']['folder'];
                    foreach ($view as $k => $rec) {
                        if ($k=='misc') continue; else {
                            $fid = $k;
                            $rp = $fsid.'/'.$fid;
                        }
                    }
                    $view = [ $rp => $rec ];
                }
                //echo '<pre style="color:purple;background:cyan;">'; var_dump ($view); echo '</pre>'; exit();

                foreach ($view as $viewsFolder => $viewSettings) {
                    $rootPath = str_replace('/NicerAppWebOS','',$rootPath_na);
                    if (file_exists($rootPath.'/'.$viewsFolder)) {
                        $files = getFilePathList ($rootPath.'/'.$viewsFolder, false, '/app.*/', null, array('file'), 1, 1, true)['files'];
                        //if ($debug)
                        //{ var_dump ($rootPath.'/'.$viewsFolder); echo '<pre style="color:yellow;background:red;">'; var_dump ($files); echo '</pre>'.PHP_EOL.PHP_EOL;  };

                        $titleFile = $rootPath.'/'.$viewsFolder.'/app.title.site.php';
                        $ret = [
                            'title' => execPHP($titleFile)
                        ];
                        //echo '<pre style="color:blue">'; var_dump ($ret); exit();
                        foreach ($files as $idx3 => $contentFile) {
                            if (strpos($contentFile['realPath'], 'app.dialog.')!==false) {
                                $divID = str_replace('app.dialog.', '', basename($contentFile['realPath']));
                                $divID = str_replace('.php', '', $divID);
                                $ret[$divID] = execPHP ($contentFile['realPath']);
                            }
                        }
                        //$contentFile = $rootPath.'/'.$viewsFolder.'/app.dialog.siteContent.php';
                        //$ret = [ 'siteContent' => execPHP ($contentFile) ];
                    }
                }
            }


            global $naIsBot;
            if (
                !$naIsBot
                && (
                    strpos($_SERVER['SCRIPT_NAME'], '/index.php')!==false
                    || strpos($_SERVER['SCRIPT_NAME'], '/ajax_get_content.php')!==false
                )
            ) $ret = array_merge ($ret, [
                'head' => $this->getPageCSS(true,false)
            ]);
            //echo '<pre style="color:blue">'; var_dump ($ret); exit();
            return $ret;
            //echo json_encode($ret);

        }
    }

    public function getView ($viewID, $getKeyName='seoValue') {
        $fncn = 'class NicerAppWebOS->getView()';
        $view = false;

        $db = $this->dbs->findConnection('couchdb');
        var_dump ($db->cdb->getSession()->username); exit;
        $cdb = $db->cdb;
        $dataSetName = $db->dataSetName('viewsIDs'); // i know, couchdb calls a 'SQL table' a 'database'. and that sux.

        $cdb->setDatabase ($dataSetName, false);
        $findCommand = [
            'selector' => [
                'seoValue' => substr($_GET[$getKeyName],0,1)=='/'
                    ? substr($_GET[$getKeyName],1)
                    : $_GET[$getKeyName]
            ],
            'use_index' => 'primaryIndex',
            'fields' => ['_id', 'viewID', 'appFolder' ]
        ];
        $view = [
            'findCommand' => $findCommand
        ];
        //echo '<pre style="color:orange;">'.json_encode($findCommand, JSON_PRETTY_PRINT).'</pre>'.PHP_EOL;
        try {
            $call = $cdb->find ($findCommand);
            //echo '<pre style="color:red;">'.json_encode($call, JSON_PRETTY_PRINT).'</pre>'.PHP_EOL; exit;
        } catch (Exception $e) {
            $msg = $fncn.' FAILED while trying to find in \''.$dataSetName.'\' : $e->getMessage()='.$e->getMessage().', $findCommand='.json_encode($findCommand);
            $this->view = $msg;
            echo '<h1>'.$msg.'</h1>';
            trigger_error ($msg, E_USER_NOTICE);
            exit;
            return false;
        }
        $view = [
            'findCommand' => $findCommand,
            'call' => $call
        ];

        //echo '<pre style="color:blue">$findCommand='.json_encode($findCommand, JSON_PRETTY_PRINT).'</pre><pre style="color:green">$call='.json_encode($call, JSON_PRETTY_PRINT).'</pre>';
        //return true;

        if (
            is_object($call)
            && is_object($call->body)
            && is_array($call->body->docs)
        ) {
            if (count($call->body->docs)===1) {
                $cdb->setDatabase ($db->dataSetName('views'));
                try {
                    //echo $getKeyName; echo '<pre>'; var_dump ($call->body); echo '</pre>'; echo $call->body->docs[0]->viewID;
                    $call2 = $cdb->get ($call->body->docs[0]->viewID);
                } catch (Exception $e) {
                    $msg = $fncn.' FAILED while trying to find view settings in \''.$db->dataSetName('views').'\' : $e->getMessage()='.$e->getMessage();
                    trigger_error ($msg, E_USER_NOTICE);
                    error_log($msg);
                    return $msg;
                }
                //echo '<pre>'; var_dump($call);exit();

                $view = json_decode(json_encode($call2->body->view), true);
            } else {
                $msg = $fncn.' : views count incorrect ("docs" count should be exactly 1) for seoValue='.$_GET['seoValue'].'.<br/>'."\n".'<pre>$call->headers->_HTTP->status='.$call->headers->_HTTP->status.', $call->body='.json_encode($call->body,JSON_PRETTY_PRINT).'</pre>';
                trigger_error($msg, E_USER_WARNING);
                error_log($msg);
                return $msg;
            }
        }

        return $view;
    }










    public function nonEmptyStringField ($fieldName, $arr) {
        if (
            array_key_exists($fieldName, $arr)
            && is_string ($arr[$fieldName])
            && $arr[$fieldName]!==''
        ) return true;
        return false;
    }




    public function getLinks ($files) {
        $lines = '';
        //echo '<pre>'; var_dump ($files); die();
        foreach ($files as $idx => $fileRec) {
            if (array_key_exists('indexFile', $fileRec)) {
                $indexFilepath = $fileRec['indexFile'];
                $filesRaw = file_get_contents($indexFilepath);
                $files2 = json_decode ($filesRaw);
                checkForJSONerrors ($filesRaw, $indexFilepath, '"null"');
            } else if (array_key_exists('files', $fileRec)) {
                $files2 = $fileRec['files'];
            }
            $indexType = $fileRec['type'];

            switch ($indexType) {
                case 'css': $lineSrc = "\t".'<link type="text/css" rel="StyleSheet" href="{$src}?c={$changed}">'."\r\n"; break;
                case 'javascript': $lineSrc = "\t".'<script type="text/javascript" src="{$src}?c={$changed}"></script>'."\r\n"; break;
            };

            //echo '<pre>'; var_dump ($files2); var_dump ($this->path); echo '</pre>';

            foreach ($files2 as $idx => $file) {
                $file = str_replace ('apps/{$domain}', 'apps/'.$this->viewsMID, $file);
                $file = str_replace ('apps/'.$this->domainFolder, 'apps/'.$this->viewsMID, $file);
                $file = str_replace ('{$domain}', $this->domainFolder, $file);
                if (strpos($file,'{$webPath}')!==false) {
                    $file = str_replace ('{$webPath}', $this->webPath, $file);
                    $file = str_replace ($this->path,'',$file);
                    $fp = $this->path.$file;
                } elseif (strpos($file,'{$domainPath}')!==false) {
                    $file = str_replace ('{$domainPath}', $this->domainPath, $file);
                    $file = str_replace ($this->path,'',$file);
                    $fp = $file;
                } else {
                    $file = str_replace ($this->path,'',$file);
                    $fp = $this->path.$file;
                }
                $oFile = $file;
                //echo '<pre style="color:blue;background:white">'.$file; var_dump (file_exists($file)); echo '</pre>';
                //var_dump($this->path);var_dump($file);var_dump($fp); die();
                if ($fp) {
                    $url = str_replace ($_SERVER['DOCUMENT_ROOT'], '', $fp);
                    $url = str_replace ($this->path,'',$url);
                    $url = str_replace ($this->webPath,'',$url);
                    $url = str_replace (realpath($this->path.'/..'),'',$url);
                    if (substr($url,0,1)!=='/') $url = '/'.$url;

                    //if (strpos($file,'index.css')!==false) { echo '<pre>'; var_dump ($file); echo PHP_EOL; var_dump ($url); echo '</pre>'; };
                    if (strpos($url, 'zingtouch')!==false) $lineSrc = "\t".'<script type="module"> import { ZingTouch } from \'{$src}?c={$changed}\';</script>'."\r\n"; else {
                        switch ($indexType) {
                            case 'css': $lineSrc = "\t".'<link type="text/css" rel="StyleSheet" href="{$src}?c={$changed}">'."\r\n"; break;
                            case 'javascript': $lineSrc = "\t".'<script type="text/javascript" src="{$src}?c={$changed}"></script>'."\r\n"; break;
                        };

                    }
                    $search = array ('{$src}', '{$changed}');
                    $replace = array ($url, date('Ymd_His', filemtime($fp)));
                    $lines .= str_replace ($search, $replace, $lineSrc);
                } else {
                    $errMsg = '<p>File "'.$fp.'" is missing (oFile='.$oFile.'), referenced from <span class="naCMS_getLinksFileRec">'.json_encode($fileRec).'</span>.</p>';
                    echo $errMsg;
                    //backtrace();
                    //echo '<pre>'; echo json_encode(debug_backtrace(),JSON_PRETTY_PRINT); echo '</pre>';
                    trigger_error ($errMsg, E_USER_NOTICE);
                }
            }
        }
        //echo '<pre>'; var_dump ($files2); echo '</pre><pre>'; var_dump (htmlentities($lines)); echo '</pre>'; die();
        return $lines;
    }

    public function getConcatenatedLinks ($files) {
        $lines = '';
        $latestChange = 0;
        $indexType = '';
        foreach ($files as $idx => $fileRec) {
            if (array_key_exists('indexFile', $fileRec)) {
                $indexFilepath = $fileRec['indexFile'];
                $filesRaw = file_get_contents($indexFilepath);
                $files = json_decode ($filesRaw);
                checkForJSONerrors ($filesRaw, $indexFilepath, '"null"');
            } else if (array_key_exists('files', $fileRec)) {
                $files = $fileRec['files'];
            }

            if (
                $indexType!==''
                && $indexType!==$fileRec['type']
            ) trigger_error ($this->cn.'::getConcatenatedLinks() : "type" must be identical for all $files.', E_USER_ERROR);
            $indexType = $fileRec['type'];

            foreach ($files as $idx => $file) {
                //trigger_error ($file.' (1)', E_USER_NOTICE);
                $oFile = $file;
                $file = str_replace ('apps/{$domain}', 'apps/'.$this->viewsMID, $file);
                $file = str_replace ('apps/'.$this->domainFolder, 'apps/'.$this->viewsMID, $file);
                $file = str_replace ('{$domain}', $this->domainFolder, $file);
                $file = str_replace ('{$webPath}', $this->webPath, $file);
                //trigger_error ($file.' (2)', E_USER_NOTICE);
                if (file_exists($this->path.'/'.$file)) {
                    $url = str_replace ($this->path,'',$file);
                    $fileLastChanged = filemtime($this->path.'/'.$file);
                    if ($fileLastChanged > $latestChange) $latestChange = $fileLastChanged;
                    //$search = array ('{$src}', '{$changed}');
                    //$replace = array ($url, date('Ymd_His', $fileLastChanged));
                    //$lines .= str_replace ($search, $replace, $lineSrc);
                } else {
                    trigger_error ('file "'.$this->path.'/'.$file.'" is missing (oFile='.$oFile.'), referenced from <span class="naCMS_getLinksFileRec">'.json_encode($fileRec).'</span>.', E_USER_NOTICE);
                }
            }
        }

        switch ($indexType) {
            case 'css': $lineSrc = "\t".'<link defer type="text/css" rel="StyleSheet" href="{$src}">'."\r\n"; break;
            case 'javascript': $lineSrc = "\t".'<script defer type="text/javascript" src="{$src}"></script>'."\r\n"; break;
        };
        $url = '/NicerAppWebOS/ajax_getConcatenatedFiles.php?indexType='.$indexType.'&c='.date('Ymd-His',$latestChange);
        $lines .= str_replace ('{$src}', $url, $lineSrc);

        return $lines;
    }

    public function getConcatenatedLinksContent ($files) {
        $r = '';
        foreach ($files as $idx => $fileRec) {
            if (array_key_exists('indexFile', $fileRec)) {
                $indexFilepath = $fileRec['indexFile'];
                $filesRaw = file_get_contents($indexFilepath);
                $files = json_decode ($filesRaw);
                checkForJSONerrors ($filesRaw, $indexFilepath, '"null"');
            } else if (array_key_exists('files', $fileRec)) {
                $files = $fileRec['files'];
            }
            $indexType = $fileRec['type'];

            $cacheFile = $this->path.'/domainConfig/'.$this->domainFolder.'/cache.'.$indexType.'.'.$_GET['c'].'.txt';
            if (file_exists($cacheFile)) return file_get_contents($cacheFile);

            foreach ($files as $idx => $file) {
                //trigger_error ($file.' (1)', E_USER_NOTICE);
                $oFile = $file;
                $file = str_replace ('apps/{$domain}', 'apps/'.$this->viewsMID, $file);
                $file = str_replace ('apps/'.$this->domainFolder, 'apps/'.$this->viewsMID, $file);
                $file = str_replace ('{$domain}', $this->domainFolder, $file);
                $file = str_replace ('{$webPath}', $this->webPath, $file);

                //trigger_error ($file.' (2)', E_USER_NOTICE);
                if (file_exists($this->path.'/'.$file)) {
                    $r .= file_get_contents($this->path.DIRECTORY_SEPARATOR.$file).PHP_EOL.PHP_EOL;
                } else {
                    trigger_error ('file "'.$this->path.'/'.$file.'" is missing (oFile='.$oFile.'), referenced from <span class="naCMS_getLinksFileRec">'.json_encode($fileRec).'</span>.', E_USER_NOTICE);
                }
            }
        }


        $url = 'https://www.toptal.com/developers/javascript-minifier/api/raw';
        if ($indexType=='javascript') {
            // init the request, set various options, and send it
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ["Content-Type: application/x-www-form-urlencoded"],
                CURLOPT_POSTFIELDS => http_build_query([ "input" => $r ])
            ]);

            $r = curl_exec($ch);

            // finally, close the request
            curl_close($ch);
        }
        file_put_contents ($cacheFile, $r);

        return $r;
    }

    public function getVividButtonCSSfiles () {
        $path = $this->path.'/NicerAppWebOS/businessLogic/vividUserInterface/v6.y.z/2D/button-4.2.0';
        if (!file_exists($path)) {
            $msg = $this->cn.'->getVividButtonCSSfiles() : $path='.$path.' does not exist.';
            trigger_error ($msg, E_USER_ERROR);
            echo $msg;
            exit();
        }

        $files = getFilePathList ($path, false, '/btn_.*\.css/', null, array('file'), 1, 1, true)['files'];
        foreach ($files as $idx => $file) {
            $files[$idx] = str_replace($this->path, '', $file['realPath']);
        }
        sort($files);
        return array_merge ([ '{$webPath}/NicerAppWebOS/businessLogic/vividUserInterface/v6.y.z/2D/button-4.2.0/themes.css' ], $files);
    }

    public function getVividButtonJavascriptFiles () {
        $path = $this->path.'/NicerAppWebOS/businessLogic/vividUserInterface/v6.y.z/2D/button-4.2.0';
        if (!file_exists($path)) {
            $msg = $this->cn.'->getVividButtonCSSfiles() : $path='.$path.' does not exist.';
            trigger_error ($msg, E_USER_ERROR);
            echo $msg;
            exit();
        }

        $files = getFilePathList ($path, false, '/btn_.*\.source\.js/', null, array('file'), 1, 1, true)['files'];
        foreach ($files as $idx => $file) {
            $files[$idx] = str_replace($this->path, '', $file);
        }
        sort ($files);
        return $files;
    }



    public function html($relativeIndentLevel, $html) {
        $indent = '';
        for ($i=0; $i < $this->baseIndentLevel + $relativeIndentLevel; $i++) $indent .= "\t";
        return $indent.$html.PHP_EOL;
    }

    public function html_vividButton (
        $relativeIndentLevel, $containerStyle,

        $id,
        $class, $subClassSuffix, $iconComponents_subClassSuffix,
        $buttonStyle,
        $button_event_onclick,
        $button_event_onmouseover,
        $button_event_onmouseout,

        $buttonTabIndex, $buttonTitleAlt,

        $borderImgSrc,
        $tileImgSrc,
        $buttonBGimgSrc,
        $buttonImgSrc,

        $buttonOverlayHTML,

        $buttonText,
        $buttonText_class,
        $buttonText_style
    ) {
        $il = $relativeIndentLevel;
        if ($buttonTitleAlt!== $buttonText) $buttonTitleAlt2 = $buttonTitleAlt; else $buttonTitleAlt2 = '';
        if (is_string($buttonText) && $buttonText!=='') $containerStyle = 'display:flex;'.$containerStyle;
        $r  = $this->html($il, '<div id="'.$id.'_container" class="'.str_replace('vividButton_icon','vividButton_container',$class).'" tabindex="'.$buttonTabIndex.'" style="'.$containerStyle.'" onclick="'.$button_event_onclick.'" onmouseover="'.$button_event_onmouseover.'" onmouseout="'.$button_event_onmouseout.'" title="'.$buttonTitleAlt2.'" alt="'.$buttonTitleAlt.'">');
        $r .= $this->html($il+1, '<div id="'.$id.'" class="'.$class.' tooltip" title="'.$buttonTitleAlt2.'" tabindex="'.$buttonTabIndex.'" style="'.$buttonStyle.'">');
        $r .= $this->html($il+2,    '<div class="vividButton_icon_borderCSS'.$subClassSuffix.' '.$iconComponents_subClassSuffix.'"></div>');
        if (!is_null($borderImgSrc)) $r .= $this->html($il+2,    '<img class="vividButton_icon_imgBorder'.$subClassSuffix.' '.$iconComponents_subClassSuffix.'" srcPreload="/siteMedia/'.$borderImgSrc.'"/>');
        if (!is_null($tileImgSrc)) $r .= $this->html($il+2,    '<img class="vividButton_icon_imgTile'.$subClassSuffix.' '.$iconComponents_subClassSuffix.'" srcPreload="/siteMedia/'.$tileImgSrc.'"/>');
        if (!is_null($buttonBGimgSrc)) $r .= $this->html($il+2,    '<img class="vividButton_icon_imgButtonIconBG'.$subClassSuffix.' '.$iconComponents_subClassSuffix.'" srcPreload="/siteMedia/'.$buttonBGimgSrc.'"/>');
        if (!is_null($buttonImgSrc)) $r .= $this->html($il+2,    '<img class="vividButton_icon_imgButtonIcon'.$subClassSuffix.' '.$iconComponents_subClassSuffix.'" srcPreload="/siteMedia/'.$buttonImgSrc.'"/>');
//        var_dump($buttonImgSrc); echo '<img class="vividButton_icon_imgButtonIcon'.$subClassSuffix.' '.$iconComponents_subClassSuffix.'" srcPreload="/siteMedia/'.$buttonImgSrc.'"/>'; die();
        if (!is_null($buttonOverlayHTML)) $r .= $this->html($il+2,    $buttonOverlayHTML);
        $r .= $this->html($il+1, '</div>');
        if (is_string($buttonText) && $buttonText!=='') {
            $textPartSuffix = '_text';
            $r .= $this->html($il+1, '<div id="'.$id.$textPartSuffix.'" class="vividButton_icon'.$subClassSuffix.'_text '.$buttonText_class.'" style="'.$buttonText_style.'" tabindex="'.$buttonTabIndex.'" title="'.$buttonTitleAlt2.'" alt="'.$buttonTitleAlt.'">');
            $r .= $this->html($il+2,    '<div>'.$buttonText.'</div>');
            $r .= $this->html($il+1, '</div>');
        }
        $r .= $this->html($il, '</div>');
        return $r;
    }

    public function html_vividTabPage (
        $relativeIndentLevel, $containerStyle,

        $id,
        $container_class, $container_style, $container_title, $container_alt,
        $container_event_onclick, $container_event_onmouseover, $container_event_onmouseout,

        $tabPages_title, $tabPages_title_style,
        $header_class, $header_style, $header_title, $header_alt,
        $header_event_onclick, $header_event_onmouseover, $header_event_onmouseout,
        $header_buttons,

        $content_class, $content_style, $content_title, $content_alt,
        $content_event_onclick, $content_event_onmouseover, $content_event_onmouseout,
        $tabPages_content
    ) {
        $il = $relativeIndentLevel;
        $r = $this->html ($il,
            '<div id="'.$id.'" class="vividTabPage '.$container_class.'" style="display:flex;'.$container_style.'" '
            .'onclick="'.$container_event_onclick.'" onmouseover="'.$container_event_onmouseover.'" onmouseout="'.$container_event_onmouseout.'" '
            .'title="'.$container_title.'" alt="'.$container_alt.'">'
        );
            $r .= $this->html ($il+1,
                '<div id="'.$id.'_header" class="vividTabPage_header '.$header_class.'" style="'.$header_style.'" '
                .'onclick="'.$header_event_onclick.'" onmouseover="'.$header_event_onmouseover.'" onmouseout="'.$header_event_onmouseout.'" '
                .'title="'.$header_title.'" alt="'.$header_alt.'">'
            );
                $r .= $this->html ($il+2, '<div style="order:-1;'.$tabPages_title_style.'">'.$tabPages_title.'</div>');
                $r .= $this->html ($il+2, $header_buttons);
            $r .= $this->html ($il+1, '</div>');
            $r .= $this->html ($il+1,
                '<div id="'.$id.'_content" class="vividTabPage_content vividScrollpane '.$content_class.'" style="'.$content_style.'" '
                .'onclick="'.$content_event_onclick.'" onmouseover="'.$content_event_onmouseover.'" onmouseout="'.$content_event_onmouseout.'" '
                .'title="'.$content_title,'" alt="'.$content_alt.'">'
            );
                $r .= $this->html ($il+2, $content);
            $r .= $this->html ($il+1, '</div>');
        $r .= $this->html ($il, '</div>');
        return $r;
    }

    public function getSiteMenu() {
        global $naWebOS;
        $contentFile = $naWebOS->domainPath.'/domainConfig/'.$this->domainFolder.'/mainmenu.php';
        //var_dump ($contentFile); exit();
        $content = require_return ($contentFile, false);
        //var_dump ($content); echo '-.0.-'; exit();
        return $content;
    }

    public function getPageCSS_checkPermissions($d) {
        global $naDebugAll;
        global $naLAN;
        $debug = $this->debugThemeLoading;
        $db = $this->dbs->findConnection('couchdb');
        $viewFolder = '[UNKNOWN VIEW]';

        $selectors2 = $d['selectors'];
        if ($debug) { echo '<pre style="color:yellow;background:darkred;margin:10px;padding:8px;border-radius:10px;">$selectors2='; var_dump ($selectors2); }
        $selectorNames = $d['selectorNames'];
        //$debug = true;
        foreach ($selectors2 as $idx => $selector) {
            if ($debug) { echo $idx.'<br/>'.PHP_EOL; };

            $permissions = $selector['permissions'];
            if ($debug) { echo '<pre style="color:cyan;background:navy;">$permissions='; var_dump ($permissions); echo '</pre>'; }

            foreach (['read','write'] as $idx3=>$pt) {
            $hasPermission = false;
            foreach ($permissions as $permissionType => $permissionsRec) {
                if ($permissionType==$pt) {
                    foreach ($permissionsRec as $accountType => $accountsList) {
                        if ($debug) { echo 't666b1:<pre style="color:white;background:navy;">'; var_dump ($permissionsRec); echo '</pre>'; };
                        foreach ($accountsList as $idx2 => $userOrGroupID) {
                            if ($accountType == 'users') {
                                $adjustedUserOrGroupID = $db->translate_plainUserName_to_couchdbUserName($db->translate_couchdbUserName_to_plainUserName($userOrGroupID));
                            } else {
                                $adjustedUserOrGroupID = $db->translate_plainGroupName_to_couchdbGroupName($db->translate_couchdbGroupName_to_plainGroupName($userOrGroupID));
                            }
                            $adjustedUserOrGroupID = $userOrGroupID; // TODO : check if this is necessary

                            if ($debug)
                            { echo '<pre style="color:white;background:blue;margin:10px;padding:5px;">t666b2='; var_dump($accountType); echo PHP_EOL; var_dump ($userOrGroupID); echo PHP_EOL; var_dump ($adjustedUserOrGroupID); echo PHP_EOL.PHP_EOL; echo 't666='; var_dump ($this->dbs->findConnection('couchdb')->username); echo '</pre>';}
                            if ($accountType == 'roles') {
                                if ($debug) {
                                    echo '$this->dbs->username='; var_dump($this->dbs->findConnection('couchdb')->username).'<br/>'.PHP_EOL;
                                    echo '$this->dbs->roles='; var_dump($this->dbs->findConnection('couchdb')->roles).'<br/>'.PHP_EOL;

                                };
                                if (is_string($this->dbs)) {
                                    echo $fncn.' : WARNING : invalid database connection ($this->dbs='.json_encode($this->dbs).').';
                                    //exit(); // or exit();
                                }

                                if (!is_null($this->dbs->findConnection('couchdb')->roles))
                                foreach ( $this->dbs->findConnection('couchdb')->roles
                                    as $roleIdx => $groupID
                                ) {
                                    if ($debug) { echo 't667='; var_dump($groupID); };
                                    if ($adjustedUserOrGroupID==$groupID) {
                                        $hasPermission = true;
                                    }
                                }
                            }
                            if (
                                $accountType == 'users'
                                && $this->dbs->findConnection('couchdb')->username
                                    == $adjustedUserOrGroupID
                            ) {
                                $hasPermission = true;
                                if ($debug) { echo 't777 $username='.$this->dbs->findConnection('couchdb')->username.'<br/>'.PHP_EOL; }
                            }
                        }
                    }

                    if ($hasPermission)
                        $selectors2[$idx]['has_'.$pt.'_permission'] = true;
                    else
                        $selectors2[$idx]['has_'.$pt.'_permission'] = false;
                }
            }

            }
            //echo '<pre style="background:purple;color:white;font-weight:bold;">'.$idx.'</pre>'.PHP_EOL;
        }

        if ($debug) {echo '<pre style="color:lime;background:blue;border-radius:10px;">'; var_dump ($selectors2); echo '</pre></pre>'; exit();}

        return [
            'selectors' => $selectors2,
            'selectorNames' => $selectorNames,
            //'preferredSelectorName' => $d['preferredSelectorName']
        ];
    }

    public function echoDebugData ($objectOrArray, $msg, $extraClasses) {
        $a = $objectOrArray;
        echo PHP_EOL.PHP_EOL.'<p class="naDebugData"><h2>'.$msg.'</h2></p>'.PHP_EOL;
        echo '<pre class="naDebugData '.$extraClasses.'">';
        echo json_encode ($a, JSON_PRETTY_PRINT);
        echo '</pre>';
    }

    public function getPageCSS($js=true, $doSetSpecificity=true, $doIncludeClientOnlyThemes=true, $stickToCurrentSpecificity=false, $specificityName=null) {
        $fncn = $this->cn.'->getPageCSS';

        global $naDebugAll;
        global $naLAN;
        global $naIP;
        global $naUsername;
        global $naIsBot;
        $debug = $this->debugThemeLoading;
        if ($debug) { echo '<h1>'.$fncn.'</h1>'.PHP_EOL; }

        $viewFolder = '[UNKNOWN VIEW]';
        $db = $this->dbs->findConnection('couchdb');

        $d1 = $this->getPageCSS_permissionsList($js);
        if ($debug) {
            //echo '<pre style="color:yellow;background:navy">'; var_dump ($d1['selectors']); echo '</pre>';
            //$this->echoDebugData ($d1['selectors'], $fncn.' : 1', 'naDebugData_getPageCSS');
        };
        $d2 = $this->getPageCSS_checkPermissions($d1);
        $selectors = $d2['selectors'];
        if ($debug) {
            echo '<pre style="color:lime;background:green">'; var_dump ($selectors); echo '</pre>';
            // $this->echoDebugData ($selectors, $fncn.' : 2', 'naDebugData_getPageCSS');
        };

        $selectorsCachedFN = 'getPageCSS_'.randomString(50);
        $id = encode_base64_url(json_encode($selectors));
        $id2 = encode_base64_url(gzencode(json_encode($selectors)));
        //$selectorsCachedFilepath = realpath(dirname(__FILE__).'/../..').'/NicerAppWebOS/siteCache/'.$selectorsCachedFN;
        $selectorsCachedFilepath = $_SERVER['DOCUMENT_ROOT'].'/siteCache/'.$selectorsCachedFN;

        // TODO : maybe re-enable this
        //$c = $this->getPageCSS_cached($id2, $selectorsCachedFN);
        //if (is_string($c) && $c!=='') return $c;

        //echo '<pre style="color:lime;background:green">'; var_dump ($selectors); echo '</pre>'; exit();
        $selectorNames = &$d['selectorNames'];
        $specificityName = 'current page for user '.$db->username.' at the client';

        $_SESSION['selectors'] = json_encode($selectors);
        //$_SESSION['selectorNames'] = json_encode($selectorNames);
        //echo '<pre>'; var_dump ($_SESSION);

        $selectors2 = array_reverse($selectors, true);
        //echo '<pre>'; var_dump ($selectors2); echo '</pre>'; exit();
        //$selectorNames2 = array_reverse($selectorNames, true);

        $ret = '';
        $hasJS = false;
        $hasCSS = false;
        //if ($debug)
        //echo '<pre>';var_dump ($selectors); exit();


        $mySpecificityName = null;
        foreach ($selectors2 as $idx => $selector) {
            if ($debug) { echo '<pre style="color:yellow;background:blue;padding:5px;margin:10px;border-radius:10px;"><h2>'.$idx.'</h2>'.PHP_EOL.PHP_EOL.json_encode ($selector,JSON_PRETTY_PRINT).'</pre>'.PHP_EOL.PHP_EOL; }
            if (
                !array_key_exists('has_read_permission',$selector)
                || !$selector['has_read_permission']
            ) continue;

            if (
                !$doIncludeClientOnlyThemes
                && strpos($selector['specificityName'], ' client')!==false
            ) continue;

            if (
                $stickToCurrentSpecificity
                && $selector['specificityName']!==$specificityName
            ) continue;

            $css = $this->getPageCSS_specific($selector);
            if ($debug) { echo '<pre style="color:darkred;background:yellow;padding:5px;margin:10px;border-radius:10px;">$css='.PHP_EOL.PHP_EOL; var_dump($css); echo '</pre>'; }

            $hasData = false;
            if (is_array($css)) {
                foreach ($css['themes'] as $themeName => $theme) { $hasData = true; break; };

                if (!$mySpecificityName)
                $mySpecificityName = (
                    array_key_exists($themeName, $css['themes'])
                    && array_key_exists('specificityName', $css['themes'][$themeName])
                    ? $css['themes'][$themeName]['specificityName']
                    : $selector['specificityName']
                );
            } else {
                if (!$mySpecificityName) $mySpecificityName = $selector['specificityName'];
                $hasData = is_string($css);
            }
            $selectors2[$idx]['hasData'] = $hasData;
            //{ echo '<pre>667 : $idx='.$idx; echo '$selector='; var_dump($selector); var_dump($css); var_dump($hasData); echo '</pre>'; };
            if ($debug) {
                echo '<h1>'.$specificityName.'</h1>'; echo PHP_EOL;
                echo '<pre style="color:yellow;background:navy;">'; var_dump ($selector); echo '</pre>';
                /*
                if (
                    is_array($css)
                    && array_key_exists ($themeName, $css['themes'])
                    //&& array_key_exists ('specificityName', $css['themes'][$themeName])
                ) var_dump ($css['themes'][$themeName]);
                */
                $dbg = [
                    '$idx' => $idx,
                    '$css' => $css,
                    '$hasJS' => $hasJS,
                    '$hasCSS' => $hasCSS,
                    '$selector' => $selector//,
                    //'$selectorL1' => $selectorL1
                ];
                $this->echoDebugData ($dbg, $fncn.' : 3', 'naDebugData_getPageCSS');

            }
        }
        foreach ($selectors2 as $idx => $selector) {
            $css = $this->getPageCSS_specific($selector);

            $hasData = false;
            if (is_array($css) && count($css)>0) {
                foreach ($css['themes'] as $themeName => $theme) { $hasData = true; break; };

                $specificityName = (
                    array_key_exists($themeName, $css['themes'])
                    && array_key_exists('specificityName', $css['themes'][$themeName])
                    ? $css['themes'][$themeName]['specificityName']
                    : $selector['specificityName']
                );
            } else {
                $specificityName = $selector['specificityName'];
                $hasData = is_string($css);
                //if ($hasData) {var_dump ($css);exit();};
            }


            $fn = __DIR__.'/../version.json.php';
            $fn2 = __DIR__.'/../version.json';
            if ($reloadVersionInfo || !file_exists($fn2)) {
                $json = execPHP($fn);
                $this->about = json_decode($json,true);
                file_put_contents ($fn2, $json);
            } else {
                $this->about = json_decode(file_get_contents($fn2),true);
            }
            $this->version = $this->about;


            //if (is_array($css)) $css = json_encode($css, JSON_PRETTY_PRINT);
            //$_SESSION['selectorName'] = $selectorNames[$idx];
            //$_SESSION['preferredSelectorName'] = &$d['preferredSelectorName'];
            //if ($debug) { echo '$selector=';var_dump($selector); echo '<br/>$_SESSION='; var_dump ($_SESSION); echo PHP_EOL.PHP_EOL; }
            if (
                !$hasJS
                && $js === true
                && (
                    is_array($css)
                    || is_string($css)
                )
                && array_key_exists('display',$selector)
                && $selector['display']!==false
            ) {
                $hasJS = true;
                $r = '<script id="jsPageSpecific" type="text/javascript">'.PHP_EOL;
                $r .= '// debug1'.PHP_EOL;
                $useVividTexts = !array_key_exists('uvt',$_GET) || $_GET['uvt']=='y' ? 'true' : 'false';
                $useLoadContent = !array_key_exists('lc',$_GET) || $_GET['lc']=='y' ? 'true' : 'false';
                $reloadVersionInfo = !array_key_exists('vi',$_GET) || $_GET['vi']=='y' ? false:true;

                // //echo '<pre style="color:green">'; var_dump ($css); echo '</pre>'; exit();
                $_SESSION['themeName'] = $themeName;
                $_SESSION['specificityName'] = $selector['specificityName'];
                if ($debug) { echo '<pre style="color:green">'; var_dump ($selectors2); echo '</pre>'; exit(); }

                //echo '<pre>'; var_dump ($this); exit();

                $r .= 'if (!naGlobals) { var naGlobals = {}};'.PHP_EOL;
                $r .= 'naGlobals = $.extend(naGlobals, {'.PHP_EOL;
                    //$r .= "\tdebug : ".json_encode($dbg).",".PHP_EOL;
                    $r .= "\tbackground : '".$theme['background']."',".PHP_EOL;
                    $r .= "\tbackgroundSearchKey : '".$theme['backgroundSearchKey']."',".PHP_EOL;
                    $r .= "\tclientIP : '".$naIP."',".PHP_EOL;
                    $r .= "\tclientUsername : '".$naUsername."',".PHP_EOL;
                    $r .= "\tdomain : '".$this->domain."',".PHP_EOL;
                    $r .= "\tdomainFolder : '".$this->domainFolder."',".PHP_EOL;
                    $r .= "\thasDB : ".($this->hasDB ? 'true' : 'false').','.PHP_EOL;
                    $r .= "\tisBot : ".($naIsBot ? 'true' : 'false').','.PHP_EOL;
                    $r .= "\tnaLAN : ".($naLAN ? 'true' : 'false').','.PHP_EOL;
                    $r .= "\tnaHasErrors : ".((array_key_exists('naErrors',$_SESSION) && is_string ($_SESSION['naErrors']) && $_SESSION['naErrors']!=='') ? 'true' : 'false').','.PHP_EOL;
                    //$r .= "\tspecificityName : \"".$specificityName."\",".PHP_EOL;
                    //$r .= "\tspecificityName_revert : \"".$specificityName."\",".PHP_EOL;
                    //echo '<pre style="background:navy;color:lime;border-radius:10px;">'; var_dump ($css); echo '</pre>';
                    $r .= "\tspecificityName : \"".$mySpecificityName."\",".PHP_EOL;
                    $r .= "\tspecificityNames : ".json_encode($selectorNames).",".PHP_EOL;
                    $r .= "\tthemes : ".json_encode($css['themes'], JSON_PRETTY_PRINT).",".PHP_EOL;
                    $r .= "\tthemesDBkeys : ".json_encode($selectors2, JSON_PRETTY_PRINT).",".PHP_EOL;
                    $r .= "\tthemeName : '".$themeName."',".PHP_EOL;
                    $r .= "\tuseVividTexts : ".$useVividTexts.",".PHP_EOL;
                    $r .= "\tuseLoadContent : ".$useLoadContent.",".PHP_EOL;
                    $r .= "\tview : ".json_encode($this->view, JSON_PRETTY_PRINT).",".PHP_EOL;
                    $r .= "\tversion : ".file_get_contents(__DIR__.'/../version.json').PHP_EOL;
                $r .= '});'.PHP_EOL;
                  //  $r .= 'debugger;'.PHP_EOL;
                if (
                    strpos($_SERVER['SCRIPT_NAME'], '/index.php')!==false
                    || strpos($_SERVER['SCRIPT_NAME'], '/ajax_get_content.php')!==false
                ) {
                    $r .= 'if (!naGlobals) { var naGlobals = {}}  ;';
                    $r .= 'naGlobals = $.extend(naGlobals, {'.PHP_EOL;
                        $r .= "\tdomain : '".$this->domain."',".PHP_EOL;
                        $r .= "\tdomainFolder : '".$this->domainFolder."',".PHP_EOL;
                        $r .= "\tapp : '".json_encode($this->view).'\','.PHP_EOL;
                        if (array_key_exists('apps',$_GET)) $r .= "\tapps : ".json_encode($_GET['apps']).PHP_EOL;
                    $r .= '});'.PHP_EOL;
                    $r .= '$(document).ready(function() {'.PHP_EOL;
                        $r .= 'if (!na.site.globals.themes) na.site.globals  = $.extend(na.site.globals, naGlobals);'.PHP_EOL;
                        $r .= 'if (!na.site.settings.url) na.site.settings = $.extend(na.site.settings, {'.PHP_EOL;
                            $r .= "\turl : ".json_encode($this->url).PHP_EOL;
                        $r .= "});".PHP_EOL;
                    $r .= '});'.PHP_EOL;
                };


                if ($doSetSpecificity) {
                    $r .= '$(document).ready(function() {'.PHP_EOL;
                    //$r .= "\tna.m.waitForCondition('HTML BODY : document.ready -> na.site.setSpecificity', na.m.HTMLidle, na.site.setSpecificity, 50);".PHP_EOL;
                    //$r .= "\tna.site.setSpecificity();".PHP_EOL;
                    $r .= "});".PHP_EOL;
                }
                $r .= '</script>'.PHP_EOL;
                $ret = $r.$ret;
            };

            if ((is_array($css) || is_string($css)) && !$hasCSS) {
                //echo '<pre>'; var_dump ($css); exit();
                $hasCSS = true;
                foreach ($css['themes'] as $themeName => $theme) { break; };
                //echo '<pre style="color:blue;">'; var_dump ($selector); exit();
                //$r = '<script type="text/javascript">'.PHP_EOL;
                //$r .= "\tna.site.globals = $.extend(na.site.globals, {".PHP_EOL;
                //$r .= "\t\tthemeName : '".$css['theme']."'".PHP_EOL;
                //$r .= "\t});".PHP_EOL;
                //$r .= '</script>'.PHP_EOL;
                $r = '';
                $r = '<style id="cssPageSpecific" type="text/css" theme="'.$theme['theme'].'" sel=\''.(json_encode($css['sel'])).'\' csn="'.$selector['specificityName'].'" dbID="'.$theme['dbID'].'">'.PHP_EOL;
                //echo '<pre style="color:green">'; var_dump ($theme); echo '</pre>'; exit();
                $r .= css_array_to_css2($theme['themeSettings']).PHP_EOL;


                //echo css_array_to_css2($theme['themeSettings']); exit();

                $this->theme = $theme;
                //$r .= 'h1::before, h2::before, h3::before {'."\r\n".PHP_EOL;
                    //$r .= "\t".'content : \'\''."\r\n".PHP_EOL;
                //$r .= '}'."\r\n".PHP_EOL;
                $r .= '#divFor_neCompanyLogo, #headerSiteDiv:not(.backdropped, .vividTextCSS), span:is(.backdropped), p:is(.backdropped) {'."\r\n".PHP_EOL;
                    $r .= "\t".'background : rgba(0,0,0,'.$theme['textBackgroundOpacity'].');'."\r\n".PHP_EOL;
                $r .= '}'."\r\n".PHP_EOL;
                /*
                $r .= '.mce-content-body h1, .mce-content-body h2, .mce-content-body h3, .mce-content-body p {';
                $r .= "\t".'background : inherit !important;';
                $r .= '}'."\r\n".PHP_EOL;
                */

                $fn = dirname(__FILE__).'/../themes/nicerapp_default_animations__'.$themeName.'.css';
                if (file_exists($fn) && is_readable($fn)) {
                    $theme['animations'] = css_keyframes_to_array( file_get_contents($fn) );
                    //echo '<pre class="css_keyframes_to_array">'; var_dump ($css['animations']); echo '</pre>';//exit();

                    $a1 = css_animation_template_to_animation (
                        $themeName, $theme['animations'],
                        [
                            'naHS_l0_in', 'naHS_l1_in', 'naHS_l2_in', 'naHS_json_in',
                            'naHS_releaseDate_l0_in', 'naHS_releaseDate_l1_in', 'naHS_releaseDate_l2_in'
                        ],
                        [ '0%' => [
                            'background' => [
                                [
                                    'search' => '/rgba\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*([\d\.])+\)/',
                                    'replace' => 'rgba($1, $2, $3, '.$theme['textBackgroundOpacity'].')'
                                ],
                                [
                                    'search' => '/rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)/',
                                    'replace' => 'rgba($1, $2, $3, '.$theme['textBackgroundOpacity'].')'
                                ]
                            ]
                        ]]
                    );
                    //echo '<pre style="background:darkred; color:black;">'; var_dump ($a1); echo '</pre>'; exit();
                    $a2 = css_animation_template_to_animation (
                        $themeName, $theme['animations'],
                        [
                            'naHS_l0_out', 'naHS_l1_out', 'naHS_l2_out', 'naHS_json_out',
                            'naHS_releaseDate_l0_out', 'naHS_releaseDate_l1_out', 'naHS_releaseDate_l2_out'
                        ],
                        [ '100%' => [
                            'background' => [
                                [
                                    'search' => '/rgba\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*([\d\.])+\)/',
                                    'replace' => 'rgba($1, $2, $3, '.$theme['textBackgroundOpacity'].')'
                                ],
                                [
                                    'search' => '/rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)/',
                                    'replace' => 'rgba($1, $2, $3, '.$theme['textBackgroundOpacity'].')'
                                ]
                            ]
                        ]
                    ]);

                    $r .= css_animation_array_to_css (array_merge($a1,$a2));
                    //echo '<pre style="background:darkred; color:white;">'; var_dump (htmlentities($r)); echo '</pre>'; exit();

                    $r .= css_animation_keys_to_css (
                        $themeName,
                        [
                            'li.todoList > div.active' => 'naHS_l0_in',
                                // the above key-value pair with a $css['theme']=='default', results in the following CSS rule :
                                /*
                                * li.todoList > div.active {
                                *      animation : naHS_l0_in__default 1s forwards;
                                * }
                                */

                            'li.todoList > div' => 'naHS_l0_out',

                            //'.contentSectionTitle3.active' => 'naHS_l0_in',
                            //'.contentSectionTitle3.in-active' => 'naHS_l0_out',
                            'p.todoList.active' => 'naHS_l0_in',
                            'p.todoList.in-active' => 'naHS_l0_out',
                            'h1.todoList.active' => 'naHS_l0_in',
                            'h1.todoList.in-active' => 'naHS_l0_out',
                            'h2.todoList.active' => 'naHS_l0_in',
                            'h2.todoList.in-active' => 'naHS_l0_out',
                            'h3.todoList.active' => 'naHS_l0_in',
                            'h3.todoList.in-active' => 'naHS_l0_out',
                            '.todoList_l1 > li > div.active' => 'naHS_l1_in',
                            '.todoList_l1 > li > div' => 'naHS_l1_out',
                            '.todoList_l2 > li > div.active' => 'naHS_l2_in',
                            '.todoList_l2 > li > div' => 'naHS_l2_out',

                            '.todoList_l1 > li > pre.json.active' => 'naHS_l1_in',
                            '.todoList_l1 > li > pre.json' => 'naHS_l1_out',

                            '.todoList_l2 > li > pre.json.active' => 'naHS_l2_in',
                            '.todoList_l2 > li > pre.json' => 'naHS_l2_out',

                            'li.todoList.releaseDate > div.active' => 'naHS_releaseDate_l0_in',
                            'li.todoList.releaseDate > div' => 'naHS_releaseDate_l0_out',
                            '.todoList_l1.releaseDate > li > div.active' => 'naHS_releaseDate_l1_in',
                            '.todoList_l1.releaseDate > li > div' => 'naHS_releaseDate_l1_out',
                            '.todoList_l2.releaseDate > li > div.active' => 'naHS_releaseDate_l2_in',
                            '.todoList_l2.releaseDate > li > div' => 'naHS_releaseDate_l2_out'

                        ]
                    );
                }

                $r .= '</style>'.PHP_EOL;
                $ret .= $r;
            }
            if (is_array($css)) break;
        };

        //echo '<pre>'; var_dump ($d); exit();
        //exit();
        if (isset($css) && is_array($css) && !$hasJS && $js===true) {
                foreach ($selectors2 as $idx => $selector) {
                    if ($hasJS) break;
                    $hasJS = true;
                    if (
                        array_key_exists('specificityName', $selector)
                        && array_key_exists ('specificityName', $css)
                        && $selector['specificityName']!==$css['specificityName']
                    ) continue;
                    $r = '<script id="jsPageSpecific" type="text/javascript">'.PHP_EOL;
                    $r .= '// debug4'.PHP_EOL;
                    $useVividTexts = !array_key_exists('uvt',$_GET) || $_GET['uvt']=='y' ? 'true' : 'false';
                    $dbg = [
                        'na.site.loadContent' => !array_key_exists('lc',$_GET) || $_GET['lc']=='y' ? 'true' : 'false'
                    ];
                    foreach ($css['themes'] as $themeName => $theme) { break; };
                    $r .= 'if (!naGlobals) var naGlobals = {};'.PHP_EOL;
                    $r .= 'naGlobals = $.extend(naGlobals, {'.PHP_EOL;
                        $r .= "\tdebug : ".json_encode($dbg).",".PHP_EOL;
                        $r .= "\tdomain : '".$this->domain."',".PHP_EOL;
                        $r .= "\tdomainFolder : '".$this->domainFolder."',".PHP_EOL; //TODO 1
                        $r .= "\tuseVividTexts : ".$useVividTexts.",".PHP_EOL;
                        $r .= "\tbackground : '".$theme['background']."',".PHP_EOL;
                        $r .= "\tbackgroundSearchKey : '".$theme['backgroundSearchKey']."',".PHP_EOL;
                        $r .= "\tthemes : ".json_encode($css['themes'], JSON_PRETTY_PRINT).",".PHP_EOL;
                        $r .= "\tthemeName : '".$themeName."',".PHP_EOL;
                        $r .= "\tspecificityName : \"".$mySpecificityName."\",".PHP_EOL;
                        //$r .= "\tspecificityName : \"".$specificityName."\",".PHP_EOL;
                        //$r .= "\tspecificityName_revert : \"".$specificityName."\",".PHP_EOL;
                        //$r .= "\tspecificityNames : ".json_encode($selectorNames).",".PHP_EOL;
                        $r .= "\tthemesDBkeys : ".json_encode($selectors2, JSON_PRETTY_PRINT).",".PHP_EOL;
                        $r .= "\tview : ".json_encode($this->view, JSON_PRETTY_PRINT).",".PHP_EOL;
                        $r .= "\tversion : ".file_get_contents(__DIR__.'/../version.json').PHP_EOL.',';
                        $r .= "\tisBot : ".($naIsBot ? 'true' : 'false').','.PHP_EOL;
                        $r .= "\tnaLAN : ".($naLAN ? 'true' : 'false').','.PHP_EOL;
                        $r .= "\tnaHasErrors : ".((array_key_exists('naErrors',$_SESSION) && is_string ($_SESSION['naErrors']) && $_SESSION['naErrors']!=='') ? 'true' : 'false').','.PHP_EOL;
                        $r .= "\thasDB : ".($this->hasDB ? 'true' : 'false').PHP_EOL;
                    $r .= '});'.PHP_EOL;
                    //var_dump (strpos($_SERVER['SCRIPT_NAME'], '/index.php')); var_dump (strpos($_SERVER['SCRIPT_NAME'], '/ajax_get_content.php')); var_dump ($_SERVER);
                    //var_dump ($_GET); exit();
                    if (
                        strpos($_SERVER['SCRIPT_NAME'], '/index.php')!==false
                        || strpos($_SERVER['SCRIPT_NAME'], '/ajax_get_content.php')!==false
                    ) {
                        $r .= 'if (!naGlobals) {debugger; var naGlobals = {}};'.PHP_EOL;
                        $r .= 'naGlobals = $.extend(naGlobals, {'.PHP_EOL;
                            $r .= "\tdomain : '".$this->domain."',".PHP_EOL;
                            $r .= "\tdomainFolder : '".$this->domainFolder."',".PHP_EOL; //TODO 1
                            $r .= "\tapp : ".json_encode($this->view).','.PHP_EOL;
                        $r .= '});'.PHP_EOL;
                        $r .= '$(document).ready(function() {'.PHP_EOL;
                            $r .= 'if (!na.site.globals.themes) na.site.globals = $.extend(na.site.globals, naGlobals);'.PHP_EOL;
                            $r .= 'if (!na.site.settings.url) na.site.settings = $.extend(na.site.settings, {'.PHP_EOL;
                                $r .= "\turl : ".json_encode($this->url).PHP_EOL;
                            $r .= '});'.PHP_EOL;
                        $r .= '});'.PHP_EOL;
                    };
                    $r .= '$(document).ready(function() {'.PHP_EOL;
                        $r .= "\t//setTimeout(function() {".PHP_EOL;
                        //$r .= "\t\tna.site.setSpecificity();".PHP_EOL;
                        $r .= "\t//}, 10);".PHP_EOL;
                    $r .= "});".PHP_EOL;
                    $r .= '</script>'.PHP_EOL;
                    $ret = $r.$ret;
                }
        };

        if ($debug) { echo '$ret='; var_dump(htmlentities($ret)); echo '</pre>'.PHP_EOL.PHP_EOL; };

        // spawns way too many files :
        //file_put_contents($selectorsCachedFilepath.'.idx', $id2);
        //file_put_contents($selectorsCachedFilepath.'.txt', $ret);

        //if ($this->debugThemeLoading) exit();

        return $ret;
    }

    public function getPageCSS_cached ($id2, $selectorsCachedFN) {
        //$path = realpath(dirname(__FILE__).'/../..').'/NicerAppWebOS/siteCache';
        $path = $this->domainPath.'/siteCache/';
        //echo $path; die();
        $files = getFilePathList ($path, false, '/getPageCSS_.*\.idx/', null, array('file'), 1, 1, true)['files'];
        //echo '<pre>'; var_dump($files); die();
        foreach ($files as $idx => $f) {
            $id2a = file_get_contents($f['realPath']);
            $dbg = [
                'id2' => $id2,
                'id2a' => $id2a
            ];
            //echo '<pre>'; var_dump($dbg); echo '</pre>';
            if (strlen($id2a)===strlen($id2) && $id2a==$id2) return file_get_contents(str_replace('.idx','.txt',$f['realPath']));
        }
        return false;
    }

    public function themeSettings_UL_list ($theme, $root=true) {
        $css = '';
        if ($root) $css .= '<ul class="vividMenu_mainUL" style="display:none;" itemsLevel1="1" menuStructure="vertical">'; else $css .= '<ul>';
        foreach ($theme as $key => $value) {
            if ($key=='css') continue;

            $css .= '<li><a href="#" class="nomod noPushState">'.$key.'</a>';
            if (is_array($value)) {
                $css .= $this->themeSettings_UL_list ($value, false);
            }
            $css .= '</li>';
        }
        $css .= '</ul>';
        return $css;
    }

    public function getPageCSS_permissionsList($js=true) {
        global $naDebugAll;
        global $naLAN;
        $debug = $this->debugThemeLoading;
        $db = $this->dbs->findConnection('couchdb');
        $cdb = $db;

        $viewFolder = '[UNKNOWN VIEW]';

        //echo '<pre>$this->view='; var_dump ($this->view); exit();
        if (is_string($this->view)) {
            $this->view = json_decode($this->view, true);
        }

        if (is_array($this->view) && !is_null($this->view)) {
            foreach ($this->view as $viewFolder => $viewSettings) break;
            //$viewFolder = preg_replace('/.*\//','', $viewFolder);
            //echo '<pre>'; var_dump ($this->view); exit();
            $url = '/view/'.encode_base64_url(json_encode($this->view));

            //if ($viewFolder=='/') $url = '/';
        } /*else if (array_key_exists('REQUEST_URI',$_SERVER)) {
            // use defaults if not in proper format (when URL uses HTTP URL parameters for instance)..
            $viewName = '[front page]';
            $url = '/';

            // check if SEO url exists in proper format
            $uri = $_SERVER['REQUEST_URI'];
            if ($uri!=='' && strpos('?', $uri)===false) {
                $viewName = '[app page]';
                $url = $uri;
            }
        } */else {
            $viewFolder = '[front page]';
            $url = '/';

        }
        if (
            (
                !array_key_exists('url', $_SESSION)
                || $_SERVER['REQUEST_URI'] !== $_SESSION['url']
            ) && (
                strpos($_SERVER['REQUEST_URI'], 'pageSpecificSettings.php')===false
                || strpos($_SERVER['REQUEST_URI'], 'saveTheme.php')===false
            )
        ) {
            if (is_array($this->view) && !is_null($this->view)) {
                foreach ($this->view as $viewFolder => $viewSettings) break;
                //$viewFolder = preg_replace('/.*\//','', $viewFolder);
                //var_dump ($viewFolder); exit();
                $url = '/view/'.encode_base64_url(json_encode($this->view));
                //if ($viewFolder=='/') $url = '/';
            } else {
                $viewFolder = '[front page]';
                $url = '/';

            }
            //$url = $_SERVER['REQUEST_URI'];
            $_SESSION['url'] = $url;
        } else {
            $url = $_SESSION['url'] = $url;
        }

        $appName = preg_replace('/.*\//','',$viewFolder);
        //var_dump ($_SESSION);        var_dump ($this->view);        var_dump ($appName);        var_dump ($viewFolder);        die();
        //if ($debug) { echo '<pre>'; var_dump ($url); echo PHP_EOL; var_dump ($this->view); echo '</pre>'.PHP_EOL; }
        /*if ($viewFolder!=='') {
            $appName = preg_replace('/.*\//','',$viewFolder);
            $selectors = array (
                0 => array (
                    'permissions' => [
                        'read' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Guests')
                            ]
                        ],
                        'write' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Owners'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Chief Officers')
                        ]
                    ],
                    'specificityName' => 'site (for all viewers)',
                    'role' => $db->translate_plainGroupName_to_couchdbGroupName('Guests'),
                    'display' => true,
                    'worksWithoutDatabase' => true
                ),

                1 => array (
                    'permissions' => [
                        'read' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Guests')
                            ]
                        ],
                        'write' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Owners'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Chief Officers')
                            ]
                        ]
                    ],
                    'specificityName' => 'view \''.$viewFolder.'\' (for all viewers)',
                    'view' => $viewFolder,
                    //'url' => $url,
                    'role' => $db->translate_plainGroupName_to_couchdbGroupName('Guests'),
                    'display' => true
                ),

                2 => array (
                    'permissions' => [
                        'read' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Guests')
                            ]
                        ],
                        'write' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Owners'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Chief Officers')
                            ]
                        ]
                    ],
                    'specificityName' => 'current page (for all viewers)',
                    'url' => $url,
                    'role' => $db->translate_plainGroupName_to_couchdbGroupName('Guests'),
                    'display' => true
                )
            );

            $selectorNames = array (
                0 => 'site (for all viewers)',
                1 => 'app \''.$viewFolder.'\' (for all viewers)',
                2 => 'current page (for all viewers)'
            );
            //$preferredSelectorName = 'site (for all viewers)';

        } else {*/
            $selectors = array (
                0 => array (
                    'permissions' => [
                        'read' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Guests')
                            ]
                        ],
                        'write' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Owners'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Chief Officers'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Moderators')
                            ]
                        ]
                    ],
                    'specificityName' => 'site (for all viewers)',
                    'role' => $db->translate_plainGroupName_to_couchdbGroupName('Guests'),
                    'display' => true,
                    'worksWithoutDatabase' => true
                ),

                1 => array (
                    'permissions' => [
                        'read' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Guests')
                            ]
                        ],
                        'write' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Owners'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Chief Officers'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Moderators')
                            ]
                        ]
                    ],
                    'specificityName' => 'current page (for all viewers)',
                    'url' => $url,
                    'role' => $db->translate_plainGroupName_to_couchdbGroupName('Guests'),
                    'display' => true
                )
            );

            $selectorNames = array (
                0 => 'site (for all viewers)',
                1 => 'current page (for all viewers)'
            );
            //$preferredSelectorName = 'site (for all viewers)';so
        //}

        //echo '<pre>';var_dump ($_SESSION); var_dump ($_COOKIE); echo '</pre>';
        if (
            false
            || (
                !isset($_COOKIE)
                || !is_array($_COOKIE)
                || !array_key_exists('cdb_loginName',$_COOKIE)
                || $_COOKIE['cdb_loginName']===''
                //|| !$_SESSION['cdb_userIsAdministrator']
            )
        ) {
            $selectors[0]['display'] = false;
            $selectors[1]['display'] = false;
            if (array_key_exists(2,$selectors)) $selectors[2]['display'] = false;
        };
        //echo '<pre>'; var_dump ($selectors);

        global $naIP;
        $username100 =
            (array_key_exists('cdb_loginName', $_COOKIE)
                ? $_COOKIE['cdb_loginName']
                : 'Guest'
            );
            //var_dump($username100);
        $username100 = $db->translate_couchdbUserName_to_plainUserName ($username100);
            //var_dump($username100);
        //echo '<pre style="color:blue;">'; var_dump ($_SESSION); echo '</pre>'; exit();
        $username101 = $db->translate_plainUserName_to_couchdbUserName ($username100);
        //echo $username101; exit();


        if (
                    (
                is_object($this->dbs)
                && is_string($this->dbs->findConnection('couchdb')->username)
                && $this->dbs->findConnection('couchdb')->username!==''
                //&& $this->dbs->findConnection('couchdb')->username!==$this->domainFolderForDB.'___Guest'
                && $username100!==''
            )
        ) {
            $selectors[] = array (
                'permissions' => [
                    'read' => [ 'users' => [ $username101 ] ],
                    'write' => [
                        //'users' => [ $username101 ]
                        'roles' => [
                            $db->translate_plainGroupName_to_couchdbGroupName('Owners'),
                            $db->translate_plainGroupName_to_couchdbGroupName('Chief Officers'),
                            $db->translate_plainGroupName_to_couchdbGroupName('Moderators')
                        ]
                    ]
                ],
                'specificityName' => 'site for user '.$username100,
                'user' => $username100,
                'display' => true
            );
            $selectorNames[] = 'site for user '.$username101;
            //$preferredSelectorName = 'site user '.$username100;

            $selectors[] = array (
                'permissions' => [
                    'read' => [ 'users' => [ $username101 ] ],
                    'write' => [ 'users' => [ $username101 ] ]
                ],
                'specificityName' => 'site for user '.$username100.' at the client',
                'user' => $username100,
                'ip' => $naIP,
                'display' => true
            );
            $selectorNames[] = 'site for user '.$username100.' at the client';



                //if (true) { echo '<pre style="color:white;background:blue">'; var_dump ($this->dbs->findConnection('couchdb')->roles); echo '</pre>'; }


            foreach ($this->dbs->findConnection('couchdb')->roles as $roleIdx => $role) {
                $role2 = $this->dbs->findConnection('couchdb')->translate_couchdbGroupName_to_plainGroupName($role);
                if ($debug) { echo '<pre style="color:lime;background:navy">'; var_dump ($role2); echo '</pre>'; }
                $selectors[] = array (
                    'permissions' => array (
                        'read' => [
                            'users' => [ $username101 ],
                            'roles' => [ $db->translate_plainGroupName_to_couchdbGroupName('Guests'), $role ]
                        ],
                        'write' => [
                            //'roles' => [ $role ]
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Owners'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Chief Officers'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Moderators')
                            ]
                        ]
                    ),
                    'specificityName' => 'site for group '.$role2,
                    'role' => $role,
                    'display' => true
                );
                $selectorNames[] = 'site for group '.$role2;
                //$preferredSelectorName = 'site for group '.$role;

                $selectors[] = array (
                    'permissions' => [
                        'read' => [ 'users' => [ $username101 ] ],
                        'write' => [ 'roles' => [ $role ] ]
                    ],
                    'specificityName' => 'site for group '.$role2.' at the client',
                    'role' => $role,
                    'ip' => $naIP,
                    'display' => true
                );
                $selectorNames[] = 'site for group '.$role2.' at the client';
                //$preferredSelectorName = 'site for group '.$role.' at the client';
                if ($viewFolder!==''
                    && $viewFolder!=='/'
                    && $appName!==''
                ) {
                    $selectors[] = array (
                        'permissions' => [
                            'read' => [ 'roles' => [ $role ] ],
                            'write' => [ 'roles' => [ $role ] ]
                        ],
                        'specificityName' => 'app \''.$appName.'\' for group '.$role2,
                        'app' => $viewFolder,
                        'role' => $role,
                        'display' => true
                    );
                    $selectorNames[] = 'app \''.$appName.'\' for group '.$role2;
                    $selectors[] = array (
                        'permissions' => [
                            'read' => [ 'roles' => [ $role ] ],
                            'write' => [ 'roles' => [ $role ] ]
                        ],
                        'specificityName' => 'app \''.$appName.'\' for group '.$role2.' at the client',
                        'app' => $viewFolder,
                        'role' => $role,
                        'ip' => $naIP,
                        'display' => true
                    );
                    $selectorNames[] = 'app \''.$appName.'\' for group '.$role2.' at the client';
                }



                $selectors[] = array (
                    'permissions' => [
                        'read' => [ 'roles' => [ $role ] ],
                        'write' => [
                            //'roles' => [ $role ]
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Owners'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Chief Officers'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Moderators')
                            ]
                        ]
                    ],
                    'specificityName' => 'current page for group '.$role2,
                    'url' => $url,
                    //'app' => $viewFolder,
                    'role' => $role,
                    'display' => true
                );
                $selectorNames[] = 'current page for group '.$role;

                $selectors[] = array (
                    'permissions' => [
                        'read' => [ 'roles' => [ $role ] ],
                        'write' => [ 'roles' => [ $role ] ]
                    ],
                    'specificityName' => 'current page for group '.$role2.' at the client',
                    'url' => $url,
                    //'app' => $viewFolder,
                    'role' => $role,
                    'ip' => $naIP,
                    'display' => true
                );
                //$preferredSelectorName = $selectorNames[] = 'current page for group '.$role.' at the client';
            }


            if ($viewFolder!=='' && $appName!=='') {
                $selectors[] = array (
                    'permissions' => [
                        'read' => [ 'users' => [ $username101 ] ],
                        'write' => [
                            //'users' => [ $username101 ]
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Owners'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Chief Officers'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Moderators')
                            ]
                        ]
                    ],
                    'specificityName' => 'app \''.$appName.'\' for user '.$username100,
                    'app' => $viewFolder,
                    'user' => $username101,
                    'display' => true
                );
                $selectorNames[] = 'app \''.$appName.'\' for user '.$username100;
                $selectors[] = array (
                    'permissions' => [
                        'read' => [ 'users' => [ $username101 ] ],
                        'write' => [ 'users' => [ $username101 ] ]
                    ],
                    'specificityName' => 'app \''.$appName.'\' for user '.$username100.' at the client',
                    'app' => $viewFolder,
                    'user' => $username101,
                    'ip' => $naIP,
                    'display' => true
                );
                $selectorNames[] = 'app \''.$appName.'\' for user '.$username100.' at the client';
            }




            $selectors[] = array (
                'permissions' => [
                    'read' => [ 'users' => [ $username101 ] ],
                    'write' => [
                        //'users' => [ $username101 ]
                        'roles' => [
                            $db->translate_plainGroupName_to_couchdbGroupName('Owners'),
                            $db->translate_plainGroupName_to_couchdbGroupName('Chief Officers'),
                            $db->translate_plainGroupName_to_couchdbGroupName('Moderators')
                        ]
                    ]
                ],
                'specificityName' => 'current page for user '.$username100,
                'url' => $url,
                'user' => $username101,
                'display' => true
            );
            $selectorNames[] = 'current page for user '.$username100;
            //$preferredSelectorName = 'current page for user '.$username100; // TODO : THIS is the ONLY WAY to be doing this. #username100 FTW!

            $selectors[] = array (
                'permissions' => [
                    'read' => [ 'users' => [ $username101 ] ],
                    'write' => [ 'users' => [ $username101 ] ]
                ],
                'specificityName' => 'current page for user '.$username100.' at the client',
                'url' => $url,
                'user' => $username101,
                'ip' => $naIP,
                'display' => true
            );
            $selectorNames[] = 'current page for user '.$username100.' at the client';
            //$preferredSelectorName = 'current page for user '.$username100.' at the client';
        };

        //$preferredSelectorName = 'current page for user '.$username100.' at the client';
            //if (session_status() === PHP_SESSION_NONE) {
            //ini_set('session.gc_maxlifetime', 3600);
            //session_start();
        //};

        // TODO : filter $_SESSION['selectors'] here or in the code that exports it to js?
        $_SESSION['selectors'] = json_encode($selectors);
        //$_SESSION['selectorNames'] = json_encode($selectorNames);
        //echo '<pre>'; var_dump ($_SESSION);

        //$selectors2 = array_reverse($selectors, true);
        //$selectorNames2 = array_reverse($selectorNames, true);

        $ret = '';
        $hasJS = false;
        $hasCSS = false;


        if ($debug) {
            $dbg1a = [
                '$hasJS' => $hasJS,
                '$hasCSS' => $hasCSS,
                '$selectors' => $selectors
            ];
            echo '<pre style="color:green;">';var_dump ($dbg1a); echo '</pre>';
        }
        return [
            'selectorNames' => $selectorNames,
            'selectors' => $selectors//,
            //'preferredSelectorName' => $preferredSelectorName
        ];
    }



    public function getPageCSS_specific($selector) {
        $debug = $this->debugThemeLoading;
        $cdbFunctional1a = true;
        $fncn = $this->cn.'::getPageCSS_specific()';
        //$fncn = $this->cn.'::getPageCSS_specific("'.json_encode($selector).'")';

        $db = $this->dbs->findConnection('couchdb');

        //if ($debug)
        //{ echo '$selector='; var_dump ($selector); echo '<br/><br/>'.PHP_EOL.PHP_EOL; exit(); };

        $permissions = $selector['permissions'];
        if ($debug) {
            echo '<pre style="color:purple">$selector='; var_dump ($selector); echo '</pre><br/>'.PHP_EOL.PHP_EOL;
        }

        unset ($selector['display']);
        unset ($selector['has_read_permission']);
        unset ($selector['has_write_permission']);
        unset ($selector['permissions']);

        /*
        if (array_key_exists('worksWithoutDatabase',$selector) && $selector['worksWithoutDatabase']===true) {
                $rec = [
                    'default' => [
                        '_id' => cdb_randomString(20),
                        'role' => 'guests',
                        'theme' => 'default',
                        'specificityName' => 'site',
                        'textBackgroundOpacity' => 0.4,
                        'background' => '/siteMedia/backgrounds/tiled/grey/abstract_ice.jpg',
                        'backgroundSearchKey' => 'landscape',
                        'dialogs' => css_to_array (file_get_contents(
                            realpath(dirname(__FILE__).'/../..')
                            .'/NicerAppWebOS/themes/nicerapp_default.css'
                        ))
                    ]
                ];
                return $rec;
        */
        //} else {
            if ($cdbFunctional1a) {
                // check permissions
                $hasPermission = false;
                foreach ($permissions as $permissionType => $permissionsRec) {
                    if ($permissionType=='read') {
                        foreach ($permissionsRec as $accountType => $accountsList) {
                            foreach ($accountsList as $idx => $userOrGroupID) {
                                if ($accountType == 'users') {
                                    $adjustedUserOrGroupID = $db->translate_plainUserName_to_couchdbUserName($userOrGroupID);
                                } else {
                                    $adjustedUserOrGroupID = $db->translate_plainGroupName_to_couchdbGroupName($userOrGroupID);
                                }
                                $adjustedUserOrGroupID = $userOrGroupID;


                                if ($debug) { echo '<pre style="color:lime;background:blue;margin:10px;padding:5px;">t666c='; var_dump($accountType); var_dump ($this->dbs->findConnection('couchdb')->username); echo '<br/>'.PHP_EOL; var_dump ($userOrGroupID); echo '<br/>$adjustedUserOrGroupID='; var_dump ($adjustedUserOrGroupID); echo '</pre>';}

                                if ($accountType == 'roles') {
                                    //$adjustedUserOrGroupID = $db->translate_plainGroupName_to_couchdbGroupName($userOrGroupID);
                                    //if ($debug) { echo '$this->dbs->roles='; var_dump($this->dbs->roles); };
                                    if (is_string($this->dbs)) {
                                        echo $fncn.' : WARNING : invalid database connection ($this->dbs="'.json_encode($this->dbs).'")- this database server, or even the entire webserver, has been hacked by hostiles.';
                                        exit(); // or exit();
                                    }
                                    if (!is_null($this->dbs->findConnection('couchdb')->roles))
                                    foreach ( $this->dbs->findConnection('couchdb')->roles
                                        as $roleIdx => $groupID
                                    ) {
                                        if ($debug) { echo 't667A='; var_dump($groupID); var_dump ($adjustedUserOrGroupID);};
                                        if ($adjustedUserOrGroupID==$groupID) {
                                            $hasPermission = true;
                                        }
                                    }
                                }
                                if ($accountType == 'users' && $this->dbs->findConnection('couchdb')->username == $adjustedUserOrGroupID) {
                                    $hasPermission = true;
                                    if ($debug) { echo 't777 $username='.$this->dbs->findConnection('couchdb')->username.PHP_EOL; }
                                }
                            }
                        }
                    }
                }
            } else {
                return false;
                //$hasPermission = true;
                //if ($debug) { echo  '<h1 style="color:green;font-size:bold;">$hasPermission = true, because $cdbFunctional1a = false.</h1>'.PHP_EOL; }
            }

            if (!$hasPermission) {
                $msg = 'class.naContentManagementSystem.php::getPageCSS_specific() : !$hasPermission for username='.$this->dbs->findConnection('couchdb')->username.' - aborting';
                //if ($debug) trigger_error ($msg, E_USER_NOTICE);
                if ($debug) echo $msg.'<br/>'.PHP_EOL;

                if ($debug) echo '</pre>';
                return false;
            }

            if (false) {
                echo '<pre style="color:green">';
                var_dump ($this->dbs->findConnection('couchdb'));
                echo '</pre>';
            }

            // try to fetch the requested cosmetics data
            $dbName = $this->dbs->findConnection('couchdb')->dataSetName('themes');
            try {
                $this->dbs->findConnection('couchdb')->cdb->setDatabase($dbName, false);
            } catch (Exception $e) {
                if ($debug) { echo 'status : Failed : could not open database '.$dbName.'<br/>'.PHP_EOL; exit(); }
            }

            $sel = [];
            if (array_key_exists('user', $selector)) $sel['user'] = $selector['user']; else $sel['user'] = [ '$exists' => false ];
            if (array_key_exists('role', $selector)) $sel['role'] = $selector['role']; else $sel['role'] = [ '$exists' => false ];
            if (array_key_exists('view', $selector)) $sel['view'] = $selector['view']; else $sel['view'] = [ '$exists' => false ];
            if (array_key_exists('app', $selector)) $sel['app'] = $selector['app']; else $sel['app'] = [ '$exists' => false ];
            if (array_key_exists('url', $selector)) $sel['url'] = $selector['url']; else $sel['url'] = [ '$exists' => false ];
            if (array_key_exists('specificityName', $selector)) $sel['specificityName'] = $selector['specificityName'];
            if (array_key_exists('theme', $selector)) $sel['theme'] = $selector['theme']; //else $sel['theme'] = [ '$exists' => false ];
            if (array_key_exists('ip', $selector)) $sel['ip'] = $selector['ip']; else $sel['ip'] = [ '$exists' => false ];
            global $naIP;
            if (strpos($selector['specificityName'], 'on the client')!==false) {
                $selector['ip'] = $naIP;
                //$selector['ua'] = $_SERVER['HTTP_USER_AGENT'];
            }
            $selector['lastUsed'] = [
                '$exists' => true
            ];
            if ($debug) { echo '<pre style="color:blue">$sel = '.json_encode ($sel, JSON_PRETTY_PRINT); echo '</pre>'; };
            //array( 'url'=>$selector['url'], 'role'=>$selector['role'] ),//$selector,

            $findCommand = array (
                'selector' => $sel,
                'fields' => [ '_id', 'ip', 'user', 'view', 'role', 'lastUsed', 'theme', 'url', 'themeSettings', 'apps', 'background', 'backgroundSearchKey', 'textBackgroundOpacity', 'changeBackgroundsAutomatically', 'backgroundChange_hours', 'backgroundChange_minutes' ],
                'sort' => [['lastUsed'=>'desc']],
                'use_index' => '_design/ba8cf896bc63459689d9b5637bb39f47dca8298e'
            );

            $findCommand = array (
                'selector' => $sel,
                'fields' => [ '_id', 'lastUsed' ],
                'sort' => [['lastUsed'=>'asc']],
                'use_index' => 'sortIndex_lastUsed'
               // 'use_index' => 'primaryIndex'
            );
            $findCommand = array (
                'selector' => $sel,
                'fields' => [ '_id', 'user', 'role', 'view', 'app', 'url', 'specificityName', 'ip', 'lastUsed' ],
                'sort' => [['lastUsed'=>'asc']],
            //'use_index' => 'sortIndex_lastUsed'
                'use_index' => '_design/ba8cf896bc63459689d9b5637bb39f47dca8298e'
            );
            try {
                $call = $this->dbs->findConnection('couchdb')->cdb->find ($findCommand);
            } catch (Exception $e) {
                //$debug = true;
                if ($debug)     {
                    backtrace();
                    echo '<pre>info : $findCommand2='; var_dump ($findCommand); echo '.<br/>'.PHP_EOL;
                    if (is_object($call)) { echo 'info : $call='; var_dump ($call); echo '.</pre>'.PHP_EOL; }
                };


                if (isset($_COOKIE['PHPSESSID'])) { // or your session cookie name
                    setcookie('PHPSESSID', '', [
                        'expires' => time() - 3600, // past time
                        'path' => '/',
                        'domain' => '.nicer.app', // match the old domain to delete it properly
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'Lax'
                    ]);
                    unset($_COOKIE['PHPSESSID']);
                }

                global $naWebOS;
                //echo $this->dbs->findConnection('couchdb')->translate_plainUserName_to_couchdbUserName('Guest'); exit();
                unset ($_COOKIE['cdb_authSession_cookie']);
                $co = [
                    'expires' => time() + 604800,
                    'path' => '/',
                    'domain' => 'new.nicer.app'
                ];
                setcookie('cdb_authSession_cookie', '', $co);
                setcookie('cdb_loginName', $this->dbs->findConnection('couchdb')->translate_plainUserName_to_couchdbUserName('Guest'), $co);
//var_dump($_COOKIE);
               $msg = 'NicerAppWebOS FATAL ERROR : while trying to find in \''.$dbName.'\' as user \''.$this->dbs->findConnection('couchdb')->username.'\' : '.$e->getMessage();
                echo $msg;
                $html = '<script type="text/javascript">var delete_cookie = function(name) { document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:01 GMT;"; }; delete_cookie("cdb_authSession_cookie"); delete_cookie("cdb_loginName"); window.location.reload();</script>';
                echo $html;
                exit();
            }
            if ($debug)  {echo 'HTTP status==='.$call->headers->_HTTP->status.', count($call->body->docs)==='.count($call->body->docs).'!<br/>'; };

            $hasRecord = false;
            $rets = [];
            if ($call->headers->_HTTP->status==='200') {

                foreach ($call->body->docs as $idx => $d) {
                    $hasRecord = true;
                    //$debug = true;
                    if ($debug) { echo '$d='; var_dump ($d); }
                    $tn = ( isset($d->theme) ? $d->theme : 'default' );
                    $d2 = $this->dbs->findConnection('couchdb')->cdb->get($d->_id)->body;
                    //$d2 = &$d;
                    $ret = [
                        $tn => [
                            'dbID' => $d2->_id,
                            'themeSettings' => json_decode(json_encode($d2->themeSettings), true),
                            'apps' => json_decode(json_encode((property_exists($d2,'apps')?$d2->apps:[])), true),
                            'background' => ( isset($d2->background) ? $d2->background : '' ),
                            'backgroundSearchKey' => ( isset($d2->backgroundSearchKey) ? $d2->backgroundSearchKey : '' ),
                            'textBackgroundOpacity' => ( isset($d2->textBackgroundOpacity) ? $d2->textBackgroundOpacity : ''),
                            'changeBackgroundsAutomatically' => ( isset($d2->changeBackgroundsAutomatically)
                                ? ($d2->changeBackgroundsAutomatically ? 'true' : 'false')
                                : 'false'
                            ),
                            'backgroundChange_hours' => ( isset($d2->backgroundChange_hours) ? $d2->backgroundChange_hours : ''),
                            'backgroundChange_minutes' => ( isset($d2->backgroundChange_minutes) ? $d2->backgroundChange_minutes : ''),
                            'theme' => $tn
                        ]
                    ];
                    if (isset($d2->user)) $ret[$tn]['user'] = $d2->user;
                    if (isset($d2->role)) $ret[$tn]['role'] = $d2->role;
                    if (isset($d2->url)) $ret[$tn]['url'] = $d2->url;
                    if (isset($d2->view)) $ret[$tn]['view'] = $d2->view;
                    if (isset($d2->app)) $ret[$tn]['app'] = $d2->app;
                    if (isset($d2->specificityName)) $ret[$tn]['specificityName'] = $d2->specificityName;
                    if (isset($tn)) $ret[$tn]['theme'] = $tn;

                    if ($debug) echo '</pre>';

                    $rets = array_merge ($rets, $ret);
                    //break;
                    //return json_decode(json_encode($ret),true);
                }
                /* RETURN ALL THEMES, NOT JUST 1
                if (count($rets)>0) {
                    if ($debug) {
                        echo '<pre>info : $findCommand2='; var_dump ($findCommand); echo '.<br/>'.PHP_EOL;
                        echo 'info : $rets='; var_dump ($rets); echo '.</pre>'.PHP_EOL;
                        exit();
                    }
                    return [
                        'sel' => $sel,
                        'themes' => $rets
                    ];
                }*/
            }
            if ($debug) echo '</pre>';
        //}
        if ($hasRecord) {
            return [
                'sel' => $selector, //sel, // doesn't get used. for logging purposes only - and (TODO) probably not set correctly.
                'themes' => $rets
            ];
        }

        return false;
    }

    // helper functions
    public function currentDateTimeStamp() { // it's considered unwise to be using this function at all
        return date('Ymd_His');
    }

    public function fileDateTimeStamp($filepath) { // use this function instead
        //return $filepath;
        return date('Ymd_His', filemtime($filepath));
    }

    public function adjustPath ($fn) {
        /*
        $dbg = [
            '$this->path' => $this->path,
            '$this->domain' => $this->domain,
            '$this->domainFolder' => $this->domainFolder
        ];
        echo '<pre>'; var_dump ($dbg); echo '</pre>'; exit();
        */
        return str_replace($this->path, '', $fn);
    }



}

?>
