<?php

/**
 * dbaction.php
 *
 * Performs actions on the lifesaver database.
 *
 * @author     Ashley Carr <21591371@student.uwa.edu.au>
 * @copyright  Ashley Carr <21591371@student.uwa.edu.au>
 * @license    http://creativecommons.org/licenses/by-nc/4.0/
 * @link       https://github.com/ashleycarr/lswebservice */


namespace Lifesaver;

require_once("../settings.php");
require_once("lib_lifesaver.php");
require_once("class/cls_geocoder.php");
require_once('class/cls_user.php');


// ensure user is logged in
session_start();

if (!isset($_SESSION['user']) || !$_SESSION['user']->isLoggedIn()) {
    header('Location: login.php?error=1');
    exit;
}


if ($_GET["action"] == 'add') {
    try {
        
        // fetch latitude and longitude for address
        $geocoder = new APIHandlers\Geocoder(GOOGLE_GEOAPIKEY);
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
        $dbh = Library\localDBConnect(
            LOCALDB_DBNAME,
            LOCALDB_USERNAME,
            LOCALDB_PASSWORD
        );

        $sth = $dbh->prepare('
            INSERT INTO `healthcareAgents`
                (`id`, `name`, `address`, `phone`, `email`)
                VALUES (NULL, :name, :address, :phone, :email)');

        $sth->execute(
            array(
                ':name' => $_POST['name'],
                ':address' => $address,
                ':phone' => $phone,
                ':email' => $_POST['email'])
        );

        $sth = $dbh->prepare('
            INSERT INTO `healthcareLocations`
                (agentID, location)
                VALUES (:id, POINT(:longitude, :latitude))');

        $sth->execute(
            array(
                ':id' => $dbh->lastInsertId(),
                ':longitude' => $location['longitude'],
                ':latitude' => $location['latitude'])
        );
    } catch (exception $e) {
        
    }
    
    header('Location: ../index.php');
    exit;
}

if ($_GET["action"] == 'delete') {
    try {
        // delete from db
        $dbh = Library\localDBConnect(
            LOCALDB_DBNAME,
            LOCALDB_USERNAME,
            LOCALDB_PASSWORD
        );
        $sth = $dbh->prepare('
            DELETE FROM `healthcareAgents`
            WHERE id = :id');

        $sth->execute(
            array(
                ':id' => $_GET['id'])
        );
    } catch (exception $e) {
        header('Location: ../index.php');
        exit;
    }
    header('Location: ../index.php');
    exit;
}

header('Location: ../index.php');
exit;
