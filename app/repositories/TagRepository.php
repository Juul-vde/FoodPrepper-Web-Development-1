<?php

namespace App\Repositories;

use PDO;

class TagRepository extends BaseRepository
{
    protected $table = 'tags';

    public function create($name, $description = null)
    {
        $sql = "INSERT INTO {$this->table} (name, description) 
                VALUES (:name, :description)";
        
        $this->execute($sql, [
            ':name' => $name,
            ':description' => $description
        ]);

        return $this->db->lastInsertId();
    }

    public function update($id, $name, $description = null)
    {
        $sql = "UPDATE {$this->table} SET name = :name, description = :description WHERE id = :id";
        
        return $this->execute($sql, [
            ':id' => $id,
            ':name' => $name,
            ':description' => $description
        ])->rowCount() > 0;
    }

    public function findByName($name)
    {
        $sql = "SELECT * FROM {$this->table} WHERE name = :name";
        $stmt = $this->execute($sql, [':name' => $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTagsForRecipe($recipeId)
    {
        $sql = "SELECT t.* FROM {$this->table} t 
                INNER JOIN recipe_tags rt ON t.id = rt.tag_id 
                WHERE rt.recipe_id = :recipe_id";
        $stmt = $this->execute($sql, [':recipe_id' => $recipeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTagWithRecipeCount($tagId)
    {
        $sql = "SELECT t.*, COUNT(rt.recipe_id) as recipe_count FROM {$this->table} t 
                LEFT JOIN recipe_tags rt ON t.id = rt.tag_id 
                WHERE t.id = :id GROUP BY t.id";
        $stmt = $this->execute($sql, [':id' => $tagId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMostPopularTags($limit = 10)
    {
        $sql = "SELECT t.*, COUNT(rt.recipe_id) as recipe_count FROM {$this->table} t 
                LEFT JOIN recipe_tags rt ON t.id = rt.tag_id 
                GROUP BY t.id ORDER BY recipe_count DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
