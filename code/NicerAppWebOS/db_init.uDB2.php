<?php
/**
 * NicerApp WebOS - Database Initialization using uDB2
 * Much cleaner than the old couchdb plugin setup
 */

require_once dirname(__DIR__) . '/NicerAppWebOS/businessLogic/databases/uDB-2.0.0/uDB-2.1.0.byGrok.class.php';
require_once dirname(__DIR__) . '/NicerAppWebOS/businessLogic/databases/uDB-2.0.0/uDB2.InitHelper.class.php';

echo "=== Starting uDB2 Database Initialization ===\n\n";

try {
    // ============== CONFIGURATION ==============
    global $naWebOS;

    $cRec = [
        'driver'          => 'couchdb',
        'database'        => '',           // will be set per dataset
        'host'            => 'localhost',
        'port'            => 5984,
        'username'        => 'admin',
        'password'        => $naWebOS->cfg['couchdb']['password'] ?? '',
        // Add any other required config keys
    ];

    $adminUsername = 'admin';

    // ============== INITIALIZE uDB2 ==============
    $uDB  = uDB2::createFromConfig($cRec, $adminUsername);
    $init = new uDB2_InitHelper($uDB, $adminUsername);

    // ============== CREATE DEFAULT DATASETS ==============
    echo "Creating default system datasets...\n";
    $init->initializeDefaultDatasets([
        'system_users',
        'system_sessions',
        'system_logs',
        'system_config',
        'system_cache'
    ]);

    // ============== CREATE COMMON APP DATASETS ==============
    echo "Creating application datasets...\n";
    $apps = ['blog', 'forum', 'chat', 'gallery', 'calendar', 'notes'];
    foreach ($apps as $app) {
        $init->createAppDataSet($app);
    }

    // ============== CREATE TEST USER DATASETS ==============
    echo "Creating sample user datasets...\n";
    $testUsers = ['demo', 'john_doe', 'jane_smith'];
    foreach ($testUsers as $user) {
        $init->createUserDataSet($user);
    }

    // ============== CUSTOM DATASET EXAMPLE ==============
    echo "Creating custom dataset...\n";
    $init->createDataSet(
        'custom_highscore',
        [
            'admins'  => ['names' => ['admin'], 'roles' => ['admins']],
            'members' => ['roles' => ['users']]
        ],
        [
            [['player', 'score'], 'idx_player_score'],
            [['created'], 'idx_created']
        ]
    );

    echo "\n=== uDB2 Database Initialization Completed Successfully! ===\n";

} catch (Exception $e) {
    echo "\n=== ERROR during initialization ===\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
?>
