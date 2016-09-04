<?php

/**
 * cls_apiresponse.php
 *
 * This class contains methods for storing and encoding results in an api
 * response for the client.
 *
 * @author     Ashley Carr <21591371@student.uwa.edu.au>
 * @copyright  Ashley Carr <21591371@student.uwa.edu.au>
 * @license    http://creativecommons.org/licenses/by-nc/4.0/
 * @link       https://github.com/ashleycarr/lswebservice */

namespace Lifesaver\APIHandlers;

class Response
{
    private $resultSet;
    
    /**
     * constructor
     * @param array $result Result array from sql query
     */
    public function __construct($resultSet)
    {
        $this->resultSet = $resultSet;
    }
    
    /**
     * __tostring
     * Encodes the results as a json object for sending to the client.
     */
    public function __tostring()
    {
        return(json_encode($this->resultSet));
    }
}
