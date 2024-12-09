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

// Initialize variables
$message = '';
$title = $author = $publication_year = $price = $description = '';
$is_staff_pick = 0;

// Handle book submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture and sanitize input
    $title = $_POST['title'] ?? '';
    $author = $_POST['author'] ?? '';
    $publication_year = $_POST['publication_year'] ?? '';
    $price = $_POST['price'] ?? '';
    $description = $_POST['description'] ?? '';
    $is_staff_pick = isset($_POST['is_staff_pick']) ? 1 : 0;

    // Handle file upload for the cover image
    $cover_image = null;
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $image_name = $_FILES['cover_image']['name'];
        $image_tmp_path = $_FILES['cover_image']['tmp_name'];
        $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        if ($image_extension === 'jpg') {
            $upload_dir = '';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $cover_image = $upload_dir . uniqid() . '.jpg';
            if (!move_uploaded_file($image_tmp_path, $cover_image)) {
                $message = "Failed to upload the cover image.";
            }
        } else {
            $message = "Only .jpg files are allowed for the cover image.";
        }
    }

    // Validate required fields
    if (!empty($title) && !empty($author) && !empty($publication_year) && !empty($price) && is_numeric($price) && is_numeric($publication_year)) {
        // Check if the author exists
        $authorStmt = $pdo->prepare("SELECT author_id FROM Authors WHERE name = :author LIMIT 1");
        $authorStmt->bindParam(':author', $author);
        $authorStmt->execute();
        $authorResult = $authorStmt->fetch(PDO::FETCH_ASSOC);

        if (!$authorResult) {
            // If the author doesn't exist, create a new one
            $insertAuthorStmt = $pdo->prepare("INSERT INTO Authors (name, bio) VALUES (:author, 'Biography not provided')");
            $insertAuthorStmt->bindParam(':author', $author);
            $insertAuthorStmt->execute();
            $author_id = $pdo->lastInsertId();
        } else {
            $author_id = $authorResult['author_id'];
        }

        // Insert the book into the database
        $stmt = $pdo->prepare("INSERT INTO Books (title, cover_image_url, author_id, publication_year, price, description, is_staff_pick) 
                               VALUES (:title, :cover_image_url, :author_id, :publication_year, :price, :description, :is_staff_pick)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':cover_image_url', $cover_image);
        $stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
        $stmt->bindParam(':publication_year', $publication_year, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':is_staff_pick', $is_staff_pick, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $message = "Book added successfully!";
        } else {
            $errorInfo = $stmt->errorInfo();
            $message = "Failed to add the book. SQL Error: " . $errorInfo[2];
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
            background-color: #f4f4f9;
        }
        .dashboard-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form label {
            display: block;
            margin: 15px 0 5px;
            font-weight: bold;
            color: #333;
        }
        form input, form textarea, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form textarea {
            resize: vertical;
            height: 100px;
        }
        form button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        form button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            color: green;
            margin-bottom: 20px;
        }
        nav a {
            margin: 0 10px;
            text-decoration: none;
            color: #007BFF;
        }
        nav a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1 style="text-align:center;">Admin Dashboard</h1>
        <nav style="text-align:center;">
            <a href="index.php">Home</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main class="dashboard-container">
        <h2>Add a New Book</h2>
        <?php if (!empty($message)) { echo "<p class='message'>$message</p>"; } ?>
        <form action="admin-add-book.php" method="post" enctype="multipart/form-data">
            <label for="title">Book Title:</label>
            <input type="text" id="title" name="title" placeholder="Enter book title" required value="<?= htmlspecialchars($title) ?>">

            <label for="author">Author:</label>
            <input type="text" id="author" name="author" placeholder="Enter author's name" required value="<?= htmlspecialchars($author) ?>">

            <label for="cover_image">Cover Image (.jpg):</label>
            <input type="file" id="cover_image" name="cover_image" accept=".jpg">

            <label for="publication_year">Publication Year:</label>
            <input type="number" id="publication_year" name="publication_year" placeholder="e.g., 2023" required value="<?= htmlspecialchars($publication_year) ?>">

            <label for="price">Price:</label>
            <input type="number" step="0.01" id="price" name="price" placeholder="e.g., 19.99" required value="<?= htmlspecialchars($price) ?>">

            <label for="description">Description:</label>
            <textarea id="description" name="description" placeholder="Enter book description"><?= htmlspecialchars($description) ?></textarea>

            <label for="is_staff_pick">
                <input type="checkbox" id="is_staff_pick" name="is_staff_pick" <?= $is_staff_pick ? 'checked' : '' ?>> Staff Pick
            </label>

            <button type="submit" class="submit-button">Add Book</button>
        </form>
    </main>
</body>
</html>
