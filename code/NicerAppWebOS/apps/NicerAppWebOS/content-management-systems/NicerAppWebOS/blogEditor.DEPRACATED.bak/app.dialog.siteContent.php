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
<script type="text/javascript">
    debugger;
</script>
<div class="lds-facebook"><!-- thanks for allowing CC0 license usage : https://loading.io/css/ --><div></div><div></div><div></div></div> 
<link type="text/css" rel="StyleSheet" href="/domainConfig/<?php echo $naWebOS->domainFolder; ?>/index.css?c=<?php echo date('Ymd_His',filemtime(realpath(dirname(__FILE__).'/../../../../..').'/domainConfigs/'.$naWebOS->domainFolder.'/index.css'))?>">
<link type="text/css" rel="StyleSheet" href="/domainConfig/<?php echo $naWebOS->domainFolder; ?>/index.dark.css?c=<?php echo date('Ymd_His',filemtime(realpath(dirname(__FILE__).'/../../../../..').'/domainConfigs/'.$naWebOS->domainFolder.'/index.dark.css'))?>">
<script type="text/javascript" src="/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/blogEditor/na.blog.source.js?c=<?php echo date('Ymd_His',filemtime(dirname(__FILE__).'/na.blog.source.js'));?>"></script>
<script type="text/javascript" src="/NicerAppWebOS/logic.userInterface/photoAlbum/4.0.0/photoAlbum-4.0.0.source.js?c=<?php echo date('Ymd_His',filemtime($naWebOS->codePath.'/NicerAppWebOS/logic.userInterface/photoAlbum/4.0.0/photoAlbum-4.0.0.source.js'));?>"></script>

<script type="text/javascript" src="/NicerAppWebOS/3rd-party/tinymce-4.9.11/js/tinymce/tinymce.min.js"></script>
<!--<script src="https://cdn.tiny.cloud/1/89d73yohz5ameo5exzlj9d6kya9vij9mt8f5ipzzqjo0wkw5/tinymce/4/tinymce.min.js" referrerpolicy="origin"></script>-->

<div id="document_headers" class="vividDialogPopup">
    <div class="sections">
        <div class="navbar_section">
                <input id="documentLabel" type="text" onchange="na.blog.onchange_documentHeaders(event);na.blog.refresh();"></input>
                <!--<label id="url1_label" for="documentTitle">SEO-1, URL-1</label>-->
        </div>
        <div class="navbar_section">
                <span id="url0" class="vividTheme_input_urls" style="width:auto;"></span>
                <select id="url1_select" class="select themeEditor mainBar_forThemeEditor" onchange="na.blog.onchange_documentHeaders(event);" style="order:2;display:none;">
                    <option id="url1_select__in" name="url1_select__in" value="in">in</option>
                    <option id="url1_select__on" name="url1_select__on" value="on">on</option>
                    <option id="url1_select__during" name="url1_select__during" value="during">during</option>
                </select>
                <div id="url1_dropdown" class="vividDropDownBox" style="display:inline-block;width:auto;vertical-align:top;"></div>
                <!--<label id="url2_label" for="documentTitle">SEO-2, URL-2</label>-->
                <span id="url1-2" class="vividTheme_input_urls" style="padding-left:10px !important;padding-right:10px !important;">/</span>
                <input id="url2_value" type="text" onchange="na.blog.onchange_documentHeaders(event);"></input>
        </div>
        <div class="navbar_section">
                <label id="documentTitle_label" for="documentTitle">Title</label>
                <input id="documentTitle" type="text" onchange="na.blog.onchange_documentHeaders(event);" style="width:inherit"></input>
        </div>
    </div>
</div>
<div id="folder"></div>
<div id="upload">
    <div id="mediaFolder_navBar" class="navbar" style="display:flex;justify-items:center">
        <img id="btnUpload" class="navbar_button" src="/siteMedia/btnInsertMedia2.png" onclick="na.blog.onclick_btnUpload(event);"/>
        <img id="btnViewMedia" class="navbar_button" src="/siteMedia/btnViewMedia.png" onclick="na.blog.onclick_btnViewMedia(event);"/>
        <input id="mediaFolderLabel" class="vividTheme_input_urls" type="text" onchange="na.blog.onchange_mediaFolderLabel(event);"></input>
    </div>
    <iframe id="jQueryFileUpload" class="jQueryFileUpload" src="" style="overflow:hidden;width:100%;height:100%" allowtransparency="true"></iframe>
