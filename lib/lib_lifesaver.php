<?php

/**
 * lib_lifesaver.php
 *
 * Library of functions used by the lifesaver web service
 * This version of the library takes advantage of the improved postGIS spacial
 * functions found in MySQL 5.7.x and up.
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


/**
 * returns an assoc array of the closest professionals to the user
 * @param float $lat        Latitiude of user
 * @param float $lon        Longitude of user
 * @param integer $maxResults maximum number of results to return
 */
function getClosestProfessionals($lat, $lon, $maxResults)
{
    $startTime = microtime();
    
    $dbh = localDBConnect(LOCALDB_DBNAME, LOCALDB_USERNAME, LOCALDB_PASSWORD);
    
    // Find an appropriate search range with enough results.
    // ~250m radius to start.
    $boundRange = 0.00025;
    
    $sth = $dbh->prepare('
        SELECT COUNT(*)
        FROM `healthcareLocations`
        WHERE 
            ST_INTERSECTS(location, ST_MakeEnvelope(
            POINT(:lonMin, :latMin), POINT(:lonMax, :latMax)));
    ');
    
    //  Maximum search is 16 degrees lat/lon, or approximately 1600kms
    //  around the user location.
    while ($boundRange <= 16) {
        // generate coords for a search polygon around user location
        $sth->bindValue(':lonMin', ($lon - $boundRange), \PDO::PARAM_INT);
        $sth->bindValue(':latMin', ($lat - $boundRange), \PDO::PARAM_INT);
        $sth->bindValue(':lonMax', ($lon + $boundRange), \PDO::PARAM_INT);
        $sth->bindValue(':latMax', ($lat + $boundRange), \PDO::PARAM_INT);
        $sth->execute();
        
        // If there are enough results to work with, use this range.
        if ($sth->fetchColumn(0) >= $maxResults) {
            // could come back the other way here?
            break;
        }
        
        // Otherwise, lets try doubling the search area.
        $boundRange *= 2;
    }
    
    if ($boundRange > 10) {
        throw new exception('Search: Unable to find professionals' .
                            ' within 1600kms of user.', 400);
    }
    
    // With the search area found, perform the search.
    $sth = $dbh->prepare('
        SELECT a.id as id, a.name AS name, a.address AS address, 
        a.phone AS phone, a.email AS email, 
        ST_Y(l.location) AS latitude, ST_X(l.location) AS longitude,
        ST_Distance_Sphere(POINT(:userLon, :userLat), l.location) AS dist
        FROM `healthcareLocations` AS l
        INNER JOIN `healthcareAgents` AS a
            ON l.agentID = a.id
        WHERE 
            ST_INTERSECTS(l.location, ST_MakeEnvelope(
            POINT(:lonMin, :latMin), POINT(:lonMax, :latMax)))
        ORDER BY dist ASC LIMIT :maxResults;
        ');
    
    $sth->bindValue(':userLat', $lat, \PDO::PARAM_INT);
    $sth->bindValue(':userLon', $lon, \PDO::PARAM_INT);
    $sth->bindValue(':maxResults', $maxResults, \PDO::PARAM_INT);
    $sth->bindValue(':lonMin', ($lon - $boundRange), \PDO::PARAM_INT);
    $sth->bindValue(':latMin', ($lat - $boundRange), \PDO::PARAM_INT);
    $sth->bindValue(':lonMax', ($lon + $boundRange), \PDO::PARAM_INT);
    $sth->bindValue(':latMax', ($lat + $boundRange), \PDO::PARAM_INT);
    $sth->execute();
    
    // Format the results of search to expected array for JSON encoding.
    foreach ($sth as $row) {
        $resultArray['results'][] = array(
            'id'        => $row['id'],
            'name'      => $row['name'],
            'address'   => $row['address'],
            'phone'     => $row['phone'],
            'email'     => $row['email'],
            'latitude'  => $row['latitude'],
            'longitude' => $row['longitude'],
            'distance'  => $row['dist']
        );
    }
    
    // Add statistics.
    $resultArray['statistics'] = array(
        'queryTime' => (microtime() - $startTime) * 1000 . 'ms',
        'searchRadias' => $boundRange * 111.2 * 2 . 'km',
    );

    return($resultArray);
}
