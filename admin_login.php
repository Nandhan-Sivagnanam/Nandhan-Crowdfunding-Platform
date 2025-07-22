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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Secure query to prevent SQL Injection
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
    
        echo "Hashed password from DB: " . $admin['password'] . "<br>";
        // Verify password securely (Ensure passwords are stored using password_hash())
        if (password_verify($password, $admin['password'])) {
            echo "Password verified successfully!";
            // Store admin details in session
            $_SESSION['user'] = [
                'id' => $admin['id'],
                'name' => $admin['username'],
                'role' => 'admin' // Set admin role
            ];
            
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['errorMessage'] = "Invalid password!";
        }
    } else {
        $_SESSION['errorMessage'] = "User not found!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | CrowdFunding</title>
    <link rel="stylesheet" href="css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
 
    <div class="container">
        <h2>Admin Login</h2>
        <form method="POST" action="admin_login.php">
           <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
