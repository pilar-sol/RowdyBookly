<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

// Fetch database URL from environment variable
$dbUrl = getenv('JAWSDB_URL');
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

// Fetch admin profile data
$admin_id = $_SESSION['admin_id'];
$stmt = $pdo->prepare("SELECT username, email, address FROM Admin WHERE user_id = :admin_id");
$stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    die("Admin not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RowdyBookly</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        <?php include 'css/style.css'; ?>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            text-align: center;
            margin: 0;
        }
        .dashboard-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        h2 {
            color: #007BFF;
        }
        .profile-info p {
            margin: 10px 0;
            font-size: 18px;
        }
        .action-buttons a {
            display: inline-block;
            margin: 10px;
            padding: 15px 30px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }
        .action-buttons a:hover {
            background-color: #0056b3;
        }
    </style>
    <?php include "navigation-bar.php"; ?>
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
    </header>
    <main class="dashboard-container">
        <section class="profile-info">
            <h2>Welcome, <?php echo htmlspecialchars($admin['username']); ?>!</h2>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($admin['address'] ?: 'Not provided'); ?></p>
        </section>

        <section class="action-buttons">
            <a href="admin-add-book.php">Add Book</a>
            <a href="admin-delete-book.php">Delete Book</a>
            <a href="index.php">Back to Home</a>
        </section>
    </main>
</body>
</html>
