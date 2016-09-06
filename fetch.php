<?php

/**
 * fetch.php
 *
 * Called by client to fetch a list of health care professionals in proximity
 * to the user's location.
 *
 * @author     Ashley Carr <21591371@student.uwa.edu.au>
 * @copyright  Ashley Carr <21591371@student.uwa.edu.au>
 * @license    http://creativecommons.org/licenses/by-nc/4.0/
 * @link       https://github.com/ashleycarr/lswebservice */

namespace Lifesaver;

require_once('lib/class/cls_postman.php');
require_once('lib/class/cls_apirequest.php');
require_once('lib/class/cls_apiresponse.php');
require_once('lib/lib_lifesaver.php');
require_once('settings.php');

error_reporting(-1);
ini_set('display_errors', 'On');

$postmaster = new APIHandlers\postman();

try {
    // fetch client posted request.
    $request = $postmaster->getClientRequest();
    $parameters = $request->getRequestParameters();
    
    // generate response
    $dbh = Library\localDBConnect(
        LOCALDB_DBNAME,
        LOCALDB_USERNAME,
        LOCALDB_PASSWORD
    );
    
    $response = new APIHandlers\Response(
        Library\getClosestProfessionals(
            $dbh,
            $parameters['lat'],
            $parameters['lon'],
            $parameters['numResults']
        )
    );
    
    unset($dbh);
    
    // send good headers and response json
    $postmaster->sendHeaders(200);
    $postmaster->sendJSONContentTypeHeader();
    $postmaster->sendClientResponse($response);
    
} catch (exception $e) {
    // on error, send JSON error message
    $postmaster->sendHeaders($e->getCode());
    $postmaster->sendJSONContentTypeHeader();
    echo(json_encode(array('ErrorMessage' => $e->getMessage())));
}
