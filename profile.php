<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'config.php';  // Include database connection

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);
if (!$is_logged_in) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

// Fetch user profile data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, address FROM Users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $address);
$stmt->fetch();
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile - RowdyBookly</title>
    <link rel="stylesheet" href="css/style.css">
    <style> 
        <?php include 'css/style.css'; ?>
    </style>
    <?php include "navigation-bar.php" ?>
</head>
<body>
    
    
    <main class="main-container">

        <!-- Left side content: Profile Details -->
        <section class="welcome">
            <div class="welcome-text">
                <h2>Your Profile</h2>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($address ?: 'Not provided'); ?></p>
                <!--<a href="edit-profile.php" class="edit-button">Edit Profile</a>-->
                <a href="edit-profile.php" class="admin-login-button" style="padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">Edit Profile</a>
                <a href="admin-login.php" class="admin-login-button" style="padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">Admin Login</a>
            </div>
        </section>
    </main>
    

    <?php include "cart-overlay.php" ?>
    <footer>
        <p>&copy; 2024 RowdyBookly</p>
    </footer>

<script src="javascript/cart-interaction.js"></script>

</body>
</html>
