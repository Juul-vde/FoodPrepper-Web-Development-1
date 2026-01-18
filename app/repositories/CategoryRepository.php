<?php
// Repository for category database operations

namespace App\Repositories;

use App\Models\Category;
use PDO;

class CategoryRepository extends BaseRepository
{
    // Set table name
    protected $table = 'categories';
    
    // Get all categories sorted by display order and name
    public function findAll()
    {
        // Sort by display_order first, then by name alphabetically
        $sql = "SELECT * FROM {$this->table} ORDER BY display_order ASC, name ASC";
        $stmt = $this->execute($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create new category
    public function create(Category $category)
    {
        // Insert category data
        $sql = "INSERT INTO {$this->table} (name, description, icon) 
                VALUES (:name, :description, :icon)";
        
        $this->execute($sql, [
            ':name' => $category->getName(),
            ':description' => $category->getDescription(),
            ':icon' => $category->getIcon()
        ]);

        // Return new category ID
        return $this->db->lastInsertId();
    }

    // Update existing category
    public function update(Category $category)
    {
        // Update category data
        $sql = "UPDATE {$this->table} SET name = :name, description = :description, icon = :icon WHERE id = :id";
        
        // Return true if rows were changed
        return $this->execute($sql, [
            ':id' => $category->getId(),
            ':name' => $category->getName(),
            ':description' => $category->getDescription(),
            ':icon' => $category->getIcon()
        ])->rowCount() > 0;
    }

    // Find category by name
    public function findByName($name)
    {
        $sql = "SELECT * FROM {$this->table} WHERE name = :name";
        $stmt = $this->execute($sql, [':name' => $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get category with count of how many recipes it has
    public function getCategoryWithRecipes($categoryId)
    {
        // Join with recipes table and count them
        $sql = "SELECT c.*, COUNT(r.id) as recipe_count FROM {$this->table} c 
                LEFT JOIN recipes r ON c.id = r.category_id 
                WHERE c.id = :id GROUP BY c.id";
        
        $stmt = $this->execute($sql, [':id' => $categoryId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
