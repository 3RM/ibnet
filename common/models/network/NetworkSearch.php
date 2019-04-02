<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\network;

use common\models\network\Network;
use sammaye\solr\SolrDataProvider;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;


class NetworkSearch extends Network
{
    public $term;
   
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['term', 'string', 'max' => 20],
            ['term', 'filter', 'filter' => 'strip_tags'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Returns networks based on user search string
     */
    public function query($term)
    {
        $query = Yii::$app->solr->createSelect();
        $EDisMax = $query->getEDisMax();
        $EDisMax->setBoostQuery('name^2');
        $query->setQuery($term);
        //$this->result = Yii::$app->solr->select($query);

        $dataProvider = new SolrDataProvider([
            'query' => $query,
            'modelClass' => 'common\models\network\SolrResult',
            'pagination' => ['pageSize'=>10],
            'sort' => false,
        ]);

        return $dataProvider;
    }

}
    