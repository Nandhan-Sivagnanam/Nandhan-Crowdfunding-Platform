<?php
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$userName = $_SESSION['user']['name'] ?? 'user'; // Fix: Define user name

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/user_header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Document</title>
</head>
<body>
    <!-- Header -->
    <nav class="top-bar">
        <div class="user-container">
            <a href="index.php" class="home-link"><i class="fas fa-home"></i> Home</a>
            <div class="user-menu">
                <span class="user-name">
                    <i class="fas fa-user"></i><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?>
                    <i class="fas fa-chevron-down"></i> 
                </span>
                <div class="dropdown-menu hidden" id="adminDropdown">
                    <a href="index.php?logout=true" onclick="localStorage.removeItem('lastVisitedPage'); sessionStorage.clear();" id="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>

                </div>
            </div>
        </div>
    </nav>
    <script src="js/user_header.js" defer></script>
</body>
</html>
