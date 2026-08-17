na.tv = na.themeViewer = {
    globals : {},
    settings : { current : {} },
    
    onload : function (event, div) {
        var fncn = 'na.themeViewer.onload(event,"#'+div.id+'")';
        if (div.id=='siteToolbarTop') {
            var 
            url = '/NicerAppWebOS/apps/nicer.app/naThemeViewer/ajax_getThemes.php',
            ac = {
                type : 'GET',
                url : url,
                data : {
                    which : 'all'
                },
                success : function (data, ts, xhr) {
                    na.tv.s.c.db = JSON.parse(data);
                    na.tv.visualize (na.tv.s.c.db);
                },
                error : function (xhr, textStatus, errorThrown) {
                    na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
                }                
            };
            $.ajax(ac);
        }
    },
    
    visualize : function (db) {
        $('#siteToolbarTop').addClass('locked');
        
        var html = '<table class="tvTable">';
        html += '<tr><th>URL</th><th>User</th><th>Group</th><th>Preview theme</th></tr>';
            
        for (var i=0; i<db.length; i++) {
            var username = db[i].user;
            if (typeof username == 'undefined') username='[all]';
            var role = db[i].role;
            if (typeof role == 'undefined') role='[all]';
            
            var url = db[i].url;
            var app = {
                page : 'front page'
            }
            if (url.indexOf('/apps/')!==-1) {
                var 
                appJSON = url.replace('/apps/', ''),
                app = na.m.decode_base64_url(appJSON);
                appHTML = 
                    '<div class="tv_na_url">'+url+'</div>' 
                    +'<pre class="tv_na_url">'+na.tv.syntaxHighlight(app)+'</pre>';
            } else {
                var
                appJSON = '',
                app = app,
                appHTML = 
                    '<div class="tv_na_url">'+url+'</div>' 
                    +'<pre class="tv_na_url">'+na.tv.syntaxHighlight(app)+'</pre>';
            }
            var
            css = '<pre class="tv_na_css">'+na.tv.syntaxHighlight(db[i].dialogs)+'</pre>';
            //css = na.tv.syntaxHighlight(db[i].dialogs);
            
            var
            tryTheme = '<a href="javascript:na.tv.tryTheme('+i+');">Try theme out</a>';
            
            /*
            html += '<tr><td><table onclick="javascript:na.tv.tryTheme('+i+');" style="width:100%">';
            html += '<tr><th>URL</th><td>'+appHTML+'</td></tr>';
            html += '<tr><th>user</th><td>'+username+'</td></tr>';
            html += '<tr><th>role</th><td>'+role+'</td></tr>'; 
            html += '</table></td></tr>';
            */
            html += '<tr>';
            html += '<td>'+appHTML+'</td>';
            html += '<td>'+username+'</td>';
            html += '<td>'+role+'</td>'; 
            html += '<td><a href="javascript:na.tv.previewTheme('+i+');">Preview</a></td>'; 
            html += '</tr>';
        }
        
        html += '</table>';
        $('.tvContainer').html(html);
        $('.lds-facebook').fadeOut('normal');            
    },
    
    syntaxHighlight : function (json) { // thanks https://stackoverflow.com/questions/4810841/pretty-print-json-using-javascript
        if (typeof json != 'string') {
            json = JSON.stringify(json, undefined, 2);
        }
        json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
            var cls = 'number';
            if (/^"/.test(match)) {
                if (/:$/.test(match)) {
                    cls = 'tvKey';
                } else {
                    cls = 'tvString';
                }
            } else if (/true|false/.test(match)) {
                cls = 'tvBoolean';
            } else if (/null/.test(match)) {
                cls = 'tvNull';
            }
            return '<span class="' + cls + '">' + match + '</span>';
        });
    },
    
    previewTheme : function (i) {
        var rec = na.tv.s.c.db[i];
        na.tv.s.c.oldCSS = $('#cssPageSpecific').html();
        na.site.loadContent_getAndDisplayContent(rec.url.replace('/apps/',''));
        setTimeout(function() {
            $('#cssPageSpecific').html(rec.dialogs);
            setTimeout(function() {
                $('#cssPageSpecific').html(na.tv.s.c.oldCSS);
            }, 5 * 1000);
        }, 3 * 1000);
    }
};
na.tv.s = na.tv.settings;
na.tv.s.c = na.tv.settings.current;
