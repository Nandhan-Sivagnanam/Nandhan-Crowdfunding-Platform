<?php
session_start();

$page = basename($_SERVER['PHP_SELF']); // Get the current page name

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$user_name = isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : null;
$user_role = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/home_header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" <?php if ($page === 'index.php') echo 'disabled'; ?>>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Crowdfunding Platform</title>
</head>
<body>
<header class="home-header">
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm home-header">
        <div class="container">
            <a class="navbar-brand" href="#">CrowdFunding</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page === 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
                    </li>

                    <?php if ($page === 'index.php'): ?>
                        <li class="nav-item"><a class="nav-link" href="#projects">Projects</a></li>
                        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page === 'contact.php') ? 'active' : ''; ?>" href="contact.php">Contact Us</a>
                    </li>

                    <?php if (!$user_role): ?>
                        <!-- If no user is logged in -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="loginDropdown" role="button" data-bs-toggle="dropdown">
                                Login
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="loginDropdown">
                                <li><a class="dropdown-item" href="login.php"><i class="fas fa-user"></i> User</a></li>
                                <li><a class="dropdown-item" href="admin_login.php"><i class="fas fa-user-shield"></i> Admin</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <?php if ($user_role === 'user'): ?>
                            <!-- User Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                    Hello, <?php echo htmlspecialchars($user_name); ?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="user_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                    <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li><a class="dropdown-item" href="index.php?logout=true"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                                </ul>
                            </li>
                        <?php elseif ($user_role === 'admin'): ?>
                            <!-- Admin Dropdown -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                    Hello, Admin
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                    <li><a class="dropdown-item" href="admin_mainpage.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                                    <li><div class="dropdown-divider"></div></li>
                                    <li><a class="dropdown-item" href="index.php?logout=true"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Start Fundraiser Button -->
                    <li><a class="nav_button" href="fundraisingform.php">Start Fundraiser</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<!-- script section -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="js/home_header.js" defer></script>
</body>
</html>
