<?php

use PHPUnit\Framework\TestCase;
require_once('settings.php');
require_once(LIB_DIRECTORY . 'lib_lifesaver.php');

// Local MYSQL database settings
define('LOCALDB_USERNAME', 'lsguest');
define('LOCALDB_PASSWORD', 'lsguest');
define('LOCALDB_DBNAME', 'lifesaver');

class APIRequestTest extends TestCase
{
    public function testValidLookup()
    {
        $dbh = Lifesaver\Library\localDBConnect(
            LOCALDB_DBNAME,
            LOCALDB_USERNAME,
            LOCALDB_PASSWORD
        );

        $response = Lifesaver\Library\getClosestProfessionals(
            $dbh,
            -31.8909840,
            115.7871080,
            5
        );
        
        $this->assertArrayHasKey('results', $response);
        $this->assertArrayHasKey('statistics', $response);
    }
}
