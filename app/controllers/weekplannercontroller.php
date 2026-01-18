<?php
// This controller handles the weekly meal planner

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\WeeklyPlanService;
use App\Services\RecipeService;
use App\Services\TagService;
use App\Services\CategoryService;
use App\Services\CsrfService;

class weekplannercontroller
{
    // Variables to hold our services
    private $authService;
    private $weeklyPlanService;
    private $recipeService;
    private $tagService;
    private $categoryService;

    // Constructor runs when controller is created
    public function __construct()
    {
        // Create service objects
        $this->authService = new AuthService();
        $this->weeklyPlanService = new WeeklyPlanService();
        $this->recipeService = new RecipeService();
        $this->tagService = new TagService();
        $this->categoryService = new CategoryService();

        // Check if user is logged in
        if (!$this->authService->isAuthenticated()) {
            header('Location: /auth/index');
            exit;
        }
        
        // Create security token
        CsrfService::generateToken();
    }

    // Show the weekly planner page
    public function index()
    {
        // Get current user ID
        $userId = $_SESSION['user_id'];
        
        // Get current week's plan
        $weeklyPlan = $this->weeklyPlanService->getCurrentWeekPlan($userId);

        // If no plan exists, create one
        if (!$weeklyPlan) {
            // Get monday of this week
            $weekStartDate = date('Y-m-d', strtotime('monday this week'));
            
            // Create new plan
            $planId = $this->weeklyPlanService->createWeeklyPlan($userId, $weekStartDate, 1);
            
            // Set plan data
            $weeklyPlan = new \stdClass();
            $weeklyPlan['id'] = $planId;
            $weeklyPlan['week_start_date'] = $weekStartDate;
            $weeklyPlan['number_of_servings'] = 1;
        }

        // Get all meals for this week
        $mealsData = $this->weeklyPlanService->getWeekPlanWithMeals($weeklyPlan['id']);
        
        // Remove empty rows (from database LEFT JOIN)
        $meals = array_filter($mealsData, function($meal) {
            return !is_null($meal['recipe_id']);
        });
        $meals = array_values($meals);
        
        // Organize meals by day
        $mealsByDay = $this->organizeMealsByDay($mealsData);
        
        // Get all recipes for adding meals
        $recipes = $this->recipeService->getAllRecipes();

        // Load the weekplanner page
        include __DIR__ . '/../views/weekplanner/index.php';
    }

