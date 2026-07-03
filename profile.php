<!-- Page where users may view their profile -->
<!-- Accessed through home.php -->

<?php 
    session_start();

    if(!isset($_SESSION['user'])){
        header("location: login.php");
    }
?>

<h1> Welcome <?php echo $_SESSION['user']; ?>! </h1>

<a href="edit_profile.php">Edit Profile </a>

<a href="logout.php">logout</a>