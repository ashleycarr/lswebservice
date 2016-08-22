<?php

/**
 * lib_lifesaver.php
 * 
 * Library of functions used by the lifesaver web service
 * This file is designed to use functions available in MySQL version 5.7+
 * 
 * At the moment, this is roughly the same speed as the 5.6+ version, however
 * as MySQL 5.7x becomes a production reccomendation and the PostGIS functions
 * are optimised further, it should become faster.  Also, the ability to use
 * prepared statements in this version speeds up the multiple queries should
 * there be no hits within the first 5km search box.
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
        ST_Y(l.location) AS latitude, ST_X(l.location) AS longitude,
        ST_Distance_Sphere(Point(:userLon, :userLat), l.location) AS dist
        FROM `healthcareLocations` AS l
        INNER JOIN `healthcareAgents` AS a
            ON l.agentID = a.id
        WHERE 
            ST_WITHIN(location, ST_MakeEnvelope(
            POINT(:lonMin, :latMin), POINT(:lonMax, :latMax)))
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
            throw new exception("Search: Unable to find professionals" .
                                "within 1000kms of user.", 400);
        }
        
        // increase search area.
        $boundRange *= 5;
    }
    
    foreach($sth as $row)
    {
        $resultArray[] = array(
            'name'      => $row['name'],
            'address'   => $row['address'],
            'phone'     => $row['phone'],
            'email'     => $row['email'],
            'latitude'  => $row['latitude'],
            'longitude' => $row['longitude'],
            'distance'  => $row['dist']
        );
    }
    
    return($resultArray);
}

?>