<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\ShoppingListService;
use App\Services\WeeklyPlanService;

class shoppinglistcontroller
{
    private $authService;
    private $shoppingListService;
    private $weeklyPlanService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->shoppingListService = new ShoppingListService();
        $this->weeklyPlanService = new WeeklyPlanService();

        if (!$this->authService->isAuthenticated()) {
            header('Location: /auth/index');
            exit;
        }
    }

    public function index()
    {
        $userId = $_SESSION['user_id'];
        $weeklyPlan = $this->weeklyPlanService->getCurrentWeekPlan($userId);

        if (!$weeklyPlan) {
            $shoppingList = null;
            $items = [];
            $progress = ['total' => 0, 'checked' => 0, 'percentage' => 0];
        } else {
            // Check if shopping list exists, if not, create it
            $existingList = $this->shoppingListService->getShoppingListByWeeklyPlan($weeklyPlan['id']);
            
            if (!$existingList) {
                // Generate new shopping list
                $shoppingListId = $this->shoppingListService->generateShoppingList($userId, $weeklyPlan['id']);
            } else {
                $shoppingListId = $existingList['id'];
            }
            
            $shoppingList = $this->shoppingListService->getShoppingList($shoppingListId);
            $items = $this->shoppingListService->getShoppingListItems($shoppingListId);
            $progress = $this->shoppingListService->getCheckProgress($shoppingListId);
        }

        include __DIR__ . '/../views/shoppinglist/index.php';
    }

    public function generate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            return;
        }

        try {
            $userId = $_SESSION['user_id'];
            $weeklyPlan = $this->weeklyPlanService->getCurrentWeekPlan($userId);

            if (!$weeklyPlan) {
                throw new \Exception("No weekly plan found");
            }

            $shoppingListId = $this->shoppingListService->generateShoppingList($userId, $weeklyPlan['id']);

            $_SESSION['success'] = "Shopping list generated successfully";
            header('Location: /shoppinglist/index');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /shoppinglist/index');
            exit;
        }
    }

    public function toggleitem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }

        try {
            $itemId = $_POST['item_id'] ?? null;

            if (!$itemId) {
                throw new \Exception("Item ID is required");
            }

            $this->shoppingListService->toggleItemChecked($itemId);

            // Always return JSON for this endpoint (used by AJAX)
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    public function updateQuantity()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            return;
        }

        try {
            $itemId = $_POST['item_id'] ?? null;
            $quantity = $_POST['quantity'] ?? 0;
            $unit = $_POST['unit'] ?? '';

            if (!$itemId || !$quantity) {
                throw new \Exception("Item ID and quantity are required");
            }

            $this->shoppingListService->updateItemQuantity($itemId, $quantity, $unit);

            $_SESSION['success'] = "Item quantity updated";
            header('Location: /shoppinglist/index');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /shoppinglist/index');
            exit;
        }
    }

    public function export()
    {
        try {
            $userId = $_SESSION['user_id'];
            
            // Get shopping list ID from URL parameter
            $shoppingListId = $_GET['id'] ?? null;
            
            if (!$shoppingListId) {
                // Fallback: get current weekly plan's shopping list
                $weeklyPlan = $this->weeklyPlanService->getCurrentWeekPlan($userId);
                if (!$weeklyPlan) {
                    throw new \Exception("No weekly plan found");
                }
                $shoppingList = $this->shoppingListService->getShoppingListByWeeklyPlan($weeklyPlan['id']);
                if (!$shoppingList) {
                    throw new \Exception("No shopping list found");
                }
                $shoppingListId = $shoppingList['id'];
            }

            $text = $this->shoppingListService->exportAsText($shoppingListId);

            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="shopping-list.txt"');
            echo $text;
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /shoppinglist/index');
            exit;
        }
    }

    public function download()
    {
        // Alias for export to support both naming conventions
        $this->export();
    }

    public function deleteitem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            return;
        }

        try {
            $itemId = $_POST['item_id'] ?? null;

            if (!$itemId) {
                throw new \Exception("Item ID is required");
            }

            $this->shoppingListService->deleteItem($itemId);

            $_SESSION['success'] = "Item deleted successfully";
            header('Location: /shoppinglist/index');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /shoppinglist/index');
            exit;
        }
    }
}
