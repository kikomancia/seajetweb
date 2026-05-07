<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

$id      = (int)($_POST['id'] ?? 0);
$status  = trim($_POST['status'] ?? '');
$allowed = ['unread', 'read', 'hidden'];

if ($id <= 0 || !in_array($status, $allowed, true)) {
    echo json_encode(['statusCode' => 400, 'message' => 'Invalid input']);
    exit;
}

$stmt = $connect->prepare("UPDATE sj_messages SET status = ? WHERE id = ?");
$stmt->bind_param('si', $status, $id);
$stmt->execute();
$stmt->close();
$connect->close();

echo json_encode(['statusCode' => 200]);
