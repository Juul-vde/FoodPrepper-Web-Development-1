<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\RecipeService;
use App\Services\TagService;

class recipecontroller
{
    private $authService;
    private $recipeService;
    private $tagService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->recipeService = new RecipeService();
        $this->tagService = new TagService();

        if (!$this->authService->isAuthenticated()) {
            header('Location: /auth/index');
            exit;
        }
    }

    public function index()
    {
        $recipes = $this->recipeService->getAllRecipes();
        $tags = $this->tagService->getAllTags();

        include __DIR__ . '/../views/recipes/index.php';
    }

    public function view()
    {
        $recipeId = $_GET['id'] ?? null;

        if (!$recipeId) {
            header('Location: /recipe/index');
            exit;
        }

        $recipe = $this->recipeService->getRecipeWithIngredients($recipeId);

        if (!$recipe) {
            $_SESSION['error'] = "Recipe not found";
            header('Location: /recipe/index');
            exit;
        }

        include __DIR__ . '/../views/recipes/view.php';
    }

    public function search()
    {
        $keyword = $_GET['q'] ?? '';
        $tagId = $_GET['tag'] ?? null;

        if ($tagId) {
            $recipes = $this->recipeService->searchByTag($tagId);
        } elseif ($keyword) {
            $recipes = $this->recipeService->searchRecipes($keyword);
        } else {
            $recipes = $this->recipeService->getAllRecipes();
        }

        $tags = $this->tagService->getAllTags();

        include __DIR__ . '/../views/recipes/search.php';
    }

    public function create()
    {
        $tags = $this->tagService->getAllTags();
        $commonTags = $this->tagService->getCommonTags();

        include __DIR__ . '/../views/recipes/create.php';
    }

    public function handleCreate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /recipe/create');
            exit;
        }

        try {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $instructions = $_POST['instructions'] ?? '';
            $imageUrl = $_POST['image_url'] ?? '';
            $prepTime = $_POST['prep_time'] ?? 0;
            $cookTime = $_POST['cook_time'] ?? 0;
            $servings = $_POST['servings'] ?? 1;
            $difficulty = $_POST['difficulty'] ?? 'medium';
            $categoryId = $_POST['category_id'] ?? null;
            $tags = $_POST['tags'] ?? [];

            $recipeId = $this->recipeService->createRecipe(
                $title,
                $description,
                $instructions,
                $imageUrl,
                $prepTime,
                $cookTime,
                $servings,
                $difficulty,
                $categoryId,
                $tags
            );

            $_SESSION['success'] = "Recipe created successfully";
            header('Location: /recipe/view?id=' . $recipeId);
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /recipe/create');
            exit;
        }
    }

    public function edit()
    {
        $recipeId = $_GET['id'] ?? null;

        if (!$recipeId) {
            header('Location: /recipe/index');
            exit;
        }

        $recipe = $this->recipeService->getRecipeWithIngredients($recipeId);

        if (!$recipe) {
            $_SESSION['error'] = "Recipe not found";
            header('Location: /recipe/index');
            exit;
        }

        $tags = $this->tagService->getAllTags();
        $commonTags = $this->tagService->getCommonTags();

        include __DIR__ . '/../views/recipes/edit.php';
    }

    public function handleEdit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /recipe/index');
            exit;
        }

        try {
            $recipeId = $_POST['recipe_id'] ?? null;
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $instructions = $_POST['instructions'] ?? '';
            $imageUrl = $_POST['image_url'] ?? '';
            $prepTime = $_POST['prep_time'] ?? 0;
            $cookTime = $_POST['cook_time'] ?? 0;
            $servings = $_POST['servings'] ?? 1;
            $difficulty = $_POST['difficulty'] ?? 'medium';
            $categoryId = $_POST['category_id'] ?? null;

            if (!$recipeId) {
                throw new \Exception("Recipe ID is required");
            }

            $this->recipeService->updateRecipe(
                $recipeId,
                $title,
                $description,
                $instructions,
                $imageUrl,
                $prepTime,
                $cookTime,
                $servings,
                $difficulty,
                $categoryId
            );

            $_SESSION['success'] = "Recipe updated successfully";
            header('Location: /recipe/view?id=' . $recipeId);
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /recipe/index');
            exit;
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            return;
        }

        try {
            $recipeId = $_POST['recipe_id'] ?? null;

            if (!$recipeId) {
                throw new \Exception("Recipe ID is required");
            }

            $this->recipeService->deleteRecipe($recipeId);

            $_SESSION['success'] = "Recipe deleted successfully";
            header('Location: /recipe/index');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /recipe/index');
            exit;
        }
    }
}
