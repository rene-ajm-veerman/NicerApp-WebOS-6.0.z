<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>NicerApp WebOS – 3D Desktop v1.2.0</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { overflow: hidden; background: #05050a; font-family: system-ui, -apple-system, sans-serif; color: #e0e0e0; }

#ui {
position: absolute; top: 16px; left: 16px; z-index: 20;
background: rgba(12, 14, 24, 0.88); backdrop-filter: blur(12px);
border: 1px solid rgba(255,255,255,0.09); border-radius: 14px;
padding: 14px 18px; min-width: 260px;
}
#ui h3 { font-size: 14px; margin-bottom: 10px; color: #8ab4f8; font-weight: 600; }
#ui label { font-size: 12px; display: block; margin: 8px 0 4px; color: #aaa; }
select, button {
    width: 100%; padding: 7px 10px; border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.12);
    background: #1a1d2e; color: #eee; font-size: 13px; cursor: pointer;
}
button { margin-top: 10px; background: #2a3a5c; }
button:hover { background: #3a4f7a; }
#info {
position: absolute; bottom: 18px; left: 18px; z-index: 20;
font-size: 12px; color: #888; background: rgba(0,0,0,0.45);
padding: 6px 12px; border-radius: 8px;
}
</style>
</head>
<body>

<div id="ui">
<h3>NicerApp 3D Desktop <small style="opacity:0.6">v1.2.0</small></h3>
<label>Skybox / Environment</label>
<select id="skySelect">
<option value="night">Deep Night</option>
<option value="dusk">Dusk Horizon</option>
<option value="aurora">Aurora</option>
<option value="space">Space</option>
<option value="studio">Soft Studio</option>
</select>
<button onclick="na.desktop3D.randomizeIconPositions()">Shuffle Icons</button>
</div>

<div id="info">Hover & click the icons • Drag to orbit • Scroll to zoom</div>

<script type="importmap">
{
    "imports": {
        "three": "https://unpkg.com/three@0.170.0/build/three.module.js",
        "three/addons/": "https://unpkg.com/three@0.170.0/examples/jsm/"
    }
}
</script>

<script type="module">
import * as THREE from 'three';
import { CSS3DRenderer, CSS3DObject } from 'three/addons/renderers/CSS3DRenderer.js';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { FBXLoader } from 'three/addons/loaders/FBXLoader.js';

const na = window.na = {};

na.desktop3D = {
    scene: null,
    camera: null,
    webglRenderer: null,
    css3dRenderer: null,
    controls: null,
    taskbar: null,
    loadedIcons: {},
    hoveredIcon: null,
    panels: {},
    clock: new THREE.Clock(),
    raycaster: new THREE.Raycaster(),
    mouse: new THREE.Vector2(),
    skyMesh: null,
    currentSky: 'night',

    init() {
        const w = window.innerWidth;
        const h = window.innerHeight;

        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(0x080810);

        this.camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 2000);
        this.camera.position.set(0, 3.8, 13.5);

        // WebGL
        this.webglRenderer = new THREE.WebGLRenderer({ antialias: true });
        this.webglRenderer.setSize(w, h);
        this.webglRenderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.webglRenderer.toneMapping = THREE.ACESFilmicToneMapping;
        this.webglRenderer.toneMappingExposure = 1.15;
        this.webglRenderer.domElement.style.position = 'absolute';
        this.webglRenderer.domElement.style.top = '0';
        this.webglRenderer.domElement.style.left = '0';
        this.webglRenderer.domElement.style.zIndex = '1';
        document.body.appendChild(this.webglRenderer.domElement);

        // CSS3D
        this.css3dRenderer = new CSS3DRenderer();
        this.css3dRenderer.setSize(w, h);
        this.css3dRenderer.domElement.style.position = 'absolute';
        this.css3dRenderer.domElement.style.top = '0';
        this.css3dRenderer.domElement.style.left = '0';
        this.css3dRenderer.domElement.style.zIndex = '2';
        this.css3dRenderer.domElement.style.pointerEvents = 'none';
        document.body.appendChild(this.css3dRenderer.domElement);

        // Strong lighting
        const ambient = new THREE.AmbientLight(0x8090c0, 2.2);
        this.scene.add(ambient);

        const dir = new THREE.DirectionalLight(0xffffff, 2.8);
        dir.position.set(6, 14, 9);
        this.scene.add(dir);

        const fill = new THREE.DirectionalLight(0x88aaff, 1.0);
        fill.position.set(-8, 6, -4);
        this.scene.add(fill);

        // Ground
        const ground = new THREE.Mesh(
            new THREE.CircleGeometry(22, 64),
                                      new THREE.MeshStandardMaterial({ color: 0x0c0c14, roughness: 0.85, metalness: 0.2 })
        );
        ground.rotation.x = -Math.PI / 2;
        ground.position.y = -4.7;
        this.scene.add(ground);

        // Controls
        this.controls = new OrbitControls(this.camera, this.webglRenderer.domElement);
        this.controls.enableDamping = true;
        this.controls.dampingFactor = 0.08;
        this.controls.maxPolarAngle = Math.PI * 0.48;
        this.controls.minDistance = 6;
        this.controls.maxDistance = 28;
        this.controls.target.set(0, -2.6, 0);

        this.createSkybox('night');
        this.createTaskbar();
        this.bindEvents();
        this.animate();

        // Load the real FBX as the "files" icon
        this.loadRealIcon('files', './icons/folder.fbx', 1.1);
        this.loadRealIcon('start', './icons/rocket.fbx', 1.1);

        document.getElementById('skySelect').addEventListener('change', (e) => {
            this.createSkybox(e.target.value);
        });

        window.addEventListener('resize', () => this.onResize());
    },

    // -------------------------------------------------
    // Skyboxes
    // -------------------------------------------------
    createSkybox(type = 'night') {
        if (this.skyMesh) {
            this.scene.remove(this.skyMesh);
            this.skyMesh.geometry.dispose();
            if (this.skyMesh.material.dispose) this.skyMesh.material.dispose();
        }
        if (this._stars) {
            this.scene.remove(this._stars);
            this._stars = null;
        }

        const geo = new THREE.SphereGeometry(90, 64, 32);
        let mat;

        if (type === 'night') {
            mat = new THREE.ShaderMaterial({
                side: THREE.BackSide,
                uniforms: {
                    topColor:    { value: new THREE.Color(0x05051a) },
                                           bottomColor: { value: new THREE.Color(0x0a0a18) }
                },
                vertexShader: `
                varying vec3 vWorldPosition;
                void main() {
                    vec4 worldPosition = modelMatrix * vec4(position, 1.0);
                    vWorldPosition = worldPosition.xyz;
                    gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
                }
                `,
                fragmentShader: `
                uniform vec3 topColor;
                uniform vec3 bottomColor;
                varying vec3 vWorldPosition;
                void main() {
                    float h = normalize(vWorldPosition).y;
                    gl_FragColor = vec4(mix(bottomColor, topColor, max(h * 0.7 + 0.3, 0.0)), 1.0);
                }
                `
            });
        } else if (type === 'dusk') {
            mat = new THREE.ShaderMaterial({
                side: THREE.BackSide,
                uniforms: {
                    topColor:    { value: new THREE.Color(0x1a1035) },
                                           midColor:    { value: new THREE.Color(0xc45c26) },
                                           bottomColor: { value: new THREE.Color(0x2a1840) }
                },
                vertexShader: `
                varying vec3 vWorldPosition;
                void main() {
                    vec4 worldPosition = modelMatrix * vec4(position, 1.0);
                    vWorldPosition = worldPosition.xyz;
                    gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
                }
                `,
                fragmentShader: `
                uniform vec3 topColor;
                uniform vec3 midColor;
                uniform vec3 bottomColor;
                varying vec3 vWorldPosition;
                void main() {
                    float h = normalize(vWorldPosition).y;
                    vec3 col = h > 0.05
                    ? mix(midColor, topColor, clamp((h - 0.05) * 1.4, 0.0, 1.0))
                    : mix(bottomColor, midColor, clamp(h * 12.0, 0.0, 1.0));
                    gl_FragColor = vec4(col, 1.0);
                }
                `
            });
        } else if (type === 'aurora') {
            mat = new THREE.ShaderMaterial({
                side: THREE.BackSide,
                uniforms: { time: { value: 0 } },
                vertexShader: `
                varying vec3 vWorldPosition;
                void main() {
                    vec4 worldPosition = modelMatrix * vec4(position, 1.0);
                    vWorldPosition = worldPosition.xyz;
                    gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
                }
                `,
                fragmentShader: `
                uniform float time;
                varying vec3 vWorldPosition;
                void main() {
                    vec3 dir = normalize(vWorldPosition);
                    float y = dir.y * 0.5 + 0.5;
                    vec3 col = mix(vec3(0.02,0.03,0.09), vec3(0.04,0.12,0.22), y);
                    float aurora = pow(max(sin(dir.x * 3.5 + time * 0.25) * sin(dir.z * 2.2 + time * 0.18), 0.0), 2.8);
                    col += vec3(0.08, 0.55, 0.38) * aurora * smoothstep(0.15, 0.7, y);
                    gl_FragColor = vec4(col, 1.0);
                }
                `
            });
        } else if (type === 'space') {
            mat = new THREE.MeshBasicMaterial({ side: THREE.BackSide, color: 0x03030c });
            const starsGeo = new THREE.BufferGeometry();
            const pos = [];
            for (let i = 0; i < 1800; i++) {
                const r = 75 + Math.random() * 12;
                const theta = Math.random() * Math.PI * 2;
                const phi = Math.acos(2 * Math.random() - 1);
                pos.push(
                    r * Math.sin(phi) * Math.cos(theta),
                         r * Math.sin(phi) * Math.sin(theta),
                         r * Math.cos(phi)
                );
            }
            starsGeo.setAttribute('position', new THREE.Float32BufferAttribute(pos, 3));
            this._stars = new THREE.Points(starsGeo, new THREE.PointsMaterial({ color: 0xffffff, size: 0.4 }));
            this.scene.add(this._stars);
        } else {
            mat = new THREE.ShaderMaterial({
                side: THREE.BackSide,
                uniforms: {
                    topColor:    { value: new THREE.Color(0x1e2438) },
                                           bottomColor: { value: new THREE.Color(0x0d1018) }
                },
                vertexShader: `
                varying vec3 vWorldPosition;
                void main() {
                    vec4 worldPosition = modelMatrix * vec4(position, 1.0);
                    vWorldPosition = worldPosition.xyz;
                    gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
                }
                `,
                fragmentShader: `
                uniform vec3 topColor;
                uniform vec3 bottomColor;
                varying vec3 vWorldPosition;
                void main() {
                    float h = normalize(vWorldPosition).y * 0.5 + 0.5;
                    gl_FragColor = vec4(mix(bottomColor, topColor, h), 1.0);
                }
                `
            });
        }

        this.skyMesh = new THREE.Mesh(geo, mat);
        this.scene.add(this.skyMesh);
        this.currentSky = type;
    },

    // -------------------------------------------------
    // Taskbar + placeholders
    // -------------------------------------------------
    createTaskbar() {
        this.taskbar = new THREE.Group();

        const bar = new THREE.Mesh(
            new THREE.PlaneGeometry(15.8, 1.45),
                                   new THREE.MeshStandardMaterial({
                                       color: 0x10101c,
                                       transparent: true,
                                       opacity: 0.92,
                                       roughness: 0.3,
                                       metalness: 0.4
                                   })
        );
        bar.position.set(0, -4.15, 0);
        bar.rotation.x = -0.14;
        this.taskbar.add(bar);

        const icons = [
            { id: 'start',    color: 0x00e5c7, x: -5.9 },
            { id: 'browser',  color: 0x4fc3f7, x: -3.5 },
            { id: 'files',    color: 0xffb74d, x: -1.1 },
            { id: 'settings', color: 0xce93d8, x:  1.3 },
            { id: 'theme',    color: 0x81c784, x:  3.7 }
        ];

        icons.forEach(cfg => {
            const model = this.createPlaceholderIcon(cfg.color);
            model.position.set(cfg.x, -3.95, 0.5);
            model.scale.setScalar(0.58);

            model.userData = {
                type: 'taskbarIcon',
                id: cfg.id,
                originalY: -3.95,
                originalScale: 0.58,
                originalRotation: model.rotation.clone(),
                      hoverScale: 1.32,
                      isHovered: false,
                      isClicked: false,
                      clickTime: 0
            };

            model.traverse(c => { if (c.isMesh) c.userData = model.userData; });
            this.taskbar.add(model);
            this.loadedIcons[cfg.id] = model;
        });

        this.scene.add(this.taskbar);
    },

    createPlaceholderIcon(color) {
        const g = new THREE.Group();

        const body = new THREE.Mesh(
            new THREE.BoxGeometry(1.15, 1.15, 0.26),
                                    new THREE.MeshStandardMaterial({
                                        color: color,
                                        roughness: 0.3,
                                        metalness: 0.5,
                                        emissive: color,
                                        emissiveIntensity: 0.55
                                    })
        );
        g.add(body);

        const accent = new THREE.Mesh(
            new THREE.SphereGeometry(0.24, 16, 16),
                                      new THREE.MeshStandardMaterial({
                                          color: 0xffffff,
                                          roughness: 0.2,
                                          metalness: 0.85,
                                          emissive: 0xffffff,
                                          emissiveIntensity: 0.25
                                      })
        );
        accent.position.set(0.3, 0.3, 0.2);
        g.add(accent);

        return g;
    },

    // -------------------------------------------------
    // Robust real icon loader (GLB / GLTF / FBX)
    // -------------------------------------------------
    loadRealIcon(id, url, targetSize = 1.1) {
        const extension = url.split('.').pop().toLowerCase();

        const onLoad = (object) => {
            const model = object.scene ? object.scene : object;

            // Center
            const box = new THREE.Box3().setFromObject(model);
            const center = box.getCenter(new THREE.Vector3());
            const size = box.getSize(new THREE.Vector3());
            model.position.sub(center);

            // Force consistent size
            const maxDim = Math.max(size.x, size.y, size.z) || 1;
            const scale = targetSize / maxDim;
            model.scale.setScalar(scale);

            // Remove previous icon
            if (this.loadedIcons[id]) {
                this.taskbar.remove(this.loadedIcons[id]);
            }

            // Position on taskbar (keep previous X if possible)
            const oldX = this.loadedIcons[id] ? this.loadedIcons[id].position.x : -1.1;
            model.position.set(oldX, -3.95, 0.5);

            model.userData = {
                type: 'taskbarIcon',
                id: id,
                originalY: -3.95,
                originalScale: scale,
                originalRotation: model.rotation.clone(),
                hoverScale: 1.28,
                isHovered: false,
                isClicked: false,
                clickTime: 0
            };

            model.traverse(child => {
                if (child.isMesh) {
                    child.castShadow = true;
                    child.receiveShadow = true;
                    child.userData = model.userData;

                    if (child.material) {
                        const mats = Array.isArray(child.material) ? child.material : [child.material];
                        mats.forEach(m => {
                            m.transparent = false;
                            m.opacity = 1;
                            if (m.emissive) {
                                m.emissiveIntensity = Math.max(m.emissiveIntensity || 0, 0.3);
                            }
                        });
                    }
                }
            });

            this.taskbar.add(model);
            this.loadedIcons[id] = model;
            console.log(`%c Loaded real icon: ${id} from ${url}`, 'color:#00e5c7');
        };

        if (extension === 'fbx') {
            const loader = new FBXLoader();
            loader.load(url, onLoad, undefined, (err) => {
                console.warn('FBX load failed, keeping placeholder:', err);
            });
        } else {
            const loader = new GLTFLoader();
            loader.load(url, onLoad, undefined, (err) => {
                console.warn('GLTF load failed, keeping placeholder:', err);
            });
        }
    },

    randomizeIconPositions() {
        const xs = [-5.9, -3.5, -1.1, 1.3, 3.7];
        const ids = Object.keys(this.loadedIcons);
        for (let i = xs.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [xs[i], xs[j]] = [xs[j], xs[i]];
        }
        ids.forEach((id, i) => {
            if (this.loadedIcons[id]) this.loadedIcons[id].position.x = xs[i];
        });
    },

    // -------------------------------------------------
    // Animation
    // -------------------------------------------------
    animate() {
        requestAnimationFrame(() => this.animate());

        const t = this.clock.getElapsedTime();
        const delta = this.clock.getDelta();

        if (this.skyMesh?.material?.uniforms?.time) {
            this.skyMesh.material.uniforms.time.value = t;
        }

        Object.values(this.loadedIcons).forEach(model => {
            const ud = model.userData;
            if (!ud) return;

            model.position.y = ud.originalY + Math.sin(t * 1.85 + (ud.id?.length || 1) * 1.4) * 0.07;
            model.rotation.y = ud.originalRotation.y + Math.sin(t * 0.65 + (ud.id?.length || 1)) * 0.18;

            const targetScale = ud.isHovered ? ud.originalScale * ud.hoverScale : ud.originalScale;
            const s = model.scale.x + (targetScale - model.scale.x) * 0.14;
            model.scale.setScalar(s);

            const targetRotX = ud.isHovered ? -0.28 : 0;
            model.rotation.x += (targetRotX - model.rotation.x) * 0.12;

            if (ud.isClicked) {
                ud.clickTime += delta;
                const pulse = 1 + Math.sin(ud.clickTime * 26) * 0.2 * Math.exp(-ud.clickTime * 4.5);
                model.scale.multiplyScalar(pulse);
                if (ud.clickTime > 0.55) {
                    ud.isClicked = false;
                    ud.clickTime = 0;
                }
            }
        });

        this.controls.update();
        this.webglRenderer.render(this.scene, this.camera);
        this.css3dRenderer.render(this.scene, this.camera);
    },

    // -------------------------------------------------
    // Events
    // -------------------------------------------------
    bindEvents() {
        const canvas = this.webglRenderer.domElement;

        canvas.addEventListener('pointermove', (e) => {
            this.mouse.x =  (e.clientX / window.innerWidth)  * 2 - 1;
            this.mouse.y = -(e.clientY / window.innerHeight) * 2 + 1;

            this.raycaster.setFromCamera(this.mouse, this.camera);
            const hits = this.raycaster.intersectObjects(this.taskbar.children, true);

            let newHover = null;
            if (hits.length) {
                let obj = hits[0].object;
                while (obj && obj.userData?.type !== 'taskbarIcon') obj = obj.parent;
                if (obj) newHover = obj;
            }

            if (this.hoveredIcon && this.hoveredIcon !== newHover) {
                this.hoveredIcon.userData.isHovered = false;
            }
            if (newHover && newHover !== this.hoveredIcon) {
                newHover.userData.isHovered = true;
            }
            this.hoveredIcon = newHover;
            canvas.style.cursor = newHover ? 'pointer' : 'default';
        });

        canvas.addEventListener('pointerdown', (e) => {
            this.mouse.x =  (e.clientX / window.innerWidth)  * 2 - 1;
            this.mouse.y = -(e.clientY / window.innerHeight) * 2 + 1;

            this.raycaster.setFromCamera(this.mouse, this.camera);
            const hits = this.raycaster.intersectObjects(this.taskbar.children, true);

            if (hits.length) {
                let obj = hits[0].object;
                while (obj && obj.userData?.type !== 'taskbarIcon') obj = obj.parent;
                if (obj) {
                    obj.userData.isClicked = true;
                    obj.userData.clickTime = 0;
                    this.onTaskbarIconClick(obj.userData.id);
                }
            }
        });
    },

    onTaskbarIconClick(id) {
        this.launchAppAsCSS3DPanel({
            id: 'panel_' + id + '_' + Date.now(),
                                   title: id.charAt(0).toUpperCase() + id.slice(1) + ' App',
                                   position: new THREE.Vector3(
                                       (Math.random() - 0.5) * 5,
                                                               1.8 + Math.random(),
                                                               -5.2
                                   )
        });
    },

    launchAppAsCSS3DPanel({ id, title, position }) {
        const div = document.createElement('div');
        div.style.cssText = `
        width: 480px; height: 300px;
        background: rgba(16,18,32,0.96);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 14px; overflow: hidden;
        pointer-events: auto;
        box-shadow: 0 16px 48px rgba(0,0,0,0.55);
        font-family: system-ui, sans-serif; color: #e2e2e2;
        `;
        div.innerHTML = `
        <div style="padding:11px 16px; background:rgba(255,255,255,0.05); display:flex; justify-content:space-between; align-items:center;">
        <strong style="font-size:14px;">${title}</strong>
        <button style="background:#ef5350;border:none;color:white;width:22px;height:22px;border-radius:50%;cursor:pointer;font-size:14px;">×</button>
        </div>
        <div style="padding:22px; font-size:14px; line-height:1.55;">
        Real DOM content inside a CSS3DObject.<br><br>
        You can load any NicerApp view, form, editor, etc. here.
        </div>
        `;
        div.querySelector('button').onclick = () => {
            this.scene.remove(css3d);
            div.remove();
            delete this.panels[id];
        };

        const css3d = new CSS3DObject(div);
        css3d.position.copy(position);
        css3d.scale.set(0.01, 0.01, 0.01);
        this.scene.add(css3d);
        this.panels[id] = { div, css3d };
    },

    onResize() {
        const w = window.innerWidth;
        const h = window.innerHeight;
        this.camera.aspect = w / h;
        this.camera.updateProjectionMatrix();
        this.webglRenderer.setSize(w, h);
        this.css3dRenderer.setSize(w, h);
    }
};

// Boot
try {
    if (!window.WebGLRenderingContext) {
        throw new Error('WebGL is not supported in this browser');
    }
    na.desktop3D.init();
    console.log('%c NicerApp 3D Desktop v1.2.0 initialized successfully', 'color:#00e5c7; font-weight:bold');
} catch (err) {
    console.error('Failed to start 3D Desktop:', err);
    document.body.innerHTML += `
    <div style="position:fixed;inset:0;background:#1a1a2e;color:#ff6b6b;
    display:flex;align-items:center;justify-content:center;
    font-family:system-ui;font-size:18px;text-align:center;padding:40px;z-index:9999">
    <div>
    <h2>3D Desktop failed to start</h2>
    <p style="margin-top:12px;color:#ccc">${err.message}</p>
    </div>
    </div>`;
}
</script>
</body>
</html>
