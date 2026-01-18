<?php
// Service for managing user profiles
// Handles getting and updating user information

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;

class UserService
{
    // Repository to access user data from database
    private $userRepository;

    // Constructor runs when service is created
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    // Get user profile data
    public function getUserProfile($userId)
    {
        return $this->userRepository->findById($userId);
    }

    // Update user profile information
    public function updateProfile($userId, $name, $profilePhoto = null, $dietaryPreferences = null, $allergies = null)
    {
        // Get current user data from database
        $user = $this->userRepository->findById($userId);
        
        // Check if user exists
        if (!$user) {
            throw new \Exception("User not found");
        }

        // Create user object with new data
        $userModel = new User();
        $userModel->setId($userId);
        $userModel->setName($name);
        $userModel->setEmail($user['email']); // Keep existing email
        
        // Use new photo if provided, otherwise keep old one
        $userModel->setProfilePhoto($profilePhoto ?? $user['profile_photo']);
        
        // Use new preferences if provided, otherwise keep old ones
        $userModel->setDietaryPreferences($dietaryPreferences ?? $user['dietary_preferences']);
        
        // Use new allergies if provided, otherwise keep old ones
        $userModel->setAllergies($allergies ?? $user['allergies']);
        
        // Keep admin status unchanged
        $userModel->setIsAdmin($user['is_admin'] ?? 0);

        // Save updated user to database
        return $this->userRepository->update($userModel);
    }

    // Update just the profile photo
    public function updateProfilePhoto($userId, $photoUrl)
    {
        // Get current user data
        $user = $this->userRepository->findById($userId);
        
        // Check if user exists
        if (!$user) {
            throw new \Exception("User not found");
        }

        // Create user object with photo updated
        $userModel = new User();
        $userModel->setId($userId);
        $userModel->setName($user['name']);
        $userModel->setEmail($user['email']);
        $userModel->setProfilePhoto($photoUrl); // New photo URL
        $userModel->setDietaryPreferences($user['dietary_preferences']);
        $userModel->setAllergies($user['allergies']);

        // Save to database
        return $this->userRepository->update($userModel);
    }

    // Update dietary preferences only
    public function updateDietaryPreferences($userId, $preferences)
    {
        // Get current user data
        $user = $this->userRepository->findById($userId);
        
        // Check if user exists
        if (!$user) {
            throw new \Exception("User not found");
        }

        // Create user object with preferences updated
        $userModel = new User();
        $userModel->setId($userId);
        $userModel->setName($user['name']);
        $userModel->setEmail($user['email']);
        $userModel->setProfilePhoto($user['profile_photo']);
        $userModel->setDietaryPreferences($preferences); // New preferences
        $userModel->setAllergies($user['allergies']);

        // Save to database
        return $this->userRepository->update($userModel);
    }

    // Update allergies only
    public function updateAllergies($userId, $allergies)
    {
        // Get current user data
        $user = $this->userRepository->findById($userId);
        
        // Check if user exists
        if (!$user) {
            throw new \Exception("User not found");
        }

        // Create user object with allergies updated
        $userModel = new User();
        $userModel->setId($userId);
        $userModel->setName($user['name']);
        $userModel->setEmail($user['email']);
        $userModel->setProfilePhoto($user['profile_photo']);
        $userModel->setDietaryPreferences($user['dietary_preferences']);
        $userModel->setAllergies($allergies); // New allergies

        // Save to database
        return $this->userRepository->update($userModel);
    }
}
