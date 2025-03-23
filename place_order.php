<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["address"])) {
    $user_id = $_SESSION["user_id"];
    $address = $_POST["address"];

    // Fetch user's cart items
    $stmt = $pdo->prepare("SELECT product_id FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();

    // Move to orders table
    foreach ($cart_items as $item) {
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, product_id, address) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $item['product_id'], $address]);
    }

    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);

    echo "Order placed successfully!";
    header("Location: dashboard.php");
    exit();
}
?>
