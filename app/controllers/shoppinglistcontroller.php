<?php
// This controller handles the shopping list page

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\ShoppingListService;
use App\Services\WeeklyPlanService;
use App\Services\CsrfService;

class shoppinglistcontroller
{
    // Variables to hold our services
    private $authService;
    private $shoppingListService;
    private $weeklyPlanService;

    // Constructor runs when controller is created
    public function __construct()
    {
        // Create service objects
        $this->authService = new AuthService();
        $this->shoppingListService = new ShoppingListService();
        $this->weeklyPlanService = new WeeklyPlanService();

        // Check if user is logged in
        if (!$this->authService->isAuthenticated()) {
            header('Location: /auth/index');
            exit;
        }
        
        // Create security token
        CsrfService::generateToken();
    }

    // Show the shopping list page
    public function index()
    {
        // Get current user ID
        $userId = $_SESSION['user_id'];
        
        // Get the current weekly plan
        $weeklyPlan = $this->weeklyPlanService->getCurrentWeekPlan($userId);

        // Check if there is a weekly plan
        if (!$weeklyPlan) {
            // No plan = no shopping list
            $shoppingList = null;
            $items = [];
            $progress = ['total' => 0, 'checked' => 0, 'percentage' => 0];
        } else {
            // Check if shopping list already exists
            $existingList = $this->shoppingListService->getShoppingListByWeeklyPlan($weeklyPlan['id']);
            
            if (!$existingList) {
                // Create new shopping list
                $shoppingListId = $this->shoppingListService->generateShoppingList($userId, $weeklyPlan['id']);
            } else {
                // Use existing shopping list
                $shoppingListId = $existingList['id'];
            }
            
            // Get shopping list and items
            $shoppingList = $this->shoppingListService->getShoppingList($shoppingListId);
            $items = $this->shoppingListService->getShoppingListItems($shoppingListId);
            $progress = $this->shoppingListService->getCheckProgress($shoppingListId);
        }

        // Load the shopping list page
        include __DIR__ . '/../views/shoppinglist/index.php';
    }

    // Generate a new shopping list
    public function generate()
    {
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            return;
        }

        try {
            // Check security token
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!CsrfService::validateToken($csrfToken)) {
                throw new \Exception("Invalid security token. Please try again.");
            }
            
            // Get current user ID
            $userId = $_SESSION['user_id'];
            
            // Get current weekly plan
            $weeklyPlan = $this->weeklyPlanService->getCurrentWeekPlan($userId);

            // Check if there is a plan
            if (!$weeklyPlan) {
                throw new \Exception("No weekly plan found");
            }

            // Generate the shopping list
            $shoppingListId = $this->shoppingListService->generateShoppingList($userId, $weeklyPlan['id']);

            // Show success message
            $_SESSION['success'] = "Shopping list generated successfully";
            header('Location: /shoppinglist/index');
            exit;
            
        } catch (\Exception $e) {
            // Show error message
            $_SESSION['error'] = $e->getMessage();
            header('Location: /shoppinglist/index');
            exit;
        }
    }

    // Toggle checkbox for an item (used by JavaScript)
    public function toggleitem()
    {
        // Check if request is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }

        try {
            // Check security token
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!CsrfService::validateToken($csrfToken)) {
                throw new \Exception("Invalid security token.");
            }
            
            // Get item ID from form
            $itemId = $_POST['item_id'] ?? null;

            // Check if item ID is provided
            if (!$itemId) {
                throw new \Exception("Item ID is required");
            }

            // Toggle the checkbox
            $this->shoppingListService->toggleItemChecked($itemId);

            // Return success as JSON
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
            
        } catch (\Exception $e) {
            // Return error as JSON
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
}
