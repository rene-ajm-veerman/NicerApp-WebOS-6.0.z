<div id="placesPanel" class="vividPanel" style="width: 240px; height: 100%; overflow-y: auto; padding: 8px; box-sizing: border-box;">
<h3 style="margin: 8px 0 12px 0; padding-left: 8px;">Places</h3>

<div class="places-section">
<div class="places-header">Accreditions</div>
<ul id="places-locations" class="places-list">
<li class="place-item"><a href="https://afraid.org">Afraid.org Free DNS</a></li>
<li class="place-item"><a href="https://grok.com">Grok.com AI</a></li>
<li class="place-item"><a href="https://claude.ai/chat">Claude AI</a></li>
</ul>
</div>

<div class="places-section">
<div class="places-header">View</div>
<ul id="places-locations" class="places-list">
<li class="place-item" onclick="na.threed.switchView(event)"><input type="checkbox" id="checkbox_defaultView" class="checkbox_naThreeD_viewType" onclick="na.threed.switchView(event)" checked>Default</input></li>
<li class="place-item" onclick="na.threed.switchView(event)"><input type="checkbox" id="checkbox_FibonnaciView" class="checkbox_naThreeD_viewType" onclick="na.threed.switchView(event)">Fibonacci</input></li>
<li class="place-item" onclick="na.threed.switchView(event)"><input type="checkbox" id="checkbox_treeView" class="checkbox_naThreeD_viewType" onclick="na.threed.switchView(event)">Tree</input></li>
<li class="place-item" onclick="na.threed.switchView(event)"><input type="checkbox" id="checkbox_bushView" class="checkbox_naThreeD_viewType" onclick="na.threed.switchView(event)">Bush</input></li>
</ul>
</div>


<div class="places-section">
<div class="places-header">Favorites</div>
<ul id="places-favorites" class="places-list"></ul>
</div>

<div class="places-section">
<div class="places-header">Locations</div>
<ul id="places-locations" class="places-list">
<li data-path="/" class="place-item">🖥️ Root</li>
<li data-path="/music" class="place-item">🎵 Music</li>
<li data-path="/photos" class="place-item">🖼️ Pictures</li>
<li data-path="/videos" class="place-item">🎥 Videos</li>
<li data-path="/documents" class="place-item">📄 Documents</li>
</ul>
</div>

<div class="places-section">
<div class="places-header">Recent</div>
<ul id="places-recent" class="places-list"></ul>
</div>

<div id="historyControls" class="toolbar-section">
    <h4>History</h4>
    <div id="historyList" class="history-list"></div>
    <div class="history-buttons" style="margin: 10px 0;">
    <button id="btnUndo" title="Undo (Ctrl+Z)" disabled>↩ Undo</button>
    <button id="btnRedo" title="Redo (Ctrl+Shift+Z)" disabled>↪ Redo</button>
    </div>
    <div id="historyStatus" class="history-status"></div>
</div>
</div>
<style>
.places-section { margin-bottom: 16px; }
.places-header {
    font-weight: bold;
    padding: 4px 8px;
    color: #aaa;
    font-size: 0.9em;
    text-transform: uppercase;
}
.places-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.places-list li {
    padding: 6px 8px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s;
}
.places-list li:hover {
    background: rgba(255,255,255,0.1);
}
.place-item.active {
    background: rgba(0, 120, 255, 0.3);
    color: white;
}
#historyControls {
margin-top: 16px;
padding: 8px 12px;
border-top: 1px solid #333;
}

.history-buttons {
    display: flex;
    gap: 8px;
    margin-bottom: 6px;
}

.history-buttons button {
    flex: 1;
    padding: 6px 10px;
    font-size: 0.9rem;
}

