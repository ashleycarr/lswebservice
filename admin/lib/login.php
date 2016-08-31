<?php

/**
 * dbaction.php
 *
 * Performs actions on the lifesaver database.
 *
 * This work is licensed under the Creative Commons Attribution-NonCommercial
 * 4.0 International License.
 *
 * To view a copy of this license, visit
 * http://creativecommons.org/licenses/by-nc/4.0/ or send a letter to
 * Creative Commons, PO Box 1866, Mountain View, CA 94042, USA. */

namespace Lifesaver;

require_once('../settings.php');
require_once('lib_lifesaver.php');
require_once('class/cls_user.php');

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
