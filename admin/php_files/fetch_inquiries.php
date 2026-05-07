<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

$filter  = $_GET['filter'] ?? 'all';
$allowed = ['all', 'unread', 'read', 'hidden'];
if (!in_array($filter, $allowed, true)) $filter = 'all';

// Counts for all statuses (for tab badges)
$countResult = mysqli_query($connect, "SELECT status, COUNT(*) AS cnt FROM sj_messages GROUP BY status");
$counts = ['unread' => 0, 'read' => 0, 'hidden' => 0];
while ($r = mysqli_fetch_assoc($countResult)) {
    if (isset($counts[$r['status']])) {
        $counts[$r['status']] = (int)$r['cnt'];
    }
}

// Filtered messages
if ($filter === 'all') {
    $result   = mysqli_query($connect, "SELECT * FROM sj_messages WHERE status != 'hidden' ORDER BY date_time DESC");
    $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $stmt = $connect->prepare("SELECT * FROM sj_messages WHERE status = ? ORDER BY date_time DESC");
    $stmt->bind_param('s', $filter);
    $stmt->execute();
    $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

mysqli_close($connect);

echo json_encode(['statusCode' => 200, 'data' => $messages, 'counts' => $counts]);
