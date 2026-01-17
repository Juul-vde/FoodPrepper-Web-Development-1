<?php

namespace App\Controllers;

use App\Services\AuthService;

class authcontroller
{
    private $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function index()
    {
        if ($this->authService->isAuthenticated()) {
            header('Location: /dashboard/index');
            exit;
        }
        include __DIR__ . '/../views/auth/login.php';
    }

    public function register()
    {
        if ($this->authService->isAuthenticated()) {
            header('Location: /dashboard/index');
            exit;
        }
        include __DIR__ . '/../views/auth/register.php';
    }

    public function handleRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /auth/register');
            exit;
        }

        try {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if ($password !== $confirmPassword) {
                throw new \Exception("Passwords do not match");
            }

            $this->authService->register($name, $email, $password);
            header('Location: /dashboard/index');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /auth/register');
            exit;
        }
    }

    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /auth/index');
            exit;
        }

        try {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $this->authService->login($email, $password);
            header('Location: /dashboard/index');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /auth/index');
            exit;
        }
    }

    public function logout()
    {
        $this->authService->logout();
        header('Location: /auth/index');
        exit;
    }
}
