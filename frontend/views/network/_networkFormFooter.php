<?php

use common\models\network\Network;
use yii\bootstrap\Html;

?>
        <div class="row top-margin-40">
            <div class="col-md-8">
                <?= Html::a('Cancel', ['my-networks'], ['class' => 'btn btn-primary']) ?>
                <?= Html::submitButton('Save', [
                    'method' => 'POST',
                    'class' => 'btn btn-primary',
                    'name' => 'continue',
                ]) ?>
            </div>
        </div>