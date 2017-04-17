<?php

namespace common\models\profile;

use common\models\profile\GoogleGeocoder;
use common\models\profile\Profile;
use sammaye\solr\SolrDataProvider;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * ProfileBrowse represents the model behind the browse form.
 */
class ProfileBrowse extends Profile
{
    public $distance;
    public $location;
    public $lat;
    public $lng;

    public function scenarios() {
        return[
            'browse' => ['distance', 'location', 'lat', 'lng'],
        ];
    }

    /**
    * @inheritdoc
    */
    public function rules() {
        return[
            ['location', 'default', 'value' => NULL, 'on' => 'browse'],
            ['distance', 'default', 'value' => 60, 'on' => 'browse'],
            ['location', 'string', 'max' => 120, 'on' => 'browse'],
            ['location', 'filter', 'filter' => 'strip_tags', 'on' => 'browse'],
            ['lat', 'safe', 'on' => 'browse'],
            ['lng', 'safe', 'on' => 'browse'],
        ];
    }

    /**
     * Fetch facets and set query
     */
    public function query()
    {
        
        // Initialize Query
        $query = Yii::$app->solr->createSelect();
        $EDisMax = $query->getEDisMax();
        $EDisMax->setBoostQuery('org_name^2 ind_last_name^2');
      
        // Get Facets
        
        // Type 
        $facetSet = $query->getFacetSet();
        $facetSet->createFacetField('type')->setField('f_type');
        $facetSet->createFacetField('sub_type')->setField('f_sub_type');
        $facetSet->setMinCount(1);

        // Type specific
        $facetSet->createFacetField('program')->setField('f_pg_org_name');
        $facetSet->createFacetField('miss_field')->setField('f_miss_field');
        $facetSet->createFacetField('miss_status')->setField('f_miss_status');
        $facetSet->createFacetField('miss_agcy')->setField('f_miss_agcy');
        $facetSet->createFacetField('tag')->setField('f_tag');
        $facetSet->createFacetField('level')->setField('f_level');
        $facetSet->createFacetField('title')->setField('f_title');
        
        // Location
        $facetPivot = $facetSet->createFacetPivot('f_country');
        $facetPivot->addFields('f_country,f_state,f_city');
        $facetPivot->setMinCount(1);

        // Distinctives
        $facetSet->createFacetField('bible')->setField('f_bible');
        $facetSet->createFacetField('worship_style')->setField('f_worship_style');
        $facetSet->createFacetField('polity')->setField('f_polity');
        
        // Set Query
        $query->setQuery('*:*');
        $resultSet = $this->resultSet($query);

        return $query;
    }

    /**
     * Returns search results for a given query
     */
    public function resultSet($query)
    {
        return $resultSet = Yii::$app->solr->select($query);
    }


    /**
     * Data provider for browse page listview
     */
    public function dataProvider($query)
    {
        $dataProvider = new SolrDataProvider([
            'query' => $query,
            'modelClass' => 'common\models\profile\SolrResult',
            'pagination' => ['pageSize'=>10],
            'sort' => false,
        ]);

        return $dataProvider;
    }

    /**
     * Returns Latitude & Longitude for a given "city, state"
     */
    public function getLatLng($spatial)
    {
        $location = preg_replace('/\s+/', '+', $this->location);                                    // Replace all spaces with "+"
        $geocoder = new GoogleGeocoder();
        $result = $geocoder->getLatLngOfAddress($location);
        $spatial['lat'] = $result['lat'];
        $spatial['lng'] = $result['lng'];
 
        return $spatial;
    }

}