<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["adminloggedin"]) || $_SESSION["adminloggedin"] !== true) {
    header("location: ../admin/index.php");
    exit;
}

// Include config file
require_once "../config.php";

// Define variables and initialize with empty values
$bookname = $author = $description = $status = "";
$bookname_err = $author_err = $description_err = $status_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate book name
    if (empty(trim($_POST["bookname"]))) {
        $bookname_err = "Please enter a book name.";
    } else {
        $bookname = trim($_POST["bookname"]);
    }

    // Validate author
    if (empty(trim($_POST["author"]))) {
        $author_err = "Please enter an author.";
    } else {
        $author = trim($_POST["author"]);
    }

    // Validate description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter a description.";
    } else {
        $description = trim($_POST["description"]);
    }

    // Validate status
    if (empty(trim($_POST["status"]))) {
        $status_err = "Please enter a status.";
    } else {
        $status = trim($_POST["status"]);
    }

    // Inserting data into the database
    if (empty($bookname_err) && empty($author_err) && empty($description_err) && empty($status_err)) {
        $sql = "INSERT INTO ict (bookname, author, description, status) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssss", $param_bookname, $param_author, $param_description, $param_status);
            $param_bookname = $bookname;
            $param_author = $author;
            $param_description = $description;
            $param_status = $status;
            if (mysqli_stmt_execute($stmt)) {
                header("location: ict.php"); // Redirect to the same page after successful submission
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle status toggle
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_status'])) {
    $book_id = $_POST['book_id'];
    $current_status = $_POST['current_status'];
    $new_status = $current_status === "AVAILABLE" ? "UNAVAILABLE" : "AVAILABLE";
    $sql = "UPDATE ict SET status = ? WHERE id = ?";
    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "si", $new_status, $book_id);
        if (mysqli_stmt_execute($stmt)) {
            header("location: ict.php"); // Redirect to the same page after successful status toggle
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
}
if(isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    
    // Perform deletion query
    $delete_query = "DELETE FROM ict WHERE id = '$user_id'";
    mysqli_query($link, $delete_query);
    
    // Redirect back to the previous page
    header("Location: ".$_SERVER['HTTP_REFERER']);
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
        <a href="../admin/adminpanel.php">Back</a>    
    </div>
    <div class="category">
        <h1 class="my-5">ICT PANEL</h1>
    </div>

    <div class="col-md-12">
        <div class="card mt-4">
            <div class="card-body">
                <div class="containerbooklist">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label>Book Name</label>
                        <input type="text" name="bookname" class="form-control" value="">
                    </div>
                    <div class="form-group">
                        <label>Author</label>
                        <input type="text" name="author" class="form-control" value="">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="AVAILABLE">AVAILABLE</option>
                            <option value="UNAVAILABLE">UNAVAILABLE</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Add Book">
                    </div>
                </form>
                </div>
                <div class="containerbooklist">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Book Name</th>
                            <th>Author</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- PHP code for displaying existing entries goes here -->
                        
                        <?php
                                    
                                    require_once "../config.php";

                                    if(isset($_GET['search']))
                                    {
                                        $filtervalues = $_GET['search'];
                                        $query = "SELECT * FROM ict WHERE CONCAT(booknamne, author, description, status) LIKE '%$filtervalues%' ";
                                    } else {
                                        $limit = 50; // Number of entries to show in a page.
                                        // Look for a GET variable page if not found default is 1.        
                                        $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                        $start = ($page-1) * $limit;                                 
                                        $query = "SELECT * FROM ict LIMIT $start, $limit";
                                    }
                                    
                                    $query_run = mysqli_query($link, $query);

                                    if(mysqli_num_rows($query_run) > 0)
                                    {
                                        foreach($query_run as $items)
                                        {
                                            ?>
                                            <tr>
                                                <td><?= $items['id']; ?></td>
                                                <td><?= $items['bookname']; ?></td>
                                                <td><?= $items['author']; ?></td>
                                                <td><?= wordwrap($items['description'], 50, "\n", true); ?></td>
                                                <td><?= $items['status']; ?></td>
                                                <td>
                                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                                        <input type="hidden" name="book_id" value="<?= $items['id']; ?>">
                                                        <input type="hidden" name="current_status" value="<?= $items['status']; ?>">
                                                        <button type="submit" name="toggle_status" class="btn btn-delete"><?php echo ($items['status'] === 'AVAILABLE') ? 'Set Unavailable' : 'Set Available'; ?></button>
                                                    </form>
                                                    <form action="ict.php" method="POST">
                                                        <input type="hidden" name="user_id" value="<?= $items['id']; ?>">
                                                        <button type="submit" class="btn btn-delete">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    else
                                    {
                                        ?>
                                            <tr>
                                                <td colspan="5">No Record Found</td>
                                            </tr>
                                        <?php
                                    }
                                ?>
                    </tbody>
                </table>
                </div>
                <?php
                // PHP code for displaying pagination links goes here
                        
                        if(!isset($_GET['search'])) {
                            // Determine the total number of pages available
                            $result = mysqli_query($link, "SELECT COUNT(id) AS id FROM ict");
                            $userCount = mysqli_fetch_all($result, MYSQLI_ASSOC);
                            $total = $userCount[0]['id'];
                            $pages = ceil($total / $limit);

                            // Display the Next button if more pages are available
                            if ($page < $pages) {
                                echo '<a href="ict.php?page=' . ($page + 1) . '" class="btn btn-primary">Next 50 Books</a>';
                            }
                        }
                        
                ?>

            </div>
        </div>
    </div>

</body>
</html>
