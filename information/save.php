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
if(isset($_POST['content'])) {
    $content = $_POST['content'];
    file_put_contents("data.txt", $content);
    header("Location: ../admin/adminpanel.php"); // Redirect back to the main page
    exit();
}
?>
