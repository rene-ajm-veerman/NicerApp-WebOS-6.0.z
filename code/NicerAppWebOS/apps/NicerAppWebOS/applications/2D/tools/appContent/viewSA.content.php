<div id="yum" style="position:absolute;top:2%;left:2%;width:96%;height:96%;">&nbsp;</div>
<script type="text/javascript">
	sa.m.waitForCondition ('window.parent.window.sa.apps.recipes.settings.db loaded', function () {
		return (
			typeof window.parent.window.sa !== 'undefined'
			&& sa.m.settings.initialized.site===true
			&& !sa.desktop.settings.animating
		);
			
	}, function () {
		var db = window.parent.window.sa;
		var opt = { 
			htmlID : 'yum', 
			opacity : sa.m.globals.jsonViewer.defaultOpacity
		}
		jQuery('#siteContent').css({width:'100%',height:'100%'});
		hm (db, 'var sa =', opt );
	}, 400);
</script>
