<?php
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');

global $naWebOS;
$cdb = $naWebOS->dbs->findConnection('couchdb')->cdb;
$view = $naWebOS->view;//json_decode (decode_base64_url($_GET['apps']), true);

$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);
/*if (
    $ip !== '::1'
    && $ip !== '127.0.0.1'
    && $ip !== '80.101.238.137'
) {
    header('HTTP/1.0 403 Forbidden');
    echo '403 - Access forbidden.';
    exit();
}*/


?>
<div class="lds-facebook"><!-- thanks for allowing CC0 license usage : https://loading.io/css/ --><div></div><div></div><div></div></div>
<!--
<link type="text/css" rel="StyleSheet" href="/domainConfig/<?php echo $naWebOS->domainFolder; ?>/index.css?c=<?php echo date('Ymd_His',filemtime(realpath(dirname(__FILE__).'/../../../../..').'/domainConfigs/'.$naWebOS->domainFolder.'/index.css'))?>">
<link type="text/css" rel="StyleSheet" href="/domainConfig/<?php echo $naWebOS->domainFolder; ?>/index.dark.css?c=<?php echo date('Ymd_His',filemtime(realpath(dirname(__FILE__).'/../../../../..').'/domainConfigs/'.$naWebOS->domainFolder.'/index.dark.css'))?>">
-->
<script type="text/javascript" src="/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/na.cms.source.js?c=<?php echo date('Ymd_His',filemtime(dirname(__FILE__).'/na.cms.source.js'));?>"></script>
<script type="text/javascript" src="/NicerAppWebOS/businessLogic/vividUserInterface/v5.y.z/photoAlbum/4.0.0/photoAlbum-4.0.0.source.js?c=<?php echo date('Ymd_His',filemtime($naWebOS->domainPath.'/NicerAppWebOS/businessLogic/vividUserInterface/v5.y.z/photoAlbum/4.0.0/photoAlbum-4.0.0.source.js'));?>"></script>

<script type="text/javascript" src="/NicerAppWebOS/3rd-party/tinymce-4.9.11/js/tinymce/tinymce.js"></script>
<script type="text/javascript" src="/NicerAppWebOS/3rd-party/tinymce-4.9.11/js/tinymce/jquery.tinymce.min.js"></script>
<!--<script src="https://cdn.tiny.cloud/1/89d73yohz5ameo5exzlj9d6kya9vij9mt8f5ipzzqjo0wkw5/tinymce/4/tinymce.min.js" referrerpolicy="origin"></script>-->
<script type="text/javascript">
    //na.m.waitForCondition ('page loaded?', function() {
      //  return na.site && na.m.desktopIdle();
    //}, function() {
    $(document).ready(function() {
        na.desktop.settings.visibleDivs.push('#siteToolbarLeft');
        //if ($(window).width()>800) na.desktop.settings.visibleDivs.push('#siteToolbarRight');
        setTimeout (function () {
            $('#menu__btnAddUser_menu').css({display:'none'});
        }, 20);
    });
    //}, 100);
</script>

<div id="document_headers" class="vividDialogPopup">
    <div class="sections">
        <div class="navbar_section">
                <input id="documentLabel" type="text" onchange="na.cms.onchange_documentHeaders(event);na.cms.refresh();"></input>
                <!--<label id="url1_label" for="documentTitle">SEO-1, URL-1</label>-->
        </div>
        <div class="navbar_section">
                <span id="url0" class="vividTheme_input_urls" style="width:auto;display:block;"></span>
                <select id="url1_select" class="select themeEditor mainBar_forThemeEditor" onchange="na.cms.onchange_documentHeaders(event);" style="order:2;display:none;">
                    <option id="url1_select__in" name="url1_select__in" value="in">in</option>
                    <option id="url1_select__on" name="url1_select__on" value="on">on</option>
                    <option id="url1_select__during" name="url1_select__during" value="during">during</option>
                </select>
                <div id="url1_dropdown" class="vividDropDownBox" style="display:inline-block;width:auto;vertical-align:top;"></div>
                <!--<label id="url2_label" for="documentTitle">SEO-2, URL-2</label>-->
                <span id="url1-2" class="vividTheme_input_urls" style="padding-left:10px !important;padding-right:10px !important;">/</span>
                <input id="url2_value" type="text" onchange="na.cms.onchange_documentHeaders(event);"></input>
        </div>
        <div class="navbar_section">
                <label id="documentTitle_label" for="documentTitle">Title</label>
                <input id="documentTitle" type="text" onchange="na.cms.onchange_documentHeaders(event);" style="width:inherit"></input>
        </div>
    </div>
</div>
<div id="folder" class="naVividTabPage"></div>
<div id="upload" class="naVividTabPage">
    <div id="mediaFolder_navBar" class="navbar" style="display:flex;justify-items:center">
        <img id="btnUpload" class="navbar_button" src="/siteMedia/btnInsertMedia2.png" onclick="na.cms.onclick_btnUpload(event);"/>
        <img id="btnViewMedia" class="navbar_button" src="/siteMedia/btnViewMedia.png" onclick="na.cms.onclick_btnViewMedia(event);"/>
        <input id="mediaFolderLabel" class="vividTheme_input_urls" type="text" onchange="na.cms.onchange_mediaFolderLabel(event);"></input>
    </div>
    <iframe id="jQueryFileUpload" class="jQueryFileUpload" src="" style="overflow:hidden;width:100%;height:100%" allowtransparency="true"></iframe>
