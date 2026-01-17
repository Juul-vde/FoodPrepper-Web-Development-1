<?php

namespace App\Models;

class Recipe
{
    private $id;
    private $title;
    private $description;
    private $instructions;
    private $imageUrl;
    private $prepTime;
    private $cookTime;
    private $servings;
    private $difficulty;
    private $category;
    private $createdAt;
    private $updatedAt;

    public function __construct($title = null, $description = null)
    {
        $this->title = $title;
        $this->description = $description;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getInstructions()
    {
        return $this->instructions;
    }

    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function getPrepTime()
    {
        return $this->prepTime;
    }

    public function getCookTime()
    {
        return $this->cookTime;
    }

    public function getServings()
    {
        return $this->servings;
    }

    public function getDifficulty()
    {
        return $this->difficulty;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function setInstructions($instructions)
    {
        $this->instructions = $instructions;
        return $this;
    }

    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function setPrepTime($prepTime)
    {
        $this->prepTime = $prepTime;
        return $this;
    }

    public function setCookTime($cookTime)
    {
        $this->cookTime = $cookTime;
        return $this;
    }

    public function setServings($servings)
    {
        $this->servings = $servings;
        return $this;
    }

    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;
        return $this;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
