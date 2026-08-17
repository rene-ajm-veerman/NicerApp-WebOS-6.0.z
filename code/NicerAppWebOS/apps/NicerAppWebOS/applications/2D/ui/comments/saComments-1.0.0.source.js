seductiveapps.comments = {
	about : {
		whatsThis : 'The javascript end of a comments engine for web resources.',
		copyright : '(c) (r) 2012 by [the owner of seductiveapps.com] <seductiveapps@gmail.com>',
		license : 'http://seductiveapps.com/seductiveapps/license.txt',
		noWarranty : 'NO WARRANTY EXPRESSED OR IMPLIED. USE ONLY AT YOUR OWN RISK.',
		version : '1.0.0',
		firstReleased : '2012 April',
		lastUpdated : '2012 June 19, 12:30 CEST',
		knownBugs : {
			1 : "None so far. Please report/fix any bugs you find, and notify me.."
		},
		downloadURL : 'http://seductiveapps.com/'
	},
	globals : {
		//OPTIONAL;
		//removeCallback : function (subscriptionName, commentIdx, result, statusAsText) {}   // gets called after any comment is removed.
	},
	language : {
		removeWarning : 'Are you sure you want to remove this comment?'
	},
	settings : {
		confirmCount : 0
	},
	
	newComment : function (entry, successCallback) {
		var ajaxCommand = {
			type : 'POST',
			url : sa.m.globals.urls.framework + '/'+'ui/comments/ajax_newComment.php',
			data : entry,
			success : function (result, statusAsText) {
				if (typeof successCallback == 'function') successCallback (result, statusAsText);
			}
		};
		debugger;
		jQuery.ajax (ajaxCommand);
	},
	
	removeComment : function (subscriptionName, idx) {
		/* 2017-10-13 disabled for now
		sa.m.confirm(undefined, {
			id : 'saComment_remove_'+sa.comments.settings.confirmCount,
			msg : sa.comments.language.removeWarning, 
			dialogCSSoverrides : {
				internalCSS : {
					top : '25%',
					height : '50%'
				}
			},
			buttons : {
				btnYes : {
					label : 'Yes',
					vividTheme : 'polarbear_001',
					click : function () {
						var ajaxCommand = {
							type : 'GET',
							url : sa.m.globals.urls.os + '/'+'com/ui/comments/ajax_removeComment.php',
							data : {
								subscription : subscriptionName,
								idx : idx
							},
							success : function (result, statusAsText) {
								if (typeof sa.comments.globals.removeCallback == 'function') sa.comments.globals.removeCallback (subscriptionName, idx, result, statusAsText);
								sa.desktop.systemDialogs.hide('#siteComments', 'confirm');
							}
						};
						
						jQuery.ajax (ajaxCommand);
					}
				},
				btnNo : {
					label : 'No, KEEP IT!',
					vividTheme : 'divers_001',
					click : function () {
						sa.desktop.systemDialogs.hide('#siteComments', 'confirm');
					}
				}
			}
		});
				if (false && confirm(sa.comments.language.removeWarning)) {
		*/
					var ajaxCommand = {
						type : 'GET',
						url : sa.m.globals.urls.framework + '/ui/comments/ajax_removeComment.php',
						data : {
							subscription : subscriptionName,
							idx : idx
						},
						success : function (result, statusAsText) {
							if (typeof sa.comments.globals.removeCallback == 'function') sa.comments.globals.removeCallback (subscriptionName, idx, result, statusAsText);
						}
					};
					
					jQuery.ajax (ajaxCommand);
				//};
				
				
	},
	
	
	siteCode : {
		simpleCode : {
			settings : {
				firstUpdateTimes : true
			},
			
			startApp : function () {
				sa.comments.globals = {
					removeCallback : sa.site.code.removeComment
				};


				//app.hideCommentsEditor();
				window.parent.window.sa.userContent.wall.initialize(window, document.body); // {-TODO: consider moving this code into this content's iframe
				app.updateTimes();
				app.startTinyMCE();
			},

			startTinyMCE : function () {
				var css_e = sa.m.globals.urls.os + 'lib/tinymce-3.5.10/jscripts/tiny_mce/themes/advanced/skins/default/ui_seductiveappsTransparency.css';
				var path = sa.m.globals.urls.os + "lib/tinymce-3.5.10/seductiveappsTransparency/";
				var css_c = path + 'editor_comment.css,';
				if (navigator.userAgent.match(/MSIE/)) {
					css_c += path + 'editor_comment_ie5.css';
				} else {
					css_c += path + 'editor_comment_css3.css';
				};

				setTimeout (function () {
					/*
					tinyMCE.init({
						mode : "textareas",
						theme : "advanced",
						plugins : 'emotions',
						skin : 'default',
						
						//init_instance_callback : sa.m.traceFunction(function(){sa.site.code.editorInitialized()}), 

						theme_advanced_buttons1 : "bold,italic,underline,strikethrough,indent,outdent,bullist,numlist",
						theme_advanced_buttons2: "justifyleft,justifycenter,justifyright,justifyfull,link,unlink,emotions",
						theme_advanced_buttons3: "styleselect,formatselect,fontselect,fontsizeselect,forecolor,backcolor",
						theme_advanced_buttons4: "",
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "center",
						font_size_style_values : "8px,10px,12px,14px,18px,24px,36px",
						//keep_style : true, 
						content_css : css_c,
						editor_css : css_e,
						inline_styles : true,
						theme_advanced_resize_horizontal : false,
						theme_advanced_resizing : false,
						//apply_source_formatting : true,
						convert_fonts_to_spans : true
					});
					*/
					tinyMCE.init({
							// General options
							mode : "textareas",
							theme : "advanced",
							plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

							// Theme options
							theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
							theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
							theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
							theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
							theme_advanced_toolbar_location : "top",
							theme_advanced_toolbar_align : "left",
							theme_advanced_statusbar_location : "bottom",
							theme_advanced_resizing : false,

							// Skin options
							skin : "default",

							// Example content CSS (should be your site CSS)
							content_css : css_c,
							editor_css : css_e,
							
							init_instance_callback : sa.m.traceFunction(function(){sa.site.code.editorInitialized()}), 

							// Drop lists for link/image/media/template dialogs
							template_external_list_url : "js/template_list.js",
							external_link_list_url : "js/link_list.js",
							external_image_list_url : "js/image_list.js",
							media_external_list_url : "js/media_list.js"
					});	
				}, 1500);
			},
			
			editorInitialized : function () {
				sa.m.waitForCondition('.vividDialog_dialog', function () {
					return $('.vividDialog_dialog',$('#comments')[0]).length==$('.saComment_div').length;
				}, function () {
					sa.sp.containerSizeChanged(jQuery('#comments')[0]);

					$('.saComment_div').each (function (idx, el) {
						var css = {opacity:1,display:'none',visibility:'visible'};
						$('#'+el.id+'__dialog').css(css).fadeIn(2500);
						setTimeout (function () {
							$(el).css(css).fadeIn(1000);
							/*
							setTimeout (function () {
								$('.saComment_div').each (function (idx,el) {
									$('#'+el.id+'__dialog').animate({height:el.scrollHeight+30});
								});
							}, 1010);
							*/

						},2000);
					});
				});
			},

			toggleDisplayWhenDetails : function (commentID) {
				var details = $('.saComments_when_details', jQuery('#'+commentID)[0]);
				if (details.css('display')!=='none') details.slideUp(); else details.show();
			},
			
			updateTimes : function () {
				$('.saComment_when_display').each (function (el, id) {
				
					var
					now = new Date(),
					jQueryWhen = $('.saComment_when', this),
					jQueryWhenUTC = $('.saComment_whenUTC', this),
					whenUTC = new Date(parseInt(jQueryWhenUTC.html())),
					whenFromViewerPerspective = parseInt(jQueryWhenUTC.html()),
					displayDetails = 
						app.settings.firstUpdateTimes
						? 'none'
						: jQuery('.saComments_when_details',this).css('display'),
					html = 
						'<div class="saComments_when_timeLapsed_div" onclick="app.toggleDisplayWhenDetails(\''+jQuery(this).parents('.saComment')[0].id+'\');" style="text-decoration:underline"><span class="saComments_when_timeLapsed_title">Posted : </span> <span class="saComments_when_timeLapsed">' + sa.m.dateForComments_difference(whenUTC) + '</span> <span class="saComments_when_timeLapsed_title2">ago</span></div>'
						+'<div class="saComments_when_details" style="display:'+displayDetails+';"><span class="saComments_when_yourTimezone">In your timezone : ' + sa.m.dateForComments_viewerPerspective(whenFromViewerPerspective) + '</span> <br/>'
						+ '<span class="saComments_when_postedFrom">In poster\'s timezone : ' + jQueryWhen.html() + '</span></div>';
					$('.saComment_when_displayToViewer',this).html (html);
						
				});
				
				setTimeout (function () {
					mp3site.settings.firstUpdateTimes = false;
					mp3site.updateTimes();
				}, 1000);
			},
			
			enterNewComment : function () {
				var 
				ed = tinyMCE.get('newComment'),
				now = new Date(),
				now_utc = new Date(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate(),  now.getUTCHours(), now.getUTCMinutes(), now.getUTCSeconds());
				var entry = {
					subscription : 'saUserContent_wall_'+$('#wallComments_forUser').html(),
					from : jQuery('#newCommentFrom')[0].value,
					when : sa.m.dateForComments(),
					whenGetTime : now.getTime(),
					whenTimezoneOffset : now.getTimezoneOffset(),
					whenUTC : now_utc.getTime(),
					comment : ed.getContent()
				};
				jQuery.cookie ('commentFrom', jQuery('#newCommentFrom')[0].value, sa.m.cookieOptions());
				sa.comments.newComment (entry, function (result, statusAsText) {
					jQuery('#comments').prepend (result);
					sa.vcc.init (jQuery('.vividUnitialized')[0]);
					setTimeout (function() {
						jQuery('.vividUnitialized').each(function(idx,el){
							$(el).removeClass('vividUninitialized');
							app.editorInitialized(); // shows the new entry
						});
					}, 1000);
					window.parent.window.sa.site.code.transformLinks (jQuery('#comments')[0]);
					sa.tabs.gotoTab (ciid, 0);
				});
			},
			
			removeComment : function (subscriptionName, commentIdx, result, statusAsText) {
				var jQueryc = jQuery('#saComment_subscription_' + subscriptionName + '_item_' + commentIdx);
				jQueryc.slideUp('slow', function () {
					jQueryc.remove();
					sa.sp.containerSizeChanged(jQuery('#comments')[0]);
				});
			},
			
			hideCommentsEditor : function () {
				sa.site.code.settings.showCommentsEditor = false;
				jQuery('.saComment_div').each(function(idx,el) {
					$('#'+el.id+'__dialog').fadeIn('normal');
				});
				jQuery('#comment_editor').slideUp ('slow');
				jQuery('#comments_table').css ({height : jQuery('#siteContent').height()-150});
				jQuery('iframeContent').css ({height:jQuery('#siteContent').height()-20, width:jQuery('#siteContent').width()-20});
				sa.sp.containerSizeChanged(jQuery('#iframeContent')[0]);
				jQuery('#newCommentShowEditor_td').animate ({height:'35px'},'slow',function() {
					jQuery('#newCommentShowEditor').fadeIn ('slow');
					jQuery('#comments__scrollpane__container').stop().animate ({height:(jQuery('#siteContent').height()-100) + 'px'},{
						duration : window.parent.window.sa.desktop.settings.animating?1:'fast',
						progress : function() {
							sa.sp.containerSizeChanged(jQuery('#comments')[0]);
						},
						complete : function() {
							sa.sp.containerSizeChanged(jQuery('#comments')[0]);
						}
					});					
				});
			},
			
			showCommentsEditor : function () {
				sa.site.code.settings.showCommentsEditor = true;
				jQuery('.saComment_div').each(function(idx,el) {
					$('#'+el.id+'__dialog').fadeOut('normal');
				});
				jQuery('#comments_table').css ({height : jQuery('#siteContent').height()-150});
				tinyMCE.DOM.setStyle(tinyMCE.DOM.get("newComment" + '_ifr'), 'height', jQuery('#siteContent').height()-270 + 'px');

				jQuery('#comments__scrollpane__container').animate ({height:0/*(jQuery('#siteContent').height()-140/*520)*/ + 'px'}, {
					duration : 'slow', 
					progress : function () {
						
						sa.sp.containerSizeChanged(jQuery('#comments')[0]);
					},
					complete : function () {
						sa.sp.containerSizeChanged(jQuery('#comments')[0]);
					}
				});
				jQuery('#newCommentShowEditor').fadeOut ('slow', function () {
					jQuery('#newCommentShowEditor_td').animate ({height:'1px'},'slow');
					jQuery('#comment_editor').show ('slow');
				});
			}
		},
		
		vividTabs : {
			settings : {
				firstUpdateTimes : true
			},
			
			startApp : function () {
				sa.comments.globals = {
					removeCallback : sa.comments.siteCode.vividTabs.removeComment
				};
				sa.vividTabs.settings.onTabChange = function () {
					sa.comments.siteCode.vividTabs.onresize();
				};
				jQuery('#newPostFrom')[0].value = jQuery.cookie ('postFrom');
				
								
				$(window).resize(sa.comments.siteCode.vividTabs.onresize);
				sa.comments.siteCode.vividTabs.onresize();
				setTimeout(function(){
					//app.hideCommentsEditor();
					
					//debugger;
					
					window.parent.window.sa.userContent.wall.initialize(window, document.body); // {-TODO: consider moving this code into this content's iframe
					app.updateTimes();
					sa.comments.siteCode.vividTabs.onresize();

					sa.m.waitForCondition ('parent stopped animating', function () {
						return window.parent.window.sa.desktop.settings.animating===false;
					}, function () {
						var css = { display : 'none', visibility:'visible' };
						jQuery('.vividDialog_dialog, .vividDialog').not('#sitePopup_confirm__dialog, #sitePopup_error__dialog').css(css).fadeIn('normal');
						setTimeout(function() {
							sa.comments.siteCode.vividTabs.onresize();
							css = { width : '97%' };
							jQuery('.vividDialog_dialog', jQuery('.saWall')[0]).css(css);
							jQuery('.vividUninitialized').removeClass('vividUninitialized');
							
						}, 1000);
					}, 250);					
				}, 1000);


				app.startTinyMCE();
			},
			
			onresize : function () { 
				jQuery('#siteContent').css ({
					left : 0,
					top : 0,
					width : '100%',
					height : '100%'
				});
			
				jQuery('#vividTabs_wall').css({
					height : jQuery('#siteContent').height() - 50
				});
				//debugger;
				jQuery('#vividTabs_wall__scrollpane__container, #vividTabs_wall__scrollpane').css({
					width : '100%',
					height : jQuery('#siteContent').height()
				});
				
				sa.vividTabs.onresize('#vividTabs_wall');
				jQuery('#newPost_dialog, #newPost_dialog__dialog').css({
					height : '100%'
				});
				
				var newHeight = (jQuery('#vividTabs_wall_newPost').height()-295);
				//var newHeight = (jQuery('#newPost_dialog').height()-170);
				tinyMCE.DOM.setStyle(
					tinyMCE.DOM.get("newPost" + '_ifr'), 
					'height', 
					newHeight + 'px'
				);
				jQuery('#newPost_tbl').css({
					height : newHeight
				});
				
				jQuery('.vividDialog_dialog', jQuery('#vividTabs_wall')).css ({
					width : '100%'
				});
				
				jQuery('.saComment_div', jQuery('#vividTabs_wall')).each(function(idx,el){
					var css = { width : jQuery('#'+el.id+'__dialog').width() -110, height : el.children[0].scrollHeight+30 };
					jQuery('#'+el.id+'__scrollpane__container, #'+el.id+'__scrollpane, #'+el.id).css(css);
					jQuery('#'+el.id+'__dialog').css({height : el.children[0].scrollHeight+30});
					jQuery('#'+el.id).css({height : '100%'});
					//debugger;
					jQuery('#'+el.id+'__item__0').css({zIndex:-1,height:'100%'});
				});
				jQuery('.vividDialog', jQuery('#vividTabs_wall')).each(function(idx,el){
					sa.dialog.onresize(el);
				});
				
				/*
				var css = { width : '100%', height:jQuery(jQuery('#newPost_ifr').parents('.tabPage')[0]).height()-60 };
				
				jQuery('#newPost_dialog__dialog').css(css);
				jQuery('.vsp_container, #newPost_dialog__dialog__scrollpane', jQuery('#newPost_dialog__dialog')).css(css);
				sa.vividScrollpane.containerSizeChanged (jQuery('#vividTabs_wall__scrollpane')[0]);
				*/
				/*
				jQuery('#vividTabs_wall__scrollpane__container').css ({
					height : jQuery('#siteContent').height() - 50 
				});
				*/
			},

			startTinyMCE : function () {
				var css_e = sa.m.globals.urls.os + 'lib/tinymce-3.5.10/jscripts/tiny_mce/themes/advanced/skins/default/ui_seductiveappsTransparency.css';
				var css_c = '';
				
				var path = sa.m.globals.urls.os + "lib/tinymce-3.5.10/seductiveappsTransparency/";
				var css_c = path + 'editor_comment.css,';
				if (navigator.userAgent.match(/MSIE/)) {
					css_c += path + 'editor_comment_ie5_fullyTransparent.css';
				} else {
					css_c += path + 'editor_comment_css3_fullyTransparent.css';
				};
				
				setTimeout (function () {
					/*
					tinyMCE.init({
						mode : "textareas",
						theme : "advanced",
						plugins : 'emotions',
						skin : 'default',
						
						//init_instance_callback : sa.m.traceFunction(function(){sa.comments.siteCode.vividTabs.editorInitialized()}), 

						theme_advanced_buttons1 : "bold,italic,underline,strikethrough,indent,outdent,bullist,numlist",
						theme_advanced_buttons2: "justifyleft,justifycenter,justifyright,justifyfull,link,unlink,emotions",
						theme_advanced_buttons3: "styleselect,formatselect,fontselect,fontsizeselect,forecolor,backcolor",
						theme_advanced_buttons4: "",
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "center",
						font_size_style_values : "8px,10px,12px,14px,18px,24px,36px",
						//keep_style : true, 
						content_css : css_c,
						editor_css : css_e,
						inline_styles : true,
						theme_advanced_resize_horizontal : false,
						theme_advanced_resizing : false,
						//apply_source_formatting : true,
						convert_fonts_to_spans : true
					});
					*/
					tinyMCE.init({
							// General options
							mode : "textareas",
							theme : "advanced",
							plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

							// Theme options
							theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
							theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
							theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
							theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
							theme_advanced_toolbar_location : "top",
							theme_advanced_toolbar_align : "left",
							theme_advanced_statusbar_location : "bottom",
							theme_advanced_resizing : false,

							// Skin options
							skin : "default",

							// Example content CSS (should be your site CSS)
							content_css : css_c,
							editor_css : css_e,
							
							init_instance_callback : sa.m.traceFunction(function(){sa.comments.siteCode.vividTabs.editorInitialized()}), 

							// Drop lists for link/image/media/template dialogs
							template_external_list_url : "js/template_list.js",
							external_link_list_url : "js/link_list.js",
							external_image_list_url : "js/image_list.js",
							media_external_list_url : "js/media_list.js"
					});	
				}, 1500);
			},
			
			editorInitialized : function () {
				/*
				sa.m.waitForCondition('.vividDialog_dialog', function () {
					return $('.vividDialog_dialog',$('#comments')[0]).length==$('.saComment_div').length;
				}, function () {
					sa.sp.containerSizeChanged(jQuery('#comments')[0]);

					$('.saComment_div').each (function (idx, el) {
						var css = {opacity:1,display:'none',visibility:'visible'};
						$('#'+el.id+'__dialog').css(css).fadeIn(2500);
						setTimeout (function () {
							$(el).css(css).fadeIn(1000);
							/ *
							setTimeout (function () {
								$('.saComment_div').each (function (idx,el) {
									$('#'+el.id+'__dialog').animate({height:el.scrollHeight+30});
								});
							}, 1010);
							* /

						},2000);
					});
				});
				*/
			},

			toggleDisplayWhenDetails : function (commentID) {
				var details = $('.saComments_when_details', jQuery('#'+commentID)[0]);
				if (details.css('display')!=='none') details.slideUp(); else details.show();
			},
			
			updateTimes : function () {
				$('.saComment_when_display').each (function (idx,el) {
				
					var
					now = new Date(),
					jQueryWhen = $('.saComment_when', this),
					jQueryWhenUTC = $('.saComment_whenUTC', this),
					whenUTC = new Date(parseInt(jQueryWhenUTC.html())),
					whenFromViewerPerspective = parseInt(jQueryWhenUTC.html());
					
					//debugger;
					var
					displayDetails = 
						app.settings.firstUpdateTimes
						|| jQuery(el).parents('.vividUninitialized').length>0
						? 'none'
						: jQuery('.saComments_when_details',this).css('display'),
					html = 
						'<div class="saComments_when_timeLapsed_div" onclick="app.toggleDisplayWhenDetails(\''+jQuery(this).parents('.saComment')[0].id+'\');" style="text-decoration:underline"><span class="saComments_when_timeLapsed_title">Posted : </span> <span class="saComments_when_timeLapsed">' + sa.m.dateForComments_difference(whenUTC) + '</span> <span class="saComments_when_timeLapsed_title2">ago</span></div>'
						+'<div class="saComments_when_details" style="display:'+displayDetails+';"><span class="saComments_when_yourTimezone">In your timezone : ' + sa.m.dateForComments_viewerPerspective(whenFromViewerPerspective) + '</span> <br/>'
						+ '<span class="saComments_when_postedFrom">In poster\'s timezone : ' + jQueryWhen.html() + '</span></div>';
					$('.saComment_when_displayToViewer',this).html (html);
						
				});
				
				setTimeout (function () {
					mp3site.settings.firstUpdateTimes = false;
					mp3site.updateTimes();
				}, 1000);
			},
			
			enterNewPost : function () {
				jQuery('#newPost_parent').remove(); //BUGFIX _ TODO:FIX tinyMCE and get rid of this bugfix
				var 
				ed = tinyMCE.get('newPost'),
				edfix = jQuery('#newPost_ifr')[0],
				now = new Date(),
				now_utc = new Date(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate(),  now.getUTCHours(), now.getUTCMinutes(), now.getUTCSeconds());
				var entry = {
					subscription : 'saUserContent_wall_'+$('#wallComments_forUser').html(),
					from : jQuery('#newPostFrom')[0].value,
					when : sa.m.dateForComments(),
					whenGetTime : now.getTime(),
					whenTimezoneOffset : now.getTimezoneOffset(),
					whenUTC : now_utc.getTime(),
					//comment : ed.getContent()
					comment : edfix.contentDocument.children[0].children[1].innerHTML
				};
				jQuery.cookie ('postFrom', jQuery('#newPostFrom')[0].value, sa.m.cookieOptions());
				sa.comments.newComment (entry, function (result, statusAsText) {
					sa.tabs.gotoTab ('vividTabs_wall', 0);
					jQuery(jQuery('#vividTabs_wall_wall')[0].childNodes[0]).prepend (result);
					sa.vcc.init (jQuery('.vividUninitialized')[0], function () {
						//setTimeout (function() {
							jQuery('.vividUninitialized').each(function(idx,el){
								if (el.style.visibility='hidden') {
									
									$(el).css({
										display : 'block',
										visibility : 'visible',
										opacity : 0.01
									});
									setTimeout (function () {
										$('#'+el.id+'__dialog').css({
											width : '100%',
											height : el.scrollHeight + 40,
											display : 'none',
											visibility : 'visible',
										}).fadeIn(300);
										setTimeout (function () {
											$(el).css ({
												//width : '100%',
												height : el.scrollHeight,
												opacity : 1,
												display : 'none'
											}).fadeIn('normal');
											$('#'+el.id+'__scrollpane, #'+el.id+'__scrollpane__container').css ({
												width : '100%',
												height : el.scrollHeight
											});
											sa.comments.siteCode.vividTabs.updateTimes();
										}, 350);
										$(el).removeClass('vividUninitialized');
										
										app.editorInitialized(); // shows the new entry
									},250);
								}
							});
						//}, 1000);
						window.parent.window.app.transformLinks (jQuery('#vividTabs_wall_wall')[0]);
						//sa.comments.siteCode.vividTabs.hideCommentsEditor();
						sa.comments.siteCode.vividTabs.onresize();
					});
				});
			},
			
			removeComment : function (subscriptionName, commentIdx, result, statusAsText) {
				var jQueryc = jQuery('#saComment_subscription_' + subscriptionName + '_item_' + commentIdx);
				jQuery('#'+jQueryc[0].id+'__dialog').slideUp('slow');
				jQueryc.slideUp('slow', function () {
					jQuery('#'+jQueryc[0].id+'__dialog').remove();
					//debugger;
					//sa.dialog.destroy (jQuery('#'+jQueryc[0].id+'__dialog')[0]);
					//sa.sp.containerSizeChanged(jQuery('#comments')[0]);
				});
			},
			
			hideCommentsEditor : function () {
				sa.comments.siteCode.vividTabs.settings.showCommentsEditor = false;
				jQuery('.saComment_div').each(function(idx,el) {
					$('#'+el.id+'__dialog').fadeIn('normal');
				});
				jQuery('#comment_editor').slideUp ('slow');
				jQuery('#comments_table').css ({height : jQuery('#siteContent').height()-150});
				jQuery('iframeContent').css ({height:jQuery('#siteContent').height()-20, width:jQuery('#siteContent').width()-20});
				sa.sp.containerSizeChanged(jQuery('#iframeContent')[0]);
				jQuery('#newPostShowEditor_td').animate ({height:'35px'},'slow',function() {
					jQuery('#newPostShowEditor').fadeIn ('slow');
					jQuery('#comments__scrollpane__container').stop().animate ({height:(jQuery('#siteContent').height()-100) + 'px'},{
						duration : window.parent.window.sa.desktop.settings.animating?1:'fast',
						progress : function() {
							sa.sp.containerSizeChanged(jQuery('#comments')[0]);
						},
						complete : function() {
							sa.sp.containerSizeChanged(jQuery('#comments')[0]);
						}
					});					
				});
			},
			
			showCommentsEditor : function () {
				sa.comments.siteCode.vividTabs.settings.showCommentsEditor = true;
				jQuery('.saComment_div').each(function(idx,el) {
					$('#'+el.id+'__dialog').fadeOut('normal');
				});
				jQuery('#comments_table').css ({height : jQuery('#siteContent').height()-150});
				tinyMCE.DOM.setStyle(tinyMCE.DOM.get("newPost" + '_ifr'), 'height', jQuery('#siteContent').height()-270 + 'px');

				jQuery('#comments__scrollpane__container').animate ({height:0/*(jQuery('#siteContent').height()-140/*520)*/ + 'px'}, {
					duration : 'slow', 
					progress : function () {
						
						sa.sp.containerSizeChanged(jQuery('#comments')[0]);
					},
					complete : function () {
						sa.sp.containerSizeChanged(jQuery('#comments')[0]);
					}
				});
				jQuery('#newPostShowEditor').fadeOut ('slow', function () {
					jQuery('#newPostShowEditor_td').animate ({height:'1px'},'slow');
					jQuery('#comment_editor').show ('slow');
				});
			}
		}
	}
};
