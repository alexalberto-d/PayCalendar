<?php
session_start();

require_once __DIR__ . '/../config.php';

// Simple Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';

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

use App\Controllers\ApiController;
use App\Controllers\AuthController;

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Helper to check auth
$isLoggedIn = isset($_SESSION['user_id']);

// Simple Routing
if ($uri === '/login') {
    if ($isLoggedIn) {
        header('Location: /');
        exit;
    }
    include __DIR__ . '/../app/Views/login.php';
} elseif ($uri === '/register') {
    if ($isLoggedIn) {
        header('Location: /');
        exit;
    }
    include __DIR__ . '/../app/Views/register.php';
} elseif ($uri === '/logout') {
    $controller = new AuthController();
    $controller->logout();
} elseif ($uri === '/api/login' && $method === 'POST') {
    $controller = new AuthController();
    $controller->login();
} elseif ($uri === '/api/register' && $method === 'POST') {
    $controller = new AuthController();
    $controller->register();
} elseif ($uri === '/' || $uri === '/index.php') {
    if (!$isLoggedIn) {
        header('Location: /login');
        exit;
    }
    include __DIR__ . '/../app/Views/dashboard.php';
} elseif (strpos($uri, '/api/subscriptions') === 0) {
    if (!$isLoggedIn) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
    $controller = new ApiController();

    if ($method === 'GET') {
        $controller->index();
    } elseif ($method === 'POST') {
        $controller->store();
    } elseif ($method === 'PUT') {
        // Simple PUT simulation for vanilla PHP
        $id = basename($uri);
        $controller->update($id);
    } elseif ($method === 'DELETE') {
        $id = basename($uri);
        $controller->delete($id);
    }
} else {
    http_response_code(404);
    echo "404 Not Found";
}
