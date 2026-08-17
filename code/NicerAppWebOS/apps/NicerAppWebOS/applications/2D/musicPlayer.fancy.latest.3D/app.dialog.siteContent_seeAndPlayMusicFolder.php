<?php
require_once (realpath(dirname(__FILE__).'/../../../../../..').'/NicerAppWebOS/boot.php');
global $naWebOS; global $naLAN;
$view = $naWebOS->view;
//echo '<pre>'; var_dump ($view); die();

//$setPath = $view['folder']['path'];
//$authorEmail = 'rene.veerman.netherlands@gmail.com';
//$spacer = "\n\t\t\t\t";
    $appFolder = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.javascriptRendering';
    $rf = dirname(__FILE__).'/music';

        $setPath = $rf.$view[$appFolder]['webRelPath'];
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
        //var_dump ($files); exit();
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

    $authorEmail = 'rene.veerman.netherlands@gmail.com';
    $spacer = "\n\t\t\t\t";
    //$htmlIntro = file_get_contents ($setPath.'/index.html');
    //$htmlTitleMeta = file_get_contents ($setPath.'/index.title_meta.html');
?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="en">
        <meta http-equiv="content-language" content="english">
        <link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.javascriptRendering/index.css?changed=<?php echo date('Ymd-His', filemtime(dirname(__FILE__).'/index.css'));?>"/>
        <link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/3rd-party/jQuery/jPlayer-2.9.1/jplayer.vivid.css"/>

        <!--<script src="/NicerAppWebOS/3rd-party/jQuery/jquery-ui-1.12.1/jquery-ui.js"></script>-->
        <script type="text/javascript" src="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.javascriptRendering/app.musicPlayer_siteContent.source.js?changed=<?php echo date('Ymd-His', filemtime(dirname(__FILE__).'/app.musicPlayer_siteContent.source.js'));?>"></script>
        <script type="text/javascript">
            delete na.site.settings.current.loadingApps;

            delete na.site.settings.current.startingApps;
        </script>

        <div id="horizontalMover__containmentBox2" style="display:none;position:absolute;height:20px;border-radius:8px;background:black;opacity:0.2"></div>
        <div id="horizontalMover__containmentBox1" style="display:none;position:absolute;height:16px;top:2px;border-radius:4px;background:black;opacity:0.0"></div>
        <div id="horizontalMover" class="draggable ui-widget-content" style="display:none;position:absolute;top:4px;height:10px;width:730px;border-radius:4px;background:navy;border : 1px solid white;opacity:0.7"></div>

        <div id="app__musicPlayer__header" class="vividDialog" style="opacity:0.0001;position:absolute;display:flex;background:rgba(0,0,0,0.4);border:1px solid white;border-radius:15px;font-weight:bold;justify-content:center;vertical-align:middle;align-content: center;align-items : center;padding:5px;margin-bottom:10px;">
            <div class="vividDialogContent" style="text-align:center;margin:2px;width:100%;">
            <h1 class="pageTitle vividTextCSS">
                <span class="contentSectionTitle1_span" id="folderName"><?php echo str_replace('_', ' ', $view['set']) ?></span>&nbsp;
                on&nbsp;<a href="/music"><span class="contentSectionTitle3_span">https://nicer.app/music</span></a>.
            </h1>
            </div>
        </div>

        <div id="mp3s" class="vividMenu vividScrollpane noFlex naNoComments" type="vertical" theme="dark" style="overflow:hidden;overflow-y:auto;opacity:0.001;position:absolute;text-align:center;width:100%;">
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
                    $filez2[$idx] = $fileLabel;

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
                    $fileLabel = preg_replace('/\.mp3$/','',$fileLabel);
                    $filez[$idx] = $fileLabel;

                    $idx++;
                }
                asort ($filez);
                //echo '<pre>'; var_dump($filez); die();
                $idx = 0;
                foreach ($filez as $idx=>$fn) {
                    $id = 'mp3_'.$idx;
                    echo "\t\t".'<div id="'.$id.'" file="'.$filez2[$idx].'" class="mp3 vividButton" theme="dark" style="" onclick="na.mp.selectMP3(\''.$id.'\', \''.str_replace("'", "\\'", $view[$appFolder]['webRelPath'].$filez2[$idx]).'\');" style="width:220px"><div class="vdBackground"></div><span style="opacity:1">'.$fn.'</span></div>'.PHP_EOL;
                    $idx++;
                }
    ?>
        </div>

        <div id="app__musicPlayer__player" class="vividDialog naNoComments" style="overflow:visible;position:absolute;width:320px;height:120px;">
            <audio id="audioTag">
                <?php
                foreach ($filez2 as $idx=>$fn) {
                    $id = 'mp3Source_'.$idx;
                    echo PHP_EOL;
                    echo "\t\t\t".'<source id="'.$id.'" src="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.javascriptRendering/music'.$view[$appFolder]['webRelPath'].$fn.'" type="audio/mpeg">'.PHP_EOL;
                }
                ?>
            </audio>

            <div class="audioPlayerUI">
                <div class="audioPlayerButtons">
                    <div id="btnPlayPause" class="vividButton4" buttonType="btn_audioVideo_playPause" onclick="na.musicPlayer.playpause()"></div>
                    <div id="btnMuteUnmute" class="vividButton4" buttonType="btn_audioVideo_muteUnmute" onclick="na.musicPlayer.mute()"></div>
                    <div id="btnShuffle" class="vividButton4" buttonType="btn_audioVideo_shuffle" onclick="na.musicPlayer.toggleShuffle()"></div>
                    <div id="btnRepeat" class="vividButton4" buttonType="btn_audioVideo_repeat" onclick="na.musicPlayer.toggleRepeat()"></div>
                </div>
                <div class="flexBreak"></div>
                <div class="audioPlayerControls">
                    <div class="audioVolumeBar" onclick="na.musicPlayer.setVolume(event);">
                        <div class="audioVolumeBar_setting" style="width:calc(100% - 4px);"></div>
                    </div>
                    <div class="audioSeekBar" onclick="na.musicPlayer.seek(event);">
                        <div class="audioSeekBar_setting" style="width:0px;"></div>
                    </div>
                </div>
                <div class="audioPlayerControlsLabels">
                    <div class="audioVolumeBarLabel" style="text-align:center">Volume : 100</div>
                    <div class="audioSeekBarLabel">
                        <div class="audioSeekBarLabel_currentTime">0:00</div>
                        <div class="audioSeekBarLabel_length">1:15:00</div>
                    </div>
                </div>
            </div>
        </div>

        <div id="app__musicPlayer__playlist" class="vividDialog naNoComments" theme="dark" style="text-align:center;opacity:0.001;overflow:visible;position:absolute; width:300px;height:300px;">
            <h2 class="vt backdropped" style="padding:0px !important; margin:20px !important;display:flex;justify-content:center;align-items:center;width:auto;height:50px;font-size:10px;background:rgba(0,0,255,0.25);color:white;border-radius:10px;box-shadow:2px 2px 3px 2px rgba(0,0,0,0.7);">Playlist<br/>(drag and drop items onto this window)</h2>
            <ul id="playlist" class="vividScrollpane" style="width:100%;height:calc(100% - 50px);"></ul>
        </div>

        <div id="app__musicPlayer__description" class="vividDialog naNoComments" theme="dark" style="opacity:0.001;overflow:visible;position:absolute;width:320px;height:300px;">
            <div class="vividDialogContent" style="font-size:inherit">
                <div id="mp3descText" style="font-size:inherit"></div>
                <div id="siteIntroText">

                    <?php //echo $htmlIntro?>
                </div>
            </div>
        </div>
