<?php
// src/controllers/PageController.php

class PageController
{

    public function home()
    {
        $this->render('home', ['title' => 'BookBoxd']);
    }

    public function login()
    {
        $this->render('login', ['title' => 'Вход']);
    }

    public function register()
    {
        $this->render('register', ['title' => 'Регистрация']);
    }

    public function bookDetail($id)
    {
        $this->render('book_detail', ['title' => 'Книга', 'bookId' => $id]);
    }

    public function profile()
    {
        $this->render('profile', ['title' => 'Профиль']);
    }

    public function adminBooks()
    {
        $this->render('admin_books', ['title' => 'Управление книгами']);
    }

    private function render($view, $data = [])
    {
        extract($data);
        require __DIR__ . '/../../templates/layout.php';
    }
}
