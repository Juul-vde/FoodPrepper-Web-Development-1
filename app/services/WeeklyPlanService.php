<?php
// Service for managing weekly meal plans
// Handles creating meal plans and adding recipes to specific days
// Plans are organized by week (Monday to Sunday) and meal types

namespace App\Services;

use App\Repositories\WeeklyPlanRepository;
use App\Repositories\WeeklyPlanItemRepository;
use App\Models\WeeklyPlan;
use App\Models\WeeklyPlanItem;

class WeeklyPlanService
{
    // Repositories to access data from database
    private $weeklyPlanRepository;
    private $weeklyPlanItemRepository;

    // Constructor runs when service is created
    public function __construct()
    {
        $this->weeklyPlanRepository = new WeeklyPlanRepository();
        $this->weeklyPlanItemRepository = new WeeklyPlanItemRepository();
    }

    // Get current week's meal plan for a user
    // This looks for a plan for the current week (Monday to Sunday)
    public function getCurrentWeekPlan($userId)
    {
        return $this->weeklyPlanRepository->findCurrentWeekByUser($userId);
    }

    // Get meal plan for a specific week
    // $weekStartDate should be a Monday in format 'YYYY-MM-DD'
    public function getWeekPlanByDate($userId, $weekStartDate)
    {
        return $this->weeklyPlanRepository->findByUserAndDate($userId, $weekStartDate);
    }

    // Get weekly plan with all its meals
    // This includes all breakfast, lunch, dinner, snacks for the week
    public function getWeekPlanWithMeals($weeklyPlanId)
    {
        return $this->weeklyPlanRepository->getWeeklyPlanWithMeals($weeklyPlanId);
    }

    // Get all weekly plans for a user
    // Returns list of all past and current meal plans
    public function getUserWeekPlans($userId)
    {
        return $this->weeklyPlanRepository->findByUserId($userId);
    }

    // Create a new weekly meal plan
    // $weekStartDate should be a Monday in format 'YYYY-MM-DD'
    // $numberOfServings = how many people are eating (default 1)
    public function createWeeklyPlan($userId, $weekStartDate, $numberOfServings = 1)
    {
        // Check if plan for this week already exists
        // We don't want duplicate plans for same week
        $existingPlan = $this->getWeekPlanByDate($userId, $weekStartDate);
        if ($existingPlan) {
            // Return ID of existing plan instead of creating new one
            return $existingPlan['id'];
        }

        // Create new weekly plan
        $weeklyPlan = new WeeklyPlan($userId, $weekStartDate, $numberOfServings);
        return $this->weeklyPlanRepository->create($weeklyPlan);
    }

    // Update how many people are eating
    // This affects the shopping list quantities
    public function updateNumberOfServings($weeklyPlanId, $numberOfServings)
    {
        $weeklyPlan = new WeeklyPlan();
        $weeklyPlan->setId($weeklyPlanId);
        $weeklyPlan->setNumberOfServings($numberOfServings);

        return $this->weeklyPlanRepository->update($weeklyPlan);
    }

    // Add a meal to a specific day in the week
    // $dayOfWeek: 1=Monday, 2=Tuesday, 3=Wednesday, 4=Thursday, 5=Friday, 6=Saturday, 7=Sunday
    // $mealType: 'breakfast', 'lunch', 'dinner', or 'snack'
    // $servings: how many people are eating this meal (default 1)
    public function addMealToDay($weeklyPlanId, $recipeId, $dayOfWeek, $mealType = 'lunch', $servings = 1)
    {
        // Check if day is valid (must be 1-7)
        if ($dayOfWeek < 1 || $dayOfWeek > 7) {
            throw new \Exception("Day of week must be between 1 and 7");
        }

        // Check if meal type is valid
        // Only allow these four types
        if (!in_array($mealType, ['breakfast', 'lunch', 'dinner', 'snack'])) {
            throw new \Exception("Invalid meal type");
        }

        // Create weekly plan item (represents one meal)
        $item = new WeeklyPlanItem($weeklyPlanId, $recipeId, $dayOfWeek);
        $item->setMealType($mealType);
        $item->setServings($servings);

        // Save to database
        return $this->weeklyPlanItemRepository->create($item);
    }

    // Update an existing meal
    // Can change the day, meal type, or number of servings
    public function updateMeal($itemId, $dayOfWeek, $mealType, $servings)
    {
        // Create item with new values
        $item = new WeeklyPlanItem();
        $item->setId($itemId);
        $item->setDayOfWeek($dayOfWeek);
        $item->setMealType($mealType);
        $item->setServings($servings);

        return $this->weeklyPlanItemRepository->update($item);
    }

    // Remove a meal from the weekly plan
    public function removeMeal($itemId)
    {
        return $this->weeklyPlanItemRepository->delete($itemId);
    }

    // Get all meals for a specific day
    // For example, get all meals (breakfast, lunch, dinner, snack) for Monday
    public function getMealsForDay($weeklyPlanId, $dayOfWeek)
    {
        return $this->weeklyPlanItemRepository->findByWeeklyPlanAndDay($weeklyPlanId, $dayOfWeek);
    }

    // Get all meals in the entire week
    // Returns all meals from Monday to Sunday
    public function getAllMealsInPlan($weeklyPlanId)
    {
        return $this->weeklyPlanItemRepository->findByWeeklyPlanId($weeklyPlanId);
    }

    // Convert day number to name
    // 1 = Monday, 2 = Tuesday, 3 = Wednesday, etc.
    public function getDayName($dayOfWeek)
    {
        // Array mapping numbers to day names
        $days = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday'
        ];
        
        // Return day name, or 'Unknown' if invalid day number
        return $days[$dayOfWeek] ?? 'Unknown';
    }
}
