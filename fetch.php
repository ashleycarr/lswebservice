<?php

/**
 * fetch.php
 * 
 * Called by client to fetch a list of health care professionals in proximity
 * to the user's location.
 * 
 * Written by Ashley Carr (21591371@student.uwa.edu.au)
 *
 * This work is licensed under the Creative Commons Attribution-NonCommercial 
 * 4.0 International License.
 * 
 * To view a copy of this license, visit 
 * http://creativecommons.org/licenses/by-nc/4.0/ or send a letter to
 * Creative Commons, PO Box 1866, Mountain View, CA 94042, USA. */

require_once("lib/class/cls_postman.php");
require_once("lib/class/cls_apirequest.php");
require_once("lib/class/cls_apiresponse.php");
require_once("lib/lib_lifesaver_5.6x.php");
require_once("settings.php");

$postmaster = new postman();

try {
    // fetch client posted request.
    $postmaster->setExpectedHeaders(array("Content-Type" => "application/json"));
    $request = $postmaster->getClientRequest();
    $parameters = $request->getRequestParameters();
    
    // generate response
    $response = new apiResponse(
        getClosestProfessionals(
            $parameters['lat'], $parameters['lon'], $parameters['numResults'])
    );
    
    // send good headers and response json
    $postmaster->sendHeaders(200);
    $postmaster->sendJSONContentTypeHeader();
    $postmaster->sendClientResponse($response);
}
catch (exception $e)
{
    // on error, send JSON error message
    $postmaster->sendHeaders($e->getCode());
    $postmaster->sendJSONContentTypeHeader();
    echo(json_encode(array('ErrorMessage' => $e->getMessage())));
}

?>
