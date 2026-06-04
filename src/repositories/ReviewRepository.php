<?php
// src/repositories/ReviewRepository.php

class ReviewRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getByBookId($bookId)
    {
        $stmt = $this->db->prepare("
            SELECT r.*, u.username 
            FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.book_id = ? 
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$bookId]);
        return $stmt->fetchAll();
    }

    public function create($userId, $bookId, $rating, $reviewText)
    {
        $stmt = $this->db->prepare("INSERT INTO reviews (user_id, book_id, rating, review_text) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $bookId, $rating, $reviewText]);
        return $this->db->lastInsertId();
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public function findByUserAndBook($userId, $bookId)
    {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE user_id = ? AND book_id = ?");
        $stmt->execute([$userId, $bookId]);
        return $stmt->fetch();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
