# php_mvc_template
A PHP MVC Template :) .

# 1 Download : 
```
git clone https://github.com/OthmaneRegragui/php_mvc_template
```

# 2 Edit bootstrap.php file to add or modify any const variables Specially DATA BASE VARIABLES!!!
```
<?php
define('BASE_URL','http://localhost/Projects/php_mvc_template/');
define('host','localhost');
define('dbname','database_name');
define('dbuser','root');
define('dbpassword','password');

?>
```

# 3 autoload.php to make sure include database file / models / controllers ....
```
<?php
session_start();
require_once "./bootstrap.php";

spl_autoload_register('autoload');

function autoload($class_name){
    $array_paths = array(
        'database/',
        'app/classes',
        'models/',
        'controllers/'
    );

    $parts = explode('\\',$class_name);
    $name = array_pop($parts);

    foreach($array_paths as $path){
        $file = sprintf($path.'%s.php',$name); 
        if(is_file(($file))){
            include_once $file;
        }
    }
}
?>
```
