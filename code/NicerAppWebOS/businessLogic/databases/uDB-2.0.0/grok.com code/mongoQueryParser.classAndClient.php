<?php

declare(strict_types=1);

class MongoQueryParser
{
    /**
     * Parse MongoDB query JSON string into PHP array (ready for MongoDB driver)
     */
    public static function parse(string $queryJson): array
    {
        $decoded = json_decode($queryJson, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($decoded)) {
            throw new InvalidArgumentException('MongoDB query must be a JSON object');
        }

        return self::normalizeQuery($decoded);
    }

    /**
     * Normalize and clean the query (recursively)
     */
    private static function normalizeQuery(array $query): array
    {
        $normalized = [];

        foreach ($query as $key => $value) {
            if (is_array($value)) {
                // Handle operators like $and, $or, $nor
                if (str_starts_with($key, '$')) {
                    $normalized[$key] = array_map(
                        fn($item) => is_array($item) ? self::normalizeQuery($item) : $item,
                                                  $value
                    );
                } else {
                    // Nested document / field
                    $normalized[$key] = self::normalizeQuery($value);
                }
            } else {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }

    /**
     * Parse and extract information about the query (for analysis/debugging)
     */
    public static function analyze(array $query): array
    {
        $analysis = [
            'fields' => [],
            'operators' => [],
            'hasLogical' => false,
            'complexity' => 'simple'
        ];

        self::traverseQuery($query, $analysis);

        $analysis['complexity'] = count($analysis['operators']) > 3 ||
        $analysis['hasLogical'] ? 'complex' : 'simple';

        return $analysis;
    }

    private static function traverseQuery(array $query, array &$analysis, string $parent = ''): void
    {
        foreach ($query as $key => $value) {
            if (str_starts_with($key, '$')) {
                $analysis['operators'][] = $key;
                if (in_array($key, ['$and', '$or', '$nor', '$nor'])) {
                    $analysis['hasLogical'] = true;
                }
            } elseif (is_string($key)) {
                $analysis['fields'][] = $parent ? "$parent.$key" : $key;
            }

            if (is_array($value) && !empty($value)) {
                self::traverseQuery($value, $analysis, $key);
            }
        }
    }

    /**
     * Validate common MongoDB operators
     */
    public static function validate(array $query): bool
    {
        $validOperators = [
            '$eq', '$ne', '$gt', '$gte', '$lt', '$lte', '$in', '$nin',
            '$and', '$or', '$nor', '$not', '$exists', '$type',
            '$regex', '$text', '$elemMatch', '$size', '$all'
        ];

        $iterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($query));

        foreach ($iterator as $key => $value) {
            if (is_string($key) && str_starts_with($key, '$')) {
                if (!in_array($key, $validOperators, true)) {
                    throw new InvalidArgumentException("Invalid MongoDB operator: $key");
                }
            }
        }

        return true;
    }

    /**
     * Convert query to pretty string (for logging/debug)
     */
    public static function toString(array $query, int $indent = 0): string
    {
        $output = [];
        $prefix = str_repeat('  ', $indent);

        foreach ($query as $key => $value) {
            if (is_array($value)) {
                $output[] = "$prefix$key: " . self::toString($value, $indent + 1);
            } else {
                $val = is_bool($value) ? ($value ? 'true' : 'false') : json_encode($value);
                $output[] = "$prefix$key: $val";
            }
        }

        return "{\n" . implode(",\n", $output) . "\n" . str_repeat('  ', $indent) . "}";
    }

    /**
     * Simple query builder helper
     */
    public static function build(array $conditions): array
    {
        return self::normalizeQuery($conditions);
    }
}

// ==================== USAGE EXAMPLES ====================

// Example 1: Basic parsing
$queryJson = '{
"status": "active",
"age": { "$gte": 18, "$lte": 65 },
"$or": [
{ "role": "admin" },
{ "permissions": { "$in": ["write", "delete"] } }
]
}';

try {
    $parsed = MongoQueryParser::parse($queryJson);
    MongoQueryParser::validate($parsed);

    echo "Parsed Query:\n";
    print_r($parsed);

    echo "\nAnalysis:\n";
    print_r(MongoQueryParser::analyze($parsed));

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Example 2: Build query programmatically
$customQuery = MongoQueryParser::build([
    'userId' => ['$in' => [123, 456]],
    'createdAt' => ['$gte' => new MongoDB\BSON\UTCDateTime(strtotime('-30 days') * 1000)],
                                       '$and' => [
                                           ['status' => 'published'],
                                       ['$or' => [['views' => ['$gt' => 1000]], ['featured' => true]]]
                                       ]
]);
