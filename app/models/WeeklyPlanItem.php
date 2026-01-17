<?php

namespace App\Models;

class WeeklyPlanItem
{
    private $id;
    private $weeklyPlanId;
    private $recipeId;
    private $dayOfWeek;
    private $mealType;
    private $servings;
    private $createdAt;
    private $updatedAt;

    public function __construct($weeklyPlanId = null, $recipeId = null, $dayOfWeek = null)
    {
        $this->weeklyPlanId = $weeklyPlanId;
        $this->recipeId = $recipeId;
        $this->dayOfWeek = $dayOfWeek;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getWeeklyPlanId()
    {
        return $this->weeklyPlanId;
    }

    public function getRecipeId()
    {
        return $this->recipeId;
    }

    public function getDayOfWeek()
    {
        return $this->dayOfWeek;
    }

    public function getMealType()
    {
        return $this->mealType;
    }

    public function getServings()
    {
        return $this->servings;
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

    public function setWeeklyPlanId($weeklyPlanId)
    {
        $this->weeklyPlanId = $weeklyPlanId;
        return $this;
    }

    public function setRecipeId($recipeId)
    {
        $this->recipeId = $recipeId;
        return $this;
    }

    public function setDayOfWeek($dayOfWeek)
    {
        $this->dayOfWeek = $dayOfWeek;
        return $this;
    }

    public function setMealType($mealType)
    {
        $this->mealType = $mealType;
        return $this;
    }

    public function setServings($servings)
    {
        $this->servings = $servings;
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
