<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use App\Models\Category;

class CategoryService
{
    private $categoryRepository;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->findAll();
    }

    public function getCategoryById($categoryId)
    {
        return $this->categoryRepository->findById($categoryId);
    }

    public function createCategory($name, $description = null, $icon = null)
    {
        if (empty($name)) {
            throw new \Exception("Category name is required");
        }

        $category = new Category($name);
        $category->setDescription($description);
        $category->setIcon($icon);

        return $this->categoryRepository->create($category);
    }
}
