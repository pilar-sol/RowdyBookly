<?php
session_start(); // Start a session to track logged-in users

// Fetch database URL from environment variable
//$dbUrl = getenv('CLEARDB_DATABASE_URL');
//$dbUrl = 'mysql://aqapvw1dt4k36dav:cp8n1pd5tgos08nw@qn0cquuabmqczee2.cbetxkdyhwsb.us-east-1.rds.amazonaws.com:3306/rp7q9eqqkuuf90wn';
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

    // Fetch user data from the database
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify user and password
    if ($user && password_verify($password, $user['password_hash'])) {
        // Password is correct; set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];

        // Redirect to a dashboard or home page
        header("Location: index.php");
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
    <title>Login - RowdyBookly</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh; /* Full height for centering */
            justify-content: flex-start;
        }
        
        .login-container {
            display: flex;
            flex-direction: column;
            align-items: center; /* Horizontally center the form */
            justify-content: center; /* Vertically center the form */
            height: 100vh; /* Full viewport height */
            text-align: center; /* Center text inside the container */
            padding: 2rem;
        }
        
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 2rem 2.5rem;
            background-color: #333;
            color: #fdfafa;
        }
        
        .login-container h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            color: #333;
        }
        
        /* Styling for form labels and inputs */
        .login-container label {
            font-size: 1.5rem;
            color: #333;
            display: block;
            margin: 0.5rem 0;
        }
        
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%; /* Full width to fill container */
            max-width: 300px; /* Limit the width */
            padding: 0.8rem;
            font-size: 1.2rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f5f5f5;
            text-align: center;
        }

    </style>
</head>
<body>
    <main class="login-container">
        <h1>Login</h1>
        <?php if (!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
        <form action="login.php" method="post">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit" class="login-button">Login</button>
        </form>
        <p><a href="signup.php" class="signup-link">Not a member? Sign up</a></p>
    </main>
</body>
</html>