.history-status {
    font-size: 0.8rem;
    color: #888;
    text-align: center;
}
</style>
<script type="text/javascript">
    na.desktop.settings.visibleDivs.push('#siteToolbarLeft');
    na.desktop.resize();



    // ==================== UNDO / REDO SYSTEM ====================

    let history = {
        stack: [],      // past + current
        redoStack: []
    };
    let historyIndex = -1;
    const MAX_HISTORY = 50;

        function restoreState(state) {
        if (!state) return;

        // Restore scroll
        if (typeof state.scrollY === 'number') {
            window.scrollTo({ top: state.scrollY, behavior: 'auto' });
        }

        // Restore camera
        if (state.camera && window.currentCamera) {
            //delete state.camera.rotation;

            if (state.camera) {
                const cam = window.currentCamera;
                const pos = state.camera.pos;
                const forward = window.getForwardVector();
                const delta = forward.multiplyScalar(14);

                if (
                    typeof state.camera.x === 'number'
                    && typeof state.camera.y === 'number'
                    && typeof state.camera.z === 'number'
                ) {
                    window.threed.graph.cameraPosition(
                        window.threed.currentNode,
                        {
                            x: ((pos.lookAt?.x ?? 0) + delta.x),
                                                       y: ((pos.lookAt?.y ?? 0) + delta.y),
                                                       z: ((pos.lookAt?.z ?? 0) + delta.z)
                        },
                        100
                    );
                    window.threed.graph.cameraPosition(
                        state.camera.pos,
                        {
                            x: ((pos.lookAt?.x ?? 0) + delta.x),
                            y: ((pos.lookAt?.y ?? 0) + delta.y),
                            z: ((pos.lookAt?.z ?? 0) + delta.z)
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
            if (activeEl) activeEl.classLisadd('active');
        }
    }

    function updateHistoryUI () {
        const t = this;

        // === 1. Update History List ===
        const listContainer = document.getElementById('historyList');
        if (listContainer) {
            listContainer.innerHTML = '';

            if (history.stack.length === 0) {
                const empty = document.createElement('div');
                empty.className = 'history-empty';
                empty.textContent = '(no history yet)';
                empty.style.fontStyle = 'italic';
                empty.style.color = '#888';
                listContainer.appendChild(empty);
            } else {
                // Show history items (newest at top)
                for (let i = history.stack.length - 1; i >= 0; i--) {
                    const state = history.stack[i];
                    const item = document.createElement('div');
                    item.className = 'history-item';
                    item.style.cursor = 'pointer';
                    item.style.padding = '6px 8px';
                    item.style.borderRadius = '4px';
                    item.style.marginBottom = '3px';

                    // Highlight current position
                    if (i === history.stack.length - 1) {
                        item.style.backgroundColor = 'rgba(0, 255, 120, 0.2)';
                        item.style.fontWeight = 'bold';
                    }

                    const label = state.d|| state.action || `History ${i + 1}`;

                    item.innerHTML = `<small style="color:#999;">${i+1}.</small> ${label}`;

                    item.onclick = () => jumpToHistory(i);
                    listContainer.appendChild(item);
                }
            }
        }

        // === 2. Enable / Disable Undo & Redo Buttons ===
        const btnUndo = document.getElementById('btnUndo');
        const btnRedo = document.getElementById('btnRedo');

        if (btnUndo) {
            btnUndo.disabled = history.stack.length <= 1;   // can't undo if only initial state
            // Optional: visual feedback
            btnUndo.style.opacity = btnUndo.disabled ? '0.4' : '1';
        }

        if (btnRedo) {
            btnRedo.disabled = !history.redoStack || history.redoStack.length === 0;
            btnRedo.style.opacity = btnRedo.disabled ? '0.4' : '1';
        }
    }


    function jumpToHistory (index) {
        const t = this;

        if (index < 0 || index >= history.stack.length) return;

        // Move all states after the selected one to redoStack
        const currentState = history[index];

        history.redoStack = history.redoStack.splice(index + 1).reverse();
        history.stack = history.stack.slice(0, index + 1);

        restoreState(currentState);
        updateHistoryUI();
    }

    function pushHistory (state, label = '') {
        const t = this;

        if (!history) {
            history = { stack: [], redoStack: [] };
        }

        // Clear redo stack when new action happens
        history.redoStack = [];

        // Add label if provided
        if (label) state.label = label;

        history.stack.push(state);

        // Optional: limit history size to prevent memory bloat
        if (history.stack.length > 50) {
            history.stack.shift();
        }

        updateHistoryUI();
    };

    // ====================== UNDO ======================
    function undo () {
        if (!history || history.stack.length <= 1) return false;

        // Take current state and move it to redoStack
        const currentState = history.stack.pop();
        history.redoStack.push(currentState);

        // Restore the new current state
        const previousState = history.stack[history.stack.length - 1];
        restoreState(previousState);

        updateHistoryUI();
        return true;
    };

    // ====================== REDO ======================
    function redo () {
        if (!history || history.redoStack.length === 0) return false;

        // Take state from redoStack and push it back to stack
        const nextState = history.redoStack.pop();
        history.stack.push(nextState);

        restoreState(nextState);

        updateHistoryUI();
        return true;
    };

    // ==================== BUTTONS & KEYBOARD ====================
    function initHistoryControls() {
        document.addEventListener('click', (e) => {
            if (e.targeid === 'btnUndo') undo();
            if (e.targeid === 'btnRedo') redo();
        });

            // keyboard shortcuts stay the same
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
                    e.preventDefault();
                    if (e.shiftKey) redo(); else undo();
                } else if ((e.ctrlKey || e.metaKey) && e.key === 'y') {
                    e.preventDefault();
                    redo();
                }
            });
    }

    // Expose for easy calling from onclick_node() etc.
    window.undo = undo;
    window.redo = redo;

    // Initialize everything
    function initUndoRedo() {
        initHistoryControls();

        // Save initial state
        setTimeout(() => {
            $('#siteToolbarLeft').css({width:'fit-content'});
            pushHistory("Initial state");
        }, 300);

        console.log("✅ Undo/Redo system initialized with working buttons");
    }

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initUndoRedo);
    } else {
        initUndoRedo();
    }

    // Expose for other modules
    window.historyManager = { pushHistory, undo, redo };
</script>
