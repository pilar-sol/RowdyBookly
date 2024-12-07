<?php
session_start();
include 'config.php';  // Include your database connection

// Capture the search term from the GET request
$search = isset($_GET['query']) ? $_GET['query'] : '';

// Check if genre is passed in the URL
if (isset($_GET['genre']) && !empty($_GET['genre'])) {
    $genre = $_GET['genre'];
} else {
    $genre = 'All';  // Set default genre to 'All' if not set in the URL
}

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Count the total number of items in the cart
$cart_item_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];  // Sum up quantities of all items
    }
}

// Build the SQL query based on whether a search term is provided
if ($genre == 'All' && empty($search)) {
    // No search term, fetch all books without filtering
    $books_query = $conn->prepare("
        SELECT b.book_id, b.title, b.cover_image_url, b.publication_year, b.price, b.description, a.name AS author_name
        FROM Books b
        JOIN Authors a ON b.author_id = a.author_id
    ");
} else {
    // Search for books by title or author, and filter by genre if specified
    $books_query = $conn->prepare("
        SELECT b.book_id, b.title, b.cover_image_url, b.publication_year, b.price, b.description, a.name AS author_name
        FROM Books b
        JOIN Authors a ON b.author_id = a.author_id
        LEFT JOIN BookGenres bg ON b.book_id = bg.book_id
        LEFT JOIN Genres g ON bg.genre_id = g.genre_id
        WHERE (b.title LIKE ? OR a.name LIKE ?)
        AND (? = 'All' OR g.genre_name = ?)
    ");
    
    // Set the search term with wildcards
    $search_term = '%' . $search . '%';
    $books_query->bind_param('ssss', $search_term, $search_term, $genre, $genre);
}

// Execute the query
$books_query->execute();
$books_result = $books_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books in <?php echo htmlspecialchars(ucwords($genre)); ?> Genre</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/book-display.css">
</head>
<body>
    <header>
        <h1 class="logo">
            <a class="main-page" href="index.php">
            Rowdy Bookly
            </a>
        </h1>
        <nav>
            <a href="categories.php" class="category-button">Categories</a>
            <form action="search-results.php" method="get">
            <input type="text" name="query" placeholder="Search books by title, author, genre" required>
            <button type="submit" class="search-button">🔍</button>
            <?php if ($is_logged_in): ?>
                    <a href="profile.php" class="icon">👤</a>
                    <a href="logout.php" class="icon" title="Logout">
                        <img src="images/logout.png" alt="Logout" style="width:30px; height:30px;">
            </a>
            <?php else: ?>
                <a href="profile.php" class="icon">👤</a>
            <?php endif; ?>
            <a href="javascript:void(0);" class="icon" onclick="openCart()">🛒</a>
        </nav>
    </header>

<?php if ($books_result->num_rows > 0): ?>
    <div class="book-list-container">
        <h2>Books in <?php echo htmlspecialchars(ucwords($genre)); ?> Genre</h2>
        <div class="books">
            <?php while ($book = $books_result->fetch_assoc()): ?>
                <div class="book-item">
                    <a href="book-detail.php?book_id=<?php echo (int)$book['book_id']; ?>" class="book-link">
                        <img src="images/<?php echo htmlspecialchars($book['cover_image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                        <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p><strong>By:</strong> <?php echo htmlspecialchars($book['author_name']); ?></p>
                        <p><strong>Price:</strong> $<?php echo number_format($book['price'], 2); ?></p>
                    </a>
                    <form action="add-to-cart.php" method="post">
                        <input type="hidden" name="book_id" value="<?php echo (int)$book['book_id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" max="10">
                        <input type="hidden" name="genre" value="<?php echo htmlspecialchars($genre); ?>">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php else: ?>
    <p>No books found in this genre.</p>
<?php endif; ?>

<footer>
    <p>&copy; 2024 RowdyBookly</p>
</footer>

<div class="overlay" id="overlay"></div>
<div class="cart-panel" id="cartPanel">
    <div class="cart-header">
        <h2>Shopping Cart 🛒</h2>
        <button class="close-cart" onclick="closeCart()">✖</button>
    </div>
    <div class="cart-content">
        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            <ul>
                <?php
                $subtotal = 0;
                foreach ($_SESSION['cart'] as $book_id => $item) {
                    $sql = "SELECT title, price FROM Books WHERE book_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $book_id);
                    $stmt->execute();
                    $stmt->bind_result($title, $price);
                    $stmt->fetch();
                    $stmt->close();

                    $item_total = $price * $item['quantity'];
                    $subtotal += $item_total;
                ?>
                    <li>
                        <div class="cart-item">
                            <strong><?php echo htmlspecialchars($title); ?></strong><br>
                            Price: $<?php echo number_format($price, 2); ?><br>
                            Quantity: <?php echo $item['quantity']; ?><br>
                            Total: $<?php echo number_format($item_total, 2); ?>
                        </div>
                    </li>
                <?php } ?>
            </ul>
            <hr>
            <p><strong>Subtotal(before taxes): $<?php echo number_format($subtotal, 2); ?></strong></p>
            <a href="review-order.php" class="checkout-button">Review order</a>
        <?php else: ?>
            <p>Your cart is empty. <a href="categories.php">Browse Books</a></p>
        <?php endif; ?>
    </div>
</div>

<script src="javascript/cart-interaction.js"></script>

</body>
</html>
