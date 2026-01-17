<?php

namespace App\Models;

class Ingredient
{
    private $id;
    private $name;
    private $calories;
    private $protein;
    private $carbs;
    private $fat;
    private $createdAt;
    private $updatedAt;

    public function __construct($name = null, $calories = null)
    {
        $this->name = $name;
        $this->calories = $calories;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCalories()
    {
        return $this->calories;
    }

    public function getProtein()
    {
        return $this->protein;
    }

    public function getCarbs()
    {
        return $this->carbs;
    }

    public function getFat()
    {
        return $this->fat;
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

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setCalories($calories)
    {
        $this->calories = $calories;
        return $this;
    }

    public function setProtein($protein)
    {
        $this->protein = $protein;
        return $this;
    }

    public function setCarbs($carbs)
    {
        $this->carbs = $carbs;
        return $this;
    }

    public function setFat($fat)
    {
        $this->fat = $fat;
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
