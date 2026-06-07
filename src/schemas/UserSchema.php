<?php
class UserSchema
{
    public static function validateRegister($data)
    {
        $errors = [];

        if (empty($data['username'])) {
            $errors[] = 'Username is required';
        } elseif (strlen($data['username']) < 3) {
            $errors[] = 'Username must be at least 3 characters';
        }

        if (empty($data['password'])) {
            $errors[] = 'Password is required';
        } elseif (strlen($data['password']) < 4) {
            $errors[] = 'Password must be at least 4 characters';
        }

        return $errors;
    }

    public static function validateLogin($data)
    {
        $errors = [];

        if (empty($data['username'])) {
            $errors[] = 'Username is required';
        }
        if (empty($data['password'])) {
            $errors[] = 'Password is required';
        }

        return $errors;
    }
}
