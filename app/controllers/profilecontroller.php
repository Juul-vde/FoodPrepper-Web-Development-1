<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\UserService;

class profilecontroller
{
    private $authService;
    private $userService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->userService = new UserService();

        if (!$this->authService->isAuthenticated()) {
            header('Location: /auth/index');
            exit;
        }
    }

    public function index()
    {
        $userId = $_SESSION['user_id'];
        $user = $this->userService->getUserProfile($userId);

        include __DIR__ . '/../views/profile/index.php';
    }

    public function edit()
    {
        $userId = $_SESSION['user_id'];
        $user = $this->userService->getUserProfile($userId);

        include __DIR__ . '/../views/profile/edit.php';
    }

    public function handleUpdate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile/index');
            exit;
        }

        try {
            $userId = $_SESSION['user_id'];
            $name = $_POST['name'] ?? '';
            $profilePhoto = $_POST['profile_photo'] ?? null;
            $dietaryPreferences = $_POST['dietary_preferences'] ?? null;
            $allergies = $_POST['allergies'] ?? null;

            if (empty($name)) {
                throw new \Exception("Name is required");
            }

            $this->userService->updateProfile(
                $userId,
                $name,
                $profilePhoto,
                $dietaryPreferences,
                $allergies
            );

            $_SESSION['user_name'] = $name;
            $_SESSION['success'] = "Profile updated successfully";
            header('Location: /profile/index');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /profile/edit');
            exit;
        }
    }

    public function updatePhoto()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            return;
        }

        try {
            $userId = $_SESSION['user_id'];
            $photoUrl = $_POST['photo_url'] ?? null;

            if (!$photoUrl) {
                throw new \Exception("Photo URL is required");
            }

            $this->userService->updateProfilePhoto($userId, $photoUrl);

            $_SESSION['success'] = "Profile photo updated";
            header('Location: /profile/index');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /profile/index');
            exit;
        }
    }

    public function updatePreferences()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            return;
        }

        try {
            $userId = $_SESSION['user_id'];
            $preferences = $_POST['dietary_preferences'] ?? null;

            $this->userService->updateDietaryPreferences($userId, $preferences);

            $_SESSION['success'] = "Dietary preferences updated";
            header('Location: /profile/index');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /profile/index');
            exit;
        }
    }

    public function updateAllergies()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            return;
        }

        try {
            $userId = $_SESSION['user_id'];
            $allergies = $_POST['allergies'] ?? null;

            $this->userService->updateAllergies($userId, $allergies);

            $_SESSION['success'] = "Allergies updated";
            header('Location: /profile/index');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /profile/index');
            exit;
        }
    }
}
