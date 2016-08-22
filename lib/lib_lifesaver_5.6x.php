<?php

/**
 * lib_lifesaver.php
 * 
 * Library of functions used by the lifesaver web service.
 * This file is designed to use functions available in MySQL version 5.6+
 * 
 * Written by Ashley Carr (21591371@student.uwa.edu.au)
 *
 * This work is licensed under the Creative Commons Attribution-NonCommercial 
 * 4.0 International License.
 * 
 * To view a copy of this license, visit 
 * http://creativecommons.org/licenses/by-nc/4.0/ or send a letter to
 * Creative Commons, PO Box 1866, Mountain View, CA 94042, USA. */ 

/**
 * creates a new PDO object to the local DB
 */
function localDBConnect()
{
    $dbh = new PDO(
        'mysql:host=localhost;dbname=' . LOCALDB_DBNAME,
        LOCALDB_USERNAME,
        LOCALDB_PASSWORD
    );
    
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return($dbh);
}


/**
 * returns an assoc array of the closest professionals to the user
 * @param float $lat        Latitiude of user
 * @param float $lon        Longitude of user
 * @param integer $maxResults maximum number of results to return
 */
function getClosestProfessionals($lat, $lon, $maxResults)
{
    $dbh = localDBConnect();
    
    $sth = $dbh->prepare("
        SELECT a.name AS name, a.address AS address, 
        a.phone AS phone, a.email AS email, 
        Y(l.location) AS latitude, X(l.location) AS longitude,
        HAVERSINE(:userLat, :userLon, Y(l.location), X(l.location)) AS dist
        FROM `healthcareLocations` AS l
        INNER JOIN `healthcareAgents` AS a
            ON l.agentID = a.id
        WHERE 
            X(l.location) BETWEEN :lonMin AND :lonMax AND
            Y(l.location) BETWEEN :latMin AND :latMax
        ORDER BY dist ASC LIMIT :maxResults;
    ");
        
    $sth->bindValue(':userLat', $lat, PDO::PARAM_INT);
    $sth->bindValue(':userLon', $lon, PDO::PARAM_INT);
    $sth->bindValue(':maxResults', $maxResults, PDO::PARAM_INT);
    
    //  Approximately a 5 square kilometer search area
    $boundRange = 0.05;
    
    while(true) {
        // generate coords for a search polygon around user location
        $sth->bindValue(':lonMin', ($lon - $boundRange), PDO::PARAM_INT);
        $sth->bindValue(':latMin', ($lat - $boundRange), PDO::PARAM_INT);
        $sth->bindValue(':lonMax', ($lon + $boundRange), PDO::PARAM_INT);
        $sth->bindValue(':latMax', ($lat + $boundRange), PDO::PARAM_INT);
        
        $sth->execute();

        // break if search has returned enough results
        if($sth->rowCount() > 0) {
            break;
        }
        
        if($boundRange > 9) {
            throw new exception("Search: Unable to find professionals within 1000kms of user.", 400);
        }
        
        // increase search area.
        $boundRange *= 5;
    }
    
    foreach($sth as $row)
    {
        $resultArray[] = array(
            'name' => $row['name'],
            'address' => $row['address'],
            'phone' => $row['phone'],
            'email' => $row['email'],
            'distance' => $row['dist']
        );
    }
    
    return($resultArray);
}

?>