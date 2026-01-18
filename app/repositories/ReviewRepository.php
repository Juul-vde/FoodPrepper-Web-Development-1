<?php
// Repository for recipe review database operations

namespace App\Repositories;

use App\Models\Review;
use PDO;

class ReviewRepository extends BaseRepository
{
    // Set table name
    protected $table = 'reviews';

    // Create new review
    public function create(Review $review)
    {
        // Insert review with rating and comment
        $sql = "INSERT INTO {$this->table} (recipe_id, user_id, rating, comment) 
                VALUES (:recipe_id, :user_id, :rating, :comment)";
        
        $this->execute($sql, [
            ':recipe_id' => $review->getRecipeId(),
            ':user_id' => $review->getUserId(),
            ':rating' => $review->getRating(),
            ':comment' => $review->getComment()
        ]);

        // Return new review ID
        return $this->db->lastInsertId();
    }

    // Update existing review
    public function update(Review $review)
    {
        // Update rating and comment
        $sql = "UPDATE {$this->table} SET rating = :rating, comment = :comment WHERE id = :id";
        
        // Return true if rows were changed
        return $this->execute($sql, [
            ':id' => $review->getId(),
            ':rating' => $review->getRating(),
            ':comment' => $review->getComment()
        ])->rowCount() > 0;
    }

    // Get all reviews for a recipe
    public function findByRecipeId($recipeId)
    {
        // Join with users table to get reviewer names
        $sql = "SELECT r.*, u.name as user_name FROM {$this->table} r 
                INNER JOIN users u ON r.user_id = u.id 
                WHERE r.recipe_id = :recipe_id ORDER BY r.created_at DESC";
        
        $stmt = $this->execute($sql, [':recipe_id' => $recipeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all reviews by a user
    public function findByUserId($userId)
    {
        // Join with recipes to get recipe titles
        $sql = "SELECT r.*, rec.title as recipe_title FROM {$this->table} r 
                INNER JOIN recipes rec ON r.recipe_id = rec.id 
                WHERE r.user_id = :user_id ORDER BY r.created_at DESC";
        
        $stmt = $this->execute($sql, [':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Calculate average rating for a recipe
    public function getAverageRating($recipeId)
    {
        // Use AVG() function to calculate average rating
        $sql = "SELECT AVG(rating) as average_rating, COUNT(*) as total_reviews FROM {$this->table} WHERE recipe_id = :recipe_id";
        $stmt = $this->execute($sql, [':recipe_id' => $recipeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Check if user has already reviewed a recipe
    public function userHasReviewed($recipeId, $userId)
    {
        // Check if review exists
        $sql = "SELECT id FROM {$this->table} WHERE recipe_id = :recipe_id AND user_id = :user_id";
        $stmt = $this->execute($sql, [':recipe_id' => $recipeId, ':user_id' => $userId]);
        
        // Return true if review found, false otherwise
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
}
