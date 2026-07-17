<!-- Page where users may view their profile -->
<!-- Accessed through home.php -->

<?php
    session_start();
    include 'connection.php';

    if(!isset($_SESSION['user'])){
        header("location: login.php");
        exit();
    }

    $profile_message = "";
    if (isset($_GET['updated']) && $_GET['updated'] == 'success') {
        $profile_message = "Profile updated successfully!";
    }

    $user_id = (int) $_SESSION['user_id'];

    function e($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /* CODEX CHANGE: Helper functions added for profile avatar, course pills,
       and star display. They mirror homepage behavior for the user's own posts. */
    function profile_initials($name) {
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

    function profile_stars($rating) {
        $rating = max(0, min(5, (int) $rating));
        echo '<div class="stars" aria-label="' . $rating . ' out of 5 stars">';
        for ($i = 1; $i <= 5; $i++) {
            $filled = $i <= $rating ? ' is-filled' : '';
            echo '<span class="star' . $filled . '">&#9733;</span>';
        }
        echo '</div>';
    }

    function profile_course_pills($course_code) {
        $codes = array_filter(array_map('trim', explode(',', (string) $course_code)));
        foreach ($codes as $code) {
            echo '<span class="course-pill">' . e(strtoupper($code)) . '</span>';
        }
    }

    $sql = "SELECT users.*, universities.uni_name
            FROM users
            JOIN universities ON users.uni_id = universities.uni_id
            WHERE users.user_id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    $numrows = mysqli_num_rows($result);

    if($numrows==1){
        $row = mysqli_fetch_array($result);
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['user'] = $row['fname'] . " " . $row['lname'];
        $_SESSION['bday'] = $row['bday'];
        $_SESSION['uni_id'] = $row['uni_id'];
        $_SESSION['uni_name'] = $row['uni_name'];
        $user = $row;
    }

    /* CODEX CHANGE: Query added to show only the current user's posts on the
       profile page, with professor and university data joined for display. */
    $reviews_sql = "
        SELECT
            reviews.review_id,
            reviews.user_id,
            reviews.teacher_id,
            reviews.course_code,
            reviews.approach_rating,
            reviews.knowledge_rating,
            reviews.lenient_level,
            reviews.time_man_rating,
            reviews.comments,
            teachers.teacher_fname,
            teachers.teacher_lname,
            teacher_university.uni_name AS teacher_university
        FROM reviews
        JOIN teachers ON reviews.teacher_id = teachers.teacher_id
        JOIN universities AS teacher_university ON teachers.uni_id = teacher_university.uni_id
        WHERE reviews.user_id = '$user_id'
        ORDER BY reviews.review_id DESC
    ";
    $reviews = mysqli_query($conn, $reviews_sql);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile | LectSure</title>
    <link rel="stylesheet" href="css/main_css.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<!-- CODEX CHANGE: Figma-inspired profile layout added. -->
<main class="profile-shell">
    <aside class="profile-sidebar">
        <a class="profile-logout" href="logout.php">Logout</a>
        <div class="profile-avatar"><?php echo e(profile_initials($_SESSION['user'])); ?></div>
        <h1><?php echo e($_SESSION['user']); ?></h1>
        <p><?php echo e($_SESSION['uni_name']); ?></p>
        <a class="profile-edit-button" href="edit_profile.php">Edit Profile</a>
        <?php if ($profile_message !== "") { ?>
            <p class="profile-message"><?php echo e($profile_message); ?></p>
        <?php } ?>
    </aside>

    <section class="profile-feed" aria-label="Your reviews">
        <?php if (!$reviews || mysqli_num_rows($reviews) === 0) { ?>
            <article class="empty-feed">
                <h1>No posts yet</h1>
                <p>Your reviews will appear here after you create them.</p>
            </article>
        <?php } ?>

        <?php while ($review = mysqli_fetch_assoc($reviews)) {
            $review_id = (int) $review['review_id'];
            $teacher_name = trim($review['teacher_fname'] . ' ' . $review['teacher_lname']);
        ?>
            <article class="post-card profile-post-card">
                <a class="profile-post-menu-trigger" href="#profile-post-menu-<?php echo $review_id; ?>" aria-label="Open post actions">...</a>
                <div class="profile-post-menu" id="profile-post-menu-<?php echo $review_id; ?>">
                    <a href="edit_review.php?review_id=<?php echo $review_id; ?>">Edit Post</a>
                    <form action="delete_review.php" method="post">
                        <input type="hidden" name="review_id" value="<?php echo $review_id; ?>">
                        <button type="submit">Delete Post</button>
                    </form>
                </div>

                <header class="post-author">
                    <div class="avatar"><?php echo e(profile_initials($_SESSION['user'])); ?></div>
                    <div>
                        <h2><?php echo e($_SESSION['user']); ?></h2>
                        <p><?php echo e($_SESSION['uni_name']); ?></p>
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
                            <?php profile_course_pills($review['course_code']); ?>
                        </div>
                    </div>

                    <div class="rating-grid">
                        <div>
                            <span>Approachability</span>
                            <?php profile_stars($review['approach_rating']); ?>
                        </div>
                        <div>
                            <span>Leniency</span>
                            <?php profile_stars($review['lenient_level']); ?>
                        </div>
                        <div>
                            <span>Knowledge</span>
                            <?php profile_stars($review['knowledge_rating']); ?>
                        </div>
                        <div>
                            <span>Time Management</span>
                            <?php profile_stars($review['time_man_rating']); ?>
                        </div>
                    </div>
                </section>
            </article>
        <?php } ?>
    </section>
</main>
</body>
</html>
