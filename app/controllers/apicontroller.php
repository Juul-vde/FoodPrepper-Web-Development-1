<?php
// This controller provides API endpoints that return JSON data
// API = Application Programming Interface (for other apps to get our data)

namespace App\Controllers;

use App\Services\RecipeService;
use App\Services\IngredientService;
use App\Services\ShoppingListService;

class apicontroller
{
    // Variables to hold our services
    private $recipeService;
    private $ingredientService;
    private $shoppingListService;

    // Constructor runs when controller is created
    public function __construct()
    {
        // Create service objects
        $this->recipeService = new RecipeService();
        $this->ingredientService = new IngredientService();
        $this->shoppingListService = new ShoppingListService();
        
        // Tell browser we're sending JSON data
        header('Content-Type: application/json');
        
        // Allow other websites to access this API
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
    }

    // GET /api/recipes - Returns all recipes
    public function recipes()
    {
        try {
            // Get all recipes from database
            $recipes = $this->recipeService->getAllRecipes();
            
            // Create JSON response with success status
            $response = [
                'success' => true,
                'count' => count($recipes),
                'data' => $recipes
            ];
            
            // Send success code (200 = OK)
            http_response_code(200);
            
            // Convert array to JSON and send it
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            // If something went wrong, send error
            $this->sendError($e->getMessage(), 500);
        }
        exit;
    }

    // GET /api/ingredients - Returns all ingredients
    public function ingredients()
    {
        try {
            // Get all ingredients from database
            $ingredients = $this->ingredientService->getAllIngredients();
            
            // Create JSON response
            $response = [
                'success' => true,
                'count' => count($ingredients),
                'data' => $ingredients
            ];
            
            // Send success code
            http_response_code(200);
            
            // Convert to JSON and send
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            // Send error if something failed
            $this->sendError($e->getMessage(), 500);
        }
        exit;
    }

    // GET /api/shoppinglist?id=123 - Returns shopping list with items
    public function shoppinglist()
    {
        try {
            // Get shopping list ID from URL
            $shoppingListId = $_GET['id'] ?? null;
            
            // Check if ID was provided
            if (!$shoppingListId) {
                $this->sendError('Shopping list ID is required. Use ?id=123', 400);
                exit;
            }

            // Check if ID is a number
            if (!is_numeric($shoppingListId)) {
                $this->sendError('Invalid shopping list ID format', 400);
                exit;
            }

            // Get shopping list from database
            $shoppingList = $this->shoppingListService->getShoppingList($shoppingListId);
            
            // Check if shopping list exists
            if (!$shoppingList) {
                $this->sendError('Shopping list not found', 404);
                exit;
            }

            // Get items and progress
            $items = $this->shoppingListService->getShoppingListItems($shoppingListId);
            $progress = $this->shoppingListService->getCheckProgress($shoppingListId);
            
            // Create JSON response with all data
            $response = [
                'success' => true,
                'data' => [
                    'shopping_list' => $shoppingList,
                    'items' => $items,
                    'progress' => $progress
                ]
            ];
            
            // Send success code and data
            http_response_code(200);
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            // Send error if something failed
            $this->sendError($e->getMessage(), 500);
        }
        exit;
    }

    // Send error message as JSON
    // $message = error text to show
    // $statusCode = HTTP error code (400 = bad request, 500 = server error)
    private function sendError($message, $statusCode = 400)
    {
        // Set error code
        http_response_code($statusCode);
        
        // Send error as JSON
        echo json_encode([
            'success' => false,
            'error' => $message
        ], JSON_PRETTY_PRINT);
    }
}
