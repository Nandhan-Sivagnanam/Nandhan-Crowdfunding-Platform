<?php
include 'includes_db.php'; 

$users = [];

// Ensure connection is established
if (!isset($conn) || !$conn instanceof mysqli) {
    die("Database connection error.");
}

// Fetch users list
$query = "SELECT id, name, email, role FROM users ORDER BY id DESC";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admin_users.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>All Users | Crowdfunding</title>
</head>
<body>
    <?php include 'include/admin_header.php'; ?>
    <?php include 'include/admin_sidebar.php'; ?>
    
    <div class="main-content">
        <section id="usersSection" class="content-section">
           <!--  Wrap h2 and search box in a flex container -->
            <div class="search-container">
                <h2>All Users</h2>
                <input type="text" id="searchInput" placeholder="Name,Email..." class="search-box">
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                <?php if (empty($users)): ?>
                    <tr class="no-results">
                        <td colspan="4">No results found</td> <!--  Ensure colspan matches column count -->
                    </tr>
                <?php else: ?>

                        <?php foreach ($users as $user): ?>
                            <tr data-id="<?= htmlspecialchars($user['id']) ?>">
                                <td class="user-name"><?= htmlspecialchars($user['name']) ?></td>
                                <td class="user-email"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="user-role"><?= htmlspecialchars($user['role']) ?></td>
                                <td>
                                    <button class="edit-btn" data-id="<?= htmlspecialchars($user['id']) ?>">Edit</button>
                                    <button class="save-btn hidden" data-id="<?= htmlspecialchars($user['id']) ?>">Save</button>
                                    <button class="cancel-btn hidden" data-id="<?= htmlspecialchars($user['id']) ?>">Cancel</button>
                                    <button class="delete-btn" data-id="<?= htmlspecialchars($user['id']) ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>    

    <script defer src="js/admin_users.js"></script>
</body>
</html>

<?php
$conn->close();
?>
