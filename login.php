<?php
session_start();
include 'includes_db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, name, email, role, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // Store user details in session correctly
            $_SESSION['user'] = [
                'id' => $user['id'], 
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => 'user' // Ensuring correct role
            ];

            header("Location: index.php"); // Redirect to home
            exit();
        } else {
            $_SESSION['errorMessage'] = "Invalid password!";
        }
    } else {
        $_SESSION['errorMessage'] = "No account found with that email!";
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CrowdFunding</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    
    <div class="login-container">
        <h2>Login</h2>
        <?php
        if (isset($_SESSION['errorMessage'])) {
            echo '<p class="error-message">' . $_SESSION['errorMessage'] . '</p>';
            unset($_SESSION['errorMessage']);
        }
        ?>
        <form action="login.php" method="post">
            <div class="input-group">
                <label for="Email">Email</label>
                <input type="email" id="Email" placeholder="Enter Email" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" placeholder="Password" name="password" required>
            </div>
            <button type="submit">Login</button>
            <div class="create-acc">
                <span class="bottom text">New Here?</span>
                <a href="signup.php" class="create-new">Create Account</a> 
            </div>
        </form>
        
    </div>
</body>
</html>
