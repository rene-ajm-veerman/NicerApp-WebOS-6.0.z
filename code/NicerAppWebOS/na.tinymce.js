 
na.tinymce = {

    init : function (el, parentEl) {
            var
            now = (new Date().getTime()),
            h = 200,
            useDarkMode = true;
            tinymce.ready = false;

            //$('#siteCommentsEditor').css({border:'',boxShadow:''});

            //NOT NEEDED WHEN USING CLOUD VERSION (valid though) : tinymce.baseURL = 'https://cdn.tiny.cloud/1/89d73yohz5ameo5exzlj9d6kya9vij9mt8f5ipzzqjo0wkw5/tinymce/4';
            tinymce.baseURL = '/NicerAppWebOS/3rd-party/tinymce-4.9.11/js/tinymce';

            tinymce.suffix = '';//'.min';
            $(el).tinymce({
                suffix : '',
                selector: '#'+el.id,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor textcolor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste code help table'
                ],
                external_plugins : {
                    'emoticons' : '/NicerAppWebOS/3rd-party/tinymce-4/plugins/naEmoticons/plugin.min.js'
                },
                resize : true,
                menubar: false,//'file edit view insert format tools table help',
                toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect | formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl | table tabledelete | tableprops tablerowprops tablecellprops | tableinsertrowbefore tableinsertrowafter tabledeleterow | tableinsertcolbefore tableinsertcolafter tabledeletecol',
                toolbar_sticky: true,
                height: h,
                editor_css : '/NicerAppWebOS/3rd-party/tinymce-4/themes/charcoal/editor.na.css',
                skin_url : '/NicerAppWebOS/3rd-party/tinymce-4/themes/charcoal',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',

                content_css: [
                    '/NicerAppWebOS/3rd-party/tinymce-4/themes/charcoal/content.min.css?now='+now,
                    'https://fonts.googleapis.com/css?family=ABeeZee|Aclonica|Acme|Actor|Advent+Pro|Akronim|Alex+Brush|Architects+Daughter|Archivo+Black|Baloo|Bebas+Neue|Caveat|Chewy|Cookie|Cormorant|Courgette|Covered+By+Your+Grace|Dancing+Script|El+Messiri|Exo|Exo+2|Galada|Gloria+Hallelujah|Great+Vibes|Handlee|Indie+Flower|Kalam|Kaushan+Script|Khula|Knewave|Krona+One|Lacquer|Lemonada|Lusitana|M+PLUS+1p|Marck+Script|Merienda+One|Modak|Montserrat|Montserrat+Alternates|Mr+Dafoe|Nanum+Pen+Script|Noto+Serif+JP|Odibee+Sans|Oleo+Script|Orbitron|PT+Sans|Parisienne|Pathway+Gothic+One|Permanent+Marker|Playball|Pridi|Quattrocento+Sans|Rock+Salt|Sacramento|Saira+Condensed|Saira+Extra+Condensed|Saira+Semi+Condensed|Satisfy|Shadows+Into+Light|Shadows+Into+Light+Two|Sigmar+One|Signika+Negative|Slabo+27px|Source+Code+Pro|Special+Elite|Spectral|Spinnaker|Sriracha|Unica+One|Acme|Lato:300,300i,400,400i|Montserrat|Mukta+Malar|Ubuntu|Indie+Flower|Raleway|Pacifico|Fjalla+One|Work+Sans|Gloria+Hallelujah&display=swap',
                    '/NicerAppWebOS/3rd-party/tinymce-4/themes/charcoal/content.na.css?now='+now,
                    '/domainConfig/index.css?now='+now,
                    '/domainConfig/index.dark.css?now='+now,
                    '/domainConfig/index.textDecorations.dark.css?now='+now
                ],
                font_formats: 'ABeeZee=ABeeZee;Aclonica=Aclonica;Actor=Actor;Advent Pro=Advent Pro;Akronim=Akronim;Alex Brush=Alex Brush;Architects Daughter=Architects Daughter;Archivo Black=Archivo Black;Baloo=Baloo;Bebas Neue=Bebas Neue;Caveat=Caveat;Chewy=Chewy;Cookie=Cookie;Cormorant=Cormorant;Courgette=Courgette;Covered By Your Grace=Covered By Your Grace;Dancing Script=Dancing Script;El Messiri=El Messiri;Exo=Exo;Exo 2=Exo 2;Galada=Galada;Great Vibes=Great Vibes;Kalam=Kalam;Kaushan Script=Kaushan Script;Khula=Khula;Knewavel=Knewavel;Krona One=Krona One;Lacquer=Lacquer;Lemonada=Lemonada;Lusitana=Lusitana;M PLUS 1p=M PLUS 1p;Marck Script=Marck Script;Merienda One=Merienda One;Modak=Modak;Montserat Alternates=Montserrat Alternates;Mr Dafoe=Mr Dafoe;Nanum Pen Script=Nanum Pen Script;Noto Serif JP=Noto Serif JP;Odibee Sans=Odibee Sans;Oleo Script=Oleo Script;Orbitron=Orbitron;PT Sans=PT Sans;Parisienne=Parisienne;Pathway Gothic One=Pathway Gothic One;Permanent Marker=Permanent Marker;Playball=Playball;Pridi=Pridi;Quattrocento Sans=Quattrocento Sans;Rock Salt=Rock Salt;Sacramento=Sacramento;Saira Condensed=Saira Condensed;Saira Extra Condensed=Saira Extra Condensed;Saira Semi Condensed=Saira Semi Condensed;Satisfy=Satisfy;Shadows Into Light=Shadows Into Light;Shadows Into Light Two=Shadows Into Light Two;Sigmar Once=Sigmar One;Signika Negative=Signika Negative;Slabo 27px=Slabo 27px;Source Code Pro=Source Code Pro;Special Elite=Special Elite;Spectral=Spectral;Spinnaker=Spinnaker;Sriracha=Sriracha;Unica One=Unica One;Acme=Acme;Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Fjalla One=Fjalla One;Georgia=georgia,palatino;Gloria Hallelujah=Gloria Hallelujah;Helvetica=helvetica;Impact=impact,chicago;Indie Flower=Indie Flower;Montserrat=Montserrat;Mukta Malar=Mukta Malar;Pacifico=Pacifico;Raleway=Raleway;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Ubuntu=Ubuntu;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats;Work Sans=Work Sans',
                //link_list : na.cms.tinymce_link_list,
                relative_urls : false,
                init_instance_callback : function(editor) {
                    $(editor.editorContainer).addClass('fade-in');
                    $('#siteContent .lds-facebook').fadeOut('slow');

                    // rajmv, 2025-10-18 19:33CET: a big THANK YOU for
                    // https://stackoverflow.com/questions/33689336/tinymce-4-add-class-to-selected-element
                    // && https://stackoverflow.com/questions/36411839/proper-way-of-modifying-toolbar-after-init-in-tinymce
                    //add a button to the editor buttons
                        editor.addButton('mysecondbutton', {
                        text: 'S.T.',
                        tooltip : 'Add a semi-transparent background.',
                        icon: false,
                        onclick: function () {
                            var newContent =
                                "<span class='backdropped'>" + editor.selection.getContent() + "</span>";
                            editor.selection.setContent(newContent);
                        }
                    });

                    //the button now becomes
                    var button=editor.buttons['mysecondbutton'];

                        //find the buttongroup in the toolbar found in the panel of the theme
                    var bg=editor.theme.panel.find('toolbar buttongroup')[0];

                    //without this, the buttons look weird after that
                    bg._lastRepaintRect=bg._layoutRect;

                    //append the button to the group
                    bg.append(button);

                    tinymce.ready = true;
                }
            });

    },

    resize : function (el, parentEl) {
        //debugger;
        //alert ('onr');
        //na.m.waitForCondition ('na.cms.onresize : desktopIdle?', na.m.desktopIdle, function () {
            //alert ('onres1');
            //na.desktop.resize(function (t) {
                //if (!t) t = this;
                //if (t.id=='siteContent') {
        //debugger;
                    $(parentEl).css({overflow:''});
                    //alert ('onres');
                    /*
                    na.m.waitForCondition('na.cms.onresize() : tree node selected and rich text editor (tinymce) ready?',
                        function () {
                            var r = (
                                na.m.desktopIdle()
                                &&  na.cms.settings.current.selectedTreeNode
                                && (
                                    (
                                        na.cms.settings.current.selectedTreeNode.type == 'naDocument'
                                        && typeof $('#tinymce_ifr')[0] == 'object'
                                        && $('#tinymce_ifr').css('visibility')!=='hidden'
                                        && $('#tinymce_ifr').css('display')!=='none'
                                    )
                                    || na.cms.settings.current.selectedTreeNode.type == 'naMediaAlbum'
                                )
                            );
                            //debugger;
                            return r;
                        },
                        function () {
                            //if (na.cms.settings.current.activeDialog=='#siteContent')
                            switch (na.cms.settings.current.selectedTreeNode.type) {
                                case 'naDocument':
                                    let w = 0, d = $('#document').css('display');
                                    $('#document').css({display:'block'});
*/
                    /*
                                    if ($('#siteContent .vividDialogContent').width() < 400) {
                                        w += $('#url0').width() + 20;
                                        w += $('#url1_dropdown_selected').width() + 20;
                                        w += $('#url1-2').width() + 30;
                                        $('#url2_value').css({width: $('#siteContent .vividDialogContent').width() - w});
                                    } else {
                                        $('.navbar_button', $('#document_navBar')[0]).each(function(idx,el){
                                            w += $(el).width();
                                        });
                                        w += $('#nb_url0').width() + 20;
                                        w += $('#nb_url1_dropdown_selected').width() + 20;
                                        w += $('#nb_url1-2').width() + 20;
                                        w += $('#nb_url2_value').width() + 20;
                                        w += $('#nb_documentLabel_label').width() + 20;
                                        w += $('#nb_documentLabel_value').width() + 20;
                                        w += $('#nb_documentTitle_label').width();
                                        $('#documentTitle').css({
                                            width : jQuery('#siteContent .vividDialogContent').width() - w - 45
                                        });
                                    }
                        */
                                    var editorHeight = $(parentEl).height() - $('h1', parentEl).height() - 15;
                                    //$('#jsTree').css({ height : $('#siteToolbarLeft .vividDialogContent').height() - $('#jsTree_navBar').height() - 30 });
                                    var mce_bars_height = 0;
                                    //alert ($('#document_navBar').height());
                                    $('.mce-toolbar-grp, .mce-statusbar').each(function() { mce_bars_height += $(this).height(); });
                                    $('#document, .mce-tinymce, #'+el.id+'_ifr, .mce-edit-area', parentEl).css({
                                        width : $(parentEl).width() - 4,//'calc(100% - 4px)',
                                        height : $(parentEl).height() - $('.vividButton_icon_50x50', parentEl).height() - $('.mce-first').height() - 70
                                    });
                                    $('.mce-tinymce', parentEl).css({
                                        height : $(parentEl).height() - $('.mce-first').height() - $('.vividButton_icon_50x50', parentEl).height() - mce_bars_height
                                    });
                                    $('.mce-top-part, .mce-statusbar', parentEl).css({
                                        background : 'rgba(0,0,50,0.45)'
                                    });
                                    $('.mce-edit-area', parentEl).css ({
                                        background : 'rgba(0,0,0,0.8)'
                                    });
                                    $('.mce-first', parentEl).css({
                                        background : 'rgba(0,0,50,0.45)',
                                        color : 'white',
                                        fontWeight : 'bold',
                                        fontSize : 'large'
                                    });
                                    //$('#document').css({display:d});

                                    /*
                            if ($('#siteContent .vividDialogContent').width() < 400) {
                                $('#document_navBar .navbar_section').not('.shown').css({ display : 'none' });
                            }


/*
                            if ($('#btnTree').css('display')!=='none') {
                                var w = $('#document_navBar').width() - $('#btnTree').position().left - 60;
                                $('#nb_documentLabel').css({
                                    minWidth : 120,
                                    width : w,
                                    maxWidth : 300
                                });
                            } else {*/
/*
                                var w = $('#document_navBar').width() - $('#btnPublish').position().left - 60;
                                $('#nb_documentLabel').css({
                                    minWidth : 120,
                                    width : w,
                                    maxWidth : 300
                                });
                            //}

                            /*if ($('#nb_url0').position().left < $('#btnPublish').position().left) {
                                var w = $('#document_navBar').width() - $('#nb_url2_value').position().left - 10;
                                $('#nb_url2_value').css({
                                    minWidth : 120,
                                    width : w,
                                    maxWidth : 'none'
                                });
                            } else {
                                var w = $('#document_navBar').width() - $('#nb_documentTitle_label').width() - $('#nb_documentTitle').position().left - 10;
                                $('#nb_documentTitle').css({
                                    minWidth : 120,
                                    width : w,
                                    maxWidth : $('#nb_url2_value').position().left + $('#nb_url2_value').width() + 120
                                });
                            };*/
                            /*
                                var w = $('#document_navBar').width() - $('#nb_url2_value').position().left - 10;
                                $('#nb_url2_value').css({
                                    minWidth : 120,
                                    width : w,
                                    maxWidth : 300
                                });

                            if ($('#nb_documentTitle').position().left < $('#btnPublish').position().left) {
                                var w = $('#document_navBar').width() - $('#nb_documentTitle_label').width() - 10;
                                $('#nb_documentTitle').css({
                                    minWidth : 120,
                                    width : w,
                                    maxWidth : 'none'
                                });
                            } else {
                                var w = $('#document_navBar').width() - $('#nb_documentTitle_label').width() - $('#nb_documentTitle').position().left - 10;
                                $('#nb_documentTitle').css({
                                    minWidth : 120,
                                    width : w,
                                    maxWidth : $('#nb_url2_value').position().left + $('#nb_url2_value').width() + 120
                                });
                            };

                            na.site.startUIvisuals();
                            */
                            if (
                                typeof settings == 'object'
                                && typeof settings.callback == 'function'
                            ) settings.callback (settings);

                    //}, 50);
                //}
           // });
        //}, 100);

        /*
        if (
            typeof settings == 'object'
            && typeof settings.callback == 'function'
        ) settings.callback (settings);
        */
    }
}
