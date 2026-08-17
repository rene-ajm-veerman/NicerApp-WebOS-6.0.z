<?php
    // public vars :
    $rescanContent = false;
//echo decode_base64_url ('eyJhaWQiOjAsImZkcyI6NDYyLCJycCI6IlwvRWFybHkgMjFzdCBDZW50dXJ5XC9BcmFiaWMgVHJvcGljYWwgRWxlY3Ryb25pYyBEb3dudGVtcG9cLzAxXC9NYXVnbGlcLyJ9'); exit;
    // private vars & code :
    $rootPath_vkdmd = realpath(dirname(__FILE__).'/../../../../../..');
    //echo $rootPath_vkdmd; die();
    require_once ($rootPath_vkdmd.'/NicerAppWebOS/boot.php');
    global $naWebOS;
    global $naURLs;
    require_once ($naWebOS->domainPath.'/mainmenu.items.php');
    //var_dump ($naURLs);
    $appFolder = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.beatPulse';
    $rf = __DIR__.'/music/';
    $ffi = []; // file and folder index
    $views = [];
    $k3 = 0;
    $linksOnPage = 100;
    //var_dump ($_GET); die();
    $pw = 'outtaLuck';
    if (array_key_exists('pw',$_GET)) $pw = $_GET['pw'];
    $idxStart = 0;
    if (array_key_exists('idxStart', $_GET)) $idxStart = intval($_GET['idxStart']); else $idxStart = 0;
    global $naLAN;
    //var_dump($rf); die();
    $read = false;
    $collectionDurationInSeconds = 0;
    $pageDurationInSeconds = 0;

    $cacheFile = __DIR__.'/index.views.json';
    $rescanContent = (!file_exists($cacheFile) || !is_readable($cacheFile) || file_get_contents($cacheFile)=='');
    var_dump($rescanContent);exit;
    if (!$rescanContent) {
    //if (!$naLAN && file_exists(dirname(__FILE__).'/index.views.json') && (!array_key_exists('rc',$_GET) || $_GET['rc']!=='true')) {
        // ONLY SLOWS THINGS DOWN CONSIDERABLY $folders = json_decode(file_get_contents($rf.'/index.foldersAndFiles.json'),true);
        $views = json_decode(file_get_contents($cacheFile),true);
        $read = true;
        //var_dump(json_last_error_msg());
        //var_dump($views);exit;
    } else {
        $folders = getFilePathList ($rf,true,'/.*/',null,['dir']);
        sort ($folders);
        //echo '<pre>'; var_dump ($rf); var_dump ($folders); echo '</pre>'; exit;
        //echo json_encode($folders,JSON_PRETTY_PRINT);exit;
        foreach ($folders as $idx => $folder) {
            if (strpos($folder['path'],'/meta')!==false) continue;
            $k1 = 'music__index__';
            //$k2a1 = str_replace(' ','-t1-',basename($folder['realPath']));
            //$k2a1 = str_replace('/','-t2-',$k2a1);
            $k2a1 = basename($folder['realPath']);

            //$k2a2 = str_replace(' ','-t1-',basename($folder['realPath']));
            //$k2a2 = str_replace('/','-t2-',$k2a2);
            //$k2a2 = basename($folder['realPath']);
            //$k3 = $k1.$k2a1;

            $files = getFilePathList ($folder['path'], false, '/.*.mp3/i', null, ['file']);
            //echo '<pre style="background:navy;color:white;">'; var_dump ($files); echo '</pre>';
            if (count($files)===0) continue;
            $k3++;
            //var_dump ($files); die();
            //$k = $folder['webPath'];
            /*
            $ffi = [];
            foreach ($files as $idx2 => $file) {
                $f = str_replace($appFolder.'/music/','','/'.$file['webPath']);
                //var_dump ($f); die();
                //$f = str_replace(' ','-t0000-',$f); // start unicode compatible custom translation table for JSON files :
                //$f = str_replace('/','-t0001-',$f);
                //$f = str_replace('(','-t0002-',$f);
                //$f = str_replace(')','-t0003-',$f);
                $ffi[] = encode_base64_url($f);
            }
            */

            /*
            $views[] = [
                $k3 => [
                    $appFolder => [
                        'relPath' => str_replace($appFolder.'/music','','/'.$folder['webPath'])//,
                        //'set' => $k2a1
                    ]
                ]
            ] ;
            */

            $folderDurationInSeconds = 0;
            foreach ($files as $idx=>$file) {
                $f = $file['path'];
                if (true) {
                    $exec = 'mp3info -p "%S" "'.$f.'"';
                    $output = null;
                    $result = null;
                    exec ($exec, $output, $result);

                    if ($result===0) {
                        $folderDurationInSeconds += intval($output[0]);
                    }
                } else {
                    $folderDurationInSeconds += 10;
                }
                //echo '<pre style="background:navy;color:lime;">'; var_dump ($f); var_dump ($output); var_dump ($result); echo '</pre>';
            }

            $appID = 0;
            $views[] = [
                $k3 => [
                    $appID => [
                        'aid' => $appID,
                        'fds' => $folderDurationInSeconds,
                        'rp' => str_replace('///','/',str_replace($appFolder.'/music','','/'.$folder['webPath']))//,
                        //'set' => $k2a1
                    ]
                ]
            ] ;

        }
        $json = array();
        $urls = array();
        //echo'<pre>';    var_dump($views);die();
        // ONLY SLOWS THINGS DOWN CONSIDERABLY : file_put_contents(dirname(__FILE__).'/index.foldersAndFiles.json', json_encode($folders));
    };

    $json = [];
    $urls = [];
    foreach ($views as $viewIDX => &$view) {
        foreach ($view as $viewIDX2 => &$app) {
            foreach ($app as $appFolder2 => &$viewSettings) {
                $urls[$viewIDX2] = '/view/'.encode_base64_url(json_encode($viewSettings)).'?idxStart='.$idxStart.'&pw='.$pw;
            }
        }
    };
    if (!$read) file_put_contents(dirname(__FILE__).'/index.views.json', json_encode($views));

    //$rescanContent = false; // not really cheating when you're bypassing 10 minutes worth of mp3info exec() calls
    if ($rescanContent) {
        function arrayWalk_key_buildMenu ($cd) {
            global $naWebOS;

            $ki = $cd['params']['i'];
            $cd['params']['i']++;
            $t = 0;
            $views = &$cd['params']['views'];
            $urls = &$cd['params']['urls'];
            $html = &$cd['params']['html'];
            if ($ki<1) return false;

            foreach ($views[$ki-1][$ki] as $af => &$vs) {
                break;
            }

            $doUL = false;
            if (is_array($cd['v'])) {
                $doUL = true;
                $cd['level']++;
            };

            if ($doUL) {
                $html .= $naWebOS->html($cd['level'], '<ul>');//<li><a href="'.$href.'">'.$cd['k'].'</a></li><ul>'.PHP_EOL;
            } else {
                $html .= $naWebOS->html($cd['level'], '</ul></li>');
            }

            $u = encode_base64_url(json_encode([
                'fds' => $folderDurationInSeconds,
                'rp' => str_replace($appFolder.'/music','','/'.$folder['webPath'])//,
            ]));

            $html .= $naWebOS->html($cd['level'], '<li><a href="'.$urls[$ki].'">'.$vs['rp'].'</a>');
        }

        $folders = getFilePathList ($rf,false,'/.*/',null,['dir'], null, null, true);
        //echo '<pre style="color:yellow;font-size:small;">'; var_dump ($folders); echo '</pre>'; exit;
        $keyCount = 0;
        $valueCount = 0;
        $html = '<li><a href="/music?pw='.$_GET['pw'].'">Music</a><ul>';
        $params = array (
            'debugMe' => true,
            'a' => &$folders,
            'i' => 1,
            'keyCount' => &$keyCount,
            'valueCount' => &$valueCount,
            'urls' => &$urls,
            'views' => &$views,
            'html' => &$html
        );
        walkArray ( $folders, 'arrayWalk_key_buildMenu', null, false, $params );
        file_put_contents(dirname(__FILE__).'/mainmenu.liOnly.php', $params['html'].'</ul></li></ul></li></ul>');
    }

    //echo '<pre>'; var_dump($views); die();
    //echo '<pre>'; var_dump($urls); die();

    /*
    if (file_exists(dirname(__FILE__).'/index.files.json') && (!array_key_exists('rc',$_GET) || $_GET['rc']!=='true')) {
        //$files = json_decode (file_get_contents(dirname(__FILE__).'/index.files.json'),true);
    //} else {
        $files = getFilePathList ($rf,true,'*.*',null,['file']);
        //var_dump($files); die();
        file_put_contents(dirname(__FILE__).'/index.files.json', json_encode($folders));
    };
    var_dump (count($files)); die();
    */

