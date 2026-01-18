<?php
// Base repository with common database functions
// All other repositories inherit from this one

namespace App\Repositories;

use PDO;

abstract class BaseRepository
{
    // Database connection
    protected $db;
    
    // Table name (set by child classes)
    protected $table;

    // Constructor runs when repository is created
    public function __construct()
    {
        // Get database connection
        $this->db = Database::getInstance()->getConnection();
    }

    // Get all rows from the table
    public function findAll()
    {
        // Build SQL query
        $sql = "SELECT * FROM {$this->table}";
        
        // Prepare and execute query
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        // Return all results as array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get one row by ID
    public function findById($id)
    {
        // Build SQL query with placeholder
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        
        // Prepare and execute with ID parameter
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        // Return single result
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Delete a row by ID
    public function delete($id)
    {
        // Build DELETE query
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        
        // Prepare and execute
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // Helper function to run any SQL query
    // $sql = the SQL query to run
    // $params = array of parameters (e.g., [':id' => 5])
    protected function execute($sql, $params = [])
    {
        // Prepare query (prevents SQL injection)
        $stmt = $this->db->prepare($sql);
        
        // Execute with parameters
        $stmt->execute($params);
        
        // Return statement object
        return $stmt;
    }
}
