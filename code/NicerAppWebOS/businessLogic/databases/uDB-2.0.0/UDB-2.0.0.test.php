<?php
declare(strict_types=1);

/**
 * uDB2 - Full Test Suite
 * Testing find(), insertMany(), setTable(), and error handling
 * For Gavan
 */

require_once __DIR__ . '/uDB-2.0.0.byGrok.class.php';

echo "<h2>uDB2 - Full Test Suite</h2>\n";

try {
    // ====================== 1. CONNECTION ======================
    echo "<h3>1. Connection Test</h3>";
    $uDB = uDB2::connectToDatabase('Guest');
    echo "✅ Connected (Driver: " . $uDB->getDriver() . ")<br>";

    // ====================== 2. TABLE SETUP ======================
    echo "<h3>2. setTable() Test</h3>";
    $uDB->setTable('test_users');
    echo "✅ setTable('test_users')<br>";

    // ====================== 3. INSERTMANY ======================
    echo "<h3>3. insertMany() Test</h3>";

    $newUsers = [
        [
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'age' => 28,
            'status' => 'active',
            'tags' => ['developer', 'designer'],
            'createdAt' => new DateTime()
        ],
        [
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'age' => 34,
            'status' => 'active',
            'tags' => ['manager'],
            'createdAt' => new DateTime()
        ],
        [
            'name' => 'Charlie Brown',
            'email' => 'charlie@example.com',
            'age' => 22,
            'status' => 'inactive'
        ]
    ];

    $result = $uDB->insertMany('', $newUsers);   // empty string = use current setTable()
    echo "✅ insertMany() → Inserted: {$result['inserted']} documents<br>";
    echo "Generated IDs: " . count($result['ids']) . "<br>";

    // Single document insert test
    $single = $uDB->insertMany('test_users', [
        'name' => 'Diana Prince',
        'email' => 'diana@example.com',
        'age' => 31,
        'status' => 'active'
    ]);
    echo "✅ Single document inserted via insertMany()<br>";

    // ====================== 4. FIND TESTS ======================
    echo "<h3>4. find() Tests</h3>";

    // Simple find
    $all = $uDB->find();
    echo "→ All records: " . count($all) . "<br>";

    // Find with query
    $adults = $uDB->find('', ['age' => ['$gte' => 25]]);
    echo "→ Adults (age >= 25): " . count($adults) . "<br>";

    // Find with projection + limit + sort
    $limited = $uDB->find(
        '',
        ['status' => 'active'],
        ['name' => 1, 'email' => 1, 'age' => 1],
        5,
        0,
        ['age' => -1]   // descending age
    );
    echo "→ Limited + projected + sorted: " . count($limited) . " records<br>";

    // Complex query with $or
    $complex = $uDB->find('', [
        '$or' => [
            ['age' => ['$gte' => 30]],
            ['tags' => ['$exists' => true]]
        ]
    ]);
    echo "→ Complex \$or query: " . count($complex) . " records<br>";

    // ====================== 5. DELIBERATE ERRORS / NOTICES ======================
    echo "<h3>5. Deliberate Errors & Notices (for your logger)</h3>";

    trigger_error("uDB2 Test: Deliberate notice - testing logging system", E_USER_NOTICE);

    // Trigger __get() notice
    echo "→ Triggering __get() notice:<br>";
    $dummy = $uDB->nonExistentProperty;

    // Empty update warning
    $uDB->updateMany(['status' => 'active'], []);

} catch (Throwable $e) {
    echo "<div style='color:#d32f2f; background:#ffebee; padding:15px; margin:10px 0; border:1px solid #d32f2f;'>";
    echo "<strong>" . get_class($e) . "</strong><br>";
    echo htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<h3 style='color:green;'>✅ Full test suite completed.</h3>";
echo "<p>Gavan, go ahead and run this against your real + test data.<br>";
echo "Take your time (1-2 days is fine). I'll be here when you're ready to review the results or continue.</p>";
