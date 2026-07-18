<!-- Page where users logs in -->
<!-- initial page after register -->
<?php 
    session_start();

    /* Store status text in variables so messages appear inside the card */
    $success_message = "";
    $error_message = "";

    if (isset($_GET['signup']) && $_GET['signup'] == 'success') {
        $success_message = "Sign Up Successful! Please login.";
    }

    if (isset($_POST['Login'])) {
        $email = strtolower(trim($_POST['email']));
        $pass = trim($_POST['pass']);
    
        include 'connection.php';

        $sql = "SELECT users.*, universities.uni_name
                FROM users 
                JOIN universities ON users.uni_id = universities.uni_id
                WHERE users.email = '$email'";
        $result = mysqli_query($conn, $sql);
        $numrows = mysqli_num_rows($result);

        if($numrows==1){
            $row = mysqli_fetch_array($result);
            
            if(password_verify($pass, $row['password'])){

                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user'] = $row['fname'] . " " . $row['lname'];
                $_SESSION['bday'] = $row['bday'];
                $_SESSION['uni_id'] = $row['uni_id'];
                $_SESSION['uni_name'] = $row['uni_name'];

                header("location: homepage.php");
                exit();
            } else {
                $error_message = "Invalid login credentials.";
            }
        } else {
            $error_message = "Invalid login credentials.";
        }
    }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | LectSure</title>
    <link rel="stylesheet" href="css/main_css.css">
</head>
<body class="account-page">
    <main class="account-canvas account-canvas-login">
        <h1 class="account-logo">LectSure</h1>

        <section class="account-card login-card">
            <h2>Login to your school account</h2>

            <?php if ($success_message !== "") { ?>
                <p class="account-message success"><?php echo htmlspecialchars($success_message); ?></p>
            <?php } ?>

            <?php if ($error_message !== "") { ?>
                <p class="account-message error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php } ?>

            <form method="post" class="account-form">
                <label>
                    <span>Email</span>
                    <input type="email" name="email" placeholder="@school.edu.ph" required>
                </label>

                <label>
                    <span>Password</span>
                    <input type="password" name="pass" placeholder="Enter your password" required>
                </label>

                <input class="account-submit" type="submit" value="Login now" name="Login">
            </form>

            <p class="account-switch">Don't Have An Account? <a href="signup.php">Sign Up</a></p>
        </section>
    </main>
</body>
</html>
