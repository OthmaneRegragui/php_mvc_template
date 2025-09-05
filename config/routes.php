<?php
return [

    // Blocked folders
    'blocked' => [
        'includes',
        'assets'
    ],

    // Folder / file type configuration
    'types' => [
        'api/*' => 'json',   // Everything inside views/api/ is JSON type
    ],

    // Custom rules per folder or file
    'rules' => [
        'dashboard/*' => function () {
            if (!isset($_SESSION['user'])) {
                header("Location: /");
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
