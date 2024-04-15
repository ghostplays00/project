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

    <title>Welcome</title>

    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">

    <style>

        body{ font: 14px sans-serif; text-align: center; }

    </style>

</head>

<body>  
    
    <div class="topnav">
        <h1>Welcome to Admin Eclarian's Nook Panel</h1>  
        <img src="../pictures/logo.png">   
        <a href="adminlogout.php" class="btn btn-danger ml-3">Logout</a>     
        <a href="adminpanel.php" class="btn btn-danger ml-3">Back</a>      
    </div>

    <div class="catnav">
        <a href="../timer/settimer.php">Book Request</a>  
 
        <a href="../requestupdate/list.php">Requests</a>   

        <a href="../users/list.php">List of Users</a>       

        <a href="admin_reset.php">ChangePassword</a>       

        <div>
        

</body>
</html>