<?php
declare(strict_types=1);

/**
 * Redis-backed cache layer for naScreenshots
 * Falls back gracefully when Redis is unavailable.
 */
trait naScreenshotsRedisCache
{
    /** @var \Predis\Client|\Redis|null */
    private $redis = null;

    private string $redisPrefix = 'na:ss:';
    private int $defaultTtl = 604800; // 7 days

    /**
     * Lazy-connect to Redis. Supports both Predis and the phpredis extension.
     */
    private function redis(): ?object
    {
        if ($this->redis !== null) {
            return $this->redis;
        }

        try {
            // Prefer Predis (pure PHP) if available
            if (class_exists('\Predis\Client')) {
                $this->redis = new \Predis\Client([
                    'scheme' => 'tcp',
                    'host'   => getenv('REDIS_HOST') ?: '127.0.0.1',
                    'port'   => (int)(getenv('REDIS_PORT') ?: 6379),
                    'password' => getenv('REDIS_PASSWORD') ?: null,
                    'database' => (int)(getenv('REDIS_DB') ?: 0),
                ]);
                $this->redis->ping();
                return $this->redis;
            }

            // Fallback to phpredis extension
            if (class_exists('\Redis')) {
                $r = new \Redis();
                $r->connect(
                    getenv('REDIS_HOST') ?: '127.0.0.1',
                    (int)(getenv('REDIS_PORT') ?: 6379),
                    1.5
                );
                if ($pw = getenv('REDIS_PASSWORD')) {
                    $r->auth($pw);
                }
                $r->select((int)(getenv('REDIS_DB') ?: 0));
                $r->ping();
                $this->redis = $r;
                return $this->redis;
            }
        } catch (Throwable $e) {
            // Redis not available – degrade silently
            $this->redis = false; // mark as permanently unavailable for this request
        }

        return null;
    }

    private function cacheKey(string $type, string $url): string
    {
        return $this->redisPrefix . $type . ':' . md5($url);
    }

    private function cacheGet(string $type, string $url): ?array
    {
        $r = $this->redis();
        if (!$r) return null;

        try {
            $raw = $r->get($this->cacheKey($type, $url));
            if ($raw === false || $raw === null) return null;
            $data = json_decode($raw, true);
            return is_array($data) ? $data : null;
        } catch (Throwable $e) {
            return null;
        }
    }

    private function cacheSet(string $type, string $url, array $data, int $ttl = 0): void
    {
        $r = $this->redis();
        if (!$r) return;

        $ttl = $ttl > 0 ? $ttl : $this->defaultTtl;

        try {
            $r->setex(
                $this->cacheKey($type, $url),
                $ttl,
                json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        } catch (Throwable $e) {
            // ignore
        }
    }

    private function cacheDelete(string $type, string $url): void
    {
        $r = $this->redis();
        if (!$r) return;

        try {
            $r->del($this->cacheKey($type, $url));
        } catch (Throwable $e) {
            // ignore
        }
    }

    /**
     * Invalidate all cache entries for a URL
     */
    public function invalidateCache(string $url): void
    {
        $this->cacheDelete('url', $url);
        $this->cacheDelete('file', $url);
        $this->cacheDelete('ready', $url);
    }
}

/**
 * Minimal working Screenshots manager for NicerApp
 * MIT licensed
 */
global $naWebOS;
class naScreenshots
{
    public string $cn = 'naScreenshots';
    use naScreenshotsRedisCache;

    /** @var object */
    private object $db;

    private string $table;
    private string $siteDataRoot;
    private string $nodeScript;
    private int $lockTimeoutSeconds = 300;

    public function __construct($db = null)
    {
        global $naWebOS;

        $this->table = (str_replace('.', '_', $naWebOS->domainFolder ?? 'default')) . '___screenshots';

        $this->siteDataRoot = (isset($naWebOS)
        ? str_replace('/domainConfig', '', $naWebOS->domainPath) . '/siteData'
        : '');

        $this->nodeScript = realpath(dirname(__FILE__) . '/nodeScreenshot.js')
        ?: realpath(dirname(__FILE__) . '/../businessLogic/nodeScreenshot.js');

        if ($db instanceof uDB2) {
            $this->db = $db;
        } elseif (is_object($db)) {
            $conn = $naWebOS->dbsAdmin->findConnection('couchdb')
            ?? $naWebOS->dbsAdmin->findConnection('couchdb')
            ?? null;
            $this->db = $this->wrapOldCouchConnector($conn);
        } elseif (isset($naWebOS)) {
            $conn = $naWebOS->dbsAdmin->findConnection('couchdb')
            ?? $naWebOS->dbsAdmin->findConnection('couchdb')
            ?? null;

            if (!$conn) {
                throw new RuntimeException('No CouchDB connection available');
            }
            $this->db = $this->wrapOldCouchConnector($conn);
        } else {
            throw new InvalidArgumentException('No database connection given');
        }
    }

