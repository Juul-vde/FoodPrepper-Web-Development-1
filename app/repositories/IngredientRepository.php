<?php

namespace App\Repositories;

use App\Models\Ingredient;
use PDO;

class IngredientRepository extends BaseRepository
{
    protected $table = 'ingredients';

    public function create(Ingredient $ingredient)
    {
        $sql = "INSERT INTO {$this->table} (name, calories, protein, carbs, fat) 
                VALUES (:name, :calories, :protein, :carbs, :fat)";
        
        $this->execute($sql, [
            ':name' => $ingredient->getName(),
            ':calories' => $ingredient->getCalories(),
            ':protein' => $ingredient->getProtein(),
            ':carbs' => $ingredient->getCarbs(),
            ':fat' => $ingredient->getFat()
        ]);

        return $this->db->lastInsertId();
    }

    public function update(Ingredient $ingredient)
    {
        $sql = "UPDATE {$this->table} SET name = :name, calories = :calories, 
                protein = :protein, carbs = :carbs, fat = :fat WHERE id = :id";
        
        return $this->execute($sql, [
            ':id' => $ingredient->getId(),
            ':name' => $ingredient->getName(),
            ':calories' => $ingredient->getCalories(),
            ':protein' => $ingredient->getProtein(),
            ':carbs' => $ingredient->getCarbs(),
            ':fat' => $ingredient->getFat()
        ])->rowCount() > 0;
    }

    public function findByName($name)
    {
        $sql = "SELECT * FROM {$this->table} WHERE name = :name";
        $stmt = $this->execute($sql, [':name' => $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getIngredientsByRecipe($recipeId)
    {
        $sql = "SELECT i.*, ri.quantity, ri.unit FROM {$this->table} i 
                INNER JOIN recipe_ingredients ri ON i.id = ri.ingredient_id 
                WHERE ri.recipe_id = :recipe_id";
        $stmt = $this->execute($sql, [':recipe_id' => $recipeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search($keyword)
    {
        $sql = "SELECT * FROM {$this->table} WHERE name LIKE :keyword";
        $stmt = $this->execute($sql, [':keyword' => "%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
