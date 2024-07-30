<?php

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        echo "Passwords do not match.";
        exit;
    }

    $stmt = $conn->prepare("SELECT email FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email);
        $stmt->fetch();
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);
        $stmt->execute();

        echo "Password has been reset successfully.";
    } else {
        echo "Invalid token.";
    }

    $stmt->close();
    $conn->close();
}