?>
    <link href="https://fonts.googleapis.com/css?family=Krona+One&display=swap" rel="stylesheet"> 
	<script type="text/javascript">
        $(document).ready(function(){
            delete na.site.settings.loadingApps;
            delete na.site.settings.running_loadContent;
            delete na.site.settings.running_loadTheme;
        })
	</script>

    <style>
        p {
            color : white;
            background : rgba(0,0,0,0.4);
            border-radius : 14px;
        }
        li {
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        #pageTitle {
            display : inline-block;
        }

        .container {
            display : flex;
            justify-items : center;
            align-items : start;
            justify-content : center;
            align-content : start;
            width : 100%;
            height : 100%;
        }

        .bg {
            display : inline-block;
            background : rgba(0,0,0,0.4);
            border-radius : 14px;
            height : fit-content;
        }
        p.p2 { width : 380px; text-align:center;}
        .naMusicPlayer_folder {
            font-size : large;
            font-weight : bold;
            color : skyblue;
            width : fit-content;
            display:inline-block;
            padding : 10px;
            margin : 15px;
            background : rgba(0,0,0,0.4);
            border-radius : 15px;
            border : 1px solid grey;
            box-shadow : inset 2px 2px 4px 1px rgba(0,0,0,0.555), 0px 0px 4px 1px rgba(0,0,0,0.555), 2px 2px 5px 2px rgba(0,0,0,0.7);
        }
        .paginationIdx.active a {
            color : yellow;
        }
    </style>

    <div>
        <?php
            if (false) {
                $di = [
                    'matches' => $views
                ];
                $cmd = [
                    'di' => $di,
                    '{"HTML_className":"naWebOS-debug-outer-DIV"}' => 'naWebOS_desktopos_info',
                    '$appFolder' => $appFolder
                ];
                naWebOS_output_debug_info ($cmd);
                //echo '<pre style="color:lime;">'; var_dump ($views); //die();
            };
            //echo '<pre style="color:lime;background:blue;">'; var_dump ($views); echo '</pre>'; //die();

            $k4 = 0;
            $k5 = 0;

            foreach ($views as $k1 => $v1) {
                foreach ($v1 as $k2 => $v2) {
                    foreach ($v2 as $k3 => $v3) {
                        if ($k4 >= $idxStart && $k4 < $idxStart + $linksOnPage) {
                            $pageDurationInSeconds += $v3['fds'];
                        }
                        $collectionDurationInSeconds += $v3['fds'];
                        $k4++;
                    }
                }
            };
            $k4 = 0;
            $pagerHTML = '<br/><div style="z-index:10000000000000;position:relative;width:fit-content;background:rgba(0,0,50,0.8);border:2px ridge skyblue; border-radius:20px;   padding:20px;margin:10px;box-shadow:inset 2px 2px 3px 1px rgba(0,0,0,0.7), 3px 3px 4px 1px rgba(0,0,0,0.8);">';
            foreach ($views as $k1 => $v1) {
                foreach ($v1 as $k2 => $v2) {
                    foreach ($v2 as $k3 => $v3) {

                        $class = 'paginationIdx';
                        if ($k5 >= $idxStart && $k5 < $idxStart + $linksOnPage) {
                            $class .= ' active';
                        }

                        if ($k5===0 || $k5/$linksOnPage == intval($k5/$linksOnPage) ) {
                            $pagerHTML .= '<span class="'.$class.'" style="font-size:x-large;font-weight:bold;margin:5px;"><a href="/music?pw='.$pw.'&idxStart='.$k5.'">'.(($k5/$linksOnPage)+1).'</a>&nbsp;</span>';
                        }
                        $k5++;
                    }
                }
            }
            $pagerHTML .= '<br/>(<span style="color:lime">'.secondsToTimeString($pageDurationInSeconds).' on this page,</span> <span style="color:white;">'.secondsToTimeString($collectionDurationInSeconds).' overall.</span>) (<a href="/musicCopyrightDisclaimer" class="nomod noPushState" target="_new">Copyright disclaimer</a>)<br/>';
            $pagerHTML .= '</div>';
            echo $pagerHTML;
            echo '<div style="">';
            foreach ($views as $k1 => $v1) {
                foreach ($v1 as $k2 => $v2) {
                    foreach ($v2 as $k3 => $v3) {
                        if ($k4 >= $idxStart && $k4 < $idxStart + $linksOnPage) {
                            $viewName = $k2;//$v3['seoValue'];
                            $appFolder = $k3;
                            //$json2load = $json[$viewName];
                            //$url = '/view/'.encode_base64_url($json2load);
                            $url = $urls[$viewName];
                            //$files = $v3['files'];

                            //echo '<pre style="color:skyblue;">'; var_dump ($v3); echo '</pre>'; die();
                            //var_dump ($v3);   var_dump($urls); die();
            ?>
                            <div class="naMusicPlayer_folder"><a href="<?php echo $url;?>"><span><?php echo $v3['rp'].'<br/>('.secondsToTimeString($v3['fds']).')'?></span></a></div>
            <?php
                        }
                        $k4++;
                    }
                }
            }

            echo '</div>';
        ?>
    </div>
