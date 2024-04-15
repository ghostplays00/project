<?php

// Initialize the session

session_start();

 

// Check if the user is logged in, if not then redirect him to login page

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){

    header("location: login.php");

    exit;

}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Click Button to Save Date and Name</title>
</head>
<body>
    
    <a href="../welcome.php" class="btn btn-warning">BACK</a>
    <h1>REQUEST BOOKS</h1>
    
    <form method="post" action="save_date.php">
        <label for="name">Book Type:</label>
        <input type="text" id="book" name="book" required>
        <button type="submit" name="save_date">Save Date</button>
    </form>
    
    <div>
        <?php
        // Display the saved log
        if (file_exists('saved_log.txt')) {
            $log_entries = file('saved_log.txt');
            if (!empty($log_entries)) {
                echo "<p>Last Saved Logs:</p>";
                echo "<ul>";
                foreach ($log_entries as $entry) {
                    echo "<li>$entry</li>";
                }
                echo "</ul>";
            }
        }
        ?>
    </div>
</body>
</html>
