<?php

/**
 * login.php
 *
 * Logs the user into the admin website checking supplied credentials.
 *
 * @author     Ashley Carr <21591371@student.uwa.edu.au>
 * @copyright  Ashley Carr <21591371@student.uwa.edu.au>
 * @license    http://creativecommons.org/licenses/by-nc/4.0/
 * @link       https://github.com/ashleycarr/lswebservice */

namespace Lifesaver;

require_once('../settings.php');
require_once('lib_lifesaver.php');
require_once('class/cls_user.php');

if (!isset($_POST['username']) || !isset($_POST['password']) ||
    $_POST['username'] == '' || $_POST['password'] == '') {
    header('Location: ../login.php?error=2');
    exit;
}

session_start();

$_SESSION['user'] = new Library\User($_POST['username']);

if ($_SESSION['user']->authenticate($_POST['password'])) {
    $_SESSION['user']->setLoggedIn();
    $_SESSION['user']->setLastActivity();
    header('Location: ../index.php');
    exit;
} else {
    header('Location: ../login.php?error=0');
    exit;
}
