<?php

/**
 * lib_lifesaver.php
 * 
 * Library of functions used by the lifesaver web service.
 *
 * This work is licensed under the Creative Commons Attribution-NonCommercial 
 * 4.0 International License.
 * 
 * To view a copy of this license, visit 
 * http://creativecommons.org/licenses/by-nc/4.0/ or send a letter to
 * Creative Commons, PO Box 1866, Mountain View, CA 94042, USA. */ 

function localDBConnect()
{
    return new PDO(
        'mysql:host=localhost;dbname=' . LOCALDB_DBNAME,
        LOCALDB_USERNAME,
        LOCALDB_PASSWORD
    );
}

function getClosestProfessionals($lat, $lon, $numResults)
{
    $dbh = localDBConnect();
    
    $boundRange = 0.5;
    $foundResults = false;
    
    while(!$foundResults) {
        $query = "
            SELECT name, address, phone, email, latitude, longitude,
            6372800 * 2 * ASIN(SQRT(POWER(
            SIN(($lat - latitude) * pi()/180 / 2), 2) + 
            COS($lat * pi()/180) * COS(latitude * pi()/ 180) * 
            POWER(SIN(($lon - longitude) * pi() /180 / 2), 2) )) as dist 
            FROM `healthcare_agents` 
            WHERE latitude 
                BETWEEN $lat - $boundRange AND $lat + $boundRange AND 
            longitude 
                BETWEEN $lon - $boundRange AND $lon + $boundRange 
            ORDER BY dist ASC LIMIT $numResults;";

        $results = $dbh->query($query);
        if($results->rowCount() > 0) {
            $foundResults = true;
        }
        
        $boundRange *= 4;
    }

    foreach($results as $row)
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