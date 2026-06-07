<?php
class JWT
{
    private static $secret = 'bookbox-secret-key-2026';
    private static $expiry = 86400;

    public static function generate($userId, $username, $role)
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'user_id' => $userId,
            'username' => $username,
            'role' => $role,
            'iat' => time(),
            'exp' => time() + self::$expiry
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public static function validate($token)
    {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) return null;

            list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = $parts;
            $signature = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlSignature));
            $expectedSignature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, self::$secret, true);

            if (!hash_equals($signature, $expectedSignature)) return null;

            $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $base64UrlPayload)), true);
            if ($payload['exp'] < time()) return null;

            return $payload;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function getFromHeader()
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
