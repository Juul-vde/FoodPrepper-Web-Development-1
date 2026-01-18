<?php
// Repository for shopping list item database operations

namespace App\Repositories;

use App\Models\ShoppingListItem;
use PDO;

class ShoppingListItemRepository extends BaseRepository
{
    // Set table name
    protected $table = 'shopping_list_items';

    // Create new shopping list item
    public function create(ShoppingListItem $item)
    {
        // Insert item with ingredient, quantity, and checked status
        $sql = "INSERT INTO {$this->table} (shopping_list_id, ingredient_id, quantity, unit, is_checked) 
                VALUES (:shopping_list_id, :ingredient_id, :quantity, :unit, :is_checked)";
        
        $this->execute($sql, [
            ':shopping_list_id' => $item->getShoppingListId(),
            ':ingredient_id' => $item->getIngredientId(),
            ':quantity' => $item->getQuantity(),
            ':unit' => $item->getUnit(),
            ':is_checked' => $item->getIsChecked() ? 1 : 0 // Convert boolean to 1 or 0
        ]);

        // Return new item ID
        return $this->db->lastInsertId();
    }

    // Update shopping list item
    public function update(ShoppingListItem $item)
    {
        // Update quantity, unit, and checked status
        $sql = "UPDATE {$this->table} SET quantity = :quantity, unit = :unit, is_checked = :is_checked WHERE id = :id";
        
        // Return true if rows were changed
        return $this->execute($sql, [
            ':id' => $item->getId(),
            ':quantity' => $item->getQuantity(),
            ':unit' => $item->getUnit(),
            ':is_checked' => $item->getIsChecked() ? 1 : 0
        ])->rowCount() > 0;
    }

    // Toggle checked status (checked to unchecked, or vice versa)
    public function toggleChecked($itemId)
    {
        // NOT flips the boolean (true becomes false, false becomes true)
        $sql = "UPDATE {$this->table} SET is_checked = NOT is_checked WHERE id = :id";
        return $this->execute($sql, [':id' => $itemId])->rowCount() > 0;
    }

    // Get all items for a shopping list
    public function findByShoppingListId($shoppingListId)
    {
        // Join with ingredients to get ingredient names
        $sql = "SELECT sli.*, i.name as ingredient_name 
                FROM {$this->table} sli
                LEFT JOIN ingredients i ON sli.ingredient_id = i.id
                WHERE sli.shopping_list_id = :shopping_list_id ORDER BY i.name";
        
        $stmt = $this->execute($sql, [':shopping_list_id' => $shoppingListId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get only unchecked items
    public function findUncheckedByShoppingListId($shoppingListId)
    {
        // Same as above but only where is_checked = FALSE
        $sql = "SELECT sli.*, i.name as ingredient_name 
                FROM {$this->table} sli
                LEFT JOIN ingredients i ON sli.ingredient_id = i.id
                WHERE sli.shopping_list_id = :shopping_list_id AND sli.is_checked = FALSE 
                ORDER BY i.name";
        
        $stmt = $this->execute($sql, [':shopping_list_id' => $shoppingListId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete all items for a shopping list
    public function deleteByShoppingListId($shoppingListId)
    {
        $sql = "DELETE FROM {$this->table} WHERE shopping_list_id = :shopping_list_id";
        return $this->execute($sql, [':shopping_list_id' => $shoppingListId])->rowCount();
    }

    // Delete single item
    public function delete($itemId)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->execute($sql, [':id' => $itemId]);
        return $stmt->rowCount() > 0;
    }
}
