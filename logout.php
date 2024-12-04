<?php
session_start(); // Start the session

// Destroy all session data
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: white;
            font-family: Arial, sans-serif;
            font-size: 1.5rem;
            color: black;
        }
    </style>
    <script>
        // Redirect to index.php after 1 seconds
        setTimeout(() => {
            window.location.href = "index.php";
        }, 1000);
    </script>
</head>
<body>
    Logging out...
</body>
</html>
