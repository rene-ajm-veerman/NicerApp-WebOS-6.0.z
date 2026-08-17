<?php

$parallel = new Http2Parallel([
    'base_uri' => 'https://httpbin.org/',
], concurrency: 150);

$parallel
    ->onFulfilled(function ($response, $key, $userData) {
        echo "✓ [{$key}] {$response->getStatusCode()} – " . strlen($response->getBody()) . " bytes (user: {$userData})\n";
    })
    ->onRejected(function ($reason, $key, $userData) {
        $msg = $reason instanceof RequestException
            ? $reason->getMessage()
            : $reason->getMessage();
        echo "✗ [{$key}] FAILED: {$msg}\n";
    });

// Add hundreds of requests
for ($i = 1; $i <= 300; $i++) {
    $delay = $i % 6; // 0–5 seconds
    $parallel->addRequest(
        key: "req_{$i}",
        request: new Request('GET', "delay/{$delay}"),
        userData: "user_123"
    );
}

// Optional: POST with JSON
$parallel->addRequest('login', new Request(
    'POST',
    'https://api.example.com/login',
    ['Content-Type' => 'application/json'],
    json_encode(['user' => 'john'])
));

echo "Firing 300+ requests over a single HTTP/2 connection...\n";
$parallel->execute();

echo "All done!\n";
