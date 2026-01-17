<?php

namespace App\Repositories;

use App\Models\WeeklyPlan;
use PDO;

class WeeklyPlanRepository extends BaseRepository
{
    protected $table = 'weekly_plans';

    public function create(WeeklyPlan $weeklyPlan)
    {
        $sql = "INSERT INTO {$this->table} (user_id, week_start_date, number_of_servings) 
                VALUES (:user_id, :week_start_date, :number_of_servings)";
        
        $this->execute($sql, [
            ':user_id' => $weeklyPlan->getUserId(),
            ':week_start_date' => $weeklyPlan->getWeekStartDate(),
            ':number_of_servings' => $weeklyPlan->getNumberOfServings()
        ]);

        return $this->db->lastInsertId();
    }

    public function update(WeeklyPlan $weeklyPlan)
    {
        $sql = "UPDATE {$this->table} SET number_of_servings = :number_of_servings WHERE id = :id";
        
        return $this->execute($sql, [
            ':id' => $weeklyPlan->getId(),
            ':number_of_servings' => $weeklyPlan->getNumberOfServings()
        ])->rowCount() > 0;
    }

    public function findByUserId($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY week_start_date DESC";
        $stmt = $this->execute($sql, [':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findCurrentWeekByUser($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id AND week_start_date <= CURDATE() AND DATE_ADD(week_start_date, INTERVAL 6 DAY) >= CURDATE()";
        $stmt = $this->execute($sql, [':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByUserAndDate($userId, $weekStartDate)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id AND week_start_date = :week_start_date";
        $stmt = $this->execute($sql, [':user_id' => $userId, ':week_start_date' => $weekStartDate]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getWeeklyPlanWithMeals($weeklyPlanId)
    {
        $sql = "SELECT wp.*, wpi.id as item_id, wpi.recipe_id, wpi.day_of_week, wpi.meal_type, wpi.servings, 
                r.title as recipe_title, r.image_url, r.prep_time, r.cook_time
                FROM {$this->table} wp
                LEFT JOIN weekly_plan_items wpi ON wp.id = wpi.weekly_plan_id
                LEFT JOIN recipes r ON wpi.recipe_id = r.id
                WHERE wp.id = :id ORDER BY wpi.day_of_week, wpi.meal_type";
        $stmt = $this->execute($sql, [':id' => $weeklyPlanId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
