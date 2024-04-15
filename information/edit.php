<?php

// Initialize the session

session_start();

 

// Check if the user is logged in, if not then redirect him to login page

if(!isset($_SESSION["adminloggedin"]) || $_SESSION["adminloggedin"] !== true){

    header("location: index.php");

    exit;

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eclarian's Nook Library System</title>
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
</head>
<body>

    <div class="topnav">
        <h1>Welcome to Admin Eclarian's Nook Panel</h1>   
        <img src="../pictures/logo.png">
        <a href="../admin/adminpanel.php">Back</a>
    </div>
    <div class="update-content">
        <h2>Edit Update</h2>
        <form action="save.php" method="post">
            <textarea class="contenttxt" name="content" id="content" rows="10" cols="50"><?php echo file_get_contents("data.txt"); ?></textarea>
            <br>
            <input type="submit" value="Save">
        </form>
    </div>

</body>
</html>
