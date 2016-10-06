<?php

/**
 * lib_lifesaver.php
 *
 * Library of functions used by the lifesaver web service
 * This version of the library uses functions found in MySQL 5.6.x and up.
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
            X(location) BETWEEN :lonMin AND :lonMax AND
            Y(location) BETWEEN :latMin AND :latMax;');
    
    //  Maximum search is 8 degrees lat/lon, or approximately 900kms
    //  around the user location.
    while ($boundRange <= 8) {
        // generate coords for a search polygon around user location
        $sth->bindValue(':lonMin', ($lon - $boundRange), \PDO::PARAM_INT);
        $sth->bindValue(':latMin', ($lat - $boundRange), \PDO::PARAM_INT);
        $sth->bindValue(':lonMax', ($lon + $boundRange), \PDO::PARAM_INT);
        $sth->bindValue(':latMax', ($lat + $boundRange), \PDO::PARAM_INT);
        $sth->execute();
        
        $found = $sth->fetchColumn(0);
        
        // If there are enough results to work with, use this range.
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
        Y(l.location) AS latitude, X(l.location) AS longitude,
        HAVERSINE(:userLon, :userLat, X(l.location), Y(l.location)) AS dist 
        FROM `healthcareLocations` AS l
        INNER JOIN `healthcareAgents` AS a
            ON l.agentID = a.id
        WHERE 
            X(l.location) BETWEEN :lonMin AND :lonMax AND
            Y(l.location) BETWEEN :latMin AND :latMax
        LIMIT :maxResults;');
    
    
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
            'address'   => $row['address1'] . ' ' .
                           $row['address2'] . ' ' .
                           $row['state'] . ' ' .
                           $row['postcode'],
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
