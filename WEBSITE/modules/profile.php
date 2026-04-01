<?php
$user_id = $_SESSION['user_id']; 

if (isset($_POST['update_profile'])) {
    $new_username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_password = $_POST['password'];
    $photo_query = "";

    // 1. Handle Photo Upload
    if (!empty($_FILES['profile_photo']['name'])) {
        $target_dir = "assets/profiles/";
        // Create folder if it doesn't exist
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        
        $file_extension = pathinfo($_FILES["profile_photo"]["name"], PATHINFO_EXTENSION);
        $file_name = "user_" . $user_id . "." . $file_extension;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["profile_photo"]["tmp_tmp_name"], $target_file)) {
            $photo_query = ", profile_photo = '$file_name'";
        }
    }

    // 2. Build Update Query
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET username = '$new_username', password = '$hashed_password' $photo_query WHERE id = '$user_id'";
    } else {
        $update_query = "UPDATE users SET username = '$new_username' $photo_query WHERE id = '$user_id'";
    }

    if (mysqli_query($conn, $update_query)) {
        $_SESSION['username'] = $new_username;
        echo "<script>alert('Profile updated!'); window.location.href='?page=profile';</script>";
    }
}

$user_res = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($user_res);
$current_photo = !empty($user['profile_photo']) ? "assets/profiles/" . $user['profile_photo'] : "assets/default_avatar.png";
?>

<div class="glass-card" style="max-width: 500px; margin: 0 auto; text-align: center;">
    <h2 style="margin-bottom: 20px;">Your <span style="color: #0066FF;">Profile</span></h2>
    
    <form method="POST" action="" enctype="multipart/form-data">
        <div style="margin-bottom: 20px;">
            <img src="<?= $current_photo ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #0066FF; margin-bottom: 10px;">
            <input type="file" name="profile_photo" style="font-size: 12px; color: #888;">
        </div>

        <div style="text-align: left;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; color: #888;">Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" 
                       style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #333; background: rgba(255,255,255,0.1); color: white;" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: #888;">New Password</label>
                <input type="password" name="password" placeholder="Leave blank to keep current" 
                       style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #333; background: rgba(255,255,255,0.1); color: white;">
            </div>
        </div>

        <button type="submit" name="update_profile" 
                style="width: 100%; padding: 12px; border: none; border-radius: 8px; background: #0066FF; color: white; font-weight: bold; cursor: pointer;">
            Update Profile
        </button>
    </form>
</div>