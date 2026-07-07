<!-- Page where users may edit their previous reviews -->
<!-- Accessed through home.php -->

<?php 

session_start();

if (!isset($_GET['uni_id']) || $_GET['uni_id'] == '') {
    header("Location: homepage.php");
    exit();
}

$uni_id = $_GET['uni_id'];

include 'navbar.php'; 


$sql = "SELECT * FROM teachers WHERE uni_id = '$uni_id'";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    echo "<div>";
    echo "<h3>" . htmlspecialchars($row['teacher_fname'] . " " . $row['teacher_lname']) . "</h3>";
    echo "<a href='teacher.php?teacher_id=" . $row['teacher_id'] . "'>View Profile</a>";
    echo "<br>---------------------------------------------------------------";
    echo "</div>";
}

?>
