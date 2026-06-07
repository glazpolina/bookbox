<?php
class ReviewSchema
{
    public static function validate($data)
    {
        $errors = [];

        if (empty($data['book_id'])) {
            $errors[] = 'Book ID is required';
        }
        if (empty($data['rating']) || $data['rating'] < 1 || $data['rating'] > 10) {
            $errors[] = 'Rating must be between 1 and 10';
        }
        if (empty($data['review_text'])) {
            $errors[] = 'Review text is required';
        }
        return $errors;
    }
}
