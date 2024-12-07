<header>
    <h1 class="logo">
        <a class="main-page" href="index.php">Rowdy Bookly</a>
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
        <a href="javascript:void(0);" class="icon" onclick="openCart()">🛒</a>
    </nav>
</header>
