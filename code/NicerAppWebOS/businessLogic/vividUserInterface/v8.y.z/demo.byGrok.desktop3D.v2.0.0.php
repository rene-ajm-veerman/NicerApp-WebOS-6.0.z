<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Skybox Selector with Loading Dialog</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { overflow: hidden; background: #05050a; font-family: system-ui, sans-serif; color: #eee; }

#ui {
position: absolute; top: 16px; left: 16px; z-index: 20;
background: rgba(12,14,24,0.88); backdrop-filter: blur(12px);
border: 1px solid rgba(255,255,255,0.1); border-radius: 14px;
padding: 16px 18px; min-width: 280px;
}
#ui h3 { font-size: 15px; margin-bottom: 12px; color: #8ab4f8; }
label { font-size: 12px; display: block; margin: 10px 0 4px; color: #aaa; }
select, input, button {
    width: 100%; padding: 8px 10px; border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.12);
    background: #1a1d2e; color: #eee; font-size: 13px;
}
button { margin-top: 10px; background: #2a3a5c; cursor: pointer; }
button:hover { background: #3a4f7a; }
#info {
position: absolute; bottom: 16px; left: 16px; z-index: 20;
font-size: 12px; color: #888; background: rgba(0,0,0,0.4);
padding: 6px 12px; border-radius: 8px;
}

/* ===== Loading Dialog ===== */
#loadingDialog {
position: fixed;
inset: 0;
background: rgba(0,0,0,0.55);
display: none;
align-items: center;
justify-content: center;
z-index: 1000;
backdrop-filter: blur(6px);
}
#loadingDialog.visible {
display: flex;
}
.dialog-box {
    background: rgba(16, 18, 32, 0.95);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 16px;
    padding: 28px 32px;
    width: 340px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    text-align: center;
}
.dialog-box h3 {
    font-size: 16px;
    margin-bottom: 6px;
    color: #e0e0e0;
}
.dialog-box p {
    font-size: 13px;
    color: #999;
    margin-bottom: 20px;
}
.progress-track {
    width: 100%;
    height: 8px;
    background: rgba(255,255,255,0.08);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 10px;
}
.progress-bar {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #4fc3f7, #00e5c7);
    border-radius: 4px;
    transition: width 0.15s ease;
}
.progress-text {
    font-size: 12px;
    color: #aaa;
}
</style>
</head>
<body>

<div id="ui">
<h3>Skybox Selector</h3>

<label>Preset Skyboxes</label>
<select id="skySelect">
<option value="night">Deep Night</option>
<option value="dusk">Dusk Horizon</option>
<option value="aurora">Aurora</option>
<option value="space">Space + Stars</option>
<option value="studio">Soft Studio</option>
<option value="custom">Custom URL (below)</option>
</select>

<label>Custom Equirectangular URL</label>
<input type="text" id="customUrl" value="https://dl.polyhaven.org/file/ph-assets/HDRIs/extra/Tonemapped%20JPG/industrial_sunset_puresky.jpg">
<button onclick="loadCustomSky()">Load Custom Skybox</button>
</div>

<div id="info">Drag to orbit • Scroll to zoom</div>

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
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { RGBELoader } from 'three/addons/loaders/RGBELoader.js';
import { GroundedSkybox } from 'three/addons/objects/GroundedSkybox.js';

let scene, camera, renderer, controls, skyMesh, stars;

init();
animate();

function init() {
    const w = window.innerWidth;
    const h = window.innerHeight;

    scene = new THREE.Scene();

    camera = new THREE.PerspectiveCamera(60, w / h, 0.1, 2000);
    camera.position.set(0, 2, 12);

    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(w, h);
    renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    document.body.appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0x8090c0, 1.5));
    const dir = new THREE.DirectionalLight(0xffffff, 1.8);
    dir.position.set(5, 10, 7);
    scene.add(dir);

    const ground = new THREE.Mesh(
        new THREE.CircleGeometry(30, 64),
                                  new THREE.MeshStandardMaterial({ color: 0x111118, roughness: 0.9 })
    );
    ground.rotation.x = -Math.PI / 2;
    ground.position.y = -3;
    scene.add(ground);

    controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;

    setSkybox('night');

    document.getElementById('skySelect').addEventListener('change', (e) => {
        if (e.target.value !== 'custom') setSkybox(e.target.value);
    });

        window.addEventListener('resize', () => {
            camera.aspect = innerWidth / innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(innerWidth, innerHeight);
        });
}

function clearSky() {
    if (skyMesh) {
        scene.remove(skyMesh);
        skyMesh.geometry.dispose();
        if (skyMesh.material.map) skyMesh.material.map.dispose();
        skyMesh.material.dispose();
        skyMesh = null;
    }
    if (stars) {
        scene.remove(stars);
        stars = null;
    }
    scene.background = null;
    scene.environment = null;
}

