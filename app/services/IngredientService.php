<?php
// Service for managing ingredients
// Handles ingredient creation, updates, searching, and nutrition calculations

namespace App\Services;

use App\Repositories\IngredientRepository;
use App\Models\Ingredient;

class IngredientService
{
    // Repository to access ingredient data from database
    private $ingredientRepository;

    // Constructor runs when service is created
    public function __construct()
    {
        $this->ingredientRepository = new IngredientRepository();
    }

    // Get all ingredients
    public function getAllIngredients()
    {
        return $this->ingredientRepository->findAll();
    }

    // Get one ingredient by ID
    public function getIngredientById($ingredientId)
    {
        return $this->ingredientRepository->findById($ingredientId);
    }

    // Find ingredient by name
    public function getIngredientByName($name)
    {
        return $this->ingredientRepository->findByName($name);
    }

    // Search ingredients by keyword
    public function searchIngredients($keyword)
    {
        return $this->ingredientRepository->search($keyword);
    }

    // Create a new ingredient
    public function createIngredient($name, $calories = null, $protein = null, $carbs = null, $fat = null)
    {
        // Check if name is provided
        if (empty($name)) {
            throw new \Exception("Ingredient name is required");
        }

        // Check if ingredient already exists
        $existing = $this->ingredientRepository->findByName($name);
        if ($existing) {
            // Return existing ingredient ID instead of creating duplicate
            return $existing['id'];
        }

        // Create new ingredient object
        $ingredient = new Ingredient($name, $calories);
        $ingredient->setProtein($protein);
        $ingredient->setCarbs($carbs);
        $ingredient->setFat($fat);

        // Save to database and return new ID
        return $this->ingredientRepository->create($ingredient);
    }

    // Update an existing ingredient
    public function updateIngredient($ingredientId, $name, $calories = null, $protein = null, $carbs = null, $fat = null)
    {
        // Check if name is provided
        if (empty($name)) {
            throw new \Exception("Ingredient name is required");
        }

        // Create ingredient object with updated data
        $ingredient = new Ingredient($name, $calories);
        $ingredient->setId($ingredientId);
        $ingredient->setProtein($protein);
        $ingredient->setCarbs($carbs);
        $ingredient->setFat($fat);

        // Save updated ingredient to database
        return $this->ingredientRepository->update($ingredient);
    }

    // Delete an ingredient
    public function deleteIngredient($ingredientId)
    {
        return $this->ingredientRepository->delete($ingredientId);
    }

    // Get all ingredients for a specific recipe
    public function getIngredientsByRecipe($recipeId)
    {
        return $this->ingredientRepository->getIngredientsByRecipe($recipeId);
    }

    // Calculate total nutrition for a list of ingredients
    // Used to show nutrition facts for a recipe
    public function getNutritionInfo($ingredients)
    {
        // Start with zero totals
        $totalCalories = 0;
        $totalProtein = 0;
        $totalCarbs = 0;
        $totalFat = 0;

        // Add up nutrition from each ingredient
        foreach ($ingredients as $ingredient) {
            // Get quantity (default to 1 if not specified)
            $quantity = $ingredient['quantity'] ?? 1;
            
            // Multiply nutrition values by quantity and add to totals
            $totalCalories += ($ingredient['calories'] ?? 0) * $quantity;
            $totalProtein += ($ingredient['protein'] ?? 0) * $quantity;
            $totalCarbs += ($ingredient['carbs'] ?? 0) * $quantity;
            $totalFat += ($ingredient['fat'] ?? 0) * $quantity;
        }

        // Return totals rounded to 2 decimal places
        return [
            'calories' => round($totalCalories, 2),
            'protein' => round($totalProtein, 2),
            'carbs' => round($totalCarbs, 2),
            'fat' => round($totalFat, 2)
        ];
    }
}
