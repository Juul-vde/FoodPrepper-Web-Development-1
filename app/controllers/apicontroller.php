<?php

namespace App\Controllers;

use App\Services\RecipeService;
use App\Services\IngredientService;
use App\Services\ShoppingListService;

class apicontroller
{
    private $recipeService;
    private $ingredientService;
    private $shoppingListService;

    public function __construct()
    {
        $this->recipeService = new RecipeService();
        $this->ingredientService = new IngredientService();
        $this->shoppingListService = new ShoppingListService();
        
        // Set JSON response headers
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
    }

    /**
     * GET /api/recipes
     * Returns all recipes with their categories and basic information
     */
    public function recipes()
    {
        try {
            $recipes = $this->recipeService->getAllRecipes();
            
            // Format response
            $response = [
                'success' => true,
                'count' => count($recipes),
                'data' => $recipes
            ];
            
            http_response_code(200);
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
        exit;
    }

    /**
     * GET /api/ingredients
     * Returns all ingredients with nutritional information
     */
    public function ingredients()
    {
        try {
            $ingredients = $this->ingredientService->getAllIngredients();
            
            // Format response
            $response = [
                'success' => true,
                'count' => count($ingredients),
                'data' => $ingredients
            ];
            
            http_response_code(200);
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
        exit;
    }

    /**
     * GET /api/shoppinglist?id=123
     * Returns shopping list items for a specific shopping list ID
     */
    public function shoppinglist()
    {
        try {
            $shoppingListId = $_GET['id'] ?? null;
            
            if (!$shoppingListId) {
                $this->sendError('Shopping list ID is required. Use ?id=123', 400);
                exit;
            }

            // Validate ID is numeric
            if (!is_numeric($shoppingListId)) {
                $this->sendError('Invalid shopping list ID format', 400);
                exit;
            }

            $shoppingList = $this->shoppingListService->getShoppingList($shoppingListId);
            
            if (!$shoppingList) {
                $this->sendError('Shopping list not found', 404);
                exit;
            }

            $items = $this->shoppingListService->getShoppingListItems($shoppingListId);
            $progress = $this->shoppingListService->getCheckProgress($shoppingListId);
            
            // Format response
            $response = [
                'success' => true,
                'data' => [
                    'shopping_list' => $shoppingList,
                    'items' => $items,
                    'progress' => $progress
                ]
            ];
            
            http_response_code(200);
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
        exit;
    }

    /**
     * GET /api/recipe?id=123
     * Returns a single recipe with full details, ingredients, and instructions
     */
    public function recipe()
    {
        try {
            $recipeId = $_GET['id'] ?? null;
            
            if (!$recipeId) {
                $this->sendError('Recipe ID is required. Use ?id=123', 400);
                exit;
            }

            if (!is_numeric($recipeId)) {
                $this->sendError('Invalid recipe ID format', 400);
                exit;
            }

            $recipe = $this->recipeService->getRecipeWithIngredients($recipeId);
            
            if (!$recipe) {
                $this->sendError('Recipe not found', 404);
                exit;
            }
            
            $response = [
                'success' => true,
                'data' => $recipe
            ];
            
            http_response_code(200);
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
        exit;
    }

    /**
     * Send error response in JSON format
     */
    private function sendError($message, $statusCode = 400)
    {
        http_response_code($statusCode);
        echo json_encode([
            'success' => false,
            'error' => $message
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Default index method - API documentation
     */
    public function index()
    {
        $response = [
            'success' => true,
            'message' => 'FoodPrepper API v1.0',
            'endpoints' => [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/recipes',
                    'description' => 'Get all recipes with categories and basic information'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/recipe?id={id}',
                    'description' => 'Get a single recipe with full details and ingredients'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/ingredients',
                    'description' => 'Get all ingredients with nutritional information'
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/shoppinglist?id={id}',
                    'description' => 'Get shopping list items for a specific shopping list ID'
                ]
            ],
            'documentation' => 'Visit /api/index for this documentation'
        ];
        
        http_response_code(200);
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
