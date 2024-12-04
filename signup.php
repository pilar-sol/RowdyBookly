<?php
// Start session
session_start();

// Database configuration
$host = 'localhost';      // Database host
$dbname = 'rowdybookly';  // Database name
$username = 'root';       // Database username
$password = '';           // Database password

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize error messages
$error = '';
$success = '';

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    // Validate input
    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        // Check if email or username already exists
        $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = :email OR username = :username");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        if ($stmt->fetch()) {
            $error = "Email or username already exists.";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into database
            $stmt = $pdo->prepare("INSERT INTO Users (email, username, password_hash, address, created_at) 
                                   VALUES (:email, :username, :password_hash, :address, :created_at)");
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password_hash', $hashedPassword);
            $stmt->bindValue(':address', ''); // Default value for address (can be updated later)
            $stmt->bindValue(':created_at', date('Y-m-d H:i:s'));

            if ($stmt->execute()) {
                $success = "Account created successfully. <a href='login.php'>Log in here</a>";
            } else {
                $error = "An error occurred. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - RowdyBookly</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <a href="index.php" class="home-icon">🏠</a> <!-- Home icon to go back to the main page -->
    </header>

    <main class="signup-container">
        <h1>Create Account</h1>
        <?php 
        if (!empty($error)) { 
            echo "<p style='color:red;'>$error</p>"; 
        } 
        if (!empty($success)) { 
            echo "<p style='color:green;'>$success</p>"; 
        } 
        ?>
        <form action="signup.php" method="post">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>
            
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="confirm-password">Verify Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" required>
            
            <button type="submit" class="signup-button">➔</button>
        </form>
        <p><a href="login.php" class="login-link">Already a member? Log in</a></p>
    </main>
</body>
</html>
