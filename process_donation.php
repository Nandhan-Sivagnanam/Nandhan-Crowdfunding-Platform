<?php
include 'includes_db.php'; // Include database connection

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get data from the AJAX request
$campaign_id = isset($_POST['campaign_id']) ? intval($_POST['campaign_id']) : 0;
$donation_amount = isset($_POST['donation_amount']) ? floatval($_POST['donation_amount']) : 0;
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';
$mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';

// Log the data to see what is being received
error_log("Received data: campaign_id=$campaign_id, donation_amount=$donation_amount, payment_method=$payment_method, name=$name, email=$email, mobile=$mobile");

// Validate inputs
if ($campaign_id <= 0 || $donation_amount <= 0 || !is_numeric($donation_amount) || empty($payment_method) || empty($name) || empty($email) || empty($mobile)) {
    error_log("Validation failed: Invalid donation details.");
    echo json_encode(['success' => false, 'message' => 'Invalid donation details. Please fill in all fields correctly.']);
    exit;
}

try {
    // Start a transaction
    $conn->begin_transaction();

    // Update the raised amount in the campaign table
    $stmt = $conn->prepare("UPDATE campaigns SET raised_amount = raised_amount + ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed for UPDATE query: " . $conn->error);
    }
    $stmt->bind_param("di", $donation_amount, $campaign_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to update campaign raised amount: " . $stmt->error);
    }

    // Add the donation record to the donations table
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null; // Assuming user_id is stored in session
    $donation_date = date('Y-m-d H:i:s'); // Current timestamp

    $stmt = $conn->prepare("INSERT INTO donations (user_id, campaign_id, amount, donation_date, payment_method, donor_name, donor_email, donor_mobile) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed for INSERT query: " . $conn->error);
    }
    $stmt->bind_param("iissssss", $user_id, $campaign_id, $donation_amount, $donation_date, $payment_method, $name, $email, $mobile);
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert donation record: " . $stmt->error);
    }

    // Commit the transaction
    $conn->commit();

    // Return success response
    echo json_encode(['success' => true, 'message' => 'Donation successful!']);
} catch (Exception $e) {
    // Rollback the transaction in case of error
    $conn->rollback();

    // Log the exception message for better debugging
    error_log("Exception occurred: " . $e->getMessage());

    // Return failure response
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred. Please try again later.']);
} finally {
    // Ensure the statement is closed
    if (isset($stmt) && $stmt) {
        $stmt->close();
    }
    // Close the database connection
    $conn->close(); 
}

// Ensure no unexpected output
exit;
?>
