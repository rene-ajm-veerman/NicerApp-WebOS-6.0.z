<?php
	require_once (dirname(__FILE__).'/../../../../boot.php');
?>
<html>
	<head>
		<title><?php readfile (dirname(__FILE__).'/index.php.title.txt');?></title>
		<?php require (dirname(__FILE__).'/index.php.meta.php');?>
		<link rel="StyleSheet" href="<?php echo SA_WEB?>/content.css"/>
		<link rel="StyleSheet" href="<?php echo SA_WEB?>/com/ui/vivid/themes/dialogs/dialog_001.css"/>
		<script type="text/javascript" src="<?php echo SA_WEB?>/get_javascript.php?want=all"></script>
		<script type="text/javascript">
			function startApp () {
				sa.vividControls.init(document.body, function() {
					jQuery('#dialog').css ({height:300});
					sa.sp.containerSizeChanged(jQuery('#dialog__scrollpane')[0]);
				});
				jQuery(window).resize(function() {
					jQuery('#dialog').css ({height:300});
					sa.sp.containerSizeChanged(jQuery('#dialog__scrollpane')[0]);
				});
			}
			function toggleState (dialogID) {
				var d = document.getElementById(dialogID);
				var item = document.getElementById(dialogID+'__item__0');
				var cb = document.getElementById('highlighted__'+dialogID);
				sa.vividControls.changeState (d,item,(cb.checked ? 'highlighted' : 'normal') );
			}
		</script>
	</head>
	<body onload="startApp();">
		<h1><?php readfile (dirname(__FILE__).'/index.php.title.txt');?></h1>
		
		
		<form id="control">
			<input type="checkbox" id="highlighted__dialog" onchange="toggleState('dialog');">highlighted</input>

			<div id="dialog" class="vividDialog vividTheme__dialog_skyblue_001_blackGreen_flowersSubtle_square vividScrollpane__scroll_black" style="position:absolute;width:600px;height:300px;overflow:hidden;">
				<p>
				Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent dignissim elit quis purus bibendum non pharetra erat suscipit. Donec convallis erat et nisl posuere ut ullamcorper mauris vestibulum. Etiam mattis, elit sit amet bibendum porttitor, lorem urna pulvinar nulla, sodales tristique augue orci porttitor nulla. Etiam fermentum, lorem et sollicitudin vestibulum, tellus justo consectetur quam, vel ultrices neque massa in ante. Mauris posuere magna ac justo elementum sit amet bibendum justo dignissim. Integer sed ante lorem, non varius risus. Donec vehicula metus sit amet tellus dictum auctor. Quisque ac vulputate sem. Maecenas auctor ante neque, quis tristique elit. Integer elit odio, scelerisque sed imperdiet eget, varius ut nisi. Cras bibendum condimentum viverra. Duis ut quam est, quis ultricies mi. Proin et tincidunt diam. Vivamus pulvinar neque a purus congue rhoncus. Vestibulum tempus metus nec lorem imperdiet vestibulum. Praesent porta volutpat urna sed viverra. Integer sed felis nulla.
				</p>
				
				<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Typi non habent claritatem insitam; est usus legentis in iis qui facit eorum claritatem. Investigationes demonstraverunt lectores legere me lius quod ii legunt saepius. Claritas est etiam processus dynamicus, qui sequitur mutationem consuetudium lectorum. Mirum est notare quam littera gothica, quam nunc putamus parum claram, anteposuerit litterarum formas humanitatis per seacula quarta decima et quinta decima. Eodem modo typi, qui nunc nobis videntur parum clari, fiant sollemnes in futurum.</p>
			</div>
		</form>
	</body>
</html>