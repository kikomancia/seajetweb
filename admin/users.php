<?php
$pageTitle  = 'Admin Users';
$activePage = 'users';
require 'includes/auth.php';
require 'includes/db.php';

$result      = mysqli_query($connect, "SELECT id, name, email, status FROM sj_users ORDER BY name ASC");
$users       = mysqli_fetch_all($result, MYSQLI_ASSOC);
$unreadCount = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS c FROM sj_messages WHERE status = 'unread'"))['c'];
mysqli_close($connect);

$extraScripts = <<<HTML
<script>
// Add user modal submit
$(document).ready(function () {

    $('#frmAddUser').on('submit', function (e) {
        e.preventDefault();

        var name     = $.trim($('#newName').val());
        var email    = $.trim($('#newEmail').val());
        var password = $.trim($('#newPassword').val());

        if (!name || !email || !password) {
            Swal.fire({ icon: 'warning', title: 'Missing fields', text: 'All fields are required.' });
            return;
        }

        if (password.length < 8) {
            Swal.fire({ icon: 'warning', title: 'Weak password', text: 'Password must be at least 8 characters.' });
            return;
        }

        $.ajax({
            url: 'php_files/save_user.php',
            type: 'POST',
            dataType: 'json',
            data: $(this).serialize(),
            success: function (res) {
                if (res.statusCode === 200) {
                    Swal.fire({ icon: 'success', title: 'User added!', timer: 1200, showConfirmButton: false })
                        .then(function () { location.reload(); });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Could not add user.' });
                }
            },
            error: function () {
                Swal.fire({ icon: 'error', title: 'Server error', text: 'Please try again.' });
            }
        });
    });

    // Toggle status
    $(document).on('click', '.btn-toggle', function () {
        var btn      = $(this);
        var userId   = btn.data('id');
        var userName = btn.data('name');
        var action   = btn.data('status') === 'active' ? 'deactivate' : 'activate';

        Swal.fire({
            icon: 'question',
            title: 'Confirm',
            text: 'Are you sure you want to ' + action + ' ' + userName + '?',
            showCancelButton: true,
            confirmButtonText: 'Yes, ' + action
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $.ajax({
                url: 'php_files/toggle_user.php',
                type: 'POST',
                dataType: 'json',
                data: { id: userId },
                success: function (res) {
                    if (res.statusCode === 200) {
                        Swal.fire({ icon: 'success', title: 'Done!', timer: 900, showConfirmButton: false })
                            .then(function () { location.reload(); });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Could not update user.' });
                    }
                }
            });
        });
    });

});
</script>
HTML;

include 'includes/header.php';
?>

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        <h1>Admin Users</h1>
        <p>Manage accounts that have access to this admin panel.</p>
    </div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fas fa-plus me-1"></i> Add User
    </button>
</div>

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">No users found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $i => $u): ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($u['name']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td>
                                    <span class="status-badge <?= $u['status'] === 'active' ? 'active' : 'inactive' ?>">
                                        <?= ucfirst(htmlspecialchars($u['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <button
                                        class="btn-action btn-toggle <?= $u['status'] === 'active' ? 'btn btn-sm btn-outline-danger' : 'btn btn-sm btn-outline-success' ?>"
                                        data-id="<?= $u['id'] ?>"
                                        data-name="<?= htmlspecialchars($u['name']) ?>"
                                        data-status="<?= $u['status'] ?>">
                                        <?= $u['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="addUserLabel">Add Admin User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <form id="frmAddUser">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Full Name</label>
                        <input type="text" id="newName" name="name" class="form-control" placeholder="Juan dela Cruz" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email Address</label>
                        <input type="email" id="newEmail" name="email" class="form-control" placeholder="user@seajet.com" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Password</label>
                        <input type="password" id="newPassword" name="password" class="form-control" placeholder="Min. 8 characters" required>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
