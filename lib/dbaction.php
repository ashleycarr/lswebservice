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

require_once("../settings.php");
require_once("class/cls_geocoder.php");

function localDBConnect()
{
    return new PDO(
        'mysql:host=localhost;dbname=' . LOCALDB_DBNAME,
        LOCALDB_USERNAME,
        LOCALDB_PASSWORD
    );
}

$action = $_GET["action"];

if($action == 'add')
{
    $query = "INSERT INTO `healthcare_agents` (`id`, `name`, `address`, `phone`, " .
             "`email`, `latitude`, `longitude`) VALUES (";
    
    $geocoder = new geocoder(GOOGLE_GEOAPIKEY);
    $geocoder->setRegion("au");
    
    $address = 
        $_POST['addr1'] . " " .
        $_POST['addr2'] . " " .
        $_POST['pcode'] . " " .
        $_POST['state'];
    
    $location = $geocoder->getCoordinatesOfStreetAddress($address);

    $query = "INSERT INTO `healthcare_agents` VALUES (NULL, '" .
        $_POST['name'] . "', '" .
        $address . "', '" .
        preg_replace('/[^0-9\,\-]/', '', $_POST['phone']) . "', '" .
        $_POST['email'] . "', " .
        $location['latitude'] . ", " .
        $location['longitude'] . ");";
    
    echo $query;
    
    $dbh = localDBConnect();
    $dbh->exec($query);
    
    exit;
}

if($action == 'update')
{
    
    exit;
}

if($action == 'delete')
{
    exit;
}

?>
