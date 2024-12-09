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

// Debugging: Print all records before deletion
echo "<h3>Records Before Deletion:</h3>";
$beforeStmt = $pdo->query("SELECT * FROM Books");
foreach ($beforeStmt as $row) {
    echo "Book ID: " . $row['book_id'] . " | Title: " . $row['title'] . "<br>";
}

// Handle book deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_book_id'])) {
    $delete_book_id = $_POST['delete_book_id'];

    // Debugging
    echo "Form submitted.<br>";
    echo "Received book ID for deletion: " . htmlspecialchars($delete_book_id) . "<br>";

    if (empty($delete_book_id)) {
        echo "No book ID received.<br>";
    }

    try {
        $deleteStmt = $pdo->prepare("DELETE FROM Books WHERE book_id = :book_id");
        $deleteStmt->bindParam(':book_id', $delete_book_id, PDO::PARAM_INT);

        if ($deleteStmt->execute()) {
            $message = "Book removed successfully!";
        } else {
            $errorInfo = $deleteStmt->errorInfo();
            $message = "Failed to remove the book. SQL Error: " . $errorInfo[2];
        }
    } catch (PDOException $e) {
        $message = "Error while deleting book: " . $e->getMessage();
    }
}

// Debugging: Print all records after deletion
echo "<h3>Records After Deletion:</h3>";
$afterStmt = $pdo->query("SELECT * FROM Books");
foreach ($afterStmt as $row) {
    echo "Book ID: " . $row['book_id'] . " | Title: " . $row['title'] . "<br>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Books</title>
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
        <h2>Manage Books</h2>
        <?php if (!empty($message)) { echo "<p class='message'>$message</p>"; } ?>

        <table>
            <thead>
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Publication Year</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($books)): ?>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['book_id']); ?></td>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['publication_year']); ?></td>
                            <td><?php echo htmlspecialchars($book['price']); ?></td>
                            <td>
                                <form action="admin-dashboard.php" method="post">
                                    <input type="hidden" name="delete_book_id" value="<?php echo $book['book_id']; ?>">
                                    <button type="submit" class="delete-button">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No books found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
