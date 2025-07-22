<?php
include 'includes_db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Check if database connection is successful
if (!$conn) {
    echo json_encode(["success" => false, "error" => "Database connection failed: " . mysqli_connect_error()]);
    exit();
}

// Fetch campaigns for display
$campaigns = [];
$query = "SELECT id, title, IFNULL(category, 'Uncategorized') AS category, 
                 goal_amount AS goal, IFNULL(raised, 0) AS raised, start_date, 
                 IFNULL(end_date, 'N/A') AS end_date, created_by, 
                 IFNULL(status, 'Pending') AS status 
          FROM campaigns";

if ($result = $conn->query($query)) {
    while ($row = $result->fetch_assoc()) {
        $row['start_date'] = ($row['start_date'] == '0000-00-00' || empty($row['start_date'])) ? 'N/A' : $row['start_date'];
        $row['end_date'] = ($row['end_date'] == '0000-00-00' || empty($row['end_date'])) ? 'N/A' : $row['end_date'];
        $row['status'] = ucfirst($row['status']); // Capitalize first letter
        $campaigns[] = $row;
    }
    $result->free(); // Free result set
} else {
    error_log("Error fetching campaigns: " . $conn->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/campaign.css">
    <link rel="stylesheet" href="css/confirmation_dialog.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Campaign | Crowdfunding</title>
</head>
<body>
    <?php include 'include/admin_header.php'; ?>
    <?php include 'include/admin_sidebar.php'; ?>

    <div class="main-content">
        <section id="campaignsSection" class="content-section">
            
            <!--  Search Box -->
            <div class="search-container">
                <h2>All Campaigns</h2>
                <button id="addCampaignBtn" class="add-button"><i class="fa fa-plus"></i> Add</button>
                <input type="text" id="searchInput" placeholder="Title, Category, Status..." class="search-box">
            </div>

            <table class="campaign-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Goal | Raised</th>
                        <th>Start Date | End Date</th>
                        <th>Created By</th>
                        <th>Approval Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="campaignTableBody">
                    <?php if (!empty($campaigns)) {
                        foreach ($campaigns as $campaign) {
                            $statusClass = strtolower($campaign['status']);
                            $statusClass = in_array($statusClass, ['approved', 'rejected']) ? $statusClass : 'pending';

                            // Ensure data is properly escaped for security
                            $title = htmlspecialchars($campaign['title'], ENT_QUOTES, 'UTF-8');
                            $category = htmlspecialchars($campaign['category'], ENT_QUOTES, 'UTF-8');
                            $status = htmlspecialchars($campaign['status'], ENT_QUOTES, 'UTF-8');

                            echo "<tr data-title='$title' data-category='$category' data-status='$status'>
                                <td>$title</td>
                                <td><span class='category'>$category</span></td>
                                <td>₹" . number_format($campaign['goal'], 0) . " / <span class='raised-amount'>₹" . number_format($campaign['raised'], 0) . "</span></td>
                                <td>{$campaign['start_date']} / <span class='end-date'>{$campaign['end_date']}</span></td>
                                <td>{$campaign['created_by']}</td>
                                <td><span class='status $statusClass'>$status</span></td>
                                <td>
                                    <button class='approve-btn' data-id='{$campaign['id']}'>Approve</button>
                                    <button class='reject-btn' data-id='{$campaign['id']}'>Reject</button>
                                    <button class='edit-btn' data-id='{$campaign['id']}'>Edit</button>
                                </td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No campaigns found.</td></tr>";
                    } ?>
                </tbody>
            </table>
        </section>
        
        <!-- add campaign section -->
        <section id="addCampaignSection" class="hidden" style="margin-top: 60px; display:none;">
            <div class="back-button-container">
                <button type="button" id="backToCampaignsBtn" class="back-button">Back</button>
            </div>
    
            <h2>Add New Campaign</h2>
    
            <form id="addCampaignForm" enctype="multipart/form-data">
                <input type="hidden" class="edit-input" id="addCampaignId" name="campaign_id">

                <div class="form-group">
                    <label for="addTitle">Title:</label>
                    <input type="text" class="edit-input" id="addTitle" name="title" required>
                </div>

                <div class="form-group">
                    <label for="addCategory">Category:</label>
                    <select class="edit-input" id="addCategory" name="category" required>
                        <option value="" disabled selected>Select a category</option>
                        <option value="Medical Support">Medical Support</option>
                        <option value="Financial Support">Financial Support</option>
                        <option value="Environment">Environment</option>
                        <option value="Education">Education</option>
                        <option value="other">Other Cause</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="addDescription">Description:</label>
                    <textarea class="edit-input" id="addDescription" name="description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="addGoal">Goal Amount:</label>
                    <input type="number" class="edit-input" id="addGoal" name="goal_amount" min="1" required>
                </div>

                <div class="form-group">
                    <label for="addStartDate">Start Date:</label>
                    <input type="date" class="edit-input" id="addStartDate" name="start_date" required>
                </div>

                <div class="form-group">
                    <label for="addEndDate">End Date:</label>
                    <input type="date" class="edit-input" id="addEndDate" name="end_date">
                </div>

                <div class="form-group">
                    <label for="addCloseType">Close Type:</label>
                    <select class="edit-input" id="addCloseType" name="close_type">
                        <option value="" disabled selected>Select Close Type</option>
                        <option value="end_date">End Date</option>
                        <option value="goal_achieved">Goal Achieved</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="addImage">Campaign Image:</label>
                    <input type="file" class="edit-input" id="addImage" name="image">
                </div>

                <button type="submit" class="edit-button">Create Campaign</button>
            </form>
        </section>

        <!-- Edit Campaign Section (Initially Hidden) -->
        <section id="editCampaignSection" class="hidden" style="margin-top: 60px; , max-width: 0px;">
            <h2>Edit Campaign</h2>
            <form id="editCampaignForm" enctype="multipart/form-data">
            <input type="hidden" class="edit-input" id="editCampaignId" name="campaign_id">

            <div class="form-group">
                <label for="editTitle">Title:</label>
                <input type="text" class="edit-input" id="editTitle" name="title">
            </div>

            <div class="form-group">
                <label for="editCategory">Category:</label>
                <select class="edit-input" id="editCategory" name="category">
                    <option value="" disabled selected>Select a category</option>
                    <option value="Medical Support">Medical Support</option>
                    <option value="Financial Support">Financial Support</option>
                    <option value="Environment">Environment</option>
                    <option value="Education">Education</option>
                </select>
            </div>


            <div class="form-group">
                <label for="editDescription">Description:</label>
                <textarea class="edit-input" id="editDescription" name="description" ></textarea>
            </div>

            <div class="form-group">
                <label for="editGoal">Goal Amount:</label>
                <input type="number" class="edit-input" id="editGoal" name="goal_amount" min="1" >
            </div>

            <div class="form-group">
                <label for="editStartDate">Start Date:</label>
                <input type="date" class="edit-input" id="editStartDate" name="start_date" >
            </div>

            <div class="form-group">
                <label for="editEndDate">End Date:</label>
                <input type="date" class="edit-input" id="editEndDate" name="end_date">
            </div>

            <div class="form-group">
                <label for="editCloseType">Close Type:</label>
                <select class="edit-input" id="editCloseType" name="close_type">
                    <option value="" disabled selected>Select Close Type</option>
                    <option value="end_date">End Date</option>
                    <option value="goal_achieved">Goal Achieved</option>
                </select>
            </div>

            <div class="form-group">
                <label for="editImage">Campaign Image:</label>
                <input type="file" class="edit-input" id="editImage" name="image">
            </div>

            <button type="submit" class="edit-button">Update Campaign</button>
        </form>
    </section>
    </div>    
    <script defer src="js/campaign.js"></script>
    <script defer src="js/edit_campaign.js"></script>
</body>
</html>
<?php
$conn->close();
?>

