<?php
session_start();
include 'config.php';  // Include your database connection

// Check if genre is passed in the URL
if (!isset($_GET['genre'])) {
    die("Genre not specified.");
}

$genre = $_GET['genre'];

// Fetch books by genre
$books_query = $conn->prepare("
    SELECT b.title, b.cover_image_url, b.publication_year, b.price, b.description, a.name AS author_name
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
    <style><?php include 'css/style.css'; ?>
        .book-list-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .book-item {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 200px;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .book-item img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .book-item h3 {
            font-size: 1.2rem;
            margin: 10px 0;
        }

        .book-item p {
            font-size: 0.9rem;
            color: #555;
        }

        .book-item button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        .book-item button:hover {
            background-color: #218838;
        }
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
                <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author_name']); ?></p>
                <p><strong>Price:</strong> $<?php echo number_format($book['price'], 2); ?></p>
                <p><strong>Published:</strong> <?php echo htmlspecialchars($book['publication_year']); ?></p>
                <!-- <p><?php echo htmlspecialchars($book['description']); ?></p> -->
                
                <!-- Add to Cart Button -->
                <form action="add_to_cart.php" method="POST">
                    <!-- <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>"> -->   
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