<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Fetch user's cart items with product details
$stmt = $pdo->prepare("SELECT cart.id AS cart_id, products.* FROM cart 
    JOIN products ON cart.product_id = products.id 
    WHERE cart.user_id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$cart_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
</head>
<body>
    <h2>Your Cart</h2>

    <?php if (count($cart_items) > 0): ?>
        <form method="POST" action="place_order.php">
            <label>Enter your Address:</label>
            <textarea name="address" required></textarea><br>

            <?php foreach ($cart_items as $item): ?>
                <div style="border: 1px solid black; padding: 10px; margin: 10px; width: 250px;">
                    <?php 
                    if (!empty($item['pic'])) {
                        $imageSrc = 'data:image/jpeg;base64,' . base64_encode($item['pic']);
                        echo "<img src='$imageSrc' alt='Product Image' width='200'><br>";
                    } else {
                        echo "<p>No Image</p>";
                    }
                    ?>
                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                    <p>Price: $<?php echo htmlspecialchars($item['price']); ?></p>

                    <!-- Remove from Cart -->
                    <form method="POST" action="remove_from_cart.php">
                        <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                        <button type="submit">Remove</button>
                    </form>
                </div>
            <?php endforeach; ?>

            <form method="POST" action="pay.php">
    <button type="submit">Pay</button>
</form>

        </form>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
