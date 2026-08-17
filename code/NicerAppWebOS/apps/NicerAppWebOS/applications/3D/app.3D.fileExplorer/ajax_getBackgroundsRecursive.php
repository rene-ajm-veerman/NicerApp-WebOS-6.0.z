<?php
    $naRoot = realpath(dirname(__FILE__).'/../../../../../..');
    require_once($naRoot.'/NicerAppWebOS/boot.php');
    global $naWebOS;
    $debug = false;
    $debug_jsonViewer = false;
    global $debug;

    $fp = str_replace('/domainConfig','',$naWebOS->domainPath).'/siteCache/app.3D.fileBrowser.json';
    //var_dump ($fp); echo PHP_EOL;
    //if (file_exists($fp)) unlink ($fp);
    if (file_exists($fp)) readfile($fp);
    else {
        $mi = [];

        $root = $naRoot.'/siteMedia/backgrounds/landscape';
        $root =   realpath(dirname(__FILE__).'/../../../../../../../nicer.app-musicPlayer-music');
        //var_dump ($root); die();
        $fileFormats = '/\.mp3$/';
        global $fileFormats;
        $excl = '/.*thumbs.*/';
        global $excl;
        //$f = getFilePathList ($root, true, $fileFormats, $excl, array('dir','file'), null, 1, true);
        $f = getFilePathList ($root, true, $fileFormats, $excl, array('dir', 'file'), null, 1, true);
        $f = getFileDetails ($f);
        //echo '<pre style="color:green;">'; var_dump ($root); echo '</pre><pre style="color:blue;">'; echo json_encode($f,JSON_PRETTY_PRINT); echo '</pre>'; die();

        $mi[] = [
            'root' => str_replace($rootPath_na, '', $root),
            'thumbnails' => './thumbs',
            'filesAtRoot' => $f
        ];


        if ($debug_jsonViewer && $debug) {
            $c = [
                'siteContent' => hmJSON($mi, '$mi')
            ];
            echo $naWebOS->getSite($c);
            echo '<script type="text/javascript">setTimeout (function() {na.site.settings.current.running_loadTheme = false; na.site.settings.current.loadingApps = false; na.hms.startProcessing()}, 1500); na.site.transformLinks()</script>';
        } else {
            $smi = json_encode($mi);
            file_put_contents ($fp, $smi);
            echo $smi;
        }
    }

    function getFileDetails (&$f) {
        $x = walkArray ($f, 'getFileDetails_walkKey', 'getFileDetails_walkValue');
        return $f;
    }

    function getFileDetails_walkKey ($cd) {
        global $excl;
        global $fileFormats;
        global $debug;
        $k = &$cd['k'];
        $v = &$cd['v'];

        // condense the data :
        if ($k=='files') $v = array_keys ($v);
    }

    function getFileDetails_walkValue ($cd) {
        return $cd;
        /*
        global $excl;
        global $fileFormats;
        global $debug;
        $k = &$cd['k'];
        $v = &$cd['v'];
        //if ($k=='files') { echo '<pre style="color:green">'; var_dump ($cd); echo '</pre>'; }
        if ($k=='files') $v = array_keys ($v);
        return $cd;
        */
    }

?>
