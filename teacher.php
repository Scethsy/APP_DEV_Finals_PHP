<!-- Page where users may view teacher's reviews -->
<!-- Accessed through school.php -->

<?php    
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['teacher_id']) || $_GET['teacher_id'] == '') {
    header("Location: homepage.php");
    exit();
}

$teacher_id = $_GET['teacher_id'];

if (isset($_GET['review']) && $_GET['review'] == 'success') {
    echo "Review Successfully Added.";
}

include 'navbar.php'; 

//Teacher Information
$teacher_sql = "SELECT teachers.*, universities.uni_name
                FROM teachers
                JOIN universities ON teachers.uni_id = universities.uni_id
                WHERE teachers.teacher_id = '$teacher_id'";

$teacher_result = mysqli_query($conn, $teacher_sql);
$teacher = mysqli_fetch_assoc($teacher_result);
?>