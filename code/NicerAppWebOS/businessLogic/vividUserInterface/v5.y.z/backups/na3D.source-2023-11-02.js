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
  Vector3,
  WebGLRenderer,
  sRGBEncoding
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
        
        this.box = new Box3 (min,max);
        return this;
    }
    
}


export class na3D_fileBrowser {
    constructor(el, parent, parameters) {
        var t = this;
        
        t.autoRotate = true;
        t.showLines = true;
        
        t.p = parent;
        t.el = el;
        t.t = $(this.el).attr('theme');
        t.settings = { parameters : parameters };
        t.data = parameters.views[0];
        t.loading = false;
        t.resizing = false;
        t.lights = [];
        t.folders = [];
        t.ld1 = {}; //levelDataOne
        t.ld2 = {}; //levelDataTwo


        
        this.items = [ {
            name : 'backgrounds',
            offsetY : 0,
            offsetX : 0,
            offsetZ : 0,
            column : 0,
            row : 0,
            columnCount : 1,
            rowCount : 1,
            idxPath : ''
        } ];
        
        this.lines = []; // onhover lines only in here
        this.permaLines = []; // permanent lines, the lines that show all of the parent-child connections.
        
        var 
        c = $.cookie('3DFDM_lineColors');
        if (typeof c=='string' && c!=='') {
            this.lineColors = JSON.parse(c);
        }
        
        this.scene = new THREE.Scene();
        this.camera = new THREE.PerspectiveCamera( 75, $(el).width() / $(el).height(), 0.1, 10 * 1000 );
        

        this.renderer = new THREE.WebGLRenderer({alpha:true, antialias : true});
        this.renderer.physicallyCorrectLights = true;
        debugger;
        this.renderer.outputEncoding = sRGBEncoding;
        this.renderer.setPixelRatio (window.devicePixelRatio);
        this.renderer.setSize( $(el).width()-20, $(el).height()-20 );
        this.renderer.toneMappingExposure = 1.0;
        
        el.appendChild( this.renderer.domElement );
        
        $(this.renderer.domElement).bind('mousemove', function() {
            //event.preventDefault(); 
            t.onMouseMove (event, t)
        });
        $(this.renderer.domElement).click (function(event) {  
            event.preventDefault(); 
            if (event.detail === 2) { // double click
                t.controls.autoRotate = !t.controls.autoRotate 
                if (t.controls.autoRotate) $('#autoRotate').removeClass('vividButton').addClass('vividButtonSelected'); 
                else $('#autoRotate').removeClass('vividButtonSelected').addClass('vividButton');
                    
            } else if (event.detail === 3) { // triple click
                if (t.controls.autoRotateSpeed<0) t.controls.autoRotateSpeed = 1; else t.controls.autoRotateSpeed = -1;
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
        
        this.loader = new GLTFLoader();
        this.initializeItems (this, this.items, this.data, 0, 0, 0, '0', '');

        const light1  = new AmbientLight(0xFFFFFF, 0.3);
        light1.name = 'ambient_light';
        light1.intensity = 0.3;
        light1.color = 0xFFFFFF;
        this.camera.add( light1 );

        const light2  = new DirectionalLight(0xFFFFFF, 0.8 * Math.PI);
        light2.position.set(0.5, 0, 0.866); // ~60º
        light2.name = 'main_light';
        light2.intensity = 0.8 * Math.PI;
        light2.color = 0xFFFFFF;
        this.camera.add( light2 );
        
        this.lights.push(light1, light2);        
        
        this.pmremGenerator = new PMREMGenerator( this.renderer );
        this.pmremGenerator.compileEquirectangularShader();
        
        //this.updateEnvironment(this);
        
        this.raycaster = new THREE.Raycaster();
        this.mouse = new THREE.Vector2();
        this.mouse.x = 0;
        this.mouse.y = 0;
        this.mouse.z = 0;

        this.camera.position.z = 1500;
        this.camera.position.y = 200;
        
        this.animate(this);
    }
    
    animate(t) {
        requestAnimationFrame( function() { t.animate (t) } );
        if (t.mouse.x!==0 || t.mouse.y!==0) {        
            t.raycaster.setFromCamera (t.mouse, t.camera);
            
            const intersects = t.raycaster.intersectObjects (t.scene.children, true);
            //debugger;
            //if (intersects[0]) {
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
                        //t.hoverOverName = hoveredItem.it.name;
                    //debugger;    
                        var 
                        it = hoveredItem.it,
                        parent = t.items[it.parent],
                        haveLine = false;
                        
                        // draw line to parent(s)
                        while (it.parent && it.parent!==0 && typeof it.parent !== 'undefined') {
                            var 
                            parent = t.items[it.parent],
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
                            it = t.items[it.parent];
                        }
                                                
                        // draw lines to children
                        for (var j=0; j<t.items.length; j++) {
                            var child = t.items[j];
                            if (
                                hoveredItem && hoveredItem.it && hoveredItem.it.model && child.model
                                && hoveredItem.it.idx === child.parent
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
                    
                    hoveredItem = hoveredItem.parent;
                }
                
                // show folder name for item under mouse and closest to the country
                $('#site3D_label').html(t.hoverOverName).css({display:'flex'});
            }
            if (!intersects[0]) {
                $('#site3D_label').fadeOut();
            } else {
                if (intersects[0] && intersects[0].object && intersects[0].object.parent && intersects[0].object.parent.parent) {
                    var model = intersects[0].object.parent.parent.parent.parent.parent.parent;
                    model.rotation.z += 0.02; //TODO : auto revert back to model.rotation.z = 0;
                }
            }
        }
        
        if (t.controls) t.controls.update();

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
    
    onMouseMove( event, t ) {
        var rect = t.renderer.domElement.getBoundingClientRect();
        t.mouse.x = ( ( event.clientX - rect.left ) / ( rect.width - rect.left ) ) * 2 - 1;
        t.mouse.y = - ( ( event.clientY - rect.top ) / ( rect.bottom - rect.top) ) * 2 + 1;        
        t.mouse.layerX =  event.layerX;
        t.mouse.layerY =  event.layerY;

        $('#site3D_label').html(t.hoverOverName).css({ position:'absolute', padding : 10, zIndex : 5000, top : event.layerY + 10, left : event.layerX + 30 });
    }
    
    onMouseWheel( event, t ) {
        debugger;
    }
    
    initializeItems (t, items, data, parent, level, levelDepth, idxPath, filepath) {
        if (!t) t = this;
        //debugger;
        na.m.waitForCondition ('waiting for other initializeItems_do() commands to finish',
            function () {
                //debugger;
                return t.loading === false;
            },
            function () {
                //debugger;
                t.initializeItems_do (t, items, data, parent, level, levelDepth, idxPath, filepath);
            }, 100
        );
    }

    initializeItems_do (t, items, data, parent, level, levelDepth, idxPath, filepath) {
        if (data.model) { alert ('data.model!'); return false; };
        if (!t.ld2[level]) t.ld2[level] = { parent : parent, initItemsDoingIdx : 0, idxPath : idxPath };
        if (!t.ld2[level].keys) t.ld2[level].keys = Object.keys(data);
        if (t.ld2[level].initItemsDoingIdx >= t.ld2[level].keys.length) return false;
        
        if (!t.ld2[level].levelIdx) t.ld2[level].levelIdx = 0;
        
        if (!t.ld1[level]) t.ld1[level] = { levelIdx : 0 };
        
        if (!t.initCounter) t.initCounter=0;
        
        if (!t.ld3) t.ld3 = {};
        if (!t.ld3[idxPath]) t.ld3[idxPath] = { itemCount : 0, items : [] };
        t.ld3[idxPath].itemCount++;
         
        while (t.ld2[level].initItemsDoingIdx < t.ld2[level].keys.length) {
            var 
            keyIdx = t.ld2[level].initItemsDoingIdx,
            key = t.ld2[level].keys[ keyIdx ],
            itd = data[key];
            
            if (itd.files) t.initializeItems_do (t, items, itd.files, items.length-1, level+1, levelDepth+1, '0', itd.root); 
            else if (key!=='it' && key!=='thumbs')
            if (typeof itd == 'object' && itd!==null) {
                let 
                idxPath2 = !t.items[parent]||t.items[parent].idxPath===''?''+parent:t.items[parent].idxPath+','+parent,
                it = {
                    data : itd,
                    level : levelDepth,
                    name : key,
                    idx : items.length,
                    idxPath : idxPath2,
                    filepath : filepath,
                    levelIdx : t.ld2[level].levelIdx,
                    parent : parent
                };
                
                itd.it = it;
                
                items[items.length] = it;
                t.ld2[level].levelIdx++;

                if (!t.ld3[idxPath2]) t.ld3[idxPath2] = { itemCount : 0, items : [] };
                //t.ld3[path2].itemCount++;

                t.ld3[idxPath2].items.push (it.idx);
                
                
                let 
                cd = { //call data
                    t : t,
                    it : it,
                    items : items,
                    itd : itd,
                    parent : parent,
                    idxPath : idxPath2,
                    filepath : filepath,
                    levelDepth : levelDepth + 1
                };
                
                clearTimeout (t.onresizeInitTimeout);
                clearTimeout (t.linedrawTimeout);
                
                var 
                textures = [];
                for (var i=0; i<6; i++) textures[i] = '/siteMedia/folderIcon.png';
                for (var i=0; i<6; i++) {
                    var p = null;
                    if (itd[''+i] && typeof itd[''+i]=='object' && Object.keys(itd[''+i])[0].match(/.*\.png|.*\.jpeg|.*\.jpg|.*\.gif$/)) {
                        var
                        fullPath = '/siteMedia/backgrounds' + itd[''+i],
                        filename = fullPath.replace(/^.*[\\\/]/, ''),
                        path = fullPath.replace('/'+filename,''),
                        pathThumb = path+'/thumbs/300/'+filename;
                        textures[i] = pathThumb;//'/NicerAppWebOS/'+filepath+'/'+key+'/thumbs/'+itd[''+i];//fn;
                        textures[i] = textures[i].replace(/\/\//g, '/');
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
                var cube = new THREE.Mesh( new THREE.BoxGeometry( 50, 50, 50 ), materials );
                this.scene.add( cube );
                cube.it = it;
                it.model = cube;
                    console.log (items.length + ' - ' + it.name);
                
                    var
                    newLevel = (
                        Object.keys(t.ld2).length > 1
                        ? parseInt(Object.keys(t.ld2).reduce(function(a, b){ return t.ld2[a] > t.ld2[b] ? a : b }))+1
                        : 2
                    );
                    cd.level = newLevel;
                //setTimeout (function() {
                    //t.loading = false;
                    cd.t.initializeItems_do (cd.t, cd.items, cd.itd, cd.it.idx, newLevel, cd.levelDepth, cd.idxPath, filepath+'/'+key);
                //}, 50);
                
                /*
                t.loading = true;
                t.loader.load( '/NicerAppWebOS/3rd-party/3D/models/folder icon/scene.gltf', function ( gltf, cd) {
                    clearTimeout (t.onresizeInitTimeout);
                    
                    gltf.scene.scale.setScalar (10);
                    t.scene.add (gltf.scene);
                    cd.it.model = gltf.scene;
                    cd.it.model.it = cd.it;
                    cd.t.updateTextureEncoding(t, gltf.scene);
                    t.initCounter++;
                    
                    var
                    newLevel = (
                        Object.keys(t.ld2).length > 1
                        ? parseInt(Object.keys(t.ld2).reduce(function(a, b){ return t.ld2[a] > t.ld2[b] ? a : b }))+1
                        : 2
                    );
                    cd.level = newLevel;
                    
                    t.loading = false;                
                    cd.t.initializeItems (cd.t, cd.items, cd.itd, cd.it.idx, newLevel, cd.levelDepth, cd.path);
                    
                }, function ( xhr ) {
                    console.log( 'model "folder icon" : ' + ( xhr.loaded / xhr.total * 100 ) + '% loaded' );
                }, function ( error ) {
                    console.error( error );
                },  cd );*/
            } 
            t.ld2[level].initItemsDoingIdx++;
            
            clearTimeout (t.onresizeInitTimeout);
            t.onresizeInitTimeout = setTimeout(function() {
                var objs = [];
                for (var i=0; i<t.items.length; i++) if (t.items[i].model) objs[objs.length] = t.items[i].model;
                                               
                t.controls = new OrbitControls( t.camera, t.renderer.domElement );
                t.controls.autoRotate = true;
                //$('#autoRotate').removeClass('vividButtonSelected').addClass('vividButton');
                //t.controls.listenToKeyEvents( window ); // optional
                t.controls.enabled = false;
                setTimeout (function(){
                     t.controls.enabled = true;
                }, 1000);
                                               
                t.dragndrop = new DragControls( objs, t.camera, t.renderer.domElement );
                
                $(t.renderer.domElement).contextmenu(function() {
                    return false;
                });
                
                t.dragndrop.addEventListener( 'dragstart', function ( event ) {
                    if (t.controls) t.controls.dispose();
                                             
                    t.dragndrop.cube = event.object;
                    t.dragndrop.mouseX = t.mouse.layerX;
                    t.dragndrop.mouseY = t.mouse.layerY;
                    
                    let cube = event.object;
                    $(cube).hover(function() {
                        na.site.setStatusMsg ('Folder : '+cube.it.filepath);
                    }, function () {
                        na.site.setStatusMsg (na.site.settings.defaultStatusMsg);
                    });

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
                    //event.object.material.emissive.set( 0x000000 );
                    t.controls = new OrbitControls( t.camera, t.renderer.domElement );
                    //this.controls.autoRotate = true;
                    $('#autoRotate').removeClass('vividButtonSelected').addClass('vividButton');
                    //t.controls.listenToKeyEvents( window ); // optional
                    t.controls.enabled = true;
                    
                    if (t.showLines) t.drawLines(t);
                } );
                
                /*t.databaseToPosData(t, function(loadedPosData) {
                    if (!loadedPosData) t.onresize (t); else if (t.showLines) t.drawLines(t);
                });*/
                t.onresize(t);
                
            }, 200);
        }
    }
    
    onresize (t, levels) {
        if (!t) t = this;
        //debugger;
        na.m.waitForCondition ('waiting for other onresize commands to finish',
            function () { return t.resizing === false; },
            function () { t.onresize_do (t, levels); }, 
            200
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
                    column = 0,
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
                    //if (it.filepath=='siteMedia/backgrounds/tiled/active') debugger;
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
        
        
        for (var i=0; i<t.items.length; i++) {
            var
            offsetXY = 100,
            it = t.items[i],
            p = t.items[it.parent];
            
            if (p && p.parent && t.items[p.parent]) {
                var
                it2 = t.items[p.parent],
                ppLeftRight = it2.leftRight,
                ppUpDown = it2.upDown;                
            } else {
                var
                it2 = null,
                ppLeftRight = 1,
                ppUpDown = 1;
            };
            
            if (p) {
                
                var
                pmaxc = p.maxColumnIta.maxColumn,//p.level > 0 ? p.maxColumnIta.maxColumn : (p.maxColumnIta.maxColumn+1),
                pmaxr = p.maxRowIta.maxRow,
                pLeftRight = p.column > Math.floor(pmaxc / 2) ?  ppLeftRight * 1 : ppLeftRight * -1,
                pUpDown = p.row > Math.floor(p.maxRowIta.maxRow / 2) ? ppUpDown * 1 :  ppUpDown * -1,
                pModifierC = (
                    p.level > 1
                    ? pLeftRight
                    : it2 
                        ? p.model.position.x > it2.model.position.x ? 1 : -1
                        : 1
                ),
                pitcp = (
                      pModifierC * -1 * ((pmaxc / 2) - p.column)
                ),
                pitcPercentage = (pitcp*1.00) / pmaxc,
                pModifierR = (
                    p.level > 1
                    ? pUpDown
                    : it2 
                        ? p.model.position.y > it2.model.position.y ? 1 : -1
                        : 1
                ),
                pitrp = (
                      pModifierR * -1 * ((pmaxr / 2) - p.row)
                ),
                pitrPercentage = (pitrp*1.00) / pmaxr,
                pitc = offsetXY * pitcPercentage * p.maxColumnIta.maxColumn,
                pitr = offsetXY * pitrPercentage * p.maxRowIta.maxRow;

                it.pitcp = pitcp;
                it.pitrp = pitrp;
                it.parentColumOffset = pitc;
                it.parentRowOffset = pitr;
                it.parentColumOffsetPercentage = pitcPercentage;
                it.parentRowOffsetPercentage = pitrPercentage;
                it.parentLeftRight = pLeftRight;
                it.parentUpDown = pUpDown;
//debugger;
                var
                itmaxc = it.maxColumnIta.maxColumn,
                itmaxr = it.maxRowIta.maxRow,
                itLeftRight = pLeftRight,//it.column > Math.floor(itmaxc / 2) ? 1 : -1,
                itUpDown = pUpDown, //it.row > Math.floor(itmaxr / 2) ? 1 : -1,
                itc = (
                    itLeftRight * ((itmaxc / 2) - it.column)
                ),
                itcPercentage = (itc*1.00) / itmaxc,
                itr = (
                    itUpDown * ((itmaxr / 2) - it.row)
                ),
                itrPercentage = (itr*1.00) / itmaxr,
                itco = offsetXY * itcPercentage * it.maxColumnIta.maxColumn,
                itro = offsetXY * itrPercentage * it.maxRowIta.maxRow;
                
                it.columnOffsetValue = itc;
                it.rowOffsetValue = itr;
                it.leftRight = itLeftRight;
                it.upDown = itUpDown;                
                
            } else { var mc = 0, mr = 0; };
        
            //if (p && p.name=='tiled') debugger;
            if (it.model && p && p.model) {
                
                var pOffsetX_now = 0, pOffsetY_now = 0;
                for (var l=0; l < t.items.length; l++) {
                    //debugger;
                    var it1 = t.items[l];
                    if (it.p && it1.idxPath===it.p.idxPath) {
                        pOffsetX_now = it1.p.leftRight * it1.p.column * 10;
                        pOffsetY_now = it1.p.upDown * it1.p.row * 10;
                    
                    }
                }
                
                it.model.position.x = Math.round(
                    p.model.position.x 
                    + pitc 
                     + ((p.column-1)*offsetXY) + ((p.column-1)*pOffsetX_now)
                    + ( ( p.leftRight * (it.column-1) * offsetXY))
                    + ( p.leftRight * t.items[it.parent].columnOffsetValue * 300 )
                );
                it.model.position.y = Math.round(
                    p.model.position.y 
                    + pitr 
                    + ((p.row-1)*offsetXY) + ((p.row-1)*pOffsetY_now)
                    + ( (p.upDown *  (it.row-1) * offsetXY))
                    + ( p.upDown * t.items[it.parent].rowOffsetValue * 200 )
                );
                it.model.position.z = -1 * ((it.level+1) * 500 );
                
                var x = it.data.it;
                //debugger;
                //if (p.name=='space stars night sky darkmode') debugger;
                //if (p.name=='sunrise sunset') debugger;
            }else if (it.model) {
                it.model.position.x = (it.column+1) * 100;
                it.model.position.y = (it.row-1) * 100;
                it.model.position.z = -1 * (it.level+1) * 100;
            }
                //if (p && (p.name=='tiled'||p.name=='iframe')) debugger;
                //if (p && (p.name=='landscape' || p.name=='scenery'||p.name=='animals')) debugger;
                //if (p && p.name=='space stars night sky darkmode') debugger;
        }
        
        //t.drawLines(t);

        setTimeout(function() {
            t.onresize_do_overlapChecks2(t, callback);
            //if (typeof callback=='function') callback(t);
        }, 10);
    }
    

    onresize_do_overlapChecks2 (t, callback) {
        //t.overlaps = [];
        for (var i=0; i < t.overlaps.length; i++) {
            var it = t.overlaps[i];
            it.overlappingItems_count = 0;
            it.conflicts = 0;
            it.itemsa = [];
            it.itemsb = [];
        }
       
        for (var patha in t.ld3) {
            if (patha!=='') {
                var ld3a = t.ld3[patha];
                for (var pathb in t.ld3) {
                    if (pathb!=='' && pathb!==patha) {
                        var ld3b = t.ld3[pathb];
                        
                        for (var i=0; i<ld3a.items.length; i++) {
                            var ita = t.items[ld3a.items[i]];
                            
                            for (var j=0; j<ld3b.items.length; j++) {
                                var itb = t.items[ld3b.items[j]];
                                
                                if (
                                    ita.model && itb.model
                                    /*
                                    && (
                                        ita.model.position.x >= itb.model.position.x
                                        && ita.model.position.x <= itb.model.position.x + 60
                                    )

                                    && (
                                        ita.model.position.y >= itb.model.position.y
                                        && ita.model.position.y <= itb.model.position.y + 60
                                    )*/

                                    && (
                                        ita.model.position.x >= itb.model.position.x - 20
                                        && ita.model.position.x <= itb.model.position.x + 90
                                    )
                                    && (
                                        ita.model.position.y >= itb.model.position.y - 20
                                        && ita.model.position.y <= itb.model.position.y + 90
                                    )
                                    /*
                                    && ita.model.position.x === itb.model.position.x
                                    && ita.model.position.y === itb.model.position.y
                                    */
                                    && ita.model.position.z === itb.model.position.z
                                ) {
                                    var have = false;
                                    for (var k=0; k<t.overlaps.length; k++) {
                                        if (
                                            (
                                                t.overlaps[k].patha === patha
                                                && t.overlaps[k].pathb === pathb
                                            )
                                            || (
                                                t.overlaps[k].patha === pathb
                                                && t.overlaps[k].pathb === patha
                                            )
                                        ) {
                                            have = true;
                                            break;
                                        }
                                            
                                    };
                                    if (!have) {
                                        t.overlaps.push ({overlappingItems_count : 0, patha : patha, pathb : pathb, conflicts : 1});
                                        var o = t.overlaps[t.overlaps.length-1];
                                    } else {
                                        var 
                                        o = t.overlaps[k];
                                        o.conflicts++;
                                        o.ita = ita;
                                        o.itb = itb;
                                    }
                                    t.overlaps[k].overlappingItems_count++;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        var 
        leastOverlappingItems = { overlappingItems_count : 200, j : -1 }, 
        mostOverlappingItems = { overlappingItems_count : 0, j : -1 }, 
        mostConflicts = {conflicts : 1, j : -1}, 
        largest = null, 
        smallest = null;
        
        for (var j=0; j<t.overlaps.length; j++) {
            if (t.overlaps[j].overlappingItems_count > mostOverlappingItems.overlappingItems_count)
                mostOverlappingItems = { overlappingItems_count : t.overlaps[j].overlappingItems_count, j : j};

            if (t.overlaps[j].overlappingItems_count < leastOverlappingItems.overlappingItems_count)
                leastOverlappingItems = { overlappingItems_count : t.overlaps[j].overlappingItems_count, j : j};
            
            if (t.overlaps[j].conflicts > mostConflicts.conflicts) mostConflicts = {conflicts:t.overlaps[j].conflicts, j : j};
            
            if (
                !largest 
                || (
                    t.ld3[t.overlaps[j].patha].itemCountA > largest.itemCountA 
                    && t.ld3[t.overlaps[j].pathb].itemCountB > largest.itemCountB
                )
            ) largest = { 
                pathb : t.overlaps[j].pathb, 
                itemCountA : t.ld3[t.overlaps[j].patha].itemCount, 
                itemCountB : t.ld3[t.overlaps[j].pathb].itemCount, 
                j : j 
            };
            
            if (
                !smallest 
                || (
                    t.ld3[t.overlaps[j].patha].itemCountA < smallest.itemCountA 
                    && t.ld3[t.overlaps[j].pathb].itemCountB < smallest.itemCountB
                )
            ) smallest = { 
                pathb : t.overlaps[j].pathb, 
                itemCountA : t.ld3[t.overlaps[j].patha].itemCount, 
                itemCountB : t.ld3[t.overlaps[j].pathb].itemCount, 
                j : j 
            };
                
        }
        
        /*
        // this for loop can be commented out for speed optimization, it's only here for debugging purposes
        for (var i=0; i<t.overlaps.length; i++) {
            var o = t.overlaps[i];
            o.itemsa = [];
            o.itemsb = [];
            for (var j=0; j<t.items.length; j++) {
                var it = t.items[j];
                if (it.path === o.patha) { o.itemsa.push(it); o.parenta = t.items[it.parent]; }
                if (it.path === o.pathb) { o.itemsb.push(it); o.parentb = t.items[it.parent]; }
            }
        };*/
        
        for (var j=0; j<t.items.length; j++) {
            t.items[j].adjustedModXmin = 0;
            t.items[j].adjustedModXadd = 0;
            t.items[j].adjustedModYmin = 0;
            t.items[j].adjustedModYadd = 0;
            t.items[j].assignments = [];
        };

        for (var i=0; i<t.overlaps.length; i++) {
            //if (i===mostConflicts.j) {

            if (i===largest.j) {


                var 
                x = mostOverlappingItems,
                overlapFixes = [ 'top', 'topright', 'middleright', 'bottomright', 'bottom', 'bottomleft', 'middleleft', 'topleft'],
                overlapFixData = [],
                overlapFix = null;
                
                for (var j=0; j<overlapFixes.length; j++) {
                    var 
                    ofs = overlapFixes[j], 
                    d = { quadrant : ofs };
                    
                    if (ofs.match('top')) d.upDown = 1; else if (ofs.match('bottom')) d.upDown = -1; else d.upDown = 0;
                    if (ofs.match('left')) d.leftRight = -1; else if (ofs.match('right')) d.leftRight = 1; else d.leftRight = 0;
                    

                    d.newPos = t.onresize_testCalculateOverlaps (t, t.overlaps[i].patha, t.overlaps[i].pathb, d);
                    overlapFixData.push (d);
                }
                
                overlapFix = t.onresize_calculateBestOverlapFix (t, i, overlapFixData);
                if (overlapFix) {
                    if (!t.overlaps[i].history) t.overlaps[i].history = [];
                    if (overlapFix[0]) t.overlaps[i].history.push (overlapFix[0].quadrant);
                    t.onresize_applyBestOverlapFix (t, overlapFix);
                } else {
                    t.overlaps.splice (i, 1);
                    break;
                }
                //debugger;
            }
        }   
        if (t.overlaps.length > 0) {
            //debugger;
            //console.log ('onresize_do_overlapChecks2() : t.overlaps.length='+t.overlaps.length, t.overlaps);
            setTimeout (function() {
                t.onresize_do_overlapChecks2(t, callback);
            }, 10);
        } else {
            t.drawLines(t);
            if (typeof callback=='function') callback(t);
        }
    }
    
    onresize_applyBestOverlapFix (t, overlapFix) {
        var fix = overlapFix[0];
        if (fix)
        for (var i=0; i<fix.itemsa.length; i++) {
            var ita = t.items[fix.itemsa[i].idx];
            if (ita.model) {
                ita.model.position.x = fix.itemsa[i].x;
                ita.model.position.y = fix.itemsa[i].y;
                ita.model.position.z = fix.itemsa[i].z;
            }
        }
    }
    
    onresize_calculateBestOverlapFix (t, i, overlapFixData) {
        var
        compare = function (a,b) { return a.newPos.overlaps.length - b.newPos.overlaps.length; },
        x = overlapFixData.sort(compare).reverse(),
        compare2 = function (a,b) {
            if (
                a.newPos.overlaps[0] 
                && b.newPos.overlaps[0]
                && x[0].newPos.overlaps[0]
               // && a.newPos.overlaps[0].patha == x[0].newPos.overlaps[0].patha
              //  && b.newPos.overlaps[0].pathb == x[0].newPos.overlaps[0].pathb
            ) return a.newPos.overlaps[0].overlappingItems_count - b.newPos.overlaps[0].overlappingItems_count;
            else return 0;
        },
        y = x.sort(compare2);

        var allGood = true;
        for (var j=0; j < overlapFixData.length; j++) {
            if (overlapFixData[j].newPos.overlaps.length > 0) allGood = false;
        }
        if (allGood) {
            //debugger;
            return false;
        }


        if (typeof t.overlaps[i].history=='object') {
            for (var j=1; j<=10; j++) {
                var pat = t.findArrayPattern (t.overlaps[i].history,j);
                if (pat && typeof pat!=='boolean') {

                    var
                    h = t.overlaps[i].history;
                    /*
                    strategy = h[pat.idx],
                    strategies = [];
                    strategies.push(strategy);
                    strategies.push(strategy);
                    */

                    var
                    x = t.overlaps[i],
                    itap = x.ita.idxPath,
                    itapTxt = t.translateIdxPathToText(t, itap),
                    itbp = x.itb.idxPath,
                    itbpTxt = t.translateIdxPathToText(t, itbp),
                    ld3a = t.ld3[itap],
                    ld3b = t.ld3[itbp],
                    kpda = { path : itap },
                    kpdb = { path : itbp };

                    for (var k=0; k<ld3a.items.length-1; k++) {
                        var
                        l = ld3a.items[k],
                        it = t.items[l],
                        kp = it.idxPath+','+it.idx,
                        kpTxt = t.translateIdxPathToText(t, kp);

                        kpda[l] = kpTxt;
                    }

                    for (var k=0; k<ld3b.items.length-1; k++) {
                        var
                        l = ld3b.items[k],
                        it = t.items[l],
                        kp = it.idxPath+','+it.idx,
                        kpTxt = t.translateIdxPathToText(t, kp);

                        kpdb[l]= kpTxt;
                    }

                    var
                    overlapFixes = [ 'top', 'topright', 'middleright', 'bottomright', 'bottom', 'bottomleft', 'middleleft', 'topleft'],
                    po = Object.assign([], overlapFixes);
                    for (var k=0; k<po.length; k++) {
                        for (var l=0; l<pat.r.length; l++) {
                            if (po[k] === pat.r[l]) {
                                po.splice(k,1);
                                k--
                            }
                        }
                    }

                    // pick a random strategy
                    var
                    rnda = Math.floor(Math.random() * po.length),
                    rndb = Math.floor(Math.random() * po.length),
                    strategyA = po[rnda],
                    itaQuadrant = '',
                    itbQuadrant = '',
                    itaParent = t.items[x.ita.parent],
                    itbParent = t.items[x.itb.parent];

                    switch (strategyA) {
                        case 'top' : var strategyB = 'bottom'; break;
                        case 'topright' : var strategyB = 'bottomleft'; break;
                        case 'middleright' : var strategyB = 'middleleft'; break;
                        case 'bottomright' : var strategyB = 'topleft'; break;
                        case 'bottom' : var strategyB = 'top'; break;
                        case 'bottomleft' : var strategyB = 'topright'; break;
                        case 'middleleft' : var strategyB = 'middleright'; break;
                        case 'topleft' : var strategyB = 'bottomright'; break;
                    }
                    /*
                    if (x.ita.upDown < 0) itaQuadrant += 'bottom';
                    if (x.ita.upDown === 0) itaQuadrant += 'middle';
                    if (x.ita.upDown > 0) itaQuadrant += 'top';
                    if (x.itb.upDown < 0) itbQuadrant += 'bottom';
                    if (x.itb.upDown === 0) itbQuadrant += 'middle';
                    if (x.itb.upDown > 0) itbQuadrant += 'top';
                    if (x.ita.leftRight < 0) itaQuadrant += 'left';
                    if (x.ita.leftRight > 0) itaQuadrant += 'right';
                    if (x.itb.leftRight < 0) itbQuadrant += 'left';
                    if (x.itb.leftRight > 0) itbQuadrant += 'right';
                    */

                    if (itaParent.upDown < 0) itaQuadrant += 'bottom';
                    if (itaParent.upDown === 0) itaQuadrant += 'middle';
                    if (itaParent.upDown > 0) itaQuadrant += 'top';

                    if (itbParent.upDown < 0) itbQuadrant += 'bottom';
                    if (itbParent.upDown === 0) itbQuadrant += 'middle';
                    if (itbParent.upDown > 0) itbQuadrant += 'top';

                    if (itaParent.leftRight < 0) itaQuadrant += 'left';
                    if (itaParent.leftRight > 0) itaQuadrant += 'right';

                    if (itbParent.leftRight < 0) itbQuadrant += 'left';
                    if (itbParent.leftRight > 0) itbQuadrant += 'right';

                    t.onresize_applyBestOverlapFix2 (t, y, itaQuadrant, itbQuadrant);
                    //t.onresize_applyBestOverlapFix2 (t, y, strategyA, strategyA);
                    debugger;
                    return true;
                }
            }
        }
        
        return y;
    }

    onresize_applyBestOverlapFix2 (t, overlapFix, itaQuadrant, itbQuadrant) {
        var fix = null;
        for (var i=0; i<overlapFix.length; i++) {
            if (overlapFix[i].quadrant==itaQuadrant) fix = overlapFix[i];
        }
        for (var i=0; i<fix.itemsa.length; i++) {
            var ita = t.items[fix.itemsa[i].idx];
            if (
                typeof fix.itemsa[i].xOffset==='number'
                && typeof fix.itemsa[i].yOffset==='number'
                && ita.model
            ) {
                ita.model.position.x += fix.itemsa[i].xOffset;
                ita.model.position.y += fix.itemsa[i].yOffset;
                ita.model.position.z = fix.itemsa[i].z;
            }/* else if (ita.model) {
                ita.model.position.x = fix.itemsa[i].x;
                ita.model.position.y = fix.itemsa[i].y;
                ita.model.position.z = fix.itemsa[i].z;
            }*/
        }
        var fix = null;
        for (var i=0; i<overlapFix.length; i++) {
            if (overlapFix[i].quadrant==itbQuadrant) fix = overlapFix[i];
        }
        for (var i=0; i<fix.itemsb.length; i++) {
            var ita = t.items[fix.itemsb[i].idx];
            if (
                typeof fix.itemsb[i].xOffset==='number'
                && typeof fix.itemsb[i].yOffset==='number'
                && ita.model
            ) {
                ita.model.position.x += fix.itemsb[i].xOffset;
                ita.model.position.y += fix.itemsb[i].yOffset;
                ita.model.position.z = fix.itemsb[i].z;
            }
        }
    }

    translateIdxPathToText (t, idxPath) {
        var
        r = '',
        parts = idxPath.split(',');

        for (var i=0; i<parts.length; i++) {
            if (r!=='') r+='/';
            r+=t.items[parseInt(parts[i])].name;
        }

        return r;
    }

    findArrayPattern (arr, l) {
        var
        p1 = [],
        p2 = [];

        if (arr.length < (l*2) -1 ) {
            return false;
        }
        //debugger;

        for (var point1 = arr.length-1; point1 > arr.length - 1 - l; point1--) {
            p1.push (arr[point1]);
        }
        var allGood = true, i = 0;
        for (var point2 = point1; point2 > point1 - l; point2--) {
            if (
                arr[point2]
                && p1[i] !== arr[point2]
            ) allGood = false;

            p2.push (arr[point2]);
            i++;
        }
        if (allGood) {
            var x = {
                r : arr.slice().splice(arr.length-1-l, l),
                idx : arr.length - 1 - (l*2)
            };
            return x;
        };

        return false;
    }

    onresize_testCalculateOverlaps (t, patha, pathb, ofd4quadrant) {
        var r = {
            overlaps : []
        };
        ofd4quadrant.itemsa = [];
        ofd4quadrant.itemsb = [];
        
        
        var ld3a = t.ld3[patha];
        var ld3b = t.ld3[pathb];
        var psi = null;
        for (var i=0; i<ld3a.items.length; i++) {
            var ita = t.items[ld3a.items[i]];
            if (!ita.model) continue;
            ofd4quadrant.itemsa.push($.extend({},ita.model.position));
            var ita1 = ofd4quadrant.itemsa[ofd4quadrant.itemsa.length-1];
            ita1.idx = ita.idx;
            //debugger;
            //ita1.x += ofd4quadrant.leftRight * 500;
            //ita1.y += ofd4quadrant.upDown * 500;
            var
            ps = ita.idxPath.split(','),
            //psi = !psi ? Math.round(Math.random() * (ps.length + 1)) : psi,
            pidx = ps[ps.length-1],
            itaParent = null,
            offset = 55;//t.items[pidx];

            if (itaParent) {
                ita1.xOffset = itaParent.leftRight * offset;
                ita1.yOffset = itaParent.upDown * offset;
                ita1.x += itaParent.leftRight * offset;
                ita1.y += itaParent.upDown * offset;
            } else {
                ita1.xOffset = ofd4quadrant.leftRight * offset;
                ita1.yOffset = ofd4quadrant.upDown * offset;
                ita1.x += ofd4quadrant.leftRight * offset;
                ita1.y += ofd4quadrant.upDown * offset;
                //ita1.x += ita.leftRight * 50;
                //ita1.y += ita.upDown * 50;
            }


            for (var j=0; j<ld3b.items.length; j++) {
                var itb = t.items[ld3b.items[j]];

                var
                itan = '',
                itap = ita.idxPath.split(','),
                itbn = '',
                itbp = itb.idxPath.split(',');
                for (var k=0; k<itap.length; k++) {
                    if (itan!=='') itan += '/';
                    itan += t.items[parseInt(itap[k])].name;
                }
                itan += '/' + ita.name;
                for (var k=0; k<itbp.length; k++) {
                    if (itbn!=='') itbn += '/';
                    itbn += t.items[parseInt(itbp[k])].name;
                }
                itbn += '/' + itb.name;

                var
                dbg = {
                    //i : i,
                    //j : j,
                    //la : ld3a.items.length,
                    //lb : ld3b.items.length,
                    lan : itan,
                    lbn : itbn,
                    ita : ita,
                    itb : itb,
                    ofd4quadrant : ofd4quadrant
                };


                if (!itb.model) continue;
                if (i===0) {
                    ofd4quadrant.itemsb.push($.extend({},itb.model.position));
                    var itb1 = ofd4quadrant.itemsb[ofd4quadrant.itemsb.length-1];
                    itb1.idx = itb.idx;
                } else {
                    var itb1 = null;
                    for (var k=0; k<ofd4quadrant.itemsb.length; k++) {
                        if (
                            //itb.model.position.x === ofd4quadrant.itemsb[k].x
                            //&& itb.model.position.y === ofd4quadrant.itemsb[k].y
                            (
                                ita.model.position.x >= itb.model.position.x
                                && ita.model.position.x <= itb.model.position.x + 70
                            )
                            && (
                                ita.model.position.y >= itb.model.position.y
                                && ita.model.position.y <= itb.model.position.y + 70
                            )
                            && itb.model.position.z === ofd4quadrant.itemsb[k].z
                        ) itb1 = ofd4quadrant.itemsb[k];
                    }
                }
                
                if (
                    ita1 && itb1 &&
                    (
                        ita1.x >= itb1.x - 20
                        && ita1.x <= itb1.x + 90
                    )
                    && (
                        ita1.y >= itb1.y - 20
                        && ita1.y <= itb1.y + 90
                    )
                    && ita1.z === itb1.z
                ) {
                    var have = false;
                    for (var k=0; k<r.overlaps.length; k++) {
                        if (
                            (
                                r.overlaps[k].patha === patha
                                && r.overlaps[k].pathb === pathb
                            )
                            || (
                                r.overlaps[k].patha === pathb
                                && r.overlaps[k].pathb === patha
                            )
                        ) {
                            have = true;
                            break;
                        }
                            
                    };
                    if (!have) {
                        //console.log ('onresize_testCalculateOverlaps() : '+ofd4quadrant.quadrant, dbg);
                        r.overlaps.push ({overlappingItems_count : 0, patha : patha, pathb : pathb, conflicts : 1 });
                        var o = r.overlaps[t.overlaps.length-1];
                    } else {
                        var 
                        o = r.overlaps[k];
                        o.conflicts++;
                    }
                    r.overlaps[k].overlappingItems_count++;
                }
            }
        }
        
        r.leastOverlappingItems = { overlappingItems_count : 200, j : -1 }, 
        r.mostOverlappingItems = { overlappingItems_count : 0, j : -1 }, 
        r.mostConflicts = {conflicts : 1, j : -1};
        
        for (var j=0; j<r.overlaps.length; j++) {
            if (r.overlaps[j].overlappingItems_count > r.mostOverlappingItems.overlappingItems_count)
                r.mostOverlappingItems = { overlappingItems_count : r.overlaps[j].overlappingItems_count, j : j};

            if (r.overlaps[j].overlappingItems_count < r.leastOverlappingItems.overlappingItems_count)
                r.leastOverlappingItems = { overlappingItems_count : r.overlaps[j].overlappingItems_count, j : j};
            
            if (r.overlaps[j].conflicts > r.mostConflicts.conflicts) r.mostConflicts = {conflicts:r.overlaps[j].conflicts, j : j};
        }
        
        return r;
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
            parent = t.items[it.parent],
            haveThisLineAlready = false;
            
            if (!this.showLines) return false;
            if (!it.model) return false;
            
            if (it.parent===0 || typeof it.parent === 'undefined') continue;
            
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
                if (!t.lineColors[it.parent]) {
                    var x=Math.round(0xffffff * Math.random()).toString(16);
                    var y=(6-x.length);
                    var z="000000";
                    var z1 = z.substring(0,y);
                    var color= z1 + x;                    
                    t.lineColors[it.parent] = color;
                }
                var color = t.lineColors[it.parent];
                
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
        /*const encoding = this.state.textureEncoding === 'sRGB'
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

            /*if ((!envMap || !this.state.background) && this.activeCamera === this.defaultCamera) {
                t.scene.add(this.vignette);
            } else {
                t.scene.remove(this.vignette);
            }*/

            t.scene.environment = envMap;
            //this.scene.background = this.state.background ? envMap : null;

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

                    const envMap = this.pmremGenerator.fromEquirectangular( texture ).texture;
                    this.pmremGenerator.dispose();

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
        this.p = parent;
        this.el = el;
        this.t = $(this.el).attr('theme');
        
        this.data = data;
        
        this.lights = [];
        this.folders = [];
   
        this.items = [];
        
        this.scene = new THREE.Scene();
        this.camera = new THREE.PerspectiveCamera( 75, $(el).width() / $(el).height(), 0.1, 1000 );
        

        this.renderer = new THREE.WebGLRenderer({alpha:true, antialias : true});
        this.renderer.physicallyCorrectLights = true;
        this.renderer.outputEncoding = sRGBEncoding;
        this.renderer.setPixelRatio (window.devicePixelRatio);
        this.renderer.setSize( $(el).width()-20, $(el).height()-20 );
        
        this.renderer.toneMappingExposure = 1.0;
        
        el.appendChild( this.renderer.domElement );
        
        this.controls = new OrbitControls( this.camera, this.renderer.domElement );
        //this.controls.listenToKeyEvents( window ); // optional
        
        this.loader = new GLTFLoader();
        
        this.loader.load( '/NicerAppWebOS/3rd-party/3D/models/human armor/scene.gltf', function ( gltf ) {
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
        this.loader.load( '/NicerAppWebOS/3rd-party/3D/models/photoCamera/scene.gltf', function ( gltf ) {
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
        this.camera.add( light1 );

        const light2  = new DirectionalLight(0xFFFFFF, 0.8 * Math.PI);
        light2.position.set(0.5, 0, 0.866); // ~60º
        light2.name = 'main_light';
        light2.intensity = 0.8 * Math.PI;
        light2.color = 0xFFFFFF;
        this.camera.add( light2 );

        this.lights.push(light1, light2);        
        
        this.pmremGenerator = new PMREMGenerator( this.renderer );
        this.pmremGenerator.compileEquirectangularShader();
        
        this.updateEnvironment(this);
        
        $(el).bind('mousemove', function() { t.onMouseMove (event, t) });
        
        this.raycaster = new THREE.Raycaster();
        this.mouse = new THREE.Vector2();
        this.mouse.x = 0;
        this.mouse.y = 0;

        this.camera.position.z = 700;
        
        this.animate(this);
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
        /*const encoding = this.state.textureEncoding === 'sRGB'
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
            if (!envMap || !this.state.background) && this.activeCamera === this.defaultCamera) {
                t.scene.add(this.vignette);
            } else {
                t.scene.remove(this.vignette);
            }*/
            t.scene.add(this.vignette);

            t.scene.environment = envMap;
            //this.scene.background = envMap;//this.state.background ? envMap : null;

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

                    const envMap = this.pmremGenerator.fromEquirectangular( texture ).texture;
                    this.pmremGenerator.dispose();

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
        this.p = parent;
        this.el = el;
        this.t = $(this.el).attr('theme');
        
        this.scene = new THREE.Scene();
        this.camera = new THREE.PerspectiveCamera( 75, $(el).width() / $(el).height(), 0.1, 1000 );

        this.renderer = new THREE.WebGLRenderer({ alpha : true });
        this.renderer.setSize( $(el).width()-20, $(el).height()-20 );
        el.appendChild( this.renderer.domElement );
        
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
        this.cube = new THREE.Mesh( new THREE.BoxGeometry( 1, 1, 1 ), materials );
        this.scene.add( this.cube );
        var t = this;
        $(el).bind('mousemove', function() { t.onMouseMove (event, t) });
        
        this.raycaster = new THREE.Raycaster();
        this.mouse = new THREE.Vector2();

        this.camera.position.z = 5;
        this.cube.rotation.x = 0.3;
        this.cube.rotation.y = 0.4;
        this.animate(this);
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