</div>
<div id="document" class="naVividTabPage" style="height:calc(100% - 70px)">
    <div id="document_navBar" class="navbar" style="display:flex">
        <div class="sections" style="display:flex;">
            <div class="navbar_section navbarTheme_navy" style="display:flex;">
<?php
    global $naWebOS;
    echo $naWebOS->html_vividButton (
        1, 'width:100%;height:50px;',

        'btnAddUser',
        'vividButton_icon_50x50 grouped navbar_button', '_50x50', 'grouped',
        '',
        'tinymce.activeEditor.execCommand(\'mceLink\');',
        '',
        '',

        421, 'Add user',

        'btnCssVividButton_outerBorder.png',
        null,//'btnCssVividButton.yellow4a.png',
        null,//'btnCssVividButton_iconBackground.png',
        'btnInsertLink.png',//.$naWebOS->dbs->userIDicon,

        '',

        null, null, null
    );
    echo $naWebOS->html_vividButton (
        1, 'width:100%;height:50px;',

        'btnInsertMedia',
        'vividButton_icon_50x50 grouped navbar_button', '_50x50', 'grouped',
        '',
        'na.cms.onclick_insertMedia()',
        '',
        '',

        422, 'Insert media',

        'btnCssVividButton_outerBorder.png',
        null,//'btnCssVividButton.yellow4a.png',
        null,//'btnCssVividButton_iconBackground.png',
        'btnInsertMedia.png',//.$naWebOS->dbs->userIDicon,

        '',

        null, null, null
    );
    echo $naWebOS->html_vividButton (
        1, 'width:100%;height:50px;',

        'btnInsertPageBackground',
        'vividButton_icon_50x50 grouped navbar_button', '_50x50', 'grouped',
        '',
        'alert(\'To be implemented later.\');',
        '',
        '',

        423, 'Add page background',

        'btnCssVividButton_outerBorder.png',
        null,//'btnCssVividButton.yellow4a.png',
        null,//'btnCssVividButton_iconBackground.png',
        'btnInsertPageBackground.png',//.$naWebOS->dbs->userIDicon,

        '',

        null, null, null
    );

?>
<!--
                    <img id="btnInsertLink" class="navbar_button" src="/siteMedia/btnInsertLink.png" onclick="tinymce.activeEditor.execCommand('mceLink');"/>
                    <img id="btnInsertMedia" class="navbar_button" src="/siteMedia/btnInsertMedia.png" onclick="na.cms.onclick_insertMedia()"/>
                    <img id="btnInsertPageBackground" class="navbar_button" src="/siteMedia/btnInsertPageBackground.png"/>
-->
            </div>
            <div class="navbar_section navbarTheme_navy" style="display:flex;">
<?php
    global $naWebOS;
    echo $naWebOS->html_vividButton (
        1, '',

        'btnPublish',
        'vividButton_icon_50x50 grouped navbar_button', '_50x50', 'grouped',
        '',
        'na.cms.onclick_publish(event);',
        '',
        '',

        431, 'Publish',

        'btnCssVividButton_outerBorder.png',
        null,//'btnCssVividButton.yellow4a.png',
        null,//'btnCssVividButton_iconBackground.png',
        'iconPublish.png',//.$naWebOS->dbs->userIDicon,

        '',

        null, null, null
    );
    /*
    echo $naWebOS->html_vividButton (
        1, '',

        'btnSettingsHeaders',
        'vividButton_icon_50x50 grouped navbar_button', '_50x50', 'grouped',
        '',
        'na.cms.onclick_editHeaders(event);',
        '',
        '',

        432, 'Edit headers',

        'btnCssVividButton_outerBorder.png',
        null,//'btnCssVividButton.yellow4a.png',
        null,//'btnCssVividButton_iconBackground.png',
        'btnSettings2.png',//.$naWebOS->dbs->userIDicon,

        '',

        null, null, null
    );
    echo $naWebOS->html_vividButton (
        1, '',

        'btnTree',
        'vividButton_icon_50x50 grouped navbar_button', '_50x50', 'grouped',
        '',
        'na.cms.onclick_btnTree(event);',
        '',
        '',

        433, 'Tree',

        'btnCssVividButton_outerBorder.png',
        null,//'btnCssVividButton.yellow4a.png',
        null,//'btnCssVividButton_iconBackground.png',
        'btnTree.png',//.$naWebOS->dbs->userIDicon,

        '',

        null, null, null
    );
    */