    // ------------------------------------------------------------------
    // Public API
    // ------------------------------------------------------------------

public function enqueue(string $url, array $options = []): array
{
$url = trim($url);
        if ($url === '') {
            throw new InvalidArgumentException('URL is required');
        }

        $force  = (bool)($options['force']  ?? false);
        $retain = (int)($options['retain'] ?? 0);   // 0 = keep forever (or use defaultTtl)

        // 1. Fast path: Redis cache
        if (!$force) {
            $cached = $this->cacheGet('url', $url);
            if ($cached && ($cached['status'] ?? '') === 'ready') {
                // Respect retain window if set
                if ($retain > 0) {
                    $createdTs = strtotime($cached['created'] ?? $cached['updated'] ?? '0');
                    if ((time() - $createdTs) >= $retain) {
                        // expired → fall through to re-queue
                    } else {
                        return $cached;
                    }
                } else {
                    return $cached; // keep forever
                }
            }
        }

        // 2. Disk existence (also cached)
        $filename = md5($url) . '.png';
        $fileCache = $this->cacheGet('file', $url);
        $historicalPath = $fileCache['path'] ?? null;

        if ($historicalPath === null) {
            $historicalPath = $this->locateExistingFileOnDisk($filename);
            if ($historicalPath) {
                $this->cacheSet('file', $url, ['path' => $historicalPath], 86400);
            }
        }
        $fileExistsOnDisk = ($historicalPath !== null);

        $paths = $this->buildFilePath($url);
        if ($fileExistsOnDisk) {
            $paths['absolute'] = $historicalPath;
            $paths['relative'] = str_replace($this->siteDataRoot . '/', '', $historicalPath);
        }

        $existing = $this->findByUrl($url);   // also Redis-backed now

        if ($existing && !$force) {
            $status = $existing['status'] ?? '';

            if ($status === 'ready' && $fileExistsOnDisk) {
                if ($retain <= 0 || (time() - strtotime($existing['created'] ?? $existing['updated'] ?? '0')) < $retain) {
                    $this->cacheSet('url', $url, $existing, $retain > 0 ? $retain : $this->defaultTtl);
                    return $existing;
                }
            }

            if ($status !== 'ready' && $fileExistsOnDisk) {
                $existing['status']       = 'ready';
                $existing['filePath']     = $paths['absolute'];
                $existing['relativePath'] = $paths['relative'];
                $existing['updated']      = date('Y-m-d H:i:s');

                if (method_exists($this->db, 'setTable')) {
                    $this->db->setTable($this->table);
                }
                $this->db->updateMany(['url' => $url], ['$set' => [
                    'status'       => 'ready',
                    'filePath'     => $paths['absolute'],
                    'relativePath' => $paths['relative'],
                    'updated'      => $existing['updated'],
                ]]);

                $this->cacheSet('url', $url, $existing, $retain > 0 ? $retain : $this->defaultTtl);
                return $existing;
            }

            if (in_array($status, ['pending', 'processing'], true)) {
                return $existing;
            }
	}

	// If no DB job entry exists, but file is sitting on disk, create pre-completed entry
        if (!$existing && $fileExistsOnDisk && !$force) {
            $now = date('Y-m-d H:i:s');
            $job = [
                'url'          => $url,
                'urlHash'      => $paths['filename'],
                'filePath'     => $paths['absolute'],
                'relativePath' => $paths['relative'],
                'width'        => (int)($options['width']  ?? 3840),
                'height'       => (int)($options['height'] ?? 2160),
                'status'       => 'ready',
                'priority'     => (int)($options['priority'] ?? 0),
                'attempts'     => 0,
                'maxAttempts'  => (int)($options['maxAttempts'] ?? 3),
                'lockedAt'     => null,
                'lockedBy'     => null,
                'created'      => $now,
                'updated'      => $now,
                'error'        => null,
                'meta'         => $options['meta'] ?? [],
                'retain'       => $retain,
            ];

            if (method_exists($this->db, 'setTable')) {
                $this->db->setTable($this->table);
            }
            $res = $this->db->insertOne($job);
            $job['_id'] = $res['_id'] ?? null;
            return $job;
        }

        // 3. Standard Generation Fallback Path
        $now = date('Y-m-d H:i:s');

        $job = [
            'url'          => $url,
            'urlHash'      => $paths['filename'],
            'filePath'     => $paths['absolute'],
            'relativePath' => $paths['relative'],
            'width'        => (int)($options['width']  ?? 3840),
            'height'       => (int)($options['height'] ?? 2160),
            'status'       => 'pending',
            'priority'     => (int)($options['priority'] ?? 0),
            'attempts'     => 0,
            'maxAttempts'  => (int)($options['maxAttempts'] ?? 3),
            'lockedAt'     => null,
            'lockedBy'     => null,
            'created'      => $now,
            'updated'      => $now,
            'error'        => null,
            'meta'         => $options['meta'] ?? [],
            'retain'       => $retain,
        ];

        if (method_exists($this->db, 'setTable')) {
            $this->db->setTable($this->table);
        }

        if ($existing) {
            $this->db->updateMany(['url' => $url], ['$set' => $job]);
            $job['_id'] = $existing['_id'] ?? null;
        } else {
            $res = $this->db->insertOne($job);
            $job['_id'] = $res['_id'] ?? null;
        }

	$this->cacheSet('url', $url, $job, 300); // 5 min while processing
        return $job;
    }


