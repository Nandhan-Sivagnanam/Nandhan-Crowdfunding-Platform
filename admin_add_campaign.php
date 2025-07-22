<?php
include 'includes_db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Invalid request method"]);
    exit();
}

// Check database connection
if (!$conn) {
    echo json_encode(["success" => false, "error" => "Database connection failed: " . mysqli_connect_error()]);
    exit();
}

// Sanitize user inputs
$title = trim(mysqli_real_escape_string($conn, $_POST['title'] ?? ''));
$category = trim(mysqli_real_escape_string($conn, $_POST['category'] ?? 'Uncategorized'));
$description = trim(mysqli_real_escape_string($conn, $_POST['description'] ?? ''));
$goal_amount = floatval($_POST['goal_amount'] ?? 0);
$start_date = $_POST['start_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$close_type = $_POST['close_type'] ?? null;
$created_by = 1; // Hardcoded user ID (Replace with session-based user ID)

// Validate required fields
if (empty($title) || empty($description) || $goal_amount <= 0 || empty($start_date)) {
    echo json_encode(["success" => false, "error" => "Required fields are missing or invalid"]);
    exit();
}

// Handle File Upload (If Image Exists)
$image_url = null;
if (!empty($_FILES['image']['name'])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Create directory if not exists
    }

    $file_name = time() . "_" . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate file type
    $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_extensions)) {
        echo json_encode(["success" => false, "error" => "Invalid file type. Only JPG, JPEG, PNG & GIF allowed."]);
        exit();
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image_url = $target_file;
    } else {
        echo json_encode(["success" => false, "error" => "Failed to upload image."]);
        exit();
    }
}

// Insert campaign into database
$query = "INSERT INTO campaigns (title, category, description, goal_amount, start_date, end_date, close_type, created_by, image_url, status) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')";

$stmt = $conn->prepare($query);
$stmt->bind_param("sssisssis", $title, $category, $description, $goal_amount, $start_date, $end_date, $close_type, $created_by, $image_url);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Campaign added successfully!"]);
} else {
    echo json_encode(["success" => false, "error" => "Error adding campaign: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
