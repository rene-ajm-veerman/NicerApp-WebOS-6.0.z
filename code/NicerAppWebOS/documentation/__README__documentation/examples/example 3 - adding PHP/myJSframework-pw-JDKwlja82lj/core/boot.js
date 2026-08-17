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