	public function findByUrl(string $url): ?array
    {
        // 1. Redis first
        $cached = $this->cacheGet('url', $url);
        if ($cached !== null) {
            return $cached;
        }

        // 2. DB
        $row = $this->db->findOne(['url' => $url]);

        // 3. Populate cache
        if ($row) {
            $ttl = (int)($row['retain'] ?? 0);
            $this->cacheSet('url', $url, $row, $ttl > 0 ? $ttl : $this->defaultTtl);
        }

        return $row;
    }
    // ------------------------------------------------------------------
    // Internal Path and Optimization Engines
    // ------------------------------------------------------------------

    /**
     * Call this from processJob() after a successful capture
     */
    public function markReady(array $job): void
    {
        $url = $job['url'] ?? '';
        if ($url === '') return;

        $ttl = (int)($job['retain'] ?? 0);
        $this->cacheSet('url', $url, $job, $ttl > 0 ? $ttl : $this->defaultTtl);

        if (!empty($job['filePath'])) {
            $this->cacheSet('file', $url, ['path' => $job['filePath']], 86400);
        }
    }

    /**
     * Call this on failure or when force-refreshing
     */
    public function markInvalid(string $url): void
    {
        $this->invalidateCache($url);
    }

    /**
     * Searches recursively within siteData/screenshots directory for an existing filename
     */
    private function locateExistingFileOnDisk(string $filename): ?string
    {
        $baseDir = $this->siteDataRoot . '/screenshots';
        if (!is_dir($baseDir)) {
            return null;
        }

        // Use a recursive directory iterator to locate old files matching our MD5 hash
        $directory = new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === $filename) {
                return $file->getPathname();
            }
        }

