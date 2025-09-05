<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'bootstrap.php';
require 'autoload.php';

$routes = require __DIR__ . '/config/routes.php';

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$base = trim(parse_url(BASE_URL, PHP_URL_PATH), '/');
if (!empty($base) && strpos($uri, $base) === 0) {
    $uri = trim(substr($uri, strlen($base)), '/');
}

if ($uri === '' || $uri === '/') $uri = 'home';

$viewFile = __DIR__ . '/views/' . $uri . '.php';

// ----------------------
// Blocked folders
// ----------------------
foreach ($routes['blocked'] as $blocked) {
    if (str_starts_with($uri, $blocked)) {
        http_response_code(403);
        exit("<h1>403 Forbidden</h1>");
    }
}

// ----------------------
// Run rules
// ----------------------
foreach ($routes['rules'] as $pattern => $callback) {
    $regex = '#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '$#';
    if (preg_match($regex, $uri)) $callback();
}

// ----------------------
// Determine type (default html)
// ----------------------
$type = 'html';
foreach ($routes['types'] as $pattern => $t) {
    $regex = '#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '$#';
    if (preg_match($regex, $uri)) $type = $t;
}

// ----------------------
// Render view
// ----------------------
if (file_exists($viewFile)) {

    if ($type === 'html') {
        // Corrected paths to use existing 'includes' directory
        include __DIR__ . '/views/includes/header.php';
        include $viewFile;
        include __DIR__ . '/views/includes/footer.php';
    } elseif ($type === 'json') {
        header('Content-Type: application/json');
        include $viewFile; // this view should echo JSON
    } else {
        include $viewFile; // fallback
    }
} else {
    http_response_code(404);
    echo "<h1>404 - View not found</h1>";
}
