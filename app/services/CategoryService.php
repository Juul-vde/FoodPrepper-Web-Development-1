<?php
// Service for managing recipe categories
// Categories group recipes together (e.g., "Breakfast", "Dinner", "Desserts")

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Models\Category;

class CategoryService
{
    // Repository to access category data from database
    private $categoryRepository;

    // Constructor runs when service is created
    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
    }

    // Get all categories
    public function getAllCategories()
    {
        return $this->categoryRepository->findAll();
    }

    // Get one category by ID
    public function getCategoryById($categoryId)
    {
        return $this->categoryRepository->findById($categoryId);
    }

    // Create a new category
    public function createCategory($name, $description = null, $icon = null)
    {
        // Check if name is provided
        if (empty($name)) {
            throw new \Exception("Category name is required");
        }

        // Create category object
        $category = new Category($name);
        $category->setDescription($description);
        $category->setIcon($icon);

        // Save to database and return new ID
        return $this->categoryRepository->create($category);
    }
}
