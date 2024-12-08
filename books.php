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

// Sorting logic from URL (defaults to title and ascending)
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'title';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';

// Map sort_by to corresponding column
switch ($sort_by) {
    case 'price':
        $order_by = 'b.price';
        break;
    case 'publication_year':
        $order_by = 'b.publication_year';
        break;
    case 'title':
    default:
        $order_by = 'b.title';
        break;
}

// Apply the sort order
$order_clause = $order_by . ' ' . $sort_order;

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Count the total number of items in the cart
$cart_item_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];  // Sum up quantities of all items
    }
}

// Fetch genre description
$genre_description = '';
if ($genre !== 'All') {
    $genre_query = $conn->prepare("
        SELECT genre_name, description
        FROM Genres
        WHERE genre_name = ? 
    ");
    $genre_query->bind_param("s", $genre);
    $genre_query->execute();
    $genre_result = $genre_query->get_result();

    if ($genre_result->num_rows > 0) {
        $genre_data = $genre_result->fetch_assoc();
        $genre_description = $genre_data['description'];
    } else {
        die("Genre not found.");
    }
}

// Fetch books by genre and apply sorting
if ($genre === 'All') {
    // If the genre is 'All', fetch all books without filtering by genre
    $books_query = $conn->prepare("
        SELECT b.book_id, b.title, b.cover_image_url, b.publication_year, b.price, b.description, a.name AS author_name
        FROM Books b
        JOIN Authors a ON b.author_id = a.author_id
        ORDER BY $order_clause
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
        AND g.genre_name = ?
        ORDER BY $order_clause
    ");
    
    // Set the search term with wildcards
    $search_term = '%' . $search . '%';
    $books_query->bind_param('sss', $search_term, $search_term, $genre);
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
    <link rel="stylesheet" href="css/book-display.css">
    <style>
            <?php include 'css/book-display.css'; ?>
            <?php include 'css/style.css'; ?>
            .form-container {
                margin: 20px 0;
            }

            form input[type="text"] {
                padding: 10px;
                margin-right: 10px;
                width: 300px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            form select {
                padding: 10px;
                margin-right: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            form .sort-button {
                padding: 10px 20px;
                background-color: #ff6600;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            form .sort-button:hover {
                background-color: #e65c00;
            }

            .selected {
                background-color: #ff6600;
                color: white;
            }
        </style>
    <?php include 'navigation-bar.php'; ?>
</head>
<body>
    <div class="body0">
        <nav class="breadcrumb">
            <a href="index.php">Home</a>
            <span>&raquo;</span>
            <a href="categories.php">Categories</a>
            <span>&raquo;</span> <strong>
            <a href="books.php?genre=<?php echo htmlspecialchars(ucwords($genre)); ?>"> <?php echo htmlspecialchars(ucwords($genre)); ?> Genre</a>
        </strong>
        </nav>

        <?php if ($books_result->num_rows > 0): ?>
            <h2>Books in <?php echo htmlspecialchars(ucwords($genre)); ?> genre</h2>
            <p class="genre-description"><?php echo htmlspecialchars($genre_description); ?></p>

            <!-- Search and Sorting Form -->
            <form action="books.php" method="GET" class="form-container">
                <!-- Keep the Current Genre in URL -->
                <input type="hidden" name="genre" value="<?php echo htmlspecialchars($genre); ?>">
                <!-- Sort By Dropdown -->
                <select name="sort_by">
                    <option value="title" <?php echo $sort_by == 'title' ? 'selected' : ''; ?>>Sort by Title</option>
                    <option value="price" <?php echo $sort_by == 'price' ? 'selected' : ''; ?>>Sort by Price</option>
                    <option value="publication_year" <?php echo $sort_by == 'publication_year' ? 'selected' : ''; ?>>Sort by Publication Year</option>
                </select>
                <!-- Sort Order Dropdown -->
                <select name="sort_order">
                    <option value="ASC" <?php echo $sort_order == 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                    <option value="DESC" <?php echo $sort_order == 'DESC' ? 'selected' : ''; ?>>Descending</option>
                </select>

                <button type="submit" class="sort-button">Sort</button>
            </form>

            <!-- Display Books -->
            <div class="book-list-container">
                <div class="books">
                    <?php while ($book = $books_result->fetch_assoc()): ?>
                        <div class="book-item">
                            <a href="book-detail.php?book_id=<?php echo (int)$book['book_id']; ?>" class="book-link">
                                <img src="images/<?php echo htmlspecialchars($book['cover_image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                                <p><strong>By:</strong> <?php echo htmlspecialchars($book['author_name']); ?></p>
                                <p><strong>Price:</strong> $<?php echo number_format($book['price'], 2); ?></p>
                            <form action="add-to-cart.php" method="post">
                                <input type="hidden" name="book_id" value="<?php echo (int)$book['book_id']; ?>">
                                <input type="number" name="quantity" value="1" min="1" max="10">
                                <input type="hidden" name="genre" value="<?php echo htmlspecialchars($genre); ?>">
                                <input type="hidden" name="return_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>"> <!-- Current URL -->   
                                <button class="add-to-cart" type="submit">Add to Cart</button>
                            </form>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php else: ?>
            <p>No books found in this genre.</p>
        <?php endif; ?>
        <?php include 'cart-overlay.php'; ?>
    </div>
    
</body>
<footer>
    <p>&copy; 2024 RowdyBookly</p>
</footer>
</html>
