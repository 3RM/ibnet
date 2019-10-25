<?php

use common\models\profile\ProfileSearch;
use common\models\profile\ProfileGuestSearch;
use yii\base\Event;
use yii\web\View;


// Uploads & Downloads
Yii::setAlias('@packet', '/uploads/packet');
Yii::setAlias('@update', '/uploads/missionary-update');
Yii::setAlias('@downloads', '/downloads');

Event::on(View::className(), View::EVENT_BEFORE_RENDER, function() {
	$searchModel = Yii::$app->user->isGuest ? new ProfileGuestSearch() : new ProfileSearch();
	$searchModel->term = $_GET['term'] ?? NULL;
    Yii::$app->view->params['searchModel'] = $searchModel;
});