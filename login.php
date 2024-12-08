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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - RowdyBookly</title>
    <style>
        /* Login Page Styling */

/* Centering the login container */
    
    header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 2rem 2.5rem; /* Adjust padding to make banner thicker */
        background-color: #333; /* Gray background color */
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
        width: 100%;
        max-width: 300px; /* Limits the width of the input fields */
        padding: 0.8rem;
        font-size: 1.2rem;
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f5f5f5;
        text-align: center;
    }
    
    /* Login button styling */
    .login-button {
        font-size: 1.5rem;
        background-color: #333;
        color: #fff;
        padding: 0.8rem 1.2rem;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        margin-top: 1rem;
        transition: background-color 0.3s ease;
    }
    
    .login-button:hover {
        background-color: #555;
    }
        </style>
    </style>
</head>
<body>
    <header>
        <a href="index.php" class="home-icon">🏠</a> <!-- Home icon to go back to the main page -->
    </header>
    <div class="login-wrapper">
        <main class="login-container">
            <h1>Login</h1>
            <?php if (!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
            <form action="login.php" method="post">
                <label for="email">Email:</label>
                <input class="textbox" type="text" id="email" name="email" required>
                
                <label for="password">Password:</label>
                <input class="textbox" type="password" id="password" name="password" required>
                
                <button type="submit" class="login-button">Login</button>
            </form>
            <p><a href="signup.php" class="signup-link">Not a member? Sign up</a></p>
        </main>
    </div>
</body>

</html>
