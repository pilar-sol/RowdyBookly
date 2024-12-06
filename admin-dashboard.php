<?php
session_start();

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

// Initialize variables
$message = '';

// Handle book submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $cover_image_url = $_POST['cover_image_url'];
    $author_id = $_POST['author_id'];
    $publication_year = $_POST['publication_year'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Validate inputs
    if (
        !empty($title) && 
        !empty($author_id) && 
        !empty($publication_year) && 
        !empty($price) && 
        !empty($description) && 
        is_numeric($price) && 
        is_numeric($publication_year)
    ) {
        // Insert the book into the database
        $stmt = $pdo->prepare("INSERT INTO Books (title, cover_image_url, author_id, publication_year, price, description) 
                               VALUES (:title, :cover_image_url, :author_id, :publication_year, :price, :description)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':cover_image_url', $cover_image_url);
        $stmt->bindParam(':author_id', $author_id);
        $stmt->bindParam(':publication_year', $publication_year);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            $message = "Book added successfully!";
        } else {
            $message = "Failed to add the book.";
        }
    } else {
        $message = "Please fill in all required fields correctly.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Add Book</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main class="dashboard-container">
        <h2>Add a New Book</h2>
        <?php if (!empty($message)) { echo "<p style='color:green;'>$message</p>"; } ?>
        <form action="admin-dashboard.php" method="post">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="cover_image_url">Cover Image URL:</label>
            <input type="url" id="cover_image_url" name="cover_image_url">

            <label for="author_id">Author ID:</label>
            <input type="number" id="author_id" name="author_id" required>

            <label for="publication_year">Publication Year:</label>
            <input type="number" id="publication_year" name="publication_year" required>

            <label for="price">Price:</label>
            <input type="number" step="0.01" id="price" name="price" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <button type="submit" class="submit-button">Add Book</button>
        </form>
    </main>
</body>
</html>
