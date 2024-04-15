
<?php
// Include config file
require_once "config.php";
// Define variables and initialize with empty values
$firstname = $lastname = $section = $lrn = $username = $password = $confirm_password = "";
$firstname_err = $lastname_err = $section_err = $lrn_err = $username_err = $password_err = $confirm_password_err = "";
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    

    // Validate firstname
    if(empty(trim($_POST["firstname"]))){
        $firstname_err = "Please enter your first name.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["firstname"]))){
        $firstname_err = "Please enter a valid name.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE firstname = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_firstname);
            
            // Set parameters
            $param_firstname = trim($_POST["firstname"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                    $firstname = trim($_POST["firstname"]);
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    // Validate lastname
    if(empty(trim($_POST["lastname"]))){
        $lastname_err = "Please enter your last name.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["lastname"]))){
        $lastname_err = "Please enter a valid name.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE lastname = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_lastname);
            
            // Set parameters
            $param_lastname = trim($_POST["lastname"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                    $lastname = trim($_POST["lastname"]);
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    // Validate section
    if(empty(trim($_POST["section"]))){
        $section_err = "Please enter your Section.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE section = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_section);
            
            // Set parameters
            $param_section = trim($_POST["section"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                    $section = trim($_POST["section"]);
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate lrn
    if(empty(trim($_POST["lrn"]))){
        $lrn_err = "Please enter your LRN.";
    } elseif(!preg_match('/^[0-9_]+$/', trim($_POST["lrn"]))){
        $lrn_err = "LRN can only contain numbers.";
    } else{
        // Prepare a select statement to check if the LRN already exists
        $sql = "SELECT id FROM users WHERE lrn = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_lrn);

            // Set parameters
            $param_lrn = trim($_POST["lrn"]);
    
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
    
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $lrn_err = "This LRN is already registered.";
                } else{
                    $lrn = trim($_POST["lrn"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }






    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($section_err) && empty($lrn_err) && empty($firstname_err) && empty($lastname_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO register (username, password, section, firstname, lastname, lrn) VALUES (?, ?, ?, ?, ?, ?)";
 
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $param_username, $param_password, $param_section, $param_firstname, $param_lastname, $param_lrn);
   
            // Set parameters
            $param_firstname = $firstname;
            $param_lastname = $lastname;
            $param_lrn = $lrn;
            $param_section = $section;
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: index.php");

                // Create text file with user's LRN as filename
                $messageFolder = "messages/";
                if (!file_exists($messageFolder)) {
                    mkdir($messageFolder, 0777, true);
                }
                $filename = $messageFolder . $lrn . ".txt";
                $file = fopen($filename, "w");
                fclose($file);
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eclarian's Nook Library System</title>
  <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>



    <div class="topnav">
        <h1>Welcome to Eclarian's Nook</h1>
        <img src="pictures/logo.png">
        <a href="admin/index.php">Admin</a>
    </div>
    <div>
        <div class="loginform">
            <h2>Sign Up</h2>
            <p>Please fill this form to create an account.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                   <label>Username</label>
                    <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>    
                <div class="form-group">
                   <label>First Name</label>
                    <input type="text" name="firstname" class="form-control <?php echo (!empty($firstname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $firstname; ?>">
                    <span class="invalid-feedback"><?php echo $firstname_err; ?></span>
                </div>    
                <div class="form-group">
                   <label>Last Name</label>
                    <input type="text" name="lastname" class="form-control <?php echo (!empty($lastname_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $lastname; ?>">
                    <span class="invalid-feedback"><?php echo $lastname_err; ?></span>
                </div>    
                <div class="form-group">
                   <label>LRN</label>
                    <input type="text" name="lrn" required pattern="\d{12}" class="form-control <?php echo (!empty($lrn_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $lrn; ?>">
                    <span class="invalid-feedback"><?php echo $lrn_err; ?></span>
                </div>    
                <div class="form-group">
                   <label>Section</label>
                    <input type="text" name="section" class="form-control <?php echo (!empty($section_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $section; ?>">
                    <span class="invalid-feedback"><?php echo $section_err; ?></span>
                </div>    
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Submit">
                    <input type="reset" class="btn btn-secondary ml-2" value="Clear">
                </div class="classreg">
                <p>Already have an account? <a href="index.php">Login here</a>.</p>
            </form>
        </div>  
    </div>

</body>
</html>



