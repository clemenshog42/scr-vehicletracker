<?php

declare(strict_types=1);

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict',
]);

set_exception_handler(function ($e) {
    error_log((string)$e);
    http_response_code(500);
    echo "<h1>500 Internal Server Error</h1>";
    echo "<p>Something went wrong. Please try again later.</p>";
    exit;
});

// Simple PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

require_once __DIR__ . '/src/functions.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Simple Routing
$routes = [
    'GET' => [
        '/' => ['HomeController', 'index'],
        '/login' => ['AuthController', 'showLogin'],
        '/register' => ['AuthController', 'showRegister'],
        '/dashboard' => ['DashboardController', 'index'],
        '/vehicles' => ['VehicleController', 'index'],
        '/vehicles/add' => ['VehicleController', 'showAdd'],
        '/vehicles/edit' => ['VehicleController', 'showEdit'],
        '/entries' => ['EntryController', 'index'],
        '/entries/add' => ['EntryController', 'showAdd'],
        '/entries/edit' => ['EntryController', 'showEdit'],
        '/admin/users' => ['AdminController', 'index'],
    ],
    'POST' => [
        '/login' => ['AuthController', 'login'],
        '/register' => ['AuthController', 'register'],
        '/logout' => ['AuthController', 'logout'],
        '/vehicles/add' => ['VehicleController', 'add'],
        '/vehicles/edit' => ['VehicleController', 'edit'],
        '/vehicles/delete' => ['VehicleController', 'delete'],
        '/entries/add' => ['EntryController', 'add'],
        '/entries/edit' => ['EntryController', 'edit'],
        '/entries/delete' => ['EntryController', 'delete'],
        '/admin/users/status' => ['AdminController', 'updateStatus'],
    ]
];

$route = $routes[$method][$path] ?? null;

if ($route) {
    [$controllerName, $action] = $route;
    $controllerClass = "App\\Controllers\\$controllerName";
    
    if (class_exists($controllerClass)) {
        $controller = new $controllerClass();
        $controller->$action();
    } else {
        http_response_code(404);
        echo "404 Not Found - Controller $controllerClass not found";
    }
} else {
    http_response_code(404);
    echo "404 Not Found - Route $path not found";
}
