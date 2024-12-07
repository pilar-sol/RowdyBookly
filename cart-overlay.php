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
                            <div class='cart-item'>
                                <strong><?php echo htmlspecialchars($title); ?></strong><br>
                                Price: $<?php echo number_format($price, 2); ?><br>
                                Quantity: <?php echo $item['quantity']; ?><br>
                                Total: $<?php echo number_format($item_total, 2); ?>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
                <hr>
                <p><strong>Subtotal (before taxes): $<?php echo number_format($subtotal, 2); ?></strong></p>
                <a href="review-order.php" class="checkout-button">Review order</a>
            <?php else: ?>
                <p>Your cart is empty. <a href="categories.php">Browse Books</a></p>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="javascript/cart-interaction.js"></script>
