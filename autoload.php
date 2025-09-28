<?php
require_once "./bootstrap.php";
foreach (glob(__DIR__ . '/functions/*.php') as $file) {
    require_once $file;
}
spl_autoload_register('autoload');

function autoload($class_name)
{
    $array_paths = array(
        'database/',
        'app/classes/',
        'models/',
        'controllers/',
        'widgets/',
    );

    $parts = explode('\\', $class_name);
    $name = array_pop($parts);

    foreach ($array_paths as $path) {
        $file = sprintf($path . '%s.php', $name);
        if (is_file(($file))) {
            include_once $file;
        }
    }
}
