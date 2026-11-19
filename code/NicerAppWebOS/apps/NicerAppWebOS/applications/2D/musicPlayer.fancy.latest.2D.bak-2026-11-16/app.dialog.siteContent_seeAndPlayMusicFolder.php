<?php
require_once (realpath(dirname(__FILE__).'/../../../../../..').'/NicerAppWebOS/boot.php');
global $naWebOS; global $naLAN;
$view = $naWebOS->view;
//echo '<pre>'; var_dump ($view); //die();
//debug_print_backtrace();

//$setPath = $view['folder']['path'];
//$authorEmail = 'rene.veerman.netherlands@gmail.com';
//$spacer = "\n\t\t\t\t";
    $appFolder = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.fancy.latest.2D';
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
        <link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.fancy.latest.2D/index.css?changed=<?php echo date('Ymd-His', filemtime(dirname(__FILE__).'/index.css'));?>"/>
        <link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/3rd-party/jQuery/jPlayer-2.9.1/jplayer.vivid.css"/>

        <!--<script src="/NicerAppWebOS/3rd-party/jQuery/jquery-ui-1.12.1/jquery-ui.js"></script>-->
        <script type="text/javascript" src="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.fancy.latest.2D/app.musicPlayer_siteContent.source.js?changed=<?php echo date('Ymd-His', filemtime(dirname(__FILE__).'/app.musicPlayer_siteContent.source.js'));?>"></script>
        <script type="text/javascript">
            delete na.site.settings.loadingApps;

            delete na.site.settings.startingApps;
        </script>

        <div id="horizontalMover__containmentBox2" style="display:none;position:absolute;height:20px;border-radius:8px;background:black;opacity:0.2"></div>
        <div id="horizontalMover__containmentBox1" style="display:none;position:absolute;height:16px;top:2px;border-radius:4px;background:black;opacity:0.0"></div>
        <div id="horizontalMover" class="draggable ui-widget-content" style="display:none;position:absolute;top:4px;height:10px;width:730px;border-radius:4px;background:navy;border : 1px solid white;opacity:0.7"></div>

        <div id="app__musicPlayer__header" class="vividDialog" style="opacity:0.0001;position:absolute;display:flex;padding:5px;margin-bottom:10px;">
            <div class="vividDialogBackground1"></div>
            <div class="vividDialogContent" style="text-align:center;margin:2px;width:100%;">
            <h1 class="pageTitle vividTextCSS">
                <span class="contentSectionTitle1_span" id="folderName"><?php echo str_replace('_', ' ', $view[$appFolder]['set']) ?></span>&nbsp;
                on&nbsp;<a href="/music-2D-fancy"><span class="contentSectionTitle3_span">https://nicer.app/music-2D-fancy</span></a>.
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
                    echo "\t\t".'<div id="'.$id.'" file="'.$filez2[$idx].'" class="mp3 vividButton" theme="dark" style="" onclick="na.mp.selectMP3(\''.$id.'\', \''.str_replace("'", "\\'", $view[$appFolder]['webRelPath'].$filez2[$idx]).'\');" style="width:220px"><span style="opacity:1">'.$fn.'</span></div>'.PHP_EOL;
                    $idx++;
                }
    ?>
        </div>

        <div id="app__musicPlayer__player" class="vividDialog naNoComments" style="overflow:visible;position:absolute;opacity:0.0001;">
            <audio id="audioTag">
                <?php
                foreach ($filez2 as $idx=>$fn) {
                    $id = 'mp3Source_'.$idx;
                    echo PHP_EOL;
                    echo "\t\t\t".'<source id="'.$id.'" src="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.fancy.latest.2D/music'.$view[$appFolder]['webRelPath'].$fn.'" type="audio/mpeg">'.PHP_EOL;
                }
                ?>
            </audio>

            <!--
            <div class="audioPlayerUI">
            <div class="control-rows">
                <div class="audioPlayerButtons control-row">
                    <div id="btnPlayPause" class="vividButton4" buttonType="btn_audioVideo_playPause" onclick="na.musicPlayer.playpause()"></div>
                    <div id="btnMuteUnmute" class="vividButton4" buttonType="btn_audioVideo_muteUnmute" onclick="na.musicPlayer.mute()"></div>
                    <div id="btnShuffle" class="vividButton4" buttonType="btn_audioVideo_shuffle" onclick="na.musicPlayer.toggleShuffle()"></div>
                    <div id="btnRepeat" class="vividButton4" buttonType="btn_audioVideo_repeat" onclick="na.musicPlayer.toggleRepeat()"></div>
                </div>
                <!--<div class="flexBreak"></div>-- >
            </div>
            <div class="control-rows">
                <div class="audioPlayerControls control-row">
                    <div class="audioVolumeBar" onclick="na.musicPlayer.setVolume(event);">
                        <div class="audioVolumeBar_setting""></div>
                    </div>
                    <div class="audioSeekBar" onclick="na.musicPlayer.seek(event);">
                        <div class="audioSeekBar_setting" style="width:0px;"></div>
                    </div>
                </div>
            </div>
            <div class="control-rows">
                <div class="audioPlayerControlsLabels control-row">
                    <div class="audioVolumeBarLabel" style="text-align:center">Volume : 100</div>
                    <div class="audioSeekBarLabel">
                        <div class="audioSeekBarLabel_currentTime">0:00</div>
                        <div class="audioSeekBarLabel_length">1:15:00</div>
                    </div>
                </div>
            </div>
            -->






            <style>
            .audioPlayerUI {
                display: grid;
                grid-template-areas:
                "controls       controls    controls"
                "statsVolume    stats       statsProgress"
                "infoVolume     info        infoProgress";

                        grid-template-columns: auto auto auto;   /* adjust 180px to your cover size */
                        grid-template-rows: auto auto auto;
                        gap: 12px 20px;
                        align-items: center;
                        padding: 16px;
                        border-radius: 12px;
                        max-width: 1000px;
                        margin: 0 auto;
            }

            /* Example child classes – adjust these to match your actual div classes */
            .audioPlayerUI > div, .playback-controls { z-index : 99999999; justify-self:space-evenly; }
            .audioPlayerUI > div > div > div { display : inline-block; }
            .audioPlayerUI .playback-controls  { grid-area: controls; display : flex; justify-content : space-evenly; }
            .audioPlayerUI .progress-container { grid-area: statsProgress; }
            .audioPlayerUI .volume-container   { grid-area: statsVolume; }
            .audioPlayerUI .infoVolume-container { grid-area: infoVolume; }
            .audioPlayerUI .infoProgress-container { grid-area: infoProgress;  }
            .audioSeekBarLabel { width : 100%; display : flex; justify-content : space-evenly;}

            /* Make it stack nicely on smaller screens
            @media (max-width: 768px) {
                .audioPlayerUI {
                    grid-template-areas:
                    "art     art"
                    "info    info"
                    "controls controls"
                    "progress progress"
                    "extras  volume";
                    grid-template-columns: 1fr 1fr;
                }
            }
            */
            </style>

            <div class="audioPlayerUI">
            <!-- 1. Album art -- >
            <div class="cover-art">
            <img src="your-album-cover.jpg" alt="Album Art" style="width:100%; border-radius:8px;">
            </div>

            <!-- 2. Track info -- >
            <div class="track-info">
            <div class="track-title">Track Name Here</div>
            <div class="track-artist">DJ Firesnake</div>
            <div class="track-album">Mix / Release Name</div>
            </div>
            -->

            <!-- 3. Playback controls -->
            <div class="playback-controls">
                <div id="btnPlayPause" class="vividButton4" buttonType="btn_audioVideo_playPause" onclick="na.musicPlayer.playpause()"></div>
                <div id="btnMuteUnmute" class="vividButton4" buttonType="btn_audioVideo_muteUnmute" onclick="na.musicPlayer.mute()"></div>
                <div id="btnShuffle" class="vividButton4" buttonType="btn_audioVideo_shuffle" onclick="na.musicPlayer.toggleShuffle()"></div>
                <div id="btnRepeat" class="vividButton4" buttonType="btn_audioVideo_repeat" onclick="na.musicPlayer.toggleRepeat()"></div>
            </div>

            <!-- 4. Progress bar -->
            <div class="progress-container" style="width:100%;">
                <div class="audioSeekBar" style="width:100%" onclick="na.musicPlayer.seek(event);">
                    <div class="audioSeekBar_setting" style="width:0px;"></div>
                </div>
            </div>

            <!-- 5. Extra buttons (lyrics, queue, etc.) -- >
            <div class="extra-buttons">
            <button>Lyrics</button>
            <button>Queue</button>
            <button>♥</button>
            </div>

            <!-- 6. Volume -->
            <div class="volume-container" style="width:100%;">
                <div class="audioVolumeBar" style="width:100%" onclick="na.musicPlayer.setVolume(event);">
                    <div class="audioVolumeBar_setting""></div>
                </div>
            </div>

            <div class="infoVolume-container">
                <div class="audioVolumeBarLabel" style="text-align:center">Volume : 100</div>
            </div>
            <div class="infoProgress-container">
                <div class="audioSeekBarLabel">
                    <div class="audioSeekBarLabel_currentTime">0:00</div>
                    <div class="audioSeekBarLabel_length">1:15:00</div>
                </div>
            </div>
        </div>
        </div>



        <div id="app__musicPlayer__playlist" class="vividDialog naNoComments" theme="dark" style="text-align:center;opacity:0.001;overflow:visible;position:absolute;">
            <h2 class="vt backdropped" style="padding:0px !important; margin:20px !important;display:flex;justify-content:center;align-items:center;width:auto;height:50px;font-size:10px;background:rgba(0,0,255,0.25);color:white;border-radius:10px;box-shadow:2px 2px 3px 2px rgba(0,0,0,0.7);">Playlist<br/>(drag and drop items onto this window)</h2>
            <ul id="playlist" class="vividScrollpane" style="width:100%;height:calc(100% - 50px);"></ul>
        </div>

        <div id="app__musicPlayer__description" class="vividDialog naNoComments" theme="dark" style="opacity:0.001;overflow:visible;position:absolute;">
            <div class="vividDialogContent" style="font-size:inherit">
                <div id="mp3descText" style="font-size:inherit"></div>
                <div id="siteIntroText"></div>
            </div>
        </div>
