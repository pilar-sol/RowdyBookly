<?php
session_start();
include 'config.php'; 

// Fetch all authors from the database
$authors_query = "SELECT author_id, name, author_photo FROM Authors";
$authors_result = $conn->query($authors_query);

$is_logged_in = isset($_SESSION['user_id']);

// Count the total number of items in the cart
$cart_item_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_item_count += $item['quantity'];  
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authors - RowdyBookly</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        <?php include 'css/style.css'; ?>
        
        .authors-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 20px;
        }

        .author {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            width: 150px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-decoration: none;
            background-color: #f9f9f9;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .author:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .author img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .author p {
            font-size: 16px;
            color: #333;
        }

        .author:hover p {
            color: #e67f00;
        }
    </style>
    <?php include 'navigation-bar.php'; ?>
</head>
<body>
<div class="body0">
    <nav class="breadcrumb">
        <a href="index.php">Home</a>
        <span>&raquo;</span>
        <a href="authors.php"><strong>Author</strong></a>
        
    </nav>
    <main class="authors-container">
        <?php if ($authors_result && $authors_result->num_rows > 0): ?>
            <?php while ($author = $authors_result->fetch_assoc()): ?>
                <a href="author-detail.php?author_id=<?php echo urlencode($author['author_id']); ?>" class="author">
                    <img src="author-image/<?php echo htmlspecialchars($author['author_photo']); ?>" alt="<?php echo htmlspecialchars($author['name']); ?>">
                    <p><?php echo htmlspecialchars($author['name']); ?></p>
                </a>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No authors found in the database.</p>
        <?php endif; ?>
    </main>
</div>

<?php include 'cart-overlay.php'; ?>
<footer>
    <p>&copy; 2024 RowdyBookly</p>
</footer>
</body>
</html>

<?php $conn->close(); ?>
