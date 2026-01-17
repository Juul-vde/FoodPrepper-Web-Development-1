<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\WeeklyPlanService;
use App\Services\RecipeService;

class dashboardcontroller
{
    private $authService;
    private $weeklyPlanService;
    private $recipeService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->weeklyPlanService = new WeeklyPlanService();
        $this->recipeService = new RecipeService();

        if (!$this->authService->isAuthenticated()) {
            header('Location: /auth/index');
            exit;
        }
    }

    public function index()
    {
        $userId = $_SESSION['user_id'];
        
        // Get current week plan
        $weeklyPlan = $this->weeklyPlanService->getCurrentWeekPlan($userId);
        
        if ($weeklyPlan) {
            $mealsData = $this->weeklyPlanService->getWeekPlanWithMeals($weeklyPlan['id']);
            $mealsByDay = $this->organizeMealsByDay($mealsData);
        } else {
            $mealsByDay = [];
        }

        $user = $this->authService->getCurrentUser();

        include __DIR__ . '/../views/dashboard/index.php';
    }

    private function organizeMealsByDay($mealsData)
    {
        $organized = [];
        
        foreach ($mealsData as $meal) {
            $day = $meal['day_of_week'] ?? null;
            if ($day === null) continue;

            if (!isset($organized[$day])) {
                $organized[$day] = [];
            }

            $organized[$day][] = $meal;
        }

        return $organized;
    }
}
