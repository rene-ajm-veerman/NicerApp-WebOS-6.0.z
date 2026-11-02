<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>NicerApp WebOS – 3D Desktop Demo (Improved)</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { overflow: hidden; background: #05050a; font-family: system-ui, -apple-system, sans-serif; color: #e0e0e0; }

#ui {
position: absolute; top: 16px; left: 16px; z-index: 20;
background: rgba(12, 14, 24, 0.85); backdrop-filter: blur(12px);
border: 1px solid rgba(255,255,255,0.08); border-radius: 14px;
padding: 14px 18px; min-width: 260px;
}
#ui h3 { font-size: 14px; margin-bottom: 10px; color: #8ab4f8; font-weight: 600; }
#ui label { font-size: 12px; display: block; margin: 8px 0 4px; color: #aaa; }
select, button {
    width: 100%; padding: 7px 10px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.12);
    background: #1a1d2e; color: #eee; font-size: 13px; cursor: pointer;
}
button { margin-top: 10px; background: #2a3a5c; }
button:hover { background: #3a4f7a; }
#info {
position: absolute; bottom: 18px; left: 18px; z-index: 20;
font-size: 12px; color: #888; background: rgba(0,0,0,0.4);
padding: 6px 12px; border-radius: 8px;
}
</style>
</head>
<body>

<div id="ui">
<h3>NicerApp 3D Desktop</h3>
<label>Skybox / Environment</label>
<select id="skySelect">
<option value="night">Deep Night</option>
<option value="dusk">Dusk Horizon</option>
<option value="aurora">Aurora</option>
<option value="space">Space Gradient</option>
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

        this.camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 2000);
        this.camera.position.set(0, 2.2, 11);

        // WebGL
        this.webglRenderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        this.webglRenderer.setSize(w, h);
        this.webglRenderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.webglRenderer.toneMapping = THREE.ACESFilmicToneMapping;
        this.webglRenderer.toneMappingExposure = 1.1;
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

        // Lights
        this.scene.add(new THREE.AmbientLight(0x404060, 0.55));
        const dir = new THREE.DirectionalLight(0xffffff, 1.4);
        dir.position.set(6, 12, 8);
        this.scene.add(dir);

        // Soft ground
        const ground = new THREE.Mesh(
            new THREE.CircleGeometry(18, 64),
                                      new THREE.MeshStandardMaterial({ color: 0x0c0c14, roughness: 0.85, metalness: 0.15 })
        );
        ground.rotation.x = -Math.PI / 2;
        ground.position.y = -4.6;
        this.scene.add(ground);

        // Controls
        this.controls = new OrbitControls(this.camera, this.webglRenderer.domElement);
        this.controls.enableDamping = true;
        this.controls.maxPolarAngle = Math.PI * 0.49;
        this.controls.minDistance = 6;
        this.controls.maxDistance = 22;
        this.controls.target.set(0, 0.5, 0);

        this.createSkybox('night');
        this.createTaskbar();
        this.bindEvents();
        this.animate();

        // Sky selector
        document.getElementById('skySelect').addEventListener('change', (e) => {
            this.createSkybox(e.target.value);
        });

        window.addEventListener('resize', () => this.onResize());
    },

    // -------------------------------------------------
    // Multiple public-domain style skyboxes
    // -------------------------------------------------
    createSkybox(type = 'night') {
        if (this.skyMesh) {
            this.scene.remove(this.skyMesh);
            this.skyMesh.geometry.dispose();
            this.skyMesh.material.dispose();
        }

        const geo = new THREE.SphereGeometry(80, 64, 32);
        let mat;

        if (type === 'night') {
            mat = new THREE.ShaderMaterial({
                side: THREE.BackSide,
                uniforms: {
                    topColor: { value: new THREE.Color(0x02020a) },
                                           bottomColor: { value: new THREE.Color(0x0a0a18) },
                                           offset: { value: 0.4 },
                                           exponent: { value: 0.7 }
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
                uniform float offset;
                uniform float exponent;
                varying vec3 vWorldPosition;
                void main() {
                    float h = normalize(vWorldPosition + offset).y;
                    gl_FragColor = vec4(mix(bottomColor, topColor, max(pow(max(h, 0.0), exponent), 0.0)), 1.0);
                }
                `
            });
        } else if (type === 'dusk') {
            mat = new THREE.ShaderMaterial({
                side: THREE.BackSide,
                uniforms: {
                    topColor: { value: new THREE.Color(0x1a1030) },
                                           midColor: { value: new THREE.Color(0xc45c26) },
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
                    vec3 col = h > 0.1 ? mix(midColor, topColor, (h-0.1)*1.2) : mix(bottomColor, midColor, h*8.0);
                    gl_FragColor = vec4(col, 1.0);
                }
                `
            });
        } else if (type === 'aurora') {
            mat = new THREE.ShaderMaterial({
                side: THREE.BackSide,
                uniforms: {
                    time: { value: 0 }
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
                uniform float time;
                varying vec3 vWorldPosition;
                void main() {
                    vec3 dir = normalize(vWorldPosition);
                    float y = dir.y * 0.5 + 0.5;
                    vec3 col = mix(vec3(0.02,0.03,0.08), vec3(0.05,0.15,0.25), y);
                    float aurora = sin(dir.x * 3.0 + time * 0.3) * sin(dir.z * 2.0 + time * 0.2);
                    aurora = pow(max(aurora, 0.0), 3.0) * (0.4 + 0.3 * sin(time));
                    col += vec3(0.1, 0.6, 0.4) * aurora * smoothstep(0.1, 0.6, y);
                    gl_FragColor = vec4(col, 1.0);
                }
                `
            });
        } else if (type === 'space') {
            mat = new THREE.MeshBasicMaterial({
                side: THREE.BackSide,
                color: 0x050510
            });
            // Add simple stars
            const starsGeo = new THREE.BufferGeometry();
            const positions = [];
            for (let i = 0; i < 1200; i++) {
                const r = 70 + Math.random() * 10;
                const theta = Math.random() * Math.PI * 2;
                const phi = Math.acos(2 * Math.random() - 1);
                positions.push(
                    r * Math.sin(phi) * Math.cos(theta),
                               r * Math.sin(phi) * Math.sin(theta),
                               r * Math.cos(phi)
                );
            }
            starsGeo.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
            const stars = new THREE.Points(starsGeo, new THREE.PointsMaterial({ color: 0xffffff, size: 0.35 }));
            this.scene.add(stars);
            this._stars = stars;
        } else { // studio
            mat = new THREE.ShaderMaterial({
                side: THREE.BackSide,
                uniforms: {
                    topColor: { value: new THREE.Color(0x1e2438) },
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

        // Clean previous stars if any
        if (type !== 'space' && this._stars) {
            this.scene.remove(this._stars);
            this._stars = null;
        }
    },

    // -------------------------------------------------
    // Taskbar
    // -------------------------------------------------
    createTaskbar() {
        this.taskbar = new THREE.Group();

        // Bar
        const bar = new THREE.Mesh(
            new THREE.PlaneGeometry(15.5, 1.4),
                                   new THREE.MeshStandardMaterial({
                                       color: 0x10101c,
                                       transparent: true,
                                       opacity: 0.9,
                                       roughness: 0.3,
                                       metalness: 0.4
                                   })
        );
        bar.position.set(0, -4.05, 0);
        bar.rotation.x = -0.13;
        this.taskbar.add(bar);

        // Lights – make them stronger
        const ambient = new THREE.AmbientLight(0x8090c0, 1.8);
        this.scene.add(ambient);

        const dir = new THREE.DirectionalLight(0xffffff, 2.4);
        dir.position.set(6, 14, 9);
        this.scene.add(dir);

        const icons = [
            { id: 'start',    color: 0x00e5c7, x: -5.9 },
            { id: 'browser',  color: 0x4fc3f7, x: -3.5 },
            { id: 'files',    color: 0xffb74d, x: -1.1 },
            { id: 'settings', color: 0xce93d8, x:  1.3 },
            { id: 'theme',    color: 0x81c784, x:  3.7 }
        ];

        icons.forEach(cfg => {
            const model = this.createPlaceholderIcon(cfg.color);
            model.position.set(cfg.x, -3.85, 0.45);
            model.scale.setScalar(0.58);

            model.userData = {
                type: 'taskbarIcon',
                id: cfg.id,
                originalY: model.position.y,
                originalScale: 0.58,
                originalRotation: model.rotation.clone(),
                      hoverScale: 1.3,
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
            new THREE.BoxGeometry(1.15, 1.15, 0.25),
                                    new THREE.MeshStandardMaterial({
                                        color,
                                        roughness: 0.32,
                                        metalness: 0.5,
                                        emissive: color,
                                        emissiveIntensity: 0.1
                                    })
        );
        g.add(body);

        const accent = new THREE.Mesh(
            new THREE.SphereGeometry(0.24, 20, 20),
                                      new THREE.MeshStandardMaterial({ color: 0xffffff, roughness: 0.2, metalness: 0.85 })
        );
        accent.position.set(0.3, 0.3, 0.2);
        g.add(accent);
        return g;
    },

    // Helper for real GLB icons later
    loadRealIcon(id, url, scale = 0.55) {
        const extension = url.split('.').pop().toLowerCase();

        const onLoad = (object) => {
            // FBXLoader returns the object directly, GLTFLoader returns { scene }
            const model = object.scene ? object.scene : object;

            // Center the model
            const box = new THREE.Box3().setFromObject(model);
            const center = box.getCenter(new THREE.Vector3());
            model.position.sub(center);

            // Remove previous icon with the same id
            if (this.loadedIcons[id]) {
                this.taskbar.remove(this.loadedIcons[id]);
            }

            model.scale.setScalar(scale);
            model.position.y = -3.85;
            model.position.z = 0.45;

            // Keep previous X position if it existed
            if (this.loadedIcons[id]) {
                model.position.x = this.loadedIcons[id].position.x;
            }

            model.userData = {
                type: 'taskbarIcon',
                id: id,
                originalY: model.position.y,
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
                }
            });

            this.taskbar.add(model);
            this.loadedIcons[id] = model;
        };

        debugger;
        if (extension === 'fbx') {
            // Load FBX
            import('three/addons/loaders/FBXLoader.js').then(({ FBXLoader }) => {
                const loader = new FBXLoader();
                loader.load(url, onLoad, undefined, (err) => {
                    console.error('FBX load error:', err);
                });
            });
        } else {
            // Default: GLTF / GLB
            const loader = new GLTFLoader();
            loader.load(url, onLoad, undefined, (err) => {
                console.error('GLTF load error:', err);
            });
        }
    },

    randomizeIconPositions() {
        const xs = [-5.9, -3.5, -1.1, 1.3, 3.7];
        const ids = Object.keys(this.loadedIcons);
        // simple shuffle
        for (let i = xs.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [xs[i], xs[j]] = [xs[j], xs[i]];
        }
        ids.forEach((id, i) => {
            this.loadedIcons[id].position.x = xs[i];
        });
    },

    // -------------------------------------------------
    // Animation
    // -------------------------------------------------
    animate() {
        requestAnimationFrame(() => this.animate());
        const t = this.clock.getElapsedTime();
        const delta = this.clock.getDelta();

        // Aurora animation
        if (this.skyMesh && this.skyMesh.material.uniforms && this.skyMesh.material.uniforms.time) {
            this.skyMesh.material.uniforms.time.value = t;
        }

        Object.values(this.loadedIcons).forEach(model => {
            const ud = model.userData;

            // Idle float + rotation
            model.position.y = ud.originalY + Math.sin(t * 1.85 + ud.id.length * 1.4) * 0.075;
            model.rotation.y = ud.originalRotation.y + Math.sin(t * 0.7 + ud.id.length) * 0.2;

            // Hover
            const targetScale = ud.isHovered ? ud.originalScale * ud.hoverScale : ud.originalScale;
            const s = model.scale.x + (targetScale - model.scale.x) * 0.13;
            model.scale.setScalar(s);

            const targetRotX = ud.isHovered ? -0.3 : 0;
            model.rotation.x += (targetRotX - model.rotation.x) * 0.11;

            // Click pulse
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
            this.mouse.x = (e.clientX / window.innerWidth) * 2 - 1;
            this.mouse.y = -(e.clientY / window.innerHeight) * 2 + 1;
            this.raycaster.setFromCamera(this.mouse, this.camera);
            const hits = this.raycaster.intersectObjects(this.taskbar.children, true);

            let newHover = null;
            if (hits.length) {
                let obj = hits[0].object;
                while (obj && obj.userData.type !== 'taskbarIcon') obj = obj.parent;
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
            this.mouse.x = (e.clientX / window.innerWidth) * 2 - 1;
            this.mouse.y = -(e.clientY / window.innerHeight) * 2 + 1;
            this.raycaster.setFromCamera(this.mouse, this.camera);
            const hits = this.raycaster.intersectObjects(this.taskbar.children, true);

            if (hits.length) {
                let obj = hits[0].object;
                while (obj && obj.userData.type !== 'taskbarIcon') obj = obj.parent;
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
                                                               1.6 + Math.random() * 1.2,
                                                               -5.5
                                   )
        });
    },

    launchAppAsCSS3DPanel({ id, title, position }) {
        const div = document.createElement('div');
        div.style.cssText = `
        width: 480px; height: 300px;
        background: rgba(16,18,32,0.95);
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
// Boot with error handling
try {
    if (!window.WebGLRenderingContext) {
        throw new Error('WebGL is not supported in this browser');
    }
    na.desktop3D.init();
    console.log('%c NicerApp 3D Desktop initialized successfully', 'color: #00e5c7');
} catch (err) {
    console.error('Failed to start 3D Desktop:', err);
    document.body.innerHTML += `
    <div style="position:fixed;inset:0;background:#1a1a2e;color:#ff6b6b;
    display:flex;align-items:center;justify-content:center;
    font-family:system-ui;font-size:18px;text-align:center;padding:40px;z-index:9999">
    <div>
    <h2>3D Desktop failed to start</h2>
    <p style="margin-top:12px;color:#ccc">${err.message}</p>
    <p style="margin-top:20px;font-size:14px;color:#888">
    Open the browser console (F12) for more details.
    </p>
    </div>
    </div>
    `;
}</script>
</body>
</html>
