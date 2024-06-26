<?php

require_once './autoload.php';
require_once './views/includes/header.php';

/* Show errors */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$home = new HomeController();


$pages = ['home']; 
if(isset($_GET['page'])){
    if(in_array($_GET['page'],$pages)){
        $page = $_GET['page'];
        $home->index($page);
    }else{
        include('views/includes/404.php');
    }
}else{
    $home->index('home');
}
?>

<?php
require_once './views/includes/footer.php';
?>