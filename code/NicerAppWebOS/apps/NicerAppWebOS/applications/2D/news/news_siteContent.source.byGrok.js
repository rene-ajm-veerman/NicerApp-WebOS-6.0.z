if (!na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'])
    na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'] = {
        about: {
            whatsThis: 'Application code for this news app (RSS reader)',
            copyright: 'Copyright (C) 2011-2026 by Rene AJM Veerman',
            license: 'https://nicer.app/license [MIT]',
            firstCreated: '2018',
            lastModified: '2026-05-15',
            version: '3.5.1-masonry'
        },
        globals: {
            readHistory_numHours: 1
        },
        settings: {
            idx: 0,
            loadedIn: {
                '#siteContent': {
                    settings: { initialized: false, ready: false },
                    saConfigUpdate: function() { nicerapp.site.globals.desktop.configUpdate(); },
                    mergeMenu: function() {
                        const na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'];
                        const mainmenu = $('#newsApp_mainmenu')[0];
                        if (!mainmenu.linksTransformedAlready) {
                            na.menu.preprocess('newsApp_mainmenu');
                            window.top.na.s.s.transformLinks($('#newsApp_mainmenu')[0]);
                            mainmenu.linksTransformedAlready = true;
                        }
                        $('#newsApp_mainmenu').css({display:'none'});
                        na.menu.merge('siteMenu', 'na.site.code.transformLinks', '-saLinkpoint-appMenu', $('#newsApp_mainmenu')[0]);
                        na.desktop.onresize({reloadMenu: true});
                    },
                    onload: function(settings) {
                        if (settings.callbackParams) { settings.callback(); return; }

                        $('.lds-facebook').fadeIn('normal');
                        na.m.waitForCondition('news onload', na.m.HTMLidle, function() {
                            const na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'];
                            na.site.startUIvisuals('#siteContent');
                            $('#siteContent .vividDialogContent').css({overflow:'hidden'});

                            na1.setupLayout();
                            $('#siteContent__header').fadeIn('normal').css({display:'flex'});

                            na1.themeAppsChanged();

                            na.m.waitForCondition('news dialog ready', () =>
                            $('#siteContent__content').length > 0 && na.m.desktopIdle() && na.site.s.c.booted,
                                                  () => {
                                                      na1.onresize();
                                                      na1.settings.loadedIn['#siteContent'].settings.initialized = true;
                                                      na1.settings.loadedIn['#siteContent'].settings.ready = true;
                                                      document.addEventListener('keyup', na1.onkeyup);
                                                      setTimeout(() => na1.nestedStartApp(), 300);
                                                      na.site.onresize_doContent({});
                                                  }, 50);
                        }, 50);
                    },
                    ondestroy: function() {
                        const na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'];
                        na1.cleanupAllTimers();
                        $('.newsApp__item__outer').remove();
                        document.removeEventListener('keyup', na1.onkeyup);
                    },
                    onresize: function() {
                        const na1 = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news'];
                        na1.onresize();
                    }
                }
            },
            current: {
                db: [],
                locked: false,
                countDown: 28,        // slightly longer for more items
                countDownInterval: null
            }
        },

        setupLayout: function() {
            const header = $('#siteContent__header');
            let content = $('#newsApp_content');
            if (!content.length) {
                content = $('<div id="newsApp_content"></div>');
            }

            // Place content directly under header
            header.after(content);

            if (!$('#newsApp_timer').length) {
                header.append('<div id="newsApp_timer" style="position:absolute; top:12px; right:20px; background:rgba(0,0,0,0.75); color:#0f0; padding:5px 12px; border-radius:4px; font-size:0.95em; z-index:100;"></div>');
            }
        },

        cleanupAllTimers: function() {
            const s = this.settings;
            Object.keys(s).forEach(key => {
                if (key.includes('timer') || key.includes('Interval') || key.includes('Timeout')) {
                    if (s[key]) clearTimeout(s[key]);
                    if (s[key]) clearInterval(s[key]);
                    delete s[key];
                }
            });
            s.loading = false;
            s.displaying = false;
        },

        nestedStartApp: function() {
            na.m.waitForCondition('siteContent ready', () => $('#siteContent')[0], () => this.startApp(), 30);
        },

        startApp: function() {
            const na1 = this;
            const s = na1.settings;

            na1.cleanupAllTimers();
            $('.newsApp__item__outer').remove();

            s.dtCurrent = new Date(Date.now() - 1000*60*60*na1.globals.readHistory_numHours);
            s.dtEnd = new Date();
            s.loads = 0;
            s.current.db = [];

            na1.loadNews_read_loop();
        },

        startCountdown: function() {
            const na1 = this;
            const c = na1.settings.current;
            let remaining = c.countDown;

            $('#newsApp_timer').html(`Volgende pagina over <b>${remaining}</b>s`);

            clearInterval(c.countDownInterval);
            c.countDownInterval = setInterval(() => {
                remaining--;
                $('#newsApp_timer').html(`Volgende pagina over <b>${remaining}</b>s`);

                if (remaining <= 0) {
                    clearInterval(c.countDownInterval);
                    na1.displayNewNewsItems();
                }
            }, 1000);
        },

        displayNewNewsItems: function() {
            const na1 = this;
            const s = na1.settings;
            const c = s.current;

            if (s.locked || s.displaying) return;
            s.displaying = true;

            $('.newsApp__item__outer').remove();

            let candidates = [];
            na.m.walkArray(c.db, c.db, undefined, (cd) => {
                const it = cd.v;
                if (it && na1.match_searchCriteria(it)) candidates.push(it);
            });

                candidates = na1.shuffleArray(candidates);
                const toDisplay = candidates.slice(0, 55);   // Increased item count

                let delay = 0;
                toDisplay.forEach((it) => {
                    delay += Math.floor(Math.random() * 280) + 180;   // slightly faster stagger

                    setTimeout(() => {
                        na1.displayNews_formatItem(it);
                        it.displayed = true;
                    }, delay);
                });

                s.displaying = false;

                setTimeout(() => na1.startCountdown(), delay + 1400);
        },

        shuffleArray: function(array) {
            const arr = array.slice();
            for (let i = arr.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [arr[i], arr[j]] = [arr[j], arr[i]];
            }
            return arr;
        },

        displayNews_formatItem: function(it) {
            if (!it || (!it.t && !it.de)) return false;

            const id = 'newsApp__item__' + (it.idx || Date.now() + Math.random().toString(36).substr(2, 6));
            const title = (it.t || 'Untitled').replace(/'/g, '&#39;');

            let desc = (it.de || '');
            desc = desc.replace(/<img([^>]+)>/gi, (match, attributes) => {
                return `<img ${attributes} style="width:100%; max-height:420px; height:auto; display:block; margin:10px 0 12px 0; border-radius:6px; object-fit:cover;">`;
            });
            desc = desc.replace(/<script.*?>.*?<\/script>/gi, '');

            const html = `
            <div id="${id}" class="newsApp__item__outer">
            <div class="newsApp__item__title">
            <a href="${it._id || '#'}" target="_blank">${title}</a>
            </div>
            <div class="newsApp__item__desc">
            ${desc}
            </div>
            </div>`;

            $('#newsApp_content').append(html);
            setTimeout(() => $('#' + id).css({opacity: 1}), 30);

            return true;
        },

        match_searchCriteria: function(it) { return true; },

        loadNews_read_loop: function() {
            const na1 = this;
            const s = na1.settings;
            const c = s.current;

            if (s.loading) return;
            na1.cleanupAllTimers();

            s.loading = true;
            s.loads++;

            const data = {
                loads: s.loads,
                direction: 'past',
                section: (s.section || '').replace(/-/g,'/'),
                dateBegin: na1.formatDateForLoading(s.dtCurrent),
                dateEnd: na1.formatDateForLoading(s.dtEnd)
            };

            $.ajax({
                type: 'GET',
                url: '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news/ajax_get_items.php',
                data: data,
                success: function(data) {
                    s.loading = false;
                    if (!data || !Array.isArray(data)) {
                        na1.scheduleNextLoad(7000);
                        return;
                    }

                    c.db = c.db.concat(data).slice(-2000);
                    na1.displayNewNewsItems();
                },
                error: function() {
                    s.loading = false;
                    na1.scheduleNextLoad(10000);
                }
            });
        },

        scheduleNextLoad: function(delay) {
            const s = this.settings;
            s.timerLoadNews_read_loop = setTimeout(() => this.loadNews_read_loop(), delay);
        },

        onresize: function() {
            const s = this.settings;
            clearTimeout(s.timeout_onresize);
            s.timeout_onresize = setTimeout(() => {
                $('#newsApp_content').css({
                    display: 'grid',
                    gridTemplateColumns: 'repeat(auto-fill, minmax(320px, 1fr))',
                                          gap: '18px',
                                          padding: '15px',
                                          width: '100%',
                                          height: 'calc(100% - 75px)',
                                          overflow: 'auto',
                                          alignItems: 'start',
                                          gridAutoFlow: 'row dense'
                });
            }, 100);
        },

        toggleLock: function() {
            this.settings.locked = !this.settings.locked;
        },

        formatDateForLoading: function(dt) { return dt.toISOString(); },
            themeAppsChanged: function() {},
            gotoNextPage: function() {}
    };
