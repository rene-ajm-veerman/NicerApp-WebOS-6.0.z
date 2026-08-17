<?php
declare(strict_types=1);

/**
 * Minimal working Screenshots manager for NicerApp
 * MIT licensed
 */
global $naWebOS;
class naScreenshots
{
    public string $cn = 'naScreenshots';

    /** @var object */
    private object $db;

    private string $table;
    private string $siteDataRoot;
    private string $nodeScript;
    private int $lockTimeoutSeconds = 300;

    public function __construct($db = null)
    {
        global $naWebOS;
        //return false;

        $this->table = (str_replace('.','_',$naWebOS->domainFolder) ?? 'default') . '___screenshots';
        //echo 'Table : '.$this->table.PHP_EOL;

        $this->siteDataRoot = (isset($naWebOS) ? str_replace('/domainConfig', '', $naWebOS->domainPath) . '/siteData' : '');

        $this->nodeScript = realpath(dirname(__FILE__) . '/screenshot_other.js')
        ?: realpath(dirname(__FILE__) . '/../businessLogic/screenshot_other.js');

        if ($db instanceof uDB2) {
            $this->db = $db;
        } elseif (is_object($db)) {
            $this->db = $this->wrapOldCouchConnector($db);
        } elseif (isset($naWebOS)) {
            $conn = $naWebOS->dbsAdmin->findConnection('couchdb')
            ?? $naWebOS->dbs->findConnection('couchdb')
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
        return [];

        $url = trim($url);
        if ($url === '') {
            throw new InvalidArgumentException('URL is required');
        }

        $force  = (bool)($options['force']  ?? false);
        $retain = (int)($options['retain'] ?? 0);

        $existing = $this->findByUrl($url);
        //var_dump ($url); var_dump ($existing); echo PHP_EOL;

        if ($existing && !$force) {
            $status = $existing['status'] ?? '';

            if ($status === 'ready' && $retain > 0) {
                $createdTs = strtotime($existing['created'] ?? $existing['updated'] ?? '0');
                if ((time() - $createdTs) < $retain) {
                    return $existing;
                }
            }

            if (in_array($status, ['pending', 'processing'], true)) {
                return $existing;
            }
        }

        $paths = $this->buildFilePath($url);
        $now   = date('Y-m-d H:i:s');

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

        var_dump ($this->table); var_dump ($existing); echo PHP_EOL;
        $this->db->cdb->setDatabase ($this->table);
        if ($existing) {
            $this->db->updateMany(['url' => $url], ['$set' => $job]);
            $job['_id'] = $existing['_id'] ?? null;
        } else {
            $res = $this->db->insertOne($job);
            var_dump ($res); echo PHP_EOL;
            $job['_id'] = $res['_id'] ?? null;
        }

        //var_dump ($job); echo PHP_EOL;
        return $job;
    }

    public function findByUrl(string $url): ?array
    {
        return $this->db->findOne(['url' => $url]);
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
            echo 'Job : '.PHP_EOL.json_encode($job,JSON_PRETTY_PRINT).PHP_EOL;
            if (!$job) break;

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
            if ($sleepSeconds > 0) sleep($sleepSeconds);
        }

        return $summary;
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
            // -------------------------------------------------
            // Detect driver
            // -------------------------------------------------
            $isCouch = false;

            // Adjust these checks to match how your uDB2 instance exposes the driver
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

        // Get the raw CouchDB connector
        $db  = $naWebOS->dbs->findConnection('couchdb');
        $cdb = $db->cdb;

        // Use the same naming convention as the rest of NicerApp
        $dbName = $db->dataSetName('screenshots');   // → something like "nicerapp_screenshots" or similar
        var_dump ($dbName); echo PHP_EOL;

        // 1. Create the database if it does not exist
        try {
            $cdb->setDatabase($dbName, true);   // true = create if missing
            $result['database'] = "Created or already exists: {$dbName}";
        } catch (Throwable $e) {
            $result['errors'][] = "Database creation failed: " . $e->getMessage();
            return $result;
        }

        // 2. Create indexes
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
                $cdb->setIndex($def);          // or $cdb->createIndex($def) depending on your connector
                $result['indexes'][] = "Created index: {$def['name']}";
            } catch (Throwable $e) {
                // Index already exists is fine
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

        $table = $this->table;   // usually 'screenshots'

        // 1. Create the table if it does not exist
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
                // Adjust to however your uDB2 / ADOdb / mysqli wrapper runs raw SQL
                $this->db->query($createTableSql);          // or $this->db->Execute(...) etc.
                $result['database'] = "Table `{$table}` created or already exists";
            } catch (Throwable $e) {
                $result['errors'][] = "Table creation failed: " . $e->getMessage();
                return $result;
            }

