<?php
// Fetch database URL from environment variable
$dbUrl = getenv('CLEARDB_DATABASE_URL');

// Parse the URL
$dbParts = parse_url($dbUrl);

define('DB_HOST', $dbParts['host']);
define('DB_USER', $dbParts['user']);
define('DB_PASSWORD', $dbParts['pass']);
define('DB_NAME', ltrim($dbParts['path'], '/'));

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
