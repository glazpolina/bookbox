<?php
class Router
{
    private $routes = [];

    public function get($path, $controller, $method)
    {
        $this->routes['GET'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function post($path, $controller, $method)
    {
        $this->routes['POST'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function put($path, $controller, $method)
    {
        $this->routes['PUT'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function delete($path, $controller, $method)
    {
        $this->routes['DELETE'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function run()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = str_replace('/bookbox', '', $uri);
        $uri = rtrim($uri, '/');
        if ($uri === '') $uri = '/';
        foreach ($this->routes[$method] ?? [] as $routePath => $handler) {
            $pattern = preg_replace('/:([a-zA-Z0-9_]+)/', '([^/]+)', $routePath);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                $controllerName = $handler['controller'];
                $methodName = $handler['method'];

                $controllerFile = __DIR__ . "/../controllers/{$controllerName}.php";
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    $controller = new $controllerName();
                    echo $controller->$methodName(...$matches);
                } else {
                    http_response_code(500);
                    echo json_encode(['error' => "Controller not found: {$controllerName}"]);
                }
                return;
            }
        }
        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }
}
