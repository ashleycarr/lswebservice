<?php

/**
 * fetch.php
 *
 * This work is licensed under the Creative Commons Attribution-NonCommercial 
 * 4.0 International License.
 * 
 * To view a copy of this license, visit 
 * http://creativecommons.org/licenses/by-nc/4.0/ or send a letter to
 * Creative Commons, PO Box 1866, Mountain View, CA 94042, USA. */

require_once("cls_postman.php");
require_once("cls_apirequest.php");
require_once("cls_apiresponse.php");
require_once("lib_lifesaver.php");

try {
    $postmaster = new postman();
    $request = $postmaster->getClientRequest();
    $request->getRemoteCoordinates();
    $response = new apiResponse(getClosestProfessionals());
    $postmaster->sendHeaders(200);
    $postmaster->sendJSONContentTypeHeader();
    $postmaster->sendClientResponse($response);
}
catch (exception $e)
{
    $postmaster->sendHeaders(400);
    $postmaster->sendJSONContentTypeHeader();
    echo(json_encode(array('ErrorMessage' => $e->getMessage)));
}

?>
