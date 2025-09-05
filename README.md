# PHP MVC Template

This repository provides a straightforward PHP MVC (Model-View-Controller) template, designed for developers who want a clear and maintainable structure for their web applications. It emphasizes separation of concerns, making the codebase easier to understand, manage, and scale.,,

## 1. Download

Clone the repository to get started:

```bash
git clone https://github.com/OthmaneRegragui/php_mvc_template
```

## 2. Project Structure Overview

The template organizes your application into logical directories:

*   **`app/`**: Contains core application classes like `Session` and `Redirect`.
    *   `app/classes/Session.php`
    *   `app/classes/Redirect.php`
*   **`config/`**: Holds configuration files, primarily `routes.php`.
    *   `config/routes.php`
*   **`controllers/`**: Where your application logic resides, handling user input and interacting with models.
    *   `controllers/HomeController.php`
*   **`database/`**: Contains classes for database connection and CRUD operations.
    *   `database/DB.php`
    *   `database/CRUD.php`
*   **`models/`**: Represents your application's data structure and business logic, interacting with the database.
    *   `models/User.php`
*   **`views/`**: Contains presentation logic (HTML templates) that display data to the user.
    *   `views/home.php`
    *   `views/api/test.php`
    *   `views/includes/header.php`
    *   `views/includes/footer.php`
    *   `views/includes/404.php`
*   **`assets/`**: For static assets like CSS, JavaScript, and images.
    *   `assets/css/main.css`
*   **`.htaccess`**: Apache configuration for URL rewriting.
*   **`autoload.php`**: Handles automatic class loading.
*   **`bootstrap.php`**: Defines global constants and initial settings.
*   **`index.php`**: The single entry point for all requests, responsible for routing and dispatching.

## 3. Configuration (`./bootstrap.php`)

The `bootstrap.php` file is crucial for setting up global constants for your application. You **must** modify the database variables to match your environment.

```php
<?php

define('BASE_URL', 'http://localhost/Projects/php_mvc_template/'); // Your application's base URL
define('host', 'localhost');      // Database host
define('dbname', 'database_name'); // Your database name
define('dbuser', 'root');         // Database username
define('dbpassword', 'password'); // Database password
```

## 4. Autoloading Classes (`./autoload.php`)

The `autoload.php` file sets up automatic class loading using `spl_autoload_register()`.,,, This function registers a custom autoloader that automatically includes class files when they are first used, eliminating the need for manual `require` or `include` statements for each class.,

```php
<?php
session_start(); // Starts the session, making $_SESSION superglobal available
require_once "./bootstrap.php"; // Includes the base configuration

spl_autoload_register('autoload'); // Registers the 'autoload' function

function autoload($class_name)
{
    // Define an array of paths where class files might be located
    $array_paths = array(
        'database/',
        'app/classes',
        'models/',
        'controllers/'
    );

    // Extract the class name without namespaces (if any)
    $parts = explode('\\', $class_name);
    $name = array_pop($parts);

    // Iterate through the defined paths to find and include the class file
    foreach ($array_paths as $path) {
        $file = sprintf($path . '%s.php', $name);
        if (is_file(($file))) {
            include_once $file;
        }
    }
}
```

This setup allows you to instantiate classes like `new User()` or `new CRUD()` without explicitly including `User.php` or `CRUD.php` at the top of every file.

## 5. Database Interaction (`./database/DB.php` and `./database/CRUD.php`)

### `DB.php` - Database Connection

This class provides a static method to establish a secure PDO (PHP Data Objects) connection to your MySQL database. PDO is used to interact with databases in a consistent and secure way, supporting prepared statements to prevent SQL injection.,,

```php
<?php
class DB{
    static public function connect(){
        // Creates a new PDO instance using constants defined in bootstrap.php
        $db= new PDO("mysql:host=".host.";dbname=".dbname,dbuser,dbpassword);
        $db->exec('set names utf8'); // Ensures proper UTF-8 character encoding
        $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING); // Sets error mode to warnings
        return $db;
    }
}
?>
```

### `CRUD.php` - Generic Database Operations

The `CRUD` class offers a set of generic methods to perform Create, Read, Update, and Delete operations on any database table. It leverages PDO prepared statements for security and efficiency.,,

