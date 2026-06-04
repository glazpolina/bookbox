<?php
// src/repositories/BookRepository.php

class BookRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM v_books_with_rating ORDER BY id");
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM v_books_with_rating WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO books (title, author, year, description, cover_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$data['title'], $data['author'], $data['year'], $data['description'], $data['cover_image'] ?? null]);
        return $this->getById($this->db->lastInsertId());
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE books SET title = ?, author = ?, year = ?, description = ?, cover_image = ? WHERE id = ?");
        $stmt->execute([$data['title'], $data['author'], $data['year'], $data['description'], $data['cover_image'] ?? null, $id]);
        return $this->getById($id);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM books WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
    public function updateCover($id, $coverPath)
    {
        $stmt = $this->db->prepare("UPDATE books SET cover_image = ? WHERE id = ?");
        $stmt->execute([$coverPath, $id]);
    }
}
