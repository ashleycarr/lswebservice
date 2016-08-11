<?php

/**
 * cls_apiresponse.php
 * This class contains methods for storing and encoding results in an api 
 * response for the client. 
 *
 * This work is licensed under the Creative Commons Attribution-NonCommercial 
 * 4.0 International License.
 * 
 * To view a copy of this license, visit 
 * http://creativecommons.org/licenses/by-nc/4.0/ or send a letter to
 * Creative Commons, PO Box 1866, Mountain View, CA 94042, USA. */


class apiResponse
{
    private $results;
    
    /**
     * constructor
     * @param array $result Result array from sql query
     */
    public function __construct($resultSet)
    {
        $this->results = $resultSet;
    }
    
    /**
     * __tostring
     * Encodes the results as a json object for sending to the client.
     */
    public function __tostring()
    {
        return(json_encode($this->results));
    }
}

?>
