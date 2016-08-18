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
require_once("lib_lifesaver.php");

if($_GET["action"] == 'add')
{
    try {
        
        // fetch latitude and longitude for address
        $geocoder = new geocoder(GOOGLE_GEOAPIKEY);
        $geocoder->setRegion("au");

        $address = 
            $_POST['addr1'] . " " .
            $_POST['addr2'] . " " .
            $_POST['state'] . " " .
            $_POST['pcode'];

        $location = $geocoder->getCoordinatesOfStreetAddress($address);

        // remove phone number formatting
        $phone = preg_replace('/[^0-9\,\-]/', '', $_POST['phone']);

        // insert into db
        $dbh = localDBConnect();
        $sth = $dbh->prepare('
            INSERT INTO `healthcare_agents`
                (`id`, `name`, `address`, `phone`, `email`, 
                `latitude`, `longitude`)
                VALUES (NULL, :name, :address, :phone, :email, 
                :latitude, :longitude)');

        $sth->execute(
            array(
                ':name' => $_POST['name'],
                ':address' => $address,
                ':phone' => $phone,
                ':email' => $_POST['email'],
                ':latitude' => $location['latitude'],
                ':longitude' => $location['longitude']
            ));
    }
    catch (exception $e) {
        echo($e->getMessage());
    }
    exit;
}

if($_GET["action"] == 'update')
{
    
    exit;
}

if($_GET["action"] == 'delete')
{
    exit;
}

?>
