<?php
session_start();
include 'config.php'; // Include your database connection

// Check if author_id is passed in the URL
if (!isset($_GET['author_id'])) {
    die("Author ID not specified.");
}

// Get the author_id from the URL
$author_id = (int) $_GET['author_id'];

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Count the total number of items in the cart
$cart_item_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];
    }
}

// Fetch author details
$author_query = $conn->prepare("SELECT name, bio, author_photo FROM Authors WHERE author_id = ?");
$author_query->bind_param("i", $author_id);
$author_query->execute();
$author_result = $author_query->get_result();

// Check if the author was found
if ($author_result->num_rows === 0) {
    die("Author not found.");
}
$author = $author_result->fetch_assoc();

// Fetch all books by the author
$books_query = $conn->prepare("
    SELECT book_id, title, cover_image_url, price, publication_year 
    FROM Books 
    WHERE author_id = ?
");
$books_query->bind_param("i", $author_id);
$books_query->execute();
$books_result = $books_query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($author['name']); ?> - Author Details</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/author-detail.css">
    <style>
        <?php include 'css/book-display.css' ?>
        .author-detail-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            gap: 20px;
        }

        .author-profile {
            text-align: center;
        }

        .author-profile img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .author-books {
            width: 100%;
        }

        .book-item {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 0;
            list-style: none;
        }

        .book-item li {
            background: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            width: 200px;
        }

        .book-item  img {
            width: 100%;
            height: auto;
            margin-bottom: 10px;
        }
        .book-item  a{
            text-decoration: none;
        }
        </style>
</head>
<body>
<?php include 'navigation-bar.php'; ?>
<div class="body0">
    <nav class="breadcrumb">
        <a href="index.php">Home</a>
        <span>&raquo;</span>
        <span class="current"><?php echo htmlspecialchars($author['name']); ?></span>
    </nav>
    <main>
        <div class="author-detail-container">
            <div class="author-profile">
                <img src="author-image/<?php echo htmlspecialchars($author['author_photo']); ?>" alt="<?php echo htmlspecialchars($author['name']); ?>">
                <h2><?php echo htmlspecialchars($author['name']); ?></h2>
                <p><?php echo nl2br(htmlspecialchars($author['bio'])); ?></p>
            </div>
            <div class="author-books">
                <h3>Books by <?php echo htmlspecialchars($author['name']); ?></h3>
                <ul class="book-item">
                    <?php if ($books_result->num_rows > 0): ?>
                        <?php while ($book = $books_result->fetch_assoc()): ?>
                            <li>
                                <a href="book-detail.php?book_id=<?php echo htmlspecialchars($book['book_id']); ?>">
                                    <img src="images/<?php echo htmlspecialchars($book['cover_image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                    <p><strong><?php echo htmlspecialchars($book['title']); ?></strong></p>
                                    <p>Price: $<?php echo number_format($book['price'], 2); ?></p>
                                    <p>Published: <?php echo htmlspecialchars($book['publication_year']); ?></p>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No books available for this author.</p>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </main>
    <?php include 'cart-overlay.php'; ?>
</div>
<footer>
    <p>&copy; 2024 RowdyBookly</p>
</footer>
<script src="javascript/cart-interaction.js"></script>
</body>
</html>
