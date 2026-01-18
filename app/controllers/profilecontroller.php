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
            $profilePhotoUrl = $_POST['profile_photo'] ?? '';
            $dietaryPreferences = $_POST['dietary_preferences'] ?? null;
            $allergies = $_POST['allergies'] ?? null;

            if (empty($name)) {
                throw new \Exception("Name is required");
            }

            // Get current user data to preserve photo if not updating
            $currentUser = $this->userService->getUserProfile($userId);
            $profilePhoto = $currentUser['profile_photo'] ?? null;

            // Handle file upload if present
            if (isset($_FILES['profile_photo_file']) && $_FILES['profile_photo_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['profile_photo_file'];
                
                // Validate file size (5MB max)
                if ($file['size'] > 5 * 1024 * 1024) {
                    $_SESSION['error'] = "Photo too large (max 5MB). Profile updated without changing photo.";
                    // Continue without updating photo - will use existing photo
                } else {
                    // Validate file type
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    if (!in_array($file['type'], $allowedTypes)) {
                        $_SESSION['error'] = "Invalid file type. Only JPG, PNG, GIF allowed. Profile updated without changing photo.";
                        // Continue without updating photo
                    } else {
                        // Create uploads directory if it doesn't exist
                        $uploadDir = __DIR__ . '/../public/uploads/profiles/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        
                        // Generate unique filename
                        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
                        $uploadPath = $uploadDir . $filename;
                        
                        // Move uploaded file
                        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                            $profilePhoto = '/uploads/profiles/' . $filename;
                        } else {
                            $_SESSION['error'] = "Failed to upload photo. Profile updated without changing photo.";
                        }
                    }
                }
            } elseif (isset($_FILES['profile_photo_file']) && $_FILES['profile_photo_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Handle other upload errors
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'Photo exceeds server upload limit.',
                    UPLOAD_ERR_FORM_SIZE => 'Photo exceeds form upload limit.',
                    UPLOAD_ERR_PARTIAL => 'Photo was only partially uploaded.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                    UPLOAD_ERR_EXTENSION => 'Upload blocked by extension.',
                ];
                $errorCode = $_FILES['profile_photo_file']['error'];
                $_SESSION['error'] = ($errorMessages[$errorCode] ?? 'Unknown upload error.') . ' Profile updated without changing photo.';
            }
            
            // Handle URL-based photo update
            if (!empty($profilePhotoUrl)) {
                // If URL provided, use that instead
                $profilePhoto = $profilePhotoUrl;
            }
            // Otherwise keep existing photo

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
