<?php
session_start();
include 'config.php';  // Include your database connection

//Check if genre is passed in the URL
if (!isset($_GET['genre'])) {
    die("Genre not specified.");
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

$genre = $_GET['genre'];

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

// Fetch books by genre
if ($genre === 'All') {
    // If the genre is 'All', fetch all books without filtering by genre
    $books_query = $conn->prepare("
        SELECT b.book_id, b.title, b.cover_image_url, b.publication_year, b.price, b.description, a.name AS author_name
        FROM Books b
        JOIN Authors a ON b.author_id = a.author_id
    ");
} else {
    // Fetch books by the specified genre
    $books_query = $conn->prepare("
        SELECT b.book_id, b.title, b.cover_image_url, b.publication_year, b.price, b.description, a.name AS author_name
        FROM Books b
        JOIN BookGenres bg ON b.book_id = bg.book_id
        JOIN Genres g ON bg.genre_id = g.genre_id
        JOIN Authors a ON b.author_id = a.author_id
        WHERE g.genre_name = ?
    ");
    $books_query->bind_param("s", $genre);
}
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
    <style>
        
        <?php include 'css/style.css'; ?>
        <?php include 'css/book-display.css'; ?>
        body {
            font-family: Arial, sans-serif;
            
        }
        .body0{
            padding: 20px;
        }
        .breadcrumb {
            font-size: 1rem;
            margin: 20px 0;
            display: flex;
            align-items: center;
        }
        .breadcrumb a {
            text-decoration: none;
            color: #007bff;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        .breadcrumb span {
            margin: 0 5px;
            color: #6c757d;
        }
        .breadcrumb .current {
            color: #495057;
            font-weight: bold;
        }
    </style>
    
</head>
<body>
    <div class=body0>
    <nav class="breadcrumb">
        <a href="index.php">Home</a>
        <span>&raquo;</span>
        <a href="categories.php">Categories</a>
        <span>&raquo;</span>
        <span class="current"><?php echo htmlspecialchars(ucwords($genre)); ?> Genre</span>
    </nav>

    <?php if ($books_result->num_rows > 0):?>

        <h2><a href ="index.php">Home</a> >> <?php echo htmlspecialchars(ucwords($genre)); ?> Genre</h2>
        <p class="genre-description"><?php echo htmlspecialchars($genre_description); ?></p>    
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
                                <button type="submit">Add to Cart</button>
                            </form>
                        </a>
                    </div>
        
                
            
            <?php endwhile; ?>
            </div>
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
                    // Initialize subtotal
                    $subtotal = 0;

                    foreach ($_SESSION['cart'] as $book_id => $item) {
                        // Fetch book details from the database
                        $sql = "SELECT title, price FROM Books WHERE book_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $book_id);
                        $stmt->execute();
                        $stmt->bind_result($title, $price);
                        $stmt->fetch();
                        $stmt->close();

                        // Calculate total price for the item
                        $item_total = $price * $item['quantity'];
                        $subtotal += $item_total;
                    ?>
                        <li>
                            <div class='cart-item'>
                                <strong><?php echo htmlspecialchars($title); ?></strong><br>
                                Price: $<?php echo number_format($price, 2); ?> <br>
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
</div>
</body>
<script src="javascript/cart-interaction.js"></script>
</html>
