<?php
session_start();
include 'config.php';  // Include database connection

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
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
        }

        table {
            width: 80%;
            max-width: 1000px;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        input[type="number"] {
            width: 50px;
            padding: 5px;
        }

        input[type="submit"], .checkout-button, .back-button {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: inline-block;
            text-decoration: none;
        }

        input[type="submit"]:hover, .checkout-button:hover, .back-button:hover {
            background-color: #218838;
        }

        .checkout-button, .back-button {
            display: block;
            text-align: center;
            margin: 20px auto;
            width: 200px;
        }

        .back-button {
            background-color: #007bff;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
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
