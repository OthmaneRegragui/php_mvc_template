<?php
class DB{
    static public function connect(){
        $db= new PDO("mysql:host=".host.";dbname=".dbname,dbuser,dbpassword);
        $db->exec('set names utf8');
        $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
        return $db;
    }
}
?>