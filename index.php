<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'config.php';  // Include database connection

// Check if the user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Count the total number of items in the cart
$cart_item_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];  // Sum up quantities of all items
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RowdyBookly</title>
    <link rel="stylesheet" href="css/style.css">
    <style> 
        <?php include 'css/style.css'; ?>
        
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
    
    <main class="main-container">
        <!-- Left side content -->
        <section class="welcome">
            <div class="welcome-text">
                <h2>Welcome to RowdyBookly!</h2>
                <p>Our mission is to provide a fast, convenient, and secure shopping experience, tailored to meet the unique needs of the bookworm community. Enjoy a reliable service that ensures smooth access to the books you love, anytime, anywhere.</p>
            </div>
        </section>
        
        <!-- Right side content: Books listing -->
        <aside class="book-sidebar">
            <section class="books-section">
                <h3>Recently Viewed</h3>
                <ul class="book-list">
                    <li>Book Title 1</li>
                    <li>Book Title 1</li>
                    <li>Book Title 2</li>
                    <li>Book Title 2</li>
                </ul>
                
                <h3>Popular</h3>
                <ul class="book-list">
                    <li>Book Title 3</li>
                    <li>Book Title 3</li>
                    <li>Book Title 4</li>
                    <li>Book Title 4</li>
                </ul>
            </section>
        </aside>
    </main>
    
    <footer>
        <p>&copy; 2024 RowdyBookly</p>
    </footer>

<!-- Cart Overlay and Sliding Cart Panel -->
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

<script src="javascript/cart-interaction.js"></script>

</body>
</html>
