<?php
/* It saves reviews and creates a teacher first when the user chose "+ Add New". */
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
$uni_id = isset($_POST['uni_id']) ? (int) $_POST['uni_id'] : 0;
$teacher_id_raw = $_POST['teacher_id'] ?? '';
$course_code = strtoupper(trim($_POST['course_code'] ?? ''));
$comments = trim($_POST['comments'] ?? '');
$approach_rating = max(0, min(5, (int) ($_POST['approach_rating'] ?? 0)));
$knowledge_rating = max(0, min(5, (int) ($_POST['knowledge_rating'] ?? 0)));
$lenient_level = max(0, min(5, (int) ($_POST['lenient_level'] ?? 0)));
$time_man_rating = max(0, min(5, (int) ($_POST['time_man_rating'] ?? 0)));

if ($uni_id <= 0 || $course_code === '' || $comments === '') {
    header("Location: homepage.php?review=missing");
    exit();
}

if ($teacher_id_raw === 'add') {
    $teacher_fname = ucwords(strtolower(trim($_POST['teacher_fname'] ?? '')));
    $teacher_lname = ucwords(strtolower(trim($_POST['teacher_lname'] ?? '')));

    if ($teacher_fname === '' || $teacher_lname === '') {
        header("Location: homepage.php?review=missing_teacher");
        exit();
    }

    $teacher_sql = "INSERT INTO teachers (teacher_fname, teacher_lname, uni_id)
                    VALUES (?, ?, ?)";
    $teacher_stmt = mysqli_prepare($conn, $teacher_sql);
    mysqli_stmt_bind_param($teacher_stmt, "ssi", $teacher_fname, $teacher_lname, $uni_id);

    if (!mysqli_stmt_execute($teacher_stmt)) {
        die("Error adding teacher: " . mysqli_error($conn));
    }

    $teacher_id = mysqli_insert_id($conn);
} else {
    $teacher_id = (int) $teacher_id_raw;

    if ($teacher_id <= 0) {
        header("Location: homepage.php?review=missing_teacher");
        exit();
    }

    $teacher_check_sql = "SELECT teacher_id FROM teachers WHERE teacher_id = ? AND uni_id = ?";
    $teacher_check_stmt = mysqli_prepare($conn, $teacher_check_sql);
    mysqli_stmt_bind_param($teacher_check_stmt, "ii", $teacher_id, $uni_id);
    mysqli_stmt_execute($teacher_check_stmt);
    $teacher_check_result = mysqli_stmt_get_result($teacher_check_stmt);

    if (mysqli_num_rows($teacher_check_result) !== 1) {
        header("Location: homepage.php?review=invalid_teacher");
        exit();
    }
}

/* When the homepage popup loaded an existing review, update that
   owned row from the popup instead of sending the user to another page. */
if ($review_id > 0) {
    $update_sql = "UPDATE reviews
                   SET teacher_id = ?, course_code = ?, approach_rating = ?, knowledge_rating = ?, lenient_level = ?, time_man_rating = ?, comments = ?
                   WHERE review_id = ? AND user_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "isiiiisii", $teacher_id, $course_code, $approach_rating, $knowledge_rating, $lenient_level, $time_man_rating, $comments, $review_id, $user_id);

    if (!mysqli_stmt_execute($update_stmt)) {
        die("Error updating review: " . mysqli_error($conn));
    }

    header("Location: homepage.php?review=updated");
    exit();
}

/* Prevent duplicate reviews for the same user and teacher.
   This fallback protects the UNIQUE(user_id, teacher_id) index if a request
   reaches this handler without the existing review_id from the popup. */
$existing_review_sql = "SELECT review_id FROM reviews WHERE user_id = ? AND teacher_id = ? LIMIT 1";
$existing_review_stmt = mysqli_prepare($conn, $existing_review_sql);
mysqli_stmt_bind_param($existing_review_stmt, "ii", $user_id, $teacher_id);
mysqli_stmt_execute($existing_review_stmt);
$existing_review_result = mysqli_stmt_get_result($existing_review_stmt);
$existing_review = mysqli_fetch_assoc($existing_review_result);

if ($existing_review) {
    header("Location: homepage.php?review_uni_id=" . $uni_id . "&review_teacher_id=" . $teacher_id . "#review-modal");
    exit();
}

$review_sql = "INSERT INTO reviews
    (user_id, teacher_id, course_code, approach_rating, knowledge_rating, lenient_level, time_man_rating, comments)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$review_stmt = mysqli_prepare($conn, $review_sql);
mysqli_stmt_bind_param(
    $review_stmt,
    "iisiiiis",
    $user_id,
    $teacher_id,
    $course_code,
    $approach_rating,
    $knowledge_rating,
    $lenient_level,
    $time_man_rating,
    $comments
);

if (!mysqli_stmt_execute($review_stmt)) {
    die("Error adding review: " . mysqli_error($conn));
}

header("Location: homepage.php?review=success");
exit();
