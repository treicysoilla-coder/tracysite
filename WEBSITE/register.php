<?php
require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 1. Hash the password securely before saving
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 2. Insert into the database
    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'client')");
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        echo "Registration successful! You can now <a href='login.php'>Login</a>.";
    } else {
        echo "Error: This email might already be registered.";
    }
}
?>

<form method="POST" action="register.php">
    <label>Email:</label><input type="email" name="username" required>
    <label>Password:</label><input type="password" name="password" required>
    <button type="submit">Sign Up</button>
</form>