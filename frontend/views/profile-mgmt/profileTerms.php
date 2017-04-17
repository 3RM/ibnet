<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'Requirements';
?>

<div class="profile-terms">
    <div class="terms-header">
        <div class="container">
            <h1>Create a New Profile</h1>
        </div>
    </div>
    <div class="container">
        <p>Three requirements must be met in order to create a new profile.</p>

        <ol>
            <li>
                <p><strong>The ministry must be independent</strong>.</p>
                <p>A church must be autonomous and free from any governing body outside of and over the church, such as a convention.  A church is still considered independent if it belongs to an Independent Baptist association or fellowship.</p>
            </li>
            <li>
                <p><strong>The ministry affirms the following beliefs:</strong></p>
                
                <h3>Doctrine</h3>
                <ol>
                    <li>Verbal, plenary inspiration of Scripture, rendering it infallible and inerrant</li>
                    <li>Complete preservation of Scripture down through the ages</li>
                    <li>One Triune God, revealed in the Persons of the Father, Son, and Holy Spirit</li> 
                    <li>Literal six day creation</li>
                    <li>Deity, virgin-birth, and bodily resurrection of Jesus Christ</li>
                    <li>Total depravity of man</li>
                    <li>Salvation by grace alone through faith alone in the substitutionary death, burial, and resurrection of Jesus Christ</li>
                    <li>Security of the believer</li>
                    <li>Local church as the center of God’s program in this present age</li>
                    <li>Male headship in the church and home</li>
                    <li>Marriage is between one man and one woman</li>
                    <li>Cessation of the sign gifts</li>
                    <li>Premillennial return of Jesus Christ</li>
                    <li>Literal heaven and hell</li>
                </ol>

                <h3>Baptist Distinctives</h3>
                <ul>
                    <li>Bible, the sole authority of faith and practice</li>
                    <li>Regenerated and immersed church membership</li>
                    <li>Autonomy of the local church</li>
                    <li>Priesthood of the believer</li>
                    <li>Soul liberty</li>
                    <li>Immersion and the Lord’s Supper, the only two ordinances</li>
                    <li>Separation of Church and State</li>
                    <li>Separation: ethically and ecclesiastically <p>(After Dr. Richard Weeks. Read more <a href="http://www.mbu.edu/seminary/sunesis/the-logic-of-brapsis/" target="_blank">here</a>.)</p></li>
                </ul>
            </li>
            <li>
                <p><strong>You, as the owner of this profile, must be a part of the profiled ministry</strong>.</p>
                <p>This requirement helps keep this directory current and accurate.  A church profile, for example, should be created by the pastor or a church member.</p>
            </li>
        </ol>
        <p>Any information that you provide in the proceeding forms will be available for public viewing through the IBNet directory and accompanying features unless stated otherwise.  By clicking "I agree" below, you acknowledge your agreement with these stated requirements and that you have read and agreed to our posted <?= HTML::a('Terms', ['site/terms'], ['target' => 'blank']) ?> of use.</P>
    </div>
</div>
<div class="container">
    <?php $form = ActiveForm::begin(); ?>
    <?= Html::submitButton('I Agree', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
</div>