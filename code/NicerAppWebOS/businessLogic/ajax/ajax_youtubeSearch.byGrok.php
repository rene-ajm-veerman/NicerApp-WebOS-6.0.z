<?php
/**
 * ajax_youtubeSearch.php
 * Server-side proxy for YouTube Data API v3 search.
 * - Keeps the API key private
 * - Rate-limits to 200 searches per calendar day per user (or per IP for guests)
 *
 * Parameters (GET or POST):
 *   q          string   search query (required)
 *   maxResults int      1-50 (default 24)
 *   pageToken  string   optional
 */

require_once(realpath(dirname(__FILE__).'/../../NicerAppWebOS/boot.php')); // adjust path if needed
global $naWebOS;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// ------------------------------------------------------------------
// Configuration
// ------------------------------------------------------------------
const YT_DAILY_LIMIT = 200;   // searches per day

// ------------------------------------------------------------------
// 1. Identify the requester
// ------------------------------------------------------------------
$loginName = $_SESSION['cdb_loginName'] ?? ($_COOKIE['cdb_loginName'] ?? 'Guest');
$loginName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $loginName); // safe filename

$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    // take the first IP if behind a proxy
    $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
}
$ipSafe = str_replace([':', '.'], ['-', '_'], $ip);

// Prefer logged-in user identity; fall back to IP for guests
$rateKey = ($loginName !== 'Guest' && $loginName !== '')
? 'user_' . $loginName
: 'ip_'   . $ipSafe;

// ------------------------------------------------------------------
// 2. Rate-limit storage (one small JSON file per day + key)
// ------------------------------------------------------------------
$today = date('Y-m-d');
$rateDir = $naWebOS->domainPath . '/siteCache/youtubeSearchQuota';
if (!is_dir($rateDir)) {
    @mkdir($rateDir, 0750, true);
}

$rateFile = $rateDir . '/' . $today . '_' . $rateKey . '.json';

// Clean up files older than 3 days (cheap housekeeping)
$oldFiles = glob($rateDir . '/*.json');
$cutoff = strtotime('-3 days');
foreach ($oldFiles as $f) {
    if (filemtime($f) < $cutoff) {
        @unlink($f);
    }
}

// ------------------------------------------------------------------
// 3. Check / increment quota
// ------------------------------------------------------------------
$count = 0;
if (is_readable($rateFile)) {
    $data = json_decode(file_get_contents($rateFile), true);
    $count = intval($data['count'] ?? 0);
}

if ($count >= YT_DAILY_LIMIT) {
    http_response_code(429); // Too Many Requests
    echo json_encode([
        'error'     => 'Daily YouTube search limit reached (200 per day)',
                     'limit'     => YT_DAILY_LIMIT,
                     'used'      => $count,
                     'resetsAt'  => date('Y-m-d 00:00:00', strtotime('tomorrow')),
                     'identity'  => $rateKey
    ]);
    exit;
}

// Increment (atomic-ish write)
$count++;
file_put_contents(
    $rateFile,
    json_encode([
        'count'     => $count,
        'last'      => date('c'),
                'identity'  => $rateKey,
                'ip'        => $ip
    ], JSON_PRETTY_PRINT),
    LOCK_EX
);

// ------------------------------------------------------------------
// 4. Input sanitisation
// ------------------------------------------------------------------
$q = trim($_REQUEST['q'] ?? '');
$maxResults = intval($_REQUEST['maxResults'] ?? 24);
$pageToken  = trim($_REQUEST['pageToken'] ?? '');

if ($q === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or empty parameter: q']);
    exit;
}
$maxResults = max(1, min(50, $maxResults));

// ------------------------------------------------------------------
// 5. Load API key
// ------------------------------------------------------------------
$keyCandidates = [
    $naWebOS->domainPath . '/domainConfig/apiKey.youtube.txt',
$naWebOS->domainPath . '/domainConfig/secrets/apiKey.youtube.txt',
dirname(__FILE__) . '/apiKey.youtube.txt',
];

$apiKey = null;
foreach ($keyCandidates as $fn) {
    if (is_readable($fn)) {
        $apiKey = trim(file_get_contents($fn));
        break;
    }
}

if (!$apiKey || strlen($apiKey) < 20) {
    http_response_code(500);
    echo json_encode([
        'error' => 'YouTube API key not configured on server',
        'hint'  => 'Create domainConfig/apiKey.youtube.txt containing only the key'
    ]);
    exit;
}

// ------------------------------------------------------------------
// 6. Call YouTube
// ------------------------------------------------------------------
$params = [
    'part'            => 'snippet',
'type'            => 'video',
'q'               => $q,
'maxResults'      => $maxResults,
'key'             => $apiKey,
'safeSearch'      => 'moderate',
'videoEmbeddable' => 'true',
];
if ($pageToken !== '') {
    $params['pageToken'] = $pageToken;
}

$url = 'https://www.googleapis.com/youtube/v3/search?' . http_build_query($params);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 12,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_USERAGENT      => 'NicerApp-WebOS/6.0 (YouTube search proxy)',
                  CURLOPT_HTTPHEADER     => ['Accept: application/json'],
                  CURLOPT_SSL_VERIFYPEER => true,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    // Optional: refund the quota count on hard upstream failure
    // (comment out the next 4 lines if you prefer to keep the count)
    $count--;
    file_put_contents($rateFile, json_encode([
        'count' => max(0, $count),
                                             'last'  => date('c'),
                                             'identity' => $rateKey,
                                             'ip' => $ip
    ], JSON_PRETTY_PRINT), LOCK_EX);

    http_response_code(502);
    echo json_encode([
        'error'    => 'Upstream YouTube API failure',
        'httpCode' => $httpCode,
        'curl'     => $curlErr
    ]);
    exit;
}

// ------------------------------------------------------------------
// 7. Clean & return
// ------------------------------------------------------------------
$data = json_decode($response, true);
if (!is_array($data) || !isset($data['items'])) {
    http_response_code(502);
    echo json_encode(['error' => 'Invalid JSON from YouTube']);
    exit;
}

foreach ($data['items'] as &$item) {
    unset($item['etag'], $item['kind']);
    if (isset($item['snippet'])) {
        unset(
            $item['snippet']['publishTime'],
            $item['snippet']['liveBroadcastContent'],
            $item['snippet']['thumbnails']['default'],
            $item['snippet']['thumbnails']['high']
        );
    }
}
unset($item);

// Add quota info so the front-end can show a nice message
$data['_quota'] = [
    'used'      => $count,
'limit'     => YT_DAILY_LIMIT,
'remaining' => max(0, YT_DAILY_LIMIT - $count),
'resetsAt'  => date('Y-m-d 00:00:00', strtotime('tomorrow'))
];

echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
