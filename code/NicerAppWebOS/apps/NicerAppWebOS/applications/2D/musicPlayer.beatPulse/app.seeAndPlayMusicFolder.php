<?php
require_once (realpath(dirname(__FILE__).'/../../../../../..').'/NicerAppWebOS/boot.php');
global $naWebOS; global $naLAN;
$view = $naWebOS->view;
//echo '<pre>'; var_dump ($view); die();
$cacheFile = dirname(__FILE__).'/index.views.json';
$rescanContent = (!file_exists($cacheFile) || !is_readable($cacheFile) || file_get_contents($cacheFile)=='');
if ($rescanContent) {
    echo '<h1>Content scan. Refresh this page.</h1>';
}
//$setPath = $view['folder']['path'];
//$authorEmail = 'rene.veerman.netherlands@gmail.com';
//$spacer = "\n\t\t\t\t";
    $appFolder = realpath(dirname(__FILE__).'/../../../../../..').'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.beatPulse';
    $rf = dirname(__FILE__).'/music';
    $views = json_decode(file_get_contents($cacheFile),true);
    $pw = 'outtaLuck';
    if (array_key_exists('pw',$_GET)) $pw = $_GET['pw'];
    $linksOnPage = 100;
    $collectionDurationInSeconds = 0;
    $pageDurationInSeconds = 0;
    $idxStart = 0;
    if (array_key_exists('idxStart', $_GET)) $idxStart = intval($_GET['idxStart']);
    //if (count($views)===0) {

        $setPath = $rf.$view['rp'];
        //var_dump ($setPath); exit();
        /*
        if (file_exists($setPath.'/regex_filenameFilter.js-regexps.json')) {
            $res = json_decode(file_get_contents($setPath.'/regex_filenameFilter.js-regexps.json'),true);
            //var_dump(file_get_contents($setPath.'/regex_filenameFilter.js-regexps.json'));        var_dump($res);die();
        } else {
            $res = [];
        }
        */

        //var_dump (FILE_FORMATS_mp3s); exit();
        $files = getFilePathList ($setPath, false, FILE_FORMATS_mp3s, null, array('file'), 1, 1, false);
        //echo '<!-- JSON, $files : '.json_encode($files,JSON_PRETTY_PRINT).' -->'.PHP_EOL;
        foreach ($files as $idx => $filepath) {
            $files[$idx] = str_replace(realpath(dirname(__FILE__.'/../..')), '', $files[$idx]);
            $files[$idx] = str_replace('\\\\', '/', $files[$idx]);
            $files[$idx] = str_replace('\\', '/', $files[$idx]);
            /*
            for ($i=0; $i < count($res); $i++) {
                //for ($j=0; $j < count($res[$i]); $j++) {
                    $it = $res[$i];//[$j];
                    $itRegExps = $it[0];
                    $itReplaceString = $it[1];
                    for ($k=0; $k < count($itRegExps); $k++) {
                        $files[$idx] = preg_replace($itRegExps[$k], $itReplaceString, $files[$idx]);
                    }
                //}
            }
            */
        }
        //echo '<pre>'; var_dump ($files); exit();
    //}
    //echo 't66'; var_dump (count($views)); echo '<br/>'.PHP_EOL;
    //echo '6tj'; var_dump ($files); exit;

    $authorEmail = 'rene.veerman.netherlands@gmail.com';
    $spacer = "\n\t\t\t\t";
    //$htmlIntro = file_get_contents ($setPath.'/index.html');
    //$htmlTitleMeta = file_get_contents ($setPath.'/index.title_meta.html');
