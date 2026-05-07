<?php $activePage = $activePage ?? ''; ?>
<aside id="sidebar">
    <div class="sidebar-brand">
        <img src="../sj_components/images/general/gen_off_seajetlogo.svg" alt="SeaJet">
    </div>

    <nav class="flex-grow-1">
        <div class="sidebar-section-label">Main</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="dashboard.php" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="inquiries.php" class="<?= $activePage === 'inquiries' ? 'active' : '' ?>">
                    <i class="fas fa-comment-dots"></i> Inquiries
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Management</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="users.php" class="<?= $activePage === 'users' ? 'active' : '' ?>">
                    <i class="fas fa-users-cog"></i> Admin Users
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        Logged in as <strong><?= htmlspecialchars($_SESSION['disp_name']) ?></strong>
    </div>
</aside>
