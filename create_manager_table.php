<?php
// Include the database settings
include 'settings.php';

// Create connection to the database
$conn = new mysqli($host, $user, $pwd, $sql_db);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to create `managers` table if it doesn't exist
$create_managers_table_query = "
CREATE TABLE IF NOT EXISTS `managers` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Execute the query
if ($conn->query($create_managers_table_query) === TRUE) {
    // Table created or already exists
} else {
    echo "Error creating `managers` table: " . $conn->error;
}

// Optionally close the connection here
// $conn->close();
?>