<?php
$dns = "mysql";
$host = "localhost";
$dbname = "face";
$user = 'root';
$pass = '';

try {
    $dbh = new PDO("$dns:host=$host;dbname=$dbname", $user, $pass);
} catch (PDOException $e) {
    print "Ошибка: " . $e->getMessage() . "<br/>";
    die();
}