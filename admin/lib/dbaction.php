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
                (`id`, `name`, `address1`, `address2`, `state`, `postcode`, `phone`, `email`)
                VALUES (NULL, :name, :address1, :address2, :state, :postcode, :phone, :email)');

        $sth->execute(
            array(
                ':name' => $_POST['name'],
                ':address1' => $_POST['addr1'],
                ':address2' => $_POST['addr2'],
                ':state' => $_POST['state'],
                ':postcode' => $_POST['pcode'],
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

if ($_GET["action"] == 'update') {
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
            UPDATE `healthcareAgents` SET
                `name` = :name, `address1` = :address1, `address2` = :address2,
                `state` = :state, `postcode` = :postcode, `phone` = :phone,
                `email` = :email
                WHERE id = :id');

        $sth->execute(
            array(
                ':name' => $_POST['name'],
                ':address1' => $_POST['addr1'],
                ':address2' => $_POST['addr2'],
                ':state' => $_POST['state'],
                ':postcode' => $_POST['pcode'],
                ':phone' => $phone,
                ':email' => $_POST['email'],
                ':id' => $_POST['id'])
        );

        $sth = $dbh->prepare('
            UPDATE `healthcareLocations`
                SET `location` = POINT(:longitude, :latitude)
                WHERE agentid = :id');

        $sth->execute(
            array(
                ':longitude' => $location['longitude'],
                ':latitude' => $location['latitude'],
                ':id' => $_POST['id'])
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
