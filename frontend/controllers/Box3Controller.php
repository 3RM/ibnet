<?php
namespace frontend\controllers;

use common\models\profile\ProfileSearch;
use frontend\models\Box3Content;
use Yii;
use yii\web\Controller;

/**
 * Box 3 Content controller
 */
class Box3Controller extends Controller
{
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionNext()
    {
        $searchModel = new ProfileSearch();
        $term = '';

        $content = new Box3Content();
        $box3Content = $content->getBox3Content();
        
        return $this->render('/site/index', [
            'searchModel' => $searchModel, 
            'term' => $term,
            'box3Content' => $box3Content,
        ]);
    }
}
