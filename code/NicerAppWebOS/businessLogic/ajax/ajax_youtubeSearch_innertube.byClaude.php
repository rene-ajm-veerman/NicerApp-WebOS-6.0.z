<?php
/**
 * ajax_youtubeSearch_innertube.byClaude.php
 *
 * YouTube search WITHOUT an API key (Innertube), with a CouchDB cache.
 *
 * Cache design
 * ------------
 *  - Database : nicer_app___youtube_videos  (created automatically if missing)
 *  - One document per (normalised search term, page number), pages 1..5:
 *        _id = q_<sha1(normalised term)>_p<page>
 *        { type, query, queryNorm, page, items[], continuation, fetchedAt, expiresAt }
 *  - The `continuation` stored in page N's document is the token needed to
 *    fetch page N+1, so page 3 can be fetched later without re-fetching 1 and 2.
 *  - Cache hits cost NOTHING against the daily / burst limits. Only live
 *    Innertube calls are counted.
 *  - Expired documents are refreshed on the next request. If the live refresh
 *    fails (rate limit, YouTube down), the stale copy is served instead.
 *  - Pages beyond 5 are fetched live using the client-supplied `continuation`
 *    token and are NOT cached.
 *
 * Parameters
 * ----------
 *  q             string  required
 *  page          int     1-based page number (default 1)
 *  continuation  string  only used for page > 5 (take it from the previous response)
 *
 * Response
 * --------
 *  { items[], continuation, page, hasMore, cached, stale?, cachedAt?, _quota{} }
 */

require_once(realpath(dirname(__DIR__).'/../boot.php')); // adjust if needed

global $naWebOS;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// ------------------------------------------------------------------
// Configuration
// ------------------------------------------------------------------
const YT_DAILY_LIMIT    = 200;
const YT_BURST_LIMIT    = 10;           // live calls per minute
const YT_BURST_WINDOW   = 60;           // seconds

const YT_CACHE_DB       = 'nicer_app___youtube_videos';
const YT_CACHE_PAGES    = 5;            // pages 1..5 are cached
const YT_CACHE_TTL      = 7 * 86400;    // seconds before a cached page is refreshed
const YT_MAX_LIVE_PAGE  = 50;           // sanity cap for uncached pages
const YT_CLIENT_VERSION = '2.20241001.00.00';

/**
 * CouchDB connection. I couldn't see how NicerApp's own CouchDB wrapper gets
 * its credentials, so wire this to whatever you already use. Until then it
 * reads environment variables and falls back to a local, unauthenticated CouchDB.
 */
function na_yt_couchCfg() {
    return [
        'url'  => getenv('NA_COUCHDB_URL')  ?: 'http://127.0.0.1:5984',
        'user' => getenv('NA_COUCHDB_USER') ?: '',
        'pass' => getenv('NA_COUCHDB_PASS') ?: '',
    ];
}

// ------------------------------------------------------------------
// Requester identity + rate-limit storage (unchanged logic, now as functions)
// ------------------------------------------------------------------
$loginName = $_SESSION['cdb_loginName'] ?? ($_COOKIE['cdb_loginName'] ?? 'Guest');
$loginName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $loginName);

$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
}
$ipSafe  = str_replace([':', '.'], ['-', '_'], $ip);
$rateKey = ($loginName !== 'Guest' && $loginName !== '') ? 'user_' . $loginName : 'ip_' . $ipSafe;

$rateDir = $naWebOS->domainPath . '/siteCache/youtubeSearchQuota';
if (!is_dir($rateDir)) @mkdir($rateDir, 0750, true);
foreach (glob($rateDir . '/*.json') as $f) {
    if (filemtime($f) < strtotime('-3 days')) @unlink($f);
}

$YT = [
    'ip'        => $ip,
'rateKey'   => $rateKey,
'dailyFile' => $rateDir . '/' . date('Y-m-d') . '_' . $rateKey . '.json',
'burstFile' => $rateDir . '/burst_' . $rateKey . '.json',
];

