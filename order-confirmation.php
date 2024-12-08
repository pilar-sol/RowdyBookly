<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmation - RowdyBookly</title>
	
<link rel="stylesheet" href="css/style.css">
	<style>
		<?php include 'css/style.css'; ?>

	</style>
	<?php
    include 'navigation-bar.php'; // Include the header
    ?>
</head>
	
<body>
	
	<div class="container">
        <!--Confirmation Message-->
        <div class="confirmation-message">
            <h1>Order Confirmed!</h1>
            <p>Your order has been successfully placed. Thank you for shopping with RowdyBookly.</p>
			
			<?php include 'cart-overlay.php'; ?>
			<script src="javascript/cart-interaction.js"></script>
			
            
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
		
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		<br>
		
		<footer>
    	<p>&copy; 2024 RowdyBookly</p>
    	</footer>
	
	
        
        <?php
        $conn = new mysqli('localhost', 'root', '', 'rowdybooks_db');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		

$stmt->close();
$conn->close();
		
		
?>
</body>
</html>

