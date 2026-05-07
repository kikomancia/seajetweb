<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

$name     = trim($_POST['name']     ?? '');
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

if ($name === '' || $email === '' || $password === '') {
    echo json_encode(['statusCode' => 400, 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['statusCode' => 400, 'message' => 'Invalid email address']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['statusCode' => 400, 'message' => 'Password must be at least 8 characters']);
    exit;
}

// Check for duplicate email
$check = $connect->prepare("SELECT id FROM sj_users WHERE email = ?");
$check->bind_param('s', $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['statusCode' => 409, 'message' => 'An account with that email already exists']);
    $check->close();
    $connect->close();
    exit;
}
$check->close();

$hashed = password_hash($password, PASSWORD_DEFAULT);
$status = 'active';

$stmt = $connect->prepare("INSERT INTO sj_users (name, email, user_pass, status) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $name, $email, $hashed, $status);

if ($stmt->execute()) {
    echo json_encode(['statusCode' => 200, 'message' => 'User created successfully']);
} else {
    echo json_encode(['statusCode' => 500, 'message' => 'Failed to create user']);
}

$stmt->close();
$connect->close();
