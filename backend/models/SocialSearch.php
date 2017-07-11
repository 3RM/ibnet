<?php

namespace backend\models;

use common\models\profile\Social;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class SocialSearch extends \common\models\profile\Social
{
    public function rules()
    { 
        // only fields in rules() are searchable
        return [
            [['id'], 'integer'],
            [['sermonaudio', 'facebook', 'linkedin', 'twitter', 'google', 'rss', 'youtube', 'vimeo', 'pinterest', 'tumblr', 'soundcloud', 'instagram', 'flickr'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Social::find()->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
    		        'pageSize' => 10,
    		    ],
        ]);

        // load the search form data and validate
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // adjust the query by adding the filters
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'facebook', $this->facebook])
              ->andFilterWhere(['like', 'instagram', $this->instagram])
              ->andFilterWhere(['like', 'flickr', $this->flickr])
              ->andFilterWhere(['like', 'google', $this->google])
              ->andFilterWhere(['like', 'linkedin', $this->linkedin])
              ->andFilterWhere(['like', 'pinterest', $this->pinterest])
              ->andFilterWhere(['like', 'rss', $this->rss])
              ->andFilterWhere(['like', 'sermonaudio', $this->sermonaudio])
              ->andFilterWhere(['like', 'soundcloud', $this->soundcloud])
              ->andFilterWhere(['like', 'tumblr', $this->tumblr])
              ->andFilterWhere(['like', 'twitter', $this->twitter])
              ->andFilterWhere(['like', 'vimeo', $this->vimeo])
              ->andFilterWhere(['like', 'youtube', $this->youtube]);

        return $dataProvider;
    }
}