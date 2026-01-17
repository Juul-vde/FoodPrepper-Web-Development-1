<?php

namespace App\Models;

class Review
{
    private $id;
    private $recipeId;
    private $userId;
    private $rating;
    private $comment;
    private $createdAt;
    private $updatedAt;

    public function __construct($recipeId = null, $userId = null, $rating = null)
    {
        $this->recipeId = $recipeId;
        $this->userId = $userId;
        $this->rating = $rating;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getRecipeId()
    {
        return $this->recipeId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function getComment()
    {
        return $this->comment;
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

    public function setRecipeId($recipeId)
    {
        $this->recipeId = $recipeId;
        return $this;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
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
