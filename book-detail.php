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
    <style>
                body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .book-detail-container {
            display: flex;
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .book-detail-container img {
            width: 40%;
            object-fit: cover;
        }
        .book-details {
            padding: 20px;
            width: 60%;
            display: flex;
            flex-direction: column;
        }
        .book-details h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .book-details p {
            margin: 5px 0;
        }
        .book-details form {
            margin-top: 20px;
        }
        .book-details form input[type="number"] {
            width: 60px;
            margin-right: 10px;
        }
        .book-details form button {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        .book-details form button:hover {
            background: #0056b3;
        }
        .price {
            font-size: 24px;
            font-weight: bold;
            color: #d9534f;
            margin-top: 10px;
        }
        .actions {
            margin-top: 20px;
        }
        .actions button {
            margin-right: 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .actions .buy-now {
            background: #d9534f;
            color: white;
        }
        .actions .add-to-cart {
            background: #5cb85c;
            color: white;
        }
        footer {
            text-align: center;
            margin-top: 20px;
        }
        </style>
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
                <p><strong>Genres:</strong> 
                    <?php echo !empty($genres) ? htmlspecialchars(implode(", ", $genres)) : "Not specified"; ?>
                </p>
                <p><strong>Published:</strong> <?php echo htmlspecialchars($book['publication_year']); ?></p>
                <p><strong>Price:</strong> $<?php echo number_format($book['price'], 2); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($book['description']); ?></p>
                <form action="add-to-cart.php" method="post">
                    <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                    <input type="number" name="quantity" value="1" min="1" max="10">
                    <button type="submit">Add to Cart</button>
                </form>
            </div>
        </div>
    </main>
<div class="overlay" id="overlay">
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

<footer>
    <p>&copy; 2024 RowdyBookly</p>
</footer>
<script src="javascript/cart-interaction.js"></script>
</body>
</html>
