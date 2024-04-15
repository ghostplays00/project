<?php

session_start();

if (!isset($_SESSION["adminloggedin"]) || $_SESSION["adminloggedin"] !== true) {
    header("location: index.php");
    exit;
}

include_once "../config.php";

// Define a function to handle redirection with error
function redirectWithError($error) {
    $_SESSION['error'] = $error;
    header("Location: settimer.php"); // Redirect to a custom error page
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $validSubjectCategories = ['gas', 'humms', 'abm', 'ict', 'stem'];
    $subjectCategory = $_POST['subject'];
    
    if (!in_array($subjectCategory, $validSubjectCategories)) {
        redirectWithError("Invalid subject category.");
    }

    // Additional code where errors might occur
    // Replace echo or die() with redirectWithError() wherever necessary
    



    $lrn = $_POST['LRN'];
    $bookid = $_POST['bookId'];

    // Assuming your validation passed and you're ready to query
    $stmt_user = $link->prepare("SELECT firstname, lastname FROM users WHERE lrn = ?");
    $stmt_user->bind_param("s", $lrn);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        $row = $result_user->fetch_assoc();
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];

        // Fetch bookname from the subject category table
        $query = "SELECT bookname FROM $subjectCategory WHERE id = ?";
        $stmt_book = $link->prepare($query);
        if ($stmt_book) {
            $stmt_book->bind_param("i", $bookid);
            $stmt_book->execute();
            $result_book = $stmt_book->get_result();
            if ($result_book->num_rows > 0) {
                $book_row = $result_book->fetch_assoc();
                $bookname = $book_row['bookname'];
                // Now you have the bookname, proceed with your logic
            } else {
                redirectWithError( "No book found with ID: " . $bookid);
            }
            $stmt_book->close();
        } else {
            // Handle error: Prepare statement failed
            redirectWithError( "Error preparing statement for fetching book information.");
        }

        // Prepare and bind parameters for inserting into bookrequested table
        $stmt = $link->prepare("INSERT INTO bookrequested (book_id, subject_id, lrn, date_start, due_date, firstname, lastname, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'CLAIMED')");
        $stmt->bind_param("sssssss", $bookname, $subjectCategory, $lrn, $dateStart, $dueDate, $firstname, $lastname);

        // Set parameters
        $subjectId = $_POST['bookId']; // Assuming 'bookId' corresponds to subject_id in the database
        $subjectCategory = $_POST['subject']; // Assuming 'subject' corresponds to subject_category in the database
        $lrn = $_POST['LRN']; // Assuming 'LRN' corresponds to lrn in the database
        $dateStart = date("d - m - Y - H - i", strtotime($_POST['countdownTime'])); // Format date and time for date_start
        $dueDate = date("d - m - Y - H - i", strtotime($_POST['dateAdded'])); // Format date and set time to 23:00 for due_date

        // Execute the statement
        if ($stmt->execute()) {
            header("Location: ".$_SERVER['HTTP_REFERER']);
        } else {
            echo "Error: " . $stmt->error;
        }

    } else {
        redirectWithError( "No user found with LRN: " . $lrn);
    }
    $stmt_user->close();
}

$link->close();
?>
