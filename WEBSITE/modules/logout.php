<?php
// Start the session to gain access to it
session_start();

// 1. Unset all session variables (User ID, Role, Username, etc.)
$_SESSION = array();

// 2. If it's desired to kill the session, also delete the session cookie.
// This is a professional step to ensure the browser clears the link to the server.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Finally, destroy the session.
session_destroy();

// 4. Redirect back to the login page immediately
header("Location: login.php");
exit();
?>