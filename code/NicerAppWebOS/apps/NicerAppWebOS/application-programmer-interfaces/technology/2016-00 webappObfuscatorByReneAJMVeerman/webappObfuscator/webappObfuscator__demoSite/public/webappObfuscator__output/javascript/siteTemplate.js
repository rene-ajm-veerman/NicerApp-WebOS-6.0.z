var dlp = { poV  : { Xtr : {} }	,
	BIQ : {}
}; 

dlp.s = dlp.BIQ;

dlp.s.c = dlp.BIQ.code = {
	ANF : { 	sJf : {
				BIQ : ''
		}
	}, 
	settings : { },
	
	E_K : function () {
	  var History = window.History;   if ( !History.enabled ) {
	                  alert ('History not enabled!');
	  }	  
	  
	  $('li a').click(function(e) {
	      if (!!(window.History && History.pushState)) {
		  e.preventDefault();
		  History.pushState(null, null, this.href);
	      }
	  });	  

	  History.Adapter.bind(window,'statechange', dlp.s.c.statechange);   dlp.m.hUb ('siteCode started');
	},
	
	statechange : function () {
		var 
		state = History.getState(),
		url = state.url;
		
		
		url = url.replace (dlp.s.c.ANF.sJf.BIQ, '');
			dlp.s.c.eSv (url);
		
	},
	
	eSv : function (url) {
	  dlp.s.c._Kv (url);
	},
	
	_Kv : function (url) {
		var LPG = {
			type : 'GET',
			url : dlp.s.c.ANF.sJf.BIQ + '/public/siteLogic/get_content.php?url='+url, 
									success : dlp.s.c.DlO
		};
		
		jQuery.ajax(LPG);
	},
	
	DlO : function (data, QKA, xhr) {
		jQuery('#FPF').fadeOut ('normal', function() {
			jQuery('#FPF').html(data).fadeIn('normal');
		});
	},
	
	QVM : function (KvB, HZX) {
		var html = 
			'<div id="thi_0">'
			
					+'<p id="thi_1" class="thi_a">'+KvB+'</p>' 
			
					+"<p id='thi_2' class='thi_b thi_c'>"+HZX+"</p>" 
			+'</div>';
			
		jQuery('#FPF').append (html);
	},
	
	goQ : function (hJp, dsD, fol) {
		if (typeof hJp==='undefined') {
			hJp = new RegExp ('/H3ll0/');
		};
		if (typeof dsD==="undefined") {
			dsD = /Hello/;
		}
		
		
		if (typeof fol==='string') {
			jQuery ("#FPF").append (fol.replace (hJp, dsD));
		}
	},
	
	QHk : function (nNp) {
		switch (nNp) {
			case 'bla' : return 'test'; 
			case "blie" : var r = "test2"; break;
			default : 
				var r = "test3"; 
				break;
		};
		return r;
	}
};



dlp.m = dlp.RoP = { ANF : {
		vTg : 1000 },
	settings : {
	},
	
	hUb : function (LlE) {
		dlp.m.log (1, LlE);
		jQuery('#gac').fadeOut ('normal', function () {
				jQuery('#gac').append('<p class="dfo__msgToEndUser">'+LlE+'</p>').fadeIn('normal');
		});
	},
	
	log : function (vTg, LlE) {
		if (
			vTg < dlp.m.ANF.vTg
			&& typeof console=='object'
			&& typeof console.log=='function'
		) console.log (LlE);
	}
};


