<!--
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8" />
-->
<style>
  body { margin: 0; background: rgba(0,0,0,0); color: #fff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow: hidden; }
  #controls {
    position: fixed; top: 10px; right: 10px; background: rgba(0,0,0,0.85);
    padding: 15px; border-radius: 10px; max-width: 340px;
    box-shadow: 0 0 15px rgba(255,255,255,0.2);
    z-index: 10;
  }
  label, select, input {
    display: block; margin: 10px 0;
    width: 100%;
  }
  canvas { display: block; }
  h1 {
    margin: 0 0 10px 0;
    font-weight: 700;
    font-size: 1.5rem;
    text-align: center;
    color: #00ffff;
    text-shadow: 0 0 8px #00ffff;
  }
</style>
<!--<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>-->
<!--<script src="https://cdn.jsdelivr.net/npm/three@0.150.1/build/three.min.js"></script>-->
<!--<script src="https://cdn.jsdelivr.net/npm/three@0.150.1/examples/jsm/controls/OrbitControls.js"></script>-->
<!--</head>
<body>
-->
<div id="controls" style="overflow:auto;height:100%;max-height:fit-content;">
  <h1>BeatPulse</h1>
  <label>Preset:
    <select id="presetSelect"></select>
  </label>

  <label>Style:
    <select id="visualizationStyle">
      <option value="spheres">Bollen</option>
      <option value="bars">Bars</option>
      <option value="lines">Lijnen</option>
      <option value="particles">Partikels</option>
    </select>
  </label>

  <label>Bass:
    <input type="color" id="bassColor" value="#ff0000" />
  </label>

  <label>Mid:
    <input type="color" id="midColor" value="#00ff00" />
  </label>

  <label>Treble:
    <input type="color" id="trebleColor" value="#0000ff" />
  </label>

  <label>Beat sensitivity:
    <input type="range" id="beatSensitivity" min="0" max="1" step="0.01" value="0.5" />
  </label>

  <label>Animation speed:
    <input type="range" id="animSpeed" min="0.1" max="3" step="0.1" value="1" />
  </label>
</div>

<script type="importmap">
{
  "imports": {
    "THREE": "https://cdn.jsdelivr.net/npm/three@0.180.0/build/three.module.js",
    "three": "https://cdn.jsdelivr.net/npm/three@0.180.0/build/three.module.js",
    "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.181.0/examples/jsm/"
  }
}
</script>
<!--
<script type="importmap">
{
  "imports": {
    "three": "https://cdn.jsdelivr.net/npm/three@0.167.0/build/three.module.js",
    "OrbitControls": "https://cdn.jsdelivr.net/npm/three@0.167.0/examples/js/controls/OrbitControls.js"
  }
}
</script>
-->
<script type="module">
import * as THREE from 'https://cdn.jsdelivr.net/npm/three@0.180.0/build/three.module.js';
import * as three from 'https://cdn.jsdelivr.net/npm/three@0.180.0/build/three.module.js';
import { OrbitControls } from 'https://cdn.jsdelivr.net/npm/three@0.167.0/examples/jsm/controls/OrbitControls.js';

import { Aubio } from "/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.javascriptRendering/beatPulse-0.1.0/aubio-0.2.1.js";

