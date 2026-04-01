<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Connection Safety
require_once('db.php'); 
if (!isset($conn)) {
    // Fallback if db.php doesn't define $conn correctly
    $conn = mysqli_connect("localhost", "root", "", "car_rental_db");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // 2. Fetch User Data
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // 3. Verify Password
        if (password_verify($password, $row['password'])) {
            
            // 4. CRITICAL: SET SESSIONS CORRECTLY
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            
            // This MUST be 'role' to match the check in cars.php
            $_SESSION['role'] = strtolower($row['role']); 

            // 5. Redirect to Dashboard
            header("Location: Dashboard.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Invalid username or password.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Login | FastCar</title>
    <style>
        body { background: #0f0f0f; display: flex; justify-content: center; align-items: center; height: 100vh; color: #fff; font-family: 'Montserrat', sans-serif; margin: 0; }
        .login-card { background: rgba(255,255,255,0.03); backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; width: 350px; text-align: center; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        h2 { margin-bottom: 25px; color: #0066FF; letter-spacing: 1px; }
        input { width: 100%; padding: 14px; margin: 12px 0; background: rgba(0,0,0,0.5); border: 1px solid #333; color: white; border-radius: 10px; box-sizing: border-box; outline: none; transition: 0.3s; }
        input:focus { border-color: #0066FF; box-shadow: 0 0 10px rgba(0,102,255,0.2); }
        
        /* Forgot Password Styling */
        .forgot-container { text-align: right; margin-top: -5px; margin-bottom: 10px; }
        .forgot-link { color: #555; font-size: 11px; text-decoration: none; transition: 0.3s; }
        .forgot-link:hover { color: #0066FF; }

        button { width: 100%; padding: 14px; background: #0066FF; border: none; color: white; border-radius: 10px; cursor: pointer; font-weight: bold; margin-top: 10px; font-size: 16px; transition: 0.3s; }
        button:hover { background: #0052cc; transform: translateY(-2px); }
        .error { background: rgba(231, 76, 60, 0.1); color: #e74c3c; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 0.85rem; border: 1px solid #e74c3c; }
    </style>
</head>
<body>

<div class="login-card">
    <div style="font-size: 40px; margin-bottom: 10px;">🚗</div>
    <h2>FASTCAR LOGIN</h2>
    
    <?php if($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required autocomplete="off">
        <input type="password" name="password" placeholder="Password" required>
        
        <div class="forgot-container">
            <a href="forgot_password.php" class="forgot-link">Forgot Password?</a>
        </div>

        <button type="submit">Unlock Dashboard</button>
    </form>
    
    <p style="font-size: 0.75rem; color: #555; margin-top: 25px; text-transform: uppercase; letter-spacing: 1px;">Secure Staff Portal</p>
</div>

</body>
</html>