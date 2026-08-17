<?php 
require_once (realpath(dirname(__FILE__).'/../../..').'/functions.php');

$rootURL = 'http://localhost';

if (array_key_exists('inputJSONurl', $_REQUEST)) {
    $inputJSONurl = $_REQUEST['inputJSONurl'];
} else {
    $inputJSONurl = '/siteMedia/backgrounds';
}

if (array_key_exists('apps', $_GET)) {
    $views = $_GET['apps'];
} else {
    $views = encode_base64_url(json_encode([
        'app.3D.fileExplorer' => [
            'inputJSONurl' => $inputJSONurl,
            'thumbnails' => './thumbs/$filename.$ext'
        ]
    ]));
}

$view = json_decode (decode_base64_url($views), true);

$theme = '{$theme}';
if ($theme === '{$theme}') $theme = 'dark';


?>
<div id="site3D_backgroundsBrowser" class="na3D" theme="<?php echo $theme;?>">
</div>
<div id="site3D_label" class="vividDialog vividScrollpane" theme="<?php echo $theme;?>"></div>
<script type="text/j    avascript" src="<?php echo $rootURL;?>/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.js"></script>
<script type="module">
import {
  AmbientLight,
  AnimationMixer,
  AxesHelper,
  Box3,
  Cache,
  CubeTextureLoader,
  DirectionalLight,
  GridHelper,
  HemisphereLight,
  LinearEncoding,
  LoaderUtils,
  LoadingManager,
  PMREMGenerator,
  PerspectiveCamera,
  RGBFormat,
  Scene,
  SkeletonHelper,
  UnsignedByteType,
  Vector3,
  WebGLRenderer,
  sRGBEncoding,
} from '<?php echo $rootURL;?>/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.module.js';
import Stats from '<?php echo $rootURL;?>/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/libs/stats.module.js';
import { GLTFLoader } from '<?php echo $rootURL;?>/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/GLTFLoader.js';
import { KTX2Loader } from '<?php echo $rootURL;?>/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/KTX2Loader.js';
import { DRACOLoader } from '<?php echo $rootURL;?>/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/DRACOLoader.js';
import { OrbitControls } from '<?php echo $rootURL;?>/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/OrbitControls-noPreventDefault.js';
import { RGBELoader } from '<?php echo $rootURL;?>/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/RGBELoader.js';
import { DragControls } from '<?php echo $rootURL;?>/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/DragControls.js';
    
    import { na3D_fileBrowser, na3D_demo_models, na3D_demo_cube } 
		from '<?php echo $rootURL;?>/NicerAppWebOS/logic.userInterface/na3D.source.js?c=<?php echo date('Ymd_His',filemtime(dirname(__FILE__).'/../../../userInterface/na3D.source.js'));?>';
		
    //$(document).ready(function() {
        na.m.waitForCondition ('Now loading filesystem index',
            function () { 
				return 
					na.site.settings.menus['#siteMenu'] 
					&& na.site.settings.backgroundsRecursive 
					&& typeof THREE !== 'undefined'; },
					// ^-- wait for this to become non-null, non-undefined, aka filled with data fetched in 
					// /NicerAppWebOS/site.source.js:::na.site.onload() to /domainConfig/SOME_NAME/ajax_backgrounds.php
            function () {
                na.desktop.setConfig ('contentAndToolbarRight');

                // and when the data has loaded, do the following.. :
                $('.na3D').each(function(idx,el){
                    //na.site.settings.na3D['#'+el.id] = new na3D_demo_cube (el, $(el).parent()[0]);
                    //na.site.settings.na3D['#'+el.id] = new na3D_demo_models (el, $(el).parent()[0]);
                    na.site.settings.na3D['#'+el.id] = new na3D_fileBrowser(el, $(el).parent()[0], na.site.settings.backgroundsRecursive);
                    
                });
                
                var 
                html = 
                    '<h2 id="titleHowTo">How To</h2>'
                    + '<p>Drag and drop folders to re-arrange them.<br/>'
                   // + 'Your folder-arrangement will be saved for your next visit to this page.</p>'
                    + '<p>Hold the left mouse button and drag to manually rotate the data.</p>'
                    + '<p>Hold the right mouse button and drag to manually pan the camera.</p>'
                    + '<p>Double click a mouse button to toggle automatic rotation of the data.</p>'
                    + '<p>Triple click a mouse button to reverse direction for automatic rotation of the data.</p>'
                    + '<p>Use the mouse wheel to zoom in or out.</p>'
                    + '<div id="threedfm_buttons">'
                    + '<div id="newColors" class="vividButton" theme="dark" style="position:relative;" onclick="for (var id in na.site.settings.na3D) { var td = na.site.settings.na3D[id]; td.useNewColors(); }">New Colors</div>'
                    + '<div id="newArrangement" class="vividButton" theme="dark" style="position:relative;" onclick="for (var id in na.site.settings.na3D) { var td = na.site.settings.na3D[id]; td.useNewArrangement(); }">New Arrangement</div>'
                    + '<div id="autoRotate" class="vividButtonSelected" theme="dark" style="position:relative;" onclick="for (var id in na.site.settings.na3D) { var td = na.site.settings.na3D[id]; td.toggleAutoRotate(); }">Auto-rotate</div>'
                    +'<div id="showLines" class="vividButtonSelected" theme="dark" style="position:relative;" onclick="for (var id in na.site.settings.na3D) { var td = na.site.settings.na3D[id]; td.toggleShowLines(); }">Show Lines</div>'
                    +'</div>';
                
                $('#siteToolbarRight .vividDialogContent').html (html);
                na.site.settings.buttons['#newColors'] = new naVividButton ($('#newColors')[0]);
                
                var vividTextCmd = {
                        el : jQuery('#titleHowTo')[0],
                        theme : na.cg.themes.saColorgradientSchemeOrangeYellow, 
                        animationType : na.vividText.globals.animationTypes[0],
                        animationSpeed : 4 * 1000
                };
                na.vividText.initElement (vividTextCmd);
                
            }, 100
        );
    //});
</script>
