<?php

function localDBConnect()
{
    return new PDO(
        'mysql:host=localhost;dbname=' . LOCALDB_DBNAME,
        LOCALDB_USERNAME,
        LOCALDB_PASSWORD
    );
}

/**
 * Haversine - using the haversine formula, calculate the distance between
 *             two latitude and longitude points
 *             http://en.wikipedia.org/wiki/Haversine_formula
 * @param  float $lat1 latitude point 1
 * @param  float $lon1 longitude point 1
 * @param  float $lat2 latitude point 2
 * @param  float $lon2 longitude point 2
 * @return float distance in km
 */
function haversine($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6372.8; // Earth radius in meters
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $lat1 = deg2rad($lat1);
    $lat2 = deg2rad($lat2);
    
    $a = pow(sin($dLat / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($dLon / 2), 2);
    $c = 2 * asin(sqrt($a));
    
    return($earthRadius * $c);

}

function getClosestProfessionals($lat, $lon)
{
    $dbh = localDBConnect();
    // 2d approximation for faster lookup.
    $query = "SELECT name, address, phone, email, latitude, longitude, " .
        "(ABS($lat - latitude) + ABS($lon - longitude)) as dist " .
        "FROM `healthcare_agents` ORDER BY dist LIMIT 5";
    
    $results = $dbh->query($query);
    
    foreach($results as $row)
    {
        $resultArray[] = array(
            'name' => $row['name'],
            'address' => $row['address'],
            'phone' => $row['phone'],
            'email' => $row['email'],
            'dist' => haversine($lat, $lon, $row['latitude'], $row['longitude'])
        );
    }
    return($resultArray);
}

?>