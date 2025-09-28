<?php

class Redirect
{
    // Redirect to a specific URL
    public static function to($location)
    {
        if (!headers_sent()) {
            header('Location: ' . $location);
            exit();
        } else {
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $location . '";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="0;url=' . $location . '" />';
            echo '</noscript>';
            exit();
        }
    }

    // Redirect to the previous page
    public static function back()
    {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            self::to($_SERVER['HTTP_REFERER']);
        } else {
            self::to(BASE_URL);
        }
    }

    public static function get_url($view_file_path = 'home.php', $params = [])
    {
        $view_file_path = preg_replace('/\.php$/', '', $view_file_path);
        $url = BASE_URL . $view_file_path;
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $url .= '&' . urlencode($key) . '=' . urlencode($value);
            }
        }
        return $url;
    }
}
