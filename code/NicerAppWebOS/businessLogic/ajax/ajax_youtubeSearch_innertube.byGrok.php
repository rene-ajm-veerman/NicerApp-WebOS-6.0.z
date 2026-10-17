<?php
/**
 * ajax_youtubeSearch_innertube.php
 * YouTube search WITHOUT an official API key.
 * Uses YouTube's internal Innertube /youtubei/v1/search endpoint.
 *
 * Same rate limits as the official version:
 *   - 200 searches / calendar day / user-or-IP
 *   - burst limit (10 / minute)  ← already added earlier
 *
 * Parameters:
 *   q          string   required
 *   maxResults int      1-30 (default 20)
 */

require_once(realpath(dirname(__DIR__).'/../boot.php')); // adjust if needed
global $naWebOS;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// ------------------------------------------------------------------
// Configuration
// ------------------------------------------------------------------
const YT_DAILY_LIMIT  = 200;
const YT_BURST_LIMIT  = 10;   // per minute
const YT_BURST_WINDOW = 60;   // seconds

// ------------------------------------------------------------------
// 1. Identify requester (same logic as before)
// ------------------------------------------------------------------
$loginName = $_SESSION['cdb_loginName'] ?? ($_COOKIE['cdb_loginName'] ?? 'Guest');
$loginName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $loginName);

$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
}
$ipSafe = str_replace([':', '.'], ['-', '_'], $ip);

$rateKey = ($loginName !== 'Guest' && $loginName !== '')
? 'user_' . $loginName
: 'ip_'   . $ipSafe;

// ------------------------------------------------------------------
// 2. Rate-limit storage
// ------------------------------------------------------------------
$today   = date('Y-m-d');
$rateDir = $naWebOS->domainPath . '/siteCache/youtubeSearchQuota';
if (!is_dir($rateDir)) {
    @mkdir($rateDir, 0750, true);
}

// Clean files older than 3 days
foreach (glob($rateDir . '/*.json') as $f) {
    if (filemtime($f) < strtotime('-3 days')) {
        @unlink($f);
    }
}

$dailyFile = $rateDir . '/' . $today . '_' . $rateKey . '.json';
$burstFile = $rateDir . '/burst_' . $rateKey . '.json';

// ----- Daily limit -----
$dailyCount = 0;
if (is_readable($dailyFile)) {
    $d = json_decode(file_get_contents($dailyFile), true);
    $dailyCount = intval($d['count'] ?? 0);
}
if ($dailyCount >= YT_DAILY_LIMIT) {
    http_response_code(429);
    echo json_encode([
        'error'    => 'Daily YouTube search limit reached (200 per day)',
                     'limit'    => YT_DAILY_LIMIT,
                     'used'     => $dailyCount,
                     'resetsAt' => date('Y-m-d 00:00:00', strtotime('tomorrow'))
    ]);
    exit;
}

// ----- Burst limit (sliding 60-second window) -----
$now = time();
$burstTimestamps = [];
if (is_readable($burstFile)) {
    $b = json_decode(file_get_contents($burstFile), true);
    $burstTimestamps = array_filter(
        $b['timestamps'] ?? [],
        fn($ts) => ($now - $ts) < YT_BURST_WINDOW
    );
}
if (count($burstTimestamps) >= YT_BURST_LIMIT) {
    http_response_code(429);
    echo json_encode([
        'error'   => 'Too many searches in a short time (max ' . YT_BURST_LIMIT . ' per minute)',
                     'retryIn' => YT_BURST_WINDOW - ($now - min($burstTimestamps))
    ]);
    exit;
}

// Increment both counters
$dailyCount++;
file_put_contents($dailyFile, json_encode([
    'count'    => $dailyCount,
    'last'     => date('c'),
                                          'identity' => $rateKey,
                                          'ip'       => $ip
], JSON_PRETTY_PRINT), LOCK_EX);

$burstTimestamps[] = $now;
file_put_contents($burstFile, json_encode([
    'timestamps' => array_values($burstTimestamps),
                                          'identity'   => $rateKey
], JSON_PRETTY_PRINT), LOCK_EX);

// ------------------------------------------------------------------
// 3. Input
// ------------------------------------------------------------------
$q           = trim($_REQUEST['q'] ?? '');
$maxResults  = max(1, min(50, intval($_REQUEST['maxResults'] ?? 20)));
$continuation = trim($_REQUEST['continuation'] ?? '');   // ← NEW

if ($q === '' && $continuation === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or empty parameter: q (or continuation)']);
    exit;
}

