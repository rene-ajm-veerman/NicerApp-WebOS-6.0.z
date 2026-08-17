<?php

/**
 * Flattens ANY JSON record (object or array, with full nested hierarchies)
 * into a flat associative array of SQL-friendly fields.
 *
 * - Objects → "parent_child" keys
 * - Arrays  → indexed keys like "tags_0", "items_0_name", "items_1_price", etc.
 * - Keys are automatically sanitized to valid SQL column names (only a-zA-Z0-9_)
 * - Values stay in their original PHP type (string, int, float, bool, null) → perfect for PDO/MySQLi binding
 * - Top-level arrays are safely prefixed so column names never start with a digit
 *
 * @param string|array $input   JSON string OR already-decoded array
 * @param string $separator     Default "_" (you can change to "__" or "." if you prefer, but "_" is safest for SQL)
 * @return array                Flat [ 'column_name' => value, ... ]
 */
function jsonToSqlFields($input, string $separator = '_'): array
{
    if (is_string($input)) {
        $data = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
    } else {
        $data = $input;
    }

    if (!is_array($data)) {
        // scalar top-level value (very rare, but handled)
        return ['value' => $data];
    }

    return flattenRecursive($data, '', $separator);
}

/**
 * Internal recursive flattener (you don't need to call this directly)
 */
function flattenRecursive(array $data, string $prefix, string $separator): array
{
    $flat = [];

    foreach ($data as $key => $value) {
        // Sanitize key for SQL column name
        $safeKey = preg_replace('/[^a-zA-Z0-9_]/', '_', (string)$key);

        // If this is a top-level numeric key (root is an array), prefix it so column never starts with digit
        if ($prefix === '' && (ctype_digit($safeKey) || $safeKey === '')) {
            $safeKey = 'data' . ($safeKey === '' ? '' : '_' . $safeKey);
        }

        $currentKey = $prefix ? $prefix . $separator . $safeKey : $safeKey;

        if (is_array($value)) {
            // Recurse deeper (objects or arrays)
            $sub = flattenRecursive($value, $currentKey, $separator);
            $flat = array_merge($flat, $sub);
        } else {
            // Leaf value (string, number, bool, null)
            $flat[$currentKey] = $value;
        }
    }

    return $flat;
}

/**
 * Helper: turns the flattened fields into ready-to-use INSERT parts
 * (columns, placeholders, values array) – perfect for prepared statements.
 *
 * Example output:
 * [
 *     'columns'      => '`name`, `address_city`, `tags_0`, `items_0_id`',
 *     'placeholders' => '?, ?, ?, ?',
 *     'values'       => ['John', 'New York', 'php', 42]
 * ]
 */
function sqlFieldsToInsertParts(array $fields): array
{
    $quotedColumns = array_map(
        fn($col) => "`$col`",
                               array_keys($fields)
    );

    return [
        'columns'      => implode(', ', $quotedColumns),
        'placeholders' => implode(', ', array_fill(0, count($fields), '?')),
        'values'       => array_values($fields)
    ];
}

/**
 * Bonus helper: quick one-liner for UPDATE SET clause (useful too)
 */
function sqlFieldsToUpdateParts(array $fields): array
{
    $set = [];
    $values = [];
    foreach ($fields as $col => $val) {
        $set[] = "`$col` = ?";
        $values[] = $val;
    }
    return [
        'set'    => implode(', ', $set),
        'values' => $values
    ];
}

// =============================================================================
// EXAMPLE USAGE (copy-paste and try it)
// =============================================================================

