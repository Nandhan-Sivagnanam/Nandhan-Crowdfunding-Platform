<?php
// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes_db.php'; // Ensure only one inclusion of DB connection

// Ensure session variables are set
if (!isset($_SESSION['user']['id'])) {
    $_SESSION['errorMessage'] = "❌ Admin ID is not set in session.";
    header("Location: admin_header.php"); // Redirect to avoid script execution
    exit();
}

$adminId = $_SESSION['user']['id'];
$adminName = htmlspecialchars($_SESSION['user']['name'] ?? 'Admin');

// Check if database connection is active
if (!isset($conn) || !$conn instanceof mysqli || !$conn->ping()) {
    $_SESSION['errorMessage'] = "❌ Database connection lost. Refresh the page.";
    header("Location: admin_header.php");
    exit();
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
// Handle Password Change
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['change_password'])) {
    $oldPassword = trim($_POST['old_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        $_SESSION['errorMessage'] = "❌ All fields are required.";
    } elseif ($newPassword !== $confirmPassword) {
        $_SESSION['errorMessage'] = "⚠️ New passwords do not match.";
    } else {
        $query = "SELECT password FROM admins WHERE id = ?";
        if ($checkStmt = $conn->prepare($query)) {
            $checkStmt->bind_param("i", $adminId);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {
                $checkStmt->bind_result($dbPassword);
                $checkStmt->fetch();

                if (!empty($dbPassword) && password_verify($oldPassword, $dbPassword)) {
                    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                    $updateQuery = "UPDATE admins SET password = ? WHERE id = ?";
                    if ($updateStmt = $conn->prepare($updateQuery)) {
                        $updateStmt->bind_param("si", $hashedPassword, $adminId);
                        if ($updateStmt->execute()) {
                            $_SESSION['successMessage'] = "✅ Password changed successfully!";
                        } else {
                            $_SESSION['errorMessage'] = "❌ Database error. Please try again.";
                        }
                        $updateStmt->close();
                    } else {
                        $_SESSION['errorMessage'] = "❌ Failed to prepare update statement.";
                    }
                } else {
                    $_SESSION['errorMessage'] = "❌ Old password is incorrect.";
                }
            } else {
                $_SESSION['errorMessage'] = "❌ Admin account not found.";
            }
            $checkStmt->close();
        } else {
            $_SESSION['errorMessage'] = "❌ Failed to prepare password check statement.";
        }
    }

    // Redirect to admin main page to display message
    header("Location: admin_mainpage.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin_header.css">
    <link rel="stylesheet" href="css/message_box.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Admin Panel</title>
</head>
<body>
    <!-- Message Box -->
    <?php if (isset($_SESSION['successMessage']) || isset($_SESSION['errorMessage'])): ?>
        <div id="messageBox" class="message-box <?= isset($_SESSION['successMessage']) ? 'success' : 'error' ?>">
            <?= htmlspecialchars($_SESSION['successMessage'] ?? $_SESSION['errorMessage']); ?>
        </div>
        <?php unset($_SESSION['successMessage']); ?>
        <?php unset($_SESSION['errorMessage']); ?>
    <?php endif; ?>

    <!-- Header -->
    <nav class="top-bar">
        <div class="admin-container">
            <a href="index.php" class="home-link"><i class="fas fa-home"></i></a>
            <div class="admin-menu">
                <span class="admin-name" id="adminDropdownToggle">
                    <i class="fas fa-user"></i> <?= $adminName; ?>
                    <i class="fas fa-chevron-down"></i> 
                </span>
                <div class="dropdown-menu hidden" id="adminDropdown">
                    <a class="password" href="#" id="changePasswordBtn"><i class="fas fa-key"></i> Change Password</a>
                    <a href="index.php?logout=true" onclick="sessionStorage.clear();" id="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="modal hidden">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Change Password</h2>
            <form method="POST">
                <input type="password" id="old_password" name="old_password" required placeholder="Old Password">
                <input type="password" id="new_password" name="new_password" required placeholder="New Password">
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm Password">
                <button type="submit" name="change_password">Update Password</button>
            </form>
        </div>
    </div>
    <script src="js/admin_header.js" defer></script>
    <script src="js/admin_message_box.js" defer></script>
</body>
</html>
