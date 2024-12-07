<?php
session_start(); // Start a session to track admin users

// Fetch database URL from environment variable
$dbUrl = getenv('JAWSDB_URL');

// Parse the URL
$dbParts = parse_url($dbUrl);

$host = $dbParts['host'];
$dbname = ltrim($dbParts['path'], '/');
$username = $dbParts['user'];
$password = $dbParts['pass'];

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch admin data from the database
    $stmt = $pdo->prepare("SELECT * FROM Admin WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify admin credentials
    if ($admin && password_verify($password, $admin['password'])) {
        // Credentials are correct; set session variables
        $_SESSION['admin_id'] = $admin['user_id'];
        $_SESSION['admin_name'] = $admin['username'];

        // Redirect to admin dashboard
        header("Location: admin-dashboard.php");
        exit;
    } else {
        // Invalid email or password
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - RowdyBookly</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="login-container">
        <h1>Admin Login</h1>
        <?php if (!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
        <form action="admin-login.php" method="post">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit" class="login-button">Login</button>
        </form>
    </main>
</body>
</html>
