<?php
include 'includes_db.php';

header('Content-Type: application/json'); // Set JSON header

// Fetch message details
if (isset($_GET['view_id'])) {
    $id = intval($_GET['view_id']);
    $query = "SELECT name, email, phone, subject, message FROM contact_queries WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            "status" => "success",
            "data" => $row
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Message not found"]);
    }
    exit();
}

// Mark message as read
if (isset($_GET['mark_read'])) {
    $id = intval($_GET['mark_read']);
    $query = "UPDATE contact_queries SET status = 'read' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "success", "message" => "Message marked as read"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update status"]);
    }
    exit();
}

// Delete message
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $query = "DELETE FROM contact_queries WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "success", "message" => "Message deleted"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete message"]);
    }
    exit();
}

echo json_encode(["status" => "error", "message" => "Invalid request"]);
?>
