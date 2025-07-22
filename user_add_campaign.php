<?php
session_start();
include 'includes_db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$userId = $_SESSION['user']['id'];
$title = $_POST['title'] ?? 'No Title';
$category = $_POST['category'] ?? 'Uncategorized';
$description = $_POST['description'] ?? '';
$goalAmount = isset($_POST['goal_amount']) ? (int)$_POST['goal_amount'] : 0;
$startDate = $_POST['start_date'] ?? '';
$endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
$closeType = $_POST['close_type'] ?? 'end_date';

// Debugging: Save posted data for checking (optional)
file_put_contents("debug_post.txt", print_r($_POST, true));

// Ensure database connection is established
if (!$conn) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . mysqli_connect_error()]));
}

// ✅ **Fetch Username from users Table**
$userQuery = $conn->prepare("SELECT name FROM users WHERE id = ?");
$userQuery->bind_param("i", $userId);
$userQuery->execute();
$userResult = $userQuery->get_result();
$userRow = $userResult->fetch_assoc();
$createdBy = $userRow['name'] ?? 'Unknown'; // Default to 'Unknown' if not found
$userQuery->close();

// ✅ **Handling Image Upload**
$imageUrl = '';
if (!empty($_FILES["image"]["name"])) {
    // Debugging: Save uploaded file details (optional)
    file_put_contents("debug_files.txt", print_r($_FILES, true));

    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(["status" => "error", "message" => "Invalid image format. Only JPG, PNG, GIF allowed"]);
        exit;
    }

    if ($_FILES["image"]["size"] > 2 * 1024 * 1024) { // 2MB max
        echo json_encode(["status" => "error", "message" => "Image size exceeds 2MB"]);
        exit;
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        $imageUrl = $targetFilePath;
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to upload image"]);
        exit;
    }
}

// ✅ **Insert Data into Campaigns Table**
$query = "INSERT INTO campaigns (user_id, title, category, description, goal_amount, start_date, end_date, close_type, image_url, status, created_by) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', ?)";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
    exit;
}

$stmt->bind_param("isssisssss", $userId, $title, $category, $description, $goalAmount, $startDate, $endDate, $closeType, $imageUrl, $createdBy);

if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Execute failed: " . $stmt->error]);
} else {
    echo json_encode(["status" => "success", "message" => "Campaign added successfully"]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
