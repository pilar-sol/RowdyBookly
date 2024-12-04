<?php
$dbUrl = 'mysql://aqapvw1dt4k36dav:cp8n1pd5tgos08nw@qn0cquuabmqczee2.cbetxkdyhwsb.us-east-1.rds.amazonaws.com:3306/rp7q9eqqkuuf90wn';

// Parse the connection string
$dbParts = parse_url($dbUrl);

$host = $dbParts['host'];
$user = $dbParts['user'];
$password = $dbParts['pass'];
$dbname = ltrim($dbParts['path'], '/');
$port = 3306;

// Connect to MySQL
$conn = new mysqli($host, $user, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

echo "Database connection successful!<br>";

// Run a test query
$result = $conn->query("SHOW TABLES");

if ($result) {
    echo "Tables in the database:<br>";
    while ($row = $result->fetch_assoc()) {
        echo $row['Tables_in_' . $dbname] . "<br>";
    }
} else {
    echo "Query failed: " . $conn->error;
}

$conn->close();
?>
