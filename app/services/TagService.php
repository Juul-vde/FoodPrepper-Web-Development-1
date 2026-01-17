<?php

namespace App\Services;

use App\Repositories\TagRepository;

class TagService
{
    private $tagRepository;

    public function __construct()
    {
        $this->tagRepository = new TagRepository();
    }

    public function getAllTags()
    {
        return $this->tagRepository->findAll();
    }

    public function getTagById($tagId)
    {
        return $this->tagRepository->findById($tagId);
    }

    public function getTagByName($name)
    {
        return $this->tagRepository->findByName($name);
    }

    public function createTag($name, $description = null)
    {
        if (empty($name)) {
            throw new \Exception("Tag name is required");
        }

        // Check if tag already exists
        $existing = $this->tagRepository->findByName($name);
        if ($existing) {
            return $existing['id'];
        }

        return $this->tagRepository->create($name, $description);
    }

    public function updateTag($tagId, $name, $description = null)
    {
        if (empty($name)) {
            throw new \Exception("Tag name is required");
        }

        return $this->tagRepository->update($tagId, $name, $description);
    }

    public function deleteTag($tagId)
    {
        return $this->tagRepository->delete($tagId);
    }

    public function getMostPopularTags($limit = 10)
    {
        return $this->tagRepository->getMostPopularTags($limit);
    }

    public function getTagsForRecipe($recipeId)
    {
        return $this->tagRepository->getTagsForRecipe($recipeId);
    }

    public function getCommonTags()
    {
        return [
            ['name' => 'Vegetarian', 'description' => 'No meat'],
            ['name' => 'Vegan', 'description' => 'No animal products'],
            ['name' => 'Gluten-Free', 'description' => 'No gluten'],
            ['name' => 'High-Protein', 'description' => 'High protein content'],
            ['name' => 'Low-Carb', 'description' => 'Low carbohydrates'],
            ['name' => 'Quick & Easy', 'description' => 'Preparation under 30 minutes'],
            ['name' => 'Dairy-Free', 'description' => 'No dairy products'],
            ['name' => 'Keto', 'description' => 'Ketogenic diet friendly']
        ];
    }
}
