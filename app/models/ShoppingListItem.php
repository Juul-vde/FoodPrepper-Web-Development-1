<?php

namespace App\Models;

class ShoppingListItem
{
    private $id;
    private $shoppingListId;
    private $ingredientId;
    private $quantity;
    private $unit;
    private $isChecked;
    private $createdAt;
    private $updatedAt;

    public function __construct($shoppingListId = null, $ingredientId = null, $quantity = null, $unit = null)
    {
        $this->shoppingListId = $shoppingListId;
        $this->ingredientId = $ingredientId;
        $this->quantity = $quantity;
        $this->unit = $unit;
        $this->isChecked = false;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getShoppingListId()
    {
        return $this->shoppingListId;
    }

    public function getIngredientId()
    {
        return $this->ingredientId;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public function getIsChecked()
    {
        return $this->isChecked;
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

    public function setShoppingListId($shoppingListId)
    {
        $this->shoppingListId = $shoppingListId;
        return $this;
    }

    public function setIngredientId($ingredientId)
    {
        $this->ingredientId = $ingredientId;
        return $this;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function setUnit($unit)
    {
        $this->unit = $unit;
        return $this;
    }

    public function setIsChecked($isChecked)
    {
        $this->isChecked = $isChecked;
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
