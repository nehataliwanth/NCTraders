<?php
// Database Setup Script

$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read SQL file
$sql = file_get_contents(__DIR__ . '/database/schema.sql');

// Execute SQL statements
if ($conn->multi_query($sql)) {
    echo "<h2 style='color: green;'>✓ Database setup completed successfully!</h2>";
    echo "<p>The following were created:</p>";
    echo "<ul>";
    echo "<li>Database: nctraders_db</li>";
    echo "<li>Tables: roles, users, categories, products, cart, orders, reviews, messages, wishlist, and more</li>";
    echo "<li>Default roles: Admin, Moderator, Seller, Buyer</li>";
    echo "</ul>";
    echo "<p><a href='index.php' style='color: blue; text-decoration: none;'>&larr; Go to Homepage</a></p>";
} else {
    echo "<h2 style='color: red;'>✗ Error setting up database</h2>";
    echo "<p>" . $conn->error . "</p>";
    echo "<p><a href='setup.php' style='color: blue; text-decoration: none;'>Retry</a></p>";
}

$conn->close();
?>
