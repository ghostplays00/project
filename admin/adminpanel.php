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
        <a href="adminmanage.php" class="btn btn-danger ml-3">Tools</a>
    </div>
    <div class="category">
        <h1 class="my-5">Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to Eclarian's Nook Library.</h1>
    </div>
    
    <div class="catnav">
        <a href="../admin_subject/ict.php" class="btn btn-warning">ICT</a>

        <a href="../admin_subject/gas.php" class="btn btn-warning">GAS</a>

        <a href="../admin_subject/humms.php" class="btn btn-warning">HUMMS</a>

        <a href="../admin_subject/abm.php" class="btn btn-warning">ABM</a>

        <a href="../admin_subject/stem.php" class="btn btn-warning">STEM</a>
        <a href="../admin_subject/coresubject.php" class="btn btn-warning">CORE SUBJECT</a>

    </div>
    <div class="reader">
        <h1>Updates</h1>
        <div class="file-content">
            <?php 
            include('../Parsedown.php');


            $readme = file_get_contents('../information/data.txt');
            $Parsedown = new Parsedown();

            $html = $Parsedown->text($readme);


            echo $html; 
            ?>
        </div>
        
        <a href="../information/edit.php">UPDATE</a>
    </div>
        

</body>
</html>