/*
 * $ json = '{                                                              *
 * "name": "Alice",
 * "age": 30,
 * "active": true,
 * "address": {
 * "street": "123 Main St",
 * "city": "Wonderland",
 * "geo": {
 * "lat": 51.5074,
 * "lng": -0.1278
 * }
 * },
 * "tags": ["php", "json", "sql"],
 * "orders": [
 * {"id": 101, "amount": 59.99},
 * {"id": 102, "amount": 19.50}
 * ],
 * "nullField": null
 * }';
 *
 * $fields = jsonToSqlFields($json);
 *
 * print_r($fields);
 *
 * /*
 * O utput:                                                                 *
 * Array
 * (
 *     [name] => Alice
 *     [age] => 30
 *     [active] => 1
 *     [address_street] => 123 Main St
 *     [address_city] => Wonderland
 *     [address_geo_lat] => 51.5074
 *     [address_geo_lng] => -0.1278
 *     [tags_0] => php
 *     [tags_1] => json
 *     [tags_2] => sql
 *     [orders_0_id] => 101
 *     [orders_0_amount] => 59.99
 *     [orders_1_id] => 102
 *     [orders_1_amount] => 19.5
 *     [nullField] =>
 *     )
 */

 // Ready for PDO insert:
 //$insert = sqlFieldsToInsertParts($fields);
 //$sql = "INSERT INTO users ({$insert['columns']}) VALUES ({$insert['placeholders']})";
 // Then: $stmt = $pdo->prepare($sql); $stmt->execute($insert['values']);

 /**
  * Rebuilds nested structure (arrays + objects) from flat SQL-style array.
  * Inverse of jsonToSqlFields().
  *
  * @param array  $flat      Flat array: ['user_name' => 'Alice', 'tags_0' => 'php', 'orders_0_id' => 101, ...]
  * @param string $separator Default '_'
  * @return array            Nested structure (can be json_encoded again)
  */
 function sqlFieldsToJson(array $flat, string $separator = '_'): array
 {
     $result = [];

     foreach ($flat as $key => $value) {
         $parts = explode($separator, $key);
         $current = &$result;

         // Process all parts except the last one
         foreach ($parts as $i => $part) {
             // Last segment → assign the value
             if ($i === count($parts) - 1) {
                 // Try to detect if this should be boolean/null
                 if ($value === 'true' || $value === true || $value === 1 || $value === '1') {
                     $current[$part] = true;
                 } elseif ($value === 'false' || $value === false || $value === 0 || $value === '0') {
                     $current[$part] = false;
                 } elseif ($value === null || $value === 'null' || $value === '') {
                     $current[$part] = null;
                 } else {
                     $current[$part] = $value;
                 }
                 break;
             }

             // Handle numeric parts → treat as array index
             if (is_numeric($part)) {
                 $index = (int)$part;

                 // Initialize array if not exists
                 if (!isset($current[$index]) || !is_array($current[$index])) {
                     $current[$index] = [];
                 }
                 $current = &$current[$index];
             }
             // Non-numeric → treat as object key
             else {
                 // Clean key (in case it was sanitized)
                 $cleanKey = preg_replace('/[^a-zA-Z0-9_]/', '', $part);

                 if (!isset($current[$cleanKey]) || !is_array($current[$cleanKey])) {
                     // Decide if next part is numeric → array or object
                     $nextPart = $parts[$i + 1] ?? null;
                     if ($nextPart !== null && is_numeric($nextPart)) {
                         $current[$cleanKey] = [];
                     } else {
                         $current[$cleanKey] = [];
                     }
                 }
                 $current = &$current[$cleanKey];
             }
         }
     }

     // Final step: convert numeric-only keys arrays into proper indexed arrays
     $result = normalizeNumericArrays($result);

     return $result;
 }

 /**
  * Recursively converts arrays with only integer keys into indexed arrays (0,1,2...)
  * instead of associative with string keys.
  */
 function normalizeNumericArrays($data)
 {
     if (!is_array($data)) {
         return $data;
     }

     // Check if all keys are consecutive integers starting from 0
     $keys = array_keys($data);
     $isIndexed = count($keys) > 0
     && array_keys($keys) === range(0, count($keys) - 1)
     && array_reduce($keys, fn($carry, $k) => $carry && is_int($k) && $k >= 0, true)
     && min($keys) === 0
     && max($keys) === count($keys) - 1;

     if ($isIndexed) {
         $normalized = [];
         foreach ($data as $v) {
             $normalized[] = normalizeNumericArrays($v);
         }
         return $normalized;
     }

     // Otherwise recurse into children
     $result = [];
     foreach ($data as $k => $v) {
         $result[$k] = normalizeNumericArrays($v);
     }
     return $result;
 }

 /**
  * Convenience wrapper: flat array → JSON string
  */
 function sqlFieldsToJsonString(array $flat, string $separator = '_', int $jsonOptions = JSON_PRETTY_PRINT): string
 {
     $nested = sqlFieldsToJson($flat, $separator);
     return json_encode($nested, $jsonOptions | JSON_THROW_ON_ERROR);
 }

 // ────────────────────────────────────────────────
 //                  EXAMPLE ROUND-TRIP
 // ────────────────────────────────────────────────

 /*
  * $ *flatExample = [
  * 'name'                => 'Alice',
  * 'age'                 => 30,
  * 'active'              => true,
  * 'address_street'      => '123 Main',
  * 'address_city'        => 'Wonderland',
  * 'address_geo_lat'     => 51.5074,
  * 'address_geo_lng'     => -0.1278,
  * 'tags_0'              => 'php',
  * 'tags_1'              => 'json',
  * 'tags_2'              => 'sql',
  * 'orders_0_id'         => 101,
  * 'orders_0_amount'     => 59.99,
  * 'orders_1_id'         => 102,
  * 'orders_1_amount'     => 19.50,
  * 'nullField'           => null,
  * ];
  *
  * // Reverse
  * $reconstructed = sqlFieldsToJson($flatExample);
  * echo json_encode($reconstructed, JSON_PRETTY_PRINT);
  *
  * // Should give you back something very close to the original JSON structure
  */

 // Bonus: quick test helper
 function roundTripTest(array $flat, string $sep = '_'): void
 {
     $nested = sqlFieldsToJson($flat, $sep);
     echo "Reconstructed:\n" . json_encode($nested, JSON_PRETTY_PRINT) . "\n\n";
 }

 /**
  * Inverse: flat ['user_name' => 'Alice', 'tags_0' => 'php', ...] → nested structure
  * function sqlFieldsToJson(array $flat, string $separator = '_'): array
  * {
  *    $root = [];
  *
  *    foreach ($flat as $flatKey => $value) {
  *        $parts = explode($separator, $flatKey);
  *        $current = &$root;
  *
  *        foreach ($parts as $idx => $segment) {
  *            $isLast = $idx === count($parts) - 1;
  *
  *            // Determine container type for this level
  *            $nextSegment = $parts[$idx + 1] ?? null;
  *            $shouldBeArray = $nextSegment !== null && is_numeric($nextSegment);
  *
  *            // Use original segment as key (minimal sanitization only if needed)
  *            $key = $segment;
  *
  *            if ($isLast) {
  *                // Final assignment — try smart type restoration
  *                $current[$key] = restoreOriginalType($value);
  *                break;
  *            }
  *
  *            if (is_numeric($segment)) {
  *                $index = (int)$segment;
  *                if (!isset($current[$index]) || !is_array($current[$index])) {
  *                    $current[$index] = [];
  *                }
  *                $current = &$current[$index];
  *            } else {
  *                // Object key
  *                if (!isset($current[$key]) || !is_array($current[$key])) {
  *                    $current[$key] = $shouldBeArray ? [] : [];
  *                }
  *                $current = &$current[$key];
  *            }
  *        }
  *    }
  *
  *    // Normalize numeric-key-only arrays to indexed [] syntax
  *    return normalizeNumericKeys($root);
  * }
  *
  * / **
  * Minimal sanitization — keep original meaning when possible
  * /
  * function restoreOriginalType(mixed $value): mixed
  * {
  *    if ($value === null) {
  *        return null;
  *    }
  *
  *    if (is_string($value)) {
  *        $trimmed = trim($value);
  *        if ($trimmed === '') return null;
  *        if ($trimmed === 'true' || $trimmed === '1') return true;
  *        if ($trimmed === 'false' || $trimmed === '0') return false;
  *
  *        // numeric strings → int/float when possible
  *        if (is_numeric($trimmed)) {
  *            return strpos($trimmed, '.') !== false ? (float)$trimmed : (int)$trimmed;
  *        }
  *    }
  *
  *    return $value; // already int/float/bool/null → keep as-is
  * }
  */

 /**
  * Recursively turn purely sequential integer-key arrays into []
  */
 function normalizeNumericKeys(mixed $data): mixed
 {
     if (!is_array($data)) {
         return $data;
     }

     $keys = array_keys($data);

     // Is it a perfect 0-based consecutive numeric array?
     $isSequential = $keys !== []
     && array_is_list($data)
     && min($keys) === 0
     && max($keys) === count($keys) - 1;

     if ($isSequential) {
         return array_map('normalizeNumericKeys', array_values($data));
     }

     // Otherwise keep assoc & recurse
     $result = [];
     foreach ($data as $k => $v) {
         $result[$k] = normalizeNumericKeys($v);
     }
     return $result;
 }

 // ────────────────────────────────────────────────
 // Round-trip test helper
 // ────────────────────────────────────────────────
 function testRoundTrip(array $flat, string $sep = '_'): void
 {
     echo "Input flat:\n";
     print_r($flat);
     echo "\nReconstructed:\n";
     $nested = sqlFieldsToJson($flat, $sep);
     echo json_encode($nested, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
     echo "\n\n";
 }

 // ────────────────────────────────────────────────
 // Example
 // ────────────────────────────────────────────────
 $exampleFlat = [
     'name'              => 'Bob',
'age'               => '28',
'is_active'         => '1',
'score'             => '19.95',
'tags_0'            => 'php',
'tags_1'            => 'backend',
'address_street'    => '456 Elm St',
'address_city'      => 'Dreamville',
'address_geo_lat'   => '40.7128',
'address_geo_lng'   => '-74.0060',
'orders_0_id'       => '201',
'orders_0_amount'   => '89.50',
'orders_0_items_0'  => 'Laptop',
'orders_0_items_1'  => 'Mouse',
'orders_1_id'       => '202',
'orders_1_amount'   => '12.00',
'meta_data'         => null,
 ];
 /* expected output :
  * {
  *    "name": "Bob",
  *    "age": 28,
  *    "is_active": true,
  *    "score": 19.95,
  *    "tags": [
  *        "php",
  *        "backend"
  *    ],
  *    "address": {
  *        "street": "456 Elm St",
  *        "city": "Dreamville",
  *        "geo": {
  *            "lat": 40.7128,
  *            "lng": -74.006
  *        }
  *    },
  *    "orders": [
  *        {
  *            "id": 201,
  *            "amount": 89.5,
  *            "items": [
  *                "Laptop",
  *                "Mouse"
  *            ]
  *        },
  *        {
  *            "id": 202,
  *            "amount": 12
  *        }
  *    ],
  *    "meta_data": null
  * }
  */
 //testRoundTrip($exampleFlat);

 function atomicWrite(array $data): void
 {
     $json = json_encode($data, JSON_THROW_ON_ERROR);

     $tmp = $this->path . '.tmp.' . uniqid(more_entropy: true);

     if (file_put_contents($tmp, $json, LOCK_EX) === false) {
         throw new RuntimeException("atomicWrite(): Temp write failed");
     }

     // rename is atomic on POSIX filesystems
     if (!rename($tmp, $this->path)) {
         @unlink($tmp);
         throw new RuntimeException("atomicWrite(): Rename failed");
     }

     // Optional: chmod($this->path, 0664); or similar
 }

?>
