<?php
	require_once (dirname(__FILE__).'/../../../../boot.php');
?>
<html>
	<head>
		<title><?php readfile (dirname(__FILE__).'/index.php.title.txt');?></title>
		<?php require (dirname(__FILE__).'/index.php.meta.php');?>
		<link type="text/css" rel="StyleSheet" media="screen" href="<?php echo SA_WEB?>/com/ui/vivid/text/index.css"/>
		<script type="text/javascript" src="<?php echo SA_WEB?>/get_javascript.php?want=all"></script>
		<script type="text/javascript" src="<?php echo SA_WEB?>/com/ui/vivid/text/index.siteCode.source.js"></script>
		<script type="text/javascript">
			function startApp () {
				ajtt.init();
			}
		</script>
	</head>
	<body onload="startApp();">
		<div id="header" class="vividDialog vividTheme__dialog_009" style="width:96%;left:2%;height:70px;">
			<h1 style="font-size:95%;"><?php readfile (dirname(__FILE__).'/index.php.title.txt');?></h1>
		</div>
		
		<div id="colorTheme" class="vividDialog vividTheme__dialog_008" style="position:absolute;"></div>
		<div id="fieldType" class="vividDialog vividTheme__dialog_008" style="position:absolute;"></div>
		<div id="animationType" class="vividDialog vividTheme__dialog_008" style="position:absolute;"></div>
		<div id="miscSettings" class="vividDialog vividTheme__dialog_008" style="position:absolute;"><form id="miscForm">
			<h2>Misc Settings</h2>
			<table style="width:100%;">
				<tr>
					<td>animation speed</td>
					<td><input type="text" id="animationSpeed" style="width:100px;" value="2000" onchange="ajtt.redrawOutput();"></input></td>
				</tr>
				<tr>
					<td>font</td>
					<td>
						<select id="font" onchange="ajtt.redrawOutput();">
							<option value="Arial, Helvetica, sans-serif">Arial</option>
							<option value="Arial Black, Gadget, sans-serif">Arial Black</option>
							<option value="Comic Sans MS, cursive">Comic Sans MS</option>
							<option value="Courier New, monospace">Courier New</option>
							<option value="Georgia, serif">Georgia</option>
							<option value="Impact, Charcoal, sans-serif">Impact</option>
							<option value="Lucida Console, Monaco, monospace">Lucida Console</option>
							<option value="Lucida Sans Unicode, Lucida Grande, sans-serif">Lucida Sans Unicode</option>
							<option value="Palatino Linotype, Book Antiqua, Palatino, serif">Palatino Linotype</option>
							<option value="Tahoma, Geneva, sans-serif">Tahoma</option>
							<option value="Times New Roman, Times, serif">Times New Roman</option>
							<option value="Trebuchet MS, sans-serif">Trebuchet MS</option>
							<option value="Verdana, Geneva, sans-serif">Verdana</option>
							<option value="MS Sans Serif, Geneva, sans-serif">MS Sans Serif</option>
							<option value="MS Serif, New York, serif">MS Serif</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" onclick="ajtt.redrawOutput(); return false;"></input>
					</td>
				</tr>
			</table>
		</form></div>
		<div id="output" class="vividDialog vividTheme__dialog_008" style="position:absolute;"></div>
		<div id="code" class="vividDialog vividTheme__dialog_008" style="position:absolute;"></div>					
	</body>
</html>