<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join FastCar Rental | Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            margin: 0; padding: 0; font-family: 'Montserrat', sans-serif; height: 100vh; 
            display: flex; justify-content: center; align-items: center; 
            background: url('mazda.jpg') no-repeat center center fixed; background-size: cover;
            overflow: hidden;
        }
        body::before {
            content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6); z-index: 1;
        }
        .signup-card { 
            position: relative; z-index: 2; background: rgba(255, 255, 255, 0.08); 
            padding: 40px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.15); 
            width: 420px; backdrop-filter: blur(20px); box-shadow: 0 25px 50px rgba(0, 0, 0, 0.6);
            text-align: center;
        }
        h2 { color: #fff; margin-bottom: 5px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; }
        h2 span { color: #ff6600; }
        .subtitle { color: #ccc; font-size: 13px; margin-bottom: 25px; display: block; }
        .input-row { display: flex; gap: 10px; margin-bottom: 15px; }
        .input-group { text-align: left; width: 100%; }
        input, select { 
            width: 100%; padding: 12px 15px; border-radius: 10px; border: 1px solid rgba(255, 255, 255, 0.1); 
            background: rgba(0, 0, 0, 0.4); color: #fff; font-size: 14px; outline: none; transition: 0.3s; box-sizing: border-box;
        }
        input:focus, select:focus { border-color: #ff6600; background: rgba(0, 0, 0, 0.6); box-shadow: 0 0 15px rgba(255, 102, 0, 0.3); }
        .hint { color: #aaa; font-size: 10px; margin-top: 5px; margin-bottom: 15px; }
        button { 
            width: 100%; padding: 15px; border: none; border-radius: 10px; 
            background: linear-gradient(45deg, #ff6600, #ff4500); color: #fff; 
            font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.4s ease; text-transform: uppercase;
        }
        button:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(255, 102, 0, 0.4); }
        .login-link { color: #bbb; display: block; margin-top: 20px; text-decoration: none; font-size: 13px; }
        .login-link span { color: #ff6600; font-weight: 700; }
    </style>
</head>
<body>
    <div class="signup-card">
        <h2>FAST<span>CAR</span></h2>
        <span class="subtitle">Elevate your journey. Join us today.</span>
        
        <form action="process_signup.php" method="POST" onsubmit="return validatePasswords()">
            <div class="input-row">
                <div class="input-group"><input type="text" name="full_name" placeholder="Full Name" required></div>
                <div class="input-group"><input type="text" name="username" placeholder="Username" required></div>
            </div>
            
            <div style="margin-bottom: 15px;"><input type="email" name="email" placeholder="Email Address" required></div>
            
            <div class="input-row">
                <div class="input-group"><input type="password" id="pass" name="password" placeholder="Password" pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$" required></div>
                <div class="input-group"><input type="password" id="confirm_pass" name="confirm_password" placeholder="Confirm" required></div>
            </div>
            <p class="hint">* Min. 8 characters with letters & numbers.</p>
            
            <div style="margin-bottom: 20px;">
                <select name="role" required>
                    <option value="" disabled selected>Select Your Role</option>
                    <option value="client">Client</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            
            <button type="submit">Create Account</button>
        </form>
        <a href="login.php" class="login-link">Already a member? <span>Login here</span></a>
    </div>

    <script>
        function validatePasswords() {
            var pass = document.getElementById("pass").value;
            var confirm = document.getElementById("confirm_pass").value;
            if (pass !== confirm) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>