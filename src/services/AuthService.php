<?php
// src/services/AuthService.php

class AuthService
{
    private $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    public function register($data)
    {
        $errors = UserSchema::validateRegister($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'status' => 400];
        }

        $existing = $this->userRepo->findByUsername($data['username']);
        if ($existing) {
            return ['success' => false, 'errors' => ['Username already exists'], 'status' => 409];
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $user = $this->userRepo->create($data['username'], $passwordHash, 'user');

        return [
            'success' => true,
            'data' => ['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role']],
            'status' => 201
        ];
    }

    public function login($data)
    {
        $errors = UserSchema::validateLogin($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'status' => 400];
        }

        $user = $this->userRepo->findByUsername($data['username']);
        if (!$user || !password_verify($data['password'], $user['password_hash'])) {
            return ['success' => false, 'errors' => ['Invalid credentials'], 'status' => 401];
        }

        $token = JWT::generate($user['id'], $user['username'], $user['role']);

        return [
            'success' => true,
            'data' => [
                'token' => $token,
                'user' => ['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role']]
            ],
            'status' => 200
        ];
    }
}
