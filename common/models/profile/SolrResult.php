<?php

namespace common\models\profile;

use sammaye\solr\SolrDocumentInterface;

class SolrResult implements SolrDocumentInterface
{
    public static function populateFromSolr($doc)
    {
        return $doc;
    }
}