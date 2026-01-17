<?php

namespace App\Repositories;

use App\Models\Order;
use PDO;

class OrderRepository extends BaseRepository
{
    protected $table = 'orders';

    public function create(Order $order)
    {
        $sql = "INSERT INTO {$this->table} (user_id, recipe_id, quantity, total_price, status, order_date, delivery_date, notes) 
                VALUES (:user_id, :recipe_id, :quantity, :total_price, :status, :order_date, :delivery_date, :notes)";
        
        $this->execute($sql, [
            ':user_id' => $order->getUserId(),
            ':recipe_id' => $order->getRecipeId(),
            ':quantity' => $order->getQuantity(),
            ':total_price' => $order->getTotalPrice(),
            ':status' => $order->getStatus() ?? 'pending',
            ':order_date' => $order->getOrderDate() ?? date('Y-m-d H:i:s'),
            ':delivery_date' => $order->getDeliveryDate(),
            ':notes' => $order->getNotes()
        ]);

        return $this->db->lastInsertId();
    }

    public function update(Order $order)
    {
        $sql = "UPDATE {$this->table} SET user_id = :user_id, recipe_id = :recipe_id, quantity = :quantity, 
                total_price = :total_price, status = :status, delivery_date = :delivery_date, notes = :notes WHERE id = :id";
        
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

    public function updateStatus($orderId, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        return $this->execute($sql, [':id' => $orderId, ':status' => $status])->rowCount() > 0;
    }

    public function findByUserId($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY order_date DESC";
        $stmt = $this->execute($sql, [':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByStatus($status)
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = :status ORDER BY order_date DESC";
        $stmt = $this->execute($sql, [':status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderWithDetails($orderId)
    {
        $sql = "SELECT o.*, u.name as user_name, u.email as user_email, r.title as recipe_title 
                FROM {$this->table} o 
                INNER JOIN users u ON o.user_id = u.id 
                INNER JOIN recipes r ON o.recipe_id = r.id 
                WHERE o.id = :id";
        $stmt = $this->execute($sql, [':id' => $orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findRecentOrders($limit = 10)
    {
        $sql = "SELECT o.*, u.name as user_name, r.title as recipe_title 
                FROM {$this->table} o 
                INNER JOIN users u ON o.user_id = u.id 
                INNER JOIN recipes r ON o.recipe_id = r.id 
                ORDER BY o.order_date DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