</div>
<div id="document">
    <div id="document_navBar" class="navbar" style="display:flex">
        <div class="sections">
            <div class="navbar_section shown">
                    <img id="btnInsertLink" class="navbar_button" src="/siteMedia/btnInsertLink.png" onclick="tinymce.activeEditor.execCommand('mceLink');"/>
                    <img id="btnInsertMedia" class="navbar_button" src="/siteMedia/btnInsertMedia.png" onclick="na.blog.onclick_insertMedia()"/>
                    <img id="btnInsertPageBackground" class="navbar_button" src="/siteMedia/btnInsertPageBackground.png"/>
                    <img id="btnPublish" class="navbar_button" src="/siteMedia/iconPublish.png" onclick="na.blog.onclick_publish(event);"/>
                    <img id="btnSettingsHeaders" class="navbar_button" src="/siteMedia/btnSettingsHeaders.png" onclick="na.blog.onclick_editHeaders(event);"/>
                    <img id="btnTree" class="navbar_button" src="/siteMedia/btnTree.png" onclick="na.blog.onclick_btnTree(event);"/>
            </div>
            <div class="navbar_section">
                    <!--<label id="documentLabel_label" for="documentLabel">Label</label>-->
                    <input id="nb_documentLabel" type="text" onchange="na.blog.onchange_documentHeaders(event);na.blog.refresh();"></input>
            </div>
            <div class="navbar_section">
                    <span id="nb_url0" class="vividTheme_input_urls" style="width:auto;"></span>
                    <!--<label id="url1_label" for="documentTitle">SEO-1, URL-1</label>-->
                    <select id="nb_url1_select" class="select themeEditor mainBar_forThemeEditor" onchange="na.blog.onchange_documentHeaders(event);" style="order:2;display:none;">
                        <option id="nb_url1_select__in" name="url1_select__in" value="in">in</option>
                        <option id="nb_url1_select__on" name="url1_select__on" value="on">on</option>
                        <option id="nb_url1_select__during" name="url1_select__during" value="during">during</option>
                    </select>
                    <div id="nb_url1_dropdown" class="vividDropDownBox" style="display:inline-block;width:auto;vertical-align:top;"></div>
                    <!--<label id="url2_label" for="documentTitle">SEO-2, URL-2</label>-->
                    <span id="nb_url1-2" class="vividTheme_input_urls" style="padding-left:10px !important;padding-right:10px !important;">/</span>
                    <input id="nb_url2_value" type="text" onchange="na.blog.onchange_documentHeaders(event);"></input>
            </div>
            <div class="navbar_section">
                    <label id="nb_documentTitle_label" for="documentTitle">Title</label>
                    <input id="nb_documentTitle" type="text" onchange="na.blog.onchange_documentHeaders(event);" style="width:inherit"></input>
            </div>
        </div>
    </div>
    <textarea id="tinymce" onchange="na.blog.saveEditorContent();"></textarea>

    <script type="text/javascript">
    //document.addEventListener('DOMContentLoaded', () => {
    setTimeout (function() {
        //$(document).ready(function() {
        
        /*
        TINYMCE BUGREPORT : 
        This cdn.tiny.cloud page can’t be foundNo webpage was found for the web address: https://cdn.tiny.cloud/1/no-origin/tinymce/4.9.11-104/tinymce/themes/modern/theme.min.js
HTTP ERROR 404
        */
        
            var useDarkMode = true;
            tinymce.ready = false;
            //NOT NEEDED WHEN USING CLOUD VERSION (valid though) : tinymce.baseURL = 'https://cdn.tiny.cloud/1/89d73yohz5ameo5exzlj9d6kya9vij9mt8f5ipzzqjo0wkw5/tinymce/4';
            tinymce.baseURL = '/NicerAppWebOS/3rd-party/tinymce-4.9.11/js/tinymce';
            
            tinymce.suffix = '.min';
            tinymce.init({
                selector: 'textarea#tinymce',
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor textcolor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste code help'
                ],
                external_plugins : {
                    'emoticons' : '/NicerAppWebOS/3rd-party/tinymce-4/plugins/naEmoticons/plugin.min.js'
                },
                resize : true,
                menubar: false,//'file edit view insert format tools table help',
                toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect | formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
                toolbar_sticky: true,
                height: $('#siteContent .vividDialogContent').height() - $('#document_navBar').height(),
                editor_css : '/NicerAppWebOS/3rd-party/tinymce-4/themes/charcoal/editor.na.css',
                skin_url : '/NicerAppWebOS/3rd-party/tinymce-4/themes/charcoal',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                
                content_css: [
                    '/NicerAppWebOS/3rd-party/tinymce-4/themes/charcoal/content.min.css?c='+(new Date().getTime()),
                    'https://fonts.googleapis.com/css?family=ABeeZee|Aclonica|Acme|Actor|Advent+Pro|Akronim|Alex+Brush|Architects+Daughter|Archivo+Black|Baloo|Bebas+Neue|Caveat|Chewy|Cookie|Cormorant|Courgette|Covered+By+Your+Grace|Dancing+Script|El+Messiri|Exo|Exo+2|Galada|Gloria+Hallelujah|Great+Vibes|Handlee|Indie+Flower|Kalam|Kaushan+Script|Khula|Knewave|Krona+One|Lacquer|Lemonada|Lusitana|M+PLUS+1p|Marck+Script|Merienda+One|Modak|Montserrat|Montserrat+Alternates|Mr+Dafoe|Nanum+Pen+Script|Noto+Serif+JP|Odibee+Sans|Oleo+Script|Orbitron|PT+Sans|Parisienne|Pathway+Gothic+One|Permanent+Marker|Playball|Pridi|Quattrocento+Sans|Rock+Salt|Sacramento|Saira+Condensed|Saira+Extra+Condensed|Saira+Semi+Condensed|Satisfy|Shadows+Into+Light|Shadows+Into+Light+Two|Sigmar+One|Signika+Negative|Slabo+27px|Source+Code+Pro|Special+Elite|Spectral|Spinnaker|Sriracha|Unica+One|Acme|Lato:300,300i,400,400i|Montserrat|Mukta+Malar|Ubuntu|Indie+Flower|Raleway|Pacifico|Fjalla+One|Work+Sans|Gloria+Hallelujah&display=swap',
                    '/NicerAppWebOS/3rd-party/tinymce-4/themes/charcoal/content.na.css?c='+(new Date().getTime()),
                    '/domainConfig/'+na.site.globals.domain+'/index.css?c='+(new Date().getTime()),
                    '/domainConfig/'+na.site.globals.domain+'/index.dark.css?c='+(new Date().getTime())
                ],
                font_formats: 'ABeeZee=ABeeZee;Aclonica=Aclonica;Actor=Actor;Advent Pro=Advent Pro;Akronim=Akronim;Alex Brush=Alex Brush;Architects Daughter=Architects Daughter;Archivo Black=Archivo Black;Baloo=Baloo;Bebas Neue=Bebas Neue;Caveat=Caveat;Chewy=Chewy;Cookie=Cookie;Cormorant=Cormorant;Courgette=Courgette;Covered By Your Grace=Covered By Your Grace;Dancing Script=Dancing Script;El Messiri=El Messiri;Exo=Exo;Exo 2=Exo 2;Galada=Galada;Great Vibes=Great Vibes;Kalam=Kalam;Kaushan Script=Kaushan Script;Khula=Khula;Knewavel=Knewavel;Krona One=Krona One;Lacquer=Lacquer;Lemonada=Lemonada;Lusitana=Lusitana;M PLUS 1p=M PLUS 1p;Marck Script=Marck Script;Merienda One=Merienda One;Modak=Modak;Montserat Alternates=Montserrat Alternates;Mr Dafoe=Mr Dafoe;Nanum Pen Script=Nanum Pen Script;Noto Serif JP=Noto Serif JP;Odibee Sans=Odibee Sans;Oleo Script=Oleo Script;Orbitron=Orbitron;PT Sans=PT Sans;Parisienne=Parisienne;Pathway Gothic One=Pathway Gothic One;Permanent Marker=Permanent Marker;Playball=Playball;Pridi=Pridi;Quattrocento Sans=Quattrocento Sans;Rock Salt=Rock Salt;Sacramento=Sacramento;Saira Condensed=Saira Condensed;Saira Extra Condensed=Saira Extra Condensed;Saira Semi Condensed=Saira Semi Condensed;Satisfy=Satisfy;Shadows Into Light=Shadows Into Light;Shadows Into Light Two=Shadows Into Light Two;Sigmar Once=Sigmar One;Signika Negative=Signika Negative;Slabo 27px=Slabo 27px;Source Code Pro=Source Code Pro;Special Elite=Special Elite;Spectral=Spectral;Spinnaker=Spinnaker;Sriracha=Sriracha;Unica One=Unica One;Acme=Acme;Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Fjalla One=Fjalla One;Georgia=georgia,palatino;Gloria Hallelujah=Gloria Hallelujah;Helvetica=helvetica;Impact=impact,chicago;Indie Flower=Indie Flower;Montserrat=Montserrat;Mukta Malar=Mukta Malar;Pacifico=Pacifico;Raleway=Raleway;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Ubuntu=Ubuntu;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats;Work Sans=Work Sans',
                link_list : na.blog.tinymce_link_list,
                relative_urls : false,
                init_instance_callback : function(editor) {
                    $(editor.editorContainer).addClass('fade-in'); 
                    $('#siteContent .lds-facebook').fadeOut('slow');
                    tinymce.ready = true;   
                }
            });
       // });
    }, 500);
    </script>
</div>