// ------------------------------------------------------------------
// 4. Call YouTube Innertube (no API key)
// ------------------------------------------------------------------
$innertubeUrl = 'https://www.youtube.com/youtubei/v1/search?prettyPrint=false';

$payload = [
    'context' => [
        'client' => [
            'clientName'    => 'WEB',
'clientVersion' => '2.20241001.00.00',
'hl'            => 'en',
'gl'            => 'US',
        ]
    ],
];

if ($continuation !== '') {
    // Pagination request
    $payload['continuation'] = $continuation;
} else {
    // Fresh search
    $payload['query'] = $q;
}

$ch = curl_init($innertubeUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
                  CURLOPT_TIMEOUT        => 12,
                  CURLOPT_CONNECTTIMEOUT => 5,
                  CURLOPT_HTTPHEADER     => [
                      'Content-Type: application/json',
                  'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36',
                  'Accept: */*',
                  'Origin: https://www.youtube.com',
                  'Referer: https://www.youtube.com/',
                  ],
                  CURLOPT_SSL_VERIFYPEER => true,
]);

$raw = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);

if ($raw === false || $httpCode !== 200) {
    $dailyCount = max(0, $dailyCount - 1);
    file_put_contents($dailyFile, json_encode([
        'count' => $dailyCount, 'last' => date('c'),
                                              'identity' => $rateKey, 'ip' => $ip
    ], JSON_PRETTY_PRINT), LOCK_EX);

    http_response_code(502);
    echo json_encode([
        'error'    => 'Innertube request failed',
        'httpCode' => $httpCode,
        'curl'     => $curlErr
    ]);
    exit;
}

$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(502);
    echo json_encode(['error' => 'Invalid JSON from YouTube']);
    exit;
}

// ------------------------------------------------------------------
// 5. Parse video results + extract next continuation token
// ------------------------------------------------------------------
$items = [];
$nextContinuation = null;

/**
 * Walk any nested array looking for videoRenderer + continuationItemRenderer
 */
function na_yt_walk($node, &$items, &$nextContinuation, $maxResults) {
    if (!is_array($node) || count($items) >= $maxResults) return;

    // Video card
    if (isset($node['videoRenderer'])) {
        $v = $node['videoRenderer'];
        $videoId = $v['videoId'] ?? null;
        if ($videoId) {
            $title = $v['title']['runs'][0]['text']
            ?? $v['title']['simpleText']
            ?? '';
            $channel = $v['ownerText']['runs'][0]['text']
            ?? $v['longBylineText']['runs'][0]['text']
            ?? '';
            $thumb = "https://i.ytimg.com/vi/{$videoId}/mqdefault.jpg";
            if (!empty($v['thumbnail']['thumbnails']) && is_array($v['thumbnail']['thumbnails'])) {
                $last = end($v['thumbnail']['thumbnails']);
                if (!empty($last['url'])) $thumb = $last['url'];
            }
            $items[] = [
                'id' => ['videoId' => $videoId],
                'snippet' => [
                    'title'        => $title,
                    'channelTitle' => $channel,
                    'thumbnails'   => ['medium' => ['url' => $thumb]]
                ]
            ];
        }
        return;
    }

    // Continuation token (for next page)
    if (isset($node['continuationItemRenderer'])) {
        $token = $node['continuationItemRenderer']['continuationEndpoint']['continuationCommand']['token']
        ?? $node['continuationItemRenderer']['continuationEndpoint']['command']['token']
        ?? null;
        if ($token) $nextContinuation = $token;
        return;
    }

    // Recurse
    foreach ($node as $child) {
        if (is_array($child)) {
            na_yt_walk($child, $items, $nextContinuation, $maxResults);
            if (count($items) >= $maxResults) return;
        }
    }
}

// First-page path
$contents = $data['contents']['twoColumnSearchResultsRenderer']['primaryContents']
['sectionListRenderer']['contents'] ?? null;

// Continuation-page path
if (!$contents) {
    $contents = $data['onResponseReceivedCommands'] ?? $data['onResponseReceivedActions'] ?? null;
}

if ($contents) {
    na_yt_walk($contents, $items, $nextContinuation, $maxResults);
}

// ------------------------------------------------------------------
// 6. Return
// ------------------------------------------------------------------
echo json_encode([
    'items'         => $items,
    'continuation'  => $nextContinuation,   // ← null when no more pages
    '_quota' => [
        'used'      => $dailyCount,
        'limit'     => YT_DAILY_LIMIT,
        'remaining' => max(0, YT_DAILY_LIMIT - $dailyCount),
                 'resetsAt'  => date('Y-m-d 00:00:00', strtotime('tomorrow')),
                 'source'    => 'innertube'
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
