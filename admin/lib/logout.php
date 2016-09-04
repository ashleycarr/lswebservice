<?php

/**
 * logout.php
 *
 * logs a user out of the admin website.
 *
 * @author     Ashley Carr <21591371@student.uwa.edu.au>
 * @copyright  Ashley Carr <21591371@student.uwa.edu.au>
 * @license    http://creativecommons.org/licenses/by-nc/4.0/
 * @link       https://github.com/ashleycarr/lswebservice */

session_start();
session_destroy();
header('Location: ../login.php');
