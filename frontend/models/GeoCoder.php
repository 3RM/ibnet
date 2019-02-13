<?php
namespace frontend\models;

use Geocoder\Query\GeocodeQuery;
use yii;
use yii\base\Model;

/**
 * GeoCoder Class
 */
class GeoCoder extends Model
{

	/**
     * Returns latitude and longitude coordinates for a given address and api
     * @var string $address Address formatted for geocoding
     * @var string $apiKey Api for geocoding service
     * @return string || array
     */ 
    public static function getCoordinates($address, $key, $array=NULL) {
        
        // Replace all spaces with "+"
        $address = preg_replace('/\s+/', '+', $address);

        $httpClient = new \Http\Adapter\Guzzle6\Client();
        $provider = new \Geocoder\Provider\GoogleMaps\GoogleMaps($httpClient, NULL, $key);
        $geocoder = new \Geocoder\StatefulGeocoder($provider, 'en');

        $result = $geocoder->geocodeQuery(GeocodeQuery::create($address));
        $lat = $result->first()->getCoordinates()->getLatitude();
        $lng = $result->first()->getCoordinates()->getLongitude();
        if (empty($lat) || empty($lng)) {
            return NULL;
        }
        return ($array) ? ['lat' => $lat, 'lng' => $lng] : $lat . ',' . $lng;
    }
}
