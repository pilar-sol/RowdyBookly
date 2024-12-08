<?php
function getBooks($conn, $query, $sort_by, $sort_order) {
    // Default sort column
    $order_by = match ($sort_by) {
        'price' => 'b.price',
        'publication_year' => 'b.publication_year',
        default => 'b.title',
    };

    // Prepare the SQL query
    $order_clause = $order_by . ' ' . $sort_order;
    $search_query = $conn->prepare("
        SELECT DISTINCT b.book_id, b.title, b.cover_image_url, b.price, a.name AS author_name
        FROM Books b
        JOIN Authors a ON b.author_id = a.author_id
        LEFT JOIN BookGenres bg ON b.book_id = bg.book_id
        LEFT JOIN Genres g ON bg.genre_id = g.genre_id
        WHERE b.title LIKE ? OR a.name LIKE ? OR g.genre_name LIKE ?
        ORDER BY $order_clause
    ");

    $search_term = "%" . $query . "%";
    $search_query->bind_param("sss", $search_term, $search_term, $search_term);
    $search_query->execute();
    return $search_query->get_result();
}

function getBookDetails($conn, $book_id) {
    $book_query = $conn->prepare("
        SELECT b.book_id, b.title, b.cover_image_url, b.price, b.publication_year, b.description, a.name AS author_name
        FROM Books b
        JOIN Authors a ON b.author_id = a.author_id
        WHERE b.book_id = ?
    ");
    $book_query->bind_param("i", $book_id);
    $book_query->execute();
    return $book_query->get_result();
}

function getCartItemCount() {
    $cart_item_count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cart_item_count += $item['quantity'];
        }
    }
    return $cart_item_count;
}
?>
