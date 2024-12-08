<style>
<?php include 'css/cart-overlay.css' ?>


.empty-message {
    color: rgb(241, 180, 67);
    font-size: 14px;
    background-color: #8b2800;
    padding: 10px;
    border-radius: 5px;
    text-decoration: none;
}

.empty-message:hover {
    background-color: #ff733a;
    color: beige;}

    </style>

<div class="overlay" id="overlay">
    <div class="cart-panel" id="cartPanel">
        <div class="cart-header">
            <h2>Shopping Cart 🛒</h2>
            <button class="close-cart" onclick="closeCart()">✖</button>
        </div>
        <div class="cart-content">
            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                <ul class="cart-items-list">
                    <?php
                    $subtotal = 0;
                    foreach ($_SESSION['cart'] as $book_id => $item) {
                        $sql = "SELECT title, price, cover_image_url FROM Books WHERE book_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $book_id);
                        $stmt->execute();
                        $stmt->bind_result($title, $price, $cover_image_url);
                        $stmt->fetch();
                        $stmt->close();
                        $item_total = $price * $item['quantity'];
                        $subtotal += $item_total;
                    ?>
                        <li class="cart-item">
                            <div class="cart-item-image">
                                <img src="images/<?php echo htmlspecialchars($cover_image_url); ?>" alt="<?php echo htmlspecialchars($title); ?>" class="book-preview">
                            </div>
                            <div class="cart-item-description">
                                <h4 class="cart-item-title"><?php echo htmlspecialchars($title); ?></h4>
                                <p class="cart-item-price">Price: $<?php echo number_format($price, 2); ?></p>
                                <p class="cart-item-quantity">Quantity: <?php echo $item['quantity']; ?></p>
                                <p class="cart-item-total">Total: $<?php echo number_format($item_total, 2); ?></p>
								 <!-- Remove button -->
                                <form action="remove-from-cart.php" method="POST" class="remove-item-form">
									<input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
									<input type="hidden" name="current_page" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
									<button type="submit" class="remove-item-button">Remove</button>
								</form>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
                <hr>
                <p><strong>Subtotal (before taxes): $<?php echo number_format($subtotal, 2); ?></strong></p>
                <a href="checkout.php" class="checkout-button">Checkout!</a>
            <?php else: ?>
                <a class="empty-message" href="categories.php"> Your cart is empty. Let's put some books into it first!</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="javascript/cart-interaction.js"></script>
