<!--<div id="canvas" style="position:absolute;top:2%;left:2%;width:96%;height:47%;">&nbsp;</div>-->
<div id="html" style="position:absolute;top:2%;left:2%;width:96%;height:96%%;">&nbsp;</div>
<script type="text/javascript">
	/*
	sa.m.waitForCondition ('!window.parent.window.sa.desktop.settings.animating', function () {
		return (
			!window.parent.window.sa.desktop.settings.animating
			//&& window.parent.window.sa.desktop.settings.initialized.site
		);
			
	}, function () {
		sa.serviceLog.visualize.mainPage('#canvas', '#html');
		//jQuery(window).resize(sa.serviceLog.visualize.onResize);
	}, 400);
	*/
	var appCode = {
		onload : function () {
			sa.serviceLog.visualize.mainPage('#canvas', '#html');
		},
		onresize : function (isManualResize) {
			
		}
	}
	
</script>