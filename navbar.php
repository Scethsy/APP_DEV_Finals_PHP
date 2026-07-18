<?php
include 'connection.php';
$universities = mysqli_query($conn, "SELECT uni_id, uni_name FROM universities");
?>

<nav class="main-nav">
    <a class="nav-brand" href="homepage.php">LectSure</a>
    <a class="nav-link" href="homepage.php">Home</a>

    <form class="nav-school-form" method="get" action="school.php">
        <select name="uni_id" onchange="this.form.submit()" aria-label="Select university">
            <option value="">Universities</option>

            <?php while ($row = mysqli_fetch_assoc($universities)) { ?>
                <option value="<?php echo $row['uni_id']; ?>">
                    <?php echo htmlspecialchars($row['uni_name']); ?>
                </option>
            <?php } ?>
        </select>
    </form>

    <a class="nav-link" href="profile.php">Profile</a>
</nav>
