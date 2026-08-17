<?php
// proxy-daemon.php - Full-featured HTTPS proxy + file server + uploader with WS progress

require __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Factory;
use React\Http\Message\Response;
use React\Http\Server;
use React\Socket\Server as SocketServer;
use React\Socket\SecureServer;
use React\Stream\ReadableStreamInterface;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Ratchet\Http\HttpServer as RatchetHttp;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

// === CONFIG ===
$listenHost = '127.0.0.1';          // '0.0.0.0' for external (dangerous without firewall!)
$httpPort   = 8081;
$wsPort     = 8082;

$proxyTargetBase = 'http://127.0.0.1:3000'; // Internal service to proxy

$authUser = 'admin';
$authPass = 'supersecret123';       // CHANGE THIS! Use env vars in prod

$certFile = __DIR__ . '/server.crt';
$keyFile  = __DIR__ . '/server.key';

$uploadDir = __DIR__ . '/uploads/'; // Writable directory
$maxFileSize = 50 * 1024 * 1024;    // 50 MB
$allowedExtensions = ['txt', 'pdf', 'jpg', 'png', 'zip', 'docx', 'csv'];

$allowedFilePrefixes = [            // Restrict readable paths
    '/var/www/',
    '/home/user/projects/',
    __DIR__ . '/public/',
    $uploadDir,
];

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// === WebSocket for Progress ===
$clients = new \SplObjectStorage();

