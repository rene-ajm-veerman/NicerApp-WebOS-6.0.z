<?php
declare(strict_types=1);

require_once __DIR__ . '/../boot.php';
require_once __DIR__ . '/../businessLogic/class.screenshots.php';

global $naWebOS;

$manager = new naScreenshots($naWebOS->dbsAdmin);

// ← change this to any URL you want
$url = $argv[1] ?? 'https://nicer.app/NicerAppWebOS/businessLogic/vividUserInterface/v8.y.z/demo.byGrok.desktop3D.v2.1.0.php';

echo "=== Target URL ===\n$url\n\n";

// Force re-queue
echo "=== enqueue (force) ===\n";
$job = $manager->enqueue($url, [
    'force'    => true,
    'priority' => 0,
]);
print_r($job);

// Process it directly (bypasses the queue claim issues)
echo "\n=== processJob (direct) ===\n";
$job['status']   = 'processing';
$job['lockedBy'] = 'manual-test';
$job['lockedAt'] = date('Y-m-d H:i:s');
$job['attempts'] = ($job['attempts'] ?? 0) + 1;

$result = $manager->processJob($job);
print_r($result);

echo "\n=== Done ===\n";
echo "Check file: " . ($result['filePath'] ?? $job['filePath'] ?? '(none)') . "\n";
echo "Status:     " . ($result['status'] ?? '(unknown)') . "\n";
?>
