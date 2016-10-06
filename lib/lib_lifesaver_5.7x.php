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
 * returns an assoc array of the closest professionals to the user
 * @param pdo     $dbh        PDO object
 * @param float   $lat        Latitiude of user
 * @param float   $lon        Longitude of user
 * @param integer $maxResults maximum number of results to return
 */
function getClosestProfessionals($dbh, $lat, $lon, $maxResults)
{
    $startTime = microtime();
   
    // Find an appropriate search range with enough results.
    // 500m radius to start.
    $boundRange = 0.0005;
    $foundResults = false;

    $sth = $dbh->prepare('
        SELECT COUNT(*)
        FROM `healthcareLocations`
        WHERE 
            ST_INTERSECTS(location, ST_MakeEnvelope(
            POINT(:lonMin, :latMin), POINT(:lonMax, :latMax)));');
    
    //  Maximum search is 8 degrees lat/lon, or approximately 900kms
    //  around the user location.
    while ($boundRange < 8.192) {
        // generate coords for a search polygon around user location
        $sth->bindValue(':lonMin', ($lon - $boundRange), \PDO::PARAM_INT);
        $sth->bindValue(':latMin', ($lat - $boundRange), \PDO::PARAM_INT);
        $sth->bindValue(':lonMax', ($lon + $boundRange), \PDO::PARAM_INT);
        $sth->bindValue(':latMax', ($lat + $boundRange), \PDO::PARAM_INT);
        $sth->execute();
        
        // If there are enough results to work with, use this range.
        $found = $sth->fetchColumn(0);
        
        if ($found >= $maxResults) {
            break;
        }
        
        if ($found > 0) {
            $foundResults = true;
        }
        
        // Otherwise, lets try doubling the search area.
        $boundRange *= 2;
    }
    
    if (!$foundResults) {
        throw new \exception('Search: Unable to find professionals' .
                             ' within 900kms of user.', 400);
    }
    
    // With the search area found, perform the search.
    $sth = $dbh->prepare('
        SELECT a.id as id, a.name AS name, a.address1 AS address1,
        a.address2 AS address2, a.state AS state,
        a.postcode AS postcode, a.phone AS phone, a.email AS email, 
        ST_Y(l.location) AS latitude, ST_X(l.location) AS longitude,
        ST_Distance_Sphere(POINT(:userLon, :userLat), l.location) AS dist
        FROM `healthcareLocations` AS l
        INNER JOIN `healthcareAgents` AS a
            ON l.agentID = a.id
        WHERE 
            ST_INTERSECTS(l.location, ST_MakeEnvelope(
            POINT(:lonMin, :latMin), POINT(:lonMax, :latMax)))
        ORDER BY dist ASC LIMIT :maxResults;');
    
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
            'address1'  => $row['address1'],
            'address2'  => $row['address2'],
            'state'     => $row['state'],
            'postcode'  => $row['postcode'],
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
        'searchRadius' => $boundRange * 111.2 . 'km',
    );

    return($resultArray);
}
