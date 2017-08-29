<?php

namespace common\models\profile;

use Yii;
use yii\web\HttpException;

/**
 * Allows you to interface with the Google Directions API and the Google
 * Geocoder API.
 * 
 * usage examples: http://thisinterestsme.com/downloads/php-google-directions-and-geocoding-wrapper-class/
 *
 * @author Wayne Whitty - thisinterestsmeblog@gmail.com
 * 
 */

class GoogleGeocoder{
    
    
    /**
     * Geocoder API URL.
     */
    const GEOCODE_URL = 'http://maps.googleapis.com/maps/api/geocode/json';
    
    /**
     * Directions API URL.
     */
    const DIRECTIONS_URL = 'http://maps.googleapis.com/maps/api/directions/json';
    
    /**
     * Your (optional) API key.
     * 
     * @var string
     */
    protected $apiKey = '';
    
    /**
     * Parameters that can be used with the Google Directions API. You can 
     * change this if Google removes or adds extra options.
     * 
     * @var array 
     */
    protected $recognizedDirectionsParams = array(
        'origin', 'destination', 'mode', 'key', 'waypoints',
        'alternatives', 'avoid', 'language', 'units', 'region',
        'departure_time', 'arrival_time', 'transit_mode',
        'transit_routing_preference'
    );
    
