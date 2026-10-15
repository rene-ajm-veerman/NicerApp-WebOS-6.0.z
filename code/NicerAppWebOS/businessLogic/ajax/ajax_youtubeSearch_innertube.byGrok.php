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
$q = trim($_REQUEST['q'] ?? '');
$maxResults = max(1, min(30, intval($_REQUEST['maxResults'] ?? 20)));

if ($q === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or empty parameter: q']);
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
'clientVersion' => '2.20241001.00.00',   // keep reasonably recent
'hl'            => 'en',
'gl'            => 'US',
        ]
    ],
'query' => $q,
];

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
// curl_close() is deprecated in PHP 8.5 and has been a no-op since 8.0

if ($raw === false || $httpCode !== 200) {
    // optional: refund daily count on hard failure
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
// 5. Parse video results from the (quite nested) response
// ------------------------------------------------------------------
$items = [];

$contents = $data['contents']['twoColumnSearchResultsRenderer']['primaryContents']
['sectionListRenderer']['contents'] ?? [];

foreach ($contents as $section) {
    $sectionItems = $section['itemSectionRenderer']['contents'] ?? [];
    foreach ($sectionItems as $entry) {
        if (!isset($entry['videoRenderer'])) continue;
        $v = $entry['videoRenderer'];

        $videoId = $v['videoId'] ?? null;
        if (!$videoId) continue;

        $title = '';
        if (isset($v['title']['runs'][0]['text'])) {
            $title = $v['title']['runs'][0]['text'];
        } elseif (isset($v['title']['simpleText'])) {
            $title = $v['title']['simpleText'];
        }

        $channel = '';
        if (isset($v['ownerText']['runs'][0]['text'])) {
            $channel = $v['ownerText']['runs'][0]['text'];
        } elseif (isset($v['longBylineText']['runs'][0]['text'])) {
            $channel = $v['longBylineText']['runs'][0]['text'];
        }

        $thumb = "https://i.ytimg.com/vi/{$videoId}/mqdefault.jpg";
        if (isset($v['thumbnail']['thumbnails']) && is_array($v['thumbnail']['thumbnails'])) {
            $last = end($v['thumbnail']['thumbnails']);
            if (!empty($last['url'])) $thumb = $last['url'];
        }

        // Build a shape that is almost identical to the official API
        // so your existing renderYouTubeResults() keeps working
        $items[] = [
            'id' => ['videoId' => $videoId],
            'snippet' => [
                'title'         => $title,
                'channelTitle'  => $channel,
                'thumbnails'    => [
                    'medium' => ['url' => $thumb]
                ]
            ]
        ];

        if (count($items) >= $maxResults) break 2;
    }
}

// ------------------------------------------------------------------
// 6. Return
// ------------------------------------------------------------------
echo json_encode([
    'items'  => $items,
    '_quota' => [
        'used'      => $dailyCount,
        'limit'     => YT_DAILY_LIMIT,
        'remaining' => max(0, YT_DAILY_LIMIT - $dailyCount),
                 'resetsAt'  => date('Y-m-d 00:00:00', strtotime('tomorrow')),
                 'source'    => 'innertube'
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
