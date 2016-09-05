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


// Connect to the database.
$dbh = Library\localDBConnect(
    LOCALDB_DBNAME,
    LOCALDB_USERNAME,
    LOCALDB_PASSWORD
);

// Fetch healthcareAgents
if (isset($_GET['query'])) {
    $sth = $dbh->prepare('
        SELECT SQL_CALC_FOUND_ROWS * FROM `healthcareAgents`
        WHERE name LIKE :query
        LIMIT :pageStart, :pageSize
    ');
    
    $sth->bindValue(':query', '%' . $_GET['query'] . '%');
} else {
    $sth = $dbh->prepare('
        SELECT SQL_CALC_FOUND_ROWS * FROM `healthcareAgents`
        LIMIT :pageStart, :pageSize
    ');
}

// Check we're not on a negative page.
if (isset($_GET['page'])) {
    if($_GET['page'] < 0) {
        $page = 0;
    } else {
        $page = $_GET['page'];
    }
} else {
    $page = 0;
}

$sth->bindValue(':pageStart', $page * ADMIN_RESULTSPERPAGE);
$sth->bindValue(':pageSize', ADMIN_RESULTSPERPAGE);
$sth->execute();

// Check if we're on the last page.
$sthFoundRows = $dbh->query('SELECT FOUND_ROWS()');

// if we're on an invalid page.
if(ADMIN_RESULTSPERPAGE * $page > $sthFoundRows->fetchColumn(0)) {
    header('Location: index.php');
}

if(ADMIN_RESULTSPERPAGE * ($page + 1) > $sthFoundRows->fetchColumn(0))
{
    $lastPage = true;
} else {
    $lastPage = false;
}

unset($sthFoundRows);

?><!DOCTYPE HTML>

<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>LifeSaver Administration</title>
    <link rel="stylesheet" href="styles\clear.css">
    <link rel="stylesheet" href="styles\styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
</head>

<body>

	<header>
        <img src="images/lifesaverlogo.png" />
        <p>logged in as: <?= $_SESSION['user']->getUsername(); ?>. 
            <a href="lib/logout.php">logout</a>
        </p>
		<nav>
			<ul>
                <li><a onclick="$('#search').hide(); $('#edit').hide(); $('#add').toggle();">Add Professional</a></li>
                <?php
                if(isset($_GET['query'])) {
                    echo("<li><a href=\"index.php\">All Professionals</a></li>");
                } else {
                    echo("<li><a onclick=\"$('#edit').hide(); $('#add').hide(); $('#search').toggle();\">Search Professionals</a></li>");
                } ?>
                <li><a href="index.html">Help</a></li>
			</ul>
		</nav>
	</header>
    
    
	<aside>
		<h2>LifeSaver professionals</h2>
		<p>This page alows you to browse, add, edit and delete professionals in the database.</p>
	</aside>
	
	<section>
        <nav>
            <ul>
                <?php
                if ($page > 0) {
                    echo("<li><a href=\"?page=<?=$page-1?>\">prev</a></li>");
                } ?>
                <?php
                if (!$lastPage) {
                    echo("<li><a href=\"?page=<?=$page+1?>\">next</a></li>");
                } ?>
            </ul>
        </nav>
        <table>
            <tr>
                <td>Name</td>
                <td>Address</td>
                <td>Phone</td>
                <td>Email</td>
                <td>Options</td>
            </tr>
            <?php
            foreach ($sth as $row) {
            ?><tr>
                <td><?=$row['name']?></td>
                <td><?=$row['address']?></td>
                <td><?=$row['phone']?></td>
                <td><?=$row['email']?></td>
                <td>
                    <a href="lib/dbaction.php?action=delete&id=<?=$row['id']?>"><img src="images/trash.png" /></a>
                </td>
            </tr>
            <?php } ?>
        </table>
	</section>
    
    <dialog id="add">
        <a onclick="$('#add').hide()"><img src="images/cross.png" /></a>
        <p>Use this form to add a professional to the database.</p>
        <form id="addForm" action="lib/dbaction.php?action=add" method="post">
            <label>Name</label>
            <input id="name" type="text" name="name" max=128 />
            <label>Address</label>
            <input id="addr1" type="text" name="addr1" max=128 />
            <input id="addr2" type="text" name="addr2" max=128 />
            <label>State and Postcode</label>
            <select id="state" name="state">
                <option value="NSW">New South Wales</option>
                <option value="QLD">Queensland</option>
                <option value="VIC">Victoria</option>
                <option value="ACT">ACT</option>
                <option value="NT">Northern Territory</option>
                <option value="TAS">Tasmania</option>
                <option value="WA" selected="selected">Western Australia</option>
            </select>
            <input id="pcode" type="text" name="pcode" max=4 />
            <label>Phone</label>
            <input id="phone" type="text" name="phone" max=10 />
            <label>Email</label>
            <input id="email" type="text" name="email" max=254 />
            <input id="submit" type="submit" value="Add to database"/>
        </form>
    </dialog>
    
    <dialog id="search">
        <a onclick="$('#search').hide()"><img src="images/cross.png" /></a>
        <p>Use this form to search for a particular professional in the database.</p>
        <form id="searchForm" action="index.php" method="get">
            <label>Name</label>
            <input id="query" type="text" name="query" max=128 />
            <input id="submit" type="submit" value="Search database"/>
        </form>
    </dialog>

	<footer>
		<p>Written by Ashley Carr (
            <a href="mailto:21591371@student.uwa.edu.au">21591371@student.uwa.edu.au</a>
        )</p>
	</footer>

</body>

</html>