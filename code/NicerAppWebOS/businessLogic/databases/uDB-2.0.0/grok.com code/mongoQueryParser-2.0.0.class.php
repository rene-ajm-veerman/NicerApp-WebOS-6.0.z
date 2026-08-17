<?php

declare(strict_types=1);

/**
 * Full-featured MongoDB Query Matcher + Updater + Utilities
 */
class MongoDBTools
{
    // ====================== QUERY MATCHING ======================

    public static function matches(array $document, array $query): bool
    {
        if (empty($query)) return true;
        return self::evaluateQuery($document, $query);
    }

    private static function evaluateQuery(array $doc, array $query, string $path = ''): bool
    {
        foreach ($query as $key => $value) {
            if (str_starts_with($key, '$')) {
                return self::evaluateLogical($key, $value, $doc);
            }

            $fieldValue = self::getFieldValue($doc, $key);

            if (is_array($value) && !empty($value)) {
                $first = array_key_first($value);
                if (is_string($first) && str_starts_with($first, '$')) {
                    if (!self::evaluateFieldOperators($fieldValue, $value, $key)) {
                        return false;
                    }
                } else {
                    if (!self::equals($fieldValue, $value)) return false;
                }
            } else {
                if (!self::equals($fieldValue, $value)) return false;
            }
        }
        return true;
    }

    private static function evaluateLogical(string $op, mixed $value, array $doc): bool
    {
        if (!is_array($value)) return false;

        return match ($op) {
            '$and' => self::evaluateAnd($value, $doc),
            '$or'  => self::evaluateOr($value, $doc),
            '$nor' => !self::evaluateOr($value, $doc),
            '$not' => !self::evaluateQuery($doc, $value),
            default => false,
        };
    }

    private static function evaluateAnd(array $conds, array $doc): bool
    {
        foreach ($conds as $c) if (!self::evaluateQuery($doc, $c)) return false;
        return true;
    }

    private static function evaluateOr(array $conds, array $doc): bool
    {
        foreach ($conds as $c) if (self::evaluateQuery($doc, $c)) return true;
        return false;
    }

    private static function evaluateFieldOperators(mixed $value, array $ops, string $field): bool
    {
        foreach ($ops as $op => $opVal) {
            if (!self::compare($value, $opVal, $op, $field, $ops)) {
                return false;
            }
        }
        return true;
    }

    private static function compare(mixed $val, mixed $qVal, string $op, string $field = '', array $fullOps = []): bool
    {
        return match ($op) {
            '$eq'         => self::equals($val, $qVal),
            '$ne'         => !self::equals($val, $qVal),
            '$gt', '$gte' => self::greater($val, $qVal, $op),
            '$lt', '$lte' => self::less($val, $qVal, $op),
            '$in'         => self::inArray($val, $qVal),
            '$nin'        => !self::inArray($val, $qVal),
            '$all'        => self::allMatch($val, $qVal),
            '$size'       => is_array($val) && count($val) === (int)$qVal,
            '$exists'     => $qVal ? $val !== null : $val === null,
            '$regex'      => self::regexMatch($val, $qVal, $fullOps['$options'] ?? ''),
            '$mod'        => self::modMatch($val, $qVal),
            '$elemMatch'  => self::elemMatch($val, $qVal),
            '$type'       => self::typeMatch($val, $qVal),
            '$text'       => self::textSearch($val, $qVal),
            '$bitsAllSet' => self::bitsAllSet($val, $qVal),
            '$bitsAnySet' => self::bitsAnySet($val, $qVal),
            default       => false,
        };
    }

    // Core comparison helpers (same as before, slightly optimized)
    private static function equals(mixed $a, mixed $b): bool
    {
        if (is_array($a) && is_array($b)) {
            return json_encode($a) === json_encode($b);
        }
        return $a == $b;
    }

    private static function greater(mixed $a, mixed $b, string $op): bool
    {
        if (!is_numeric($a) || !is_numeric($b)) return false;
        return $op === '$gte' ? $a >= $b : $a > $b;
    }

    private static function less(mixed $a, mixed $b, string $op): bool
    {
        if (!is_numeric($a) || !is_numeric($b)) return false;
        return $op === '$lte' ? $a <= $b : $a < $b;
    }

