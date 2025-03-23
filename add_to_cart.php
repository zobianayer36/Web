<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["product_id"])) {
    $user_id = $_SESSION["user_id"];
    $product_id = $_POST["product_id"];

    // Insert into cart
    $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $product_id]);

    header("Location: cart.php");
    exit();
}
?>
