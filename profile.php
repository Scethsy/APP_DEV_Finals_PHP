<!-- Page where users may view their profile -->
<!-- Accessed through home.php -->

<?php 
    session_start();

    if(!isset($_SESSION['user'])){
        header("location: login.php");
        exit();
    }

    if (isset($_GET['updated']) && $_GET['updated'] == 'success') {
        echo "Profile updated successfully!";
    }

    include 'navbar.php';
?>

<h1> Welcome <?php echo $_SESSION['user']; ?>! </h1>
Name: <?php echo $_SESSION['user']; ?> <br>
Birthday: <?php echo $_SESSION['bday']; ?><br>
University: <?php echo $_SESSION['uni_name']; ?> <br>


<a href="edit_profile.php">Edit Profile </a><br>

<a href="logout.php">logout</a>