<?php
    $errors = array("Login failed: Invalid username or password.",
                    "Logged out: Session timed out",
                    "You must enter a username and password.");
?><!DOCTYPE HTML>

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>LifeSaver Administration</title>
    <link rel="stylesheet" href="styles\clear.css">
    <link rel="stylesheet" href="styles\styles.css">
</head>

<body>
    <dialog>
        <img src="images/lifesaverlogo.png" />
        <?php
        if (isset($_GET['error'])  && $_GET['error'] != '' && isset($errors[$_GET['error']])) {
            echo('<h3>' . $errors[$_GET['error']] . "</h3>\n");
        }
        ?><p>Please use the login form below:</p>
        <form action="lib/login.php" method="post">
            <label>Username</label>
            <input type="text" name="username" />
            <label>Password</label>
            <input type="password" name="password" />
            <input type="submit" value="Login" />
        </form>
	</dialog>

</body>

</html>