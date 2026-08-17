var naLog = {
    onclick_logEntry : function (evt) {
        var
        url = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/logs/ajax_logEntry.php',
        dat = {
            i : $(evt.currentTarget).attr('i')
        },
        ac = {
            type : 'GET',
            url : url,
            data : dat,
            success : function (data, ts, xhr) {
                $('#siteContent .vividDialogContent').html(data);
            },
            error : function (xhr, textStatus, errorThrown) {
            }
        };
        $.ajax(ac);
    },
    onclick_logEntry_details : function (evt) {
        var
        url = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/logs/ajax_logEntry_details.php',
        dat = {
            id : evt.currentTarget.id
        },
        ac = {
            type : 'GET',
            url : url,
            data : dat,
            success : function (data, ts, xhr) {
                $('#siteContent .vividDialogContent').html(data);
            },
            error : function (xhr, textStatus, errorThrown) {
            }
        };
        $.ajax(ac);
    },
    showEvents : function (evt,type) {
        var
        url = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/logs/ajax_siteToolbarLeft.php',
        dat = {
            type : type
        },
        ac = {
            type : 'GET',
            url : url,
            data : dat,
            success : function (data, ts, xhr) {
                $('#siteToolbarLeft .vividDialogContent').html(data);
            },
            error : function (xhr, textStatus, errorThrown) {
            }
        };
        $.ajax(ac);
        var
        url = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/logs/ajax_siteContent.php',
        dat = {
            type : type
        },
        ac = {
            type : 'GET',
            url : url,
            data : dat,
            success : function (data, ts, xhr) {
                $('#siteContent .vividDialogContent').html(data);
            },
            error : function (xhr, textStatus, errorThrown) {
            }
        };
        $.ajax(ac);
    }
};
