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
    $query = "SELECT * FROM `healthcare_agents`";
    
    $results = $dbh->query($query);
    
    foreach($results as $row)
    {
        $resultArray[] = array(
            'name' => $row['name'],
            'address' => $row['address'],
            'phone' => $row['phone'],
            'email' => $row['email']
        );
    }
    return($resultArray);
}

?>