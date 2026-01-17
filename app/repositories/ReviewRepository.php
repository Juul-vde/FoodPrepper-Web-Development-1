<?php

namespace App\Repositories;

use App\Models\Review;
use PDO;

class ReviewRepository extends BaseRepository
{
    protected $table = 'reviews';

    public function create(Review $review)
    {
        $sql = "INSERT INTO {$this->table} (recipe_id, user_id, rating, comment) 
                VALUES (:recipe_id, :user_id, :rating, :comment)";
        
        $this->execute($sql, [
            ':recipe_id' => $review->getRecipeId(),
            ':user_id' => $review->getUserId(),
            ':rating' => $review->getRating(),
            ':comment' => $review->getComment()
        ]);

        return $this->db->lastInsertId();
    }

    public function update(Review $review)
    {
        $sql = "UPDATE {$this->table} SET rating = :rating, comment = :comment WHERE id = :id";
        
        return $this->execute($sql, [
            ':id' => $review->getId(),
            ':rating' => $review->getRating(),
            ':comment' => $review->getComment()
        ])->rowCount() > 0;
    }

    public function findByRecipeId($recipeId)
    {
        $sql = "SELECT r.*, u.name as user_name FROM {$this->table} r 
                INNER JOIN users u ON r.user_id = u.id 
                WHERE r.recipe_id = :recipe_id ORDER BY r.created_at DESC";
        $stmt = $this->execute($sql, [':recipe_id' => $recipeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUserId($userId)
    {
        $sql = "SELECT r.*, rec.title as recipe_title FROM {$this->table} r 
                INNER JOIN recipes rec ON r.recipe_id = rec.id 
                WHERE r.user_id = :user_id ORDER BY r.created_at DESC";
        $stmt = $this->execute($sql, [':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAverageRating($recipeId)
    {
        $sql = "SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews FROM {$this->table} WHERE recipe_id = :recipe_id";
        $stmt = $this->execute($sql, [':recipe_id' => $recipeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function userHasReviewed($recipeId, $userId)
    {
        $sql = "SELECT id FROM {$this->table} WHERE recipe_id = :recipe_id AND user_id = :user_id";
        $stmt = $this->execute($sql, [':recipe_id' => $recipeId, ':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}
