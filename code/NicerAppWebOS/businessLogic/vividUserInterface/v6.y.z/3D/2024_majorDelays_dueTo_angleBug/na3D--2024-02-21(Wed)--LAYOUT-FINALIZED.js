/*--- LICENSE : https://opensource.org/licenses/MIT
----- Copyright 2002-2024 by Nicer Enterprises
---*/

import * as THREE from '/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.module.js';
import { Stats } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/libs/stats.module.js';
import { GLTFLoader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/GLTFLoader.js';
import { FBXLoader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/FBXLoader.js';
import { KTX2Loader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/KTX2Loader.js';
import { DRACOLoader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/DRACOLoader.js';
import { OrbitControls } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/OrbitControls.js';
import { RGBELoader } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/RGBELoader.js';
import { DragControls } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/DragControls.js';
import { FlyControls } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/FlyControls.js';
import { FirstPersonControls } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/FirstPersonControls.js';
import gsap from "https://unpkg.com/gsap@3.12.2/index.js";
import { CameraControls, approxZero } from '/NicerAppWebOS/3rd-party/3D/libs/three.js/dist_camera-controls.module.js';


/*
  import {
    CSS2DRenderer,
    CSS2DObject,
  } from 'https://unpkg.com/three@0.125.2/examples/jsm/renderers/CSS2DRenderer.js';
*/

export class na3D_fileBrowser {
    constructor(el, parent, parameters) {
        var t = this;

        t.debug = true;
        
        t.autoRotate = false;
        t.showLines = true;
        t.showFiles = true;
        t.animationDuration = 20;
        
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
        t.items = [];
        t.itemsFolders = [];
        t.meshLength = 100;

        //na.d.s.visibleDivs.push ('#siteToolbarLeft');
        na.d.s.visibleDivs.push ('#siteToolbarRight');
        na.desktop.resize();

        var it = {
            id : na.m.randomString(),
            name : 'music',
            idx : 0,
            levelIdx : 0,
            level : 0,
            offsetY : 0,
            offsetX : 0,
            offsetZ : 0,
            column : 0,
            row : 0,
            depth : 0,
            pos : {x : 0, y : 0, z : 0},
            columnCount : 1,
            rowCount : 1,
            depthCount : 1,
            columnField : 0,
            rowField : 0,
            idxPath : '/0',
            filepath : '/0/filesAtRoot/folders',
            leftRight : 0,
            upDown : 0,
            backForth : 0,
            columnOffsetValue : 0,
            rowOffsetValue : 0,
            depthOffsetValue : 0,
            parentRowOffset : 0,
            parentColumOffset : 0,
            model : { position : { x : 0, y : 0, z : 0 } }
        }, fit = {
            id : na.m.randomString(),
            type : 'naFolder',
            text : 'music',
            parent : '#',
            idx : 0,
            idxPath : '/0',
            state : { opened : true }
        };
        t.items.push (it);
        t.itemsFolders.push (fit);


        var sideLength = t.meshLength, length = sideLength, width = sideLength;
        var
        materials2 = [
            new THREE.MeshBasicMaterial({
                color : it.color ? it.color : 'rgb(0,0,255)',
                opacity : 0.5,
                transparent : true
            }),
            new THREE.MeshBasicMaterial({
                color : it.color ? it.color : 'rgb(0,0,255)',
                opacity : 0.5,
                transparent : true
            }),
            new THREE.MeshBasicMaterial({
                color : it.color ? it.color : 'rgb(0,0,255)',
                opacity : 0.5,
                transparent : true
            }),
            new THREE.MeshBasicMaterial({
                color : it.color ? it.color : 'rgb(0,0,255)',
                opacity : 0.5,
                transparent : true
            }),
            new THREE.MeshBasicMaterial({
                color : it.color ? it.color : 'rgb(0,0,255)',
                opacity : 0.5,
                transparent : true
            }),
            new THREE.MeshBasicMaterial({
                color : it.color ? it.color : 'rgb(0,0,255)',
                opacity : 0.5,
                transparent : true
            })

        ];
        var cube = new THREE.Mesh( new THREE.BoxGeometry( t.meshLength, t.meshLength, t.meshLength ), materials2 );
        it.model = cube;
        
        t.lines = []; // onhover lines only in here
        t.permaLines = []; // permanent lines, the lines that show all of the parent-child connections.
        t.s2 = []; // search array filled with the files and folders three.js models, used by raycaster.intersectObjects()

        var 
        c = $.cookie('3DFDM_lineColors');
        if (typeof c=='string' && c!=='') {
            t.lineColors = JSON.parse(c);
        }
        
        t.scene = new THREE.Scene();
        t.scene.add(cube)
        t.scene.add(new THREE.AxesHelper(5000))
        t.camera = new THREE.PerspectiveCamera( 90  , $(el).width() / $(el).height(), 0.01, 100*1000 );

        
        t.renderer = new THREE.WebGLRenderer({alpha:true, antialias : true});
        t.renderer.physicallyCorrectLights = true;
        t.renderer.outputEncoding = THREE.sRGBEncoding;
        t.renderer.setPixelRatio (window.devicePixelRatio);
        t.renderer.setSize( $(el).width()-20, $(el).height()-20 );
        t.renderer.toneMappingExposure = 1.0;
        
        el.appendChild( t.renderer.domElement );


        $(t.renderer.domElement).bind('mousemove', function() {
            //event.preventDefault(); 
            t.onMouseMove (event, t)
        });
        $(t.renderer.domElement).bind('pointerup', function() { t.onPointerUp (event, t) });
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
        
        const light1  = new THREE.AmbientLight(0xFFFFFF, 0.3);
        light1.name = 'ambient_light';
        light1.intensity = 0.3;
        light1.color = 0xFFFFFF;
        t.camera.add( light1 );

        const light2  = new THREE.DirectionalLight(0xFFFFFF, 0.8 * Math.PI);
        light2.position.set(0.5, 0, 0.866); // ~60º
        light2.name = 'main_light';
        light2.intensity = 0.8 * Math.PI;
        light2.color = 0xFFFFFF;
        t.camera.add( light2 );
        
        t.lights.push(light1, light2);
        
        t.pmremGenerator = new THREE.PMREMGenerator( t.renderer );
        t.pmremGenerator.compileEquirectangularShader();
        
        //t.updateEnvironment(this);
        
        t.raycaster = new THREE.Raycaster();
        t.mouse = new THREE.Vector2();
        t.mouse.x = 0;
        t.mouse.y = 0;

        // Handle window resize
        window.addEventListener('resize', () => {
            setTimeout(function() {
                //const { innerWidth, innerHeight } = window;
                var innerWidth = $('#siteContent .vividDialogContent').width();
                var innerHeight = $('#siteContent .vividDialogContent').height() - $('#header').position().top - $('#header').height();

                t.renderer.setSize(innerWidth, innerHeight);
                t.camera.aspect = innerWidth / innerHeight;
                t.camera.updateProjectionMatrix();
            }, 800);
        });
        setTimeout (function() {
            var innerWidth = $('#siteContent .vividDialogContent').width();
            var innerHeight = $('#siteContent .vividDialogContent').height() - $('#header').position().top - $('#header').height();

            t.renderer.setSize(innerWidth, innerHeight);
            t.camera.aspect = innerWidth / innerHeight;
            t.camera.updateProjectionMatrix();
        }, 500);

        t.renderer.setAnimationLoop(() => {
            //controls.update();

            // Pick objects from view using normalized mouse coordinates
            t.raycaster.setFromCamera(t.mouse, t.camera);

        });

        CameraControls.install ({ THREE : THREE });
        t.clock = new THREE.Clock();
        t.lookClock = -1;

        t.orbitControls = new OrbitControls( t.camera, t.renderer.domElement );
        t.orbitControls.enabled = true;
        //t.orbitControls.listenToKeyEvents( window ); // optional

        t.cameraControls = new CameraControls (t.camera, t.renderer.domElement);
        t.cameraControls.enabled = false;

        t.flyControls = new FlyControls (t.camera, t.renderer.domElement, t.cameraControls);
        t.flyControls.enabled = false;
        t.flyControls.movementSpeed = 2000;
        t.flyControls.dragToLook = true;
        t.flyControls.rollSpeed = Math.PI / 24;
        t.flyControls.autoMove = false;

        t.fpControls = new FirstPersonControls (t.camera, t.renderer.domElement);
        t.fpControls.enabled = false;
        t.fpControls.movementSpeed = 3000;
        t.fpControls.lookSpeed = Math.PI/24;
        t.fpControls.activeLook = false;
        t.fpControls.mouseDragOn = false;
        t.fpControls.moveForward = false;
        setTimeout(function() {
            t.initializeItems (t);
            //t.initializeFolderList (t, t.data);

            //t.camera.lookAt (t.s2[0].position);
            //t.cameraControls._camera.lookAt (t.s2[0].position);

            na.m.waitForCondition('animate?', function() {
                return t.items.length > 2 && t.winners
            }, function() {
                t.animate(t, null);
            }, 2)
        }, 25);
    }
    /*
    animate(t, evt) {
        requestAnimationFrame( function(evt) { t.animate (t,evt) });
        const delta = t.clock.getDelta();
        t.cameraControls.enabled = true;
        t.cameraControls.update (delta, true);
        //t.orbitControls.enabled = true;
        //t.orbitControls.update (delta);
        t.renderer.render( t.scene, t.camera );
    }*/


    animate(t, p) {
        requestAnimationFrame( function(p) { t.animate (t,p) });
        //if (t.mouse.x!==0 || t.mouse.y!==0) {

            for (var i=0; i<t.s2.length; i++) {
                var it = t.s2[i];
                it.updateMatrixWorld();
            };
            t.raycaster.setFromCamera (t.mouse, t.camera);

            t.scene.matrixWorldAutoUpdate = true;;
            t.camera.matrixWorldAutoUpdate = true;

            //t.flyControls.enabled = false;

            //if (t.debug) console.log ('t.lookClock', t.lookClock);
            if (t.lookClock === -2) {
                t.lookClock = Date.now();
            }
            if (t.lookClock > 0) {
                //debugger;
                var delta2 = Date.now() - 1000;
                if (t.debug) console.log ('animate(): delta2', delta2 > t.lookClock);
            } else {
                var delta2 = 0;
            }
            const delta = t.clock.getDelta();
            /*
            var dbg = {
                    't.cameraControls.deltaX' : t.cameraControls.deltaX,
                    't.cameraControls.deltaY' : t.cameraControls.deltaY
                };
            if (t.debug) console.log (dbg);*/

            if (false) {
                var x = t.cameraControls._activePointers;
                //if (x[0]) debugger;
                var dbg = {
                    'x[0]' : x[0],
                    'x[0].mouseButton' : x[0] ? x[0].mouseButton : null,
                    't.lookClock' : t.lookClock,
                    'delta2' : delta2 > t.lookClock
                };
                if (t.debug) console.log (dbg);
            }

            /*
            if (t.animPlaying) {
                t.fpControls.enabled = false;
                t.cameraControls.enabled = true;
            } else {
                t.fpControls.enabled = true;
                t.cameraControls.enabled = false;
            }*/

            /*
            if (delta2 > t.lookClock) {
                t.orbitControls.enabled = false;
                t.flyControls.enabled = true;
            } else {
                t.orbitControls.enabled = true;
                t.flyControls.enabled = false;
            };*/

/*
            var threshold = 50;
            if (t.evt && t.evt2 && t.evt.buttons===1) {
                // left or middle mouse button(s) held down
                if (
                    //!t.cameraControls._isUserControllingTruck
                    (
                        t.evt.clientX - t.evt2.clientX < -1 * threshold
                        || t.evt.clientX - t.evt2.clientX > threshold
                        || t.evt.clientY - t.evt2.clientY < -1 * threshold
                        || t.evt.clientY - t.evt2.clientY > threshold
                    )
                ) {
                    // mouse pointer falls outside threshold from when first clicked

                    if (t.lookClock<0) {
                        t.lookClock = -2;
                    }
                } else {
                        delete t.evt2;
                        t.flyControls.enabled = false;
                        t.orbitControls.enabled = true;
                }
            } else if (t.evt && t.evt.buttons===2) {
                // right mouse button held down
                t.flyControls.enabled = false;
                t.orbitControls.enabled = true;
            }
*/



/*
            if (t.cameraControls.enabled) {
                /*t.camera.lookAt (t.middle.x, t.middle.y, t.middle.z);
                t.cameraControls.enabled = true;
                t.cameraControls.setLookAt (
                    t.cameraOrigin.x,
                    t.cameraOrigin.y,
                    t.cameraOrigin.z,
                    t.middle.x,
                    t.middle.y,
                    t.middle.z,
                    false
                );* /
                var tar = t.cameraControls._targetEnd.clone();
                tar.set(0,0,-10).applyQuaternion(t.camera.quaternion).add(t.camera.position);
                t.cameraControls.setLookAt (
                    t.camera.position.x,
                    t.camera.position.y,
                    t.camera.position.z,
                    tar.x,
                    tar.y,
                    tar.z,
                    false
                );
                t.cameraControls.update (delta, true);
            } else {
                /*t.fpControls.object.position.x = t.middle.x;
                t.fpControls.object.position.y = t.middle.y;
                t.fpControls.object.position.z = t.middle.z;* /
                t.fpControls.update(delta);
            }*/

            if (t.cameraOrigin && t.cameraOrigin.x && t.middle && t.middle.x) {
                if (t.cameraControls.enabled && t.cameraOrigin && t.cameraOrigin.x) {
                    /*
                    t.cameraControls.setLookAt (
                        t.cameraOrigin.x,
                        t.cameraOrigin.y,
                        t.cameraOrigin.z,
                        t.middle.x,
                        t.middle.y,
                        t.middle.z,
                        false
                    );*/
                }

                if (t.middle && t.middle.x) {
                    /*t.orbitControls.center =  new THREE.Vector3(
                        t.middle.x,
                        t.middle.y,
                        t.middle.z
                    );*/

                    if (!t.started3) {
                        t.camera.position.x = 0;
                        t.camera.position.y = t.middle.y
                        t.camera.position.z = 10*1000;//5 * t.middle.z ;
                        t.camera.lookAt (t.middle.x, t.middle.y, t.middle.z);
                        t.started3 = true;
                    }
                };
            }

            /*
            if (t.middle && t.middle.x) {
                t.camera.position.x = t.middle.x;
                t.camera.position.y = t.middle.y;
                t.camera.position.z = t.middle.z;
                /*t.camera.setViewOffset(
                    $('#siteContent > .vividDialogContent').width(),
                    $('#siteContent > .vividDialogContent').height(),
                    t.middle.x, t.middle.y, 1920, 800
                );* /
            }

            if (t.cameraOrigin && t.cameraOrigin.x) {
                t.camera.position.x = t.cameraOrigin.x;
                t.camera.position.y = t.cameraOrigin.y;
                t.camera.position.z = t.cameraOrigin.z;
                /*t.camera.setViewOffset(
                    $('#siteContent > .vividDialogContent').width(),
                    $('#siteContent > .vividDialogContent').height(),
                    t.middle.x, t.middle.y, 1920, 800
                );* /
            }*/
            if (t.orbitControls.enabled/* && t.middle && t.middle.x*/) {
                //t.orbitControls.target.set(t.middle.x, t.middle.y, t.middle.z);
                t.orbitControls.update(delta);
            };
            if (t.flyControls.enabled && t.middle && t.middle.x) {
                //t.flyControls.object.set (t.middle.x, t.middle.y, t.middle.z);
                t.flyControls.update(delta);
            };
            if (t.fpControls.enabled) {
                t.fpControls.update(delta);
            };


            t.camera.updateProjectionMatrix();
            t.camera.updateWorldMatrix (true, false);

            var intersects = t.raycaster.intersectObjects (t.s2);
            t.hoverOverName = '';
            if (intersects[0] && intersects[0].object.type!=='Line')
            for (var i=0; i<intersects.length; i++) {
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
                        t.hoverOverName += hoveredItem.it.name;
                    //debugger;
                        var
                        it = hoveredItem.it;

                        // draw line to parent(s)
                        while (it && it.parent) {
                            var
                            parent = it.parent,
                            haveLine = false;

                            if (parent && parent.model) {
                                if (!haveLine) {
                                    var
                                    p1 = it.model.position,
                                    p2 = parent.model.position;
                                    //if (p1.x===0 && p1.y===0 && p1.z===0) continue;
                                    //if (p2.x===0 && p2.y===0 && p2.z===0) continue;
                                    const points = [];
                                    points.push( new THREE.Vector3( p1.x, p1.y, p1.z ) );
                                    points.push( new THREE.Vector3( p2.x, p2.y, p2.z ) );

                                    var
                                    geometry = new THREE.BufferGeometry().setFromPoints (points);


                                    geometry.dynamic = true;
                                    geometry.verticesNeedUpdate = true;

                                    var material = new THREE.LineBasicMaterial({ color: 0x0000FF, linewidth:2 });
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
                            it = it.parent;
                        }


                        // draw lines to children
                        for (var j=0; j<t.items.length; j++) {
                            var child = t.items[j];
                            //if (child.name.match(/\(Hard-/)) debugger;
                            //if (child.name.match(/Sabaton/)) debugger;
                            if (
                                hoveredItem && hoveredItem.it && hoveredItem.it.model && child && child.model && child.parent
                                && hoveredItem.it.idx=== child.parent.idx
                            ) {
                                //if (child.name.match(/\.mp3$/)) continue;

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

                                var material = new THREE.LineBasicMaterial({ color: 0xFF0000, linewidth : 1 });
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

                    //hoveredItem = t.items[hoveredItem.parent.idx];
                }

            if (!t.animPlaying) {
                    // show folder name for item under mouse and closest to the country
                    $('#site3D_label').css({display:'flex',opacity:1});

                    delete t.hovered;
                    const intersects2 = t.raycaster.intersectObjects (t.s2, true);
                    if (intersects2 && intersects2[0]) t.hovered = intersects2[0];
                    if (t.hovered && t.hovered.object.type!=='Line') {
                        t.drawLines(t);
                        //t.orbitControls.enabled = false;

                        // Setup label
                        t.renderer.domElement.className = 'hovered';

                        var
                        name = '',
                        parent = t.hovered;

                        while (parent) {
                            //$('#site3D_label')[0].textContent =
                            //  t.hovered.object.it.name.replace(/-\s*[\w]+\.mp3/, '.mp3');
                            var l =
                                parent.object.it.filepath
                                    .replace('/0/filesAtRoot/folders/','')
                                    .replace('/0/filesAtRoot/folders','');
                            if (l!=='') l+= '/';
                            l += parent.object.it.name.replace(/\s*-\s*[-_\w]+\.mp3$/,'.mp3')
                            //l += ' ('+parent.object.it.parent.rndz+')';
                            l = l.replace(/folders\//g, '');
                            /*
                            name += l+' ('+parent.object.it.model.position.x+','
                                +parent.object.it.model.position.y+','
                                +parent.object.it.model.position.z+') '
                                +'('+parent.object.it.columnOffsetValue+','
                                +parent.object.it.rowOffsetValue+','
                                +parent.object.it.depthOffsetValue+')<br/>';
                            */
                            parent = parent.parent;
                        }

                        name = l;
                        $('#site3D_label').html (name);


                        $('#site3D_label').css({
                            display : 'block',
                            left : t.mouse.layerX + 20,
                            top : t.mouse.layerY + 50
                        });
                        /*console.log({
                            name : $('#site3D_label')[0].textContent,
                            left : t.mouse.layerX + 20,
                            top : t.mouse.layerY + 20
                        });*/
                    } else {
                        //t.orbitControls.enabled = true;
                        t.renderer.domElement.className = '';
                        $('#site3D_label').css({ display : 'none' });
                    }
            } else {
                t.renderer.domElement.className = '';
                $('#site3D_label').css({ display : 'none' });
            }

            for (var i=0; i<t.lines.length; i++) {
                var it = t.lines[i];
                if (it && it.geometry) it.geometry.verticesNeedUpdate = true;
            };
            for (var i=0; i<t.permaLines.length; i++) {
                var it = t.permaLines[i];
                if (it && it.geometry) it.geometry.verticesNeedUpdate = true;
            };
        }
        
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
        t.mouse.layerX =  event.layerX;
        t.mouse.layerY =  event.layerY;
        t.mouse.clientX = event.clientX;
        t.mouse.clientY = event.clientY;
        t.mouse.event = event;
        t.fpControls.activeLook = event.buttons === 1;

        var rect = t.renderer.domElement.getBoundingClientRect();
        t.mouse.x = ( ( event.clientX - rect.left ) / ( rect.right - rect.left ) ) * 2 - 1;
        t.mouse.y = -1 * ( ( event.clientY - rect.top ) / ( rect.bottom - rect.top) ) * 2 + 1;
        t.evt = event;
        setTimeout (function(t, evt) {
            t.evt2 = evt;
        }, 1000, t, event);

        t.camera.updateMatrixWorld();
        t.raycaster.setFromCamera (t.mouse, t.camera);
    }

    onPointerUp( event, t ) {
        if (false) {
            if (t.flyControls.enabled) {
                t.flyControls.enabled = false;
                t.orbitControls.enabled = true;
                t.orbitControls.object.position.x = t.flyControls.object.position.x;
                t.orbitControls.object.position.y = t.flyControls.object.position.y;
                t.orbitControls.object.position.z = t.flyControls.object.position.z;
            } else {
                t.flyControls.enabled = true;
                t.flyControls.object.position.x = t.orbitControls.object.position.x;
                t.flyControls.object.position.y = t.orbitControls.object.position.y;
                t.flyControls.object.position.z = t.orbitControls.object.position.z;
                t.orbitControls.enabled = false;
            }
        } else {
            if (event.button !== 2)
            if (t.orbitControls.enabled) {
                t.orbitControls.enabled = false;
                t.flyControls.object.position.x = t.orbitControls.object.position.x;
                t.flyControls.object.position.y = t.orbitControls.object.position.y;
                t.flyControls.object.position.z = t.orbitControls.object.position.z;
                t.flyControls.enabled = true;

            } else {
                t.flyControls.enabled = false;
                t.orbitControls.object.position.x = t.flyControls.object.position.x;
                t.orbitControls.object.position.y = t.flyControls.object.position.y;
                t.orbitControls.object.position.z = t.flyControls.object.position.z;

                t.orbitControls.enabled = true;

            }


        }

        //t.fpControls.activeLook = false;
    }


    onMouseWheel( event, t ) {
    }

    onclick (t, event) {
        const intersects = t.raycaster.intersectObjects (t.s2);
        if (intersects[0] && intersects[0].object.type!=='Line')
        for (var i=0; i<1/*intersects.length <-- this just gets an endless
        series of hits from camera into the furthest reaches of what's visible
        behind the mouse pointer */; i++) {
            var cit/*clickedItem*/ = intersects[i].object, done = false;
            while (cit && !done) {
                var html = '', j = 0;
                for (var file in cit.it.parent.data.files) {
                    if (file.match(/\.mp3$/)) {
                        var file2 = file.replace(/\-[\-\w]+\.mp3/, '.mp3');
                        html += '<div id="file_'+j+'" class="vividButton" style="position:relative; font-size:small;">'+file2+'</div>';
                        j++;
                    }
                };
                $('.naFolderFilesList').html(html);
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
                /*var dbg = {
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
                na.site.setStatusMsg (msg, true, 10 * 1000);*/

                var html =
                    '<div class="vividButton" style="position:relative; font-size : small;">'
                    +cit.it.name.replace(/\-[\-\w]+\.mp3/, '.mp3')
                    +'</div>';
                $('.naAudioPlayerPlaylist').append(html);

                done = true;
            }
        }
    }
    onclick_triple (t, event) {
            var
            ol = 10 * 1000,
            numPoints = 720,
            radius = 10*1000;



            t.curve4b = new THREE.CatmullRomCurve3( [
                new THREE.Vector3 (t.camera.position.x, t.camera.position.y, t.camera.position.z),
                new THREE.Vector3 (t.winners.west - ol, 0, ol),
                new THREE.Vector3 (t.winners.west - ol, 0, t.winners.front - ol),
                new THREE.Vector3 (t.winners.east + ol, 0, t.winners.front - ol),
                new THREE.Vector3 (t.winners.east + ol, 0, ol),
                new THREE.Vector3 (0, 0, -ol),
            ]);
            var first = {x:0,y:0,z:-ol};
            var last = {x:0,y:0,z:-ol};
            t.points4b = t.curve4b.getPoints(numPoints);
            t.curves4a = [
                new THREE.Vector3(t.cameraOrigin.x,t.cameraOrigin.y,t.cameraOrigin.z),
                new THREE.Vector3(first.x,first.y,first.z)
            ];
            t.curve4a = new THREE.CatmullRomCurve3(t.curves4a);
            t.points4a = t.curve4a.getPoints(50);
            t.curves4z = [
                new THREE.Vector3(last.x,last.y,last.z),
                new THREE.Vector3(t.cameraOrigin.x,t.cameraOrigin.y,t.cameraOrigin.z)
            ];
            t.curve4z = new THREE.CatmullRomCurve3(t.curves4z);
            t.points4z = t.curve4z.getPoints(50);

            t.curves4x = t.points4a.concat (t.points4b, t.points4z);
            t.curve4 = new THREE.CatmullRomCurve3(t.curves1x);
            t.points4 = t.curve4.getPoints(numPoints);

            t._tmp4 = new THREE.Vector3();
            t.animationProgress4 = { value: 0 };
            t.pathAnimation4 = gsap.fromTo(
                t.animationProgress,
                {
                    value: 0,
                },
                {
                    value: 1,
                    duration: t.animationDuration,
                    overwrite: true,
                    paused: true,
                    onUpdateParams: [ t.animationProgress4 ],
                    onUpdate( { value } ) {

                        if ( ! this.isActive() ) return;

                        t.curve4.getPoint ( value, t._tmp4);
                        t.cameraControls.setLookAt(
                            t._tmp.x,
                            t._tmp.y,
                            t._tmp.z,
                            t.middle.x,
                            t.middle.y,
                            t.middle.z,
                            false, // IMPORTANT! disable cameraControls's transition and leave it to gsap.
                        );

                    },
                    onStart() {
                        t.animPlaying = true;
                    },
                    onComplete() {
                        t.fpControls.enabled = true;
                        t.cameraControls.enabled = false;
                        t.animPlaying = false;
                        t.onresize_postDo(t);
                    }
                }
            );

            t.fpControls.enabled = false;
            t.cameraControls.enabled = true;
            t.pathAnimation4.play(0);

    }

    initializeItems (t) {
        var p = { t : t, ld2 : {}, idxPath : '', idxPath2 : '/0' };
        t.s2 = [];
        na.m.walkArray (t.data, t.data, t.initializeItems_walkKey, t.initializeItems_walkValue, false, p);

        var innerWidth = $('#siteContent .vividDialogContent').width();
        var innerHeight = $('#siteContent .vividDialogContent').height() - $('#header').position().top - $('#header').height();
        t.renderer.setSize(innerWidth, innerHeight);
        t.camera.aspect = innerWidth / innerHeight;
        t.camera.updateProjectionMatrix();

        t.onresize(t);
    }
    initializeItems_walkKey (cd) {
        var ps = cd.path.split('/');
        if (ps[ps.length-1]=='files') {
            //console.log ('initializeItems_walkKey', 'files', cd);
        } else if (ps[ps.length-1]=='folders') {

            var
            lastParent = cd.params.t.items[0],
            pk = cd.path;
            if (!cd.params.ld2[pk]) cd.params.ld2[pk] = { levelIdx : 0 };
            for (var i=0; i<cd.params.t.items.length; i++) {
                var it2 = cd.params.t.items[i];
                if (it2.filepath+'/'+it2.name+'/folders' === cd.path) {
                    lastParent = it2;
                }
            }


            //debugger;
            if (cd.level <= 4) {
                cd.params.idxPath = '/0';// + cd.params.t.items.length;
            } else {
                var
                il1 = (cd.level - 4) / 2,
                il2 = cd.params.idxPath.split('/'),
                il3 = null,
                j = il2.length;

                for (var i=0; i<j; i++) {
                    if (parseInt(il2[i])===lastParent.idx) il3 = lastParent.idx;
                    if (il3) il2.pop();
                }

                cd.params.idxPath = il2.join('/') + '/' + lastParent.idx;
                cd.params.idxPath2 = cd.params.idxPath;
            };
            //debugger;

            var
            it = {
                level : cd.level,
                name : cd.k,
                idx : cd.params.t.items.length,
                idxPath : cd.params.idxPath,//localIdx + '/' + cd.params.t.items.length,
                filepath : cd.path,
                levelIdx : ++cd.params.ld2[pk].levelIdx,
                parent : lastParent,
                leftRight : 0,
                upDown : 0,
                columnOffsetValue : 1000,
                rowOffsetValue : 1000,
                model : { position : { x : 0, y : 0, z : 0 } },
                data : cd.at[cd.k]
            };
            if (!cd.k.match(/\/.mp3$/)) {
                console.log ('t779', it.filepath, it.name, it);
            };

            if (!cd.params.t.ld3) cd.params.t.ld3 = {};
            if (!cd.params.t.ld3[it.idxPath]) cd.params.t.ld3[it.idxPath] = { itemCount : 0, folderCount : 0, items : [] };
            if (!cd.params.t.ld3[it.idxPath].folderCount) cd.params.t.ld3[it.idxPath].folderCount = 0;
            cd.params.t.ld3[it.idxPath].folderCount++;
            cd.params.t.ld3[it.idxPath].itemCount++;
            cd.params.t.ld3[it.idxPath].items.push (it);
            //cd.params.idxPath2 = cd.params.idxPath + '/' + it1a.idx;
            cd.params.t.items.push (it);

            // display files :
            if (it.data.files)
            for (var fkey in it.data.files) {
                if (fkey.match(/\.mp3$/)) {
                    var p = null;

                    /*var ps2 = $.extend([],ps);
                    delete ps2[ps2.length-1];
                    var ps2Str = ps2.join('/');
                    var parent = it.parent;//na.m.chaseToPath (cd.root, ps2Str+'/files/'+fkey, false);*/
                    //var level = lastParent.level/2;//ps2.length;


                    var
                    //pk = cd.path+'/'+cd.k+'/'+fkey,
                    it1a = {
                        data : it.data.files[fkey],
                        level : cd.level+1,
                        name : fkey,
                        idx : cd.params.t.items.length,
                        idxPath : cd.params.idxPath + '/' + it.idx,// + '/' + cd.params.t.items.length,//cd.params.t.items.length,
                        filepath : cd.path+'/'+cd.k,
                        levelIdx : ++cd.params.ld2[pk].levelIdx,
                        parent : it,
                        leftRight : 0,
                        upDown : 0,
                        columnOffsetValue : 1000,
                        rowOffsetValue : 1000,
                        model : { position : { x : 0, y : 0, z : 0 } }
                    };

                    if (!cd.params.t.ld3) cd.params.t.ld3 = {};
                    if (!cd.params.t.ld3[it1a.idxPath]) cd.params.t.ld3[it1a.idxPath] = { itemCount : 0, items : [] };
                    cd.params.t.ld3[it1a.idxPath].itemCount++;
                    cd.params.t.ld3[it1a.idxPath].items.push (it1a);
                    cd.params.idxPath2 = cd.params.idxPath + '/' + it1a.idx;
                    cd.params.t.items.push (it1a);
                }
            }
        }
    }
    initializeItems_walkValue (cd) {
        //console.log ('initializeItems_walkValue', 'cd', cd);
    }

    initializeFolderList (t, data) {
        var p = { t : t, ld2 : {}, data2 : t.itemsFolders };
        na.m.walkArray (data, data, t.initializeFolderView_walkKey, null, false, p);
        t.initializeFolderView (t, p.data2);
    }

    initializeFolderView_walkKey (cd) {
        var ps = cd.path.split('/');
        if (ps[ps.length-1]=='files') {
            //console.log ('initializeItems_walkKey', 'files', cd);
        } else if (ps[ps.length-1]=='folders') {
            var
            lastParent = cd.params.t.itemsFolders[0],
            pk = cd.path;
            if (!cd.params.ld2[pk]) cd.params.ld2[pk] = { levelIdx : 0 };
            for (var i=0; i<cd.params.t.itemsFolders.length; i++) {
                var it2 = cd.params.t.itemsFolders[i];
                if (it2.filepath+'/'+it2.name+'/folders' === cd.path) {
                    lastParent = it2;
                }
            }


            //debugger;
            if (cd.level <= 4) {
                cd.params.idxPath = '/0';// + cd.params.t.itemsFolders.length;
            } else {
                var
                il1 = (cd.level - 4) / 2,
                il2 = cd.params.idxPath.split('/'),
                il3 = null,
                j = il2.length;

                for (var i=0; i<j; i++) {
                    if (parseInt(il2[i])===lastParent.idx) il3 = lastParent.idx;
                    if (typeof il3=='number') il2.pop();
                }

                cd.params.idxPath = il2.join('/') + '/' + lastParent.idx;
                cd.params.idxPath2 = cd.params.idxPath;
            };
            //debugger;
            var fit = {
                type : 'naFolder',
                id : na.m.randomString(),
                parent : lastParent.id,
                text : cd.k,
                idx : cd.params.t.itemsFolders.length - 1,
                idxPath : cd.params.idxPath
            };

            if (!cd.params.t.fd3) cd.params.t.fd3 = {};
            if (!cd.params.t.fd3[fit.idxPath]) cd.params.t.fd3[fit.idxPath] = { itemCount : 0, itemsFolders : [] };
            cd.params.t.fd3[fit.idxPath].itemCount++;
            cd.params.t.fd3[fit.idxPath].itemsFolders.push (fit);
            //cd.params.idxPath2 = cd.params.idxPath + '/' + it1a.idx;
            cd.params.t.itemsFolders.push (fit);

            cd.params.data2.push (fit);
        }
    }
    
    initializeFolderView(t, foldersListForJStree) {
        var fv = $('.naFoldersList');
        if (!fv.is('.jstree'))
            fv.jstree ({
                core : {
                    data : foldersListForJStree,
                    check_callback : true
                },
                types : {
                    "naSystemFolder" : {
                        "icon" : "/siteMedia/na.view.tree.naSystemFolder.png",
                        "valid_children" : []
                    },
                    "naUserRootFolder" : {
                        "max_depth" : 14,
                        "icon" : "/siteMedia/na.view.tree.naUserRootFolder.png",
                        "valid_children" : ["naFolder", "naMediaAlbum", "naDocument"]
                    },
                    "naGroupRootFolder" : {
                        "max_depth" : 14,
                        "icon" : "/siteMedia/na.view.tree.naGroupRootFolder.png",
                        "valid_children" : ["naFolder", "naMediaAlbum", "naDocument"]
                    },
                    "naFolder" : {
                        "icon" : "/siteMedia/na.view.tree.naFolder.png",
                        "valid_children" : ["naFolder", "naMediaAlbum", "naDocument"]
                    },
                    "naDialog" : {
                        "icon" : "/siteMedia/na.view.tree.naSettings.png",
                        "valid_children" : []
                    },
                    "naSettings" : {
                        "icon" : "/siteMedia/na.view.tree.naSettings.png",
                        "valid_children" : []
                    },
                    "naTheme" : {
                        "icon" : "/siteMedia/na.view.tree.naVividThemes.png",
                        "valid_children" : []
                    },
                    "naVividThemes" : {
                        "icon" : "/siteMedia/na.view.tree.naVividThemes.png",
                        "valid_children" : []
                    },
                    "naMediaAlbum" : {
                        "icon" : "/siteMedia/na.view.tree.naMediaAlbum.png",
                        "valid_children" : [ "naMediaAlbum" ]
                    },
                    "naDocument" : {
                        "icon" : "/siteMedia/na.view.tree.naDocument.png",
                        "valid_children" : []
                    },
                    "saApp" : {
                        "icon" : "/siteMedia/na.view.tree.naApp.png",
                        "valid_children" : []
                    }
                },
                plugins : [
                    "contextmenu", "dnd", "search", "state", "types", "wholerow"
                ]
            }).on('ready.jstree', function (e, data) {
                var tree = $('.naFoldersList').jstree(true);
                for (var i=0; i<tree.settings.core.data.length; i++) {
                    var it = tree.settings.core.data[i];
                    if (it.state && it.state.selected) tree.select_node(it._id);
                }
            }).on('open_node.jstree', function (e, data) {
                na.cms.onchange_folderStatus_openOrClosed(e, data);

            }).on('close_node.jstree', function (e, data) {
                na.cms.onchange_folderStatus_openOrClosed(e, data);

            }).on('rename_node.jstree', function (e, data) {
                na.cms.onchange_rename_node(e, data);

            }).on('changed.jstree', function (e, data) {

                if (
                    //data.action!=='ready'
                    //&&
                    /*data.action!=='model'
                    && */data.action!=='select_node'
                ) return false;

                $('#siteContent .vividTabPage').fadeOut('fast');
                clearTimeout(na.cms.settings.timeout_changed);
                na.cms.settings.timeout_changed = setTimeout (function(data) {
                    var l = data.selected.length, rec = null;
                    for (var i=0; i<l; i++) {
                        var d = data.selected[i], rec2 = data.instance.get_node(d);
                        if (rec2 && rec2.original) rec = rec2;
                    }

                    if (
                        na.cms.settings.current.selectedTreeNode
                        && rec
                        && na.cms.settings.current.selectedTreeNode.id!==rec.id
                        && na.cms.settings.current.selectedTreeNode.type=='naDocument'
                    ) na.cms.saveEditorContent(na.cms.settings.current.selectedTreeNode, function(){
                        na.cms.settings.current.selectedTreeNode = rec;
                        //na.cms.onchange_selectedNode (settings, data, rec, function() {
                            //na.cms.refresh(function() {
                        //      na.cms.onchange_jsTreeNode(settings, data,rec);
                            //});
                        //});
                    })
                    else if (rec) na.cms.onchange_jsTreeNode(settings, data, rec);

                    if (rec && rec.type=='naDocument') $('#document').fadeIn('slow');
                    if (rec && rec.type=='naMediaAlbum') $('#upload').fadeIn('slow');
                    if (
                        rec
                        && (
                            rec.type=='naDocument'
                            || rec.type=='naMediaAlbum'
                        )
                    ) {
                        if ($(window).width() < 400) {
                            na.cms.settings.current.activeDialog = '#siteContent';
                            arrayRemove(na.desktop.settings.visibleDivs, '#siteToolbarLeft');
                            arrayRemove(na.desktop.settings.visibleDivs, '#siteContent');
                            na.desktop.settings.visibleDivs.push('#siteContent');
                            na.desktop.resize();
                        } else {
                            na.cms.settings.current.activeDialog = '#siteContent';
                            arrayRemove(na.desktop.settings.visibleDivs, '#siteToolbarLeft');
                            arrayRemove(na.desktop.settings.visibleDivs, '#siteContent');
                            na.desktop.settings.visibleDivs.push('#siteToolbarLeft');
                            na.desktop.settings.visibleDivs.push('#siteContent');
                            na.desktop.resize();
                        };
                    }

                    na.site.settings.buttons['#btnAddUser'].disable();
                    na.site.settings.buttons['#btnAddGroup'].disable();
                    na.site.settings.buttons['#btnAddFolder'].disable();
                    na.site.settings.buttons['#btnAddDocument'].disable();
                    na.site.settings.buttons['#btnAddMediaAlbum'].disable();
                    na.site.settings.buttons['#btnDeleteRecord'].disable();

                    if (rec && rec.type=='naSystemFolder' && rec.text=='Users')
                        na.site.settings.buttons['#btnAddUser'].enable();


                    if (rec && rec.type=='naSystemFolder' && rec.text=='Groups')
                        na.site.settings.buttons['#btnAddGroup'].enable();


                    if (rec &&
                        (
                            rec.type=='naUserRootFolder'
                            || rec.type=='naGroupRootFolder'
                            || rec.type=='naFolder'
                        )
                    ) na.site.settings.buttons['#btnAddFolder'].enable();


                    if (rec &&
                        (
                            rec.type=='naFolder'
                        )
                    ) {
                        na.site.settings.buttons['#btnAddDocument'].enable();
                        na.site.settings.buttons['#btnAddMediaAlbum'].enable();
                    }

                    if (rec &&
                        (
                            rec.type=='naFolder'
                            || rec.type=='naDocument'
                            || rec.type=='naMediaAlbum'
                        )
                    ) na.site.settings.buttons['#btnDeleteRecord'].enable();
                }, 500, data);

                //clearTimeout (na.cms.settings.current.timeoutRefresh);
                //na.cms.settings.current.timeoutRefresh = setTimeout(na.cms.refresh,1000);

            }).on('move_node.jstree', function (e, data) {

                var
                tree = $('#jsTree').jstree(true),
                oldPath = na.cms.currentPath(tree.get_node(data.old_parent)),
                newPath = na.cms.currentPath(tree.get_node(data.parent)),
                url2 = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_moveNode.php',
                ac = {
                    type : 'POST',
                    url : url2,
                    data : {
                        database : data.node.original.database,
                        oldParent : data.old_parent,
                        oldPath : oldPath,
                        newParent : data.parent,
                        newPath : newPath,
                        target : data.node.original._id || original.id
                    },
                    success : function (data, ts, xhr) {
                    },
                    error : function (xhr, textStatus, errorThrown) {
                        na.site.ajaxFail(fncn, url2, xhr, textStatus, errorThrown);
                    }
                };
                $.ajax(ac);

            });

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
        t.ld4 = [];
        t.s2 = [];

        for (var path in t.ld3) {
            t.ld4.push(path)
        }
        for (var i=0; i<t.ld4.length; i++) {
            var p1 = t.ld4[i].substr(1).split('/');

            setTimeout (function(p1, i) {
                var colorGradientScheme = {
                    themeName: 'naColorgradientScheme_custom__'+p1.join('_'),
                    cssGeneration: {
                        colorTitle : 'yellow',
                        colorLegend : '#00BBBB',
                        colorLegendHREF : '#00EEEE',
                        colorStatus : 'goldenrod',
                        colorStatusHREF : 'yellow',
                        colorLevels: {
                        0: {
                            background: '#7A95FF',
                            color: 'rgb('
                                +(50+Math.random()*205)+','
                                +(50+Math.random()*205)+','
                                +(50+Math.random()*205)+')'
                        },
                        100: {
                            background: 'white',
                            color: 'rgb('
                                +(50+Math.random()*205)+','
                                +(50+Math.random()*205)+','
                                +(50+Math.random()*205)+')'
                        }
                        }
                    },
                    htmlTopLevelTableProps: ' cellspacing="5"',
                    htmlSubLevelTableProps: ' cellspacing="5"',
                    showFooter: true,
                    showArrayKeyValueHeader: false,
                    showArrayStats: true,
                    showArrayPath: true,
                    showArraySiblings: true,
                    jQueryScrollTo: {
                        duration: 900
                    }
                    }

                var list = naCG.generateList_basic (colorGradientScheme, p1.length);
                t.ld3[t.ld4[i]].colorList = list;
                t.ld3[t.ld4[i]].p1 = p1;
            }, i + (Math.random() * 500), p1, i);
        }
        na.m.waitForCondition('onresize_do_phase2()', function() {
            for (var i=0; i<t.ld4.length; i++) {
                if (!t.ld3[t.ld4[i]].colorList) return false;
            };
            var r = t.items.length > 2;
            return r;
        }, function() {
            t.onresize_do_phase2 (t, callback);
        }, 25);
    }

    onresize_do_phase2(t, callback) {
        for (var path in t.ld3) {
            var ld3 = t.ld3[path];
            if (path!=='') {
                for (var i=0; i<ld3.items.length; i++) {
                    var
                    it = t.items[ld3.items[i].idx];

                    ld3.rowColumnCount = Math.ceil(Math.sqrt(ld3.itemCount));
                    ld3.cubeSideLengthCount = Math.ceil(Math.cbrt(ld3.itemCount));
                    var
                    pos = { x : 1, xField : 1, y : 1, yField : 1, z : 1 },

                    // 2D view
                    columnField = 1,
                    rowField = 1,

                    // 3D view
                    column = 1,
                    row = 1,
                    depth = 1;


                    //if (it.filepath=='siteMedia/backgrounds/tiled/active') debugger;
                    for (var j=0; j<ld3.items.length; j++) {
                        var it2 = t.items[ld3.items[j].idx];
                        if (
                            (it.parent ? it.parent === it2.parent : true)
                            && it2.levelIdx <= it.levelIdx
                        ) {
                            if (
                                column >= ld3.cubeSideLengthCount
                                && row >= ld3.cubeSideLengthCount
                            ) {
                                pos.z++;
                                depth++;
                                column = 1;
                                row = 1;
                            } else if (column >= ld3.cubeSideLengthCount) {
                                pos.y++;
                                pos.x = 1;
                                row++;
                                column = 1;
                            } else {
                                column++;
                                pos.x++;
                            }

                            if (columnField >= ld3.rowColumnCount) {
                                pos.yField++;
                                pos.xField = 1;
                                rowField++;
                                columnField = 1;
                            } else {
                                columnField++;
                                pos.xField++;
                            }


                        }
                    };

                    it.rowField = rowField;
                    it.columnField = columnField;
                    it.row = row;
                    it.column = column;
                    it.depth = depth;
                    it.pos = pos;
                    it.ld3 = ld3;
                    //if (it.name=='gull' || it.name=='owl') debugger;
                }
            }
            //debugger;
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

        its.sort (compare1);


        var
        x = t.data, // x[a][b][c].it
        maxLevel = 0;

        for (var i=0; i<its.length; i++) {
            if (maxLevel < its[i].level) maxLevel = its[i].level;
            for (var j=0; j<its.length; j++) {

                var
                name = '',
                parent = t.hovered;

                while (parent) {
                    //$('#site3D_label')[0].textContent =
                    //  t.hovered.object.it.name.replace(/-\s*[\w]+\.mp3/, '.mp3');
                    var li =
                        parent.object.it.filepath
                            .replace('/0/filesAtRoot/folders/','')
                            .replace('/0/filesAtRoot/folders','');
                    if (li!=='') li+= '/';
                    li += parent.object.it.name.replace(/\s*-\s*[-_\w]+\.mp3$/,'.mp3')
                    //l += ' ('+parent.object.it.parent.rndz+')';
                    li = li.replace(/folders\//g, '');

                    var lj =
                        its[j].filepath
                            .replace('/0/filesAtRoot/folders/','')
                            .replace('/0/filesAtRoot/folders','');
                    if (lj!=='') lj+= '/';
                    lj += its[j].name.replace(/\s*-\s*[-_\w]+\.mp3$/,'.mp3')
                    //l += ' ('+parent.object.it.parent.rndz+')';
                    lj = lj.replace(/folders\//g, '');

                    parent = parent.parent;
                }

                if (
                    //its[i].idxPath+'/'+its[i].idx === its[j].idxPath+'/'+its[j].idx
                    //its[i].idxPath === its[j].idxPath
                    //its[i].filepath === its[j].filepath
                    //&& its[i].name === its[j].name
                    /*
                    its[i].pos.x === its[j].pos.x
                    && its[i].pos.y === its[j].pos.y
                    && its[i].pos.z === its[j].pos.z*/
                    li === lj
                ) {
                    var
                    ita = {
                        level: its[i].level,
                        maxColumn : Math.max( its[i].columnField, its[j].columnField ),
                        maxRow : Math.max( its[i].rowField, its[j].rowField ),
                        maxDepth : Math.max ( its[i].depth, its[j].depth )
                    };
                    if (ita.maxColumn === its[i].columnField) ita.maxColumnIt = its[i]; else ita.maxColumnIt = its[j];
                    if (ita.maxRow === its[i].rowField) ita.maxRowIt = its[i]; else ita.maxRowIt = its[j];
                    if (ita.maxDepth === its[i].depth) ita.maxDepthIt = its[i]; else ita.maxDepthIt = its[j];
                    its[i].maxColumnIta = ita;
                    its[i].maxRowIta = ita;
                    its[i].maxDepthIta = ita;
                    its[j].maxColumnIta = ita;
                    its[j].maxRowIta = ita;
                    its[j].maxDepthIta = ita;

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

        var pox = {}, poy = {}, poz = {}, pd = {};
        if (t.items.length > 2)
        for (var i=0; i<t.items.length; i++) {
            var
            offsetXY = 200,
            it = t.items[i],
            p = (it.parent ? it.parent : null),
            p1 = (it.parent && it.parent.parent ? it.parent.parent : null),
            rndMax = 1500 + (it.ld3 ? (it.ld3.rowColumnCount * 200) : 0);

            if (p && !pox[p.idx]) pox[p.idx] = Math.abs(Math.random() * rndMax);
            if (p && !poy[p.idx]) poy[p.idx] = Math.abs(Math.random() * rndMax);
            if (p && !poz[p.idx]) poz[p.idx] = Math.abs(Math.random() * rndMax);

            //if (p && !pox[p.idx]) pox[p.idx] = it.level * p.columnOffsetValue;
            //if (p && !poy[p.idx]) poy[p.idx] = it.level * p.rowOffsetValue;
            //if (p && !poz[p.idx]) poz[p.idx] = it.level * 500;

            if (p) var rndx = pox[p.idx]; else var rndx = 0;
            if (p) var rndy = poy[p.idx]; else var rndy = 0;
            if (p) var rndz = poz[p.idx]; else var rndz = 0;
            it.rndx = rndx;
            it.rndy = rndy;
            it.rndz = rndz;

            if (p) {
                var
                itmaxc = it.maxColumnIta.maxColumn,
                itmaxr = it.maxRowIta.maxRow,
                itmaxd = it.maxRowIta.maxDepth,
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
                itBackForth = /*p.upDown * */(
                    it.depth-1 == itmaxd/2
                    ? 0
                    : itmaxd===1
                        ? 0
                        : itmaxd - it.depth == it.depth - 1
                            ? 0
                            : itmaxr - it.depth < it.depth - 1
                                ? 1
                                : -1
                            ),
                itc = (itmaxc - 1 - it.columnField),
                itr = (itmaxr - 1 - it.rowField),
                itd = (itmaxd - 1 - it.depth);
                it.columnOffsetValue = itc;//Math.floor(itc);
                if (itc==18) debugger;
                it.rowOffsetValue = itr;//Math.floor(itr);
                it.depthOffsetValue = itd;//Math.floor(itr);
                it.leftRight = itLeftRight;
                it.upDown = itUpDown;
                it.backForth = itBackForth;

            } else {
                var mc = 0, mr = 0;

                var
                itmaxc = it.maxRowIta.maxColumn,
                itmaxr = it.maxRowIta.maxRow,
                itmaxd = it.maxRowIta.maxDepth,
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
                itBackForth = /*p.upDown * */(
                    it.depth-1 == itmaxd/2
                    ? 0
                    : itmaxd===1
                        ? 0
                        : itmaxd - it.depth == it.depth - 1
                            ? 0
                            : itmaxr - it.depth < it.depth - 1
                                ? 1
                                : -1
                            ),
                itc = (itmaxc - 1 - it.columnField),
                itr = (itmaxr - 1 - it.rowField),
                itd = (itmaxd - 1 - it.depth);

                it.columnOffsetValue = itc;//Math.floor(itc);
                it.rowOffsetValue = itr;//Math.floor(itr);
                it.depthOffsetValue = itd;//Math.floor(itr);
                it.leftRight = itLeftRight;
                it.upDown = itUpDown;
                it.backForth = itBackForth;
                //if (it.name=='landscape') debugger;
            };



            var
            z = (it.level/4) * 1000,//(it.level < 2 ? 1 : it.level-2) * 200 / 2,
            //z = -1 * it.depthOffsetValue * 2500,
            //plc = p.columnOffsetValue === 0 ? 0.01 : p.columnOffsetValue,
            //plr = p.rowOffsetValue === 0 ? 0.01 : p.rowOffsetValue,
            m = 10 * 1000,
            ilc = (p?p.columnOffsetValue * m:it.columnOffsetValue*m), //it.leftRight * it.column,// * p.columnOffsetValue,
            ilr = (p?p.rowOffsetValue * m:it.columnOffsetValue*m),//it.upDown * it.row,// * p.rowOffsetValue,

            min = 2, m0 = (it.level-2) < 5 ? it.level-2 : 4, m1 = 500, m2 = 500, m1a = 500, m2a =  500, m3a = 500, m3b = 500, m3c = 1000, m3d = 2500, n = 0.5, n1 = 500, n2 = 500, o = 1, s = 1,
            u = 1 * (p && p.leftRight===0?ilc:(p?p.leftRight:it.leftRight)),
            v = 1,
            w = 1 * (p && p.upDown===0?ilr:(p?p.upDown:it.upDown)),
            x = 1,
            u2 = (p?p.columnOffsetValue:it.columnOffsetValue),
            v2 = (p?p.rowOffsetValue:it.rowOffsetValue),
            w2 = (p?p.depthOffsetValue:it.depthOffsetValue),
            u2a = it.column,
            v2a = it.row,
            w2a = it.depth,
            divider = 1;

            /*
            if (p) {
                u = p.leftRight;
                w = p.upDown;
                u2 = -1 * p.columnOffsetValue;
                v2 = -1 * p.rowOffsetValue;
                w2 = -1 * p.depthOffsetValue;
                u2 = p.columnField;
                v2 = p.rowField;
                w2 = p.depth;
            }
*/

//if (it.name.match(/becoming insane/i)) debugger;
            if (it.model) {
                if (it.name.match(/\.mp3$/)) {
                    if (!t.showFiles) { /*delete it.model;*/} else {
                        it.model.position.x = Math.round( (
                            p.model.position.x
                            //+ (p.columnField * 500)
                            //+ (p.columnField * m3c)
                            + ((it.column-1)*500)
                            + (it.ld3 ? (/*(p.columnField-1) * */it.ld3.cubeSideLengthCount * 50) : 0)
                            //- (it.columnOffsetValue * 2500)
                            //- (u2 * m1a)+(it.columnOffsetValue*m1)
                            //+ (it.level > min ? (u2 * v * ((o * n1))) : 0)
                            //+ (it.level > min ? (u2 * v * ((o * n2))) : 0)
                            //+ (it.level > min ? rndx : 0)
                        ) / divider);
                        it.model.position.y = Math.round( (
                            p.model.position.y
                            //+ (p.rowField * 500)
                            //+ (p.rowField * m3c)
                            + ((it.row-1)*500)
                            + (it.ld3 ? (/*(p.rowField-1) * */it.ld3.cubeSideLengthCount * 50) : 0)

                            //+ (it.level > min ? (v2 * x * ((o * n1))) : 0)
                            //+ (it.level > min ? (v2 * x * ((o * n2))) : 0)
                            //+ (it.level > min ? rndy : 0)
                        ) / divider);
                        it.model.position.z = Math.round( (
                            (p.model.position.z ? p.model.position.z : 0)
                            + (it.depth*500)
                            //+ (it.level > min ? (w2 * x * ((o * n1))) : 0)
                            //+ (it.level > min ? (w2a * x * ((o * n2))) : 0)
                            + (it.level > min ? -1 * it.level/2*300 : 0)
                        ) / divider);
                        /*it.model2.position.x = it.model.position.x + 30;
                        it.model2.position.y = it.model.position.y + 30;
                        it.model2.position.z = it.model.position.z + 30;*/
                        if (it.name.match('Club Remixes')) debugger;
                    }

                } else if (it.model && p && p1) {


                    it.model.position.x = Math.round( (
                        p.model.position.x
                        + (/*u2*/p.columnOffsetValue * 200)//+(it.columnField*m1)
                        + ((it.columnField-1) * 200)

                        //+ (p.ld3 ? (Math.sqrt(p.ld3.folderCount) * 200) : 0)

                        //+ (it.level/2 * 500)
                        //+ (p.columnOffsetValue * m3c)

                        //+ rndx
                        //+ (p.columnOffsetValue * (it.level-2) * 500)
                        //+ (it.level > min ? (u2 * v * ((o * n1))) : 0)
                        //+ (it.level > min ? (u2 * v * ((o * n2))) : 0)

                        + (it.level > min ? rndx : 0)

                        //+ (it.columnField * 500)
                    ) / divider);
                    it.model.position.y = Math.round( (
                        p.model.position.y
                        + (p.rowOffsetValue /* * v2*/ * 200)//+(it.rowField*m2)
                        + ((it.rowField-1) * 200)

                        //+ (p.ld3 ? (Math.sqrt(p.ld3.folderCount) * 200) : 0)

                        //+ (it.level/2 * 500)
                        //+ (p.rowOffsetValue * m3c)

                        //+ rndy
                        //+ (p.rowOffsetValue * (it.level-2) * 500)
                        //+ (it.level > min ? (v2 * x * ((o * n1))) : 0)
                        //+ (it.level > min ? (v2 * x * ((o * n2))) : 0)

                        + (it.level > min ? rndy : 0)

                        //+ (it.rowField * 500)
                    ) / divider);
                    //it.model.position.x = p.model.position.x + it.columnOffsetValue * 500;
                    //it.model.position.y = p.model.position.y + it.rowOffsetValue * 500;
                    //it.model.position.x = p.model.position.x + /*(it.leftRight * 400) +*/ it.columnOffsetValue * 500;
                    //it.model.position.y = p.model.position.y + /*(it.upDown * 400) +*/ it.rowOffsetValue * 500;
                    it.model.position.z = p.model.position.z + (p.backForth *  500) / divider;// - rndz;
                    /*
                        Math.round(
                            (p.model.position.z ? p.model.position.z : 0)
                            + (w2 * m3b)+(it.depth*m2)
                            + (it.level > min ? (w2 * x * ((o * n1))) : 0)
                            //+ (it.level > min ? (w2 * x * ((o * n2))) : 0)
                            + (it.level > min ? -1 * z - rndz : 0)
                        );
                    it.model.position.z = p.model.position.z + (it.depth * 1000);
                    */
                    console.log ('t555p1', it.filepath, it.name, it.model.position);
                    if (it.name.match('Relaxation')) debugger;
                } else if (it.model && p) {


                    it.model.position.x = Math.round( (
                        p.model.position.x
                        + (/*u2*/p.columnOffsetValue * 200)//+(it.columnField*m1)
                        + ((it.columnField-1) * 200)

                        //+ (p.ld3 ? (Math.sqrt(p.ld3.folderCount) * 200) : 0)

                        //+ (it.level * 500)
                        //+ (p.columnOffsetValue * m3c)

                        //+ rndx
                        //+ (p.columnOffsetValue * (it.level-2) * 500)
                        //+ (it.level > min ? (u2 * v * ((o * n1))) : 0)
                        //+ (it.level > min ? (u2 * v * ((o * n2))) : 0)

                        + (it.level > min ? rndx : 0)

                        //+ (it.columnField * 500)
                    ) / divider);
                    it.model.position.y = Math.round( (
                        p.model.position.y
                        + (p.rowOffsetValue /* * v2*/ * 200)//+(it.rowField*m2)
                        + ((it.rowField-1) * 200)

                        //+ (p.ld3 ? (Math.sqrt(p.ld3.folderCount) * 200) : 0)

                        //+ (it.level * 500)
                        //+ (p.rowOffsetValue * m3c)

                        //+ rndy
                        //+ (p.rowOffsetValue * (it.level-2) * 500)
                        //+ (it.level > min ? (v2 * x * ((o * n1))) : 0)
                        //+ (it.level > min ? (v2 * x * ((o * n2))) : 0)

                        + (it.level > min ? rndy : 0)

                        //+ (it.rowField * 500)
                    ) / divider);
                    //it.model.position.x = p.model.position.x + it.columnOffsetValue * 500;
                    //it.model.position.y = p.model.position.y + it.rowOffsetValue * 500;
                    //it.model.position.x = p.model.position.x + /*(it.leftRight * 400) +*/ it.columnOffsetValue * 500;
                    //it.model.position.y = p.model.position.y + /*(it.upDown * 400) +*/ it.rowOffsetValue * 500;
                    it.model.position.z = p.model.position.z + (p.backForth * 500) / divider;
                    /*
                        Math.round(
                            (p.model.position.z ? p.model.position.z : 0)
                            + (w2 * m3b)+(it.depth*m2)
                            + (it.level > min ? (w2 * x * ((o * n1))) : 0)
                            //+ (it.level > min ? (w2 * x * ((o * n2))) : 0)
                            + (it.level > min ? -1 * z - rndz : 0)
                        );
                    it.model.position.z = p.model.position.z + (it.depth * 1000);
                    */
                    console.log ('t555p', it.filepath, it.name, it.model.position);
                    if (it.name.match('Relaxation')) debugger;
                } else if (it.model) {
                    it.model.position.x = it.columnField  * 500;
                    it.model.position.y = it.rowField  * 500;
                    it.model.position.z = 0;
                }
            }

            if (it.model) {
                var dbg = {
                    pos : it.pos,
                    px : it.model.position.x,
                    py : it.model.position.y,
                    pz : it.model.position.z,
                    it : it
                };
                //console.log ('t750', it.filepath, it.name, dbg);
            }
        }

        var
        sideLength = 100, length = sideLength, width = sideLength,
        shape = new THREE.Shape();
        shape.moveTo( 0,0 );
        shape.lineTo( 0, width );
        shape.lineTo( length, width );
        shape.lineTo( length, 0 );
        shape.lineTo( 0, 0 );

        var extrudeSettings = {
        steps: 40,
        depth: sideLength,
        bevelEnabled: true,
        bevelThickness: 40,
        bevelSize: 40,
        bevelOffset: 0,
        bevelSegments: 40
        };

        for (var j=0; j<t.items.length; j++) {
            var p = t.items[j].idxPath;
            var p2 = p.substr(1).split('/');
            if (t.ld3[p]) {
                var list = t.ld3[p].colorList;
                var p1 = t.ld3[p].p1;
                var it = t.items[j];
                if (it) {
                    //if (it.name.match(/SABATON/)) debugger;
                    if (it.parent && it.parent) {
                        for (var k=0; k<list.length; k++) {
                            if (p1[k]==it.parent.idx) {
                                it.color = list[k].color;
                            }
                        }
                    }
                    if (!it.color) {
                        for (var k=0; k<list.length; k++) {
                            if (p1[k]==it.idx)
                                it.color = list[k].color;
                        }
                    }
                    //console.log ('t321', it.name, it.color);

                    var sideLength = 300, length = sideLength, width = sideLength;
                    var
                    materials2 = [
                        new THREE.MeshBasicMaterial({
                            color : it.color ? it.color : 'rgb(0,0,255)',
                            opacity : 0.5,
                            transparent : true
                        }),
                        new THREE.MeshBasicMaterial({
                            color : it.color ? it.color : 'rgb(0,0,255)',
                            opacity : 0.5,
                            transparent : true
                        }),
                        new THREE.MeshBasicMaterial({
                            color : it.color ? it.color : 'rgb(0,0,255)',
                            opacity : 0.5,
                            transparent : true
                        }),
                        new THREE.MeshBasicMaterial({
                            color : it.color ? it.color : 'rgb(0,0,255)',
                            opacity : 0.5,
                            transparent : true
                        }),
                        new THREE.MeshBasicMaterial({
                            color : it.color ? it.color : 'rgb(0,0,255)',
                            opacity : 0.5,
                            transparent : true
                        }),
                        new THREE.MeshBasicMaterial({
                            color : it.color ? it.color : 'rgb(0,0,255)',
                            opacity : 0.5,
                            transparent : true
                        })

                    ];
                    if (it.parent) {
                        // parent/current folder :
                        if (it.name.match(/\.mp3$/)) {
                            if (t.showFiles)
                            var cube = new THREE.Mesh( new THREE.BoxGeometry( t.meshLength, t.meshLength, t.meshLength ), materials2 );
                        } else {
                            var cube = t.createSphere (t.meshLength * 3, it.color);
                        }
                        if (t.showFiles || !it.name.match(/\.mp3$/)) {
                            cube.it = it;
                            cube.position.x = it.model.position.x;
                            cube.position.y = it.model.position.y;
                            cube.position.z = it.model.position.z;
                            t.scene.remove(it.model);
                            //if (it.name.match('SABATON')) debugger;
                            it.model = cube;
                            t.scene.add( cube );
                            t.s2.push(cube);
                            //t.items.push (it);
                        }
                    } else {
                        var cube = t.createSphere(t.meshLength * 3, it.color);
                        cube.it = it;
                        cube.position.x = it.model.position.x;
                        cube.position.y = it.model.position.y;
                        cube.position.z = it.model.position.z;
                        it.model = cube;
                        t.scene.add( cube );
                        t.s2.push(cube);
                    }
                }
            }
        }

        t.onresize_postDo(t, true);
    }

    createSphere (size, color) {
        const geometry = new THREE.SphereGeometry( size/3, size/3, size/3 );
        const material = new THREE.MeshBasicMaterial( { color: color } );
        const sphere = new THREE.Mesh( geometry, material );

        return sphere;
    }


    createDodecahedron (size, color) {
        var g = new THREE.DodecahedronGeometry(size);

        const base = new THREE.Vector2(0, 0.5);
        const center = new THREE.Vector2();
        const angle = THREE.MathUtils.degToRad(72);
        var baseUVs = [
            base.clone().rotateAround(center, angle * 1).addScalar(0.5),
            base.clone().rotateAround(center, angle * 2).addScalar(0.5),
            base.clone().rotateAround(center, angle * 3).addScalar(0.5),
            base.clone().rotateAround(center, angle * 4).addScalar(0.5),
            base.clone().rotateAround(center, angle * 0).addScalar(0.5)
        ];

        var uvs = [];
        var sides = [];
        for (var i = 0; i < 12; i++) {
            uvs.push(
                baseUVs[1].x, baseUVs[1].y,
                baseUVs[2].x, baseUVs[2].y,
                baseUVs[0].x, baseUVs[0].y,

                baseUVs[2].x, baseUVs[2].y,
                baseUVs[3].x, baseUVs[3].y,
                baseUVs[0].x, baseUVs[0].y,

                baseUVs[3].x, baseUVs[3].y,
                baseUVs[4].x, baseUVs[4].y,
                baseUVs[0].x, baseUVs[0].y
            );
            sides.push(i, i, i, i, i, i, i, i, i);
        };
        g.setAttribute("uv", new THREE.Float32BufferAttribute(uvs, 2));
        g.setAttribute("sides", new THREE.Float32BufferAttribute(sides, 1));

        var m = new THREE.MeshStandardMaterial({
            roughness: 0.25,
            metalness: 0.75,
            color : (color?color:'#0000FF'),
            emissive : (color?color:'#00FF00'),
            opacity : 0.5,
            transparent : true
        });
        var o = new THREE.Mesh(g, m);
        return o;
    }

    createTexture(){
        let c = document.createElement("canvas");
        let step = 250;
        c.width = step * 16;
        c.height = step;
        let ctx = c.getContext("2d");
        ctx.fillStyle = "#7f7f7f";
        ctx.fillRect(0, 0, c.width, c.height);
        ctx.font = "40px Arial";
        ctx.textAlign = "center";
        ctx.fillStyle = "aqua";
        ctx.textBaseline = "middle";
        for (let i = 0; i < 12; i++){
            ctx.fillText(i + 1, step * 0.5 + step * i, step * 0.5);
        }

        return new THREE.CanvasTexture(c);
    }

    onresize_postDo (t, animate=false) {
        t.drawLines(t);
        //t.cameraControls._camera.lookAt (t.s2[0].position);

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
        ol = 10 * 1000,
        numPoints = 720,
        radius = 10*1000;
        t.middle = {
            x : Math.round((t.winners.west + t.winners.east) / 2),
            y : Math.round((t.winners.north + t.winners.south) / 2),
            z : Math.round((t.winners.front + t.winners.behind) /2)
        };

        t.cameraOrigin = {
            x : t.middle.x,
            y : t.middle.y,
            z : t.middle.z * 5
        };

        if (false) {
            if (!t.started) {

                t.fpControls.object.position.x = 0;
                t.fpControls.object.position.y = t.middle.y;
                t.fpControls.object.position.z = -10*1000;
                t.fpControls.object.lookAt(
                    t.cameraOrigin.x,
                    t.cameraOrigin.y,
                    t.cameraOrigin.z
                );

                var tar = t.cameraControls._targetEnd.clone();
                tar.set(0,0,0).applyQuaternion(t.camera.quaternion).add(t.camera.position);


                t.cameraControls.setLookAt (
                    t.cameraOrigin.x,
                    t.cameraOrigin.y,
                    t.cameraOrigin.z,
                    tar.x,
                    tar.y,
                    tar.z,
                    false
                );
                t.started = true;
            };
            console.log ('t778', t.winners, t.middle);


            t.curve1b = new THREE.CatmullRomCurve3( [
                new THREE.Vector3 (0, 0, ol),
                new THREE.Vector3 (t.winners.west - ol, 0, ol),
                new THREE.Vector3 (t.winners.west - ol, 0, t.winners.front - ol),
                new THREE.Vector3 (t.winners.east + ol, 0, t.winners.front - ol),
                new THREE.Vector3 (t.winners.east + ol, 0, ol),
                new THREE.Vector3 (0, 0, ol),
            ]);
            var first = last = {x:0,y:0,z:-ol};
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
                z = t.middle.z - (radius * Math.sin (2 * Math.PI * i / numPoints) / 2);
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
                z = t.middle.z - (radius * Math.sin (2 * Math.PI * i / numPoints) / 2);
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


            if (false && !t.dragndrop) {
                t.orbitControls.center =  new THREE.Vector3(
                    t.middle.x,
                    t.middle.y,
                    t.middle.z
                );
                t.cameraControls._target.copy (t.middle);
            }

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
                    duration: t.animationDuration,
                    overwrite: true,
                    paused: true,
                    onUpdateParams: [ t.animationProgress ],
                    onUpdate( { value } ) {

                        if ( ! this.isActive() ) return;

                        t.curve1.getPoint ( value, t._tmp );
                        t.cameraControls.setLookAt(
                            t._tmp.x,
                            t._tmp.y,
                            t._tmp.z,
                            t.middle.x,
                            t.middle.y,
                            t.middle.z,
                            false, // IMPORTANT! disable cameraControls's transition and leave it to gsap.
                        );

                    },
                    onStart() {
                        t.animPlaying = true;
                    },
                    onComplete() {
                        t.animPlaying = false;
                        t.onresize_postDo(t);
                    }
                }
            );

            t._tmp2 = new THREE.Vector3();
            t.animationProgress2 = { value: 0 };
            t.pathAnimation2 = gsap.fromTo(
                t.animationProgress2,
                {
                    value: 0,
                },
                {
                    value: 1,
                    duration: t.animationDuration,
                    overwrite: true,
                    paused: true,
                    onUpdateParams: [ t.animationProgress2 ],
                    onUpdate( { value } ) {

                        if ( ! this.isActive() ) return;

                        t.curve2.getPoint ( value, t._tmp2 );
                        t.cameraControls.setLookAt(
                            t._tmp2.x,
                            t._tmp2.y,
                            t._tmp2.z,
                            t.middle.x,
                            t.middle.y,
                            t.middle.z,
                            false, // IMPORTANT! disable cameraControls's transition and leave it to gsap.
                        );

                    },
                    onStart() {
                        t.animPlaying = true;
                        t.flyControls.enabled = false;
                        t.cameraControls.enabled = false;
                    },
                    onComplete() {
                        t.animPlaying = false;
                        t.onresize_postDo(t);
                    }
                }
            );

            t._tmp3 = new THREE.Vector3();
            t.animationProgress3 = { value: 0 };
            t.pathAnimation3 = gsap.fromTo(
                t.animationProgress3,
                {
                    value:  0,
                },
                {
                    value: 1,
                    duration: t.animationDuration,
                    overwrite: true,
                    paused: true,
                    onUpdateParams: [ t.animationProgress3 ],
                    onUpdate( { value } ) {

                        if ( ! this.isActive() ) return;

                        t.curve3.getPoint ( value, t._tmp3 );
                        t.cameraControls.setLookAt(
                            t._tmp3.x,
                            t._tmp3.y,
                            t._tmp3.z,
                            t.middle.x,
                            t.middle.y,
                            t.middle.z,
                            false, // IMPORTANT! disable cameraControls's transition and leave it to gsap.
                        );

                    },
                    onStart() {
                        t.animPlaying = true;
                    },
                    onComplete() {
                        t.animPlaying = false;
                        t.onresize_postDo(t);
                    }
                }
            );

            setTimeout (function() {

                if (!t.started2) {
                    t.started2 = true;
                    //t.cameraControls.enabled = true;
                    //if (animate) t.pathAnimation.play(0);
                    //t.camera.lookAt (t.s2[0].position);
                    //t.cameraControls._camera.lookAt (t.s2[0].position);
                    //t.cameraControls._camera.position = t.cameraOrigin;


                    t.renderer.domElement.addEventListener ('pointerdown', function (evt) {
                        /*const intersects = t.raycaster.intersectObjects (t.s2);
                        console.log ('pointerdown(): t.lookClock set to -1');
                        //t.lookClock = null;
                        t.lookClock = -1;
                        if (intersects[0] && intersects[0].object.type!=='Line') {
                            t.orbitControls.enabled = false;
                        t.cameraControls.enabled= false;
                        t.flyControls.enabled = false;
                            console.log ('pointerdown()',t.orbitControls.enabled, t.cameraControls.enabled, t.flyControls.enabled);
                            //t.camera.lookAt (t.s2[0].position);
                            //t.cameraControls._camera.lookAt (t.s2[0].position);
                            //t.cameraControls._camera.position = t.cameraOrigin;
                        } else {
                        t.cameraControls.enabled= false;
                        t.flyControls.enabled = true;
                            t.orbitControls.enabled = true;
                            console.log ('pointerdown()',t.orbitControls.enabled, t.cameraControls.enabled, t.flyControls.enabled);
                            //t.camera.lookAt (t.s2[0].position);
                            //t.cameraControls._camera.lookAt (t.s2[0].position);
                            //t.cameraControls._camera.position = t.cameraOrigin;
                        }*/
                    });
                    t.renderer.domElement.addEventListener ('pointermove', function (evt) {
                        var dbg = {
                            't.cameraControls._isDragging' : t.cameraControls._isDragging,
                            't.cameraControls._dragNeedsUpdate' : t.cameraControls._dragNeedsUpdate,
                            't.lookClock' : t.lookClock
                        };
                        //if (t.debug) console.log (dbg);
                    });
                    t.renderer.domElement.addEventListener ('pointerup', function (evt) {
                        /*
                        if (t.debug) console.log ('pointerup() t.lookClock === -1, t.cameraControls.enabled');
                        t.lookClock = -1;
                        t.cameraControls.enabled = true;
                        t.orbitControls.enabled = true;
                        t.flyControls.enabled = false;
                        */
                    });
                }



                if (false && !t.dragndrop) {
                    console.log ('Initializing drag and drop');
                    //var objs = [];
                    //for (var i=0; i<t.items.length; i++) if (t.items[i].model) objs[objs.length] = t.items[i].model;

                    t.dragndrop = new DragControls(
                        t.s2, t.camera, t.renderer.domElement
                    );
                    t.dragndrop.activate();
                    $(t.renderer.domElement).contextmenu(function() {
                        return false;
                    });

                    t.dragndrop.addEventListener( 'dragstart', function ( event ) {
                        console.log ('dragstart() : init');;
                        t.cameraControls.enabled = false;
                        t.flyControls.enabled = false;
                        t.flyControls.moveState.forward = 0;
                        t.flyControls.moveState.back = 0;
                        t.orbitControls.enabled = false;

                        t.dragndrop.cube = event.object;
                        t.dragndrop.mouseX = t.mouse.layerX;
                        t.dragndrop.mouseY = t.mouse.layerY;

                        let cube = event.object;

                        for (let i=0; i<t.items.length; i++) {
                            let it2 = t.items[i];
                            if (it2.idxPath === cube.it.idxPath) {
                                debugger;
                                it2.model.position.dragStartX = it2.model.position.x;
                                it2.model.position.dragStartY = it2.model.position.y;
                                it2.model.position.dragStartZ = it2.model.position.z;
                                /*it2.model2.position.dragStartX = it2.model2.position.x;
                                it2.model2.position.dragStartY = it2.model2.position.y;
                                it2.model2.position.dragStartZ = it2.model2.position.z;*/
                            }
                        }

                    } );

                    t.dragndrop.addEventListener( 'drag', function (event) {
                        let cube = event.object;

                        //if (false)
                        for (let i=0; i<t.items.length; i++) {
                            let it2 = t.items[i];
                            if (it2.idxPath === cube.it.idxPath) {
                                debugger;
                                it2.model.position.x = it2.model.position.dragStartX + (t.dragndrop.mouseX - t.mouse.layerX) * 10;
                                it2.model.position.y = it2.model.position.dragStartY + (t.dragndrop.mouseY - t.mouse.layerY) * 10;
                                it2.model.position.z = it2.model.position.dragStartZ ;

                                /*
                                it2.model2.position.x = it2.model2.position.dragStartX - (t.dragndrop.mouseX - t.mouse.layerX);
                                it2.model2.position.y = it2.model2.position.dragStartY + (t.dragndrop.mouseY - t.mouse.layerY);
                                it2.model2.position.z = it2.model2.position.dragStartZ ;*/
                            }
                        }
                        /*
                        clearTimeout (t.posDataToDB);
                        t.posDataToDB = setTimeout(function() {
                            t.posDataToDatabase(t);
                        },
                        1000);
                        */

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
                        t.lookClock = -2;
                        t.cameraControls.enabled = true;
                        t.orbitControls.enabled = true;
                        t.flyControls.enabled = false;
                    } );

                };
            }, 50);
        };

        if (!t.started4) {
            t.started4 = true;
            t.onresize();
        };
        if (typeof callback=='function') callback(t);
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
        if (!t.showLines) return false;
        for (var i=0; i<t.permaLines.length; i++) {
            var l = t.permaLines[i];
            t.scene.remove(l.line);
            l.geometry.dispose();
            l.material.dispose();
        };
        t.lineColors = {};
        for (var i=1; i<t.items.length; i++) {
            var 
            it = t.items[i];

//            debugger;
            if (it.parent) {
                var
                parent = it.parent,
                haveThisLineAlready = false;

                if (it.name.match(/\.mp3$/)) continue;
                if (!it.model) continue;

                for (var j=0; j<t.permaLines.length; j++) {
                    if (t.permaLines[j].it === it) {
                        haveThisLineAlready = true;
                        break;
                    }
                };

                for (var p1 in t.ld3) {
                    if (p1==it.idxPath) {
                        var p1s = p1.split('/');
                        var idx = p1s[p1s.length-2];
                        if (typeof idx=='number') var color = t.items[parseInt(idx)].color; else var color = null;
                    }
                }

                var
                p1 = it.model.position,
                p2 = parent.model.position;

                //if (p1.x===0 && p1.y===0 && p1.z===0) continue;
                //if (p2.x===0 && p2.y===0 && p2.z===0) continue;

                const points = [];
                points.push( new THREE.Vector3( p1.x, p1.y, p1.z ) );
                points.push( new THREE.Vector3( p2.x, p2.y, p2.z ) );

                var
                geometry = new THREE.BufferGeometry().setFromPoints (points);
                if (!t.lineColors) t.lineColors = {};
                if (!t.lineColors[it.parent.idx] && color) {
                    t.lineColors[it.parent.idx] = color;
                } else {
                    var color = t.lineColors[it.parent.idx];
                }

                if (!color) color = 'rgb(255,255,255)';

                var
                material = new THREE.LineBasicMaterial({ color: color, linewidth :1, opacity : 0.5, transparent : true }),
                line = new THREE.Line( geometry, material );
                t.scene.add(line);

                t.permaLines.push ({
                    line : line,
                    geometry : geometry,
                    material : material,
                    it : it
                });
            }
        }
        //$.cookie('3DFDM_lineColors', JSON.stringify(t.lineColors), na.m.cookieOptions());
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
        
        el.addEventListener('mousemove', function() { debugger; t.onMouseMove (event, t) });
        el.addEventListener('pointerup', function() { debugger; t.onPointerUp (event, t) });

        t.raycaster = new THREE.Raycaster();
        t.mouse = new THREE.Vector2();
        t.mouse.x = 0;
        t.mouse.y = 0;

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
        t.evt = event;
        debugger;
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