    private static function inArray(mixed $v, mixed $arr): bool { return is_array($arr) && in_array($v, $arr, true); }
    private static function allMatch(mixed $v, mixed $req): bool
    {
        if (!is_array($v) || !is_array($req)) return false;
        foreach ($req as $item) if (!in_array($item, $v, true)) return false;
        return true;
    }

    private static function elemMatch(mixed $value, array $query): bool
    {
        if (!is_array($value)) return false;
        foreach ($value as $item) {
            if (is_array($item) && self::evaluateQuery($item, $query)) return true;
        }
        return false;
    }

    private static function regexMatch(mixed $v, mixed $p, string $opt = ''): bool
    {
        if (!is_string($v)) return false;
        $flags = '';
        if (str_contains($opt, 'i')) $flags .= 'i';
        return @preg_match('/' . $p . '/' . $flags, $v) === 1;
    }

    private static function modMatch(mixed $v, mixed $mod): bool
    {
        if (!is_numeric($v) || !is_array($mod) || count($mod) !== 2) return false;
        return fmod((float)$v, (float)$mod[0]) == (float)$mod[1];
    }

    private static function textSearch(mixed $v, mixed $s): bool
    {
        if (!is_string($v) || !is_string($s)) return false;
        $words = array_filter(explode(' ', strtolower($s)));
        $text = strtolower($v);
        foreach ($words as $w) if (str_contains($text, trim($w))) return true;
        return false;
    }

    private static function bitsAllSet(mixed $v, mixed $m): bool { return is_int($v) && is_int($m) && ($v & $m) === $m; }
    private static function bitsAnySet(mixed $v, mixed $m): bool { return is_int($v) && is_int($m) && ($v & $m) !== 0; }

    private static function typeMatch(mixed $v, mixed $t): bool
    {
        return match ($t) {
            2, 'string' => is_string($v),
            1, 19, 'double', 'number' => is_numeric($v),
            16, 18, 'int', 'long' => is_int($v),
            8, 'bool' => is_bool($v),
            4, 'array' => is_array($v),
            3, 'object' => is_array($v) && !array_is_list($v),
            10, 'null' => $v === null,
            default => false,
        };
    }

    private static function getFieldValue(array $doc, string $field): mixed
    {
        $parts = explode('.', $field);
        $current = $doc;
        foreach ($parts as $part) {
            if (is_array($current) && array_key_exists($part, $current)) {
                $current = $current[$part];
            } else {
                return null;
            }
        }
        return $current;
    }

    // ====================== PROJECTION ======================
    public static function project(array $doc, array $projection): array
    {
        if (empty($projection)) return $doc;

        $result = [];
        foreach ($projection as $field => $inc) {
            if ($inc === 1 || $inc === true) {
                $val = self::getFieldValue($doc, (string)$field);
                if ($val !== null) {
                    $parts = explode('.', (string)$field);
                    $target = &$result;
                    foreach ($parts as $i => $p) {
                        if ($i === count($parts) - 1) {
                            $target[$p] = $val;
                        } else {
                            $target[$p] ??= [];
                            $target = &$target[$p];
                        }
                    }
                }
            }
        }
        return $result;
    }

    // ====================== UPDATE OPERATORS ======================

    public static function applyUpdate(array $document, array $update): array
    {
        $doc = $document; // copy

        foreach ($update as $op => $changes) {
            if (!str_starts_with($op, '$')) continue;

            match ($op) {
                '$set'       => self::updateSet($doc, $changes),
                '$unset'     => self::updateUnset($doc, $changes),
                '$inc'       => self::updateInc($doc, $changes),
                '$mul'       => self::updateMul($doc, $changes),
                '$push'      => self::updatePush($doc, $changes),
                '$pull'      => self::updatePull($doc, $changes),
                '$pullAll'   => self::updatePullAll($doc, $changes),
                '$addToSet'  => self::updateAddToSet($doc, $changes),
                '$rename'    => self::updateRename($doc, $changes),
                '$min', '$max' => self::updateMinMax($doc, $changes, $op),
                default => null,
            };
        }

        return $doc;
    }

    private static function updateSet(array &$doc, array $changes): void
    {
        foreach ($changes as $field => $value) {
            self::setFieldValue($doc, $field, $value);
        }
    }

