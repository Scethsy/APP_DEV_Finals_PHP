<?php 
include 'connection.php';

    $universities = mysqli_query($conn, "SELECT uni_id, uni_name FROM universities");

?>

<form method="post">
    <input type="text" name="fname" placeholder="Given name"><br>
    <input type="text" name="lname" placeholder="Surname"><br>
    <input type="email" name="email" placeholder="Email"><br>
    <input type="password" name="pass" placeholder="Password"><br>
    <input type="password" name="pass2" placeholder="Confirm Password"><br>

    <!-- University Dropdown -->
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
            echo "Sign Up Successful! Please proceed to Login!";
        } else {
            echo "Error while signing up.";
        }
    } else{
        echo "Password do not match!";
    }

}
?>