<?php
// This controller handles user login, registration, and logout

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\CsrfService;

class authcontroller
{
    // Variable to hold the auth service
    private $authService;

    // Constructor runs when the controller is created
    public function __construct()
    {
        // Create a new AuthService object
        $this->authService = new AuthService();
        
        // Create a security token for forms
        CsrfService::generateToken();
    }

    // Show the login page
    public function index()
    {
        // Check if user is already logged in
        if ($this->authService->isAuthenticated()) {
            // If yes, send them to dashboard
            header('Location: /dashboard/index');
            exit;
        }
        
        // Show the login page
        include __DIR__ . '/../views/auth/login.php';
    }

    // Handle user registration
    public function register()
    {
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            try {
                // Get the security token from the form
                $csrfToken = $_POST['csrf_token'] ?? '';
                
                // Check if the security token is valid
                if (!CsrfService::validateToken($csrfToken)) {
                    throw new \Exception("Invalid security token. Please try again.");
                }
                
                // Get all form data
                $name = $_POST['name'] ?? '';
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                // Check if passwords match
                if ($password !== $confirmPassword) {
                    throw new \Exception("Passwords do not match");
                }

                // Create the new user
                $this->authService->register($name, $email, $password);
                
                // Create a new security token after registration
                CsrfService::regenerateToken();
                
                // Redirect to dashboard
                header('Location: /dashboard/index');
                exit;
                
            } catch (\Exception $e) {
                // If something went wrong, save error message
                $_SESSION['error'] = $e->getMessage();
                header('Location: /auth/register');
                exit;
            }
            
        } else {
            // If user is already logged in, send to dashboard
            if ($this->authService->isAuthenticated()) {
                header('Location: /dashboard/index');
                exit;
            }
            
            // Show registration form
            include __DIR__ . '/../views/auth/register.php';
        }
    }

    // Handle user login
    public function login()
    {
        // Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            try {
                // Get the security token from the form
                $csrfToken = $_POST['csrf_token'] ?? '';
                
                // Check if the security token is valid
                if (!CsrfService::validateToken($csrfToken)) {
                    throw new \Exception("Invalid security token. Please try again.");
                }
                
                // Get email and password from form
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';

                // Try to log the user in
                $this->authService->login($email, $password);
                
                // Create a new security token after login
                CsrfService::regenerateToken();
                
                // Redirect to dashboard
                header('Location: /dashboard/index');
                exit;
                
            } catch (\Exception $e) {
                // If login failed, save error message
                $_SESSION['error'] = $e->getMessage();
                header('Location: /auth/index');
                exit;
            }
            
        } else {
            // If user is already logged in, send to dashboard
            if ($this->authService->isAuthenticated()) {
                header('Location: /dashboard/index');
                exit;
            }
            
            // Show login form
            include __DIR__ . '/../views/auth/login.php';
        }
    }

    // Log the user out
    public function logout()
    {
        // Call logout function
        $this->authService->logout();
        
        // Redirect to login page
        header('Location: /auth/index');
        exit;
    }
}
