<!-- Page when users logout -->
<!-- Accessed through homepage.php -->

<?php 

    session_start();
    session_destroy();

?>

You have successfully logged out! Thank you for using LectSure! Please click here to <a href="login.php"> login </a>.