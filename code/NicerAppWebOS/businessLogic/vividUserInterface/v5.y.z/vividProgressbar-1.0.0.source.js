na.pbar = nicerapp.vividProgressbar = {
	about : {
		whatsThis : 'seductiveapps.pbar = seductiveapps.vividProgressbar = a fancy progressbar component',
		copyright : '(c) (r) 2011-2013 by [the owner of seductiveapps.com] <seductiveapps@gmail.com>',
		license : 'http://seductiveapps.com/LICENSE.txt',
		noWarranty : 'NO WARRANTY EXPRESSED OR IMPLIED. USE ONLY AT YOUR OWN RISK.',
		version : '1.0.0',
		firstReleased : '2011',
		lastUpdated : '2013 January 04, 14:06 CEST'
	},
	settings : {
		animatedSliderMove : [],
		animatedScrollTo : []
	},
	globals : {
		
	},
	
	init : function (el) {
		var html = 
		'<div class="vividProgressbar" style="height:10px;position:relative;">'
		+'<div class="vividProgressbar_bg" style="position:absolute;background:red;height:100%;width:100%;z-index:10000000">&nbsp;</div>'
		+'<div class="vividProgressbar_outer" style="position:absolute;background:green;height:100%;width:50%;z-index:10000001">&nbsp;</div>'
		+'</div>';
		el.innerHTML = html;
	},
	
	setPercentage : function (el, count, total) {
		el.children[0].children[1].style.width = (count*100/total) + '%';
		if ((count*100)/total>100) debugger;
	}
};
