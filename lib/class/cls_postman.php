<?php

/**
 * cls_postman.php
 *
 * This class handles sending and receiving of API requests and responses.
 *
 * @author     Ashley Carr <21591371@student.uwa.edu.au>
 * @copyright  Ashley Carr <21591371@student.uwa.edu.au>
 * @license    http://creativecommons.org/licenses/by-nc/4.0/
 * @link       https://github.com/ashleycarr/lswebservice */

namespace Lifesaver\APIHandlers;

class Postman
{
    private $expectedHeaders;
    
    
    /**
     * Receives the client post request and returns an apiRequest object.
     */
    public function getClientRequest()
    {
        $this->validateRequestHeaders();
        return(new Request(trim(file_get_contents("php://input"))));
    }
    
    
    /**
     * Sends the client a result set response
     * @param string $reponse json string response set
     */
    public function sendClientResponse(Response $response)
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
        foreach ($this->expectedHeaders as $key => $value) {
            if (!isset($headers[$key]) ||
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
