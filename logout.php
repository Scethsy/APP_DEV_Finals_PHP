<!-- Page when users logout -->
<!-- Accessed through homepage.php -->

<?php 

    session_start();
    session_destroy();

?>
<!-- CODEX CHANGE: Designed page wrapper added to match the Figma /logout screen.
     Original session_start() and session_destroy() behavior above is preserved. -->
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logged Out | LectSure</title>
    <link rel="stylesheet" href="css/main_css.css">
</head>
<body class="account-page">
    <main class="account-canvas logout-canvas">
        <section class="logout-content">
            <h1 class="account-logo">LectSure</h1>
            <p>You have successfully logged out!</p>
            <a class="logout-login-button" href="login.php">Login</a>
        </section>
    </main>
</body>
</html>
