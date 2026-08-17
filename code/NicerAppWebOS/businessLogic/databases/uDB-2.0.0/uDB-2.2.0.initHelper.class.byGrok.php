<?php
declare(strict_types=1);

/**
 * uDB2 Initialization Helper
 */

class uDB2_InitHelper
{
    private uDB2 $uDB;
    private string $username;

    public function __construct(uDB2 $uDB, string $username = 'Guest')
    {
        $this->uDB = $uDB;
        $this->username = $username;
    }

    public function createDataSet(
        string $datasetName,
        array $security = [],
        array $indexes = []
    ): bool {
        $dbName = $this->makeDBName($datasetName);

        if (!$this->uDB->createDatabase($dbName)) {
            trigger_error("Failed to create database: $dbName", E_USER_WARNING);
            return false;
        }

        $this->uDB->setSecurity($dbName, $security['admins'] ?? [], $security['members'] ?? []);

        foreach ($indexes as $idx) {
            if (is_array($idx)) {
                $fields = $idx['fields'] ?? $idx;
                $name = $idx['name'] ?? null;
                $this->uDB->createIndex($fields, $name);
            }
        }

        return true;
    }

    public function createUserDataSet(string $username)
    {
        $dataset = "user_{$username}";
        return $this->createDataSet($dataset, [
            'admins'  => ['names' => [$username, 'admin'], 'roles' => ['admins']],
            'members' => ['names' => [$username], 'roles' => ['users']]
        ], [
            [['username'], 'idx_username'],
            [['email'], 'idx_email'],
            [['created'], 'idx_created']
        ]);
    }

    public function createAppDataSet(string $appName)
    {
        $dataset = "app_{$appName}";
        return $this->createDataSet($dataset, [
            'admins'  => ['names' => ['admin'], 'roles' => ['admins']],
            'members' => ['roles' => ['users', 'guests']]
        ], [
            [['app'], 'idx_app'],
            [['type'], 'idx_type'],
            [['created'], 'idx_created']
        ]);
    }

    public function createSystemDataSet(string $name)
    {
        $dataset = "sys_{$name}";
        return $this->createDataSet($dataset, [
            'admins'  => ['names' => ['admin'], 'roles' => ['admins', 'system']],
            'members' => ['roles' => ['system']]
        ]);
    }

    public function initializeDefaultDatasets(array $extraDatasets = [])
    {
        $datasets = array_merge([
            'system_users',
            'system_sessions',
            'system_logs',
            'system_config',
            'system_cache'
        ], $extraDatasets);

        foreach ($datasets as $ds) {
            $this->createDataSet($ds);
        }

        echo "uDB2: Initialized " . count($datasets) . " default datasets.\n";
    }

    private function makeDBName(string $name)
    {
        $name = strtolower(trim($name));
        return preg_replace('/[^a-z0-9_]/', '_', $name);
    }
}
?>
