<?php

namespace Lifesaver;

require_once('settings.php');
require_once('lib/lib_lifesaver.php');
require_once('lib/class/cls_user.php');

// Check if user is logged in
session_start();

if (!isset($_SESSION['user']) || !$_SESSION['user']->isLoggedIn()) {
    header('Location: login.php?error=1');
    exit;
}
// Update users activity timestamp
$_SESSION['user']->setLastActivity();

?>
<!DOCTYPE HTML>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>LifeSaver Administration</title>
    <link rel="stylesheet" href="styles\clear.css">
    <link rel="stylesheet" href="styles\styles.css">
</head>

<body>

	<header>
        <img src="images/lifesaverlogo.png" />
        <p>logged in as: <?= $_SESSION['user']->getUsername(); ?>. 
            <a href="lib/logout.php">logout</a>
        </p>
		<nav>
			<ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="help.php">Help</a></li>
			</ul>
		</nav>
	</header>
    
	<aside>
		<h2>LifeSaver administration site help</h2>
		<p>This page alows you to browse, add, edit and delete professionals in the database.</p>
	</aside>
	
	<section id='help'>
        <h2>Access</h2>

        <p>After successful installation, accessing the back end of the service to input professionals is a matter of navigating to the administration page in any web browser. The URL for the website will be of the form:</p>

        </p><strong>www.mysite.com/public_dir_with_installed_files/admin</strong></p>

            <p>Navigating to this page will present you with a login page where you will need to enter your credentials set up during installation.</p>
    
    <img src="images/login.png" alt="Login window" />

    <h2>Adding professionals</h2>

    <p>Simply clicking on the "Add Professional" link on the navigation panel will present a window where you will be able to add a professional. In this window you will enter the professional's name, address, email and phone number. This will be used to generate a latitude and longitude automatically for the address using google's geocoding API.</p>

    <p>Bare in mind, to facilitate the best user experience on the mobile application, Address 1 should be reserved only for specific details such as building numbers, floor levels etc, while Address 2 should contain the actual street address of the professional.</p>

    <h2>Searching for an existing professional</h2>

    <p>Click on the "Search Professionals" link on the navigation panel. This will present you a window where you can enter the name of the professional you would like to select. After clicking search, a list of professionals that match the search string will be presented.</p>

    <h2>Deleting professionals</h2>

    <p>After each entry in the main professional list in the center of the screen is the trash can icon:</p>

    <img src="images/trash.png" alt="Delete Icon" />

    <p>Clicking on this icon following the the professional you wish to delete will immediately remove it from the database.</p>

    <h2>Updating professionals</h2>

    <p>After each entry in the main professional list in the center of the screen is the edit icon:</p>

    <img src="images/edit.png" alt="Edit Icon" />

    <p>Clicking on this icon following the the professional you wish to update will bring up an update window which allows you to modify any detail about the selected professional.</p>
    </section>
 
	<footer>
		<p>Written by Ashley Carr (
            <a href="mailto:21591371@student.uwa.edu.au">21591371@student.uwa.edu.au</a>
        )</p>
	</footer>

</body>

</html>