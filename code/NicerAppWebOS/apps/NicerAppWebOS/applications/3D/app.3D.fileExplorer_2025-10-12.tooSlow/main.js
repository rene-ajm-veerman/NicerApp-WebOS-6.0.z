/*import {
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
  RGBAFormat,
  Scene,
  SkeletonHelper,
  UnsignedByteType,
  Vector3,
  WebGLRenderer,
  sRGBEncoding

} from '/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.module.js';
*/
import * as three from '/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.module.js';
import * as THREE from '/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.module.js';
import { Stats } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/libs/stats.module.js';
import { GLTFLoader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/GLTFLoader.js';
import { KTX2Loader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/KTX2Loader.js';
import { DRACOLoader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/DRACOLoader.js';
import { OrbitControls } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/OrbitControls.js';
import { RGBELoader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/RGBELoader.js';
import { DragControls } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/DragControls.js';
    
    import { na3D_fileBrowser }
		from '/NicerAppWebOS/logic.vividUserInterface/v6.y.z/3D/na3D.js';
		
    //$(document).ready(function() {
        setTimeout (function() {

        delete na.site.settings.loadingApps;

        na.m.waitForCondition ('app.3D.fileExplorer : Filesystem index loaded?',
            function () { 
				var r =
					typeof na.site.c.menus['#siteMenu'] !== 'undefined'
					//&& typeof na.site.settings.backgroundsRecursive !== 'undefined'
                    //&& typeof na.site.settings.na3D['#app_3D_fileExplorer'] !== 'undefined'
                    //&& typeof na.site.settings.na3D['#app_3D_fileExplorer'].settings.parameters !== 'undefined'
					&& typeof THREE !== 'undefined';
					// ^-- wait for this to become non-null, non-undefined, aka filled with data fetched in 
					// /NicerAppWebOS/site.source.js:::na.site.onload() to /domainConfig/SOME_NAME/ajax_backgrounds.php
                return r;
            },
            function () {

                //sna.desktop.setConfig ('contentAndToolbarRight');

                // and when the data has loaded, do the following.. :
                na.site.closeAll_3D_apps();

                var
                fncn = '/NicerAppWebOS/apps/NicerAppWebOS/applications/3D/app.3D.fileExplorer/main.js::Filesystem index loaded = true',
                url = '/NicerAppWebOS/apps/NicerAppWebOS/applications/3D/app.3D.fileExplorer/ajax_getBackgroundsRecursive.php';

                na.m.log (1555, fncn+' : BEGIN download '+url);
                $('.na3D').each(function(idx,el){
                    if (idx==0) {
                        var ac = {
                            type : 'GET',
                            url : url,
                            success : function (data, ts) {
                                na.m.log (1555, fncn+' : BEGIN decode '+data.length+' bytes, url='+url);
                                var parameters = { views : [ JSON.parse(data) ] };
                                na.m.log (1555, fncn+' : END decode '+data.length+' bytes, url='+url);
                                debugger;

                                na.site.settings.na3D= {
                                    '#app_3D_fileExplorer' : new na3D_fileBrowser(el, $(el).parent()[0], parameters)
                                };
                            }
                        };
                        $.ajax(ac);
                    }


                });
                
                var 
                html = 
                    '<h2 id="titleHowTo" class="vividTextCSS">How To</h2>'
                    + '<p class="vividTextCSS backdropped">Drag and drop folders to re-arrange them.<br/>'
                   // + 'Your folder-arrangement will be saved for your next visit to this page.</p>'
                    + '<p class="vividTextCSS backdropped">Hold the left mouse button and drag to manually rotate the data.</p>'
                    + '<p class="vividTextCSS backdropped">Hold the right mouse button and drag to manually pan the camera.</p>'
                    + '<p class="vividTextCSS backdropped">Double click a mouse button to toggle automatic rotation of the data.</p>'
                    + '<p class="vividTextCSS backdropped">Triple click a mouse button to reverse direction for automatic rotation of the data.</p>'
                    + '<p class="vividTextCSS backdropped">Use the mouse wheel to zoom in or out.</p>'
                    + '<div id="threedfm_buttons">'
                    + '<div id="newColors" class="vividButton" theme="dark" style="position:relative;" onclick="for (var id in na.site.settings.na3D) { var td = na.site.settings.na3D[id]; td.useNewColors(); }">New Colors</div>'
                    + '<div id="newArrangement" class="vividButton" theme="dark" style="position:relative;" onclick="for (var id in na.site.settings.na3D) { var td = na.site.settings.na3D[id]; td.useNewArrangement(); }">New Arrangement</div>'
                    + '<div id="rotate" class="vividButton" theme="dark" style="position:relative;" onclick="for (var id in na.site.settings.na3D) { var td = na.site.settings.na3D[id]; td.rotate(event,td); }">Rotate I</div>'
                    + '<div id="rotate2" class="vividButton" theme="dark" style="position:relative;" onclick="for (var id in na.site.settings.na3D) { var td = na.site.settings.na3D[id]; td.rotate2(event,td); }">Rotate II</div>'
                    + '<div id="rotate3" class="vividButton" theme="dark" style="position:relative;" onclick="for (var id in na.site.settings.na3D) { var td = na.site.settings.na3D[id]; td.rotate3(event,td); }">Rotate III</div>'
                    +'<div id="showLines" class="vividButtonSelected" theme="dark" style="position:relative;" onclick="for (var id in na.site.settings.na3D) { var td = na.site.settings.na3D[id]; td.toggleShowLines(); }">Show Lines</div>'
                    +'</div>';
                $('#siteToolbarRight .vividDialogContent').html (html);
                //na.site.c.buttons['#newColors'] = new naVividButton ($('#newColors')[0]);
                
                /*
                var vividTextCmd = {
                        el : jQuery('#titleHowTo')[0],
                        theme : na.cg.themes.naColorgradientSchemeOrangeYellow, 
                        animationType : na.vividText.globals.animationTypes[0],
                        animationSpeed : 4 * 1000
                };
                na.vividText.initElement (vividTextCmd);
                */
            }, 100
        );

    }, 100);
 
