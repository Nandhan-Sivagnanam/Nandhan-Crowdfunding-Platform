<?php
//  Reset to "dashboard.php" on first login or after logout
if (!isset($_SESSION['logged_in'])) {
    $_SESSION['logged_in'] = true;
    $_SESSION['last_page'] = 'dashboard.php';
    header("Location: dashboard.php"); // Redirect to dashboard after login
    exit();
}

//  Always reset to dashboard when logging in again
if (!isset($_SESSION['force_dashboard'])) {
    $_SESSION['last_page'] = 'dashboard.php';
    $_SESSION['force_dashboard'] = true; // Ensure it only happens once
}

//  Update last visited page when navigating
if (isset($_GET['page'])) {
    $_SESSION['last_page'] = $_GET['page'];
}

//  Ensure `$page` is always set
$page = $_SESSION['last_page'] ?? 'dashboard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin_sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Admin Panel</title>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="title">
        <h2>CrowdFunding</h2>
    </div>
    <ul>
        <li><a href="dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'dashboard.php') ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Dashboard</a></li>
        <li><a href="admin_users.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'admin_users.php') ? 'active' : ''; ?>"><i class="fas fa-users"></i> Manage Users</a></li>
        <li><a href="campaign.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'campaign.php') ? 'active' : ''; ?>"><i class="fas fa-bullhorn"></i> Manage Campaigns</a></li>
        <li><a href="admin_messages.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'admin_messages.php') ? 'active' : ''; ?>"><i class="fas fa-envelope"></i> Messages</a></li>
    </ul>
</div>

<script src="js/admin_sidebar.js" defer></script>

</body>
</html>
