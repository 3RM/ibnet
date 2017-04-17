<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>

<div class="">
    <p>A profile has been flagged as innappropriate:</p>

    <p>Profile: <?= Html::a(Html::encode($url), $url) ?></p>

    <?php if (isset($user)) { ?>
    	<?= '<p>Flagged by user: ' . $user . ' </p>' ?>
    <?php } else { ?>
    	<?= 'Flagged by User: Guest' ?>
    <?php } ?>
</div>
