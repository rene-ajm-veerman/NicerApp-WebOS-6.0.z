seductiveapps.lb = seductiveapps.linkbase = {
	createHTML : function (sites, jQueryID) {
		var
		html = '<table cellpadding="5" cellmargin="0" style="width:100%;"><tr>';
		
		
		html += '</tr><tr>';
		for (var line in sites) {
			var l = sites[line];
			
			html += '</tr><tr>';

			for (var header in l) {
				html += '<th><span class="saFrontpage_newsCategory">'+header+'</span></th>';
			};
			
			html += '</tr><tr>';
		
			for (var header in l) {
				html += '<td>';
				
				var s = l[header];
				
				for (var siteTitle in s) {
					html += 
						'<p><a href="javascript:window.parent.window.sa.site.code.pushState (null, \'' + siteTitle 
						+ ' {via SeductiveApps.com}\', sa.m.globals.urls.app+\'url/'
						+ s[siteTitle].replace('http://','')
						+ '\');">'
						+ siteTitle
						+ '</a></p>';
				}
				
				html += '</td>';
			};
		};
		html += '</tr></table>';
		
		jQuery(jQueryID).html(html);
	
		jQuery('.saFrontpage_newsCategory').each ( function (idx) {
			var vividTextCmd = {
				el : this,
				theme : sa.cg.themes.saColorgradientSchemeFullRangeWhiteBackground, 
				animationType : sa.vividText.globals.animationTypes[0],
				animationSpeed : 2500
			};
			sa.vividText.initElement (vividTextCmd);	
		});
	}
}