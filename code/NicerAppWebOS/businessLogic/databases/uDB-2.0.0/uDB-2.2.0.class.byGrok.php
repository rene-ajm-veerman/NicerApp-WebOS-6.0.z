<?php
declare(strict_types=1);

/**
 * uDB2 - Universal Database Layer 2.1
 * MongoDB-style API supporting CouchDB + future SQL
 */

class uDB2
{
    private $db;
    private string $driver;
    private bool $isCouchDB = false;
    private string $table = '';
    private array $config = [];
    private ?object $couchConnector = null;

    public string $cn = 'uDB2';

    public static function createFromConfig(array $cRec, string $username = 'Guest'): self
    {
        $driver = strtolower($cRec['driver'] ?? $cRec['dbConnectionType'] ?? 'mysqli');

        if (strpos($driver, 'couchdb') !== false) {
            global $naWebOS;
            $instance = new self(null, 'couchdb');
            $instance->config = $cRec;
            $instance->couchConnector = new class_NicerAppWebOS_database_API_couchdb_3_2__2_0_0(
                clone $naWebOS, $username, $cRec
            );
            $instance->isCouchDB = true;
            return $instance;
        }

        // SQL path (add your existing connection logic here)
        throw new RuntimeException("SQL backend not fully implemented yet");
    }

    public function __construct($connection = null, string $driver = 'mysqli'): void
    {
        $this->driver = strtolower($driver);
        $this->isCouchDB = (strpos($this->driver, 'couchdb') !== false);
        if (!$this->isCouchDB) {
            $this->db = $connection;
        }
    }

    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    private function isCouch(): bool
    {
        return $this->isCouchDB && $this->couchConnector !== null;
    }

    private function getCurrentDatabase(): string
    {
        return $this->config['database'] ?? $this->table ?? 'default';
    }

    private function ensureCouchConnector(): void
    {
        if (!$this->isCouch()) {
            throw new RuntimeException("CouchDB connector not initialized");
        }
    }

    private function translateToMango(array $query): array
    {
        if (empty($query)) return [];

        $mango = [];
        foreach ($query as $field => $value) {
            if (str_starts_with($field, '$')) {
                $op = substr($field, 1);
                $mango['$' . $op] = array_map([$this, 'translateToMango'], (array)$value);
            } elseif (is_array($value)) {
                $mango[$field] = $value;
            } else {
                $mango[$field] = ['$eq' => $value];
            }
        }
        return $mango;
    }

    // ====================== CRUD ======================

    public function insertOne(array $document, array $options = []): array
    {
        if ($this->isCouch()) return $this->couchInsertOne($document, $options);
        throw new RuntimeException("uDB2.byGrok.class.php : insertOne() not implemented for SQL yet");
    }

