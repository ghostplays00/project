<?php

require_once "../config.php";

// Create table if it doesn't exist
$create_table_query = "CREATE TABLE IF NOT EXISTS request (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(30) NOT NULL,
    book VARCHAR(30) NOT NULL,
    lrn VARCHAR(30) NOT NULL,
    firstname VARCHAR(30) NOT NULL,
    lastname VARCHAR(30) NOT NULL,
    section VARCHAR(30) NOT NULL,
    status VARCHAR(30) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($link->query($create_table_query) === TRUE) {
    echo "Table created successfully\n";
} else {
    echo "Error creating table: " . $link->error;
}

$link->close();
?>




CREATE TABLE IF NOT EXISTS register (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    lrn VARCHAR(50) NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    section VARCHAR(50) NOT NULL,
    status VARCHAR(50) NOT NULL,
    profile_picture VARCHAR(255) NOT NULL,
)