            // 2. Extra safety – try to create the indexes individually
            //    (in case the table already existed without them)
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
    /*
     *     $screenshots = new naScreenshots($uDB2);
     *
     *     $report = $screenshots->createDatabaseAndIndexes();
     *
     *     echo "<pre>";
     *     print_r($report);
     *     echo "</pre>";
     */


    public function claimNextJob(string $workerId = 'default'): ?array
    {
        var_dump ($this->db);
        $jobs = $this->db->find(
            ['status' => 'pending'],
            ['sort' => [['priority' => 'asc'], ['created' => 'asc']], 'limit' => 1]
        );
        //echo 't77:'; var_dump ($jobs); echo PHP_EOL;
        return $jobs[0];

        if (empty($jobs)) return null;

        $job = $jobs[0];
        $now = date('Y-m-d H:i:s');

        $updated = $this->db->updateMany(
            ['url' => $job['url'], 'status' => 'pending'],
            ['$set' => [
                'status'   => 'processing',
                'lockedAt' => $now,
                'lockedBy' => $workerId,
                'updated'  => $now,
                'attempts' => ($job['attempts'] ?? 0) + 1
            ]]
        );

        if ($updated < 1) return null;

        $job['status']   = 'processing';
        $job['lockedAt'] = $now;
        $job['lockedBy'] = $workerId;
        $job['attempts'] = ($job['attempts'] ?? 0) + 1;

        return $job;
    }

    public function processJob(array $job): array
    {
        echo 'Processing job : '.PHP_EOL.json_encode($job,JSON_PRETTY_PRINT).PHP_EOL;
        $url   = $job['url'];
        $paths = $this->buildFilePath($url);
        $this->ensureDirectory($paths['dir']);

        if (
            strpos($url,'https://said.by')!==false
            || strpos($url,'https://nicer.app')!==false
        ) {
            $s = realpath(dirname(__FILE__) . '/screenshot_nicerAppServers.js')
            ?: realpath(dirname(__FILE__) . '/../businessLogic/screenshot_nicerAppServers.js');
        } else {
            $s = $this->nodeScript;
        }

        $cmd = sprintf(
            'node %s %s %s 2>&1',
            escapeshellarg($s),
            escapeshellarg($url),
            escapeshellarg($paths['absolute'])
        );

        $output = [];
        $returnCode = 0;
        exec($cmd, $output, $returnCode);
        $dbg = [
            '$cmd' => $cmd,
            '$output' => $output,
            '$rc' => $returnCode
        ];
        var_dump($dbg);

        $success = ($returnCode === 0 && file_exists($paths['absolute']));
        $now = date('Y-m-d H:i:s');

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
            $attempts    = (int)($job['attempts'] ?? 1);
            $maxAttempts = (int)($job['maxAttempts'] ?? 3);

            $update = [
                'status'   => ($attempts >= $maxAttempts) ? 'failed' : 'pending',
                'lockedAt' => null,
                'lockedBy' => null,
                'error'    => implode("\n", $output),
                'updated'  => $now,
            ];
        }

