seductiveapps.agenda = sa.a = {
	globals : {
		datepicker : {
			changeMonth: true,
			changeYear: true,
			showOtherMonths: true,
			selectOtherMonths: true,
			onSelect : function (date) {
				sa.a.siteCode.selectView();
			}
			
		}
	},
	settings : {},
	
	html : {
		viewAgenda : function (paneHTMLid, forWhat, beginDate, categoryTreenode) {
			var
			html = 
				'<table style="width:100%;height:100%;" cellspacing="3" cellpadding="0">'
				+ '<tr><th id="saAgenda_now" colspan="999" class="saAgenda_now" style="height:1.2em"></th></tr>'
				+ '<tr><td id="saAgenda_items_td" class="vividScrollpane__offsetLeftZero" colspan="999">',
			whichAgendas = jQuery(categoryTreenode).parents('.saAgenda_namedAgendas_treenode').attr('datapath');
			
			switch (forWhat) {
				case 'btnSwitchView_viewAgenda':
				
					var 
					dateLoopBegin = new Date (jQuery('#saAgenda_dateBegin').datepicker('getDate')),
					dateLoopEnd = new Date (jQuery('#saAgenda_dateEnd').datepicker('getDate')),
					agendaItems = sa.a.tools.getAgendaItems(whichAgendas, dateLoopBegin, dateLoopEnd);
					
					html += sa.a.tools.formatAgendaItems(agendaItems);
					
					
				
					break;
			
			
			/*
				case 'btnSwitchView_day':
					var
					dateLoopBegin = new Date(beginDate.getTime()),
					dateLoopEnd = new Date(beginDate.getTime());

					dateLoopBegin.setHours(0);
					dateLoopBegin.setMinutes(0);
					dateLoopBegin.setSeconds(0);
					dateLoopEnd.setHours(1);
					dateLoopEnd.setMinutes(0);
					dateLoopEnd.setSeconds(0);

					for (var i=0; i<=23; i++) {
						var taim = (
							i < 10
							? '0' + i
							: i
						) + ':00-' + (
							i < 10
							? '0' + i
							: i
						) + ':59',
						timeContent = sa.a.tools.getAgendaItems(whichAgendas, dateLoopBegin, dateLoopEnd);
						
						html += '<tr><td id="saAgenda_day___'+sa.a.tools.formatDateHTMLid(dateLoopBegin)+'" class="saAgenda_timeslot" style="width:100px;">'+taim+'</td><td class="saAgenda_timeDescription">'+timeContent+'</td></tr>';
						dateLoopBegin.setHours(dateLoopBegin.getHours()+1);
						dateLoopEnd.setHours(dateLoopEnd.getHours()+1);
					}
					break;


				case 'btnSwitchView_week':
					var dateToList = new Date(beginDate.getTime());
					html += '<tr><td>&nbsp;</td>';
					for (var i=0; i<7; i++) {
						html += '<th class="saAgenda_date">'+sa.a.tools.formatDateShort(dateToList)+'</th>';
						dateToList.setDate (dateToList.getDate()+1);
					}
					html += '</tr>';
					
					var 
					dateLoopBegin = new Date(beginDate.getTime()),
					dateLoopEnd = new Date (beginDate.getTime());
					
					dateLoopBegin.setHours(0);
					dateLoopBegin.setMinutes(0);
					dateLoopBegin.setSeconds(0);
					dateLoopEnd.setHours(1);
					dateLoopEnd.setMinutes(0);
					dateLoopEnd.setSeconds(0);
					
					for (var i=0; i<=23; i++) {
						var taim = (
							i < 10
							? '0' + i
							: i
						) + ':00-' + (
							i < 10
							? '0' + i
							: i
						) + ':59',
						timeContent = sa.a.tools.getAgendaItems(whichAgendas, dateLoopBegin, dateLoopEnd);			

						html += '<tr>';
						html += '<td id="saAgenda_week___'+sa.a.tools.formatDateHTMLid(dateLoopBegin)+'" class="saAgenda_timeslot" style="width:100px;">'+taim+'</td>';
						for (var j=0; j<7; j++) {
							html += '<td class="saAgenda_timeDescription">'+timeContent+'</td>';
							
							var
							dateInnerLoopBegin = new Date (dateLoopBegin.getTime()),
							dateInnerLoopEnd = new Date (dateLoopEnd.getTime());
							
							dateInnerLoopBegin.setDate (dateLoopBegin.getDate()+j+1);
							dateInnerLoopEnd.setDate (dateLoopEnd.getDate()+j+1);
							timeContent = sa.a.tools.getAgendaItems(whichAgendas, dateInnerLoopBegin, dateInnerLoopEnd);
						}
						html += '</tr>';

						dateLoopBegin.setHours(dateLoopBegin.getHours()+1);
						dateLoopEnd.setHours(dateLoopEnd.getHours()+1);
					}
					
					break;
				*/
			}
			
			html += '</td></tr></table>';

			// get rid of the old vividDialog internal settings for the possibly previously displayed agenda items
			// (allows the now rendered agenda items to display as a vividDialog)
			for (var divID in sa.vcc.settings) {
				if (divID.indexOf('saAgenda_item')!==false && divID.indexOf('saAgenda_item')!==-1) {
					delete sa.vcc.settings[divID];
				}
			};
			
			jQuery('#'+paneHTMLid).html(html);
			sa.vcc.init(jQuery('#'+paneHTMLid)[0]);
			setTimeout(function () {
				sa.a.siteCode.onresize();
				//debugger;
				jQuery('#'+paneHTMLid).find('.vdialog_relative').css({
					opacity : 1,
					zIndex : 1101
				}).fadeIn('slow');
//				sa.sp.containerSizeChanged (jQuery('#saAgenda_items')[0]);
			},3000);
		}
	},
	
	tools : {
		formatAgendaItems : function (agendaItems) {
			var 
			dialogTheme = 'dialog_black_stoneWall_square',
			dialogClass = 'vdialog_relative vividUninitialized saDontDoCSSinnervividDialog vividTheme__'+dialogTheme+' ',
			html = '<div id="saAgenda_items" class="vividScrollpane vividTheme__scroll_black" style="width:100%;height:100%;">';
			
			
			for (var agendaItemDate in agendaItems) {
				var agendaItem = agendaItems[agendaItemDate];
				
				html += 
					'<div id="saAgenda_item__'+agendaItemDate.replace(' ','__').replace(':','_')+'" class="'+dialogClass+'" style="z-index:1000;opacity:0.01">'
					+'<div class="saAgenda_item__dbPath">'+agendaItem.dbPath+'</div>'
					+'<div class="saAgenda_item__date">'+agendaItemDate+'</div>'
					+'<div class="saAgenda_item__title">'+agendaItem.title+'</div>'
					+'</div>';
			};
			
			html += '</div>';
			
			return html;
		},
	
		formatDate : function (date) {
			return date.toString();//date.getFullYear()+'-'+date.getMonth()+'-'+date.getDay();
		},
		formatDateShort : function (date) {
			return date.toDateString();//date.getFullYear()+'-'+date.getMonth()+'-'+date.getDay();
		},
		formatDateHTMLid : function (date) {
			return date.getFullYear()+'_'+date.getMonth()+'_'+date.getDay()+'__'+date.getHours()+'_'+date.getMinutes();
		},
		getAgendaItems : function (whichAgendas, datetimeBegin, datetimeEnd) {
			var 
			db = sa.a.siteCode.settings.db,
			dbRelevant = sa.a.tools.getRelevantDB(whichAgendas, db);
			
			if (dbRelevant===false) dbRelevant = {
				db : db,
				k : null
			};
			
			
			if (typeof dbRelevant==='object') {
				var r = sa.a.tools.getAgendaItems_getContent(dbRelevant, datetimeBegin, datetimeEnd);
			} else {
				var r = {};
			}			
			
			var 
			keys = Object.keys(r),
			rSorted = {};
			
			for (var i = 0; i < keys.length; i++) {
				var k = keys[i];
				rSorted[k] = r[k];
			};
			
			return rSorted;
		},
		
		getAgendaItems_getContent : function (dbRelevant, datetimeBegin, datetimeEnd) {
			return sa.a.tools.getAgendaItems_getContent_recurse (dbRelevant.db, dbRelevant.k, datetimeBegin, datetimeEnd);
		},
		
		getAgendaItems_getContent_recurse : function (db, key, datetimeBegin, datetimeEnd, dbPath) {
			var 
			html = '',
			agendaItems = {},
			db = (
				key!==null
				? db[key]
				: db
			);
			
			if (dbPath===undefined) dbPath = '';

			//html += '<div class="saAgenda_debug">-1-</div>';

			for (var k in db) {
				var 
				v = db[k],
				dait = Date.parse(k);
				
				if (typeof dait!=='number') continue;
				//html += '<div class="saAgenda_debug">'+k+'</div>';
				if (
					typeof v==='object' 
					&& v.saObjectType==='saAgenda_item'
					&& dait > datetimeBegin.getTime()
					&& dait < datetimeEnd.getTime()
				
				) {
					v.dbPath = dbPath;
					agendaItems[k] = v;
					/*
					html += 
						'<div class="saAgenda_item"><span class="saAgenda_item_datetime">'+k+'</span><br/>'
						+'<span class="saAgenda_item_title">'+v.title+'</span>'
						+'</div>';				
					*/
				}
				
				if (typeof db[k]==='object') {
					//html += sa.a.tools.getAgendaItems_getContent_recurse(db, k, datetimeBegin,datetimeEnd);
					agendaItems = sa.m.negotiateOptions(
						agendaItems,
						sa.a.tools.getAgendaItems_getContent_recurse(db, k, datetimeBegin,datetimeEnd, dbPath+'/'+k)
					);
				}
			}
			
			return agendaItems;
		},
		
		getRelevantDB : function (whichAgendas, db) {
			var
			stringSplit = whichAgendas.split('__'),
			path = stringSplit[1],
			pathSplit = path.split('.'),
			label = stringSplit[0],
			r = false;

			for (var k in db) {
				
				if (k===label) {
					return {
						db : db,
						k : k
					}
				} else {				
					var v = db[k];
					if (typeof v==='object') {
						r = sa.a.tools.getRelevantDB_recurse (whichAgendas, db, pathSplit, k, db[k]); 
						if (typeof r==='object') return r;
					} 
				}
			};
			return r;
		},
		
		getRelevantDB_recurse : function (whichAgendas, db, pathSplit, key, value) {
			var
			stringSplit = whichAgendas.split('__'),
			path = stringSplit[1],
			pathSplit = path.split('.'),
			label = stringSplit[0],
			r = false;

			for (var k in value) {
				if (k===label) return {
					db : value,
					k : k
				};
				
				var v = value[k];
				if (typeof v==='object') {
					r = sa.a.tools.getRelevantDB_recurse (whichAgendas, db, pathSplit, k, db[k]); 
					if (typeof r==='object') return r;
				}
			};
			return r;
		}
	},
	
	siteCode : {
		settings : {
			firstUpdateTimes : true
		},
		
		loadDB : function (callback) { 
			var 
			userViewed = app.settings.userViewed,
			ajaxCommand = {
				type : 'GET',
				url : sa.m.globals.urls.app + '/seductiveapps/com/ui/agenda/1.0.0/ajax_initialize.php',
				data : {
					userViewed : userViewed
				},
				success : sa.m.traceFunction(function (retData, ts2) {
					var ajaxCommand = {
						type : 'GET',
						url : sa.m.globals.urls.app + '/apps/user/appData/users/'+userViewed+'/agenda.json',
						success : sa.m.traceFunction(function (retData, ts2) {
							sa.a.siteCode.settings.db = retData;
							if (typeof callback=='function') callback();
							//app.settings.db = eval('('+retData+')');
							/*
							sa.json.decode.small (retData, null, 'gpthe.start().success()', null, function (data) {
								app.settings.db = data;
							});
							*/
						})
					};
					jQuery.ajax (ajaxCommand);
				})
			};
			jQuery.ajax (ajaxCommand);
				
		},
		
		startApp : function (/*loginname*/) {
			//sa.a.siteCode.settings.loginname = loginname;

			$(window).resize (sa.a.siteCode.onresize);
			sa.a.siteCode.onresize();
			
			sa.a.siteCode.loadDB(sa.a.siteCode.startApp_loadedDB);
			
			setInterval (sa.a.siteCode.updateNow, 1000);				
			jQuery(window).resize(sa.a.siteCode.onresize);
		},
		
		onresize : function () { 
			jQuery('#siteContent').css({
				width : '100%',
				height : '100%'
			});
		
			jQuery('#saAgenda_view').css({
				height : jQuery('body').height() - 50 - jQuery('#pageHeader').height() - jQuery('#saAgenda_switchView').height()
			});
			//debugger;
			var h = jQuery('#saAgenda_view').height() - jQuery('#saAgenda_now').height();
			jQuery('#saAgenda_items_td').css ({
				height : h
			});
			jQuery('#saAgenda_items, #saAgenda_items__container').css({
				height : h
			});
			sa.sp.containerSizeChanged(jQuery('#saAgenda_items')[0]);
			jQuery('#saAgenda_treeview__container, #saAgenda_treeview').css({
				height : jQuery('#saAgenda_view').height() - jQuery('#saAgenda_dateBegin').height() - jQuery('#saAgenda_dateEnd').height() - jQuery('#saAgenda_dateSpacer').height() - jQuery('#saAgenda_dateBegin_title').height() - jQuery('#saAgenda_dateEnd_title').height() - 10
			});
			sa.sp.containerSizeChanged(jQuery('#saAgenda_treeview')[0]);
		},

		updateNow : function () {
			jQuery('.saAgenda_now').html('<span style="color:lime">Now :</span> ' + sa.a.tools.formatDate(new Date));
		},
		

		startApp_loadedDB : function () {
			var 
			pane = jQuery('#saAgenda_view')[0],
			urlTop = window.top.document.location.href,
			urlThisIframe = document.location.href,
			html = 
				'<table style="width:100%;height:100%;">'
				+'<tr>'
				+'<td id="saAgenda_leftPane" style="width:200px;">'
				+'<div id="saAgenda_treeview" style="width:100%;height:100%;"  class="vividScrollpane vividTheme__scroll_black"></div>'
				+'<div id="saAgenda_dateBegin_title">Date Begin</div>'
				+'<div id="saAgenda_dateBegin"></div>'
				+'<div id="saAgenda_dateSpacer">&nbsp;</div>'
				+'<div id="saAgenda_dateEnd_title">Date End</div>'
				+'<div id="saAgenda_dateEnd"></div>'
				+'</td>'
				+'<td><div id="saAgenda_detail" class="" style="width:100%;height:100%;"></div></td>'
				+'</tr>'
				+'</table>',
			userViewed = window.top.app.settings.userViewed,
			treeviewDivID = 'saAgenda_treeview',
			agendaDivID = 'saAgenda_detail',
			treeviewVarID = 'sa.a.siteCode.settings.db';
			
			jQuery(pane).html(html);
			sa.treeview.create (treeviewDivID, urlTop, urlThisIframe, treeviewVarID);
			jQuery('#saAgenda_dateBegin').datepicker(sa.m.negotiateOptions(
				sa.a.globals.datepicker,
				{ defaultDate : '+0d' }
			)).css({
				opacity : 0.75
			});
			jQuery('#saAgenda_dateEnd').datepicker(sa.m.negotiateOptions(
				sa.a.globals.datepicker,
				{ defaultDate : '+1d' }
			)).css({
				opacity : 0.75
			});
			
			sa.vcc.init (jQuery('#saAgenda_treeview')[0]);
			sa.vcc.init (jQuery('#saAgenda_view')[0]);
			
			sa.a.siteCode.selectTreeviewNode(jQuery('.saAgenda_namedAgendas_treenode_text:first')[0]);

			sa.a.siteCode.onresize();
		},
		
		selectTreeviewNode : function (element) {
			sa.agenda.settings.selectedTreenode = element;
			jQuery(element).addClass('saAgenda_namedAgendas_treenode_text_selected');
			jQuery('.saAgenda_namedAgendas_treenode_text_selected').not(element).removeClass('saAgenda_namedAgendas_treenode_text_selected');
			sa.a.siteCode.selectView();
		},
		
		selectView : function (btnIDview, date) {
			if (typeof btnIDview==='undefined') btnIDview = 'btnSwitchView_viewAgenda';
			if (typeof date==='undefined') date = new Date;
			
			jQuery('.saAgenda_viewBtn').each(function(el,idx){
				sa.vcc.changeState (this, jQuery('#'+this.id+'__item__0')[0], 
					(this.id===btnIDview 
					? 'selected'
					: 'normal'
					)
				);
				
				if (this.id===btnIDview) sa.agenda.html.viewAgenda ('saAgenda_detail', this.id, date, sa.agenda.settings.selectedTreenode);
			});
			
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
					
					//init_instance_callback : sa.m.traceFunction(function(){sa.comments.siteCode.editorInitialized()}), 

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
						
						init_instance_callback : sa.m.traceFunction(function(){sa.comments.siteCode.editorInitialized()}), 

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
					sa.s.c.settings.firstUpdateTimes
					|| jQuery(el).parents('.vividUninitialized').length>0
					? 'none'
					: jQuery('.saComments_when_details',this).css('display'),
				html = 
					'<div class="saComments_when_timeLapsed_div" onclick="sa.s.c.toggleDisplayWhenDetails(\''+jQuery(this).parents('.saComment')[0].id+'\');" style="text-decoration:underline"><span class="saComments_when_timeLapsed_title">Posted : </span> <span class="saComments_when_timeLapsed">' + sa.m.dateForComments_difference(whenUTC) + '</span> <span class="saComments_when_timeLapsed_title2">ago</span></div>'
					+'<div class="saComments_when_details" style="display:'+displayDetails+';"><span class="saComments_when_yourTimezone">In your timezone : ' + sa.m.dateForComments_viewerPerspective(whenFromViewerPerspective) + '</span> <br/>'
					+ '<span class="saComments_when_postedFrom">In poster\'s timezone : ' + jQueryWhen.html() + '</span></div>';
				$('.saComment_when_displayToViewer',this).html (html);
					
			});
			
			setTimeout (function () {
				sa.s.c.settings.firstUpdateTimes = false;
				sa.s.c.updateTimes();
			}, 5000);
		},
		
		enterNewPost : function () {
			var 
			ed = tinyMCE.get('newPost'),
			now = new Date(),
			now_utc = new Date(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate(),  now.getUTCHours(), now.getUTCMinutes(), now.getUTCSeconds());
			var entry = {
				subscription : 'saUserContent_wall_'+$('#wallComments_forUser').html(),
				from : jQuery('#newPostFrom')[0].value,
				when : sa.m.dateForComments(),
				whenGetTime : now.getTime(),
				whenTimezoneOffset : now.getTimezoneOffset(),
				whenUTC : now_utc.getTime(),
				comment : ed.getContent()
			};
			jQuery.cookie ('postFrom', jQuery('#newPostFrom')[0].value, sa.m.cookieOptions());
			sa.comments.newComment (entry, function (result, statusAsText) {
				sa.tabs.gotoTab ('vividTabs_agenda', 0);
				jQuery(jQuery('#vividTabs_agenda_wall')[0].childNodes[0]).prepend (result);
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
										sa.comments.siteCode.updateTimes();
									}, 350);
									$(el).removeClass('vividUninitialized');
									
									sa.s.c.editorInitialized(); // shows the new entry
								},250);
							}
						});
					//}, 1000);
					window.parent.window.sa.s.c.transformLinks (jQuery('#vividTabs_agenda_wall')[0]);
					//sa.comments.siteCode.hideCommentsEditor();
					sa.comments.siteCode.onresize();
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
			sa.comments.siteCode.settings.showCommentsEditor = false;
			jQuery('.saComment_div').each(function(idx,el) {
				$('#'+el.id+'__dialog').fadeIn('normal');
			});
			jQuery('#comment_editor').slideUp ('slow');
			jQuery('#comments_table').css ({height : jQuery('body').height()-150});
			jQuery('iframeContent').css ({height:jQuery('body').height()-20, width:jQuery('body').width()-20});
			sa.sp.containerSizeChanged(jQuery('#iframeContent')[0]);
			jQuery('#newPostShowEditor_td').animate ({height:'35px'},'slow',function() {
				jQuery('#newPostShowEditor').fadeIn ('slow');
				jQuery('#comments__container').stop().animate ({height:(jQuery('body').height()-100) + 'px'},{
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
			sa.comments.siteCode.settings.showCommentsEditor = true;
			jQuery('.saComment_div').each(function(idx,el) {
				$('#'+el.id+'__dialog').fadeOut('normal');
			});
			jQuery('#comments_table').css ({height : jQuery('body').height()-150});
			tinyMCE.DOM.setStyle(tinyMCE.DOM.get("newPost" + '_ifr'), 'height', jQuery('body').height()-270 + 'px');

			jQuery('#comments__container').animate ({height:0/*(jQuery('body').height()-140/*520)*/ + 'px'}, {
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
