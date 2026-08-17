<?php
require_once (realpath(dirname(__FILE__).'/../../../../../..').'/NicerAppWebOS/boot.php');
global $naWebOS; global $naLAN;
$view = $naWebOS->view;

$authorEmail = 'rene.veerman.netherlands@gmail.com';
$spacer = "\n\t\t\t\t";

	global $saFrameworkFolder;
?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="en">
	<meta http-equiv="content-language" content="english">
	<link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/3rd-party/jQuery/jPlayer-2.9.1/jplayer.vivid.css"/>

	<div id="app__musicPlayer__player" class="vividDialog naNoComments" style="margin:20px;overflow:visible;position:relative;width:calc(100% - 40px);height:120px;">
        <audio id="audioTag"></audio>
        
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

	<div id="app__musicPlayer__playlist" class="vividDialog naNoComments backdropped" theme="dark" style="margin:10px;margin-left:20px;text-align:center;overflow:visible;position:relative; width:calc(100% - 60px);height:calc(50% - 130px);">
        <h2 class="vt backdropped" style="padding:0px !important; margin:10px !important;display:flex;justify-content:center;align-items:center;width:auto;height:50px;font-size:10px;background:rgba(0,0,255,0.25);color:white;border-radius:10px;box-shadow:2px 2px 3px 2px rgba(0,0,0,0.7);">Playlist<br/>(drag and drop files into this window)</h2>
		<ul id="playlist" class="vividScrollpane" style="width:100%;height:calc(100% - 80px);"></ul>
	</div>
	
	<div id="app__musicPlayer__fileListing" class="vividDialog naNoComments backdropped" theme="dark" style="margin:10px;margin-top:0;margin-left:20px;overflow:visible;position:relative;width:calc(100% - 60px);height:calc(50% - 120px);">
        <h2 class="vt backdropped" style="padding:0px !important; margin:10px !important;display:flex;justify-content:center;align-items:center;width:auto;height:50px;font-size:10px;background:rgba(0,0,255,0.25);color:white;border-radius:10px;box-shadow:2px 2px 3px 2px rgba(0,0,0,0.7);">Files List<p class="backdropped"><a href="javascript:$('#fileListing').html('')">Clear</a></p></h2>

		<div id="fileListing" class="vividScrollpane" style="width:auto;height:calc(100% - 94px);"></div>
	</div>

	<style>
        #playlist, #fileListing { list-style-type: none; margin: 0; padding: 0; margin-bottom: 10px; }
        #playlist li, #fileListing li { border : none; margin : 0; padding : 0; background : none; box-shadow : none; }
	</style>

	<script type="text/javascript">
        $(document).ready(function() {
            $('#siteToolbarRight').css({width:360});
            $('#siteToolbarRight .vividScrollpane').css({overflow:'hidden'});

            $( "#playlist" ).sortable({
                revert: true
            });

            $('#siteContent .vividScrollpane').css({overflow:'hidden'});
            na.m.waitForCondition('na.d?', function() { return na.d; }, function() {
                na.d.s.visibleDivs.push ('#siteToolbarRight');
                na.d.s.visibleDivs.push ('#siteContent');
                na.d.resize();
            }, 100);
        });
	</script>
