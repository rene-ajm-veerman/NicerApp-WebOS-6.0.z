if (typeof na!=='object') { var NicerApp_WebOS = nicerapp = na = {}; }
na.backgrounds = na.background = na.bg = {
    globals : { fadingSpeed : 1000 },
    settings : {
        useFading : true,
        fadingMaxTime : 10*1000
    },

    initialize (settings) {
        var t = this;
        t.settings = $.extend (na.bg.settings, settings);

        var
        url = '/domainConfig/ajax_backgrounds.php',
        ac = {
            type : 'GET',
            url : url,
            success : function (data, ts, xhr) {
                try {
                    if (typeof data=='string') {
                        data = data.replace(/\</g,'');

                        t.data = JSON.parse(data);
                    } else
                        t.data = data;
                    /*
                    t.next ('#siteBackground', null, null, false, function() {
                        var
                        url2 = '/domainConfig/ajax_backgrounds_recursive.php',
                        ac2 = {
                            type : 'GET',
                            url : url2,
                            success : function (data, ts, xhr) {
                                try {
                                    t.recursive = JSON.parse(data);
                                } catch (err) {
                                    t.recursive = false;
                                }
                            },
                            error : function (xhr, textStatus, errorThrown) {

                            }
                        };
                        $.ajax(ac2);
                    });
                    */
                    na.m.log (200, 'na.backgrounds.loadBackgrounds FINISHED.');

                } catch (err) {
                    t.data = false;
                    t.recursive = false;
                }

            },
            error : function (xhr, textStatus, errorThrown) {
            }
        };
        na.m.log (200, 'na.backgrounds.loadBackgrounds STARTS.')
        $.ajax(ac);

        var
        url = '/domainConfig/ajax_backgroundsMetaInfo.php',
        ac = {
            type : 'GET',
            url : url,
            success : async function (data, ts, xhr) {
                try {
                    if (typeof data=='string') {
                        data = data.replace(/\</g,'');

                        t.metaInfo = JSON.parse(data);
                    } else
                        t.metaInfo = data;

                    na.m.log (200, 'na.backgrounds.loadMetaInfo FINISHED.')

                } catch (err) {
                    t.metaInfo = false;
                }
            },
            error : function (xhr, textStatus, errorThrown) {
            }
        };
        na.m.log (200, 'na.backgrounds.loadMetaInfo STARTS.')
        $.ajax(ac);

        return this;
    },

    // below here was all written with the free help of grok.com
    showBrowser : function () {
        $('#siteBackgrounds_content').html(`
        <div id="siteBackgrounds_leftPanel" class="siteBackgrounds_panel vividScrollpane"></div>
        <div id="siteBackgrounds_rightPanel" class="siteBackgrounds_panel vividScrollpane"></div>
        `);
        $('#siteBackgrounds').css({
            position : 'absolute',
            top : '4%',
            left : '4%',
            width : '92%',
            height : 'calc(96% - 100px)'
        }).fadeIn('normal');


        // Wait for metaInfo if not ready yet
        na.m.waitForCondition('metaInfo ready', function() {
            return na.background.metaInfo && Object.keys(na.background.metaInfo).length > 0;
        }, function() {
            const totalKeywords = na.background.buildBackgroundsDialogContent(na.background);

            na.background.renderKeywordsIncrementally(na.background, 50);
        }, 100, 50); // check every 100ms, timeout after 5s
    },

    processMetaInfoAsync : function (metaInfo, onComplete) {
        // Split the for..in loop into batches using requestIdleCallback or setTimeout
        let keys = Object.keys(metaInfo);
        let idx = 0;
        var t = this;
        const batch = 200; // tune

        const re = /^[\d\.]+.*/;

        t.keywords = {};
        t.sortedKeywords = [];
        t.tokenIndex = {};           // token → {count, files: Set or Array}

        const seen = new Set(), seenRFP = new Set(); // faster deduplication than .includes() on array

        const banned = function (rfp) {
            return (
                // i deleted these folders from my siteMedia/backgrounds folder by now, for reaons of lameness. :p
                rfp.match(/Australia/i)
                || rfp.match('fantasy')
                || rfp.match('flowers')
            )
        }

        // Helper to normalize tokens (optional but recommended)
        function normalizeToken(str) {
            return str
            .trim()
            .toLowerCase()
            .replace(/[^a-z0-9\- ]/g, '')     // remove punctuation except - and space
            .replace(/\s+/g, ' ');            // normalize spaces
        }

        function processBatch() {
            const end = Math.min(idx + batch, keys.length);
            for (let i = idx; i < end; i++) {
                // your existing rfp processing logic here
                let rfp = keys[idx];

                if (banned(rfp)) continue;
                const r = t.metaInfo[rfp];
                for (const providerName in r) {
                    const pr = r[providerName];

                    if (
                        typeof pr.keywords !== 'string' ||
                        pr.keywords === '' ||
                        pr.keywords.startsWith('[')
                    ) continue;

                    const lines = pr.keywords.split('\n');
                    for (const line of lines) {
                        if (!line.trim()) continue;

                        const parts = line.split(':');
                        const kwPart = parts[parts.length - 1];
                        const kws = kwPart.split(',');

                        for (let kw of kws) {
                            kw = kw
                            .replace(/[\[\]"'#*+-]/g, '')
                            .replace('10 - 20 precise key words for this ', '')
                            .replace('10 - 20 precise keywords for this ', '')
                            .replace('10 precise tags for this image include:', '')
                            .replace('10 - 20 precise tags for this ', '')
                            .trim();

                            if (!kw || typeof kw === 'number' || kw.match(re)) continue;
                            if (!kw || kw === '' || typeof kw === 'number') continue;

                            if (!t.keywords[kw]) {
                                t.keywords[kw] = { f: [] };
                            }
                            if (!seenRFP.has(rfp)) {
                                seenRFP.add(rfp);
                                t.keywords[kw].f.push(rfp);
                            }

                            if (!seen.has(kw)) {
                                seen.add(kw);
                                t.sortedKeywords.push(kw);
                            }

                            // Split into tokens
                            const tokens = kw.split(' ');

                            // For each meaningful token
                            tokens.forEach(tokenRaw => {
                                const token = normalizeToken(tokenRaw);
                                if (token.length < 3) return;               // skip very short junk
                                if (/^\d+$/.test(token)) return;            // skip pure numbers
                                if (
                                    token=='in' || token=='with' || token=='for'
                                    || token=='and' || token=='the'
                                ) return;

                                if (!t.tokenIndex[token]) {
                                    t.tokenIndex[token] = {
                                        count: 0,
                                        files: new Set(),           // prevents duplicate rfp per token
                                           sources: new Set()      // ← uncomment if you want original phrases
                                    };
                                }

                                const entry = t.tokenIndex[token];

                                entry.count++;
                                entry.files.add(rfp);

                                // Optional: remember which full keyword phrase led to this token
                                entry.sources.add(kw);
                            });
                        }
                    }
                }
            }
            idx = end;

            if (idx < keys.length) {
                requestIdleCallback(processBatch); // or setTimeout(processBatch, 0)
            } else {
                // finish sorting + tokenIndex
                // ────────────────────────────────────────────────
                // After all loops have finished – prepare sorted list for rendering
                t.sortedTokens = Object.keys(t.tokenIndex).sort((a, b) => {
                    // Option 1: sort by frequency descending (most useful first)
                    const diff = t.tokenIndex[b].count - t.tokenIndex[a].count;
                    if (diff !== 0) return diff;

                    // Option 2: frequency tie → alphabetical
                    return a.localeCompare(b);
                });

                // Alternative sorts you might want to try:
                // → alphabetical only:   return a.localeCompare(b);
                // → longer tokens last:  return b.length - a.length;
                // → short & frequent first: custom comparator

                return t.sortedKeywords.length; // total count, for progress bar max
                onComplete();
            }
        }
        requestIdleCallback(processBatch);
    },

    buildBackgroundsDialogContent : function (t) {
        const re = /^[\d\.]+.*/;

        t.keywords = {};
        t.sortedKeywords = [];
        t.tokenIndex = {};           // token → {count, files: Set or Array}

        const seen = new Set(), seenRFP = new Set(); // faster deduplication than .includes() on array

        const banned = function (rfp) {
            return (
                // i deleted these folders from my siteMedia/backgrounds folder by now, for reaons of lameness. :p
                rfp.match(/Australia/i)
                || rfp.match('fantasy')
                || rfp.match('flowers')
            )
        }

        for (const rfp in t.metaInfo) {
            if (banned(rfp)) continue;
            const r = t.metaInfo[rfp];
            for (const providerName in r) {
                const pr = r[providerName];

                if (
                    typeof pr.keywords !== 'string' ||
                    pr.keywords === '' ||
                    pr.keywords.startsWith('[')
                ) continue;

                const lines = pr.keywords.split('\n');
                for (const line of lines) {
                    if (!line.trim()) continue;

                    const parts = line.split(':');
                    const kwPart = parts[parts.length - 1];
                    const kws = kwPart.split(',');

                    for (let kw of kws) {
                        kw = kw
                            .replace(/[\[\]"'#*+-]/g, '')
                            .replace('10 - 20 precise key words for this ', '')
                            .replace('10 - 20 precise keywords for this ', '')
                            .replace('10 precise tags for this image include:', '')
                            .replace('10 - 20 precise tags for this ', '')
                            .trim();

                        if (!kw || typeof kw === 'number' || kw.match(re)) continue;
                        if (!kw || kw === '' || typeof kw === 'number') continue;

                        if (!t.keywords[kw]) {
                            t.keywords[kw] = { f: [] };
                        }
                        if (!seenRFP.has(rfp)) {
                            seenRFP.add(rfp);
                            t.keywords[kw].f.push(rfp);
                        }

                        if (!seen.has(kw)) {
                            seen.add(kw);
                            t.sortedKeywords.push(kw);
                        }

                        // Split into tokens
                        const tokens = kw.split(' ');

                        // For each meaningful token
                        tokens.forEach(tokenRaw => {
                            const token = normalizeToken(tokenRaw);
                            if (token.length < 3) return;               // skip very short junk
                            if (/^\d+$/.test(token)) return;            // skip pure numbers
                            if (
                                token=='in' || token=='with' || token=='for'
                                || token=='and' || token=='the'
                            ) return;

                            if (!t.tokenIndex[token]) {
                                t.tokenIndex[token] = {
                                    count: 0,
                                    files: new Set(),           // prevents duplicate rfp per token
                                    sources: new Set()      // ← uncomment if you want original phrases
                                };
                            }

                            const entry = t.tokenIndex[token];

                            entry.count++;
                            entry.files.add(rfp);

                            // Optional: remember which full keyword phrase led to this token
                            entry.sources.add(kw);
                        });

                    }


                }
            }
        }

        // Helper to normalize tokens (optional but recommended)
        function normalizeToken(str) {
            return str
                .trim()
                .toLowerCase()
                .replace(/[^a-z0-9\- ]/g, '')     // remove punctuation except - and space
                .replace(/\s+/g, ' ');            // normalize spaces
        }



        // ────────────────────────────────────────────────
        // After all loops have finished – prepare sorted list for rendering
        t.sortedTokens = Object.keys(t.tokenIndex).sort((a, b) => {
            // Option 1: sort by frequency descending (most useful first)
            const diff = t.tokenIndex[b].count - t.tokenIndex[a].count;
            if (diff !== 0) return diff;

            // Option 2: frequency tie → alphabetical
            return a.localeCompare(b);
        });

        // Alternative sorts you might want to try:
        // → alphabetical only:   return a.localeCompare(b);
        // → longer tokens last:  return b.length - a.length;
        // → short & frequent first: custom comparator

        return t.sortedKeywords.length; // total count, for progress bar max
    },


    showImagesForToken : function (token) {
        const panel = $('#siteBackgrounds_rightPanel').empty();
        const files = [...na.background.tokenIndex[token].files]; // copy array

        // Optional: sort files somehow (name, date, random, ...)

        const fragment = document.createDocumentFragment();

        files.forEach(rfp => {
            if (rfp.indexOf('/')!==-1) {
                var fn = rfp.match(/\/(.*?)$/)[1];
                var f = rfp.replace(fn,'');
                var rfp2 = f+'thumbs/300/'+fn;
                var rfp2 = f+'/'+fn;
            } else {
                var rfp2 = 'thumbs/300/'+rfp;
                var rfp2 = '/'+rfp;
            }
            const img = document.createElement('img');
            img.src = '/siteMedia/backgrounds/Landscape/' + rfp2; // adjust path
            img.loading = 'lazy';
            img.decoding = 'async';
            img.onclick = () => {
                na.backgrounds.next("#siteBackground", null, '/siteMedia/backgrounds/Landscape/' + rfp);
                $('#siteBackgrounds').fadeOut('normal');
                $('#btnSiteBackground')[0].vividUserInterface_2D_button.deselect();
                $('#changeBackgroundsAutomatically')[0].checked = false;
                na.site.saveTheme();
            };
            img.alt = rfp;
            img.style.cssText = 'width:23%; cursor: pointer; height:auto; display:inline-block; margin:0.4em;';
            img.className = 'keywordImage';
            fragment.appendChild(img);
        });

        panel.append(fragment);
    },

    renderKeywordsIncrementally : function (t, batchSize = 100) {
        const container = document.getElementById('siteBackgrounds_leftPanel');
        if (!container) return;

        // Remove old progress bar container if exists
        document.getElementById('keywordProgressContainer')?.remove();

        const total = t.sortedTokens.length;
        let i = 0;

        const progress = document.createElement('progress');
        progress.id = 'keywordProgress';
        progress.max = total;
        progress.value = 0;
        progress.style.cssText = 'width:100%; height:18px; margin:8px 0;';

        const text = document.createElement('div');
        text.id = 'keywordProgressText';
        text.textContent = `0 / ${total} relevant tags loaded`;
        text.style.padding = '8px';

        const progContainer = document.createElement('div');
        progContainer.id = 'keywordProgressContainer';
        progContainer.style.padding = '12px';
        progContainer.append(text, progress);

        container.appendChild(progContainer);

        function appendBatch() {
            const fragment = document.createDocumentFragment();
            const end = Math.min(i + batchSize, total);

            while (i < end) {
                const token = t.sortedTokens[i];
                const data = t.tokenIndex[token];
                if (data.count < 3) { i++; continue; } // ← hide rare tokens (tune threshold)

                const span = document.createElement('span');
                span.className = 'kw token';
                span.textContent = `${token} (${data.count}) `;
                span.title = `${data.files.size} images`;
                span.onclick = () => {
                    // Highlight + show images
                    document.querySelectorAll('.kw.token').forEach(el => el.classList.remove('kwSel'));
                    span.classList.add('kwSel');
                    na.background.showImagesForToken(token); // you'll write this next
                };

                fragment.appendChild(span);
                i++;
            }

            container.appendChild(fragment);

            progress.value = i;
            text.textContent = `${i} / ${total} relevant tags loaded`;

            if (i < total) {
                requestAnimationFrame(appendBatch);
            } else {
                progContainer.remove();
            }
        }

        requestAnimationFrame(appendBatch);
    },

    // below is all (C) 2026 rene.veerman.netherlands@gmail.com
    next : function (div, search, url, saveTheme, callback, callStack) {
        var t = na.background;
        var fncn = 'na.background.next()';//na.m.myName(t);
        na.m.waitForCondition (fncn+' : t.data?', function() {
            return t.data;
        }, function() {
            t.next_do (div, search, url, saveTheme, callback, callStack);
        }, 20);
    },

    next_do : function (div, search, url, saveTheme, callback, callStack) {
        var t = na.background;
        if (!div) div = '#siteBackground';
        if (saveTheme!==false) saveTheme = true;
        if (!callStack) callStack = '';
        if (!callback && saveTheme) callback = na.site.saveTheme;
        if (!search) search = t.settings.backgroundSearchKey;
        if (!search) {
            search = 'Landscape';
        };
        t.settings.backgroundSearchKey = search;

        var oldBSK = na.site.globals.backgroundSearchKey;
        if (oldBSK==='' || oldBSK=='Landscape' || oldBSK=='Portrait') {
            if ( parseFloat($(window).width()) > parseFloat($(window).height()) )
                na.site.globals.backgroundSearchKey
                    = na.site.globals.backgroundSearchKey.replace ('Portrait', 'Landscape');
            else
                na.site.globals.backgroundSearchKey
                    = na.site.globals.backgroundSearchKey.replace ('Landscape', 'Portrait');
        }
        /*
        if (oldBSK !== '' && oldBSK != na.site.globals.backgroundSearchKey)
            na.backgrounds.next (
                '#siteBackground',
                na.site.globals.backgroundSearchKey,
                null,
                false
            );
        */


        var
        fncn = 'na.backgrounds.next(div,search,url,saveTheme,callback)',
        bgs = t.data,
        sk = search.split(/\s+/),
        hits = [];

        $('#siteBackground, #siteBackground img, #siteBackground div, #siteBackground iframe').css({
            position:'absolute',
            width : $(window).width(),
            height : $(window).height()
        });

        t.settings.lastMenuSelection = search;
        //debugger;

        var useRoot = true;
        if (typeof url !== 'string' || url === '') {
            for (var collectionIdx=0; collectionIdx<bgs.length; collectionIdx++) {
                if (!bgs[collectionIdx].files) continue;

                for (var i=0; i<bgs[collectionIdx].files.length; i++) {
                    var
                    bg = bgs[collectionIdx].files[i],
                    hit = true;

                    for (var bgk in bg) break;
                    var
                    bgSize = bg[bgk].split('x'),
                    w = parseInt(bgSize[0]),
                    h = parseInt(bgSize[1]);

                    for (var j=0; j<sk.length; j++) {
                        var re = new RegExp(sk[j], 'i');
                        if (sk[j].substr(0,1)==='-') {
                            if (bgk.match(re)) hit = false;
                        } else {
                            if (!bgk.match(re)) hit = false;
                        }
                    }

                    if (
                        (
                            bgk.match(/.*\.jpg$/i)
                            || bgk.match(/.*\.png$/i)
                            || bgk.match(/.*\.gif$/i)
                        )
                        && (
                            $(window).width() > w
                        )
                    ) {
                        hit = false;
                    }


                    // pre-parsing; date-ranges & forbidden keywords
                    if (hit) {
                        if (!search.match('women') && bgk.match('women')) hit = false;
                    }



                    if (hit) {
                        if (useRoot)
                            hits[hits.length] = document.location.origin+'/'+bgs[collectionIdx].root+'/'+bgk;
                        else
                            hits[hits.length] = bgk;
                    }
                };
            }

            if (hits.length===0) { na.m.addLogEntry(fncn+' : no backgrounds found.'); return false; };
            var url = hits[Math.floor(Math.random() * Math.floor(hits.length))];
        };
        na.m.log (10, fncn+' : url='+url, true);
        //debugger;
        t.settings.div = div;

        /*
        var
        ajaxCommand = {
            type : 'GET',
            url : url,
            success : function (data, ts, xhr) {
        */
                var
                currentTime = performance.timeOrigin + performance.now(),
                div = t.settings.div,
                bgf = $(div+' img.bg_first')[0],
                bgl = $(div+' img.bg_last')[0],
                bgDiv = $(div+'_bg')[0],
                bgDiv2 = $(div+'_bg2')[0];
                if (!bgl) debugger;
                $('#siteBackground_iframe_js').html ('');

               //debugger;

                if (url.match('tiled')) {
                    if (na.bg.settings.useFading) {
                        $(bgf).add(bgl).fadeOut('slow');
                        $(bgDiv2).css ({
                            width: jQuery(window).width() * na.site.settings.current.scale,
                            height: jQuery(window).height() * na.site.settings.current.scale,
                            background : 'url("'+url+'") repeat'
                        });
                        setTimeout(function() {
                            $(bgDiv2).stop().fadeIn(na.bg.globals.fadingSpeed, 'swing', function () {
                                $(bgDiv).css ({
                                    display : 'block',
                                    width: jQuery(window).width() * na.site.settings.current.scale,
                                    height: jQuery(window).height() * na.site.settings.current.scale,
                                    background : 'url("'+url+'") repeat'
                                });
                                setTimeout(function(){
                                    $(bgDiv2).css ({display:'none'});
                                }, 50);

                                if (typeof callback == 'function') callback();
                            })
                        }, 50);
                    } else {
                        //$(bgf).add(bgl).hide();
                        $(bgDiv).css ({
                            display : 'block',
                            width: jQuery(window).width() * na.site.settings.current.scale,
                            height: jQuery(window).height() * na.site.settings.current.scale,
                            background : 'url("'+url+'") repeat'
                        });
                        if (typeof callback == 'function') callback();
                    }

                } else if (url.match('youtube')) {
                    $(bgDiv).add(bgDiv2).css({display:'none'});
                    $(bgf).add(bgl).stop().fadeOut(na.bg.globals.fadingSpeed);

                    var ac = {
                        type : 'GET',
                        url : url,
                        success : function (data, ts, xhr) {
                            var
                            outsideURL = data;

                            var vidID = /embed\/(.*)\?/.exec(outsideURL);
                            if (vidID) {
                                vidID = vidID[1];
                            } else {
                                vidID = /watch\?v\=(.*)\&/.exec(outsideURL);
                                if (vidID) vidID = vidID[1];
                            };

                            var html = 'var player; function onYouTubeIframeAPIReady() {  player = new YT.Player("siteBackground_iframe", { height: "100%", width: "100%", videoId: "'+vidID+'", playerVars: { "playsinline": 1 }, events: { "onReady": na.backgrounds.onPlayerReady, "onStateChange": na.backgrounds.onPlayerStateChange } }); }';
                            $('#siteBackground_iframe_js').html (html);

                            outsideURL = 'https://youtube.com/embed/'+vidID+'?autoplay=1&vq=hd2160&wmode=transparent&enablejsapi=1&html5=1&origin='+document.location.href;

                            $('#siteBackground_iframe').css({display:'block',width:'100%',height:'100%'});
                            $('#siteBackground_iframe')[0].src = outsideURL;
                            $(bgDiv).add(bgDiv2).add(bgl).add(bgf).css({display:'none'});

                            if (typeof callback == 'function') callback();
                        },
                        error : function (xhr, textStatus, errorThrown) {
                            na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
                        }
                    };
                    $.ajax(ac);

                } else {
                    if (na.bg.settings.useFading) {
                        bgf.src = bgl.src;
                        $(bgl).css({display:'none'});
                        bgl.onload = function (evt) {
                            $('img.bg_last').fadeIn(na.bg.globals.fadingSpeed, 'swing', function () {
                                $('img.bg_first',$(this).parent())[0].src = $('img.bg_last',$(this).parent())[0].src;
                                $('img.bg_last',$(this).parent()).css({display:'none'});
                            })

                        };
                        bgl.src = url;
                    } else {
                        bgl.src = url;
                        if (typeof callback == 'function') callback();
                    }
                };


                na.site.globals.backgroundSearchKey = search;
                na.site.globals.background = url;
                if (na.site.globals.debug_backgroundChanges) debugger;
                if (!$.cookie('cdb_loginName') || $.cookie('cdb_loginName')==naGlobals.domain.replace('.','_')+'___Guest') {
                    $.cookie('siteBackground_search', search, na.m.cookieOptions());
                    $.cookie('siteBackground_url', url, na.m.cookieOptions());
                }
                if (!url.match(/cracked-surface/)) {
                    na.m.addLogEntry ('Background set to "'+na.site.globals.backgroundSearchKey+'"; '+url, 'naStatus_backgroundChange');
                    if (saveTheme) na.site.saveTheme();
                };
/*
            },
            error : function (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
                //TODO: re-enable:
                //na.backgrounds.next (div, search, '', saveTheme, callback);
            }
        };
        //debugger;
        $.ajax(ajaxCommand);
*/
    },

    onPlayerReady : function (a,b,c,d,e,f,g) {
        debugger;
    },

    onPlayerStateChange : function (a,b,c,d,e,f,g) {
        debugger;
    }
}
