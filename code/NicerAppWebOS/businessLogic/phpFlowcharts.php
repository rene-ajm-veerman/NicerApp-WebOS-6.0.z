#!/usr/bin/env php
<?php
/**
 * phpFlowcharts.php
 * Thin CLI wrapper around phpFlowcharts.class.php
 */

declare(strict_types=1);

require_once __DIR__ . '/phpFlowcharts.class.php';

function usage(): void
{
    echo <<<USAGE
    phpFlowcharts – JSON → Flowchart generator

    Usage:
    php phpFlowcharts.php <input.json> [options]

    Options:
    --out=basename     Output base name (default: flowchart)
    --html             Generate HTML page (default: yes)
    --mmd              Generate .mmd Mermaid source
    --dot              Generate Graphviz DOT file
    --no-html          Skip HTML
    --dir=path         Output directory (default: current)
    -h, --help         Show this help

    Examples:
    php phpFlowcharts.php install-flow.example.json
    php phpFlowcharts.php install-flow.example.json --out=nicerapp-install --mmd --dot

    USAGE;
}

// ---------- CLI parsing ----------
$opts = getopt("h", ["help", "out:", "html", "mmd", "dot", "no-html", "dir:"], $optind);
$argv = array_slice($argv ?? [], $optind);

if (isset($opts['h']) || isset($opts['help']) || empty($argv[0])) {
    usage();
    exit(empty($argv[0]) ? 1 : 0);
}

$inputFile = $argv[0];
$outBase   = $opts['out'] ?? 'flowchart';
$outDir    = $opts['dir'] ?? '.';
$wantHtml  = !isset($opts['no-html']);
$wantMmd   = isset($opts['mmd']);
$wantDot   = isset($opts['dot']);

// Default to HTML if nothing else was requested
if (!$wantMmd && !$wantDot && !$wantHtml) {
    $wantHtml = true;
}

try {
    $fc = new phpFlowcharts($inputFile);

    $written = $fc->write(
        outDir:   $outDir,
        basename: $outBase,
        html:     $wantHtml,
        mmd:      $wantMmd,
        dot:      $wantDot
    );

    foreach ($written as $type => $path) {
        echo "Wrote " . strtoupper($type) . str_repeat(' ', 5 - strlen($type)) . ": {$path}\n";
    }

    if (isset($written['html'])) {
        echo "Open the HTML file in a browser to view the interactive flowchart.\n";
    }

    echo "Done.\n";

} catch (Throwable $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
    exit(1);
}
