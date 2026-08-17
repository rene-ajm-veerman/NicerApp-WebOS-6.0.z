if (!na.apps) na.apps = {};
na.m.waitForCondition ( 'na.m.desktopInitialized() for app.2D.forums', na.m.desktopInitialized, function () {        
        
na.analytics.logMetaEvent ('app.2D.forums : init-stage-1');

delete na.apps.loaded['app.2D.forums'];
na.apps.loaded['app.2D.forums'] = na.forums = {

    globals : {
        about : {
            version : '1.0.0'
        },
        baseURL : '/NicerAppWebOS/apps/nicer.app/app.2D.forums',
        jsMe : 'na.apps.loaded[\'app.2D.forums\']'
    },

    settings : {
        config : false,
        perPage : 50,
        showSpeed : 'normal',
        loadedIn : {
            '#siteContent' : {
                onload : function () {
                    na.site.settings.current.app = 'app.2D.forums';
                    
                    $('#siteContent .vividButton_icon_50x50').each(function(idx,el){
                        if (!na.site.settings.buttons['#'+el.id]) na.site.settings.buttons['#'+el.id] = new naVividButton(el);
                    });
                    
                    $('#siteContent img[srcPreload]').each(function(idx,el2) {
                        na.site.settings.current.preLoad++;
                        //$(el2).attr('onload', 'na.site.settings.current.srcPreload--; if (na.site.settings.current.preLoad===0) na.desktop.init();');
                        if (!el2.src || el2.src==='') {
                            $(el2).attr('src', $(el2).attr('srcPreload'));
                            $(el2).removeAttr('srcPreload');
                        }
                    });
                },
                ondestroy : function () {
                    $('#siteContent .vividButton_icon_50x50').each(function(idx,el){
                        if (na.site.settings.buttons['#'+el.id]) delete na.site.settings.buttons['#'+el.id];
                    });
                    
                    setTimeout (function() {
                        delete na.apps.loaded['app.2D.forums'];
                    }, 100);
                }
            }
        }
    },

    onclick_btnAddForumCategory : function () {
        $('#naForums_inputBar_btnAddForumCategory').slideDown(na.apps.loaded['app.2D.forums'].settings.showSpeed);
        $('#naForums_buttonBar_btnAddForumCategory').slideUp(na.apps.loaded['app.2D.forums'].settings.showSpeed);
    },
    
    onclick_btnAddForumCategory_saveCategory : function (evt) {
        var 
        fncn1a = 'na.apps.loaded["app.2D.forums"].onclick_btnAddForumCategory_saveCategory(evt)',
        url1a = na.apps.loaded['app.2D.forums'].globals.baseURL+'/ajax_addForumCategory.php',
        ajaxCmd = {
            type : 'POST',
            url : url1a,
            data : {
                newCategory : $('#naForums_inputBar_btnAddForumCategory input').val()
            },
            success : function (data, ts, xhr) {
                na.apps.loaded['app.2D.forums'].reloadForumIndexPage();
            },
            error : function (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn1a, url1a, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ajaxCmd);    
    },

    onclick_btnAddForum_saveForum : function (forumCategory, inputHTMLid) {
        var 
        fncn1b = 'na.apps.loaded["app.2D.forums"].onclick_btnAddForum_saveForum(forumCategory,inputHTMLid)',
        url1b = na.apps.loaded['app.2D.forums'].globals.baseURL+'/ajax_addForum.php',
        ajaxCmd = {
            type : 'POST',
            url : url1b,
            data : {
                categoryName : forumCategory,
                newForum : $('#'+inputHTMLid).val()
            },
            success : function (data, ts, xhr) {
                na.apps.loaded['app.2D.forums'].reloadForumIndexPage();
            },
            error : function (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn1b, url1b, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ajaxCmd);    
    },

    onclick_btnRenameForumCategory : function (evt) {
        var showSpeed = 'fast';//na.apps.loaded['app.2D.forums'].settings.showSpeed
        $(evt.target).parents('.naForums_forumCategory').children('.naForums_forumCategoryName').css({position:'absolute'}).fadeOut(showSpeed);
        $(evt.target).parents('.naForums_forumCategory').children('.naForums_forumCategoryName_input').css({position:'relative'}).fadeIn(showSpeed);
        $(evt.target).parents('.naForums_forumCategory').children('.vividButton_icon_50x50_text.btnRename').children('div').html('Save');
        $(evt.target).parents('.naForums_forumCategory').children('.vividButton_icon_50x50_text.btnRename')[0].setAttribute (
            'onclick', na.apps.loaded['app.2D.forums'].globals.jsMe+'.onclick_btnRenameForumCategory_save(event);'
        );
        $(evt.target).parents('.naForums_forumCategory').children('.vividButton_icon_50x50.btnRename')[0].setAttribute (
            'onclick', na.apps.loaded['app.2D.forums'].globals.jsMe+'.onclick_btnRenameForumCategory_save(event);'
        );
    },
    
    onclick_btnRenameForumCategory_save : function (evt) {
        var 
        fncn1c = 'na.apps.loaded["app.2D.forums"].onclick_btnRenameForumCategory_save(evt)',
        url1c = na.apps.loaded['app.2D.forums'].globals.baseURL+'/ajax_editForumCategory.php',
        ajaxCmd = {
            type : 'POST',
            url : url1c,
            data : {
                oldCategoryName : $(evt.target).parents('.naForums_forumCategory').children('.naForums_forumCategoryName').html(),
                newCategoryName : $(evt.target).parents('.naForums_forumCategory').children('.naForums_forumCategoryName_input').val()
            },
            success : function (data, ts, xhr) {
                /*
                var newForumCategoryName = $(evt.target).parents('.naForums_forumCategory').children('.naForums_forumCategoryName_input').val();
                $(evt.target).parents('.naForums_forumCategory').children('.naForums_forumCategoryName').html(newForumCategoryName);
                
                var showSpeed = 'fast';//na.apps.loaded['app.2D.forums'].settings.showSpeed
                $(evt.target).parents('.naForums_forumCategory').children('.naForums_forumCategoryName').css({position:'relative'}).fadeIn(showSpeed);
                $(evt.target).parents('.naForums_forumCategory').children('.naForums_forumCategoryName_input').css({position:'absolute'}).fadeOut(showSpeed);
                $(evt.target).parents('.naForums_forumCategory').children('.vividButton_icon_50x50_text.btnRename').children('div').html('Rename');
                $(evt.target).parents('.naForums_forumCategory').children('.vividButton_icon_50x50_text.btnRename')[0].setAttribute (
                    'onclick', 'na.apps.loaded['app.2D.forums'].onclick_btnRenameForumCategory(event);'
                );*/
                na.apps.loaded['app.2D.forums'].reloadForumIndexPage();
            },
            error : function (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn1c, url1c, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ajaxCmd);    
    },

    onclick_btnRenameForum : function (evt) {
        var showSpeed = 'fast';//na.apps.loaded['app.2D.forums'].settings.showSpeed
        $(evt.target).parents('.naForums_forum').children('.naForums_forumName').css({position:'absolute'}).fadeOut(showSpeed);
        $(evt.target).parents('.naForums_forum').children('.naForums_forumName_input').css({position:'relative'}).fadeIn(showSpeed);
        $(evt.target).parents('.naForums_forum').children('.vividButton_icon_50x50_text.btnRename').children('div').html('Save');
        $(evt.target).parents('.naForums_forum').children('.vividButton_icon_50x50_text.btnRename')[0].setAttribute (
            'onclick', na.apps.loaded['app.2D.forums'].globals.jsMe+'.onclick_btnRenameForum_save(event);'
        );
        $(evt.target).parents('.naForums_forum').children('.vividButton_icon_50x50.btnRename')[0].setAttribute (
            'onclick', na.apps.loaded['app.2D.forums'].globals.jsMe+'.onclick_btnRenameForum_save(event);'
        );
    },
    
    onclick_btnRenameForum_save : function (evt) {
        var 
        fncn1d = 'na.apps.loaded["app.2D.forums"].onclick_btnRenameForum_save(evt)',
        url1d = na.apps.loaded['app.2D.forums'].globals.baseURL+'/ajax_editForum.php',
        ajaxCmd = {
            type : 'POST',
            url : url1d,
            data : {
                categoryName : $(evt.target).parents('.naForums_forumCategory_container').find('.naForums_forumCategoryName').html(),
                oldForumName : $(evt.target).parents('.naForums_forum').children('.naForums_forumName').html(),
                newForumName : $(evt.target).parents('.naForums_forum').children('.naForums_forumName_input').val()
            },
            success : function (data, ts, xhr) {
                /*
                var newForumCategoryName = $(evt.target).parents('.naForums_forum').children('.naForums_forumName_input').val();
                $(evt.target).parents('.naForums_forum').children('.naForums_forumName').html(newForumCategoryName);
                
                var showSpeed = 'fast';//na.apps.loaded['app.2D.forums'].settings.showSpeed
                $(evt.target).parents('.naForums_forum').children('.naForums_forumName').css({position:'relative'}).fadeIn(showSpeed);
                $(evt.target).parents('.naForums_forum').children('.naForums_forumName_input').css({position:'absolute'}).fadeOut(showSpeed);
                $(evt.target).parents('.naForums_forum').children('.vividButton_icon_50x50_text.btnRename').children('div').html('Rename');
                $(evt.target).parents('.naForums_forum').children('.vividButton_icon_50x50_text.btnRename')[0].setAttribute (
                    'onclick', 'na.apps.loaded['app.2D.forums'].onclick_btnRenameForum(event);'
                );
                $(evt.target).parents('.naForums_forum').children('.vividButton_icon_50x50.btnRename')[0].setAttribute (
                    'onclick', 'na.apps.loaded['app.2D.forums'].onclick_btnRenameForum(event);'
                );*/
                na.apps.loaded['app.2D.forums'].reloadForumIndexPage();
            },
            error : function (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn1d, url1d, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ajaxCmd);    
    },

    onclick_btnDeleteForumCategory : function (evt) {
        var 
        fncn1e = 'na.apps.loaded["app.2D.forums"].onclick_btnDeleteForumCategory(evt)',
        url1e = na.apps.loaded['app.2D.forums'].globals.baseURL+'/ajax_deleteForumCategory.php',
        ajaxCmd = {
            type : 'POST',
            url : url1e,
            data : {
                categoryName : $(evt.target).parents('.naForums_forumCategory').children('.naForums_forumCategoryName').html()
            },
            success : function (data, ts, xhr) {
                var 
                showSpeed = na.apps.loaded['app.2D.forums'].settings.showSpeed;
                $(evt.target).parents('.naForums_forumCategory_container').slideUp(showSpeed, function() {
                    $(evt.target).parents('.naForums_forumCategory_container').remove();
                });
            },
            error : function (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn1e, url1e, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ajaxCmd);
    },

    onclick_btnDeleteForum : function (evt) {
        var 
        fncn1f = 'na.apps.loaded["app.2D.forums"].onclick_btnDeleteForum(evt)',
        url1f = na.apps.loaded['app.2D.forums'].globals.baseURL+'/ajax_deleteForum.php',
        ajaxCmd = {
            type : 'POST',
            url : url1f,
            data : {
                categoryName : $(evt.target).parents('.naForums_forumCategory').children('.naForums_forumCategoryName').html(),
                forumName : $(evt.target).parents('.naForums_forum').children('.naForums_forumName').html()
            },
            success : function (data, ts, xhr) {
                var showSpeed = na.apps.loaded['app.2D.forums'].settings.showSpeed;
                $(evt.target).parents('.naForums_forum').slideUp(showSpeed, function() {
                    $(evt.target).parents('.naForums_forum').remove();
                });
            },
            error : function (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn1f, url1f, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ajaxCmd);    
    },

    reloadForumIndexPage : function () {
        var 
        fncn1g = 'na.apps.loaded["app.2D.forums"].reloadForumIndexPage()',
        url1g = na.apps.loaded['app.2D.forums'].globals.baseURL+'/ajax_reloadForumIndexPage.php',
        ajaxCmd = {
            type : 'GET',
            url : url1g,
            success : function (data, ts, xhr) {
                var targetEl = $('#siteContent .vividDialogContent');
                $(targetEl).children().fadeOut(na.apps.loaded['app.2D.forums'].settings.showSpeed,function() {
                    $(targetEl).html(data).children().fadeIn(na.apps.loaded['app.2D.forums'].settings.showSpeed, function () {
                        $('.vividButton_icon_50x50', targetEl).each(function(idx,el){
                            if (!na.site.settings.buttons['#'+el.id]) na.site.settings.buttons['#'+el.id] = new naVividButton(el);
                        });
                        $('img[srcPreload]', targetEl).each(function(idx,el2) {
                            na.site.settings.current.preLoad++;
                            if (!el2.src || el2.src === '') {
                                $(el2).attr('src', $(el2).attr('srcPreload'));
                                $(el2).removeAttr('srcPreload');
                            }
                        });
                        
                    });
                });
            },
            error : function (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn1g, url1g, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ajaxCmd);    
        
    }
}
}, 100);
