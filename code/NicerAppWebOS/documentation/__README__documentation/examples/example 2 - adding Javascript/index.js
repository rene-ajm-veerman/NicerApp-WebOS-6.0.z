// this is a javascript one-line COMMENT

/* this is a javascript 
   multiple-lines COMMENT

 */ 

var myJSframework = { // "{" starts a js object and "}" ends the js object definition.
    /* an object is a folder sorta say,
     * -    an object contains variables (which can be nested objects (subfolders),
     *      and functions (also called methods in some other languages)
     */

    globals : {}, // globals are variables that do not ever change.
    
    settings : {}, // settings are variables that are used in multiple places in the code.
    
    apps : {
        addAppToJqueryID : function (id, app) {
            app.settings.instances[id] = {
                id : id
            };
            jQuery(id).append(app.globals.myHTMLtemplate);
        }
    }
};


myJSframework.apps.app1 = {
    globals : { 
    // these are the globals SPECIFIC ONLY to app1
        HTMLtemplate : 
            '<div id="app1" class="appDiv">'
            +'</div>'
    },
    settings : {
        instances : {}
    }
};

myJSframework.apps.app2 = {
    globals : { 
    // these are the globals SPECIFIC ONLY to app2
        HTMLtemplate : 
            '<div id="app2" class="appDiv">'
            +'</div>'
    },
    settings : {
        instances : {}
    }
};

myJSframework.apps.app3 = {
    globals : {
        HTMLtemplate : 
            '<div id="app3" class="appDiv">'
            +'</div>'
    },
    settings : {
        instances : {}
    }
};

myJSframework.apps.app4 = {
    globals : {
        HTMLtemplate : 
            '<div id="app4" class="appDiv">'
            +'</div>'
    },
    settings : {
        instances : {}
    }
};

var mySite = {
    globals : {},
    settings : {},
    
    bodyOnload : function () {
        myJSframework.apps.addAppToJqueryID ('#leftSidebar', myJSframework.apps.app1);
        myJSframework.apps.addAppToJqueryID ('#leftSidebar', myJSframework.apps.app4);
    }
};