<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RowdyBookly</title>
    <link rel="stylesheet" href="css/style.css">
    
</head>
<body>
    <header>
        <a href="index.php" class="home-icon">🏠</a> <!-- Home icon to go back to the main page -->
    </header>

    <main class="login-container">
        <h1>Login</h1>
        <form action="login.php" method="post">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>
        <!--    <label for="username">Username:</label>
            <input type="text" id="username" name="username" required> -->
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit" class="login-button">➔</button>
        </form>
        <p><a href="signup.php" class="signup-link">Not a member? Sign up</a></p>
    </main>
</body>
</html>
