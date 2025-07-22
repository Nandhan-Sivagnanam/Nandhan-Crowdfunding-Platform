<?php
session_start();

$adminName = isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CrowdFunding</title>
    <link rel="stylesheet" href="css/admin_mainpage_.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  
    
</head>
<body>
    <!-- header -->
    <?php include 'include/admin_header.php'; ?>
    <!-- sidebar -->
    <?php include 'include/admin_sidebar.php'; ?>
</body>
</html>
