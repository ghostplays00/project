<?php

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["adminloggedin"]) || $_SESSION["adminloggedin"] !== true){
    header("location: ../admin/index.php");
    exit;
}

// Include configuration file
require_once "../config.php";

// Function to update status
function updateStatus($link, $id, $status) {
    $update_query = "UPDATE request SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($link, $update_query);
    mysqli_stmt_bind_param($stmt, "si", $status, $id);
    if(mysqli_stmt_execute($stmt)) {
        return true;
    } else {
        return false;
    }
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
    </div>
    <div class="container-list">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Request Manager</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-7">
                                <form action="" method="GET">
                                    <div class="input-group mb-3">
                                        <input type="text" name="search" required value="<?php if(isset($_GET['search'])){ echo $_GET['search']; } ?>" class="form-control" placeholder="Search data">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                        <?php if (isset($_GET['search']) && !empty($_GET['search'])) : ?>
                                            <a href="list.php" class="btn btn-secondary">Clear Filter</a>
                                        <?php endif; ?>
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
                                    <th>id</th>
                                    <th>Subject</th>
                                    <th>BookID / Book Name</th>
                                    <th>LRN</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Section</th>
                                    <th>Status</th>
                                    <th>Update Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // Handle status update
                                    if(isset($_POST['status_update'])) {
                                        $id = $_POST['id'];
                                        $status = $_POST['status'];
                                        if(updateStatus($link, $id, $status)) {
                                            echo '<div class="alert alert-success" role="alert">Status updated successfully.</div>';
                                        } else {
                                            echo '<div class="alert alert-danger" role="alert">Failed to update status.</div>';
                                        }
                                    }
                                    
                                    if(isset($_GET['search']) && !empty($_GET['search']))
                                    {
                                        $filtervalues = $_GET['search'];
                                        $query = "SELECT * FROM request WHERE CONCAT(subject, book, lrn, firstname, lastname, section, status) LIKE '%$filtervalues%' ";
                                    } else {
                                        $limit = 50; // Number of entries to show in a page.
                                        // Look for a GET variable page if not found default is 1.        
                                        $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                        $start = ($page-1) * $limit;                                 
                                        $query = "SELECT * FROM request LIMIT $start, $limit";
                                    }
                                    
                                    $query_run = mysqli_query($link, $query);

                                    if(mysqli_num_rows($query_run) > 0)
                                    {
                                        foreach($query_run as $items)
                                        {
                                            ?>
                                            <tr>
                                                <td><?= $items['id']; ?></td>
                                                <td><?= $items['subject']; ?></td>
                                                <td><?= $items['book']; ?></td>
                                                <td><?= $items['lrn']; ?></td>
                                                <td><?= $items['firstname']; ?></td>
                                                <td><?= $items['lastname']; ?></td>
                                                <td><?= $items['section']; ?></td>
                                                <td>
                                                    <form action="" method="POST">
                                                        <input type="hidden" name="id" value="<?= $items['id']; ?>">
                                                        <select name="status" onchange="this.form.submit()">
                                                            <option value="approved" <?= $items['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                                                            <option value="decline" <?= $items['status'] == 'decline' ? 'selected' : ''; ?>>Decline</option>
                                                            <option value="pending" <?= $items['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        </select>
                                                        <input type="hidden" name="status_update">
                                                    </form>
                                                </td>
                                                <td><?= $items['reg_date']; ?></td>
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
                            $result = mysqli_query($link, "SELECT COUNT(id) AS id FROM request");
                            $userCount = mysqli_fetch_all($result, MYSQLI_ASSOC);
                            $total = $userCount[0]['id'];
                            $pages = ceil($total / $limit);

                            // Display the Next button if more pages are available
                            if ($page < $pages) {
                                echo '<a href="list.php?page=' . ($page + 1) . '" class="btn btn-primary">Next 50 request</a>';
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
