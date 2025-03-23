<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Fetch user's cart items
$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$cart_items = $stmt->fetchAll();

// If no items in cart, redirect to cart page
if (count($cart_items) === 0) {
    header("Location: cart.php");
    exit();
}

// Initialize error message
$error_message = "";

// Handle payment submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if form fields are set
    $cvc = isset($_POST["cvc"]) ? trim($_POST["cvc"]) : "";
    $expiry = isset($_POST["expiry"]) ? trim($_POST["expiry"]) : "";
    $card_number = isset($_POST["card_number"]) ? trim($_POST["card_number"]) : "";
    $address = isset($_POST["address"]) ? trim($_POST["address"]) : "";

    // Validate card number (must be exactly 16 digits)
    if (!preg_match('/^\d{16}$/', $card_number)) {
        $error_message = "Invalid card number! It must be exactly 16 digits.";
    } elseif (!preg_match('/^\d{3}$/', $cvc)) {
        $error_message = "Invalid CVC! It must be exactly 3 digits.";
    } elseif (!preg_match('/^\d{2}\/\d{2}$/', $expiry)) {
        $error_message = "Invalid expiry date format! Use MM/YY.";
    } elseif (empty($address)) {
        $error_message = "Address cannot be empty!";
    } else {
        // Insert each cart item into the orders table with the user's address
        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, product_id, address) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION["user_id"], $item["product_id"], $address]);
        }

        // Clear the cart after successful payment
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$_SESSION["user_id"]]);

        // Redirect to success page
        header("Location: order_success.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
</head>
<body>
    <h2>Enter Payment Details</h2>

    <?php if (!empty($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Card Number (16 digits):</label>
        <input type="text" name="card_number" maxlength="16" required pattern="\d{16}"><br>

        <label>Expiry Date (MM/YY):</label>
        <input type="text" name="expiry" required pattern="\d{2}/\d{2}"><br>

        <label>CVC (3 digits):</label>
        <input type="text" name="cvc" maxlength="3" required pattern="\d{3}"><br>

        <label>Enter Your Address:</label>
        <textarea name="address" required></textarea><br>

        <button type="submit">Done</button>
    </form>
</body>
</html>
