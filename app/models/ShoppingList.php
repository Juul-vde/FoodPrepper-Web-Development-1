<?php

namespace App\Models;

class ShoppingList
{
    private $id;
    private $userId;
    private $weeklyPlanId;
    private $generatedDate;
    private $createdAt;
    private $updatedAt;

    public function __construct($userId = null, $weeklyPlanId = null)
    {
        $this->userId = $userId;
        $this->weeklyPlanId = $weeklyPlanId;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getWeeklyPlanId()
    {
        return $this->weeklyPlanId;
    }

    public function getGeneratedDate()
    {
        return $this->generatedDate;
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

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function setWeeklyPlanId($weeklyPlanId)
    {
        $this->weeklyPlanId = $weeklyPlanId;
        return $this;
    }

    public function setGeneratedDate($generatedDate)
    {
        $this->generatedDate = $generatedDate;
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
