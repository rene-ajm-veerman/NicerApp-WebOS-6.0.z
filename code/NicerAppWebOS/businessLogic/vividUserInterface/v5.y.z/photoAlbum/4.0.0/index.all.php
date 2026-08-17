<html style="overflow:hidden">
<head>
    <link type="text/css" rel="StyleSheet" href="/domainConfig/index.css?c=<?php echo date('Ymd_His');?>">
    <link type="text/css" rel="StyleSheet" href="/domainConfig/index.dark.css?c=<?php echo date('Ymd_His');?>">
    <style>
        .filename {
            color : white;
        }
    </style>
</head>
<body style="overflow:hidden">
<div id="photoAlbumSelection__scrollpane" class="vividScrollpane vividTheme__scroll_black" style="width:100%; height:100%;">
<?php
    $root = realpath(dirname(__FILE__).'/../../../../../..');
    require_once ($root.'/NicerAppWebOS/boot.php');
    set_time_limit(5 * 60);
    /*
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.gc_maxlifetime', 3600);
        session_start(); 
    };*/

    
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);    

    global $naWebOS;
    
    $baseURL = '/siteData/'.$naWebOS->domainFolder;
    $baseDir = $naWebOS->domainPath.'/siteData/'.$naWebOS->domainFolder;
    //secho '<pre style="color:white;">'; var_dump($baseDir); echo '</pre>';

    /*
    $couchdbConfigFilepath = $root.'/domainConfigs/'.$naWebOS->domainFolder.'/couchdb.json';
    $cdbConfig = json_decode(file_get_contents($couchdbConfigFilepath), true);

    $cdb = new Sag($cdbConfig['domain'], $cdbConfig['port']);
    $cdb->setHTTPAdapter($cdbConfig['httpAdapter']);
    $cdb->useSSL($cdbConfig['useSSL']);
    $cdb->login($cdbConfig['adminUsername'], $cdbConfig['adminPassword']);
    */
    $cdb = $naWebOS->dbsAdmin->findConnection('couchdb')->cdb;

    // create users
    $dbs = $cdb->getAllDatabases();
    //echo '<pre style="color:white;">'; var_dump ($dbs); echo '</pre>'; exit();
    
    //$albums = json_decode(decode_base64_url($_GET['albums']));
    //echo '<div style="color:white;">'; var_dump ($albums); echo '</div>'; exit();
    
    /*  $albums can NOT be passed on the URL from the browser na.tree.tinyMCE_photoAlbums_list(),
        hits real limitations when used in conjunction with larger (5000+ photos) photo databases
        
        so instead, we query the couchdb server from PHP :
    */
    //$dbs = $couchdb->getAllDbs();
    $dbList = '';
    $albums = array();













    foreach ($dbs->body as $idx => $dbName) {
        $dbList .= $dbName.'<br/>';
        
        if (
            (strpos($dbName,'tree___role')!==false)
            || (strpos($dbName,'tree___user')!==false)
        ) {
            $do = true;
            try { $db = $cdb->setDatabase($dbName,false); } catch (Exception $e) { echo $e->getMessage(); echo '<br/>'; $do = false; exit(); }
            if ($do) {
                $docs = $cdb->getAllDocs();
                //echo '<pre style="color:lime;">'; var_dump ($dbName); var_dump ($docs); echo '</pre>'; //exit();
                //$dbList .= json_encode($docs).'<br/>';
                //$dbList .= 'count : '.count($docs).'<br/>';
                $parentsURL = '';
                for ($i=0; $i<count($docs->body->rows); $i++) {
    set_time_limit(5 * 60);
                    $it = $cdb->get($docs->body->rows[$i]->id);
                    //echo '<pre style="color:red;">'; var_dump ($it); echo '</pre>';// exit();
                    if (property_exists($it->body, 'type') && $it->body->type==='naMediaAlbum') {
                        $j = $i;
                        $it2 = $cdb->get($docs->body->rows[$j]->id);
                        $parentsURL = $it2->body->text;

                        //echo '<pre style="color:yellow;">'; var_dump ($it2); echo '</pre>';// exit();
                        
                        while ($it2->body->parent!=='#') {
                            $done = false;
                            for ($k=0; $k<count($docs->body->rows); $k++) {
                                $it3 = $cdb->get($docs->body->rows[$k]->id);
                                if (
                                    property_exists($it3->body,'id')
                                    && $it3->body->id===$it2->body->parent
                                ) {
                                    $parentsURL = $it3->body->text . '/' . $parentsURL;
                                    $done = true;
                                    break;
                                }
                            }
                            if (!$done) break;
                            $it2 = $it3;
                        }
                    }
                    if ($parentsURL!=='') {
                        if (strpos($dbName,'tree___user')!==false) $parentsURL = 'Users/'.$parentsURL;
                        if (strpos($dbName,'tree___role')!==false) $parentsURL = 'Groups/'.$parentsURL;
                        //echo '<pre style="color:lime;font-weight:bold;">'; var_dump ($baseDir.'/'.$parentsURL); var_dump (is_string(realpath($baseDir.'/'.$parentsURL))); echo '</pre>';
                        if (is_string(realpath($baseDir.'/'.$parentsURL))) array_push($albums, $parentsURL);
                    }
                    
                }

            }
        }
    };
    if (false) {
        echo '<div style="color:white;">'; var_dump ($parentsURL); echo '</div>';
        echo '<div style="color:yellow;">'; var_dump ($dbList); echo '</div>';
        echo '<pre style="color:lime;">'; var_dump ($albums); echo '</pre>'; 
        exit();
    };

    
    /*---
    ----- format the photo albums found into HTML to be used in the tinyMCE popup 
    ---*/
    foreach ($albums as $idx => $albumRelativePath) {
    set_time_limit(5 * 60);
        $targetDir = $baseDir.'/'.$albumRelativePath;
        //echo '<pre style="color:lime;">'; var_dump ($targetDir); echo '</pre>'; exit();
        $thumbDir = $targetDir.'/thumbs';
        //echo '<div style="color:white;">'; var_dump ($targetDir); echo '</div>'; exit();
        
        /*
        $smID = $_GET['smID'];
        $iid = $_GET['iid'];
        $dialogID = $_GET['dialogID'];
        */
        $imgStyle = ''; // boxShadow perhaps

        if (!realpath($targetDir)) {
            echo '<p style="color:red;background:white;">'; var_dump($targetDir); echo '</pre>';
        } else {

            $files = getFilePathList ($targetDir, false, FILE_FORMATS_photos, null, array('file'), 1,1,false);
            //echo '<pre style="color:lime">'; var_dump($targetDir); var_dump ($files); echo '</pre>';
            
            $dbg = array (
                'baseURL' => $baseURL,
                'baseDir' => $baseDir,
                'targetDir' => $targetDir,
                'files' => $files
            );
            //if (count($files)>0) {echo '<pre style="color:red;background:white;border-radius:3px;border:1px solid black;">'; var_dump ($dbg); echo '</pre>'; exit();};

            //echo '<pre style="color:red;background:white;border-radius:3px;border:1px solid black;">'; var_dump ($dbg); echo '</pre>';
            








            $idx3 = 0;
            foreach ($files as $idx2 => $filePath) {
                if ($idx3===0) {
                    $filePath = $filePath['webPath'];
                    //var_dump ($filePath);
                    $fileName = str_replace ($targetDir.'/', '', $filePath);
                            $fileName = preg_replace ('/^\//', '', $fileName);

                    $thumbPath = $thumbDir.'/'.$fileName;
                    $thumbURL = str_replace ($baseDir, $baseURL, $thumbPath);
                    $fileURL = str_replace ($baseDir, $baseURL, $filePath);
                    $dbg = array (
                        'fileName' => $fileName,
                        'filePath' => $filePath,
                        'albumRelativePath' => $albumRelativePath,
                        'baseDir' => $baseDir,
                        'thumbDir' => $thumbDir,
                        'thumbPath' => $thumbPath,
                        'thumbURL' => $thumbURL
                    );
                    //echo '<pre style="color:black;background:white;border-radius:3px;border:1px solid black;">'; var_dump ($dbg); echo '</pre>';
                    echo '<div style="overflow:hidden;position:relative;width:500px;height:225px;margin:5px;padding:10px;padding-top:20px;border-radius:10px;border:1px solid black;background:rgba(0,0,0,0.7);box-shadow:2px 2px 2px rgba(0,0,0,0.5), inset 1px 1px 1px rgba(0,0,255,0.5), inset -1px -1px 1px rgba(0,0,255,0.5);">';
                    
                    $onclick = 'onclick="window.top.na.cms.insertMediaFolder(\''.$albumRelativePath.'\');"';
                    
                    if (strpos($thumbURL,'ortrait')!==false) 
                    echo '<center><img src="'.$thumbURL.'" style="width:100px" '.$onclick.'/><br/><span class="filename">'.$albumRelativePath.'<br/>('.count($files).' files)'.'</span></center>';
                    else
                    echo '<center><img src="'.$thumbURL.'" style="width:200px" '.$onclick.'/><br/><span class="filename">'.$albumRelativePath.'<br/>('.count($files).' files)'.'</span></center>';
                    
                    echo '</div>';
                }
                $idx3++;
            }
            ob_flush(); ob_end_flush(); ob_start();
        }
    }
    ?>
</div>
<script type="text/javascript">
    /*
    var 
    pane = window.top.jQuery('#photoAlbumSelection__scrollpane', document)[0];
    
    pane.document = document;
    
    window.top.na.vcc.init (pane);*/
</script>
</body>
</html>
