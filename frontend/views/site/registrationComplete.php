<?php

use common\widgets\Alert;
use kartik\checkbox\CheckboxX;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Profile */

$this->title = 'Registration Complete';
?>
<?= Alert::widget() ?>

<div class="profile-create">
	<div class="container registration-complete">
	    <h2>Success! Thank You for Registering.</h2>
	    <?= HTML::a('My Account', Url::to(['/site/settings']), ['class' => 'btn-link']) ?>
	</div>
</div>

<div class="container-flex-center top-margin-40">
	<div>
		<h3>Take a moment and review your email preferences:</h3>
		<p>These settings can be changed at anytime in your <?= Html::a('account settings', ['/site/settings']) ?>.</p>
		<?php $form = ActiveForm::begin(['action' => 'account-settings']); ?>
        <?= $form->field($user, 'emailMaintenance', ['options' => ['class' => 'top-margin-10']])->widget(CheckboxX::classname(), [
            'initInputType' => CheckboxX::INPUT_CHECKBOX,
            'autoLabel' => true,
            'disabled'=>true,
            'pluginOptions'=>[
                'theme' => 'krajee-flatblue',
                'enclosedLabel' => true,
                'threeState'=>false, 
            ]
        ])->label(false) ?>
        <?= $form->field($user, 'emailPrefProfile')->widget(CheckboxX::classname(), [
            'initInputType' => CheckboxX::INPUT_CHECKBOX,
            'autoLabel' => true,
            'pluginOptions'=>[
            'theme' => 'krajee-flatblue',
                'theme' => 'krajee-flatblue',
                'enclosedLabel' => true,
                'threeState'=>false, 
            ]
        ])->label(false) ?>
        <?= $form->field($user, 'emailPrefLinks')->widget(CheckboxX::classname(), [
            'initInputType' => CheckboxX::INPUT_CHECKBOX,
            'autoLabel' => true,
            'pluginOptions'=>[
                'theme' => 'krajee-flatblue',
                'enclosedLabel' => true,
                'threeState'=>false, 
            ]
        ])->label(false) ?>
        <?= $form->field($user, 'emailPrefComments')->widget(CheckboxX::classname(), [
            'initInputType' => CheckboxX::INPUT_CHECKBOX,
            'autoLabel' => true,
            'pluginOptions'=>[
                'theme' => 'krajee-flatblue',
                'enclosedLabel' => true,
                'threeState'=>false, 
            ]
        ])->label(false) ?>
        <?= $form->field($user, 'emailPrefFeatures')->widget(CheckboxX::classname(), [
            'initInputType' => CheckboxX::INPUT_CHECKBOX,
            'autoLabel' => true,
            'pluginOptions'=>[
                'theme' => 'krajee-flatblue',
                'enclosedLabel' => true,
                'threeState'=>false, 
            ]
        ])->label(false) ?>
        <?= $form->field($user, 'emailPrefBlog')->widget(CheckboxX::classname(), [
            'initInputType' => CheckboxX::INPUT_CHECKBOX,
            'autoLabel' => true,
            'pluginOptions'=>[
                'theme' => 'krajee-flatblue',
                'enclosedLabel' => true,
                'threeState'=>false, 
            ]
        ])->label(false) ?>
        <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>
    	<?php $form = ActiveForm::end(); ?>  
	</div>
</div>