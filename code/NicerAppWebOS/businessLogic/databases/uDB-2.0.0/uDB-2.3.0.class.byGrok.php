<?php
declare(strict_types=1);

/**
 * uDB2 - Universal Database Layer 2.3
 * MongoDB-style API supporting CouchDB + SQL (mysqli)
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

        // SQL path
        $instance = new self(null, 'mysqli');
        $instance->config = $cRec;
        $instance->connectSQL();
        return $instance;
    }

    private function connectSQL(): void
    {
        $c = $this->config;
        $host = $c['host'] ?? 'localhost';
        $user = $c['user'] ?? $c['username'] ?? '';
        $pass = $c['pass'] ?? $c['password'] ?? '';
        $dbname = $c['database'] ?? $c['dbname'] ?? '';

        $this->db = new mysqli($host, $user, $pass, $dbname);

        if ($this->db->connect_error) {
            throw new RuntimeException("SQL Connection failed: " . $this->db->connect_error);
        }

        $this->db->set_charset('utf8mb4');
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
        return $this->config['database'] ?? 'default';
    }

    private function ensureCouchConnector(): void
    {
        if (!$this->isCouch()) {
            throw new RuntimeException("CouchDB connector not initialized");
        }
    }

    private function ensureTable(): void
    {
        if (empty($this->table)) {
            throw new RuntimeException("No table selected. Call setTable() first.");
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

    // ====================== CRUD - SQL ======================

    private function buildWhere(array $filter): array
    {
        if (empty($filter)) {
            return ['sql' => '1=1', 'params' => []];
        }

        $conditions = [];
        $params = [];
        $types = '';

        foreach ($filter as $field => $value) {
            if (is_array($value) && isset($value['$eq'])) {
                $conditions[] = "`$field` = ?";
                $params[] = $value['$eq'];
                $types .= $this->getParamType($value['$eq']);
            } elseif (is_array($value)) {
                // Support basic operators later ($gt, $lt, $in, etc.)
                $conditions[] = "`$field` = ?";
                $params[] = $value;
                $types .= $this->getParamType($value);
            } else {
                $conditions[] = "`$field` = ?";
                $params[] = $value;
                $types .= $this->getParamType($value);
            }
        }

        return [
            'sql' => implode(' AND ', $conditions),
            'params' => $params,
            'types' => $types
        ];
    }

    private function getParamType($value): string
    {
        if (is_int($value)) return 'i';
        if (is_float($value)) return 'd';
        return 's';
    }

    public function insertOne(array $document, array $options = []): array
    {
        if ($this->isCouch()) {
            return $this->couchInsertOne($document, $options);
        }

        $this->ensureTable();

        $columns = array_keys($document);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = "INSERT INTO `{$this->table}` (" .
        implode(', ', array_map(fn($c) => "`$c`", $columns)) .
        ") VALUES (" . implode(', ', $placeholders) . ")";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException("Prepare failed: " . $this->db->error);
        }

        $types = '';
        $values = [];
        foreach ($document as $value) {
            $types .= $this->getParamType($value);
            $values[] = $value;
        }

        $stmt->bind_param($types, ...$values);
        $success = $stmt->execute();

        $id = $this->db->insert_id;

        $stmt->close();

        return [
            'ok' => $success,
            '_id' => $id,
            'insertedId' => $id
        ];
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

        $results = [];
        foreach ($documents as $doc) {
            $results[] = $this->insertOne($doc, $options);
        }
        return $results;
    }

    public function find(array $filter = [], array $options = []): array
    {
        if ($this->isCouch()) {
            return $this->couchFind($filter, $options);
        }

        $this->ensureTable();

        $where = $this->buildWhere($filter);
        $limit = isset($options['limit']) ? " LIMIT " . (int)$options['limit'] : '';
        $skip = isset($options['skip']) ? " OFFSET " . (int)$options['skip'] : '';

        $sql = "SELECT * FROM `{$this->table}` WHERE {$where['sql']}{$limit}{$skip}";

        $stmt = $this->db->prepare($sql);
        if (!empty($where['params'])) {
            $stmt->bind_param($where['types'], ...$where['params']);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $rows;
    }

    public function findOne(array $filter = [], array $options = []): ?array
    {
        $options['limit'] = 1;
        $result = $this->find($filter, $options);
        return $result[0] ?? null;
    }

    public function updateMany(array $filter, array $update, array $options = []): int
    {
        if ($this->isCouch()) {
            return $this->couchUpdateMany($filter, $update, $options);
        }

        $this->ensureTable();

        $setParts = [];
        $params = [];
        $types = '';

        $updateData = $update['$set'] ?? $update;

        foreach ($updateData as $field => $value) {
            $setParts[] = "`$field` = ?";
            $params[] = $value;
            $types .= $this->getParamType($value);
        }

        $where = $this->buildWhere($filter);

        $sql = "UPDATE `{$this->table}` SET " . implode(', ', $setParts) .
        " WHERE " . $where['sql'];

        $stmt = $this->db->prepare($sql);
        $allParams = array_merge($params, $where['params']);
        $allTypes = $types . $where['types'];

        if (!empty($allParams)) {
            $stmt->bind_param($allTypes, ...$allParams);
        }

        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected;
    }

    public function deleteOne(array $filter): bool
    {
        if ($this->isCouch()) {
            return $this->couchDeleteOne($filter);
        }

        $this->ensureTable();
        $where = $this->buildWhere($filter);

        $sql = "DELETE FROM `{$this->table}` WHERE {$where['sql']} LIMIT 1";

        $stmt = $this->db->prepare($sql);
        if (!empty($where['params'])) {
            $stmt->bind_param($where['types'], ...$where['params']);
        }

        $stmt->execute();
        $success = $stmt->affected_rows > 0;
        $stmt->close();

        return $success;
    }

    public function deleteMany(array $filter): int
    {
        if ($this->isCouch()) {
            return $this->couchDeleteMany($filter);
        }

        $this->ensureTable();
        $where = $this->buildWhere($filter);

        $sql = "DELETE FROM `{$this->table}` WHERE {$where['sql']}";

        $stmt = $this->db->prepare($sql);
        if (!empty($where['params'])) {
            $stmt->bind_param($where['types'], ...$where['params']);
        }

        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected;
    }

    // ====================== CouchDB methods (unchanged) ======================

    private function couchInsertOne(array $document, array $options = []): array { /* ... existing code ... */
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
            trigger_error("uDB2 : CouchDB insertOne error: " . $e->getMessage(), E_USER_WARNING);
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    private function couchFind(array $filter = [], array $options = []): array { /* ... existing ... */
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

    private function couchUpdateMany(array $filter, array $update, array $options = []): int { /* existing */
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

    private function couchDeleteOne(array $filter): bool { /* existing */
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

    private function couchDeleteMany(array $filter): int { /* existing */
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
