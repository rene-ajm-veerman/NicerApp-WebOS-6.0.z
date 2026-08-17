/*import {
  AmbientLight,
  AnimationMixer,
  AxesHelper,
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
		from '/NicerAppWebOS/businessLogic/vividUserInterface/v6.y.z/3D/na3D.js';
		
    //$(document).ready(function() {
        setTimeout (function() {

        delete na.site.settings.loadingApps;

        na.m.waitForCondition ('app.3D.fileExplorer : Filesystem index loaded?',
            function () { 
				var r =
                    na.site.initialized
                    && na.d && na.d.s && !na.d.s.animating
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
                if (na.backgrounds.settings.backgroundSearchKey!=='3Dee') na.backgrounds.next('#siteBackground', '3Dee');

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

                                na.site.settings.na3D = {
                                    '#app_3D_fileExplorer' : new na3D_fileBrowser(el, $(el).parent()[0], parameters)
                                };
                                na.apps.loaded.threed_fileExplorer = na.site.settings.na3D['#app_3D_fileExplorer'];
                            }
                        };
                        $.ajax(ac);
                    }


                });

            }, 100);
    }, 100);

