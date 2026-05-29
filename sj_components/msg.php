<?php
header('Content-Type: application/json');

date_default_timezone_set('Asia/Manila');

// Sanitize inputs first (length limits prevent oversized payloads)
$txtName     = substr(trim($_POST['txtName']     ?? ''), 0, 100);
$txtEmail    = substr(trim($_POST['txtEmail']    ?? ''), 0, 150);
$txtPhoneNum = substr(trim($_POST['txtPhoneNum'] ?? ''), 0, 20);
$txtMessage  = substr(trim($_POST['txtMessage']  ?? ''), 0, 1000);

// 1. FIELD VALIDATION — always runs first so real users always get proper errors
if ($txtName === "" || $txtEmail === "" || $txtPhoneNum === "" || $txtMessage === "") {
    echo json_encode(["statusCode" => 201]);
    exit;
}
if (!filter_var($txtEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["statusCode" => 202, "message" => "Invalid email"]);
    exit;
}

// 2. HONEYPOT — bots auto-fill all fields including hidden ones; humans never see it
if (!empty($_POST['website'])) {
    echo json_encode(["statusCode" => 200]); // fake success to confuse bots
    exit;
}

// 3. TIME TOKEN — bots submit in milliseconds; real users take time to type
$form_token = (int)($_POST['form_token'] ?? 0);
if ($form_token === 0 || (time() - $form_token) < 3) {
    echo json_encode(["statusCode" => 200]); // fake success
    exit;
}

// 4. IP RATE LIMITING — max 1 real submission per 10 minutes per IP
$ip        = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$rate_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sj_rl_' . md5($ip) . '.txt';
if (file_exists($rate_file) && (time() - (int)file_get_contents($rate_file)) < 600) {
    echo json_encode(["statusCode" => 429]);
    exit;
}

// Database connection
$connect = new mysqli('localhost', 'seajetin_admindb', '@DminDATABASE!23', 'seajetin_admin_acct');
if ($connect->connect_error) {
    echo json_encode(["statusCode" => 500, "message" => "Database connection failed"]);
    exit;
}

$type      = 'new';
$date_time = date('Y-m-d H:i:s');

$stmt = $connect->prepare("
    INSERT INTO sj_messages (cli_name, cli_email, cli_num, cli_message, date_time, type)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("ssssss", $txtName, $txtEmail, $txtPhoneNum, $txtMessage, $date_time, $type);
$stmt->execute();
$stmt->close();

file_put_contents($rate_file, time());

echo json_encode(["statusCode" => 200]);

$connect->close();
