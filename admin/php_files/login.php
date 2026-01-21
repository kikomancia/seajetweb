<?php
session_start();
header('Content-Type: application/json');

 require 'db_connection.php';

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

// Check user
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
        $_SESSION['disp_name'] = $row['name'];
        $_SESSION['email']   = $row['email'];
        $_SESSION['logged']  = true;

        echo json_encode(["statusCode" => 200]);
    } else {
        echo json_encode(["statusCode" => 202, "message" => "Invalid password"]);
    }
} else {
    echo json_encode(["statusCode" => 203, "message" => "User not found"]);
}

$stmt->close();
$connect->close();
