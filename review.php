<!-- Page where users may create new reviews -->
<!-- Accessed through home/school/teacher/.php -->
<?php 
include 'connection.php';

    $universities = mysqli_query($conn, "SELECT uni_id, uni_name FROM universities");
    $teachers = mysqli_query($conn, "SELECT teacher_id, teacher_fname, teacher_lname, uni_id FROM teachers")

?>

<form method = "post" action = "teacher.php">
    <!-- Name -->
    <select name="teacher_id">
        <option value="">-- Select Existing Teachers --</option>

        <?php
        while ($row = mysqli_fetch_assoc($teachers)) {
            echo "<option value='{$row['teacher_id']}'>";
            echo htmlspecialchars($row['teacher_fname'] . " " . $row['teacher_lname']);
            echo "</option>";
        }
        ?>

    </select><br><br>

    Add New Teacher
    <input type="text" name="teacher_fname" placeholder="Given name"><br>
    <input type="text" name="teacher_lname" placeholder="Surname"><br>

    <!-- Course Code -->
    <input type="text" name="course_code" placeholder="Course Code" required><br>

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

    </select><br><br>

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

    <input type="submit" value="Sign Up" name="signup">
</form>

<?php
if (isset($_POST['signup'])) {
    $teacher_id = $_POST['teacher_id'];
    $teacher_fname = $_POST['teacher_fname'];
    $teacher_lname = $_POST['teacher_lname'];
    $course_code = $_POST['course_code'];
    $approach_rating = $_POST['approach_rating'];
    $knowledge_rating = $_POST['knowledge_rating'];
    $strict_level = $_POST['strict_level'];
    $time_man_rating = $_POST['time_man_rating'];
    $comments = $_POST['comments'];
    if (trim($teacher_fname === "") || trim($teacher_lname === "")) { //Existing Teacher
        $sql = "INSERT INTO reviews()
                VALUES('',)";
    } else if ($teacher_id == "") { //New Teacher
        $sql = "INSERT INTO teachers(teacher_fname, teachers_lname, course_code, approach_rating, knowledge_rating, strict_level, time_man_rating, )
                VALUES('$teacher_fname', '$teacher_lname', '$course_code', '$approach_rating', '$knowledge_rating', '$strict_level', '$time_man_rating')";
    } else if (!(trim($teacher_fname === "") || trim($teacher_lname === "")) && !($teacher_id == "")) { //If User Did Both

    } else if ((trim($teacher_fname === "") || trim($teacher_lname === "")) && $teacher_id == "") { //If User Did Neither

    } 

    
    // $result = mysqli_query($conn, $sql);
    
    // if ($result){
    //     echo "Review for Professor $fname $lname has been added successfully.";
    // } else {
    //     echo "Error while submitting review. Please try again.";
    // }

}
?>