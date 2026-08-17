seductiveapps.vt = seductiveapps.vividText = {
	about : {
		whatsThis : 'seductiveapps.vt = seductiveapps.vividText = cool color animations for HTML texts',
		copyright : '(c) (r) 2013-2017 by [the owner of seductiveapps.com] <seductiveapps@gmail.com>',
		license : 'http://seductiveapps.com/LICENSE.txt',
		noWarranty : 'NO WARRANTY EXPRESSED OR IMPLIED. USE ONLY AT YOUR OWN RISK.',
		version : '1.1.0',
		firstReleased : '2013 January 13, 13:28 CET',
		lastUpdated : '2018 January 15, 00:18 CET'
	},
	globals : {
		animationTypes : [
			/* 'singleColor', 'singleBackgroundColor', 'singleColorBackgroundColor', 'doubleColor', 'doubleBackgroundColor', 'doubleColorBackgroundColor', */
			'animatedDoubleColor', 'animatedDoubleBackgroundColor', 'animatedDoubleColorBackgroundColor']
	},
	settings : {
		masterIdx : 0,
		commands : []
	},

	init : function () {
		
	},
	
	initElement : function (ajtCommand) {
		
		/*if (sa.m.userDevice.isPhone) {
                // if (sa.s.c.settings.browsersideLoadBalance.ui.plugins.vividText==='disabled') { // (sa.m.userDevice.isPhone) {
			sa.m.log (1, 'seductiveapps.vividText.initElement() : sa.m.userDevice.isPhone===true, saving phone CPU resources by not vividTexting this element.');
			return false;
		}*/
		
		if (ajtCommand.el.children.length>0) {
			sa.m.log (1, 'seductiveapps.vividText.initElement(): el.children.length>0! NOT initializing this element for color animations!');
			return false;
		};
		
		delete sa.vt.settings.allOnPageFullyInitialized;
		
		var 
		orig = ajtCommand.el.innerHTML,
		html = '',
		extraChars = 5,
		startChr = 0, 
		endChr = -1 + ajtCommand.el.innerHTML.length + (2 * extraChars),
		length = endChr - startChr,
		evenCount = Math.floor(length/2)===(length/2),
		cmd = {
			typeOfObject : 'seductiveapps.vividText.settings.commands',
			el : ajtCommand.el, 
			theme : ajtCommand.theme,
			animationType : ajtCommand.animationType,
			animationSpeed : ajtCommand.animationSpeed ? ajtCommand.animationSpeed : 2000,
			animationDirection : 'forward',
			extraChars : extraChars,
			beginIdx : sa.vt.settings.masterIdx,
			startChr : startChr,
			endChr : endChr,
			length : length,
			evenCount : evenCount,
			animatedDoubleMiddleIdx : 1,
			animationData : []
		};
        
		for (var i=0; i<sa.vt.settings.commands.length; i++) {
			var it = sa.vt.settings.commands[i];
            if (!it) continue;
			if (it.el.id===cmd.el.id) {
                delete sa.vt.settings.commands[i];
                i--;
                delete sa.vt.settings.allOnPageFullyInitialized;
            }
		};
        
        
        //sa.vt.unInitElement(cmd.el);

		for (var i=-1*cmd.extraChars; i<orig.length+cmd.extraChars; i++) {
			var  
			ampstr=orig.substr(i,5),
			ampcheck=(ampstr==='&amp;'),
			c=(ampcheck? ampstr : (i >= 0 && i < orig.length ? orig.substr(i,1) : '&nbsp;')), 
			id=sa.vt.settings.masterIdx++;
			if(ampcheck) {
				i += 4;
				cmd.endChr-=4;
			}
			
			html += '<span id="SA_ajt_'+id+'" '
				+ (
					c==='&nbsp;'
					? 'style="position:absolute;width:0px;"'
					: ''
				)
				+ '>'+c+'</span>';
		};

		cmd.el.innerHTML = html;
		
		cmd.endIdx = sa.vt.settings.masterIdx - 1;
		//sa.vt.settings.commands[cmd.el.id] = cmd;
		sa.vt.settings.commands[sa.vt.settings.commands.length] = cmd;
		
		//debugger;
		sa.m.waitForCondition ('vividText for #'+cmd.el.id+' waiting for sa.m.settings.initialized.site', 
			function () {
				return sa.m.settings.initialized.site
			}, function () {
				//debugger;
                for (var i=0; i<sa.vt.settings.commands.length; i++) {
                    setTimeout (function () {
                        sa.vt.initAnimationsOnElement(cmd.el);
                    }, i * 1500); // calculate only 1 at a time. the number is in milliseconds.
                }
			}, 500
		);
		
		return cmd;
	},
	
	findCmd : function (elid) {
		for (var i=0; i<sa.vt.settings.commands.length; i++) {
			var it = sa.vt.settings.commands[i];
            if (!it) continue;
			if (it.el.id===elid) return i;
		};
		return false;
	},

	unInitElement : function (el) {
		var cmdIdx = sa.vt.findCmd(el.id);
		if (cmdIdx===false) {
			sa.m.log (1, 'seductiveapps.vividText.unInitElement(): no command issued for el.id='+el.id);
			return false;
		};
		var cmd = sa.vt.settings.commands[cmdIdx];
		
		for (var i=0; i<el.children.length; i++) {
			var
			html='',
			e=el.children[i];
			
			html += e.innerHTML;
		};
		
		el.innerHTML = html;
		cmd.deconstructed=true;
	},
	
	initAnimationsOnElement : function (el) {
		var cmdIdx = sa.vt.findCmd(el.id);
		if (cmdIdx===false) {
			sa.m.log (1, 'seductiveapps.vividText.unInitElement(): no command issued for el.id='+el.id);
			return false;
		} else {
			var cmd = sa.vt.settings.commands[cmdIdx];
            if (!document.getElementById('SA_ajt_'+(cmd.beginIdx+cmd.startChr))) return false;
			sa.vt.doAnimation (cmd);	
		}
	},
	
	doAnimation : function (cmd) {
		//sa.vt.doAnimation_paint_clear (cmd);
        
        if (!document.getElementById('SA_ajt_'+(cmd.beginIdx+cmd.startChr))) return false;
        
		
		if (cmd.animationType.match('animatedDoubleColor')) sa.vt.doAnimation_paint_double_animated (cmd);
	},
	
	doAnimation_paint_double_animated : function (cmd) {

		if (cmd.animationDirection=='forward') {
			cmd.animatedDoubleMiddleIdx++;
			if (cmd.animatedDoubleMiddleIdx >= cmd.length  ) {
				cmd.animationDirection = 'backward';
				cmd.animatedDoubleMiddleIdx--;
			}
		} else {
			cmd.animatedDoubleMiddleIdx--;
			if (cmd.animatedDoubleMiddleIdx <= 0 ) {
				cmd.animationDirection = 'forward';
				cmd.animatedDoubleMiddleIdx = 0;
			}
		};
		

		if (!cmd.animationData[cmd.animatedDoubleMiddleIdx]) {
			cmd.colorSteps1 = sa.cg.generateList_basic (cmd.theme, cmd.animatedDoubleMiddleIdx);
			cmd.colorSteps2 = sa.cg.generateList_basic (cmd.theme, cmd.length-cmd.animatedDoubleMiddleIdx);

			cmd.colorSteps2.reverse();

			cmd.animationData[cmd.animatedDoubleMiddleIdx] = {
				colorSteps1 : cmd.colorSteps1,
				colorSteps2 : cmd.colorSteps2
			}
		} else {
			var ad = cmd.animationData[cmd.animatedDoubleMiddleIdx];
			cmd.colorSteps1 = ad.colorSteps1;
			cmd.colorSteps2 = ad.colorSteps2;
			cmd.initialized = true;
		};
		
/*		sa.vt.doAnimation_paint_stretch (cmd, cmd.colorSteps1, cmd.startChr, cmd.animatedDoubleMiddleIdx+cmd.extraChars);
		sa.vt.doAnimation_paint_stretch (cmd, cmd.colorSteps2, cmd.animatedDoubleMiddleIdx+-cmd.extraChars, cmd.endChr+(cmd.extraChars) );*/



		sa.vt.doAnimation_paint_stretch (cmd, cmd.colorSteps1, cmd.startChr, cmd.animatedDoubleMiddleIdx);
		sa.vt.doAnimation_paint_stretch (cmd, cmd.colorSteps2, cmd.animatedDoubleMiddleIdx, cmd.endChr);
		
		if (
			cmd.animatedDoubleMiddleIdx > 0
			&& !sa.vt.settings.allOnPageFullyInitialized
		) {
			for (var i = 0; i<sa.vt.settings.commands.length; i++) {
				var it = sa.vt.settings.commands[i];
                if (!it) continue;
				if (!it.initialized && i > 1) {
					setTimeout (sa.m.traceFunction(function () {
						sa.vt.doAnimation (cmd);
					}), 10);
					break;
				}
				if (cmd.el.id === it.el.id) {
					setTimeout (sa.m.traceFunction(function () {
						sa.vt.doAnimation (cmd);
					}), cmd.animationSpeed/(cmd.length));
					break;
				}
			}
			if (i==sa.vt.settings.commands.length-1) sa.vt.settings.allOnPageFullyInitialized = true;
		} else {
			setTimeout (sa.m.traceFunction(function () {
				sa.vt.doAnimation (cmd);
			}), cmd.animationSpeed/(cmd.length));
		}
	},
	
	doAnimation_paint_stretch : function (cmd, colorSteps, startChr, endChr) {
		var l = endChr - startChr;
		for (var i=0; i<=l; i++) {
			var 
			idx = cmd.beginIdx + startChr + i,
			e = document.getElementById ('SA_ajt_'+idx),
			cs = colorSteps[i];
			if (!cs || !e) return false; // debugger might b an idea. some other time.
			if (cmd.animationType=='animatedDoubleColor' || cmd.animationType=='animatedDoubleColorBackgroundColor') {
				e.style.color = cs.color;
			};
			if (cmd.animationType=='animatedDoubleBackgroundColor' || cmd.animationType=='animatedDoubleColorBackgroundColor') {
				e.style.backgroundColor = cs.background;
			}
		}
	},
	
	doAnimation_paint_clear : function (cmd) {
		for (var i=cmd.beginIdx; i<=cmd.endIdx; i++) {
			var 
			e = document.getElementById('SA_ajt_'+i);
			if (e) {
				e.style.color = '';
				e.style.backgroundColor = '';
			}
		}
	}
};