function na_yt_dailyCount() {
    global $YT;
    if (!is_readable($YT['dailyFile'])) return 0;
    $d = json_decode(file_get_contents($YT['dailyFile']), true);
    return intval($d['count'] ?? 0);
}

function na_yt_writeDaily($count) {
    global $YT;
    file_put_contents($YT['dailyFile'], json_encode([
        'count' => $count, 'last' => date('c'),
                                                    'identity' => $YT['rateKey'], 'ip' => $YT['ip']
    ], JSON_PRETTY_PRINT), LOCK_EX);
}

function na_yt_burstTimestamps() {
    global $YT;
    $now = time();
    if (!is_readable($YT['burstFile'])) return [];
    $b = json_decode(file_get_contents($YT['burstFile']), true);
    return array_values(array_filter($b['timestamps'] ?? [], fn($ts) => ($now - $ts) < YT_BURST_WINDOW));
}

function na_yt_writeBurst($stamps) {
    global $YT;
    file_put_contents($YT['burstFile'], json_encode([
        'timestamps' => array_values($stamps), 'identity' => $YT['rateKey']
    ], JSON_PRETTY_PRINT), LOCK_EX);
}

/** Reserve one live call. Returns null when allowed, else an error array. */
function na_yt_rlConsume() {
    $daily = na_yt_dailyCount();
    if ($daily >= YT_DAILY_LIMIT) {
        return [
            'http' => 429,
            'error' => 'Daily YouTube search limit reached (' . YT_DAILY_LIMIT . ' per day)',
            'limit' => YT_DAILY_LIMIT, 'used' => $daily,
            'resetsAt' => date('Y-m-d 00:00:00', strtotime('tomorrow'))
        ];
    }
    $stamps = na_yt_burstTimestamps();
    if (count($stamps) >= YT_BURST_LIMIT) {
        return [
            'http' => 429,
            'error' => 'Too many searches in a short time (max ' . YT_BURST_LIMIT . ' per minute)',
            'retryIn' => YT_BURST_WINDOW - (time() - min($stamps))
        ];
    }
    na_yt_writeDaily($daily + 1);
    $stamps[] = time();
    na_yt_writeBurst($stamps);
    return null;
}

/** Give back a reserved call (live request failed). */
function na_yt_rlRefund() {
    na_yt_writeDaily(max(0, na_yt_dailyCount() - 1));
    $stamps = na_yt_burstTimestamps();
    array_pop($stamps);
    na_yt_writeBurst($stamps);
}

// ------------------------------------------------------------------
// CouchDB helpers
// ------------------------------------------------------------------
function na_yt_couch($method, $path, $body = null) {
    $cfg = na_yt_couchCfg();
    $ch  = curl_init(rtrim($cfg['url'], '/') . '/' . $path);
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_TIMEOUT        => 5,
        CURLOPT_CONNECTTIMEOUT => 2,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
    ];
    if ($cfg['user'] !== '') $opts[CURLOPT_USERPWD] = $cfg['user'] . ':' . $cfg['pass'];
    if ($body !== null) $opts[CURLOPT_POSTFIELDS] = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    curl_setopt_array($ch, $opts);
    $raw  = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    return [$code, ($raw === false || $raw === '') ? null : json_decode($raw, true)];
}

function na_yt_cacheId($norm, $page) {
    return 'q_' . sha1($norm) . '_p' . $page;
}

/** Returns the stored document, or null (also null when CouchDB is down: the cache is optional). */
function na_yt_cacheGet($id) {
    [$code, $doc] = na_yt_couch('GET', YT_CACHE_DB . '/' . rawurlencode($id));
    return ($code === 200 && is_array($doc)) ? $doc : null;
}

