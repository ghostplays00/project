<?php

// Initialize the session

session_start();

 

// Check if the user is logged in, if not then redirect him to login page

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){

    header("location: login.php");

    exit;

}

if (isset($_POST['save_date'])) {
    // Get the current date and time
    $current_date = date('Y-m-d H:i:s');
    
    // Get the name and book from the form
    $name = $_SESSION["username"];
    $book = $_POST['book'];

    // Format the log entry
    $log_entry = "$name - $book - $current_date\n";

    // Append the log entry to the log file
    file_put_contents('saved_log.txt', $log_entry, FILE_APPEND);

    // Redirect back to index.php
    header('Location: index.php');
    exit();
} else {
    // If someone tries to access this page directly, redirect to index.php
    header('Location: index.php');
    exit();
}
?>
