<!-- Page where users may edit their previous reviews -->
<!-- Accessed through profile.php -->

<?php
    //To remember the User_ID Through Session
    session_start();
    include 'connection.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $review_id = $_GET['review_id'];
    
    if (isset($_POST['update_review'])) {
        $course_code = mysqli_real_escape_string($conn, $_POST['course_code']);
        $approach_rating = $_POST['approach_rating'];
        $knowledge_rating = $_POST['knowledge_rating'];
        $strict_level = $_POST['strict_level'];
        $time_man_rating = $_POST['time_man_rating'];
        $comments = mysqli_real_escape_string($conn, $_POST['comments']);

        $sql = "UPDATE reviews 
                SET course_code = '$course_code',
                    approach_rating = '$approach_rating',
                    knowledge_rating = '$knowledge_rating',
                    strict_level = '$strict_level',
                    time_man_rating = '$time_man_rating',
                    comments = '$comments'
                WHERE review_id = '$review_id' AND user_id = '$user_id'";

        $update_result = mysqli_query($conn, $sql);

        if ($update_result) {
            header("Location: profile.php?review_updated=success");
            exit();
        } else {
            echo "Error updating review: " . mysqli_error($conn);
        }
    }

    $sql = "SELECT review_id, teacher_id, course_code, approach_rating, knowledge_rating, strict_level, time_man_rating, comments FROM reviews WHERE review_id = '$review_id' AND user_id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    $reviews = mysqli_fetch_assoc($result);
?> 
<!-- Review Form -->
<form method = "post">
        <h5> Review Form </h5>
        <!-- Course Code -->
        Course Code:
        <input type="text" name="course_code" placeholder="Course Code" value = "<?php echo htmlspecialchars($reviews['course_code']); ?>" required><br>

        <!-- Rating --> 
        <!-- Approachable -->
        <label>Approachable Rating:</label>
        <select name="approach_rating" required>
            <option value="">Select a rating</option>
            <?php for ($i = 1; $i <= 5; $i++) { ?>
                <option value="<?php echo $i; ?>" <?php if ($reviews['approach_rating'] == $i) echo "selected"; ?>>
                    <?php echo $i; ?>
                </option>
            <?php } ?>
        </select>
        <br><br>

        <!-- Knowledgeable -->
        <label for="rating">Knowledgeable Rating:</label>
        <select id="rating" name="knowledge_rating" required>
            <option value="">Select a rating</option>
            <?php for ($i = 1; $i <= 5; $i++) { ?>
                <option value="<?php echo $i; ?>" <?php if ($reviews['knowledge_rating'] == $i) echo "selected"; ?>>
                    <?php echo $i; ?>
                </option>
            <?php } ?>
        </select>
        <br><br>

        <!-- Strict Level -->
        <label for="rating">Strict Level Rating:</label>
        <select id="rating" name="strict_level" required>
            <option value="">Select a rating</option>
            <?php for ($i = 1; $i <= 5; $i++) { ?>
                <option value="<?php echo $i; ?>" <?php if ($reviews['strict_level'] == $i) echo "selected"; ?>>
                    <?php echo $i; ?>
                </option>
            <?php } ?>
        </select>
        <br><br>

        <!-- Time Management -->
        <label for="rating">Time Management Rating:</label>
        <select id="rating" name="time_man_rating" required>
            <option value="">Select a rating</option>
            <?php for ($i = 1; $i <= 5; $i++) { ?>
                <option value="<?php echo $i; ?>" <?php if ($reviews['time_man_rating'] == $i) echo "selected"; ?>>
                    <?php echo $i; ?>
                </option>
            <?php } ?>
        </select>
        <br><br>

        <!-- Comments -->
        Comments:<input type="text" name="comments" placeholder="Optional." value="<?php echo htmlspecialchars($reviews['comments']); ?>"><br>

        <input type="hidden" name="review_id" value="<?php echo $review_id; ?>">

        <input type="submit" value="Submit Review" name="update_review">
    </form>