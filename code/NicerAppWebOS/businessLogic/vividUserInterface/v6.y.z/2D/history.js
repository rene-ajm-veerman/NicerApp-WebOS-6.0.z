/**
 * Generic History Viewer for uDB2 dataParts
 * Works with any document that has a matching ___history database.
 */
na.history = {

    /**
     * Open the revision history timeline for any document.
     *
     * @param {string} documentID
     * @param {object} options
     *        - title           Dialog title
     *        - ajaxUrl         Endpoint that returns { ok, history: [...] }
     *        - contentField    Field that holds the main content (or path inside snapshot)
     *        - limit           Max revisions to fetch
     *        - dialogId        DOM id of the dialog (auto-created if missing)
     */
    view : function (documentID, options = {}) {
        const defaults = {
            title        : 'Revision History',
            ajaxUrl      : '/NicerAppWebOS/apps/NicerAppWebOS/userInterfaces/siteComments-3.0.0/ajax_getHistory.php',
            contentField : 'msgHTML',          // fallback; better to use snapshot.msgHTML
            limit        : 50,
            dialogId     : 'naGenericHistoryDialog'
        };
        const opts = Object.assign({}, defaults, options);

        // Ensure dialog shell exists
        if ($('#' + opts.dialogId).length === 0) {
            $('body').append(`
                <div id="${opts.dialogId}" class="vividDialog naHistoryTimeline" style="display:none;">
                    <div class="vividDialogTitle">${opts.title}</div>
                    <div class="vividDialogContent naHistoryTimelineContent">
                        <div class="naHistoryLoading">Loading history…</div>
                    </div>
                    <div class="vividDialogButtons">
                        <button class="naHistoryCloseBtn">Close</button>
                    </div>
                </div>
            `);

            $('#' + opts.dialogId + ' .naHistoryCloseBtn').on('click', function () {
                $('#' + opts.dialogId).fadeOut(180);
            });
        }

        const $dialog  = $('#' + opts.dialogId);
        const $content = $dialog.find('.naHistoryTimelineContent');

        // Update title in case it changed
        $dialog.find('.vividDialogTitle').text(opts.title);

        $content.html('<div class="naHistoryLoading">Loading history…</div>');
        $dialog
            .css({
                position : 'fixed',
                top      : '7%',
                left     : '10%',
                width    : '80%',
                height   : '82%',
                maxWidth : '1100px',
                zIndex   : 2000000
            })
            .fadeIn(180);

        $.ajax({
            type : 'POST',
            url  : opts.ajaxUrl,
            data : {
                id    : documentID,
                limit : opts.limit
            },
            success : function (raw) {
                let data;
                try {
                    data = (typeof raw === 'string') ? JSON.parse(raw) : raw;
                } catch (e) {
                    $content.html('<div class="naHistoryError">Could not parse history response.</div>');
                    return;
                }

                if (!data.ok || !Array.isArray(data.history)) {
                    $content.html('<div class="naHistoryError">No history found.</div>');
                    return;
                }

                na.history.renderTimeline($content, data.history, opts);
            },
            error : function () {
                $content.html('<div class="naHistoryError">Network error while loading history.</div>');
            }
        });
    },

    /**
     * Render the vertical timeline
     */
    renderTimeline : function ($container, history, opts) {
        if (history.length === 0) {
            $container.html('<div class="naHistoryEmpty">No previous revisions exist.</div>');
            return;
        }

        let html = '<div class="naHistoryTimelineTrack">';

        history.forEach(function (rev, idx) {
            // Support both the new "snapshot" format and the older flat format
            const snap = rev.snapshot || rev;

            const when = rev.historyDatetimeStr
                      || (rev.historyDatetime ? new Date(rev.historyDatetime * 1000).toLocaleString() : 'unknown');

            const who  = rev.historyBy || snap.clientUsername || 'unknown';

            // Resolve content field (supports nested paths like "snapshot.msgHTML")
            let content = '';
            if (opts.contentField.indexOf('.') > -1) {
                const parts = opts.contentField.split('.');
                let cur = rev;
                for (const p of parts) {
                    cur = cur ? cur[p] : null;
                }
                content = cur || '';
            } else {
                content = snap[opts.contentField] || rev[opts.contentField] || '';
            }

            html += `
                <div class="naHistoryEntry" data-idx="${idx}">
                    <div class="naHistoryDot"></div>
                    <div class="naHistoryCard">
                        <div class="naHistoryMeta">
                            <span class="naHistoryWhen">${when}</span>
                            <span class="naHistoryBy">by ${who}</span>
                            ${rev.originalRev ? `<span class="naHistoryRev">rev ${rev.originalRev}</span>` : ''}
                        </div>
                        <div class="naHistoryBody">${content}</div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        $container.html(html);
    }
};
