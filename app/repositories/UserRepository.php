<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    protected $table = 'users';

    public function create(User $user)
    {
        $sql = "INSERT INTO {$this->table} (name, email, password, profile_photo, dietary_preferences, allergies, is_admin) 
                VALUES (:name, :email, :password, :profile_photo, :dietary_preferences, :allergies, :is_admin)";
        
        $this->execute($sql, [
            ':name' => $user->getName(),
            ':email' => $user->getEmail(),
            ':password' => password_hash($user->getPassword(), PASSWORD_BCRYPT),
            ':profile_photo' => $user->getProfilePhoto(),
            ':dietary_preferences' => $user->getDietaryPreferences(),
            ':allergies' => $user->getAllergies(),
            ':is_admin' => $user->getIsAdmin() ?? 0
        ]);

        return $this->db->lastInsertId();
    }

    public function update(User $user)
    {
        $sql = "UPDATE {$this->table} SET name = :name, email = :email, profile_photo = :profile_photo, dietary_preferences = :dietary_preferences, allergies = :allergies, is_admin = :is_admin 
                WHERE id = :id";
        
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

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        $stmt = $this->execute($sql, [':email' => $email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function verifyPassword($email, $password)
    {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
