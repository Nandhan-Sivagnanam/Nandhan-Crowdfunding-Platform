<?php
include 'includes_db.php';

header("Content-Type: application/json");

// Get JSON input data
$data = json_decode(file_get_contents("php://input"), true);

// Ensure database connection is established
if (!$conn) {
    echo json_encode(["success" => false, "error" => "Database connection failed"]);
    exit();
}

// Validate action and ID
if (!isset($data['action'], $data['id']) || empty($data['id'])) {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
    exit();
}

$userId = intval($data['id']);

if ($data['action'] === "edit") {
    // Validate input fields
    $name = isset($data['name']) ? trim($data['name']) : '';
    $email = isset($data['email']) ? trim($data['email']) : '';
    $role = isset($data['role']) ? trim($data['role']) : '';

    if (empty($name) || empty($email) || empty($role)) {
        echo json_encode(["success" => false, "error" => "Missing required fields"]);
        exit();
    }

    // Update user data in database
    $updateQuery = "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    if ($stmt) {
        $stmt->bind_param("sssi", $name, $email, $role, $userId);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Database error: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Statement preparation failed"]);
    }
}

elseif ($data['action'] === "delete") {
    // Delete user from database
    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Database error: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Statement preparation failed"]);
    }
}

else {
    echo json_encode(["success" => false, "error" => "Invalid action"]);
}

$conn->close();
?>
