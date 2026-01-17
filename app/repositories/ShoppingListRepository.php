<?php

namespace App\Repositories;

use App\Models\ShoppingList;
use PDO;

class ShoppingListRepository extends BaseRepository
{
    protected $table = 'shopping_lists';

    public function create(ShoppingList $shoppingList)
    {
        $sql = "INSERT INTO {$this->table} (user_id, weekly_plan_id) 
                VALUES (:user_id, :weekly_plan_id)";
        
        $this->execute($sql, [
            ':user_id' => $shoppingList->getUserId(),
            ':weekly_plan_id' => $shoppingList->getWeeklyPlanId()
        ]);

        return $this->db->lastInsertId();
    }

    public function findByUserId($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY generated_date DESC";
        $stmt = $this->execute($sql, [':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByWeeklyPlanId($weeklyPlanId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE weekly_plan_id = :weekly_plan_id ORDER BY generated_date DESC LIMIT 1";
        $stmt = $this->execute($sql, [':weekly_plan_id' => $weeklyPlanId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getShoppingListWithItems($shoppingListId)
    {
        $sql = "SELECT sl.*, sli.id as item_id, sli.ingredient_id, sli.quantity, sli.unit, sli.is_checked, 
                i.name as ingredient_name
                FROM {$this->table} sl
                LEFT JOIN shopping_list_items sli ON sl.id = sli.shopping_list_id
                LEFT JOIN ingredients i ON sli.ingredient_id = i.id
                WHERE sl.id = :id ORDER BY i.name";
        $stmt = $this->execute($sql, [':id' => $shoppingListId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteOldLists($userId, $daysOld = 30)
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = :user_id AND generated_date < DATE_SUB(NOW(), INTERVAL :days DAY)";
        return $this->execute($sql, [':user_id' => $userId, ':days' => $daysOld])->rowCount();
    }
}
