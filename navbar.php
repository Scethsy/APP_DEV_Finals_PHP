<?php
include 'connection.php';
$universities = mysqli_query($conn, "SELECT uni_id, uni_name FROM universities");
?>

<nav>
    <a href="homepage.php">Home</a>

    <form method="get" action="school.php" style="display:inline;">
        <select name="uni_id" onchange="this.form.submit()">
            <option value="">University</option>

            <?php while ($row = mysqli_fetch_assoc($universities)) { ?>
                <option value="<?php echo $row['uni_id']; ?>">
                    <?php echo htmlspecialchars($row['uni_name']); ?>
                </option>
            <?php } ?>
        </select>
    </form>

    <a href="profile.php">Profile</a>

<a href="review.php">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
</svg>
</a>
</nav>

