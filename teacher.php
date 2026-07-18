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

$teacher_id = (int) $_GET['teacher_id'];

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function teacher_initials($name) {
    $parts = preg_split('/\s+/', trim($name));
    $initials = '';
    foreach ($parts as $part) {
        if ($part !== '') {
            $initials .= strtoupper($part[0]);
        }
        if (strlen($initials) >= 2) {
            break;
        }
    }
    return $initials ?: 'LS';
}

function teacher_page_stars($rating) {
    $rating = max(0, min(5, (int) $rating));
    echo '<div class="stars" aria-label="' . $rating . ' out of 5 stars">';
    for ($i = 1; $i <= 5; $i++) {
        $filled = $i <= $rating ? ' is-filled' : '';
        echo '<span class="star' . $filled . '">&#9733;</span>';
    }
    echo '</div>';
}

function teacher_course_pills($course_code) {
    $codes = array_filter(array_map('trim', explode(',', (string) $course_code)));
    foreach ($codes as $code) {
        echo '<span class="course-pill">' . e(strtoupper($code)) . '</span>';
    }
}

/* To display rating averages cleanly while keeping at least one decimal. */
function teacher_average_number($rating) {
    if ($rating === null) {
        return '0.0';
    }

    $formatted = rtrim(rtrim(number_format((float) $rating, 2), '0'), '.');
    return strpos($formatted, '.') === false ? $formatted . '.0' : $formatted;
}

//Teacher Information
$teacher_sql = "SELECT teachers.*, universities.uni_name
                FROM teachers
                JOIN universities ON teachers.uni_id = universities.uni_id
                WHERE teachers.teacher_id = ?";
$teacher_stmt = mysqli_prepare($conn, $teacher_sql);
mysqli_stmt_bind_param($teacher_stmt, "i", $teacher_id);
mysqli_stmt_execute($teacher_stmt);
$teacher_result = mysqli_stmt_get_result($teacher_stmt);
$teacher = mysqli_fetch_assoc($teacher_result);

if (!$teacher) {
    header("Location: homepage.php");
    exit();
}

$teacher_name = trim($teacher['teacher_fname'] . ' ' . $teacher['teacher_lname']);

/* Rating summary query for the teacher profile sidebar. */
$rating_summary_sql = "
    SELECT
        AVG(approach_rating) AS approach_avg,
        AVG(knowledge_rating) AS knowledge_avg,
        AVG(lenient_level) AS strict_avg,
        AVG(time_man_rating) AS time_avg
    FROM reviews
    WHERE teacher_id = ?
";
$rating_summary_stmt = mysqli_prepare($conn, $rating_summary_sql);
mysqli_stmt_bind_param($rating_summary_stmt, "i", $teacher_id);
mysqli_stmt_execute($rating_summary_stmt);
$rating_summary_result = mysqli_stmt_get_result($rating_summary_stmt);
$rating_summary = mysqli_fetch_assoc($rating_summary_result);

/* To show every review that belongs to this teacher, including the student name and school for the post header. */
$reviews_sql = "
    SELECT
        reviews.review_id,
        reviews.course_code,
        reviews.approach_rating,
        reviews.knowledge_rating,
        reviews.lenient_level,
        reviews.time_man_rating,
        reviews.comments,
        users.fname,
        users.lname,
        universities.uni_name AS user_university
    FROM reviews
    JOIN users ON reviews.user_id = users.user_id
    JOIN universities ON users.uni_id = universities.uni_id
    WHERE reviews.teacher_id = ?
    ORDER BY reviews.review_id DESC
";
$reviews_stmt = mysqli_prepare($conn, $reviews_sql);
mysqli_stmt_bind_param($reviews_stmt, "i", $teacher_id);
mysqli_stmt_execute($reviews_stmt);
$reviews = mysqli_stmt_get_result($reviews_stmt);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($teacher_name); ?> | LectSure</title>
    <link rel="stylesheet" href="css/main_css.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="teacher-profile-shell">
    <a class="teacher-back-button" href="school.php?uni_id=<?php echo (int) $teacher['uni_id']; ?>">Back</a>
    <aside class="teacher-profile-sidebar">
        <div class="profile-avatar teacher-profile-avatar"><?php echo e(teacher_initials($teacher_name)); ?></div>
        <h1><?php echo e($teacher_name); ?></h1>
        <p><?php echo e($teacher['uni_name']); ?></p>

        <!-- Teacher rating summary below the profile information. -->
        <section class="teacher-rating-summary" aria-label="Teacher rating summary">
            <div class="teacher-rating-row">
                <span>Approachability</span>
                <strong><?php echo e(teacher_average_number($rating_summary['approach_avg'] ?? null)); ?></strong>
            </div>
            <div class="teacher-rating-row">
                <span>Knowledge</span>
                <strong><?php echo e(teacher_average_number($rating_summary['knowledge_avg'] ?? null)); ?></strong>
            </div>
            <div class="teacher-rating-row">
                <span>Leniency</span>
                <strong><?php echo e(teacher_average_number($rating_summary['strict_avg'] ?? null)); ?></strong>
            </div>
            <div class="teacher-rating-row">
                <span>Time Management</span>
                <strong><?php echo e(teacher_average_number($rating_summary['time_avg'] ?? null)); ?></strong>
            </div>
        </section>
    </aside>

    <section class="teacher-review-feed" aria-label="Reviews for <?php echo e($teacher_name); ?>">
        <?php if (mysqli_num_rows($reviews) === 0) { ?>
            <article class="empty-feed">
                <h1>No reviews yet</h1>
                <p>This teacher does not have reviews yet.</p>
            </article>
        <?php } ?>

        <?php while ($review = mysqli_fetch_assoc($reviews)) {
            $student_name = trim($review['fname'] . ' ' . $review['lname']);
        ?>
            <article class="post-card teacher-review-card">
                <header class="post-author">
                    <div class="avatar"><?php echo e(teacher_initials($student_name)); ?></div>
                    <div>
                        <h2><?php echo e($student_name); ?></h2>
                        <p><?php echo e($review['user_university']); ?></p>
                    </div>
                </header>

                <p class="post-text"><?php echo nl2br(e($review['comments'])); ?></p>

                <section class="professor-card">
                    <div class="professor-heading">
                        <div>
                            <span>Professor:</span>
                            <strong><?php echo e($teacher_name); ?></strong>
                            <small><?php echo e($teacher['uni_name']); ?></small>
                        </div>
                        <div class="course-pills">
                            <?php teacher_course_pills($review['course_code']); ?>
                        </div>
                    </div>

                    <div class="rating-grid">
                        <div>
                            <span>Approachability</span>
                            <?php teacher_page_stars($review['approach_rating']); ?>
                        </div>
                        <div>
                            <span>Leniency</span>
                            <?php teacher_page_stars($review['lenient_level']); ?>
                        </div>
                        <div>
                            <span>Knowledge</span>
                            <?php teacher_page_stars($review['knowledge_rating']); ?>
                        </div>
                        <div>
                            <span>Time Management</span>
                            <?php teacher_page_stars($review['time_man_rating']); ?>
                        </div>
                    </div>
                </section>
            </article>
        <?php } ?>
    </section>
</main>
</body>
</html>
