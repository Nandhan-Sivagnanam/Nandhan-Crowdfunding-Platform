<?php
include 'includes_db.php';
header("Content-Type: application/json");

// Read raw POST input
$data = json_decode(file_get_contents("php://input"), true);

// Log received data for debugging
error_log("Received data: " . print_r($data, true));

// Validate input data
if (!isset($data['campaign_id'], $data['action'])) {
    echo json_encode(["success" => false, "error" => "Missing required parameters."]);
    exit();
}

$campaignId = filter_var($data['campaign_id'], FILTER_VALIDATE_INT);
$action = strtolower(trim($data['action']));

if (!$campaignId || !in_array($action, ['approve', 'reject'])) {
    echo json_encode(["success" => false, "error" => "Invalid campaign ID or action."]);
    exit();
}

// Set status based on action
$status = ($action === 'approve') ? 'Approved' : 'Rejected';

// Check database connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Prepare the SQL statement
$query = "UPDATE campaigns SET status = ? WHERE id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => "Database error: " . $conn->error]);
    exit();
}

// Bind parameters and execute query
$stmt->bind_param("si", $status, $campaignId);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Campaign status updated.", "status" => $status]);
} else {
    echo json_encode(["success" => false, "error" => "Failed to update campaign status: " . $stmt->error]);
}

// Close statement and database connection
$stmt->close();
$conn->close();
?>
