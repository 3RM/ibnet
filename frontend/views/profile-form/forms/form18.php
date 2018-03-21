<?php

use common\models\profile\Profile;
use common\models\profile\Tag;
use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Profile Tags';
?>

<?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

<div class="wrap profile-form">

    <div class="forms-container">

        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-11">
                <p><?= HTML::icon('info-sign') ?> Select one or more tags that relate to your ministry.  Tagging your profile 
                    helps others find your ministry through the directory search and browse features.  If
                    you would like to add additional tags, please contact us with your suggestions.
                </p>
            </div>
        </div>
    
        <div class="row">
            <div class="col-md-11">
                <?= $form->field($profile, 'select')->widget(Select2::classname(), [                 // see customization options here: http://demos.krajee.com/widget-details/select2
                    'data' => ArrayHelper::map(Tag::find()->orderBy('tag')->all(), 'id', 'tag'),
                    'language' => 'en',
                    'theme' => 'krajee',
                    'options' => [
                        'placeholder' => 'Select tag(s) ...',
                        'multiple' => true,
                    ],
                    'pluginOptions' => ['allowClear' => true],
                ])->label(''); ?>
            </div>
        </div>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>
