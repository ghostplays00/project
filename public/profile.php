<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../index.php");
    exit;
}

require_once "../config.php";
// Fetch the username from session
$username = $_SESSION["username"];
// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["size"] > 0) {
    $target_dir = "../profile_pictures/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profile_picture"]["size"] > 500000) {
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // File uploaded successfully, update user's profile picture in the database
            $update_sql = "UPDATE users SET profile_picture = ? WHERE username = ?";
            if ($stmt = mysqli_prepare($link, $update_sql)) {
                mysqli_stmt_bind_param($stmt, "ss", $param_profile_picture, $param_username);
                $param_profile_picture = basename($_FILES["profile_picture"]["name"]);
                $param_username = $username;

                if (mysqli_stmt_execute($stmt)) {
                    // Profile picture updated successfully
                } else {
                    echo "Error updating profile picture.";
                }
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}





// Fetch the username from session
$username = $_SESSION["username"];

// Fetch user's details from the database
$sql = "SELECT lrn, section, firstname, lastname, profile_picture FROM users WHERE username = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $param_username);
    $param_username = $username;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $lrn, $section, $firstname, $lastname, $profile_picture);
            if (mysqli_stmt_fetch($stmt)) {
                // Store details in session
                $_SESSION["lrn"] = $lrn;
                $_SESSION["section"] = $section;
                $_SESSION["firstname"] = $firstname;
                $_SESSION["lastname"] = $lastname;
                $_SESSION["profile_picture"] = $profile_picture;
            }
        }
    }
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
    <h1>Welcome to Eclarian's Nook</h1>
    <img src="../pictures/logo.png">
    <a href="../welcome.php">Back</a>
    <a href="../reset-password.php" class="btn btn-warning">Reset Password</a>
</div>
<div class="profile">
  <div class="profileparent">
  <div class="profilechild">
    <div class="profileimg">
        <?php if (!empty($_SESSION["profile_picture"])): ?>
            <img class="profile" src="../profile_pictures/<?php echo $_SESSION["profile_picture"]; ?>">
        <?php else: ?>
            <img class="profile" src="../pictures/default_profile_picture.jpg">
        <?php endif; ?>
    </div>
  </div>
  <div class="profilechild">
    <div class="profileinfo">
        <h1 class="my-5">Hi, <b><?php echo htmlspecialchars(ucfirst($_SESSION["firstname"])); ?></b>. Welcome to
            Eclarian's Nook Library.</h1>
        <h2><b><?php echo htmlspecialchars(ucfirst($_SESSION["lastname"])); ?></b>, <b><?php echo htmlspecialchars(ucfirst($_SESSION["firstname"])); ?></b></h2>
        <p>USERNAME: <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b></p>
        <p>SECTION: <b><?php echo htmlspecialchars($_SESSION["section"]); ?></b></p>
        <p>LRN: <b><?php echo htmlspecialchars($_SESSION["lrn"]); ?></b></p>
        <!-- Form for uploading profile picture -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <input type="file" name="profile_picture" id="profile_picture">
            <input type="submit" value="Upload Profile Picture" name="submit">
        </form>
    </div>
  </div>
  </div>
