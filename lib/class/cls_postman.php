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
    private $expectedHeaders;
    
    /**
     * Receives the client post request and returns an apiRequest object.
     */
    public function getClientRequest()
    {
        $this->validateRequestHeaders();
        return(new apiRequest(trim(file_get_contents("php://input"))));
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
     * Sets the headers that are required for the client transaction
     * @param array $headerVals Assoc array of required headers
     */
    public function setExpectedHeaders($headerVals)
    {
        $this->expectedHeaders = $headerVals;
    }
    
    
    /**
     * Validates the request headers against the expected header values
     */
    private function validateRequestHeaders()
    {
        $headers = apache_request_headers();
        foreach($this->expectedHeaders as $key => $value) {
            if(!isset($headers[$key]) ||
                $headers[$key] != $value) {
                throw new exception("Unexpected $key in client " .
                                "request: expected $value", 400);
            }
        }
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
