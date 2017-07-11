<?php

use borales\extensions\phoneInput\PhoneInput;
use common\models\profile\Profile;
use frontend\assets\AjaxAsset;
use richardfan\widget\JSRegister;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Profile */
/* @var $form yii\widgets\ActiveForm */
AjaxAsset::register($this);
$this->title = 'Contact Information';
?>

<div class="wrap profile-form">

    <?= $this->render('_profileFormHeader', ['profile' => $profile, 'pp' => $pp]) ?>

    <div class="container-form">

        <?php $form = ActiveForm::begin(['id' => 'link_form']); ?>

        <div class="row">
            <div class="col-md-5">
                <?= $form->field($profile, 'phone')->widget(PhoneInput::className(), [
                    'jsOptions' => [
                        'preferredCountries' => $preferred,
                        'nationalMode' => false,
                    ],
                    'id' => 'phone',
                ]); ?>
                <?= HTML::activeHiddenInput($profile, 'phoneFull', ['id' => 'hidden']) ?>
                <?= $form->field($profile, 'email')->textInput(['maxlength' => true]) ?>
                <?= $form->field($profile, 'website')->textInput(['maxlength' => true]) ?>

            </div>
            <div class="col-md-3">
                <div class="email">
                    <h3><?= Html::icon('eye-close') ?> Email</h3>
                    <p>Wish to keep your email address private?  Create a forwarding email address for your profile.</p>
                    <h4>profileemail@ibnet.org</h4>
                    <!--- Begin Request Email Modal -->
                    <?php Modal::begin([
                        'header' => '<h3>Create a Forwarding Email Address</h3>',
                        'toggleButton' => [
                            'id' => 'request-email',
                            'label' => Html::img('@web/images/message-plus.png'),
                            'class' => 'btn btn-primary']
                    ]); ?>
                        <div class="modal-body">
                            <h3>How it works:</h3>
                            <?= isset($profile->email_pvt) ?
                                '<p>You already have a private email set up.  If you would like to update your private email, enter your new email below and click "Update."  That\'s it! We\'ll take care of the rest.</p>' :
                                '<p>Enter your email below.  Click "Set it Up."  That\'s it! We\'ll take care of the rest.</p>' ?>
                            <?= isset($profile->email_pvt) ?
                                '<p>Your profile will continue to list your ibnet.org email address.  Any emails sent to this address will be automatically forwarded to your private email.  You get to choose if you want to respond and divulge your private email address. This has the added benefit of letting you know which email enquiries come from ibnet.org.</p>' :
                                '<p>Your profile will list your new ibnet.org email address.  Any emails sent to this address will be automatically forwarded to your private email.  You get to choose if you want to respond and divulge your private email address. This has the added benefit of letting you know which email enquiries come from ibnet.org.</p>' ?>
                            <p>Note that this is a <i>forwarding email</i>, which means that no emails will be saved and there is no inbox associated with the address. Please allow 48 hours for your new email address to become active and visible on your profile.</p> 
                            <br>
                            <div class="modal-footer">
                                <div class="row">
                                    <div class="col-md-8">
                                        <h4>
                                        <?= isset($profile->email_pvt) ?
                                            'Your public email: <b> ' . $ibnetEmail . '</b>' :
                                            'Your new public email: <b> ' . $ibnetEmail . '</b>' ?>
                                        </h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-7">
                                        <?= $form->field($profile, 'email_pvt')->textInput(['maxlength' => true, 'placeholder' => 'Enter your private email address here...',])->label('') ?>
                                    </div>
                                    <div class="col-md-1">
                                        <?php isset($profile->email_pvt) ?
                                            $btn = 'Update ' . Html::icon('thumbs-up') :
                                            $btn = 'Set it Up ' . Html::icon('thumbs-up'); ?>
                                        <?= Html::a($btn, ['ajax/forwarding', 'id' => $profile->id], [
                                                'id' => 'email-id',
                                                'data-on-done' => 'emailDone',
                                                'data-form-id' => 'link_form',
                                                'class' => 'btn btn-primary'
                                            ]
                                        ) ?>
                                        <?php if (!isset($profile->email_pvt)) {  // populate private email input from main email input if private email is NULL
                                            $this->registerJs("$('#request-email').click(function() { $('#profile-email_pvt').val($('#profile-email').val());});", \yii\web\View::POS_READY);
                                        }
                                        $this->registerJs("$('#email-id').click(handleAjaxLink);", \yii\web\View::POS_READY); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div col="col-md-8">
                                        <div id="email-result" class="bg-danger"></div>   <!-- Error response for ajax request -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php Modal::end() ?>
                    <!--- End Request Email Modal -->
                </div>
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-8">
                <h3>Social Media</h3>
                <?= $form->field($social, 'sermonaudio')->textInput(['maxlength' => true]) ?>
                <?= $form->field($social, 'facebook')->textInput(['maxlength' => true]) ?>
                <?= $form->field($social, 'linkedin')->textInput(['maxlength' => true]) ?>
                <?= $form->field($social, 'twitter')->textInput(['maxlength' => true]) ?>
                <?= $form->field($social, 'google')->textInput(['maxlength' => true]) ?>
                <?= $form->field($social, 'rss')->textInput(['maxlength' => true]) ?>
                <?= $form->field($social, 'youtube')->textInput(['maxlength' => true]) ?>
                <?= $form->field($social, 'vimeo')->textInput(['maxlength' => true]) ?>
                <?= $form->field($social, 'pinterest')->textInput(['maxlength' => true]) ?>
                <?= $form->field($social, 'tumblr')->textInput(['maxlength' => true]) ?>
                <?= $form->field($social, 'soundcloud')->textInput(['maxlength' => true]) ?>
                <?= $form->field($social, 'instagram')->textInput(['maxlength' => true]) ?>
                <?= $form->field($social, 'flickr')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <?= $this->render('_profileFormFooter', ['profile' => $profile]) ?>

        <?php ActiveForm::end(); ?>

    </div>

</div>

<script type="text/javascript">
    $("form").submit(function() {
        $("#hidden").val($("#phone").intlTelInput("getNumber"));
    });
</script>
<script>
    $("td").click(function(event)
    {
           event.stopImmediatePropagation();
    });
</script>