    /**
     * Unit types that can used with the Google Directions API.
     * 
     * @var array 
     */
    protected $recognizedDirectionsUnits = array('metric', 'imperial');
    
    
    /**
     * The type of parameters that can be used with the avoid parameter in
     * the Google Directions API.
     * 
     * @var array 
     */
    protected $recognizedDirectionsAvoid = array('tolls', 'highways', 'ferries', 'indoor');
    
    
    /**
     * The type of parameters that can be used with the transit_mode parameter in
     * the Google Directions API.
     * 
     * @var array 
     */
    protected $recognizedDirectionsTransit = array('bus', 'subway', 'train', 'tram', 'rail');
    
    
    /**
     * Constructor.
     * 
     * @param string|null $apiKey Optional parameter. Your Google Geocoder API Key.
     * @throws Exception If an $apiKey is provided, but is not a valid string.
     */
    public function __construct($apiKey = null) {
        if(!is_null($apiKey)){
            if(!$this->isValidString($apiKey)){
                throw new Exception('$apiKey must be a string!');
            }
            $this->apiKey = trim($apiKey);
        }
    }
    
    
    /**
     * Set your API key (or change it).
     * 
     * @param string $apiKey Your API key.
     * @throws Exception If an $apiKey is provided, but is not a valid string.
     */
    public function setAPIKey($apiKey){
        if(!$this->isValidString($apiKey)){
            throw new Exception('$apiKey must be a string!');
        }
        $this->apiKey = trim($apiKey);
    }
    
    
    /**
     * Geocode a given address.
     * 
     * @param string $address The address in question.
     * @param array $params Optional parameters.
     * @return array Associative array.
     * @throws Exception
     */
    public function geocode($address, $params = array()){
        if(!$this->isValidString($address)){
            throw new HTTPException('$address is not a valid string!');
        }
        $options = array();
        $options['address'] = $address;
        $options = array_merge($options, $params);
        $url = $this->constructURL(self::GEOCODE_URL, $options);
        $res = $this->get($url);
        $decoded = $this->decodeJSON($res);
        $this->checkStatusCode($decoded);
        return $decoded;
    }
    
    
    /**
     * Get directions from one point to another.
     * 
     * @param string $from Textual address of origin or lat,lng of origin.
     * @param string $to Textual address of destination or lat,lng of destination.
     * @param array $options Associative array containing Google Directions parameters.
     * @return array Associative array representing the JSON response.
     * @throws Exception
     */
    public function getDirections($from, $to, $options = array()){
        
        if(!$this->isValidString($from)){
            throw new HTTPException('$from is not a valid string!');
        }
        if(!$this->isValidString($to)){
            throw new HTTPException('$to is not a valid string!');
        }
        if(!is_array($options)){
            throw new HTTPException('$options is not an array!');
        }
        
        $options['origin'] = $from;
        $options['destination'] = $to;
        
        
        foreach($options as $key => $param){
            $key = (string) $key;
            if(!in_array($key, $this->recognizedDirectionsParams)){
                trigger_error($key . ' is not recognized as a valid Directions API parameter.', E_USER_WARNING);
            }
        }
        
        if(isset($options['waypoints'])){
            if(!is_array($options['waypoints'])){
                trigger_error('The waypoints parameter for the Directions API should be an array.', E_USER_WARNING);
            }
        }
        
        if(isset($options['units'])){
            if(!in_array($options['units'], $this->recognizedDirectionsUnits)){
                trigger_error('"' . $options['units'] . '" is not a recognised unit for the Directions API.', E_USER_WARNING);
            }
        }
        
        if(isset($options['avoid'])){
            if(!is_array($options['avoid'])){
                trigger_error('The avoid parameter for the Directions API should be an array.', E_USER_WARNING);
            } else{
                foreach($options['avoid'] as $avoidParam){
                    if(!in_array($avoidParam, $this->recognizedDirectionsAvoid)){
                        trigger_error('"' . $avoidParam . '" is not a recognised avoid parameter for the Directions API.', E_USER_WARNING);
                    }
                }
            }
        }
        
        if(isset($options['transit_mode'])){
            if(!is_array($options['transit_mode'])){
                trigger_error('The transit_mode parameter for the Directions API should be an array.', E_USER_WARNING);
            } else{
                foreach($options['transit_mode'] as $transitParam){
                    if(!in_array($transitParam, $this->recognizedDirectionsTransit)){
                        trigger_error('"' . $transitParam . '" is not a recognised transit_mode parameter for the Directions API.', E_USER_WARNING);
                    }
                }
            }
        }
        
        $url = $this->constructURL(self::DIRECTIONS_URL, $options);
        $res = $this->get($url);
        $decoded = $this->decodeJSON($res);
        $this->checkStatusCode($decoded);
        return $decoded;
        
    }
    
    
    /**
     * Retrieve the lat lng values of a successful Geocoder API call.
     * 
     * @param array $decodedJSON
     * @return array
     * @throws Exception
     */
    public function getLatLngFromResult($decodedJSON){
        if(!is_array($decodedJSON)){
            throw new HTTPException('$decodedJSON is not a valid array!');
        }
        $res = $decodedJSON['results'][0]['geometry'];
        return array(
            'lat' => $res['location']['lat'],
            'lng' => $res['location']['lng'],
            'location_type' => $res['location_type']
        );
    }
    
    
    /**
     * Calculate the distance of a given route.
     * 
     * @param array $routeArr The route.
     * @return int The distance in meters.
     * @throws Exception If $routeArr doesn't represent a valid route.
     */
    public function getDistanceOfRoute($routeArr){
        if(!$this->isValidRoute($routeArr)){
            throw new HTTPException('$routeArr is not a valid route!');
        }
        $totalDistance = 0;
        foreach($routeArr['legs'] as $leg){
            $totalDistance = $totalDistance + $leg['distance']['value'];
        }
        return $totalDistance;
    }
    
    
    
