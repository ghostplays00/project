
<?php
// Include config file
require_once "../config.php";


if($_SERVER["REQUEST_METHOD"] == "POST"){
    $user_id = $_POST['user_id'];


    $stmt_user = $link->prepare("SELECT lrn, section, firstname, lastname, profile_picture, username, password   FROM register WHERE id = ?");
    $stmt_user->bind_param("s", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        $row = $result_user->fetch_assoc();
        $lrn = $row['lrn'];
        $section = $row['section'];
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $profile_picture = $row['profile_picture'];
        $username = $row['username'];
        $password = $row['password'];

       $stmt = $link->prepare("INSERT INTO users (lrn, section, firstname, lastname, profile_picture, username , password) VALUES (?, ?, ?, ?, ?, ?, ?)");
       $stmt->bind_param("sssssss", $lrn, $section, $firstname, $lastname, $profile_picture, $username, $password);
       $stmt->execute();
    }
    
if(isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    
    // Perform deletion query
    $delete_query = "DELETE FROM register WHERE id = '$user_id'";
    mysqli_query($link, $delete_query);
    
    // Redirect back to the previous page
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
}


}
$link->close();
?>