*   **`__construct()`**: Initializes the `CRUD` object by establishing a database connection via `DB::connect()`.
*   **`insert(string $table, array $data)`**: Inserts a new row into the specified table. It takes the table name and an associative array of column-value pairs. Returns the last inserted ID or `false` on failure.
*   **`select(string $table, array $conditions = [], array $columns = ['*'], string $orderBy = '', string $limit = '', bool $singleResult = false)`**: Retrieves data from a table. It supports conditions (WHERE clause), specific columns, ordering, limiting results, and fetching a single row or multiple rows.
*   **`update(string $table, array $data, array $conditions)`**: Updates existing rows in a table. It requires the table name, an associative array of new data, and an associative array for the WHERE clause to specify which rows to update.
*   **`delete(string $table, array $conditions)`**: Deletes rows from a table based on specified conditions. It includes a safety check to prevent accidental full-table deletion.
*   **`query(string $sql, array $params = [])`**: Executes a raw SQL query. This method should be used with caution for complex queries not covered by the other methods, ensuring parameters are bound for security.

Each method includes error logging for easier debugging.

## 6. User Management (`./models/User.php`)

The `User` model demonstrates how to interact with the database using the `CRUD` class for specific entity operations.

```php
<?php

class User
{
    static public function getUser($id)
    {
        $crud = new CRUD();
        // Selects a single user from the 'users' table by ID
        $user = $crud->select('users', ['id' => $id], [], '', '1', true);
        return $user;
    }

    static public function createUser($username, $email, $password)
    {
        $crud = new CRUD();
        $data = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT) // Hashes the password before storing
        ];
        // Inserts the new user data into the 'users' table
        return $crud->insert('users', $data);
    }
}
```

*   **`getUser($id)`**: Fetches a single user record from the 'users' table based on their ID.
*   **`createUser($username, $email, $password)`**: Creates a new user record. **Important**: It uses `password_hash()` to securely store passwords, which is a critical security practice.

## 7. Session Management (`./app/classes/Session.php`)

The `Session` class provides a simple, static interface for managing user sessions. Sessions allow data to be stored and accessed across multiple pages on a website, maintaining user state.,,,,

```php
<?php

class Session {
    public static function start() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start(); // Starts a new session or resumes an existing one
        }
    }

    public static function set($name, $value) {
        $_SESSION[$name] = $value; // Sets a session variable
    }

    public static function get($name) {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null; // Retrieves a session variable
    }

    public static function exists($name) {
        return isset($_SESSION[$name]); // Checks if a session variable is set
    }

    public static function delete($name) {
        if (self::exists($name)) {
            unset($_SESSION[$name]); // Removes a specific session variable
        }
    }

    public static function destroy() {
        session_unset();   // Unsets all session variables
        session_destroy(); // Destroys the entire session
    }
}
```

## 8. Redirection (`./app/classes/Redirect.php`)

The `Redirect` class offers convenient static methods to redirect users to different pages within or outside your application.,,,

```php
<?php

class Redirect {
    public static function to($location) {
        if (!headers_sent()) {
            header('Location: ' . $location); // Performs HTTP header redirect
            exit(); // Essential to stop script execution after redirect
        } else {
            // Fallback for when headers have already been sent (e.g., due to accidental output)
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $location . '";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="0;url=' . $location . '" />';
            echo '</noscript>';
            exit();
        }
    }

    public static function back() {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            self::to($_SERVER['HTTP_REFERER']); // Redirects to the previous page
        } else {
            self::to(BASE_URL); // Redirects to the base URL if referer is not available
        }
    }
}
```

It handles both standard HTTP header redirects and a JavaScript/meta refresh fallback if headers have already been sent.

## 9. Routing and Core Logic (`./.htaccess`, `./index.php`, `./config/routes.php`)

This section explains how requests are processed, routed, and handled by the application.,,,,

### `.htaccess` - URL Rewriting

The `.htaccess` file uses Apache's `mod_rewrite` module to ensure that all incoming requests are routed through `index.php`. This is fundamental for a single-entry-point MVC architecture.,,,,,

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Automatically set RewriteBase to current folder
    RewriteCond %{REQUEST_URI}::%{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_URI}::%{REQUEST_FILENAME} !-d
    # If the request is not for an existing file or directory, redirect to index.php
    RewriteRule ^ index.php [QSA,L]
