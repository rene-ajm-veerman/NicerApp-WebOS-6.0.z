//na.m.waitForCondition ( 'na.m.desktopInitialized()', na.m.WebOSidle, function () {
        
//na.analytics.logMetaEvent ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer : init-stage-1');

delete na.mp;
debugger;
na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer'] = na.musicPlayer = na.mp = {
	about : {
		whatsThis : 'Complete application code for the music playback-and-download site on http://nicer.app/musicPlayer',
		copyright : 'Copyrighted (c) 2011-2025 by Rene AJM Veerman - rene.veerman.netherlands@gmail.com',
		license : 'https://opensource.org/license/mit',
		version : '4.0.0',
		firstReleased : '2011',   
		lastUpdated : '2025-04-18(Friday) 01:23am Central European Summer Time - AMS https://Amsterdam.nl',
		knownBugs : [
			"None at the moment, i think. Please report any bugs you find.."
		]
	},
    globals : {
        url : '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer/'
    },
	settings : {
		playingIndex : 0,
		paused : false,
		stopped : true,
		repeating : false,
        shuffle : false,
		masterLeftOffset : null,
        onResizeReposition : true,
		dialogs : {},
        firstRun : true,
        
        loadedIn : {
            '#siteContent' : {
                onload : function (settings) {
                    na.site.settings.current.app = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer';
                    //na.analytics.logMetaEvent ("na.musicPlayer.onload() called.");

                    debugger;
                    $('#siteContent > .vividDialogContent').css({ overflow : 'hidden', direction:'ltr'});

                    $('.audioSeekBar_setting').css({ height : $('.audioSeekBar').height() - 4, marginTop : 2, /*marginLe*/ft : 2, borderRadius:5 });
                    $('.audioVolumeBar_setting').css({ height : $('.audioVolumeBar').height() - 4, marginTop : 2, marginLeft : 2, borderRadius:5 });
                    
                    $('#siteContent .vividDialog').each(function(idx,el){
                        na.site.settings.dialogs['#'+el.id] = new naVividDialog(el);
                    });
                    $('.audioPlayerUI .vividButton4').each(function(idx,el){
                        if (!na.site.settings.buttons['#'+el.id]) {
                            var btn = new naVividButton(el,null,$(el).parent()[0],false,idx);
                            na.site.settings.buttons['#'+el.id] = btn;
                            if (el.id=='btnPlayPause') $(el).addClass('disabled');
                            if (el.id=='btnMuteUnmute') btn.icon_svg.onclick({currentTarget:el});
                        }

                    });

                    $('.lds-facebook').fadeOut('normal');
                    
                    /*TOXIC!
                     * na.desktop.registerProgress ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer', function() {
                        var div = $('#siteContent')[0];
                        na.mp.onresize(div);
                    });*/
                    na.desktop.registerCallback ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer', '#siteContent', function (cb, div, calculationResults, sectionIdx, section, divOrderIdx) {
                        if (div.id=='siteContent') na.mp.onresize(div);
                        na.mp.settings.onResizeReposition = false;
                    });
                    //TOXIC:    na.mp.onresize($('#siteContent')[0]);
                    na.desktop.resize();
                },
                ondestroy : function (settings) {
                    //na.desktop.deleteProgress ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer');
                    na.desktop.deleteCallback ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer');
                    clearInterval (na.mp.settings.timeDisplayInterval);
                }
            }
        }
	},
	
	queueMP3 : function (id, file) {
		var pl = document.getElementById('playlist');
		var pc = na.mp.playlistCount++;
		
		var newPlaylistItem = npi = document.createElement('div');
		npi.setAttribute ('file', file);
		npi.id = 'playlist_' + pc;
        npi.file = file;
        //npi.style.padding = '2px';
        //npi.style.height = '25px';
		npi.className = 'mp3 vividButton';
        $(npi).attr('theme','dark');


		npi.innerHTML = 
			'<a href="javascript:na.apps.loaded["NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer"].selectMP3(\'' + npi.id + '\', \'' + file + '\');">'
			+ label
			+ '</a>';
		
        if ($('#textCheck')[0]) var span = $('body').append('<div id="textCheck" class="vividButton vividButton_text" style="width:'+($('#mp3s').width()-50)+'px;opacity:0.0001">'+label+'</div>'); else var span = $('#textCheck');
        try {
            var h = span.height();
        } catch (e) {
            var h = e;
            
        }
        console.log ('#textCheck4 h=',h, label);
        $('#textCheck').remove();                
        var html = '<div id="'+t.el.id+'_'+idx+'" class="vividButton" theme="'+t.t+'" style="opacity:0.0001;height:'+(h+30)+'px">'+$(li).children('a')[0].outerHTML+'</div>';
        $(npi).css({height:h+100});    
            
		pl.appendChild (npi);
        na.mp.onresize();
        if (na.mp.settings.stopped) {
            na.mp.selectMP3 (npi.id, file);
        }
	},
	
	selectMP3 : function (id, file) {
        if (na.mp.settings.ignoreClick) { na.mp.settings.ignoreClick = false; return false; }
        
        clearInterval (na.mp.settings.timeDisplayInterval);
        
        var firstRun = na.mp.settings.firstRun;
        if (firstRun) na.mp.settings.firstRun = false;
        
		na.mp.settings.activeID = id;
		
        na.mp.settings.playingIndex = false;
        delete na.mp.settings.stopped;
		var pl = $('#playlist')[0];
		for (var i=0; i<pl.children.length; i++) {
            if (pl.children[i].id==id || (pl.children[i].children[0] && pl.children[i].children[0].id==id)) na.mp.settings.playingIndex = i;
		};
        if (!file) debugger;
        
        $('.mp3').removeClass('selected').removeClass('vividButtonSelected').addClass('vividButton');
        $('#'+id).addClass('selected').removeClass('vividButton').addClass('vividButtonSelected');
        $('#btnPlayPause').addClass('selected');
        $('#line2').addClass('atPlay');
        $('#from_pause_to_play')[0].beginElement();

        var ajaxCommand = {
			type : 'GET',
			url : '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer/music/'+na.site.globals.app['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer']['set']+'/' + file + '.json',
            error: function(l0_jqXHR, l0_textStatus, l0_errorThrown) {
				var html = '';
                html += '<div style="height:15px;">&nbsp;</div>';
                html += '<table>';
				html += '<tr><td colspan="2" style="text-align:center"><a href="' + na.site.globals.url + '/download_mp3.php?file='+file+'">download</a></td></tr>';
				html += '<tr><td><span class="mp3_info_label mp3_title_label">title</span></td><td><span class="mp3_title">'+file+'</span></td></tr>';
                html += '</table>';
                na.mp.updateDescriptionDiv(id, file, html);
            },
			success : function (json, ts) {
				if (typeof json!=='object') json = eval ('('+json+') ');
                if (json.description) {
                    var html = '';
                    html += '<div style="height:15px;">&nbsp;</div>';
                    html += '<div style="margin-bottom:10px;font-size:inherit;text-align:center;"><a href="' + na.mp.globals.url + '/download_mp3.php?file='+file+'">download</a></div>';
                    html += '<div style="margin-bottom:10px;font-size:inherit;"><span class="mp3_info_label mp3_title_label hasOwnProps">title</span> <span class="mp3_title hasOwnProps">'+json.title+'</span></div>';
                    html += '<div style="margin-bottom:10px;font-size:inherit;"><span class="mp3_info_label mp3_album_label hasOwnProps">album</span> <span class="mp3_album hasOwnProps">' + json.album + '</span></div>';
                    html += '<div style="margin-bottom:10px;font-size:inherit;"><span class="mp3_info_label mp3_length_label hasOwnProps">length</span> <span class="mp3_length hasOwnProps">' + json.length + '</span></div>';
                    html += '<div style="margin-bottom:10px;font-size:inherit;"><span class="mp3_info_label mp3_year_label hasOwnProps">year</span> <span class="mp3_year hasOwnProps">'+json.year+'</span></div>';
                    html += '<div style="font-size:inherit;"><span class="mp3_description hasOwnProps">' + json.description + '</span></div>';
                } else {
                    var html = '';
                    html += '<table>';
                    html += '<tr><td colspan="2" style="text-align:center"><a href="' + na.mp.globals.url + '/download_mp3.php?file='+file+'">download</a></td></tr>';
                    html += '<tr><td colspan="2" style="text-align:center"><a href="https://youtube.com/watch?v='+json.youtubeID+'" target="_new">youtube link</a></td></tr>';
                    html += '</table>';
                }
                na.mp.updateDescriptionDiv(id, file, html);
			}
		};
		$.ajax(ajaxCommand);
        //na.analytics.logMetaEvent ('appEvent : musicPlayer : selectMP3() file='+file);
	},
    
    updateDescriptionDiv : function (id, file, html) {
        $('#siteIntroText').fadeOut (500, function () {
            $('.mp3').each (function (index,element) {
                if (this.id=='') return false;
                if (this.id==id) var state = 'selected'; else var state='normal';
            });
            $('#mp3descText').fadeOut('fast', function() {
                $('#mp3descText').html (html).delay(100).fadeIn(1000);
                $('#mp3descText a').each(function(idx,aEl) {
                    if (!aEl.el) {
                        aEl.vividTextCmd = {
                                el : aEl,
                                theme : na.cg.themes.naColorgradientSchemeGreenVividText2,
                                animationType : na.vividText.globals.animationTypes[0],
                                animationSpeed : 4 * 1000
                        };
                        na.vividText.initElement (aEl.vividTextCmd);
                    }

                });


            });

            
            var 
            mp3 = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer/music/'+na.site.globals.app['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer']['set']+'/' + file;

            $('#audioTag')[0].src = mp3;
            $('#audioTag')[0].play();
            $('.audioVolumeBarLabel').html ( 'Volume : '+ Math.round($('#audioTag')[0].volume*100) );
            na.mp.settings.stopped = false;

            if (
                $('#btnPlayPause').is('.disabled') 
            ) {
                $('#btnPlayPause').removeClass('disabled');
                na.ui.vividButton.onclick({currentTarget:$('#btnPlayPause')[0]});
            }
            
            setTimeout(na.mp.setTimeDisplayInterval,1000);
        });

    },
    
    setTimeDisplayInterval : function () {
        clearInterval (na.mp.settings.timeDisplayInterval);
        //if (!na.mp.settings.timeDisplayInterval)
            na.mp.settings.timeDisplayInterval = setInterval (function() {
                var 
                length = $('#audioTag')[0].duration, // in seconds
                strLength = na.mp.convertSecondsToTimeString(length),
                currentTime = $('#audioTag')[0].currentTime, // in seconds
                strCurrentTime = na.mp.convertSecondsToTimeString(currentTime);
                
                if (currentTime==length) {
                    debugger;
                    clearInterval (na.mp.settings.timeDisplayInterval);
                    na.mp.next();
                } else {
                    
                    $('.audioSeekBarLabel_length').html(strLength);
                    $('.audioSeekBarLabel_currentTime').html(strCurrentTime);
                    
                    var 
                    widthSeekBar = $('.audioSeekBar').width(),
                    widthPlayBar = Math.floor((widthSeekBar * currentTime)/length);
                    
                    if (!na.mp.settings.maxPlayBarWidth) na.mp.settings.maxPlayBarWidth = widthSeekBar;
                    $('.audioSeekBar_setting')[0].style.width = (widthPlayBar <= na.mp.settings.maxPlayBarWidth ? widthPlayBar : na.mp.settings.maxPlayBarWidth)+'px';
                }
                
            }, 1000);
    },
    
    convertSecondsToTimeString : function (seconds) {
        var 
        hours = Math.floor(seconds/3600),
        minutes = Math.floor( (seconds-(hours * 3600)) / 60 ),
        secs = Math.floor(seconds - (hours * 3600) - (minutes * 60));
        
        if (hours<10) hours = '0'+hours;
        if (minutes<10) minutes = '0'+minutes;
        if (secs<10) secs = '0'+secs;
        
        return hours+':'+minutes+':'+secs;
    },
	
	playpause : function () {
		if (na.mp.settings.stopped || na.mp.settings.paused) {
			$('#audioTag')[0].play();
			na.mp.settings.paused = false;
			na.mp.settings.stopped = false;
            //$('#from_play_to_pause')[0].beginElement();
            //$('#btnPlayPause').addClass('selected');
            //na.ui.vb.hoverOver(na.ui.vb.settings.buttons['btnPlayPause']);
		} else {
			$('#audioTag')[0].pause();
			na.mp.settings.paused = true;
			na.mp.settings.stopped = false;
            //$('#from_pause_to_play')[0].beginElement();
            //$('#btnPlayPause').removeClass('selected');
            //na.ui.vb.hoverOut(na.ui.vb.settings.buttons['btnPlayPause']);
		}
	},
	
	stop : function () {
        na.ui.vividButton.onclick({currentTarget:$('#btnPlayPause')[0]});
        $('#btnPlayPause').addClass('disabled');
		$('#audioTag')[0].pause();
		na.mp.settings.stopped = true;

        $('.mp3').each (function (index,element) {
			if (this.id=='') return false;
		});
		$('#mp3descText').fadeOut (1000);
		setTimeout (function () {
			$('#siteIntroText').fadeIn (1000);
		}, 1010);
	},
    
    next : function () {
        if (na.mp.settings.playingIndex===false) {
            return false;
        } else if (na.mp.settings.shuffle) {
            var found2 = true;
            while (found2) {
                var pl = $('#playlist')[0];
                if (!na.mp.settings.shuffleList) {
                    na.mp.settings.shuffleList = pl.children;
                    for (var i=0; i<pl.children.length; i++) {
                        pl.children[i].shufflePlayed = i === na.mp.settings.playingIndex;
                    }
                }
                var found = false, i = 0;
                while (!found && i < 100) {
                    var newIndex = Math.floor(Math.random() * (pl.children.length - 1));
                    found = pl.children[newIndex] && !pl.children[newIndex].shufflePlayed;
                    i++;
                    if (found) {
                        i = 0;
                        found2 = false;
                    };
                }
                if (i == 99) {
                    found2 = !found && na.mp.settings.repeating;
                    if (found2) delete na.mp.settings.shuffleList;
                }
            }
            for (var i=0; i<pl.children.length; i++) {
                var newIndex2 = 'playlist_' + newIndex;
                if (pl.children[i].id == newIndex2) {
                    na.mp.selectMP3 (newIndex2, $(pl.children[i]).attr('file'), false);
                    return true;
                }
                if (pl.children[i].children[0] && pl.children[i].children[0].id== newIndex2) {
                    na.mp.selectMP3 (newIndex2, $(pl.children[i].children[0]).attr('file'), false);
                    return true;
                }
            }

        } else {
            var pl = $('#playlist')[0];
            for (var i=0; i<pl.children.length; i++) {
                var newIndex = 'playlist_' + (na.mp.settings.playingIndex + 1);
                if (pl.children[i].id == newIndex) {
                    na.mp.selectMP3 (newIndex, $(pl.children[i]).attr('file'), false);
                    return true;
                }
                if (pl.children[i].children[0] && pl.children[i].children[0].id== newIndex) {
                    na.mp.selectMP3 (newIndex, $(pl.children[i].children[0]).attr('file'), false);
                    return true;
                }
            }
            if (na.mp.settings.repeating) {
                debugger;
                var newIndex = 'playlist_0';
                i = 0;
                if (pl.children[i].id == newIndex) {
                    na.mp.selectMP3 (newIndex, $(pl.children[i]).attr('file'), false);
                    return true;
                }
                if (pl.children[i].children[0] && pl.children[i].children[0].id== newIndex) {
                    na.mp.selectMP3 (newIndex, $(pl.children[i].children[0]).attr('file'), false);
                    return true;
                }
            }
        };            
    },
	
	mute : function () {
		if (na.mp.settings.muted) {
			$('#audioTag')[0].muted = false;
			na.mp.settings.muted = false;
		} else {
			$('#audioTag')[0].muted = true;
			na.mp.settings.muted = true;
		}
	},

	toggleShuffle : function () {
		na.mp.settings.shuffle = !na.mp.settings.shuffle;
	},

	toggleRepeat : function () {
		na.mp.settings.repeating = !na.mp.settings.repeating;
	},
    
    seek : function (evt) {
        var 
        length = $('#audioTag')[0].duration, // in seconds
        strLength = na.mp.convertSecondsToTimeString(length),
        currentTime = $('#audioTag')[0].currentTime;
        
        $('.audioSeekBarLabel_length').html(strLength);
        $('.audioSeekBarLabel_currentTime').html(currentTime);
        
        var 
        widthSeekBar = $('.audioSeekBar').width(),
        widthPlayBar = evt.offsetX;//Math.round((widthSeekBar * evt.offsetX)/length),
        newCurrentTime = Math.round((widthPlayBar * length)/widthSeekBar);

        $('.audioSeekBar_setting')[0].style.width = widthPlayBar+'px';
        $('#audioTag')[0].currentTime = newCurrentTime;
    },
    
    setVolume : function (evt) {
        var 
        widthVolumeBar = $('.audioVolumeBar').width();
        $('#audioTag')[0].volume = evt.offsetX / widthVolumeBar;
        $('.audioVolumeBarLabel').html ( 'Volume : ' + Math.round($('#audioTag')[0].volume * 100) );
        $('.audioVolumeBar_setting').css ({ width : evt.offsetX });
    },
    
    mp3drag : {
        containment : 'window',
        connectToSortable : '#playlist',
        refreshPositions : true,
        drag : function (evt, ui) {
            //debugger;
            //$(ui.helper[0]).css({opacity:0.0001});
        },
        helper : function (evt, ui) {
            if (this.id.indexOf('playlist_')!==-1) return false;
            if (this.id===na.mp.settings.resortedItem) return false;

                var
                div = document.createElement('div'),
                div2 = document.createElement('div'),
                scale = $('#siteContent > .vividDialogContent').css('scale');
                if (scale.indexOf(' ')!==-1) {
                    var
                    sp = scale.split(' '),
                    scaleX = parseFloat(sp[0]),
                    scaleY = parseFloat(sp[1]);
                } else {
                    var
                    scaleX = scale;
                    scaleY = scale;
                }
                $(this).add(div).add(div2).css({ position : 'absolute', zIndex : 110000 });

                $(document.body).append(div);
                $(div).append(div2);
                $(div2).css({
                    top : $(evt.currentTarget).offset().top,
                    left : $(evt.currentTarget).offset().left
                });
                na.mp.globals.mp3evt = $.extend({},evt);
                na.mp.globals.mp3div2 = div2;
                na.mp.globals.mp3sItem = this;
                na.mp.globals.mp3scale = scale;
                na.mp.globals.mp3scaleX = scaleX;
                na.mp.globals.mp3scaleY = scaleY;


            //debugger;
            var nt = $(this).clone(true,true).appendTo(na.mp.globals.mp3div2);
            $(nt[0]).css({
                position : 'relative',
                zIndex : 110000,
                marginLeft : ($('#playlist').width() - $(this).width())/2,
                width : $(this).width(),
                height : $(this).height(),
                scale: scale,
                display : 'block'
            });
            nt[0].id = nt[0].id.replace('mp3','dragging');
            na.mp.globals.mp3dragged = nt[0];//this
            //$(document.body).append(div);
            //$(this).remove();
            return nt[0];
        }
    },
    
    mp3sortable : {
            //items : '> div',
            //tolerance : 'pointer',
			revert : true,
			start : function (evt, ui) {
                //var x = evt.originalEvent.path[0];
                //if (x.id.match('playlist')) na.mp.settings.ignoreDrop = true;
                na.mp.settings.ignoreClick = (evt.detail===1);
			},
			stop : function (evt, ui) {
                setTimeout(na.mp.reorderPlaylist, 500);
			},
            update : function (evt, ui) {
                na.mp.reorderPlaylist();
            }
    },
    
	setupDragNDrop : function () {
		var mp3s = $('#mp3s > .mp3');
		mp3s.draggable (na.mp.mp3drag);
		$('#playlist').sortable(na.mp.mp3sortable);
		$('#playlist').droppable ({
            tolerance : 'pointer',
            activate : function (evt, ui) {
                /*
                debugger;
                var
                pl = $('#playlist')[0],
                dragged = $(ui.helper[0]).clone(true,true).detach().appendTo(pl)[0],//na.mp.globals.mp3dragged,
                x = na.mp.globals.mp3sItem;
                debugger;
                $(dragged).css({
                    position:'relative',
                    background : '',
                    color : '',
                    marginLeft : ($('#playlist').width() - $(x).width())/2,
                    left : $(x).offset().left,
                    top : 'auto',
                    display : 'block',
                    width : $(x).width(),
                    height : $(x).height(),
                    scale : 1
                });
                $(x).css({position:'relative'});
                //$(na.mp.globals.mp3dragged).remove();
                na.mp.globals.mp3dragged = dragged;
                //debugger;
                */
                na.mp.globals.mp3dragged = ui.helper[0];

            },
            over : function (evt, ui) {
                $(na.mp.globals.mp3dragged).remove();
            },
			drop : function (evt, ui) {
                if (!ui.helper[0]) return false;
                                  
                if (na.mp.settings.ignoreDrop) {
                    na.mp.settings.ignoreDrop = false;
                    return false;
                } else na.mp.settings.ignoreDrop = true;
                                  
                var 
                pl = $('#playlist')[0],
                dragged = $(ui.helper[0]).clone(true,true).detach().appendTo(pl)[0],
                o = ui.helper[0],
                pc = na.mp.playlistCount;

                let 
                oldID = ui.helper[0].id,
                newID = 'playlist_'+pc;
                
                if (oldID.match('playlist_')) return false;
                $(dragged).attr('id', newID);
                var
                scale = 'scale:'+(na.mp.globals.zfw+' '+na.mp.globals.zfh),
                original = $('#'+oldID);

                dragged.evt = evt;
                dragged.file = original.attr('file');
                dragged.oldID = oldID;

                var
                x = na.mp.globals.mp3sItem;
                $(dragged).css({
                    position:'relative',
                    background : '',
                    color : '',
                    marginLeft : 0,
                    left : na.d.g.margin/2,
                    display : 'block',
                    top : 'auto',
                    width : $(x).width(),
                    height : $(x).height(),
                    verticalAlign : 'middle',
                    scale : scale
                });
                $(dragged).attr('class', 'mp3 vividButton');// ui-draggable ui-draggable-handle');
                $(dragged).attr('file', original.attr('file'));

                $(dragged).attr(
                    'onclick',
                    ''+na.mp.globals.mp3dragged.onclick
                        .toString()
                        .replace("'"+oldID+"'", "'"+newID+"'")
                        .replace('function onclick(event) {', '')
                        .replace('\n}','')
                        .replace (new RegExp(oldID), dragged.id)
                );
                $(ui.helper[0]).remove();
                na.mp.reorderPlaylist();
                
                if (na.mp.settings.stopped)
                    na.mp.selectMP3 (newID, $(dragged).attr('file'));
                                
                na.mp.onresize($('#siteContent')[0]);
                na.mp.playlistCount++;
                
                evt.preventDefault();

                // vital for the 're-ordering' functionality of the #playlist itself :
                $(dragged).draggable({
                    containment : '#playlist',
                    connectToSortable : '#playlist',
                    stack : '.mp3',
                    helper : function (evt, ui) {
                        na.mp.settings.resortedItem = dragged.oldID;
                        var x = $('#'+newID).parent()[0];
                        var div = document.createElement('div');
                        $(div).css({width:'100%'});
                        $(this).clone(true,true).appendTo(div).css({zIndex:1100, color:'yellow', paddingLeft:30}).attr('id',newID);
                        $('#playlist').append(div);
                        return div;
                    },
                    drag : function (evt, ui) {
                        return true;
                    }
                });

                na.site.loadTheme_applySettings(na.site.globals.themes[na.site.globals.themeName], null, false);

                return dragged;
			}
		});
	},
    
    reorderPlaylist : function () {
        $('.ui-sortable-placeholder', $('#playlist')).removeClass('mp3').addClass('mp3-disabled');
        $('.mp3', $('#playlist')[0]).each(function(idx,el){
            let 
            x = $(el).attr('onclick').toString().replace(new RegExp(el.id),'playlist_'+idx),
            x1 = x.replace('javascript:','').trim();
            $(el).attr('onclick','javascript:'+x1);
            el.id = 'playlist_'+idx;
        });
    },
    
	playlistCount : 0,

	onresize : function (div) {
        if (div && div.id!=='siteContent') return false;
        if (!window.top || !$(window.top.document.getElementById('siteContent'))[0]) return false;
		var 
		contentMargin = 20,
		myWidth = $('#siteContent > .vividDialogContent').width() - (contentMargin*2),
		myHeight = (
            $('#siteContent').height()
            - $('#horizontalMover__containmentBox2').height()
            - $('#horizontalMover__containmentBox2')[0].offsetTop
            - (contentMargin*2) - (na.d.g.margin*2) -$('#app__musicPlayer__header').height()
        );
        if (na.desktop.globals.divs.includes('#siteStatusbar')) {
            myHeight = myHeight - contentMargin;
        }
        var columnWidth = ($(window).width()/2)-30;
        if (columnWidth>250) columnWidth = 250;

        var
        contentWidth = columnWidth*2 + 10,
        contentInnerWidth = columnWidth*2 + 20,
        zfh = (
                $('#siteContent').height() /
                ( $('#siteContent').height() - (contentMargin*2) )
        ),
        zfw = (
            (
                ( $('#siteContent').width() )
                / ( (contentWidth * 2) + 30 + (na.d.g.margin*3)  )
            )
        );
        na.mp.globals.zfh = zfh;
        na.mp.globals.zfw = zfw;

        var
        sc_scrollpane = $('#siteContent', window.top.document.body),
		sc_scrollpaneContainer = $('#siteContent', window.top.document.body),
		sc_siteContent = $('#siteContent', window.top.document.body);
        if (!na.site.settings.current.scaleApp) {
            na.site.settings.current.scaleApp = zfw+' '+zfh;
        };

		if (typeof na.mp.settings.masterLeftOffset == 'number' && !na.mp.settings.onResizeReposition) {
			var masterLeftOffset = na.mp.settings.masterLeftOffset;
			if (masterLeftOffset<0) masterLeftOffset=0;
		} else {
			var masterLeftOffset = ((myWidth - contentWidth) / 2);
			if (masterLeftOffset<0) masterLeftOffset=0;
			na.mp.settings.masterLeftOffset = masterLeftOffset;
		}
		
		var dialogMP3sList = '#mp3s';
		if ($('#app__musicPlayer__description').length>0) var dialogMP3desc = '#app__musicPlayer__description';
			else var dialogMP3desc = '#app__musicPlayer__description';
		if ($('#app__musicPlayer__playlist').length>0) var dialogPlaylist = '#app__musicPlayer__playlist, #app__musicPlayer__playlist';
			else var dialogPlaylist = '#app__musicPlayer__playlist';
		if ($('#app__musicPlayer__player').length>0) var dialogPlayer = '#app__musicPlayer__player, #app__musicPlayer__player, #app__musicPlayer__player__CSS3';
			else var dialogPlayer = '#app__musicPlayer__player, #app__musicPlayer__player__CSS3';
            
        var 
        leftOffset = masterLeftOffset + 0,
        playerLeft = (leftOffset + columnWidth + na.d.g.margin),
		dialogsLeft = Math.round (leftOffset);
        $('#app__musicPlayer__header').css ({
            width : contentInnerWidth,
            left : dialogsLeft,
            top : (na.d.g.margin*2)
        });
        $('#app__musicPlayer__header .vividDialogContent').css ({
            overflow : 'hidden'
        });
        
		var 
		timeDelay = 10,
		timeIncrease = 50,
		$dialogHeading = $('#heading_wrapper'),
		$dialogMP3sList = $(dialogMP3sList),
		$dialogMP3desc = $(dialogMP3desc),
		$dialogPlaylist = $(dialogPlaylist),
		$dialogPlayer = $(dialogPlayer),
		centerDialogsWidth = $(dialogMP3sList).width() + $dialogPlaylist.width() + $dialogPlayer.width(),
		dialogsTop = contentMargin + (na.d.g.margin*2) + $('#app__musicPlayer__header').position().top + $('#app__musicPlayer__header').height() + na.d.g.margin,
		dialogsTop = contentMargin + (na.d.g.margin*2) + $('#app__musicPlayer__header').height() + na.d.g.margin,
		dialogsHeight = (myHeight - dialogsTop - contentMargin);
        
		$('#horizontalMover__containmentBox2').css({
			left : contentMargin,
            top : 0,
			width : myWidth,
			opacity : 0.001,
			display : 'block'
		}).animate ({opacity:(0.3)},1000);
		$('#horizontalMover__containmentBox1').css({
			left : contentMargin,
            top : 0,
			width : myWidth,
			opacity : 0.001,
			display : 'block'
		}).animate ({opacity:(0.2)},1300);
		
        $('#app__musicPlayer__player').css({ height : 120 });
        $('#app__musicPlayer__player, #app__musicPlayer__player__CSS3').css ({
            left : playerLeft,
            width : columnWidth,
            top : dialogsTop,
            opacity : 1
        });
        $('#app__musicPlayer__player__CSS3').css ({ left : '', top : '', opacity : 0.5 });
        
        $('#app__musicPlayer__description, #app__musicPlayer__description__CSS3, #app__musicPlayer__description__item__0, #app__musicPlayer__description__item__0__img1, app__musicPlayer__desc__item__0__img2, #app__musicPlayer__description, #app__musicPlayer__description').css({
            position : 'absolute',
            width : columnWidth,
            height : (myHeight - 20 - 120) /3
        });
        $('#app__musicPlayer__description').css({
            left : playerLeft,
            top : dialogsTop + $('#app__musicPlayer__player')[0].offsetHeight + 30,
            width : columnWidth,
            height : ((myHeight - 20 - 120) /3)
        });
        $('#app__musicPlayer__description__CSS3').css({
            width : columnWidth,
            height : (myHeight - 20 - 120) /3
        });
        $('#app__musicPlayer__description > table').css({
            width : '',
            height : ((myHeight - 20 - 120) /3)
        });
        $('#horizontalMover').css({
			left : masterLeftOffset + 10,
            width : $('#app__musicPlayer__header').width()-20,
            top : 2,
            background : 'rgba(243, 245, 39, 0.55)', // yellow, semi-translucent
            boxShadow : '2px 2px 2px rgba(0,0,0,0.7)'
		});
        $('#mp3s').css ({
            visibility : 'visible',
            position : 'absolute',
            left : dialogsLeft,
            width : columnWidth,
            height : myHeight ,
            top : dialogsTop
        });

        $dialogPlaylist.css ({
            left : playerLeft,
            width : columnWidth,
            height : (myHeight - 170 - $('#app__musicPlayer__header').height()) / (3/2),
            top : ($dialogMP3desc[0].offsetTop + $dialogMP3desc.height() + 30) + 'px'
        });
        $('ul', $dialogPlaylist).css ({
            height : 'calc(100% - '+$('h2', $dialogPlaylist).outerHeight()+'px - 10px - '+$('h2', $dialogPlaylist).css('marginBottom')+' - '+$('h2', $dialogPlaylist).css('marginBottom')+')'
        });
        
		if (!na.mp.settings.afterInitializing) {
            na.mp.settings.afterInitializing = true;


            $('#mp3s .vividButton').css({position:'relative'});
            var w = ($('#mp3s').width()-25);
            na.m.waitForCondition (
                'na.site.settings.current.HTMLidle for resizing of playlist .vividButton elements (not in a .vividMenu btw)',
                function () { return na.m.HTMLidle(); },
                function () {
                    $('#mp3s .vividButton, #app__musicPlayer__player .vividButton4').each(function(idx,el) {
                        $(el).css({width:w});
                        if (!na.site.settings.buttons['#'+el.id])
                            na.site.settings.buttons['#'+el.id] = new naVividButton(el);
                    });
                    na.mp.setupDragNDrop();
                }, 150); // milliseconds delay number (milliseconds between check of 2nd parameter function call)


            setTimeout (function() {
                $('#horizontalMover').css({
                    opacity : 0.001,
                    display : 'block'
                }).animate ({opacity : (1)}, 700);
            }, 150);
        
			$('.vividDialog, .vividScrollpane, #heading_wrapper, #siteIntroText, #mp3s, #app__musicPlayer__player, #app__musicPlayer__player_table, #app__musicPlayer__playlist, #infoWindow_help')
				.not ('#siteLoginSuccessful, #siteLoginFailed, #siteLogin, #siteRegistration, #siteDateTime, #infoWindow_info, #infoWindow_tools')
				.animate ({opacity:1}, 'normal');
        }
	}
	
};
//}, 100); // na.m.waitForCondition() (see top of this file)

                na.m.waitForCondition ('DOM ready', function() {
                    return na && na.apps && typeof na.mediaPlayer =='object' && $.ui && $.ui.draggable && $('#mp3s')[0] && $('#app__musicPlayer__player')[0];
                }, function() {

                    jQuery('#horizontalMover').draggable ({
                        containment : '#horizontalMover__containmentBox1',
                        axis : 'x',
                        drag : function () {
                            na.musicPlayer.settings.masterLeftOffset = jQuery('#horizontalMover')[0].offsetLeft;
                            //debugger;
                            na.musicPlayer.onresize();
                        }
                    });
                    $('#app__musicPlayer__header .vividDialogContent').fadeIn('fast');
                    //na.desktop.setConfig('content');
                    na.desktop.g.divs.push ('#app__musicPlayer__header');
                    na.desktop.g.divs.push ('#mp3s');
                    na.desktop.g.divs.push ('#app__musicPlayer__player');
                    na.desktop.g.divs.push ('#app__musicPlayer__playlist');
                    na.desktop.g.divs.push ('#app__musicPlayer__description');
                    na.musicPlayer.settings.loadedIn['#siteContent'].onload( {} );
                    na.site.settings.current.loadingApps = false;
                    na.site.settings.current.startingApps = false;
                }, 100);
