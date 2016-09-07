<?php

use PHPUnit\Framework\TestCase;
require_once('settings.php');
require_once(ADMIN_LIB_DIRECTORY . 'class/cls_geocoder.php');
define('GOOGLE_GEOAPIKEY', "AIzaSyBpN0BcxFtmFKVteFgwsR6w5LQbz3NPioY");

class APIRequestTest extends TestCase
{
    public function test_ValidLookup()
    {
        $geocoder = new Lifesaver\APIHandlers\Geocoder(GOOGLE_GEOAPIKEY);
        $geocoder->setRegion("au");

        $address = '321A Huntriss Road Doubleview WA 6018';
        $location = $geocoder->getCoordinatesOfStreetAddress($address);

        $this->assertArrayHasKey('latitude', $location);
        $this->assertArrayHasKey('longitude', $location);
        $this->assertEquals(-31.900939999999991, $location['latitude']);
        $this->assertEquals(115.78668, $location['longitude']);
    }
    
    public function test_InvalidAPIKey()
    {
        $this->expectException(exception::class);
        $geocoder = new Lifesaver\APIHandlers\Geocoder('THE_WRONG_API_KEY');
        $geocoder->setRegion("au");
        $address = '321A Huntriss Road Doubleview WA 6018';
        $location = $geocoder->getCoordinatesOfStreetAddress($address);
    }
    
    public function test_InvalidStreetAddress()
    {
        $this->expectException(exception::class);
        $geocoder = new Lifesaver\APIHandlers\Geocoder(GOOGLE_GEOAPIKEY);
        $geocoder->setRegion("au");
        $address = '4000 Not a real street in some neighborhood that doesnt exist';
        $location = $geocoder->getCoordinatesOfStreetAddress($address);
    }
}