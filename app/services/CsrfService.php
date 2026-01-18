<?php
// CSRF Service - Protection against Cross-Site Request Forgery attacks
// 
// What is CSRF?
// A CSRF attack tricks a logged-in user into doing something they didn't intend
// For example: changing their password, making a purchase, deleting data
// 
// How this protection works:
// 1. Generate a secret random token for each user's session
// 2. Include this token in every form as a hidden field
// 3. When form is submitted, check if the token matches
// 4. If tokens don't match, reject the request (probably an attack)

namespace App\Services;

class CsrfService
{
    // Generate a CSRF token (or return existing one)
    public static function generateToken()
    {
        // Make sure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // If no token exists yet, create a new one
        if (!isset($_SESSION['csrf_token'])) {
            // Create 32 random bytes and convert to hex string (64 characters)
            // This is cryptographically secure (impossible to guess)
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Return the token
        return $_SESSION['csrf_token'];
    }

    // Get the current token without creating a new one
    public static function getToken()
    {
        // Make sure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Return token if it exists, or null if not
        return $_SESSION['csrf_token'] ?? null;
    }

    // Check if a token is valid
    // $token = the token sent with the form
    public static function validateToken($token)
    {
        // Make sure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Get the token stored in session
        $sessionToken = $_SESSION['csrf_token'] ?? null;

        // Token is invalid if:
        // - No token was sent with the form
        // - No token exists in session
        if (empty($token) || empty($sessionToken)) {
            return false;
        }

        // Compare tokens using hash_equals (secure comparison)
        // This prevents "timing attacks" where hackers measure how long
        // the comparison takes to guess the token character by character
        return hash_equals($sessionToken, $token);
    }

    // Generate HTML for a hidden input field with the token
    // Just add this inside your <form> tags
    public static function getTokenField()
    {
        // Get or create the token
        $token = self::generateToken();

        // Return HTML for hidden input
        // htmlspecialchars prevents XSS attacks
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    // Create a completely new token (replaces old one)
    // Use this after login for extra security
    public static function regenerateToken()
    {
        // Make sure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Delete old token
        unset($_SESSION['csrf_token']);

        // Generate and return new token
        return self::generateToken();
    }
}
