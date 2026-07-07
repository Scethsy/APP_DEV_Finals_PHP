<!-- Page where users may edit their profiles -->
<!-- Accessed through profile.php -->

<?php
//To remember the User_ID Through Session
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['signup'])) {
    $fname = ucwords(strtolower(trim($_POST['fname'])));
    $lname = ucwords(strtolower(trim($_POST['lname'])));
    $bday = $_POST['bday'];
    $email = strtolower(trim($_POST['email']));
    $pass = trim($_POST['pass']);
    $pass2 = trim($_POST['pass2']);
    $uni_id = $_POST['uni_id'];

    if ($pass !="" || $pass2 != "") {
        if ($pass == $pass2){

            $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

            $sql = "UPDATE users
                    SET fname='$fname', lname='$lname', email='$email', bday='$bday', password='$hashedPassword', uni_id='$uni_id'
                    WHERE user_id = '$user_id'";

            $result = mysqli_query($conn, $sql);
            
            if ($result) {
                    header("Location: profile.php?updated=success");
                    exit();
            }else {
                echo "Error while signing up.";
            }
        } else{
            echo "Password do not match!";
        } 
    }else {
            $sql = "UPDATE users 
                    SET fname='$fname', lname='$lname', email='$email', bday='$bday', uni_id='$uni_id'
                    WHERE user_id='$user_id'";
            
            $result = mysqli_query($conn, $sql);
            if ($result) {
                    header("Location: profile.php?updated=success");
                    exit();
            }
        }

}


$sql = "SELECT fname, lname, email, uni_id, bday FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

?>

<form method="post" action="">
    <!-- Name -->
    <input type = "text" name = "fname" value = "<?php echo htmlspecialchars($user['fname']); ?>" required><br>
    <input type = "text" name = "lname" value = "<?php echo htmlspecialchars($user['lname']); ?>" required><br>
    <input type="date" name="bday" value="<?php echo htmlspecialchars($user['bday']); ?>" required><br>
    <!-- Email -->
    <input type = "email" name = "email" value = "<?php echo htmlspecialchars($user['email']); ?>" required><br>
    <!-- Password -->
    Enter New Password (Dont input if you don't want to change it)
    <input type="password" name="pass" placeholder="Optional:"><br>
    <input type="password" name="pass2" placeholder="Confirm Password"><br>
    <!-- University -->
    <select name="uni_id" required>
        <option value="">Select University</option>

        <?php
        $universities = mysqli_query($conn, "SELECT uni_id, uni_name FROM universities");

        while ($row = mysqli_fetch_assoc($universities)) {
            $selected = ($row['uni_id'] == $user['uni_id']) ? "selected" : "";

            echo "<option value='" . $row['uni_id'] . "' $selected>";
            echo htmlspecialchars($row['uni_name']);
            echo "</option>";
        }
        ?>
    </select><br><br>

    <input type="submit" value="Edit Profile" name="signup">
</form>
