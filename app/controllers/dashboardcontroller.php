<?php
// This controller shows the main dashboard after login

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\WeeklyPlanService;
use App\Services\RecipeService;

class dashboardcontroller
{
    // Variables to hold our services
    private $authService;
    private $weeklyPlanService;
    private $recipeService;

    // Constructor runs when controller is created
    public function __construct()
    {
        // Create service objects
        $this->authService = new AuthService();
        $this->weeklyPlanService = new WeeklyPlanService();
        $this->recipeService = new RecipeService();

        // Check if user is logged in
        if (!$this->authService->isAuthenticated()) {
            // If not logged in, redirect to login page
            header('Location: /auth/index');
            exit;
        }
    }

    // Show the dashboard
    public function index()
    {
        // Get the current user's ID from session
        $userId = $_SESSION['user_id'];
        
        // Get the current week's meal plan
        $weeklyPlan = $this->weeklyPlanService->getCurrentWeekPlan($userId);
        
        // Check if there is a weekly plan
        if ($weeklyPlan) {
            // Get all meals for this week
            $mealsData = $this->weeklyPlanService->getWeekPlanWithMeals($weeklyPlan['id']);
            
            // Organize meals by day
            $mealsByDay = $this->organizeMealsByDay($mealsData);
        } else {
            // No plan yet, create empty array
            $mealsByDay = [];
        }

        // Get current user information
        $user = $this->authService->getCurrentUser();

        // Load and show the dashboard page
        include __DIR__ . '/../views/dashboard/index.php';
    }

    // Helper function to organize meals by day of week
    private function organizeMealsByDay($mealsData)
    {
        // Create empty array to hold organized meals
        $organized = [];
        
        // Loop through each meal
        foreach ($mealsData as $meal) {
            // Get the day number
            $day = $meal['day_of_week'] ?? null;
            
            // Skip if no day is set
            if ($day === null) {
                continue;
            }

            // Create array for this day if it doesn't exist
            if (!isset($organized[$day])) {
                $organized[$day] = [];
            }

            // Add this meal to the day
            $organized[$day][] = $meal;
        }

        // Return the organized meals
        return $organized;
    }
}
