<?php
session_start();

if (!isset($_SESSION["adminloggedin"]) || $_SESSION["adminloggedin"] !== true) {
    header("location: index.php");
    exit;
}

// Initialize variables
$content = "";
$lrn = "";


// Check if LRN is provided
if (isset($_GET['lrn'])) {
    $lrn = $_GET['lrn'];
    // Generate filename using LRN
    $filename = "../messages/{$lrn}.txt";
    // Load the content of the file
    if (file_exists($filename)) {
        $content = file_get_contents($filename);
    }
} else {
    echo "LRN not provided.";
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the submitted content
    $message = $_POST['message'];
    $name = $_SESSION["username"];
    $current_date = date('Y-m-d H:i:s');
    $log_entry = "$current_date - $name - $message\n";

    // Append the content to the log file
    file_put_contents($filename, $log_entry, FILE_APPEND);
    
    // Redirect back to the main page
    header("Location: ../users/list.php");
    exit();
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
        <a href="../users/list.php">Back</a>
    </div>
    <h2>Compose Message to LRN: <?php echo $lrn; ?></h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?lrn=" . urlencode($lrn); ?>">
        <label for="name">Message:</label>
        <input type="text" id="message" name="message" required>
        <button type="submit" name="content">Submit</button>
    </form>
    
    <div>
        <?php
        // Display the saved log
        if (file_exists($filename)) {
            $log_entries = file($filename);
            if (!empty($log_entries)) {
                echo "<p>Messages:</p>";
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