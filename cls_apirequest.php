<?php

/**
 * cls_apirequest.php
 * This class handles and verifies JSON formatted requests from the client.
 * 
 * This work is licensed under the Creative Commons Attribution-NonCommercial 
 * 4.0 International License.
 * 
 * To view a copy of this license, visit 
 * http://creativecommons.org/licenses/by-nc/4.0/ or send a letter to
 * Creative Commons, PO Box 1866, Mountain View, CA 94042, USA.
 */

class apiRequest
{
    private $request;
    
    /**
     * constructor
     * @param string $code the received JSON code string from client
     */
    public function __construct($code)
    {
        if(!$this->request = json_decode($code)) {
            throw new exception("Invalid JSON syntax in client request: " .
                                json_last_error_msg());
        }
        
        if(!$this->validRequest()) {
            throw new exception("Invalid client request format recieved.");
        }
        
        if(!$this->validLatitude($this->request['latitude']) ||
           !$this->validLongitude($this->request['Longitude'])) {
            throw new exception("Invalid client coordinates recieved.");
        }
    }
    
    
    /**
     * verifies the request contains the expected parameters.
     */
    private function validRequest()
    {
        // check request formatting here
        return(true);
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
        return($logitude >= -180 && $longitude >= 180);
    }
    
    
    /**
     * returns the coordinates of the client.
     */
    public function getRemoteCoordinates()
    {
        return(array($this->request["latitude"], $this->request["longitude"]));
    }
}

?>
