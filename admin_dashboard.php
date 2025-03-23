<?php
session_start();
require 'db.php';

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: Adminlogin.php");
    exit();
}

// Handle Product Deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_product_id"])) {
    $deleteProductId = $_POST["delete_product_id"];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$deleteProductId]);
    echo "<p style='color: red;'>Product has been successfully deleted.</p>";
}

// Handle Product Addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["name"])) {
    $name = $_POST["name"];
    $price = $_POST["price"];
    $description = $_POST["description"];

    if (!empty($_FILES["pic"]["tmp_name"])) {
        $imageData = file_get_contents($_FILES["pic"]["tmp_name"]);
    } else {
        $imageData = null;
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, price, description, pic) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $price, $description, $imageData]);

    echo "<p style='color: green;'>New product added successfully!</p>";
}

// Fetch All Products
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();

// Fetch All Orders with Product Images
$stmt = $pdo->query("SELECT orders.id, users.username, products.name, products.pic, orders.address 
    FROM orders 
    JOIN users ON orders.user_id = users.user_id 
    JOIN products ON orders.product_id = products.id");
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION["admin_username"]; ?>!</h2>
    <p>You are now viewing the admin dashboard.</p>

    <!-- Form to Add Product -->
    <h3>Add New Product</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required><br>
        <input type="number" name="price" placeholder="Price" required><br>
        <textarea name="description" placeholder="Description" required></textarea><br>
        <input type="file" name="pic" accept="image/*" required><br>
        <button type="submit">Add Product</button>
    </form>

    <!-- Display All Products -->
    <h3>All Products</h3>
    <div>
        <?php foreach ($products as $product): ?>
            <div style="border: 1px solid black; padding: 10px; margin: 10px; width: 250px;">
                <?php
                if (!empty($product['pic'])) {
                    $imageData = base64_encode($product['pic']);
                    $src = 'data:image/jpeg;base64,' . $imageData;
                    echo "<img src='$src' alt='Product Image: {$product['name']}' width='200'>";
                } else {
                    echo "<p>No Image Available</p>";
                }
                ?>
                <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                <p>Price: $<?php echo htmlspecialchars($product['price']); ?></p>
                <p><?php echo htmlspecialchars($product['description']); ?></p>

                <!-- Delete Product Button -->
                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                    <input type="hidden" name="delete_product_id" value="<?php echo $product['id']; ?>">
                    <button type="submit">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Display All Orders -->
    <h3>All Orders</h3>
    <div>
        <?php foreach ($orders as $order): ?>
            <div style="border: 1px solid black; padding: 10px; margin: 10px; width: 300px;">
                <p><strong>User:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                <p><strong>Product:</strong> <?php echo htmlspecialchars($order['name']); ?></p>
                
                <!-- Display Product Image -->
                <?php if (!empty($order['pic'])): ?>
                    <?php 
                        $imageData = base64_encode($order['pic']);
                        $src = 'data:image/jpeg;base64,' . $imageData;
                    ?>
                    <img src="<?php echo $src; ?>" alt="Ordered Product Image" width="200">
                <?php else: ?>
                    <p>No Image Available</p>
                <?php endif; ?>

                <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Admin Logout -->
    <a href="admin_logout.php" style="display: block; margin-top: 20px;">Logout</a>
</body>
</html>