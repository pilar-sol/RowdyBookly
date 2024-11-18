<?php
session_start();
include 'config.php';  // Include your database connection

//Check if genre is passed in the URL
if (!isset($_GET['genre'])) {
    die("Genre not specified.");
}

$genre = $_GET['genre'];

// Fetch books by genre
$books_query = $conn->prepare("
    SELECT b.book_id, b.title, b.cover_image_url, b.publication_year, b.price, b.description, a.name AS author_name
    FROM Books b
    JOIN BookGenres bg ON b.book_id = bg.book_id
    JOIN Genres g ON bg.genre_id = g.genre_id
    JOIN Authors a ON b.author_id = a.author_id
    WHERE g.genre_name = ?
");
$books_query->bind_param("s", $genre);
$books_query->execute();
$books_result = $books_query->get_result();

// Check if books were found for the genre
if ($books_result->num_rows > 0):
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books in <?php echo htmlspecialchars(ucwords($genre)); ?> Genre</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        
        <?php include 'css/style.css'; ?>
        <?php include 'css/book-display.css'; ?>
    </style>
</head>
<body>
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
            <a href="login.php" class="icon">👤</a>
            <a href="cart.php" class="icon">🛒</a>
        </nav>
    </header>
    
    <main class="book-list-container">
        <h2>Books in <?php echo htmlspecialchars(ucwords($genre)); ?> Genre</h2>
        <div class="books">
            <?php while ($book = $books_result->fetch_assoc()): ?>
            <div class="book-item">
                <img src="images/<?php echo htmlspecialchars($book['cover_image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                <p><strong>By:</strong> <?php echo htmlspecialchars($book['author_name']); ?></p>
                <p><strong>Price:</strong> $<?php echo number_format($book['price'], 2); ?></p>
                <!-- <p><strong>Published:</strong> <?php echo htmlspecialchars($book['publication_year']); ?></p>
                <p><?php echo htmlspecialchars($book['description']); ?></p>
                <p><?php echo htmlspecialchars( $book['book_id']); ?></p>
            -->
                <!-- Add to Cart Button -->
                <form action="add-to-cart.php" method="post">
                    <input type="hidden" name="book_id" value="<?php echo (int)$book['book_id']; ?>">
                    <input type="number" name="quantity" value="1" min="1" max="10">
                    <input type="hidden" name="genre" value="<?php echo htmlspecialchars($genre); ?>">
                    <button type="submit">Add to Cart</button>
                </form>
            </div>
            <?php endwhile; ?>
        </div>
    </main>
    
    <footer>
        <p>&copy; 2024 RowdyBookly</p>
    </footer>
</body>
</html>
<?php else: ?>
    <p>No books found in this genre.</p>
<?php endif; ?>

</body>
</html>