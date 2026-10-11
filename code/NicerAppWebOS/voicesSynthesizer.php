<?php
// voicestudio.php – Local VoiceStudio frontend with automatic saving

$saveDir = __DIR__ . '/synthesizedVoices';
if (!is_dir($saveDir)) {
    mkdir($saveDir, 0755, true);
}

// ---------- API proxy + file saving ----------
if (isset($_GET['action'])) {

    if ($_GET['action'] === 'voices') {
        header('Content-Type: application/json');
        $ch = curl_init('http://127.0.0.1:3900/v1/audio/voices');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        http_response_code($httpCode ?: 500);
        echo $response ?: json_encode(['error' => 'Could not reach VoiceStudio']);
        exit;
    }

    if ($_GET['action'] === 'speech' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['input']) || empty($input['voice'])) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Missing input or voice']);
            exit;
        }

        $text  = trim($input['input']);
        $voice = $input['voice'];

        $payload = [
            'model'           => $input['model'] ?? 'tts-1',
            'voice'           => $voice,
            'input'           => $text,
            'response_format' => 'mp3',          // ← we now request mp3
            'language'        => $input['language'] ?? 'nl',
        ];

        $ch = curl_init('http://127.0.0.1:3900/v1/audio/speech');
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
                          CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_TIMEOUT        => 180,
        ]);
        $audio = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300 || !$audio) {
            http_response_code($httpCode ?: 500);
            header('Content-Type: application/json');
            echo $audio ?: json_encode(['error' => 'Synthesis failed']);
            exit;
        }

        // ---------- Save to disk ----------
        $safeVoice = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $voice);
        $safeText  = preg_replace('/[^a-zA-Z0-9_\- ]/', '', $text);
        $safeText  = preg_replace('/\s+/', '_', trim($safeText));
        $safeText  = mb_substr($safeText, 0, 60);          // limit length
        if ($safeText === '') $safeText = 'audio';

        $filename = $safeVoice . '-' . $safeText . '.mp3';
        $filepath = $saveDir . '/' . $filename;

        // Avoid overwriting: add timestamp if file already exists
        if (file_exists($filepath)) {
            $filename = $safeVoice . '-' . $safeText . '-' . date('Ymd_His') . '.mp3';
            $filepath = $saveDir . '/' . $filename;
        }

        file_put_contents($filepath, $audio);

        // Return the audio + the saved filename
        header('Content-Type: ' . ($contentType ?: 'audio/mpeg'));
        header('X-Saved-As: ' . $filename);                 // custom header
        header('Content-Disposition: inline; filename="' . $filename . '"');
        echo $audio;
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>VoiceStudio – Local TTS</title>
<style>
:root {
    --bg: #0f1117;
    --card: #1a1d27;
    --border: #2a2e3b;
    --text: #e4e6ed;
    --muted: #9ca3af;
    --accent: #6366f1;
    --accent-hover: #818cf8;
    --success: #22c55e;
    --danger: #ef4444;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
    background: var(--bg);
    color: var(--text);
    line-height: 1.5;
    min-height: 100vh;
    padding: 2rem 1rem;
}
.container { max-width: 960px; margin: 0 auto; }
h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.25rem; }
.subtitle { color: var(--muted); margin-bottom: 2rem; font-size: 0.95rem; }
.card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
label { display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem; }
textarea {
    width: 100%;
    min-height: 120px;
    background: #12141c;
    border: 1px solid var(--border);
    border-radius: 8px;
    color: var(--text);
    padding: 0.85rem 1rem;
    font-size: 1rem;
    resize: vertical;
    font-family: inherit;
}
textarea:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(99,102,241,0.25);
}
.controls {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 1rem;
    align-items: flex-end;
}
select, button {
    font: inherit;
    border-radius: 8px;
    cursor: pointer;
}
select {
    background: #12141c;
    border: 1px solid var(--border);
    color: var(--text);
    padding: 0.55rem 0.9rem;
    min-width: 180px;
}
button {
    background: var(--accent);
    color: white;
    border: none;
    padding: 0.6rem 1.25rem;
    font-weight: 600;
    transition: background 0.15s;
}
button:hover:not(:disabled) { background: var(--accent-hover); }
button:disabled { opacity: 0.5; cursor: not-allowed; }
button.secondary {
    background: transparent;
    border: 1px solid var(--border);
    color: var(--text);
}
button.secondary:hover:not(:disabled) {
    border-color: var(--accent);
    color: var(--accent);
}
.status {
    margin-top: 1rem;
    font-size: 0.9rem;
    color: var(--muted);
    min-height: 1.4em;
}
.status.error { color: var(--danger); }
.status.success { color: var(--success); }
.voices-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 0.85rem;
    margin-top: 1rem;
}
.voice-card {
    background: #12141c;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 0.9rem 1rem;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}
.voice-card strong { font-size: 0.95rem; }
.voice-card small { color: var(--muted); font-size: 0.8rem; }
.voice-card button {
    margin-top: 0.45rem;
    padding: 0.4rem 0.75rem;
    font-size: 0.85rem;
    width: 100%;
}
audio { width: 100%; margin-top: 0.5rem; border-radius: 6px; }
.saved-as {
    font-size: 0.78rem;
    color: var(--success);
    word-break: break-all;
    margin-top: 0.3rem;
}
.loading {
    display: inline-block;
    width: 1em; height: 1em;
    border: 2px solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    vertical-align: -0.15em;
    margin-right: 0.4em;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>
<body>
<div class="container">
<h1>VoiceStudio Local TTS</h1>
<p class="subtitle">
Files are automatically saved as <code>[voice]-[text].mp3</code> in the <code>generated/</code> folder
</p>

<div class="card">
<label for="text">Text to speak</label>
<textarea id="text" placeholder="Typ hier je tekst…">Hallo, dit is een test met VoiceStudio op de RTX 3050.</textarea>

<div class="controls">
<div>
<label for="language" style="margin-bottom:0.3rem">Language</label>
<select id="language">
<option value="nl" selected>Dutch (nl)</option>
<option value="en">English (en)</option>
<option value="de">German (de)</option>
<option value="fr">French (fr)</option>
<option value="es">Spanish (es)</option>
<option value="auto">Auto</option>
</select>
</div>
<button id="btn-all" class="secondary">Generate for ALL voices</button>
<button id="btn-refresh">Refresh voice list</button>
</div>
<div id="status" class="status">Loading voices…</div>
</div>

<div class="card">
<h2 style="font-size:1.15rem; margin-bottom:0.75rem">Available voices</h2>
<div id="voices" class="voices-list"></div>
</div>
</div>

<script>
const statusEl   = document.getElementById('status');
const voicesEl   = document.getElementById('voices');
const textEl     = document.getElementById('text');
const langEl     = document.getElementById('language');
const btnAll     = document.getElementById('btn-all');
const btnRefresh = document.getElementById('btn-refresh');

let voices = [];

function setStatus(msg, type = '') {
    statusEl.textContent = msg;
    statusEl.className = 'status' + (type ? ' ' + type : '');
}

async function loadVoices() {
    setStatus('Loading voices…');
    voicesEl.innerHTML = '';
    try {
        const res = await fetch('?action=voices');
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const data = await res.json();
        voices = Array.isArray(data) ? data : (data.data || data.voices || []);
        if (!voices.length) {
            setStatus('No voices found', 'error');
            return;
        }
        setStatus(`${voices.length} voice(s) loaded`, 'success');
        renderVoices();
    } catch (err) {
        setStatus('Failed to load voices: ' + err.message, 'error');
        console.error(err);
    }
}

function renderVoices() {
    voicesEl.innerHTML = '';
    voices.forEach(v => {
        const id   = v.id || v.voice_id || v.name || 'unknown';
    const name = v.name || id;
    const desc = v.description || v.labels?.description || '';
    const lang = v.language || v.labels?.language || '';

    const card = document.createElement('div');
    card.className = 'voice-card';
    card.innerHTML = `
    <strong>${escapeHtml(name)}</strong>
    <small>${escapeHtml(id)}${lang ? ' · ' + escapeHtml(lang) : ''}</small>
    ${desc ? `<small>${escapeHtml(desc)}</small>` : ''}
    <button data-voice="${escapeHtml(id)}">Generate</button>
    <div class="audio-slot"></div>
    `;
    card.querySelector('button').addEventListener('click', () => generateOne(id, card));
    voicesEl.appendChild(card);
    });
}

async function generateOne(voiceId, cardEl) {
    const text = textEl.value.trim();
    if (!text) {
        setStatus('Please enter some text first', 'error');
        return;
    }

    const btn  = cardEl.querySelector('button');
    const slot = cardEl.querySelector('.audio-slot');
    btn.disabled = true;
    btn.innerHTML = '<span class="loading"></span> Generating…';
    slot.innerHTML = '';

    try {
        const res = await fetch('?action=speech', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                input: text,
                voice: voiceId,
                language: langEl.value,
                response_format: 'mp3'
            })
        });

        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            throw new Error(err.error || err.detail || ('HTTP ' + res.status));
        }

        const blob = await res.blob();
        const url  = URL.createObjectURL(blob);
        const savedAs = res.headers.get('X-Saved-As') || '(unknown)';

        slot.innerHTML = `
        <audio controls src="${url}"></audio>
        <div class="saved-as">Saved as: ${escapeHtml(savedAs)}</div>
        `;
        setStatus(`Generated & saved: ${savedAs}`, 'success');
    } catch (err) {
        setStatus('Error for ' + voiceId + ': ' + err.message, 'error');
        console.error(err);
    } finally {
        btn.disabled = false;
        btn.textContent = 'Generate';
    }
}

async function generateAll() {
    const text = textEl.value.trim();
    if (!text) {
        setStatus('Please enter some text first', 'error');
        return;
    }
    if (!voices.length) {
        setStatus('No voices loaded', 'error');
        return;
    }

    btnAll.disabled = true;
    btnAll.innerHTML = '<span class="loading"></span> Generating all…';

    const cards = [...document.querySelectorAll('.voice-card')];
    for (let i = 0; i < voices.length; i++) {
        const v = voices[i];
        const id = v.id || v.voice_id || v.name;
        setStatus(`Generating ${i+1}/${voices.length}: ${id}…`);
        await generateOne(id, cards[i]);
        await new Promise(r => setTimeout(r, 400));
    }

    btnAll.disabled = false;
    btnAll.textContent = 'Generate for ALL voices';
    setStatus('Finished – all files saved in generated/', 'success');
}

function escapeHtml(str) {
    return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

btnRefresh.addEventListener('click', loadVoices);
btnAll.addEventListener('click', generateAll);
loadVoices();
</script>
</body>
</html>