?>
        <!--<title><?php echo $view['rp'];?> on said.by/music</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="en">
        <meta http-equiv="content-language" content="english">
        -->
        <link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.beatPulse/index.css?changed=<?php echo date('Ymd-His', filemtime(dirname(__FILE__).'/index.css'));?>"/>
        <link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/3rd-party/jQuery/jPlayer-2.9.1/jplayer.vivid.css"/>

        <!--<script src="/NicerAppWebOS/3rd-party/jQuery/jquery-ui-1.12.1/jquery-ui.js"></script>-->
        <!--
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        -->

        <script type="text/javascript" src="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.beatPulse/app.musicPlayer_siteContent.source.js?changed=<?php echo date('Ymd-His', filemtime(dirname(__FILE__).'/app.musicPlayer_siteContent.source.js'));?>"></script>
        <script type="text/javascript">
            $('.mp3').show();
            delete na.site.settings.loadingApps;
            delete na.site.settings.startingApps;
            $(window).resize(function(){
                $('.mp3').show();
            })
        </script>
        <style>
            .paginationIdx.active a {
                color : yellow;
            }
        </style>


        <?php



                    $filez = $filez2 = array();
                    if (file_exists($setPath.'/regex_filenameFilter.js-regexps.json'))
                        $ff = safeLoadJSONfile($setPath.'/regex_filenameFilter.js-regexps.json');
                    else $ff = [];

                    $idx = 0;
                    foreach ($files as $file=>$fp) {
                        $fn = basename($fp['realPath']);
        //echo '<pre>'; var_dump($fn);die();
                        $fileLabel = $fn;//$filez[$idx];

                        foreach ($ff as $i => $it) {
                            //foreach ($ffIt as $j => $it) {
                                //note: $it === $ff[$i][$j];
                                $itRegExps = $it[0];
                                //echo '<pre>';var_dump ($itRegExps); die();
                                $itReplaceString = $it[1];
                                foreach ($itRegExps as $k => $regExp) {
                                    //echo '1:'.$fileLabel.'<br/>';
                                    $fileLabel = preg_replace ($regExp, $itReplaceString, $fileLabel);
                                    //echo '2:'.$fileLabel.'<br/>';
                                }
                            //}
                        }
                        //$fileLabel = preg_replace('/\.mp3$/','',$fileLabel);
                        $fl = $fileLabel;
                        $fl = str_replace ('[via torchbrowser.com].mp4','',$fl);
                        $fl = str_replace ('[via torchbrowser.com]','',$fl);
                        $fl = str_replace ('.mp4','',$fl);
                        $fl = preg_replace ('/-[\w\d]+\.mp3/','.mp3',$fl);
                        $fl = preg_replace ('/\s+\[[\_\-\w\d]+\]/','',$fl);
                        $fl = preg_replace ('/- Youtube/i','.mp3',$fl);
                        $fl = str_replace ('.mp3','',$fl);
                        $fn = str_replace ('#', '%23', $fn);
                        $filez[$fn] = $fl;
                        $idx++;
                    }
                    ksort ($filez);

            foreach ($filez as $file1 => $fileTitle)
                    break;
            //exit;
        ?>
        <!--
        <div style="float:right;height:500px;width:750px">
            <?php
                global $naWebOS;
                $_GET['file'] = $file1;
                $_GET['time'] = 0;
                $fn = $naWebOS->path.$appFolder.'/beatPulse-0.1.0/index3.php';//?file='.str_replace(' ','%20',$file1).'&time=0';
                //echo $fn;
                $c = '';//require_return ($fn);
                echo '<div id="visualizer" style="width:100%;height:100%;border:none;">'.$c.'</div>';
            ?>
            <!- -<iframe id="visualizer" style="width:100%;height:100%;border:none;" src=""></iframe>-- >
        </div>
        -->


        <h1 id="pageTitle" class="naVividTextCSS" style="font-size:200%;"><?php echo $view['rp']?> on <?=$naWebOS->domain?>/music</h1>

        <?php
            $k4 = 0;
            $k5 = 0;

            foreach ($views as $k1 => $v1) {
                foreach ($v1 as $k2 => $v2) {
                    foreach ($v2 as $k3 => $v3) {
                        if ($view['rp'] == $v3['rp']) {
                            $pageDurationInSeconds += $v3['fds'];
                        }
                        $collectionDurationInSeconds += $v3['fds'];
                        $k4++;
                    }
                }
            };
            $k4 = 0;
            $pagerHTML = '<div style="z-index:10000000000000;float:right;position:absolute;top:0px;right:0px;width:fit-content;background:rgba(0,0,50,0.8);border:2px ridge skyblue; border-radius:20px;   padding:20px;margin:10px;box-shadow:inset 2px 2px 3px 1px rgba(0,0,0,0.7), 3px 3px 4px 1px rgba(0,0,0,0.8);">';
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
            $pagerHTML .= '<br/>(<span style="color:lime">'.secondsToTimeString($pageDurationInSeconds).' on this page,</span> <span style="color:white;">'.secondsToTimeString($collectionDurationInSeconds).' overall.</span>)<br/>';
            $pagerHTML .= '</div>';
            echo $pagerHTML;
        ?>
        <script type="text/javascript">




            na.music = {
                load : function (url) {

                    var
                    url = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.beatPulse/beatPulse-0.1.0/index3.php',
                    ac = {
                        type : 'POST',
                        url : url,
                        data : {
                            file : url,
                            time : 0,
                            audioElementID : event.target.id
                        },
                        success : function (data, ts, xhr) {
                            $('#visualizer').html(data);
                        },
                        error : function (xhr, ts, errorThrown) {

                        }
                    };
                    $.ajax(ac);
                }
            };
        </script>

            <div id="mp3s" class="vividScrollpane noFlex naNoComments" type="vertical" theme="dark" style="wrap:inherit;overflow:hidden;overflow-y:auto;opacity:1;position:absolute;bottom:0px;text-align:center;width:auto;height:calc(100% - 123px);margin-top:20px;">
        <?php

                    //echo '<pre>'; var_dump($filez); echo '</pre>'; exit;


                    $idx = 0;
                    foreach ($filez as $fn=>$fl) {
                        echo "\t\t".'<span id="mp3Div_'.$idx.'" class="mp3Div">'.PHP_EOL;
                        echo "\t\t\t".'<a target="naMP3" id="aTag_'.$idx.'" href="javascript:na.music.load(\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.beatPulse/music'.$view['rp'].$fn.'\');" type="audio/mpeg" style="margin-left:20px;font-weight:bold">'.$fl.'</a>'.PHP_EOL;
                        echo "\t\t\t".'<audio id="audioTag_'.$idx.'"  onplay="$(\'#audioTag_'.$idx.'\')[0].volume = 1; var timeInSeconds = $(\'#audioTag_'.$idx.'\')[0].currentTime; $(\'.mp3Div\').removeClass(\'playing\');$(event.currentTarget.parentNode).removeClass(\'paused\').addClass(\'playing\');" onended="$(event.currentTarget.parentNode).removeClass(\'playing\').removeClass(\'paused\').addClass(\'ended\')" onpause="$(event.currentTarget.parentNode).removeClass(\'playing\').removeClass(\'ended\').addClass(\'paused\')" onunpause="$(event.currentTarget.parentNode).removeClass(\'paused\').removeClass(\'ended\').addClass(\'playing\')" style="display:flex;order:2;align-items:center;margin:10px;opacity:0.68" controls>'.PHP_EOL;
                        //echo "\t\t\t".'<audio id="audioTag_'.$idx.'"  onplay="na.m.addLogEntry(\'naMP3 : Now playing : '.$view['rp'].$fn.'\',\'mp3Change_start\');$(\'#audioTag_'.$idx.'\')[0].volume = 1; var timeInSeconds = Math.floor($(\'#audioTag_'.$idx.'\')[0].currentTime); na.music.load(\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.beatPulse/music'.$view['rp'].$fn.'\'); $(\'.mp3Div\').removeClass(\'playing\');$(event.currentTarget.parentNode).removeClass(\'paused\').addClass(\'playing\');" onended="na.m.addLogEntry(\'naMP3 : Now ended : '.$view['rp'].$fn.'\',\'mp3Change_ended\');$(event.currentTarget.parentNode).removeClass(\'playing\').removeClass(\'paused\').addClass(\'ended\')" onpause="na.m.addLogEntry(\'naMP3 : Now pausing : '.$view['rp'].$fn.'\',\'mp3Change_pausing\');$(event.currentTarget.parentNode).removeClass(\'playing\').removeClass(\'ended\').addClass(\'paused\')" onunpause="na.m.addLogEntry(\'naMP3 : Now unpausing : '.$view['rp'].$fn.'\',\'mp3Change_unpausing\');$(event.currentTarget.parentNode).removeClass(\'paused\').removeClass(\'ended\').addClass(\'playing\')" style="display:flex;order:2;align-items:center;margin:10px;opacity:0.68" onseeked="na.m.addLogEntry(\'naMP3 : Now unpausing : '.$view['rp'].$fn.'\',\'mp3Change_onseeked\');$(\'#audioTag_'.$idx.'\')[0].volume = 0.007; var timeInSeconds = Math.floor($(\'#audioTag_'.$idx.'\')[0].currentTime); $(\'#visualizer\')[0].src=\''.$appFolder.'/beatPulse-0.1.0/index3.php?file=/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.beatPulse/music'.$view['rp'].$fn.'&time=\'+timeInSeconds;" controls>'.PHP_EOL;
                        echo "\t\t\t\t".'<source id="'.$idx.'" src="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.beatPulse/music'.$view['rp'].$fn.'" type="audio/mpeg"/>'.PHP_EOL;
                        echo "\t\t\t".'</audio>'.PHP_EOL;
                        echo "\t\t".'</span>'.PHP_EOL;
                        $idx++;
                    }


        ?>

            </div>

