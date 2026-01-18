<?php
// Service for managing recipe tags
// Tags are labels for recipes (e.g., "Vegetarian", "Quick", "High-Protein")

namespace App\Services;

use App\Repositories\TagRepository;

class TagService
{
    // Repository to access tag data from database
    private $tagRepository;

    // Constructor runs when service is created
    public function __construct()
    {
        $this->tagRepository = new TagRepository();
    }

    // Get all tags
    public function getAllTags()
    {
        return $this->tagRepository->findAll();
    }

    // Get one tag by ID
    public function getTagById($tagId)
    {
        return $this->tagRepository->findById($tagId);
    }

    // Find tag by name
    public function getTagByName($name)
    {
        return $this->tagRepository->findByName($name);
    }

    // Create a new tag
    public function createTag($name, $description = null)
    {
        // Check if name is provided
        if (empty($name)) {
            throw new \Exception("Tag name is required");
        }

        // Check if tag already exists
        $existing = $this->tagRepository->findByName($name);
        if ($existing) {
            // Return existing tag ID instead of creating duplicate
            return $existing['id'];
        }

        // Create new tag and return its ID
        return $this->tagRepository->create($name, $description);
    }

    // Update an existing tag
    public function updateTag($tagId, $name, $description = null)
    {
        // Check if name is provided
        if (empty($name)) {
            throw new \Exception("Tag name is required");
        }

        // Save updated tag to database
        return $this->tagRepository->update($tagId, $name, $description);
    }

    // Delete a tag
    public function deleteTag($tagId)
    {
        return $this->tagRepository->delete($tagId);
    }

    // Get most used tags
    // $limit = how many tags to return (default 10)
    public function getMostPopularTags($limit = 10)
    {
        return $this->tagRepository->getMostPopularTags($limit);
    }

    // Get all tags for a specific recipe
    public function getTagsForRecipe($recipeId)
    {
        return $this->tagRepository->getTagsForRecipe($recipeId);
    }

    // Get list of common tags to suggest to users
    // Returns hardcoded list of popular tag options
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
