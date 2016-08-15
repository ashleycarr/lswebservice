<?php

function localDBConnect()
{
    return new PDO(
        'mysql:host=localhost;dbname=' . LOCALDB_DBNAME,
        LOCALDB_USERNAME,
        LOCALDB_PASSWORD
    );
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