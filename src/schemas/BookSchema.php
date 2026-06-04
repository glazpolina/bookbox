<?php
// src/schemas/BookSchema.php

class BookSchema
{
    public static function validate($data)
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors[] = 'Title is required';
        }
        if (empty($data['author'])) {
            $errors[] = 'Author is required';
        }

        return $errors;
    }
}
