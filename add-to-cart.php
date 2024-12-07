<?php
session_start();
include 'config.php';

// Check if required POST parameters are set
if (isset($_POST['book_id'], $_POST['quantity'], $_POST['return_url'])) {
    $book_id = intval($_POST['book_id']);
    $quantity = max(1, intval($_POST['quantity']));
    $return_url = $_POST['return_url']; // URL to redirect back to

    // Fetch book details from the database
    $sql = "SELECT book_id, title, price FROM Books WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if ($book) {
        // Initialize the cart if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Add or update the book in the session cart
        if (isset($_SESSION['cart'][$book_id])) {
            $_SESSION['cart'][$book_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$book_id] = [
                'name' => $book['title'],
                'price' => $book['price'],
                'quantity' => $quantity
            ];
        }

        // Redirect back to the return URL
        header("Location: " . $return_url . "&message=added_to_cart");
        exit();
    } else {
        echo "Book not found.";
    }

    $stmt->close();
} else {
    echo "Invalid request. Ensure all required fields are provided.";
}

$conn->close();
