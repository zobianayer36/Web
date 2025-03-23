<?php
session_start();
require 'db.php';

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION["username"]; ?>!</h2>

    <h3>Available Products</h3>
    <div>
        <?php foreach ($products as $product): ?>
            <div style="border: 1px solid black; padding: 10px; margin: 10px; width: 250px;">
                <?php
                if (!empty($product['pic'])) {
                    $imageData = base64_encode($product['pic']);
                    echo "<img src='data:image/jpeg;base64,{$imageData}' alt='{$product['name']}' width='200'>";
                } else {
                    echo "<p>No Image</p>";
                }
                ?>
                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                <p>Price: $<?php echo htmlspecialchars($product['price']); ?></p>
                <p><?php echo htmlspecialchars($product['description']); ?></p>

                <!-- Add to Cart Button -->
                <form method="POST" action="add_to_cart.php">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <a href="cart.php">View Cart</a> | <a href="logout.php">Logout</a>
</body>
</html>
