<?php
// This controller handles user profile viewing and editing

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\UserService;
use App\Services\CsrfService;

class profilecontroller
{
    // Variables to hold our services
    private $authService;
    private $userService;

    // Constructor runs when controller is created
    public function __construct()
    {
        // Create service objects
        $this->authService = new AuthService();
        $this->userService = new UserService();

        // Check if user is logged in
        if (!$this->authService->isAuthenticated()) {
            // If not, redirect to login
            header('Location: /auth/index');
            exit;
        }
        
        // Create security token for forms
        CsrfService::generateToken();
    }

    // Show the profile page
    public function index()
    {
        // Get current user ID
        $userId = $_SESSION['user_id'];
        
        // Get user information from database
        $user = $this->userService->getUserProfile($userId);

        // Load the profile view page
        include __DIR__ . '/../views/profile/index.php';
    }

    // Show the profile edit page
    public function edit()
    {
        // Get current user ID
        $userId = $_SESSION['user_id'];
        
        // Get user information
        $user = $this->userService->getUserProfile($userId);

        // Load the edit profile page
        include __DIR__ . '/../views/profile/edit.php';
    }

    // Save changes to profile
    public function handleUpdate()
    {
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile/index');
            exit;
        }

        try {
            // Check security token
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!CsrfService::validateToken($csrfToken)) {
                throw new \Exception("Invalid security token. Please try again.");
            }
            
            // Get current user ID
            $userId = $_SESSION['user_id'];
            
            // Get all form data
            $name = $_POST['name'] ?? '';
            $profilePhotoUrl = $_POST['profile_photo'] ?? '';
            $dietaryPreferences = $_POST['dietary_preferences'] ?? null;
            $allergies = $_POST['allergies'] ?? null;

            // Check if name is filled in
            if (empty($name)) {
                throw new \Exception("Name is required");
            }

            // Get current user info (we need the current photo)
            $currentUser = $this->userService->getUserProfile($userId);
            $profilePhoto = $currentUser['profile_photo'] ?? null;

            // Check if user uploaded a photo file
            if (isset($_FILES['profile_photo_file']) && $_FILES['profile_photo_file']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['profile_photo_file'];
                
                // Check if file is too big (max 5MB)
                $maxSize = 5 * 1024 * 1024; // 5MB in bytes
                if ($file['size'] > $maxSize) {
                    $_SESSION['error'] = "Photo too large (max 5MB). Profile updated without changing photo.";
                } else {
                    // Check if file type is allowed
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    if (!in_array($file['type'], $allowedTypes)) {
                        $_SESSION['error'] = "Invalid file type. Only JPG, PNG, GIF allowed.";
                    } else {
                        // Create folder for uploads if it doesn't exist
                        $uploadDir = __DIR__ . '/../public/uploads/profiles/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }
                        
                        // Create unique filename
                        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $filename = 'profile_' . $userId . '_' . time() . '.' . $extension;
                        $uploadPath = $uploadDir . $filename;
                        
                        // Save the uploaded file
                        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                            $profilePhoto = '/uploads/profiles/' . $filename;
                        } else {
                            $_SESSION['error'] = "Failed to upload photo.";
                        }
                    }
                }
            }
            
            // If user entered a photo URL, use that instead
            if (!empty($profilePhotoUrl)) {
                $profilePhoto = $profilePhotoUrl;
            }

            // Update the profile in database
            $this->userService->updateProfile(
                $userId,
                $name,
                $profilePhoto,
                $dietaryPreferences,
                $allergies
            );

            // Update name in session
            $_SESSION['user_name'] = $name;
            
            // Show success message
            $_SESSION['success'] = "Profile updated successfully";
            header('Location: /profile/index');
            exit;
            
        } catch (\Exception $e) {
            // If something went wrong, show error
            $_SESSION['error'] = $e->getMessage();
            header('Location: /profile/edit');
            exit;
        }
    }
}
