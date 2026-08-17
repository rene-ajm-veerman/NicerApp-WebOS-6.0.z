<?php
require_once (realpath(dirname(__FILE__).'/../../../../../..').'/NicerAppWebOS/boot.php');
global $naWebOS; global $naLAN;
$view = $naWebOS->view;
foreach ($view as $appKey => $appRec) break;
//echo '<pre>'; var_dump ($view); die();

//$setPath = $view['folder']['path'];
//$authorEmail = 'rene.veerman.netherlands@gmail.com';
//$spacer = "\n\t\t\t\t";
    $appFolder = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.beatPulse';
    $rf = __DIR__.'/music';
    //var_dump ($rf); exit;
    $views = json_decode(file_get_contents(__DIR__.'/index.views.json'),true);
    $pw = 'outtaLuck';
    if (array_key_exists('pw',$_GET)) $pw = $_GET['pw'];
    $linksOnPage = 100;
    $collectionDurationInSeconds = 0;
    $pageDurationInSeconds = 0;
    $idxStart = 0;
    if (array_key_exists('idxStart', $_GET)) $idxStart = intval($_GET['idxStart']); else $idxStart = 0;


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
        $files = getFilePathList ($setPath, true, FILE_FORMATS_mp3s, null, array('file'), 1, 1, false);
        //echo 'JSON, $files : '.count($files).'<pre>'.json_encode($files,JSON_PRETTY_PRINT).''.PHP_EOL; //exit;

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
                    //echo '<pre style="background:ivory;color:blue;">';var_dump (count($files)); var_dump ($files); exit;
                    //echo '<div class="vividScrollpane" style="height:100%;width:100%;">';
                    foreach ($files as $file=>$fp) {
                        $idx++;
                        //var_dump ($idx);
                        if (
                             !preg_match('/\.mp3$/',$fp['webPath'])
                        ) continue;
                        //if ($idx < $idxStart) continue;
                        //if ($idx > $idxStart + $linksOnPage) break;
                        $fn = str_replace('//','/',$fp['webPath']);

        //echo '<pre>'; var_dump($fn);die();
                        $fileLabel = $fn;//$filez[$idx];
                        $fileLabel = str_replace($appFolder.'/music', '', $fileLabel);
                        $fileLabel = str_replace(__DIR__.'/music','',$fileLabel);
                        //var_dump ($fileLabel);

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
                        //$fl = str_replace ('[via torchbrowser.com].mp4','',$fl);
                        $fl = str_replace ('[via torchbrowser.com]','',$fl);
                        $fl = str_replace ('.mp4','',$fl);
                        $fl = preg_replace ('/-[\w\d]+\.mp3/','.mp3',$fl);
                        $fl = str_ireplace ('/- Youtube/i','.mp3',$fl);
                        $fl = preg_replace ('~.*/~', '', $fl);
                        //echo $fl.'<br/>';
                        $fl = str_replace ('.mp3','',$fl);
                        $filez[$fn] = $fl;
                    }
                    //echo '</div>';
                    ksort ($filez);
                    //echo '<pre>'; var_dump ($filez); exit();

            foreach ($filez as $file1 => $fileTitle)
                    break;
        ?>
        <div style="float:right;height:500px;width:750px">
            <?php
            if (true) {
                global $naWebOS;
                $_GET['file'] = $file1;
                $_GET['time'] = 0;
                $fn = $naWebOS->path.$appFolder.'/beatPulse-0.1.0/index3.php';//?file='.str_replace(' ','%20',$file1).'&time=0';
                //echo $fn;
                $c = require_return ($fn);
                echo '<iframe id="visualizer" style="width:100%;height:100%;border:none;">'.$c.'</iframe>';
            }
            ?>
            <!--<iframe id="visualizer" style="width:100%;height:100%;border:none;" src=""></iframe>-->
        </div>
        <?php //echo 't942:<pre style="background:green;color:ivory;margin:20px;padding:10px;border-radius:10px">'; var_dump($views); exit; ?>

        <h1 id="pageTitle" class="naVividTextCSS" style="font-size:200%;"><?php echo $view['rp']?> on said.by/music</h1>

        <?php
            $k4 = 0;
            $k5 = 0;

            $idx2 = 0;
            foreach ($views as $k1 => $v1) {
                foreach ($v1 as $k2 => $v2) {
                    foreach ($v2 as $k3 => $v3) {
                        //echo '<pre>'; var_dump($view); var_dump($v3); exit;
                        $idx2++;
                        if ($idx2 > $idx - 100 && $idx2 < $idx) {
                            $pageDurationInSeconds += $v3['fds'];
                        }
                        $collectionDurationInSeconds += $v3['fds'];
                        $k4++;
                    }
                }
            };
            $k4 = 0;
            $pagerHTML = '<br/><div style="z-index:10000000000000;position:relative;width:fit-content;background:rgba(0,0,50,0.8);border:2px ridge skyblue; border-radius:20px;padding:20px;margin:10px;box-shadow:inset 2px 2px 3px 1px rgba(0,0,0,0.7), 3px 3px 4px 1px rgba(0,0,0,0.8);">';

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
            //exit;
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

        <div id="mp3s" class="vividScrollpane noFlex naNoComments" type="vertical" theme="dark" style="wrap:inherit;overflow:hidden;overflow-y:auto;opacity:1;position:absolute;bottom:0px;text-align:center;width:auto;height:calc(100% - 520px);margin-top:20px;">
        <?php
            //echo '<pre>'; var_dump($filez); echo '</pre>'; //exit;
            $idx = 0;
            //if (false)
            foreach ($filez as $fn=>$fl) {
                $html = "\t\t".'<span id="mp3Div_'.$idx.'" class="mp3Div">'.PHP_EOL;
                $html .= "\t\t\t".'<a target="naMP3" id="aTag_'.$idx.'" href="javascript:na.music.load(\''.$view['rp'].$fn.'\');" type="audio/mpeg" style="margin-left:20px;font-weight:bold">'.$fl.'</a>'.PHP_EOL;
                $html .= "\t\t\t".'<audio id="audioTag_'.$idx.'"  onplay="var timeInSeconds = $(\'#audioTag_'.$idx.'\')[0].currentTime; $(\'#visualizer\')[0].src=\''.$appFolder.'/beatPulse-0.1.0/index3.php?file=music'.$view['rp'].$fn.'&time=\'+timeInSeconds; $(\'.mp3Div\').removeClass(\'playing\');$(event.currentTarget.parentNode).removeClass(\'paused\').addClass(\'playing\');" onended="$(event.currentTarget.parentNode).removeClass(\'playing\').removeClass(\'paused\').addClass(\'ended\')" onpause="$(event.currentTarget.parentNode).removeClass(\'playing\').removeClass(\'ended\').addClass(\'paused\')" onunpause="$(event.currentTarget.parentNode).removeClass(\'paused\').removeClass(\'ended\').addClass(\'playing\')" style="display:flex;order:2;align-items:center;margin:10px;opacity:0.68" onseeked="var frameRef = $(\'#visualizer\')[0]; var audioEl = $(\'#audio\', frameRef.contentWindow ? frameRef.contentWindow.document : frameRef.contentDocument)[0]; audio.play(0); audio.currentTime = $(\'#audioTag_'.$idx.'\')[0].currentTime; event.preventDefault();" controls>'.PHP_EOL;
                //echo "\t\t\t".'<audio id="audioTag_'.$idx.'"  onplay="na.m.addLogEntry(\'naMP3 : Now playing : '.$view['rp'].$fn.'\',\'mp3Change_start\');$(\'#audioTag_'.$idx.'\')[0].volume = 1; var timeInSeconds = Math.floor($(\'#audioTag_'.$idx.'\')[0].currentTime); na.music.load(\'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.beatPulse/music'.$view['rp'].$fn.'\'); $(\'.mp3Div\').removeClass(\'playing\');$(event.currentTarget.parentNode).removeClass(\'paused\').addClass(\'playing\');" onended="na.m.addLogEntry(\'naMP3 : Now ended : '.$view['rp'].$fn.'\',\'mp3Change_ended\');$(event.currentTarget.parentNode).removeClass(\'playing\').removeClass(\'paused\').addClass(\'ended\')" onpause="na.m.addLogEntry(\'naMP3 : Now pausing : '.$view['rp'].$fn.'\',\'mp3Change_pausing\');$(event.currentTarget.parentNode).removeClass(\'playing\').removeClass(\'ended\').addClass(\'paused\')" onunpause="na.m.addLogEntry(\'naMP3 : Now unpausing : '.$view['rp'].$fn.'\',\'mp3Change_unpausing\');$(event.currentTarget.parentNode).removeClass(\'paused\').removeClass(\'ended\').addClass(\'playing\')" style="display:flex;order:2;align-items:center;margin:10px;opacity:0.68" onseeked="na.m.addLogEntry(\'naMP3 : Now unpausing : '.$view['rp'].$fn.'\',\'mp3Change_onseeked\');$(\'#audioTag_'.$idx.'\')[0].volume = 0.007; var timeInSeconds = Math.floor($(\'#audioTag_'.$idx.'\')[0].currentTime); $(\'#visualizer\')[0].src=\''.$appFolder.'/beatPulse-0.1.0/index3.php?file=/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.beatPulse/music'.$view['rp'].$fn.'&time=\'+timeInSeconds;" controls>'.PHP_EOL;
                $html .= "\t\t\t\t".'<source id="'.$idx.'" src="'.$fn.'" type="audio/mpeg"/>'.PHP_EOL;
                $html .= "\t\t\t".'</audio>'.PHP_EOL;
                $html .= "\t\t".'</span>'.PHP_EOL;
                echo $html;
                $idx++;
            }
        ?>
        </div>