    /**
     * Convert meters to kilometers.
     * 
     * @param int $meters Meters.
     * @return float Kilometers.
     */
    public function metersToKm($meters){
        return $meters * 0.001;
    }
    
    
    /**
     * Convert meters to miles.
     * 
     * @param int $meters Meters.
     * @return float Miles.
     */
    public function metersToMiles($meters){
        return $meters * 0.000621371;
    }
    
    
    /**
     * Retrieves the HTML / textual directions for a given route.
     * 
     * @param array $routeArr
     * @return array
     * @throws Exception
     */
    public function getDirectionInformationFromRoute($routeArr){
        if(!$this->isValidRoute($routeArr)){
            throw new HTTPException('$routeArr is not a valid route!');
        }
        $textualDirections = array();
        foreach($routeArr['legs'] as $leg){
            foreach($leg['steps'] as $step){
                $textualDirections[] = array(
                    'html' => $step['html_instructions'],
                    'distance' => $step['distance']['value'],
                    'distance_text' => $step['distance']['text'],
                    'start_location' => array(
                        'lat' => $step['start_location']['lat'],
                        'lng' => $step['start_location']['lng']
                    ),
                    'end_location' => array(
                        'lat' => $step['end_location']['lat'],
                        'lng' => $step['end_location']['lng']
                    ),
                    'polyline_points' => $step['polyline']['points'],
                    'travel_mode' => $step['travel_mode']
                );
            }
        }
        return $textualDirections;
    }
    
    
    /**
     * Get the textual directions from one point to another.
     * 
     * @param string $from Textual address of origin or lat,lng of origin.
     * @param string $to Textual address of destination or lat,lng of destination.
     * @param array $options Associative array containing Google Directions parameters.
     * @return array Array containing textual directions/
     * @throws Exception
     */
    public function getDirectionInformation($from, $to, $options = array()){
        $res = $this->getDirections($from, $to, $options);
        $firstRoute = $res['routes'][0];
        $textualDirections = $this->getDirectionInformationFromRoute($firstRoute);
        return $textualDirections;
    }
    
    
    /**
     * Convert seconds (duration) into a more human-readable format.
     * 
     * @param int $seconds The number of seconds.
     * @return string
     */
    function secondsToHumanReadableTime($seconds) {
        $dtF = new DateTime("@0");
        $dtT = new DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
    }
    
    
    /**
     * Get the duration of a given route in seconds.
     * 
     * @param type $routeArr The route.
     * @return int Seconds.
     * @throws Exception If $routeArr doesn't represent a valid route.
     */
    public function getDurationOfRoute($routeArr){
        if(!$this->isValidRoute($routeArr)){
            throw new HTTPException('$routeArr is not a valid route!');
        }
        $totalDuration = 0;
        foreach($routeArr['legs'] as $leg){
            $totalDuration = $totalDuration + $leg['duration']['value'];
        }
        return $totalDuration;
    }
    
    
    /**
     * Get distance from one point to another.
     * 
     * @param string $from Textual address of origin or lat,lng of origin.
     * @param string $to Textual address of destination or lat,lng of destination.
     * @param array $options Associative array containing Google Directions parameters.
     * @return int The distance.
     * @throws Exception
     */
    public function getDistance($from, $to, $options = array()){
        $res = $this->getDirections($from, $to, $options);
        $firstRoute = $res['routes'][0];
        $distance = $this->getDistanceOfRoute($firstRoute);
        return $distance;
    }
    
    
    /**
     * Get the duration (in seconds) that it would take to travel from the origin to the destination.
     * 
     * @param string $from Textual address of origin or lat,lng of origin.
     * @param string $to Textual address of destination or lat,lng of destination.
     * @param array $options Associative array containing Google Directions parameters.
     * @return int The duration in seconds.
     * @throws Exception
     */
    public function getDuration($from, $to, $options = array()){
        $res = $this->getDirections($from, $to, $options);
        $firstRoute = $res['routes'][0];
        $duration = $this->getDurationOfRoute($firstRoute);
        return $duration;
    }
    
    
    /**
     * Get the Lat / Lng of a given textual address.
     * 
     * @param string $address
     * @return array Associative array containing lat, lng and location_type.
     */
    public function getLatLngOfAddress($address){
        $res = $this->geocode($address);
        return $this->getLatLngFromResult($res);
    }
    
    
    /**
     * Check to see if decoded JSON has status OK.
     * 
     * @param array $decodedArr Decode JSON string.
     * @throws Exception If OK is not found.
     */
    protected function checkStatusCode($decodedArr){
        if(!is_array($decodedArr)){
            throw new HTTPException('$decodedArr is not a valid array!');
        }
        if(isset($decodedArr['status'])){
            $status = $decodedArr['status'];
            if($status == 'ZERO_RESULTS'){
                // throw new HTTPException('No results found!');
                Yii::$app->session->setFlash('danger', 'The address you entered on the "Location" form did not return valid map coordinates.  Your profile will not be able to display a map for the address you entered.');
            }
            if($status == 'OVER_QUERY_LIMIT'){
                // throw new HTTPException('You have reached your query limit quota!');
            }
            if($status == 'REQUEST_DENIED'){
                // throw new HTTPException('This request was denied!');
            }
            if($status == 'INVALID_REQUEST'){
                // throw new HTTPException('Your request was invalid. Are you missing a required parameter?');
            }
            if($status == 'NOT_FOUND'){
                // throw new HTTPException('At least one of the locations specified in the request\'s origin, destination, or waypoints could not be geocoded.');
            }
            if($status == 'MAX_WAYPOINTS_EXCEEDED'){
                // throw new HTTPException('Too many waypoints were provided in the request.');
            }
            if($status == 'UNKNOWN_ERROR'){
                // throw new HTTPException('An unknown server error occured!');
            }
        }
    }
    
    
    /**
     * Construct a URL for an API call.
     * 
     * @param string $url
     * @param array $params Query string parameters.
     * @return string
     * @throws Exception
     */
    protected function constructURL($url, $params = array()){
        if(!is_array($params)){
            throw new HTTPException('$params is not a valid array!');
        }
        if(!$this->isValidString($url)){
            throw new HTTPException('$url is not a valid string!');
        }
        if(!isset($params['key'])){
            if(strlen($this->apiKey) > 0){
                $params['key'] = $this->apiKey;
            }
        }
        foreach($params as $k => $v){
            if(is_array($v)){
                $params[$k] = implode("|", $v);
            }
        }
        $params = http_build_query($params);
        return $url . '?' . $params;
    }
    
    
    /**
     * Performs a GET request on a given URL.
     * 
     * @param string $url The URL that you want to send the GET request to.
     * @return string The output.
     * @throws Exception If the request fails.
     */
    protected function get($url){
        //Is this a valid string?
        if(!$this->isValidString($url)){
            throw new HTTPException('$url is not a valid string.');
        }
        //Figure out whether we need to use cURL or not.
        $useCurl = true;
        if(!function_exists('curl_init')){
            $useCurl = false;
        }
        //If cURL is not installed, use file_get_contents.
        if($useCurl === false){
            $res = file_get_contents($url);
            if($res === false){
                //Something went wrong.
                throw new HTTPException('Request failed: ' . $url);
            }
            return $res;
        }
        //cURL is installed.
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        if(curl_errno($ch)){
            throw new HTTPException('Request failed: ' . $url . ' - ' . curl_error($ch));
        }
        return $res;       
    }
    
    
    /**
     * Decode a JSON string into an associative array.
     * 
     * @param string $jsonStr The JSON string.
     * @return array
     * @throws Exception If the decoding process fails.
     */
    protected function decodeJSON($jsonStr){
        //Decode into an associative array.
        $decoded = json_decode($jsonStr, true);        
        //Error checking.
        if(!is_array($decoded)){
            if(!function_exists('json_last_error')){
                throw new HTTPException('Could not decode JSON!');
            } else{
                $jsonError = json_last_error();
                if($jsonError == JSON_ERROR_NONE){
                    throw new HTTPException('Could not decode JSON!');
                } else{
                    $error = 'Could not decode JSON! ';
                    switch($jsonError){
                        case JSON_ERROR_DEPTH:
                            $error .= 'Maximum depth exceeded!';
                        break;
                        case JSON_ERROR_STATE_MISMATCH:
                            $error .= 'Underflow or the modes mismatch!';
                        break;
                        case JSON_ERROR_CTRL_CHAR:
                            $error .= 'Unexpected control character found';
                        break;
                        case JSON_ERROR_SYNTAX:
                            $error .= 'Malformed JSON';
                        break;
                        case JSON_ERROR_UTF8:
                             $error .= 'Malformed UTF-8 characters found!';
                        break;
                        default:
                            $error .= 'Unknown error!';
                        break;
                    }
                    throw new HTTPException($error);
                }
            }
        }
        return $decoded;
    }
    
    
    /**
     * Check to see whether a variable is a valid string or not.
     * 
     * @param mixed $var
     * @return boolean TRUE if it is a valid string. FALSE if it isn't.
     */
    protected function isValidString($var){
        if(!is_string($var)){
            return false;
        } else{
            if(strlen(trim($var)) == 0){
                return false;
            }
        }
        return true;
    }
    
    
    /**
     * Checks to see if a variable is a valid route (associative array that represents
     * a particular route).
     * 
     * @param mixed $routeArr
     * @return boolean
     */
    protected function isValidRoute($routeArr){
        if(!is_array($routeArr)){
            return false;
        }
        if(!isset($routeArr['legs'])){
            return false;
        }
        return true;
    }
    
}