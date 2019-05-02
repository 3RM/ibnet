<?php

use common\models\group\Group;
use yii\bootstrap\Html;

?>
        <div class="row top-margin-40">
            <div class="col-md-8">
                <?= Html::a('Cancel', ['my-groups'], ['class' => 'btn btn-primary']) ?>
                <?= Html::submitButton('Save', [
                    'method' => 'POST',
                    'class' => 'btn btn-primary',
                    'name' => 'continue',
                ]) ?>
            </div>
        </div>