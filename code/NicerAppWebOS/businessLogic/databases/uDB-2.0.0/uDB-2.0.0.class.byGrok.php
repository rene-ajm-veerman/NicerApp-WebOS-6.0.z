<?php
declare(strict_types=1);

/**
 * uDB2 - Universal Database Layer 2.0 (by Grok)
 * MongoDB-style query interface over SQL databases using ADOdb
 * Designed for NicerApp WebOS
 */
class uDB2
{
    private \ADOConnection $db;
    private string $driver;
    private bool $useJsonColumns = true;
    private string $tablePrefix = '';
    private string $table = '';
    private array $config = [];

    public string $cn = 'uDB2';

    // ====================== CONNECTION ======================
    // ... (keep all previous connection methods unchanged: createFromConfig, connectToDatabase, __construct)

    public static function createFromConfig(array $cRec, string $username = 'Guest'): self { /* ... existing ... */ }
    public static function connectToDatabase(string $username = 'Guest', string $connectionType = 'adodb', ?array $cRec = null): self { /* ... existing ... */ }
    public function __construct(\ADOConnection $adodbConnection, string $driver = 'mysqli') { /* ... existing ... */ }

    // ====================== MAGIC & TABLE ======================
    public function __get(string $name)
    {
        trigger_error("uDB2: Undefined property '\$this->{$name}' accessed on table '{$this->table}'", E_USER_NOTICE);
        return null;
    }

    public function setTable(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    // ====================== FIND() ======================

    /**
     * MongoDB-style find()
     */
    public function find(string $table = '', array $query = [], array $projection = [], ?int $limit = null, ?int $skip = null, array $sort = []): array
    {
        if (!empty($table)) {
            $this->setTable($table);
        }

        if (empty($this->table)) {
            trigger_error("uDB2: No table specified in find()", E_USER_WARNING);
            return [];
        }

        $where = $this->translateQueryToWhere($query);
        $fields = $this->translateProjection($projection);

        $sql = "SELECT {$fields} FROM `{$this->tablePrefix}{$this->table}`";

        if ($where['sql']) {
            $sql .= " WHERE {$where['sql']}";
        }

        // ORDER BY
        if (!empty($sort)) {
            $orderParts = [];
            foreach ($sort as $field => $direction) {
                $dir = strtoupper($direction) === -1 || strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
                $orderParts[] = $this->jsonField($field) . " $dir";
            }
            $sql .= " ORDER BY " . implode(', ', $orderParts);
        }

        // LIMIT & OFFSET
        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $where['params'][] = (int)$limit;

            if ($skip !== null) {
                $sql .= " OFFSET ?";
                $where['params'][] = (int)$skip;
            }
        }

        try {
            $rs = $this->db->Execute($sql, $where['params']);
            return $rs ? $rs->GetRows() : [];
        } catch (Throwable $e) {
            trigger_error("uDB2: find() failed on table '{$this->table}' - " . $e->getMessage(), E_USER_WARNING);
            return [];
        }
    }

    private function translateProjection(array $projection): string
    {
        if (empty($projection)) {
            return '*';
        }

        $fields = [];
        foreach ($projection as $field => $include) {
            if ($include === 1 || $include === true) {
                $fields[] = $this->jsonField($field);
            }
        }

        return empty($fields) ? '*' : implode(', ', $fields);
    }

    // ====================== INSERTMANY() ======================

    /**
     * MongoDB-style insertMany()
     */
    public function insertMany(string $table = '', array $documents = []): array
    {
        if (!empty($table)) {
            $this->setTable($table);
        }

        if (empty($this->table)) {
            trigger_error("uDB2: No table specified in insertMany()", E_USER_WARNING);
            return ['inserted' => 0, 'ids' => []];
        }

        if (empty($documents)) {
            return ['inserted' => 0, 'ids' => []];
        }

        $insertedIds = [];
        $count = 0;

        // Convert single document to array if needed
        if (!isset($documents[0])) {
            $documents = [$documents];
        }

        foreach ($documents as $doc) {
            $result = $this->insertOneInternal($doc);
            if ($result['success']) {
                $count++;
                $insertedIds[] = $result['id'];
            }
        }

        return [
            'inserted' => $count,
            'ids' => $insertedIds
        ];
    }

