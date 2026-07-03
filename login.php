<!-- Page where users logs in -->
<!-- initial page after register -->
<?php 
    if (isset($_GET['signup']) && $_GET['signup'] == 'success') {
        echo "Sign Up Successful! Please login.";
    }
    
    if (isset($_POST['Login'])) {
        $email = $_POST['email'];
        $pass = $_POST['pass'];
    
        include 'connection.php';

        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        $numrows = mysqli_num_rows($result);

        if($numrows==1){
            $row = mysqli_fetch_array($result);
            
            if(password_verify($pass, $row['password'])){
                session_start();

                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user'] = $row['fname'] . " " . $row['lname'];

                header("location: profile.php");
                exit();
            } else {
            echo "Invalid login credentials.";
            }
        }
    }
?>

<h2> Login to LectSure! </h2>

<form method="post">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="pass" placeholder="Password" required><br>
    <input type="submit" value="Login" name = "Login">

</form>

Don't have an account for LectSure? Click here to <a href="signup.php"> Sign Up</a>!