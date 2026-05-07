<?php
$pageTitle  = 'Client Inquiries';
$activePage = 'inquiries';
require 'includes/auth.php';
require 'includes/db.php';

$result   = mysqli_query($connect, "SELECT * FROM sj_messages ORDER BY date_time DESC");
$messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_close($connect);

$extraScripts = <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function () {
    const t = document.getElementById('adminTable');
    if (t && typeof simpleDatatables !== 'undefined') {
        new simpleDatatables.DataTable(t, {
            searchable: true,
            sortable: true,
            perPage: 15,
            perPageSelect: [10, 15, 25, 50]
        });
    }
});
</script>
HTML;

include 'includes/header.php';
?>

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        <h1>Client Inquiries</h1>
        <p>All messages submitted via the contact form.</p>
    </div>
    <button class="btn btn-outline-secondary btn-sm" onclick="location.reload()">
        <i class="fas fa-sync-alt me-1"></i> Refresh
    </button>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table id="adminTable" class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact No.</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">No inquiries found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($messages as $row): ?>
                            <tr>
                                <td class="ps-4 text-nowrap">
                                    <?= date('M d, Y', strtotime($row['date_time'])) ?><br>
                                    <small class="text-muted"><?= date('h:i A', strtotime($row['date_time'])) ?></small>
                                </td>
                                <td><?= htmlspecialchars($row['cli_name']) ?></td>
                                <td><?= htmlspecialchars($row['cli_email']) ?></td>
                                <td><?= htmlspecialchars($row['cli_num']) ?></td>
                                <td class="msg-cell"><?= htmlspecialchars($row['cli_message']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
