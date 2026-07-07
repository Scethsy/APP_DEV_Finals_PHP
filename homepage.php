<!-- Page for Navigation -->
<!-- Access through login.php -->

<?php   
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'navbar.php'; ?>

You are at the homepage, welcome to LectSure!