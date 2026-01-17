<?php

namespace App\Services;

use App\Repositories\IngredientRepository;
use App\Models\Ingredient;

class IngredientService
{
    private $ingredientRepository;

    public function __construct()
    {
        $this->ingredientRepository = new IngredientRepository();
    }

    public function getAllIngredients()
    {
        return $this->ingredientRepository->findAll();
    }

    public function getIngredientById($ingredientId)
    {
        return $this->ingredientRepository->findById($ingredientId);
    }

    public function getIngredientByName($name)
    {
        return $this->ingredientRepository->findByName($name);
    }

    public function searchIngredients($keyword)
    {
        return $this->ingredientRepository->search($keyword);
    }

    public function createIngredient($name, $calories = null, $protein = null, $carbs = null, $fat = null)
    {
        if (empty($name)) {
            throw new \Exception("Ingredient name is required");
        }

        // Check if ingredient already exists
        $existing = $this->ingredientRepository->findByName($name);
        if ($existing) {
            return $existing['id'];
        }

        $ingredient = new Ingredient($name, $calories);
        $ingredient->setProtein($protein);
        $ingredient->setCarbs($carbs);
        $ingredient->setFat($fat);

        return $this->ingredientRepository->create($ingredient);
    }

    public function updateIngredient($ingredientId, $name, $calories = null, $protein = null, $carbs = null, $fat = null)
    {
        if (empty($name)) {
            throw new \Exception("Ingredient name is required");
        }

        $ingredient = new Ingredient($name, $calories);
        $ingredient->setId($ingredientId);
        $ingredient->setProtein($protein);
        $ingredient->setCarbs($carbs);
        $ingredient->setFat($fat);

        return $this->ingredientRepository->update($ingredient);
    }

    public function deleteIngredient($ingredientId)
    {
        return $this->ingredientRepository->delete($ingredientId);
    }

    public function getIngredientsByRecipe($recipeId)
    {
        return $this->ingredientRepository->getIngredientsByRecipe($recipeId);
    }

    public function getNutritionInfo($ingredients)
    {
        $totalCalories = 0;
        $totalProtein = 0;
        $totalCarbs = 0;
        $totalFat = 0;

        foreach ($ingredients as $ingredient) {
            $quantity = $ingredient['quantity'] ?? 1;
            $totalCalories += ($ingredient['calories'] ?? 0) * $quantity;
            $totalProtein += ($ingredient['protein'] ?? 0) * $quantity;
            $totalCarbs += ($ingredient['carbs'] ?? 0) * $quantity;
            $totalFat += ($ingredient['fat'] ?? 0) * $quantity;
        }

        return [
            'calories' => round($totalCalories, 2),
            'protein' => round($totalProtein, 2),
            'carbs' => round($totalCarbs, 2),
            'fat' => round($totalFat, 2)
        ];
    }
}
