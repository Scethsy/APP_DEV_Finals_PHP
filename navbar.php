<?php
include 'connection.php';
$universities = mysqli_query($conn, "SELECT uni_id, uni_name FROM universities");
?>

<nav>
    <a href="profile.php">Profile</a>
<?php
include 'connection.php';
$universities = mysqli_query($conn, "SELECT uni_id, uni_name FROM universities");
?>

<nav>
    <a href="profile.php">Profile</a>

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

    <a href="logout.php">Logout</a>
</nav>

    </a>

    <a href="logout.php">Logout</a>
</nav>
