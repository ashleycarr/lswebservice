<?php

/**
 * cls_apirequest.php
 *
 * This class handles and verifies JSON formatted requests from the client.
 *
 * @author     Ashley Carr <21591371@student.uwa.edu.au>
 * @copyright  Ashley Carr <21591371@student.uwa.edu.au>
 * @license    http://creativecommons.org/licenses/by-nc/4.0/
 * @link       https://github.com/ashleycarr/lswebservice */

namespace Lifesaver\APIHandlers;

class Request
{
    private $request;
    
    // Disallow the request of more that 50 professionals.
    private $maxResults = 50;
    
    /**
     * constructor
     * @param string $code the received JSON code string from client
     */
    public function __construct($code)
    {
        // check JSON formatting
        if (!$this->request = json_decode($code, true)) {
            throw new \exception(
                'APIRequest: Invalid JSON syntax in client request: ' .
                json_last_error_msg() . strlen($code),
                400
            );
        }
        
        // Check request formatting
        if (!$this->validFormat()) {
            throw new \exception(
                'APIRequest: Invalid client request format.',
                400
            );
        }
        
        // Check lat/lon coordinates within bounds
        if (!$this->validLatitude($this->request['lat']) ||
           !$this->validLongitude($this->request['lon'])) {
            throw new \exception(
                'APIRequest: Invalid client coordinates.',
                400
            );
        }
        
        // Check the user hasn't requested too many results.
        if (!$this->validNumResults($this->request['numResults'])) {
            throw new \exception(
                'APIRequest: Invalid number of results requested.',
                400
            );
        }
    }
    
    
    /**
     * verifies the request contains the expected parameters.
     */
    private function validFormat()
    {
        return(
            isset($this->request['lat']) &&
            is_numeric($this->request['lat']) &&
            isset($this->request['lon']) &&
            is_numeric($this->request['lon']) &&
            isset($this->request['numResults']) &&
            is_numeric($this->request['numResults'])
        );
    }
    
    
    /**
     * returns true if $latitude is between +-90 degrees
     * @param float $latitude latitude component of client coordinates
     */
    private function validLatitude($latitude)
    {
        return($latitude >= -90 && $latitude <= 90);
    }
    
    
    /**
     * returns true if $longitude is between +-180 degrees
     * @param float $longitude longitude component of client coordinate
     */
    private function validLongitude($longitude)
    {
        return($longitude >= -180 && $longitude <= 180);
    }
    
    /**
     * returns true if requested number of results are in acceptable range
     * @param integer $numResults number of results requested.
     */
    private function validNumResults($numResults)
    {
        return($numResults > 0 && $numResults <= $this->maxResults);
    }
    
    
    /**
     * returns the coordinates of the client.
     */
    public function getRequestParameters()
    {
        return(array('lat' => $this->request['lat'],
                     'lon' => $this->request['lon'],
                     'numResults' => $this->request['numResults']));
    }
}