        return null;
    }


    public function processQueue(array $options = []): array
    {
        $maxJobs      = (int)($options['maxJobs'] ?? 5);
        $workerId     = $options['workerId'] ?? ('php-' . getmypid());
        $sleepSeconds = (int)($options['sleepSeconds'] ?? 0);
        $verbose      = $options['verbose'] ?? false;

        $summary = [
            'processed' => 0,
            'succeeded' => 0,
            'failed'    => 0,
            'jobs'      => [],
            'errors'    => []
        ];

        $this->releaseStaleLocks();

        for ($i = 0; $i < $maxJobs; $i++) {
            $job = $this->claimNextJob($workerId);
            if (!$job) {
                break;
            }

            if ($verbose) {
                echo 'Job : ' . PHP_EOL . json_encode($job, JSON_PRETTY_PRINT) . PHP_EOL;
            }

            $url = $job['url'] ?? '(unknown)';

            try {
                $result = $this->processJob($job);
                $status = $result['status'] ?? 'unknown';

                $summary['jobs'][] = ['url' => $url, 'status' => $status];

                if ($status === 'ready') {
                    $summary['succeeded']++;
                } else {
                    $summary['failed']++;
                }
            } catch (Throwable $e) {
                $summary['failed']++;
                $summary['errors'][] = ['url' => $url, 'error' => $e->getMessage()];
            }

            $summary['processed']++;
            if ($sleepSeconds > 0) {
                sleep($sleepSeconds);
            }
        }

        return $summary;
    }

    public function releaseStaleLocks(): int
    {
        $cutoff = date('Y-m-d H:i:s', time() - $this->lockTimeoutSeconds);
        $stale  = $this->db->find([
            'status'   => 'processing',
            'lockedAt' => ['$lt' => $cutoff]
        ]);

        $count = 0;
        foreach ($stale as $job) {
            $this->db->updateMany(
                ['url' => $job['url']],
                ['$set' => [
                    'status'   => 'pending',
                    'lockedAt' => null,
                    'lockedBy' => null,
                    'updated'  => date('Y-m-d H:i:s')
                ]]
            );
            $count++;
        }
        return $count;
    }

    // ------------------------------------------------------------------
    // Internal helpers
    // ------------------------------------------------------------------

    /**
     * Create the screenshots database/table and all recommended indexes.
     * Safe to call multiple times (it checks if things already exist).
     *
     * @return array  Summary of what was created / already existed
     */
    public function createDatabaseAndIndexes(): array
    {
        $result = [
            'database' => null,
            'indexes'  => [],
            'errors'   => []
        ];

        try {
            $isCouch = false;

            if (property_exists($this->db, 'isCouchDB') && $this->db->isCouchDB) {
                $isCouch = true;
            } elseif (method_exists($this->db, 'isCouch') && $this->db->isCouch()) {
                $isCouch = true;
            } elseif (isset($this->db->driver) && stripos($this->db->driver, 'couch') !== false) {
                $isCouch = true;
            }

            if ($isCouch) {
                $result = array_merge($result, $this->createCouchDatabaseAndIndexes());
            } else {
                $result = array_merge($result, $this->createSqlDatabaseAndIndexes());
            }
        } catch (Throwable $e) {
            $result['errors'][] = $e->getMessage();
        }

        return $result;
    }

    /**
     * CouchDB version
     */
    private function createCouchDatabaseAndIndexes(): array
    {
        $result = [
            'database' => null,
            'indexes'  => [],
            'errors'   => []
        ];

        global $naWebOS;

        $db  = $naWebOS->dbs->findConnection('couchdb');
        $cdb = $db->cdb;

        $dbName = $db->dataSetName('screenshots');

        try {
            $cdb->setDatabase($dbName, true);   // true = create if missing
            $result['database'] = "Created or already exists: {$dbName}";
        } catch (Throwable $e) {
            $result['errors'][] = "Database creation failed: " . $e->getMessage();
            return $result;
        }

        $indexes = [
            [
                'index' => ['fields' => ['url']],
                'name'  => 'idx-url',
                'type'  => 'json',
                'ddoc'  => 'screenshots-indexes'
            ],
            [
                'index' => ['fields' => ['status', 'created']],
                'name'  => 'idx-status-created',
                'type'  => 'json',
                'ddoc'  => 'screenshots-indexes'
            ],
            [
                'index' => ['fields' => ['status', 'priority', 'created']],
                'name'  => 'idx-queue',
                'type'  => 'json',
                'ddoc'  => 'screenshots-indexes'
            ],
            [
                'index' => ['fields' => ['status', 'updated']],
                'name'  => 'idx-status-updated',
                'type'  => 'json',
                'ddoc'  => 'screenshots-indexes'
            ],
            [
                'index' => ['fields' => ['urlHash']],
                'name'  => 'idx-urlHash',
                'type'  => 'json',
                'ddoc'  => 'screenshots-indexes'
            ]
        ];

        foreach ($indexes as $def) {
            try {
                $cdb->setIndex($def);
                $result['indexes'][] = "Created index: {$def['name']}";
            } catch (Throwable $e) {
                if (stripos($e->getMessage(), 'exists') !== false || stripos($e->getMessage(), 'already') !== false) {
                    $result['indexes'][] = "Already exists: {$def['name']}";
                } else {
                    $result['errors'][] = "Index {$def['name']}: " . $e->getMessage();
                }
            }
        }

        return $result;
    }

    /**
     * SQL (MySQL / MariaDB) version
     */
    private function createSqlDatabaseAndIndexes(): array
    {
        $result = [
            'database' => null,
            'indexes'  => [],
            'errors'   => []
        ];

        $table = $this->table;

        $createTableSql = "
        CREATE TABLE IF NOT EXISTS `{$table}` (
            `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `_id`           VARCHAR(64)     NULL,
            `url`           VARCHAR(2048)   NOT NULL,
            `urlHash`       VARCHAR(512)    NOT NULL,
            `filePath`      VARCHAR(1024)   NULL,
            `relativePath`  VARCHAR(1024)   NULL,
            `width`         INT             DEFAULT 3840,
            `height`        INT             DEFAULT 2160,
            `status`        VARCHAR(32)     NOT NULL DEFAULT 'pending',
            `priority`      INT             NOT NULL DEFAULT 0,
            `attempts`      INT             NOT NULL DEFAULT 0,
            `maxAttempts`   INT             NOT NULL DEFAULT 3,
            `lockedAt`      DATETIME        NULL,
            `lockedBy`      VARCHAR(128)    NULL,
            `created`       DATETIME        NOT NULL,
            `updated`       DATETIME        NOT NULL,
            `error`         TEXT            NULL,
            `meta`          JSON            NULL,
            `retain`        INT             DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_url` (`url`(768)),
            KEY `idx_urlHash` (`urlHash`(255)),
            KEY `idx_status_created` (`status`, `created`),
            KEY `idx_queue` (`status`, `priority`, `created`),
            KEY `idx_status_updated` (`status`, `updated`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";

            try {
                $this->db->query($createTableSql);
                $result['database'] = "Table `{$table}` created or already exists";
            } catch (Throwable $e) {
                $result['errors'][] = "Table creation failed: " . $e->getMessage();
                return $result;
            }

            $extraIndexes = [
                "CREATE UNIQUE INDEX IF NOT EXISTS idx_url ON `{$table}` (url(768))",
                "CREATE INDEX IF NOT EXISTS idx_urlHash ON `{$table}` (urlHash(255))",
                "CREATE INDEX IF NOT EXISTS idx_status_created ON `{$table}` (status, created)",
                "CREATE INDEX IF NOT EXISTS idx_queue ON `{$table}` (status, priority, created)",
                "CREATE INDEX IF NOT EXISTS idx_status_updated ON `{$table}` (status, updated)",
            ];

            foreach ($extraIndexes as $sql) {
                try {
                    $this->db->query($sql);
                    $result['indexes'][] = "OK: " . substr($sql, 0, 60) . "...";
                } catch (Throwable $e) {
                    if (stripos($e->getMessage(), 'Duplicate') !== false || stripos($e->getMessage(), 'exists') !== false) {
                        $result['indexes'][] = "Already exists";
                    } else {
                        $result['errors'][] = $e->getMessage();
                    }
                }
            }

            return $result;
    }

        public function debugState(): void
    {
        echo "table = " . $this->table . "\n";
        echo "db is " . (is_object($this->db) ? get_class($this->db) : gettype($this->db)) . "\n";

        if (is_object($this->db)) {
            echo "db has isCouchDB? " . (property_exists($this->db, 'isCouchDB') ? var_export($this->db->isCouchDB, true) : 'no') . "\n";

            try {
                $pending = $this->db->find(['status' => 'pending'], ['limit' => 10]);
                echo "Pending jobs found: " . count($pending) . "\n";
                print_r($pending);
            } catch (Throwable $e) {
                echo "find() failed: " . $e->getMessage() . "\n";
            }
        } else {
            echo "\$this->db is null or not an object – constructor failed to set it.\n";
        }
    }

    public function claimNextJob(string $workerId = 'default'): ?array
    {
        $jobs = $this->db->find(
            ['status' => 'pending'],
            ['sort' => [['priority' => 'asc'], ['created' => 'asc']], 'limit' => 1]
        );

        if (empty($jobs)) {
            //echo "claimNextJob: no pending jobs\n";
            return null;
        }

        $job = $jobs[0];
        $now = date('Y-m-d H:i:s');
        $id  = $job['_id'];

        echo "<div class=\"phpError\"><span class=\"backdropped\">claimNextJob: attempting to claim {$id}</span><br/>";

        try {
	$step = 0;
            // Use the raw CouchDB client directly
            $cdb = $this->db->cdb;   // or whatever the wrapper exposes
            if (method_exists($this->db, 'getRawCdb')) {
                $cdb = $this->db->getRawCdb();
            }
	$step = 1;

            $cdb->setDatabase($this->table);
	    $step = 1.1;
            $current = $cdb->get($id);
	    $step = 1.2;
            $rev = $current->body->_rev ?? null;
	    $step = 1.3;

            if (!$rev) {
                echo "<span class=\"backdropped\">claimNextJob: no _rev for {$id}</span><br/>";
		$step = 1.4;
                return null;
            }
		    $step = 2;

            $updatedDoc = array_merge($job, [
                'status'   => 'processing',
                'lockedAt' => $now,
                'lockedBy' => $workerId,
                'updated'  => $now,
                'attempts' => ($job['attempts'] ?? 0) + 1,
                                      '_rev'     => $rev
            ]);
	    $step = 3;

            $cdb->put($id, $updatedDoc);
            echo "<span class=\"backdropped\">claimNextJob: successfully claimed {$id}</span></div>";
	    $step = 4;

            return $updatedDoc;

        } catch (Throwable $e) {
            echo "<div class=\"phpError\"><div class\"backdropped phpError\">claimNextJob EXCEPTION ($step): " . $e->getMessage() . "</div>";
	    $t = $e->getTraceAsString();
	    echo '<pre class="backdropped phpError">'.$t.'</pre></div></div>';
            return null;
        }
    }

    public function processJob(array $job): array
    {
        $url = trim($job['url'] ?? '');

        // Fast-fail obviously unusable URLs
        if (
          $url === '' ||
          !filter_var($url, FILTER_VALIDATE_URL) ||
          !preg_match('#^https?://#i', $url) ||
          strlen($url) > 2000
        ) {
            $now = date('Y-m-d H:i:s');
            $update = [
              'status'   => 'failed',
              'error'    => 'Invalid or unusable URL',
              'lockedAt' => null,
              'lockedBy' => null,
              'updated'  => $now,
            ];
            $this->db->updateMany(['_id' => $job['_id']], ['$set' => $update]);
            return array_merge($job, $update);
    	}

        $s = $this->nodeScript;   // currently forced to screenshot_other2.js

        $paths = $this->buildFilePath($url);
        try {
            $this->ensureDirectory($paths['dir']);
        } catch (Exception $e) {
            echo '<div class="phpError">Could not ensureDirectory() : '.$e->getMessage().'</div>';
        }


        $cmd = sprintf(
            'node %s %s %s 2>&1',
            escapeshellarg($s),
                       escapeshellarg($url),
                       escapeshellarg($paths['absolute'])
        );
        $cmd = "node " . escapeshellarg($this->nodeScript) . " " . escapeshellarg($job['url']) . " " . escapeshellarg($job['filePath']) . " 2>&1";
        echo "<div class=\"phpError\">Starting unix process : $cmd</div>";

        $output     = [];
        $returnCode = 0;
        exec($cmd, $output, $returnCode);

        $errorText = implode("\n", $output);
        $success   = ($returnCode === 0 && file_exists($paths['absolute']));
        	$attempts    = (int)($job['attempts'] ?? 1);
	        $maxAttempts = (int)($job['maxAttempts'] ?? 3);
        	$now         = date('Y-m-d H:i:s');
	//if ($success) {

	        $exec = 'convert "'.$job['filePath'].'" -resize 1400 "'.$job['filePath'].'_thumb.png"';
        	$output = array(); $result = -1;
	        exec ($exec, $output, $result);
        	$dbg = [ '$exec' => $exec, '$output' => $output, '$result' => $result ];
	        //if ($debug) { echo 'convert : $dbg='; var_dump ($dbg); echo PHP_EOL.PHP_EOL; }
	//};

	if ($success) {
    		$this->markReady(array_merge($job, $update));   // ← add this
	} else {
    		$this->markInvalid($url);                       // ← optional
	}

        if ($success) {
            $update = [
                'status'       => 'ready',
                'filePath'     => $paths['absolute'],
                'relativePath' => $paths['relative'],
                'lockedAt'     => null,
                'lockedBy'     => null,
                'error'        => null,
                'updated'      => $now,
            ];
        } else {
            // Decide whether this is a permanent failure
            $permanent = (
                str_contains($errorText, 'ERR_NAME_NOT_RESOLVED') ||
                str_contains($errorText, 'ERR_CONNECTION_REFUSED') ||
                str_contains($errorText, 'net::ERR_') ||
                str_contains($errorText, 'Invalid URL') ||
                str_contains($errorText, 'Navigation timeout') ||
                str_contains($errorText, 'net::ERR_ABORTED')
            );

            // Chrome missing is infrastructure – keep retrying a few times
            if (str_contains($errorText, 'Could not find Chrome')) {
                $permanent = false;
            }

            $update = [
                'status'   => ($attempts >= $maxAttempts || $permanent) ? 'failed' : 'pending',
                'lockedAt' => null,
                'lockedBy' => null,
                'error'    => $errorText,
                'updated'  => $now,
            ];
        }

        // Prefer updating by _id when we have it
        $filter = !empty($job['_id']) ? ['_id' => $job['_id']] : ['url' => $url];
        $this->db->updateMany($filter, ['$set' => $update]);

        return array_merge($job, $update);
    }


    // ------------------------------------------------------------------
    // Path helpers
    // ------------------------------------------------------------------

    public function urlToFilename(string $url): string
    {
        return rtrim(strtr(base64_encode($url), '+/', '-_'), '=') . '.png';
    }

    public function filenameToURL(string $filename): string
    {
        $base64 = preg_replace('/\.png$/i', '', $filename);
        $base64 = strtr($base64, '-_', '+/');
        $pad = strlen($base64) % 4;
        if ($pad) {
            $base64 .= str_repeat('=', 4 - $pad);
        }

        $url = base64_decode($base64, true);
        if ($url === false) {
            throw new InvalidArgumentException("Invalid filename: {$filename}");
        }
        return $url;
    }

    /**
     * Builds standard filesystem paths based on a unique URL token.
     * Uses MD5 to completely prevent ENAMETOOLONG exceptions.
     */
    public function buildFilePath(string $url): array
    {
        // Enforce safe 32-character hashes for system filenames
        $filename = md5($url) . '.png';

        // Structure directory patterns by Date segment (Year/Month/Day)
        $dateSubfolder = date('Y/m/d');

        $relativeDir = 'screenshots/' . $dateSubfolder;
        $absoluteDir = $this->siteDataRoot . '/' . $relativeDir;

        // Auto-create directories safely if they are missing
        if (!is_dir($absoluteDir)) {
            mkdir($absoluteDir, 0775, true);
            // Force the new folder to belong to the www-data group natively
            chgrp($absoluteDir, 'www-data');
            chmod($absoluteDir, 02775); // 2 enables the SetGID bit programmatically
        }

        return [
            'dir' => $absoluteDir,
            'filename' => $filename,
            'relative' => $relativeDir . '/' . $filename,
            'absolute' => $absoluteDir . '/' . $filename
        ];
    }

    public function ensureDirectory(string $dir): void
    {
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            echo "Cannot create directory: {$dir}";
        }
    }


    /**
     * Alias for processQueue() – keeps older scripts working
     */
    public function runWorker(string $workerId = 'default', int $maxJobs = 10, int $sleepSeconds = 2): void
    {
        $this->processQueue([
            'workerId'     => $workerId,
            'maxJobs'      => $maxJobs,
            'sleepSeconds' => $sleepSeconds,
            'verbose'      => true
        ]);
    }

    private function wrapOldCouchConnector(object $old): object
    {
        return new class($old) {
            public $cdb;
            public string $table;
            public bool $isCouchDB = true;

            public function __construct(object $old)
            {
                global $naWebOS;

                $this->table = (str_replace('.','_',$naWebOS->domainFolder) ?? 'default') . '___screenshots';
                $this->cdb = (property_exists($old, 'cdb') && is_object($old->cdb))
                ? $old->cdb
                : $old;

                if ($this->cdb instanceof class_NicerAppWebOS_database_API) {
                    $this->cdb->connections[0]['conn']->cdb->setDatabase($this->table);
                } else {
                    $this->cdb->setDatabase($this->table);
                }
            }

            public function setTable(string $table): self
            {
                $this->table = $table;
                return $this;
            }

            public function findOne(array $filter = [], array $options = []): ?array
            {
                $rows = $this->find($filter, array_merge($options, ['limit' => 1]));
                return $rows[0] ?? null;
            }

            /*public function find(array $filter = [], array $options = []): array
             *            {
             *                $mango = [
             *                    'selector' => empty($filter) ? new \stdClass() : $filter,
             *                    'limit'    => $options['limit'] ?? 50,
             *                ];
             *                if (!empty($options['sort'])) {
             *                    $mango['sort'] = $options['sort'];
        }

        echo "=== WRAPPER find() DEBUG ===\n";
        echo "Using table/db: " . $this->table . "\n";
        echo "Mango query:\n";
        echo json_encode($mango, JSON_PRETTY_PRINT) . "\n";

        try {
        if ($this->cdb instanceof class_NicerAppWebOS_database_API) {
            // Make sure the correct database is selected on the real connection
            $this->cdb->connections[0]['conn']->cdb->setDatabase($this->table);
            $result = $this->cdb->connections[0]['conn']->cdb->find($mango);
        } else {
            $this->cdb->setDatabase($this->table);
            $result = $this->cdb->find($mango);
        }

        echo "Raw result type: " . gettype($result) . "\n";
        if (is_object($result)) {
            echo "Result class: " . get_class($result) . "\n";
            if (isset($result->body)) {
                echo "body keys: " . implode(', ', array_keys((array)$result->body)) . "\n";
        }
        }

        $docs = $result->body->docs ?? [];
        echo "docs count: " . count($docs) . "\n";
        return json_decode(json_encode($docs), true) ?: [];
        } catch (Throwable $e) {
        echo "find() EXCEPTION: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
        return [];
        }
        }

        public function insertOne(array $document, array $options = []): array
        {
        try {
        $result = $this->cdb->post($document);
        return ['ok' => true, '_id' => $result->body->id ?? null];
        } catch (Throwable $e) {
        return ['ok' => false, 'error' => $e->getMessage()];
        }
        }*/

            public function getRawCdb()
            {
                // Return the actual low-level CouchDB client
                if ($this->cdb instanceof class_NicerAppWebOS_database_API) {
                    return $this->cdb->connections[0]['conn']->cdb;
                }
                return $this->cdb;
            }

            public function find(array $filter = [], array $options = []): array
            {
                $mango = [
                    'selector' => empty($filter) ? new \stdClass() : $filter,
                    'limit'    => $options['limit'] ?? 50,
                ];
                if (!empty($options['sort'])) {
                    $mango['sort'] = $options['sort'];
                }

                try {
                    $cdb = $this->getRawCdb();
                    $cdb->setDatabase($this->table);
                    $result = $cdb->find($mango);
                    $docs = $result->body->docs ?? [];
                    return json_decode(json_encode($docs), true) ?: [];
                } catch (Throwable $e) {
                    echo "<p class=\"phpError\">find() EXCEPTION: " . $e->getMessage() . "</p>";
                    return [];
                }
            }

            public function insertOne(array $document, array $options = []): array
            {
                try {
                    $cdb = $this->getRawCdb();
                    $cdb->setDatabase($this->table);
                    $result = $cdb->post($document);
                    return ['ok' => true, '_id' => $result->body->id ?? null];
                } catch (Throwable $e) {
                    return ['ok' => false, 'error' => $e->getMessage()];
                }
            }

            public function updateMany(array $filter, array $update, array $options = []): int
            {
                $docs = $this->find($filter, ['limit' => 100]);
                $set  = $update['$set'] ?? $update;
                $count = 0;
                $cdb = $this->getRawCdb();

                foreach ($docs as $doc) {
                    if (empty($doc['_id'])) continue;
                    try {
                        $cdb->setDatabase($this->table);
                        $current = $cdb->get($doc['_id']);
                        $rev = $current->body->_rev ?? null;
                        if (!$rev) continue;

                        $merged = array_merge($doc, $set);
                        $merged['_rev'] = $rev;
                        $cdb->put($doc['_id'], $merged);
                        $count++;
                    } catch (Throwable $e) {
                        echo "<p class=\"phpError phpErrorType_E_WARNING\">updateMany failed for {$doc['_id']}: " . $e->getMessage() . "</p>";
                    }
                }
                return $count;
            }

            public function deleteOne(array $filter): bool
            {
                $doc = $this->findOne($filter);
                if (!$doc || empty($doc['_id'])) return false;

                try {
                    $cdb = $this->getRawCdb();
                    $cdb->setDatabase($this->table);
                    $current = $cdb->get($doc['_id']);
                    $cdb->delete($current->body->_id, $current->body->_rev);
                    return true;
                } catch (Throwable $e) {
                    return false;
                }
            }        };
    }

}
