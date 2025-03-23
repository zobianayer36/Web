<?php
require 'db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE verification_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET email_verified = TRUE, verification_token = NULL WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);
        echo "Email verified successfully! <a href='login.php'>Login here</a>";
    } else {
        echo "Invalid verification link.";
    }
} else {
    echo "No verification token provided.";
}
?>
