<?php

/**
 * cls_postman.php
 * 
 * This class handles sending and receiving of API requests and responses.
 * 
 * Written by Ashley Carr (21591371@student.uwa.edu.au)
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
     * Receives the client request and returns an apiRequest object.
     */
    public function getClientRequest()
    {
        if ($this->validRequestHeaders()) {
            $this->clientRequest = trim(file_get_contents("php://input"));
        } else {
            throw new exception("Unexpected Content-Type in client " .
                                "equest: expected application json", 400);
        }
        return(new apiRequest($this->clientRequest));
    }
    
    
    /**
     * Sends the client a result set response
     * @param string $reponse json string response set
     */
    public function sendClientResponse(apiResponse $response)
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
     * @param int $codenum http status code number.
     */
    public function sendHeaders($codeNum)
    {
        $statusCodes = array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            500 => 'Internal Server Error'
        );
        
        header($codeNum . ' ' . $statusCodes[$codeNum]);
    }
    
    public function sendJSONContentTypeHeader()
    {
        header("Content-Type: application/json");
    }
}

?>
