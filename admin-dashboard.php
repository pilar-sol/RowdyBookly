<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    $author = $_POST['author'];
    $publication_year = $_POST['publication_year'];
    $price = $_POST['price'];
    $description = $_POST['description'];

    // Handle file upload for the cover image
    $cover_image = null;
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $image_name = $_FILES['cover_image']['name'];
        $image_tmp_path = $_FILES['cover_image']['tmp_name'];
        $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        if ($image_extension === 'jpg') {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
            }

            $cover_image = $upload_dir . uniqid() . '.jpg';
            move_uploaded_file($image_tmp_path, $cover_image);
        } else {
            $message = "Only .jpg files are allowed for the cover image.";
        }
    } else {
        $message = "Please upload a valid .jpg cover image.";
    }

    // Validate and insert book data
    if (!empty($title) && !empty($author) && !empty($publication_year) && !empty($price) && !empty($description) && is_numeric($price) && is_numeric($publication_year) && $cover_image) {
        // Insert the book into the database
        $stmt = $pdo->prepare("INSERT INTO Books (title, cover_image_url, author_id, publication_year, price, description) 
                               VALUES (:title, :cover_image_url, (SELECT author_id FROM Authors WHERE name = :author LIMIT 1), :publication_year, :price, :description)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':cover_image_url', $cover_image);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':publication_year', $publication_year);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            $message = "Book added successfully!";
        } else {
            $message = "Failed to add the book. Ensure the author exists.";
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
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .dashboard-container {
            width: 50%;
            margin: 0 auto;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        form label {
            margin-top: 15px;
            font-weight: bold;
        }
        form input, form textarea, form button {
            margin-top: 5px;
            padding: 10px;
            font-size: 16px;
        }
        form textarea {
            resize: none;
        }
        .submit-button {
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
        .submit-button:hover {
            background-color: #0056b3;
        }
        .message {
            color: green;
            margin-top: 20px;
        }
    </style>
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
        <?php if (!empty($message)) { echo "<p class='message'>$message</p>"; } ?>
        <form action="admin-dashboard.php" method="post" enctype="multipart/form-data">
            <label for="title">Book Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="author">Author:</label>
            <input type="text" id="author" name="author" required>

            <label for="cover_image">Cover Image (.jpg):</label>
            <input type="file" id="cover_image" name="cover_image" accept=".jpg" required>

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
