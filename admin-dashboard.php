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
$admin_id = $_SESSION['admin_id']; // This stays the same, assuming admin_id is the session key
$stmt = $pdo->prepare("SELECT username, email FROM Admin WHERE user_id = :admin_id"); // Replace admin_id with user_id in the query
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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #007BFF;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        .dashboard-container {
            max-width: 600px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: #333;
        }
        .welcome-text {
            margin-bottom: 30px;
        }
        .welcome-text p {
            font-size: 18px;
            margin: 10px 0;
            color: #555;
        }
        .action-button {
            display: inline-block;
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 5px;
            text-decoration: none;
            margin: 10px 5px;
            cursor: pointer;
        }
        .action-button:hover {
            background-color: #0056b3;
        }
        footer {
            text-align: center;
            padding: 20px;
            background-color: #333;
            color: white;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
    </header>
    <main class="dashboard-container">
        <section class="welcome">
            <div class="welcome-text">
                <h2>Welcome, <?php echo htmlspecialchars($admin['username']); ?>!</h2>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
            </div>
        </section>
        <section class="actions">
            <a href="admin-add-book.php" class="action-button">Add Book</a>
            <a href="admin-delete-book.php" class="action-button">Delete Book</a>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 RowdyBookly</p>
    </footer>
</body>
</html>
