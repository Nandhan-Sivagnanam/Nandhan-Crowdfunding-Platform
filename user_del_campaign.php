<?php
session_start();
include 'includes_db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $campaignId = $_POST['id'];
    $userId = $_SESSION['user']['id'];

    // Verify if the campaign belongs to the logged-in user
    $stmt = $conn->prepare("DELETE FROM campaigns WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $campaignId, $userId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete campaign."]);
    }

    $stmt->close();
    $conn->close();
}
?>