function setSkybox(type) {
    clearSky();
    const geo = new THREE.SphereGeometry(80, 64, 32);

    if (type === 'night') {
        skyMesh = new THREE.Mesh(geo, createGradientMaterial(0x05051a, 0x0a0a18));
    } else if (type === 'dusk') {
        skyMesh = new THREE.Mesh(geo, createDuskMaterial());
    } else if (type === 'aurora') {
        skyMesh = new THREE.Mesh(geo, createAuroraMaterial());
    } else if (type === 'space') {
        skyMesh = new THREE.Mesh(geo, new THREE.MeshBasicMaterial({ side: THREE.BackSide, color: 0x03030c }));
        createStars();
    } else if (type === 'studio') {
        skyMesh = new THREE.Mesh(geo, createGradientMaterial(0x1e2438, 0x0d1018));
    }

    if (skyMesh) scene.add(skyMesh);
}

function createGradientMaterial(top, bottom) {
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
}

function createDuskMaterial() {
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
}

function createAuroraMaterial() {
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
}

function createStars() {
    const starsGeo = new THREE.BufferGeometry();
    const pos = [];
    for (let i = 0; i < 2000; i++) {
        const r = 70 + Math.random() * 15;
        const theta = Math.random() * Math.PI * 2;
        const phi = Math.acos(2 * Math.random() - 1);
        pos.push(
            r * Math.sin(phi) * Math.cos(theta),
                 r * Math.sin(phi) * Math.sin(theta),
                 r * Math.cos(phi)
        );
    }
    starsGeo.setAttribute('position', new THREE.Float32BufferAttribute(pos, 3));
    stars = new THREE.Points(starsGeo, new THREE.PointsMaterial({ color: 0xffffff, size: 0.35 }));
    scene.add(stars);
}

// ===== Loading dialog helpers =====
function showLoading(message = 'Downloading image…') {
    document.getElementById('loadingMessage').textContent = message;
    document.getElementById('progressBar').style.width = '0%';
    document.getElementById('progressText').textContent = '0%';
    document.getElementById('loadingDialog').classList.add('visible');
}

function updateProgress(percent) {
    const pct = Math.min(100, Math.round(percent));
    document.getElementById('progressBar').style.width = pct + '%';
    document.getElementById('progressText').textContent = pct + '%';
}

function hideLoading() {
    document.getElementById('loadingDialog').classList.remove('visible');
}

// ===== Custom skybox loader with progress =====
window.loadCustomSky = function() {
    const url = document.getElementById('customUrl').value.trim();
    if (!url) {
        alert('Please enter a URL');
        return;
    }

    showLoading('Downloading skybox…');

    const isHDR = url.toLowerCase().endsWith('.hdr') || url.toLowerCase().endsWith('.exr');

    if (isHDR) {
        const loader = new RGBELoader();

        loader.load(
            url,
            (texture) => {
                clearSky();
                texture.mapping = THREE.EquirectangularReflectionMapping;
                //scene.background = texture;
                //scene.environment = texture;

                const height = 15;   // how high the original photo/camera was above the ground
                const radius = 100;  // large enough that the camera stays inside

                const skybox = new GroundedSkybox(texture, height, radius);
                skybox.position.y = height;   // puts the projected ground at y = 0
                scene.add(skybox);


                hideLoading();
                document.getElementById('skySelect').value = 'custom';
        console.log('HDR skybox loaded');
            },
            (xhr) => {
                if (xhr.lengthComputable) {
                    updateProgress((xhr.loaded / xhr.total) * 100);
                } else {
                    // fallback animation when size is unknown
                    updateProgress(Math.min(90, parseInt(document.getElementById('progressText').textContent) + 8));
                }
            },
            (err) => {
                hideLoading();
                console.error(err);
                alert('Failed to load HDR. Check the URL and CORS settings.');
            }
        );
    } else {
        // JPG / PNG / WebP
        const loader = new THREE.TextureLoader();

        loader.load(
            url,
            (texture) => {
                clearSky();
                texture.mapping = THREE.EquirectangularReflectionMapping;
                texture.colorSpace = THREE.SRGBColorSpace;

                // const geo = new THREE.SphereGeometry(80, 64, 32);
                // const mat = new THREE.MeshBasicMaterial({
                //     map: texture,
                //     side: THREE.BackSide
                // });
                // skyMesh = new THREE.Mesh(geo, mat);
                // scene.add(skyMesh);
                const height = 15;   // how high the original photo/camera was above the ground
                const radius = 100;  // large enough that the camera stays inside

                const skybox = new GroundedSkybox(texture, height, radius);
                skybox.position.y = height;   // puts the projected ground at y = 0
                scene.add(skybox);


                hideLoading();
                document.getElementById('skySelect').value = 'custom';
        console.log('Equirectangular skybox loaded');
            },
            (xhr) => {
                if (xhr.lengthComputable) {
                    updateProgress((xhr.loaded / xhr.total) * 100);
                } else {
                    updateProgress(Math.min(90, parseInt(document.getElementById('progressText').textContent) + 8));
                }
            },
            (err) => {
                hideLoading();
                console.error(err);
                alert('Failed to load image. Check the URL and CORS settings.');
            }
        );
    }
};

function animate() {
    requestAnimationFrame(animate);

    if (skyMesh?.material?.uniforms?.time) {
        skyMesh.material.uniforms.time.value = performance.now() * 0.001;
    }

    controls.update();
    renderer.render(scene, camera);
}
</script>
</body>
</html>
