<?php
header("Content-Type: application/json");
session_start();

/* Unset all session variables */
$_SESSION = [];

/* Delete session cookie */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* Destroy session */
session_destroy();

http_response_code(200);
echo json_encode([
    "status" => "success",
    "message" => "Logged out successfully"
]);
