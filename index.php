<?php
// index.php

require_once __DIR__ . '/src/core/Database.php';
require_once __DIR__ . '/src/core/JWT.php';
require_once __DIR__ . '/src/core/Auth.php';

spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/src/controllers/',
        __DIR__ . '/src/services/',
        __DIR__ . '/src/repositories/',
        __DIR__ . '/src/schemas/'
    ];
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

error_reporting(E_ALL);
ini_set('display_errors', 1);

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (isset($_GET['route'])) {
    $route = $_GET['route'];
} else {
    $route = preg_replace('#^/bookbox/#', '', $uri);
    $route = trim($route, '/');
}

//POST /api/auth/login
if ($method === 'POST' && ($route === 'api/auth/login' || $uri === '/bookbox/api/auth/login')) {
    $controller = new AuthController();
    $controller->login();
    exit;
}

//POST /api/auth/register
if ($method === 'POST' && ($route === 'api/auth/register' || $uri === '/bookbox/api/auth/register')) {
    $controller = new AuthController();
    $controller->register();
    exit;
}

//GET /api/auth/me
if ($method === 'GET' && ($route === 'api/auth/me' || $uri === '/bookbox/api/auth/me')) {
    $controller = new AuthController();
    $controller->me();
    exit;
}

//GET /api/books
if ($method === 'GET' && ($route === 'api/books' || $uri === '/bookbox/api/books')) {
    $controller = new BookController();
    $controller->getAll();
    exit;
}

//GET /api/books/:id
if ($method === 'GET' && preg_match('#^api/books/(\d+)$#', $route, $matches)) {
    $controller = new BookController();
    $controller->getOne($matches[1]);
    exit;
}
if ($method === 'GET' && preg_match('#^/bookbox/api/books/(\d+)$#', $uri, $matches)) {
    $controller = new BookController();
    $controller->getOne($matches[1]);
    exit;
}

//POST /api/books
if ($method === 'POST' && ($route === 'api/books' || $uri === '/bookbox/api/books')) {
    $controller = new BookController();
    $controller->create();
    exit;
}

//PUT /api/books/:id
if ($method === 'PUT' && preg_match('#^api/books/(\d+)$#', $route, $matches)) {
    $controller = new BookController();
    $controller->update($matches[1]);
    exit;
}

//DELETE /api/books/:id
if ($method === 'DELETE' && preg_match('#^api/books/(\d+)$#', $route, $matches)) {
    $controller = new BookController();
    $controller->delete($matches[1]);
    exit;
}

//GET /api/books/:id/reviews
if ($method === 'GET' && preg_match('#^api/books/(\d+)/reviews$#', $route, $matches)) {
    $controller = new ReviewController();
    $controller->getByBook($matches[1]);
    exit;
}

//POST /api/reviews
if ($method === 'POST' && ($route === 'api/reviews' || $uri === '/bookbox/api/reviews')) {
    $controller = new ReviewController();
    $controller->create();
    exit;
}

//DELETE /api/reviews/:id
if ($method === 'DELETE' && preg_match('#^api/reviews/(\d+)$#', $route, $matches)) {
    $controller = new ReviewController();
    $controller->delete($matches[1]);
    exit;
}
//POST /api/books/:id/upload (загрузка обложки)
if ($method === 'POST' && preg_match('#^api/books/(\d+)/upload$#', $route, $matches)) {
    $controller = new BookController();
    $controller->uploadCover($matches[1]);
    exit;
}

$pageController = new PageController();

if ($route === '' || $route === '/') {
    $pageController->home();
} elseif ($route === 'login') {
    $pageController->login();
} elseif ($route === 'register') {
    $pageController->register();
} elseif ($route === 'profile') {
    $pageController->profile();
} elseif ($route === 'admin/books') {
    $pageController->adminBooks();
} elseif (preg_match('#^book/(\d+)$#', $route, $matches)) {
    $pageController->bookDetail($matches[1]);
} else {
    http_response_code(404);
    echo "404 - Page not found";
}
