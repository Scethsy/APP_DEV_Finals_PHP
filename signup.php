<?php 
include 'connection.php';

if (isset($_POST['signup'])) {
    $fname = ucwords(strtolower(trim($_POST['fname'])));
    $lname = ucwords(strtolower(trim($_POST['lname'])));
    $bday = $_POST['bday'];
    $email = strtolower(trim($_POST['email']));
    $pass = trim($_POST['pass']);
    $pass2 = trim($_POST['pass2']);
    $uni_id = $_POST['uni_id'];

    if ($pass == $pass2) {
        $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users(fname, lname, bday, email, password, uni_id)
                VALUES('$fname', '$lname','$bday', '$email', '$hashedPassword', '$uni_id')";

        $result = mysqli_query($conn, $sql);

        if ($result) {
            header("Location: login.php?signup=success");
            exit();
        } else {
            $error = "Error while signing up.";
        }
    } else {
        $error = "Passwords do not match!";
    }
}

$universities = mysqli_query($conn, "SELECT uni_id, uni_name FROM universities");
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account | LectSure</title>
    <link rel="stylesheet" href="css/main_css.css">
</head>
<body class="account-page">
    <main class="account-canvas account-canvas-register">
        <h1 class="account-logo">LectSure</h1>

        <section class="account-card register-card">
            <h2>Create an account</h2>

            <?php if (isset($error)) { ?>
                <p class="account-message error"><?php echo htmlspecialchars($error); ?></p>
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

                <label>
                    <span>Retype Password</span>
                    <input type="password" name="pass2" placeholder="Enter your password" required>
                </label>

                <h3>Personal Information</h3>

                <div class="account-grid account-grid-name">
                    <label>
                        <span>First Name</span>
                        <input type="text" name="fname" placeholder="e.g Kirk Benedict" required>
                    </label>

                    <label>
                        <span>Last Name</span>
                        <input type="text" name="lname" placeholder="e.g Sarmiento" required>
                    </label>
                </div>

                <div class="account-grid account-grid-two">
                    <label>
                        <span>School Name</span>
                        <select name="uni_id" required>
                            <option value="">Select University</option>

                            <?php
                            while ($row = mysqli_fetch_assoc($universities)) {
                                echo "<option value='{$row['uni_id']}'>";
                                echo htmlspecialchars($row['uni_name']);
                                echo "</option>";
                            }
                            ?>
                        </select>
                    </label>

                    <label>
                        <span>Birthday</span>
                        <input type="date" name="bday" required>
                    </label>
                </div>

                <input class="account-submit" type="submit" value="Create account" name="signup">
            </form>

            <p class="account-switch">Already Have An Account? <a href="login.php">Log In</a></p>
        </section>
    </main>
</body>
</html>
