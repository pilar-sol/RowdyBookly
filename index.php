<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RowdyBookly</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1 class="logo">Rowdy<br>Bookly</h1>
        <nav>
            <a href="categories.php" class="category-button">Categories</a>
            <input type="text" placeholder="Search">
            <button class="search-button">🔍</button>
            <a href="login.php" class="icon">👤</a>
            <a href="javascript:void(0);" class="icon" onclick="openCart()">🛒</a>
        </nav>
    </header>
    
    <main class="main-container">
        <!-- Left side content -->
        <section class="welcome">
            <div class="welcome-text">
                <h2>Welcome to RowdyBookly!</h2>
                <p>Our mission is to provide a fast, convenient, and secure shopping experience, tailored to meet the unique needs of the bookworm community. Enjoy a reliable service that ensures smooth access to the books you love, anytime, anywhere.</p>
            </div>
        </section>
        
        <!-- Right side content: Books listing -->
        <aside class="book-sidebar">
            <section class="books-section">
                <h3>Recently Viewed</h3>
                <ul class="book-list">
                    <li>Book Title 1</li>
                    <li>Book Title 1</li>
                    <li>Book Title 2</li>
                    <li>Book Title 2</li>
                </ul>
                
                <h3>Popular</h3>
                <ul class="book-list">
                    <li>Book Title 3</li>
                    <li>Book Title 3</li>
                    <li>Book Title 4</li>
                    <li>Book Title 4</li>
                </ul>
            </section>
        </aside>
    </main>
    
    <footer>
        <p>&copy; 2024 RowdyBookly</p>
    </footer>

<!-- Cart Overlay and Sliding Cart Panel -->
<div class="overlay" id="overlay"></div>
<div class="cart-panel" id="cartPanel">
    <div class="cart-header">
        <h2>Shopping Cart 🛒</h2>
        <button class="close-cart" onclick="closeCart()">✖</button>
    </div>
    <div class="cart-content">
        <p>There are currently no items in your shopping cart</p>
    </div>
</div>

<script>
function openCart() {
    document.getElementById("overlay").classList.add("active");
    document.getElementById("cartPanel").classList.add("active");
}

function closeCart() {
    document.getElementById("overlay").classList.remove("active");
    document.getElementById("cartPanel").classList.remove("active");
}

// Optional: Close the cart when clicking outside of it
document.getElementById("overlay").addEventListener("click", closeCart);
</script>

</body>
</html>