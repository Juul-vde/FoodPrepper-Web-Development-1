<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;

class UserService
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function getUserProfile($userId)
    {
        return $this->userRepository->findById($userId);
    }

    public function updateProfile($userId, $name, $profilePhoto = null, $dietaryPreferences = null, $allergies = null)
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new \Exception("User not found");
        }

        $userModel = new User();
        $userModel->setId($userId);
        $userModel->setName($name);
        $userModel->setProfilePhoto($profilePhoto ?? $user['profile_photo']);
        $userModel->setDietaryPreferences($dietaryPreferences ?? $user['dietary_preferences']);
        $userModel->setAllergies($allergies ?? $user['allergies']);

        return $this->userRepository->update($userModel);
    }

    public function updateProfilePhoto($userId, $photoUrl)
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new \Exception("User not found");
        }

        $userModel = new User();
        $userModel->setId($userId);
        $userModel->setName($user['name']);
        $userModel->setEmail($user['email']);
        $userModel->setProfilePhoto($photoUrl);
        $userModel->setDietaryPreferences($user['dietary_preferences']);
        $userModel->setAllergies($user['allergies']);

        return $this->userRepository->update($userModel);
    }

    public function updateDietaryPreferences($userId, $preferences)
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new \Exception("User not found");
        }

        $userModel = new User();
        $userModel->setId($userId);
        $userModel->setName($user['name']);
        $userModel->setEmail($user['email']);
        $userModel->setProfilePhoto($user['profile_photo']);
        $userModel->setDietaryPreferences($preferences);
        $userModel->setAllergies($user['allergies']);

        return $this->userRepository->update($userModel);
    }

    public function updateAllergies($userId, $allergies)
    {
        $user = $this->userRepository->findById($userId);
        if (!$user) {
            throw new \Exception("User not found");
        }

        $userModel = new User();
        $userModel->setId($userId);
        $userModel->setName($user['name']);
        $userModel->setEmail($user['email']);
        $userModel->setProfilePhoto($user['profile_photo']);
        $userModel->setDietaryPreferences($user['dietary_preferences']);
        $userModel->setAllergies($allergies);

        return $this->userRepository->update($userModel);
    }
}
