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
            $shoppingListId = $this->shoppingListService->generateShoppingList($userId, $weeklyPlan['id']);
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

    public function toggleItem()
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

            $this->shoppingListService->toggleItemChecked($itemId);

            $_SESSION['success'] = "Item status updated";
            header('Location: /shoppinglist/index');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /shoppinglist/index');
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
            $weeklyPlan = $this->weeklyPlanService->getCurrentWeekPlan($userId);

            if (!$weeklyPlan) {
                throw new \Exception("No weekly plan found");
            }

            $shoppingListId = $this->shoppingListService->generateShoppingList($userId, $weeklyPlan['id']);
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
}
