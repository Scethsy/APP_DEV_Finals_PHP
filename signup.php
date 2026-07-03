<?php 
include 'connection.php';

if (isset($_POST['signup'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass2 = $_POST['pass2'];
    $uni_id = $_POST['uni_id'];

    if ($pass == $pass2) {
        $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users(fname, lname, email, password, uni_id)
                VALUES('$fname', '$lname', '$email', '$hashedPassword', '$uni_id')";

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

<?php
if (isset($error)) {
    echo $error;
}
?>

<form method="post">
    First Name:<input type="text" name="fname" placeholder="Given name" required><br>
    Last Name:<input type="text" name="lname" placeholder="Surname" required><br>
    Email: <input type="email" name="email" placeholder="Email" required><br>
    Password:<input type="password" name="pass" placeholder="Password" required><br>
    Confirm Password:<input type="password" name="pass2" placeholder="Confirm Password" required><br>

    University:
    <select name="uni_id" required>
        <option value="">-- Select University --</option>

        <?php
        while ($row = mysqli_fetch_assoc($universities)) {
            echo "<option value='{$row['uni_id']}'>";
            echo htmlspecialchars($row['uni_name']);
            echo "</option>";
        }
        ?>
    </select><br><br>

    <input type="submit" value="Sign Up" name="signup">
</form>