<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $phone = $_POST["phone"];
    $gender = $_POST["gender"];
    $address = $_POST["address"];
    $verification_token = bin2hex(random_bytes(32));

    // **Check if email already exists**
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo "Error: This email is already registered. Please use a different email.";
    } else {
        // Insert new user if email is not found
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, phone, gender, address, verification_token) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $password, $phone, $gender, $address, $verification_token])) {
            sendVerificationEmail($email, $verification_token);
            echo "Registration successful! Please check your email to verify your account.";
        } else {
            echo "Error registering user.";
        }
    }
}

// Function to send email via PHPMailer
function sendVerificationEmail($email, $token) {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use Gmail SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 't2solutionsofficials@gmail.com'; // Replace with your Gmail
        $mail->Password = 'vgww yjdq dubx czio'; // Replace with your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email Settings
        $mail->setFrom('your_email@gmail.com', 'Your Website');
        $mail->addAddress($email);
        $mail->Subject = 'Verify Your Email';
        $verificationLink = "http://localhost/Website/verify.php?token=" . $token;
        $mail->Body = "Click the following link to verify your email: $verificationLink";

        $mail->send();
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <input type="text" name="phone" placeholder="Phone"><br>
    <select name="gender" required>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
    </select><br>
    <textarea name="address" placeholder="Address"></textarea><br>
    <button type="submit">Register</button>
</form>
