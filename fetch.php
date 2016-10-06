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

try {
    // fetch client posted request.
    $postmaster = new APIHandlers\postman();
    $postmaster->setExpectedHeaders(array('Content-Type' => 'application/json'));
    $request = $postmaster->getClientRequest();
    $parameters = $request->getRequestParameters();

    // generate response
    // insert into db
    $dbh = Library\localDBConnect(
        LOCALDB_DBNAME,
        LOCALDB_USERNAME,
        LOCALDB_PASSWORD
    );
    
    $mysqlVersion = $dbh->query('select version()')->fetchColumn();
    $mysqlVersion = $mysqlVersion[2];
    
    require_once('lib/lib_lifesaver_5.' . $mysqlVersion . 'x.php');
    
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
} catch (\exception $e) {
    // on error, send JSON error message
    APIHandlers\postman::sendHeaders($e->getCode());
    APIHandlers\postman::sendJSONContentTypeHeader();
    echo(json_encode(array('ErrorMessage' => $e->getMessage())));
}
