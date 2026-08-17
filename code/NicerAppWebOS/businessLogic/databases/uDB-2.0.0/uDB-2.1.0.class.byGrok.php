<?php
declare(strict_types=1);

/**
 * uDB2 - Universal Database Layer 2.0 (by Grok)
 * MongoDB-style query interface supporting ADOdb (SQL) + CouchDB
 * Designed for NicerApp WebOS
 */

class uDB2
{
    private $db;                    // ADOConnection or CouchDB connector
    private string $driver;         // 'mysqli', 'postgres', 'couchdb', ...
    private bool $isCouchDB = false;
    private string $tablePrefix = '';
    private array $config = [];
    private ?object $couchConnector = null; // instance of class_NicerAppWebOS_database_API_couchdb_...

    public string $cn = 'uDB2';

    // ====================== CONNECTION & INITIALIZATION ======================

    public static function createFromConfig(array $cRec, string $username = 'Guest'): self
    {
        $driver = strtolower($cRec['driver'] ?? $cRec['dbConnectionType'] ?? 'mysqli');

        if (strpos($driver, 'couchdb') !== false) {
            // Use existing CouchDB plugin
            global $naWebOS;
            $instance = new self(null, 'couchdb');
            $instance->config = $cRec;
            $instance->couchConnector = new class_NicerAppWebOS_database_API_couchdb_3_2__2_0_0(
                clone $naWebOS, $username, $cRec
            );
            $instance->isCouchDB = true;
            return $instance;
        }

        // SQL path (existing logic)
        $adodbDriver = match($driver) {
            'mysql', 'mysqli' => 'mysqli',
            'postgresql', 'pgsql', 'postgres' => 'postgres',
            'sqlite' => 'sqlite3',
            default => $driver
        };

        $db = ADOnewConnection($adodbDriver);
        if (!$db) {
            throw new RuntimeException("Failed to create ADOdb connection for driver: $adodbDriver");
        }

        // SSL + connection (your existing code)
        if (!empty($cRec['ssl']) || !empty($cRec['config'])) {
            $ssl = $cRec['ssl'] ?? $cRec['config'] ?? [];
            if (isset($ssl['adodb_sslKeyFile']))     $db->ssl_key     = $ssl['adodb_sslKeyFile'];
            if (isset($ssl['adodb_sslCertFile']))    $db->ssl_cert    = $ssl['adodb_sslCertFile'];
            if (isset($ssl['adodb_sslCA']))          $db->ssl_ca      = $ssl['adodb_sslCA'];
            if (isset($ssl['adodb_sslCApath']))      $db->ssl_capath  = $ssl['adodb_sslCApath'];
            if (isset($ssl['adodb_sslCipher']))      $db->ssl_cipher  = $ssl['adodb_sslCipher'];
        }

        $host     = $cRec['host'] ?? '127.0.0.1';
        $user     = $cRec['user'] ?? $cRec['username'] ?? '';
        $password = $cRec['password'] ?? '';
        $database = $cRec['database'] ?? $cRec['dbName'] ?? '';

        $connected = $db->Connect($host, $user, $password, $database);
        if (!$connected) {
            throw new RuntimeException("uDB2 Connection failed: " . $db->ErrorMsg());
        }

        $db->SetFetchMode(ADODB_FETCH_ASSOC);

        if (in_array($driver, ['mysqli', 'mysql'])) {
            $db->Execute("SET NAMES utf8mb4");
            $db->Execute("SET CHARACTER SET utf8mb4");
        }

        $instance = new self($db, $adodbDriver);
        $instance->config = $cRec;
        return $instance;
    }

    public static function connectToDatabase(string $username = 'Guest', string $connectionType = 'adodb', ?array $cRec = null): self
    {
        global $naWebOS;

        if ($cRec === null) {
            $domainConfigsPath = realpath(__DIR__ . '/../../../../domains/' . ($naWebOS->domainFolder ?? 'default') . '/domainConfig/');
            $configFile = $domainConfigsPath . 'databases.username-' . $username . '.json';

            if (!file_exists($configFile)) {
                $configFile = $domainConfigsPath . 'databases.username-Guest.json';
            }

            $config = safeLoadJSONfile($configFile) ?? [];
            $cRec = $config['databases'][$connectionType] ?? [];
        }

        return self::createFromConfig($cRec, $username);
    }

