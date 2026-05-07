<?php
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}

session_start();
header('Content-Type: application/json');

require __DIR__ . '/../includes/db.php'; // provides $connect; dies on failure

$email = trim($_POST['txtUname'] ?? '');
$pass  = trim($_POST['txtPass']  ?? '');

if ($email === '' || $pass === '') {
    echo json_encode(['statusCode' => 201, 'message' => 'All fields required']);
    exit;
}

$stmt = $connect->prepare("
    SELECT id, name, email, user_pass
    FROM sj_users
    WHERE email = ? AND status = 'active'
");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$row    = $result->fetch_assoc();
$stmt->close();
$connect->close();

if (!$row) {
    echo json_encode(['statusCode' => 203, 'message' => 'User not found']);
    exit;
}

if (!password_verify($pass, $row['user_pass'])) {
    echo json_encode(['statusCode' => 202, 'message' => 'Invalid password']);
    exit;
}

$_SESSION['disp_name'] = $row['name'];
$_SESSION['email']     = $row['email'];
$_SESSION['user_id']   = $row['id'];
$_SESSION['logged']    = true;

echo json_encode(['statusCode' => 200]);
