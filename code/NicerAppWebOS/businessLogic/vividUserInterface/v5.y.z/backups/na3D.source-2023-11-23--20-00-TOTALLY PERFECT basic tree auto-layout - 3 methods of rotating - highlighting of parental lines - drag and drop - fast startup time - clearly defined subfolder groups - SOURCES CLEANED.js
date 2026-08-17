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
  RGBAFormat,
  Scene,
  SkeletonHelper,
  UnsignedByteType,
  Vector2,
  Vector3,
  WebGLRenderer,
  sRGBEncoding,
  Raycaster
} from '/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.module.js';

import * as THREE from '/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.module.js';
import { Stats } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/libs/stats.module.js';
import { GLTFLoader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/GLTFLoader.js';
import { KTX2Loader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/KTX2Loader.js';
import { DRACOLoader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/DRACOLoader.js';
import { OrbitControls } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/OrbitControls.js';
import { RGBELoader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/RGBELoader.js';
import { DragControls } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/DragControls.js';
//import { GLTFLoader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/GLTFLoader.js';
//import { FlyControls } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/FlyControls.js';
//import { FirstPersonControls} from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/FirstPersonControls.js';
import gsap from "https://unpkg.com/gsap@3.12.2/index.js";
import { CameraControls } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/dist_camera-controls.module.js';

  import {
    CSS2DRenderer,
    CSS2DObject,
  } from 'https://unpkg.com/three@0.125.2/examples/jsm/renderers/CSS2DRenderer.js';





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
  sRGBEncoding,
} from 'three';
import { Stats } from 'three/addons';
import { GLTFLoader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/GLTFLoader.js';
import { KTX2Loader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/KTX2Loader.js';
import { DRACOLoader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/DRACOLoader.js';
import { OrbitControls } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/OrbitControls.js';
import { RGBELoader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/RGBELoader.js';
import { DragControls } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/DragControls.js';
//import { GLTFLoader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/GLTFLoader.js';
*/

export class na3D_portraitFrame {
    
    constructor (el, parent, centerX, centerY, centerZ, sizeX, sizeY, sizeZ) {
        
        var minX = centerX - Math.round(sizeX/2);
        var minY = centerY - Math.round(sizeY/2);
        var minZ = centerY - Math.round(sizeZ/2);
        var min = new Vector3 (minX, minY, minZ);
        
        var maxX = centerX + Math.round(sizeX/2);
        var maxY = centerY + Math.round(sizeY/2);
        var maxZ = centerY + Math.round(sizeZ/2);
        var max = new Vector3 (maxX, maxY, maxZ);
        
        t.box = new Box3 (min,max);
        return this;
    }
    
}


export class na3D_fileBrowser {
    constructor(el, parent, parameters) {
        var t = this;
        
        t.autoRotate = false;
        t.showLines = true;
        
        t.p = parent;
        t.el = el;
        t.t = $(t.el).attr('theme');
        t.settings = { parameters : parameters };
        t.data = parameters.views[0];
        t.loading = false;
        t.resizing = false;
        t.lights = [];
        t.folders = [];
        t.ld1 = {}; //levelDataOne
        t.ld2 = {}; //levelDataTwo


        
        t.items = [ {
            name : 'backgrounds',
            offsetY : 0,
            offsetX : 0,
            offsetZ : 0,
            column : 0,
            row : 0,
            columnCount : 1,
            rowCount : 1,
            idxPath : '',
            leftRight : 0,
            upDown : 0,
            columnOffsetValue : 100,
            rowOffsetValue : 100,
            parentRowOffset : 0,
            parentColumOffset : 0
        } ];
        
        t.lines = []; // onhover lines only in here
        t.permaLines = []; // permanent lines, the lines that show all of the parent-child connections.
        
        var 
        c = $.cookie('3DFDM_lineColors');
        if (typeof c=='string' && c!=='') {
            t.lineColors = JSON.parse(c);
        }
        
        t.scene = new THREE.Scene();
        t.s2 = [];
        t.camera = new THREE.PerspectiveCamera( 50, $(el).width() / $(el).height(), 0.01, 100 * 1000 );
        
        t.renderer = new THREE.WebGLRenderer({alpha:true, antialias : true});
        t.renderer.physicallyCorrectLights = true;
        t.renderer.outputEncoding = sRGBEncoding;
        t.renderer.setPixelRatio (window.devicePixelRatio);
        t.renderer.setSize( $(el).width()-20, $(el).height()-20 );
        t.renderer.toneMappingExposure = 1.0;
        
        el.appendChild( t.renderer.domElement );

        t.controls = new OrbitControls( t.camera, t.renderer.domElement );
        //t.controls.listenToKeyEvents( window ); // optional

        if (false) {
            const sphere1 = new THREE.Mesh(
                new THREE.SphereGeometry(1, 300, 300),
                new THREE.MeshNormalMaterial({wireframe: true})
            );
            debugger;
            t.scene.add (sphere1);

            alert ('2023-07-17 07:01am CET : This app is currently being improved. It might be a bit buggy over the course of the next week.');
        }

        $(t.renderer.domElement).bind('mousemove', function() {
            //event.preventDefault(); 
            t.onMouseMove (event, t)
        });
        $(t.renderer.domElement).click (function(event) {
            event.preventDefault(); 
            if (event.detail === 2) { // double click
                //t.controls.autoRotate = !t.controls.autoRotate
                //if (t.controls.autoRotate) $('#autoRotate').removeClass('vividButton').addClass('vividButtonSelected');
                //else $('#autoRotate').removeClass('vividButtonSelected').addClass('vividButton');
                t.onclick_double (t, event);
                    
            } else if (event.detail === 3) { // triple click
                //if (t.controls.autoRotateSpeed<0) t.controls.autoRotateSpeed = 1; else t.controls.autoRotateSpeed = -1;
                t.onclick_triple (t, event);
            } else {
                t.onclick (t, event);
            }
            
        });
        $(document).on('keydown', function(event) {
            /*if (t.dragndrop && t.dragndrop.obj) {
                t.zoomInterval = setInterval(function() {
                    if (event.keyCode===16 || event.keyCode===38) {
                        for (let i=0; i<t.items.length; i++) {
                            let it = t.items[i];
                            if (it.parent === t.dragndrop.obj.it.parent) {
                                it.model.position.z -= 25;
                            }
                        }
                    };
                    if (event.keyCode===17 || event.keyCode===40) { 
                        for (let i=0; i<t.items.length; i++) {
                            let it = t.items[i];
                            if (it.parent === t.dragndrop.obj.it.parent) {
                                it.model.position.z += 25;
                            }
                        }
                    };
                }, 200);
            }*/
            if (event.keyCode===32) t.controls.autoRotate = !t.controls.autoRotate;
        });
        $(document).on('keyup', function(event) {
            event.preventDefault();
            clearInterval(t.zoomInterval);
        });
        
        t.loader = new GLTFLoader();
        t.initializeItems (t);

        const light1  = new AmbientLight(0xFFFFFF, 0.3);
        light1.name = 'ambient_light';
        light1.intensity = 0.3;
        light1.color = 0xFFFFFF;
        t.camera.add( light1 );

        const light2  = new DirectionalLight(0xFFFFFF, 0.8 * Math.PI);
        light2.position.set(0.5, 0, 0.866); // ~60º
        light2.name = 'main_light';
        light2.intensity = 0.8 * Math.PI;
        light2.color = 0xFFFFFF;
        t.camera.add( light2 );
        
        t.lights.push(light1, light2);
        
        t.pmremGenerator = new PMREMGenerator( t.renderer );
        t.pmremGenerator.compileEquirectangularShader();
        
        //t.updateEnvironment(this);
        
        t.raycaster = new THREE.Raycaster();
        t.mouse = new THREE.Vector2();
        t.mouse.x = 0;
        t.mouse.y = 0;
        t.mouse.z = 0;

        t.camera.position.z = 1500;
        t.camera.position.y = 200;

        /*
        // Setup labels
        t.labelRenderer = new CSS2DRenderer();
        t.labelRenderer.setSize(innerWidth, innerHeight);
        t.labelRenderer.domElement.style.position = 'absolute';
        t.labelRenderer.domElement.style.top = '0px';
        t.labelRenderer.domElement.style.backgroundColor = 'rgba(0,0,50,0.5)';
        t.labelRenderer.domElement.style.boxShadow = 'inset 3px 3px 2px 2px rgba(255,255,255,0.55), 4px 4px 3px 2px rgba(0,0,0,0.7)';
        t.labelRenderer.domElement.style.pointerEvents = 'none';
        document.body.appendChild(t.labelRenderer.domElement);

        t.labelDiv = document.createElement('div');
        t.labelDiv.className = 'label';
        t.labelDiv.style.backgroundColor = 'rgba(0,0,50,0.5)';
        t.labelDiv.style.boxShadow = 'inset 3px 3px 2px 2px rgba(255,255,255,0.55), 4px 4px 3px 2px rgba(0,0,0,0.7)';
        t.labelDiv.style.marginTop = '-1em';

        t.label = new CSS2DObject(t.labelDiv);
        t.label.visible = false;
        t.scene.add(t.label);
        */

        // Track mouse movement to pick objects
        //t.raycaster = new Raycaster();
        //t.mouse = new Vector2();

        window.addEventListener('mousemove', ({ clientX, clientY }) => {
            //const { innerWidth, innerHeight } = window;
            var innerWidth = $('#siteContent .vividDialogContent').width();
            var innerHeight = $('#siteContent .vividDialogContent').height();

            t.mouse.x = ((clientX-$('#siteContent .vividDialogContent').offset().left) / innerWidth) * 2 - 1;
            t.mouse.y = (-1 * ((clientY-$('#siteContent .vividDialogContent').offset().top) / innerHeight) * 2) + 1;
            //t.animate(t);
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            //const { innerWidth, innerHeight } = window;
            var innerWidth = $('#siteContent .vividDialogContent').width();
            var innerHeight = $('#siteContent .vividDialogContent').height();

            t.renderer.setSize(innerWidth, innerHeight);
            t.camera.aspect = innerWidth / innerHeight;
            t.camera.updateProjectionMatrix();
        });

        t.renderer.setAnimationLoop(() => {
            //controls.update();

            // Pick objects from view using normalized mouse coordinates
            t.raycaster.setFromCamera(t.mouse, t.camera);

        });

        CameraControls.install ({ THREE : THREE });
        t.clock = new THREE.Clock();
        t.cameraControls = new CameraControls (t.camera, t.renderer.domElement);

        debugger;
        t.animate(this, null);
    }
    
    animate(t, evt) {
        requestAnimationFrame( function(evt) { t.animate (t,evt) } );
        //if (t.mouse.x!==0 || t.mouse.y!==0) {
            t.camera.updateProjectionMatrix();

            for (var i=0; i<t.s2.length; i++) {
                var it = t.s2[i];
                it.updateMatrixWorld();
            };
            t.raycaster.setFromCamera (t.mouse, t.camera);

            t.scene.matrixWorldAutoUpdate = true;;
            t.camera.matrixWorldAutoUpdate = true;
            //t.camera.lookAt (t.s2[0].position);
            //t.flycontrols.update(0.05);
            //t.fpcontrols.update(0.3);
            const delta = t.clock.getDelta();
            const hasControlsUpdated = t.cameraControls.update(delta);

            const intersects = t.raycaster.intersectObjects (t.s2);
            if (intersects[0] && intersects[0].object.type!=='Line') 
            for (var i=0; i<1/*intersects.length <-- this just gets an endless series of hits from camera into the furthest reaches of what's visible behind the mouse pointer */; i++) {
                var hoveredItem = intersects[i].object, done = false;
                while (hoveredItem && !done) {
                
                    for (var j=0; j<t.lines.length; j++) {
                        if (t.lines[j]) {
                            if (t.lines[j].it === it) {
                                haveLine = true;
                            } else {
                                t.scene.remove(t.lines[j].line);
                                t.lines[j].geometry.dispose();
                                delete t.lines[j];
                            }
                        }
                    }

                    // build a line towards parent
                    if (hoveredItem && hoveredItem.it && !done) {
                        let p = hoveredItem.it.model.position;
                        t.hoverOverName = '('+hoveredItem.it.column+':'+hoveredItem.it.row+') ('+p.x+', '+p.y+', '+p.z + ') : ' + hoveredItem.it.name;
                        t.hoverOverName = hoveredItem.it.name;
                    //debugger;    
                        var 
                        it = hoveredItem.it,
                        parent = t.items[it.parent.idx],
                        haveLine = false;
                        
                        // draw line to parent(s)
                        while (it && it.parent && it.parent!==0 && typeof it.parent !== 'undefined') {
                            var 
                            parent = t.items[it.parent.idx],
                            haveLine = false;
                            
                            if (parent && parent.model) {
                                if (!haveLine) {
                                    var 
                                    p1 = it.model.position, 
                                    p2 = parent.model.position;
                                    if (p1.x===0 && p1.y===0 && p1.z===0) continue;
                                    if (p2.x===0 && p2.y===0 && p2.z===0) continue;

                                    const points = [];
                                    points.push( new THREE.Vector3( p1.x, p1.y, p1.z ) );
                                    points.push( new THREE.Vector3( p2.x, p2.y, p2.z ) );

                                    var
                                    geometry = new THREE.BufferGeometry().setFromPoints (points);

                                    
                                    geometry.dynamic = true;
                                    geometry.verticesNeedUpdate = true;

                                    var material = new THREE.LineBasicMaterial({ color: 0xCCCCFF, linewidth:4 });
                                    var line = new THREE.Line( geometry, material );
                                    t.scene.add(line);

                                    t.lines[t.lines.length] = {
                                        it : it,
                                        line : line,
                                        geometry : geometry,
                                        material : material
                                    };
                                } else {
                                    for (var j=0; j<t.lines.length; j++) {
                                        if (t.lines[j]) t.lines[j].geometry.verticesNeedUpdate = true;
                                    }
                                }
                            }
                            it = t.items[it.parent.idx];
                        }
                                                
                        // draw lines to children
                        for (var j=0; j<t.items.length; j++) {
                            var child = t.items[j];
                            if (
                                hoveredItem && hoveredItem.it && hoveredItem.it.model && child.model
                                && hoveredItem.it.idx === child.parent.idx
                            ) {
                                var
                                p1 = child.model.position,
                                p2 = hoveredItem.it.model.position,
                                x = child.name;

                                if (p1.x===0 && p1.y===0 && p1.z===0) continue;
                                if (p2.x===0 && p2.y===0 && p2.z===0) continue;

                                const points = [];
                                points.push( new THREE.Vector3( p1.x, p1.y, p1.z ) );
                                points.push( new THREE.Vector3( p2.x, p2.y, p2.z ) );

                                var
                                geometry = new THREE.BufferGeometry().setFromPoints (points);

                                geometry.dynamic = true;
                                geometry.verticesNeedUpdate = true;

                                var material = new THREE.LineBasicMaterial({ color: 0x000050, linewidth : 4 });
                                var line = new THREE.Line( geometry, material );
                                t.scene.add(line);

                                t.lines[t.lines.length] = {
                                    it : it,
                                    line : line,
                                    geometry : geometry,
                                    material : material
                                };
                            }
                        }
                        done = true;
                    }
                    
                    hoveredItem = t.items[hoveredItem.parent.idx];
                }
                
                // show folder name for item under mouse and closest to the country
                $('#site3D_label').html(t.hoverOverName).css({display:'flex',opacity:1});

                const [hovered] = t.raycaster.intersectObjects(t.s2);
                if (hovered && hovered.object.type!=='Line') {
                    // Setup label
                    t.renderer.domElement.className = 'hovered';
                    //$('#site3D_label')[0].textContent = hovered.object.it.name;
                    //debugger;

                    // Get offset from object's dimensions
                    const offset = new Vector3();
                    new Box3().setFromObject(hovered.object).getSize(offset);

                    // Move label over hovered element
                    $('#site3D_label').css({
                        left : t.mouse.layerX + 20,
                        top : t.mouse.layerY + 20
                    });
                } else {
                    // Reset label
                    t.renderer.domElement.className = '';
                    t.label.visible = false;
                    t.labelDiv.textContent = '';
                }

                // Render scene
                t.renderer.render(t.scene, t.camera);

                // Render labels
                //t.labelRenderer.render(t.scene, t.camera);
            }
            if (!intersects[0]) {
                $('#site3D_label').fadeOut();
            } else {
                if (intersects[0] && intersects[0].object && intersects[0].object.parent && intersects[0].object.parent.parent) {
                    var model = intersects[0].object.parent.parent.parent.parent.parent.parent;
                    model.rotation.z += 0.02; //TODO : auto revert back to model.rotation.z = 0;
                }
            }
        //}
        
        //if (t.controls) t.controls.update();

        for (var i=0; i<t.lines.length; i++) {
            var it = t.lines[i];
            if (it && it.geometry) it.geometry.verticesNeedUpdate = true;
        };
        for (var i=0; i<t.permaLines.length; i++) {
            var it = t.permaLines[i];
            if (it && it.geometry) it.geometry.verticesNeedUpdate = true;
        };

        
        t.renderer.render( t.scene, t.camera );
    }

    rotate (event, t) {
        t.pathAnimation.play(0);
    }
    rotate2 (event, t) {
        t.pathAnimation2.play(0);
    }
    rotate3 (event, t) {
        t.pathAnimation3.play(0);
    }

    onMouseMove( event, t ) {
        var rect = t.renderer.domElement.getBoundingClientRect();
        t.mouse.x = ( ( event.clientX - rect.left ) / ( rect.width - rect.left ) ) * 2 - 1;
        t.mouse.y = - ( ( event.clientY - rect.top ) / ( rect.bottom - rect.top) ) * 2 + 1;        


        t.mouse.x = ( event.clientX / window.innerWidth ) * 2 - 1;
        t.mouse.y = ( event.clientY / window.innerHeight ) * 2 + 1;
        t.raycaster.setFromCamera (t.mouse.clone(), t.camera);

        t.mouse.layerX =  event.layerX;
        t.mouse.layerY =  event.layerY;

        //$('#site3D_label').html(t.hoverOverName).css({ position:'absolute', padding : 10, zIndex : 5000, top : event.layerY + 10, left : event.layerX + 30 });
    }
    
    onMouseWheel( event, t ) {
        debugger;
    }

    onclick (t, event) {
        const intersects = t.raycaster.intersectObjects (t.s2);
        if (intersects[0] && intersects[0].object.type!=='Line')
        for (var i=0; i<1/*intersects.length <-- this just gets an endless
        series of hits from camera into the furthest reaches of what's visible
        behind the mouse pointer */; i++) {
            var cit/*clickedItem*/ = intersects[i].object, done = false;
            while (cit && !done) {
                na.site.setStatusMsg (cit.it.fullPath, true, 7 * 1000);

                done = true;
            }
        }

    }
    onclick_double (t, event) {
        const intersects = t.raycaster.intersectObjects (t.s2);
        if (intersects[0] && intersects[0].object.type!=='Line')
        for (var i=0; i<1/*intersects.length <-- this just gets an endless
        series of hits from camera into the furthest reaches of what's visible
        behind the mouse pointer */; i++) {
            var cit/*clickedItem*/ = intersects[i].object, done = false;
            while (cit && !done) {
                var dbg = {
                    pos : cit.it.model.position,
                    'p.column' : t.items[cit.it.parent.idx].column,
                    'p.maxColumnIta.maxColumn':t.items[cit.it.parent.idx].maxColumnIta.maxColumn,
                    'p.columnOffsetValue':t.items[cit.it.parent.idx].columnOffsetValue,
                    'p.leftRight' : t.items[cit.it.parent.idx].leftRight,
                    'p.row' :  t.items[cit.it.parent.idx].row,
                    'p.maxColumnIta.maxRow':t.items[cit.it.parent.idx].maxColumnIta.maxRow,
                    'p.rowOffsetValue':t.items[cit.it.parent.idx].rowOffsetValue,
                    'p.upDown' :t.items[cit.it.parent.idx].upDown,
                    'it.column':cit.it.column,
                    'it.maxColumnIta.maxColumn':cit.it.maxColumnIta.maxColumn,
                    'it.row':cit.it.row,
                    'it.maxRowIta.maxRow':cit.it.maxColumnIta.maxRow
                },
                msg = JSON.stringify(dbg,undefined,4).replace(/\n/g, '<br/>').replace(/\s/g, '&nbsp;');

                na.site.setStatusMsg (msg, true, 10 * 1000);

                done = true;
            }
        }
    }
    onclick_triple (t, event) {
    }

    initializeItems (t) {
        var p = { t : t, ld2 : {}, idxPath : '', idxPath2 : '/0' };
        na.m.walkArray (t.data, t.data, t.initializeItems_walkKey, t.initializeItems_walkValue, false, p);
        t.onresize(t);
    }
    initializeItems_walkKey (cd) {
        var ps = cd.path.split('/');
        if (ps[ps.length-1]=='folders') {
            console.log ('key', cd);
            cd.params.idxPath = cd.params.idxPath2;
            //cd.params.idxPath = cd.params.idxPath + '/' + cd.params.t.items.length;

            var ps2 = $.extend([],ps);
            delete ps2[ps2.length-1];
            var level = ps2.length;
            var ps2Str = ps2.join('/');
            var parent = na.m.chaseToPath (cd.root, ps2Str, false);

            if (!cd.params.ld2[level]) cd.params.ld2[level] = { levelIdx : 0 };



            cd.at[cd.k].idxPath = cd.params.idxPath;
            cd.at[cd.k].idx = cd.params.t.items.length;

            var
            it = {
                data : cd.at[cd.k],
                level : ps2.length,
                name : cd.k,
                idx : cd.params.t.items.length,
                idxPath : cd.params.idxPath,// + '/' + cd.params.t.items.length,
                filepath : cd.path,
                levelIdx : ++cd.params.ld2[level].levelIdx,
                parent : parent,
                leftRight : 0,
                upDown : 0,
                columnOffsetValue : 1000,
                rowOffsetValue : 1000
            };

            for (var i=0; i<cd.params.t.items.length; i++) {
                var it2 = cd.params.t.items[i];
                if (it2.filepath===it.filepath) {
                    it.idxPath = it2.idxPath;
                    break;
                }
            }

            if (!cd.params.t.ld3) cd.params.t.ld3 = {};
            if (!cd.params.t.ld3[it.idxPath]) cd.params.t.ld3[it.idxPath] = { itemCount : 0, items : [] };
            cd.params.t.ld3[it.idxPath].itemCount++;
            cd.params.t.ld3[it.idxPath].items.push (it.idx);
            cd.params.idxPath2 += '/' + it.idx;

            var
            textures = [];
            for (var i=0; i<6; i++) textures[i] = '/siteMedia/folderIcon.png';
            for (var i=0; i<6; i++) {
                var p = null;
                //debugger;
                if (it.data.files && it.data.files[i] && it.data.files[i].match(/.*\.png|.*\.jpeg|.*\.jpg|.*\.gif$/)) {
                    var
                    fullPath = cd.params.t.data[0].root+'/'+cd.path.replace('0/filesAtRoot/','').replace(/\/folders/g,'')+'/'+cd.k+'/'+it.data.files[i],
                    filename = fullPath.replace(/^.*[\\\/]/, ''),
                    path = fullPath.replace('/'+filename,''),
                    pathThumb = path+'/thumbs/300/'+filename;
                    textures[i] = pathThumb;//'/NicerAppWebOS/'+filepath+'/'+key+'/thumbs/'+itd[''+i];//fn;
                    textures[i] = textures[i].replace(/\/\//g, '/');
                    it.fullPath = fullPath;
                    console.log ('t7734:' + fullPath);
                    //if (itd.files[i] && itd.files[i].match(/streetfighter/)) debugger;
                    //alert (JSON.stringify(textures,null,2));
                } else {
                    //alert (itd[''+i]);
                }
            }
            var
            materials = [
                new THREE.MeshBasicMaterial({
                    map: new THREE.TextureLoader().load(textures[0])
                }),
                new THREE.MeshBasicMaterial({
                    map: new THREE.TextureLoader().load(textures[1])
                }),
                new THREE.MeshBasicMaterial({
                    map: new THREE.TextureLoader().load(textures[2])
                }),
                new THREE.MeshBasicMaterial({
                    map: new THREE.TextureLoader().load(textures[3])
                }),
                new THREE.MeshBasicMaterial({
                    map: new THREE.TextureLoader().load(textures[4])
                }),
                new THREE.MeshBasicMaterial({
                    map: new THREE.TextureLoader().load(textures[5])
                })
            ];
            var cube = new THREE.Mesh( new THREE.BoxGeometry( 250, 250, 250 ), materials );
            cd.params.t.scene.add( cube );
            cd.params.t.s2.push(cube);
            cube.it = it;
            it.model = cube;
            cd.params.t.items.push (it);
        }
    }
    initializeItems_walkValue (cd) {
        //console.log ('value', cd);
    }
    
    onresize (t, levels) {
        if (!t) t = this;
        //debugger;
        na.m.waitForCondition ('waiting for other onresize commands to finish',
            function () { return t.resizing === false; },
            function () { t.onresize_do (t, levels); }, 
            50
        );
    }

    
    onresize_do(t, callback) {
        t.resizing = true;
        t.overlaps = [];

        let 
        c = {};
        for (var path in t.ld3) {
            var ld3 = t.ld3[path];
            if (path!=='') {
                for (var i=0; i<ld3.items.length; i++) {
                    var
                    it = t.items[ld3.items[i]];
                    
                    ld3.rowColumnCount = Math.ceil(Math.sqrt(ld3.itemCount));
                    var
                    column = 1,
                    row = 1;

                    
                    //if (it.filepath=='siteMedia/backgrounds/tiled/active') debugger;
                    for (var j=0; j<ld3.items.length; j++) {
                        var it2 = t.items[ld3.items[j]];
                        if (it2.levelIdx <= it.levelIdx) {
                            if (column >= ld3.rowColumnCount) {
                                row++;
                                column = 1;
                            } else column++;
                        } 
                    };
                    
                    it.row = row;
                    it.column = column;
                    //if (it.name=='gull' || it.name=='owl') debugger;
                }
            }
        }
        
        var
        its = $.extend( [], t.items ),
        its2 = [],
        compare = function (a, b) { 
            return a.parent-b.parent;
        },
        compare1 = function (a, b) {
            if (a.it && b.it) {
                return a.it.level-b.it.level;
            } else return 0;
        };
        
        its.sort (compare);
        
        
        var 
        x = t.data, // x[a][b][c].it
        maxLevel = 0;

        for (var i=0; i<its.length; i++) {
            if (maxLevel < its[i].level) maxLevel = its[i].level;
            for (var j=0; j<its.length; j++) {
                if (its[i].parent === its[j].parent) {
                    var
                    ita = {
                        level : its[i].level,
                        maxColumn : Math.max( its[i].column, its[j].column ),
                        maxRow : Math.max( its[i].row, its[j].row )
                    };
                    if (ita.maxColumn === its[i].column) ita.maxColumnIt = its[i]; else ita.maxColumnIt = its[j];
                    if (ita.maxRow === its[i].row) ita.maxRowIt = its[i]; else ita.maxRowIt = its[j];
                    its[i].maxColumnIta = ita;
                    its[i].maxRowIta = ita;
                    its[j].maxColumnIta = ita;
                    its[j].maxRowIta = ita;
                    
                    its2.push (ita);
                }
            }
        }
        var
        compare2 = function (a,b) {
            var x = b.maxColumn - a.maxColumn;
            if (x === 0) return b.maxRow - a.maxRow; else return x;
        },
        its3 = its2.sort (compare2);
        
        var pox = {}, poy = {}, poz = {};
        for (var i=0; i<t.items.length; i++) {
            var
            offsetXY = 200,
            it = t.items[i],
            p = (it.parent ? t.items[it.parent.idx] : null),
            rndMax = 4000;

            if (it.parent && !pox[it.parent.idx]) pox[it.parent.idx] = Math.abs(Math.random() * rndMax);
            if (it.parent && !poy[it.parent.idx]) poy[it.parent.idx] = Math.abs(Math.random() * rndMax);
            if (it.parent && !poz[it.parent.idx]) poz[it.parent.idx] = Math.abs(Math.random() * rndMax );

            if (it.parent) var rndx = pox[it.parent.idx]; else var rndx = 0;
            if (it.parent) var rndy = pox[it.parent.idx]; else var rndy = 0;
            if (it.parent) var rndz = poz[it.parent.idx]; else var rndz = 0;

            if (p && p.parent && t.items[p.parent.idx]) {
                var
                it2 = t.items[p.parent.idx],
                ppLeftRight = it2.leftRight,
                ppUpDown = it2.upDown;                
            } else {
                var
                it2 = null,
                ppLeftRight = it.leftRight,
                ppUpDown = it.upDown;
            };
            
            if (p) {
                var
                itmaxc = it.maxColumnIta.maxColumn,
                itmaxr = it.maxRowIta.maxRow,
                itmaxc2 = Math.floor(itmaxc/2),
                itmaxr2 = Math.floor(itmaxr/2),
                itLeftRight = /*p.leftRight * */(
                    it.column-1 == itmaxc / 2
                    ? 0
                    : itmaxc===1
                        ? 0
                        : itmaxc - it.column == it.column -1
                            ? 0
                            : itmaxc - it.column < it.column - 1
                                ? 1
                                : -1
                            ),
                itUpDown = /*p.upDown * */(
                    it.row-1 == itmaxr/2
                    ? 0
                    : itmaxr===1
                        ? 0
                        : itmaxr - it.row == it.row - 1
                            ? 0
                            : itmaxr - it.row < it.row - 1
                                ? 1
                                : -1
                            ),
                itc = (itmaxc - it.column),
                itr = (itmaxr - it.row);

                it.columnOffsetValue = itc;//Math.floor(itc);
                it.rowOffsetValue = itr;//Math.floor(itr);
                it.leftRight = itLeftRight;
                it.upDown = itUpDown;                

            } else {
                var mc = 0, mr = 0, p = it;

                var
                itmaxc = it.maxColumnIta.maxColumn,
                itmaxr = it.maxRowIta.maxRow,
                itLeftRight = /*p.leftRight * */(
                    it.column-1 == itmaxc / 2
                    ? 0
                    : itmaxc===1
                        ? 0
                        : itmaxc - it.column == it.column -1
                            ? 0
                            : itmaxc - it.column < it.column - 1
                                ? 1
                                : -1
                            ),
                itUpDown = /*p.upDown * */(
                    it.row-1 == itmaxr/2
                    ? 0
                    : itmaxr===1
                        ? 0
                        : itmaxr - it.row == it.row - 1
                            ? 0
                            : itmaxr - it.row < it.row - 1
                                ? 1
                                : -1
                            ),
                itc = (itmaxc - it.column),
                itr = (itmaxr - it.row);

                it.columnOffsetValue = itc;//Math.floor(itc);
                it.rowOffsetValue = itr;//Math.floor(itr);
                it.leftRight = itLeftRight;
                it.upDown = itUpDown;
                if (it.name=='landscape') debugger;
            };
        
            if (it.model && p && p.model) {

                var
                z = -1 * ((it.level+1) * 2500 ),
                plc = p.columnOffsetValue === 0 ? 0.01 : p.columnOffsetValue,
                plr = p.rowOffsetValue === 0 ? 0.01 : p.rowOffsetValue,
                ilc = it.leftRight * it.column,// * p.columnOffsetValue,
                ilr = it.upDown * it.row,// * p.rowOffsetValue,

                min = 4, m0 = (it.level-2) < 5 ? it.level-2 : 4, m1a = 200, m1 = 2500/m0, m2 = 2500/m0, m2a = 200, n = 0.5, n1 = p.leftRight * p.column, n2 = p.upDown * p.row, o = 600, q = 500, s = 1,
                u = 1 * (p.leftRight===0?ilc:p.leftRight),
                v = 1,
                w = 1 * (p.upDown===0?ilr:p.upDown),
                x = 1,
                u1 = p.columnOffsetValue/4,
                w1 = p.rowOffsetValue/4,
                u2 = p.columnOffsetValue /4,
                w2 =  p.rowOffsetValue /4;

                it.model.position.x = Math.round(
                    p.model.position.x
                    + (u1 * m1)+(it.column*m1)
                    + (it.level > min ? (u2 * v * ((o * n))) : 0)
                    + (it.level > min ? (u2 * v * ((o * s))) : 0)
                    + p.leftRight * rndx
                );
                it.model.position.y = Math.round(
                    p.model.position.y
                    + (w1 * m2)+(it.row*m2)
                    + (it.level > min ? (w2 * x * ((o * n))) : 0)
                    + (it.level > min ? (w2 * x * ((o * s))) : 0)
                    + p.upDown * rndy
                );
                it.model.position.z = -1 * z - rndz;
                //if (it.name=='space'||it.name=='wood') debugger;

                var x = it.data.it;
            }else if (it.model) {
                //debugger;
                it.model.position.x = it.leftRight * (it.column) * 100;
                it.model.position.y = it.upDown * (it.row) * 100;
                it.model.position.z = -1 * (it.level+1) * 100;
            }

            if (false && it.model) {
                var dbg = {
                    px : it.model.position.x,
                    py : it.model.position.y,
                    pz : it.model.position.z,
                    it : it
                };
                console.log (it.filepath, dbg);
            }
        }
        
        t.onresize_postDo(t);
    }

    onresize_postDo (t) {
        t.drawLines(t);

        if (!t.cameraOrigin) t.cameraOrigin = $.extend({}, t.camera.position);

        t.winners = {
            north : 0,
            east : 0,
            south : 0,
            west : 0,
            front : 0,
            behind : 0
        };
        for (var i=0; i < t.items.length; i++) {
            var it = t.items[i];
            if (!it.model) continue;
            if (it.model.position.y > t.winners.north) t.winners.north = it.model.position.y;
            if (it.model.position.x > t.winners.east) t.winners.east = it.model.position.x;
            if (it.model.position.y < t.winners.south) t.winners.south = it.model.position.y;
            if (it.model.position.x < t.winners.west) t.winners.west = it.model.position.x;
            if (it.model.position.z > t.winners.front) t.winners.front = it.model.position.z;
            if (it.model.position.z < t.winners.behind) t.winners.behind = it.model.position.z;
        };
        var
        tf = t.winners.behind + Math.round((t.winners.behind - t.winners.front) / 2),
        middle = {
            x : Math.round((t.winners.west + t.winners.east) / 2),
            y : Math.round((t.winners.north + t.winners.south) / 2),
            z : Math.round((t.winners.front + t.winners.behind) /2)
        },
        ol = 5000,
        numPoints = 360,
        radius = 25*1000;
        console.log ('t778', t.winners, middle);


        t.curve1b = new THREE.CatmullRomCurve3( [
            new THREE.Vector3 (0, 0, ol),
            new THREE.Vector3 (t.winners.west - ol, 0, ol),
            new THREE.Vector3 (t.winners.west - ol, 0, t.winners.front - ol),
            new THREE.Vector3 (t.winners.east + ol, 0, t.winners.front - ol),
            new THREE.Vector3 (t.winners.east + ol, 0, ol),
            new THREE.Vector3 (0, 0, ol),
        ]);
        var first = last = {x:0,y:0,z:ol};
        t.points1b = t.curve1b.getPoints(numPoints);
        t.curves1a = [
            new THREE.Vector3(t.cameraOrigin.x,t.cameraOrigin.y,t.cameraOrigin.z),
            new THREE.Vector3(first.x,first.y,first.z)
        ];
        t.curve1a = new THREE.CatmullRomCurve3(t.curves1a);
        t.points1a = t.curve1a.getPoints(50);
        t.curves1z = [
            new THREE.Vector3(last.x,last.y,last.z),
            new THREE.Vector3(t.cameraOrigin.x,t.cameraOrigin.y,t.cameraOrigin.z)
        ];
        t.curve1z = new THREE.CatmullRomCurve3(t.curves1z);
        t.points1z = t.curve1z.getPoints(50);

        t.curves1x = t.points1a.concat (t.points1b, t.points1z);
        t.curve1 = new THREE.CatmullRomCurve3(t.curves1x);
        t.points1 = t.curve1.getPoints(numPoints);



        t.curves2b = [];
        for (var i=0; i<numPoints; i++) {
            var
            x = radius * Math.cos (2 * Math.PI * i / numPoints),
            y = radius * Math.sin (2 * Math.PI * i / numPoints),
            z = 1.4 * radius;
            z = middle.z - (radius * Math.sin (2 * Math.PI * i / numPoints) / 2);
            if (i===0) var first = {x:x,y:y,z:z};
            if (i===numPoints-1) var last = {x:x,y:y,z:z};
            t.curves2b.push (new THREE.Vector3(x,y,z));
        }
        t.curves2a = [
            new THREE.Vector3(t.cameraOrigin.x,t.cameraOrigin.y,t.cameraOrigin.z),
            new THREE.Vector3(first.x,first.y,first.z)
        ];
        t.curve2a = new THREE.CatmullRomCurve3(t.curves2a);
        t.points2a = t.curve2a.getPoints(50);
        t.curves2z = [
            new THREE.Vector3(last.x,last.y,last.z),
            new THREE.Vector3(t.cameraOrigin.x,t.cameraOrigin.y,t.cameraOrigin.z)
        ];
        t.curve2z = new THREE.CatmullRomCurve3(t.curves2z);
        t.points2z = t.curve2z.getPoints(50);

        t.curves2x = t.points2a.concat (t.curves2b, t.points2z);
        t.curve2 = new THREE.CatmullRomCurve3(t.curves2x);
        t.points2 = t.curve2.getPoints(numPoints);

        t.curves3b = [];
        for (var i=0; i<numPoints; i++) {
            var
            x = radius * Math.cos (2 * Math.PI * i / numPoints),
            y = radius * Math.sin (2 * Math.PI * i / numPoints),
            z = 1.4 * radius;
            z = middle.z - (radius * Math.sin (2 * Math.PI * i / numPoints) / 2);
            if (i===0) var first = {x:x,y:y,z:z};
            if (i===numPoints-1) var last = {x:x,y:y,z:z};
            t.curves3b.push (new THREE.Vector3(x,y,z));
        }
        t.curve3b = new THREE.CatmullRomCurve3(t.curves3b);
        t.points3b = t.curve3b.getPoints(numPoints);
        t.curves3a = [
            new THREE.Vector3(t.cameraOrigin.x,t.cameraOrigin.y,t.cameraOrigin.z),
            new THREE.Vector3(first.x,first.y,first.z)
        ];
        t.curve3a = new THREE.CatmullRomCurve3(t.curves3a);
        t.points3a = t.curve3a.getPoints(50);
        t.curves3z = [
            new THREE.Vector3(last.x,last.y,last.z),
            new THREE.Vector3(t.cameraOrigin.x,t.cameraOrigin.y,t.cameraOrigin.z)
        ];
        t.curve3z = new THREE.CatmullRomCurve3(t.curves3z);
        t.points3z = t.curve3z.getPoints(50);

        t.curves3x = t.points3a.concat (t.points3b, t.points3z);
        t.curve3 = new THREE.CatmullRomCurve3(t.curves3x);
        t.points3 = t.curve3.getPoints(numPoints);

/*
        const geometry = new THREE.BufferGeometry().setFromPoints( t.points );
        const material = new THREE.LineBasicMaterial( { color: 0xff0000 } );
        // Create the final object to add to the scene
        const curveObject = new THREE.Line( geometry, material );
        t.scene.add(curveObject);


        const geometry2 = new THREE.BufferGeometry().setFromPoints( t.points2 );
        const material2 = new THREE.LineBasicMaterial( { color: 0xffffff } );
        // Create the final object to add to the scene
        const curveObject2 = new THREE.Line( geometry2, material2 );
        t.scene.add(curveObject2);
*/
        t._tmp = new THREE.Vector3();
        t.animationProgress = { value: 0 };
        t.pathAnimation = gsap.fromTo(
            t.animationProgress,
            {
                value: 0,
            },
            {
                value: 1,
                duration: 60,
                overwrite: true,
                paused: true,
                onUpdateParams: [ t.animationProgress ],
                onUpdate( { value } ) {

                    if ( ! this.isActive() ) return;

                    t.curve1.getPoint ( value, t._tmp );
                    const cameraX = t._tmp.x;
                    const cameraY = t._tmp.y;
                    const cameraZ = t._tmp.z;
                    const lookAtX = middle.x;
                    const lookAtY = middle.y;
                    const lookAtZ = middle.z;

                    t.cameraControls.setLookAt(
                        cameraX,
                        cameraY,
                        cameraZ,
                        lookAtX,
                        lookAtY,
                        lookAtZ,
                        false, // IMPORTANT! disable cameraControls's transition and leave it to gsap.
                    );

                },
                onStart() {

                    t.cameraControls.enabled = false;

                },
                onComplete() {

                    t.cameraControls.enabled = true;
                    t.onresize_postDo(t);
                },
            }
        );

        t.animationProgress2 = { value: 0 };
        t.pathAnimation2 = gsap.fromTo(
            t.animationProgress2,
            {
                value: 0,
            },
            {
                value: 1,
                duration: 60,
                overwrite: true,
                paused: true,
                onUpdateParams: [ t.animationProgress2 ],
                onUpdate( { value } ) {

                    if ( ! this.isActive() ) return;

                    t.curve2.getPoint ( value, t._tmp );
                    const cameraX = t._tmp.x;
                    const cameraY = t._tmp.y;
                    const cameraZ = t._tmp.z;
                    const lookAtX = middle.x;
                    const lookAtY = middle.y;
                    const lookAtZ = middle.z;

                    t.cameraControls.setLookAt(
                        cameraX,
                        cameraY,
                        cameraZ,
                        lookAtX,
                        lookAtY,
                        lookAtZ,
                        false, // IMPORTANT! disable cameraControls's transition and leave it to gsap.
                    );

                },
                onStart() {

                    t.cameraControls.enabled = false;

                },
                onComplete() {

                    t.cameraControls.enabled = true;
                    t.onresize_postDo(t);
                },
            }
        );

        t._tmp = new THREE.Vector3();
        t.animationProgress3 = { value: 0 };
        t.pathAnimation3 = gsap.fromTo(
            t.animationProgress3,
            {
                value: 0,
            },
            {
                value: 1,
                duration: 60,
                overwrite: true,
                paused: true,
                onUpdateParams: [ t.animationProgress3 ],
                onUpdate( { value } ) {

                    if ( ! this.isActive() ) return;

                    t.curve3.getPoint ( value, t._tmp );
                    const cameraX = t._tmp.x;
                    const cameraY = t._tmp.y;
                    const cameraZ = t._tmp.z;
                    const lookAtX = middle.x;
                    const lookAtY = middle.y;
                    const lookAtZ = middle.z;

                    t.cameraControls.setLookAt(
                        cameraX,
                        cameraY,
                        cameraZ,
                        lookAtX,
                        lookAtY,
                        lookAtZ,
                        false, // IMPORTANT! disable cameraControls's transition and leave it to gsap.
                    );

                },
                onStart() {

                    t.cameraControls.enabled = false;

                },
                onComplete() {

                    t.cameraControls.enabled = true;
                    t.onresize_postDo(t);
                },
            }
        );

        setTimeout (function() {

            if (!t.started) {
                t.started = true;
                t.pathAnimation.play(0);
            }


            if (!t.dragndrop) {
                var objs = [];
                for (var i=0; i<t.items.length; i++) if (t.items[i].model) objs[objs.length] = t.items[i].model;


                t.dragndrop = new DragControls( objs, t.camera, t.renderer.domElement );

                $(t.renderer.domElement).contextmenu(function() {
                    return false;
                });

                t.dragndrop.addEventListener( 'dragstart', function ( event ) {
                    if (t.controls) t.controls.dispose();
                    t.cameraControls.enabled = false;

                    t.dragndrop.cube = event.object;
                    t.dragndrop.mouseX = t.mouse.layerX;
                    t.dragndrop.mouseY = t.mouse.layerY;

                    let cube = event.object;

                    for (let i=0; i<t.items.length; i++) {
                        let it2 = t.items[i];
                        if (it2.parent === cube.it.parent) {
                            //debugger;
                            it2.model.position.dragStartX = it2.model.position.x;
                            it2.model.position.dragStartY = it2.model.position.y;
                            it2.model.position.dragStartZ = it2.model.position.z;
                        }
                    }
                } );

                t.dragndrop.addEventListener( 'drag', function (event) {
                    let cube = event.object;

                    for (let i=0; i<t.items.length; i++) {
                        let it2 = t.items[i];
                        if (it2.parent === cube.it.parent) {
                            //debugger;
                            it2.model.position.x = it2.model.position.dragStartX - (t.dragndrop.mouseX - t.mouse.layerX);
                            it2.model.position.y = it2.model.position.dragStartY + (t.dragndrop.mouseY - t.mouse.layerY);
                            it2.model.position.z = cube.position.z;
                        }
                    }
                    clearTimeout (t.posDataToDB);
                    t.posDataToDB = setTimeout(function() {
                        t.posDataToDatabase(t);
                    }, 1000);

                    if (t.showLines) {
                        for (var i=0; i<t.permaLines.length; i++) {
                            var l = t.permaLines[i];
                            t.scene.remove (l.line);
                            l.geometry.dispose();
                            l.material.dispose();
                        }
                        t.permaLines = [];
                        t.drawLines(t);
                    }
                });

                t.dragndrop.addEventListener( 'dragend', function ( event ) {
                    if (t.showLines) t.drawLines(t);
                    t.cameraControls.enabled = true;
                } );

            };
        }, 50);

        if (typeof callback=='function') callback(t);
    }
    


    posDataToDatabase (t) {
        let address = function (databaseName, username, password) {
            var r = 
                na.site.globals.couchdb.http
                +(typeof username=='string' && username!=='' ? username : na.a.settings.username)+':'
                +(typeof password=='string' && password!=='' ? password : na.a.settings.password)+'@'
                +na.site.globals.couchdb.domain
                +':'+na.site.globals.couchdb.port
                +'/'+na.site.globals.domain+'___'+databaseName;
            return r;
        },
        s = t.settings,
        un = na.a.settings.username,
        unl = un.toLowerCase(),
        pw = na.a.settings.password,
        dbName = 'three_d_positions',
        myip = na.site.globals.myip.replace(/_/g,'.');
        
        if (!s.pouchdb[dbName]) s.pouchdb[dbName] = new PouchDB(address(dbName,un,pw));
        
        let
        doc = {
            _id : 'positions_'+un,
            positions : [{ x : 0, y : 0, z : 0 }],
            lineColors : {}
        };
        
        for (let i=0; i<t.items.length; i++) {
            if (t.items[i].model) doc.positions[i] = {
                x : t.items[i].model.position.x,
                y : t.items[i].model.position.y,
                z : t.items[i].model.position.z
            }
        };
        for (var parent in t.lineColors) {
            doc.lineColors[parent] = t.lineColors[parent];
        }
        
        s.pouchdb[dbName].get(doc._id).then(function(docStored){
            doc._rev = docStored._rev;
            return s.pouchdb[dbName].put(doc);
        }).catch(function(err){
            return s.pouchdb[dbName].put(doc);
        });
    }
    
    databaseToPosData (t, callback) {
        let address = function (databaseName, username, password) {
            var r = 
                na.site.globals.couchdb.http
                +(typeof username=='string' && username!=='' ? username : na.a.settings.username)+':'
                +(typeof password=='string' && password!=='' ? password : na.a.settings.password)+'@'
                +na.site.globals.couchdb.domain
                +':'+na.site.globals.couchdb.port
                +'/'+na.site.globals.domain+'___'+databaseName;
            return r;
        },
        s = t.settings,
        un = na.a.settings.username,
        unl = un.toLowerCase(),
        pw = na.a.settings.password,
        dbName = 'three_d_positions',
        myip = na.site.globals.myip.replace(/_/g,'.');
        
        if (!s.pouchdb[dbName]) s.pouchdb[dbName] = new PouchDB(address(dbName,un,pw));
        s.pouchdb[dbName].get('positions_'+un).then(function(doc){
            for (let i=0; i<doc.positions.length; i++) {
                if (t.items[i].model) {
                    t.items[i].model.position.x = doc.positions[i].x;
                    t.items[i].model.position.y = doc.positions[i].y;
                    t.items[i].model.position.z = doc.positions[i].z;
                }
            }
            for (var parent in doc.lineColors) {
                t.lineColors[parent] = doc.lineColors[parent];
            };
            
            callback(true);
        }).catch(function(err){
            callback(false);
        });
    }
    
    toggleShowLines () {
        var t = this;
        t.showLines = !t.showLines;
        if (t.showLines) {
            t.drawLines(t);
            $('#showLines').removeClass('vividButton').addClass('vividButtonSelected');
        } else {
            for (var i=0; i<t.permaLines.length; i++) {
                var l = t.permaLines[i];
                t.scene.remove (l.line);
                l.geometry.dispose();
                l.material.dispose();
            }
            t.permaLines = [];
            $('#showLines').removeClass('vividButtonSelected').addClass('vividButton');
        }
    }
    
    drawLines (t) {
        //debugger;
        for (var i=0; i<t.permaLines.length; i++) {
            var l = t.permaLines[i];
            t.scene.remove(l.line);
            l.geometry.dispose();
            l.material.dispose();
        };

        for (var i=1; i<t.items.length; i++) {
            var 
            it = t.items[i],
            parent = t.items[it.parent.idx],
            haveThisLineAlready = false;
            
            if (!t.showLines) return false;
            if (!it.model) return false;
            
            if (it.parent.idx===0 || typeof it.parent === 'undefined') continue;
            
            for (var j=0; j<t.permaLines.length; j++) {
                if (t.permaLines[j].it === it) {
                    haveThisLineAlready = true;
                    break;
                }
            };
            
            if (parent && parent.model) {
                var 
                p1 = it.model.position, 
                p2 = parent.model.position;
                if (p1.x===0 && p1.y===0 && p1.z===0) continue;
                if (p2.x===0 && p2.y===0 && p2.z===0) continue;

                const points = [];
                points.push( new THREE.Vector3( p1.x, p1.y, p1.z ) );
                points.push( new THREE.Vector3( p2.x, p2.y, p2.z ) );

                var
                geometry = new THREE.BufferGeometry().setFromPoints (points);

                //geometry.dynamic = true;
                //geometry.vertices.push(p1);
                //geometry.vertices.push(p2);
                //geometry.verticesNeedUpdate = true;
                
                if (!t.lineColors) t.lineColors = {};
                if (!t.lineColors[it.parent.idx]) {
                    var x=Math.round(0xffffff * Math.random()).toString(16);
                    var y=(6-x.length);
                    var z="000000";
                    var z1 = z.substring(0,y);
                    var color= z1 + x;                    
                    t.lineColors[it.parent.idx] = color;
                }
                var color = t.lineColors[it.parent.idx];
                
                var
                material = new THREE.LineBasicMaterial({ color: '#'+color, linewidth :2 }),
                line = new THREE.Line( geometry, material );
                t.scene.add(line);

                t.permaLines[t.permaLines.length] = {
                    line : line,
                    geometry : geometry,
                    material : material,
                    it : it
                };
            }
        }
        $.cookie('3DFDM_lineColors', JSON.stringify(t.lineColors), na.m.cookieOptions());
    }
    
    useNewArrangement () {
        var t = this;
        t.onresize_do(t, t.posDataToDatabase);
    }
    
    useNewColors () {
        var t = this;
        for (var i=0; i<t.permaLines.length; i++) {
            t.scene.remove (t.permaLines[i].line);
            t.permaLines[i].geometry.dispose();
            t.permaLines[i].material.dispose();
        }
        t.permaLines = [];
        delete t.lineColors;
        setTimeout (function () {
            t.drawLines (t);
        }, 500);
    }
    
    toggleAutoRotate () {
        var t = this;
        t.controls.autoRotate = !t.controls.autoRotate;
        if (t.controls.autoRotate) $('#autoRotate').removeClass('vividButton').addClass('vividButtonSelected'); 
        else $('#autoRotate').removeClass('vividButtonSelected').addClass('vividButton');
    }
    
    updateTextureEncoding (t, content) {
        /*const encoding = t.state.textureEncoding === 'sRGB'
        ? sRGBEncoding
        : LinearEncoding;*/
        const encoding = LinearEncoding;
        t.traverseMaterials(content, (material) => {
            if (material.map) material.map.encoding = encoding;
            if (material.emissiveMap) material.emissiveMap.encoding = encoding;
            if (material.map || material.emissiveMap) material.needsUpdate = true;
        });
    }
    
    traverseMaterials (object, callback) {
        object.traverse((node) => {
            if (!node.isMesh) return;
            const materials = Array.isArray(node.material)
                ? node.material
                : [node.material];
            materials.forEach(callback);
        });
    }
    
    updateEnvironment (t) {
        /*
        const environment = {
            id: 'venice-sunset',
            name: 'Venice Sunset',
            path: '/NicerAppWebOS/3rd-party/3D/assets/environment/venice_sunset_1k.hdr',
            format: '.hdr'
        };*/
        const environment = {
            id: 'footprint-court',
            name: 'Footprint Court (HDR Labs)',
            path: '/NicerAppWebOS/3rd-party/3D/assets/environment/footprint_court_2k.hdr',
            format: '.hdr'
        }

        t.getCubeMapTexture( environment ).then(( { envMap } ) => {

            /*if ((!envMap || !t.state.background) && t.activeCamera === t.defaultCamera) {
                t.scene.add(t.vignette);
            } else {
                t.scene.remove(t.vignette);
            }*/

            t.scene.environment = envMap;
            //t.scene.background = t.state.background ? envMap : null;

        });

    }    
    
    getCubeMapTexture ( environment ) {
        const { path } = environment;

        // no envmap
        if ( ! path ) return Promise.resolve( { envMap: null } );

        return new Promise( ( resolve, reject ) => {
            new RGBELoader()
                .setDataType( UnsignedByteType )
                .load( path, ( texture ) => {

                    const envMap = t.pmremGenerator.fromEquirectangular( texture ).texture;
                    t.pmremGenerator.dispose();

                    resolve( { envMap } );

                }, undefined, reject );
        });
    }
}






export class na3D_fileBrowser_extensionApp_crimeboard {
    constructor(el, parent, data) {
        var t = this;
	}
}






export class na3D_demo_models {
    constructor(el, parent, data) {
        var t = this;
        t.p = parent;
        t.el = el;
        t.t = $(t.el).attr('theme');
        
        t.data = data;
        
        t.lights = [];
        t.folders = [];
   
        t.items = [];
        
        t.scene = new THREE.Scene();
        t.camera = new THREE.PerspectiveCamera( 75, $(el).width() / $(el).height(), 0.1, 1000 );
        

        t.renderer = new THREE.WebGLRenderer({alpha:true, antialias : true});
        t.renderer.physicallyCorrectLights = true;
        t.renderer.outputEncoding = sRGBEncoding;
        t.renderer.setPixelRatio (window.devicePixelRatio);
        t.renderer.setSize( $(el).width()-20, $(el).height()-20 );
        
        t.renderer.toneMappingExposure = 1.0;
        
        el.appendChild( t.renderer.domElement );
        
        t.controls = new OrbitControls( t.camera, t.renderer.domElement );
        //t.controls.listenToKeyEvents( window ); // optional
        
        t.loader = new GLTFLoader();
        
        t.loader.load( '/NicerAppWebOS/3rd-party/3D/models/human armor/scene.gltf', function ( gltf ) {
            gltf.scene.position.x = -150;
            gltf.scene.scale.setScalar (10);
            t.cube = gltf.scene;
            t.scene.add (t.cube);
            
            t.updateTextureEncoding(t, t.cube);
        }, function ( xhr ) {
            console.log( 'model "human armor" : ' + ( xhr.loaded / xhr.total * 100 ) + '% loaded' );
        }, function ( error ) {
            console.error( error );
        } );
        t.loader.load( '/NicerAppWebOS/3rd-party/3D/models/photoCamera/scene.gltf', function ( gltf ) {
            gltf.scene.position.x = 200;
            t.cube2 = gltf.scene;
            t.scene.add (t.cube2);
            
            t.updateTextureEncoding(t, t.cube2);
            
        }, function ( xhr ) {
            console.log( 'model "photoCamera" : ' + ( xhr.loaded / xhr.total * 100 ) + '% loaded' );
        }, function ( error ) {
            console.error( error );
        } );
        
        const light1  = new AmbientLight(0xFFFFFF, 0.3);
        light1.name = 'ambient_light';
        light1.intensity = 0.3;
        light1.color = 0xFFFFFF;
        t.camera.add( light1 );

        const light2  = new DirectionalLight(0xFFFFFF, 0.8 * Math.PI);
        light2.position.set(0.5, 0, 0.866); // ~60º
        light2.name = 'main_light';
        light2.intensity = 0.8 * Math.PI;
        light2.color = 0xFFFFFF;
        t.camera.add( light2 );

        t.lights.push(light1, light2);
        
        t.pmremGenerator = new PMREMGenerator( t.renderer );
        t.pmremGenerator.compileEquirectangularShader();
        
        t.updateEnvironment(this);
        
        $(el).bind('mousemove', function() { t.onMouseMove (event, t) });
        
        t.raycaster = new THREE.Raycaster();
        t.mouse = new THREE.Vector2();
        t.mouse.x = 0;
        t.mouse.y = 0;

        t.camera.position.z = 700;
        
        t.animate(this);
    }
    
    animate(t) {
        requestAnimationFrame( function() { t.animate (t) } );
        
        t.raycaster.setFromCamera (t.mouse, t.camera);

        const intersects = t.raycaster.intersectObjects (t.scene.children, true);
        if (intersects[0] && t.cube && t.cube2) {
            t.cube.rotation.x += 0.015;
            t.cube.rotation.y += 0.02;
            t.cube2.rotation.x += 0.015;
            t.cube2.rotation.y += 0.02;
            //t.cube2.rotation.y += 0.02;
        }
        
        t.renderer.render( t.scene, t.camera );
    }
    
    
    updateTextureEncoding (t, content) {
        /*const encoding = t.state.textureEncoding === 'sRGB'
        ? sRGBEncoding
        : LinearEncoding;*/
        const encoding = sRGBEncoding;
        t.traverseMaterials(content, (material) => {
            if (material.map) material.map.encoding = encoding;
            if (material.emissiveMap) material.emissiveMap.encoding = encoding;
            if (material.map || material.emissiveMap) material.needsUpdate = true;
        });
    }
    
    traverseMaterials (object, callback) {
        object.traverse((node) => {
            if (!node.isMesh) return;
            const materials = Array.isArray(node.material)
                ? node.material
                : [node.material];
            materials.forEach(callback);
        });
    }
    
    updateEnvironment (t) {

        const environment = {
            id: 'venice-sunset',
            name: 'Venice Sunset',
            path: '/NicerAppWebOS/3rd-party/3D/assets/environment/venice_sunset_1k.hdr',
            format: '.hdr'
        };
        /*
        const environment = {
            id: 'footprint-court',
            name: 'Footprint Court (HDR Labs)',
            path: '/NicerAppWebOS/3rd-party/3D/assets/environment/footprint_court_2k.hdr',
            format: '.hdr'
        }*/

        t.getCubeMapTexture( environment ).then(( { envMap } ) => {

            /*
            if (!envMap || !t.state.background) && t.activeCamera === t.defaultCamera) {
                t.scene.add(t.vignette);
            } else {
                t.scene.remove(t.vignette);
            }*/
            t.scene.add(t.vignette);

            t.scene.environment = envMap;
            //t.scene.background = envMap;//t.state.background ? envMap : null;

        });

    }    
    
    getCubeMapTexture ( environment ) {
        const { path } = environment;

        // no envmap
        if ( ! path ) return Promise.resolve( { envMap: null } );

        return new Promise( ( resolve, reject ) => {
            new RGBELoader()
                //.setDataType( UnsignedByteType )
                .load( path, ( texture ) => {

                    const envMap = t.pmremGenerator.fromEquirectangular( texture ).texture;
                    t.pmremGenerator.dispose();

                    resolve( { envMap } );

                }, undefined, reject );
        });
    }

    
    onMouseMove( event, t ) {
        var rect = t.renderer.domElement.getBoundingClientRect();
        t.mouse.x = ( ( event.clientX - rect.left ) / ( rect.width - rect.left ) ) * 2 - 1;
        t.mouse.y = - ( ( event.clientY - rect.top ) / ( rect.bottom - rect.top) ) * 2 + 1;        
    }
    
    onMouseWheel( event, t ) {
        debugger;
    }
}







export class na3D_demo_cube {
    constructor(el,parent) {
        t.p = parent;
        t.el = el;
        t.t = $(t.el).attr('theme');
        
        t.scene = new THREE.Scene();
        t.camera = new THREE.PerspectiveCamera( 75, $(el).width() / $(el).height(), 0.1, 1000 );

        t.renderer = new THREE.WebGLRenderer({ alpha : true });
        t.renderer.setSize( $(el).width()-20, $(el).height()-20 );
        el.appendChild( t.renderer.domElement );
        
        const geometry = new THREE.BoxGeometry();
        const material = new THREE.MeshBasicMaterial( { color: 0x00ff00 } );
        var materials = [
            new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/siteMedia/backgrounds/tiled/blue/4a065201509c0fc50e7341ce04cf7902--twitter-backgrounds-blue-backgrounds.jpg')
            }),
            new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/siteMedia/backgrounds/tiled/blue/blue170.jpg')
            }),
            new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/siteMedia/backgrounds/tiled/blue/abstract_water_texture-seamless.jpg')
            }),
            new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/siteMedia/backgrounds/tiled/orange/467781133_4f4354223e.jpg')
            }),
            new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/siteMedia/backgrounds/tiled/green/dgren051.jpg')
            }),
            new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load('/siteMedia/backgrounds/tiled/green/leaves007.jpg')
            })
        ];
        t.cube = new THREE.Mesh( new THREE.BoxGeometry( 1, 1, 1 ), materials );
        t.scene.add( t.cube );
        var t = this;
        $(el).bind('mousemove', function() { t.onMouseMove (event, t) });
        
        t.raycaster = new THREE.Raycaster();
        t.mouse = new THREE.Vector2();

        t.camera.position.z = 5;
        t.cube.rotation.x = 0.3;
        t.cube.rotation.y = 0.4;
        t.animate(this);
    }
    
    onMouseMove( event, t ) {
        // calculate mouse position in normalized device coordinates
        // (-1 to +1) for both components
        //t.mouse.x = ( event.clientX / window.innerWidth ) * 2 - 1;
        //t.mouse.y = - ( event.clientY / window.innerHeight ) * 2 + 1;
        var rect = t.renderer.domElement.getBoundingClientRect();
        t.mouse.x = ( ( event.clientX - rect.left ) / ( rect.width - rect.left ) ) * 2 - 1;
        t.mouse.y = - ( ( event.clientY - rect.top ) / ( rect.bottom - rect.top) ) * 2 + 1;        
    }
    
    
    animate(t) {
        requestAnimationFrame( function() { t.animate (t) } );
        //t.cube.rotation.x += 0.02;
        //t.cube.rotation.y += 0.02;
        t.raycaster.setFromCamera (t.mouse, t.camera);
        const intersects = t.raycaster.intersectObjects (t.scene.children, true);
        for (var i=0; i<intersects.length; i++) {
            intersects[i].object.rotation.x += 0.02;
            intersects[i].object.rotation.y += 0.02;
        }
        t.renderer.render( t.scene, t.camera );
    }
}


(function (api) {
    // position a mesh to a sphere mesh with a given lat, long, and alt
    var api = {};
    api.positionToSphere = function(sphereMesh, mesh, lat, long, alt){
        // defaults for lat, long, and alt
        lat = lat === undefined ? 0 : lat;
        long = long === undefined ? 0 : long;
        alt = alt === undefined ? 0 : alt;
        // get geometry of the sphere mesh
        var sGeo = sphereMesh.geometry;
        // computer bounding sphere for geometry of the sphere mesh
        sGeo.computeBoundingSphere();
        // use radius value of Sphere instance at
        // boundingSphere of the geometry of sphereMesh
        var radius = sGeo.boundingSphere.radius;
        // position mesh to position of sphereMesh, and translate
        // from there using lat, long, alt, and radius of sphereMesh
        // using the copy, add, and apply Euler methods of the Vector3 class
        var v1 = new THREE.Vector3(0, radius + alt, 0);
        var x = Math.PI * lat;
        var z = Math.PI * 2 * long;
        var e1 = new THREE.Euler(x, 0, z)
        mesh.position.copy(sphereMesh.position).add(v1).applyEuler(e1);
    };
}
    ()
);
