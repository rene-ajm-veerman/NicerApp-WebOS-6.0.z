seductiveapps.dialog2 = {
	about : {
		whatsThis : 'seductiveapps.dialog = seductiveapps.vividDialog = A PNG-animated-sprite dialog component in javascript without dependencies',
		copyright : '(c) (r) 2014 by [the owner of seductiveapps.com] <seductiveapps@gmail.com>',
		license : 'http://seductiveapps.com/seductiveapps/license.txt',
		noWarranty : 'NO WARRANTY EXPRESSED OR IMPLIED. USE ONLY AT YOUR OWN RISK.',
		version : '2.0.0',
		lastUpdated : '2014 February 5, 01:38am'
	},

	globals : {
		scrollpaneTheme : 'scroll_black'
	},

	init : function (componentInstanceHTMLelement, initID) {
		//sa.m.log (1, 't123', componentInstanceHTMLelement.id, initID, componentInstanceHTMLelement);
		if (componentInstanceHTMLelement.style.display=='none') {
			sa.m.log (2, { msg : 'sa.vividDialog.init(): for initID='+initID+'; NOT initializing element with id="'+componentInstanceHTMLelement.id+'" and class="'+componentInstanceHTMLelement.className+'", because .style.display=="none".' } );
			return false;
		} else {
			sa.m.log (2, { msg : 'sa.vividDialog.init(): for initID='+initID+'; Initializing element with id="'+componentInstanceHTMLelement.id+'" and class="'+componentInstanceHTMLelement.className+'".' } );
		}
		sa.vcc.options[componentInstanceHTMLelement.id] = {
			items : {}
		};

		//sa.dialog.loadDialogGraphics (componentInstanceHTMLelement); // CAUSES <A HREF> and <A ONCLICK> IN DIALOG CONTENT TO BECOME BUGGY!
		if (!sa.vcc.settings[componentInstanceHTMLelement.id]) sa.vcc.newInstance (componentInstanceHTMLelement, initID);
		//setTimeout (sa.m.traceFunction(function () {
			sa.dialog.initDialog(componentInstanceHTMLelement.id);
		//}), 1100); // give artwork time to load --- POINTLESS - artwork aint loading yet
	}
	
	
};