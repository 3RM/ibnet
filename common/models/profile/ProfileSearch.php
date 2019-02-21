<?php
/**
 * @link http://www.ibnet.org/
 * @copyright  Copyright (c) IBNet (http://www.ibnet.org)
 * @author Steve McKinley <steve@themckinleys.org>
 */

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
            ['term', 'string', 'max' => 20],
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
    