<?php
/**
 * phpFlowcharts.class.php
 * Core class that turns a structured JSON flowchart definition into Mermaid / HTML / DOT
 *
 * Features:
 *  - Mermaid, HTML and Graphviz DOT output
 *  - "copy" key on nodes → clickable clipboard copy in the HTML version
 *  - Proper handling of multi-line labels and special characters
 */

declare(strict_types=1);

class phpFlowcharts
{
    private array $data;
    private string $title;
    private string $description;
    private string $direction;
    private array $nodes;
    private array $edges;

    public function __construct(string $jsonFile)
    {
        if (!is_readable($jsonFile)) {
            throw new InvalidArgumentException("Cannot read JSON file: {$jsonFile}");
        }

        $json = file_get_contents($jsonFile);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidArgumentException("Invalid JSON: " . json_last_error_msg());
        }

        if (empty($data['nodes']) || empty($data['edges'])) {
            throw new InvalidArgumentException("JSON must contain at least 'nodes' and 'edges'.");
        }

        $this->data        = $data;
        $this->title       = $data['meta']['title']       ?? 'Flowchart';
        $this->description = $data['meta']['description'] ?? '';
        $this->direction   = strtoupper($data['direction'] ?? 'TD');
        $this->nodes       = $data['nodes'];
        $this->edges       = $data['edges'];
    }

    public function toMermaid(): string
    {
        $lines = ["flowchart {$this->direction}"];

        // 1. Nodes first (no emoji inside labels for maximum compatibility)
        foreach ($this->nodes as $id => $node) {
            $type  = $node['type']  ?? 'process';
            $label = $node['label'] ?? $id;

            // Normalize newlines
            $label = str_replace(["\\n", "\r\n", "\r", "\n"], "\n", $label);

            $lines[] = "    " . $this->mermaidShape($type, $id, $label);
        }

        // 2. Edges
        foreach ($this->edges as $edge) {
            $from  = $edge['from'];
            $to    = $edge['to'];
            $label = isset($edge['label']) ? "|{$edge['label']}|" : '';
            $lines[] = "    {$from} -->{$label} {$to}";
        }

        // 3. classDef – solid colors (very reliable) + good contrast
        $lines[] = "    classDef startEnd fill:#16a34a,stroke:#4ade80,stroke-width:2px,color:#f0fdf4";
        $lines[] = "    classDef process fill:#2563eb,stroke:#93c5fd,stroke-width:1px,color:#eff6ff";
        $lines[] = "    classDef decision fill:#d97706,stroke:#fcd34d,stroke-width:2px,color:#fffbeb";
        $lines[] = "    classDef error fill:#dc2626,stroke:#fca5a5,stroke-width:2px,color:#fef2f2";
        $lines[] = "    classDef io fill:#9333ea,stroke:#d8b4fe,stroke-width:1px,color:#faf5ff";
        $lines[] = "    classDef subroutine fill:#0d9488,stroke:#5eead4,stroke-width:1px,color:#f0fdfa";
        $lines[] = "    classDef normal fill:#64748b,stroke:#cbd5e1,stroke-width:1px,color:#f8fafc";

        // 4. Apply classes
        $classMap = [
            'startEnd'   => [],
            'process'    => [],
            'decision'   => [],
            'error'      => [],
            'io'         => [],
            'subroutine' => [],
            'normal'     => [],
        ];

        foreach ($this->nodes as $id => $node) {
            $type = $node['type'] ?? 'process';
            $className = match ($type) {
                'start', 'end'  => 'startEnd',
                'decision'      => 'decision',
                'error'         => 'error',
                'io'            => 'io',
                'subroutine'    => 'subroutine',
                'process'       => 'process',
                default         => 'normal',
            };
            $classMap[$className][] = $id;
        }

        foreach ($classMap as $className => $ids) {
            if (!empty($ids)) {
                $lines[] = "    class " . implode(',', $ids) . " {$className}";
            }
        }

        // 5. Click handlers
        foreach ($this->nodes as $id => $node) {
            if (!empty($node['copy'])) {
                $lines[] = "    click {$id} call phpFlowchartsCopy(\"{$id}\") \"Click to copy command\"";
            }
        }

        return implode("\n", $lines);
    }

    /**
     * Generate Graphviz DOT source
     */
    public function toDot(): string
    {
        $rankdir = ($this->direction === 'LR') ? 'LR' : 'TB';
        $lines = [
            "digraph G {",
            "    rankdir={$rankdir};",
            "    node [fontname=\"Helvetica\"];"
        ];

        foreach ($this->nodes as $id => $node) {
            $label = str_replace(["\\n", "\r\n", "\r", "\n"], "\\n", $node['label'] ?? $id);
            if (!empty($node['copy'])) {
                $label .= " 📋";
            }
            $shape = match ($node['type'] ?? 'process') {
                'start', 'end' => 'ellipse',
                'decision'     => 'diamond',
                'error'        => 'octagon',
                default        => 'box',
            };
            $lines[] = "    {$id} [label=\"{$label}\", shape={$shape}];";
        }

        foreach ($this->edges as $edge) {
            $label = isset($edge['label']) ? " [label=\"{$edge['label']}\"]" : '';
            $lines[] = "    {$edge['from']} -> {$edge['to']}{$label};";
        }

        $lines[] = "}";
        return implode("\n", $lines);
    }

    /**
     * Generate a complete self-contained HTML page with clipboard support
     */
    public function toHtml(): string
    {
        $mermaidSource = $this->toMermaid();

        // Build the JS map of nodeId → text to copy
        $copyMap = [];
        foreach ($this->nodes as $id => $node) {
            if (!empty($node['copy'])) {
                $copyMap[$id] = $node['copy'];
            }
        }
        $copyMapJson = json_encode(
            $copyMap,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );

        // Escape only title & description for HTML safety.
        // Do NOT escape the Mermaid source – Mermaid needs the raw text.
        $title       = htmlspecialchars($this->title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $description = $this->description;

        return <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{$title}</title>
        <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
        <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            margin: 2rem;
            background: none;
            color: #e2e8f0;
        }
        h1 { margin-bottom: 0.25rem; }
        .desc { color: #94a3b8; margin-bottom: 1.5rem; max-width: 70ch; }
        .hint { color: #64748b; font-size: 0.9rem; margin-bottom: 2rem; }
        .mermaid {
            background: rgba(100,20,200,0.555);
            padding: 2rem;
            border-radius: 12px;
            overflow: auto;
        }
        #toast {
        visibility: hidden;
        min-width: 220px;
        background: #22c55e;
        color: #052e16;
        text-align: center;
        border-radius: 8px;
        padding: 14px 20px;
        position: fixed;
        z-index: 1000;
        left: 50%;
        bottom: 40px;
        transform: translateX(-50%);
        font-weight: 600;
        box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        opacity: 0;
        transition: opacity 0.3s, visibility 0.3s;
        }
        #toast.show {
        visibility: visible;
        opacity: 1;
        }
        /* Make copyable nodes stand out */
        .mermaid .node.clickable rect,
        .mermaid .node.clickable .label-container {
            stroke-dasharray: 6 3 !important;
            stroke-width: 2px !important;
        }
        </style>
        </head>
        <body>
        <h1>{$title}</h1>
        <p class="desc">{$description}</p>
        <p class="hint">
        <strong>Tip:</strong> Nodes with a dashed border are clickable — click them to copy the command to your clipboard.
        </p>

        <div class="mermaid">
        {$mermaidSource}
        </div>

        <div id="toast">Copied to clipboard!</div>

        <script>
        window.phpFlowchartsCopyMap = {$copyMapJson};

        window.phpFlowchartsCopy = function(nodeId) {
            const text = window.phpFlowchartsCopyMap[nodeId];
            if (!text) {
                console.warn('No copy text defined for node:', nodeId);
                return;
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(showToast).catch(fallbackCopy);
            } else {
                fallbackCopy();
            }

            function fallbackCopy() {
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select();
                try {
                    document.execCommand('copy');
                    showToast();
                } catch (err) {
                    alert('Could not copy automatically.\\n\\nPlease copy manually:\\n\\n' + text);
                }
                document.body.removeChild(ta);
            }

            function showToast() {
                const toast = document.getElementById('toast');
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 2200);
            }
        };

        mermaid.initialize({
            startOnLoad: true,
            theme: 'dark',
            securityLevel: 'loose',
            flowchart: {
                curve: 'basis',
                htmlLabels: true
            }
        });
        </script>
        </body>
        </html>
        HTML;
    }

    /**
     * Convenience method: write all requested formats to disk
     */
    public function write(
        string $outDir,
        string $basename,
        bool $html = true,
        bool $mmd  = false,
        bool $dot  = false
    ): array {
        $outDir = rtrim($outDir, '/');
        if (!is_dir($outDir)) {
            mkdir($outDir, 0755, true);
        }

        $written = [];

        if ($mmd) {
            $path = "{$outDir}/{$basename}.mmd";
            file_put_contents($path, $this->toMermaid());
            $written['mmd'] = $path;
        }

        if ($dot) {
            $path = "{$outDir}/{$basename}.dot";
            file_put_contents($path, $this->toDot());
            $written['dot'] = $path;
        }

        if ($html) {
            $path = "{$outDir}/{$basename}.html";
            file_put_contents($path, $this->toHtml());
            $written['html'] = $path;
        }

        return $written;
    }

    // ---------- Internal helpers ----------

    private function mermaidShape(string $type, string $id, string $label): string
    {
        $label = str_replace(["\n", '"'], ["<br/>", "'"], $label);

        return match ($type) {
            'start', 'end'   => "{$id}([\"{$label}\"])",
            'decision'       => "{$id}{{\"{$label}\"}}",
            'error'          => "{$id}[\"{$label}\"]",
            'io'             => "{$id}[/\"{$label}\"/]",
            'subroutine'     => "{$id}[[\"{$label}\"]]",
            default          => "{$id}[\"{$label}\"]",
        };
    }

    // Getters
    public function getTitle(): string       { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getDirection(): string   { return $this->direction; }
    public function getNodes(): array        { return $this->nodes; }
    public function getEdges(): array        { return $this->edges; }
}
