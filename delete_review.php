<?php
/* Deleting a review only when the logged-in user owns it. */
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: homepage.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$review_id = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;

if ($review_id <= 0) {
    header("Location: homepage.php?delete=invalid");
    exit();
}

$sql = "DELETE FROM reviews WHERE review_id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $review_id, $user_id);
mysqli_stmt_execute($stmt);

header("Location: homepage.php?delete=done");
exit();
