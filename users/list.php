<?php

// Initialize the session

session_start();

 

// Check if the user is logged in, if not then redirect him to login page

if(!isset($_SESSION["adminloggedin"]) || $_SESSION["adminloggedin"] !== true){

    header("location: index.php");

    exit;

}

?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Local CSS -->
    <link rel="stylesheet" href="../style.css?v=<?php echo time(); ?>">
    <title>User Manager</title>
</head>
<body>
    <div class="topnav">
        <h1>Welcome to Admin Eclarian's Nook Panel</h1> 
        <img src="../pictures/logo.png">    
        <a href="../admin/adminmanage.php">Back</a>   
        <a href="../users/approval.php">Registration Approval</a>      
    </div>
    <div class="container-list">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Student User Manager</h4>
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

            <div class="col-md-12">
                <div class="card mt-4">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>LRN</th>
                                    <th>Section</th>
                                    <th>Username</th>
                                    <th>Registered Date</th>
                                    <th>Message</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    
                                    require_once "../config.php";

                                    if(isset($_GET['search']))
                                    {
                                        $filtervalues = $_GET['search'];
                                        $query = "SELECT * FROM users WHERE CONCAT(username, lastname, firstname, lrn, section) LIKE '%$filtervalues%' ";
                                    } else {
                                        $limit = 50; // Number of entries to show in a page.
                                        // Look for a GET variable page if not found default is 1.        
                                        $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                        $start = ($page-1) * $limit;                                 
                                        $query = "SELECT * FROM users LIMIT $start, $limit";
                                    }
                                    
                                    $query_run = mysqli_query($link, $query);

                                    if(mysqli_num_rows($query_run) > 0)
                                    {
                                        foreach($query_run as $items)
                                        {
                                            ?>
                                            <tr>
                                                <td><?= $items['firstname']; ?></td>
                                                <td><?= $items['lastname']; ?></td>
                                                <td><?= $items['lrn']; ?></td>
                                                <td><?= $items['section']; ?></td>
                                                <td><?= $items['username']; ?></td>
                                                <td><?= $items['created_at']; ?></td>
                                                <td>
                                                    <form action="../messageadmin/message.php" method="GET">
                                                        <input type="hidden" name="lrn" value="<?= $items['lrn']; ?>">
                                                        <button type="submit" class="btn btn-delete">Message</button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <form action="delete_user.php" method="POST">
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
                                                <td colspan="4">No Record Found</td>
                                            </tr>
                                        <?php
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
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
