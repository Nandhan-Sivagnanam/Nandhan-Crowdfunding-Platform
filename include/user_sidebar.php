<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/user_sidebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Sidebar</title>
</head>
<body>
    <div class="sidebar">
        <div class="title">
            <h2>DASHBOARD</h2>
        </div>
        <ul>
            <li><a href="user_profile.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'user_profile.php') ? 'active' : ''; ?>"><i class="fas fa-user"></i> Profile</a></li>
            <li><a href="user_campaign.php" class="<?= (basename($_SERVER['PHP_SELF']) === 'admin_users.php') ? 'active' : ''; ?>"><i class="fas fa-users"></i> My Campaigns</a></li>
        </ul>
    </div>
    <script  src="js/user_sidebar.js" defer></script> 
</body>
</html>