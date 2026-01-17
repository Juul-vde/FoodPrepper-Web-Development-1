<?php

namespace App\Services;

use App\Repositories\ShoppingListRepository;
use App\Repositories\ShoppingListItemRepository;
use App\Repositories\WeeklyPlanItemRepository;
use App\Repositories\IngredientRepository;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;

class ShoppingListService
{
    private $shoppingListRepository;
    private $shoppingListItemRepository;
    private $weeklyPlanItemRepository;
    private $ingredientRepository;

    public function __construct()
    {
        $this->shoppingListRepository = new ShoppingListRepository();
        $this->shoppingListItemRepository = new ShoppingListItemRepository();
        $this->weeklyPlanItemRepository = new WeeklyPlanItemRepository();
        $this->ingredientRepository = new IngredientRepository();
    }

    public function generateShoppingList($userId, $weeklyPlanId)
    {
        // Check if shopping list already exists for this week
        $existingList = $this->shoppingListRepository->findByWeeklyPlanId($weeklyPlanId);
        if ($existingList) {
            // Delete old items and regenerate
            $this->shoppingListItemRepository->deleteByShoppingListId($existingList['id']);
        } else {
            // Create new shopping list
            $shoppingList = new ShoppingList($userId, $weeklyPlanId);
            $existingList = new \stdClass();
            $existingList['id'] = $this->shoppingListRepository->create($shoppingList);
        }

        // Get all meals in the weekly plan
        $meals = $this->weeklyPlanItemRepository->findByWeeklyPlanId($weeklyPlanId);

        // Aggregate ingredients
        $ingredientsByName = [];

        foreach ($meals as $meal) {
            if (!$meal['recipe_id']) continue;

            $ingredients = $this->ingredientRepository->getIngredientsByRecipe($meal['recipe_id']);
            $servings = $meal['servings'] ?? 1;

            foreach ($ingredients as $ingredient) {
                $ingredientName = $ingredient['name'];
                $quantity = $ingredient['quantity'] * $servings;
                $unit = $ingredient['unit'];

                if (!isset($ingredientsByName[$ingredientName])) {
                    $ingredientsByName[$ingredientName] = [
                        'ingredient_id' => $ingredient['id'],
                        'quantity' => 0,
                        'unit' => $unit
                    ];
                }

                // Add quantities (assuming same unit)
                $ingredientsByName[$ingredientName]['quantity'] += $quantity;
            }
        }

        // Create shopping list items
        foreach ($ingredientsByName as $ingredientName => $data) {
            $item = new ShoppingListItem(
                $existingList['id'],
                $data['ingredient_id'],
                $data['quantity'],
                $data['unit']
            );
            $this->shoppingListItemRepository->create($item);
        }

        return $existingList['id'];
    }

    public function getShoppingList($shoppingListId)
    {
        return $this->shoppingListRepository->getShoppingListWithItems($shoppingListId);
    }

    public function getUserShoppingLists($userId)
    {
        return $this->shoppingListRepository->findByUserId($userId);
    }

    public function getShoppingListItems($shoppingListId)
    {
        return $this->shoppingListItemRepository->findByShoppingListId($shoppingListId);
    }

    public function getUncheckedItems($shoppingListId)
    {
        return $this->shoppingListItemRepository->findUncheckedByShoppingListId($shoppingListId);
    }

    public function toggleItemChecked($itemId)
    {
        return $this->shoppingListItemRepository->toggleChecked($itemId);
    }

    public function updateItemQuantity($itemId, $quantity, $unit)
    {
        $item = new ShoppingListItem();
        $item->setId($itemId);
        $item->setQuantity($quantity);
        $item->setUnit($unit);

        return $this->shoppingListItemRepository->update($item);
    }

    public function deleteShoppingList($shoppingListId)
    {
        $this->shoppingListItemRepository->deleteByShoppingListId($shoppingListId);
        return $this->shoppingListRepository->delete($shoppingListId);
    }

    public function getCheckProgress($shoppingListId)
    {
        $items = $this->getShoppingListItems($shoppingListId);
        $total = count($items);
        $checked = count(array_filter($items, function($item) {
            return $item['is_checked'];
        }));

        return [
            'total' => $total,
            'checked' => $checked,
            'percentage' => $total > 0 ? ($checked / $total) * 100 : 0
        ];
    }

    public function exportAsText($shoppingListId)
    {
        $items = $this->getShoppingListItems($shoppingListId);
        $text = "Shopping List\n";
        $text .= "==============\n\n";

        foreach ($items as $item) {
            $checked = $item['is_checked'] ? "[x]" : "[ ]";
            $text .= "$checked {$item['quantity']} {$item['unit']} - {$item['ingredient_name']}\n";
        }

        return $text;
    }
}
