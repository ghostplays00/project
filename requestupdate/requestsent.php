<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../index.php");
    exit;
}

// Include configuration file
require_once "../config.php";

// Check for a valid ID
if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    echo "Book ID is invalid.";
    exit;
}

$id = trim($_GET['id']);
 


// Extract the subject from the referrer
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$refererPath = parse_url($referer, PHP_URL_PATH);
$segments = explode('/', $refererPath);
$subjectFolder = "";

if (count($segments) > 2) {
    // Assuming the structure is [ "", "subject", "subjectname.php" ]
    $subjectNameWithPHP = $segments[count($segments) - 1]; 
    $subjectFolder = basename($subjectNameWithPHP, '.php'); 
}

$username = $_SESSION["username"];

// Fetch book name from the $subjectFolder table based on the ID
$bookName = "";
$select_book_sql = "SELECT bookname FROM $subjectFolder WHERE id = ?";

if ($stmt = mysqli_prepare($link, $select_book_sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_bind_result($stmt, $bookName);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    } else {
        echo "Error executing book selection SQL statement: " . mysqli_error($link);
    }
} else {
    echo "Error preparing book selection SQL statement: " . mysqli_error($link);
}

if(empty($bookName)) {
    echo "Book not found.";
    exit;
}

$sql = "SELECT lrn, section, firstname, lastname FROM users WHERE username = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $username);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $lrn, $section, $firstname, $lastname);
            if (mysqli_stmt_fetch($stmt)) {
                $status = "pending";

                // Check if LRN, book name, and subject already exist
                $check_sql = "SELECT lrn FROM request WHERE lrn = ? AND book = ? AND subject = ?";
                if ($check_stmt = mysqli_prepare($link, $check_sql)) {
                    mysqli_stmt_bind_param($check_stmt, "sss", $lrn, $bookName, $subjectFolder);
                    mysqli_stmt_execute($check_stmt);
                    mysqli_stmt_store_result($check_stmt);
                    
                    if(mysqli_stmt_num_rows($check_stmt) > 0) {
                        // LRN, book name, and subject exist, update reg_date
                        $update_sql = "UPDATE request SET reg_date = CURRENT_TIMESTAMP WHERE lrn = ? AND book = ? AND subject = ?";
                        if ($update_stmt = mysqli_prepare($link, $update_sql)) {
                            mysqli_stmt_bind_param($update_stmt, "sss", $lrn, $bookName, $subjectFolder);
                            mysqli_stmt_execute($update_stmt);
                            header("Location: {$_SERVER['HTTP_REFERER']}");
                            exit;
                        } else {
                            echo "Error preparing update SQL statement: " . mysqli_error($link);
                        }
                    } else {
                        // LRN, book name, and subject don't exist, insert new record
                        $insert_sql = "INSERT INTO request (book, subject, lrn, firstname, lastname, section, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
                        if ($insert_stmt = mysqli_prepare($link, $insert_sql)) {
                            $separator = " - "; 
                            $booknameid = $id . $separator . $bookName;
                            mysqli_stmt_bind_param($insert_stmt, "sssssss", $booknameid, $subjectFolder, $lrn, $firstname, $lastname, $section, $status);

                            if (mysqli_stmt_execute($insert_stmt)) {
                                // Redirect back to the previous page
                                header("Location: {$_SERVER['HTTP_REFERER']}");
                                exit;
                            } else {
                                echo "Error executing insert SQL statement: " . mysqli_error($link);
                            }
                        } else {
                            echo "Error preparing insert SQL statement: " . mysqli_error($link);
                        }
                    }
                } else {
                    echo "Error preparing check SQL statement: " . mysqli_error($link);
                }
            } else {
                echo "Error fetching user details.";
            }
        } else {
            echo "No user found with the provided username.";
        }
    } else {
        echo "Error executing SQL statement: " . mysqli_error($link);
    }
} else {
    echo "Error preparing SQL statement: " . mysqli_error($link);
}
?>
