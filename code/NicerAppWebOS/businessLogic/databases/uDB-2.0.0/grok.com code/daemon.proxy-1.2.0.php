<?php
// proxy-daemon.php - HTTPS HTTP server + WebSocket progress tracking

require __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Factory;
use React\Http\Message\Response;
use React\Http\Server;
use React\Socket\Server as SocketServer;
use React\Socket\SecureServer;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Client as GuzzleClient;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;

// === CONFIG ===
$listenHost = '127.0.0.1';
$httpPort   = 8081;
$wsPort     = 8082;

$proxyTargetBase = 'http://127.0.0.1:3000';

$authUser = 'admin';
$authPass = 'supersecret123';

$certFile = __DIR__ . '/server.crt';
$keyFile  = __DIR__ . '/server.key';

$uploadDir = __DIR__ . '/uploads/';
$maxFileSize = 50 * 1024 * 1024;
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

// === PROGRESS STORAGE ===
$progressClients = new \SplObjectStorage();

// === WebSocket Server for Progress ===
class ProgressTracker implements MessageComponentInterface {
    public function onOpen(ConnectionInterface $conn) {
        global $progressClients;
        $progressClients->attach($conn);
        echo "New WS connection: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Client can send messages if needed (e.g., "ready")
    }

    public function onClose(ConnectionInterface $conn) {
        global $progressClients;
        $progressClients->detach($conn);
        echo "WS closed: {$conn->resourceId}\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "WS error: {$e->getMessage()}\n";
        $conn->close();
    }

    // Helper to broadcast progress
    public static function broadcast($data) {
        global $progressClients;
        foreach ($progressClients as $client) {
            $client->send(json_encode($data));
        }
    }
}

$loop = Factory::create();

// Start WebSocket server
$wsSocket = new SocketServer("$listenHost:$wsPort", $loop);
$wsApp = new HttpServer(new WsServer(new ProgressTracker()));
$wsApp->listen($wsSocket);
echo "WebSocket server on ws://$listenHost:$wsPort\n";

// === HTTP Server ===
$guzzle = new GuzzleClient([
    'timeout'         => 60,
    'allow_redirects' => true,
    'verify'          => false,
]);

$httpServer = new Server($loop, function (ServerRequestInterface $request) use (
    $guzzle,
    $proxyTargetBase,
    $authUser,
    $authPass,
    $allowedFilePrefixes,
    $uploadDir,
    $maxFileSize,
    $allowedExtensions
) {
    // Basic Auth check (same as before)
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
        $uploadedFiles = $request->getUploadedFiles();

        if (empty($uploadedFiles)) {
            return new Response(400, ['Content-Type' => 'application/json'], json_encode(['error' => 'No files uploaded']));
        }

        $results = [];
        foreach ($uploadedFiles as $name => $file) {
            if ($file->getError() !== UPLOAD_ERR_OK) {
                $results[$name] = ['error' => 'Upload error'];
                continue;
            }

            $size = $file->getSize();
            if ($size > $maxFileSize) {
                $results[$name] = ['error' => 'File too large'];
                continue;
            }

            $originalName = $file->getClientFilename();
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExtensions)) {
                $results[$name] = ['error' => 'Invalid file type'];
                continue;
            }

            $safeName = uniqid() . '-' . preg_replace('/[^A-Za-z0-9\._-]/', '_', basename($originalName));
            $targetPath = rtrim($uploadDir, '/') . '/' . $safeName;

            try {
                $file->moveTo($targetPath);

                // Simulate progress (real progress needs streaming - see notes below)
                ProgressTracker::broadcast(['type' => 'progress', 'progress' => 50]);
                usleep(500000); // demo delay
                ProgressTracker::broadcast(['type' => 'progress', 'progress' => 100]);

                $results[$name] = [
                    'success'  => true,
                    'filename' => $safeName,
                    'original' => $originalName,
                    'size'     => $size,
                    'url'      => '/file?path=' . urlencode($targetPath),
                ];
            } catch (\Exception $e) {
                $results[$name] = ['error' => $e->getMessage()];
            }
        }

        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'message' => 'Upload completed',
            'files'   => $results
        ]));
    }

    // === FILE READ & PROXY MODE (same as before - omitted for brevity) ===
});

// HTTPS setup
$socket = new SocketServer("$listenHost:$httpPort", $loop);
$socket = new SecureServer($socket, $loop, [
    'local_cert'        => $certFile,
    'local_pk'          => $keyFile,
    'allow_self_signed' => true,
    'verify_peer'       => false,
]);

$httpServer->listen($socket);

echo "HTTPS daemon on https://$listenHost:$httpPort\n";
echo "WebSocket on ws://$listenHost:$wsPort\n";

$loop->run();
