<?php
// Service for managing shopping lists
// Generates shopping lists from weekly meal plans
// Combines ingredients from multiple recipes

namespace App\Services;

use App\Repositories\ShoppingListRepository;
use App\Repositories\ShoppingListItemRepository;
use App\Repositories\WeeklyPlanItemRepository;
use App\Repositories\IngredientRepository;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;

class ShoppingListService
{
    // Repositories to access data from database
    private $shoppingListRepository;
    private $shoppingListItemRepository;
    private $weeklyPlanItemRepository;
    private $ingredientRepository;

    // Constructor runs when service is created
    public function __construct()
    {
        $this->shoppingListRepository = new ShoppingListRepository();
        $this->shoppingListItemRepository = new ShoppingListItemRepository();
        $this->weeklyPlanItemRepository = new WeeklyPlanItemRepository();
        $this->ingredientRepository = new IngredientRepository();
    }

    // Generate shopping list from a weekly meal plan
    // This is the main function - it looks at all meals in the week
    // and creates a combined list of all ingredients needed
    public function generateShoppingList($userId, $weeklyPlanId)
    {
        // Check if shopping list already exists for this week
        $existingList = $this->shoppingListRepository->findByWeeklyPlanId($weeklyPlanId);
        
        if ($existingList) {
            // Delete old items so we can regenerate fresh list
            $this->shoppingListItemRepository->deleteByShoppingListId($existingList['id']);
        } else {
            // Create new shopping list
            $shoppingList = new ShoppingList($userId, $weeklyPlanId);
            $existingList = new \stdClass();
            $existingList['id'] = $this->shoppingListRepository->create($shoppingList);
        }

        // Get all meals planned for this week
        $meals = $this->weeklyPlanItemRepository->findByWeeklyPlanId($weeklyPlanId);

        // Array to combine ingredients by name
        // This prevents duplicates (e.g., "chicken" appearing multiple times)
        $ingredientsByName = [];

        // Loop through each meal in the week
        foreach ($meals as $meal) {
            // Skip if no recipe assigned
            if (!$meal['recipe_id']) continue;

            // Get ingredients for this recipe
            $ingredients = $this->ingredientRepository->getIngredientsByRecipe($meal['recipe_id']);
            
            // Get number of servings for this meal
            $servings = $meal['servings'] ?? 1;

            // Loop through each ingredient in the recipe
            foreach ($ingredients as $ingredient) {
                $ingredientName = $ingredient['name'];
                
                // Calculate quantity needed (recipe quantity × servings)
                $quantity = $ingredient['quantity'] * $servings;
                $unit = $ingredient['unit'];

                // If we haven't seen this ingredient yet, add it to array
                if (!isset($ingredientsByName[$ingredientName])) {
                    $ingredientsByName[$ingredientName] = [
                        'ingredient_id' => $ingredient['id'],
                        'quantity' => 0,
                        'unit' => $unit
                    ];
                }

                // Add this quantity to the total for this ingredient
                $ingredientsByName[$ingredientName]['quantity'] += $quantity;
            }
        }

        // Create shopping list items from combined ingredients
        foreach ($ingredientsByName as $ingredientName => $data) {
            $item = new ShoppingListItem(
                $existingList['id'],
                $data['ingredient_id'],
                $data['quantity'],
                $data['unit']
            );
            $this->shoppingListItemRepository->create($item);
        }

        // Return shopping list ID
        return $existingList['id'];
    }

    // Get shopping list with all its items
    public function getShoppingList($shoppingListId)
    {
        return $this->shoppingListRepository->getShoppingListWithItems($shoppingListId);
    }

    // Get shopping list for a specific weekly plan
    public function getShoppingListByWeeklyPlan($weeklyPlanId)
    {
        return $this->shoppingListRepository->findByWeeklyPlanId($weeklyPlanId);
    }

    // Get all shopping lists for a user
    public function getUserShoppingLists($userId)
    {
        return $this->shoppingListRepository->findByUserId($userId);
    }

    // Get all items in a shopping list
    public function getShoppingListItems($shoppingListId)
    {
        return $this->shoppingListItemRepository->findByShoppingListId($shoppingListId);
    }

    // Get only unchecked items (items still need to buy)
    public function getUncheckedItems($shoppingListId)
    {
        return $this->shoppingListItemRepository->findUncheckedByShoppingListId($shoppingListId);
    }

    // Toggle item checked status (check or uncheck)
    // Used when clicking checkbox in shopping list
    public function toggleItemChecked($itemId)
    {
        return $this->shoppingListItemRepository->toggleChecked($itemId);
    }

    // Update quantity for an item
    public function updateItemQuantity($itemId, $quantity, $unit)
    {
        $item = new ShoppingListItem();
        $item->setId($itemId);
        $item->setQuantity($quantity);
        $item->setUnit($unit);

        return $this->shoppingListItemRepository->update($item);
    }

    // Delete entire shopping list with all its items
    public function deleteShoppingList($shoppingListId)
    {
        // Delete all items first
        $this->shoppingListItemRepository->deleteByShoppingListId($shoppingListId);
        
        // Then delete the list itself
        return $this->shoppingListRepository->delete($shoppingListId);
    }

    // Delete single item from shopping list
    public function deleteItem($itemId)
    {
        $result = $this->shoppingListItemRepository->delete($itemId);
        
        // Throw error if delete failed
        if (!$result) {
            throw new \Exception("Failed to delete item. Item may not exist.");
        }
        
        return $result;
    }

    // Calculate how many items are checked
    // Returns totals and percentage for progress bar
    public function getCheckProgress($shoppingListId)
    {
        // Get all items
        $items = $this->getShoppingListItems($shoppingListId);
        
        // Count total items
        $total = count($items);
        
        // Count how many are checked
        $checked = count(array_filter($items, function($item) {
            return $item['is_checked'];
        }));

        // Calculate percentage
        $percentage = $total > 0 ? ($checked / $total) * 100 : 0;

        return [
            'total' => $total,
            'checked' => $checked,
            'percentage' => $percentage
        ];
    }

    // Export shopping list as plain text
    // Useful for printing or copying
    public function exportAsText($shoppingListId)
    {
        // Get all items
        $items = $this->getShoppingListItems($shoppingListId);
        
        // Build text string
        $text = "Shopping List\n";
        $text .= "==============\n\n";

        // Add each item to text
        foreach ($items as $item) {
            // Show [x] if checked, [ ] if not checked
            $checked = $item['is_checked'] ? "[x]" : "[ ]";
            
            // Format: [x] 2 cups - Flour
            $text .= "$checked {$item['quantity']} {$item['unit']} - {$item['ingredient_name']}\n";
        }

        return $text;
    }
}
