/* Page where users may edit their profiles. 
It also includes a delete-account handler that removes the user's reviews and account.
Accessed through profile.php */

<?php
//To remember the User_ID Through Session
session_start();
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$edit_error = "";

/* Delete-account handler. It removes reviews owned by the
   current user before deleting the user account, then clears the session. */
if (isset($_POST['delete_account'])) {
    $delete_user_sql = "DELETE FROM users WHERE user_id = ?";
    $delete_user_stmt = mysqli_prepare($conn, $delete_user_sql);
    mysqli_stmt_bind_param($delete_user_stmt, "i", $user_id);

    if (mysqli_stmt_execute($delete_user_stmt)) {
        session_destroy();
        header("Location: account_deleted.php");
        exit();
    }

    $edit_error = "Error while deleting account.";
}

if (isset($_POST['signup'])) {
    $fname = ucwords(strtolower(trim($_POST['fname'])));
    $lname = ucwords(strtolower(trim($_POST['lname'])));
    $bday = $_POST['bday'];
    $email = strtolower(trim($_POST['email']));
    $pass = trim($_POST['pass']);
    $pass2 = trim($_POST['pass2']);
    $uni_id = $_POST['uni_id'];

    if ($pass !="" || $pass2 != "") {
        if ($pass == $pass2){

            $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

            $sql = "UPDATE users
                    SET fname='$fname', lname='$lname', email='$email', bday='$bday', password='$hashedPassword', uni_id='$uni_id'
                    WHERE user_id = '$user_id'";

            $result = mysqli_query($conn, $sql);
            
            if ($result) {
                    header("Location: profile.php?updated=success");
                    exit();
            }else {
                $edit_error = "Error while updating profile.";
            }
        } else{
            $edit_error = "Password do not match!";
        } 
    }else {
            $sql = "UPDATE users 
                    SET fname='$fname', lname='$lname', email='$email', bday='$bday', uni_id='$uni_id'
                    WHERE user_id='$user_id'";
            
            $result = mysqli_query($conn, $sql);
            if ($result) {
                    header("Location: profile.php?updated=success");
                    exit();
            }
        }

}

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function edit_profile_initials($name) {
    $parts = preg_split('/\s+/', trim($name));
    $initials = '';
    foreach ($parts as $part) {
        if ($part !== '') {
            $initials .= strtoupper($part[0]);
        }
        if (strlen($initials) >= 2) {
            break;
        }
    }
    return $initials ?: 'LS';
}

/* User query includes university name for the profile-edit
   header while preserving the existing editable fields. */
$sql = "SELECT users.fname, users.lname, users.email, users.uni_id, users.bday, universities.uni_name
        FROM users
        JOIN universities ON users.uni_id = universities.uni_id
        WHERE users.user_id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
$full_name = trim($user['fname'] . ' ' . $user['lname']);
$profile_universities = mysqli_query($conn, "SELECT uni_id, uni_name FROM universities ORDER BY uni_name");
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Profile | LectSure</title>
    <link rel="stylesheet" href="css/main_css.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<main class="edit-profile-shell">
    <section class="edit-profile-hero">
        <div class="profile-avatar edit-profile-avatar"><?php echo e(edit_profile_initials($full_name)); ?></div>
        <div>
            <h1><?php echo e($full_name); ?></h1>
            <p><?php echo e($user['uni_name']); ?></p>
        </div>
        <form method="post" class="delete-account-form">
            <button type="submit" name="delete_account" class="delete-account-button">Delete Account</button>
        </form>
    </section>

    <?php if ($edit_error !== "") { ?>
        <p class="profile-message profile-error"><?php echo e($edit_error); ?></p>
    <?php } ?>

    <form method="post" action="" class="edit-profile-form">
        <label>
            <span>First Name</span>
            <input type="text" name="fname" value="<?php echo e($user['fname']); ?>" required>
        </label>

        <label>
            <span>Surname</span>
            <input type="text" name="lname" value="<?php echo e($user['lname']); ?>" required>
        </label>

        <label>
            <span>Birthday</span>
            <input type="date" name="bday" value="<?php echo e($user['bday']); ?>" required>
        </label>

        <label>
            <span>Email</span>
            <input type="email" name="email" value="<?php echo e($user['email']); ?>" required>
        </label>

        <label>
            <span>School</span>
            <select name="uni_id" required>
                <option value="">Select University</option>
                <?php while ($row = mysqli_fetch_assoc($profile_universities)) {
                    $selected = ($row['uni_id'] == $user['uni_id']) ? "selected" : "";
                    echo "<option value='" . $row['uni_id'] . "' $selected>";
                    echo htmlspecialchars($row['uni_name']);
                    echo "</option>";
                } ?>
            </select>
        </label>

        <div class="edit-profile-passwords">
            <label>
                <span>New Password</span>
                <input type="password" name="pass" placeholder="Optional">
            </label>
            <label>
                <span>Confirm Password</span>
                <input type="password" name="pass2" placeholder="Confirm password">
            </label>
        </div>

        <input type="submit" value="Save Profile" name="signup" class="profile-edit-button save-profile-button">
    </form>
</main>
</body>
</html>
