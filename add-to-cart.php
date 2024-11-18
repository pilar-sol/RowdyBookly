<?php
session_start();
include 'config.php';  // Database connection

// Check if book_id and quantity are passed via POST
if (isset($_POST['book_id']) && isset($_POST['quantity'])) {
    $genre = ($_POST['genre']);
    $book_id = intval($_POST['book_id']);
    $quantity = max(1, intval($_POST['quantity'])); // Ensure quantity is at least 1

    // Fetch book details from the database
    $sql = "SELECT book_id, title, price FROM Books WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if ($book) {
        // Check if cart exists in session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Add or update the book in the session cart
        if (isset($_SESSION['cart'][$book_id])) {
            // Update quantity if book already exists in cart
            $_SESSION['cart'][$book_id]['quantity'] += $quantity;
        } else {
            // Add book to cart
            $_SESSION['cart'][$book_id] = [
                'name' => $book['title'],
                'price' => $book['price'],
                'quantity' => $quantity
            ];
        }
        header("Location: books.php?genre=" . urlencode($genre) . "&message=added_to_cart");
        // Redirect to the books page with a success message
        //header("Location: books.php?message=added_to_cart");
        exit();
    } else {
        // Book not found
        echo "Book not found.";
    }

    $stmt->close();
} else {
    // Handle invalid input
    echo "Invalid request. Ensure book ID and quantity are provided.";
}

$conn->close();
?>
