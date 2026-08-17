<?php
//define ('SA_SHOW_CONSTANTS', true); //un-comment this to only show the define()s that my nicerapp framework exposes
//require_once ('nicerapp-2012/boot.php');
//require_once ('nicerapp-2012/com/userInterface/comments/saComments-1.0.0.php');
require_once (realpath(dirname(__FILE__).'/../../../../../../').'/NicerAppWebOS/boot_stage_001.php');
global $naWebOS;

global $naWebOS;
var_dump ($naWebOS->view); exit();
$setPath = dirname(__FILE__).'/music/'.$_SESSION['locationBarInfo']['apps']['musicPlayer']['set'];
$files = getFilePathList ($setPath.'/', true, FILE_FORMATS_mp3s, null, array('file'));
/*
reportVariable ('$saHTDOCShd', $saHTDOCShd);
reportVariable ('$saSiteURL', $saSiteURL);
exit();
*/

foreach ($files as $idx => $filepath) {
	$files[$idx] = str_replace($saHTDOCShd, $saSiteURL, $files[$idx]);
	$files[$idx] = str_replace('\\\\', '/', $files[$idx]);
	$files[$idx] = str_replace('\\', '/', $files[$idx]);
}
//reportVariable ('f', $files); exit();

$authorEmail = 'rene.veerman.netherlands@gmail.com';
$spacer = "\n\t\t\t\t";
$htmlIntro = file_get_contents ($setPath.'/index.html');
$htmlTitleMeta = file_get_contents ($setPath.'/index.title_meta.html');

	global $saFrameworkFolder;
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <?php echo $htmlTitleMeta ?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="en">
	<meta http-equiv="content-language" content="english">
	<link type="text/css" rel="StyleSheet" media="screen" href="index.css"/>
  
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>    
	<?php echo $saCMS->resolveJavascriptHead(); ?>
    <!--<script type="text/javascript" src="<?php echo $saFrameworkFolder?>/lib/jQuery.jPlayer-2.9.1/dist/jplayer/jquery.jplayer.min.js"></script> <! -- Music-file playback code; originally from http://jplayer.org/ -->
	<script type="text/javascript" src="<?php echo $saFrameworkFolder?>/apps/NicerAppWebOS/musicPlayer/appContent/musicPlayer/mp3site.source.js?changed=<?php echo date('Ymd-His', filectime(dirname(__FILE__).'/mp3site.source.js'));?>"></script> <!-- The opensourced application code for this website that may be used commercially without cost-->
	<link type="text/css" rel="StyleSheet" media="screen" href="<?php echo $saFrameworkFolder;?>/lib/jQuery.jPlayer-2.9.1/jplayer.vivid.css"/>
</head>


<body style="overflow:hidden;width:100%;height:100%;" >

<script style="text/javascript">

    
    var naLocationBarInfo = <?php echo json_encode($_SESSION['locationBarInfo']); ?>;
    jQuery(document).ready(function() {
        na.m.waitForCondition ('main site bootup for mp3site', function () {
            //debugger;
            return (
                window.top.na.m.settings.initialized.site === true
                && window.top.na.desktop.settings.animating === false
               // && jQuery('#siteContent__iframe', jQuery('#siteContent',window.top)[0])[0]
               // && jQuery('#siteContent__iframe', jQuery('#siteContent',window.top)[0])[0].contentWindow.document.getElementsByTagName('body')[0]
            );
        }, function () {
            //siteCode_nicerapp.onLoad();
            window.top.na.vcc.settings['siteContent'].containsIframe = true;
            window.top.na.sp.containerSizeChanged (jQuery('#siteContent__scrollpane')[0], true);
            //debugger;
            window.top.na.apps.loaded.mp3site.settings.loadedIn['#siteContent'].onload();
        }, 50);
    });
</script>

	<div id="horizontalMover__containmentBox2" style="display:none;position:absolute;height:20px;border-radius:8px;background:black;opacity:0.2"></div>
	<div id="horizontalMover__containmentBox1" style="display:none;position:absolute;height:16px;top:2px;border-radius:4px;background:black;opacity:0.0"></div>
	<div id="horizontalMover" class="draggable ui-widget-content" style="display:none;position:absolute;top:4px;height:10px;width:730px;border-radius:4px;background:navy;border : 1px solid white;opacity:0.7"></div>
	<script type="text/javascript">
		jQuery('#horizontalMover').draggable ({
			containment : '#horizontalMover__containmentBox1',
			axis : 'x',
			drag : function () {
				mp3site.settings.masterLeftOffset = jQuery('#horizontalMover')[0].offsetLeft;
				mp3site.onWindowResize();
			}
		});
	</script>

	<div id="mp3s" class="vividDialog vividTheme__dialog_transparent vividScrollpane__scroll_black_left animatedOptions__noXbar vividTheme__scroll_black_left" style="visibility:hidden;position:absolute;text-align:center;width:230px; color:yellow;font-weight:bold">
<?php
			$filez = array();
			foreach ($files as $idx=>$file) {
				$fn = basename($file);
				$filez[$idx] = str_replace (' - DJ FireSnake', '', $fn);
				$filez[$idx] = str_replace ('.mp3', '', $filez[$idx]);
			}
			asort ($filez);
			foreach ($filez as $idx=>$fn) {
				$id = 'mp3_'.$idx;
				echo "\t\t".'<div id="'.$id.'" file="'.basename($files[$idx]).'" class="mp3 vividButton vividTheme__lava_002" style="padding:0px;"><a href="javascript:mp3site.selectMP3(\''.$id.'\', \''.basename($files[$idx]).'\');">'.$fn.'</a></div>'."\n";
			}
