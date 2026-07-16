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

$teacher_id = $_GET['teacher_id'];

if (isset($_GET['review']) && $_GET['review'] == 'success') {
    echo "Review Successfully Added.";
}

include 'navbar.php'; 

//Teacher Information
$teacher_sql = "SELECT teachers.*, universities.uni_name
                FROM teachers
                JOIN universities ON teachers.uni_id = universities.uni_id
                WHERE teachers.teacher_id = '$teacher_id'";

$teacher_result = mysqli_query($conn, $teacher_sql);
$teacher = mysqli_fetch_assoc($teacher_result);

if (!$teacher) {
    header("Location: homepage.php");
    exit();
}

//Average Ratings
$average_sql = "SELECT 
                    COUNT(review_id) AS total_reviews,
                    AVG(approach_rating) AS avg_approach,
                    AVG(knowledge_rating) AS avg_knowledge,
                    AVG(strict_level) AS avg_strict,
                    AVG(time_man_rating) AS avg_time
                FROM reviews
                WHERE teacher_id = '$teacher_id'";

$average_result = mysqli_query($conn, $average_sql);
$average = mysqli_fetch_assoc($average_result);

// Individual Reviews
$reviews_sql = "SELECT reviews.*, users.fname, users.lname
                FROM reviews
                JOIN users ON reviews.user_id = users.user_id
                WHERE reviews.teacher_id = '$teacher_id'
                ORDER BY reviews.created_at DESC";

$reviews_result = mysqli_query($conn, $reviews_sql);
?>

<h1>
    <?php echo htmlspecialchars($teacher['teacher_fname'] . " " . $teacher['teacher_lname']); ?>
</h1>

<p>
    University: <?php echo htmlspecialchars($teacher['uni_name']); ?>
</p>

<h2>Review Summary</h2>

<p>
    Total Reviews: <?php echo htmlspecialchars($average['total_reviews']); ?>
</p>

<?php if ($average['total_reviews'] > 0) { ?>
    <p>Average Approachability: <?php echo round($average['avg_approach'], 2); ?>/5</p>
    <p>Average Knowledge: <?php echo round($average['avg_knowledge'], 2); ?>/5</p>
    <p>Average Strictness: <?php echo round($average['avg_strict'], 2); ?>/5</p>
    <p>Average Time Management: <?php echo round($average['avg_time'], 2); ?>/5</p>
<?php } else { ?>
    <p>No ratings yet.</p>
<?php } ?>

<h2>User Reviews</h2>

<?php if (mysqli_num_rows($reviews_result) > 0) { ?>

    <?php while ($review = mysqli_fetch_assoc($reviews_result)) { ?>
        <div>
            <h3>
                <?php echo htmlspecialchars($review['fname'] . " " . $review['lname']); ?>
            </h3>

            <p>Course Code: <?php echo htmlspecialchars($review['course_code']); ?></p>
            <p>Approachability: <?php echo htmlspecialchars($review['approach_rating']); ?>/5</p>
            <p>Knowledge: <?php echo htmlspecialchars($review['knowledge_rating']); ?>/5</p>
            <p>Strictness: <?php echo htmlspecialchars($review['strict_level']); ?>/5</p>
            <p>Time Management: <?php echo htmlspecialchars($review['time_man_rating']); ?>/5</p>
            <p>Comments: <?php echo htmlspecialchars($review['comments']); ?></p>

            <hr>
        </div>
    <?php } ?>

<?php } else { ?>
    <p>No reviews yet.</p>
<?php } ?>
