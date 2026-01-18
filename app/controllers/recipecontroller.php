<?php
// This controller handles viewing recipes (admin can also create/edit)

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\RecipeService;
use App\Services\TagService;
use App\Services\CategoryService;
use App\Services\WeeklyPlanService;

class recipecontroller
{
    // Variables to hold our services
    private $authService;
    private $recipeService;
    private $tagService;
    private $categoryService;
    private $weeklyPlanService;

    // Constructor runs when controller is created
    public function __construct()
    {
        // Create service objects
        $this->authService = new AuthService();
        $this->recipeService = new RecipeService();
        $this->tagService = new TagService();
        $this->categoryService = new CategoryService();
        $this->weeklyPlanService = new WeeklyPlanService();

        // Check if user is logged in
        if (!$this->authService->isAuthenticated()) {
            header('Location: /auth/index');
            exit;
        }
    }

    // Show all recipes with filters
    public function index()
    {
        // Get search and category from URL
        $searchQuery = $_GET['q'] ?? '';
        $categoryId = $_GET['category'] ?? null;

        // Start with all recipes
        $recipes = $this->recipeService->getAllRecipes();

        // Filter by category if selected
        if ($categoryId) {
            $recipes = $this->recipeService->searchByCategory($categoryId);
        }

        // Filter by search text if entered
        if ($searchQuery) {
            $searchLower = strtolower($searchQuery);
            $recipes = array_filter($recipes, function($recipe) use ($searchLower) {
                $titleMatch = strpos(strtolower($recipe['title']), $searchLower) !== false;
                $descMatch = strpos(strtolower($recipe['description']), $searchLower) !== false;
                return $titleMatch || $descMatch;
            });
        }

        // Get all categories for dropdown filter
        $categories = $this->categoryService->getAllCategories();

        // Load the recipes page
        include __DIR__ . '/../views/recipes/index.php';
    }

    // Show one recipe with details
    public function view()
    {
        // Get recipe ID from URL
        $recipeId = $_GET['id'] ?? null;

        // Check if ID is provided
        if (!$recipeId) {
            header('Location: /recipe/index');
            exit;
        }

        // Get recipe with ingredients
        $recipe = $this->recipeService->getRecipeWithIngredients($recipeId);

        // Check if recipe exists
        if (!$recipe) {
            $_SESSION['error'] = "Recipe not found";
            header('Location: /recipe/index');
            exit;
        }

        // Check if recipe is in current week's plan
        $recipeInWeekplan = null;
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $weeklyPlan = $this->weeklyPlanService->getCurrentWeekPlan($userId);
            
            if ($weeklyPlan) {
                $mealsData = $this->weeklyPlanService->getWeekPlanWithMeals($weeklyPlan['id']);
                
                // Find meals with this recipe
                $recipeInWeekplan = array_filter($mealsData, function($meal) use ($recipeId) {
                    return $meal['recipe_id'] == $recipeId;
                });
            }
        }
        
        // Load the recipe view page
        include __DIR__ . '/../views/recipes/view.php';
    }

    // Search recipes (additional search page)
    public function search()
    {
        // Get search parameters
        $keyword = $_GET['q'] ?? '';
        $tagId = $_GET['tag'] ?? null;

        // Search by tag or keyword
        if ($tagId) {
            $recipes = $this->recipeService->searchByTag($tagId);
        } elseif ($keyword) {
            $recipes = $this->recipeService->searchRecipes($keyword);
        } else {
            $recipes = $this->recipeService->getAllRecipes();
        }

        // Get all tags for filter
        $tags = $this->tagService->getAllTags();

        // Load search page
        include __DIR__ . '/../views/recipes/search.php';
    }
}
