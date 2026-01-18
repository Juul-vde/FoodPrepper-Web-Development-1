<?php
// Repository for tag database operations
// Tags are labels for recipes (e.g., "vegetarian", "quick", "healthy")

namespace App\Repositories;

use PDO;

class TagRepository extends BaseRepository
{
    // Set table name
    protected $table = 'tags';

    // Create new tag
    public function create($name, $description = null)
    {
        // Insert tag with optional description
        $sql = "INSERT INTO {$this->table} (name, description) 
                VALUES (:name, :description)";
        
        $this->execute($sql, [
            ':name' => $name,
            ':description' => $description
        ]);

        // Return new tag ID
        return $this->db->lastInsertId();
    }

    // Update existing tag
    public function update($id, $name, $description = null)
    {
        // Update tag data
        $sql = "UPDATE {$this->table} SET name = :name, description = :description WHERE id = :id";
        
        // Return true if rows were changed
        return $this->execute($sql, [
            ':id' => $id,
            ':name' => $name,
            ':description' => $description
        ])->rowCount() > 0;
    }

    // Find tag by name
    public function findByName($name)
    {
        $sql = "SELECT * FROM {$this->table} WHERE name = :name";
        $stmt = $this->execute($sql, [':name' => $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all tags for a specific recipe
    public function getTagsForRecipe($recipeId)
    {
        // Join tags with recipe_tags table
        $sql = "SELECT t.* FROM {$this->table} t 
                INNER JOIN recipe_tags rt ON t.id = rt.tag_id 
                WHERE rt.recipe_id = :recipe_id";
        
        $stmt = $this->execute($sql, [':recipe_id' => $recipeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get tag with count of how many recipes use it
    public function getTagWithRecipeCount($tagId)
    {
        // Count how many recipes have this tag
        $sql = "SELECT t.*, COUNT(rt.recipe_id) as recipe_count FROM {$this->table} t 
                LEFT JOIN recipe_tags rt ON t.id = rt.tag_id 
                WHERE t.id = :id GROUP BY t.id";
        
        $stmt = $this->execute($sql, [':id' => $tagId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get most used tags
    // $limit = how many tags to return (default 10)
    public function getMostPopularTags($limit = 10)
    {
        // Count recipes per tag and sort by most used
        $sql = "SELECT t.*, COUNT(rt.recipe_id) as recipe_count FROM {$this->table} t 
                LEFT JOIN recipe_tags rt ON t.id = rt.tag_id 
                GROUP BY t.id ORDER BY recipe_count DESC LIMIT :limit";
        
        // Prepare and bind limit as integer
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
