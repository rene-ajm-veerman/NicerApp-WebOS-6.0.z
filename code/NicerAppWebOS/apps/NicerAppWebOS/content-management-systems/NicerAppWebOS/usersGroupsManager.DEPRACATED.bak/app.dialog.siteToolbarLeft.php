<?php 
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
<link rel="stylesheet" href="/NicerAppWebOS/3rd-party/jsTree-3.3.15/dist/themes/default/style.css" /> <!-- has style.min.css -->
<div id="jsTree_navBar">
    <img id="jsTree_addFolder" class="jsTree_navBar_button tooltip" title="Create new sub-folder" src="/siteMedia/btnAddFolder.png" onclick="na.blog.onclick_addFolder();"/>
    <img id="jsTree_addDocument" class="jsTree_navBar_button tooltip" title="Create new document in current folder" src="/siteMedia/btnAddDocument.png" onclick="na.blog.onclick_addDocument();"/>
    <img id="jsTree_addMediaAlbum" class="jsTree_navBar_button tooltip" title="Create new media album in current folder" src="/siteMedia/btnAddMediaFolder.png" onclick="na.blog.onclick_addMediaAlbum();"/>
    <img id="jsTree_delete" class="jsTree_navBar_button tooltip" title="Delete" src="/siteMedia/btnTrashcan.png" onclick="na.blog.onclick_delete();"/>
</div>
<div id="jsTree"></div>
<script type="text/javascript">
    $(document).ready(function() {
    setTimeout (function () {
        na.desktop.settings.visibleDivs.push('#siteToolbarLeft');
        //na.site.onresize();
        //na.blog.onload();
    }, 150);
    });
</script>

