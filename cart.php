<?php
session_start();
include 'config.php';  // Include database connection

$is_logged_in = isset($_SESSION['user_id']);

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty. <a href='books.php'>Go back to shopping</a></p>";
    exit();
}

// Initialize cart totals
$subtotal = 0;
$tax_rate = 0.0825;  // 8.25% tax rate

// Update cart items or remove items
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_cart'])) {
        // Update item quantities
        foreach ($_POST['quantity'] as $book_id => $quantity) {
            if ($quantity == 0) {
                unset($_SESSION['cart'][$book_id]);  // Remove item if quantity is 0
            } else {
                $_SESSION['cart'][$book_id]['quantity'] = $quantity;
            }
        }
    } elseif (isset($_POST['remove_item'])) {
        foreach ($_POST['remove_item'] as $book_id => $value) {
            unset($_SESSION['cart'][$book_id]);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <style>
        <?php include 'css/style.css'?>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            
        }

        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 2px solid #ddd;
            border-radius:2px

        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        
        tr:hover {
            background-color: #f1f1f1;
        }

        button {
            background-color: #ff4f58;
            border: none;
            color: white;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover {
            background-color: #d43f48;
        }


        /* Button styling for checkout */
        .checkout-button, .back-button {
            padding: 10px 20px;
            background-color: #3c8dbc;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }

        .checkout-button:hover, .back-button:hover {
            background-color: #2b7aa1;
        }

        /* Responsive Cart Design */
        @media (max-width: 500px) {
            h2 {
                padding-top: 100px;
            }
            table {
                width: 100%;
                font-size: 14px;
            }
            .minimize{
                display:none;
            }
            th, td {
                padding: 10px;
            }

            .group1 {
                padding: 10px;
            }
        }
        .checkout-calculation {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .checkout-calculation p {
            font-size: 1.1em;
            margin: 10px 0;
            justify-content: space-between;
            align-items: center;
        }

        .checkout-calculation p span {
            font-weight: bold;
            color: #333;
        }

        .checkout-calculation p:last-child {
            font-size: 1.3em;
            font-weight: bold;
            color: #00b4cc; 
        }

        .checkout-calculation .totals {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .checkout-calculation .totals p {
            font-size: 1.2em;
            color: #555;
        }

        /* Add hover effect on checkout-calculation box */
        .checkout-calculation:hover {
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
    </style>
<?php include 'navigation-bar.php' ?>
</head>
<body>
    <h1>Your Shopping Cart</h1>

    <form action="cart.php" method="POST">
        <table>
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Quantity</th>
                    <th>Price ($)</th>
                    <th>book ID</th>
                    <th>Total ($)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch book details from the session cart
                foreach ($_SESSION['cart'] as $book_id => $item) {
                    $sql = "SELECT title, price FROM Books WHERE book_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $book_id);
                    $stmt->execute();
                    $stmt->bind_result($title, $price);
                    $stmt->fetch();
                    $stmt->close();

                    // Calculate the total for each item
                    $item_total = $price * $item['quantity'];
                    $subtotal += $item_total;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($title); ?></td>
                        <td>
                            <input type="number" name="quantity[<?php echo $book_id; ?>]" value="<?php echo $item['quantity']; ?>" min="0">
                        </td>
                        <td><?php echo number_format($price, 2); ?></td>
                        <td><?php echo $book_id; ?></td>
                        <td><?php echo number_format($item_total, 2); ?></td>
                        <td>
                            <button type="submit" name="remove_item[<?php echo $book_id; ?>]" value="1">Remove</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div style="text-align: center; margin: 20px;">
            <input type="submit" name="update_cart" value="Update Cart">
        </div>
    </form>

    <?php
    // Calculate tax and total
    $tax_amount = $subtotal * $tax_rate;
    $total = $subtotal + $tax_amount;
    ?>

    <div style="text-align: center;">
        <p>Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
        <p>Tax (8.25%): $<?php echo number_format($tax_amount, 2); ?></p>
        <p>Total: $<?php echo number_format($total, 2); ?></p>

        <a href="checkout.php" class="checkout-button">Proceed to Checkout</a>
        <a href="books.php" class="back-button">Back to Home</a>
    </div>

    <?php $conn->close(); ?>
</body>
</html>
