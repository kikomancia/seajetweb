<?php
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);

// Only secure cookies if HTTPS is actually used
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}

session_start(); // MUST be first executable code

header('Content-Type: application/json');

require 'db_connection.php';

// Check DB connection
if ($connect->connect_error) {
    echo json_encode(["statusCode" => 500, "message" => "Database error"]);
    exit;
}

// Get POST data
$email = trim($_POST['txtUname'] ?? '');
$pass  = trim($_POST['txtPass'] ?? '');

// Validation
if ($email === "" || $pass === "") {
    echo json_encode(["statusCode" => 201, "message" => "All fields required"]);
    exit;
}

// Prepare and execute statement
$stmt = $connect->prepare("
    SELECT id, name, email, user_pass 
    FROM sj_users 
    WHERE email = ? AND status = 'active'
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (password_verify($pass, $row['user_pass'])) {
        // Set session variables
        $_SESSION['disp_name'] = $row['name'];
        $_SESSION['email']     = $row['email'];
        $_SESSION['logged']    = true;

        // Return success for AJAX
        echo json_encode(["statusCode" => 200]);
        exit; // Make sure to stop execution here
    } else {
        echo json_encode(["statusCode" => 202, "message" => "Invalid password"]);
        exit;
    }
} else {
    echo json_encode(["statusCode" => 203, "message" => "User not found"]);
    exit;
}

// Close resources
$stmt->close();
$connect->close();
