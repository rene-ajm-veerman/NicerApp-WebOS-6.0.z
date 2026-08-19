# Heated Water Electrical Powerplant – Interactive 3D Map

A lightweight, single-file Three.js demo inspired by industrial SCADA / Viewer3D-style machinery maps.

## What you get
- Procedural thermal / heated-water power plant layout
- Turbine hall, boiler block, cooling towers, steam drums, tanks, pipes (hot/cold), generator, transformer, control building, switchyard
- Orbit / pan / zoom controls
- Click any major equipment for status, temperature/flow and power readout
- Simple status coloring (OK / WARN / ALARM) with gentle pulse
- No build step, no bundler, pure ES modules from CDN

## How to run
1. Unzip the archive.
2. Open `index.html` directly in a modern browser  
   **or** serve the folder with any static server, e.g.:
   ```bash
   npx serve .
   # or
   python -m http.server 8080
   ```
3. Click equipment to inspect. Left-drag orbit, right-drag pan, wheel zoom.

## Next steps you might want
- Replace procedural meshes with real GLB models of turbines / heat exchangers
- Wire live sensor data (WebSocket / MQTT) into the `userData` and materials
- Add more detailed pipe routing or animated flow particles
- Layer a 2D schematic toggle on top of the 3D view

Built as a quick starting point for digital-twin / plant-map experiments.
