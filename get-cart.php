<?php
session_start();

// Check if there are items in the cart
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cart_items = $_SESSION['cart'];
    $response = [];

    foreach ($cart_items as $book_id => $item) {
        // Ensure you're fetching the details based on book_id
        $response[] = [
            'book_id' => $book_id,          // Include book_id for reference
            'name' => $item['name'],        // The book title
            'quantity' => $item['quantity'],// Quantity of the book in the cart
            'price' => $item['price']       // The price of the book
        ];
    }

    // Return the cart items as JSON
    echo json_encode($response);
} else {
    // Return an empty array if there are no items
    echo json_encode([]);
}
?>
