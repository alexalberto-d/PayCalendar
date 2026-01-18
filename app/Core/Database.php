<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        try {
            $this->connection = new PDO("sqlite:" . DB_PATH);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Initialize if empty
            $this->init();
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    private function init()
    {
        $schema = file_get_contents(__DIR__ . '/../../database/schema.sql');
        $this->connection->exec($schema);
        $this->migrate();
    }

    private function migrate()
    {
        // Check if user_id exists in subscriptions
        $stmt = $this->connection->query("PRAGMA table_info(subscriptions)");
        $columns = $stmt->fetchAll();
        $hasUserId = false;
        foreach ($columns as $column) {
            if ($column['name'] === 'user_id') {
                $hasUserId = true;
                break;
            }
        }

        if (!$hasUserId) {
            try {
                $this->connection->exec("ALTER TABLE subscriptions ADD COLUMN user_id INTEGER DEFAULT 0");
            } catch (PDOException $e) {
            }
        }

        // Check if end_date exists
        $hasEndDate = false;
        foreach ($columns as $column) {
            if ($column['name'] === 'end_date') {
                $hasEndDate = true;
                break;
            }
        }

        if (!$hasEndDate) {
            try {
                $this->connection->exec("ALTER TABLE subscriptions ADD COLUMN end_date DATE NULL");
            } catch (PDOException $e) {
            }
        }
    }
}
