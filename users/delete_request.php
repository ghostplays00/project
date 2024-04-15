<?php

// Initialize the session

session_start();

 

// Check if the user is logged in, if not then redirect him to login page

if(!isset($_SESSION["adminloggedin"]) || $_SESSION["adminloggedin"] !== true){

    header("location: index.php");

    exit;

}

?>
<?php
require_once "../config.php";

if(isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    
    // Perform deletion query
    $delete_query = "DELETE FROM register WHERE id = '$user_id'";
    mysqli_query($link, $delete_query);
    
    // Redirect back to the previous page
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
}
?>
