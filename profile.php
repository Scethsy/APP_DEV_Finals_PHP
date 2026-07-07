<!-- Page where users may view their profile -->
<!-- Accessed through home.php -->

<?php 
    session_start();
    include 'connection.php';

    if(!isset($_SESSION['user'])){
        header("location: login.php");
        exit();
    }

    if (isset($_GET['updated']) && $_GET['updated'] == 'success') {
        echo "Profile updated successfully!";
    }

    $user_id = $_SESSION['user_id'];

    $sql = "SELECT users.*, universities.uni_name
            FROM users 
            JOIN universities ON users.uni_id = universities.uni_id
            WHERE users.user_id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    $numrows = mysqli_num_rows($result);

    if($numrows==1){
        $row = mysqli_fetch_array($result);
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['user'] = $row['fname'] . " " . $row['lname'];
            $_SESSION['bday'] = $row['bday'];
            $_SESSION['uni_id'] = $row['uni_id'];
            $_SESSION['uni_name'] = $row['uni_name'];
    }

    include 'navbar.php';
?>

<h1>Welcome <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
Name: <?php echo htmlspecialchars($_SESSION['user']); ?><br>
Birthday: <?php echo htmlspecialchars($_SESSION['bday']); ?><br>
University: <?php echo htmlspecialchars($_SESSION['uni_name']); ?><br>


<a href="edit_profile.php">Edit Profile </a><br>
<a href="logout.php">logout</a>