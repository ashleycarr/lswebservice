<?php

/**
 * lib_lifesaver.php
 *
 * Library of functions used by the lifesaver web service
 *
 * @author     Ashley Carr <21591371@student.uwa.edu.au>
 * @copyright  Ashley Carr <21591371@student.uwa.edu.au>
 * @license    http://creativecommons.org/licenses/by-nc/4.0/
 * @link       https://github.com/ashleycarr/lswebservice */

namespace Lifesaver\Library;

/**
 * creates a new PDO object to the local DB
 */
function localDBConnect($dbName, $username, $password)
{
    $dbh = new \PDO(
        'mysql:host=localhost;dbname=' . $dbName,
        $username,
        $password
    );
    
    $dbh->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    return($dbh);
}
    