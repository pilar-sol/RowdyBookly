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

// Update user data
$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update profile information
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_address = $_POST['address'];

    // Update password if provided
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($new_password) && $new_password === $confirm_password) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Update profile and password
        $stmt = $conn->prepare("UPDATE Users SET username = ?, email = ?, address = ?, password_hash = ? WHERE user_id = ?");
        $stmt->bind_param("ssssi", $new_username, $new_email, $new_address, $hashed_password, $user_id);
    } elseif (empty($new_password)) {
        // Update only profile information
        $stmt = $conn->prepare("UPDATE Users SET username = ?, email = ?, address = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $new_username, $new_email, $new_address, $user_id);
    } else {
        $error_message = "Passwords do not match.";
    }

    if (empty($error_message) && $stmt->execute()) {
        $success_message = "Profile updated successfully!";
        $_SESSION['username'] = $new_username; // Update session variable
    } else {
        $error_message = "Error updating profile. Please try again.";
    }
    $stmt->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - RowdyBookly</title>
    <link rel="stylesheet" href="css/style.css">
    <style> 
        <?php include 'css/style.css'; ?>
    </style>
<?php include 'navigation-bar.php'; ?>
</head>
<body>
    
    
    <main class="main-container">
        <!-- Edit Profile Form -->
        <section class="edit-profile">
            <h2>Edit Profile</h2>
            <?php if ($success_message): ?>
                <p style="color:green;"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>
            <?php if ($error_message): ?>
                <p style="color:red;"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <form action="edit-profile.php" method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>">

                <hr>

                <label for="password">New Password:</label>
                <input type="password" id="password" name="password">
                
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password">
                
                <button type="submit" class="save-button">Save Changes</button>
            </form>
        </section>
    </main>
    <?php include 'cart-overlay.php'; ?>

    <footer>
        <p>&copy; 2024 RowdyBookly</p>
    </footer>

</body>
</html>
