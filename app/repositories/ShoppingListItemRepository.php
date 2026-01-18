<?php

namespace App\Repositories;

use App\Models\ShoppingListItem;
use PDO;

class ShoppingListItemRepository extends BaseRepository
{
    protected $table = 'shopping_list_items';

    public function create(ShoppingListItem $item)
    {
        $sql = "INSERT INTO {$this->table} (shopping_list_id, ingredient_id, quantity, unit, is_checked) 
                VALUES (:shopping_list_id, :ingredient_id, :quantity, :unit, :is_checked)";
        
        $this->execute($sql, [
            ':shopping_list_id' => $item->getShoppingListId(),
            ':ingredient_id' => $item->getIngredientId(),
            ':quantity' => $item->getQuantity(),
            ':unit' => $item->getUnit(),
            ':is_checked' => $item->getIsChecked() ? 1 : 0
        ]);

        return $this->db->lastInsertId();
    }

    public function update(ShoppingListItem $item)
    {
        $sql = "UPDATE {$this->table} SET quantity = :quantity, unit = :unit, is_checked = :is_checked WHERE id = :id";
        
        return $this->execute($sql, [
            ':id' => $item->getId(),
            ':quantity' => $item->getQuantity(),
            ':unit' => $item->getUnit(),
            ':is_checked' => $item->getIsChecked() ? 1 : 0
        ])->rowCount() > 0;
    }

    public function toggleChecked($itemId)
    {
        $sql = "UPDATE {$this->table} SET is_checked = NOT is_checked WHERE id = :id";
        return $this->execute($sql, [':id' => $itemId])->rowCount() > 0;
    }

    public function findByShoppingListId($shoppingListId)
    {
        $sql = "SELECT sli.*, i.name as ingredient_name 
                FROM {$this->table} sli
                LEFT JOIN ingredients i ON sli.ingredient_id = i.id
                WHERE sli.shopping_list_id = :shopping_list_id ORDER BY i.name";
        $stmt = $this->execute($sql, [':shopping_list_id' => $shoppingListId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findUncheckedByShoppingListId($shoppingListId)
    {
        $sql = "SELECT sli.*, i.name as ingredient_name 
                FROM {$this->table} sli
                LEFT JOIN ingredients i ON sli.ingredient_id = i.id
                WHERE sli.shopping_list_id = :shopping_list_id AND sli.is_checked = FALSE 
                ORDER BY i.name";
        $stmt = $this->execute($sql, [':shopping_list_id' => $shoppingListId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteByShoppingListId($shoppingListId)
    {
        $sql = "DELETE FROM {$this->table} WHERE shopping_list_id = :shopping_list_id";
        return $this->execute($sql, [':shopping_list_id' => $shoppingListId])->rowCount();
    }

    public function delete($itemId)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->execute($sql, [':id' => $itemId]);
        return $stmt->rowCount() > 0;
    }
}
