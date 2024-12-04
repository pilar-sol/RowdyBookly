<?php
session_start();
include 'config.php'; // Include your database connection

// Check if book_id is passed in the URL
if (!isset($_GET['book_id'])) {
    die("Book ID not specified.");
}

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Count the total number of items in the cart
$cart_item_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];
    }
}

$book_id = (int) $_GET['book_id'];

// Fetch book details
$book_query = $conn->prepare("
    SELECT b.title, b.cover_image_url, b.publication_year, b.price, b.description, a.name AS author_name
    FROM Books b
    JOIN Authors a ON b.author_id = a.author_id
    WHERE b.book_id = ?
");
$book_query->bind_param("i", $book_id);
$book_query->execute();
$book_result = $book_query->get_result();

// Check if the book was found
if ($book_result->num_rows === 0) {
    die("Book not found.");
}
$book = $book_result->fetch_assoc();

// Fetch genres for the book
$genres_query = $conn->prepare("
    SELECT g.genre_name
    FROM BookGenres bg
    JOIN Genres g ON bg.genre_id = g.genre_id
    WHERE bg.book_id = ?
");
$genres_query->bind_param("i", $book_id);
$genres_query->execute();
$genres_result = $genres_query->get_result();
$genres = [];
while ($row = $genres_result->fetch_assoc()) {
    $genres[] = $row['genre_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> - Details</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/book-detail.css">
    <header>
        <h1 class="logo">
            <a class="main-page" href="index.php">
            Rowdy<br>Bookly
            </a>
        </h1>
        <nav>
            <a href="categories.php" class="category-button">Categories</a>
            <input type="text" placeholder="Search">
            <button class="search-button">🔍</button>
            <?php if ($is_logged_in): ?>
                    <span class="login_welcome">Welcome, <?php echo $_SESSION['username']; ?>!</span>
                    <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php" class="icon">👤</a>
            <?php endif; ?>
            <a href="javascript:void(0);" class="icon" onclick="openCart()">🛒</a>
        </nav>
    </header>
</head>
<body>

<main>
    <div class="book-detail-container">
        <img src="images/<?php echo htmlspecialchars($book['cover_image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
        <div class="book-details">
            <h2><?php echo htmlspecialchars($book['title']); ?></h2>
            <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author_name']); ?></p>
            <p><strong>Genres:</strong> <?php echo htmlspecialchars(implode(", ", $genres)); ?></p>
            <p><strong>Published:</strong> <?php echo htmlspecialchars($book['publication_year']); ?></p>
            <p><strong>Price:</strong> $<?php echo number_format($book['price'], 2); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($book['description']); ?></p>
            <!-- Add to Cart Button -->
            <form action="add-to-cart.php" method="post">
                <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                <input type="number" name="quantity" value="1" min="1" max="10">
                <button type="submit">Add to Cart</button>
            </form>
        </div>
    </div>
</main>
<footer>
    <p>&copy; 2024 RowdyBookly</p>
</footer>
<script src="javascript/cart-interaction.js"></script>
</body>
</html>
