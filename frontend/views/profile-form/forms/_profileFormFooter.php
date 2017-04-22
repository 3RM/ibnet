<?php

use common\models\profile\Profile;
use yii\bootstrap\Html;

?>
        <div class="row top-margin">
            <div class="col-md-8">
                <?php if ($profile->status == Profile::STATUS_ACTIVE || $profile->edit == 10) { ?>
                    <?= Html::a('Cancel', ['/preview/view-preview', 'id' => $profile->id], ['class' => 'btn btn-primary']) ?>
                    <?= HTML::submitbutton('Save', [
                        'method' => 'POST',
                        'class' => 'btn btn-primary',
                        'name' => 'save',
                    ]) ?>
                <?php } else { ?>
                    <?= Html::a('Exit', ['profile-mgmt/my-profiles', 'id' => $profile->id], ['class' => 'btn btn-primary']) ?>
                    <?= Html::submitButton('Save & Continue', [
                        'method' => 'POST',
                        'class' => 'btn btn-primary',
                        'name' => 'continue',
                    ]) ?>
                <?php } ?>
            </div>
        </div>