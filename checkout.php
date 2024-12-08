<?php
session_start();
include 'config.php'; 

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty. <a href='categories.php'>Go back to shopping</a></p>";
    exit();
}

$is_logged_in = isset($_SESSION['user_id']);
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


// Initialize cart totals
$subtotal = 0;
$tax_rate = 0.0825; // 8.25% tax rate

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
}

// Calculate totals
$tax = $subtotal * $tax_rate;
$total = $subtotal + $tax;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_purchase'])) {
    // Gather form data
    $firstname = htmlspecialchars($_POST['firstname']);
    $lastname = htmlspecialchars($_POST['lastname']);
    $address = htmlspecialchars($_POST['address']);
    $city = htmlspecialchars($_POST['city']);
    $state = htmlspecialchars($_POST['state']);
    $zip = htmlspecialchars($_POST['zip']);
    
    // Validate required fields
    if (empty($firstname) || empty($lastname) || empty($address) || empty($city) || empty($state) || empty($zip)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Calculate totals
        $subtotal = 0;
        $tax_rate = 0.0825;
        $discount = 0;

        foreach ($_SESSION['cart'] as $book_id => $item) {
            $sql = "SELECT price FROM Books WHERE book_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $book_id);
            $stmt->execute();
            $stmt->bind_result($price);
            $stmt->fetch();
            $stmt->close();

            $item_total = $price * $item['quantity'];
            $subtotal += $item_total;
        }

        // Calculate tax and total
        $tax = $subtotal * $tax_rate;
        $total = $subtotal + $tax - $discount;

        // Save order details to the database
        $user_id = $_SESSION['user_id'] ?? 0;
        $created_at = date('Y-m-d H:i:s');
		
		// Check if the user is logged in
		$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

        
        // Insert order into the Orders table
        $order_query = "INSERT INTO Orders 
                        (user_id, total, tax, discount, status, firstname, lastname, address, city, state, zip, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $order_stmt = $conn->prepare($order_query);
        $status = 'pending'; 
        $order_stmt->bind_param("iddsssssssss", $user_id, $total, $tax, $discount, $status, $firstname, $lastname, $address, $city, $state, $zip, $created_at);
        $order_stmt->execute();
        $order_id = $order_stmt->insert_id;
        $order_stmt->close();

        // Save order items to the Order_Items table
        foreach ($_SESSION['cart'] as $book_id => $item) {
            $item_query = "INSERT INTO OrderItems (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)";
            $item_stmt = $conn->prepare($item_query);
            $item_stmt->bind_param("iiid", $order_id, $book_id, $item['quantity'], $item['price']);
            $item_stmt->execute();
            $item_stmt->close();
        }

        unset($_SESSION['cart']);

        header("Location: order-confirmation.php?order_id=$order_id");
        exit();
    }
}


	


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - RowdyBookly</title>
    <link rel="stylesheet" href="css/style.css">
	<style> 
        <?php include 'css/style.css'; ?>
	.container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
	.cart-items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 1em;
            font-family: Arial, sans-serif;
        }

        .cart-items-table thead {
            background-color: #f4f4f4;
        }

        .cart-items-table th, .cart-items-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .cart-items-table th {
            font-weight: bold;
        }

        .cart-items-table tbody tr:hover {
            background-color: #f9f9f9;
        }

        .cart-item-image {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }

        .cart-items-table td img {
            display: block;
            margin: auto;
        }
        .thanh-tien{
            margin-right: 0;
            padding-left: 80%;
        }
	.remove-button {
	color: white;
            background-color: #ff4f58;
            border: none;
            color: white;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 14px;
	    border-radius:10px;
        }

        .remove-button:hover {
            background-color: #d43f48;
        }
    </style>
	<?php
    include 'navigation-bar.php'; // Include the header
    ?>
</head>
<body>

    <!-- Main Content -->
    <div class="container">
        <?php if (isset($error_message)): ?>
            <p style="color: red; text-align: center;"><?php echo $error_message; ?></p>
        <?php endif; ?>
		
		<!-- Cart Summary -->
		<div class="totals">
			<h1>Checkout</h1>

			<!-- Display items being purchased -->
			<h2>Items in your cart:</h2>

			<table class="cart-items-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $book_id => $item): ?>
                    <?php
                        // Fetch the book details from the database
                        $sql = "SELECT title, price, cover_image_url FROM Books WHERE book_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $book_id);
                        $stmt->execute();
                        $stmt->bind_result($title, $price, $cover_image_url);
                        $stmt->fetch();
                        $stmt->close();

                        // Calculate the total for this item
                        $item_total = $price * $item['quantity'];
                    ?>
                    <tr>
                        <td>
                            <img src="images/<?php echo $cover_image_url; ?>" alt="Book Cover" class="cart-item-image">
                        </td>
                        <td><?php echo htmlspecialchars($title); ?></td>
                        <td>$<?php echo number_format($price, 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item_total, 2); ?></td>
                        <td>
                            <form action="checkout.php" method="POST" style="display: inline;">
                                <button type="submit" class= "remove-button" name="remove_item[<?php echo $book_id; ?>]" value="1">✖</button>
                    </form>
                </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

			
			<!-- Display totals -->
			<div class="thanh-tien">  
			<p>Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
			<p>Tax (8.25%): $<?php echo number_format($tax, 2); ?></p>
			<p>Total: $<?php echo number_format($total, 2); ?></p>
			</div>
		</div>

        <!-- Checkout Form -->
        <form action="checkout.php" method="POST" class="checkout-form">
			<div class="form-group">
				<h1>Shipping Address:</h1>
				<input type="text" id="firstname" name="firstname" placeholder="First Name" required>
			</div>
			
			<div class="form-group">
			<input type="text" id="lastname" name="lastname" placeholder="Last Name" required>
			</div>

			<div class="form-group">
				<input type="text" id="address" name="address" placeholder="Address" required>
			</div>
			
			<div class="form-group">
			<input type="text" id="city" name="city" placeholder="City" required>
			</div>
			
			<div class="form-group">
			<input type="text" id="state" name="state" placeholder="State" required>
			</div>
			
			<div class="form-group">
			<input type="text" id="zip" name="zip" placeholder="Zip Code:" required>
			</div>

			<button type="submit" name="confirm_purchase" class="checkout-button">Confirm Purchase</button>
		</form>

    </div>
	
	<?php include 'cart-overlay.php'; ?>
	<script src="javascript/cart-interaction.js"></script>


    <!-- Footer -->
    <footer>
        <p>&copy; 2024 RowdyBookly</p>
    </footer>
</body>
</html>
