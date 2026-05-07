<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

$result = mysqli_query($connect, "SELECT id, name, email, status FROM sj_users ORDER BY name ASC");
$users  = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_close($connect);

echo json_encode(['statusCode' => 200, 'data' => $users]);
