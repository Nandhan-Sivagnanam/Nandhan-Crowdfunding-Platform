<?php
// session_start();
include 'includes_db.php'; 

$adminName = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';

$totalCampaigns = 0;
$totalUsers = 0;
$totalFundRaised = 0.00;

// Fetch total campaigns
$result = $conn->query("SELECT COUNT(*) AS total FROM campaigns");
if ($row = $result->fetch_assoc()) {
    $totalCampaigns = $row['total'];
}

// Fetch total users
$result = $conn->query("SELECT COUNT(*) AS total FROM users");
if ($row = $result->fetch_assoc()) {
    $totalUsers = $row['total'];
}

// Fetch total fund raised
$result = $conn->query("SELECT SUM(amount) AS total_donations FROM donations");
if (!$result) {
    die("Error in query: " . $conn->error); // Debugging output
}
$row = $result->fetch_assoc();
$totalFundRaised = isset($row['total_donations']) ? floatval($row['total_donations']) : 0;

// Fetch recent campaigns (latest 5)
$recentCampaigns = $conn->query("SELECT title, goal_amount, image_url, status FROM campaigns ORDER BY created_at DESC LIMIT 5");

// Fetch recent users (latest 5)
$recentUsers = $conn->query("SELECT id, name, email FROM users ORDER BY created_at DESC LIMIT 5");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Dashboard | Crowdfunding</title>
</head>
<body>

    <?php include 'include/admin_header.php'; ?>
    <?php include 'include/admin_sidebar.php'; ?>

    <div class="main-content">
        <section id="dashboardSection" class="content-section">
            
            <div class="stats">
                <div class="card">
                    <i class="fas fa-bullhorn stats-icon"></i>
                    <h3>Total Campaigns</h3>
                    <p><?php echo number_format($totalCampaigns); ?></p>
                </div>
                <div class="card">
                    <i class="fas fa-users stats-icon"></i>
                    <h3>Total Users</h3>
                    <p><?php echo number_format($totalUsers); ?></p>
                </div>
                <div class="card">
                    <i class="fas fa-dollar-sign stats-icon"></i>
                    <h3>Fund Raised</h3>
                    <p>₹<?php echo number_format($totalFundRaised); ?></p>
                </div>
            </div>
        </section>

        <!-- Recent Campaigns Section -->
        <section id="recentCampaignsSection" class="content-section">
            <h2 class="text">Recent Campaigns</h2>
            <table class="campaigns-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Goal Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($campaign = $recentCampaigns->fetch_assoc()): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($campaign['image_url']); ?>" alt="Campaign Image"></td>
                            <td><?php echo htmlspecialchars($campaign['title']); ?></td>
                            <td>₹<?php echo number_format($campaign['goal_amount']); ?></td>
                            <td>
                                <button class="status-btn <?= strtolower($campaign['status']); ?>">
                                    <?= htmlspecialchars($campaign['status']); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <!-- Recent Users Section -->
        <section id="recentUsersSection" class="content-section">
        
            <table class="users-table">
            <h2 class="user-text">Recent Users</h2>
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sno = 1; while ($user = $recentUsers->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $sno++; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </div>

    <script defer src="js/dashboard.js"></script>
</body>
</html>

<?php
$conn->close();
?>
