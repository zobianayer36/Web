<?php
$host = "localhost"; // Change if needed
$dbname = "talha_mart"; // Change to your database name
$username = "root"; // Change if needed
$password = ""; // Change if needed

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
