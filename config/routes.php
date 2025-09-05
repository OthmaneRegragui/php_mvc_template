
<?php
return [

    // Blocked folders
    'blocked' => [
        'includes',
    ],

    'types' => [
        'api/*' => 'json',   // Everything inside views/api/ is JSON type
    ],

    // Custom rules per folder or file
    'rules' => [
        'assets/js/*.js' => function ($uri) {
            $filepath = __DIR__ . '/../' . $uri; // Construct the full path to the asset
            if (file_exists($filepath)) {
                header('Content-Type: application/javascript');
                readfile($filepath);
            } else {
                http_response_code(404);
                echo "File not found.";
            }
            exit;
        },
        'assets/css/*.css' => function ($uri) {
            $filepath = __DIR__ . '/../' . $uri; // Construct the full path to the asset
            if (file_exists($filepath)) {
                header('Content-Type: text/css');
                readfile($filepath);
            } else {
                http_response_code(404);
                echo "File not found.";
            }
            exit;
        },

        // This rule will only be hit if a more specific rule for .js or .css did not match,
        // effectively blocking direct access to other asset types that are not explicitly allowed.
        'assets/*' => function () {
            http_response_code(403);
            exit("<h1>403 Forbidden - Only .js and .css files are allowed in assets.</h1>");
        },

        'dashboard/*' => function () {
            if (!isset($_SESSION['user'])) {
                header("Location: " . BASE_URL); // Use BASE_URL for redirection
                exit;
            }
        },
        'dashboard/admin/*' => function () {
            if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
                http_response_code(403);
                exit("403 - Admins only");
            }
        },
    ],

];
