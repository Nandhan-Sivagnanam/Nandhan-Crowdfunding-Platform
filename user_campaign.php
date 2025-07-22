<?php
session_start();
include 'includes_db.php';

$userId = $_SESSION['user']['id'];

$campaigns = [];
$campaignQuery = "SELECT 
    id, 
    COALESCE(title, 'No Title') AS title, 
    COALESCE(category, 'Uncategorized') AS category, 
    COALESCE(goal_amount, 0) AS goal, 
    COALESCE(status, 'Pending') AS status,
    created_at 
FROM campaigns 
WHERE user_id = ? 
ORDER BY created_at DESC";  

$stmt = $conn->prepare($campaignQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $campaigns[] = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/user_campaign.css">
    <title>User Campaign</title>
</head>
<body>
    
    <?php include 'include/user_sidebar.php'; ?>
    <?php include 'include/user_header.php'; ?>
    
    <div class="main-content">
        <div class="header-container">
            <h2>My Campaigns</h2>
            <a href="addCampaignSection" class="add-campaign-btn"><i class="fa fa-plus"></i> Add</a>
            <input type="text" id="searchBox" class="search-box" placeholder="Search by title, category, status...">
        </div>

        <section class="campaigns-section">
            <table id="campaignTable">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Goal</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($campaigns)) { ?>
                        <tr>
                            <td colspan="5">No campaigns found.</td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($campaigns as $campaign) { ?>
                            <tr>
                                <td><?= htmlspecialchars($campaign['title']); ?></td>
                                <td><?= htmlspecialchars($campaign['category']); ?></td>
                                <td>₹<?= number_format($campaign['goal']); ?></td>
                                <td>
                                    <button class="status-btn <?= strtolower($campaign['status']); ?>">
                                        <?= htmlspecialchars($campaign['status']); ?>
                                    </button>
                                </td>
                                <td>
                                    <button class='edit-btn' data-id="<?= $campaign['id']; ?>">Edit</button>
                                    <button class='delete-btn' data-id="<?= $campaign['id']; ?>">Delete</button>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </section>

        <!-- Add Campaign Section -->
        <section id="addCampaignSection" class="hidden" style="display:none;">
            <div class="back-button-container">
                <button type="button" id="backToCampaignsBtn" class="back-button">Back</button>
            </div>
    
            <h2>Add New Campaign</h2>
    
            <form id="addCampaignForm" enctype="multipart/form-data">
                <input type="hidden" id="addCampaignId" name="campaign_id">

                <div class="form-group">
                    <label for="addTitle">Title:</label>
                    <input type="text" id="addTitle" name="title" required>
                </div>

                <div class="form-group">
                    <label for="addCategory">Category:</label>
                    <select id="addCategory" name="category" required>
                        <option value="" disabled selected>Select a category</option>
                        <option value="Medical Support">Medical Support</option>
                        <option value="Financial Support">Financial Support</option>
                        <option value="Environment">Environment</option>
                        <option value="Education">Education</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="addDescription">Description:</label>
                    <textarea id="addDescription" name="description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="addGoal">Goal Amount:</label>
                    <input type="number" id="addGoal" name="goal_amount" min="1" required>
                </div>

                <div class="form-group">
                    <label for="addStartDate">Start Date:</label>
                    <input type="date" id="addStartDate" name="start_date" required>
                </div>

                <div class="form-group">
                    <label for="addEndDate">End Date:</label>
                    <input type="date" id="addEndDate" name="end_date">
                </div>

                <div class="form-group">
                    <label for="addCloseType">Close Type:</label>
                    <select id="addCloseType" name="close_type">
                        <option value="" disabled selected>Select Close Type</option>
                        <option value="end_date">End Date</option>
                        <option value="goal_achieved">Goal Achieved</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="addImage">Campaign Image:</label>
                    <input type="file" id="addImage" name="image">
                </div>

                <button type="submit" class="edit-button">Create Campaign</button>
            </form>
        </section>

        <!-- Edit Campaign Section -->
        <section id="editCampaignSection" class="hidden" style="margin-top: 60px;">
            <div class="back-button-container">
                <button type="button" id="backToCampaignsBtn" class="back-button">Back</button>
            </div>
    
            <h2>Edit Campaign</h2>
            <form id="editCampaignForm" enctype="multipart/form-data">
                <input type="hidden" id="editCampaignId" name="campaign_id">

                <div class="form-group">
                    <label for="editTitle">Title:</label>
                    <input type="text" id="editTitle" name="title">
                </div>

                <div class="form-group">
                    <label for="editCategory">Category:</label>
                    <select id="editCategory" name="category">
                        <option value="" disabled selected>Select a category</option>
                        <option value="Medical Support">Medical Support</option>
                        <option value="Financial Support">Financial Support</option>
                        <option value="Environment">Environment</option>
                        <option value="Education">Education</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="editDescription">Description:</label>
                    <textarea id="editDescription" name="description"></textarea>
                </div>

                <div class="form-group">
                    <label for="editGoal">Goal Amount:</label>
                    <input type="number" id="editGoal" name="goal_amount" min="1">
                </div>

                <div class="form-group">
                    <label for="editStartDate">Start Date:</label>
                    <input type="date" id="editStartDate" name="start_date">
                </div>

                <div class="form-group">
                    <label for="editEndDate">End Date:</label>
                    <input type="date" id="editEndDate" name="end_date">
                </div>

                <div class="form-group">
                    <label for="editCloseType">Close Type:</label>
                    <select id="editCloseType" name="close_type">
                        <option value="" disabled selected>Select Close Type</option>
                        <option value="end_date">End Date</option>
                        <option value="goal_achieved">Goal Achieved</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="editImage">Campaign Image:</label>
                    <input type="file" id="editImage" name="image">
                </div>

                <button type="submit" class="edit-button">Update Campaign</button>
            </form>
        </section>
    </div>

    <script defer src="js/user_campaign.js"></script>
    <script defer src="js/user_edit_campaign.js"></script>
</body>
</html>
