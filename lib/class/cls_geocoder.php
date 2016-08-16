<?php

/**
 * cls_geocoder.php
 * 
 * This class handles conversion of a street address to latitude and longidue
 * coordinates via the Google Maps Geocoding API.
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

class geocoder
{
    private $googleAPIKey;
    private $region;
    
    /**
     * Contructor
     * @param string $googleAPIKey User API key generated by Google.
     */
    public function __construct($googleAPIKey)
    {
        $this->googleAPIKey = $googleAPIKey;
    }
    
    
    /**
     * Formats an address string.  Removes spaces and inserts '+'s for API.
     * @param string $addressString Raw address string.
     */
    private function formatAddressString($addressString)
    {
        // format address string for url.
        // strip non alpha numeric characters
        $addressString = preg_replace('/\s+/', '+', $addressString);
        $addressString = preg_replace('/[^A-Za-z0-9\++\-]/', '', $addressString);
        // replace n spaces with a single + character.
        return($addressString);
    }
    
    
    /**
     * Performs the geocode lookup via curl.
     * @param string $address Formatted address to look up.
     */
    private function fetchGeocodeData($address)
    {
        $url = "https://maps.googleapis.com/maps/api/geocode/json?" . 
               "address=$address$this->region&key=$this->googleAPIKey";

        // fetch data from google geocode api.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);
        
        if($json == false) {
            throw new exception("Unable to contact Google web service: " .
                                curl_error($ch));
        }
         // decode and return the json result object
        return(json_decode($json, true));
    }
    
    
    /**
     * APIRequestError
     * @param array $gObj returned geocode object from the API
     */
    private function APIRequestError($gObj)
    {
        return(isset($gObj['error_message']));
    }
    
    
    /**
     * sets the region to base the geocoding.
     * @param string $region two letter region code to base results
     */
    public function setRegion($region)
    {
        if($region == "")
        {
            $this->region = "";
        }
        else {
            $this->region = "&region=$region";
        }
    }
    
    /**
     * Returns the geo coordinates of a street address.
     * @param string $address Raw address string to look up.
     */
    public function getCoordinatesOfStreetAddress($address)
    {
        // format the query address
        $address = $this->formatAddressString($address);
         
        // send query to google and return results
        $gObj = $this->fetchGeocodeData($address);
        
        // if the request failed, throw exception with google's error message.
        if($this->APIRequestError($gObj)) {
            throw new exception("Google Geocoding API failed with message: " .
                                $gObj['error_message']);
        }
        
        // if there are no results to show, the request failed.
        if(!isset($gObj['results'])) {
            throw new exception("Google Geocoding API failed: " .
                                "Malformed response with no error message.");
        }
    
        if(isset($gObj['status']) && $gObj['status'] == "ZERO_RESULTS") {
            throw new exception("Google Geocoding API failed: " .
                                "Zero results returned from address lookup.");            
        }
        return(array(
            'latitude'  => $gObj['results'][0]['geometry']['location']['lat'],
            'longitude' => $gObj['results'][0]['geometry']['location']['lng']));
    }
}

?>