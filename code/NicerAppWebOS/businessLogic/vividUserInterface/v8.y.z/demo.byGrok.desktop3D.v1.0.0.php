<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>NicerApp WebOS – 3D Taskbar Demo</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { overflow: hidden; background: #0a0a12; font-family: system-ui, sans-serif; }
#info {
position: absolute; top: 12px; left: 12px; color: #aaa; font-size: 13px;
background: rgba(0,0,0,0.5); padding: 8px 12px; border-radius: 8px; z-index: 10;
}
</style>
</head>
<body>

<div id="info">
3D Taskbar Demo – Hover & click the icons<br>
(Replace the geometric icons with real .glb files later)
</div>

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

// =====================================================
// NicerApp-style 3D Desktop
// =====================================================
const na = {};
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

    init() {
        const w = window.innerWidth;
        const h = window.innerHeight;

        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(0x0a0a12);

        this.camera = new THREE.PerspectiveCamera(50, w / h, 0.1, 100);
        this.camera.position.set(0, 1.8, 9);

        // WebGL renderer
        this.webglRenderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        this.webglRenderer.setSize(w, h);
        this.webglRenderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        this.webglRenderer.domElement.style.position = 'absolute';
        this.webglRenderer.domElement.style.top = '0';
        this.webglRenderer.domElement.style.left = '0';
        this.webglRenderer.domElement.style.zIndex = '1';
        document.body.appendChild(this.webglRenderer.domElement);

        // CSS3D renderer
        this.css3dRenderer = new CSS3DRenderer();
        this.css3dRenderer.setSize(w, h);
        this.css3dRenderer.domElement.style.position = 'absolute';
        this.css3dRenderer.domElement.style.top = '0';
        this.css3dRenderer.domElement.style.left = '0';
        this.css3dRenderer.domElement.style.zIndex = '2';
        this.css3dRenderer.domElement.style.pointerEvents = 'none';
        document.body.appendChild(this.css3dRenderer.domElement);

        // Lights
        const amb = new THREE.AmbientLight(0x404060, 0.7);
        this.scene.add(amb);
        const dir = new THREE.DirectionalLight(0xffffff, 1.3);
        dir.position.set(5, 10, 7);
        this.scene.add(dir);

        // Soft ground
        const ground = new THREE.Mesh(
            new THREE.PlaneGeometry(40, 40),
                                      new THREE.MeshStandardMaterial({ color: 0x111118, roughness: 0.9, metalness: 0.1 })
        );
        ground.rotation.x = -Math.PI / 2;
        ground.position.y = -5;
        this.scene.add(ground);

        // Controls (optional – disable if you want fixed camera)
        this.controls = new OrbitControls(this.camera, this.webglRenderer.domElement);
        this.controls.enableDamping = true;
        this.controls.maxPolarAngle = Math.PI * 0.48;
        this.controls.minDistance = 5;
        this.controls.maxDistance = 18;

        this.createTaskbar();
        this.bindEvents();
        this.animate();

        window.addEventListener('resize', () => this.onResize());
    },

    // -------------------------------------------------
    // Taskbar with pure Three.js procedural animation
    // -------------------------------------------------
    createTaskbar() {
        this.taskbar = new THREE.Group();
        this.taskbar.name = 'taskbar';

        // Background bar
        const bar = new THREE.Mesh(
            new THREE.PlaneGeometry(15, 1.35),
                                   new THREE.MeshStandardMaterial({
                                       color: 0x12121f,
                                       transparent: true,
                                       opacity: 0.88,
                                       roughness: 0.25,
                                       metalness: 0.35
                                   })
        );
        bar.position.set(0, -3.95, 0);
        bar.rotation.x = -0.14;
        this.taskbar.add(bar);

        // Icon definitions (geometric placeholders)
        // Later replace the createIconGeometry calls with real GLTF models
        const icons = [
            { id: 'start',    color: 0x00e5c7, x: -5.8, label: 'Start' },
            { id: 'browser',  color: 0x4fc3f7, x: -3.5, label: 'Browser' },
            { id: 'files',    color: 0xffb74d, x: -1.2, label: 'Files' },
            { id: 'settings', color: 0xce93d8, x:  1.1, label: 'Settings' },
            { id: 'theme',    color: 0x81c784, x:  3.4, label: 'Theme' }
        ];

        icons.forEach(cfg => {
            const model = this.createPlaceholderIcon(cfg.color);
            model.position.set(cfg.x, -3.75, 0.4);
            model.scale.setScalar(0.55);

            model.userData = {
                type: 'taskbarIcon',
                id: cfg.id,
                label: cfg.label,
                originalY: model.position.y,
                originalScale: 0.55,
                originalRotation: model.rotation.clone(),
                      hoverScale: 1.28,
                      isHovered: false,
                      isClicked: false,
                      clickTime: 0
            };

            model.traverse(c => {
                if (c.isMesh) c.userData = model.userData;
            });

                this.taskbar.add(model);
                this.loadedIcons[cfg.id] = model;
        });

        this.scene.add(this.taskbar);
    },

    // Simple geometric icon (replace with GLTF later)
    createPlaceholderIcon(color) {
        const group = new THREE.Group();

        // Main body
        const body = new THREE.Mesh(
            new THREE.BoxGeometry(1.1, 1.1, 0.22),
                                    new THREE.MeshStandardMaterial({
                                        color: color,
                                        roughness: 0.35,
                                        metalness: 0.45,
                                        emissive: color,
                                        emissiveIntensity: 0.08
                                    })
        );
        group.add(body);

        // Small accent sphere
        const accent = new THREE.Mesh(
            new THREE.SphereGeometry(0.22, 16, 16),
                                      new THREE.MeshStandardMaterial({
                                          color: 0xffffff,
                                          roughness: 0.2,
                                          metalness: 0.8
                                      })
        );
        accent.position.set(0.28, 0.28, 0.18);
        group.add(accent);

        return group;
    },

    // -------------------------------------------------
    // Animation loop – pure Three.js
    // -------------------------------------------------
    animate() {
        requestAnimationFrame(() => this.animate());

        const t = this.clock.getElapsedTime();
        const delta = this.clock.getDelta();

        Object.values(this.loadedIcons).forEach(model => {
            const ud = model.userData;

            // Idle floating
            const floatY = Math.sin(t * 1.9 + ud.id.length * 1.3) * 0.07;
            model.position.y = ud.originalY + floatY;

            // Gentle idle rotation
            model.rotation.y = ud.originalRotation.y + Math.sin(t * 0.65 + ud.id.length) * 0.18;

            // Hover scale + tilt
            const targetScale = ud.isHovered ? ud.originalScale * ud.hoverScale : ud.originalScale;
            const s = model.scale.x + (targetScale - model.scale.x) * 0.14;
            model.scale.setScalar(s);

            const targetRotX = ud.isHovered ? -0.28 : 0;
            model.rotation.x += (targetRotX - model.rotation.x) * 0.12;

            // Click pulse
            if (ud.isClicked) {
                ud.clickTime += delta;
                const pulse = 1 + Math.sin(ud.clickTime * 28) * 0.22 * Math.exp(-ud.clickTime * 5);
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
            this.mouse.x =  (e.clientX / window.innerWidth)  * 2 - 1;
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
        console.log('Clicked:', id);

        // Example: open a CSS3D panel
        this.launchAppAsCSS3DPanel({
            id: 'demo_' + id,
            title: this.loadedIcons[id].userData.label + ' App',
            position: new THREE.Vector3(
                (Math.random() - 0.5) * 4,
                                        1.4 + Math.random() * 0.8,
                                        -4.5
            )
        });
    },

    // -------------------------------------------------
    // Launch a real DOM panel into CSS3D
    // -------------------------------------------------
    launchAppAsCSS3DPanel({ id, title, position }) {
        if (this.panels[id]) {
            // already open – bring to front / pulse
            return;
        }

        const div = document.createElement('div');
        div.style.width = '520px';
        div.style.height = '340px';
        div.style.background = 'rgba(18, 20, 35, 0.94)';
        div.style.border = '1px solid rgba(255,255,255,0.12)';
        div.style.borderRadius = '14px';
        div.style.overflow = 'hidden';
        div.style.pointerEvents = 'auto';
        div.style.boxShadow = '0 12px 40px rgba(0,0,0,0.5)';
        div.style.fontFamily = 'system-ui, sans-serif';
        div.style.color = '#e0e0e0';

        div.innerHTML = `
        <div style="padding:12px 16px; background:rgba(255,255,255,0.05); display:flex; justify-content:space-between; align-items:center;">
        <strong>${title}</strong>
        <button style="background:#ff5252; border:none; color:white; width:22px; height:22px; border-radius:50%; cursor:pointer;">×</button>
        </div>
        <div style="padding:24px; font-size:15px; line-height:1.5;">
        This is a real DOM element living inside a <code>CSS3DObject</code>.<br><br>
        You can put any NicerApp content, forms, TinyMCE, jQuery widgets, etc. here.<br><br>
        Click the × to close.
        </div>
        `;

        const closeBtn = div.querySelector('button');
        closeBtn.onclick = () => {
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

// Start
na.desktop3D.init();
</script>
</body>
</html>
