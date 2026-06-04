<?php
// src/core/Auth.php

class Auth
{
    public static function requireAuth()
    {
        $token = JWT::getFromHeader();
        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $user = JWT::validate($token);
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token']);
            exit;
        }

        return $user;
    }

    public static function requireAdmin()
    {
        $user = self::requireAuth();
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        return $user;
    }
}
