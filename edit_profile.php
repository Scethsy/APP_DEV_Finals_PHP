<!-- Page where users may edit their profiles -->
<!-- Accessed through profile.php -->

<?php
//To remember the User_ID Through Session
session_start();
include 'connection.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT fname, lname, email, uni_id FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>
<form method="post" action="profile.php">
    <!-- Name -->
    <input type = "text" name = "fname" value = "<?php echo htmlspecialchars($user['fname']); ?>" required>
    <input type = "text" name = "lname" value = "<?php echo htmlspecialchars($user['lname']); ?>" required>
    <!-- Email -->
    <input type = "email" name = "email" value = "<?php echo htmlspecialchars($user['email']); ?>" required>
    <!-- University -->

    <input type="submit" value="Sign Up" name="signup">
</form>