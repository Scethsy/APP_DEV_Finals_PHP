<!-- Page where users may create new reviews -->
<!-- Accessed through home/school/teacher/.php -->
<?php 
include 'connection.php';

    $universities = mysqli_query($conn, "SELECT uni_id, uni_name FROM universities");

?>

<form method= "post">
    <!-- Name -->
    <input type="text" name="fname" placeholder="Given name" required><br>
    <input type="text" name="lname" placeholder="Surname" required><br>
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
    <select id="rating" name="rating" required>
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
    <select id="rating" name="rating" required>
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
    <select id="rating" name="rating" required>
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
    <select id="rating" name="rating" required>
        <option value="">Select a rating</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
    </select>
    <br><br>

    <!-- Comment -->
    Comment:<input type="text" name="comment" placeholder="Optional."><br>

    <input type="submit" value="Sign Up" name="signup">
</form>