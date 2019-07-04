<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

namespace common\models\group;

use common\models\group\Group;
use sammaye\solr\SolrDataProvider;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class GroupSearch extends Group
{
    /**
     * @param string $term User entered search string
     */
    public $term;
   
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['term', 'string', 'max' => 100],
            ['term', 'filter', 'filter' => 'strip_tags'],
        ];
    }

    /**
     * Bypass scenarios() - implementation in the parent class
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Returns Groups based on user search string
     */
    public function query($term)
    {
        $solr = Yii::$app->solr;
        $solr->setDefaultEndpoint('group');
        $query = $solr->createSelect();
        $EDisMax = $query->getEDisMax();
        // $EDisMax->setBoostQuery('org_name^2 ind_last_name^2');
        $query->setQuery($term);
        //$this->result = Yii::$app->solr->select($query);

        $dataProvider = new SolrDataProvider([
            'query' => $query,
            'modelClass' => 'common\models\group\SolrResult',
            'pagination' => ['pageSize'=>10],
            'sort' => false,
        ]);

        return $dataProvider;
    }

}
    