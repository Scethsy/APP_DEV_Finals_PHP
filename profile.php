<!-- Page where users may view their profile -->
<!-- Accessed through home.php -->

<?php 
    session_start();
    include 'connection.php';

    if(!isset($_SESSION['user_id'])){
        header("location: login.php");
        exit();
    }

    if (isset($_GET['delete']) && $_GET['delete'] == 'success') {
        echo "Review deleted successfully!";
    }

    if (isset($_GET['review_updated']) && $_GET['review_updated'] == 'success') {
        echo "Review updated successfully!";
    }
    if (isset($_GET['updated']) && $_GET['updated'] == 'success') {
        echo "Profile updated successfully!";
    }
    
    $user_id = $_SESSION['user_id'];

    if (isset($_POST['delete_review_confirm'])) {
    $review_id = $_POST['review_id'];
    $user_id = $_SESSION['user_id'];

    $delete_sql = "DELETE FROM reviews 
                   WHERE review_id = '$review_id' AND user_id = '$user_id'";

    $delete_result = mysqli_query($conn, $delete_sql);

    if ($delete_result) {
        header("Location: profile.php?delete=success");
        exit();
    } else {
        echo "Error deleting review.";
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
    }
    
    $reviews_sql = "SELECT reviews.*, teachers.teacher_fname, teachers.teacher_lname
                FROM reviews
                JOIN teachers ON reviews.teacher_id = teachers.teacher_id
                WHERE reviews.user_id = '$user_id'
                ORDER BY reviews.created_at DESC";

    $reviews_result = mysqli_query($conn, $reviews_sql);

    include 'navbar.php';
?>

<h1>Welcome <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
Name: <?php echo htmlspecialchars($_SESSION['user']); ?><br>
Birthday: <?php echo htmlspecialchars($_SESSION['bday']); ?><br>
University: <?php echo htmlspecialchars($_SESSION['uni_name']); ?><br>


-------------------------------------------------------------------------

<h2>Your Reviews</h2>

<?php if (mysqli_num_rows($reviews_result) > 0) { ?>

    <?php while ($review = mysqli_fetch_assoc($reviews_result)) { ?>
        <div>
            <h3>
                <?php echo htmlspecialchars($review['teacher_fname'] . " " . $review['teacher_lname']); ?>
            </h3>

            <p>Course Code: <?php echo htmlspecialchars($review['course_code']); ?></p>
            <p>Approachability: <?php echo htmlspecialchars($review['approach_rating']); ?>/5</p>
            <p>Knowledge: <?php echo htmlspecialchars($review['knowledge_rating']); ?>/5</p>
            <p>Strictness: <?php echo htmlspecialchars($review['strict_level']); ?>/5</p>
            <p>Time Management: <?php echo htmlspecialchars($review['time_man_rating']); ?>/5</p>
            <p>Comments: <?php echo htmlspecialchars($review['comments']); ?></p>
            <p> <a href="edit_review.php?review_id=<?php echo $review['review_id']; ?>"> Edit Review </a> </p> 
            <form method="post">
                <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                <input type="submit" name="delete_review" value="Delete">
            </form>

            <?php if (isset($_POST['delete_review'])) {?> 
            <form method = "post">
                <input type="hidden" name="review_id" value="<?php echo $review['review_id']; ?>">
                <input type="submit" name="delete_review_confirm" value="Are you sure?">
            </form>

            <?php } ?>
            <hr>
        </div>
    <?php } ?>

<?php } else { ?>
    <p>No reviews yet.</p>
<?php } ?>

<a href="edit_profile.php">Edit Profile </a><br>
<a href="logout.php">logout</a>