    public function __construct($connection = null, string $driver = 'mysqli')
    {
        $this->driver = strtolower($driver);
        $this->isCouchDB = (strpos($this->driver, 'couchdb') !== false);

        if (!$this->isCouchDB) {
            $this->db = $connection; // ADOConnection
        }
        // CouchDB connector is set in createFromConfig
    }

    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    // ====================== COUCHDB / SQL ROUTING ======================

    private function isCouch(): bool
    {
        return $this->isCouchDB && $this->couchConnector !== null;
    }

    // ====================== QUERY METHODS (MongoDB-style) ======================

    public function find(array $filter = [], array $options = []): array
    {
        if ($this->isCouch()) {
            return $this->couchFind($filter, $options);
        }
        // Existing SQL implementation (your translateQueryToWhere etc.)
        return $this->sqlFind($filter, $options);
    }

    public function updateMany(array $filter, array $update): int
    {
        if ($this->isCouch()) {
            return $this->couchUpdateMany($filter, $update);
        }
        // your existing SQL updateMany
        $where = $this->translateQueryToWhere($filter);
        $upd = $this->buildUpdateSql($update);
        $sql = "UPDATE `{$this->tablePrefix}{$this->table}` {$upd['sql']} " .
        ($where['sql'] ? "WHERE {$where['sql']}" : "");
        return $this->execute($sql, array_merge($upd['params'], $where['params']));
    }

    // Add similar wrappers for updateOne, insert, delete, etc.

    // ====================== COUCHDB IMPLEMENTATIONS ======================

    private function couchFind(array $filter, array $options = []): array
    {
        $dbName = $this->config['database'] ?? $this->table ?? 'default';
        $this->couchConnector->cdb->setDatabase($dbName);

        $mango = [
            'selector' => $this->translateToMango($filter),
            'limit' => $options['limit'] ?? 100,
            'skip' => $options['skip'] ?? 0,
        ];

        if (!empty($options['fields'])) {
            $mango['fields'] = $options['fields'];
        }
        if (!empty($options['sort'])) {
            $mango['sort'] = $options['sort'];
        }

        try {
            $result = $this->couchConnector->cdb->find($mango);
            return $result->body->docs ?? [];
        } catch (Exception $e) {
            trigger_error("CouchDB find error: " . $e->getMessage(), E_USER_WARNING);
            return [];
        }
    }

    private function couchUpdateMany(array $filter, array $update): int
    {
        // Simplified – for production you'd want bulk ops + conflict handling
        $docs = $this->couchFind($filter);
        $updated = 0;

        foreach ($docs as $doc) {
            foreach ($update as $key => $value) {
                if (str_starts_with($key, '$')) {
                    // handle $set, $inc, etc. minimally
                    if ($key === '$set') {
                        foreach ($value as $k => $v) $doc->$k = $v;
                    }
                } else {
                    $doc->$key = $value;
                }
            }
            try {
                $this->couchConnector->cdb->put($doc);
                $updated++;
            } catch (Exception $e) {}
        }
        return $updated;
    }

    private function translateToMango(array $query): array
    {
        // Basic translation from your Mongo-style queries to CouchDB Mango
        // Extend as needed (supports $and, $or, $eq, $gt, etc.)
        if (empty($query)) return (object)[];

        $mango = [];
        foreach ($query as $field => $value) {
            if (str_starts_with($field, '$')) {
                // logical operators
                $mango['$' . substr($field, 1)] = array_map([$this, 'translateToMango'], $value);
            } else {
                $mango[$field] = is_array($value) ? $value : ['$eq' => $value];
            }
        }
        return $mango;
    }

    // ====================== SQL FALLBACKS (your original methods) ======================

    private function sqlFind(array $filter, array $options = []): array
    {
        $where = $this->translateQueryToWhere($filter);
        $sql = "SELECT * FROM `{$this->tablePrefix}{$this->table}` " .
        ($where['sql'] ? "WHERE {$where['sql']}" : "") .
        " LIMIT " . ($options['limit'] ?? 100);

        $rs = $this->db->Execute($sql, $where['params']);
        return $rs ? $rs->GetRows() : [];
    }

    // Keep all your existing private methods: translateQueryToWhere, jsonField, buildUpdateSql, etc.
    // (they remain unchanged for SQL path)

    private function execute(string $sql, array $params = []): int
    {
        if ($this->isCouch()) {
            return 0; // not used for Couch
        }
        $rs = $this->db->Execute($sql, $params);
        return $rs ? $this->db->Affected_Rows() : 0;
    }

    // ... rest of your original class methods ...
}
