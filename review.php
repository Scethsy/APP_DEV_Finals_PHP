<!-- Page where users may create new reviews -->
<!-- Accessed through home/school/teacher/.php -->
<?php 

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'connection.php';

    $universities = mysqli_query($conn, "SELECT uni_id, uni_name FROM universities");
    $teachers = mysqli_query($conn, "SELECT teacher_id, teacher_fname, teacher_lname, uni_id FROM teachers");

?>
<!-- Form Existing Or New Professor -->
<form method = "POST">
    <select name="teacher_id" onchange="this.form.submit()">
        <option value="">-- Select Existing Teachers --</option>

        <?php
        while ($row = mysqli_fetch_assoc($teachers)) {
            echo "<option value='{$row['teacher_id']}'>";
            echo htmlspecialchars($row['teacher_fname'] . " " . $row['teacher_lname']);
            echo "</option>";
        }
        ?>

        <option value="add">-- Add New Teacher -- </option>
    </select><br><br>
</form>
<?php
$choice = $_POST['teacher_id'] ?? "";
if ($choice == "add") { //Add New Teacher
?>
<!-- Form For New Professor -->
    <form method = "POST">
        <!-- Name -->
        <h5> Add Teacher Form</h5>
        Add New Teacher <br>
        <input type="text" name="teacher_fname" placeholder="Given name"><br>
        <input type="text" name="teacher_lname" placeholder="Surname"><br>

        <!-- University Dropdown -->
        <select name="uni_id" required>
            <option value="">-- Select University --</option>

            <?php
            while ($row = mysqli_fetch_assoc($universities)) {
                echo "<option value='{$row['uni_id']}'>";
                echo htmlspecialchars($row['uni_name']);
                echo "</option>";
            }
            ?>

        </select> <br><br>

        <input type="submit" value="Submit" name="signup">
    </form>
    <?php
    if (isset($_POST['signup'])) {
        $uni_id = $_POST['uni_id'];
        $teacher_fname = $_POST['teacher_fname'];
        $teacher_lname = $_POST['teacher_lname'];

        $sql = "INSERT INTO teachers(teacher_fname, teacher_lname, uni_id)
                VALUES ('$teacher_fname', '$teacher_lname', '$uni_id')";

        $teacher_result = mysqli_query($conn, $sql);
    
        $teacher_result = mysqli_query($conn, $teacher_sql);

        if ($teacher_result) {
            $teacher_id = mysqli_insert_id($conn);
        } else {
            die("Error adding teacher: " . mysqli_error($conn));
        }
    }

}?>
<!-- Review Form -->
<?php if (isset($_POST['teacher_id'])) {?>

    <form method = "post">
        <h5> Review Form </h5>
        <!-- Course Code -->
        Course Code:
        <input type="text" name="course_code" placeholder="Course Code" required><br>

        <!-- Rating --> 
        <!-- Approachable -->
        <label for="rating">Approachable Rating:</label>
        <select id="rating" name="approach_rating" required>
            <option value="">Select a rating</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <br><br>

        <!-- Knowledgeable -->
        <label for="rating">Knowledgeable Rating:</label>
        <select id="rating" name="knowledge_rating" required>
            <option value="">Select a rating</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <br><br>

        <!-- Strict Level -->
        <label for="rating">Strict Level Rating:</label>
        <select id="rating" name="strict_level" required>
            <option value="">Select a rating</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <br><br>

        <!-- Time Management -->
        <label for="rating">Time Management Rating:</label>
        <select id="rating" name="time_man_rating" required>
            <option value="">Select a rating</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <br><br>

        <!-- Comments -->
        Comments:<input type="text" name="comments" placeholder="Optional."><br>

        <input type="submit" value="Submit Review" name="signup">
    </form>
<?php
}
if (isset($_POST['signup'])) {
    $course_code = $_POST['course_code'];
    $approach_rating = $_POST['approach_rating'];
    $knowledge_rating = $_POST['knowledge_rating'];
    $strict_level = $_POST['strict_level'];
    $time_man_rating = $_POST['time_man_rating'];
    $comments = $_POST['comments'];

    $sql = "INSERT INTO reviews(course_code, approach_rating, knowledge_rating, strict_level, time_man_rating, comments)
            VALUES('$course_code', '$approach_rating', '$knowledge_rating', '$strict_level', '$time_man_rating', '$comments')";

    $review_result = mysqli_query($conn, $sql);
    
    if ($review_result) {
        header("Location: teacher.php?review=success");
    } else {
        die("Error adding teacher: " . mysqli_error($conn));
    }

}
?>