//$(document).ready(function() {
  // async function initAubio() {
  //   await aubio();
  //   const pitch = new aubio.Pitch("default", 2048, 256, 44100);
  //   console.log("Aubio initialized:", pitch);
  // }
  // setTimeout(initAubio,2000);
  async function initApp() {
  // UI elementen
  const presetSelect = document.getElementById('presetSelect');
  const visualizationStyleSelect = document.getElementById('visualizationStyle');
  const bassColorInput = document.getElementById('bassColor');
  const midColorInput = document.getElementById('midColor');
  const trebleColorInput = document.getElementById('trebleColor');
  const beatSensitivityInput = document.getElementById('beatSensitivity');
  const animSpeedInput = document.getElementById('animSpeed');
  const audioFileInput = document.getElementById('audioFile');
  const audio = document.getElementById(\'<?=$audioElementID?>\');

  // 22 presets
  const presets = {
    preset1: { name: "Bass Heavy", bassColor: "#ff0000", midColor: "#00ff00", trebleColor: "#0000ff", beatSensitivity: 0.8, animSpeed: 1.5, visualizationStyle: "bars" },
    preset2: { name: "Chill Vibes", bassColor: "#00ffff", midColor: "#ff00ff", trebleColor: "#ffff00", beatSensitivity: 0.3, animSpeed: 0.7, visualizationStyle: "particles" },
    preset3: { name: "Energetic", bassColor: "#ff6600", midColor: "#00ff66", trebleColor: "#6600ff", beatSensitivity: 0.9, animSpeed: 2.0, visualizationStyle: "spheres" },
    preset4: { name: "Deep Bass", bassColor: "#8b0000", midColor: "#556b2f", trebleColor: "#2f4f4f", beatSensitivity: 0.85, animSpeed: 1.3, visualizationStyle: "bars" },
    preset5: { name: "Soft Glow", bassColor: "#ffb6c1", midColor: "#add8e6", trebleColor: "#90ee90", beatSensitivity: 0.4, animSpeed: 0.8, visualizationStyle: "lines" },
    preset6: { name: "Night Pulse", bassColor: "#4b0082", midColor: "#800080", trebleColor: "#9370db", beatSensitivity: 0.7, animSpeed: 1.2, visualizationStyle: "particles" },
    preset7: { name: "Sunset", bassColor: "#ff4500", midColor: "#ffa500", trebleColor: "#ffd700", beatSensitivity: 0.75, animSpeed: 1.4, visualizationStyle: "bars" },
    preset8: { name: "Ocean Breeze", bassColor: "#006994", midColor: "#00ced1", trebleColor: "#20b2aa", beatSensitivity: 0.5, animSpeed: 1.0, visualizationStyle: "lines" },
    preset9: { name: "Electric", bassColor: "#00ffff", midColor: "#7fff00", trebleColor: "#ff00ff", beatSensitivity: 0.9, animSpeed: 2.2, visualizationStyle: "spheres" },
    preset10: { name: "Forest Walk", bassColor: "#228b22", midColor: "#6b8e23", trebleColor: "#9acd32", beatSensitivity: 0.6, animSpeed: 1.1, visualizationStyle: "particles" },
    preset11: { name: "Cyberpunk", bassColor: "#ff00ff", midColor: "#00ffff", trebleColor: "#ff1493", beatSensitivity: 0.85, animSpeed: 1.8, visualizationStyle: "bars" },
    preset12: { name: "Calm Waters", bassColor: "#4682b4", midColor: "#5f9ea0", trebleColor: "#b0c4de", beatSensitivity: 0.4, animSpeed: 0.9, visualizationStyle: "lines" },
    preset13: { name: "Firestorm", bassColor: "#ff4500", midColor: "#ff6347", trebleColor: "#ff7f50", beatSensitivity: 0.95, animSpeed: 2.5, visualizationStyle: "spheres" },
    preset14: { name: "Pastel Dream", bassColor: "#ffd1dc", midColor: "#c1e1c1", trebleColor: "#add8e6", beatSensitivity: 0.3, animSpeed: 0.6, visualizationStyle: "particles" },
    preset15: { name: "Metallic", bassColor: "#b0b0b0", midColor: "#d3d3d3", trebleColor: "#f0f0f0", beatSensitivity: 0.7, animSpeed: 1.3, visualizationStyle: "bars" },
    preset16: { name: "Rainbow", bassColor: "#ff0000", midColor: "#00ff00", trebleColor: "#0000ff", beatSensitivity: 0.8, animSpeed: 1.7, visualizationStyle: "lines" },
    preset17: { name: "Mystic", bassColor: "#4b0082", midColor: "#8a2be2", trebleColor: "#dda0dd", beatSensitivity: 0.6, animSpeed: 1.0, visualizationStyle: "particles" },
    preset18: { name: "Sunrise", bassColor: "#ff4500", midColor: "#ff8c00", trebleColor: "#ffd700", beatSensitivity: 0.7, animSpeed: 1.4, visualizationStyle: "spheres" },
    preset19: { name: "Ice", bassColor: "#00ffff", midColor: "#afeeee", trebleColor: "#e0ffff", beatSensitivity: 0.5, animSpeed: 1.1, visualizationStyle: "bars" },
    preset20: { name: "Jungle", bassColor: "#006400", midColor: "#228b22", trebleColor: "#32cd32", beatSensitivity: 0.65, animSpeed: 1.2, visualizationStyle: "lines" },
    preset21: { name: "Neon Lights", bassColor: "#39ff14", midColor: "#ff073a", trebleColor: "#0ff0fc", beatSensitivity: 0.9, animSpeed: 2.0, visualizationStyle: "particles" },
    preset22: { name: "Vintage", bassColor: "#a0522d", midColor: "#cd853f", trebleColor: "#deb887", beatSensitivity: 0.4, animSpeed: 0.8, visualizationStyle: "spheres" }
  };

  // Vul dropdown met presets
  Object.entries(presets).forEach(([key, preset]) => {
    const option = document.createElement('option');
    option.value = key;
    option.textContent = preset.name;
    presetSelect.appendChild(option);
  });

  // Audio context setup
  const audioContext = new (window.AudioContext || window.webkitAudioContext)();
  let source = null;
  const analyser = audioContext.createAnalyser();
  analyser.fftSize = 1024;
  const freqData = new Uint8Array(analyser.frequencyBinCount);

  // Three.js setup
  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(75, window.innerWidth/window.innerHeight, 0.1, 100);
  camera.position.z = 25;
  camera.position.y = 2;

  const renderer = new THREE.WebGLRenderer({
    alpha: true,  // Enables transparent background
    antialias: true  // Optional, for smoother edges
  });
  renderer.setClearColor(0x000000, 0);  // Clear to transparent black (alpha 0)
  renderer.setSize(500, 750);

  // Add OrbitControls
  const controls = new OrbitControls(camera, renderer.domElement);
  controls.enableDamping = true; // Smooth orbiting
  controls.dampingFactor = 0.05;
  controls.screenSpacePanning = false; // Pan only in screen space
  controls.minDistance = 1; // Min zoom
  controls.maxDistance = 500; // Max zoom
  controls.maxPolarAngle = Math.PI / 2; // Restrict vertical rotation (optional, for horizon view)

  $('#visualizer').append(renderer.domElement);

  // Licht
  //const ambientLight = new THREE.AmbientLight( 0xaaaaaa, 100);
  //scene.add(ambientLight);

  const light = new THREE.PointLight(0xffffff, 1);
  light.position.set(5,5,5);
  scene.add(light);
  const light2 = new THREE.PointLight(0xffffff, 1);
  light2.position.set(-5,5,5);
  scene.add(light2);
  const light1a = new THREE.PointLight(0xffffff, 1);
  light1a.position.set(5,10,5);
  scene.add(light1a);
  const light2a = new THREE.PointLight(0xffffff, 1);
  light2a.position.set(-5,10,5);
  scene.add(light2a);
  const light3 = new THREE.PointLight(0xffffff, 1);
  light.position.set(0,5,-5);
  scene.add(light3);

  // Visualisatie objecten containers
  let bassObjects = [];
  let midObjects = [];
  let trebleObjects = [];

  // Parameters
  let beatSensitivity = parseFloat(beatSensitivityInput.value);
  let animSpeed = parseFloat(animSpeedInput.value);
  let pulseActive = false;
  let pulseElapsed = 0;
  const pulseDuration = 0.3;

  // Aubio beatdetectie
  const bufferSize = 512;
  //await typeof aubio == 'function';//();
  const aubio = await Aubio();
  const tempo = new aubio.Tempo(/*"default", */bufferSize, bufferSize/2, audioContext.sampleRate);

  // ScriptProcessor voor beatdetectie

  /*const processor = audioContext.createScriptProcessor(bufferSize, 1, 1);
  processor.connect(audioContext.destination);

  processor.onaudioprocess = (event) => {
    const inputBuffer = event.inputBuffer.getChannelData(0);
    const isBeat = tempo.do(inputBuffer);
    if (isBeat && Math.random() < beatSensitivity) {
      pulseActive = true;
      pulseElapsed = 0;
    }
  };
  */
  //const audioContext = new (window.AudioContext || window.webkitAudioContext)();

  // Wait for module to load (important!)
  await audioContext.audioWorklet.addModule('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.javascriptRendering/beatPulse-0.1.0/beat.processor.js');  // path to your file

  const source2 = audioContext.createMediaElementSource(audioElement); // or decodeAudioData
  //const analyser = audioContext.createAnalyser();
  analyser.fftSize = 2048; // still useful for frequency data in main thread

  const workletNode = new AudioWorkletNode(audioContext, 'beat-pulse-processor', {
    numberOfInputs: 1,   // or 2 if stereo processing needed
    numberOfOutputs: 1,
    outputChannelCount: [2] // stereo out
  });

  // Receive beat events from worklet
  workletNode.port.onmessage = (e) => {
    if (e.data.type === 'beat') {
      triggerBeatPulseAnimation(e.data.energy); // your visualizer function
    }
  };

  // Optional: send params to worklet (e.g. sensitivity slider)
  // workletNode.port.postMessage({ threshold: 0.22 });

  source2.connect(analyser);
  analyser.connect(workletNode);
  workletNode.connect(audioContext.destination);

  // load audio file passed in from PHP's (the browser's) window.top
  const file = '<?php echo $_GET['file'];?>';
//debugger;
  if (!file) return;
  //const data = await fetch(file);
  const myRequest = new Request(file);

  await fetch(myRequest)
      .then((response) => {
        if (!response.ok) {
          throw new Error(`HTTP error, status = ${response.status}`);
        }
        return response.arrayBuffer();
      })
      .then((arrayBuffer) => {
        if (source) {
          source.disconnect();
        }
        return audioContext.decodeAudioData(arrayBuffer).then((audioBuffer) => {
          source = audioContext.createBufferSource();
          source.buffer = audioBuffer;
          source.connect(analyser);
          analyser.connect(processor);
          source.connect(audioContext.destination);
          debugger;
          source.start(<?php echo $_GET['time']?>);
          source.volume = 1;
          /*source.id = 'audio';
          $('body').append(source);*/
        });
      });



  // Helper: maak materiaal met kleur
  function createMaterial(color) {
    return new THREE.MeshStandardMaterial({color});
  }

  // Helper: verwijder objecten uit scene en arrays
  function clearObjects() {
    [...bassObjects, ...midObjects, ...trebleObjects].forEach(obj => {
      scene.remove(obj);
      if (obj.geometry) obj.geometry.dispose();
      if (obj.material) obj.material.dispose();
    });
    bassObjects = [];
    midObjects = [];
    trebleObjects = [];
  }

  // Visualisatie stijlen implementaties

  // 1. Bollen
  function createSpheres() {
    clearObjects();
    const geometry = new THREE.SphereGeometry(0.8, 32, 32);
    const bassMat = createMaterial(bassColorInput.value);
    const midMat = createMaterial(midColorInput.value);
    const trebleMat = createMaterial(trebleColorInput.value);

    const bassSphere = new THREE.Mesh(geometry, bassMat);
    bassSphere.position.x = -3;
    scene.add(bassSphere);
    bassObjects.push(bassSphere);

    const midSphere = new THREE.Mesh(geometry, midMat);
    midSphere.position.x = 0;
    scene.add(midSphere);
    midObjects.push(midSphere);

    const trebleSphere = new THREE.Mesh(geometry, trebleMat);
    trebleSphere.position.x = 3;
    scene.add(trebleSphere);
    trebleObjects.push(trebleSphere);
  }

  // 2. Bars
  function createBars() {
    clearObjects();
    const bassGeom = new THREE.BoxGeometry(1, 1, 1);
    const midGeom = new THREE.BoxGeometry(1, 1, 1);
    const trebleGeom = new THREE.BoxGeometry(1, 1, 1);

    const bassMat = createMaterial(bassColorInput.value);
    const midMat = createMaterial(midColorInput.value);
    const trebleMat = createMaterial(trebleColorInput.value);

    for(let i = 0; i < 10; i++) {
      const bassBar = new THREE.Mesh(bassGeom, bassMat);
      bassBar.position.set(-5 + i*1.2, 0, -10);
      scene.add(bassBar);
      bassObjects.push(bassBar);

      const midBar = new THREE.Mesh(midGeom, midMat);
      midBar.position.set(-5 + i*1.2, 0, -7);
      scene.add(midBar);
      midObjects.push(midBar);

      const trebleBar = new THREE.Mesh(trebleGeom, trebleMat);
      trebleBar.position.set(-5 + i*1.2, 0, -4);
      scene.add(trebleBar);
      trebleObjects.push(trebleBar);
    }
  }

  // 3. Lijnen
  function createLines() {
    clearObjects();

    function createLine(color, yOffset) {
      const points = [];
      for(let i = 0; i < 32; i++) {
        points.push(new THREE.Vector3(i * 0.3 - 5, yOffset, 0));
      }
      const geometry = new THREE.BufferGeometry().setFromPoints(points);
      const material = new THREE.LineBasicMaterial({color});
      const line = new THREE.Line(geometry, material);
      scene.add(line);
      return line;
    }

    bassObjects.push(createLine(bassColorInput.value, -2));
    midObjects.push(createLine(midColorInput.value, 0));
    trebleObjects.push(createLine(trebleColorInput.value, 2));
  }

  // 4. Partikels
  function createParticles() {
    clearObjects();

    function createParticleSystem(color, yOffset) {
      const particleCount = 300;
      const positions = new Float32Array(particleCount * 3);
      for(let i = 0; i < particleCount; i++) {
        positions[i*3] = (Math.random() - 0.5) * 10;
        positions[i*3 + 1] = yOffset + (Math.random() - 0.5) * 2;
        positions[i*3 + 2] = (Math.random() - 0.5) * 10;
      }
      const geometry = new THREE.BufferGeometry();
      geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
      const material = new THREE.PointsMaterial({color, size: 0.2});
      const points = new THREE.Points(geometry, material);
      scene.add(points);
      return points;
    }

    bassObjects.push(createParticleSystem(bassColorInput.value, -150));
    midObjects.push(createParticleSystem(midColorInput.value, -100));
    trebleObjects.push(createParticleSystem(trebleColorInput.value, -50));
  }

  // Visualisatie stijl switch
  function setVisualizationStyle(style) {
    switch(style) {
      case "spheres": createSpheres(); break;
      case "bars": createBars(); break;
      case "lines": createLines(); break;
      case "particles": createParticles(); break;
      default: createSpheres();
    }
  }

  // Pas preset toe
  function applyPreset(name) {
    const preset = presets[name];
    if (!preset) return;
    bassColorInput.value = preset.bassColor;
    midColorInput.value = preset.midColor;
    trebleColorInput.value = preset.trebleColor;
    beatSensitivityInput.value = preset.beatSensitivity;
    animSpeedInput.value = preset.animSpeed;
    visualizationStyleSelect.value = preset.visualizationStyle;

    beatSensitivity = preset.beatSensitivity;
    animSpeed = preset.animSpeed;
    setVisualizationStyle(preset.visualizationStyle);
    updateColors();
  }

  // Event listeners UI
  presetSelect.addEventListener('change', e => applyPreset(e.target.value));
  visualizationStyleSelect.addEventListener('change', e => setVisualizationStyle(e.target.value));
  bassColorInput.addEventListener('input', () => updateColors());
  midColorInput.addEventListener('input', () => updateColors());
  trebleColorInput.addEventListener('input', () => updateColors());
  beatSensitivityInput.addEventListener('input', () => beatSensitivity = parseFloat(beatSensitivityInput.value));
  animSpeedInput.addEventListener('input', () => animSpeed = parseFloat(animSpeedInput.value));

  // Update kleuren van materialen
  function updateColors() {
    bassObjects.forEach(obj => { if(obj.material) obj.material.color.set(bassColorInput.value); });
    midObjects.forEach(obj => { if(obj.material) obj.material.color.set(midColorInput.value); });
    trebleObjects.forEach(obj => { if(obj.material) obj.material.color.set(trebleColorInput.value); });
  }

  // Audio context resume op user interaction
  document.body.addEventListener('click', () => {
    if (audioContext.state === 'suspended') {
      audioContext.resume();
    }
  }, {once: true});

  // Analyse frequentiebanden helper
  function getAverageEnergy(startBin, endBin) {
    let sum = 0;
    let count = 0;
    for(let i = startBin; i <= endBin; i++) {
      sum += freqData[i];
      count++;
    }
    return count > 0 ? sum / count : 0;
  }

  // Animatie loop
  function animate() {
    requestAnimationFrame(animate);

    analyser.getByteFrequencyData(freqData);

    const bassEnergy = getAverageEnergy(1, 12) / 255;
    const midEnergy = getAverageEnergy(13, 93) / 255;
    const trebleEnergy = getAverageEnergy(94, 255) / 255;

    // Animaties per stijl
    switch(visualizationStyleSelect.value) {
      case "spheres":
        bassObjects.forEach(s => {
          s.scale.setScalar(1 + bassEnergy * 1.5 * animSpeed);
          s.rotation.y += bassEnergy * 0.05 * animSpeed;
        });
        midObjects.forEach(s => {
          s.scale.setScalar(1 + midEnergy * 1.2 * animSpeed);
          s.rotation.y += midEnergy * 0.05 * animSpeed;
        });
        trebleObjects.forEach(s => {
          s.scale.setScalar(1 + trebleEnergy * 1.0 * animSpeed);
          s.rotation.x += trebleEnergy * 0.1 * animSpeed;
          s.rotation.z += trebleEnergy * 0.1 * animSpeed;
        });
        break;

      case "bars":
        bassObjects.forEach((bar, i) => {
          bar.scale.y = Math.max(0.1, bassEnergy * 10 * animSpeed);
          bar.position.y = bar.scale.y / 2;
        });
        midObjects.forEach((bar, i) => {
          bar.scale.y = Math.max(0.1, midEnergy * 10 * animSpeed);
          bar.position.y = bar.scale.y / 2;
        });
        trebleObjects.forEach((bar, i) => {
          bar.scale.y = Math.max(0.1, trebleEnergy * 10 * animSpeed);
          bar.position.y = bar.scale.y / 2;
        });
        break;

      case "lines":
        function updateLine(line, energy) {
          const positions = line.geometry.attributes.position.array;
          for(let i = 0; i < positions.length / 3; i++) {
            positions[i*3 + 1] = energy * 5 * Math.sin(i + performance.now() * 0.005);
          }
          line.geometry.attributes.position.needsUpdate = true;
        }
        bassObjects.forEach(line => updateLine(line, bassEnergy * animSpeed));
        midObjects.forEach(line => updateLine(line, midEnergy * animSpeed));
        trebleObjects.forEach(line => updateLine(line, trebleEnergy * animSpeed));
        break;

      case "particles":
        function updateParticles(points, energy) {
          const positions = points.geometry.attributes.position.array;
          for(let i = 0; i < positions.length / 3; i++) {
            positions[i*3 + 1] += (Math.random() - 0.5) * energy * 0.1 * animSpeed;
            if (positions[i*3 + 1] > 3) positions[i*3 + 1] = -3;
            if (positions[i*3 + 1] < -3) positions[i*3 + 1] = 3;
          }
          points.geometry.attributes.position.needsUpdate = true;
        }
        bassObjects.forEach(points => updateParticles(points, bassEnergy * animSpeed));
        midObjects.forEach(points => updateParticles(points, midEnergy * animSpeed));
        trebleObjects.forEach(points => updateParticles(points, trebleEnergy * animSpeed));
        break;
    }

    // Beat pulsatie (alle stijlen)
    if (pulseActive) {
      pulseElapsed += 0.016 * animSpeed;
      const pulse = 1;//1 + 0.5 * Math.sin((pulseElapsed / pulseDuration) * Math.PI);
      [...bassObjects, ...midObjects, ...trebleObjects].forEach(obj => {
        obj.scale.multiplyScalar(pulse);
      });
      if (pulseElapsed >= pulseDuration) {
        pulseActive = false;
        pulseElapsed = 0;
      }
    }

    renderer.render(scene, camera);
  }

  // Initialiseer met eerste preset
  applyPreset('preset1');

  animate();

};
debugger; initApp();
//});
</script>
<!--
</body>
</html>
-->
