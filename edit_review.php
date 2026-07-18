// Page where users may edit their previous reviews

<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$review_id = isset($_GET['review_id']) ? (int) $_GET['review_id'] : (int) ($_POST['review_id'] ?? 0);

if ($review_id <= 0) {
    header("Location: profile.php");
    exit();
}

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function render_edit_rating_input($name, $selected_rating) {
    $selected_rating = max(0, min(5, (int) $selected_rating));
    echo '<div class="rating-choice">';
    echo '<input type="radio" id="' . e($name . '_0') . '" name="' . e($name) . '" value="0"' . ($selected_rating === 0 ? ' checked' : '') . '>';

    for ($i = 5; $i >= 1; $i--) {
        $id = $name . '_' . $i;
        $checked = $selected_rating === $i ? ' checked' : '';
        echo '<input type="radio" id="' . e($id) . '" name="' . e($name) . '" value="' . $i . '"' . $checked . '>';
        echo '<label for="' . e($id) . '">&#9733;</label>';
    }

    echo '</div>';
}

if (isset($_POST['update_review'])) {
    $teacher_id = (int) $_POST['teacher_id'];
    $course_code = strtoupper(trim($_POST['course_code']));
    $comments = trim($_POST['comments']);
    $approach_rating = max(0, min(5, (int) ($_POST['approach_rating'] ?? 0)));
    $knowledge_rating = max(0, min(5, (int) ($_POST['knowledge_rating'] ?? 0)));
    $lenient_level = max(0, min(5, (int) ($_POST['lenient_level'] ?? 0)));
    $time_man_rating = max(0, min(5, (int) ($_POST['time_man_rating'] ?? 0)));

    $update_sql = "UPDATE reviews
                   SET teacher_id = ?, course_code = ?, approach_rating = ?, knowledge_rating = ?, lenient_level = ?, time_man_rating = ?, comments = ?
                   WHERE review_id = ? AND user_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "isiiiisii", $teacher_id, $course_code, $approach_rating, $knowledge_rating, $lenient_level, $time_man_rating, $comments, $review_id, $user_id);
    mysqli_stmt_execute($update_stmt);

    header("Location: profile.php?updated=success");
    exit();
}

/* Review edit lookup. It only allows editing reviews owned
   by the logged-in user. */
$review_sql = "SELECT reviews.*, teachers.uni_id, teachers.teacher_fname, teachers.teacher_lname
               FROM reviews
               JOIN teachers ON reviews.teacher_id = teachers.teacher_id
               WHERE reviews.review_id = '$review_id' AND reviews.user_id = '$user_id'";
$review_result = mysqli_query($conn, $review_sql);

if (!$review_result || mysqli_num_rows($review_result) !== 1) {
    header("Location: profile.php");
    exit();
}

$review = mysqli_fetch_assoc($review_result);
$universities = mysqli_query($conn, "SELECT uni_id, uni_name FROM universities ORDER BY uni_name");
$teachers = mysqli_query($conn, "SELECT teacher_id, teacher_fname, teacher_lname, uni_id FROM teachers ORDER BY teacher_lname, teacher_fname");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Review | LectSure</title>
    <link rel="stylesheet" href="css/main_css.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="edit-review-page">
    <section class="edit-review-panel">
        <form action="edit_review.php" method="post" class="review-form edit-review-form">
            <input type="hidden" name="review_id" value="<?php echo $review_id; ?>">

            <div class="form-grid edit-review-top-fields">
                <label>
                    <span>School</span>
                    <select disabled>
                        <?php while ($university = mysqli_fetch_assoc($universities)) { ?>
                            <option value="<?php echo (int) $university['uni_id']; ?>" <?php echo ((int) $university['uni_id'] === (int) $review['uni_id']) ? 'selected' : ''; ?>>
                                <?php echo e($university['uni_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </label>

                <label>
                    <span>Teacher</span>
                    <select name="teacher_id" required>
                        <?php while ($teacher = mysqli_fetch_assoc($teachers)) {
                            $teacher_name = trim($teacher['teacher_fname'] . ' ' . $teacher['teacher_lname']);
                            $selected = ((int) $teacher['teacher_id'] === (int) $review['teacher_id']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo (int) $teacher['teacher_id']; ?>" <?php echo $selected; ?>>
                                <?php echo e($teacher_name); ?>
                            </option>
                        <?php } ?>
                    </select>
                </label>

                <label>
                    <span>Course Code</span>
                    <input type="text" name="course_code" value="<?php echo e(strtoupper($review['course_code'])); ?>" required>
                </label>
            </div>

            <label class="edit-review-comment">
                <span>Post</span>
                <textarea name="comments" required><?php echo e($review['comments']); ?></textarea>
            </label>

            <div class="modal-rating-grid edit-review-rating-grid">
                <label>
                    <span>Knowledge</span>
                    <?php render_edit_rating_input('knowledge_rating', $review['knowledge_rating']); ?>
                </label>

                <label>
                    <span>Approachability</span>
                    <?php render_edit_rating_input('approach_rating', $review['approach_rating']); ?>
                </label>

                <label>
                    <span>Leniency</span>
                    <?php render_edit_rating_input('lenient_level', $review['lenient_level']); ?>
                </label>

                <label>
                    <span>Time Management</span>
                    <?php render_edit_rating_input('time_man_rating', $review['time_man_rating']); ?>
                </label>
            </div>

            <button class="post-review-button edit-review-submit" type="submit" name="update_review">Update <span>&gt;</span></button>
        </form>
    </section>
</main>
</body>
</html>
