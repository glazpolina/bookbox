<?php
class BookRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }
    public function getAll()
    {
        $sql = "SELECT 
                b.*, 
                COALESCE(AVG(r.rating), 0) AS calculated_rating, 
                COUNT(r.id) AS reviews_count
            FROM books b
            LEFT JOIN reviews r ON b.id = r.book_id
            GROUP BY b.id
            ORDER BY calculated_rating DESC, b.id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // public function getAll()
    // {
    //     $stmt = $this->db->query("SELECT * FROM v_books_with_rating ORDER BY id");
    //     return $stmt->fetchAll();
    // }
    // public function getAll($genreId = null, $search = null)
    // {
    //     $sql = "SELECT DISTINCT 
    //             b.*, 
    //             COALESCE(AVG(r.rating), 0) AS calculated_rating, 
    //             COUNT(DISTINCT r.id) AS reviews_count
    //         FROM books b
    //         LEFT JOIN reviews r ON b.id = r.book_id
    //         LEFT JOIN book_genres bg ON b.id = bg.book_id";
    //     $conditions = [];
    //     $params = [];
    //     if ($genreId && $genreId !== '') {
    //         $conditions[] = "bg.genre_id = ?";
    //         $params[] = $genreId;
    //     }
    //     if ($search && $search !== '') {
    //         $conditions[] = "(b.title LIKE ? OR b.author LIKE ?)";
    //         $params[] = "%$search%";
    //         $params[] = "%$search%";
    //     }
    //     if (!empty($conditions)) {
    //         $sql .= " WHERE " . implode(" AND ", $conditions);
    //     }
    //     $sql .= " GROUP BY b.id ORDER BY b.id";
    //     $stmt = $this->db->prepare($sql);
    //     $stmt->execute($params);
    //     return $stmt->fetchAll();
    // }

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
    public function getGenres()
    {
        $stmt = $this->db->query("SELECT id, name FROM genres ORDER BY name");
        return $stmt->fetchAll();
    }
    public function getGenresForBook($bookId)
    {
        $stmt = $this->db->prepare("
        SELECT g.id, g.name 
        FROM genres g 
        JOIN book_genres bg ON g.id = bg.genre_id 
        WHERE bg.book_id = ?
        ORDER BY g.name
    ");
        $stmt->execute([$bookId]);
        return $stmt->fetchAll();
    }

    public function setGenres($bookId, $genreIds)
    {
        $stmt = $this->db->prepare("DELETE FROM book_genres WHERE book_id = ?");
        $stmt->execute([$bookId]);

        foreach ($genreIds as $genreId) {
            $stmt = $this->db->prepare("INSERT INTO book_genres (book_id, genre_id) VALUES (?, ?)");
            $stmt->execute([$bookId, $genreId]);
        }
    }
}
