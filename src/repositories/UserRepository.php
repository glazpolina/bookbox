<?php
class UserRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT id, username, role, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($username, $passwordHash, $role = 'user')
    {
        $stmt = $this->db->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $passwordHash, $role]);
        return $this->findById($this->db->lastInsertId());
    }
}
