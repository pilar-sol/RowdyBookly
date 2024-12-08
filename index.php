<?php
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
$sql = "SELECT b.book_id, b.title, b.cover_image_url, a.name AS author_name 
                FROM Books b 
                JOIN Authors a ON b.author_id = a.author_id 
                WHERE b.is_staff_pick = 1 LIMIT 7";

                $result = $conn->query($sql);
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
        <?php include 'css/index-format.css'; ?>

        .book-item a{
            texT-decoration: none;
            color: black;
        }
        .book-item a:hover{
            texT-decoration: none;
            color: brown;
        }
        
    </style>
    <?php
    include 'navigation-bar.php'; // Include the header
    ?>
</head>
<body>
    
    
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
                <h3>Staff Picks</h3>
                <ul class="book-list">
                    <!-- Fetch staff picks from the database -->
                    <?php if ($result->num_rows > 0): ?> 
                        <?php while ($row = $result->fetch_assoc()): ?> 
                            <li>
                                <div class="book-item">
                                <a href="book-detail.php?book_id=<?php echo (int)$row['book_id']; ?>" class="book-link">
                                    <img src="images/<?php echo htmlspecialchars($row['cover_image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" style="width:100px;height:150px;">
                                    <p><strong><?php echo htmlspecialchars($row['title']); ?></strong><br>by <?php echo htmlspecialchars($row['author_name']); ?></p>
                                    
                                </a>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li>No staff picks available at the moment.</li>
                    <?php endif; ?>
                </ul>
            </section>
        </aside>
    </main>
    
    <section class="popular-books">
    <h3>Popular Books 🔥</h3>
    <div class="book-stack">
        <?php
        // Fetch popular books from the database
        $sql = "SELECT book_id, title, cover_image_url FROM Books WHERE book_id IN (1, 2, 3, 4)";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='book-item'>
                        <a href='book-detail.php?book_id=" . htmlspecialchars($row['book_id']) . "'>
                            <p><strong>" . htmlspecialchars($row['title']) . "</strong></p>
                            <img src='images/" . htmlspecialchars($row['cover_image_url']) . "' alt='" . htmlspecialchars($row['title']) . "' class='book-cover'>
                            <div class='book-info'>
                            </div>
                        </a>
                    </div>";
            }
        } else {
            echo "<p>No popular books available at the moment.</p>";
        }
        ?>
    </div>
</section>



    <footer>
        <p>&copy; 2024 RowdyBookly</p>
    </footer>

<!-- Cart Overlay and Sliding Cart Panel -->
<?php
include 'cart-overlay.php'; ?>

<script src="javascript/cart-interaction.js"></script>

</body>
</html>