        $this->db->updateMany(['url' => $url], ['$set' => $update]);
        return array_merge($job, $update);
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
        if ($pad) $base64 .= str_repeat('=', 4 - $pad);

        $url = base64_decode($base64, true);
        if ($url === false) {
            throw new InvalidArgumentException("Invalid filename: {$filename}");
        }
        return $url;
    }

    public function buildFilePath(string $url, ?DateTimeInterface $date = null): array
    {
        $date = $date ?? new DateTimeImmutable('now');
        $year  = $date->format('Y');
        $month = $date->format('m');
        $day   = $date->format('d');

        $filename = $this->urlToFilename($url);
        $relative = "screenshots/{$year}/{$month}/{$day}/{$filename}";
        $absolute = rtrim($this->siteDataRoot, '/') . '/' . $relative;

        return [
            'absolute'  => $absolute,
            'relative'  => $relative,
            'dir'       => dirname($absolute),
            'filename'  => $filename,
        ];
    }

    public function ensureDirectory(string $dir): void
    {
        echo "Creating directory : $dir";
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException("Cannot create directory: {$dir}");
        }
    }

    // ------------------------------------------------------------------
    // Compatibility wrapper for old CouchDB connector
    // ------------------------------------------------------------------
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
                $this->table = $y = ($naWebOS->domainFolder ?? 'default') . '___screenshots';
                $this->cdb = (property_exists($old, 'cdb') && is_object($old->cdb))
                ? $old->cdb
                : $old;
                //echo '<pre>';
                $x = $this->cdb->connections[0];
                //  echo 't333:'; var_dump ($x); echo PHP_EOL;
                //echo 't334:'; var_dump ($x['conn']); echo PHP_EOL;
                //echo 't337:'; var_dump ($this->cdb); echo PHP_EOL;
                //debug_print_backtrace();
                //echo '</pre>';
                if ($this->cdb instanceof class_NicerAppWebOS_database_API) {
                    $this->cdb->connections[0]['conn']->cdb->setDatabase ($y);
                } else {
                    $this->cdb->setDatabase ($y);
                }
                //$this->cdb->connections[0]['conn']->cdb->setDatabase ($y);
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

            public function find(array $filter = [], array $options = []): array
            {
                $mango = [
                    'selector' => empty($filter) ? new \stdClass() : $filter,
                    'limit'    => $options['limit'] ?? 50,
                ];
                if (!empty($options['sort'])) $mango['sort'] = $options['sort'];

                try {
                    echo (json_encode($mango,JSON_PRETTY_PRINT));
                    if ($this->cdb instanceof class_NicerAppWebOS_database_API) {
                        $result = $this->cdb->connections[0]['conn']->cdb->find ($mango);
                    } else {
                        $result = $this->cdb->find ($mango);
                    }

                    $docs = $result->body->docs ?? [];
                    return json_decode(json_encode($docs), true) ?: [];
                } catch (Throwable $e) {
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
            }

            public function updateMany(array $filter, array $update, array $options = []): int
            {
                $docs = $this->find($filter, ['limit' => 100]);
                $set  = $update['$set'] ?? $update;
                $count = 0;

                foreach ($docs as $doc) {
                    if (empty($doc['_id'])) continue;
                    try {
                        $current = $this->cdb->get($doc['_id']);
                        $merged = array_merge($doc, $set);
                        $merged['_rev'] = $current->body->_rev ?? null;
                        $this->cdb->put($doc['_id'], $merged);
                        $count++;
                    } catch (Throwable $e) {}
                }
                return $count;
            }

            public function deleteOne(array $filter): bool
            {
                $doc = $this->findOne($filter);
                if (!$doc || empty($doc['_id'])) return false;
                try {
                    $current = $this->cdb->get($doc['_id']);
                    $this->cdb->delete($current->body->_id, $current->body->_rev);
                    return true;
                } catch (Throwable $e) {
                    return false;
                }
            }
        };
    }
}
