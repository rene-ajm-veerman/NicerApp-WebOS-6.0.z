var dfo = { // demoForObfuscation (root javascript namespace variable name)
	apps  : { loaded : {} }	,
	site : {}
}; 

dfo.s = dfo.site;

dfo.s.c = dfo.site.code = {
	globals : { // to be treated as constants/PHP define()s
		urls : {
		// gets filled in by /public/siteLogic/get_javascripts_settings.php
			site : ''
		}
	}, 
	settings : { // allowed to change during runtime of code
	},
	
	startSiteCode : function () {
	  var History = window.History; // Note: We are using a capital H instead of a lower h
	  if ( !History.enabled ) {
	      // History.js is disabled for this browser.
	      // This is because we can optionally choose to support HTML4 browsers or not.
	      alert ('History not enabled!');
	  }	  
	  
	  $('li a').click(function(e) {
	      if (!!(window.History && History.pushState)) {
		  e.preventDefault();
		  History.pushState(null, null, this.href);
	      }
	  });	  

	  History.Adapter.bind(window,'statechange', dfo.s.c.statechange); // use HTML5 History API if available:
	  dfo.m.msgToEndUser ('siteCode started');
	},
	
	statechange : function () {
		var 
		state = History.getState(),
		url = state.url;
		
		
		url = url.replace (dfo.s.c.globals.urls.site, '');
		//alert (url);
		dfo.s.c.urlSpecificSettings (url);
		
	},
	
	urlSpecificSettings : function (url) {
	  dfo.s.c.loadContent (url);
	},
	
	loadContent : function (url) {
		var xhrCommand = {
			type : 'GET',
			url : dfo.s.c.globals.urls.site + '/public/siteLogic/get_content.php?url='+url, 
				// any *relative* URLs inside strings, you HAVE to start with /
				// or webappObfuscator can't whitelist===ignorelist it..
			success : dfo.s.c.loadContent__loaded
		};
		
		jQuery.ajax(xhrCommand);
	},
	
	loadContent__loaded : function (data, ts, xhr) {
		jQuery('#dfo__content').fadeOut ('normal', function() {
			jQuery('#dfo__content').html(data).fadeIn('normal');
		});
	},
	
	testHTMLinsertion : function (str1, str2) {
		var html = 
			'<div id="thi_0">'
			
			// SINGLE QUOTES AT JS LEVEL, DOUBLE QUOTES FOR THE HTML = the best way to do things. You'll see the truth of that when your code's complexity increases.
			+'<p id="thi_1" class="thi_a">'+str1+'</p>' 
			
			// DOUBLE QUOTES = you still got a lot to learn, but that code-form is included here so you don't potentially have to change an entire stack of existing code to get obfuscated.
			+"<p id='thi_2' class='thi_b thi_c'>"+str2+"</p>" 
			+'</div>';
			
		jQuery('#dfo__content').append (html);
	},
	
	testRegexps : function (searchRegx, replaceRegx, haystack) {
		if (typeof searchRegx==='undefined') {
			searchRegx = new RegExp ('/H3ll0/');
		};
		if (typeof replaceRegx==="undefined") {
			replaceRegx = /Hello/;
		}
		
		
		if (typeof haystack==='string') {
			jQuery ("#dfo__content").append (haystack.replace (searchRegx, replaceRegx));
		}
	},
	
	getString : function (what) {
		switch (what) {
			case 'bla' : return 'test'; 
			case "blie" : var r = "test2"; break;
			default : 
				var r = "test3"; 
				break;
		};
		return r;
	}
};



dfo.m = dfo.misc = { // equivalent to a 'functions.php' - miscelleanous functions
	globals : {
		logLevel : 1000 // show all dfo.m.log() calls with a logLevel < 1000.
	},
	settings : {
	},
	
	msgToEndUser : function (msg) {
		dfo.m.log (1, msg);
		jQuery('#dfo__leftSidebar').fadeOut ('normal', function () {
				jQuery('#dfo__leftSidebar').append('<p class="dfo__msgToEndUser">'+msg+'</p>').fadeIn('normal');
		});
	},
	
	log : function (logLevel, msg) {
		if (
			logLevel < dfo.m.globals.logLevel
			&& typeof console=='object'
			&& typeof console.log=='function'
		) console.log (msg);
	}
};

