<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\WeeklyPlanService;
use App\Services\RecipeService;

class weekplannercontroller
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
        $weeklyPlan = $this->weeklyPlanService->getCurrentWeekPlan($userId);

        if (!$weeklyPlan) {
            $weekStartDate = date('Y-m-d', strtotime('monday this week'));
            $weeklyPlan = new \stdClass();
            $weeklyPlan['id'] = $this->weeklyPlanService->createWeeklyPlan($userId, $weekStartDate, 1);
            $weeklyPlan['week_start_date'] = $weekStartDate;
            $weeklyPlan['number_of_servings'] = 1;
        }

        $mealsData = $this->weeklyPlanService->getWeekPlanWithMeals($weeklyPlan['id']);
        $mealsByDay = $this->organizeMealsByDay($mealsData);
        $recipes = $this->recipeService->getAllRecipes();

        include __DIR__ . '/../views/weekplanner/index.php';
    }

    public function addMeal()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            return;
        }

        try {
            $userId = $_SESSION['user_id'];
            $recipeId = $_POST['recipe_id'] ?? null;
            $dayOfWeek = $_POST['day_of_week'] ?? null;
            $mealType = $_POST['meal_type'] ?? 'lunch';
            $servings = $_POST['servings'] ?? 1;

            if (!$recipeId || !$dayOfWeek) {
                throw new \Exception("Recipe and day are required");
            }

            $weeklyPlan = $this->weeklyPlanService->getCurrentWeekPlan($userId);
            if (!$weeklyPlan) {
                $weekStartDate = date('Y-m-d', strtotime('monday this week'));
                $weeklyPlan = new \stdClass();
                $weeklyPlan['id'] = $this->weeklyPlanService->createWeeklyPlan($userId, $weekStartDate);
            }

            $this->weeklyPlanService->addMealToDay($weeklyPlan['id'], $recipeId, $dayOfWeek, $mealType, $servings);

            $_SESSION['success'] = "Meal added to weekly plan";
            header('Location: /weekplanner/index');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /weekplanner/index');
            exit;
        }
    }

    public function removeMeal()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            return;
        }

        try {
            $itemId = $_POST['item_id'] ?? null;

            if (!$itemId) {
                throw new \Exception("Item ID is required");
            }

            $this->weeklyPlanService->removeMeal($itemId);

            $_SESSION['success'] = "Meal removed from weekly plan";
            header('Location: /weekplanner/index');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /weekplanner/index');
            exit;
        }
    }

    public function updateServings()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            return;
        }

        try {
            $weeklyPlanId = $_POST['weekly_plan_id'] ?? null;
            $numberOfServings = $_POST['number_of_servings'] ?? 1;

            if (!$weeklyPlanId) {
                throw new \Exception("Weekly plan ID is required");
            }

            $this->weeklyPlanService->updateNumberOfServings($weeklyPlanId, $numberOfServings);

            $_SESSION['success'] = "Number of servings updated";
            header('Location: /weekplanner/index');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /weekplanner/index');
            exit;
        }
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
