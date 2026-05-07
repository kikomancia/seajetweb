<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['statusCode' => 400, 'message' => 'Invalid user ID']);
    exit;
}

// Prevent deactivating your own account
if ($id === (int) $_SESSION['user_id']) {
    echo json_encode(['statusCode' => 403, 'message' => 'You cannot deactivate your own account']);
    exit;
}

// Get current status
$stmt = $connect->prepare("SELECT status FROM sj_users WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['statusCode' => 404, 'message' => 'User not found']);
    $connect->close();
    exit;
}

$newStatus = $row['status'] === 'active' ? 'inactive' : 'active';

$update = $connect->prepare("UPDATE sj_users SET status = ? WHERE id = ?");
$update->bind_param('si', $newStatus, $id);

if ($update->execute()) {
    echo json_encode(['statusCode' => 200, 'message' => 'Status updated', 'newStatus' => $newStatus]);
} else {
    echo json_encode(['statusCode' => 500, 'message' => 'Failed to update status']);
}

$update->close();
$connect->close();
