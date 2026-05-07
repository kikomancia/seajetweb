<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

$totalInquiries = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS c FROM sj_messages"))['c'];
$todayInquiries = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS c FROM sj_messages WHERE DATE(date_time) = CURDATE()"))['c'];
$totalUsers     = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS c FROM sj_users WHERE status = 'active'"))['c'];

mysqli_close($connect);

echo json_encode([
    'statusCode'      => 200,
    'totalInquiries'  => (int) $totalInquiries,
    'todayInquiries'  => (int) $todayInquiries,
    'totalUsers'      => (int) $totalUsers,
]);
