# PHP MVC Template

A simple and structured **PHP MVC (Model-View-Controller)** template designed for clarity, scalability, and maintainability. This project enforces clean separation of concerns between application layers — making it easy to extend and understand.

---

## 🚀 1. Getting Started

Clone the repository:
```bash
git clone https://github.com/OthmaneRegragui/php_mvc_template
cd php_mvc_template
```

---

## ⚙️ 2. Configuration (bootstrap.php)

All main environment constants are defined here:

```php
define('BASE_URL', 'http://localhost/Projects/php_mvc_template/');
define('host', 'localhost');
define('dbname', 'db_mvc');
define('dbuser', 'root');
define('dbpassword', '');
```

These constants are used globally for:
- Base URL resolution
- Database connection setup

---

## 🔄 3. Autoloading (autoload.php)

Handles class auto-loading from all main directories:
- `database/`
- `models/`
- `controllers/`
- `functions/`
- `widgets/`

```php
spl_autoload_register('autoload');

function autoload($class_name)
{
    $paths = [
        'database/',
        'app/classes/',
        'models/',
        'controllers/',
        'functions/',
        'widgets/',
    ];

    $parts = explode('\\', $class_name);
    $name = array_pop($parts);

    foreach ($paths as $path) {
        $file = sprintf($path . '%s.php', $name);
        if (is_file($file)) {
            include_once $file;
        }
    }
}
```

---

## 🗄️ 4. Database Layer

### `DB.php`
Handles PDO database connection:

```php
class DB {
    static public function connect() {
        $db = new PDO("mysql:host=".host.";dbname=".dbname, dbuser, dbpassword);
        $db->exec('set names utf8');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        return $db;
    }
}
```

### `CRUD.php`
Generic CRUD operations:
- `insert($table, $data)`
- `update($table, $data, $where)`
- `delete($table, $where)`
- `select($table, $conditions, ... )`

---

## 👤 5. Models (`models/User.php`)

Models define data logic. Example user model:

```php
class User {
    static public function getUser($id) {
        $crud = new CRUD();
        return $crud->select('users', ['id' => $id], [], '', '1', true);
    }

    static public function createUser($username, $email, $password) {
        $crud = new CRUD();
        $data = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ];
        return $crud->insert('users', $data);
    }
}
```

---

## 🔐 6. Sessions (`app/classes/Session.php`)

Simple session handler:

```php
Session::start();
Session::set('key', 'value');
Session::get('key');
Session::destroy();
```

---

## 🔁 7. Redirect (`app/classes/Redirect.php`)

Helper class for redirection:

```php
Redirect::to(BASE_URL . 'dashboard');
Redirect::back();
Redirect::get_url('home.php', ['id' => 3]);
```

Includes JS fallback if headers are already sent.

---

## 🧠 8. Controllers (`controllers/HomeController.php`)

Controllers handle app logic and render views.

```php
class HomeController {
    public function index($page) {
        include('views/' . $page . '.php');
    }
}
```

---

## 🧭 9. Routes (`config/routes.php`)

Defines route rules, blocked paths, and file serving.

```php
return [
    'blocked' => ['includes'],

    'types' => [
        'api/*' => 'json',
    ],

    'rules' => [
        // Serve JS files
        'assets/js/*.js' => function ($uri) {
            $file = __DIR__ . '/../' . $uri;
            if (file_exists($file)) {
                header('Content-Type: application/javascript');
                readfile($file);
            } else {
                http_response_code(404);
                echo "File not found.";
            }
            exit;
        },

        // Serve CSS files
        'assets/css/*.css' => function ($uri) {
            $file = __DIR__ . '/../' . $uri;
            if (file_exists($file)) {
                header('Content-Type: text/css');
                readfile($file);
            } else {
                http_response_code(404);
                echo "File not found.";
            }
            exit;
        },

        // Restrict other asset files
        'assets/*' => function () {
            http_response_code(403);
            exit("<h1>403 Forbidden - Only .js and .css files are allowed in assets.</h1>");
        },

        // Dashboard rules
        'dashboard/*' => function () {
            if (!isset($_SESSION['user'])) {
                header("Location: " . BASE_URL);
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
```

✅ Automatically serves `.js` and `.css`  
❌ Blocks other files in `/assets/`

---

## 🌐 10. Entry Point (`index.php`)

Handles all incoming requests:
- Starts session
- Loads configuration
- Applies route rules
- Renders views or returns 404

---

## 🖼️ 11. Views (`views/`)

Presentation files rendered by controllers.

### Example: `views/includes/header.php`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP MVC Template</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/main.css">
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</head>
<body>
<div class="container">
```

---

## 🧩 12. Widgets (`widgets/Widget.php`)

Widgets are **reusable UI components**.

### Base Class
```php
<?php
abstract class Widget {
    abstract public static function render();
}
```

### Example Widget
```php
<?php
class UserCard extends Widget {
    public static function render($user) {
        return "
        <div class='p-4 bg-white rounded shadow'>
            <h3>{$user->username}</h3>
            <p>{$user->email}</p>
        </div>
        ";
    }
}
```

Usage:
```php
require_once 'widgets/UserCard.php';
echo UserCard::render($user);
```

✅ Promotes modular design  
✅ Keeps views clean

---

## 🧰 13. Functions (`functions/tools.php`)

Reusable helper functions available globally.

```php
<?php
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}
```

Usage:
```php
echo formatDate('2025-09-28');
if (isLoggedIn()) echo "Welcome back!";
```

✅ Helps keep controllers/models DRY

---

## 🎨 14. Assets (`assets/`)

All CSS and JS files go here.

Example:
```css
/* main.css */
body {
    background-color: #fff;
}
```

---

## ✅ 15. Key Features

- MVC architecture  
- Autoloading  
- PDO Database layer  
- Route-based middleware  
- JSON API support  
- Widget system  
- Function helpers  
- Tailwind + jQuery integration  
- Automatic asset serving  

---

## 🧩 16. Extending

To add new features:
- Create controllers → `/controllers/`
- Add views → `/views/`
- Add models → `/models/`
- Create widgets → `/widgets/`
- Add helpers → `/functions/tools.php`

---