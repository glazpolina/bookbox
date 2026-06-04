<?php
// src/controllers/AuthController.php

class AuthController
{
    private $service;

    public function __construct()
    {
        $this->service = new AuthService();
    }

    public function register()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $this->service->register($data);
        http_response_code($result['status']);
        echo json_encode($result['success'] ? $result['data'] : ['errors' => $result['errors']]);
    }

    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $this->service->login($data);
        http_response_code($result['status']);
        echo json_encode($result['success'] ? $result['data'] : ['errors' => $result['errors']]);
    }

    public function me()
    {
        $user = Auth::requireAuth();
        echo json_encode(['user' => $user]);
    }
}
