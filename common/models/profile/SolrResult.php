<?php

namespace common\models\profile;

use borales\extensions\phoneInput\PhoneInputBehavior;
use sammaye\solr\SolrDocumentInterface;

class SolrResult implements SolrDocumentInterface
{
	public $phone;

	public function behaviors()
    {   
        return [
            'phoneInput' => PhoneInputBehavior::className(),
        ];
    }

    public static function populateFromSolr($doc)
    {
    	$doc->phone;
        return $doc; 
    }
}