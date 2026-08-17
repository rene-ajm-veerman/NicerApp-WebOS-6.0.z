<?php
// proxy-daemon.php - Async HTTPS proxy + file server + file upload using ReactPHP

require __DIR__ . '/vendor/autoload.php';

use React\EventLoop\Factory;
use React\Http\Message\Response;
use React\Http\Server;
use React\Socket\Server as SocketServer;
use React\Socket\SecureServer;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

// === CONFIG ===
$listenHost = '127.0.0.1';          // '0.0.0.0' only if needed (dangerous!)
$listenPort = 8081;

$proxyTargetBase = 'http://127.0.0.1:3000'; // Default proxy target

$authUser = 'admin';
$authPass = 'supersecret123';       // CHANGE THIS!

// HTTPS certs
$certFile = __DIR__ . '/server.crt';
$keyFile  = __DIR__ . '/server.key';

// File upload settings
$uploadDir = __DIR__ . '/uploads/'; // Make sure this directory exists and is writable
$maxFileSize = 10 * 1024 * 1024;    // 10 MB
$allowedExtensions = ['txt', 'pdf', 'jpg', 'png', 'zip', 'docx', 'csv'];

// File read restrictions (same as before)
$allowedFilePrefixes = [
    '/var/www/',
    '/home/user/projects/',
    __DIR__ . '/public/',
    $uploadDir,  // Allow reading uploaded files
];

// Ensure upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// === SERVER SETUP ===
$loop = Factory::create();

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
    // Basic Auth
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

    // === FILE UPLOAD ENDPOINT ===
    if ($path === '/upload' && $request->getMethod() === 'POST') {
        $uploadedFiles = $request->getUploadedFiles();

        if (empty($uploadedFiles)) {
            return new Response(400, ['Content-Type' => 'application/json'], json_encode(['error' => 'No files uploaded']));
        }

        $results = [];
        foreach ($uploadedFiles as $name => $file) {
            if ($file->getError() !== UPLOAD_ERR_OK) {
                $results[$name] = ['error' => 'Upload error: ' . $file->getError()];
                continue;
            }

            $size = $file->getSize();
            if ($size > $maxFileSize) {
                $results[$name] = ['error' => 'File too large (max: ' . round($maxFileSize / 1024 / 1024, 1) . ' MB)'];
                continue;
            }

            $originalName = $file->getClientFilename();
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExtensions)) {
                $results[$name] = ['error' => 'File type not allowed'];
                continue;
            }

            // Secure filename: unique + sanitized
            $safeName = uniqid() . '-' . preg_replace('/[^A-Za-z0-9\._-]/', '_', basename($originalName));
            $targetPath = rtrim($uploadDir, '/') . '/' . $safeName;

            try {
                $file->moveTo($targetPath);
                $results[$name] = [
                    'success'    => true,
                    'filename'   => $safeName,
                    'original'   => $originalName,
                    'size'       => $size,
                    'url'        => '/file?path=' . urlencode($targetPath), // link to view/download
                ];
            } catch (\Exception $e) {
                $results[$name] = ['error' => 'Failed to save file: ' . $e->getMessage()];
            }
        }

        return new Response(200, ['Content-Type' => 'application/json'], json_encode([
            'message' => 'Upload completed',
            'files'   => $results
        ]));
    }

    // === FILE SERVE MODE (same as before) ===
    if ($path === '/file' || $path === '/file/') {
        $query = $request->getUri()->getQuery();
        parse_str($query, $params);
        $filePath = $params['path'] ?? '';

        if (!$filePath) {
            return new Response(400, [], 'Missing ?path= parameter');
        }

        $realPath = realpath($filePath);
        if (!$realPath || !is_readable($realPath)) {
            return new Response(404, [], 'File not found or not readable');
        }

        $allowed = false;
        foreach ($allowedFilePrefixes as $prefix) {
            if (str_starts_with($realPath, realpath($prefix))) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            return new Response(403, [], 'Access to this path is not allowed');
        }

        $content = file_get_contents($realPath);
        $mime = mime_content_type($realPath) ?: 'application/octet-stream';

        // Optional: force download instead of inline
        $headers = [
            'Content-Type'   => $mime,
            'Content-Length' => strlen($content),
        ];
        if (isset($params['download'])) {
            $headers['Content-Disposition'] = 'attachment; filename="' . basename($realPath) . '"';
        }

        return new Response(200, $headers, $content);
    }

    // === PROXY MODE (everything else) ===
    $targetUrl = $proxyTargetBase . $path . '?' . $request->getUri()->getQuery();

    $method = $request->getMethod();
    $headers = $request->getHeaders();
    unset($headers['Host'], $headers['Authorization']);

    $body = $request->getBody()->getContents();

    try {
        $guzzleRequest = new GuzzleRequest($method, $targetUrl, $headers, $body);
        $guzzleResponse = $guzzle->send($guzzleRequest);

        $responseHeaders = $guzzleResponse->getHeaders();
        unset($responseHeaders['Transfer-Encoding'], $responseHeaders['Connection']);

        return new Response(
            $guzzleResponse->getStatusCode(),
            $responseHeaders,
            $guzzleResponse->getBody()->getContents()
        );
    } catch (\Exception $e) {
        return new Response(502, [], 'Proxy error: ' . $e->getMessage());
    }
});

// HTTPS socket
$socket = new SocketServer("$listenHost:$listenPort", $loop);
$socket = new SecureServer($socket, $loop, [
    'local_cert'        => $certFile,
    'local_pk'          => $keyFile,
    'allow_self_signed' => true,
    'verify_peer'       => false,
]);

$httpServer->listen($socket);

echo "HTTPS daemon listening on https://$listenHost:$listenPort\n";
echo " - Proxy mode: any path → $proxyTargetBase\n";
echo " - Read file: /file?path=/absolute/path/to/file\n";
echo " - Upload file: POST /upload (multipart/form-data)\n";
echo "Basic auth: $authUser / $authPass\n";

$loop->run();
