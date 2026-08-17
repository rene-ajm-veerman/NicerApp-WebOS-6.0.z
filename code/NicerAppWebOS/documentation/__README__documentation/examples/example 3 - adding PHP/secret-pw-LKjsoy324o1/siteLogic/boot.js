var mySite = {
    globals : {},
    settings : {},
    
    bodyOnload : function () {
        myJSframework.apps.addAppToJqueryID ('#leftSidebar', myJSframework.apps.app1);
        myJSframework.apps.addAppToJqueryID ('#leftSidebar', myJSframework.apps.app4);
    }
};