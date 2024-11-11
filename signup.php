<!-- signup.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - RowdyBookly</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="signup-container">
        <h2>Create Account</h2>
        <form action="signup.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password">
            <label for="confirm-password">Verify Password:</label>
            <input type="password" id="confirm-password" name="confirm-password">
            <button type="submit">Sign Up</button>
        </form>
    </div>
</body>
</html>