?>

            <!--
                    <img id="btnPublish" class="navbar_button" src="/siteMedia/iconPublish.png" onclick="na.cms.onclick_publish(event);"/>
                    <img id="btnSettingsHeaders" class="navbar_button" src="/siteMedia/btnSettings2a.png" onclick="na.cms.onclick_editHeaders(event);"/>
                    <img id="btnTree" class="navbar_button" src="/siteMedia/btnTree.png" onclick="na.cms.onclick_btnTree(event);"/>
            -->
            </div>
            <div class="navbar_section">
                    <!--<label id="documentLabel_label" for="documentLabel">Label</label>-->
                    <input id="nb_documentLabel" type="text" onchange="na.cms.onchange_documentHeaders(event);na.cms.refresh();"></input>
            </div>
            <div class="navbar_section">
                    <span id="nb_url0" class="vividTheme_input_urls" style="width:auto;"></span>
                    <!--<label id="url1_label" for="documentTitle">SEO-1, URL-1</label>-->
                    <select id="nb_url1_select" class="select themeEditor mainBar_forThemeEditor" onchange="na.cms.onchange_documentHeaders(event);" style="order:2;display:none;">
                        <option id="nb_url1_select__about" name="url1_select__about" value="about">about</option>
                        <option id="nb_url1_select__in" name="url1_select__in" value="in">in</option>
                        <option id="nb_url1_select__on" name="url1_select__on" value="on">on</option>
                        <option id="nb_url1_select__during" name="url1_select__during" value="during">during</option>
                    </select>
                    <div id="nb_url1_dropdown" class="vividDropDownBox" style="display:inline-block;width:auto;">
                        <div class="vividDropDownBox_selected">on</div>
                        <div class="vividDropDownBox_selector">in</div>
                        <div class="vividDropDownBox_selector">during</div>
                    </div>
                    <!--<label id="url2_label" for="documentTitle">SEO-2, URL-2</label>-->
                    <span id="nb_url1-2" class="vividTheme_input_urls" style="padding-left:10px !important;padding-right:10px !important;">/</span>
                    <input id="nb_url2_value" type="text" onchange="na.cms.onchange_documentHeaders(event);"></input>
            </div>
            <div class="navbar_section">
                    <label id="nb_documentTitle_label" for="documentTitle">Title</label>
                    <input id="nb_documentTitle" type="text" onchange="na.cms.onchange_documentHeaders(event);" style="width:inherit"></input>
            </div>
        </div>
    </div>
    <div id="tinymce_div" style="height:calc(100% - 70px);">
        <textarea id="tinymce1" onchange="na.cms.saveEditorContent();"></textarea>
    </div>

    <script type="text/javascript">
    //document.addEventListener('DOMContentLoaded', () => {
    //setTimeout (function() {
        //$(document).ready(function() {
        debugger;
        na.m.waitForCondition('cmsManager/app.dialog.siteContent.php::waitForCondition() : na.desktop.settings.animating?', function () {
            var r = (
                na
                && na.site
                && na.site.settings.c
                && na.site.settings.c.booted
                && na.desktop
                && !na.desktop.settings.animating
            );
            return r;
        }, function () {
            debugger;
            $('#btnAddUser_menu').css({display:'none'});
        /*
        TINYMCE BUGREPORT :
        This cdn.tiny.cloud page can’t be found
        No webpage was found for the web address: https://cdn.tiny.cloud/1/no-origin/tinymce/4.9.11-104/tinymce/themes/modern/theme.min.js
        HTTP ERROR 404
        */

            var
            now = (new Date().getTime()),
            h = $('#siteContent .vividDialogContent').height() - $('#document_navBar').height(),
            useDarkMode = true;
            tinymce.ready = false;
            //NOT NEEDED WHEN USING CLOUD VERSION (valid though) : tinymce.baseURL = 'https://cdn.tiny.cloud/1/89d73yohz5ameo5exzlj9d6kya9vij9mt8f5ipzzqjo0wkw5/tinymce/4';
            tinymce.baseURL = '/NicerAppWebOS/3rd-party/tinymce-4.9.11/js/tinymce';

            tinymce.suffix = '';//'.min';
            $('#tinymce1').tinymce({
                suffix : '',
                selector: 'textarea#tinymce1',
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
                link_list : na.cms.tinymce_link_list,
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
                            debugger;
                            var newContent =
                                "<span class='backdropped'>" + editor.selection.getContent() + "</span>";
                            editor.selection.setContent(newContent);
                        }
                    });

                    setTimeout (function() {
                        $('#mceu_8-open').click (function(evt) {
                            var x = document.querySelectorAll('.mce-floatpanel, .mce-menu');
                            debugger;
                        })
                    },2000);

                    //the button now becomes
                    var button=editor.buttons['mysecondbutton'];

                        //find the buttongroup in the toolbar found in the panel of the theme
                    var bg=editor.theme.panel.find('toolbar buttongroup')[0];

                    //without this, the buttons look weird after that
                    bg._lastRepaintRect=bg._layoutRect;

                    //append the button to the group
                    bg.append(button);

                    debugger;
                    tinymce.ready = true;
                }
            });
        }, 100);
    //}, 500);
    </script>
</div>
<div id="user" class="naVividTabPage">

</div>
<div id="group" class="naVividTabPage">
</div>
