<?php
// Repository for user database operations

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    // Set table name
    protected $table = 'users';

    // Create new user in database
    public function create(User $user)
    {
        // SQL query to insert new user
        $sql = "INSERT INTO {$this->table} (name, email, password, profile_photo, dietary_preferences, allergies, is_admin) 
                VALUES (:name, :email, :password, :profile_photo, :dietary_preferences, :allergies, :is_admin)";
        
        // Execute with user data
        $this->execute($sql, [
            ':name' => $user->getName(),
            ':email' => $user->getEmail(),
            ':password' => password_hash($user->getPassword(), PASSWORD_BCRYPT), // Hash password for security
            ':profile_photo' => $user->getProfilePhoto(),
            ':dietary_preferences' => $user->getDietaryPreferences(),
            ':allergies' => $user->getAllergies(),
            ':is_admin' => $user->getIsAdmin() ?? 0 // Default to regular user
        ]);

        // Return the new user's ID
        return $this->db->lastInsertId();
    }

    // Update existing user
    public function update(User $user)
    {
        // SQL query to update user data
        $sql = "UPDATE {$this->table} SET name = :name, email = :email, profile_photo = :profile_photo, dietary_preferences = :dietary_preferences, allergies = :allergies, is_admin = :is_admin 
                WHERE id = :id";
        
        // Execute update and return true if rows were changed
        return $this->execute($sql, [
            ':id' => $user->getId(),
            ':name' => $user->getName(),
            ':email' => $user->getEmail(),
            ':profile_photo' => $user->getProfilePhoto(),
            ':dietary_preferences' => $user->getDietaryPreferences(),
            ':allergies' => $user->getAllergies(),
            ':is_admin' => $user->getIsAdmin() ?? 0
        ])->rowCount() > 0;
    }

    // Find user by email address
    public function findByEmail($email)
    {
        // Query for user with matching email
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->execute($sql, [':email' => $email]);
        
        // Return user data or false if not found
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Check if email and password match
    // Used for login
    public function verifyPassword($email, $password)
    {
        // Get user by email
        $user = $this->findByEmail($email);
        
        // Check if user exists and password is correct
        if ($user && password_verify($password, $user['password'])) {
            return $user; // Return user data if login successful
        }
        
        return false; // Return false if login failed
    }
}
