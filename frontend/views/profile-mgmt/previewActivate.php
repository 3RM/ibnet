
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */
$this->title = 'Preview & Activate';
?>

<div class="profile-form">

    <h1><?= $this->title ?></h1>

    <H4>A Profile Preview with edit options goes here</H4>    

    <br>
    <br>

    <?= $profile->category == Profile::CATEGORY_IND ? 
    	Html::a('Edit', ['profile/form1', 'id' => $profile->id], ['class' => 'btn btn-primary']) :
        Html::a('Edit', ['profile/form0', 'id' => $profile->id], ['class' => 'btn btn-primary']) ?>

    <br>
    <br>

    <?= HTML::a('Activate', ['activate', 'id' => $profile->id], ['class' => 'btn btn-primary']) ?>

</div>