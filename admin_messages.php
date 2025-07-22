<?php
include 'includes_db.php';

// Fetch messages
$query = "SELECT * FROM contact_queries ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="css/admin_messages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body>
    <!-- header -->
    <?php include 'include/admin_header.php'; ?>
    <!-- sidebar -->
    <?php include 'include/admin_sidebar.php'; ?>
    
    <div class="main-content">
        <h2 class="text">Messages</h2>

        <div class="message-table-container">
            <div class="message-table">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $hasMessages = false; // Flag to check if messages exist
                        if (mysqli_num_rows($result) > 0): 
                            while ($row = mysqli_fetch_assoc($result)): 
                                $hasMessages = true; // Set flag to true if there's at least one message
                        ?>
                            <tr data-id="<?= $row['id']; ?>">
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                <td class="status">
                                    <span class="<?= ($row['status'] == 'unread') ? 'unread' : 'read'; ?>">
                                        <?= ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <button class="view-btn" onclick="viewMessage(<?= $row['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="delete-btn" onclick="confirmDelete(<?= $row['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php 
                            endwhile; 
                        endif; 

                        // If no messages were found, show the "No messages found" row
                        if (!$hasMessages): 
                        ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding:15px; font-weight:bold; background-color: #f9f9f9; color: red;">
                                    No messages found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Sidebar for Message Details -->
        <div class="message-sidebar" id="messageSidebar">
            <h3>Message Details</h3>
            <div id="messageContent"></div>
            <button onclick="closeSidebar()">Close</button>
        </div>

     <!-- script -->
     <script src="js/admin_inquiry.js"></script>
</body>
</html>
