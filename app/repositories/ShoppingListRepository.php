<?php
// Repository for shopping list database operations

namespace App\Repositories;

use App\Models\ShoppingList;
use PDO;

class ShoppingListRepository extends BaseRepository
{
    // Set table name
    protected $table = 'shopping_lists';

    // Create new shopping list
    public function create(ShoppingList $shoppingList)
    {
        // Insert shopping list linked to user and week plan
        $sql = "INSERT INTO {$this->table} (user_id, weekly_plan_id) 
                VALUES (:user_id, :weekly_plan_id)";
        
        $this->execute($sql, [
            ':user_id' => $shoppingList->getUserId(),
            ':weekly_plan_id' => $shoppingList->getWeeklyPlanId()
        ]);

        // Return new shopping list ID
        return $this->db->lastInsertId();
    }

    // Get all shopping lists for a user
    public function findByUserId($userId)
    {
        // Get user's shopping lists, newest first
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY generated_date DESC";
        $stmt = $this->execute($sql, [':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find shopping list for a specific weekly plan
    public function findByWeeklyPlanId($weeklyPlanId)
    {
        // Get most recent shopping list for this week plan
        $sql = "SELECT * FROM {$this->table} WHERE weekly_plan_id = :weekly_plan_id ORDER BY generated_date DESC LIMIT 1";
        $stmt = $this->execute($sql, [':weekly_plan_id' => $weeklyPlanId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get shopping list with all its items
    public function getShoppingListWithItems($shoppingListId)
    {
        // Join shopping list with items and ingredients
        $sql = "SELECT sl.*, sli.id as item_id, sli.ingredient_id, sli.quantity, sli.unit, sli.is_checked, 
                i.name as ingredient_name
                FROM {$this->table} sl
                LEFT JOIN shopping_list_items sli ON sl.id = sli.shopping_list_id
                LEFT JOIN ingredients i ON sli.ingredient_id = i.id
                WHERE sl.id = :id ORDER BY i.name";
        
        $stmt = $this->execute($sql, [':id' => $shoppingListId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete old shopping lists
    // $daysOld = how many days old before deleting (default 30 days)
    public function deleteOldLists($userId, $daysOld = 30)
    {
        // Delete lists older than specified days
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id AND generated_date < DATE_SUB(NOW(), INTERVAL :days DAY)";
        return $this->execute($sql, [':user_id' => $userId, ':days' => $daysOld])->rowCount();
    }
}
