<?php

namespace App\Repositories;

use App\Models\Recipe;
use PDO;

class RecipeRepository extends BaseRepository
{
    protected $table = 'recipes';

    public function create(Recipe $recipe)
    {
        $sql = "INSERT INTO {$this->table} (title, description, instructions, image_url, prep_time, cook_time, servings, difficulty, category_id) 
                VALUES (:title, :description, :instructions, :image_url, :prep_time, :cook_time, :servings, :difficulty, :category_id)";
        
        $this->execute($sql, [
            ':title' => $recipe->getTitle(),
            ':description' => $recipe->getDescription(),
            ':instructions' => $recipe->getInstructions(),
            ':image_url' => $recipe->getImageUrl(),
            ':prep_time' => $recipe->getPrepTime(),
            ':cook_time' => $recipe->getCookTime(),
            ':servings' => $recipe->getServings(),
            ':difficulty' => $recipe->getDifficulty(),
            ':category_id' => $recipe->getCategory()
        ]);

        return $this->db->lastInsertId();
    }

    public function update(Recipe $recipe)
    {
        $sql = "UPDATE {$this->table} SET title = :title, description = :description, instructions = :instructions, 
                image_url = :image_url, prep_time = :prep_time, cook_time = :cook_time, servings = :servings, 
                difficulty = :difficulty, category_id = :category_id WHERE id = :id";
        
        return $this->execute($sql, [
            ':id' => $recipe->getId(),
            ':title' => $recipe->getTitle(),
            ':description' => $recipe->getDescription(),
            ':instructions' => $recipe->getInstructions(),
            ':image_url' => $recipe->getImageUrl(),
            ':prep_time' => $recipe->getPrepTime(),
            ':cook_time' => $recipe->getCookTime(),
            ':servings' => $recipe->getServings(),
            ':difficulty' => $recipe->getDifficulty(),
            ':category_id' => $recipe->getCategory()
        ])->rowCount() > 0;
    }

    public function findByCategory($categoryId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE category_id = :category_id";
        $stmt = $this->execute($sql, [':category_id' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByTag($tagId)
    {
        $sql = "SELECT DISTINCT r.* FROM {$this->table} r 
                INNER JOIN recipe_tags rt ON r.id = rt.recipe_id 
                WHERE rt.tag_id = :tag_id";
        $stmt = $this->execute($sql, [':tag_id' => $tagId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecipeWithTags($recipeId)
    {
        $sql = "SELECT r.*, GROUP_CONCAT(t.name) as tags FROM {$this->table} r 
                LEFT JOIN recipe_tags rt ON r.id = rt.recipe_id 
                LEFT JOIN tags t ON rt.tag_id = t.id 
                WHERE r.id = :id GROUP BY r.id";
        $stmt = $this->execute($sql, [':id' => $recipeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addTag($recipeId, $tagId)
    {
        $sql = "INSERT INTO recipe_tags (recipe_id, tag_id) VALUES (:recipe_id, :tag_id)";
        return $this->execute($sql, [':recipe_id' => $recipeId, ':tag_id' => $tagId])->rowCount() > 0;
    }

    public function removeTag($recipeId, $tagId)
    {
        $sql = "DELETE FROM recipe_tags WHERE recipe_id = :recipe_id AND tag_id = :tag_id";
        return $this->execute($sql, [':recipe_id' => $recipeId, ':tag_id' => $tagId])->rowCount() > 0;
    }

    public function search($keyword)
    {
        $sql = "SELECT * FROM {$this->table} WHERE title LIKE :keyword OR description LIKE :keyword";
        $stmt = $this->execute($sql, [':keyword' => "%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
