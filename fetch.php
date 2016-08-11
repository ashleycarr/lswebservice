<?php

/**
 * fetch.php
 * 
 * Called by client to fetch a list of health care professionals in proximity
 * to the user's location.
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
require_once("lib/lib_lifesaver.php");

try {
    // fetch client posted request.
    $postmaster = new postman();
    $request = $postmaster->getClientRequest();
    $coords = $request->getRemoteCoordinates();
    
    // generate response
    $response = new apiResponse(
        getClosestProfessionals($coords[0], $coords[1])
    );
    $postmaster->sendHeaders(200);
    $postmaster->sendJSONContentTypeHeader();
    $postmaster->sendClientResponse($response);
}
catch (exception $e)
{
    // on error, send JSON error message
    header("500 Bad Request");
    header("Content-Type: application/json");
    echo(json_encode(array('ErrorMessage' => $e->getMessage())));
}

?>
