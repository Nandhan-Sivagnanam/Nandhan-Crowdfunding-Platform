<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crowdfunding";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, 3308);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute() === TRUE) {
        $_SESSION['successMessage'] = "Your account is created successfully!";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['successMessage'] = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | CrowdFunding</title>
    <link rel="stylesheet" href="css/signup.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
</head>
<body>
    <div class="container">
        <h2>Create New Account</h2>
        <?php
        // session_start();
        if (isset($_SESSION['successMessage'])) {
            echo '<p class="success-message">' . $_SESSION['successMessage'] . '</p>';
            unset($_SESSION['successMessage']);
        }
        ?>
        <form action="signup.php" method="post">
            <label for="name">Name</label>
            <input type="text" id="name" placeholder="Name" name="name" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" placeholder="Enter Email" name="email" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" placeholder="Password" name="password" required>
            
            <input type="submit" value="Create Account">
            <div class="create-acc">
                <span class="bottom text">Already have an account?</span>
                <a href="login.php" class="create-new">Sign in</a> 
            </div>
        </form>
    </div>
</body>
</html>
