<?php
$initials = strtoupper(substr($_SESSION['disp_name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — SeaJet Admin</title>
    <link rel="icon" href="../sj_components/images/general/sj_icon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
<div class="admin-wrapper">

<?php include __DIR__ . '/sidebar.php'; ?>

<div id="main">

<nav id="topnav">
    <button class="sidebar-toggle" id="sidebarToggle" title="Toggle sidebar">
        <i class="fas fa-bars"></i>
    </button>
    <span class="topnav-title"><?= htmlspecialchars($pageTitle ?? '') ?></span>

    <div class="topnav-right">
        <div class="dropdown">
            <a class="user-badge dropdown-toggle text-decoration-none" data-bs-toggle="dropdown" href="#">
                <div class="user-avatar"><?= $initials ?></div>
                <span class="user-name d-none d-sm-inline"><?= htmlspecialchars($_SESSION['disp_name']) ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                <li>
                    <a class="dropdown-item text-danger" href="php_files/logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div id="content">
