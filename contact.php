<?php
include 'includes_db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : "";
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : "";
    $phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : "";
    $subject = isset($_POST['subject']) ? $conn->real_escape_string($_POST['subject']) : "";
    $message = isset($_POST['message']) ? $conn->real_escape_string($_POST['message']) : "";

    if (!empty($name) && !empty($email) && !empty($phone) && !empty($subject) && !empty($message)) {
        $sql = "INSERT INTO contact_queries (name, email, phone, subject, message) 
                VALUES ('$name', '$email', '$phone', '$subject', '$message')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Query submitted successfully!');</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('All fields are required!');</script>";
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="css/contact.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
      <!-- header -->
  <?php include 'include/home_header.php'; ?>

    <div class="contact-container">
        <h2 class="head-text">Contact Us</h2>
        <div class="contact-details">
            <div class="contact-box">
                <i class="fas fa-envelope"></i>
                <div>
                    <p>Email Address</p>
                    <strong>crowdfunding2025@gmail.com</strong>
                </div>
            </div>
            <div class="contact-box">
                <i class="fas fa-phone"></i>
                <div>
                    <p>Phone Number</p>
                    <strong class="ph-no">9361609280</strong>
                </div>
            </div>
        </div>
        <p>If you have any questions, feel free to send us a message.</p>
        
        <form action="#" method="POST" class="query-form">
            <div class="form-row">
                <div class="form-group">
                    <input type="text" id="name" name="name" placeholder="Enter a Name" required>
                </div>
                <div class="form-group">
                    <input type="email" id="email" name="email"  placeholder="Enter a Email" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <input type="tel" id="phone" name="phone" required placeholder="Phone Number">
                </div>
                <div class="form-group">
                    <input type="text" id="subject" name="subject" placeholder="Subject" required >
                </div>
            </div>
            <div class="form-group">
                <textarea id="message" name="message" rows="4"  placeholder="Message" required></textarea>
            </div>
            <button type="submit">Send Query</button>
        </form>
    </div>
      <!-- footer -->
  <?php include 'include/home_footer.php'; ?>
  
</body>
</html>
