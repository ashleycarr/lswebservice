<?php

/**
 * lib_lifesaver.php
 * 
 * Library of functions used by the lifesaver web service.
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
    
    //  Approximately 18km
    $boundRange = 0.3;
    
    $sth = $dbh->prepare('
        SELECT name, address, phone, email, latitude, longitude,
        haversine(:userLat, :userLon, latitude, longitude) as dist 
        FROM `healthcare_agents` 
        WHERE 
        latitude 
            BETWEEN :latLowerBound AND :latUpperBound
        AND longitude 
            BETWEEN :lonLowerBound AND :lonUpperBound
        ORDER BY dist ASC LIMIT :maxResults;
    ');
    
    while(true) {
        $sth->execute(array(
            ':userLat' => $lat,
            ':userLon' => $lon,
            ':latLowerBound' => $lat - $boundRange,
            ':latUpperBound' => $lat + $boundRange,
            ':lonLowerBound' => $lon - $boundRange,
            ':lonUpperBound' => $lon + $boundRange,
            ':maxResults' => $maxResults
        ));
        
        // break if search has returned enough results
        if($sth->rowCount() > 0) {
            break;
        }
        
        if($boundRange >= 9) {
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