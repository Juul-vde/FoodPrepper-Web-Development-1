<?php

namespace App\Models;

class User
{
    private $id;
    private $name;
    private $email;
    private $password;
    private $profilePhoto;
    private $dietaryPreferences;
    private $allergies;
    private $createdAt;
    private $updatedAt;

    public function __construct($name = null, $email = null, $password = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
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

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getProfilePhoto()
    {
        return $this->profilePhoto;
    }

    public function getDietaryPreferences()
    {
        return $this->dietaryPreferences;
    }

    public function getAllergies()
    {
        return $this->allergies;
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

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    public function setProfilePhoto($profilePhoto)
    {
        $this->profilePhoto = $profilePhoto;
        return $this;
    }

    public function setDietaryPreferences($dietaryPreferences)
    {
        $this->dietaryPreferences = $dietaryPreferences;
        return $this;
    }

    public function setAllergies($allergies)
    {
        $this->allergies = $allergies;
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
