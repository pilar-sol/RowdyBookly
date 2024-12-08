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
            align-items: center; /* Centers content vertically */
            justify-content: center; /* Centers content horizontally */
            height: 100vh; /* Full viewport height */
            font-family: Arial, sans-serif;
            background-color: #f9f9f9; /* Light background for contrast */
        }

        /* Login container */
        .login-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px; /* Limits width for better responsiveness */
            text-align: center;
        }

        .login-container h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            color: #333;
        }

        /* Form labels and inputs */
        .login-container label {
            font-size: 1rem;
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
            text-align: left; /* Align labels to the left for readability */
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%; /* Full width within the container */
            padding: 0.8rem;
            font-size: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f5f5f5;
        }

        .login-container input[type="text"]:focus,
        .login-container input[type="password"]:focus {
            outline: none;
            border-color: #0073e6; /* Highlight border on focus */
            box-shadow: 0 0 4px rgba(0, 115, 230, 0.2);
        }

        .login-container .login-button {
            width: 100%;
            padding: 0.8rem;
            font-size: 1rem;
            color: #fff;
            background-color: #0073e6;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-container .login-button:hover {
            background-color: #005bb5;
        }

        .login-container p {
            margin-top: 1rem;
        }

        .login-container .signup-link {
            text-decoration: none;
            color: #0073e6;
        }

        .login-container .signup-link:hover {
            text-decoration: underline;
        }

        /* Error message styling */
        .login-container p.error {
            color: red;
            font-size: 0.9rem;
            margin-top: -1rem;
            margin-bottom: 1rem;
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
