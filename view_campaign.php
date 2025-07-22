<?php
include 'includes_db.php'; // Include database connection

// Retrieve campaign ID from the URL
$campaign_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch campaign details from database
function getCampaignDetails($conn, $campaign_id) {
    $stmt = $conn->prepare("SELECT * FROM campaigns WHERE id = ?");
    $stmt->bind_param("i", $campaign_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

$campaign = getCampaignDetails($conn, $campaign_id);

// Fetch creator's name from users table
function getCreatorName($conn, $creator_id) {
  $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
  $stmt->bind_param("i", $creator_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();
  return $user ? $user['name'] : "Unknown Creator";
}
$creator_name = isset($campaign['created_by']) ? htmlspecialchars($campaign['created_by']) : "Unknown Creator";

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($campaign['title']) ? htmlspecialchars($campaign['title']) : "Campaign Details"; ?></title>
  <link rel="stylesheet" href="css/view_style.css">
</head>
<body>
  <!-- header -->
  <?php include 'include/home_header.php'; ?>

  <!-- Main Content Container -->
  <div class="main-container">
    <!-- Campaign Section (Image, Details, and About) -->
    <div class="campaign-section">
      <!-- Image Section -->
      <div class="image-section">
        <img src="<?php echo !empty($campaign['image_url']) ? htmlspecialchars($campaign['image_url']) : 'default-image.jpg'; ?>" 
             alt="Campaign Image" class="campaign-image">
      </div>

      <!-- Right Side Details Section -->
      <div class="details-section">
        <h1 class="campaign-title"><?php echo isset($campaign['title']) ? htmlspecialchars($campaign['title']) : "No Title Available"; ?></h1>
        <p class="campaign-creator">Created by: <strong><?php echo $creator_name; ?></strong></p>
        <p class="campaign-start-end">
          Start Date: <?php echo isset($campaign['start_date']) && !empty($campaign['start_date']) ? date('F j, Y', strtotime($campaign['start_date'])) : "N/A"; ?> | 
          End Date: <?php echo isset($campaign['end_date']) && !empty($campaign['end_date']) ? date('F j, Y', strtotime($campaign['end_date'])) : "N/A"; ?>
        </p>

        <!-- Money Earned Representation -->
        <div class="money-progress">
          <label for="money-earned">Money Earned:</label>
          <!-- Progress Bar -->
          <progress id="money-earned" value="<?php echo isset($campaign['raised_amount']) ? $campaign['raised_amount'] : 0; ?>" 
                    max="<?php echo isset($campaign['goal_amount']) ? $campaign['goal_amount'] : 100; ?>"></progress>
          <div class="money-details">
            <span class="raised-amount">
              Raised: ₹<?php echo isset($campaign['raised_amount']) ? number_format($campaign['raised_amount']) : "0"; ?>
            </span>
            <span class="goal-amount">
              Goal: ₹<?php echo isset($campaign['goal_amount']) ? number_format($campaign['goal_amount']) : "N/A"; ?>
            </span>
          </div>
          <button class="cta-button" onclick="openPopup()">Contribute Now</button>
        </div>
      </div>
    </div>

    <!-- About Section -->
    <div class="about-section">
      <h2>About This Campaign</h2>
      <div class="description-text" id="description-text">
        <p><?php echo isset($campaign['description']) ? nl2br(htmlspecialchars($campaign['description'])) : "No description available."; ?></p>
      </div>
      <span id="toggle-description" class="toggle-description-text">Preview More</span>
    </div>
  </div>

  <!-- Donation Popup -->
  <div id="popup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <h2>Choose a Donation Amount</h2>
        <p>Please select an amount and a payment method to proceed.</p> <!-- Added instruction -->
        <div class="amount-buttons">
            <button onclick="selectAmount(1000)">₹ 1000</button>
            <button onclick="selectAmount(2500)">₹ 2500</button>
            <button onclick="selectAmount(4000)">₹ 4000</button>
            <button onclick="selectAmount('other')">Other</button>
        </div>
        <form id="donation-form" data-campaign-id="<?php echo $campaign_id; ?>"> <!-- Pass campaignId to the form -->
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Email ID:</label>
            <input type="email" id="email" name="email" required>
            <label for="mobile">Mobile Number:</label>
            <input type="tel" id="mobile" name="mobile" required>
            <div id="other-amount-field" style="display: none;">
                <label for="other-amount">Enter Amount:</label>
                <input type="number" id="other-amount" name="other-amount" min="0" step="any" required>
            </div>

            <!-- Payment Method Section -->
            <div id="payment-method-section">
                <label>Select Payment Method:</label>
                <button type="button" onclick="selectPaymentMethod('razorpay')">Razorpay</button>
                <button type="button" onclick="selectPaymentMethod('gpay')">Google Pay</button>
                <button type="button" onclick="selectPaymentMethod('paytm')">Paytm</button>
            </div>

            <button type="submit" onclick="submitDonation(event)">Proceed to Pay</button>
        </form>
    </div>
</div>


  <!-- footer -->
  <?php include 'include/home_footer.php'; ?>
  
  <!-- script -->
  <script src="js/view_campaign.js"></script>
  <script src="js/donation.js"></script>
</body>
</html>
