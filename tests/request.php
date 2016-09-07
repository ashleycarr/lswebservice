<?php

use PHPUnit\Framework\TestCase;
require_once('settings.php');
require_once(LIB_DIRECTORY . 'class/cls_apirequest.php');
define('MAX_RESULTS', 10);

class APIRequestTest extends TestCase
{
    public function testValidRequest()
    {
        $code = '{"lat":-31.8909840, "lon":115.7871080, "numResults": 5}';
        $request = new Lifesaver\APIHandlers\Request($code);
        $requestParams = $request->getRequestParameters();
        $this->assertArrayHasKey('lat', $requestParams);
        $this->assertArrayHasKey('lon', $requestParams);
        $this->assertArrayHasKey('numResults', $requestParams);
        $this->assertEquals(-31.8909840, $requestParams['lat']);
        $this->assertEquals(115.7871080, $requestParams['lon']);
        $this->assertEquals(5, $requestParams['numResults']);
    }
    
    public function test_invalidBadLat()
    {
        $this->expectException(exception::class);
        $code = '{"lat":-301.8909840, "lon":115.7871080, "numResults": 5}';
        $request = new Lifesaver\APIHandlers\Request($code);
    }
    
    public function test_invalidBadLon()
    {
        $this->expectException(exception::class);
        $code = '{"lat":-31.8909840, "lon":1150.7871080, "numResults": 5}';
        $request = new Lifesaver\APIHandlers\Request($code);
    }
    
    public function test_invalidBadNumResults()
    {
        $this->expectException(exception::class);
        $code = '{"lat":-31.8909840, "lon":115.7871080, "numResults": 500}';
        $request = new Lifesaver\APIHandlers\Request($code);
    }
    
    public function test_invalidBadJSON()
    {
        $this->expectException(exception::class);
        $code = '"lat":-31.8909840, "lon":115.7871080, numResults": 5}';
        $request = new Lifesaver\APIHandlers\Request($code);
    }
    
}
