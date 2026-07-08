<!-- Page where users may create new reviews -->
<!-- Accessed through home/school/teacher/.php -->
<?php 

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'connection.php';

/* Handle Add New Teacher Form */
if (isset($_POST['add_teacher'])) {
    $uni_id = $_POST['uni_id'];
    $teacher_fname = $_POST['teacher_fname'];
    $teacher_lname = $_POST['teacher_lname'];

    $sql = "INSERT INTO teachers(teacher_fname, teacher_lname, uni_id)
            VALUES ('$teacher_fname', '$teacher_lname', '$uni_id')";

    $teacher_result = mysqli_query($conn, $sql);

    if ($teacher_result) {
        $teacher_id = mysqli_insert_id($conn);
        header("Location: teacher.php?teacher_id=$teacher_id");
        exit();
    } else {
        die("Error adding teacher: " . mysqli_error($conn));
    }
}

/* Handle Review Form */
if (isset($_POST['signup2'])) {
    $course_code = $_POST['course_code'];
    $approach_rating = $_POST['approach_rating'];
    $knowledge_rating = $_POST['knowledge_rating'];
    $strict_level = $_POST['strict_level'];
    $time_man_rating = $_POST['time_man_rating'];
    $comments = $_POST['comments'];
    $teacher_id = $_POST['teacher_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO reviews(user_id, teacher_id, course_code, approach_rating, knowledge_rating, strict_level, time_man_rating, comments)
            VALUES('$user_id', '$teacher_id', '$course_code', '$approach_rating', '$knowledge_rating', '$strict_level', '$time_man_rating', '$comments')";

    $review_result = mysqli_query($conn, $sql);
    
    if ($review_result) {
        header("Location: teacher.php?teacher_id=$teacher_id&review=success");
        exit();
    } else {
        die("Error adding review: " . mysqli_error($conn));
    }
}

$universities = mysqli_query($conn, "SELECT uni_id, uni_name FROM universities");
$teachers = mysqli_query($conn, "SELECT teacher_id, teacher_fname, teacher_lname, uni_id FROM teachers");

$choice = $_POST['choice'] ?? ($_GET['teacher_id'] ?? "");

?>

<style>
.popup-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
}

.popup-box {
    background: white;
    width: 350px;
    margin: 100px auto;
    padding: 20px;
    border-radius: 8px;
}

.close-btn {
    float: right;
}
</style>

<!-- Form Existing Or New Professor -->
<form method="POST">
    <select name="choice" onchange="handleChoice(this)">
        <option value="">-- Select Existing Teachers --</option>

        <?php
        while ($row = mysqli_fetch_assoc($teachers)) {
            echo "<option value='{$row['teacher_id']}'>";
            echo htmlspecialchars($row['teacher_fname'] . " " . $row['teacher_lname']);
            echo "</option>";
        }
        ?>

        <option value="add">-- Add New Teacher --</option>
    </select><br><br>
</form>

<!-- Add New Teacher Popup -->
<div id="teacherPopup" class="popup-overlay">
    <div class="popup-box">
        <button type="button" class="close-btn" onclick="closePopup()">X</button>

        <h5>Add Teacher Form</h5>

        <form method="POST">
            <input type="hidden" name="choice" value="add">

            <input type="text" name="teacher_fname" placeholder="Given name" required><br>
            <input type="text" name="teacher_lname" placeholder="Surname" required><br>

            <select name="uni_id" required>
                <option value="">-- Select University --</option>

                <?php
                mysqli_data_seek($universities, 0);

                while ($row = mysqli_fetch_assoc($universities)) {
                    echo "<option value='{$row['uni_id']}'>";
                    echo htmlspecialchars($row['uni_name']);
                    echo "</option>";
                }
                ?>
            </select><br><br>

            <input type="submit" value="Submit" name="add_teacher">
        </form>
    </div>
</div>

<!-- Review Form -->
<?php if (filter_var($choice, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) !== false) { ?>

    <form method="POST">
        <h5>Review Form</h5>

        <?php 
        $teacher_id = $choice;
        $t_id = mysqli_query($conn, "SELECT teacher_id, teacher_fname, teacher_lname, uni_id FROM teachers WHERE teacher_id='$teacher_id'");
        $fakerow = mysqli_fetch_assoc($t_id);
        ?>

        Teacher: <?php echo htmlspecialchars($fakerow['teacher_fname'] . " " . $fakerow['teacher_lname']); ?>
        <br>

        Course Code:
        <input type="text" name="course_code" placeholder="Course Code" required><br>

        <label for="approach_rating">Approachable Rating:</label>
        <select id="approach_rating" name="approach_rating" required>
            <option value="">Select a rating</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <br><br>

        <label for="knowledge_rating">Knowledgeable Rating:</label>
        <select id="knowledge_rating" name="knowledge_rating" required>
            <option value="">Select a rating</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <br><br>

        <label for="strict_level">Strict Level Rating:</label>
        <select id="strict_level" name="strict_level" required>
            <option value="">Select a rating</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <br><br>

        <label for="time_man_rating">Time Management Rating:</label>
        <select id="time_man_rating" name="time_man_rating" required>
            <option value="">Select a rating</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <br><br>

        Comments:
        <input type="text" name="comments" placeholder="Optional."><br>

        <input type="hidden" name="teacher_id" value="<?php echo htmlspecialchars($teacher_id); ?>">

        <input type="submit" value="Submit Review" name="signup2">
    </form>

<?php } ?>

<script>
function openPopup() {
    document.getElementById("teacherPopup").style.display = "block";
}

function closePopup() {
    document.getElementById("teacherPopup").style.display = "none";
}

function handleChoice(select) {
    if (select.value === "add") {
        openPopup();
    } else if (select.value !== "") {
        select.form.submit();
    }
}
</script>