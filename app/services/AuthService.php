<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;

class AuthService
{
    private $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function register($name, $email, $password)
    {
        // Validate input
        if (empty($name) || empty($email) || empty($password)) {
            throw new \Exception("All fields are required");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Invalid email format");
        }

        if (strlen($password) < 6) {
            throw new \Exception("Password must be at least 6 characters");
        }

        // Check if user already exists
        if ($this->userRepository->findByEmail($email)) {
            throw new \Exception("Email already registered");
        }

        // Create new user
        $user = new User($name, $email, $password);
        $userId = $this->userRepository->create($user);

        if ($userId) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            return true;
        }

        return false;
    }

    public function login($email, $password)
    {
        if (empty($email) || empty($password)) {
            throw new \Exception("Email and password are required");
        }

        $user = $this->userRepository->verifyPassword($email, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            return true;
        }

        throw new \Exception("Invalid email or password");
    }

    public function logout()
    {
        session_destroy();
        return true;
    }

    public function isAuthenticated()
    {
        return isset($_SESSION['user_id']);
    }

    public function getCurrentUser()
    {
        if ($this->isAuthenticated()) {
            return $this->userRepository->findById($_SESSION['user_id']);
        }
        return null;
    }
}
