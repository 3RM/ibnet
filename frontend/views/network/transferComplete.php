<?php

use common\widgets\Alert;
use frontend\controllers\ProfileController;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $profilemodel app\models\Profile */

$this->title = 'Transfer Complete';
?>

<div class="profile-terms">
    <div class="terms-header">
        <div class="container">
            <h1><?= Html::icon('transfer') . ' ' . $this->title ?></h1>
        </div>
    </div>
</div>

<div class="container">

    <h1><?= Html::icon('thumbs-up') ?> Congratulations!</h1>

    <p>&nbsp;</p>
    <p>You now own the IBNet network "<?= $network->name ?>".</p>

    <p>You can manage the network at <?= Html::a('My Networks', ['/network/my-networks']) ?></p>

</div>