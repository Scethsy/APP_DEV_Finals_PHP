<?php
/* CODEX CHANGE: Homepage integration replaces the original placeholder text with
   a database-backed feed and a no-JavaScript review popup. Original file only
   started the session, checked login, included navbar.php, and printed a short
   welcome message. */
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = (int) $_SESSION['user_id'];
$selected_uni_id = isset($_GET['review_uni_id']) ? (int) $_GET['review_uni_id'] : 0;
$selected_teacher_id = $_GET['review_teacher_id'] ?? '';

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/* CODEX CHANGE: Helper functions added for the homepage feed display. */
function user_initials($name) {
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

function render_stars($rating) {
    $rating = max(0, min(5, (int) $rating));
    echo '<div class="stars" aria-label="' . $rating . ' out of 5 stars">';

    for ($i = 1; $i <= 5; $i++) {
        $filled = $i <= $rating ? ' is-filled' : '';
        echo '<span class="star' . $filled . '">&#9733;</span>';
    }

    echo '</div>';
}

function render_rating_input($name, $selected_rating = 0) {
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

function render_course_pills($course_code) {
    $codes = array_filter(array_map('trim', explode(',', (string) $course_code)));

    foreach ($codes as $code) {
        echo '<span class="course-pill">' . e(strtoupper($code)) . '</span>';
    }
}

/* CODEX CHANGE: Queries added for homepage feed and no-JavaScript modal options. */
$universities = mysqli_query($conn, "SELECT uni_id, uni_name FROM universities ORDER BY uni_name");

$selected_teachers = false;
if ($selected_uni_id > 0) {
    $teacher_sql = "SELECT teacher_id, teacher_fname, teacher_lname
                    FROM teachers
                    WHERE uni_id = ?
                    ORDER BY teacher_lname, teacher_fname";
    $teacher_stmt = mysqli_prepare($conn, $teacher_sql);
    mysqli_stmt_bind_param($teacher_stmt, "i", $selected_uni_id);
    mysqli_stmt_execute($teacher_stmt);
    $selected_teachers = mysqli_stmt_get_result($teacher_stmt);
}

/* CODEX CHANGE: If the logged-in user already reviewed the selected teacher,
   load that review into the homepage popup so the same form can update it. */
$popup_review = null;
if ($selected_uni_id > 0 && ctype_digit((string) $selected_teacher_id)) {
    $popup_teacher_id = (int) $selected_teacher_id;
    $popup_review_sql = "SELECT review_id, course_code, approach_rating, knowledge_rating, strict_level, time_man_rating, comments
                         FROM reviews
                         WHERE user_id = ? AND teacher_id = ?
                         LIMIT 1";
    $popup_review_stmt = mysqli_prepare($conn, $popup_review_sql);
    mysqli_stmt_bind_param($popup_review_stmt, "ii", $current_user_id, $popup_teacher_id);
    mysqli_stmt_execute($popup_review_stmt);
    $popup_review_result = mysqli_stmt_get_result($popup_review_stmt);
    $popup_review = mysqli_fetch_assoc($popup_review_result);
}

$reviews_sql = "
    SELECT
        reviews.review_id,
        reviews.user_id,
        reviews.teacher_id,
        reviews.course_code,
        reviews.approach_rating,
        reviews.knowledge_rating,
        reviews.strict_level,
        reviews.time_man_rating,
        reviews.comments,
        users.fname,
        users.lname,
        user_university.uni_name AS user_university,
        teachers.teacher_fname,
        teachers.teacher_lname,
        teacher_university.uni_name AS teacher_university
    FROM reviews
    JOIN users ON reviews.user_id = users.user_id
    JOIN universities AS user_university ON users.uni_id = user_university.uni_id
    JOIN teachers ON reviews.teacher_id = teachers.teacher_id
    JOIN universities AS teacher_university ON teachers.uni_id = teacher_university.uni_id
    ORDER BY reviews.review_id DESC
";

$reviews = mysqli_query($conn, $reviews_sql);

if (!$reviews) {
    die("Homepage query failed. Check that reviews has user_id, teacher_id, course_code, rating, and comments columns. " . mysqli_error($conn));
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LectSure Home</title>
    <link rel="stylesheet" href="css/main_css.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<!-- CODEX CHANGE: Feed layout added for homepage review posts. -->
<main class="homepage-shell">
    <section class="feed" aria-label="Homepage review feed">
        <?php if (mysqli_num_rows($reviews) === 0) { ?>
            <article class="empty-feed">
                <h1>No reviews yet</h1>
                <p>Click the plus button to add the first professor review.</p>
            </article>
        <?php } ?>

        <?php while ($review = mysqli_fetch_assoc($reviews)) {
            $review_id = (int) $review['review_id'];
            $owner_id = (int) $review['user_id'];
            $user_name = trim($review['fname'] . ' ' . $review['lname']);
            $teacher_name = trim($review['teacher_fname'] . ' ' . $review['teacher_lname']);
        ?>
            <article class="post-card">
                <header class="post-author">
                    <div class="avatar"><?php echo e(user_initials($user_name)); ?></div>
                    <div>
                        <h2><?php echo e($user_name); ?></h2>
                        <p><?php echo e($review['user_university']); ?></p>
                    </div>
                </header>

                <p class="post-text"><?php echo nl2br(e($review['comments'])); ?></p>

                <section class="professor-card">
                    <div class="professor-heading">
                        <div>
                            <span>Professor:</span>
                            <strong><?php echo e($teacher_name); ?></strong>
                            <small><?php echo e($review['teacher_university']); ?></small>
                        </div>
                        <div class="course-pills">
                            <?php render_course_pills($review['course_code']); ?>
                        </div>
                    </div>

                    <div class="rating-grid">
                        <div>
                            <span>Approachability</span>
                            <?php render_stars($review['approach_rating']); ?>
                        </div>
                        <div>
                            <span>Strictness</span>
                            <?php render_stars($review['strict_level']); ?>
                        </div>
                        <div>
                            <span>Knowledge</span>
                            <?php render_stars($review['knowledge_rating']); ?>
                        </div>
                        <div>
                            <span>Time Management</span>
                            <?php render_stars($review['time_man_rating']); ?>
                        </div>
                    </div>
                </section>
            </article>
        <?php } ?>
    </section>
</main>

<!-- CODEX CHANGE: CSS-only popup trigger. No JavaScript is used. -->
<a class="floating-review-button" href="#review-modal" aria-label="Create review">+</a>

<!-- CODEX CHANGE: Review popup changed to CSS :target and PHP form reloads.
     The onchange attributes use the same style already present in the original
     navbar.php, which the project allows. -->
<div class="review-overlay" id="review-modal">
    <a class="review-overlay-close" href="#" aria-label="Close review form"></a>
    <section class="review-modal" role="dialog" aria-modal="true" aria-labelledby="reviewModalTitle">
        <a class="modal-close" href="#" aria-label="Close review form">×</a>
        <h1 id="reviewModalTitle">Create Review</h1>

        <form action="homepage.php#review-modal" method="get" class="review-form review-filter-form">
            <div class="form-grid top-fields">
                <label>
                    <span>School</span>
                    <select name="review_uni_id" onchange="this.form.submit()" required>
                        <option value="">Select School</option>
                        <?php mysqli_data_seek($universities, 0); ?>
                        <?php while ($university = mysqli_fetch_assoc($universities)) { ?>
                            <option value="<?php echo (int) $university['uni_id']; ?>" <?php echo ((int) $university['uni_id'] === $selected_uni_id) ? 'selected' : ''; ?>>
                                <?php echo e($university['uni_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </label>

                <label>
                    <span>Teacher</span>
                    <select name="review_teacher_id" onchange="this.form.submit()" <?php echo $selected_uni_id <= 0 ? 'disabled' : ''; ?> required>
                        <option value="">Select teacher</option>
                        <?php if ($selected_teachers) { ?>
                            <?php while ($teacher = mysqli_fetch_assoc($selected_teachers)) {
                                $teacher_id = (string) $teacher['teacher_id'];
                                $teacher_name = trim($teacher['teacher_fname'] . ' ' . $teacher['teacher_lname']);
                            ?>
                                <option value="<?php echo e($teacher_id); ?>" <?php echo $selected_teacher_id === $teacher_id ? 'selected' : ''; ?>>
                                    <?php echo e($teacher_name); ?>
                                </option>
                            <?php } ?>
                            <option value="add" <?php echo $selected_teacher_id === 'add' ? 'selected' : ''; ?>>+ Add New</option>
                        <?php } ?>
                    </select>
                </label>
            </div>
        </form>

        <form action="create_review.php" method="post" class="review-form review-submit-form">
            <input type="hidden" name="uni_id" value="<?php echo $selected_uni_id; ?>">
            <input type="hidden" name="teacher_id" value="<?php echo e($selected_teacher_id); ?>">
            <input type="hidden" name="review_id" value="<?php echo (int) ($popup_review['review_id'] ?? 0); ?>">

            <label class="course-field">
                <span>Course Code</span>
                <input type="text" name="course_code" placeholder="e.g. CCS0001, GED0059" value="<?php echo e($popup_review['course_code'] ?? ''); ?>" required <?php echo $selected_teacher_id === '' ? 'disabled' : ''; ?>>
            </label>

            <?php if ($selected_teacher_id === 'add') { ?>
                <div class="form-grid new-teacher-fields">
                    <label>
                        <span>First Name</span>
                        <input type="text" name="teacher_fname" required>
                    </label>

                    <label>
                        <span>Last Name</span>
                        <input type="text" name="teacher_lname" required>
                    </label>
                </div>
            <?php } ?>

            <label class="comment-field">
                <span>Post</span>
                <textarea name="comments" placeholder="Tell us about it..." required <?php echo $selected_teacher_id === '' ? 'disabled' : ''; ?>><?php echo e($popup_review['comments'] ?? ''); ?></textarea>
            </label>

            <div class="modal-rating-grid">
                <label>
                    <span>Knowledge</span>
                    <?php render_rating_input('knowledge_rating', $popup_review['knowledge_rating'] ?? 0); ?>
                </label>

                <label>
                    <span>Approachability</span>
                    <?php render_rating_input('approach_rating', $popup_review['approach_rating'] ?? 0); ?>
                </label>

                <label>
                    <span>Strictness</span>
                    <?php render_rating_input('strict_level', $popup_review['strict_level'] ?? 0); ?>
                </label>

                <label>
                    <span>Time Management</span>
                    <?php render_rating_input('time_man_rating', $popup_review['time_man_rating'] ?? 0); ?>
                </label>
            </div>

            <button class="post-review-button" type="submit" <?php echo $selected_teacher_id === '' ? 'disabled' : ''; ?>>
                <?php echo $popup_review ? 'Update' : 'Post'; ?> <span>&gt;</span>
            </button>
        </form>
    </section>
</div>
</body>
</html>