function na_yt_cachePut($id, array $doc, $rev = null) {
    if ($rev) $doc['_rev'] = $rev;
    $path = YT_CACHE_DB . '/' . rawurlencode($id);
    [$code] = na_yt_couch('PUT', $path, $doc);
    if ($code === 404) {                       // database doesn't exist yet: create it, retry once
        na_yt_couch('PUT', YT_CACHE_DB);
        [$code] = na_yt_couch('PUT', $path, $doc);
    }
    // 409 = another request wrote the same page a moment ago; that's fine.
    return in_array($code, [201, 202], true);
}

// ------------------------------------------------------------------
// Innertube
// ------------------------------------------------------------------
/** Live call. Returns ['ok'=>true,'data'=>array] or ['ok'=>false,'http'=>..,'error'=>..]. */
function na_yt_live(array $extra) {
    if ($err = na_yt_rlConsume()) return ['ok' => false] + $err;

    $payload = ['context' => ['client' => [
        'clientName' => 'WEB', 'clientVersion' => YT_CLIENT_VERSION, 'hl' => 'en', 'gl' => 'US'
    ]]] + $extra;

    $ch = curl_init('https://www.youtube.com/youtubei/v1/search?prettyPrint=false');
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
    $raw      = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);

    $data = ($raw !== false) ? json_decode($raw, true) : null;
    if ($raw === false || $httpCode !== 200 || !is_array($data)) {
        na_yt_rlRefund();
        return ['ok' => false, 'http' => 502, 'error' => 'Innertube request failed',
        'httpCode' => $httpCode, 'curl' => $curlErr];
    }
    return ['ok' => true, 'data' => $data];
}

/** Walk the whole page: collect every videoRenderer and the continuation token. No item cap. */
function na_yt_walk($node, &$items, &$nextContinuation) {
    if (!is_array($node)) return;

    if (isset($node['videoRenderer'])) {
        $v = $node['videoRenderer'];
        $videoId = $v['videoId'] ?? null;
        if ($videoId && !isset($items[$videoId])) {
            $title   = $v['title']['runs'][0]['text'] ?? $v['title']['simpleText'] ?? '';
            $channel = $v['ownerText']['runs'][0]['text'] ?? $v['longBylineText']['runs'][0]['text'] ?? '';
            $thumb   = "https://i.ytimg.com/vi/{$videoId}/mqdefault.jpg";
            if (!empty($v['thumbnail']['thumbnails']) && is_array($v['thumbnail']['thumbnails'])) {
                $last = end($v['thumbnail']['thumbnails']);
                if (!empty($last['url'])) $thumb = $last['url'];
            }
            $items[$videoId] = [
                'id'      => ['videoId' => $videoId],
                'snippet' => [
                    'title'        => $title,
                    'channelTitle' => $channel,
                    'thumbnails'   => ['medium' => ['url' => $thumb]]
                ]
            ];
        }
        return;
    }

    if (isset($node['continuationItemRenderer'])) {
        $token = $node['continuationItemRenderer']['continuationEndpoint']['continuationCommand']['token']
        ?? $node['continuationItemRenderer']['continuationEndpoint']['command']['token']
        ?? null;
        if ($token) $nextContinuation = $token;
        return;
    }

    foreach ($node as $child) {
        if (is_array($child)) na_yt_walk($child, $items, $nextContinuation);
    }
}

/** @return array [items (list), continuation|null] */
function na_yt_parse(array $data) {
    $items = [];
    $next  = null;
    $contents = $data['contents']['twoColumnSearchResultsRenderer']['primaryContents']
    ['sectionListRenderer']['contents']
    ?? $data['onResponseReceivedCommands']
    ?? $data['onResponseReceivedActions']
    ?? $data;
    na_yt_walk($contents, $items, $next);
    return [array_values($items), $next];
}

// ------------------------------------------------------------------
// Cached page retrieval (pages 1..YT_CACHE_PAGES)
// ------------------------------------------------------------------
/**
 * @return array ['ok'=>true, items, continuation, cached, cachedAt, stale?]
 *            or ['ok'=>false, http, error, ...]
 */
