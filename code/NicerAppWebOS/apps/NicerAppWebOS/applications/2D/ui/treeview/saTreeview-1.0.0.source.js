seductiveapps.treeview = {
	globals : {},
	settings : {
		tvSpecific : {}
	},
	
	augmentToJStree : function (v, parentNode, titl) {
		var levelCount = {
			topKeys: 0,
			keys: 0,
			values: 0,
			depth: 0,
			byteSize: 0,
			level: (parentNode ? parentNode.stats.level : 0),
			keyNameOrType: titl,
			isSubArray: false//,
			//parentNode: parentNode
		};

		var w = {
			text : titl,
			children : [],
			stats : levelCount,
			state : {
				//opened : true
			}
		};

		if (typeof v == 'function') {
			/*
			var q = {
				hmFunction: '' + v //convert to plaintext
			};
			v = q;
			*/
			w.stats.isFunction = true;
			v = '' + (v.traceOff?v.traceOff:v);
		} 
		
		if (typeof v == 'object') {
			levelCount.isSubArray = true;
			levelCount.level++;
			if (v.saObjectType) levelCount.saObjectType = v.saObjectType;
			if (v===null) {
				//debugger;
				return v;
			}
			if (typeof v.stats == 'object' && typeof v.children == 'object') {
				return v;
			}
			var maxDepth = 0;
			for (var a in v) {
				/*
				if (a == 'hmo') {
					//sa.hms.settings.cs.hmo = v[a];
					continue;
				};
				*/
				
				if (
					a == 'parentNode'
					|| a == 'saObjectType'
				) {
					continue;
				};
				if (v[a]===null) {
					continue;
				};
				levelCount.topKeys++;
				levelCount.keys++;
				levelCount.byteSize += sa.hms.tools.getByteSize(a);
				
				v[a] = sa.treeview.augmentToJStree(v[a], w, a);
				v[a].stats.keyName = a;
				
				if (typeof v[a].stats != 'object') {
					sa.hms.log('Gathering stats failed for :' + a);
				} else {
					levelCount.byteSize += v[a].stats.byteSize;
					levelCount.values += v[a].stats.values;
					levelCount.keys += v[a].stats.keys;
					maxDepth = Math.max(maxDepth, v[a].stats.depth);
				}
			};
			levelCount.depth++;
			levelCount.depth += maxDepth;
			if (levelCount.depth > 1) levelCount.values++; //you won't believe how long it took to find this one.
		} else {
			levelCount.values++;
			levelCount.byteSize += sa.hms.tools.getByteSize(v);
		};

		sa.hms.settings.cs.debugID++;
		levelCount.keyID = 'hm_' + sa.hms.settings.cs.debugID; 
		sa.hms.settings.cs.debugID++;
		levelCount.valueID = 'hm_' + sa.hms.settings.cs.debugID; 
		levelCount.isObject = levelCount.isSubArray;

		if (typeof v == 'object') {
			for (k in v) {
				if (k == 'hmo') {
					w[k] = v[k];
				} else if (k=='saObjectType') {
					w.stats.saObjectType = v[k];
				} else {
					w.children[w.children.length] = v[k];
					if (levelCount.level == 1) levelCount.values++;
				}
			}
		} else {
			//w.children[w.children.length] = v;
			w.text += ' - ' + v;
		};
		if (levelCount.level == 1) levelCount.values--;
		sa.hms.settings.cs.dataPointers[levelCount.keyID] = {
			type : 'key',
			val : w
		};
		sa.hms.settings.cs.dataPointers[levelCount.valueID] = {
			type : 'data',
			val : w
		};
		
		return w;
	},
	


	create : function (divID, urlTop, urlIframe, varID) {
		var 
		v = eval (varID),
		vtv = [
			sa.treeview.augmentToJStree (sa.m.cloneObject(v))
		];
		
		vtv[0].text = 'Agendas';
		// the following should produce a "templates" node below "agendas" in my treeview 
		vtv[vtv.length] = {
			children : [],
			state : {},
			stats : {},
			text : 'Templates'
		};
		
		var
		tvs = sa.treeview.settings.tvSpecific,
		tvsID = divID+'::'+urlTop+'::'+urlIframe;

		
		tvs[tvsID] = {
			divID : divID,
			urlTop : urlTop,
			urlIframe : urlIframe,
			varID : varID,
			v : v,
			vtv : vtv
		};
		//debugger;
		sa.treeview.redraw();
		
		//jQuery('#'+divID).jstree({core : {data : vtv}});
		//debugger;
	},
	
	redraw : function () {
		var
		tvs = sa.treeview.settings.tvSpecific;
		
		for (var id in tvs) {
			var it = tvs[id];
			sa.treeview.redraw_pane (tvs, it);
		}
	},
	
	redraw_pane : function (tvs, it) {
		var
		html = sa.treeview.redraw_pane_node(tvs, it, it.vtv);
		
		//debugger;
		
		jQuery('#'+it.divID).html(html);
		
		jQuery('.saAgenda_namedAgendas_treenode_icon').click(sa.treeview.nodes.eventHandlers.clickOnIcon);
		jQuery('.saAgenda_namedAgendas_treenode_text')
			.click(sa.treeview.nodes.eventHandlers.click)
			.mousedown(sa.treeview.nodes.eventHandlers.mousedown);
	},
	
	redraw_pane_node : function (tvs, it, itChildren, level, dataPath) {
		if (typeof level=='undefined') level = 0;
		if (typeof dataPath==='undefined') dataPath = '0';
		var html = '';
		
		for (var i=0; i<itChildren.length; i++) {
			var c = itChildren[i];
			//if (i==1) debugger;
			if (c.stats.saObjectType && c.stats.saObjectType.indexOf('listInTreeView')!=-1) {		
				var
				icon = (
					c.children.length > 0
					? 'saAgenda_namedAgendas_treenode_icon_foldClose'
					: 'saAgenda_namedAgendas_treenode_icon_noChildren'
				);
				
				html += 
					'<div style="padding-left:'+(5+(10*level))+'px;" class="saAgenda_namedAgendas_treenode" level="'+level+'" datapath="'+c.text+'__'+dataPath+'">'
					+'<table>'
					+'<tr>'
					+'<td style="vertical-align:middle;"><div class="saAgenda_namedAgendas_treenode_icon '+icon+'" style="">&nbsp;</div></td>'
					+'<td style="vertical-align:middle;"><div class="saAgenda_namedAgendas_treenode_text">'+c.text+'</div></td>'
					+'</tr>'
					+'</table>'
					+'</div>';
			};
			
			if (c.children.length>0) {
				html += sa.treeview.redraw_pane_node (tvs, it, c.children, level+1, dataPath+'.'+i);
			}
		};
		
		//debugger;
		return html;
	},
	
	nodes : {
		eventHandlers : {
			click : function (evt) {
				if (jQuery(this).hasClass('saAgenda_namedAgendas_treenode_text_selected')) {
					jQuery(this).removeClass('saAgenda_namedAgendas_treenode_text_selected');
				} else {
					jQuery(this).addClass('saAgenda_namedAgendas_treenode_text_selected');
				}
				jQuery('.saAgenda_namedAgendas_treenode_text_selected').not(this).removeClass('saAgenda_namedAgendas_treenode_text_selected');
			},
			clickOnIcon : function(evt) {
				//debugger;
				if (jQuery(this).hasClass('saAgenda_namedAgendas_treenode_icon_foldClose')) {
					var 
					seenThis = false,
					currLevelDontTouch = [],
					thiz = jQuery(this).parents('.saAgenda_namedAgendas_treenode')[0],
					thizLevel = parseInt(jQuery(thiz).attr('level'));
					//debugger;
					jQuery('.saAgenda_namedAgendas_treenode').each(function(idx,treenode){
						if (!seenThis) {
							seenThis = (this===thiz);
						} else {
							var currLevel = parseInt(jQuery(this).attr('level'));
							if (currLevel >= thizLevel + 1) {
								if (jQuery('.saAgenda_namedAgendas_treenode_icon', jQuery(this)).hasClass('saAgenda_namedAgendas_treenode_icon_foldClose')) {
									currLevelDontTouch[currLevelDontTouch.length] = currLevel
								} else {
									for (var j=0; j<currLevelDontTouch.length; j++) {
										if (currLevelDontTouch[j] === currLevel) delete currLevelDontTouch[j];
									}
								}
								var show = true;
								/*
								for (var j=0; j<currLevelDontTouch.length; j++) {
									var cldt = currLevelDontTouch[j];
									if (currLevel>cldt) show = false;
								}*/
								if (show) jQuery(this).slideUp('normal');
							} else {
								seenThis = false; // dont fold open the wrong folders.
							}
						}
					});
					
					jQuery(this).removeClass('saAgenda_namedAgendas_treenode_icon_foldClose').addClass('saAgenda_namedAgendas_treenode_icon_foldOpen');
				} else {
					var 
					seenThis = false,
					currLevelDontTouch = [],
					thiz = jQuery(this).parents('.saAgenda_namedAgendas_treenode')[0],
					thizLevel = parseInt(jQuery(thiz).attr('level'));
					
					jQuery('.saAgenda_namedAgendas_treenode').each(function(idx,treenode){
						if (!seenThis) {
							seenThis = (this===thiz);
						} else {
							debugger;
							var currLevel = parseInt(jQuery(this).attr('level'));
							if (currLevel >= thizLevel + 1) {
								if (jQuery('.saAgenda_namedAgendas_treenode_icon', jQuery(this)).hasClass('saAgenda_namedAgendas_treenode_icon_foldOpen')) {
									currLevelDontTouch[currLevelDontTouch.length] = currLevel
								} else {
									for (var j=0; j<currLevelDontTouch.length; j++) {
										if (currLevelDontTouch[j] === currLevel) delete currLevelDontTouch[j];
									}
								}
								var show = true;
								for (var j=0; j<currLevelDontTouch.length; j++) {
									var cldt = currLevelDontTouch[j];
									if (currLevel>cldt) show = false;
								}
								if (show) jQuery(this).show('normal');
							} else {
								seenThis = false;
							}
						}
					});
					
					jQuery(this).addClass('saAgenda_namedAgendas_treenode_icon_foldClose').removeClass('saAgenda_namedAgendas_treenode_icon_foldOpen');
				}
			},
			mousedown : function (evt) {
				switch (evt.which) {
					case 3 :
						alert ('right click');
						break;
				}
			}
		}
	}
}