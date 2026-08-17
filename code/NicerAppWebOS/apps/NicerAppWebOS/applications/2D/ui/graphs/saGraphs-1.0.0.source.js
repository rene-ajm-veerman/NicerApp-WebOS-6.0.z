seductiveapps.graphs = {
	about : {
		whatsThis : 'sa.graphs = all javascript related to graphs that I use throughout my framework.'
	},
	settings : {
		data : {}
		//graphs : [ 'graphName1', 'graphName2' ],
		//errors : // object indexed as [jQueryhost][jQuerydate][jQueryvisitor_ip]['c'] to reach the error contexts
	},
	globals : {},
	init : function () {
	},
	math : {
		callbackCircle : function (cx, cy, offsetDegrees, radius, callbackDegrees, callback) {
			var ns = callbackDegrees;
			for (var i = 0; i < ns; i++) {
				var angle = offsetDegrees + (((Math.PI * (360/callbackDegrees)) / ns ) * i);
				sa.m.log (undefined, 'angle='+angle);

				var cos = Math.cos(angle);
				var sin = Math.sin(angle);

				var px = cos * radius;
				var py = sin * radius;
				callback (cx+px, cy+py, i);
			}
		}
	}
		
}