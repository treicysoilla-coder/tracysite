<?php
include 'db.php'; // Ensure this matches your connection file name
session_start();

$message = "";
$error = "";

if (isset($_POST['reset_request'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Verify both Username and Email match the same account
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' AND email = '$email'");
    
    if (mysqli_num_rows($check) > 0) {
        // Create a unique token
        $token = bin2hex(random_bytes(16));
        
        // Save token to database
        mysqli_query($conn, "UPDATE users SET reset_token = '$token' WHERE username = '$username'");
        
        $message = "Request Verified! <a href='reset_password.php?token=$token' style='color:#0066FF; font-weight:bold;'>Click here to Reset Password</a>";
    } else {
        $error = "Username and Email do not match our records.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>FastCar | Password Recovery</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { background: #0f0f0f; font-family: 'Montserrat', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: white; }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); width: 350px; text-align: center; }
        input { width: 100%; padding: 14px; margin: 10px 0; background: rgba(0,0,0,0.5); border: 1px solid #333; border-radius: 10px; color: white; box-sizing: border-box; outline: none; }
        button { width: 100%; padding: 14px; background: #0066FF; border: none; border-radius: 10px; color: white; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .msg { font-size: 13px; padding: 10px; border-radius: 8px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="glass-card">
        <h2 style="color: #0066FF;">Recovery</h2>
        <p style="font-size: 12px; color: #888; margin-bottom: 20px;">Enter details to verify your account</p>
        
        <?php if($message): ?>
            <div class="msg" style="background: rgba(0,102,255,0.1); border: 1px solid #0066FF;"><?= $message ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="msg" style="background: rgba(231,76,60,0.1); border: 1px solid #e74c3c; color: #e74c3c;"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Your Username" required>
            <input type="email" name="email" placeholder="Registered Email" required>
            <button type="submit" name="reset_request">VERIFY ACCOUNT</button>
        </form>
        <br>
        <a href="login.php" style="color: #555; font-size: 12px; text-decoration: none;">Return to Login</a>
    </div>
</body>
</html>