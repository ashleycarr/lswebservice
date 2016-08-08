<?php

/**
 * cls_postman.php
 * This class handles sending and receiving of API requests and responses.
 *
 * This work is licensed under the Creative Commons Attribution-NonCommercial 
 * 4.0 International License.
 * 
 * To view a copy of this license, visit 
 * http://creativecommons.org/licenses/by-nc/4.0/ or send a letter to
 * Creative Commons, PO Box 1866, Mountain View, CA 94042, USA. 
 */

class postman
{
    private $clientRequest;
    
    /**
     * Constructor
     * Checks the received headers and stores the raw client request.
     */
    public function __construct()
    {
        if ($this->validRequestHeaders()) {
            $this->clientRequest = trim(file_get_contents("php://input"));
        } else {
            throw new exception("Unexpected Content-Type in client request: " .
                                "expected application/json");
        }
    }
    
    /**
     * Receives the client request and returns an apiRequest object.
     */
    public function getClientRequest()
    {
        return(new apiRequest($this->clientRequest));
    }
    
    
    /**
     * Sends the client a result set response
     * @param string $reponse json string response set
     */
    public function sendClientResponse(apiResponse $reponse)
    {
        echo($response);
    }
    
    
    /**
     * Returns true if the request headers are valid
     */
    private function validRequestHeaders()
    {
        $headers = apache_request_headers();
        return(isset($headers['Content-Type']) &&
            $headers['Content-Type'] == 'application/json');
    }
    
    
    /**
     * Sends the client the appropriate headers
     * @param boolean $success True if fetching result set was successful.
     */
    private function sendHeaders($success)
    {
        if ($success) {
            header("HTTP/1.1 200 OK");
        } else {
            header("HTTP/1.1 400 Bad Request");
        }
        
        header("Content-Type: application/json");
    }

}

?>
