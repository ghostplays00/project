<?php
// Initialize the session
session_start();
// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../index.php");
    exit;
}

// Include config file
require_once "../config.php";

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
        <h1>Welcome to Eclarian's Nook</h1>     
        <img src="../pictures/logo.png">
        <a href="../welcome.php">Back</a>    
    </div>
    <div class="category">
        <h1 class="my-5">ABM PANEL</h1>
    </div>

    <div class="col-md-12">
        <div class="card mt-4">
            <div class="card-body">

              <div class="containerbooklist">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Book Name</th>
                            <th>Author</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Request</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- PHP code for displaying existing entries goes here -->
                        
                        <?php
                                    
                                    require_once "../config.php";

                                    if(isset($_GET['search']))
                                    {
                                        $filtervalues = $_GET['search'];
                                        $query = "SELECT * FROM abm WHERE CONCAT(booknamne, author, description, status) LIKE '%$filtervalues%' ";
                                    } else {
                                        $limit = 50; // Number of entries to show in a page.
                                        // Look for a GET variable page if not found default is 1.        
                                        $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                        $start = ($page-1) * $limit;                                 
                                        $query = "SELECT * FROM abm LIMIT $start, $limit";
                                    }
                                    
                                    $query_run = mysqli_query($link, $query);

                                    if(mysqli_num_rows($query_run) > 0)
                                    {
                                        foreach($query_run as $items)
                                        {
                                            ?>
                                            <tr>
                                                <td><?= $items['bookname']; ?></td>
                                                <td><?= $items['author']; ?></td>
                                                <td><?= $items['description']; ?></td>
                                                <td><?= $items['status']; ?></td>
                                                <td>
                                                    <form action="../requestupdate/requestsent.php" method="GET">
                                                        <input type="hidden" name="id" value="<?= $items['id']; ?>">
                                                        <button type="submit" class="btn btn-delete">REQUEST</button>
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
                            $result = mysqli_query($link, "SELECT COUNT(id) AS id FROM abm");
                            $userCount = mysqli_fetch_all($result, MYSQLI_ASSOC);
                            $total = $userCount[0]['id'];
                            $pages = ceil($total / $limit);

                            // Display the Next button if more pages are available
                            if ($page < $pages) {
                                echo '<a href="abm.php?page=' . ($page + 1) . '" class="btn btn-primary">Next 50 Books</a>';
                            }
                        }
                        
                ?>

            </div>
        </div>
    </div>

</body>
</html>
