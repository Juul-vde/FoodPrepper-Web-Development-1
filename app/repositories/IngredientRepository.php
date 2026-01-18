<?php
// Repository for ingredient database operations

namespace App\Repositories;

use App\Models\Ingredient;
use PDO;

class IngredientRepository extends BaseRepository
{
    // Set table name
    protected $table = 'ingredients';

    // Create new ingredient
    public function create(Ingredient $ingredient)
    {
        // Insert ingredient with nutrition data
        $sql = "INSERT INTO {$this->table} (name, calories, protein, carbs, fat) 
                VALUES (:name, :calories, :protein, :carbs, :fat)";
        
        $this->execute($sql, [
            ':name' => $ingredient->getName(),
            ':calories' => $ingredient->getCalories(),
            ':protein' => $ingredient->getProtein(),
            ':carbs' => $ingredient->getCarbs(),
            ':fat' => $ingredient->getFat()
        ]);

        // Return new ingredient ID
        return $this->db->lastInsertId();
    }

    // Update existing ingredient
    public function update(Ingredient $ingredient)
    {
        // Update ingredient data
        $sql = "UPDATE {$this->table} SET name = :name, calories = :calories, 
                protein = :protein, carbs = :carbs, fat = :fat WHERE id = :id";
        
        // Return true if rows were changed
        return $this->execute($sql, [
            ':id' => $ingredient->getId(),
            ':name' => $ingredient->getName(),
            ':calories' => $ingredient->getCalories(),
            ':protein' => $ingredient->getProtein(),
            ':carbs' => $ingredient->getCarbs(),
            ':fat' => $ingredient->getFat()
        ])->rowCount() > 0;
    }

    // Find ingredient by name
    public function findByName($name)
    {
        $sql = "SELECT * FROM {$this->table} WHERE name = :name";
        $stmt = $this->execute($sql, [':name' => $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get all ingredients for a specific recipe
    // Includes quantity and unit from recipe_ingredients table
    public function getIngredientsByRecipe($recipeId)
    {
        // Join ingredients with recipe_ingredients to get quantities
        $sql = "SELECT i.*, ri.quantity, ri.unit FROM {$this->table} i 
                INNER JOIN recipe_ingredients ri ON i.id = ri.ingredient_id 
                WHERE ri.recipe_id = :recipe_id";
        
        $stmt = $this->execute($sql, [':recipe_id' => $recipeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Search ingredients by keyword
    public function search($keyword)
    {
        // Use LIKE for partial matching
        $sql = "SELECT * FROM {$this->table} WHERE name LIKE :keyword";
        $stmt = $this->execute($sql, [':keyword' => "%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
