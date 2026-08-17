/*--- LICENSE : https://opensource.org/licenses/MIT
 * ----- Copyright 2020-2026 by Rene AJM Veerman (rene.veerman.netherlands@gmail.com), https://grok.com and https://claude.ai/chat
 * -----
 * ----- At https://nicer.app/3D and https://nicer.app/NicerAppWebOS/businessLogic/vividUserInterface/v6.y.z/3D/na3D.js, i have a live copy runniing of https://github.com/rene-ajm-veerman/NicerApp-WebOS-MIT-5.10.z/tree/main/NicerAppWebOS/apps/NicerAppWebOS/applications/3D/app.3D.fileExplorer and https://github.com/rene-ajm-veerman/NicerApp-WebOS-MIT-5.10.z/blob/main/NicerAppWebOS/businessLogic/vividUserInterface/v6.y.z/3D/na3D.js i have data supplied by https://github.com/rene-ajm-veerman/NicerApp-WebOS-MIT-5.10.z/blob/main/NicerAppWebOS/apps/NicerAppWebOS/applications/3D/app.3D.fileExplorer/ajax_getBackgroundsRecursive.php
 * ---*/

import * as three from '/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.module.js';
import * as THREE from '/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.module.js';
import { Stats } from "/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/libs/stats.module.js";
import { GLTFLoader } from "/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/GLTFLoader.js";
import { FBXLoader } from "/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/FBXLoader.js";
import { KTX2Loader } from "/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/KTX2Loader.js";
import { DRACOLoader } from "/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/DRACOLoader.js";
import { OrbitControls } from "/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/OrbitControls.js";
import { RGBELoader } from "/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/loaders/RGBELoader.js";
import { DragControls } from "/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/DragControls.js";
import { FlyControls } from "/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/FlyControls.js";
import { FirstPersonControls } from "/NicerAppWebOS/3rd-party/3D/libs/three.js/examples/jsm/controls/FirstPersonControls.js";
import gsap from "https://cdn.jsdelivr.net/npm/gsap@3.12.2/index.js";
import { EffectComposer, BloomEffect, EffectPass, RenderPass } from "https://esm.sh/postprocessing@6.36.3";
import SpriteText from "https://esm.sh/three-spritetext@1.9.3";
/*
 *  import {
 *    CSS2DRenderer,
 *    CSS2DObject,
 *  } from "https://unpkg.com/three@0.125.2/examples/jsm/renderers/CSS2DRenderer.js";
 */

