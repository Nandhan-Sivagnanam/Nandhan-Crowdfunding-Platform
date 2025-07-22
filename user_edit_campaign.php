<?php
include 'includes_db.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    // Fetch campaign details
    $campaignId = intval($_GET['id']);
    
    $query = "SELECT * FROM campaigns WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $campaignId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(["success" => true] + $row);
    } else {
        echo json_encode(["success" => false, "message" => "Campaign not found."]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update campaign details
    $campaignId = intval($_POST['campaign_id']);
    $title = $_POST['title'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $goal = $_POST['goal_amount'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $close_type = $_POST['close_type'];

    $query = "UPDATE campaigns SET 
                title = ?, category = ?, description = ?, goal_amount = ?, 
                start_date = ?, end_date = ?, close_type = ? 
              WHERE id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssisisi", $title, $category, $description, $goal, $start_date, $end_date, $close_type, $campaignId);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Update failed."]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

echo json_encode(["success" => false, "message" => "Invalid request."]);
?>