    private function insertOneInternal(array $document): array
    {
        // Auto-generate _id if not present
        if (empty($document['_id'])) {
            $document['_id'] = $this->generateId();
        }

        $fields = [];
        $placeholders = [];
        $params = [];

        foreach ($document as $key => $value) {
            $fields[] = "`$key`";
            $placeholders[] = '?';
            $params[] = $this->prepareInsertValue($value);
        }

        $sql = "INSERT INTO `{$this->tablePrefix}{$this->table}`
        (" . implode(', ', $fields) . ")
        VALUES (" . implode(', ', $placeholders) . ")";

        try {
            $this->db->Execute($sql, $params);
            return [
                'success' => true,
                'id' => $document['_id']
            ];
        } catch (Throwable $e) {
            trigger_error("uDB2: insert failed - " . $e->getMessage(), E_USER_WARNING);
            return ['success' => false, 'id' => null];
        }
    }

    private function generateId(): string
    {
        return bin2hex(random_bytes(12)); // 24 char hex ID (MongoDB-like)
    }

    private function prepareInsertValue(mixed $value): mixed
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }
        return $value;
    }

    // ====================== QUERY TRANSLATION ======================

    private function translateQueryToWhere(array $query): array
    {
        if (empty($query)) {
            return ['sql' => '', 'params' => []];
        }

        $conditions = [];
        $params = [];

        foreach ($query as $field => $value) {
            if (str_starts_with($field, '$')) {
                $result = $this->translateLogicalOperator($field, $value);
                if ($result['sql']) {
                    $conditions[] = $result['sql'];
                    $params = array_merge($params, $result['params']);
                }
            } else {
                $result = $this->translateFieldCondition($field, $value);
                if ($result['sql']) {
                    $conditions[] = $result['sql'];
                    $params = array_merge($params, $result['params']);
                }
            }
        }

        $sql = !empty($conditions) ? '(' . implode(' AND ', $conditions) . ')' : '';
        return ['sql' => $sql, 'params' => $params];
    }

    private function translateFieldCondition(string $field, mixed $value): array
    {
        if (!is_array($value)) {
            return [
                'sql' => $this->jsonField($field) . ' = ?',
                'params' => [$this->prepareValue($value)]
            ];
        }

        $conditions = [];
        $params = [];

        foreach ($value as $op => $opValue) {
            $result = $this->translateOperator($field, $op, $opValue);
            if ($result['sql']) {
                $conditions[] = $result['sql'];
                $params = array_merge($params, $result['params']);
            }
        }

        return [
            'sql' => implode(' AND ', $conditions),
            'params' => $params
        ];
    }

    private function translateOperator(string $field, string $operator, mixed $value): array
    {
        $col = $this->jsonField($field);

        switch ($operator) {
            case '$eq':   return ['sql' => "$col = ?", 'params' => [$this->prepareValue($value)]];
            case '$ne':   return ['sql' => "$col != ?", 'params' => [$this->prepareValue($value)]];
            case '$gt':   return ['sql' => "$col > ?", 'params' => [$this->prepareValue($value)]];
            case '$gte':  return ['sql' => "$col >= ?", 'params' => [$this->prepareValue($value)]];
            case '$lt':   return ['sql' => "$col < ?", 'params' => [$this->prepareValue($value)]];
            case '$lte':  return ['sql' => "$col <= ?", 'params' => [$this->prepareValue($value)]];

            case '$in':
                $ph = implode(',', array_fill(0, count($value), '?'));
                return ['sql' => "$col IN ($ph)", 'params' => array_map([$this, 'prepareValue'], $value)];

            case '$nin':
                $ph = implode(',', array_fill(0, count($value), '?'));
                return ['sql' => "$col NOT IN ($ph)", 'params' => array_map([$this, 'prepareValue'], $value)];

            case '$regex':
                return $this->translateRegex($col, $value['$regex'] ?? $value, $value['$options'] ?? '');

            case '$exists':
                return $this->translateExists($field, (bool)$value);

            default:
                return ['sql' => "$col = ?", 'params' => [$this->prepareValue($value)]];
        }
    }

    private function translateLogicalOperator(string $operator, array $conditions): array
    {
        $parts = [];
        $params = [];

        foreach ($conditions as $cond) {
            $result = $this->translateQueryToWhere($cond);
            if ($result['sql']) {
                $parts[] = $result['sql'];
                $params = array_merge($params, $result['params']);
            }
        }

        if (empty($parts)) return ['sql' => '', 'params' => []];

        return match($operator) {
            '$and' => ['sql' => '(' . implode(' AND ', $parts) . ')', 'params' => $params],
            '$or'  => ['sql' => '(' . implode(' OR ', $parts) . ')', 'params' => $params],
            '$nor' => ['sql' => 'NOT (' . implode(' OR ', $parts) . ')', 'params' => $params],
            default => ['sql' => '', 'params' => []]
        };
    }

    private function translateRegex(string $column, string $pattern, string $flags = ''): array
    {
        if (in_array($this->driver, ['mysqli', 'mysql'])) {
            return ['sql' => "$column REGEXP ?", 'params' => [$pattern]];
        }
        return ['sql' => "$column LIKE ?", 'params' => ["%$pattern%"]];
    }

    private function translateExists(string $field, bool $exists): array
    {
        $col = $this->jsonField($field);
        if ($exists) {
            return ['sql' => "$col IS NOT NULL", 'params' => []];
        }
        return ['sql' => "$col IS NULL", 'params' => []];
    }

    private function jsonField(string $field): string
    {
        if (!$this->useJsonColumns || strpos($field, '.') === false) {
            return "`$field`";
        }

        $parts = explode('.', $field);
        $root = array_shift($parts);

        return match($this->driver) {
            'mysqli', 'mysql' => "`$root`->>'$.'" . implode('.', $parts) . "'",
            'postgres'        => "`$root`->>'" . implode('.', $parts) . "'",
            'sqlite3'         => "json_extract(`$root`, '$.'" . implode('.', $parts) . "')",
            default           => "`$root`"
        };
    }

    private function prepareValue(mixed $value): mixed
    {
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        if (is_bool($value)) {
            return $value ? 1 : 0;
        }
        return $value;
    }

    // ====================== UPDATE BUILDER ======================

    private function buildUpdateSql(array $update): array
    {
        if (empty($update)) {
            throw new InvalidArgumentException('Update document cannot be empty');
        }

        $setParts = [];
        $params = [];

        foreach ($update as $op => $fields) {
            if (!str_starts_with($op, '$')) {
                $fields = [$op => $fields];
                $op = '$set';
            }

            match($op) {
                '$set' => $this->processSet($fields, $setParts, $params),
                '$inc' => $this->processInc($fields, $setParts, $params),
                '$unset' => $this->processUnset($fields, $setParts),
                '$push' => $this->processPush($fields, $setParts, $params),
                default => $setParts[] = $this->jsonField($op) . " = ?",
            };
        }

        return [
            'sql' => !empty($setParts) ? 'SET ' . implode(', ', $setParts) : '',
            'params' => $params
        ];
    }

    private function processSet(array $fields, array &$setParts, array &$params): void
    {
        foreach ($fields as $field => $value) {
            $setParts[] = $this->jsonField($field) . ' = ?';
            $params[] = $this->prepareValue($value);
        }
    }

    private function processInc(array $fields, array &$setParts, array &$params): void
    {
        foreach ($fields as $field => $value) {
            $col = $this->jsonField($field);
            $setParts[] = "$col = $col + ?";
            $params[] = (float)$value;
        }
    }

    private function processUnset(mixed $fields, array &$setParts): void
    {
        foreach ((array)$fields as $field) {
            $setParts[] = $this->jsonField($field) . ' = NULL';
        }
    }

    private function processPush(array $fields, array &$setParts, array &$params): void
    {
        foreach ($fields as $field => $value) {
            if (in_array($this->driver, ['mysqli', 'mysql'])) {
                $setParts[] = $this->jsonField($field) . " = JSON_ARRAY_APPEND(" . $this->jsonField($field) . ", '$', ?)";
                $params[] = json_encode($value);
            } else {
                $setParts[] = $this->jsonField($field) . ' = ?';
                $params[] = json_encode($value);
            }
        }
    }

    // ====================== PUBLIC CRUD METHODS ======================

    public function updateMany(array $filter, array $update): int
    {
        $where = $this->translateQueryToWhere($filter);
        $upd = $this->buildUpdateSql($update);

        $sql = "UPDATE `{$this->tablePrefix}{$this->table}` {$upd['sql']} " .
               ($where['sql'] ? "WHERE {$where['sql']}" : "");

        return $this->execute($sql, array_merge($upd['params'], $where['params']));
    }

    public function updateOne(array $filter, array $update): int
    {
        $where = $this->translateQueryToWhere($filter);
        $upd = $this->buildUpdateSql($update);

        $sql = "UPDATE `{$this->tablePrefix}{$this->table}` {$upd['sql']} " .
               ($where['sql'] ? "WHERE {$where['sql']}" : "") . " LIMIT 1";

        return $this->execute($sql, array_merge($upd['params'], $where['params']));
    }

    private function execute(string $sql, array $params = []): int
    {
        $rs = $this->db->Execute($sql, $params);
        return $rs ? $this->db->Affected_Rows() : 0;
    }

    public function getConfig(): array { return $this->config; }
    public function getDriver(): string { return $this->driver; }
}
