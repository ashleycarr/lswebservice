<?php

/**
 * lib_lifesaver.php
 *
 * Library of functions used by the lifesaver web service
 * This version of the library takes advantage of the improved postGIS spacial
 * functions found in MySQL 5.7.x and up.
 *
 * Written by Ashley Carr (21591371@student.uwa.edu.au)
 *
 * This work is licensed under the Creative Commons Attribution-NonCommercial
 * 4.0 International License.
 *
 * To view a copy of this license, visit
 * http://creativecommons.org/licenses/by-nc/4.0/ or send a letter to
 * Creative Commons, PO Box 1866, Mountain View, CA 94042, USA. */

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
