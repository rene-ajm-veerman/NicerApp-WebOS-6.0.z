na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/userInterfaces/siteComments'] = na.comments = na.c = {
    //settings : { current : { mediaFolderView : 'view' } },
    settings : {
        initialized : false,
        current : { },
		loadedIn : {
			'#siteComments' : {
				onload : function (settings) {
                    if (!na.c.s.initialized) {
                        na.c.onload (settings);
                        na.c.s.initialized = true;
                    }
				},
                ondestroy : function (settings) {
                },
				onresize : function (settings) {
				}
			}
		}
    },

    onload : async function(settings) {
        na.c.s.c.onload = settings;
    }
};
na.c.s = na.c.settings;
na.c.s.c = na.c.s.current;
