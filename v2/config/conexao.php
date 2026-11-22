<?php
$host     = 'localhost';
$port     = 3306;          // XAMPP: 3306 | MAMP: 8889
$dbname   = 'sistemaVet';
$username = 'root';
$password = '';            // XAMPP: vazia | MAMP: 'root'

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Não foi possível conectar ao banco de dados.");
}