export class na3D_fileBrowser {
    constructor(el, parent, parameters) {
        var t = window.threed = na.threeD = na.threed = window.threeD = this;
        t.me = 'na3D.js::na3D_fileBrowser';
        var fncn = t.me + '::constructor(el,parent,parameters)';


        na.threeD.audioVisualizer3D = {
            scene: null,
            analyser: null,
            audioContext: null,
            source: null,
            currentAudio: null,
            visualGroup: null,
            particles: null,
            centralOrb: null,
            animationFrame: null,

            // Smoothing buffers
            bassHistory: new Array(12).fill(0),
            midHistory: new Array(12).fill(0),
            trebleHistory: new Array(12).fill(0),
            volumeHistory: new Array(8).fill(0),

            lastTime: 0,
            beatThreshold: 1.4,

            init(t) {
                this.scene = t.graph.scene();
                if (!this.scene) return console.warn("No scene found");

                this.visualGroup = new THREE.Group();
                this.scene.add(this.visualGroup);

                this.createCentralOrb();
                this.createParticles();

                console.log("3D Audio Visualizer (Smooth) initialized");
            },

            createCentralOrb() {
                const geometry = new THREE.IcosahedronGeometry(8, 4);
                const material = new THREE.MeshPhongMaterial({
                    color: 0x00ffcc,
                    emissive: 0x0088aa,
                    shininess: 120,
                    transparent: true,
                    opacity: 0.555
                });

                this.centralOrb = new THREE.Mesh(geometry, material);
                this.visualGroup.add(this.centralOrb);

                // Glow
                const glow = new THREE.Mesh(
                    new THREE.IcosahedronGeometry(9.8, 4),
                                            new THREE.MeshBasicMaterial({
                                                color: 0x44ffff,
                                                transparent: true,
                                                opacity: 0.3,
                                                blending: THREE.AdditiveBlending
                                            })
                );
                this.centralOrb.add(glow);
            },

            /* simple (2D like)
             *            createParticles() {
             *                const count = 1200;
             *                const positions = new Float32Array(count * 3);
             *                const colors = new Float32Array(count * 3);
             *
             *                for (let i = 0; i < count * 3; i += 3) {
             *                    const r = 18 + Math.random() * 35;
             *                    const theta = Math.random() * Math.PI * 2;
             *                    const phi = Math.acos(2 * Math.random() - 1);
             *
             *                    positions[i]     = r * Math.sin(phi) * Math.cos(theta);
             *                    positions[i + 1] = r * Math.sin(phi) * Math.sin(theta) * 0.6;
             *                    positions[i + 2] = r * Math.cos(phi);
             *
             *                    colors[i]     = 0.1;
             *                    colors[i + 1] = 0.7 + Math.random() * 0.3;
             *                    colors[i + 2] = 1.0;
        }

        const geo = new THREE.BufferGeometry();
        geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        geo.setAttribute('color', new THREE.BufferAttribute(colors, 3));

        const mat = new THREE.PointsMaterial({
        size: 0.7,
        vertexColors: true,
        transparent: true,
        opacity: 0.85,
        blending: THREE.AdditiveBlending,
        depthTest: false
        });

        this.particles = new THREE.Points(geo, mat);
        this.visualGroup.add(this.particles);
        },
        */

            // Fibonacci sphere for particles :
            createParticles() {
                const COUNT = 1200;
                const PHI = Math.PI * (3 - Math.sqrt(5)); // golden angle ~137.5°

                const positions  = new Float32Array(COUNT * 3);
                const origins    = new Float32Array(COUNT * 3); // rest positions on unit sphere
                const colors     = new Float32Array(COUNT * 3);

                for (let i = 0; i < COUNT; i++) {
                    // Fibonacci sphere — evenly distributed, no clumping at poles
                    const y     = 1 - (2 * i / (COUNT - 1));       // -1 → +1
                    const r     = Math.sqrt(Math.max(0, 1 - y * y));
                    const theta = PHI * i;

                    const nx = r * Math.cos(theta);
                    const ny = y;
                    const nz = r * Math.sin(theta);

                    const radius = 28; // rest radius of the shell
                    origins[i * 3]     = nx;
                    origins[i * 3 + 1] = ny;
                    origins[i * 3 + 2] = nz;

                    positions[i * 3]     = nx * radius;
                    positions[i * 3 + 1] = ny * radius;
                    positions[i * 3 + 2] = nz * radius;

                    // Cool blue-teal palette
                    colors[i * 3]     = 0.05 + Math.random() * 0.15;  // R
                    colors[i * 3 + 1] = 0.6  + Math.random() * 0.35;  // G
                    colors[i * 3 + 2] = 1.0;                           // B
                }

                const geo = new THREE.BufferGeometry();
                geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
                geo.setAttribute('color',    new THREE.BufferAttribute(colors,    3));

                // Store origins for use in the animation loop
                geo._fibOrigins = origins;

                const mat = new THREE.PointsMaterial({
                    size: 0.8,
                    vertexColors: true,
                    transparent: true,
                    opacity: 0.9,
                    blending: THREE.AdditiveBlending,
                    depthTest: false
                });

                this.particles = new THREE.Points(geo, mat);
                this.visualGroup.add(this.particles);
            },

            connectAudio(audioElement) {
                if (!this.audioContext) {
                    try {
                        this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    } catch(e) {
                        console.warn('AudioContext failed (needs user gesture):', e);
                        return; // bail gracefully, visualizer won't work until first tap
                    }
                    this.analyser = this.audioContext.createAnalyser();
                    this.analyser.fftSize = 2048;
                    this.analyser.smoothingTimeConstant = 0.82;
                }

                if (!this.source) {
                    this.source = this.audioContext.createMediaElementSource(audioElement);
                    this.source.connect(this.analyser);
                    this.analyser.connect(this.audioContext.destination);
                }

                this.currentAudio = audioElement;
                this.startVisualization();
            },

            startVisualization() {
                if (this.animationFrame) cancelAnimationFrame(this.animationFrame);

                const dataArray = new Uint8Array(this.analyser.frequencyBinCount);

                const animate = (timestamp) => {
                    const dt = Math.min((timestamp - (this.lastTime || timestamp)) / 16, 3);
                    this.lastTime = timestamp;

                    this.analyser.getByteFrequencyData(dataArray);

                    // === Smoothed frequency bands ===
                    let bass = 0, mid = 0, treble = 0;
                    for (let i = 0; i < 12; i++) bass += dataArray[i];
                    for (let i = 30; i < 120; i++) mid += dataArray[i];
                    for (let i = 180; i < 500; i++) treble += dataArray[i];

                    bass = bass / 12 / 255;
                    mid = mid / 90 / 255;
                    treble = treble / 320 / 255;

                    // Exponential smoothing
                    this.bassHistory.push(bass); this.bassHistory.shift();
                    this.midHistory.push(mid); this.midHistory.shift();
                    this.trebleHistory.push(treble); this.trebleHistory.shift();

                    const smoothBass = this.bassHistory.reduce((a,b)=>a+b)/this.bassHistory.length;
                    const smoothMid = this.midHistory.reduce((a,b)=>a+b)/this.midHistory.length;
                    const smoothTreble = this.trebleHistory.reduce((a,b)=>a+b)/this.trebleHistory.length;

                    const volume = (smoothBass + smoothMid * 1.1 + smoothTreble * 0.8) / 2.9;

                    // === Smooth Orb Animation ===
                    if (this.centralOrb) {
                        const targetScale = 1 + smoothBass * 2.2 + smoothMid * 0.8;
                        this.centralOrb.scale.lerp(new THREE.Vector3(targetScale, targetScale, targetScale), 0.18);

                        this.centralOrb.rotation.y += 0.003 + smoothTreble * 0.018;
                        this.centralOrb.rotation.x = Math.sin(timestamp/1200) * 0.08 * smoothBass;
                    }

                    // === Smooth Particles ===
                    if (this.particles) {
                        const pos = this.particles.geometry.attributes.position.array;
                        for (let i = 1; i < pos.length; i += 3) {
                            pos[i] += Math.sin(timestamp/180 + i) * 0.035 * smoothMid;
                        }
                        this.particles.geometry.attributes.position.needsUpdate = true;

                        this.particles.rotation.y += 0.0012 + smoothTreble * 0.022;
                        const pScale = 1 + volume * 1.1;
                        this.particles.scale.lerp(new THREE.Vector3(pScale, pScale, pScale), 0.22);
                    }

                    this.animationFrame = requestAnimationFrame(animate);
                };

                this.lastTime = performance.now();
                animate(this.lastTime);
            },

            stop() {
                if (this.animationFrame) cancelAnimationFrame(this.animationFrame);
                if (this.centralOrb) this.centralOrb.scale.setScalar(1);
                if (this.particles) this.particles.scale.setScalar(1);
            },

            /*
             *            followNode(node) {
             *                if (node?.__threeObj && this.visualGroup) {
             *                    this.visualGroup.position.copy(node.__threeObj.position);
             *                    this.visualGroup.position.y += 15;
        }
        }*/

            followNode(node) {
                if (!node || !this.visualGroup) return;

                // Get world position from the ForceGraph3D three.js object
                const pos = node.__threeObj?.position ?? node.pos ?? { x: 0, y: 0, z: 0 };
                this.visualGroup.position.set(pos.x, pos.y + 15, pos.z);

                // Resolve hierarchical color from node level
                debugger;
                var
                d = node.item?.level ?? node.level ?? 0,
                d = Math.round(d / 2) + 1;
                const hex = window.threed.getHierarchicalColor(window.threed, d);
                const color = new THREE.Color(hex);

                // Recolor central orb
                if (this.centralOrb) {
                    this.centralOrb.material.color.set(color);
                    this.centralOrb.material.emissive.set(color.clone().multiplyScalar(0.4));

                    // Recolor glow child
                    const glow = this.centralOrb.children[0];
                    if (glow?.material) {
                        glow.material.color.set(color);
                    }
                }

                // Recolor particles — shift their hue toward the node color
                if (this.particles) {
                    const colors = this.particles.geometry.attributes.color.array;
                    const count  = colors.length / 3;
                    const r = color.r, g = color.g, b = color.b;

                    for (let i = 0; i < count; i++) {
                        // Blend each particle toward the node color, keep some brightness variation
                        const t = 0.8 + Math.random() * 0.2;  // 80–100% toward node color
                        colors[i * 3]     = r * t;
                        colors[i * 3 + 1] = g * t;
                        colors[i * 3 + 2] = b * t;
                    }
                    this.particles.geometry.attributes.color.needsUpdate = true;
                }
            }

        };


        t.debug = true;

        t.autoRotate = false;
        t.showLines = false;
        t.showFiles = false;
        t.animationDuration = 20;
        t.useCameraControls = true;
        t.controlsEnabled = true;

        t.p = parent;
        t.el = el;
        t.t = $(t.el).attr("theme");
        t.settings = { parameters : parameters };
        t.data = parameters.views[0];
        t.fid = 1; // (int) folder id in #fileListing
        t.loading = false;
        t.resizing = false;
        t.lights = [];
        t.folders = [];
        t.ld1 = {}; //levelDataOne
        t.ld2 = {}; //levelDataTwo
        t.items = [];
        t.itemsFolders = [];
        t.meshLength = 150;
        t.wireframe = false;
        window.totaldelta = 0;

        // Show progress
        const progressContainer = document.getElementById('na3D_progress');
        if (progressContainer) progressContainer.style.display = 'block';

        let currentProgress = 5;
        const updateProgress = (percent, text) => {
            currentProgress = Math.max(currentProgress, percent);
            const bar = document.getElementById('na3D_progressBar');
            const txt = document.getElementById('na3D_progressText');
            if (bar) bar.style.width = `${currentProgress}%`;
            if (txt) txt.textContent = text || `${Math.round(currentProgress)}%`;
        };

        t._progressCallback = updateProgress;

        //na.d.s.visibleDivs.push ("#siteToolbarLeft");
        //na.d.s.visibleDivs.push ("#siteToolbarRight");
        //na.desktop.resize();

        var it = {
            id : 1,
            name : "music",
            data : 'music',
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
            idxPath : "/0",
            filepath : "/0/filesAtRoot/folders",
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
            id : 1,
            type : "naFolder",
            text : "music",
            parent : "#",
            idx : 0,
            idxPath : "/0",
            state : { opened : true }
        };
        t.items.push (it);
        t.itemsFolders.push (fit);


        t.lines = []; // onhover lines only in here
        t.permaLines = []; // permanent lines, the lines that show all of the parent-child connections.
        t.s2 = []; // search array filled with the files and folders three.js models, used by raycaster.intersectObjects()

        var
        c = $.cookie("3DFDM_lineColors");
        if (typeof c=="string" && c!=="") {
            t.lineColors = JSON.parse(c);
        }

        /*t.scene = new THREE.Scene();
         *        t.scene.add(cube)
         *        t.scene.add(new THREE.AxesHelper(5000))
         *        t.graph.camera = new THREE.PerspectiveCamera( 90  , $(el).width() / $(el).height(), 0.01, 100*1000 );
         *
         *        t.graph.camera().rotation.order = 'YXZ';
         */

        const testCanvas = document.createElement('canvas');
        const gl = testCanvas.getContext('webgl');
        const hasLogDepth = gl && !!gl.getExtension('EXT_frag_depth');
        t.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, logarithmicDepthBuffer: hasLogDepth });
        t.renderer.physicallyCorrectLights = true;
        t.renderer.outputEncoding = THREE.sRGBEncoding;
        t.renderer.setSize( $(parent).width()-20, $(parent).height()-20);
        t.renderer.setPixelRatio (window.devicePixelRatio);
        t.renderer.toneMappingExposure = 1.0;

        var innerWidth = $("#siteContent .vividDialogContent").width();
        var innerHeight = $("#siteContent .vividDialogContent").height();
        t.renderer.setSize(innerWidth, innerHeight);

        t.container = el;
        t.wglcanvas = document.getElementById("wglcanvas");
        t.controls = null;
        t.lock = null;
        t.speedx = 0;
        t.speedy = 1;
        t.speedz = 0;
        t.key = 0;
        t.paused = false;
        t.reqid = null;
        t.ctrlid = null;
        t.cup = null;
        t.clock = new THREE.Clock();
        t.delta = 0;
        t.aclock = new THREE.Clock();
        t.adelta = 0;
        t.radius = 250;
        t.eye = 6.62 * t.radius;
        t.fov = 17;
        t.near = 0.000001;
        t.far = 144000000 * t.radius;
        t.v = { controls : 4 };
        t.maxcontrols = 10;

        t.mouse = new THREE.Vector2();
        t.mouse.x = 0;
        t.mouse.y = 0;

        na.threeD = t;

        t.initializeItems (t);
        t.initHistory(t);
    }

    // ====================== HISTORY SYSTEM ======================

    initHistory(t) {
        t.history = {
            stack: [],
            redoStack: []
        };

        document.getElementById('btnUndo').onclick = () => t.undo();
        document.getElementById('btnRedo').onclick = () => t.redo();
    }

    pushHistory(t, state, label = 'Action') {
        if (!t.history) t.initHistory();

        t.history.redoStack = [];                    // clear future on new action
        if (label) state.label = label;

        t.history.stack.push(state);

        // Limit history size
        if (t.history.stack.length > 40) t.history.stack.shift();

        t.updateHistoryUI(t);
    }

    undo(t) {
        var t = this;
        if (!t.history || t.history.stack.length <= 1) return false;

        const current = t.history.stack.pop();
        t.history.redoStack.push(current);

        const previous = t.history.stack[t.history.stack.length - 1];
        debugger;
        t.restoreState(t,previous);

        t.updateHistoryUI(t);
        return true;
    }

    redo(t) {
        var t = this;
        if (!t.history || t.history.redoStack.length === 0) return false;

        const nextState = t.history.redoStack.pop();
        t.history.stack.push(nextState);

        t.restoreState(t,nextState);
        t.updateHistoryUI(t);
        return true;
    }

    updateHistoryUI(t) {
        var t = this;
        const list = document.getElementById('historyList');
        const btnUndo = document.getElementById('btnUndo');
        const btnRedo = document.getElementById('btnRedo');

        // Update list
        if (list) {
            list.innerHTML = '';

            if (t.history.stack.length === 0) {
                list.innerHTML = '<div style="padding:8px; font-style:italic; color:#888;">(no history yet)</div>';
            } else {
                for (let i = t.history.stack.length - 1; i >= 0; i--) {
                    const item = document.createElement('div');
                    item.className = 'history-item';
                    item.style.cssText = `
                    padding: 6px 10px;
                    margin: 2px 0;
                    border-radius: 4px;
                    cursor: pointer;
                    transition: all 0.2s;
                    `;

                    if (i === t.history.stack.length - 1) {
                        item.style.backgroundColor = 'rgba(0, 255, 120, 0.25)';
                        item.style.fontWeight = 'bold';
                    }

                    const label = t.history.stack[i].label || `State ${i+1}`;
                    item.innerHTML = `<small style="color:#aaa">${i+1}.</small> ${label}`;

                    item.onclick = () => t.jumpToHistory(i);
                    list.appendChild(item);
                }
            }
        }

        // Update buttons
        if (btnUndo) btnUndo.disabled = t.history.stack.length <= 1;
        if (btnRedo) btnRedo.disabled = t.history.redoStack.length === 0;
    }

    jumpToHistory(t,index) {
        if (!t.history || index < 0 || index >= t.history.stack.length) return;

        const target = t.history.stack[index];
        t.history.redoStack = t.history.stack.splice(index + 1).reverse();
        t.history.stack = t.history.stack.slice(0, index + 1);

        t.restoreState(t,target);
        t.updateHistoryUI(t);
    }

    restoreState(t,state) {
        if (!state) return;

        // Restore scroll
        if (typeof state.scrollY === 'number') {
            window.scrollTo({ top: state.scrollY, behavior: 'auto' });
        }

        // Restore camera
        if (state.camera && window.currentCamera) {
            if (state.camera.length) {
                state.camera = {
                    x: state.camera[0], y : state.camera[1], z : state.camera[2]
                }
            }
            if (state.camera.pos.length) {
                state.camera.pos = {
                    x: state.camera.pos[0], y : state.camera.pos[1], z : state.camera.pos[2]
                }
            }

            if (state.camera) {
                const cam = window.currentCamera;
                const pos = state.camera.pos;
                const forward = window.getForwardVector();
                const delta = forward.multiplyScalar(20);

                if (
                    typeof state.camera.x === 'number'
                    && typeof state.camera.y === 'number'
                    && typeof state.camera.z === 'number'
                ) {
                    window.threed.graph.cameraPosition(
                        window.threed.currentNode,
                        {
                            x: ((state.camera.pos?.lookAt?.x ?? 0) + delta.x),
                                                       y: ((state.camera.pos?.lookAt?.y ?? 0) + delta.y),
                                                       z: ((state.camera.pos?.lookAt?.z ?? 0) + delta.z)
                        },
                        100
                    );
                    window.threed.graph.cameraPosition(
                        state.camera.pos,
                        {
                            x: ((state.camera.pos?.lookAt?.x ?? 0) + delta.x),
                                                       y: ((state.camera.pos?.lookAt?.y ?? 0) + delta.y),
                                                       z: ((state.camera.pos?.lookAt?.z ?? 0) + delta.z)
                        },
                        1600
                    );
                }
                if (typeof state.camera.zoom === 'number') cam.zoom = state.camera.zoom;
                if (state.camera.rotation) {
                    cam.rotation.set(
                        state.camera.rotation.x,
                        state.camera.rotation.y,
                        state.camera.rotation.z,
                        state.camera.rotation.order
                    );
                }
            };

            if (typeof window.updateCamera === 'function') {
                window.updateCamera();
            } else if (typeof window.render === 'function') {
                window.render(); // fallback
            } else if (typeof window.currentCamera.updateProjectionMatrix==='function') {
                window.currentCamera.updateProjectionMatrix();
                window.currentCamera.updateMatrixWorld(true);
            }
        }

        // Restore active item
        if (state.activeItemId) {
            documenquerySelectorAll('.active').forEach(el => el.classLisremove('active'));
            const activeEl = documengetElementById(state.activeItemId);
            if (activeEl) activeEl.classList.add('active');
        }
    }

    onclick_node (t, node) {
        var cit = t.items[node.id], done = false;

        if (cit) {
            var html = "", j = 0;
            if (cit.name!=parseInt(cit.name)) {
                var n = cit.name;
            } else {
                var n = cit.data;
            }
            if (cit.data.files) var f = cit.data.files.sort();
            else if (cit.data.folders) var f = cit.data.folders[n].files.sort();
            else if (cit.parent?.data?.folders) var f = cit.parent.data.folders[n].files.sort();
            else debugger;
            for (var i=0; i<f.length; i++) {
                var file = ''+f[i];
                if (file.match(/\.mp3$/)) {
                    var
                    path = cit.filepath
                    .replace(/\/0\/filesAtRoot\/folders/, "")
                    .replace(/\/folders/g,"")
                    .replace('\/filesAtRoot','')
                    .replace(/\/\//g,'/')
                    .replace(/\'/g, '\\'),
                    file2 = file
                    .replace(/\-[\-\w]+\.mp3/, ".mp3")
                    .replace('.mp3', '');
                    html += '<div id="'+t.fid+'_'+j+'__'+cit.idx+'" class="vividButton" style="position:relative; font-size:small;" ><a href="#"><span>'+path+'/'+n+'/'+file2+'</span></a></div>';
                    j++;
                }
            };

            $('#fileListing').html(html).delay(500);
            na.site.startUIvisuals('fileListing');

            setTimeout (function (t, f) {
                for (var i=0; i<f.length; i++) {
                    var file = ''+f[i];
                    if (file.match(/\.mp3$/)) {
                        var
                        path = cit.filepath
                        .replace(/\/0\/filesAtRoot\/folders/, "")
                        .replace(/\/folders/g,"")
                        .replace('\/filesAtRoot','')
                        .replace(/\/\//g,'/')
                        .replace(/\'/g, '\\'),
                        id = '#'+t.fid+'_'+i+'__'+cit.idx,
                        el = $('a',$(id)[0])[0];

                        if (!el) debugger;
                        el.dataset.path = path+'/'+cit.name+'/'+file;

                        el.addEventListener('click', (evt) => {
                            na.threeD.play($(evt.currentTarget).parents('.vividButton'), evt.currentTarget.dataset.path);
                        });
                    }
                };

                t.fid++;
            }, 500, t, f);


            if (na.threeD.audioVisualizer3D) {
                na.threeD.audioVisualizer3D.followNode(node);
            }

            done = true;
        }
    }

    async initializeItems(t) {
        const p = { t, ld2: {} };
        t.s2 = [];
        t._itemsByPath = new Map();
        // seed the map with root item
        t._itemsByPath.set(t.items[0].filepath.replace(/\/folders/g, '').replace('/filesAtRoot','') + "/" + t.items[0].name, t.items[0]);

        t._progressCallback(10, "Walking file tree...");

        // Instead of na.m.walkArray (synchronous), do a chunked async walk:
        await t.createGraph(t);


        setTimeout((t) => {
            na.threeD.audioVisualizer3D.init(window.threed || t);
            t.graph.refresh();           // or t.graph._d3ReheatSimulation?.();
        t.graph.resumeAnimation?.()
        }, 100, t);

        console.time('timed 1_walk');
        await t._chunkedWalkArray(t, p);
        console.timeEnd('timed 1_walk');

        console.time('timed 2_itemsToGraphData');
        t._progressCallback(45, "Building graph data...");
        t.forcegraph3d_data = await t.itemsToGraphData(t);
        console.timeEnd('timed 2_itemsToGraphData');

        console.time('timed 3_createGraph');
        t._progressCallback(75, "Creating visualization...");
        await t.createGraph(t);
        console.timeEnd('timed 3_createGraph');
        t.itemsInitialized = true;

        await new Promise(r => setTimeout(r, 0));
        t._progressCallback(100, "Done!");

        // Hide progress bar after a short delay
        setTimeout(() => {
            const progressContainer = document.getElementById('na3D_progress');
            if (progressContainer) progressContainer.style.display = 'none';
        }, 4000);
    }

    _flattenFolderEntries(filesAtRoot) {
        const result = [];

        // Mirrors what na.m.walkArray does when it hits a "folders" key:
        // cd = { path, k, at, level, params }
        function recurse(node, path, level) {
            if (!node || !node.folders) return;

            for (const k in node.folders) {
                if (!node.folders.hasOwnProperty(k)) continue;

                const childPath = path + '/folders';
                result.push({
                    path  : childPath,   // the path TO the folders object (parent key)
                k     : k,           // the folder name
                at    : node.folders, // the object containing k
                level : level,
                params: null         // filled in by _chunkedWalkArray before calling walkKey
                });

                recurse(node.folders[k], path + '/' + k, level + 2);
            }
        }

        recurse(filesAtRoot, 'filesAtRoot', 2); // level 2 matches original walkArray behaviour
        return result;
    }
    async _chunkedWalkArray(t, p) {
        const CHUNK = 200;
        const entries = t._flattenFolderEntries(t.data[0]['filesAtRoot']);

        for (let i = 0; i < entries.length; i += CHUNK) {
            const batch = entries.slice(i, i + CHUNK);
            for (const cd of batch) {
                cd.params = p;           // inject shared params (contains t, ld2, etc.)
                t.initializeItems_walkKey(cd);
            }
            const pct = 10 + 35 * (i / entries.length);
            t._progressCallback(pct, `Processing folders… ${i + batch.length}/${entries.length}`);
            await new Promise(r => setTimeout(r, 0)); // yield to browser paint
        }
    }

    initializeItems_walkKey(cd) {
        var ps = cd.path.split("/");
        if (ps[ps.length-1] === "files") {
            return;
        } else if (ps[ps.length-1] === "folders") {

            var path = cd.path.replace(/\/folders/g, '').replace('filesAtRoot','');
            if (path[0] !== '/') path = '/' + path;
            let pk = path;

            if (!cd.params.ld2[pk]) cd.params.ld2[pk] = { levelIdx: 0 };

            if (!cd.params.t._itemsByPath) {
                cd.params.t._itemsByPath = new Map();
                // Seed root once
                const root = cd.params.t.items[0];
                cd.params.t._itemsByPath.set(
                    root.filepath.replace(/\/folders/g, '').replace('filesAtRoot','') + "/" + root.name,
                                             root
                );
            }

            // Find parent using clean key
            var lastParent = cd.params.t._itemsByPath.get(pk) ?? cd.params.t.items[0];
            var idxPath2 = lastParent.idxPath + (lastParent.id?'/' + lastParent.id:'') || '/0';


            // Create the folder item
            const idx = cd.params.t.items.length;
            const it = {
                level: cd.level,
                name: cd.k,
                idx: idx,
                idxPath: idxPath2 + '/' + idx,
                filepath: path,
                levelIdx: ++cd.params.ld2[pk].levelIdx,
                parent: lastParent,
                leftRight: 0,
                upDown: 0,
                columnOffsetValue: 1000,
                rowOffsetValue: 1000,
                model: { position: new THREE.Vector3(0,0,0) },
                data: cd.at[cd.k]
            };

            // Store in ld3
            if (!cd.params.t.ld3) cd.params.t.ld3 = {};
            if (!cd.params.t.ld3[it.idxPath]) {
                cd.params.t.ld3[it.idxPath] = { itemCount: 0, folderCount: 0, items: [] };
            }
            cd.params.t.ld3[it.idxPath].folderCount++;
            cd.params.t.ld3[it.idxPath].itemCount++;
            cd.params.t.ld3[it.idxPath].items.push(it);

            // Add to main arrays + map
            cd.params.t.items.push(it);
            cd.params.t._itemsByPath.set(
                it.filepath.replace('filesAtRoot','').replace(/\/folders/g, '') + "/" + it.name,
                                         it
            );

            cd.params.it = it;

            // === Files (if enabled) ===
            if (it.data && it.data.folders && it.data.folders[it.name]) {
                const files = it.data.folders[it.name].files;
                if (it.name=='Graduated Fool') debugger;
                for (const fkey in files) {
                    if (files.hasOwnProperty(fkey)) {
                        const fileIdx = cd.params.t.items.length;
                        const it1a = {
                            data: files[fkey],
                            level: cd.level + 1,
                            name: fkey,
                            idx: fileIdx,
                            idxPath: it.idxPath + '/' + fileIdx,
                            filepath: path + "/" + cd.k,
                            levelIdx: ++cd.params.ld2[pk].levelIdx,
                            parent: it,
                            leftRight: 0,
                            upDown: 0,
                            columnOffsetValue: 1000,
                            rowOffsetValue: 1000,
                            model: { position: new THREE.Vector3(0,0,0) }
                        };

                        if (!cd.params.t.ld3[it1a.idxPath]) {
                            cd.params.t.ld3[it1a.idxPath] = { itemCount: 0, items: [] };
                        }
                        cd.params.t.ld3[it1a.idxPath].itemCount++;
                        cd.params.t.ld3[it1a.idxPath].items.push(it1a);

                        cd.params.t.items.push(it1a);
                        cd.params.t._itemsByPath.set(
                            it1a.filepath + "/" + it1a.name,                                                     it1a
                        );
                    }
                }
            }
        }
    }
    initializeItems_walkValue (cd) {
        //console.log ("initializeItems_walkValue", "cd", cd);
    }

    onresize (t, levels) {
        var t = this;
        //debugger;
        //t.onresize_do (t, levels);
        na.m.waitForCondition ("waiting for other onresize commands to finish",
                               function () { return na.desktop.settings.animating === false },
                               function () { t.onresize_do (t, levels); },
                               50
        );
    }


    onresize_do(t, callback) {
        t.resizing = true;
        t.overlaps = [];

        let
        fncn = 'na3D.js::onresize_do()',
        c = {};

        t.ld4 = [];
        t.s2 = [];

        $('.na3D').css({
            width : $("#siteContent .vividDialogContent").width(),
                       height : $("#siteContent .vividDialogContent").height()
        });

        t.onresize_do_phase2 (t, callback);

    }


    getAllAncestors(node) {
        const ancestors = new Set([0]);
        if (!node?.item?.idxPath) return ancestors;

        const path = node.item.idxPath;
        if (typeof path === 'string') {
            const parts = path.replace(/^\//, '').split('/').filter(Boolean);
            for (let p of parts) {
                const idx = parseInt(p);
                if (!isNaN(idx)) ancestors.add(idx);
            }
        }
        ancestors.add(node.id || node.idx);
        return ancestors;
    }

    getAllDescendants(t, node) {
        const descendants = new Set();
        if (!node) return descendants;

        const queue = [node.id || node];
        while (queue.length) {
            const id = queue.shift();
            descendants.add(id);

            // Use childMap if available, or fall back to links
            const children = (t.childMap && t.childMap.get(id)) ||
            t.graph.graphData().links
            .filter(l => (l.source.id || l.source) === id)
            .map(l => l.target.id || l.target);

            children.forEach(childId => {
                if (!descendants.has(childId)) queue.push(childId);
            });
        }
        return descendants;
    }


    onresize_do_phase2(t, callback) {
        let fncn = 'na3D.js::onresize_do_phase2()';
        // NEW: After all meshes added, start recursive projection from root
        //t.projectHierarchy(t, t.items[0], 10*1000); // Start radius ~5000; adjust as needed

        var textures = {};


        // Polyfill for libraries that expect Node.js globals
        window.process = window.process || {};
        window.process.env = window.process.env || {};
        window.process.env.NODE_ENV = 'production';   // or 'development'


        $('#fileListing, #playlist').css({overflowY:'auto'});
        t.initialized = true;
        var x = t.items;
        t.onresize_postDo(t, true);
    }

    onresize_postDo (t, animate=false) {
        //t.drawLines(t);
        //t.graph.controls()._camera().lookAt (t.s2[0].position);

        const width = t.el.clientWidth;
        const height = t.el.clientHeight;

        if (t.graph) t.graph
            .width(width)
            .height(height);

        t.resizing = false;

        if (!t.started4) {
            t.started4 = true;
        };
        if (typeof callback=="function") callback(t);
    }

    getHierarchicalColor(t, depth) {
        const colors = [
            '#4dabf7',   // 0 - root / top level (blue)
            '#51cf66',   // 1 - light green
            '#ffd43b',   // 2 - yellow
            '#ff922b',   // 3 - orange
            '#ffbbdd',   // 4 - pink
            '#9775fa',   // 5 - purple
            '#74c0fc',   // 6
            '#63e6be',
            'lime',
            'cyan',
            'red',
            '#f06595', // PINK
            'grey',
            'white',
            'ivory'
        ];
        const maxDepth = t.maxLevel;

        if (depth <= 0 || maxDepth <= 0) return colors[0];

        // Linear relative mapping - Recommended
        // Spreads colors evenly from depth 0 to maxDepth
        const relativeIndex = Math.floor((depth / maxDepth) * (colors.length - 1));
        return colors[Math.min(relativeIndex, colors.length - 1)];

        /*
         *        var y = Math.min(depth, colors.length - 1);
         *        var x = colors[y] || '#aaaaaa';
         *        //return x;
         *        //return colors[Math.min(Math.abs(colors.length*depth/100), colors.length - 1)] || '#aaaaaa';
         *        return colors[depth % colors.length]*/
    }

    encodePath (str) {
        if (typeof str !== 'string') return '';

        let result = '';
        for (let i = 0; i < str.length; i++) {
            const char = str[i];
            const code = char.charCodeAt(0);

            // Only keep safe alphanumeric characters
            if ((code >= 65 && code <= 90) ||    // A-Z
                (code >= 97 && code <= 122) ||   // a-z
                (code >= 48 && code <= 57)) {    // 0-9
                    result += char;
                } else {
                    // Everything else (including @ ( ) & # $ % ^ etc.) gets encoded
                    if (char=='%')
                        result += '%';
                    else if (char=='/')
                        result += '/';
                    else if (code < 256) {
                        result += '%' + code.toString(16).toUpperCase().padStart(2, '0');
                    } else {
                        // Unicode support
                        result += encodeURIComponent(char);
                    }
                }
        }
        return result;
    }

    play(buttonEl, relPath) {
        const rawPath = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.fancy.latest.2D/music/' + relPath;
        const fullUrl = na.m.encodeUnicodePath(rawPath);  // encode ONCE, here

        const doPlay = async (audio) => {
            if (decodeURIComponent(audio.src) !== decodeURIComponent(location.origin + fullUrl)) {
                // Safely abort any in-flight play before changing src
                try { audio.pause(); } catch(e) {}
                audio.src = fullUrl;
                audio.load();
            }
            try {
                await audio.play();
            } catch (err) {
                if (err.name !== 'AbortError') {
                    console.error("Playback failed:", err);
                }
                // AbortError is expected when track changes quickly — safe to ignore
            }
        };

        if (!na.threeD.currentAudio) {
            const audio = na.threeD.currentAudio = new Audio();

            // Connect to visualizer ONCE, before any src is set
            if (na.threeD.audioVisualizer3D) {
                na.threeD.audioVisualizer3D.connectAudio(audio);
            }

            audio.addEventListener('ended', () => {
                na.threeD.audioVisualizer3D?.stop();
            });
            audio.addEventListener('error', (e) => {
                console.error("Audio error:", audio.src, e);
                na.threeD.audioVisualizer3D?.stop();
            });
        }

        doPlay(na.threeD.currentAudio);

        // UI updates
        $('#fileListing .vividButtonSelected').removeClass('vividButtonSelected').addClass('vividButton');
        $(buttonEl).addClass('vividButtonSelected');

        let i = $('#playlist li').length;
        let html = '<li style="margin-right:10px;"><div id="playList_'+i+'" class="vividButton" style="position:relative;"><a href="javascript:na.apps.loaded.threed_fileExplorer.play($(\'#playList_'+i+'\')[0], \''+relPath.replace(/'/g,"\\'")+'\');" style="font-size:medium">'+relPath+'</a></div></li>';
        $('#playlist ul').append(html);
        na.site.startUIvisuals();
    }
    getChildren(item) {
        return t.items.filter(it => it.parent === item && (t.showFiles || it.name.indexOf('.')===-1));
    }


    applyAutoRotate(t) {
        const controls = t.graph?.controls();
        if (!controls) return;

        controls.target.set(0, 0, 0);        // always orbit origin, not camera focus point
        controls.autoRotate      = t.autoRotate;
        controls.autoRotateSpeed = 0.6;
        controls.update();
    }


    switchView(evt) {
        var t = this;
        $('.checkbox_naThreeD_viewType').each(function(idx,el){
            el.checked = false;
        });
        evt.currentTarget.checked = true;
        $('input', evt.currentTarget)[0].checked = true;
        if ($('input', evt.currentTarget)[0]) {
            t.currentViewMode = $('input', evt.currentTarget)[0].id;
        } else {
            t.currentViewMode = evt.currentTarget.id;
        }
        t.switchView_doGraph();
    }

    switchView_doGraph() {
        var t = this;
        t.createGraph(t);
    }

    async createGraph(t) {
        var vm = t.currentViewMode;
        debugger;

        if (t.graph) {
            t.graph._destructor?.();
            t.graph = null;
        }
        // === Before creating t.graph ===
        const maxLevels = 12; // adjust based on your deepest hierarchy
        t.dagLevelDistances = new Array(maxLevels).fill(0).map((_, level) => {
            return 100 + (level * 40) + Math.random() * 380;
        });
        t.maxLevel = 0;
        for (var i=0; i<t.items.length; i++) {
            var it = t.items[i];
            if (it.level > t.maxLevel) t.maxLevel = it.level;
        }

        const container = t.el;
        if (!container) return;

        const data = t.forcegraph3d_data;
        if (!data?.nodes?.length) return;

        const nodeIds = new Set(data.nodes.map(n => n.id));
        const validLinks = data.links.filter(link => {
            let src = typeof link.source === 'object' ? link.source.id : link.source;
            let tgt = typeof link.target === 'object' ? link.target.id : link.target;
            return nodeIds.has(src) && nodeIds.has(tgt);
        });

        // All data loaded — now assign positions, THEN start simulation
        // Build parent->children map
        const childMap = new Map();
        validLinks.forEach(link => {
            const src = typeof link.source === 'object' ? link.source.id : link.source;
            const tgt = typeof link.target === 'object' ? link.target.id : link.target;
            if (!childMap.has(src)) childMap.set(src, []);
            childMap.get(src).push(tgt);
        });
        t.childMap = childMap;


        const nodeMap = new Map(data.nodes.map(n => [n.id, n]));
        t.nodeMap = nodeMap;

        // Find root
        const targetIds = new Set(validLinks.map(l => typeof l.target === 'object' ? l.target.id : l.target));
        const rootNode = data.nodes.find(n => !targetIds.has(n.id));
        const rootId = rootNode?.id ?? 0;

        const levelRadius = 50;  // distance per level — tune this

        // BFS: collect nodes level by level
        const levels = [];
        const visited2 = new Set();
        let currentLevel = [rootId];
        visited2.add(rootId);
        while (currentLevel.length > 0) {
            levels.push(currentLevel);
            const nextLevel = [];
            currentLevel.forEach(id => {
                (childMap.get(id) || []).forEach(childId => {
                    if (!visited2.has(childId)) {
                        visited2.add(childId);
                        nextLevel.push(childId);
                    }
                });
            });
            currentLevel = nextLevel;
        }

        // Position root at origin
        const root = nodeMap.get(rootId);
        if (root) { root.x = root.fx = 0; root.y = root.fy = 0; root.z = root.fz = 0; }

        const goldenRatio = (1 + Math.sqrt(5)) / 2;

        // Level-1 nodes define the cap centers for ALL their descendants
        const level1Nodes = levels[1] ?? [];
        const level1Count = level1Nodes.length;
        const nodeSectorCenter = new Map();

        level1Nodes.forEach((childId, i) => {
            const phi   = Math.acos(1 - 2 * (i + 0.5) / level1Count);
            const theta = 2 * Math.PI * i / goldenRatio;
            const dir = {
                x: Math.sin(phi) * Math.cos(theta),
                            y: Math.sin(phi) * Math.sin(theta),
                            z: Math.cos(phi),
            };
            // Stamp this direction as cap center for the level-1 node and ALL descendants
            nodeSectorCenter.set(childId, dir);
            const queue = [...(childMap.get(childId) || [])];
            while (queue.length) {
                const id = queue.shift();
                nodeSectorCenter.set(id, dir);
                (childMap.get(id) || []).forEach(c => queue.push(c));
            }
        });

        // Cap angle based on number of level-1 sectors
        const capAngle = Math.PI / Math.sqrt(Math.max(level1Count, 1));

        if (
            vm=='checkbox_treeView'
            || vm=='checkbox_bushView'
        ) {
            const parentMap = new Map();
            validLinks.forEach(link => {
                const src = typeof link.source === 'object' ? link.source.id : link.source;
                const tgt = typeof link.target === 'object' ? link.target.id : link.target;
                parentMap.set(tgt, src);
            });
            const goldenAngle = Math.PI * (3 - Math.sqrt(5));  // ~2.399963 rad
            t.positionBushTree(t, levels, t.nodeMap, t.childMap, parentMap, goldenAngle);
        } else if (vm=='checkbox_FibonnaciView') {
            // Position each level as Fibonacci caps
            levels.forEach((levelNodes, levelIdx) => {
                if (levelIdx === 0) return;

                const totalCount = levelNodes.length;
                //const r = Math.max(levelIdx * 25, Math.sqrt(totalCount) * 40);

                // Use the item's actual level as distance from center
                const itemLevel = levelNodes[0].item?.level ?? levelIdx;
                const r = itemLevel * 750;  // tune the 250 to taste

                // Group by sector center
                const bySector = new Map();
                levelNodes.forEach(nodeId => {
                    const center = nodeSectorCenter.get(nodeId);
                    if (!center) return;
                    const key = `${center.x.toFixed(3)}_${center.y.toFixed(3)}`;
                    if (!bySector.has(key)) bySector.set(key, { center, nodes: [] });
                    bySector.get(key).nodes.push(nodeId);
                });

                bySector.forEach(({ center, nodes }) => {
                    // Sort alphabetically within sector
                    nodes.sort((a, b) =>
                    (nodeMap.get(a)?.name ?? '').localeCompare(nodeMap.get(b)?.name ?? '')
                    );

                    const count = nodes.length;
                    const cosCapAngle = Math.cos(capAngle);

                    // Two axes perpendicular to center for rotating Fibonacci onto cap
                    const right = normalize(cross(
                        center,
                        Math.abs(center.z) < 0.9 ? { x: 0, y: 0, z: 1 } : { x: 1, y: 0, z: 0 }
                    ));
                    const up = normalize(cross(right, center));

                    nodes.forEach((nodeId, i) => {
                        const node = nodeMap.get(nodeId);
                        if (!node) return;

                        // Fibonacci on spherical cap — phi spans [0, capAngle]
                        const phi   = Math.acos(1 - (1 - cosCapAngle) * (i + 0.5) / count);
                        const theta = 2 * Math.PI * i / goldenRatio;

                        const sinPhi = Math.sin(phi);
                        const cosPhi = Math.cos(phi);

                        // Rotate Fibonacci point onto cap centered at `center`
                        const dir = {
                            x: center.x * cosPhi + (Math.cos(theta) * right.x + Math.sin(theta) * up.x) * sinPhi,
                                  y: center.y * cosPhi + (Math.cos(theta) * right.y + Math.sin(theta) * up.y) * sinPhi,
                                  z: center.z * cosPhi + (Math.cos(theta) * right.z + Math.sin(theta) * up.z) * sinPhi,
                        };

                        node.x = node.fx = r * dir.x;
                        node.y = node.fy = r * dir.y;
                        node.z = node.fz = r * dir.z;

                        if (window.threed.audioVisualizer3D && t.currentNode && nodeId===t.currentNode.id) window.threed.audioVisualizer3D.followNode(t.currentNode);
                    });
                });
            });
        } else {

            // ── REPLACE the entire levels.forEach(…) positioning block ──────────────────
            // (the block that starts with: levels.forEach((levelNodes, levelIdx) => {)
            // and ends just before: function cross(a, b) {
            // ── with this: ───────────────────────────────────────────────────────────────
            const parentMap = new Map();
            validLinks.forEach(link => {
                const src = typeof link.source === 'object' ? link.source.id : link.source;
                const tgt = typeof link.target === 'object' ? link.target.id : link.target;
                parentMap.set(tgt, src);
            });
            const goldenAngle = Math.PI * (3 - Math.sqrt(5));  // ~2.399963 rad

            // Position root
            if (root) { root.x = root.fx = 0; root.y = root.fy = 0; root.z = root.fz = 0; }

            // Per-parent Fibonacci sphere: iterate BFS level by level
            levels.forEach((levelNodes, levelIdx) => {
                if (levelIdx === 0) return;  // root already placed

                // Group children by their parent
                const byParent = new Map();
                levelNodes.forEach(nodeId => {
                    const parentId = parentMap.get(nodeId);
                    if (parentId === undefined) return;
                    if (!byParent.has(parentId)) byParent.set(parentId, []);
                    byParent.get(parentId).push(nodeId);
                });

                byParent.forEach((children, parentId) => {
                    const parentNode = nodeMap.get(parentId);
                    if (!parentNode) return;

                    const count = children.length;

                    // Sphere radius scales with child count — tune the multiplier to taste
                    // Level 1 children of root use a larger radius so the big sphere stays prominent
                    const baseRadius = levelIdx === 1
                    ? Math.max(400, Math.sqrt(count) * 60)
                    : Math.max(80,  Math.sqrt(count) * 30);

                    // Sort for stable ordering
                    children.sort((a, b) =>
                    (nodeMap.get(a)?.name ?? '').localeCompare(nodeMap.get(b)?.name ?? '')
                    );

                    children.forEach((nodeId, i) => {
                        const node = nodeMap.get(nodeId);
                        if (!node) return;

                        // Fibonacci sphere around the parent position
                        const phi   = Math.acos(1 - 2 * (i + 0.5) / count);
                        const theta = goldenAngle * i;

                        node.x = node.fx = (parentNode.fx ?? parentNode.x ?? 0) + baseRadius * Math.sin(phi) * Math.cos(theta);
                        node.y = node.fy = (parentNode.fy ?? parentNode.y ?? 0) + baseRadius * Math.sin(phi) * Math.sin(theta);
                        node.z = node.fz = (parentNode.fz ?? parentNode.z ?? 0) + baseRadius * Math.cos(phi);

                    });
                });
            });
            // ── end replacement ──────────────────────────────────────────────────────────
            // ── Fibonacci sphere positioning with minimum radial distance ────────────────
            const LEVEL_RADIUS_MULTIPLIER = 350; // tune: units of min distance per item.level unit

            // Helper: ensure a node's (fx,fy,fz) is at least minR from origin,
            // scaling outward along its existing direction. Returns true if moved.
            function enforceMinRadius(node, minR) {
                const x = node.fx ?? node.x ?? 0;
                const y = node.fy ?? node.y ?? 0;
                const z = node.fz ?? node.z ?? 0;
                const r = Math.sqrt(x*x + y*y + z*z);
                if (r < minR) {
                    if (r < 0.001) {
                        // Degenerate: place along +X axis
                        node.x = node.fx = minR;
                        node.y = node.fy = 0;
                        node.z = node.fz = 0;
                    } else {
                        const scale = minR / r;
                        node.x = node.fx = x * scale;
                        node.y = node.fy = y * scale;
                        node.z = node.fz = z * scale;
                    }
                    return true;
                }
                return false;
            }

            // Position root
            if (root) { root.x = root.fx = 0; root.y = root.fy = 0; root.z = root.fz = 0; }

            // BFS level by level so parents are always positioned before children
            levels.forEach((levelNodes, levelIdx) => {
                if (levelIdx === 0) return; // root already placed

                // Group children by their parent
                const byParent = new Map();
                levelNodes.forEach(nodeId => {
                    const parentId = parentMap.get(nodeId);
                    if (parentId === undefined) return;
                    if (!byParent.has(parentId)) byParent.set(parentId, []);
                    byParent.get(parentId).push(nodeId);
                });

                byParent.forEach((children, parentId) => {
                    const parentNode = nodeMap.get(parentId);
                    if (!parentNode) return;

                    // ── Enforce minimum radius on the PARENT first ──────────────────────
                    // so the whole child cluster inherits a valid anchor point
                    const parentItem = parentNode.item;
                    const parentLevel = parentItem?.level ?? 0;
                    if (parentLevel > 0) {
                        enforceMinRadius(parentNode, parentLevel * LEVEL_RADIUS_MULTIPLIER);
                    }

                    const count = children.length;

                    // Sphere radius scales with child count
                    const baseRadius = levelIdx === 1
                    ? Math.max(400, Math.sqrt(count) * 60)
                    : Math.max(80,  Math.sqrt(count) * 30);

                    // Sort for stable ordering
                    children.sort((a, b) =>
                    (nodeMap.get(a)?.name ?? '').localeCompare(nodeMap.get(b)?.name ?? '')
                    );

                    // Place children as a Fibonacci sphere centred on the (now corrected) parent
                    children.forEach((nodeId, i) => {
                        const node = nodeMap.get(nodeId);
                        if (!node) return;

                        const phi   = Math.acos(1 - 2 * (i + 0.5) / count);
                        const theta = goldenAngle * i;

                        node.x = node.fx = (parentNode.fx ?? parentNode.x ?? 0) + baseRadius * Math.sin(phi) * Math.cos(theta);
                        node.y = node.fy = (parentNode.fy ?? parentNode.y ?? 0) + baseRadius * Math.sin(phi) * Math.sin(theta);
                        node.z = node.fz = (parentNode.fz ?? parentNode.z ?? 0) + baseRadius * Math.cos(phi);

                        // ── Enforce minimum radius on each child as well ─────────────────
                        // catches any child that the Fibonacci sphere aimed back at center
                        const childItem = node.item;
                        const childLevel = childItem?.level ?? 0;
                        if (childLevel > 0) {
                            enforceMinRadius(node, childLevel * LEVEL_RADIUS_MULTIPLIER);
                        }
                        if (window.threed.audioVisualizer3D && t.currentNode && nodeId===t.currentNode.id) window.threed.audioVisualizer3D.followNode(t.currentNode);
                    });
                });
            });
            // ── end Fibonacci sphere positioning with minimum radius from center for all sublevels ─────────────────────────────────────────
        }

        function cross(a, b) {
            return {
                x: a.y * b.z - a.z * b.y,
                y: a.z * b.x - a.x * b.z,
                z: a.x * b.y - a.y * b.x,
            };
        }
        function normalize(v) {
            const len = Math.sqrt(v.x**2 + v.y**2 + v.z**2);
            return len === 0 ? { x: 0, y: 1, z: 0 } : { x: v.x/len, y: v.y/len, z: v.z/len };
        }


        na.m.waitForCondition('ForceGraph3D JS loaded?', function () {
            if (typeof ForceGraph3D === 'undefined') {
                console.error('ForceGraph3D not loaded yet — check script load order');
                return false;
            }
            return true;
        }, function () {

            t.graph = window.graph = ForceGraph3D();
            t.graph(container);  // mount to DOM immediately

            t.graph.d3Force('charge', null);
            t.graph.d3Force('center', null);

            t.graph
            .pauseAnimation()
            .backgroundColor('rgba(0,0,0,0.22)')   // ← changed for visibility
            .width(t.el.clientWidth || 1000)
            .height(t.el.clientHeight || 700)
            //.dagLevelDistance(1000)
            .nodeId('id')           // ← tell the library which field is the ID
            .linkSource('source')
            .linkTarget('target')

            .nodeLabel(null)
            .nodeOpacity(0.55)
            .warmupTicks(0)
            .cooldownTicks(0)
            .cooldownTime(0)
            .nodeId('id')
            .linkSource('source')
            .linkTarget('target')
            .nodeLabel('data')
            .linkWidth(2)
            .linkColor(() => 'rgba(200, 200, 255, 0.4)')
            .nodeColor(n => {
                return t.getHierarchicalColor(t, (n.item?.level ?? 0)/2 + 1);
            })
            .nodeRelSize(10)           // slightly bigger nodes so they don't get lost
            .nodeRelSize(6)
            .nodeVal(n => {
                if (n._isTreeRoot) return 40;   // Tree roots: big
                if (n._isTrunk)    return 20;   // Trunk nodes: medium
                return 6;                        // Branch nodes: small
            })
            .nodeColor(n => {
                if (n.item?.level === 0) return '#ffffff';         // root
                if (n._isTreeRoot)       return '#ffdd00';         // tree roots: gold
                if (n._isTrunk)          return '#ff8800';         // trunk: orange
                const depth = (n.item?.level ?? 0) / 2 + 1;
                return t.getHierarchicalColor(t, depth);           // branches: normal
            })
            .d3AlphaDecay(0.02)        // slower cooling = more final spread
            // === Custom Nodes & Links ===
            /*
             *         .nodeThreeObjectExtend(true)
             *         .nodeThreeObject(node => {
             *        //     //debugger;
             *        //
             *
             *             const text = node.name != parseInt(node.name) ? node.name : node.data;//`${node.filepath.replace(/\/\//g, '/')}/${node.data}`;
             *             const sprite = new SpriteText(text);
             *             node.sprite = sprite;
             *             sprite.color = 'rgba(255,255,255,0.7)';
             *             sprite.textHeight = 5;
             *             sprite.fontFace = 'Arial';
             *             sprite.position.set(0, 18, 0);
             *        //
             *        //     // Optional: make sure it renders in front
             *             sprite.material.depthWrite = false;
             *             sprite.material.depthTest = false;
             *        //
             *        //     //t.graph.scene().add(sprite);
             *             return sprite;
             *
        })
        */
            .nodeThreeObjectExtend(false)
            .nodeThreeObject(null)

            // Custom Link Labels
            .linkThreeObjectExtend(false)
            .linkThreeObject(null)
            // === INTERACTIONS ===
            .onNodeHover(node => {
                t.currentHoverNode = node;

                // Remove old hover label
                if (t.hoverLabel) {
                    t.graph.scene().remove(t.hoverLabel);
                    t.hoverLabel.material.dispose();  // ← add this
                    t.hoverLabel = null;
                };

                if (!node) {
                    tooltip.style.display = 'none';
                    // Reset when hover ends
                    t.graph.nodeColor(n => {
                        var depth = n?.item?.level ?? n.item.level ?? 0;
                        depth = depth / 2 + 1;
                        return t.getHierarchicalColor(t,depth);
                    });
                } else {

                    var path = node.filepath
                    .replace(/\/0\/filesAtRoot\/folders/, "")
                    .replace(/\/folders/g,"")
                    .replace('\/filesAtRoot','')
                    .replace(/\/\//g,'/')
                    .replace(/\'/g, '\\');

                    // Fill content
                    tooltip.innerHTML = `
                    <div class="na-tooltip-icon">📁</div>
                    <div class="na-tooltip-name">${node.data}</div>
                    <div class="na-tooltip-path">${path ?? ''}</div>
                    `;

                    tooltip.style.display = 'block';

                    // Big hover label
                    //node.sprite.visibile = false;

                    //debugger;
                    /*
                     *                const text = (node.data!=parseInt(node.data)?node.data:node.name);//('.'+(node?.item?.filepath.replace(/\/\//g,'./') || '') + '/' + node.name).replace('..','.');
                     *                t.hoverLabel = new SpriteText(text);
                     *                t.hoverLabel.color = '#ffff88';
                     *                t.hoverLabel.textHeight = 5;
                     *                t.hoverLabel.fontFace = 'Arial';
                     *                t.hoverLabel.fontWeight = 'bold';
                     *                t.hoverLabel.position.set(node.x, node.y + 18, node.z);
                     *                t.hoverLabel.material.depthWrite = false;
                     *                t.hoverLabel.material.depthTest = false;
                     *                t.graph.scene().add(t.hoverLabel);
                     *                // t.graph.refresh();
                     */

                    // Get all nodes to highlight
                    const ancestors = t.getAllAncestors(node);
                    const descendants = t.getAllDescendants(t, node);
                    const highlightedNodes = new Set([...ancestors, ...descendants, node.id || node]);

                    t.graph
                    .nodeColor(n => {
                        if (n === node) return '#ffff44';                    // hovered node

                        var depth = n?.item?.level ?? n.item.level;
                        depth = depth / 2 + 1;
                        if (highlightedNodes.has(n.id)) { return t.getHierarchicalColor(t,depth); };
                        return 'rgba(150,150,150,0.5)';

                    })
                    .linkColor(link => {
                        const defaultColor = 'rgba(200,200,255,0.4)'
                        const hovered = t.currentHoverNode;
                        if (!hovered) return defaultColor;

                        const hoveredAncestors = t.getAllAncestors(hovered);
                        const sourceAncestors = t.getAllAncestors(link.source);
                        if (sourceAncestors.size===0) return defaultColor;
                        var sourceDepth = t.items[Array.from(sourceAncestors)[sourceAncestors.size-1]].level ?? 0;
                        sourceDepth = Math.round(sourceDepth / 2) + 1;

                        const targetAncestors = t.getAllAncestors(link.target);
                        if (targetAncestors.size===0) return defaultColor;
                        var targetDepth = t.items[Array.from(targetAncestors)[targetAncestors.size-1]].level ?? 0;
                        targetDepth = Math.round(targetDepth / 2) + 1;

                        //const sourceDescendants = t.getAllDescendants(t, link.source);
                        //const targetDescendants = t.getAllDescendants(t, link.target);

                        const sourceId = typeof link.source === 'object' ? link.source.id : link.source;
                        const targetId = typeof link.target === 'object' ? link.target.id : link.target;


                        const isAncestorLink =  ancestors.has(sourceId) && ancestors.has(targetId);
                        const isDescendantLink = descendants.has(sourceId) && descendants.has(targetId);


                        if (ancestors.has(sourceId) && ancestors.has(targetId)) {
                            return t.getHierarchicalColor(t,sourceDepth);
                        }
                        // Direct children
                        if (isDescendantLink) {
                            return t.getHierarchicalColor(t,targetDepth);
                        }

                        return defaultColor;

                    })
                    .linkWidth(link => {
                        const hovered = t.currentHoverNode;
                        if (!hovered) return 1;

                        const sourceId = link.source?.id ?? link.source;
                        const targetId = link.target?.id ?? link.target;
                        const hoverId  = hovered.id ?? hovered;
                        const allRelated = new Set([...ancestors, ...descendants, hoverId]);

                        if (allRelated.has(sourceId) && allRelated.has(targetId)) {
                            return 4;
                        } else {
                            return 1;
                        }
                    })
                }
            })

            .onNodeClick(node => {
                if (!node) return;
                var t = na.threed;
                t.currentNode = node;

                t.pushHistory(t, {
                    timestamp: Date.now(),

                              // Navigation
                              scrollY: window.scrollY,
                              activeItemId: document.querySelector('.active')?.id || null,

                              // Camera (customize these properties to match your viewer)
                              camera: window.currentCamera ? {
                                  x: window.currentCamera.x ?? 0,
                                  y: window.currentCamera.y ?? 0,
                                  z: window.currentCamera.z ?? 0,
                                  zoom: window.currentCamera.zoom ?? 1,
                                  rotation: window.currentCamera.rotation
                                  ? {
                                      x: window.currentCamera.rotation.x,
                                      y: window.currentCamera.rotation.y,
                                      z: window.currentCamera.rotation.z,
                                      order: window.currentCamera.rotation.order
                                  }
                                  : null,
                                  pos : window.threed.graph.cameraPosition()
                              } : null
                }, `Before node clicked : ${node.data}`)

                const distance = 180;
                const nodeDistance = Math.hypot(node.x || 0, node.y || 0, node.z || 0);

                // If node is at/near origin, approach from current camera direction
                if (nodeDistance < 1) {
                    t.graph.cameraPosition(
                        { x: distance, y: distance, z: distance },
                        { x: 0, y: 0, z: 0 },
                        1600
                    );
                } else {
                    const distRatio = 1 + distance / nodeDistance;
                    t.graph.cameraPosition(
                        {
                            x: (node.x || 0) * distRatio,
                                           y: (node.y || 0) * distRatio,
                                           z: (node.z || 0) * distRatio
                        },
                        node,
                        1600
                    );
                }

                setTimeout (function(node) {
                    t.pushHistory(t, {
                        timestamp: Date.now(),

                                  // Navigation
                                  scrollY: window.scrollY,
                                  activeItemId: document.querySelector('.active')?.id || null,

                                  // Camera (customize these properties to match your viewer)
                                  camera: window.currentCamera ? {
                                      x: window.currentCamera.x ?? 0,
                                      y: window.currentCamera.y ?? 0,
                                      z: window.currentCamera.z ?? 0,
                                      zoom: window.currentCamera.zoom ?? 1,
                                      rotation: window.currentCamera.rotation
                                      ? {
                                          x: window.currentCamera.rotation.x,
                                          y: window.currentCamera.rotation.y,
                                          z: window.currentCamera.rotation.z,
                                          order: window.currentCamera.rotation.order
                                      }
                                      : null,
                                      pos : window.threed.graph.cameraPosition()
                                  } : null
                    }, `After node clicked : ${node.data}`)
                }, 1700, node);
                if (typeof t.onclick_node === 'function') t.onclick_node(t, node);
            })
            .nodeLabel('')           // disable built-in tooltip entirely

            .graphData({ nodes: data.nodes, links: validLinks })

            .numDimensions(3);

            if (vm !== 'checkbox_bushView' && vm !== 'checkbox_treeView') {
                t.graph.dagMode('radialout');
            } else {
                t.graph.dagMode(null);    // ← critical: let your fx/fy/fz positions win
                t.graph.d3Force('link', null);
            }

            // Move tooltip on mousemove — offset ABOVE the cursor
            const tooltip = document.createElement('div');
            tooltip.className = 'na-node-tooltip';
            tooltip.style.display = 'none';
            document.body.appendChild(tooltip);

            document.addEventListener('mousemove', e => {
                if (tooltip.style.display === 'none') return;
                const w = tooltip.offsetWidth;
                const h = tooltip.offsetHeight;
                // Position above and horizontally centered on cursor
                tooltip.style.left = (e.clientX - w / 2) + 'px';
                tooltip.style.top  = (e.clientY - h - 24) + 'px';  // 24px above cursor tip
            });

            // Access the underlying d3 simulation and stop it cold after 1 tick
            const sim = t.graph.d3Force('charge')?.strength ? t.graph : null;
            t.graph.d3Force('charge', null);
            t.graph.d3Force('center', null);
            //t.graph.d3ReheatSimulation();

            window.currentCamera = t.graph.camera();
            t.setupFlightControls(t);

            console.time('timed 5_resumeAnimation');
            t.graph.resumeAnimation();
            console.timeEnd('timed 5_resumeAnimation');

            console.log('stats : dagMode:', t.graph.dagMode());
            console.log('stats : charge force:', t.graph.d3Force('charge'));

            setTimeout(() => {
                t.graph.zoomToFit(1000, 50);  // 1000ms transition, 50px padding
            }, 500);

        }, 200);
    }

    positionBushTree(t, levels, nodeMap, childMap, parentMap, goldenAngle) {

        // ── Tuning ────────────────────────────────────────────────────────────────
        const BUSH_RADIUS      = 4000;  // much further apart — was 1800
        const TREE_RING_RADIUS =  600;  // wider ring — was 320
        const TRUNK_STEP_Y     =  300;  // taller trunk steps — was 180
        const TRUNK_LEAN       = 0.12;  // slightly more lean — was 0.08
        const BRANCH_LENGTH    =  250;  // longer branches — was 120
        const BRANCH_SPREAD    =  2.2;  // wider fan — was 1.8
        const BRANCH_DROOP_Y   = -0.08; // less droop — was -0.15
        // ─────────────────────────────────────────────────────────────────────────

        function cross3(a, b) {
            return { x: a.y*b.z - a.z*b.y, y: a.z*b.x - a.x*b.z, z: a.x*b.y - a.y*b.x };
        }
        function normalize3(v) {
            const len = Math.sqrt(v.x*v.x + v.y*v.y + v.z*v.z);
            return len < 0.0001 ? { x: 0, y: 1, z: 0 } : { x: v.x/len, y: v.y/len, z: v.z/len };
        }

        // Root at origin
        const rootId  = levels[0]?.[0];
        const rootNode = nodeMap.get(rootId);
        if (rootNode) { rootNode.fx = rootNode.x = 0; rootNode.fy = rootNode.y = 0; rootNode.fz = rootNode.z = 0; }

        // ── Step 1 : Bush nodes (level-1, e.g. /music) on a Fibonacci sphere ─────
        // The Bush NODE IS the bush — its position is the bush center.
        const bushIds  = levels[1] ?? [];
        const bushCount = bushIds.length;

        bushIds.forEach((bushId, i) => {
            const phi   = Math.acos(1 - 2 * (i + 0.5) / Math.max(bushCount, 1));
            const theta = goldenAngle * i;

            const bx = BUSH_RADIUS * Math.sin(phi) * Math.cos(theta);
            const by = BUSH_RADIUS * Math.sin(phi) * Math.sin(theta);
            const bz = BUSH_RADIUS * Math.cos(phi);

            const bushNode = nodeMap.get(bushId);
            if (bushNode) { bushNode.fx = bushNode.x = bx; bushNode.fy = bushNode.y = by; bushNode.fz = bushNode.z = bz; }

            // ── Step 2 : Tree nodes (level-2, e.g. /music/Artists) ───────────────
            // Placed in a ring around the Bush node, in the plane perpendicular
            // to the radial direction from origin → Bush.
            const bushDir = normalize3({ x: bx, y: by, z: bz });
            const right3  = normalize3(cross3(
                Math.abs(bushDir.y) < 0.9 ? { x: 0, y: 1, z: 0 } : { x: 1, y: 0, z: 0 },
                                              bushDir
            ));
            const fwd3    = normalize3(cross3(bushDir, right3));

            const treeIds  = childMap.get(bushId) ?? [];
            const treeCount = treeIds.length;

            treeIds.forEach((treeId, ti) => {
                const angle = (2 * Math.PI * ti) / Math.max(treeCount, 1);

                const tx = bx + TREE_RING_RADIUS * (Math.cos(angle) * right3.x + Math.sin(angle) * fwd3.x);
                const ty = by + TREE_RING_RADIUS * (Math.cos(angle) * right3.y + Math.sin(angle) * fwd3.y);
                const tz = bz + TREE_RING_RADIUS * (Math.cos(angle) * right3.z + Math.sin(angle) * fwd3.z);

                const treeNode = nodeMap.get(treeId);
                if (treeNode) {
                    treeNode.fx = treeNode.x = tx;
                    treeNode.fy = treeNode.y = ty;
                    treeNode.fz = treeNode.z = tz;
                    treeNode._isTrunk = true;
                    treeNode._isTreeRoot = true;
                }

                // Lean direction = away from Bush center, in the ring plane
                const leanLen = Math.sqrt((tx-bx)**2 + (tz-bz)**2) || 1;
                const leanDirX = (tx - bx) / leanLen;
                const leanDirZ = (tz - bz) / leanLen;

                // ── Step 3 : Trunk + Branches grow from each Tree node upward ────
                t.positionTrunk(
                    t, treeId,
                    { x: tx, y: ty, z: tz },
                    leanDirX, leanDirZ,
                    bushDir,
                    t.nodeMap, t.childMap,
                    TRUNK_STEP_Y, TRUNK_LEAN,
                    BRANCH_LENGTH, BRANCH_SPREAD, BRANCH_DROOP_Y
                );            });
        });
    }
    positionTrunk(t, trunkRootId, origin, leanDirX, leanDirZ,
                  outDir,                // ← new: normalized direction away from scene center
                  nodeMap, childMap,
                  TRUNK_STEP_Y, TRUNK_LEAN,
                  BRANCH_LENGTH, BRANCH_SPREAD, BRANCH_DROOP_Y) {

        // Two axes perpendicular to outDir — used for branch fanning
        function cross3(a, b) {
            return { x: a.y*b.z - a.z*b.y, y: a.z*b.x - a.x*b.z, z: a.x*b.y - a.y*b.x };
        }
        function normalize3(v) {
            const len = Math.sqrt(v.x*v.x + v.y*v.y + v.z*v.z);
            return len < 0.0001 ? { x: 0, y: 1, z: 0 } : { x: v.x/len, y: v.y/len, z: v.z/len };
        }

        // Build two axes perpendicular to outDir for the branch fan plane
        const branchRight = normalize3(cross3(
            Math.abs(outDir.y) < 0.9 ? { x: 0, y: 1, z: 0 } : { x: 1, y: 0, z: 0 },
                                              outDir
        ));
        const branchUp = normalize3(cross3(outDir, branchRight));

        let currentId = trunkRootId;

        while (true) {
            const children = (childMap.get(currentId) ?? []).slice();
            if (children.length === 0) break;

            // Trunk spine = child with most descendants
            children.sort((a, b) => {
                const aCount = (childMap.get(a) ?? []).length;
                const bCount = (childMap.get(b) ?? []).length;
                return bCount - aCount ||
                (nodeMap.get(a)?.name ?? '').localeCompare(nodeMap.get(b)?.name ?? '');
            });

            const nextTrunkId = children[0];
            const trunkNode   = nodeMap.get(nextTrunkId);
            if (!trunkNode) break;

            const parentNode = nodeMap.get(currentId);
            const px = parentNode?.fx ?? parentNode?.x ?? origin.x;
            const py = parentNode?.fy ?? parentNode?.y ?? origin.y;
            const pz = parentNode?.fz ?? parentNode?.z ?? origin.z;

            // Step outward along outDir (+ tiny lean in branchRight for visual interest)
            trunkNode.fx = trunkNode.x = px + outDir.x * TRUNK_STEP_Y + branchRight.x * TRUNK_LEAN * TRUNK_STEP_Y;
            trunkNode.fy = trunkNode.y = py + outDir.y * TRUNK_STEP_Y + branchRight.y * TRUNK_LEAN * TRUNK_STEP_Y;
            trunkNode.fz = trunkNode.z = pz + outDir.z * TRUNK_STEP_Y + branchRight.z * TRUNK_LEAN * TRUNK_STEP_Y;
            trunkNode._isTrunk = true;

            // Branches fan out in the branchRight/branchUp plane (perpendicular to trunk)
            const branchChildren = children.slice(1);
            const bCount = branchChildren.length;

            if (bCount > 0) {
                branchChildren.forEach((branchId, bi) => {
                    const branchNode = nodeMap.get(branchId);
                    if (!branchNode) return;

                    const fanFraction = bCount === 1 ? 0 : (bi / (bCount - 1)) - 0.5;
                    const angle       = fanFraction * BRANCH_SPREAD;
                    const branchScale = 1 + Math.abs(fanFraction) * 0.4;

                    // Fan in the plane perpendicular to outDir
                    const fanX = Math.cos(angle) * branchRight.x + Math.sin(angle) * branchUp.x;
                    const fanY = Math.cos(angle) * branchRight.y + Math.sin(angle) * branchUp.y;
                    const fanZ = Math.cos(angle) * branchRight.z + Math.sin(angle) * branchUp.z;

                    // Add a slight backward droop (toward origin) instead of Y droop
                    branchNode.fx = branchNode.x = trunkNode.fx + fanX * BRANCH_LENGTH * branchScale + outDir.x * BRANCH_DROOP_Y * BRANCH_LENGTH;
                    branchNode.fy = branchNode.y = trunkNode.fy + fanY * BRANCH_LENGTH * branchScale + outDir.y * BRANCH_DROOP_Y * BRANCH_LENGTH;
                    branchNode.fz = branchNode.z = trunkNode.fz + fanZ * BRANCH_LENGTH * branchScale + outDir.z * BRANCH_DROOP_Y * BRANCH_LENGTH;

                    t.positionBranchChildren(
                        t, branchId, angle, outDir, branchRight, branchUp,
                        { x: branchNode.fx, y: branchNode.fy, z: branchNode.fz },
                        nodeMap, childMap,
                        BRANCH_LENGTH * 0.65, BRANCH_SPREAD * 0.7, BRANCH_DROOP_Y * 1.3,
                        0
                    );
                });
            }

            currentId = nextTrunkId;
        }
                  }
                  positionBranchChildren(t, parentId, parentAngle, outDir, branchRight, branchUp,
                                         parentPos, nodeMap, childMap,
                                         branchLen, branchSpread, droopY, depth) {
                      if (depth > 4) return;

                      const children = (childMap.get(parentId) ?? []).slice().sort((a, b) =>
                      (nodeMap.get(a)?.name ?? '').localeCompare(nodeMap.get(b)?.name ?? '')
                      );
                      if (children.length === 0) return;

                      children.forEach((childId, ci) => {
                          const childNode = nodeMap.get(childId);
                          if (!childNode) return;

                          const fanFraction = children.length === 1 ? 0 : (ci / (children.length - 1)) - 0.5;
                          const angle       = parentAngle + fanFraction * branchSpread;
                          const scale       = 1 + Math.abs(fanFraction) * 0.35;

                          const fanX = Math.cos(angle) * branchRight.x + Math.sin(angle) * branchUp.x;
                          const fanY = Math.cos(angle) * branchRight.y + Math.sin(angle) * branchUp.y;
                          const fanZ = Math.cos(angle) * branchRight.z + Math.sin(angle) * branchUp.z;

                          childNode.fx = childNode.x = parentPos.x + fanX * branchLen * scale + outDir.x * droopY * branchLen;
                          childNode.fy = childNode.y = parentPos.y + fanY * branchLen * scale + outDir.y * droopY * branchLen;
                          childNode.fz = childNode.z = parentPos.z + fanZ * branchLen * scale + outDir.z * droopY * branchLen;

                          t.positionBranchChildren(
                              t, childId, angle, outDir, branchRight, branchUp,
                              { x: childNode.fx, y: childNode.fy, z: childNode.fz },
                              nodeMap, childMap,
                              branchLen * 0.65, branchSpread * 0.75, droopY * 1.2,
                              depth + 1
                          );
                      });
                                         }

    setupFlightControls(t) {
        const container = t.el;
        let flyInterval = null;
        let mouseButton = null;
        let holdTimer = null;
        const HOLD_THRESHOLD = 2000;  // ms before long-click activates
        const FLY_SPEED = 25;        // units per tick — tune this

        const getForwardVector = window.getForwardVector = () => {
            const camera = window.currentCamera = t.graph.camera();
            const dir = new THREE.Vector3();
            camera.getWorldDirection(dir);  // normalised forward direction
            return dir;
        };

        const startFlying = (direction) => {
            if (flyInterval) return;
            flyInterval = setInterval(() => {
                na.threeD.autoRotate = !na.threeD.autoRotate; na.threeD.applyAutoRotate(na.threeD)
                const camera = t.graph.camera();
                const forward = getForwardVector();
                const delta = forward.multiplyScalar(FLY_SPEED * direction);
                const pos = t.graph.cameraPosition();

                // Move both camera AND its look target together
                // so OrbitControls doesn't snap back
                t.graph.cameraPosition(
                    { x: pos.x + delta.x, y: pos.y + delta.y, z: pos.z + delta.z },
                    { x: (pos?.lookAt?.x ?? 0) + delta.x, y: (pos?.lookAt?.y ?? 0) + delta.y, z: (pos?.lookAt?.z ?? 0) + delta.z }
                    // no duration = instant
                );
            }, 20);
        };

        const stopFlying = () => {
            if (flyInterval) {
                clearInterval(flyInterval);
                flyInterval = null;
            }
            if (holdTimer) {
                clearTimeout(holdTimer);
                holdTimer = null;
            }
            na.threeD.autoRotate = !na.threeD.autoRotate; na.threeD.applyAutoRotate(na.threeD);
            mouseButton = null;
        };

        container.addEventListener('mousedown', (e) => {
            // Ignore if over a node (ForceGraph handles that)
            if (e.target !== container.querySelector('canvas')) return;

            if (e.button === 0) {
                // Left button — wait for long press
                mouseButton = 0;
                holdTimer = setTimeout(() => {
                    if (mouseButton === 0) startFlying(1);  // forward
                }, HOLD_THRESHOLD);
            } else if (e.button === 2) {
                // Right button — immediate backward (or also long-press if preferred)
                e.preventDefault();
                mouseButton = 2;
                holdTimer = setTimeout(() => {
                    if (mouseButton === 2) startFlying(-1);  // backward
                }, HOLD_THRESHOLD);
            }
        });

        container.addEventListener('mouseup', stopFlying);
        container.addEventListener('mouseleave', stopFlying);

        // Prevent context menu on right click
        container.addEventListener('contextmenu', (e) => {
            e.preventDefault();
        });

        // Optional: scroll wheel also flies (smoother than zoom)
        container.addEventListener('wheel', (e) => {
            e.preventDefault();
            const direction = e.deltaY < 0 ? 1 : -1;
            const forward = getForwardVector();
            const speed = Math.abs(e.deltaY) * 1.5;
            const delta = forward.multiplyScalar(speed * direction);
            const pos = t.graph.cameraPosition();
            t.graph.cameraPosition(
                { x: pos.x + delta.x, y: pos.y + delta.y, z: pos.z + delta.z },
                { x: (pos?.lookAt?.x ?? 0) + delta.x, y: (pos?.lookAt?.y ?? 0) + delta.y, z: (pos?.lookAt?.z ?? 0) + delta.z }
            );
        }, { passive: false });


        // Alongside mousedown:
        container.addEventListener('touchstart', (e) => {
            const touch = e.touches[0];
            // treat as equivalent to mousedown with button 0
            mouseButton = 0;
            holdTimer = setTimeout(() => startFlying(1), HOLD_THRESHOLD);
        }, { passive: true });

        container.addEventListener('touchend', stopFlying);
        container.addEventListener('touchmove', (e) => {
            // map touch delta to camera pan
        }, { passive: true });


        // After graph is created, unlock the internal OrbitControls
        const internalControls = t.graph.controls();
        if (internalControls) {
            internalControls.minDistance = 0;       // can fly right through
            internalControls.maxDistance = Infinity; // no outer wall
            internalControls.minPolarAngle = 0;      // no vertical clamping
            internalControls.maxPolarAngle = Math.PI;
            internalControls.enablePan = true;
            internalControls.screenSpacePanning = true;
        }

        // Also push the camera far plane out so distant nodes don't clip
        const camera = t.graph.camera();
        if (camera) {
            camera.far = 10000000;
            camera.near = 0.1;
            camera.updateProjectionMatrix();
        }

        console.log('Flight controls ready — long left-click: fly forward, long right-click: fly backward, scroll: fly in/out');
    }


    itemsToGraphData(t) {
        const nodes = [];
        var links = [];

        const seen = new Set();
        const visibleItems = t.items.filter(it => {
            // Always exclude mp3 files unless showFiles is on

            if (!t.showFiles) {
                const nonFolder = (
                    it.name.match(/\.gif$/i)
                    || it.name.match(/\.jpg$/i)
                    || it.name.match(/\.png$/i)
                    || it.name.match(/\.mp3$/i)
                    || it.name.match(/\.flac$/i)
                    || it.name.match(/\.wav$/i)
                    || it.name.match(/\.wma$/i)
                )
                if (typeof it.name === 'string' && nonFolder) return false;
                if (typeof it.data === 'string' && nonFolder) return false;
            }

            if (seen.has(it.idx)) return false;  // hard dedup

            const r = t.showFiles || it.idx === 0 || it.idx === 1 || (
                (typeof it.data === 'string' && it.data.indexOf('.') === -1 && it.data != parseInt(it.data))
                || (
                    (typeof it.name === 'string' && it.name.indexOf('.') === -1 && it.name != parseInt(it.name))
                    && it.data && (it.data.files || it.data.folders)
                )
            );

            if (r) seen.add(it.idx);
            return r;
        });

        // Build a Set of visible idx values so we can check parent validity
        const visibleIdxSet = new Set(visibleItems.map(it => it.idx));

        visibleItems.forEach(item => {
            nodes.push({
                id: item.idx,
                name: item.name,
                data: item.data && typeof item.data === 'string' ? item.data : item.name,
                type: 'folder',
                item: item,
                level: item.level,
                filepath: item.filepath,
                idxPath: item.idxPath,
                idx: item.idx,
            });

            // Only create link if parent is also visible
            /*if (item.parent && item.parent.idx !== undefined && visibleIdxSet.has(item.parent.idx)) {
             *                links.push({
             *                    source: item.parent.idx,
             *                    target: item.idx
        });
        }
        // If parent is NOT visible, walk up until we find one that is
        else if (item.parent && item.parent.idx !== undefined && !visibleIdxSet.has(item.parent.idx)) {
            let ancestor = item.parent.parent;
            while (ancestor) {
                if (visibleIdxSet.has(ancestor.idx)) {
                    links.push({ source: ancestor.idx, target: item.idx });
        break;
        }
        ancestor = ancestor.parent;
        }
        }*/

            if (item.parent?.idx !== undefined) {
                // Walk up until we hit a visible ancestor
                let p = item.parent;
                while (p && !visibleIdxSet.has(p.idx)) {
                    p = p.parent;
                }
                if (p && visibleIdxSet.has(p.idx)) {
                    links.push({ source: p.idx, target: item.idx });
                }
                // If no visible ancestor found, node becomes a root — no link added
            }
        });





        // After visibleItems is built:
        console.log('Total t.items:', t.items.length);
        console.log('Visible items:', visibleItems.length);

        // Check for duplicate idx in visibleItems
        const idxCount = new Map();
        visibleItems.forEach(it => {
            idxCount.set(it.idx, (idxCount.get(it.idx) || 0) + 1);
        });
        const dupeIdx = [...idxCount.entries()].filter(([,v]) => v > 1);
        console.log('Duplicate idx in visibleItems:', dupeIdx);

        // Check for duplicate names
        const nameCount = new Map();
        visibleItems.forEach(it => {
            const key = (it.filepath || '') + '/' + (it.name || it.data);
            nameCount.set(key, (nameCount.get(key) || 0) + 1);
        });
        const dupeNames = [...nameCount.entries()].filter(([,v]) => v > 1);
        console.log('Duplicate paths in visibleItems:', dupeNames.slice(0, 20));

        // Also check t.items itself
        const rawIdxCount = new Map();
        t.items.forEach(it => rawIdxCount.set(it.idx, (rawIdxCount.get(it.idx) || 0) + 1));
        const rawDupes = [...rawIdxCount.entries()].filter(([,v]) => v > 1);
        console.log('Duplicate idx in raw t.items:', rawDupes);



        const nodeIdSet = new Set(nodes.map(n => n.id));
        // Final safety net
        links = links.filter(l => visibleIdxSet.has(l.source) && visibleIdxSet.has(l.target));

        return { nodes, links: links };

        //return { nodes, links };
    }

    toggleShowLines () {
        var t = this;
        t.showLines = !t.showLines;
        if (t.showLines) {
            t.drawLines(t);
            $("#showLines").removeClass("vividButton").addClass("vividButtonSelected");
        } else {
            for (var i=0; i<t.permaLines.length; i++) {
                var l = t.permaLines[i];
                t.scene.remove (l.line);
                l.geometry.dispose();
                l.material.dispose();
            }
            t.permaLines = [];
            $("#showLines").removeClass("vividButtonSelected").addClass("vividButton");
        }
    }

}



export class na3D_demo_models {
    constructor(el, parent, data) {
        var t = this;
        t.p = parent;
        t.el = el;
        t.t = $(t.el).attr("theme");

        t.data = data;

        t.lights = [];
        t.folders = [];

        t.items = [];


        t.scene = new THREE.Scene();
        t.graph.camera = new THREE.PerspectiveCamera( 75, $(el).width() / $(el).height(), 0.1, 1000 );


        t.renderer = new THREE.WebGLRenderer({alpha:true, antialias : true});
        t.renderer.physicallyCorrectLights = true;
        t.renderer.outputEncoding = sRGBEncoding;
        t.renderer.setPixelRatio (window.devicePixelRatio);
        t.renderer.setSize( $(el).width()-20, $(el).height()-20 );

        t.renderer.toneMappingExposure = 1.0;

        el.appendChild( t.renderer.domElement );

        t.controls = new OrbitControls( t.graph.camera, t.renderer.domElement );
        //t.graph.controls().listenToKeyEvents( window ); // optional

        t.loader = new GLTFLoader();

        t.loader.load( "/NicerAppWebOS/3rd-party/3D/models/human armor/scene.gltf", function ( gltf ) {
            gltf.scene.position.x = -150;
            gltf.scene.scale.setScalar (10);
            t.cube = gltf.scene;
            t.scene.add (t.cube);

            t.updateTextureEncoding(t, t.cube);
        }, function ( xhr ) {
            console.log( "model 'human armor' : " + ( xhr.loaded / xhr.total * 100 ) + "% loaded" );
        }, function ( error ) {
            console.error( error );
        } );
        t.loader.load( "/NicerAppWebOS/3rd-party/3D/models/photoCamera/scene.gltf", function ( gltf ) {
            gltf.scene.position.x = 200;
            t.cube2 = gltf.scene;
            t.scene.add (t.cube2);

            t.updateTextureEncoding(t, t.cube2);

        }, function ( xhr ) {
            console.log( "model 'photoCamera' : " + ( xhr.loaded / xhr.total * 100 ) + "% loaded" );
        }, function ( error ) {
            console.error( error );
        } );

        const light1  = new AmbientLight(0xFFFFFF, 0.3);
        light1.name = "ambient_light";
        light1.intensity = 0.3;
        light1.color = 0xFFFFFF;
        t.graph.camera().add( t.light1 );
        t.graph.camera().add( t.light2 );

        const light2  = new DirectionalLight(0xFFFFFF, 0.8 * Math.PI);
        light2.position.set(0.5, 0, 0.866); // ~60º
        light2.name = "main_light";
        light2.intensity = 0.8 * Math.PI;
        light2.color = 0xFFFFFF;
        //t.graph.camera().add( light2 );

        t.lights.push(light1, light2);

        t.pmremGenerator = new PMREMGenerator( t.renderer );
        t.pmremGenerator.compileEquirectangularShader();

        t.updateEnvironment(this);

        el.addEventListener("mousemove", function() { t.onMouseMove (event, t) });
        el.addEventListener("pointerup", function() { t.onPointerUp (event, t) });

        t.raycaster = new THREE.Raycaster();
        t.mouse = new THREE.Vector2();
        t.mouse.x = 0;
        t.mouse.y = 0;

        //t.animate(this);
    }

    /*
     *    animate(t) {
     *        requestAnimationFrame( function() { t.st) } );
     *
     *        t.raycaster.setFromCamera (t.mouse, t.graph.camera);
     *
     *        const intersects = t.raycaster.intersectObjects (t.scene.children, true);
     *        if (intersects[0] && t.cube && t.cube2) {
     *            t.cube.rotation.x += 0.015;
     *            t.cube.rotation.y += 0.02;
     *            t.cube2.rotation.x += 0.015;
     *            t.cube2.rotation.y += 0.02;
     *            //t.cube2.rotation.y += 0.02;
}

t.renderer.render( t.scene, t.graph.camera );
}*/


    updateTextureEncoding (t, content) {
        /*const encoding = t.state.textureEncoding === "sRGB"
         *        ? sRGBEncoding
         *        : LinearEncoding;*/
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
            id: "venice-sunset",
            name: "Venice Sunset",
            path: "/NicerAppWebOS/3rd-party/3D/assets/environment/venice_sunset_1k.hdr",
            format: ".hdr"
        };
        /*
         *        const environment = {
         *            id: "footprint-court",
         *            name: "Footprint Court (HDR Labs)",
         *            path: "/NicerAppWebOS/3rd-party/3D/assets/environment/footprint_court_2k.hdr",
         *            format: ".hdr"
    }*/

        t.getCubeMapTexture( environment ).then(( { envMap } ) => {

            /*
             *            if (!envMap || !t.state.background) && t.activeCamera === t.defaultCamera) {
             *                t.scene.add(t.vignette);
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


    onMouseMove(event, t) {
        if (!t.renderer || !t.renderer.domElement) return;

        const rect = t.renderer.domElement.getBoundingClientRect();

        // Correct normalized coordinates (this was sometimes broken)
        t.mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        t.mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

        t.mouse.layerX = event.layerX;
        t.mouse.layerY = event.layerY;
        t.mouse.clientX = event.clientX;
        t.mouse.clientY = event.clientY;
        t.mouse.event = event;
        t.evt = event;

        // THIS IS THE CRITICAL LINE that was missing after refactor
        t.graph.camera().updateMatrixWorld(true);
        t.raycaster.setFromCamera(t.mouse, t.graph.camera);
        const intersects = t.raycaster.intersectObjects(t.s2);

        console.log('Intersects:', intersects.length, intersects[0] ? intersects[0].object.it?.name : null);
    }

    onMouseWheel( event, t ) {
        debugger;
    }
}

// Keyboard handler
document.addEventListener('keydown', (e) => {
    if (e.ctrlKey && e.key === 'z') {
        e.preventDefault();
        if (e.shiftKey) {
            na.apps.loaded.threed_fileExplorer.redo();
        } else {
            na.apps.loaded.threed_fileExplorer.undo();
        }
    }
});





export class na3D_demo_cube {
    constructor(el,parent) {
        t.p = parent;
        t.el = el;
        t.t = $(t.el).attr("theme");

        t.scene = new THREE.Scene();
        t.graph.camera = new THREE.PerspectiveCamera( 75, $(el).width() / $(el).height(), 0.1, 1000 );

        t.renderer = new THREE.WebGLRenderer({ alpha : true });
        t.renderer.setSize( $(el).width()-20, $(el).height()-20 );
        el.appendChild( t.renderer.domElement );

        const geometry = new THREE.BoxGeometry();
        const material = new THREE.MeshBasicMaterial( { color: 0x00ff00 } );
        var materials = [
            new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load("/siteMedia/backgrounds/tiled/blue/4a065201509c0fc50e7341ce04cf7902--twitter-backgrounds-blue-backgrounds.jpg")
            }),
            new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load("/siteMedia/backgrounds/tiled/blue/blue170.jpg")
            }),
            new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load("/siteMedia/backgrounds/tiled/blue/abstract_water_texture-seamless.jpg")
            }),
            new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load("/siteMedia/backgrounds/tiled/orange/467781133_4f4354223e.jpg")
            }),
            new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load("/siteMedia/backgrounds/tiled/green/dgren051.jpg")
            }),
            new THREE.MeshBasicMaterial({
                map: new THREE.TextureLoader().load("/siteMedia/backgrounds/tiled/green/leaves007.jpg")
            })
        ];
        t.cube = new THREE.Mesh( new THREE.BoxGeometry( 1, 1, 1 ), materials );
        t.scene.add( t.cube );
        var t = this;
        $(el).bind("mousemove", function() { t.onMouseMove (event, t) });

        t.raycaster = new THREE.Raycaster();
        t.mouse = new THREE.Vector2();

        t.graph.camera().position.z = 5;
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
        t.raycaster.setFromCamera(t.mouse, t.graph.camera);
    }


    /*
     *    animate(t) {
     *        requestAnimationFrame( function() { t.animate (t) } );
     *        //t.cube.rotation.x += 0.02;
     *        //t.cube.rotation.y += 0.02;
     *        t.raycaster.setFromCamera (t.mouse, t.graph.camera);
     *        const intersects = t.raycaster.intersectObjects (t.scene.children, true);
     *        for (var i=0; i<intersects.length; i++) {
     *            intersects[i].object.rotation.x += 0.02;
     *            intersects[i].object.rotation.y += 0.02;
}
t.renderer.render( t.scene, t.graph.camera );
}*/
}
