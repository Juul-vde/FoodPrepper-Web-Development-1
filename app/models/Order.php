<?php

namespace App\Models;

class Order
{
    private $id;
    private $userId;
    private $recipeId;
    private $quantity;
    private $totalPrice;
    private $status;
    private $orderDate;
    private $deliveryDate;
    private $notes;
    private $createdAt;
    private $updatedAt;

    public function __construct($userId = null, $recipeId = null, $quantity = null)
    {
        $this->userId = $userId;
        $this->recipeId = $recipeId;
        $this->quantity = $quantity;
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

    public function getRecipeId()
    {
        return $this->recipeId;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getOrderDate()
    {
        return $this->orderDate;
    }

    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    public function getNotes()
    {
        return $this->notes;
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

    public function setRecipeId($recipeId)
    {
        $this->recipeId = $recipeId;
        return $this;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function setOrderDate($orderDate)
    {
        $this->orderDate = $orderDate;
        return $this;
    }

    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
        return $this;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
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
