
<style>
    .cart {
        position: relative;
        display: grid;
        align-items: center;
        padding-right: 0;
        z-index: 10;
        width: 40px;
        height: 40px;
    }
    .cart img{
        width: 30px;
        height: 30px;
    }
    .cart .cart-count {
    position: absolute;
    top: 0;
    right: 0;    
    background-color: red;
    color: white;
    font-size: 12px;
    width: 20px;
    height: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    z-index: 10;
}
    </style>
<header>
    <h1 class="logo">
            <a class="main-page" href="index.php">
            Rowdy<br>Bookly
            </a>
    </h1>
    <nav>
        <a href="categories.php" class="category-button">Categories</a>
        <form action="search-results.php" method="GET">
            <input 
                type="text" 
                name="query" 
                placeholder="Search books by title, author, genre" 
                value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" 
                required>
            <button type="submit" class="search-button">🔍</button>
        </form>
        <?php if ($is_logged_in): ?>
            <a href="profile.php" class="icon">👤</a>
            <a href="logout.php" class="icon" title="Logout">
                <img src="images/logout.png" alt="Logout" style="width:30px; height:30px;">
            </a>
        <?php else: ?>
            <a href="login.php" class="icon">👤</a>
        <?php endif; ?>
        <a class ="cart" href="javascript:void(0);" class="icon" onclick="openCart()">
            <img src="icon/shopping-cart.png" alt="Cart">
            <?php if ($cart_item_count > 0): ?>
                <div class="cart-count"><?php echo $cart_item_count; ?></div>
            <?php endif; ?>
        </a>
    </nav>
</header>
