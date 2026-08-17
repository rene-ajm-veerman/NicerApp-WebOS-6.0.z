<?php
declare(strict_types=1);

/**
 * class_naDB_2_0_0 - Modern CouchDB Connector for NicerApp WebOS uDB2
 *
 * Enhanced version focused on dbName routing and compatibility with old methods
 * used in db_init.uDB2.php and class.database_API.php
 */

require_once realpath(dirname(__FILE__).'/../../../../..') . '/NicerAppWebOS/boot.php';
require_once realpath(dirname(__FILE__).'/../../../../..') . '/NicerAppWebOS/functions.php';
require_once 'CouchDB-specific/sag/src/Sag.php';
require_once 'CouchDB-specific/Sag-support-functions.php';

class class_naDB_2_0_0
{
    public string $cn = 'class_naDB_2_0_0';
    public string $connectionType = 'couchdb';

    public $debug = false;
    public $naWebOS;
    public $cdb;                    // Sag instance
    public $config = [];
    public $username = null;
    public $roles = [];
    public $isAdmin = false;
    public $admin = false;
    public $currentDatabase = '';

    public function __construct($naWebOS, string $username = 'Guest', ?array $cRec = null)
    {
        if (is_null($naWebOS)) {
            trigger_error('class_naDB_2_0_0::__construct() : $naWebOS is null', E_USER_ERROR);
        }

        $this->naWebOS = $naWebOS;
        $this->config = $cRec ?? [];
        $this->username = $username;

        $this->admin = ($username === 'admin' ||
        $username === $this->translate_plainUserName_to_couchdbUserName($naWebOS->ownerInfo['OWNER_NAME'] ?? ''));

        $this->initSagConnection();
        $this->performLogin();

        if ($this->admin) {
            $this->isAdmin = true;
            $_SESSION['cdb_userIsAdministrator'] = true;
        }

        return $this;
    }

    private function initSagConnection(): void
    {
        $host = $this->config['host'] ?? '127.0.0.1';
        $port = $this->config['port'] ?? 5984;

        $this->cdb = new Sag($host, $port);
        $this->cdb->setHTTPAdapter($this->config['httpAdapter'] ?? 'HTTP_CURL');
        $this->cdb->useSSL($this->config['useSSL'] ?? false);
    }

    private function performLogin(): void
    {
        try {
            if (!empty($this->config['username'])) {
                $result = cdb_login($this, $this->cdb, $this->config, $this->config['username']);
            } else {
                $result = cdb_login($this, $this->cdb, null, $this->username);
            }
        } catch (Exception $e) {
            trigger_error("CouchDB login failed: " . $e->getMessage(), E_USER_WARNING);
            $result = cdb_login($this, $this->cdb, null, 'Guest');
        }

        if (!$result) {
            trigger_error("Failed to login to CouchDB as {$this->username}", E_USER_WARNING);
            return;
        }

        $session = $this->cdb->getSession();
        if (isset($session->body->userCtx)) {
            $u = $session->body->userCtx;
            $this->username = $u->name ?? $this->username;
            $this->roles = $u->roles ?? [];
        }
    }

    public function translate_plainUserName_to_couchdbUserName(string $name): string
    {
        return strtolower(str_replace([' ', '.'], ['__', '_'], $name));
    }

    // ====================== DATABASE ROUTING ======================

    public function setDatabase(string $dbName): self
    {
        $this->currentDatabase = $dbName;
        $this->cdb->setDatabase($dbName);
        return $this;
    }

    public function getCurrentDatabase(): string
    {
        return $this->currentDatabase;
    }

    // ====================== HIGH-LEVEL OPERATIONS (uDB2 compatible) ======================

    public function createDatabase(string $dbName): bool
    {
        try {
            $this->setDatabase($dbName);
            $this->cdb->createDatabase($dbName);
            return true;
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'file_exists') !== false || strpos($e->getMessage(), 'already exists') !== false) {
                return true;
            }
            trigger_error("Failed to create database {$dbName}: " . $e->getMessage(), E_USER_WARNING);
            return false;
        }
    }

    public function setSecurity(string $dbName, array $admins = [], array $members = []): bool
    {
        $this->setDatabase($dbName);

        $security = [
            'admins'  => $admins,
            'members' => $members
        ];

        try {
            $this->cdb->put('_security', $security);
            return true;
        } catch (Exception $e) {
            trigger_error("Failed to set _security on {$dbName}", E_USER_WARNING);
            return false;
        }
    }

    public function createIndex(array $fields, ?string $name = null, string $dbName = ''): bool
    {
        if ($dbName) $this->setDatabase($dbName);

        $indexData = [
            'index' => ['fields' => $fields],
            'name'  => $name ?? 'idx_' . implode('_', array_map('strtolower', $fields)),
            'type'  => 'json'
        ];

        try {
            $this->cdb->setIndex($indexData);
            return true;
        } catch (Exception $e) {
            trigger_error("Failed to create index on {$this->currentDatabase}", E_USER_WARNING);
            return false;
        }
    }

    // ====================== CRUD ======================

    public function find(array $selector = [], array $options = []): object
    {
        $dbName = $options['database'] ?? $this->currentDatabase ?? $this->config['database'] ?? '';
        if ($dbName) $this->setDatabase($dbName);

        $query = array_merge([
            'selector' => $selector,
            'limit'    => $options['limit'] ?? 100,
        ], array_filter([
            'bookmark' => $options['bookmark'] ?? null,
            'sort'     => $options['sort'] ?? null,
            'fields'   => $options['fields'] ?? null,
            'skip'     => $options['skip'] ?? null,
        ]));

        return $this->cdb->find($query);
    }

    public function insertOne(array $doc, string $dbName = ''): array
    {
        if ($dbName) $this->setDatabase($dbName);
        $result = $this->cdb->post($doc);
        return is_object($result) ? (array)$result : $result;
    }

    public function getDoc(string $id, string $dbName = ''): ?object
    {
        if ($dbName) $this->setDatabase($dbName);
        try {
            return $this->cdb->get($id);
        } catch (Exception $e) {
            return null;
        }
    }

    public function updateDoc(string $id, array $updates, string $dbName = ''): array
    {
        if ($dbName) $this->setDatabase($dbName);

        $doc = $this->getDoc($id, $dbName);
        if (!$doc) return ['error' => 'not_found'];

        $updatedDoc = array_merge((array)$doc->body, $updates);
        return (array) $this->cdb->put($id, $updatedDoc);
    }

    public function deleteDoc(string $id, string $rev, string $dbName = ''): bool
    {
        if ($dbName) $this->setDatabase($dbName);
        try {
            $this->cdb->delete($id, $rev);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // ====================== LEGACY COMPATIBILITY ======================

    public function getAllDatabases(): object
    {
        return $this->cdb->getAllDatabases();
    }

    public function getIndexes(string $dbName = ''): object
    {
        if ($dbName) $this->setDatabase($dbName);
        return $this->cdb->getIndexes();
    }

    public function getSession(): object
    {
        return $this->cdb->getSession();
    }

    // Forward other unknown methods to Sag if needed
    public function __call(string $method, array $args)
    {
        if (method_exists($this->cdb, $method)) {
            if ($this->currentDatabase) {
                $this->cdb->setDatabase($this->currentDatabase);
            }
            return call_user_func_array([$this->cdb, $method], $args);
        }

        trigger_error("Method {$method} not found in class_naDB_2_0_0 or Sag", E_USER_WARNING);
        return null;
    }
}
?>