function na_yt_getPage($q, $norm, $page) {
    $id    = na_yt_cacheId($norm, $page);
    $doc   = na_yt_cacheGet($id);
    $fresh = $doc && ($doc['expiresAt'] ?? 0) > time();

    $fromDoc = function ($stale) use ($doc) {
        return [
            'ok' => true, 'items' => $doc['items'] ?? [], 'continuation' => $doc['continuation'] ?? null,
            'cached' => true, 'cachedAt' => $doc['fetchedAt'] ?? null
        ] + ($stale ? ['stale' => true] : []);
    };

    if ($fresh) return $fromDoc(false);

    // Need a live fetch. Page 1 starts from the query; later pages from the previous page's token.
    if ($page === 1) {
        $extra = ['query' => $q];
    } else {
        $prev = na_yt_getPage($q, $norm, $page - 1);
        if (!$prev['ok']) return $doc ? $fromDoc(true) : $prev;
        if (empty($prev['continuation'])) {
            return ['ok' => true, 'items' => [], 'continuation' => null, 'cached' => $prev['cached'], 'cachedAt' => null];
        }
        $extra = ['continuation' => $prev['continuation']];
    }

    $live = na_yt_live($extra);
    if (!$live['ok']) return $doc ? $fromDoc(true) : $live;

    [$items, $next] = na_yt_parse($live['data']);

    na_yt_cachePut($id, [
        'type'         => 'searchPage',
        'query'        => $q,
        'queryNorm'    => $norm,
        'page'         => $page,
        'items'        => $items,
        'continuation' => $next,
        'fetchedAt'    => date('c'),
                   'expiresAt'    => time() + YT_CACHE_TTL,
    ], $doc['_rev'] ?? null);

    return ['ok' => true, 'items' => $items, 'continuation' => $next, 'cached' => false, 'cachedAt' => null];
}

// ------------------------------------------------------------------
// Request handling
// ------------------------------------------------------------------
$q            = trim($_REQUEST['q'] ?? '');
$page         = max(1, min(YT_MAX_LIVE_PAGE, intval($_REQUEST['page'] ?? 1)));
$continuation = trim($_REQUEST['continuation'] ?? '');

if (mb_strlen($q) > 200) $q = mb_substr($q, 0, 200);
if ($q === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or empty parameter: q']);
    exit;
}

$norm = mb_strtolower(trim(preg_replace('/\s+/u', ' ', $q)));

if ($page <= YT_CACHE_PAGES) {
    $r = na_yt_getPage($q, $norm, $page);
} else {
    // Beyond the cached range: live only, using the token the client got from the previous page.
    if ($continuation === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Pages beyond ' . YT_CACHE_PAGES . ' need the continuation parameter']);
        exit;
    }
    $live = na_yt_live(['continuation' => $continuation]);
    if ($live['ok']) {
        [$items, $next] = na_yt_parse($live['data']);
        $r = ['ok' => true, 'items' => $items, 'continuation' => $next, 'cached' => false, 'cachedAt' => null];
    } else {
        $r = $live;
    }
}

if (!$r['ok']) {
    http_response_code($r['http'] ?? 502);
    unset($r['ok'], $r['http']);
    echo json_encode($r, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

$used = na_yt_dailyCount();
echo json_encode([
    'items'        => $r['items'],
    'continuation' => $r['continuation'],
    'page'         => $page,
    'hasMore'      => !empty($r['continuation']),
                 'cached'       => $r['cached'],
                 'cachedAt'     => $r['cachedAt'] ?? null,
                 'stale'        => $r['stale'] ?? false,
                 '_quota'       => [
                     'used'      => $used,
                 'limit'     => YT_DAILY_LIMIT,
                 'remaining' => max(0, YT_DAILY_LIMIT - $used),
                 'resetsAt'  => date('Y-m-d 00:00:00', strtotime('tomorrow')),
                 'source'    => $r['cached'] ? 'cache' : 'innertube'
                 ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