    private static function updateUnset(array &$doc, array $fields): void
    {
        foreach ($fields as $field => $val) {
            $parts = explode('.', $field);
            $current = &$doc;
            foreach ($parts as $i => $part) {
                if ($i === count($parts) - 1) {
                    unset($current[$part]);
                } else {
                    if (!isset($current[$part])) break;
                    $current = &$current[$part];
                }
            }
        }
    }

    private static function updateInc(array &$doc, array $changes): void
    {
        foreach ($changes as $field => $amount) {
            $val = self::getFieldValue($doc, $field) ?? 0;
            self::setFieldValue($doc, $field, (is_numeric($val) ? $val : 0) + $amount);
        }
    }

    private static function updateMul(array &$doc, array $changes): void
    {
        foreach ($changes as $field => $amount) {
            $val = self::getFieldValue($doc, $field) ?? 0;
            self::setFieldValue($doc, $field, (is_numeric($val) ? $val : 0) * $amount);
        }
    }

    private static function updatePush(array &$doc, array $changes): void
    {
        foreach ($changes as $field => $value) {
            $arr = self::getFieldValue($doc, $field) ?? [];
            if (!is_array($arr)) $arr = [];
            $arr[] = $value;
            self::setFieldValue($doc, $field, $arr);
        }
    }

    private static function updatePull(array &$doc, array $changes): void
    {
        foreach ($changes as $field => $condition) {
            $arr = self::getFieldValue($doc, $field);
            if (!is_array($arr)) continue;

            $filtered = [];
            foreach ($arr as $item) {
                if (is_array($condition) && self::matches($item, $condition)) {
                    continue; // remove
                }
                if ($item !== $condition) {
                    $filtered[] = $item;
                }
            }
            self::setFieldValue($doc, $field, $filtered);
        }
    }

    private static function updatePullAll(array &$doc, array $changes): void
    {
        foreach ($changes as $field => $values) {
            $arr = self::getFieldValue($doc, $field);
            if (!is_array($arr)) continue;
            $arr = array_values(array_diff($arr, (array)$values));
            self::setFieldValue($doc, $field, $arr);
        }
    }

    private static function updateAddToSet(array &$doc, array $changes): void
    {
        foreach ($changes as $field => $value) {
            $arr = self::getFieldValue($doc, $field) ?? [];
            if (!is_array($arr)) $arr = [];
            if (!in_array($value, $arr, true)) {
                $arr[] = $value;
            }
            self::setFieldValue($doc, $field, $arr);
        }
    }

    private static function updateRename(array &$doc, array $changes): void
    {
        foreach ($changes as $old => $new) {
            $val = self::getFieldValue($doc, $old);
            if ($val !== null) {
                self::setFieldValue($doc, $new, $val);
                // remove old
                $parts = explode('.', $old);
                $current = &$doc;
                foreach ($parts as $i => $p) {
                    if ($i === count($parts)-1) unset($current[$p]);
                    else $current = &$current[$p];
                }
            }
        }
    }

    private static function updateMinMax(array &$doc, array $changes, string $op): void
    {
        foreach ($changes as $field => $value) {
            $current = self::getFieldValue($doc, $field);
            if ($current === null || ($op === '$min' ? $value < $current : $value > $current)) {
                self::setFieldValue($doc, $field, $value);
            }
        }
    }

    private static function setFieldValue(array &$doc, string $field, mixed $value): void
    {
        $parts = explode('.', $field);
        $current = &$doc;
        foreach ($parts as $i => $part) {
            if ($i === count($parts) - 1) {
                $current[$part] = $value;
            } else {
                if (!isset($current[$part]) || !is_array($current[$part])) {
                    $current[$part] = [];
                }
                $current = &$current[$part];
            }
        }
    }

    // ====================== JSON HELPERS ======================
    public static function matchesJson(string $docJson, string $queryJson): bool
    {
        $doc = json_decode($docJson, true, 512, JSON_THROW_ON_ERROR);
        $q   = json_decode($queryJson, true, 512, JSON_THROW_ON_ERROR);
        return self::matches($doc, $q);
    }

    public static function applyUpdateJson(string $docJson, string $updateJson): array
    {
        $doc = json_decode($docJson, true, 512, JSON_THROW_ON_ERROR);
        $upd = json_decode($updateJson, true, 512, JSON_THROW_ON_ERROR);
        return self::applyUpdate($doc, $upd);
    }
}
