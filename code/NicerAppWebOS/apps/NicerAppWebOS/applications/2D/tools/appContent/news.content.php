<div id="yum" style="position:absolute;top:2%;left:2%;width:96%;height:96%;">&nbsp;</div>
<script type="text/javascript">
	sa.m.waitForCondition ('window.parent.window.sa.apps.recipes.settings.db loaded', function () {
		return (
			typeof window.parent.window.sa.site.code.settings.db !== 'undefined'
			&& !sa.desktop.settings.animating
		);
			
	}, function () {
		var db = window.parent.window.sa.site.code.settings.db;
		var opt = { 
			htmlID : 'yum', 
			opacity : sa.m.globals.jsonViewer.defaultOpacity
		}
		hm (db, 'var sa.site.code.settings.db =', opt );
	}, 400);
</script>