    // Add a meal to the weekly plan
    public function addMeal()
    {
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            try {
                // Check security token
                $csrfToken = $_POST['csrf_token'] ?? '';
                if (!CsrfService::validateToken($csrfToken)) {
                    throw new \Exception("Invalid security token. Please try again.");
                }
                
                // Get current user ID
                $userId = $_SESSION['user_id'];
                
                // Get form data
                $recipeId = $_POST['recipe_id'] ?? null;
                $dayOfWeek = $_POST['day_of_week'] ?? null;
                $mealType = $_POST['meal_type'] ?? 'lunch';
                $servings = $_POST['servings'] ?? 1;

                // Check required fields
                if (!$recipeId || !$dayOfWeek) {
                    throw new \Exception("Recipe and day are required");
                }

                // Check if day is valid (1-7 for Monday-Sunday)
                if (!is_numeric($dayOfWeek) || $dayOfWeek < 1 || $dayOfWeek > 7) {
                    throw new \Exception("Invalid day of week");
                }

                // Check if servings is valid
                if (!is_numeric($servings) || $servings < 1 || $servings > 20) {
                    throw new \Exception("Servings must be between 1 and 20");
                }

                // Get current weekly plan
                $weeklyPlan = $this->weeklyPlanService->getCurrentWeekPlan($userId);
                
                // Create plan if it doesn't exist
                if (!$weeklyPlan) {
                    $weekStartDate = date('Y-m-d', strtotime('monday this week'));
                    $weeklyPlan = new \stdClass();
                    $weeklyPlan['id'] = $this->weeklyPlanService->createWeeklyPlan($userId, $weekStartDate, 1);
                }

                // Add the meal
                $this->weeklyPlanService->addMealToDay($weeklyPlan['id'], $recipeId, $dayOfWeek, $mealType, $servings);

                // Show success message
                $_SESSION['success'] = "Meal added to weekly plan successfully";
                header('Location: /weekplanner/index');
                exit;
                
            } catch (\Exception $e) {
                // Show error message
                $_SESSION['error'] = $e->getMessage();
                header('Location: /weekplanner/addmeal');
                exit;
            }
            
        } else {
            // Show the form to add a meal
            
            try {
                // Get search filters from URL
                $search = $_GET['search'] ?? '';
                $categoryId = $_GET['category'] ?? null;

                // Start with all recipes
                $recipes = $this->recipeService->getAllRecipes();

                // Filter by category if selected
                if ($categoryId) {
                    $recipes = $this->recipeService->searchByCategory($categoryId);
                }

                // Filter by search text if entered
                if ($search) {
                    $searchLower = strtolower($search);
                    $recipes = array_filter($recipes, function($recipe) use ($searchLower) {
                        $titleMatch = strpos(strtolower($recipe['title']), $searchLower) !== false;
                        $descMatch = strpos(strtolower($recipe['description']), $searchLower) !== false;
                        return $titleMatch || $descMatch;
                    });
                }

                // Get all categories for filter dropdown
                $categories = $this->categoryService->getAllCategories();

                // Load the add meal page
                include __DIR__ . '/../views/weekplanner/addmeal.php';
                
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('Location: /weekplanner/index');
                exit;
            }
        }
    }

    // Remove a meal from the weekly plan
    public function removeMeal()
    {
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            return;
        }

        try {
            // Check security token
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!CsrfService::validateToken($csrfToken)) {
                throw new \Exception("Invalid security token. Please try again.");
            }
            
            // Get item ID from form
            $itemId = $_POST['item_id'] ?? null;

            // Check if ID is provided
            if (!$itemId) {
                throw new \Exception("Item ID is required");
            }

            // Remove the meal
            $this->weeklyPlanService->removeMeal($itemId);

            // Show success message
            $_SESSION['success'] = "Meal removed from weekly plan";
            header('Location: /weekplanner/index');
            exit;
            
        } catch (\Exception $e) {
            // Show error message
            $_SESSION['error'] = $e->getMessage();
            header('Location: /weekplanner/index');
            exit;
        }
    }

    // Show edit form for a meal
    public function edit()
    {
        try {
            // Get meal ID from URL
            $mealId = $_GET['meal_id'] ?? null;
            
            // Check if ID is provided
            if (!$mealId) {
                throw new \Exception("Meal ID is required");
            }

            // Get meal details
            $mealItem = $this->weeklyPlanService->getMealById($mealId);
            
            // Check if meal exists
            if (!$mealItem) {
                throw new \Exception("Meal not found");
            }

            // Get all recipes for dropdown
            $recipes = $this->recipeService->getAllRecipes();

            // Load the edit page
            include __DIR__ . '/../views/weekplanner/edit.php';
            
        } catch (\Exception $e) {
            // Show error message
            $_SESSION['error'] = $e->getMessage();
            header('Location: /weekplanner/index');
            exit;
        }
    }

    // Update a meal in the weekly plan
    public function update()
    {
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            return;
        }

        try {
            // Check security token
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!CsrfService::validateToken($csrfToken)) {
                throw new \Exception("Invalid security token. Please try again.");
            }
            
            // Get form data
            $itemId = $_POST['item_id'] ?? null;
            $recipeId = $_POST['recipe_id'] ?? null;
            $dayOfWeek = $_POST['day_of_week'] ?? null;
            $mealType = $_POST['meal_type'] ?? null;
            $servings = $_POST['servings'] ?? 1;

            // Check required fields
            if (!$itemId || !$recipeId || !$dayOfWeek || !$mealType) {
                throw new \Exception("All fields are required");
            }

            // Check if day is valid
            if (!is_numeric($dayOfWeek) || $dayOfWeek < 1 || $dayOfWeek > 7) {
                throw new \Exception("Invalid day of week");
            }

            // Check if servings is valid
            if (!is_numeric($servings) || $servings < 1 || $servings > 20) {
                throw new \Exception("Servings must be between 1 and 20");
            }

            // Update the meal
            $this->weeklyPlanService->updateMeal($itemId, $recipeId, $dayOfWeek, $mealType, $servings);

            // Show success message
            $_SESSION['success'] = "Meal updated successfully";
            header('Location: /weekplanner/index');
            exit;
            
        } catch (\Exception $e) {
            // Show error message
            $_SESSION['error'] = $e->getMessage();
            header('Location: /weekplanner/index');
            exit;
        }
    }

    // Helper function to organize meals by day
    private function organizeMealsByDay($mealsData)
    {
        // Create empty array
        $organized = [];

        // Loop through each meal
        foreach ($mealsData as $meal) {
            // Get the day number
            $day = $meal['day_of_week'] ?? null;
            
            // Skip if no day
            if ($day === null) {
                continue;
            }

            // Create array for this day if it doesn't exist
            if (!isset($organized[$day])) {
                $organized[$day] = [];
            }

            // Add meal to this day
            $organized[$day][] = $meal;
        }

        return $organized;
    }
}
