<?php
require 'class.ultraParallel.php';

$ultra = new UltraParallel(concurrency: 500);

$ultra
    ->onFulfilled(fn($res, $key) => null) // optional custom handling
    ->onRejected(fn($err, $key) => null);

for ($i = 1; $i <= 500; $i++) {
    $delay = $i % 7;
    $ultra->add("req_{$i}", new Request('GET', "https://httpbin.org/delay/{$delay}"), "batch_2025");
}

// Fire and forget (or await results)
$results = $ultra->execute();

echo "Success: " . $ultra->getSuccessCount() . PHP_EOL;
echo "Failed : " . $ultra->getFailedCount() . PHP_EOL;

// Use collected data
foreach ($results['success'] as $key => $data) {
    // $data['body'], $data['userData'], etc.
}
?>
