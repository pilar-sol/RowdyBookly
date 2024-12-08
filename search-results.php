<?php
session_start();
include 'config.php'; // Include your database connection

// Check if the user is logged
$is_logged_in = isset($_SESSION['user_id']);

// Check if search query is passed
$query = isset($_GET['query']) ? $_GET['query'] : ''; // Get the search query from the URL or set to empty

// Default sort is by title in ascending order
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'title';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'ASC';  // Default to ascending

// Map the sort criteria to the corresponding SQL column and handle ascending/descending order
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

// Set the ORDER BY clause to reflect the selected sorting order
$order_clause = $order_by . ' ' . $sort_order;

// Get books that match the search query and apply sorting
$search_query = $conn->prepare("
    SELECT DISTINCT b.book_id, b.title, b.cover_image_url, b.price, a.name AS author_name
    FROM Books b
    JOIN Authors a ON b.author_id = a.author_id
    LEFT JOIN BookGenres bg ON b.book_id = bg.book_id
    LEFT JOIN Genres g ON bg.genre_id = g.genre_id
    WHERE b.title LIKE ? OR a.name LIKE ? OR g.genre_name LIKE ?
    ORDER BY $order_clause
");

$search_term = "%" . $query . "%";
$search_query->bind_param("sss", $search_term, $search_term, $search_term);
$search_query->execute();
$search_result = $search_query->get_result();

// Count the total number of items in the cart
$cart_item_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];  // Sum up quantities of all items
    }
}

// Check if a book_id is passed for showing the details directly
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

    // Get full details for the specific book
    $book_query = $conn->prepare("
        SELECT b.book_id, b.title, b.cover_image_url, b.price, b.publication_year, b.description, a.name AS author_name
        FROM Books b
        JOIN Authors a ON b.author_id = a.author_id
        WHERE b.book_id = ?
    ");
    $book_query->bind_param("i", $book_id);
    $book_query->execute();
    $book_result = $book_query->get_result();

    // If the book is found, display the details
    if ($book_result->num_rows > 0) {
        $book = $book_result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Rowdy Bookly</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
    <?php include 'css/style.css'; ?>
    <?php include 'css/book-display.css'; ?>
    .search-results {
        display: flex;
        flex-wrap: wrap;
        gap: 20px; /* Adds space between items */
        justify-content: flex-start; /* Aligns items to the left */
    }
     </style>
    <?php include 'navigation-bar.php'; ?>
</head>
<body>
    <div class="body0">
        <main>
        <form action="search-results.php" method="GET" style="display: inline;" class="form-container">
            <!-- Include the search query as a hidden field -->
            <input type="hidden" name="query" value="<?php echo htmlspecialchars($query); ?>">

            <!-- Sort By Dropdown -->
            <select name="sort_by">
                <option value="title" <?php echo $sort_by === 'title' ? 'selected' : ''; ?>>Sort by Title</option>
                <option value="price" <?php echo $sort_by === 'price' ? 'selected' : ''; ?>>Sort by Price</option>
                <option value="publication_year" <?php echo $sort_by === 'publication_year' ? 'selected' : ''; ?>>Sort by Publication Year</option>
            </select>

            <!-- Sort Order Dropdown -->
            <select name="sort_order">
                <option value="ASC" <?php echo $sort_order === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                <option value="DESC" <?php echo $sort_order === 'DESC' ? 'selected' : ''; ?>>Descending</option>
            </select>

            <!-- Sort Button -->
            <button type="submit" class="sort-button">Sort</button>
        </form>

            <?php if (isset($book)): ?>
                <div class="book-details">
                    <h2><?php echo htmlspecialchars($book['title']); ?></h2>
                    <img src="images/<?php echo htmlspecialchars($book['cover_image_url']); ?>" alt="Book Cover" width="300">
                    <p><strong>By:</strong> <?php echo htmlspecialchars($book['author_name']); ?></p>
                    <p><strong>Price:</strong> $<?php echo number_format($book['price'], 2); ?></p>
                    <p><strong>Publication Year:</strong> <?php echo htmlspecialchars($book['publication_year']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($book['description']); ?></p>
                    <!-- Add to Cart form -->
                    <form action="add-to-cart.php" method="post">
                        <input type="hidden" name="book_id" value="<?php echo (int)$book['book_id']; ?>">
                        <input type="number" name="quantity" value="1" min="1" max="10">
                        <button type="submit">Add to Cart</button>
                    </form>
                </div>
            <?php else: ?>
                <!-- If no book_id is provided, just show the search results -->
                <?php if ($search_result->num_rows === 0): ?>
                    <p>No books found matching your search criteria.</p>
                <?php else: ?>
                    <h2>Search Results for: <?php echo htmlspecialchars($query); ?> (<?php echo $search_result->num_rows; ?> results)</h2>
                    <div class="search-results">
                        <?php while ($book = $search_result->fetch_assoc()): ?>
                            <!-- Display each book with a link to show details on the same page -->
                            <div class="book-item">
                                <a href="book-detail.php?book_id=<?php echo (int)$book['book_id']; ?>" class="book-link">
                                    <img src="images/<?php echo htmlspecialchars($book['cover_image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>"
                                        style="width: 150px; height: 200px; object-fit: cover;">
                                    <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                                    <p><strong>By:</strong> <?php echo htmlspecialchars($book['author_name']); ?></p>
                                    <p><strong>Price:</strong> $<?php echo number_format($book['price'], 2); ?></p>
                                </a>
                                <p class="view-details">Click for more details</p>
                                <form action="add-to-cart.php" method="post">
                                    <input type="hidden" name="book_id" value="<?php echo (int)$book['book_id']; ?>">
                                    <input type="number" name="quantity" value="1" min="1" max="10">
                                    <button type="submit">Add to Cart</button>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
    <footer>
        <p>&copy; 2024 RowdyBookly</p>
    </footer>
</body>
</html>
