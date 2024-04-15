<?php

// Initialize the session

session_start();

 

// Check if the user is logged in, if not then redirect him to login page

if(!isset($_SESSION["adminloggedin"]) || $_SESSION["adminloggedin"] !== true){

    header("location: index.php");

    exit;

}

require_once "../config.php";


// Define the three status types
$status_types = ['CLAIMED', 'SURRENDER', 'DUE DATE'];

if(isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    
    // Perform deletion query
    $delete_query = "DELETE FROM bookrequested WHERE id = '$user_id'";
    mysqli_query($link, $delete_query);
    
    // Redirect back to the previous page
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
}
// Handle status toggle
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_status'])) {
    $id = $_POST['book_id'];
    $current_status = $_POST['current_status'];
    
    // Get the index of the current status in the array
    $current_index = array_search($current_status, $status_types);
    
    // Increment the index with wrap-around
    $next_index = ($current_index + 1) % count($status_types);
    
    // Get the next status from the array
    $new_status = $status_types[$next_index];
    
    // Update status in the database
    $sql = "UPDATE bookrequested SET status = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "si", $new_status, $id);
        if (mysqli_stmt_execute($stmt)) {
            // Redirect to the same page after successful status toggle
            header("location: ".$_SERVER['HTTP_REFERER']);
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eclarian's Nooks Library System</title>
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<style>
    form {
        margin: 20px auto;
        width: 50%;
        border: 1px solid #ccc;
        padding: 20px;
    }
    input[type=text], input[type=datetime-local], input[type=number], input[type=date] {
        width: 100%;
        padding: 12px 20px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    input[type=submit] {
        width: 100%;
        background-color: #4CAF50;
        color: white;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
</style>
</head>
<body>


    <?php if (isset($_SESSION['error'])): ?>
        <div class="modal" id="errorModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Error</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><?php echo $_SESSION['error']; ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $('#errorModal').modal('show');
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>




<div class="topnav">
    <h1>Welcome to Eclarian's Nooks</h1>
    <img src="../pictures/logo.png">
    <a href="../admin/adminpanel.php">Back</a>
</div>




<form action="add_book.php" method="post">
    <label for="bookId">Book ID:</label><br>
    <input type="number" id="bookId" name="bookId" required><br>
    <label for="subject">Subject:</label><br>
    <input type="text" id="subject" name="subject" list="subjectList" required><br>
    <datalist id="subjectList">
        <option value="gas">
        <option value="humms">
        <option value="abm">
        <option value="ict">
        <option value="stem">
    </datalist><br>
    <label for="LRN">LRN:</label><br>
    <input type="number" id="LRN" name="LRN" title="LRN must be exactly 12 digits" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="12" required><br>
    <label for="countdownTime">Start Date and Time (DD-MM-YYYY HH:MM AM/PM):</label><br>
    <input type="datetime-local" id="countdownTime" name="countdownTime" required><br>
    <label for="dateAdded">Date Added (DD-MM-YYYY HH:MM AM/PM):</label><br>
    <input type="datetime-local" id="dateAdded" name="dateAdded" required><br><br>
    <input type="submit" value="Add Book">
</form>
<div class="profile">
<div class="containerrequest">

    <div class="card mt-4">
            <div class="col-md-12">
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>List of Borrowed Books</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-7">
                                <form action="" method="GET">
                                    <div class="input-group mb-3">
                                        <input type="text" name="search" required value="<?php if(isset($_GET['search'])){ echo $_GET['search']; } ?>" class="form-control" placeholder="Search data">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        
        <div class="card-body">
            <table class="table table-bordered ">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>book Id</th>
                        <th>Subject Id</th>
                        <th>Borrower</th>
                        <th>Date Start</th>
                        <th>Due Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        
                            if(isset($_GET['search']))
                            {
                                $filtervalues = $_GET['search'];
                                $query = "SELECT * FROM bookrequested WHERE CONCAT(book_id, subject_id, firstname, lastname, status, lrn) LIKE '%$filtervalues%' ";
                            } else {
                                $limit = 50; // Number of entries to show in a page.
                                // Look for a GET variable page if not found default is 1.        
                                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                $start = ($page-1) * $limit;                                 
                                $query = "SELECT * FROM bookrequested LIMIT $start, $limit";
                            }
                        

                            $query_run = mysqli_query($link, $query);

                            if(mysqli_num_rows($query_run) > 0) {
                                foreach($query_run as $items) {
                                    ?>
                                    <tr>
                                        <td><?= $items['id']; ?></td>
                                        <td><?= $items['book_id']; ?></td>
                                        <td><?= $items['subject_id']; ?></td>
                                        <td><?= $items['lastname']; ?>, <?= $items['firstname']; ?></td>
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
                                        <td>
                                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                                        <input type="hidden" name="book_id" value="<?= $items['id']; ?>">
                                                        <input type="hidden" name="current_status" value="<?= $items['status']; ?>">
                                                        <button type="submit" name="toggle_status" class="btn btn-delete"><?php echo $items['status']?></button>
                                                    </form>
                                                    <form action="settimer.php" method="POST">
                                                        <input type="hidden" name="user_id" value="<?= $items['id']; ?>">
                                                        <button type="submit" class="btn btn-delete">Delete</button>
                                                    </form>
                                                </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='10'>No requests found.</td></tr>";
                            }
                        
                    ?>
                </tbody>
            </table>
            <?php
            if(!isset($_GET['search'])) {
                // Determine the total number of pages available
                $result = mysqli_query($link, "SELECT COUNT(id) AS id FROM users");
                $userCount = mysqli_fetch_all($result, MYSQLI_ASSOC);
                $total = $userCount[0]['id'];
                $pages = ceil($total / $limit);

                            // Display the Next button if more pages are available
                if ($page < $pages) {
                    echo '<a href="yourfilename.php?page=' . ($page + 1) . '" class="btn btn-primary">Next 50 Users</a>';
                }
            }
            ?>
        </div>
    </div>
</div>
<div>



</body>
</html>