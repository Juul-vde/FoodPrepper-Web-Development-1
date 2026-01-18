<?php
// Database connection class
// Uses singleton pattern (only one connection for entire app)

namespace App\Repositories;

use PDO;
use PDOException;

class Database
{
    // Store the single instance
    private static $instance = null;
    
    // The actual database connection
    private $connection;

    // Private constructor (can't create Database directly)
    // Forces everyone to use getInstance()
    private function __construct()
    {
        try {
            // Database connection settings
            $type = "mysql";
            $servername = "mysql";
            $username = "root";
            $password = "secret123";
            $database = "FoodPrepper";

            // Build connection string (DSN = Data Source Name)
            $dsn = "$type:host=$servername;dbname=$database;charset=utf8mb4";
            
            // Create PDO connection
            $this->connection = new PDO($dsn, $username, $password);
            
            // Set error mode to throw exceptions
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // If connection fails, stop program and show error
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Get the single Database instance
    // Creates it if it doesn't exist yet
    public static function getInstance()
    {
        // Check if instance already exists
        if (self::$instance === null) {
            // Create new instance if not
            self::$instance = new self();
        }
        
        // Return the instance
        return self::$instance;
    }

    // Get the PDO connection object
    public function getConnection()
    {
        return $this->connection;
    }
}
