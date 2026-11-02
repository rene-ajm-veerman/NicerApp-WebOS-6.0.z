<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>NicerApp WebOS – 3D Desktop v2.1.0</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { overflow: hidden; background: #05050a; font-family: system-ui, -apple-system, sans-serif; color: #e0e0e0; }

  #ui {
    position: absolute; top: 16px; left: 16px; z-index: 20;
    background: rgba(12, 14, 24, 0.90); backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.09); border-radius: 14px;
    padding: 14px 18px; min-width: 280px;
  }
  #ui h3 { font-size: 14px; margin-bottom: 10px; color: #8ab4f8; font-weight: 600; }
  #ui label { font-size: 12px; display: block; margin: 8px 0 4px; color: #aaa; }
  select, input, button {
    width: 100%; padding: 7px 10px; border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.12);
    background: #1a1d2e; color: #eee; font-size: 13px; cursor: pointer;
  }
  button { margin-top: 8px; background: #2a3a5c; }
  button:hover { background: #3a4f7a; }
  #info {
    position: absolute; bottom: 18px; left: 18px; z-index: 20;
    font-size: 12px; color: #888; background: rgba(0,0,0,0.45);
    padding: 6px 12px; border-radius: 8px;
  }

  /* Loading Dialog */
  #loadingDialog {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.55);
    display: none; align-items: center; justify-content: center;
    z-index: 1000; backdrop-filter: blur(6px);
  }
  #loadingDialog.visible { display: flex; }
  .dialog-box {
    background: rgba(16, 18, 32, 0.96);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 16px; padding: 28px 32px; width: 340px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.5); text-align: center;
  }
  .dialog-box h3 { font-size: 16px; margin-bottom: 6px; color: #e0e0e0; }
  .dialog-box p { font-size: 13px; color: #999; margin-bottom: 20px; }
  .progress-track {
    width: 100%; height: 8px; background: rgba(255,255,255,0.08);
    border-radius: 4px; overflow: hidden; margin-bottom: 10px;
  }
  .progress-bar {
    height: 100%; width: 0%;
    background: linear-gradient(90deg, #4fc3f7, #00e5c7);
    border-radius: 4px; transition: width 0.15s ease;
  }
  .progress-text { font-size: 12px; color: #aaa; }
</style>
</head>
<body>

<div id="ui">
  <h3>NicerApp 3D Desktop <small style="opacity:0.6">v2.1.0</small></h3>

  <label>Skybox / Environment</label>
  <select id="skySelect">
    <option value="night">Deep Night</option>
    <option value="dusk">Dusk Horizon</option>
    <option value="aurora">Aurora</option>
    <option value="space">Space + Stars</option>
    <option value="studio">Soft Studio</option>
    <option value="custom">Custom URL</option>
  </select>

  <label>Custom Equirectangular / HDR URL</label>
  <input type="text" id="customUrl" value="https://dl.polyhaven.org/file/ph-assets/HDRIs/extra/Tonemapped%20JPG/industrial_sunset_puresky.jpg">
  <button onclick="na.desktop3D.loadCustomSky()">Load Custom Skybox</button>

  <button onclick="na.desktop3D.randomizeIconPositions()" style="margin-top:12px">Shuffle Icons</button>
</div>

<div id="info">Hover & click the icons • Drag to orbit • Scroll to zoom</div>

<!-- Loading Dialog -->
<div id="loadingDialog">
  <div class="dialog-box">
    <h3>Loading Skybox</h3>
    <p id="loadingMessage">Downloading image…</p>
    <div class="progress-track">
      <div class="progress-bar" id="progressBar"></div>
    </div>
    <div class="progress-text" id="progressText">0%</div>
  </div>
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
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { FBXLoader } from 'three/addons/loaders/FBXLoader.js';
import { RGBELoader } from 'three/addons/loaders/RGBELoader.js';
import { GroundedSkybox } from 'three/addons/objects/GroundedSkybox.js';

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
  groundedSky: null,
  stars: null,
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

    // Lighting
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
        new THREE.CircleGeometry(28, 64),
                                  new THREE.MeshStandardMaterial({
                                      color: 0x1e1e2a,
                                      roughness: 0.75,
                                      metalness: 0.15,
                                      emissive: 0x0a0a12,
                                      emissiveIntensity: 0.12
                                  })
    );
    ground.rotation.x = -Math.PI / 2;
    ground.position.y = -4.55;
    this.scene.add(ground);
    this.groundMesh = ground;   // keep a reference
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

    // Load real FBX as files icon
    this.loadRealIcon('files', './icons/folder.fbx', 1.1);
    this.loadRealIcon('start', './icons/rocket.fbx', 1.1);

    document.getElementById('skySelect').addEventListener('change', (e) => {
      const val = e.target.value;
      if (val !== 'custom') this.createSkybox(val);
    });

    window.addEventListener('resize', () => this.onResize());
  },

  // ---------- Loading dialog helpers ----------
  showLoading(msg = 'Downloading image…') {
    document.getElementById('loadingMessage').textContent = msg;
    document.getElementById('progressBar').style.width = '0%';
    document.getElementById('progressText').textContent = '0%';
    document.getElementById('loadingDialog').classList.add('visible');
  },
  updateProgress(pct) {
    const p = Math.min(100, Math.round(pct));
    document.getElementById('progressBar').style.width = p + '%';
    document.getElementById('progressText').textContent = p + '%';
  },
  hideLoading() {
    document.getElementById('loadingDialog').classList.remove('visible');
  },

  // ---------- Sky system ----------
  clearSky() {
    if (this.skyMesh) {
      this.scene.remove(this.skyMesh);
      this.skyMesh.geometry?.dispose();
      if (this.skyMesh.material) {
        if (this.skyMesh.material.map) this.skyMesh.material.map.dispose();
        this.skyMesh.material.dispose();
      }
      this.skyMesh = null;
    }
    if (this.groundedSky) {
      this.scene.remove(this.groundedSky);
      this.groundedSky = null;
    }
    if (this.stars) {
      this.scene.remove(this.stars);
      this.stars = null;
    }
    this.scene.background = null;
    this.scene.environment = null;
  },

  createSkybox(type = 'night') {
    this.clearSky();
    this.currentSky = type;
    const geo = new THREE.SphereGeometry(90, 64, 32);

    if (type === 'night') {
      this.skyMesh = new THREE.Mesh(geo, this._gradientMat(0x05051a, 0x0a0a18));
    } else if (type === 'dusk') {
      this.skyMesh = new THREE.Mesh(geo, this._duskMat());
    } else if (type === 'aurora') {
      this.skyMesh = new THREE.Mesh(geo, this._auroraMat());
    } else if (type === 'space') {
      this.skyMesh = new THREE.Mesh(geo, new THREE.MeshBasicMaterial({ side: THREE.BackSide, color: 0x03030c }));
      this._createStars();
    } else if (type === 'studio') {
      this.skyMesh = new THREE.Mesh(geo, this._gradientMat(0x1e2438, 0x0d1018));
    }

    if (this.skyMesh) this.scene.add(this.skyMesh);
  },

  _gradientMat(top, bottom) {
    return new THREE.ShaderMaterial({
      side: THREE.BackSide,
      uniforms: {
        topColor:    { value: new THREE.Color(top) },
        bottomColor: { value: new THREE.Color(bottom) }
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
  },

  _duskMat() {
    return new THREE.ShaderMaterial({
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
            ? mix(midColor, topColor, clamp((h-0.05)*1.4, 0.0, 1.0))
            : mix(bottomColor, midColor, clamp(h*12.0, 0.0, 1.0));
          gl_FragColor = vec4(col, 1.0);
        }
      `
    });
  },

  _auroraMat() {
    return new THREE.ShaderMaterial({
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
          float aurora = pow(max(sin(dir.x*3.5 + time*0.25) * sin(dir.z*2.2 + time*0.18), 0.0), 2.8);
          col += vec3(0.08, 0.55, 0.38) * aurora * smoothstep(0.15, 0.7, y);
          gl_FragColor = vec4(col, 1.0);
        }
      `
    });
  },

  _createStars() {
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
    this.stars = new THREE.Points(starsGeo, new THREE.PointsMaterial({ color: 0xffffff, size: 0.4 }));
    this.scene.add(this.stars);
  },

  loadCustomSky() {
    const url = document.getElementById('customUrl').value.trim();
    if (!url) {
      alert('Please enter a URL');
      return;
    }

    this.showLoading('Downloading skybox…');
    const isHDR = /\.(hdr|exr)$/i.test(url);

    if (isHDR) {
      new RGBELoader().load(
        url,
        (texture) => {
          this.clearSky();
          texture.mapping = THREE.EquirectangularReflectionMapping;
          this.scene.background = texture;
          this.scene.environment = texture;
          this.hideLoading();
          document.getElementById('skySelect').value = 'custom';
          this.currentSky = 'custom';
          console.log('HDR skybox loaded');
        },
        (xhr) => {
          if (xhr.lengthComputable) this.updateProgress((xhr.loaded / xhr.total) * 100);
          else this.updateProgress(Math.min(90, parseInt(document.getElementById('progressText').textContent) + 8));
        },
        (err) => {
          this.hideLoading();
          console.error(err);
          alert('Failed to load HDR. Check URL and CORS.');
        }
      );
    } else {
      new THREE.TextureLoader().load(
        url,
        (texture) => {
          this.clearSky();
          texture.mapping = THREE.EquirectangularReflectionMapping;
          texture.colorSpace = THREE.SRGBColorSpace;

          // Use GroundedSkybox for a more realistic ground projection
          const height = 15;
          const radius = 100;
          this.groundedSky = new GroundedSkybox(texture, height, radius);
          this.groundedSky.position.y = 1;
          this.scene.add(this.groundedSky);
          // inside the TextureLoader success callback, after creating GroundedSkybox:
          if (this.groundMesh) this.groundMesh.visible = false;

          this.hideLoading();
          document.getElementById('skySelect').value = 'custom';
          this.currentSky = 'custom';
          console.log('Equirectangular skybox loaded (GroundedSkybox)');
        },
        (xhr) => {
          if (xhr.lengthComputable) this.updateProgress((xhr.loaded / xhr.total) * 100);
          else this.updateProgress(Math.min(90, parseInt(document.getElementById('progressText').textContent) + 8));
        },
        (err) => {
          this.hideLoading();
          console.error(err);
          alert('Failed to load image. Check URL and CORS.');
        }
      );
    }
  },

  // ---------- Taskbar (from v1.2.0) ----------
  createTaskbar() {
    this.taskbar = new THREE.Group();

    const bar = new THREE.Mesh(
      new THREE.PlaneGeometry(15.8, 1.45),
      new THREE.MeshStandardMaterial({
        color: 0x10101c, transparent: true, opacity: 0.92,
        roughness: 0.3, metalness: 0.4
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
        type: 'taskbarIcon', id: cfg.id,
        originalY: -3.95, originalScale: 0.58,
        originalRotation: model.rotation.clone(),
        hoverScale: 1.32, isHovered: false, isClicked: false, clickTime: 0
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
        color, roughness: 0.3, metalness: 0.5,
        emissive: color, emissiveIntensity: 0.55
      })
    );
    g.add(body);

    const accent = new THREE.Mesh(
      new THREE.SphereGeometry(0.24, 16, 16),
      new THREE.MeshStandardMaterial({
        color: 0xffffff, roughness: 0.2, metalness: 0.85,
        emissive: 0xffffff, emissiveIntensity: 0.25
      })
    );
    accent.position.set(0.3, 0.3, 0.2);
    g.add(accent);
    return g;
  },

  loadRealIcon(id, url, targetSize = 1.1) {
    const extension = url.split('.').pop().toLowerCase();

    const onLoad = (object) => {
      const model = object.scene ? object.scene : object;

      const box = new THREE.Box3().setFromObject(model);
      const center = box.getCenter(new THREE.Vector3());
      const size = box.getSize(new THREE.Vector3());
      model.position.sub(center);

      const maxDim = Math.max(size.x, size.y, size.z) || 1;
      const scale = targetSize / maxDim;
      model.scale.setScalar(scale);

      if (this.loadedIcons[id]) this.taskbar.remove(this.loadedIcons[id]);

      const oldX = this.loadedIcons[id] ? this.loadedIcons[id].position.x : -1.1;
      model.position.set(oldX, -3.95, 0.5);

      model.userData = {
        type: 'taskbarIcon', id,
        originalY: -3.95, originalScale: scale,
        originalRotation: model.rotation.clone(),
        hoverScale: 1.28, isHovered: false, isClicked: false, clickTime: 0
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
              if (m.emissive) m.emissiveIntensity = Math.max(m.emissiveIntensity || 0, 0.3);
            });
          }
        }
      });

      this.taskbar.add(model);
      this.loadedIcons[id] = model;
      console.log(`%c Loaded real icon: ${id}`, 'color:#00e5c7');
    };

    if (extension === 'fbx') {
      new FBXLoader().load(url, onLoad, undefined, (err) => console.warn('FBX failed:', err));
    } else {
      new GLTFLoader().load(url, onLoad, undefined, (err) => console.warn('GLTF failed:', err));
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

  // ---------- Animation ----------
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

  // ---------- Events ----------
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

      if (this.hoveredIcon && this.hoveredIcon !== newHover) this.hoveredIcon.userData.isHovered = false;
      if (newHover && newHover !== this.hoveredIcon) newHover.userData.isHovered = true;
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
      position: new THREE.Vector3((Math.random()-0.5)*5, 1.8 + Math.random(), -5.2)
    });
  },

  launchAppAsCSS3DPanel({ id, title, position }) {
      const div = document.createElement('div');
      div.className = 'css3d-panel';
      div.style.cssText = `
      width: 480px;
      height: 300px;
      min-width: 280px;
      min-height: 180px;
      background: rgba(16,18,32,0.96);
      border: 1px solid rgba(255,255,255,0.12);
      border-radius: 14px;
      overflow: hidden;
      pointer-events: auto;
      box-shadow: 0 16px 48px rgba(0,0,0,0.55);
      font-family: system-ui, sans-serif;
      color: #e2e2e2;
      position: relative;
      resize: none; /* we handle resize ourselves */
      `;

      div.innerHTML = `
      <div class="panel-header" style="
      padding: 11px 16px;
      background: rgba(255,255,255,0.05);
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: move;
      user-select: none;
      ">
      <strong style="font-size:14px;">${title}</strong>
      <button class="panel-close" style="
      background:#ef5350; border:none; color:white;
      width:22px; height:22px; border-radius:50%;
      cursor:pointer; font-size:14px; line-height:1;
      ">×</button>
      </div>
      <div class="panel-body" style="padding:22px; font-size:14px; line-height:1.55; height: calc(100% - 46px); overflow:auto;">
      Real DOM content inside a CSS3DObject.<br><br>
      You can load any NicerApp view, form, editor, etc. here.
      </div>

      <!-- Resize handles -->
      <div class="resize-handle n"  data-dir="n"></div>
      <div class="resize-handle s"  data-dir="s"></div>
      <div class="resize-handle e"  data-dir="e"></div>
      <div class="resize-handle w"  data-dir="w"></div>
      <div class="resize-handle ne" data-dir="ne"></div>
      <div class="resize-handle nw" data-dir="nw"></div>
      <div class="resize-handle se" data-dir="se"></div>
      <div class="resize-handle sw" data-dir="sw"></div>
      `;

      // Add the resize-handle styles once
      if (!document.getElementById('css3d-panel-styles')) {
          const style = document.createElement('style');
          style.id = 'css3d-panel-styles';
          style.textContent = `
          .css3d-panel .resize-handle {
              position: absolute;
              background: transparent;
              z-index: 10;
          }
          .css3d-panel .resize-handle.n  { top: 0; left: 8px; right: 8px; height: 6px; cursor: ns-resize; }
          .css3d-panel .resize-handle.s  { bottom: 0; left: 8px; right: 8px; height: 6px; cursor: ns-resize; }
          .css3d-panel .resize-handle.e  { top: 8px; right: 0; bottom: 8px; width: 6px; cursor: ew-resize; }
          .css3d-panel .resize-handle.w  { top: 8px; left: 0; bottom: 8px; width: 6px; cursor: ew-resize; }
          .css3d-panel .resize-handle.ne { top: 0; right: 0; width: 12px; height: 12px; cursor: nesw-resize; }
          .css3d-panel .resize-handle.nw { top: 0; left: 0; width: 12px; height: 12px; cursor: nwse-resize; }
          .css3d-panel .resize-handle.se { bottom: 0; right: 0; width: 12px; height: 12px; cursor: nwse-resize; }
          .css3d-panel .resize-handle.sw { bottom: 0; left: 0; width: 12px; height: 12px; cursor: nesw-resize; }

          /* Highlight on hover */
          .css3d-panel .resize-handle:hover,
          .css3d-panel .resize-handle.active {
              background: rgba(0, 229, 199, 0.45);
          }
          .css3d-panel .resize-handle.n:hover,
          .css3d-panel .resize-handle.s:hover,
          .css3d-panel .resize-handle.n.active,
          .css3d-panel .resize-handle.s.active {
              background: linear-gradient(to bottom, transparent, rgba(0,229,199,0.5), transparent);
          }
          .css3d-panel .resize-handle.e:hover,
          .css3d-panel .resize-handle.w:hover,
          .css3d-panel .resize-handle.e.active,
          .css3d-panel .resize-handle.w.active {
              background: linear-gradient(to right, transparent, rgba(0,229,199,0.5), transparent);
          }

          /* Glowing border while resizing */
          .css3d-panel.resizing {
              box-shadow: 0 0 0 2px rgba(0, 229, 199, 0.6), 0 16px 48px rgba(0,0,0,0.55);
          }
          `;
          document.head.appendChild(style);
      }

      // Close button
      div.querySelector('.panel-close').onclick = () => {
          this.scene.remove(css3d);
          div.remove();
          delete this.panels[id];
      };

      // ---------- Resize logic ----------
      let isResizing = false;
      let currentDir = null;
      let startX, startY, startW, startH;

      const handles = div.querySelectorAll('.resize-handle');

      handles.forEach(handle => {
          handle.addEventListener('pointerdown', (e) => {
              e.preventDefault();
              e.stopPropagation();

              isResizing = true;
              currentDir = handle.dataset.dir;
              handle.classList.add('active');
              div.classList.add('resizing');

              startX = e.clientX;
              startY = e.clientY;
              startW = div.offsetWidth;
              startH = div.offsetHeight;

              handle.setPointerCapture(e.pointerId);
          });
      });

      const onPointerMove = (e) => {
          if (!isResizing) return;

          const dx = e.clientX - startX;
          const dy = e.clientY - startY;

          let newW = startW;
          let newH = startH;

          if (currentDir.includes('e')) newW = startW + dx;
          if (currentDir.includes('w')) newW = startW - dx;
          if (currentDir.includes('s')) newH = startH + dy;
          if (currentDir.includes('n')) newH = startH - dy;

          // Enforce minimum size
          newW = Math.max(280, newW);
          newH = Math.max(180, newH);

          div.style.width  = newW + 'px';
          div.style.height = newH + 'px';
      };

      const onPointerUp = (e) => {
          if (!isResizing) return;
          isResizing = false;
          currentDir = null;
          div.classList.remove('resizing');
          handles.forEach(h => h.classList.remove('active'));
      };

      // Attach to window so dragging still works when cursor leaves the handle
      window.addEventListener('pointermove', onPointerMove);
      window.addEventListener('pointerup', onPointerUp);

      // Clean up listeners when panel is closed
      const originalClose = div.querySelector('.panel-close').onclick;
      div.querySelector('.panel-close').onclick = () => {
          window.removeEventListener('pointermove', onPointerMove);
          window.removeEventListener('pointerup', onPointerUp);
          originalClose();
      };

      // ---------- Create CSS3DObject ----------
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
  if (!window.WebGLRenderingContext) throw new Error('WebGL is not supported');
  na.desktop3D.init();
  console.log('%c NicerApp 3D Desktop v2.1.0 initialized successfully', 'color:#00e5c7; font-weight:bold');
} catch (err) {
  console.error(err);
  document.body.innerHTML += `<div style="position:fixed;inset:0;background:#1a1a2e;color:#ff6b6b;display:flex;align-items:center;justify-content:center;font-family:system-ui;font-size:18px;text-align:center;padding:40px;z-index:9999"><div><h2>3D Desktop failed to start</h2><p style="margin-top:12px;color:#ccc">${err.message}</p></div></div>`;
}
</script>
</body>
</html>
