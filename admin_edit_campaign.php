<?php
include 'includes_db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Check database connection
if (!$conn) {
    echo json_encode(["success" => false, "error" => "Database connection failed: " . mysqli_connect_error()]);
    exit();
}

// Log requests for debugging
file_put_contents("debug.log", "Request received: " . json_encode($_REQUEST) . "\n", FILE_APPEND);

// **1️⃣ Fetch Campaign Details (GET Request)**
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $campaign_id = intval($_GET['id']); // Ensure it's an integer

    // Debug: Log the campaign ID
    file_put_contents("debug.log", "Fetching campaign ID: " . $campaign_id . "\n", FILE_APPEND);

    $query = "SELECT id, title, category, description, goal_amount, start_date, end_date, image_url FROM campaigns WHERE id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $campaign_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            echo json_encode(["success" => true, "data" => $row]); // ✅ SUCCESS
        } else {
            echo json_encode(["success" => false, "error" => "Campaign not found."]);
        }
        
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Query failed: " . $conn->error]);
    }
    exit();
}

// **2️⃣ Update Campaign Details (POST Request)**
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $campaign_id = intval($_POST['campaign_id']);
    $title = !empty($_POST['title']) ? $_POST['title'] : null; 
    $category = !empty($_POST['category']) ? $_POST['category'] : null; 
    $description = !empty($_POST['description']) ? $_POST['description'] : null; 
    $goal_amount = isset($_POST['goal_amount']) ? floatval($_POST['goal_amount']) : null; 
    $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;

    // Debug: Log form data before update
    file_put_contents("debug.log", "Updating campaign ID $campaign_id with data: " . json_encode($_POST) . "\n", FILE_APPEND);

    // **Handle Image Upload**
    $image_url = null;
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = "uploads/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $upload_dir . time() . "_" . $image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        } else {
            echo json_encode(["success" => false, "error" => "Image upload failed."]);
            exit();
        }
    }

    // **Prepare SQL Query**
    if ($image_url) {
        $query = "UPDATE campaigns SET title=?, category=?, description=?, goal_amount=?, start_date=?, end_date=?, image_url=? WHERE id=?";
    } else {
        $query = "UPDATE campaigns SET title=?, category=?, description=?, goal_amount=?, start_date=?, end_date=? WHERE id=?";
    }

    if ($stmt = $conn->prepare($query)) {
        if ($image_url) {
            $stmt->bind_param("sssssssi", $title, $category, $description, $goal_amount, $start_date, $end_date, $image_url, $campaign_id);
        } else {
            $stmt->bind_param("ssssssi", $title, $category, $description, $goal_amount, $start_date, $end_date, $campaign_id);
        }

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Campaign updated successfully."]);
        } else {
            echo json_encode(["success" => false, "error" => "Update failed: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Query failed: " . $conn->error]);
    }
    exit();
}

$conn->close();
?>
