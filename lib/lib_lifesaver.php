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
    $query = "
        SELECT name, address, phone, email, latitude, longitude,
        6372.8 * 2 * ASIN(SQRT(POWER(
        SIN(($lat - latitude) * pi()/180 / 2), 2) + 
        COS($lat * pi()/180) * COS(latitude * pi()/ 180) * 
        POWER(SIN(($lon - longitude) * pi() /180 / 2), 2) )) as dist 
        FROM `healthcare_agents` 
        WHERE latitude 
            BETWEEN $lat - 0.5 AND $lat + 0.5 AND 
        longitude 
            BETWEEN $lon - 0.5 AND $lon + 0.5 
        ORDER BY dist LIMIT 5";
    
    $results = $dbh->query($query);
    
    foreach($results as $row)
    {
        $resultArray[] = array(
            'name' => $row['name'],
            'address' => $row['address'],
            'phone' => $row['phone'],
            'email' => $row['email'],
            'dist' => $row['dist']
        );
    }
    return($resultArray);
}

?>