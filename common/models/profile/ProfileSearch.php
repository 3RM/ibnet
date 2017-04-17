<?php

namespace common\models\profile;

use common\models\profile\Profile;
use sammaye\solr\SolrDataProvider;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * ProfileSearch represents the model behind the search form about `common\models\profile\Profile`.
 */
class ProfileSearch extends Profile
{
    public $term;                                                                                   // User entered search string
   
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['term', 'filter', 'filter' => 'strip_tags'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();                                                                  // bypass scenarios() - implementation in the parent class
    }

    /**
     * Returns Profile rows based on user search string
     */
    public function query($term)
    {
        $query = Yii::$app->solr->createSelect();
        $EDisMax = $query->getEDisMax();
        $EDisMax->setBoostQuery('org_name^2 ind_last_name^2');
        $query->setQuery($term);
        //$this->result = Yii::$app->solr->select($query);

        $dataProvider = new SolrDataProvider([
            'query' => $query,
            'modelClass' => 'common\models\profile\SolrResult',
            'pagination' => ['pageSize'=>10],
            'sort' => false,
        ]);

        return $dataProvider;
    }

}
    