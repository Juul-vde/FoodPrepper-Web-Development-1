<?php
// Service for managing recipes
// Handles recipe creation, updates, searching, and ingredient management

namespace App\Services;

use App\Repositories\RecipeRepository;
use App\Repositories\TagRepository;
use App\Repositories\IngredientRepository;
use App\Models\Recipe;

class RecipeService
{
    // Repositories to access data from database
    private $recipeRepository;
    private $tagRepository;
    private $ingredientRepository;

    // Constructor runs when service is created
    public function __construct()
    {
        $this->recipeRepository = new RecipeRepository();
        $this->tagRepository = new TagRepository();
        $this->ingredientRepository = new IngredientRepository();
    }

    // Get all recipes
    public function getAllRecipes()
    {
        return $this->recipeRepository->findAll();
    }

    // Get one recipe by ID
    public function getRecipeById($recipeId)
    {
        return $this->recipeRepository->getRecipeWithTags($recipeId);
    }

    // Search recipes by tag
    public function searchByTag($tagId)
    {
        return $this->recipeRepository->findByTag($tagId);
    }

    // Search recipes by category
    public function searchByCategory($categoryId)
    {
        return $this->recipeRepository->findByCategory($categoryId);
    }

    // Search recipes by keyword (in title or description)
    public function searchRecipes($keyword)
    {
        return $this->recipeRepository->search($keyword);
    }

    // Create a new recipe
    public function createRecipe($title, $description, $instructions, $imageUrl, $prepTime, $cookTime, $servings, $difficulty, $categoryId, $tags = [])
    {
        // Validate required fields
        if (empty($title) || empty($instructions)) {
            throw new \Exception("Title and instructions are required");
        }

        // Create recipe object
        $recipe = new Recipe($title, $description);
        $recipe->setInstructions($instructions);
        $recipe->setImageUrl($imageUrl);
        $recipe->setPrepTime($prepTime);
        $recipe->setCookTime($cookTime);
        $recipe->setServings($servings ?? 1); // Default to 1 serving
        $recipe->setDifficulty($difficulty);
        $recipe->setCategory($categoryId);

        // Save recipe to database
        $recipeId = $this->recipeRepository->create($recipe);

        // Add tags to recipe
        foreach ($tags as $tagId) {
            $this->recipeRepository->addTag($recipeId, $tagId);
        }

        // Return new recipe ID
        return $recipeId;
    }

    // Update an existing recipe
    public function updateRecipe($recipeId, $title, $description, $instructions, $imageUrl, $prepTime, $cookTime, $servings, $difficulty, $categoryId)
    {
        // Create recipe object with updated data
        $recipe = new Recipe($title, $description);
        $recipe->setId($recipeId);
        $recipe->setInstructions($instructions);
        $recipe->setImageUrl($imageUrl);
        $recipe->setPrepTime($prepTime);
        $recipe->setCookTime($cookTime);
        $recipe->setServings($servings);
        $recipe->setDifficulty($difficulty);
        $recipe->setCategory($categoryId);

        // Save updated recipe to database
        return $this->recipeRepository->update($recipe);
    }

    // Delete a recipe
    public function deleteRecipe($recipeId)
    {
        return $this->recipeRepository->delete($recipeId);
    }

    // Add a tag to a recipe
    public function addTagToRecipe($recipeId, $tagId)
    {
        return $this->recipeRepository->addTag($recipeId, $tagId);
    }

    // Remove a tag from a recipe
    public function removeTagFromRecipe($recipeId, $tagId)
    {
        return $this->recipeRepository->removeTag($recipeId, $tagId);
    }

    // Add an ingredient to a recipe
    public function addIngredientToRecipe($recipeId, $ingredientId, $quantity, $unit)
    {
        return $this->recipeRepository->addIngredient($recipeId, $ingredientId, $quantity, $unit);
    }

    // Remove one ingredient from a recipe
    public function removeIngredientFromRecipe($recipeId, $ingredientId)
    {
        return $this->recipeRepository->removeIngredient($recipeId, $ingredientId);
    }

    // Remove all ingredients from a recipe
    public function removeAllIngredientsFromRecipe($recipeId)
    {
        return $this->recipeRepository->removeAllIngredients($recipeId);
    }

    // Get recipe with all its ingredients
    public function getRecipeWithIngredients($recipeId)
    {
        // Get recipe data
        $recipe = $this->getRecipeById($recipeId);
        
        // Get ingredients for this recipe
        $ingredients = $this->ingredientRepository->getIngredientsByRecipe($recipeId);
        
        // Add ingredients to recipe array
        $recipe['ingredients'] = $ingredients;
        
        return $recipe;
    }
}