</IfModule>
```

*   `RewriteEngine On`: Activates the rewrite engine.
*   `RewriteCond %{REQUEST_URI}::%{REQUEST_FILENAME} !-f`: Condition: The requested URI is not an existing file.
*   `RewriteCond %{REQUEST_URI}::%{REQUEST_FILENAME} !-d`: Condition: The requested URI is not an existing directory.
*   `RewriteRule ^ index.php [QSA,L]`: Rule: If the above conditions are met, rewrite the request to `index.php`.
    *   `^`: Matches any URI.
    *   `index.php`: The internal path to rewrite to.
    *   `[QSA]`: Appends the query string from the original request.
    *   `[L]`: Last rule, stops processing further rewrite rules.

### `index.php` - Front Controller

`index.php` is the application's front controller. It's the first script executed for every request and is responsible for:

1.  **Error Reporting**: Configures PHP to display all errors during development.
2.  **Includes**: Loads `bootstrap.php` (for constants) and `autoload.php` (for class loading).
3.  **Route Loading**: Includes `config/routes.php` to get defined routing rules.
4.  **URI Parsing**: Extracts the relevant part of the URL (the URI) to determine the requested page or resource.
5.  **Blocked Folders**: Checks if the requested URI matches any blocked folders defined in `routes.php` (e.g., `includes`, `assets`) and returns a 403 Forbidden error if it does.
6.  **Custom Rules Execution**: Iterates through custom rules defined in `routes.php`. These rules act like middleware, allowing you to execute specific logic (e.g., authentication checks) before rendering a view.
7.  **Content Type Determination**: Based on patterns in `routes.php`, it determines the expected content type (e.g., `html`, `json`).
8.  **View Rendering**:
    *   If the content type is `html`, it includes `views/includes/header.php`, the specific view file (`views/{$uri}.php`), and then `views/includes/footer.php`.
    *   If the content type is `json` (e.g., for API endpoints), it sets the `Content-Type` header to `application/json` and includes the view file.
    *   For any other type, it simply includes the view file.
9.  **404 Handling**: If no corresponding view file is found, it returns a 404 Not Found error.

### `config/routes.php` - Route Configuration

This file defines application-wide routing logic, including blocked paths, content type mappings, and middleware-like rules.

```php
<?php
return [

    // Blocked folders: Requests to URIs starting with these will return a 403 Forbidden error.
    'blocked' => [
        'includes', // Prevents direct access to 'views/includes/'
        'assets'    // Prevents direct access to 'assets/'
    ],

    // Folder / file type configuration: Maps URL patterns to content types.
    'types' => [
        'api/*' => 'json',   // All routes starting with 'api/' will be treated as JSON responses
    ],

    // Custom rules per folder or file: Functions executed before rendering a view.
    // Useful for authentication, authorization, logging, etc.
    'rules' => [
        // Example: Protects dashboard routes, redirecting to home if user is not logged in.
        'dashboard/*' => function () {
            Session::start(); // Ensure session is started to check for user
            if (!Session::exists('user')) {
                Redirect::to(BASE_URL); // Redirect to home if 'user' session variable is not set
            }
        },
        // Example: Protects admin dashboard routes, requiring 'admin' role.
        'dashboard/admin/*' => function () {
            Session::start(); // Ensure session is started
            if (!Session::exists('user') || Session::get('role') !== 'admin') {
                http_response_code(403);
                exit("403 - Admins only"); // Forbidden for non-admin users
            }
        },
    ],

];
```

## 10. Controllers (`./controllers/HomeController.php`)

Controllers are responsible for handling user requests, processing input, interacting with models, and selecting the appropriate view to render.,,,,,

```php
<?php
class HomeController{
    public function index($page){
        // In this simple setup, the controller directly includes the view.
        // In more complex applications, data would be fetched from a model
        // and passed to the view.
        include('views/'.$page.'.php');
    }
}
?>
```

The `HomeController` is a basic example. In a full application, controllers would contain methods (actions) that correspond to specific routes (e.g., `showUser`, `editPost`).

## 11. Views (`./views/` directory)

Views are responsible for presenting data to the user, typically as HTML.,,,

*   **`views/home.php`**: An example of a simple HTML view.
*   **`views/api/test.php`**: Demonstrates how to output JSON for API endpoints. Because `routes.php` defines `api/*` as `json` type, `index.php` will set the `Content-Type: application/json` header before including this file, ensuring proper API response formatting.
*   **`views/includes/header.php` / `views/includes/footer.php`**: These files contain common HTML structure (like `<!DOCTYPE html>`, `<head>`, `<body>` tags, and shared navigation/scripts) that are included in every HTML view by `index.php`. This promotes consistency and reusability.
*   **`views/includes/404.php`**: A placeholder for a custom 404 Not Found page, though `index.php` currently just echoes a simple message.

## 12. Assets (`./assets/css/main.css`)

The `assets/` directory is intended for static files like CSS, JavaScript, images, and fonts.

*   **`assets/css/main.css`**: An empty CSS file, ready for your styles. The `header.php` includes Tailwind CSS and jQuery via CDN, so local assets would be included separately if needed.
