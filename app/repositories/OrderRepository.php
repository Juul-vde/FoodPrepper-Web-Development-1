<?php
// Repository for order database operations
// (Note: This appears to be for future feature, not currently used)

namespace App\Repositories;

use App\Models\Order;
use PDO;

class OrderRepository extends BaseRepository
{
    // Set table name
    protected $table = 'orders';

    // Create new order
    public function create(Order $order)
    {
        // Insert order with all details
        $sql = "INSERT INTO {$this->table} (user_id, recipe_id, quantity, total_price, status, order_date, delivery_date, notes) 
                VALUES (:user_id, :recipe_id, :quantity, :total_price, :status, :order_date, :delivery_date, :notes)";
        
        $this->execute($sql, [
            ':user_id' => $order->getUserId(),
            ':recipe_id' => $order->getRecipeId(),
            ':quantity' => $order->getQuantity(),
            ':total_price' => $order->getTotalPrice(),
            ':status' => $order->getStatus() ?? 'pending', // Default to pending
            ':order_date' => $order->getOrderDate() ?? date('Y-m-d H:i:s'), // Default to now
            ':delivery_date' => $order->getDeliveryDate(),
            ':notes' => $order->getNotes()
        ]);

        // Return new order ID
        return $this->db->lastInsertId();
    }

    // Update existing order
    public function update(Order $order)
    {
        // Update all order fields
        $sql = "UPDATE {$this->table} SET user_id = :user_id, recipe_id = :recipe_id, quantity = :quantity, 
                total_price = :total_price, status = :status, delivery_date = :delivery_date, notes = :notes WHERE id = :id";
        
        // Return true if rows were changed
        return $this->execute($sql, [
            ':id' => $order->getId(),
            ':user_id' => $order->getUserId(),
            ':recipe_id' => $order->getRecipeId(),
            ':quantity' => $order->getQuantity(),
            ':total_price' => $order->getTotalPrice(),
            ':status' => $order->getStatus(),
            ':delivery_date' => $order->getDeliveryDate(),
            ':notes' => $order->getNotes()
        ])->rowCount() > 0;
    }

    // Update only the order status
    public function updateStatus($orderId, $status)
    {
        // Quick update for status changes
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        return $this->execute($sql, [':id' => $orderId, ':status' => $status])->rowCount() > 0;
    }

    // Get all orders for a user
    public function findByUserId($userId)
    {
        // Get user's orders, newest first
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY order_date DESC";
        $stmt = $this->execute($sql, [':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get orders by status (e.g., 'pending', 'completed', 'cancelled')
    public function findByStatus($status)
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = :status ORDER BY order_date DESC";
        $stmt = $this->execute($sql, [':status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get order with user and recipe details
    public function getOrderWithDetails($orderId)
    {
        // Join with users and recipes to get full details
        $sql = "SELECT o.*, u.name as user_name, u.email as user_email, r.title as recipe_title 
                FROM {$this->table} o 
                INNER JOIN users u ON o.user_id = u.id 
                INNER JOIN recipes r ON o.recipe_id = r.id 
                WHERE o.id = :id";
        
        $stmt = $this->execute($sql, [':id' => $orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get recent orders
    // $limit = how many orders to return (default 10)
    public function findRecentOrders($limit = 10)
    {
        // Get most recent orders with user and recipe names
        $sql = "SELECT o.*, u.name as user_name, r.title as recipe_title 
                FROM {$this->table} o 
                INNER JOIN users u ON o.user_id = u.id 
                INNER JOIN recipes r ON o.recipe_id = r.id 
                ORDER BY o.order_date DESC LIMIT :limit";
        
        // Prepare and bind limit as integer
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
