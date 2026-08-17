var ajtt = {
	settings : {
			
	},
	init : function () {
		var html = '<h2>Color Theme</h2>';
		
		for (var themeName in sa.cg.themes) {
			if (!ajtt.settings.colorTheme) {
				ajtt.settings.colorTheme=sa.cg.themes[themeName];
				var c = 'href href_colorTheme selected';
			} else {
				var c = 'href href_colorTheme';
			}
			html += '<p><a id="ajtt_colorTheme__'+themeName+'" class="'+c+'" href="javascript:ajtt.selectTheme(\''+themeName+'\');">'+themeName+'</a></p>';
		}
		jQuery('#colorTheme').html (html);
		
		html = '<h2>Field Type</h2><p><a id="ajtt_fieldType__--plaintext--" class="href href_fieldType selected" href="javascript:ajtt.selectFieldType(\'--plaintext--\');">--plaintext--</a></p>';
		ajtt.settings.fieldType = '--plaintext--';
		for (var themeName in sa.vcc.themes) {
			var t = sa.vcc.themes[themeName];
			if (t.themeType=='vividButtonOrMenu') html += '<p><a id="ajtt_fieldType__'+themeName+'" class="href href_fieldType" href="javascript:ajtt.selectFieldType(\''+themeName+'\');">'+themeName+'</a></p>';
		}
		jQuery('#fieldType').html (html);
		
		html = '<h2>Animation Type</h2>';
		for (var i in sa.vt.globals.animationTypes) {
			var themeName = sa.vt.globals.animationTypes[i];
			if (!ajtt.settings.animationType) {
				ajtt.settings.animationType = themeName;
				var c = 'href href_animationType selected';
			} else {
				var c = 'href href_animationType';
			}
			html += '<p><a id="ajtt_animationType__'+themeName+'" class="'+c+'" href="javascript:ajtt.selectAnimationType(\''+themeName+'\');">'+themeName+'</a></p>';
		};
		jQuery('#animationType').html (html);
		ajtt.resize();
		
		
		sa.vcc.init(document.body, function () {
			jQuery(window).resize(ajtt.resize);
			ajtt.resize();
			ajtt.redrawOutput();
		});
	},
	
	selectTheme : function (themeName) {
		ajtt.settings.colorTheme = sa.cg.themes[themeName];
		jQuery('.href_colorTheme').removeClass('selected');
		jQuery('#ajtt_colorTheme__'+themeName).addClass('selected');
		ajtt.redrawOutput();
	},
	
	selectFieldType : function (fieldType) {
		ajtt.settings.fieldType = fieldType;
		jQuery('.href_fieldType').removeClass('selected');
		jQuery('#ajtt_fieldType__'+fieldType).addClass('selected');
		ajtt.redrawOutput();
	},
	
	selectAnimationType : function (animationType) {
		ajtt.settings.animationType = animationType;
		jQuery('.href_animationType').removeClass('selected');
		jQuery('#ajtt_animationType__'+animationType).addClass('selected');
		ajtt.redrawOutput();
	},
	
	redrawOutput : function () {
		if (ajtt.settings.fieldType=='--plaintext--') {
			var html = '<div id="outputText">Output : 1, 2, 3, 4, 5, 6, 7, 8, 9, 10...</div>';
			jQuery('#output').html (html);
			jQuery('#outputText').css ({fontFamily:jQuery('#font').val()});
			ajtt.initElement();
		} else {
			var html = 
			'<div id="outputButton" class="vividButton vividTheme__'+ajtt.settings.fieldType+'"><a href="#" id="outputText">Output Test</a></div>'
			jQuery('#output').html (html);
			jQuery('#outputText').css ({fontFamily:jQuery('#font').val()});
			delete sa.vcc.settings['outputButton'];
			sa.vcc.init (jQuery('#outputButton')[0], ajtt.initElement);
		}
	},
	
	initElement : function () {
		var ajtCommand = {
			el : jQuery('#outputText')[0],
			theme : ajtt.settings.colorTheme,
			animationType : ajtt.settings.animationType,
			animationSpeed : parseInt(jQuery('#animationSpeed').val())
		}
		sa.vt.initElement (ajtCommand);
	},
	
	resize : function () {
		var
		jQueryct = jQuery('#colorTheme__dialog'),
		jQueryft = jQuery('#fieldType__dialog'),
		jQueryat = jQuery('#animationType__dialog'),
		jQueryms = jQuery('#miscSettings__dialog'),
		jQueryo = jQuery('#output__dialog'),
		jQueryc = jQuery('#code__dialog'),
		ww = Math.floor ( (jQuery(window).width() - 40) / 3),
		wh = Math.floor ( (jQuery(window).height() - 125) / 2);
		
		jQueryct.css ({ top : 110, left : 10, width : ww, height : wh });
		jQueryft.css ({ top : 110, left : 15 + ww, width : ww, height : wh });
		jQueryat.css ({ top : 115+wh, left : 10, width : ww, height : wh });
		jQueryms.css ({ top : 115+wh, left : 15 + ww, width : ww, height : wh });
		jQueryo.css ({ top : 110, left : 20 + (ww*2), width : ww, height : wh });
		jQueryc.css ({ top : 115 + wh, left : 20+(ww*2), width : ww, height : wh });
		//debugger;
		jQuery('#header__dialog').css ({ width : jQuery(window).width()-20, left : 10, height:100 });
		jQuery('#colorTheme, #fieldType, #animationType, #miscSettings, #output, #code').css ({width:'auto',height:'auto'});
		sa.sp.containerSizeChanged (jQuery('#header__scrollpane')[0], true);
		sa.sp.containerSizeChanged (jQuery('#colorTheme__scrollpane')[0], true);
		sa.sp.containerSizeChanged (jQuery('#fieldType__scrollpane')[0], true);
		sa.sp.containerSizeChanged (jQuery('#animationType__scrollpane')[0], true);
		sa.sp.containerSizeChanged (jQuery('#output__scrollpane')[0], true);
		sa.sp.containerSizeChanged (jQuery('#code__scrollpane')[0], true);
	}
};