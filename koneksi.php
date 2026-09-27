<?php
$host = 'localhost';
$db   = 'library';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$db", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage() . '<br>');
}
?>