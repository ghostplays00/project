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
$filename = "../messages/{$lrn}.txt";
    // Load the content of the file
if (file_exists($filename)) {
    $content = file_get_contents($filename);
}
 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compose Message</title>
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="topnav">
        <h1>Welcome to Admin Eclarian's Nook Panel</h1>   
        <img src="../pictures/logo.png">
        <a href="../welcome.php">Back</a>
    </div>
    <h2>Admin Response for: <b><?php echo htmlspecialchars(ucfirst($_SESSION["lastname"])); ?></b>, <b><?php echo htmlspecialchars(ucfirst($_SESSION["firstname"])); ?></b></h2>
    
    <div>
        <?php
        // Display the saved log
        if (file_exists($filename)) {
            $log_entries = file($filename);
            if (!empty($log_entries)) {
                echo "<p>Response:</p>";
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