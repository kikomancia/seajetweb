<?php
header('Content-Type: application/json');

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Database connection
// $connect = new mysqli('localhost', 'root', '', 'sj_datab');
$connect = new mysqli('localhost', 'seajetin_admindb', '@DminDATABASE!23', 'seajetin_admin_acct');

if ($connect->connect_error) {
    echo json_encode(["statusCode" => 500, "message" => "Database connection failed"]);
    exit;
}

// Safely get POST values
$txtName        = trim($_POST['txtName'] ?? '');
$txtEmail       = trim($_POST['txtEmail'] ?? '');
$txtPhoneNum    = trim($_POST['txtPhoneNum'] ?? '');
$txtMessage     = trim($_POST['txtMessage'] ?? '');
$type           = 'new';
$date_time      = date('Y-m-d H:i:s');

// Basic validation
if ($txtName === "" || $txtEmail === "" || $txtPhoneNum === "" || $txtMessage === "") {
    echo json_encode(["statusCode" => 201]);
    exit;
} else if ((!filter_var($txtEmail, FILTER_VALIDATE_EMAIL))) {
    // ivalid email format
    echo json_encode(["statusCode" => 202, "message" => "Invalid email"]);
    exit;
} else {

    // success send data: Insert into database
    $stmt = $connect->prepare("
        INSERT INTO sj_messages (cli_name, cli_email, cli_num, cli_message, date_time, type)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssss", $txtName, $txtEmail, $txtPhoneNum, $txtMessage, $date_time, $type);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["statusCode" => 200]);
}


$connect->close();
