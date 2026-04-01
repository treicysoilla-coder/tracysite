<?php
include 'db.php';

$token = $_GET['token'] ?? '';
$error = "";
$success = false;

if (isset($_POST['update_password'])) {
    $new_pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        $error = "Passwords do not match!";
    } else {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $update = mysqli_query($conn, "UPDATE users SET password = '$hashed_pass', reset_token = NULL WHERE reset_token = '$token'");
        
        if (mysqli_affected_rows($conn) > 0) { $success = true; } 
        else { $error = "Invalid or expired session."; }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>FastCar | New Password</title>
    <style>
        body { background: #0f0f0f; font-family: 'Montserrat', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: white; }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); width: 350px; text-align: center; }
        input { width: 100%; padding: 14px; margin: 10px 0; background: rgba(0,0,0,0.5); border: 1px solid #333; border-radius: 10px; color: white; box-sizing: border-box; }
        button { width: 100%; padding: 14px; background: #28a745; border: none; border-radius: 10px; color: white; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="glass-card">
        <h2 style="color: #28a745;">New Password</h2>
        <?php if($success): ?>
            <p style="color: #28a745; font-size: 13px;">Success! Password changed.</p>
            <a href="login.php" style="color: white; font-size: 14px;">Login Now</a>
        <?php else: ?>
            <?php if($error) echo "<p style='color:#e74c3c; font-size:12px;'>$error</p>"; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit" name="update_password">SAVE PASSWORD</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>