    private function couchInsertOne(array $document, array $options = []): array
    {
        $this->ensureCouchConnector();
        $dbName = $this->getCurrentDatabase();
        $this->couchConnector->cdb->setDatabase($dbName);

        if (empty($document['_id'])) {
            $document['_id'] = ($options['idPrefix'] ?? '') . bin2hex(random_bytes(12));
        }

        try {
            $response = $this->couchConnector->cdb->post($document);
            return (array)($response->body ?? ['ok' => true, '_id' => $document['_id']]);
        } catch (Exception $e) {
            trigger_error("uDB2.byGrok.class.php : CouchDB insertOne error: " . $e->getMessage(), E_USER_WARNING);
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function insertMany(array $documents, array $options = []): array
    {
        if ($this->isCouch()) {
            $results = [];
            foreach ($documents as $doc) {
                $results[] = $this->couchInsertOne($doc, $options);
            }
            return $results;
        }
        throw new RuntimeException("uDB2.byGrok.class.php : insertMany() not implemented for SQL yet");
    }

    public function find(array $filter = [], array $options = []): array
    {
        if ($this->isCouch()) return $this->couchFind($filter, $options);
        throw new RuntimeException("uDB2.byGrok.class.php : find() not implemented for SQL yet");
    }

    private function couchFind(array $filter = [], array $options = []): array
    {
        $this->ensureCouchConnector();
        $dbName = $this->getCurrentDatabase();
        $this->couchConnector->cdb->setDatabase($dbName);

        $mangoQuery = [
            'selector' => $this->translateToMango($filter),
            'limit'    => $options['limit'] ?? 100,
            'skip'     => $options['skip'] ?? 0,
        ];

        if (!empty($options['sort']))   $mangoQuery['sort'] = $options['sort'];
        if (!empty($options['fields'])) $mangoQuery['fields'] = $options['fields'];

        try {
            $result = $this->couchConnector->cdb->find($mangoQuery);
            return $result->body->docs ?? [];
        } catch (Exception $e) {
            trigger_error("CouchDB find error: " . $e->getMessage(), E_USER_WARNING);
            return [];
        }
    }

    public function findOne(array $filter = [], array $options = []): ?array
    {
        $options['limit'] = 1;
        $result = $this->find($filter, $options);
        return $result[0] ?? null;
    }

    public function updateMany(array $filter, array $update, array $options = []): int
    {
        if ($this->isCouch()) return $this->couchUpdateMany($filter, $update, $options);
        throw new RuntimeException("updateMany() not implemented for SQL yet");
    }

    private function couchUpdateMany(array $filter, array $update, array $options = []): int
    {
        $docs = $this->couchFind($filter, ['limit' => 9999]);
        $updated = 0;

        foreach ($docs as $doc) {
            $doc = (array)$doc;
            if (isset($update['$set'])) {
                foreach ($update['$set'] as $k => $v) $doc[$k] = $v;
            } else {
                foreach ($update as $k => $v) $doc[$k] = $v;
            }

            try {
                $this->couchConnector->cdb->put($doc);
                $updated++;
            } catch (Exception $e) {}
        }
        return $updated;
    }

    public function deleteOne(array $filter): bool
    {
        if ($this->isCouch()) return $this->couchDeleteOne($filter);
        throw new RuntimeException("deleteOne() not implemented for SQL yet");
    }

    private function couchDeleteOne(array $filter): bool
    {
        $docs = $this->couchFind($filter, ['limit' => 1]);
        if (empty($docs)) return false;

        $doc = (array)$docs[0];
        try {
            $this->couchConnector->cdb->delete($doc['_id'], $doc['_rev'] ?? null);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function deleteMany(array $filter): int
    {
        if ($this->isCouch()) return $this->couchDeleteMany($filter);
        throw new RuntimeException("deleteMany() not implemented for SQL yet");
    }

    private function couchDeleteMany(array $filter): int
    {
        $docs = $this->couchFind($filter, ['limit' => 9999]);
        $count = 0;
        foreach ($docs as $doc) {
            $doc = (array)$doc;
            try {
                $this->couchConnector->cdb->delete($doc['_id'], $doc['_rev'] ?? null);
                $count++;
            } catch (Exception $e) {}
        }
        return $count;
    }

    // ====================== ADMIN ======================

    public function createDatabase(string $dbName): bool
    {
        if (!$this->isCouch()) return false;
        $this->ensureCouchConnector();
        try {
            $this->couchConnector->cdb->createDatabase($dbName);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function setSecurity(string $dbName, array $admins = [], array $members = []): bool
    {
        if (!$this->isCouch()) return false;
        $this->ensureCouchConnector();
        $this->couchConnector->cdb->setDatabase($dbName);

        $security = [
            'admins'  => ['names' => $admins['names'] ?? [], 'roles' => $admins['roles'] ?? []],
            'members' => ['names' => $members['names'] ?? [], 'roles' => $members['roles'] ?? []]
        ];

        try {
            $this->couchConnector->cdb->putSecurity($security);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createIndex(array $fields, ?string $name = null): bool
    {
        if (!$this->isCouch()) return false;
        $this->ensureCouchConnector();

        $index = ['index' => ['fields' => $fields]];
        if ($name) $index['name'] = $name;

        try {
            $this->couchConnector->cdb->createIndex($index);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
