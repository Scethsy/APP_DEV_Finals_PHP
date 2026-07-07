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

$sql = "SELECT fname, lname, email, uni_id FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>
<form method="post" action="profile.php">
    <!-- Name -->
    <input type = "text" name = "fname" value = "<?php echo htmlspecialchars($user['fname']); ?>" required><br>
    <input type = "text" name = "lname" value = "<?php echo htmlspecialchars($user['lname']); ?>" required><br>
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

<?php
if (isset($_POST['signup'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass2 = $_POST['pass2'];
    $uni_id= $_POST['uni_id'];

    if ($pass == $pass2){

        $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users(fname, lname, email, password, uni_id)
                VALUES('$fname', '$lname', '$email', '$pass', '$uni_id')";

        $result = mysqli_query($conn, $sql);
        
        if ($result){
            echo "Profile Updated!";
        } else {
            echo "Error while signing up.";
        }
    } else{
        echo "Password do not match!";
    }

}
?>