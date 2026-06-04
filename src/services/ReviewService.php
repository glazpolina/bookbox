<?php
// src/services/ReviewService.php

class ReviewService
{
    private $reviewRepo;

    public function __construct()
    {
        $this->reviewRepo = new ReviewRepository();
    }

    public function getByBook($bookId)
    {
        return $this->reviewRepo->getByBookId($bookId);
    }

    public function create($data, $user)
    {
        if (!$user) {
            return ['success' => false, 'errors' => ['Unauthorized'], 'status' => 401];
        }

        $errors = ReviewSchema::validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'status' => 400];
        }

        $existing = $this->reviewRepo->findByUserAndBook($user['user_id'], $data['book_id']);
        if ($existing) {
            return ['success' => false, 'errors' => ['You already reviewed this book'], 'status' => 409];
        }

        $this->reviewRepo->create($user['user_id'], $data['book_id'], $data['rating'], $data['review_text']);
        return ['success' => true, 'status' => 201];
    }

    public function delete($id, $user)
    {
        if (!$user) {
            return ['success' => false, 'errors' => ['Unauthorized'], 'status' => 401];
        }

        $review = $this->reviewRepo->findById($id);
        if (!$review) {
            return ['success' => false, 'errors' => ['Review not found'], 'status' => 404];
        }

        if ($review['user_id'] != $user['user_id'] && $user['role'] !== 'admin') {
            return ['success' => false, 'errors' => ['Forbidden'], 'status' => 403];
        }

        $this->reviewRepo->delete($id);
        return ['success' => true, 'status' => 204];
    }
}
