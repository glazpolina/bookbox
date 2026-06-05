<?php
// src/services/BookService.php

class BookService
{
    private $bookRepo;

    public function __construct()
    {
        $this->bookRepo = new BookRepository();
    }

    // public function getAll()
    // {
    //     return $this->bookRepo->getAll();
    // }
    public function getAll($genreId = null, $search = null)
    {
        return $this->bookRepo->getAll($genreId, $search);
    }

    public function getOne($id)
    {
        $book = $this->bookRepo->getById($id);
        if (!$book) {
            return ['success' => false, 'errors' => ['Book not found'], 'status' => 404];
        }
        return ['success' => true, 'data' => $book];
    }

    public function create($data, $user)
    {
        if ($user['role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Forbidden'], 'status' => 403];
        }

        $errors = BookSchema::validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'status' => 400];
        }

        $book = $this->bookRepo->create($data);
        return ['success' => true, 'data' => $book, 'status' => 201];
    }

    public function update($id, $data, $user)
    {
        if ($user['role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Forbidden'], 'status' => 403];
        }

        $existing = $this->bookRepo->getById($id);
        if (!$existing) {
            return ['success' => false, 'errors' => ['Book not found'], 'status' => 404];
        }

        $errors = BookSchema::validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'status' => 400];
        }

        $book = $this->bookRepo->update($id, $data);
        return ['success' => true, 'data' => $book];
    }

    public function delete($id, $user)
    {
        if ($user['role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Forbidden'], 'status' => 403];
        }

        $existing = $this->bookRepo->getById($id);
        if (!$existing) {
            return ['success' => false, 'errors' => ['Book not found'], 'status' => 404];
        }

        $this->bookRepo->delete($id);
        return ['success' => true, 'status' => 204];
    }
    public function updateCover($id, $coverPath, $user)
    {
        if ($user['role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Forbidden'], 'status' => 403];
        }

        $book = $this->bookRepo->getById($id);
        if (!$book) {
            return ['success' => false, 'errors' => ['Book not found'], 'status' => 404];
        }

        $this->bookRepo->updateCover($id, $coverPath);
        return ['success' => true];
    }
    public function getGenres()
    {
        return $this->bookRepo->getGenres();
    }
    public function getGenresForBook($bookId)
    {
        return $this->bookRepo->getGenresForBook($bookId);
    }

    public function setGenres($bookId, $genreIds, $user)
    {
        if ($user['role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Forbidden'], 'status' => 403];
        }
        $this->bookRepo->setGenres($bookId, $genreIds);
        return ['success' => true];
    }
}
