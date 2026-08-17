<?php
	require_once (dirname(__FILE__).'/../../../../boot.php');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" style="width:100%;height:100%;">
<head>
		<title><?php readfile (dirname(__FILE__).'/index.php.title.txt');?></title>
		<?php require (dirname(__FILE__).'/index.php.meta.php');?>
		<link rel="StyleSheet" href="<?php echo SA_WEB?>/content.css"/>
		<script type="text/javascript" src="<?php echo SA_WEB?>/get_javascript.php?want=all"></script>
		<script type="text/javascript">
			function startApp() {
				sa.vividControls.init(document.body);
				jQuery(window).resize(function() {
					sa.sp.containerSizeChanged(jQuery('#iframeContent')[0]);
				});
				var html = '';
				for (var t in sa.vcc.themes) {
					if (sa.vcc.themes[t].themeType=='vividButtonOrMenu') {
						if (t=='menu_001') {
							html += '<div class="theme selected" onclick="selectTheme(this);">' + t + '</div>';
						} else {
							html += '<div class="theme" onclick="selectTheme(this);">' + t + '</div>';
						};
					}
				};
				document.getElementById('theme').innerHTML=html
			}
			
			function selectTheme (theme) {
				sa.menu.destroyMenu ('divMenu');
				jQuery('.theme').removeClass('selected');
				jQuery(theme).addClass('selected');
				document.getElementById('divMenu').className = 'vividMenu vividTheme__'+theme.innerHTML;
				sa.vcc.init(document.getElementById('divMenu'));
			}
			
			function setOrientation (orient) {
				sa.menu.destroyMenu ('divMenu');
				jQuery('.orientation').removeClass('selected');
				jQuery(orient).addClass('selected');
				if (orient.innerHTML == 'vertical') {
					sa.vcc.setOptions('divMenu',{
					});
				} else {
					sa.vcc.setOptions('divMenu',{
						level_0_global : {
							orientation : 'horizontal',
							animspeed : 400
						}
					});
				};
				sa.vcc.init(document.getElementById('divMenu'));
			}
		</script>
		<style>
			.theme, .orientation {
				text-align : center;
			}
			.selected {
				background : blue;
				color : white;
			}
		</style>
	</head>
	<body onload="startApp();" style="margin:0px;padding:0px;overflow:hidden;width:100%;height:100%">
		<div id="iframeContent" class="content vividScrollpane vividTheme__<?php echo SA_SITES_SEDUCTIVEAPPS_SCROLLPANE_THEME?>" style="width:100%;height:100%;">
		
			<h1><?php readfile (dirname(__FILE__).'/index.php.title.txt');?></h1>
	
			<div id="divMenu" class="vividMenu vividTheme__menu_001" style="position:relative;width:400px; height:100px;">
				<ul style="display:none">
					<li><a href="javascript:alert('Sub 1 clicked!');">Sub 1</a>
						<ul>
							<li><a href="javascript:alert('Sub 1a clicked!');">Sub 1a</a></li>
							<li><a href="javascript:alert('Sub 1b clicked!');">Sub 1b</a>
								<ul>
									<li><a href="javascript:alert('Sub 1b1 clicked!');">Sub 1b1</a></li>
									<li><a href="javascript:alert('Sub 1b2 clicked!');">Sub 1b2</a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li><a href="javascript:alert('Sub 2 clicked!');">Sub 2</a>
						<ul>
							<li><a href="javascript:alert('Sub 2a clicked!');">Sub 2a</a>
								<ul>
									<li><a href="javascript:alert('Sub 2a1 clicked!');">Sub 2a1</a></li>
									<li><a href="javascript:alert('Sub 2a2 clicked!');">Sub 2a2</a></li>
								</ul>
							</li>
							<li><a href="javascript:alert('Sub 2b clicked!');">Sub 2b</a></li>
						</ul>
					</li>
				</ul>
			</div>
			<div id="control">
					<table>
						<tr>
							<td>
								<p>
								Theme:
								</p>
								<div id="theme" style="float:left;width:300px;height:400px;overflow:auto;border:1px solid purple"></div>
							</td>
							<td>
								<p>
								Orientation:
								</p>
								<div id="orientation" style="float:left;width:150px;height:50px;overflow:auto;border:1px solid purple">
									<div class="orientation selected" onclick="setOrientation(this);">vertical</div>
									<div class="orientation" onclick="setOrientation(this);">horizontal</div>
								</div>
							</td>
						</tr>
					</table>
			</div>
			
			<div id="explanation">
			This menu is real simple to use, for an example please see the source of this page and look for "vividMenu".
			</div>
		</div>
	</body>
</html>