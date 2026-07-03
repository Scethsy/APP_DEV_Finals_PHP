<!-- Page where users may edit their previous reviews -->
<!-- Accessed through home.php -->

<?php include 'navbar.php'; 

$uni_id = $_GET['uni_id'];

$sql = "SELECT * FROM teachers WHERE uni_id = '$uni_id'";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    echo $row['teacher_fname'] . " " . $row['teacher_lname'] . "<br>";
}

?>