class ProgressWs implements MessageComponentInterface {
    public function onOpen(ConnectionInterface $conn) {
        global $clients;
        $clients->attach($conn);
        echo "WS connected: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // e.g., client sends {'sessionId': 'abc'} to associate
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

    public static function broadcast(array $data) {
        global $clients;
        $json = json_encode($data);
        foreach ($clients as $client) {
            $client->send($json);
        }
    }
}

// === SETUP ===
$loop = Factory::create();

// WebSocket Server
$wsSocket = new SocketServer("$listenHost:$wsPort", $loop);
$wsApp = new RatchetHttp(new WsServer(new ProgressWs()));
$wsApp->listen($wsSocket); // Note: Ratchet's listen() is called here

$guzzle = new GuzzleClient([
    'timeout'         => 60,
    'allow_redirects' => true,
    'verify'          => false,
]);

// HTTP Server with Streaming for Progress
$httpServer = new Server($loop, function (ServerRequestInterface $request) use (
    $guzzle, $proxyTargetBase, $authUser, $authPass, $allowedFilePrefixes,
    $uploadDir, $maxFileSize, $allowedExtensions
) {
    // Basic Auth
    $auth = $request->getHeaderLine('Authorization');
    if (!$auth || strpos($auth, 'Basic ') !== 0) {
        return new Response(401, ['WWW-Authenticate' => 'Basic realm="Protected"'], 'Auth required');
    }
    $credentials = base64_decode(substr($auth, 6));
    [$user, $pass] = explode(':', $credentials, 2) + [null, null];
    if ($user !== $authUser || $pass !== $authPass) {
        return new Response(403, [], 'Forbidden');
    }

    $path = $request->getUri()->getPath();

    // === UPLOAD WITH STREAMING PROGRESS ===
    if ($path === '/upload' && $request->getMethod() === 'POST') {
        $contentLength = (int)$request->getHeaderLine('Content-Length');
        $receivedBytes = 0;

        $body = $request->getBody();
        if ($body instanceof ReadableStreamInterface) {
            $body->on('data', function ($chunk) use (&$receivedBytes, $contentLength) {
                $receivedBytes += strlen($chunk);
                $percent = $contentLength > 0 ? round(($receivedBytes / $contentLength) * 100) : 0;
                ProgressWs::broadcast(['type' => 'progress', 'percent' => $percent]);
            });
            $body->on('end', function () {
                ProgressWs::broadcast(['type' => 'complete', 'percent' => 100]);
            });
            $body->on('error', function (\Exception $e) {
                ProgressWs::broadcast(['type' => 'error', 'message' => $e->getMessage()]);
            });
        }

        $uploadedFiles = $request->getUploadedFiles();
        if (empty($uploadedFiles)) {
            return new Response(400, ['Content-Type' => 'application/json'], json_encode(['error' => 'No files']));
        }

        $results = [];
        foreach ($uploadedFiles as $name => $file) {
            if ($file->getError() !== UPLOAD_ERR_OK) {
                $results[$name] = ['error' => 'Upload error: ' . $file->getError()];
                continue;
            }

            $size = $file->getSize();
            if ($size > $maxFileSize) {
                $results[$name] = ['error' => 'Too large'];
                continue;
            }

            $originalName = $file->getClientFilename();
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExtensions)) {
                $results[$name] = ['error' => 'Invalid type'];
                continue;
            }

            $safeName = uniqid() . '-' . preg_replace('/[^A-Za-z0-9\._-]/', '_', basename($originalName));
            $targetPath = rtrim($uploadDir, '/') . '/' . $safeName;

            try {
                $file->moveTo($targetPath);
                $results[$name] = [
                    'success' => true,
                    'filename' => $safeName,
                    'original' => $originalName,
                    'size' => $size,
                    'url' => '/file?path=' . urlencode($targetPath),
                ];
            } catch (\Exception $e) {
                $results[$name] = ['error' => $e->getMessage()];
            }
        }

        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'message' => 'Upload complete',
            'files' => $results
        ]));
    }

    // === FILE SERVE ===
    if ($path === '/file') {
        $query = $request->getUri()->getQuery();
        parse_str($query, $params);
        $filePath = $params['path'] ?? '';

        if (!$filePath) {
            return new Response(400, [], 'Missing path');
        }

        $realPath = realpath($filePath);
        if (!$realPath || !is_readable($realPath)) {
            return new Response(404, [], 'Not found');
        }

        $allowed = false;
        foreach ($allowedFilePrefixes as $prefix) {
            if (strpos($realPath, realpath($prefix)) === 0) {
                $allowed = true;
                break;
            }
        }
        if (!$allowed) {
            return new Response(403, [], 'Not allowed');
        }

        $content = file_get_contents($realPath);
        $mime = mime_content_type($realPath) ?: 'application/octet-stream';
        $headers = [
            'Content-Type' => $mime,
            'Content-Length' => strlen($content),
        ];
        if (isset($params['download'])) {
            $headers['Content-Disposition'] = 'attachment; filename="' . basename($realPath) . '"';
        }

        return new Response(200, $headers, $content);
    }

    // === PROXY MODE ===
    $targetUrl = $proxyTargetBase . $path . '?' . $request->getUri()->getQuery();

    $method = $request->getMethod();
    $headers = $request->getHeaders();
    unset($headers['Host'], $headers['Authorization']);

    $body = $request->getBody()->getContents();

    try {
        $guzzleReq = new GuzzleRequest($method, $targetUrl, $headers, $body);
        $guzzleRes = $guzzle->send($guzzleReq);

        $resHeaders = $guzzleRes->getHeaders();
        unset($resHeaders['Transfer-Encoding'], $resHeaders['Connection']);

        return new Response(
            $guzzleRes->getStatusCode(),
            $resHeaders,
            $guzzleRes->getBody()->getContents()
        );
    } catch (\Exception $e) {
        return new Response(502, [], 'Proxy error: ' . $e->getMessage());
    }
});

// HTTPS Socket
$socket = new SocketServer("$listenHost:$httpPort", $loop);
$socket = new SecureServer($socket, $loop, [
    'local_cert'        => $certFile,
    'local_pk'          => $keyFile,
    'allow_self_signed' => true,
    'verify_peer'       => false,
]);

$httpServer->listen($socket);

echo "HTTPS on https://$listenHost:$httpPort\n";
echo "WebSocket on ws://$listenHost:$wsPort\n";

$loop->run();
