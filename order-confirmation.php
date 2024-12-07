<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmation - RowdyBookly</title>
	
<link rel="stylesheet" href="css/style.css">
</head>
	
<body>
	
	<header>
        <h1 class="logo">
            <a href="index.php" class="main-page">Rowdy<br>Bookly</a>
        </h1>
        <nav>
			<a href="index.php" class="icon">🏠</a>
            <a href="categories.php" class="category-button">Categories</a>
            <input type="text" placeholder="Search">
            <button class="search-button">🔍</button>
            <a href="login.php" class="icon">👤</a>
            <a href="javascript:void(0);" class="icon" onclick="openCart()">🛒</a>
        </nav>
    </header>
	
	<div class="container">
        <!--Confirmation Message-->
        <div class="confirmation-message">
            <h1>Order Confirmed!</h1>
            <p>Your order has been successfully placed. Thank you for shopping with RowdyBookly.</p>
			
            
            <?php
            // Define an array of gif files
            $gifs = [
                "confirmation1.gif",
                "confirmation2.gif",
                "confirmation3.gif",
                "confirmation4.gif"
            ];

            // Randomly select one gif from the array
            $random_gif = $gifs[array_rand($gifs)];
            ?>

            <!--Display the gif-->
            <img src="images/<?php echo $random_gif; ?>" alt="Order Confirmation GIF">
        </div>
	
        
        <?php
        $conn = new mysqli('localhost', 'root', '', 'rowdybooks_db');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

$stmt->close();
$conn->close();
		
?>
		
		
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
	</script>
	<footer>
    <p>&copy; 2024 RowdyBookly</p>
    </footer>
</body>
</html>