?> 
	</div>
		
	<div id="player" class="vividDialog vividTheme__dialog_black_simple_square vividScrollpane__hidden" style="overflow:visible; visibility:hidden;position:absolute; width:320px; height:120px; padding-left:35px;">
        <audio id="audioTag">
            <?php 
			foreach ($filez as $idx=>$fn) {
                $id = 'mp3Source_'.$idx;
                echo '<source id="'.$id.'" src="music/'.$_SESSION['locationBarInfo']['apps']['musicPlayer']['set'].'/'.basename($files[$idx]).'" type="audio/mpeg">';
            }
            ?>
        </audio>
		<table id="player_table" style="width:100%; visibility:hidden;">
			<tr>
				<td>
					<div id="jplayer" class="jp-jplayer"></div>
					<div id="jp_container_1" class="jp-audio">
						<div class="jp-type-single">
							<div id="jp_interface_1" class="jp-gui jp-interface">
								<table border="0" class="jp-controls" cellspacing="5" style="width:100%">
									<tr>
										<td class="jp-button"><div id="btn_playpause" title="Toggle play / pause" class="vividButton vividTheme__playpause_002"><a href="javascript:mp3site.playpause();" class="jp-play" tabindex="1"></a></div></td>
										<!--<td class="jp-button"><div id="btn_stop" title="Stop" class="vividButton vividTheme__stop_001"><a href="javascript:mp3site.stop();" class="jp-pause" tabindex="2"></a></div></td>-->
										<td class="jp-button"><div id="btn_mute" title="Mute / un-mute"class="vividButton vividTheme__shuffle_001"><a href="javascript:mp3site.shuffle();" class="jp-pause" tabindex="3"></a></div></td>
										<td class="jp-button"><div id="btn_repeat" title="Toggle repeating of playlist" class="vividButton vividTheme__repeat_002"><a href="javascript:mp3site.toggleRepeat();" tabindex="4"></a></div></td>
									</tr>
									<tr>
										<td style="vertical-align:top;">
											<div class="jp-volume-bar" title="Volume" onclick="mp3site.setVolume(event);">
												<div class="jp-volume-bar-value"></div>
											</div>
										</td>
										<td colspan="3" style="vertical-align:top;">
											<div class="jp-progress" title="Position in track">
												<div class="jp-seek-bar" onclick="mp3site.seek(event);">
													<div class="jp-play-bar"></div>
												</div>
											</div>
											<div class="jp-time-holder">
												<div class="jp-current-time"></div>
												<div class="jp-duration"></div>
											</div>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>

	<div id="app__musicPlayer__playlist" class="vividDialog vividTheme__dialog_black_simple_square vividScrollpane__hidden" style="overflow:visible; visibility:hidden; position:absolute; width:300px;height:300px;">
		<ul id="playlist" style="padding:10px;padding-left:50px;width:100%;"></ul>
	</div>
	
	<div id="app__musicPlayer__desc" class="vividDialog vividScrollpane__scroll_black vividTheme__dialog_black_simple_square" style="overflow:visible;visibility:hidden; position:absolute;width:320px;height:300px;">
		<div id="mp3descText"></div>
		<div id="siteIntroText" style="visibility:hidden;">
			<?php echo $htmlIntro?>
		</div>
	</div>
	
	<!--
	<div id="infoWindow_comments" class="vividDialog vividTheme__dialog_black_simple_square vividScrollpane__hidden" style="overflow:visible; visibility:hidden;position:absolute;width:400px;height:300px;">
		<table id="comments_table" style="width:100%;height:100%;">
			<tr>
				<td style="vertical-align:top">	
					<div id="comments" class="vividScrollpane vividTheme__scroll_black" style="margin:10px;visibility:hidden; width:100%;height:100%;">
					<?php
						//saComments_echoSubscription ('DJ_FireSnake');
					?>
					</div>
				</td>
			</tr><tr>
				<td id="newCommentShowEditor_td" colspan="2" style="height:	40px;padding-left:80px;">
					<div id="newCommentShowEditor" class="vividButton vividTheme__menu_001"><a href="javascript:mp3site.showCommentsEditor();">Enter New Comment</a></div>
				</td>
			</tr><tr>
				<td style="height:1px;">
					<div id="comment_editor" style="display:none">
						<form>
							<table style="padding-left:10px;width:350px;padding-left:10px;">
								<tr>
									<td colspan="2">
										<span style="font-size:9px; color:red;background:white;">
										Comments can only be removed by the IP address they were posted from..
										</span>
									</td>
								</tr>
								<tr>
									<td>From : </td>
									<td><input id="newCommentFrom" name="newCommentFrom" style="width:100%;"/></td>
								</tr>
								<tr><td colspan="2">
									<textarea id="newComment" name="newComment" style="width:350px; height:300px;"> </textarea>
								</td></tr>
							</table>
							<table>
								<tr><td style="width:20px">&nbsp;</td><td>
									<div id="newCommentSubmit" class="vividButton vividTheme__menu_002"><a href="javascript:mp3site.enterNewComment();">Make Comment</a></div>
								</td><td>
									<div id="cancelCommentSubmit" class="vividButton vividTheme__menu_002"><a href="javascript:mp3site.hideCommentsEditor();">Cancel</a></div>
								</td></tr>
							</table>
						</form>
					</div>
				</td>
			</tr>
		</table>
	</div>-->
</body>
</html>
