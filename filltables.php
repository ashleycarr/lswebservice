<?php

// this script attempts to fill the lifesaver tables with randomly placed
// agents around Australian population centers.

function frand($min, $max, $decimals = 0) {
  $scale = pow(10, $decimals);
  return mt_rand($min * $scale, $max * $scale) / $scale;
}

$dbh = new PDO(
    'mysql:host=localhost;dbname=lifesaver',
    'root',
    'root'
);

$sth1 = $dbh->prepare('
    INSERT INTO `healthcareAgents`
        (`id`, `name`, `address`, `phone`, `email`)
        VALUES (NULL, :name, :address, :phone, :email)');
        
        
$sth2 = $dbh->prepare('
    INSERT INTO `healthcareLocations`
        (agentID, location)
        VALUES (:id, POINT(:longitude, :latitude))');

$kmPerDeg = 111.2;
$doctorsInAus = 6000000;

// lat center, lon center, spread in degrees, percentage population
$urbanCenters = array(
    array(-33.87, 151.11, 80 / $kmPerDeg, 0.2070, 'Sydney'),
    array(-37.81, 145.03, 80 / $kmPerDeg, 0.1905, 'Melbourne'),
    array(-27.50, 153.09, 80 / $kmPerDeg, 0.0971, 'Brisbane'),
    array(-31.88, 115.90, 80 / $kmPerDeg, 0.0858, 'Perth'),
    array(-34.90, 138.63, 90 / $kmPerDeg, 0.0858, 'Adelaide'),
    array(-35.31, 149.11, 40 / $kmPerDeg, 0.0127, 'ACT'),
    array(-42.81, 147.31, 70 / $kmPerDeg, 0.0093, 'Hobart'),
    array(-12.45, 131.05, 80 / $kmPerDeg, 0.0060, 'Darwin')
);

for($i = 0; $i < count($urbanCenters); $i++)
{
    $numDoctors = round($urbanCenters[$i][3] * $doctorsInAus);
    $latMin = $urbanCenters[$i][0] - $urbanCenters[$i][2];
    $lonMin = $urbanCenters[$i][1] - $urbanCenters[$i][2];
    $latMax = $urbanCenters[$i][0] + $urbanCenters[$i][2];
    $lonMax = $urbanCenters[$i][1] + $urbanCenters[$i][2];
    echo($urbanCenters[$i][4] . 
        ': generating POIs between (' . $lonMin . ', ' . $latMin . ')' .
        ' and (' . $lonMax . ', ' . $latMax . ')'));
    for($d = 0; $d < $numDoctors; $d++)
    {
        $sth1->execute(
            array(
                ':name' => $urbanCenters[$i][4] . ' DocNo' . $d,
                ':address' => $urbanCenters[$i][4] . ' DocNo' . '\'s address',
                ':phone' => $i . $d,
                ':email' => $i . $d . '@email.com'
            ));

        $sth2->execute(
            array(
                ':id' => $dbh->lastInsertId(),
                ':longitude' => frand($lonMin, $lonMax, 5),
                ':latitude' => frand($latMin, $latMax, 5)
            ));  
    }
}

?>
