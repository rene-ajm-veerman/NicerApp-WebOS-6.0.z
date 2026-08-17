<?php
declare(strict_types=1);

/**
 * uDB2 Compatibility Layer with Enhanced Error Handling + Return Types
 * Allows old code using the original couchdb class to work during migration
 */

class uDB2_Compat extends uDB2
{
    public $cdb;                    // Old code expects this property
    private $originalConnector;
    private string $lastError = '';
    private array $errorLog = [];

    public function __construct($naWebOS, string $username = 'Guest', array $cRec = []): void
    {
        try {
            parent::__construct(null, 'couchdb');

            $this->couchConnector = new class_NicerAppWebOS_database_API_couchdb_3_2__2_0_0(
                clone $naWebOS, $username, $cRec
            );

            $this->cdb = $this->couchConnector->cdb;
            $this->originalConnector = $this->couchConnector;
            $this->config = $cRec;

        } catch (Exception $e) {
            $this->logError("Failed to initialize uDB2_Compat", $e);
            throw $e;
        }
    }

    // ====================== SAFE FORWARDED METHODS ======================

    public function setDatabase(string $dbName): bool
    {
        try {
            $this->setTable($dbName);
            if ($this->cdb) {
                $this->cdb->setDatabase($dbName);
            }
            return true;
        } catch (Exception $e) {
            $this->logError("setDatabase($dbName) failed", $e);
            return false;
        }
    }

    public function createDataSet(string $datasetName, array $security = [], array $indexes = []): bool
    {
        try {
            $helper = new uDB2_InitHelper($this, $this->config['username'] ?? 'Guest');
            return $helper->createDataSet($datasetName, $security, $indexes);
        } catch (Exception $e) {
            $this->logError("createDataSet($datasetName) failed", $e);
            return false;
        }
    }

    // ====================== COMMON CRUD WITH ERROR HANDLING ======================

    public function find(array $filter = [], array $options = []): array
    {
        try {
            return parent::find($filter, $options);
        } catch (Exception $e) {
            $this->logError("find() failed", $e);
            return [];
        }
    }

    public function insertOne(array $document, array $options = []): array
    {
        try {
            return parent::insertOne($document, $options);
        } catch (Exception $e) {
            $this->logError("insertOne() failed", $e);
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function updateMany(array $filter, array $update, array $options = []): int
    {
        try {
            return parent::updateMany($filter, $update, $options);
        } catch (Exception $e) {
            $this->logError("updateMany() failed", $e);
            return 0;
        }
    }

    public function deleteOne(array $filter): bool
    {
        try {
            return parent::deleteOne($filter);
        } catch (Exception $e) {
            $this->logError("deleteOne() failed", $e);
            return false;
        }
    }

    public function deleteMany(array $filter): int
    {
        try {
            return parent::deleteMany($filter);
        } catch (Exception $e) {
            $this->logError("deleteMany() failed", $e);
            return 0;
        }
    }

    // ====================== ERROR HANDLING ======================

    private function logError(string $message, Exception $exception): void
    {
        $this->lastError = $message . ': ' . $exception->getMessage();

        $this->errorLog[] = [
            'time'      => date('Y-m-d H:i:s'),
            'message'   => $message,
            'error'     => $exception->getMessage(),
            'trace'     => $exception->getTraceAsString()
        ];

        trigger_error("uDB2_Compat: $message - " . $exception->getMessage(), E_USER_WARNING);

        // Optional file logging
        $logFile = dirname(__DIR__, 3) . '/logs/uDB2_compat_errors.log';
        if (is_dir(dirname($logFile)) && is_writable(dirname($logFile))) {
            $entry = date('[Y-m-d H:i:s] ') . $message . PHP_EOL . $exception . PHP_EOL . str_repeat('-', 80) . PHP_EOL;
            file_put_contents($logFile, $entry, FILE_APPEND);
        }
    }

    public function getLastError(): string
    {
        return $this->lastError;
    }

    public function getErrorLog(): array
    {
        return $this->errorLog;
    }

    public function clearErrorLog(): void
    {
        $this->errorLog = [];
        $this->lastError = '';
    }

    // ====================== MAGIC METHOD ======================

    public function __call(string $name, array $arguments): mixed
    {
        try {
            // 1. Try the old cdb connector first (highest priority for legacy)
            if ($this->cdb && method_exists($this->cdb, $name)) {
                return $this->cdb->$name(...$arguments);
            }

            // 2. Try parent uDB2 method
            if (method_exists($this, $name) || method_exists(parent::class, $name)) {
                return $this->{$name}(...$arguments);
            }

            trigger_error("uDB2_Compat: Method '{$name}' not found in compatibility layer", E_USER_WARNING);
            return null;

        } catch (Exception $e) {
            $this->logError("Call to {$name}() failed", $e);
            return null;
        }
    }
}
?>
