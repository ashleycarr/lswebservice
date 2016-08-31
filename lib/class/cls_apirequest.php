<?php

/**
 * cls_apirequest.php
 *
 * This class handles and verifies JSON formatted requests from the client.
 *
 * Written by Ashley Carr (21591371@student.uwa.edu.au)
 *
 * This work is licensed under the Creative Commons Attribution-NonCommercial
 * 4.0 International License.
 *
 * To view a copy of this license, visit
 * http://creativecommons.org/licenses/by-nc/4.0/ or send a letter to
 * Creative Commons, PO Box 1866, Mountain View, CA 94042, USA. */

namespace Lifesaver\APIHandlers;

class Request
{
    private $request;
    
    /**
     * constructor
     * @param string $code the received JSON code string from client
     */
    public function __construct($code)
    {
        if (!$this->request = json_decode($code, true)) {
            throw new exception(
                'APIRequest: Invalid JSON syntax in client request: ' .
                json_last_error_msg() . strlen($code),
                400
            );
        }
        
        if (!$this->validRequest()) {
            throw new exception(
                'APIRequest: Invalid client request format.',
                400
            );
        }
        
        if (!$this->validLatitude($this->request['lat']) ||
           !$this->validLongitude($this->request['lon'])) {
            throw new exception('APIRequest: Invalid client coordinates.', 400);
        }
        
        if (!$this->validNumResults($this->request['numResults'])) {
            throw new exception(
                'APIRequest: Invalid number of results requested.',
                400
            );
        }
    }
    
    
    /**
     * verifies the request contains the expected parameters.
     */
    private function validRequest()
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
        return($numResults > 0 && $numResults <= MAX_RESULTS);
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
