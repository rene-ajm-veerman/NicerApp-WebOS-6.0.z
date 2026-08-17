<?php
// proxy-daemon.php - Async HTTPS HTTP server + WebSocket + file upload progress

require __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Factory;
use React\Http\Message\Response;
use React\Http\Server;
use React\Socket\Server as SocketServer;
use React\Socket\SecureServer;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Ratchet\WebSocket\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

// === CONFIG ===
$listenHost = '127.0.0.1';
$httpPort   = 8081;                 // HTTP/HTTPS server port
$wsPort     = 8082;                 // WebSocket port (separate for simplicity)

$proxyTargetBase = 'http://127.0.0.1:3000';

$authUser = 'admin';
$authPass = 'supersecret123';

$certFile = __DIR__ . '/server.crt';
$keyFile  = __DIR__ . '/server.key';

$uploadDir = __DIR__ . '/uploads/';
$maxFileSize = 50 * 1024 * 1024;    // 50 MB
$allowedExtensions = ['txt', 'pdf', 'jpg', 'png', 'zip', 'docx', 'csv'];

$allowedFilePrefixes = [
    '/var/www/',
    '/home/user/projects/',
    __DIR__ . '/public/',
    $uploadDir,
];

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// === SHARED PROGRESS STORAGE (simple in-memory for single process) ===
$clients = new \SplObjectStorage(); // WebSocket clients
$progressTrackers = []; // [clientId => ['progress' => 0, 'file' => 'name']]

// === WEBSOCKET SERVER (for progress updates) ===
class ProgressWs implements MessageComponentInterface {
    public function onOpen(ConnectionInterface $conn) {
        global $clients;
        $clients->attach($conn);
        echo "New WS connection: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Optional: client can send messages (e.g., "start_upload")
        echo "WS message: $msg\n";
    }

    public function onClose(ConnectionInterface $conn) {
        global $clients;
        $clients->detach($conn);
        echo "WS closed: {$conn->resourceId}\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "WS error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$loop = Factory::create();

// Start WebSocket server on separate port
$wsSocket = new SocketServer("$listenHost:$wsPort", $loop);
$wsServer = new HttpServer(new WsServer(new ProgressWs()));
$wsServer->listen($wsSocket);
echo "WebSocket server listening on ws://$listenHost:$wsPort\n";

// === HTTP SERVER ===
$guzzle = new GuzzleClient([...]); // same as before

$httpServer = new Server($loop, function (ServerRequestInterface $request) use (
    $guzzle, $proxyTargetBase, $authUser, $authPass, $allowedFilePrefixes,
    $uploadDir, $maxFileSize, $allowedExtensions, $clients, $progressTrackers
) {
    // Basic Auth (same as before)
    $auth = $request->getHeaderLine('Authorization');
    if (!$auth || strpos($auth, 'Basic ') !== 0) {
        return new Response(401, ['WWW-Authenticate' => 'Basic realm="Protected Access"'], 'Authentication required');
    }
    $credentials = base64_decode(substr($auth, 6));
    [$user, $pass] = explode(':', $credentials, 2);
    if ($user !== $authUser || $pass !== $authPass) {
        return new Response(403, [], 'Forbidden');
    }

    $path = $request->getUri()->getPath();

    // === UPLOAD WITH PROGRESS ===
    if ($path === '/upload' && $request->getMethod() === 'POST') {
        // For progress, we need to stream the request body
        // ReactPHP's default buffering works for small files, but for large:
        // We use a custom handler to read chunks manually

        $body = $request->getBody();
        $body->on('data', function ($chunk) use (&$progressTrackers, $clients) {
            // Simulate progress calculation (real: track bytes read vs content-length)
            foreach ($clients as $client) {
                $client->send(json_encode([
                    'type' => 'progress',
                    'progress' => rand(1, 99), // Placeholder; replace with real calc
                    'message' => 'Uploading chunk...'
                ]));
            }
        });

        $body->on('end', function () use (&$progressTrackers, $clients) {
            foreach ($clients as $client) {
                $client->send(json_encode([
                    'type' => 'complete',
                    'progress' => 100
                ]));
            }
        });

        // Proceed with normal upload handling (same as before)
        $uploadedFiles = $request->getUploadedFiles();

        if (empty($uploadedFiles)) {
            return new Response(400, ['Content-Type' => 'application/json'], json_encode(['error' => 'No files uploaded']));
        }

        $results = [];
        foreach ($uploadedFiles as $name => $file) {
            if ($file->getError() !== UPLOAD_ERR_OK) continue;

            $size = $file->getSize();
            if ($size > $maxFileSize) continue;

            $originalName = $file->getClientFilename();
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExtensions)) continue;

            $safeName = uniqid() . '-' . preg_replace('/[^A-Za-z0-9\._-]/', '_', basename($originalName));
            $targetPath = rtrim($uploadDir, '/') . '/' . $safeName;

            try {
                $file->moveTo($targetPath);
                $results[$name] = [
                    'success'  => true,
                    'filename' => $safeName,
                    'original' => $originalName,
                    'size'     => $size,
                    'url'      => '/file?path=' . urlencode($targetPath),
                ];
            } catch (\Exception $e) {
                $results[$name] = ['error' => 'Failed to save: ' . $e->getMessage()];
            }
        }

        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'message' => 'Upload completed',
            'files'   => $results
        ]));
    }

    // === OTHER ENDPOINTS (file read, proxy) ===
    // Same as previous version - omitted for brevity
});

// HTTPS setup (same as before)
$socket = new SocketServer("$listenHost:$httpPort", $loop);
$socket = new SecureServer($socket, $loop, [
    'local_cert'        => $certFile,
    'local_pk'          => $keyFile,
    'allow_self_signed' => true,
    'verify_peer'       => false,
]);

$httpServer->listen($socket);

echo "HTTPS daemon on https://$listenHost:$httpPort\n";
echo "WebSocket on ws://$listenHost:$wsPort (use wss:// in production)\n";

$loop->run();
