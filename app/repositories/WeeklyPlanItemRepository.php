<?php
// Repository for weekly plan meal item database operations

namespace App\Repositories;

use App\Models\WeeklyPlanItem;
use PDO;

class WeeklyPlanItemRepository extends BaseRepository
{
    // Set table name
    protected $table = 'weekly_plan_items';

    // Create new meal item in weekly plan
    public function create(WeeklyPlanItem $item)
    {
        // Insert meal with day, meal type, recipe, and servings
        $sql = "INSERT INTO {$this->table} (weekly_plan_id, recipe_id, day_of_week, meal_type, servings) 
                VALUES (:weekly_plan_id, :recipe_id, :day_of_week, :meal_type, :servings)";
        
        $this->execute($sql, [
            ':weekly_plan_id' => $item->getWeeklyPlanId(),
            ':recipe_id' => $item->getRecipeId(),
            ':day_of_week' => $item->getDayOfWeek(),
            ':meal_type' => $item->getMealType(),
            ':servings' => $item->getServings() ?? 1 // Default to 1 serving if not provided
        ]);

        // Return new item ID
        return $this->db->lastInsertId();
    }

    // Update existing meal item
    public function update(WeeklyPlanItem $item)
    {
        // Update day, meal type, and servings
        $sql = "UPDATE {$this->table} SET day_of_week = :day_of_week, meal_type = :meal_type, servings = :servings WHERE id = :id";
        
        // Return true if rows were changed
        return $this->execute($sql, [
            ':id' => $item->getId(),
            ':day_of_week' => $item->getDayOfWeek(),
            ':meal_type' => $item->getMealType(),
            ':servings' => $item->getServings()
        ])->rowCount() > 0;
    }

    // Get all meals for a weekly plan
    public function findByWeeklyPlanId($weeklyPlanId)
    {
        // Join with recipes to get recipe details
        $sql = "SELECT wpi.*, r.title as recipe_title, r.image_url, r.prep_time, r.cook_time 
                FROM {$this->table} wpi
                LEFT JOIN recipes r ON wpi.recipe_id = r.id
                WHERE wpi.weekly_plan_id = :weekly_plan_id ORDER BY wpi.day_of_week, wpi.meal_type";
        
        $stmt = $this->execute($sql, [':weekly_plan_id' => $weeklyPlanId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get meals for a specific day of the week
    public function findByWeeklyPlanAndDay($weeklyPlanId, $dayOfWeek)
    {
        // Get all meals for this plan and day
        $sql = "SELECT wpi.*, r.title as recipe_title, r.image_url, r.prep_time, r.cook_time 
                FROM {$this->table} wpi
                LEFT JOIN recipes r ON wpi.recipe_id = r.id
                WHERE wpi.weekly_plan_id = :weekly_plan_id AND wpi.day_of_week = :day_of_week 
                ORDER BY wpi.meal_type";
        
        $stmt = $this->execute($sql, [':weekly_plan_id' => $weeklyPlanId, ':day_of_week' => $dayOfWeek]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete all meals for a weekly plan
    public function deleteByWeeklyPlanId($weeklyPlanId)
    {
        $sql = "DELETE FROM {$this->table} WHERE weekly_plan_id = :weekly_plan_id";
        return $this->execute($sql, [':weekly_plan_id' => $weeklyPlanId])->rowCount();
    }
}
