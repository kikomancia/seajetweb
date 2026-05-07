<?php
$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
require 'includes/auth.php';
require 'includes/db.php';

// Stat queries
$totalInquiries = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS c FROM sj_messages"))['c'];
$todayInquiries = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS c FROM sj_messages WHERE DATE(date_time) = CURDATE()"))['c'];
$totalUsers     = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS c FROM sj_users WHERE status = 'active'"))['c'];

// Recent 5 inquiries
$recentResult   = mysqli_query($connect, "SELECT date_time, cli_name, cli_email, cli_message FROM sj_messages ORDER BY date_time DESC LIMIT 5");
$recentMessages = mysqli_fetch_all($recentResult, MYSQLI_ASSOC);

mysqli_close($connect);

include 'includes/header.php';
?>

<div class="page-header">
    <h1>Dashboard</h1>
    <p>Welcome back, <?= htmlspecialchars($_SESSION['disp_name']) ?>. Here's what's happening.</p>
</div>

<!-- Stat cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-envelope-open-text"></i></div>
            <div class="stat-body">
                <div class="stat-label">Total Inquiries</div>
                <div class="stat-value"><?= number_format($totalInquiries) ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-calendar-day"></i></div>
            <div class="stat-body">
                <div class="stat-label">Today's Inquiries</div>
                <div class="stat-value"><?= number_format($todayInquiries) ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-user-shield"></i></div>
            <div class="stat-body">
                <div class="stat-label">Active Admin Users</div>
                <div class="stat-value"><?= number_format($totalUsers) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent inquiries -->
<div class="admin-card">
    <div class="admin-card-header">
        <h5><i class="fas fa-clock me-2 text-muted"></i>Recent Inquiries</h5>
        <a href="inquiries.php" class="btn btn-sm btn-outline-primary">View all</a>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentMessages)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No inquiries yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentMessages as $row): ?>
                            <tr>
                                <td class="ps-4 text-nowrap"><?= date('M d, Y', strtotime($row['date_time'])) ?></td>
                                <td><?= htmlspecialchars($row['cli_name']) ?></td>
                                <td><?= htmlspecialchars($row['cli_email']) ?></td>
                                <td class="msg-cell"><?= htmlspecialchars(mb_strimwidth($row['cli_message'], 0, 80, '…')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
