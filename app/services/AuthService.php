<?php
// Service for user authentication (login, register, logout)
// Handles checking if users are logged in and if they are admins

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;

class AuthService
{
    // Repository to access user data from database
    private $userRepository;

    // Constructor runs when service is created
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    // Register a new user
    public function register($name, $email, $password)
    {
        // Check if all required fields are filled
        if (empty($name) || empty($email) || empty($password)) {
            throw new \Exception("All fields are required");
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email format");
        }

        // Check password length (minimum 6 characters)
        if (strlen($password) < 6) {
            throw new \Exception("Password must be at least 6 characters");
        }

        // Check if email is already registered
        if ($this->userRepository->findByEmail($email)) {
            throw new \Exception("Email already registered");
        }

        // Create new user object
        $user = new User($name, $email, $password);
        
        // Save user to database
        $userId = $this->userRepository->create($user);

        // If user was created successfully, log them in
        if ($userId) {
            // Store user info in session (keeps them logged in)
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['is_admin'] = 0; // New users are not admins
            return true;
        }

        return false;
    }

    // Log in an existing user
    public function login($email, $password)
    {
        // Check if email and password are provided
        if (empty($email) || empty($password)) {
            throw new \Exception("Email and password are required");
        }

        // Check if email and password match a user in database
        $user = $this->userRepository->verifyPassword($email, $password);

        // If user found and password correct
        if ($user) {
            // Store user info in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'] ?? 0;
            return true;
        }

        // If no match found, throw error
        throw new \Exception("Invalid email or password");
    }

    // Log out current user
    public function logout()
    {
        // Destroy session (removes all session data)
        session_destroy();
        return true;
    }

    // Check if user is logged in
    public function isAuthenticated()
    {
        // User is logged in if user_id exists in session
        return isset($_SESSION['user_id']);
    }

    // Get current logged-in user's data
    public function getCurrentUser()
    {
        // If user is logged in
        if ($this->isAuthenticated()) {
            // Get user data from database
            return $this->userRepository->findById($_SESSION['user_id']);
        }
        
        // No user logged in
        return null;
    }

    // Check if current user is an admin
    public function isAdmin()
    {
        // Check if is_admin flag is set to 1 in session
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }

    // Require admin access (throw error if not admin)
    // Use this to protect admin-only pages/features
    public function requireAdmin()
    {
        if (!$this->isAdmin()) {
            throw new \Exception("Admin access required");
        }
    }
}
