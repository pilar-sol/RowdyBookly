<?php
// Database credentials
define('DB_HOST', 'localhost:3306');
define('DB_USER', 'root');
define('DB_PASSWORD', ''); // Leave empty if no password is set
define('DB_NAME', 'valerie-bookstore');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to execute SQL scripts
function executeSqlFile($conn, $filePath) {
    $queries = file_get_contents($filePath);
    if ($conn->multi_query($queries)) {
        do {
            // Fetch results for every query
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
    } else {
        echo "Error initializing database: " . $conn->error;
    }
}

// Initialize database schema (optional)
$schemaFile = 'schema.sql'; // Path to your SQL file
if (file_exists($schemaFile)) {
    executeSqlFile($conn, $schemaFile);
}


?>