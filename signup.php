<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - RowdyBookly</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <a href="index.php" class="home-icon">🏠</a> <!-- Home icon to go back to the main page -->
    </header>

    <main class="signup-container">
        <h1>Create Account</h1>
        <form action="signup.php" method="post">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="confirm-password">Verify Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" required>
            
            <button type="submit" class="signup-button">➔</button>
        </form>
        <p><a href="login.php" class="login-link">Already a member? Log in</a></p>
    </main>
</body>
</html>
