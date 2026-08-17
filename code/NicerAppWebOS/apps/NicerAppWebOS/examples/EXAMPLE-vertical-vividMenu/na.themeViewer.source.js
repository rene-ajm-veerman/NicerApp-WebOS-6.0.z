na.tv = na.themeViewer = {
    globals : {},
    settings : { current : {} },
    
    onload : function (event) {
        var ac = {
            type : 'GET',
            url : '/NicerAppWebOS/apps/nicer.app/naThemeViewer/ajax_getThemes.php',
            data : {
                which : 'all'
            },
            success : function (data, ts, xhr) {
                na.tv.s.c.db = JSON.parse(data);
                na.tv.visualize (na.tv.s.c.db);
            },
            failure : function (xhr, ajaxOptions, thrownError) {
                debugger;
            }
        };
        $.ajax(ac);
    },
    
    visualize : function (db) {
        var html = 
            '<table class="tvTable">'
            +'<tr>'
                +'<th>URL</th>'
                +'<th>User</th>'
                +'<th>Group</th>'
            +'</tr>';
            
        for (var i=0; i<db.length; i++) {
            var username = db[i].user;
            if (username === undefined) username='';
            var group = db[i].role;
            if (group === undefined) group='';
            
            html += '<tr>';
            html += '<td>'+db[i].url+'</td>';
            html += '<td>'+db[i].user+'</td>';
            html += '<td>'+db[i].role+'</td>'; // a 'role' is 100% synonym with 'group'.
            html += '</tr>';
        }
        
        html += '</table>';
        $('#siteContent .vividDialogContent').html(html);
    }
};
na.tv.s = na.tv.settings;
na.tv.s.c = na.tv.settings.current;
