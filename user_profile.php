<?php
session_start();
include 'includes_db.php'; // Database connection

date_default_timezone_set('Asia/Kolkata'); // Set the correct timezone

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user']['id'];
$userName = $_SESSION['user']['name'];
$userEmail = $_SESSION['user']['email'] ?? "Not available";

// Fetch the number of campaigns created by the user
$campaignCount = 0;
$query = "SELECT COUNT(*) AS total FROM campaigns WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $campaignCount = $row['total'];
}
$stmt->close();

// Determine the greeting based on the correct time
$hour = date('H');
if ($hour >= 5 && $hour < 12) {
    $greeting = "Good morning, $userName!";
} elseif ($hour >= 12 && $hour < 17) {
    $greeting = "Good afternoon, $userName!";
} else {
    $greeting = "Good evening, $userName!";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    if (!empty($name) && !empty($email)) {
        $query = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $name, $email, $userId);

        if ($stmt->execute()) {
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            $_SESSION['successMsg'] = "Profile updated successfully!";
            header("Location: user_profile.php"); // Refresh page
            exit();
        } else {
            $errorMsg = "Database error. Try again.";
        }

        $stmt->close();
    } else {
        $errorMsg = "Fields cannot be empty.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/user_profile.css">
    <title>Profile</title>
</head>
<body>
    
    <?php include 'include/user_sidebar.php'; ?>
    <?php include 'include/user_header.php'; ?>
    
    <div class="main-content">
        <section class="profile-section">
            <h2><?= htmlspecialchars($greeting); ?></h2>

            <?php if (isset($_SESSION['successMsg'])): ?>
                <p id="successMessage" style="color: green;"><?= htmlspecialchars($_SESSION['successMsg']); ?></p>
                <?php unset($_SESSION['successMsg']); // Remove message after displaying ?>
            <?php endif; ?>

            <?php if (isset($errorMsg)): ?>
                <p id="errorMessage" style="color: red;"><?= htmlspecialchars($errorMsg); ?></p>
            <?php endif; ?>

            <p><strong>Name:</strong> <span id="displayName"><?= htmlspecialchars($userName); ?></span></p>
            <p><strong>Email:</strong> <span id="displayEmail"><?= htmlspecialchars($userEmail); ?></span></p>
            <p><strong>Campaigns Created:</strong> <span id="campaignCount"><?= number_format($campaignCount); ?></span></p> <!-- Added campaign count -->

            <button id="editProfileBtn">Edit Profile</button>
        </section>
    </div>

    <!-- Popup Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Profile</h2>
            <form method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($userName); ?>" required>
                
                <label class="email" for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($userEmail); ?>" required>
                
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script defer src="js/user_profile.js"></script>
</body>
</html>
