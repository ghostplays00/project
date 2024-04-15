<?php

// Initialize the session

session_start();

 

// Check if the user is logged in, if not then redirect him to login page

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){

    header("location: index.php");

    exit;

}

?>

 

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>Welcome</title>

    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">

    <style>

        body{ font: 14px sans-serif; text-align: center; }

    </style>

</head>

<body>  
    
    <div class="topnav">
        <h1>Welcome to Eclarian's Nook</h1>   
        <img src="pictures/logo.png">  
        <a href="logout.php" class="btn btn-danger ml-3">Logout</a>      
        <a href="public/profile.php">My profile</a>      
        <a href="public/message.php">Message</a>    
    </div>
    <div class="category">
        <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to Eclarian's Nook Library.</h1>
    </div>
    
    <div class="catnav">
        <a href="subject/ict.php" class="btn btn-warning">ICT</a>

        <a href="subject/gas.php" class="btn btn-warning">GAS</a>

        <a href="subject/humms.php" class="btn btn-warning">HUMMS</a>

        <a href="subject/abm.php" class="btn btn-warning">ABM</a>

        <a href="subject/stem.php" class="btn btn-warning">STEM</a>
        <a href="subject/coresubject.php" class="btn btn-warning">Core Subject</a>
    </div>
    <div class="reader">
        <h1>Updates</h1>
        <div class="file-content">
            <?php 
            include('Parsedown.php');


            $readme = file_get_contents('information/data.txt');
            $Parsedown = new Parsedown();

            $html = $Parsedown->text($readme);


            echo $html; 
            ?>
        </div>
        
    </div>

</body>
</html>