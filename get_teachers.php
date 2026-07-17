<?php
session_start();
include 'connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([]);
    exit();
}

$uni_id = isset($_GET['uni_id']) ? (int) $_GET['uni_id'] : 0;

if ($uni_id <= 0) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT teacher_id, teacher_fname, teacher_lname
        FROM teachers
        WHERE uni_id = ?
        ORDER BY teacher_lname, teacher_fname";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $uni_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$teachers = [];

while ($row = mysqli_fetch_assoc($result)) {
    $teachers[] = [
        'teacher_id' => (int) $row['teacher_id'],
        'teacher_name' => trim($row['teacher_fname'] . ' ' . $row['teacher_lname']),
    ];
}

echo json_encode($teachers);
