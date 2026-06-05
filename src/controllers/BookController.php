<?php
// src/controllers/BookController.php

class BookController
{
    private $service;

    public function __construct()
    {
        $this->service = new BookService();
    }

    // public function getAll()
    // {
    //     $books = $this->service->getAll();
    //     header('Content-Type: application/json; charset=utf-8');
    //     echo json_encode($books, JSON_UNESCAPED_UNICODE);
    // }
    public function getAll()
    {
        $genreId = $_GET['genre'] ?? null;
        $search = $_GET['search'] ?? null;
        $books = $this->service->getAll($genreId, $search);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($books, JSON_UNESCAPED_UNICODE);
    }

    public function getOne($id)
    {
        $result = $this->service->getOne($id);
        http_response_code($result['status'] ?? 200);
        echo json_encode($result['success'] ? $result['data'] : ['error' => $result['errors'][0]]);
    }

    public function create()
    {
        $user = Auth::requireAdmin();
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $this->service->create($data, $user);
        http_response_code($result['status']);
        echo json_encode($result['success'] ? $result['data'] : ['errors' => $result['errors']]);
    }

    public function update($id)
    {
        $user = Auth::requireAdmin();
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $this->service->update($id, $data, $user);
        http_response_code($result['status'] ?? 200);
        echo json_encode($result['success'] ? $result['data'] : ['errors' => $result['errors']]);
    }

    public function delete($id)
    {
        $user = Auth::requireAdmin();
        $result = $this->service->delete($id, $user);
        http_response_code($result['status']);
        if (!$result['success']) {
            echo json_encode(['errors' => $result['errors']]);
        }
    }
    public function uploadCover($id)
    {
        $user = Auth::requireAdmin();

        $book = $this->service->getOne($id);
        if (!$book['success']) {
            http_response_code(404);
            echo json_encode(['error' => 'Book not found']);
            return;
        }

        if (!isset($_FILES['cover']) || $_FILES['cover']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded or upload error']);
            return;
        }

        $file = $_FILES['cover'];

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Only JPG, PNG, WEBP, GIF allowed']);
            return;
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['error' => 'File too large (max 2MB)']);
            return;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'book_' . $id . '_' . time() . '.' . $extension;
        $uploadPath = __DIR__ . '/../../public/uploads/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $coverPath = 'uploads/' . $filename;
            $this->service->updateCover($id, $coverPath, $user);
            echo json_encode(['success' => true, 'path' => $coverPath]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save file']);
        }
    }
    public function getGenres()
    {
        $genres = $this->service->getGenres();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($genres, JSON_UNESCAPED_UNICODE);
    }
    public function getGenresForBook($id)
    {
        $genres = $this->service->getGenresForBook($id);
        echo json_encode($genres, JSON_UNESCAPED_UNICODE);
    }

    public function setGenres($id)
    {
        $user = Auth::requireAdmin();
        $data = json_decode(file_get_contents('php://input'), true);
        $genreIds = $data['genres'] ?? [];
        $result = $this->service->setGenres($id, $genreIds, $user);
        http_response_code($result['status']);
        echo json_encode($result['success'] ? ['message' => 'Genres updated'] : ['error' => $result['errors'][0]]);
    }
}
