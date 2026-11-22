delete na.mp;
na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.fancy.latest.2D'] = na.musicPlayer = na.mp = {
	about : {
		whatsThis : 'Complete application code for the music playback-and-download site on http://nicer.app/musicPlayer',
		copyright : 'Copyrighted (c) 2011-2025 by Rene AJM Veerman - rene.veerman.netherlands@gmail.com',
		license : 'https://opensource.org/license/mit',
		version : '5.0.1',
		firstReleased : '2011',   
		lastUpdated : '2025-04-18',
		knownBugs : []
	},
    globals : {
        url : '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.fancy.latest.2D/'
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
                    na.site.settings.app = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.fancy.latest.2D';
                    if (typeof na.site.globals.app === 'string') {
                        na.site.globals.app = JSON.parse(na.site.globals.app);
                    }

                    $('#siteContent > .vividDialogContent').css({ overflow : 'hidden' });
                    $('.audioSeekBar_setting').css({
                        position: 'revert-layer',
                        height : Math.max(0, $('.audioSeekBar').height() - 4),
                        top : 0,
                        left : 4,
                        borderRadius: 5
                    });
                    $('.audioVolumeBar_setting').css({
                        position: 'revert-layer',
                        height : Math.max(0, $('.audioVolumeBar').height() - 4),
                        top : 0,
                        left : 4,
                        borderRadius: 5
                    });
                    
                    $('#siteContent .vividDialog').each(function(idx, el) {
                        if (!na.site.c.dialogs['#' + el.id]) {
                            na.site.c.dialogs['#' + el.id] = new vividUserInterface_2D_dialog(el);
                        }
                    });
                    $('.audioPlayerUI .vividButton4').each(function(idx, el) {
                        if (!na.site.c.buttons['#' + el.id]) {
                            var btn = new vividUserInterface_2D_button({ naSite : na.site, el : el });
                            na.site.c.buttons['#' + el.id] = btn;
                            if (el.id === 'btnPlayPause') $(el).addClass('disabled');
                            if (el.id === 'btnMuteUnmute' && btn.el.onclick) btn.el.onclick({ currentTarget: el });
                        }
                    });

                    $('.lds-facebook').fadeOut('normal');
                    setTimeout (function() {
                        na.desktop.resize({callback:na.mp.onresize});
                    }, 1000);

                },
                ondestroy : function (settings) {
                    na.desktop.deleteCallback('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.fancy.latest.2D');
                    clearInterval(na.mp.settings.timeDisplayInterval);
                }
            }
        }
	},
	
	queueMP3 : function (id, file, label) {
		var pl = document.getElementById('playlist');
		if (!pl || !file) return false;

		var pc = na.mp.playlistCount++;
		var displayLabel = label || file;
		
		var npi = document.createElement('div');
		npi.setAttribute('file', file);
		npi.id = 'playlist_' + pc;
        npi.file = file;
		npi.className = 'mp3 vividButton';
        $(npi).attr('theme', 'dark');

        var link = document.createElement('a');
        link.href = '#';
        link.textContent = displayLabel;
        $(npi).on('click', function(e) {
            e.preventDefault();
            na.mp.selectMP3(npi.id, npi.getAttribute('file'));
        });
        npi.appendChild(link);
		
        var $span = $('<div id="textCheck" class="vividButton vividButton_text" style="width:' + ($('#mp3s').width() - 50) + 'px;opacity:0.0001"></div>').text(displayLabel);
        $('body').append($span);
        var h = $span.height() || 25;
        $span.remove();                
        $(npi).css({ height: h + 30 });    
            
		pl.appendChild(npi);
        na.mp.reorderPlaylist();
        na.mp.onresize();
        if (na.mp.settings.stopped) {
            na.mp.selectMP3(npi.id, file);
        }
	},
	
	selectMP3 : function (id, file) {
        if (na.mp.settings.ignoreClick) {
            na.mp.settings.ignoreClick = false;
            return false;
        }
        if (!file) return false;
        
        clearInterval(na.mp.settings.timeDisplayInterval);
        
        if (na.mp.settings.firstRun) na.mp.settings.firstRun = false;
		na.mp.settings.activeID = id;
        na.mp.settings.playingIndex = false;
        na.mp.settings.stopped = false;
        na.mp.settings.paused = false;

		var pl = $('#playlist')[0];
		if (pl) {
            for (var i = 0; i < pl.children.length; i++) {
                if (pl.children[i].id === id || (pl.children[i].children[0] && pl.children[i].children[0].id === id)) {
                    na.mp.settings.playingIndex = i;
                    break;
                }
            }
        }
        
        $('.mp3').removeClass('selected vividButtonSelected').addClass('vividButton');
        $('#' + id).addClass('selected vividButtonSelected').removeClass('vividButton');
        $('#btnPlayPause').addClass('selected');
        $('#line2').addClass('atPlay');
        if ($('#from_pause_to_play')[0] && $('#from_pause_to_play')[0].beginElement) {
            $('#from_pause_to_play')[0].beginElement();
        }

        var encodedFile = encodeURIComponent(file);
        var baseUrl = na.mp.globals.url;

        var ajaxCommand = {
			type : 'GET',
            url : /*baseUrl + 'music/' + */encodedFile + '.json',
            error: function() {
				var html = '';
                html += '<div style="height:15px;">&nbsp;</div>';
                html += '<table>';
				html += '<tr><td colspan="2" style="text-align:center"><a href="' + baseUrl + 'download_mp3.php?file=' + encodedFile + '">download</a></td></tr>';
				html += '<tr><td><span class="mp3_info_label mp3_title_label">title</span></td><td><span class="mp3_title">' + $('<div>').text(na.m.basename(file)).html() + '</span></td></tr>';
                html += '</table>';
                na.mp.updateDescriptionDiv(id, file, html);
            },
			success : function (json) {
				if (typeof json !== 'object') {
                    try {
                        json = JSON.parse(json);
                    } catch (e) {
                        json = {};
                    }
                }
                var html = '';
                if (json.description) {
                    html += '<div style="height:15px;">&nbsp;</div>';
                    html += '<div style="margin-bottom:10px;font-size:inherit;text-align:center;"><a href="' + baseUrl + 'download_mp3.php?file=' + encodedFile + '">download</a></div>';
                    html += '<div style="margin-bottom:10px;font-size:inherit;"><span class="mp3_info_label mp3_title_label hasOwnProps">title</span> <span class="mp3_title hasOwnProps">' + (json.title || file) + '</span></div>';
                    html += '<div style="margin-bottom:10px;font-size:inherit;"><span class="mp3_info_label mp3_album_label hasOwnProps">album</span> <span class="mp3_album hasOwnProps">' + (json.album || '') + '</span></div>';
                    html += '<div style="margin-bottom:10px;font-size:inherit;"><span class="mp3_info_label mp3_length_label hasOwnProps">length</span> <span class="mp3_length hasOwnProps">' + (json.length || '') + '</span></div>';
                    html += '<div style="margin-bottom:10px;font-size:inherit;"><span class="mp3_info_label mp3_year_label hasOwnProps">year</span> <span class="mp3_year hasOwnProps">' + (json.year || '') + '</span></div>';
                    html += '<div style="font-size:inherit;"><span class="mp3_description hasOwnProps">' + json.description + '</span></div>';
                } else {
                    html += '<table>';
                    html += '<tr><td colspan="2" style="text-align:center"><a href="' + baseUrl + 'download_mp3.php?file=' + encodedFile + '">download</a></td></tr>';
                    if (json.youtubeID) {
                        html += '<tr><td colspan="2" style="text-align:center"><a href="https://youtube.com/watch?v=' + encodeURIComponent(json.youtubeID) + '" target="_blank" rel="noopener">youtube link</a></td></tr>';
                    }
                    html += '</table>';
                }
                na.mp.updateDescriptionDiv(id, file, html);
			}
		};
		$.ajax(ajaxCommand);
	},

    updateDescriptionDiv : function (id, file, html) {
        $('#siteIntroText').fadeOut(500, function () {
            var audio = $('#audioTag')[0];
            file = file.replace('musicPlayer.fancy.latest.2D/','musicPlayer.fancy.latest.2D/music/');

            var mp3 = audio.src = file;///*na.mp.globals.url + 'music/' +*/ encodeURIComponent(file);
            audio.play().catch(function(err) {
                console.warn('Audio playback prevented or failed:', err);
            });
            $('.audioVolumeBarLabel').html('Volume : ' + Math.round(audio.volume * 100));
            na.mp.settings.stopped = false;
            na.mp.settings.paused = false;

            if ($('#btnPlayPause').is('.disabled')) {
                $('#btnPlayPause').removeClass('disabled');
                if (na.ui && na.ui.vividButton) {
                    na.ui.vividButton.onclick({ currentTarget: $('#btnPlayPause')[0] });
                }
            }
            
            $('#mp3descText').fadeOut('fast', function() {
                $('#mp3descText').html(html).delay(100).fadeIn(1000);
                $('#mp3descText a').each(function(idx, aEl) {
                    if (!aEl.el && na.vividText && na.cg) {
                        aEl.vividTextCmd = {
                            el : aEl,
                            theme : na.cg.themes.naColorgradientSchemeGreenVividText2,
                            animationType : na.vividText.globals.animationTypes,
                            animationSpeed : 4 * 1000
                        };
                        na.vividText.initElement(aEl.vividTextCmd);
                    }
                });
            });

            setTimeout(na.mp.setTimeDisplayInterval, 500);
        });
    },
    
    setTimeDisplayInterval : function () {
        clearInterval(na.mp.settings.timeDisplayInterval);
        na.mp.settings.timeDisplayInterval = setInterval(function() {
            var audio = $('#audioTag')[0];
            if (!audio) return;

            var length = audio.duration;
            var currentTime = audio.currentTime;

            if (!isFinite(length) || length <= 0) {
                $('.audioSeekBarLabel_length').html('00:00:00');
                $('.audioSeekBarLabel_currentTime').html(na.mp.convertSecondsToTimeString(currentTime || 0));
                return;
            }
            
            if (audio.ended || (length - currentTime) <= 0.25) {
                clearInterval(na.mp.settings.timeDisplayInterval);
                na.mp.next();
            } else {
                $('.audioSeekBarLabel_length').html(na.mp.convertSecondsToTimeString(length));
                $('.audioSeekBarLabel_currentTime').html(na.mp.convertSecondsToTimeString(currentTime));
                
                var widthSeekBar = $('.audioSeekBar').width();
                var widthPlayBar = Math.floor((widthSeekBar * currentTime) / length);
                
                if (!na.mp.settings.maxPlayBarWidth) na.mp.settings.maxPlayBarWidth = widthSeekBar;
                $('.audioSeekBar_setting')[0].style.width = Math.min(widthPlayBar, widthSeekBar) + 'px';
            }
        }, 500);
    },
    
    convertSecondsToTimeString : function (seconds) {
        if (!isFinite(seconds) || seconds < 0) seconds = 0;
        var hours = Math.floor(seconds / 3600);
        var minutes = Math.floor((seconds - (hours * 3600)) / 60);
        var secs = Math.floor(seconds - (hours * 3600) - (minutes * 60));
        
        if (hours < 10) hours = '0' + hours;
        if (minutes < 10) minutes = '0' + minutes;
        if (secs < 10) secs = '0' + secs;
        
        return hours + ':' + minutes + ':' + secs;
    },
	
	playpause : function () {
        var audio = $('#audioTag')[0];
        if (!audio || $('#btnPlayPause').is('.disabled')) return;

		if (na.mp.settings.stopped || na.mp.settings.paused) {
			audio.play();
			na.mp.settings.paused = false;
			na.mp.settings.stopped = false;
            $('#btnPlayPause').addClass('selected');
            if ($('#from_pause_to_play')[0] && $('#from_pause_to_play')[0].beginElement) {
                $('#from_pause_to_play')[0].beginElement();
            }
		} else {
			audio.pause();
			na.mp.settings.paused = true;
			na.mp.settings.stopped = false;
            $('#btnPlayPause').removeClass('selected');
            if ($('#from_play_to_pause')[0] && $('#from_play_to_pause')[0].beginElement) {
                $('#from_play_to_pause')[0].beginElement();
            }
		}
	},
	
	stop : function () {
        var audio = $('#audioTag')[0];
        if (na.ui && na.ui.vividButton) {
            na.ui.vividButton.onclick({ currentTarget: $('#btnPlayPause')[0] });
        }
        $('#btnPlayPause').addClass('disabled').removeClass('selected');
        if (audio) {
            audio.pause();
            audio.currentTime = 0;
        }
		na.mp.settings.stopped = true;
        na.mp.settings.paused = false;
        clearInterval(na.mp.settings.timeDisplayInterval);

        $('.audioSeekBar_setting').css({ width: 0 });
        $('.audioSeekBarLabel_currentTime').html('00:00:00');

		$('#mp3descText').fadeOut(1000);
		setTimeout(function () {
			$('#siteIntroText').fadeIn(1000);
		}, 1010);
	},
    
    next : function () {
        var pl = $('#playlist')[0];
        if (!pl || pl.children.length === 0 || na.mp.settings.playingIndex === false) {
            return false;
        }

        var items = Array.from(pl.children).filter(function(el) {
            return $(el).hasClass('mp3');
        });
        if (items.length === 0) return false;

        if (na.mp.settings.shuffle) {
            var unplayed = items.filter(function(el, idx) {
                return !el.shufflePlayed && idx !== na.mp.settings.playingIndex;
            });

            if (unplayed.length === 0) {
                if (!na.mp.settings.repeating) {
                    na.mp.stop();
                    return false;
                }
                items.forEach(function(el) { el.shufflePlayed = false; });
                unplayed = items.filter(function(el, idx) {
                    return items.length === 1 || idx !== na.mp.settings.playingIndex;
                });
            }

            var nextEl = unplayed[Math.floor(Math.random() * unplayed.length)];
            nextEl.shufflePlayed = true;
            var file = $(nextEl).attr('file') || $(nextEl.children[0]).attr('file');
            na.mp.selectMP3(nextEl.id, file);
            return true;
        } else {
            var nextIdx = na.mp.settings.playingIndex + 1;
            if (nextIdx >= items.length) {
                if (na.mp.settings.repeating) {
                    nextIdx = 0;
                } else {
                    na.mp.stop();
                    return false;
                }
            }
            var targetEl = items[nextIdx];
            var targetFile = $(targetEl).attr('file') || $(targetEl.children[0]).attr('file');
            na.mp.selectMP3(targetEl.id, targetFile);
            return true;
        }
    },
	
	mute : function () {
        var audio = $('#audioTag')[0];
        if (!audio) return;
		na.mp.settings.muted = !na.mp.settings.muted;
		audio.muted = na.mp.settings.muted;
	},

	toggleShuffle : function () {
		na.mp.settings.shuffle = !na.mp.settings.shuffle;
	},

	toggleRepeat : function () {
		na.mp.settings.repeating = !na.mp.settings.repeating;
	},
    
    seek : function (evt) {
        var audio = $('#audioTag')[0];
        if (!audio || !isFinite(audio.duration) || audio.duration <= 0) return;

        var length = audio.duration;
        var widthSeekBar = $('.audioSeekBar').width();
        var widthPlayBar = Math.max(0, Math.min(evt.offsetX, widthSeekBar));
        var newCurrentTime = (widthPlayBar * length) / widthSeekBar;

        $('.audioSeekBarLabel_length').html(na.mp.convertSecondsToTimeString(length));
        $('.audioSeekBarLabel_currentTime').html(na.mp.convertSecondsToTimeString(newCurrentTime));
        $('.audioSeekBar_setting')[0].style.width = widthPlayBar + 'px';
        audio.currentTime = newCurrentTime;
    },
    
    setVolume : function (evt) {
        var audio = $('#audioTag')[0];
        if (!audio) return;
        var widthVolumeBar = $('.audioVolumeBar').width();
        var clampedX = Math.max(0, Math.min(evt.offsetX, widthVolumeBar));
        audio.volume = clampedX / widthVolumeBar;
        $('.audioVolumeBarLabel').html('Volume : ' + Math.round(audio.volume * 100));
        $('.audioVolumeBar_setting').css({ width : clampedX });
    },
    
    mp3drag : {
        containment : 'window',
        connectToSortable : '#playlist',
        refreshPositions : true,
        helper : function (evt, ui) {
            if (this.id.indexOf('playlist_') !== -1) return false;
            if (this.id === na.mp.settings.resortedItem) return false;

            var div = document.createElement('div');
            var div2 = document.createElement('div');
            var scale = $('#siteContent > .vividDialogContent').css('scale') || '1';
            var scaleX = 1, scaleY = 1;

            if (scale && scale !== 'none') {
                if (scale.indexOf(' ') !== -1) {
                    var sp = scale.split(' ');
                    scaleX = parseFloat(sp[0]) || 1;
                    scaleY = parseFloat(sp[1]) || 1;
                } else {
                    scaleX = scaleY = parseFloat(scale) || 1;
                }
            }

            $(this).add(div).add(div2).css({ position : 'absolute', zIndex : 110000 });
            $(document.body).append(div);
            $(div).append(div2);
            $(div2).css({
                top : $(evt.currentTarget).offset().top,
                left : $(evt.currentTarget).offset().left
            });

            na.mp.globals.mp3evt = $.extend({}, evt);
            na.mp.globals.mp3div2 = div2;
            na.mp.globals.mp3sItem = this;
            na.mp.globals.mp3scale = scale;
            na.mp.globals.mp3scaleX = scaleX;
            na.mp.globals.mp3scaleY = scaleY;

            var nt = $(this).clone(true, true).appendTo(na.mp.globals.mp3div2)[0];
            $(nt).css({
                position : 'relative',
                zIndex : 110000,
                marginLeft : ($('#playlist').width() - $(this).width()) / 2,
                width : $(this).width(),
                height : $(this).height(),
                scale: scale !== 'none' ? scale : 1,
                display : 'block'
            });
            nt.id = nt.id.replace('mp3', 'dragging');
            na.mp.globals.mp3dragged = nt;
            return nt;
        }
    },
    
    mp3sortable : {
		revert : true,
		start : function (evt, ui) {
            na.mp.settings.ignoreClick = (evt.detail === 1);
		},
		stop : function (evt, ui) {
            setTimeout(na.mp.reorderPlaylist, 100);
		},
        update : function (evt, ui) {
            na.mp.reorderPlaylist();
        }
    },
    
	setupDragNDrop : function () {
        $('#horizontalMover').draggable({
            axis: 'x',
            containment: '#horizontalMover__containmentBox1',
            drag: function(event, ui) {
                $('#horizontalMover').css({left:ui.position.left});
                na.mp.onresize();
            }
        });

        var mp3s = $('#mp3s > .mp3');
		mp3s.draggable(na.mp.mp3drag);
		$('#playlist').sortable(na.mp.mp3sortable);
		$('#playlist').droppable({
            tolerance : 'pointer',
            activate : function (evt, ui) {
                na.mp.globals.mp3dragged = ui.helper;
            },
			drop : function (evt, ui) {
                if (!ui.helper || !ui.helper[0]) return false;
                var oldID = ui.helper[0].id;
                if (oldID.indexOf('playlist_') !== -1) return false;
                                  
                var pl = $('#playlist');
                var pc = na.mp.playlistCount++;
                var newID = 'playlist_' + pc;
                var original = $(na.mp.globals.mp3sItem || '#' + oldID.replace('dragging', 'mp3'));
                var file = original.attr('file');
                var dragged = $(ui.helper).clone(false).detach().appendTo(pl)[0];

                $(dragged).attr('id', newID);
                dragged.evt = evt;
                dragged.file = file;
                dragged.oldID = oldID;

                var x = na.mp.globals.mp3sItem;
                $(dragged).css({
                    position: 'relative',
                    background : '',
                    color : '',
                    marginLeft : 0,
                    left : na.d.g.margin / 2,
                    display : 'block',
                    top : 'auto',
                    width : $(x).width(),
                    height : $(x).height(),
                    verticalAlign : 'middle',
                    scale : (na.mp.globals.zfw && na.mp.globals.zfh) ? (na.mp.globals.zfw + ' ' + na.mp.globals.zfh) : '1'
                });
                $(dragged).attr('class', 'mp3 vividButton');
                $(dragged).attr('file', file);
                $(dragged).removeAttr('onclick');
                $(dragged).find('a').removeAttr('href').removeAttr('onclick');

                $(dragged).on('click', function(e) {
                    e.preventDefault();
                    na.mp.selectMP3(this.id, $(this).attr('file'));
                });

                $(ui.helper).remove();
                na.mp.reorderPlaylist();
                
                if (na.mp.settings.stopped) {
                    na.mp.selectMP3(newID, file);
                }
                                
                na.mp.onresize($('#siteContent')[0]);
                evt.preventDefault();

                if (na.site.loadTheme_applySettings && na.site.globals.themes) {
                    na.site.loadTheme_applySettings(na.site.globals.themes[na.site.globals.themeName], null, false);
                }

                return dragged;
			}
		});
	},
    
    reorderPlaylist : function () {
        $('.ui-sortable-placeholder', $('#playlist')).removeClass('mp3').addClass('mp3-disabled');
        $('.mp3', $('#playlist')).each(function(idx, el) {
            var newId = 'playlist_' + idx;
            el.id = newId;
            if (el.classList.contains('selected')) {
                na.mp.settings.activeID = newId;
                na.mp.settings.playingIndex = idx;
            }
        });
    },
    
	playlistCount : 0,

	onresize : function (div) {
		var contentMargin = 20;
		var myWidth = $('#siteContent > .vividDialogContent').width() - (contentMargin * 2);
		var myHeight = (
            $('#siteContent').height()
            - $('#horizontalMover__containmentBox2').height()
            - ($('#horizontalMover__containmentBox2')[0] ? $('#horizontalMover__containmentBox2')[0].offsetTop : 0)
            - (contentMargin * 2) - (na.d.g.margin * 2) - $('#app__musicPlayer__header').height()
        );

        if ($('#siteTaskbar').css('display') !== 'none') {
            myHeight = myHeight - contentMargin;
        }
        var columnWidth = ($(window).width() / 2) - 30;
        if (columnWidth > 450) columnWidth = 450;

        var contentWidth = columnWidth * 2 + 10;
        var contentInnerWidth = columnWidth * 2 + 20;
        var zfh = $('#siteContent').height() / Math.max(1, ($('#siteContent').height() - (contentMargin * 2)));
        var zfw = $('#siteContent').width() / ((contentWidth * 2) + 30 + (na.d.g.margin * 3));

        na.mp.globals.zfh = zfh;
        na.mp.globals.zfw = zfw;

        if (!na.site.settings.scaleApp) {
            na.site.settings.scaleApp = zfw + ' ' + zfh;
        }

        var masterLeftOffset;
			masterLeftOffset = $('#horizontalMover').offset().left - $('#siteContent').offset().left;//Math.max(0, ((myWidth - contentWidth) / 2) - ($('#horizontalMover').width()/2));
			na.mp.settings.masterLeftOffset = masterLeftOffset;
		
		var dialogMP3sList = '#mp3s';
		var dialogMP3desc = '#app__musicPlayer__description';
		var dialogPlaylist = '#app__musicPlayer__playlist';
		var dialogPlayer = '#app__musicPlayer__player, #app__musicPlayer__player__CSS3';
            
        var leftOffset = masterLeftOffset;
        var playerLeft = leftOffset + columnWidth + na.d.g.margin;
		var dialogsLeft = Math.round(leftOffset);

        $('#app__musicPlayer__header').css({
            width : contentInnerWidth,
            left : dialogsLeft,
            top : (na.d.g.margin * 2)
        });
        $('#app__musicPlayer__header .vividDialogContent').css({
            overflow : 'hidden'
        });
        
		var $dialogMP3desc = $(dialogMP3desc);
		var $dialogPlaylist = $(dialogPlaylist);
		var dialogsTop = contentMargin + (na.d.g.margin * 2) + $('#app__musicPlayer__header').height() + na.d.g.margin;
		var playerHeight = 150;
        var descTop = dialogsTop + playerHeight + 30;
        var descHeight = Math.max(80, (myHeight - 20 - playerHeight) / 3);
        var playlistTop = descTop + descHeight + 30;
        var playlistHeight = Math.max(100, (myHeight - 170 - $('#app__musicPlayer__header').height()) / (3 / 2));
        
		$('#horizontalMover__containmentBox2').css({
			left : contentMargin,
            top : 0,
			width : myWidth,
			opacity : 0.001,
			display : 'block'
		}).animate({ opacity: 0.3 }, 1000);

		$('#horizontalMover__containmentBox1').css({
			left : contentMargin,
            top : 0,
			width : myWidth,
			opacity : 0.001,
			display : 'block'
		}).animate({ opacity: 0.2 }, 1300);
		
        $('#app__musicPlayer__player').css({ opacity : 1, height : playerHeight });
        $('#app__musicPlayer__player, #app__musicPlayer__player__CSS3').css({
            left : playerLeft,
            width : columnWidth,
            top : dialogsTop,
            opacity : 1
        });
        $('#app__musicPlayer__player__CSS3').css({ left : '', top : '', opacity : 0.5 });
        
        $('#app__musicPlayer__description, #app__musicPlayer__description__CSS3, #app__musicPlayer__description__item__0, #app__musicPlayer__description__item__0__img1, #app__musicPlayer__desc__item__0__img2').css({
            position : 'absolute'
        }).stop(true, true).animate({
            width : columnWidth,
            height : descHeight
        });
        $('#app__musicPlayer__description').stop(true, true).animate({
            left : playerLeft,
            top : descTop,
            width : columnWidth,
            height : descHeight
        });
        $('#app__musicPlayer__description > table').css({
            width : ''
        }).stop(true, true).animate({
            height : descHeight
        });

        $('#horizontalMover').css({
            background : 'rgba(243, 245, 39, 0.55)',
            boxShadow : '2px 2px 2px rgba(0,0,0,0.7)',
            left : masterLeftOffset + 10,
            width : contentInnerWidth - 20,
            top : 2
        });

        $('#mp3s').css({
            width : columnWidth - 50,
            visibility : 'visible',
            position : 'absolute'
        }).stop(true, true).animate({
            left : dialogsLeft,
            height : myHeight,
            top : dialogsTop
        });

        $dialogPlaylist.stop(true, true).animate({
            left : playerLeft,
            width : columnWidth,
            height : playlistHeight,
            top : playlistTop + 'px'
        });

        var h2Height = $('h2', $dialogPlaylist).outerHeight() || 0;
        var h2Margin = parseFloat($('h2', $dialogPlaylist).css('marginBottom')) || 0;
        $('ul', $dialogPlaylist).stop(true, true).animate({
            height : 'calc(100% - ' + (h2Height + 10 + (h2Margin * 2)) + 'px)'
        });
        
		if (!na.mp.settings.afterInitializing) {
            na.mp.settings.afterInitializing = true;

            $('#mp3s .vividButton').css({ position: 'relative' });
            var w = $('#mp3s').width() - 50;
            na.m.waitForCondition(
                'na.site.settings.HTMLidle for resizing of playlist .vividButton elements',
                function () { return na.m.HTMLidle(); },
                function () {
                    var c = na.site.components;
                    $('.vividButton, .vividButton4, .vividButton_icon_50x50, .vividButton_icon_100x100', $('#mp3s')).each(function(idx, el) {
                        $(el).stop(true, true).css({ width: w });
                        c.buttons['#' + el.id] = new vividUserInterface_2D_button({ naSite : na.site, el : el });
                    });

                    na.mp.setupDragNDrop();
                }, 150
            );

            setTimeout(function() {
                $('#horizontalMover').css({
                    opacity : 0.001,
                    display : 'block'
                }).animate({ opacity : 1 }, 700);
            }, 150);
        
			$('.vividDialog, .vividScrollpane, #heading_wrapper, #siteIntroText, #mp3s, #app__musicPlayer__player, #app__musicPlayer__player_table, #app__musicPlayer__playlist, #infoWindow_help')
				.not('#siteLoginSuccessful, #siteLoginFailed, #siteLogin, #siteRegistration, #siteDateTime, #infoWindow_info, #infoWindow_tools')
				.animate({ opacity: 1 }, 'normal');
        }
        na.site.settings.running_loadContent = false;
    }
};

