
<style>
    
    .cart {
        position: relative;
        display: grid;
        align-items: center;
        margin:0;
        z-index: 10;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center; 
        justify-content: center;
    }
    .cart:hover {
        background-color:#19abe0;
        border-radius: 10px;
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
    .nav-button{
        text-decoration: none;
        color: beige;
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    .nav-button:hover{
        background-color: #e8935b;
        border-radius: 10px;
        color: brown;
    }
    .search-button{
        align-items: center;
        height: 40px;
        width:40px;
    }
    
    .search {
        margin-left:10px;
        display: flex;
        align-items: center; 
        justify-content: center;  
          
    }

    .search input {
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 10px;
        width: 300px; 
    }

    .search-button {
        background: none;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center; 
        justify-content: center;
    }

    .search-button img {
        width: 30px;
        height: 30px;
        object-fit: contain;
    }
    .search-button img:hover{
        background: #e8935b;
        border-radius: 10px;
        padding:5px;
    }
    .icon:hover {
    background-color: #e8935b;
    border-radius: 10px;
    }

    .icon {
        margin: 0; 
        padding: 0;
        width: 40px;
        height: 40px;
        padding: 5px;
        display: flex;
        align-items: center; 
        justify-content: center;
    }

    .icon img {
        width: 30px;
        height: 30px;
        object-fit: contain;
    }
    </style>
<header>
    <h1 class="logo">
            <a class="main-page" href="index.php">
            Rowdy<br>Bookly
            </a>
    </h1>
    <nav>
        <a href="authors.php" class="nav-button">Authors</a>
        <a href="categories.php" class="nav-button">Categories</a>
        <form action="search-results.php" method="GET" class="search">
            <input 
                type="text" 
                name="query" 
                placeholder="Search books by title, author, genre" 
                value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" 
                required>
            <button type="submit" class="search-button">
                <img src = "icon-image/search.png">
            </button>
        </form>
        <?php if ($is_logged_in): ?>
            <a href="profile.php" class="icon">
                <img src="icon-image/user.png" alt="Logout">
            </a>

            <a href="logout.php" class="icon" title="Logout">
                <img src="icon-image/logout.png" alt="Logout" style="width:30px; height:30px;">
            </a>
        <?php else: ?>
            <a href="login.php" class="icon">
                <img src="icon-image/login.png" alt="Logout">
            </a>
        <?php endif; ?>
        <a class ="cart" href="javascript:void(0);" class="icon" onclick="openCart()">
            <img src="icon/shopping-cart.png" alt="Cart">
            <?php if ($cart_item_count > 0): ?>
                <div class="cart-count"><?php echo $cart_item_count; ?></div>
            <?php endif; ?>
        </a>
    </nav>
</header>
