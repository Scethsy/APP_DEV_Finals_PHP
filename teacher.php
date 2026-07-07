<!-- Page where users may view teacher's reviews -->
<!-- Accessed through school.php -->

<?php    
session_start();
include 'connection.php';

$teacher_id = $_GET['teacher_id'];

if (isset($_GET['review']) && $_GET['review'] == 'success') {
    echo "Review Successfully Added.";
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'navbar.php'; ?>