<div class="containerrequest">
    <div class="card mt-4">
        <div class="card-body">
            <table class="table table-bordered ">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Subject</th>
                        <th>Book</th>
                        <th>Status</th>
                        <th>Update Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Fetch request details from the database based on the LRN
                        $query = "SELECT * FROM request WHERE lrn = ?";
                        if ($stmt = mysqli_prepare($link, $query)) {
                            mysqli_stmt_bind_param($stmt, "s", $lrn);
                            mysqli_stmt_execute($stmt);
                            $query_run = mysqli_stmt_get_result($stmt);

                            if(mysqli_num_rows($query_run) > 0) {
                                foreach($query_run as $items) {
                                    ?>
                                    <tr>
                                        <td><?= $items['id']; ?></td>
                                        <td><?= $items['subject']; ?></td>
                                        <td><?= $items['book']; ?></td>
                                        <td><?= $items['status']; ?></td>
                                        <td><?= $items['reg_date']; ?></td>
                                        <td>
                                            <form action="delete_user.php" method="POST">
                                                <input type="hidden" name="user_id" value="<?= $items['id']; ?>">
                                                <button type="submit" class="btn btn-delete">Delete Request</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='10'>No requests found.</td></tr>";
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="containerrequest">
    <div class="card mt-4">
        <div class="card-body">
            <table class="table table-bordered ">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>book Id</th>
                        <th>Subject Id</th>
                        <th>Date Start</th>
                        <th>Due Date</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Fetch request details from the database based on the LRN
                        $query = "SELECT * FROM bookrequested WHERE lrn = ?";
                        if ($stmt = mysqli_prepare($link, $query)) {
                            mysqli_stmt_bind_param($stmt, "s", $lrn);
                            mysqli_stmt_execute($stmt);
                            $query_run = mysqli_stmt_get_result($stmt);

                            if(mysqli_num_rows($query_run) > 0) {
                                foreach($query_run as $items) {
                                    ?>
                                    <tr>
                                        <td><?= $items['id']; ?></td>
                                        <td><?= $items['book_id']; ?></td>
                                        <td><?= $items['subject_id']; ?></td>
                                        <td><?= $items['date_start']; ?></td>
                                        <td><?= $items['due_date']; ?></td>
                                        <td>
                                        <div id="countdown_<?php echo $items['id']; ?>"></div>
                                        <script>
                                                // Get the current date and time in Singapore time
                                                var currentDate = new Date().toLocaleString("en-US", {timeZone: "Asia/Singapore"});
                                                currentDate = new Date(currentDate).getTime();
                                        
                                                // Set the due date for the current request in Singapore time (assuming $items['due_date'] is in format 'd - m - Y - H - i')
                                                var dueDateStr = "<?php echo $items['due_date']; ?>".split(" - ");
                                                var dueDate_<?php echo $items['id']; ?> = new Date(dueDateStr[2], dueDateStr[1] - 1, dueDateStr[0], dueDateStr[3], dueDateStr[4]).getTime();
                                        
                                                // Calculate the time difference between due date and current date
                                                var distance_<?php echo $items['id']; ?> = dueDate_<?php echo $items['id']; ?> - currentDate;
                                        
                                                // Update the count down every 1 second
                                                var x_<?php echo $items['id']; ?> = setInterval(function() {
                                                    
                                                    // Recalculate the current date at each iteration to handle time zone changes
                                                    var currentDate_<?php echo $items['id']; ?> = new Date().toLocaleString("en-US", {timeZone: "Asia/Singapore"});
                                                    currentDate_<?php echo $items['id']; ?> = new Date(currentDate_<?php echo $items['id']; ?>).getTime();
                                        
                                                    // Recalculate the distance at each iteration to account for elapsed time
                                                    distance_<?php echo $items['id']; ?> = dueDate_<?php echo $items['id']; ?> - currentDate_<?php echo $items['id']; ?>;
                                        
                                                    // Time calculations for days, hours, minutes and seconds
                                                    var days_<?php echo $items['id']; ?> = Math.floor(distance_<?php echo $items['id']; ?> / (1000 * 60 * 60 * 24));
                                                    var hours_<?php echo $items['id']; ?> = Math.floor((distance_<?php echo $items['id']; ?> % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                                    var minutes_<?php echo $items['id']; ?> = Math.floor((distance_<?php echo $items['id']; ?> % (1000 * 60 * 60)) / (1000 * 60));
                                                    var seconds_<?php echo $items['id']; ?> = Math.floor((distance_<?php echo $items['id']; ?> % (1000 * 60)) / 1000);
            
                                                    // Format the due date
                                                    var formattedDate_<?php echo $items['id']; ?> = "<?php echo $items['due_date']; ?>";

                                                    // Output the result in the element
                                                    document.getElementById("countdown_<?php echo $items['id']; ?>").innerHTML = days_<?php echo $items['id']; ?> + "d " + hours_<?php echo $items['id']; ?> + "h "
                                                    + minutes_<?php echo $items['id']; ?> + "m " + seconds_<?php echo $items['id']; ?> + "s";

            
                                                    // If the count down is over, write some text 
                                                    if (distance_<?php echo $items['id']; ?> < 0) {
                                                        clearInterval(x_<?php echo $items['id']; ?>);
                                                        document.getElementById("countdown_<?php echo $items['id']; ?>").innerHTML = "EXPIRED";
                                                    }
                                                }, 1000);
                                        </script>
                                        </td>
                                        <td><?= $items['status']; ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='10'>No requests found.</td></tr>";
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</body>
</html>
