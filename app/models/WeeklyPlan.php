<?php

namespace App\Models;

class WeeklyPlan
{
    private $id;
    private $userId;
    private $weekStartDate;
    private $numberOfServings;
    private $createdAt;
    private $updatedAt;

    public function __construct($userId = null, $weekStartDate = null, $numberOfServings = 1)
    {
        $this->userId = $userId;
        $this->weekStartDate = $weekStartDate;
        $this->numberOfServings = $numberOfServings;
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

    public function getWeekStartDate()
    {
        return $this->weekStartDate;
    }

    public function getNumberOfServings()
    {
        return $this->numberOfServings;
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

    public function setWeekStartDate($weekStartDate)
    {
        $this->weekStartDate = $weekStartDate;
        return $this;
    }

    public function setNumberOfServings($numberOfServings)
    {
        $this->numberOfServings = $numberOfServings;
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