na.m.waitForCondition('DOM ready', function() {
    return na && na.desktop && na.desktop.g && na.apps && typeof na.musicPlayer === 'object' && $('#mp3s')[0] && $('#app__musicPlayer__player')[0];
}, function() {

    /*
    // in app.musicPlayer_siteContent.source.js, inside the 'DOM ready' callback
    (function () {
        const mp = na.mp;
        mp.settings.dialogResizing = false;
        mp.settings.playlistUser = null;               // {width, height, top, leftDelta}

        const $pl = () => $('#app__musicPlayer__playlist');
        const cssNum = (sel, p) => parseFloat($(sel).css(p)) || 0;

        // a resize drag started on one of the playlist's own handles?
        document.addEventListener('pointerdown', e => {
            const h = e.target.closest && e.target.closest('.na-resize-handle');
            if (h && h.parentElement && h.parentElement.id === 'app__musicPlayer__playlist')
                mp.settings.dialogResizing = true;
        }, true);

        // after the drag: store the result, relative to the player column
        document.addEventListener('pointerup', () => {
            if (!mp.settings.dialogResizing) return;
            setTimeout(() => {                           // let desktop.js finish its own pointerup first
                mp.settings.dialogResizing = false;
                const p = $pl();
                mp.settings.playlistUser = {
                    width:     p.width(),
                       height:    p.height(),
                       top:       cssNum('#app__musicPlayer__playlist', 'top'),
                       leftDelta: cssNum('#app__musicPlayer__playlist', 'left')
                       - cssNum('#app__musicPlayer__player', 'left')
                };
            }, 0);
        }, true);

        const orig = mp.onresize;
        mp.onresize = function () {
            if (mp.settings.dialogResizing) return;      // don't fight the live drag
            const r = orig.apply(this, arguments);

            if (!na.mp.settings.playlistUser) {
                $dialogPlaylist.css({ left: playerLeft, width: columnWidth});
            } else {
                var u = na.mp.settings.playlistUser;
                $dialogPlaylist.css({ left: playerLeft + u.leftDelta, width: u.width, height: u.height, top: u.top });
            }


            return r;
        };
    })();*/

    $('#app__musicPlayer__header .vividDialogContent').fadeIn('fast');

    var divsToRegister = [
        '#app__musicPlayer__header',
        '#mp3s',
        '#app__musicPlayer__player',
        '#app__musicPlayer__playlist',
        '#app__musicPlayer__description'
    ];

    divsToRegister.forEach(function(selector) {
        if (!na.desktop.g.originalDivs.includes(selector)) na.desktop.g.originalDivs.push(selector);
        if (!na.desktop.g.visibleDivs.includes(selector)) na.desktop.g.visibleDivs.push(selector);
    });

    na.musicPlayer.settings.loadedIn['#siteContent'].onload({});
    na.site.settings.loadingApps = false;
    na.site.settings.startingApps = false;
}, 100);

na.mp.settings.dialogResizing = false;
/*
document.addEventListener('pointerdown', e => {
    if (e.target.closest && e.target.closest('.na-resize-handle'))
        na.mp.settings.dialogResizing = true;
}, true);

document.addEventListener('pointerup', () => {
    na.mp.settings.dialogResizing = false;
}, true);

*/
