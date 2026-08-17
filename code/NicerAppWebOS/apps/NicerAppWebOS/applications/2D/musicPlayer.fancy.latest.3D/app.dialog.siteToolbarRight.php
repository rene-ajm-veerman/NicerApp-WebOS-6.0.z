<?php
require_once (realpath(dirname(__FILE__).'/../../../../../..').'/NicerAppWebOS/boot.php');
global $naWebOS; global $naLAN;
$view = $naWebOS->view;

$setPath = $view['folder']['path'];
$authorEmail = 'rene.veerman.netherlands@gmail.com';
$spacer = "\n\t\t\t\t";

	global $saFrameworkFolder;
?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="en">
	<meta http-equiv="content-language" content="english">
	<link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/3rd-party/jQuery/jPlayer-2.9.1/jplayer.vivid.css"/>

	<div id="app__musicPlayer__player" class="vividDialog naNoComments" style="overflow:visible;position:absolute;width:320px;height:120px;">
        <audio id="audioTag">
            <?php 
            $vs = '/'.str_replace(' ','%20',$view['folder']['webPath']);
            foreach ($filez2 as $idx=>$fn) {
                $id = 'mp3Source_'.$idx;
                echo PHP_EOL;
                echo "\t\t\t".'<source id="'.$id.'" src="'.$vs.$filez2[$idx].'" type="audio/mpeg">'.PHP_EOL;
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
