<!-- Page where users may view teachers by university -->
<!-- Accessed through navbar.php -->

<?php
session_start();
include 'connection.php';

if (!isset($_GET['uni_id']) || $_GET['uni_id'] == '') {
    header("Location: homepage.php");
    exit();
}

$uni_id = (int) $_GET['uni_id'];

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/* To display average teacher ratings rounded to the nearest half star. */
function render_average_stars($rating) {
    $rating = round(((float) $rating) * 2) / 2;
    echo '<div class="teacher-average-stars" aria-label="' . e($rating) . ' out of 5 stars">';

    for ($i = 1; $i <= 5; $i++) {
        if ($rating >= $i) {
            echo '<span class="avg-star is-filled">&#9733;</span>';
        } elseif ($rating >= ($i - 0.5)) {
            echo '<span class="avg-star is-half">&#9733;</span>';
        } else {
            echo '<span class="avg-star">&#9733;</span>';
        }
    }

    echo '</div>';
}

$university_sql = "SELECT uni_id, uni_name FROM universities WHERE uni_id = ?";
$university_stmt = mysqli_prepare($conn, $university_sql);
mysqli_stmt_bind_param($university_stmt, "i", $uni_id);
mysqli_stmt_execute($university_stmt);
$university_result = mysqli_stmt_get_result($university_stmt);
$university = mysqli_fetch_assoc($university_result);

if (!$university) {
    header("Location: homepage.php");
    exit();
}

/* The average of all review rating columns for that teacher. */
$teachers_sql = "
    SELECT
        teachers.teacher_id,
        teachers.teacher_fname,
        teachers.teacher_lname,
        AVG((reviews.approach_rating + reviews.knowledge_rating + reviews.lenient_level + reviews.time_man_rating) / 4) AS average_rating
    FROM teachers
    LEFT JOIN reviews ON teachers.teacher_id = reviews.teacher_id
    WHERE teachers.uni_id = ?
    GROUP BY teachers.teacher_id, teachers.teacher_fname, teachers.teacher_lname
    ORDER BY teachers.teacher_lname, teachers.teacher_fname
";
$teachers_stmt = mysqli_prepare($conn, $teachers_sql);
mysqli_stmt_bind_param($teachers_stmt, "i", $uni_id);
mysqli_stmt_execute($teachers_stmt);
$teachers = mysqli_stmt_get_result($teachers_stmt);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($university['uni_name']); ?> | LectSure</title>
    <link rel="stylesheet" href="css/main_css.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="university-page">
    <section class="university-hero">
        <h1><?php echo e($university['uni_name']); ?></h1>
    </section>

    <section class="teacher-list-panel" aria-label="Teachers in <?php echo e($university['uni_name']); ?>">
        <?php if (mysqli_num_rows($teachers) === 0) { ?>
            <article class="teacher-row empty-teacher-row">
                <span>No teachers listed yet.</span>
            </article>
        <?php } ?>

        <?php while ($teacher = mysqli_fetch_assoc($teachers)) {
            $teacher_name = trim($teacher['teacher_fname'] . ' ' . $teacher['teacher_lname']);
            $average_rating = $teacher['average_rating'] === null ? 0 : (float) $teacher['average_rating'];
        ?>
            <a class="teacher-row" href="teacher.php?teacher_id=<?php echo (int) $teacher['teacher_id']; ?>">
                <span><?php echo e($teacher_name); ?></span>
                <?php render_average_stars($average_rating); ?>
            </a>
        <?php } ?>
    </section>
</main>
</body>
</html>
